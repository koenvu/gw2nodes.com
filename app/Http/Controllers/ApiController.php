<?php

namespace App\Http\Controllers;

use App\Map;
use App\Poi;
use App\Node;
use App\Container;
use App\ItemOfInterest;
use App\Events\NodeCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;

class ApiController extends Controller
{
    /**
     * Call to find which map contains the point (x,y).
     *
     * @param  int  $x The x-coordinate to search for.
     * @param  int  $y The y-coordinate to search for.
     * @return Response
     */
    public function whichmap($x, $y)
    {
        $map = Map::where('is_public', '=', 1)
                  ->where('left', '<', $x)
                  ->where('top', '<', $y)
                  ->where('right', '>', $x)
                  ->where('bottom', '>', $y)
                  ->with('containers')
                  ->first();

        if ($map != null) {
            return $map;
        } else {
            return '{}';
        }
    }

    public function addNode(Request $request)
    {
        $x = Input::get('x');
        $y = Input::get('y');
        $server = Input::get('server');
        $rich = Input::get('rich') == 'true';
        $notes = Input::get('notes', '');

        $map = Map::where('is_public', '=', 1)
                  ->where('left', '<', $x)
                  ->where('top', '<', $y)
                  ->where('right', '>', $x)
                  ->where('bottom', '>', $y)
                  ->with('containers')
                  ->first();

        $container = Container::findOrFail(Input::get('type'));

        if ($map != null && strlen($server) > 0) {
            $node = new Node();
            $node->server = $server;
            $node->x = $x;
            $node->y = $y;
            $node->is_rich = (int) $rich;
            $node->notes = $notes;
            $node->map()->associate($map);
            $node->container()->associate($container);
            $node->build_id = max(0, Node::max('build_id'));
            $node->save();

            Cache::tags(['server-'.$server])->flush();

            event(new NodeCreated($node, $map, $container, $request->ip()));

            return [
                'status' => 'OK',
                'nodes' => $this->findNodes($server),
            ];
        } else {
            return [
                'status' => 'FAIL',
                'nodes' => $this->findNodes($server),
            ];
        }
    }

    protected function findNodes($server)
    {
        $nodes = Cache::tags(
            ['node', 'server', 'server-'.$server]
        )->remember(
            'nodes-'.$server,
            60,
            function () use ($server) {
                return Node::where(function ($query) use ($server) {
                    $query->where('server', '=', $server)
                          ->orWhere('is_permanent', '=', 1);
                })->with(['container.items.containers'])->limit(750)->get()->map(function ($item) {
                    $item->addHidden('container', 'build_id', 'server', 'map_id');

                    return $item;
                });
            }
        );

        return $nodes;
    }

    public function nodes(Request $request, $server)
    {
        return $this->findNodes($server);
    }

    public function nodeInfo($id)
    {
        $node = Node::with(['container', 'map', 'container.maps', 'container.items'])->find($id);

        return $node;
    }

    public function reportNode(Request $request)
    {
        $id = Input::get('id');

        $node = Node::with(['container', 'map', 'container.maps', 'container.items'])->find($id);

        if ($node != null && $node->is_permanent == 0) {
            Cache::tags(['server-'.$node->server])->flush();

            $node->delete();
        }

        return ['nodes' => $this->findNodes($request->get('server'))];
    }

    public function demoteNode(Request $request)
    {
        $id = Input::get('id');

        $node = Node::with(['container', 'map', 'container.maps', 'container.items'])->find($id);

        if ($node != null && $node->is_permanent == 0) {
            Cache::tags(['server-'.$node->server])->flush();

            $node->is_rich = false;
            $node->save();
        }

        return ['nodes' => $this->findNodes($request->get('server'))];
    }

    public function promoteNode(Request $request)
    {
        $id = Input::get('id');

        $node = Node::with(['container', 'map', 'container.maps', 'container.items'])->find($id);

        if ($node != null && $node->is_permanent == 0) {
            Cache::tags(['server-'.$node->server])->flush();

            $node->is_rich = true;
            $node->save();
        }

        return ['nodes' => $this->findNodes($request->get('server'))];
    }

    public function waypoints()
    {
        $waypoints = Poi::where('type', '=', 'waypoint')->get();

        return $waypoints;
    }

    public function landmarks()
    {
        $landmarks = Poi::where('type', '=', 'landmark')->get();

        return $landmarks;
    }

    public function itemsOfInterest()
    {
        return ItemOfInterest::with('item')->get();
    }

    public function findItem($id, $token)
    {
        $results = collect();
        $contents = file_get_contents("https://api.guildwars2.com/v2/characters?access_token={$token}&page=0");

        $json = json_decode($contents);

        $lookingFor = $id;

        foreach ($json as $character) {
            foreach ($character->equipment as $item) {
                if ($item->id == $lookingFor) {
                    $results->push('Character: '.$character->name);
                }
            }

            foreach ($character->bags as $bag) {
                if ($bag) {
                    foreach ($bag->inventory as $item) {
                        if ($item && $item->id == $lookingFor) {
                            $results->push('Character: '.$character->name);
                        }
                    }
                }
            }
        }

        $contents = file_get_contents("https://api.guildwars2.com/v2/account/bank?access_token={$token}");

        $json = json_decode($contents);

        foreach ($json as $k => $item) {
            if ($item && $item->id == $lookingFor) {
                $number = floor($k / 30) + 1;
                $results->push("Bank (tab #{$number})");
            }
        }

        return $results->unique()->values();
    }

    public function containers()
    {
        return Cache::tags(['container', 'price'])->remember('containers', 60, function () {
            return json_encode(
                Container::where('is_public', '=', 1)->with('items')->get()->sortBy('name')->values()
            );
        });
    }
}
