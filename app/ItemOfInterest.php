<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemOfInterest extends Model
{
    protected $hidden = [
        'created_at', 'deleted_at',
    ];

    public function item()
    {
        return $this->belongsTo('App\Item');
    }
}
