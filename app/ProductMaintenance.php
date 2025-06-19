<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductMaintenance extends Model
{
    protected $fillable = ['brand', 'model', 'product_bonus'];
}
