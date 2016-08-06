<?php

namespace App\Console\Commands;

use App\Item;
use App\Node;
use App\Price;
use GW2Treasures\GW2Api\GW2Api;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ApiPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieves prices for public items.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $gw2 = app()->make(GW2Api::class);

        $maxBuild = Node::max('build_id');

        $idCollection = Item::where('is_public', '=', 1)->pluck('api_id');
        $chunks = array_chunk($idCollection->toArray(), 50);

        foreach ($chunks as $chunk) {
            $prices = $gw2->commerce()->prices()->many($chunk);
            foreach ($prices as $price) {
                $item = Item::where('api_id', '=', $price->id)->first();
                if ($item != null) {
                    $item->price = $price->buys->unit_price;
                    $this->info($item->name.' '.$item->price);
                    $item->save();

                    // Store the prices as a row in the database
                    // so that we can analyze them later. Be
                    // sure to add build_id for trends.
                    $itemPrice = new Price;
                    $itemPrice->buys_quantity = $price->buys->quantity;
                    $itemPrice->buys_unit_price = $price->buys->unit_price;
                    $itemPrice->sells_quantity = $price->sells->quantity;
                    $itemPrice->sells_unit_price = $price->sells->unit_price;
                    $itemPrice->build_id = $maxBuild;
                    $item->prices()->save($itemPrice);
                }
            }
        }

        Cache::tags(['price'])->flush();
    }
}
