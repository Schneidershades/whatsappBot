<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Model;

class ModelController extends Controller
{
    public function index()
    {
    	return Model::all();
    }

    public function show($id);
    {
    	
    }
}
