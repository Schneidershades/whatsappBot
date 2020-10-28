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
    public function store(Request $request)
    {
    	$from = $request->input('From');
        $body = strtolower($request->input('Body'));

        $client = new \GuzzleHttp\Client();

        $bodyItems = explode(", ", strtolower($body));

        $container = [];

        foreach ($bodyItems as $bodyItem) {
        	return $this->confirmTable($bodyItem);
        	array_push($this->confirmTable($bodyItem), $container);
        }

        return $container;




  //       if(in_array('find', $bodyItems) && in_array('vehicle', $bodyItems)){
		// 	return 'ggo';
		// } 


		// if(
		// 		in_array('year', $bodyItems) 
		// 	&& 	in_array('make', $bodyItems) 
		// 	&& 	in_array('model', $bodyItems) 
		// 	&& 	in_array('component', $bodyItems)
		// )
		// {
		// 	// array_keys($bodyItems, "blue")
			
		// }

		 


		if(in_array('year', $bodyItems) && in_array('vehicle', $bodyItems)){
			return 'ggo';
		} 

		if(in_array('year', $bodyItems) && in_array('vehicle', $bodyItems)){
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

  //   public function allTables($items)
  //   {
  //   	// dd($items);
  //   	$year = Year::whereIn('year', $items)->distinct()->get()->pluck('year')->toArray();
  //   	$make = Make::whereIn('company', $items)->distinct()->get()->pluck('company')->toArray();
  //   	$model = CarModel::whereIn('model', $items)->distinct()->get()->pluck('model')->toArray();
  //   	$component = Component::whereIn('component', $items)->distinct()->get()->pluck('component')->toArray();

  //   	$collection = array_merge($year, $make, $model, $component);

		// return array_intersect($items, $collection);
  //   }

    public function confirmTable($item)
    {
    	$year = null;
    	$make = null;
    	$model = null;
    	$component = null;

    	$year = Year::where('year', $item)->first();

    	if($year!=null){
    		return array('year' => $year->year);
    	}

    	$make = Make::where('company', $item)->first();

    	if($make!=null){
    		return ['make' => $make->company];
    	}

    	$model = CarModel::where('model', $item)->first();

    	if($model!=null){
    		return ['model' => $model->model];
    	}

    	$component = Component::where('component', $item)->first();

    	if($component!=null){
    		return ['component' => $component->component];
    	}

    }
}


