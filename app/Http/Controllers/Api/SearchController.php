<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Year;
use App\Models\Make;
use App\Models\CarModel;
use App\Models\Component;
use App\Models\Search;

class SearchController extends Controller
{
    public function store(Request $request)
    {
    	$from = $request->input('From');
        $body = strtolower($request->input('Body'));

        $client = new \GuzzleHttp\Client();


        // $bodyItems = explode(" ", strtolower($body));
        // str_word_count("Hello world!")

        if(str_word_count($body) == 1){

	        if($body == 'search'){

	        	$message = null;

	        	$phone = $this->dbSavedRequest($from, $body);

	        	if($body == 'cancel'){
			    	$phone->terminate = true;
			    	$phone->finished = true;
			    	$phone->save();
	        	}
	        	
				if($phone->stage_model == 'new' && $phone->year == null){
					return $this->newStage($body);
				}



				if($phone->stage_model == 'year'){

					// if(!$phone->year){
					// 	return $this->newStage($body);
					// }

					$yearItems = Year::where('year', $phone->year)->get()->pluck('makeid')->toArray();

					if($yearItems){
			    		$phone->year_id = $body;
					}

					$makeids = Make::whereIn('makeid', $yearItems)->get()->pluck('company')->toArray();

					foreach($makeids as $make){
			    		$message .= $make ." \n ";
			    	}

			    	$phone->stage_model = 'make';
			    	$phone->save();

			    	return $message;
				}

				// if($phone->stage_model == 'make'){
				// 	$yearItems = Make::where('company', $year)->get()->pluck('makeid')->toArray();
				// 	$makeids = Make::whereIn('makeid', $yearItems)->get()->pluck('company')->toArray();

				// 	foreach($makeids as $make){
			 //    		$message .= $make ." \n ";
			 //    	}

			 //    	return $message;
				// }

				if($phone->stage_model == 'component'){
					
				}

		    	// return $this->sendWhatsAppMessage($message, $from);
			}

			// if(in_array('2000', $bodyItems)){

		 //    	$message = null;

			// 	$phone = Search::where('phone', $from)
			// 		->where('terminate', 'no')
			// 		->where('finished', 'no')
			// 		->first();

			// 	if(!$phone){
			// 		return 'ss';
			// 	}

				

			// 	$phone->save();


		    	
		 //    	// return $this->sendWhatsAppMessage($message, $from);
			// }
        }


        

        








    }

    public function dbSavedRequest($from, $body)
    {
    	$phone = Search::where('phone', $from)
			->where('terminate', false)
			->where('finished', false)
			->first();

		if(!$phone){
			$phone = new Search;
            $phone->phone = $from;
            $phone->request_received = $body;
            $phone->save();
		}

		return $phone;

    }

    public function allCarYears()
    {
    	$year = Year::select('year')
	    		->distinct()
	    		->orderBy('year')
	    		->get()
	    		->pluck('year')
	    		->toArray()
	    		;
	    return $year;
    }

    public function newStage($from, $body)
    {
    	$message = null;

    	if((int)$body){
			$message .= 'Invalid year selection';
		}

		$years = $this->allCarYears();

    	foreach($years as $year){
    		$message .= $year ." \n ";
    	}

    	$phone = $this->dbSavedRequest($from, $body);

    	$phone->stage_model = 'year';
    	$phone->year = $body;
    	$phone->save();

    	return $message;
    }
}
