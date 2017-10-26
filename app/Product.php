<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function getPriceAttribute($id)
    {
        return $this->attributes['price'];
    }
}
