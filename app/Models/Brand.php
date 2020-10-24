<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Component;
use App\Models\Category;

class Brand extends Model
{
    protected $table = 'brand';

    public function component()
    {
    	return $this->belongsTo(Component::class, 'component_id');
    }

    public function category()
    {
    	return $this->belongsTo(Category::class, 'category_id');
    }
}
