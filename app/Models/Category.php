<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Brand;

class Category extends Model
{
    protected $table = 'category';

    public function brands()
    {
    	return $this->hasMany(Brand::class, 'brand_id');
    }
}
