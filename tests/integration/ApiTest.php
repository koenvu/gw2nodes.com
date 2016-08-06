<?php

use App\Map;
use App\Item;
use App\Node;
use App\Container;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ApiTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function it_can_tell_which_visible_map_coordinates_are_in()
    {
        $map1 = factory(Map::class)->create([
            'bottom' => 150,
            'left' => 50,
            'right' => 150,
            'top' => 50,
        ]);

        $map2 = factory(Map::class)->create([
            'bottom' => 250,
            'left' => 50,
            'right' => 150,
            'top' => 150,
        ]);

        $map3 = factory(Map::class)->create([
            'bottom' => 350,
            'left' => 50,
            'right' => 150,
            'top' => 250,
            'is_public' => false,
        ]);

        $this->get('api/whichmap/100/100')
             ->assertResponseStatus(200)
             ->see($map1->name)
             ->dontSee($map2->name)
             ->dontSee($map3->name);

        $this->get('api/whichmap/100/200')
             ->assertResponseStatus(200)
             ->dontSee($map1->name)
             ->see($map2->name)
             ->dontSee($map3->name);

        $this->get('api/whichmap/100/300')
             ->assertResponseStatus(200)
             ->dontSee($map1->name)
             ->dontSee($map2->name)
             ->dontSee($map3->name);
    }

    /** @test */
    function it_can_serve_node_locations()
    {
        $containers = factory(Container::class, 3)->create();

        $containers->each(function ($container) {
            $container->items()->save(factory(Item::class)->create());
        });

        $nodes = factory(Node::class, 3)->create([
            'server' => '0.0.0.0',
            'container_id' => $containers->get(0)->id,
        ]);
        $otherNode = factory(Node::class)->create([
            'server' => '0.0.0.1',
            'container_id' => $containers->get(1)->id,
        ]);
        $permanentNode = factory(Node::class)->create([
            'server' => '0.0.0.1',
            'is_permanent' => true,
            'container_id' => $containers->get(2)->id,
        ]);

        $this->get('api/nodes/0.0.0.0')
             ->assertResponseStatus(200)
             ->dontSeeJson(['name' => $otherNode->container->name])
             ->seeJson(['name' => $permanentNode->container->name])
             ->seeJson(['name' => $nodes->first()->container->name])
             ->seeJsonStructure([
                '*' => [
                    'id',
                    'server',
                    'x',
                    'y',
                    'is_rich',
                    'container_id',
                    'map_id',
                    'build_id',
                    'is_permanent',
                    'notes',
                    'container' => [
                        'id',
                        'name',
                        'thumbnail',
                        'earnings',
                        'items' => [
                            '*' => [
                                'name',
                                'api_id',
                                'image_url',
                                'price'
                            ],
                        ],
                    ],
                ],
             ]);
    }

    /** @test */
    function it_allows_creating_nodes()
    {
        $container = factory(Container::class)->create();
        $item = factory(Item::class)->create();
        $container->items()->save($item);

        $map1 = factory(Map::class)->create([
            'bottom' => 150,
            'left' => 50,
            'right' => 150,
            'top' => 50,
        ]);

        $this->post('api/add-node', [
            'x' => 100,
            'y' => 100,
            'type' => $container->id,
            'server' => '1.2.3.4',
            'rich' => false,
            'notes' => 'Hard to find',
        ])->assertResponseStatus(200)
          ->seeJson(['name' => $container->name])
          ->seeJson(['status' => 'OK'])
          ->seeInDatabase('nodes', [
            'server' => '1.2.3.4',
            'x' => 100,
            'y' => 100,
            'map_id' => $map1->id,
            'container_id' => $container->id,
            'is_permanent' => false,
            'is_rich' => false,
            'notes' => 'Hard to find',
          ]);
    }

    /** @test */
    function it_will_not_insert_a_node_with_an_invalid_container_id()
    {
        $container = factory(Container::class)->create();
        $item = factory(Item::class)->create();
        $container->items()->save($item);

        $map1 = factory(Map::class)->create([
            'bottom' => 150,
            'left' => 50,
            'right' => 150,
            'top' => 50,
        ]);

        $this->post('api/add-node', [
            'x' => 100,
            'y' => 100,
            'type' => $container->id + 1,
            'server' => '1.2.3.4',
            'rich' => false,
            'notes' => 'Hard to find',
        ])->assertResponseStatus(404);
    }

    /** @test */
    function it_will_delete_a_reported_node()
    {
        $container = factory(Container::class)->create();
        $item = factory(Item::class)->create();
        $container->items()->save($item);

        $map1 = factory(Map::class)->create([
            'bottom' => 150,
            'left' => 50,
            'right' => 150,
            'top' => 50,
        ]);

        $node1 = factory(Node::class)->create();
        $node2 = factory(Node::class)->create(['is_permanent' => true]);

        $this->post('api/report-node', [
            'id' => $node1->id,
        ])->assertResponseStatus(200)->dontSeeInDatabase('nodes', [
            'id' => $node1->id,
            'deleted_at' => null,
        ]);

        $this->post('api/report-node', [
            'id' => $node2->id,
        ])->assertResponseStatus(200)->seeInDatabase('nodes', [
            'id' => $node2->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    function it_can_demote_a_node_from_rich_to_normal()
    {
        $node1 = factory(Node::class)->create(['is_rich' => true,]);
        $node2 = factory(Node::class)->create(['is_rich' => true, 'is_permanent' => true]);

        $this->post('api/demote-node', ['id' => $node1->id])
             ->assertResponseStatus(200)
             ->seeInDatabase('nodes', [
                'id' => $node1->id,
                'is_rich' => false,
             ]);

        $this->post('api/demote-node', ['id' => $node2->id])
             ->assertResponseStatus(200)
             ->seeInDatabase('nodes', [
                'id' => $node2->id,
                'is_rich' => true,
             ]);
    }

    /** @test */
    function it_can_promote_a_node_from_normal_to_rich()
    {
        $node1 = factory(Node::class)->create(['is_rich' => false,]);
        $node2 = factory(Node::class)->create(['is_rich' => false, 'is_permanent' => true]);

        $this->post('api/promote-node', ['id' => $node1->id])
             ->assertResponseStatus(200)
             ->seeInDatabase('nodes', [
                'id' => $node1->id,
                'is_rich' => true,
             ]);

        $this->post('api/promote-node', ['id' => $node2->id])
             ->assertResponseStatus(200)
             ->seeInDatabase('nodes', [
                'id' => $node2->id,
                'is_rich' => false,
             ]);
    }
}
