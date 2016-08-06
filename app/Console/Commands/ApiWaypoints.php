<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ApiWaypoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:waypoints';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch most recent PoI information';

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
        $maps = \App\Map::where('is_public', '=', 1)->get();
        \App\Poi::truncate();

        foreach ($maps as $map) {
            $url = "http://api.guildwars2.com/v2/continents/1/floors/{$map->default_floor}/regions/{$map->api_region_id}/maps/{$map->api_id}/pois?ids=all";
            $contents = file_get_contents($url);
            $json = json_decode($contents);

            foreach ($json as $poi) {
                if (! isset($poi->name)) {
                    $poi->name = 'Vista';
                }

                $obj = new \App\Poi();
                $obj->api_id = $poi->id;
                $obj->map()->associate($map);
                $obj->name = $poi->name;
                $obj->type = $poi->type;
                $obj->x = $poi->coord[0];
                $obj->y = $poi->coord[1];
                $obj->save();
            }
        }
    }
}
