<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $visible = ['maps', 'price', 'image_url', 'name', 'api_id'];

    public function containers()
    {
        return $this->belongsToMany('App\Container');
    }

    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    public function getMapsAttribute()
    {
        $maps = collect();

        foreach ($this->containers as $container) {
            $maps = $maps->merge($container->maps);
        }

        return $maps->unique('api_id')->values()->all();
    }
}
