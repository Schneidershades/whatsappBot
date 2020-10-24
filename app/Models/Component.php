<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Brand;

class Component extends Model
{
    protected $table = 'component';

    public function brands()
    {
    	return $this->hasMany(Brand::class, 'brand_id');
    }
}
