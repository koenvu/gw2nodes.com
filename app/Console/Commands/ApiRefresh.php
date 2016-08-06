<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ApiRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pulls data from the official GW2 API.';

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
        $gw2 = app()->make('\GW2Treasures\GW2Api\GW2Api');
        $maps = $gw2->maps()->all();

        $ids = $gw2->items()->ids();
        $newIds = [];

        foreach ($ids as $itemId) {
            $item = \App\Item::where('api_id', '=', $itemId)->first();
            if ($item == null) {
                $newIds[] = $itemId;
            }
        }

        $chunks = array_chunk($newIds, 50);

        foreach ($chunks as $k => $chunk) {
            $items = $gw2->items()->many($chunk);

            foreach ($items as $itemInfo) {
                $item = app()->make('\App\Item');
                $item->api_id = $itemInfo->id;
                $item->name = $itemInfo->name;
                $item->is_public = false;
                $item->type = $itemInfo->type;
                $item->image_url = $itemInfo->icon;
                $item->save();
            }

            sleep(1);
            $this->info(($k + 1).' / '.count($chunks));
        }

        foreach ($maps as $mapInfo) {
            $map = \App\Map::where('api_id', '=', $mapInfo->id)->first();
            if ($map == null) {
                $map = app()->make('\App\Map');
                $map->api_id = $mapInfo->id;
                $map->is_public = false;
            }

            $map->api_region_id = $mapInfo->region_id;
            $map->name = $mapInfo->name;
            $map->left = $mapInfo->continent_rect[0][0];
            $map->top = $mapInfo->continent_rect[0][1];
            $map->right = $mapInfo->continent_rect[1][0];
            $map->bottom = $mapInfo->continent_rect[1][1];
            $map->default_floor = $mapInfo->default_floor;
            $map->save();
        }
    }
}
