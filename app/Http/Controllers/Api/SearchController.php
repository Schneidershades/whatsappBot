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

        if(str_word_count($body) == 1 || is_numeric($body)){

        	$message = null;

        	$phone = $this->dbSavedRequest($from, $body);

        	if($body == 'cancel'){
		    	$phone->terminate = true;
		    	$phone->finished = true;
		    	$phone->save();

		    	$message .= "Search Session was cancelled. Type Search to proceed to new search";
        	}

	        if($body == 'search'){

		    	$phone->stage_model = 'year';
		    	$phone->save();

				if($phone->stage_model == 'new' || $phone->year == null){
					$message .= $this->newStage($from, $body);
				}
			}

			if(is_numeric($body)){

    			

				if($phone->stage_model == 'year' && $phone->make == null){

					$message .= "$phone->year is your Selected year \n \n";
    				$message .= "Please Select a your company manufacturer \n ";

					$yearItems = Year::where('year', $body)
								->select('makeid')
								->distinct()
								->get()
								->pluck('makeid')
								->toArray();


					if($yearItems){
			    		$phone->year = $body;
					}

					$makeids = Make::whereIn('makeid', $yearItems)->orderBy('company', 'asc')->get();

					foreach($makeids as $make){
			    		$message .= $make->makeid . " - " . $make->company . " \n ";
			    	}

			    	$phone->stage_model = 'make';
			    	$phone->save();
				}

				// dd($phone);

				if($phone->stage_model == 'make'){

					dd($body);

					$makeId =  Make::where('makeid', $body)->first();

					dd($makeId->make);

					if(!$makeId){
						return $message = 'Invalid Item Selection';
					}

					$models = CarModel::where('makeid', $makeId->id)->get();

					if($models == null){
						return $mesage = "We have no models available in $makeId->make";
					}

    				$message .= "$phone->year is your Selected year \n ";
    				$message .= "$makeId->make is your Selected year \n ";
    				$message .= "Please Select a your company manufacturer model \n ";

					foreach($models as $model){
			    		$message .= $model->modelid . " - " . $makeId->make.' - '. $model->model . " \n ";
			    	}

			    	// $phone->stage_model = 'make';
			    	// $phone->save();

				}

				// if($phone->stage_model == 'component' ||  $phone->make == null){
					
				// }



				return $message;


		    	// return $this->sendWhatsAppMessage($message, $from);
			}
			
			
			return $message;



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

    	$message .= "Please Select a year \n ";

    	if(is_numeric($body)){
			$message .= 'Invalid year selection';
		}

		$years = $this->allCarYears();

    	foreach($years as $year){
    		$message .= $year ." \n ";
    	}

    	$phone = $this->dbSavedRequest($from, $body);

    	return $message;
    }
}
