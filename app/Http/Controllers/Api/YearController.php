<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Year;

class YearController extends Controller
{
    public function index()
    {
   //  	return Year::distinct()
			// ->get();

		return $faces = Year::select('yearid','year')
	        ->groupBy('year')
	        ->get();
			
			// return $user_names = Year::distinct()->get(['yearid', 'year']);
    }

    public function show($year)
    {
    	
    }
}


