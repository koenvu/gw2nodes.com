<?php

$factory->define(App\Container::class, function ($faker) {
    return [
        'name' => $faker->unique()->sentence(3, true),
        'notes' => '',
        'image_url' => '',
        'is_public' => true,
    ];
});

$factory->define(App\Item::class, function ($faker) {
    return [
        'name' => $faker->unique()->sentence(3, true),
        'api_id' => $faker->unique()->numberBetween(1, 10000),
        'type' => 'CraftingMaterial',
        'image_url' => '',
        'price' => 0,
        'is_public' => true,
    ];
});

$factory->define(App\Map::class, function ($faker) {
    return [
        'name' => $faker->sentence(3, true),
        'api_id' => $faker->unique()->numberBetween(1, 10000),
        'api_region_id' => $faker->numberBetween(1, 100),
        'left' => $faker->numberBetween(-1000, 1000),
        'top' => $faker->numberBetween(-1000, 1000),
        'right' => $faker->numberBetween(-1000, 1000),
        'bottom' => $faker->numberBetween(-1000, 1000),
        'default_floor' => $faker->numberBetween(1, 20),
        'is_public' => true,
    ];
});

$factory->define(App\Node::class, function ($faker) {
    return [
        'server' => $faker->ipv4,
        'x' => $faker->numberBetween(-1000, 1000),
        'y' => $faker->numberBetween(-1000, 1000),
        'is_rich' => false,
        'container_id' => function () {
            return factory(App\Container::class)->create()->id;
        },
        'map_id' => function () {
            return factory(App\Map::class)->create()->id;
        },
        'build_id' => 1,
        'is_permanent' => false,
        'notes' => '',
    ];
});
