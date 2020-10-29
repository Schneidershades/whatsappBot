<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Year;
use App\Models\Make;
use App\Models\CarModel;
use App\Models\Component;
use Illuminate\Support\Arr;

class YearController extends Controller
{
	public function index()
	{
		return Year::all();
	}

    public function store(Request $request)
    {
    	$from = $request->input('From');
        $body = strtolower($request->input('Body'));

        $client = new \GuzzleHttp\Client();

        $bodyItems = explode(", ", strtolower($body));

        $containers = array();

        if(in_array('yearId', $bodyItems)){
	    	$year = Year::where('year', $bodyItems[0])->pluck('makeid')->toArray();

	    	$make = Make::whereIn('makeid', $year)->get();

	    	return $make;
		}

		if(in_array('makeId', $bodyItems)){

	    	return $year = Year::whereIn('year', $items)
	    		->distinct()
	    		->get()
	    		->pluck('year')
	    		->toArray();
		}


		elseif(in_array('Selected', $bodyItems) && Yea){

	    	return $year = Year::whereIn('year', $items)
	    		->distinct()
	    		->get()
	    		->pluck('year')
	    		->toArray();
	    	
	  //   	$make = Make::whereIn('company', $items)->distinct()->get()->pluck('company')->toArray();
	  //   	$model = CarModel::whereIn('model', $items)->distinct()->get()->pluck('model')->toArray();
	  //   	$component = Component::whereIn('component', $items)->distinct()->get()->pluck('component')->toArray();

	  //   	$collection = array_merge($year, $make, $model, $component);

			// return array_intersect($items, $collection);

		}

		else{
			foreach ($bodyItems as $bodyItem) {
	        	if($this->confirmTable($bodyItem) != null){
	        		$containers[] = $this->confirmTable($bodyItem);
	        	}
	        }

	        $keyYear = null;
	        $keyModel = null;
	        $keyMake = null;
	        $keyComponent = null;
	        $keyYearName = null;
	        $keyModelName = null;
	        $keyMakeName = null;
	        $keyMakeName = null;

	        foreach ($containers as $container) {
	        	if(array_key_exists('yearid', $container)){
	        		$keyYear = $container['yearid'];
	        		$keyYearName = $container['year'];
	        	}

	        	if(array_key_exists('modelid', $container)){
	        		$keyModel = $container['modelid'];
	        		$keyModelName = $container['model'];
	        	}

	        	if(array_key_exists('makeid', $container)){
	        		$keyMake = $container['makeid'];
	        		$keyMakeName = $container['make'];
	        	}

	        	if(array_key_exists('component_id', $container)){
	        		$keyMake = $container['component_id'];
	        		$keyMakeName = $container['component'];
	        	}
	        }


	        if($keyYear == null){
	        	$message =  'No Year Result';
	        }elseif($keyModel == null){
	        	$message =  'No Year Result';
	        }elseif($keyMake == null){
	        	$message =  'No Year Result';
	        }

	        $search = $this->yearSearch($keyYear, $keyMake, $keyModel);

	        if($search == null){
	        	return 'We have no '. $keyModelName .' in '. $keyMakeName;
	        }
		}



		// if(in_array('year', $bodyItems) && in_array('vehicle', $bodyItems)){
		// 	return 'ggo';
		// } 
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

    public function confirmTable($item)
    {
    	$year = null;
    	$make = null;
    	$model = null;
    	$component = null;

    	$year = Year::where('year', $item)->first();

    	if($year!=null){
    		return array (
			  	'yearid' => $year->yearid,
			  	'year' => $year->year
			);
    	}

    	$make = Make::where('company', $item)->first();

    	if($make!=null){
    		return [
    			'makeid' => $make->makeid,
    			'make' => $make->company
    		];
    	}

    	$model = CarModel::where('model', $item)->first();

    	if($model!=null){
    		return [
    			'modelid' => $model->modelid,
    			'model' => $model->model
    		];
    	}

    	$component = Component::where('component', $item)->first();

    	if($component!=null){
    		return [
    			'componentid' => $component->component_id,
    			'component' => $component->component
    		];
    	}

    }

    public function yearSearch($year, $makeid, $modelid)
    {
    	$year = Year::where('yearid', $year)
    		->where('modelid', $modelid)
    		->where('makeid', $makeid)
    		->first();

    	return $year;
    }

}


