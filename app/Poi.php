<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Poi extends Model
{
    public $timestamps = false;

    protected $appends = ['chatcode'];

    public function map()
    {
        return $this->belongsTo('App\Map');
    }

    public function getChatcodeAttribute()
    {
        return '[&'.base64_encode(chr(0x04).pack('V', $this->api_id)).']';
    }
}
