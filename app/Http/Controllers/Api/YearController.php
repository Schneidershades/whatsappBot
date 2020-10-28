<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Year;

class YearController extends Controller
{
    public function index()
    {
    	$years = Year::select('year')
			->distinct()
			->get();

        $replies = [];

		foreach ($years as $year) {
            array_push($replies, $year['year']);
		}

		return $replies;
    }

    public function show($year)
    {
    	
    }
}


