<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarModel;

class CarModelController extends Controller
{
    public function index()
    {
    	return CarModel::all();
    }

    public function show($id)
    {
    	
    }
}
