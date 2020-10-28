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

        return $bodyItems = explode(" ", strtolower($body));

		if(array_search('find', $bodyItems) && array_search('vehicle', $bodyItems)){
			return 'ggo';
		}  

		// if($body != strtolower('Find Component')){
		// 	$message = "Missing Request. please use *Find Component* to proceed";
		// }

		
		// return ($message);
    }

    public function show($year)
    {
    	$from = $request->input('From');
        $body = strtolower($request->input('Body'));

        $client = new \GuzzleHttp\Client();

        $array1 = explode(" ", $body);

        if($body != strtolower('Find Component')){
			$message = "Missing Request. please type *year of ve* to proceed";
		}

		if((int)$body){
			$message = "Invalid Request. please input a number to proceed";
		}

    	$years = Year::where('year', $year)->distinct()->get();
    }

    public function findYear(){

    	$years = Year::select('year')
			->distinct()
			->get();

		$message = null;

		foreach ($years as $year) {
           	$message =  $year->year .",\n";
		}

    }
}


