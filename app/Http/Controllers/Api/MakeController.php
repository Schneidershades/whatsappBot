<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Make;

class MakeController extends Controller
{
    public function index()
    {
    	return Make::all();
    }
}
