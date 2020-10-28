<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Year;

class YearController extends Controller
{
    public function store(Request $request)
    {
    	$from = $request->input('From');
        $body = strtolower($request->input('Body'));

        $client = new \GuzzleHttp\Client();

        $array1 = explode(" ", $body);

    	$years = Year::select('year')
			->distinct()
			->get();

		if($body != strtolower('Find Component')){
			$message = "Missing Request. please use *Find Compnent* to proceed";
		}

		foreach ($years as $year) {
           	echo $year->year .",\n";
		}

		
		return ($message);
    }

    public function show($year)
    {
    	
    }
}


