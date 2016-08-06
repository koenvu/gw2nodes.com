<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Node extends Model
{
    use SoftDeletes;

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function map()
    {
        return $this->belongsTo('\App\Map');
    }

    public function container()
    {
        return $this->belongsTo('\App\Container');
    }
}
