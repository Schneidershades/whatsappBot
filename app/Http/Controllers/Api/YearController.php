<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Year;

class YearController extends Controller
{
    public function index()
    {
    	return Year::all();
    }

    public function show($year)
    {
    	return Year::where('year', $year)
			->select('year')
			->distinct()
			->get();
    }
}


