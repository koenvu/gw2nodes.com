<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    protected $appends = ['thumbnail', 'earnings'];
    protected $visible = ['id', 'name', 'thumbnail', 'maps', 'items', 'earnings'];

    public function items()
    {
        return $this->belongsToMany('App\Item')->withPivot('num_results');
    }

    public function maps()
    {
        return $this->belongsToMany('App\Map');
    }

    public function getThumbnailAttribute()
    {
        if ($this->attributes['image_url'] != '') {
            return $this->attributes['image_url'];
        } elseif ($this->items->count() > 0) {
            return $this->items->sortByDesc('price')->first()->image_url;
        } else {
            return 'default_img_source';
        }
    }

    public function getEarningsAttribute()
    {
        if (! $this->items->count()) {
            return 0;
        }

        $total = 0;

        foreach ($this->items as $item) {
            $total += $item->price * $item->pivot->num_results;
        }

        return floor($total / $this->items->count());
    }
}
