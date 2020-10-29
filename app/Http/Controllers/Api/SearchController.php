<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Year;
use App\Models\Make;
use App\Models\CarModel;
use App\Models\Component;

class SearchController extends Controller
{
    public function store(Request $request)
    {
    	$from = $request->input('From');
        $body = strtolower($request->input('Body'));

        $client = new \GuzzleHttp\Client();

        $bodyItems = explode(" ", strtolower($body));

        $containers = array();

        if(in_array('search', $bodyItems)){
	    	$years = $this->allCarYears();

	    	$message = null;

	    	foreach($years as $year){
	    		$message =  $year .'<br>';
	    	}

	    	return $message;
		}
    }

    public function allCarYears()
    {
    	$year = Year::select('year')
	    		->distinct()
	    		->get()
	    		->pluck('year')
	    		->toArray();
	    return $year;
    }
}
