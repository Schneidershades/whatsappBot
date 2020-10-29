<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Year;
use App\Models\Make;
use App\Models\CarModel;
use App\Models\Component;
use App\Models\BotSearchRequest;

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

	    	if($replies == null || $replies == []){
	            $newChat = new BotSearchRequest;
	            $newChat->phone = $from;
	            $newChat->request_received = $body;
	            $newChat->save();
	        }


	    	foreach($years as $year){
	    		$message .= $year ." \n ";
	    	}

	    	return $message;

	    	// return $this->sendWhatsAppMessage($message, $from);
		}

		if(in_array('2000', $bodyItems)){

	    	$message = null;

			$phone = BotSearchRequest::where('phone', $from)
				->where('terminate', 'no')
				->where('finished', 'no')
				->first();

			if(!$phone){
				return 'ss';
			}

			if($phone->stage_model == 'year'){
				$yearItems = Year::where('year', $year)->get()->pluck('makeid')->toArray();
				$makeids = Make::whereIn('makeid', $yearItems)->get()->pluck('company')->toArray();

				foreach($makeids as $make){
		    		$message .= $make ." \n ";
		    	}

		    	return $message;
			}

			if($phone->stage_model == 'make'){
				
			}

			if($phone->stage_model == 'component'){
				
			}

			$phone->save();


	    	
	    	// return $this->sendWhatsAppMessage($message, $from);
		}








    }

    public function allCarYears()
    {
    	$year = Year::select('year')
	    		->distinct()
	    		->orderBy('year')
	    		->get()
	    		// ->pluck('year')
	    		// ->toArray()
	    		;
	    return $year;
    }
}
