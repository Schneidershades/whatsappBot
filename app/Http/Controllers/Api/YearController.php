<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Year;

class YearController extends Controller
{
    public function index()
    {
   //  	return Year::select('yearid', 'year')
			// ->distinct()
			// ->get();
			// 
			return $user_names = Year::distinct()->get(['yearid', 'year']);
    }

    public function show($year)
    {
    	
    }
}


