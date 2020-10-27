<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Year;

class YearController extends Controller
{
    public function index()
    {
    	return Year::select('yearid', 'year')
			// ->distinct()
            ->groupBy('year')
			->get();
    }

    public function show($year)
    {
    	
    }
}


