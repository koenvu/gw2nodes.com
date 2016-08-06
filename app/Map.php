<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Map extends Model
{
    protected $visible = ['name', 'api_id', 'containers'];

    public function containers()
    {
        return $this->belongsToMany('App\Container');
    }

    public function pois()
    {
        return $this->hasMany('App\Poi');
    }
}
