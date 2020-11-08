<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Year;
use App\Models\Make;
use App\Models\CarModel;
use App\Models\Component;
use App\Models\Search;
use Twilio\Rest\Client;

class SearchController extends Controller
{
    public function store(Request $request)
    {
    	$from = $request->input('From');
        $body = strtolower($request->input('Body'));

        $client = new \GuzzleHttp\Client();

        $message = null;

        $phone = $this->dbSavedRequest($from, $body);

        if($phone->stage_model == 'new'){
            if($body == "f2"){
                $phone->stage_model = 'yearShortList';
                $phone->save();
                return $this->yearShortList($from, $body);
            }

        }

        if($phone->stage_model == 'yearShortList' || $phone->stage_model == 'yearFullList' && $phone->year == null){
            return $this->yearResponseToMakeTable($from, $body);
        }

        // if($phone->stage_model == 'awaitingYear' && $phone->year == null){

        // }

        // if($phone->stage_model == 'makeShortList' && $phone->make == null){
        //     $message .= $this->modelShortList($from, $body);
        // }

        // if($phone->stage_model == 'makeFullList' && $phone->make == null){
        //     $message .= $this->modelFullList($from, $body);
        // }

        // if($phone->stage_model == 'modelShortList' && $phone->model == null){

        // }

        // if($phone->stage_model == 'modelFullList' && $phone->model == null){

        // }

        // if($phone->stage_model == 'componentShortList' && $phone->component == null){

        // }

        // if($phone->stage_model == 'componentFullList' && $phone->component == null){

        // }

        // if($body == 'cancel'){
        //     $phone->terminate = true;
        //     $phone->finished = true;
        //     $phone->save();

        //     $message .= "Search Session was cancelled. Type menu to proceed";
        // }

        // return $message;
        

        return $message;
        

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
            $phone->stage_model = 'new';
            $phone->request_received = $body;
            $phone->save();
            return $phone;
		}

		return $phone;

    }

    public function shortCarYearsList()
    {
    	$year = Year::select('year')
	    		->distinct()
	    		->orderBy('year', 'desc')
	    		->limit(8)
	    		->pluck('year')
	    		->toArray()
	    		;
	    return $year;
    }

    public function fullCarYearsList()
    {
        $year = Year::select('year')
          ->distinct()
          ->orderBy('year', 'desc')
          ->get()
          ->pluck('year')
          ->toArray()
          ;
        return $year;
    }

    public function yearShortList($from, $body)
    {
        $message = null;

        $phone = $this->dbSavedRequest($from, $body);
        $phone->stage_model = 'yearShortList';
        $phone->save();

        $message .= "Please Select a year \n ";

        $years = $this->shortCarYearsList();

        foreach($years as $year){
            $message .= $year ." \n ";
        }

        $message .= "Press *9* to view full list \n ";
        $message .= "Press *10* to go to previous \n ";

        return $message;
    }

    public function yearFullList($from, $body)
    {
        $message = null;

        $phone = $this->dbSavedRequest($from, $body);
        $phone->stage_model = 'yearFullList';
        $phone->save();
        
        $message .= "Please Select a year \n ";

        $years = $this->fullCarYearsList();

        foreach($years as $year){
            $message .= $year ." \n ";
        }

        return $message;
    }

    public function yearResponseToMakeTable($from, $body)
    {   
        $message = null;

        return $phone = $this->dbSavedRequest($from, $body);

        if($body == 9 && $phone->stage_model = 'yearShortList'){
            return $this->yearFullList($from, $body);
        }

        if($body == 10 && $phone->stage_model = 'yearShortList'){
            $phone->stage_model = 'random';
            $phone->terminate = true;
            $phone->finished = true;
            $phone->save();
        }

        $yearItems = Year::where('year', $body)
                    ->select('makeid')
                    ->distinct()
                    ->limit(8)
                    ->pluck('makeid')
                    ->toArray();

        if($yearItems){
            $phone->year = $body;

            $message .= "Year selected : $phone->year \n ";
            $message .= "Please Select a your company manufacturer \n ";

            $makeids = Make::whereIn('makeid', $yearItems)->orderBy('company', 'asc')->get();

            foreach($makeids as $make){
                $message .= $make->makeid . " - " . $make->company . " \n ";
            }

            $message .= "Please Press *9* to view full list \n ";
            $message .= "Please Press *10* to go to previous \n ";

            $phone->stage_model = 'yearShortList';
            $phone->save();
        }

        

        return $message;
    }

    public function makeShortTable($from, $body)
    {
        $message = null;
        $message .= "Year selected : $phone->year \n ";
        $message .= "Please Select a your company manufacturer \n ";

        $yearItems = Year::where('year', $body)
              ->select('makeid')
              ->distinct()
              ->limit(8)
              ->pluck('makeid')
              ->toArray();


        if($yearItems){
            $phone->year = $body;
        }

        $makeids = Make::whereIn('makeid', $yearItems)->orderBy('company', 'asc')->get();

        foreach($makeids as $make){
            $message .= $make->makeid . " - " . $make->company . " \n ";
          }

          $phone->stage_model = 'makeShortList';
          $phone->save();

        return $message;
    }

    public function makeFullList($body, $phone)
    {
      	$message = null;
    		$message .= "Year selected : $phone->year \n ";
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

        	$phone->stage_model = 'makeShortList';
        	$phone->save();

    		return $message;
    }

    public function makeResponse()
    {
        $phone = $this->dbSavedRequest($from, $body);

        if($body == 9 && $phone->stage_model = 'makeShortList'){
            return $this->yearFullList($from, $body);
        }

        if($body == 10 && $phone->stage_model = 'makeShortList'){
            $phone->stage_model = 'random';
            $phone->terminate = true;
            $phone->finished = true;
            $phone->save();
        }
    }


  //   public function modelShortList($body, $phone)
  //   {
  //       $message = null;
  //       $message .= "Year selected : $phone->year \n ";
  //       $message .= "Please Select a your company manufacturer \n ";

  //       $yearItems = Year::where('year', $body)
  //                   ->select('makeid')
  //                   ->distinct()
  //                   ->limit(8)
  //                   ->pluck('makeid')
  //                   ->toArray();

  //       if($yearItems){
  //           $phone->year = $body;
  //       }

  //       $makeids = Make::whereIn('makeid', $yearItems)->orderBy('company', 'asc')->get();

  //       foreach($makeids as $make){
  //           $message .= $make->makeid . " - " . $make->company . " \n ";
  //       }

  //       $message .= "Please Press *9* to view full list \n ";
  //       $message .= "Please Press *10* to go to previous \n ";

  //       $phone->stage_model = 'make';
  //       $phone->save();

  //       return $message;
  //   }

  //   public function modelFullList($body, $phone)
  //   {
  //       $message = null;
  //       $message .= "Year selected : $phone->year \n ";
  //       $message .= "Please Select a your company manufacturer \n ";

  //       $yearItems = Year::where('year', $body)
  //                   ->select('makeid')
  //                   ->distinct()
  //                   ->get()
  //                   ->pluck('makeid')
  //                   ->toArray();


  //       if($yearItems){
  //           $phone->year = $body;
  //       }

  //       $makeids = Make::whereIn('makeid', $yearItems)->orderBy('company', 'asc')->get();

  //       foreach($makeids as $make){
  //           $message .= $make->makeid . " - " . $make->company . " \n ";
  //       }

  //       $phone->stage_model = 'make';
  //       $phone->save();

  //       return $message;
  //   }

    public function chatModel()
    {
        $array1 = explode(" ", $body);

        $chats = Chat::all();

        $replies = [];

        foreach($chats as $chat){

            $array2 = explode(" ", $chat['incoming_message']);

            $similar = array_intersect($array1, $array2);

            $a = round(count($similar));

            $b = count($array1);

            $average = $a/$b*100;

            if($average >= 50 && $chat['outgoing_message'] != null){
                $newdata = array (
                    'average' => $average,
                    'reply' => $chat['outgoing_message']
                );

                array_push($replies, $newdata);
            }
        }

        // $message = "1. About AutoPartz\n";
        // $message .= "2. Contact AutoPartz\n";

        // $message = "*Address* 55, Akobi Crescent, Off Atunrashe Street, Mushin, Lagos\n";
        // $message .= "*Phone* 08097772886 (WhatsApp), 09030007004 (WhatsApp)\n";
        // $message .= "*Email* info@autopartz.com\n";
        // $message .= "*Website* https://www.autopartz.com\n";

        // dd($message);

        if($replies == null || $replies == []){
            $newChat = new Chat;
            $newChat->incoming_message = $body;
            $newChat->phone = $from;
            $newChat->save();

            $message = "*Welcome To AutoPartz!!!*\n";
            $message .= "I am here to assist you\n";
            $message .= "Please kindly press *menu* to access our support features\n";

            return $this->sendWhatsAppMessage($message, $from);
        }

        $maximum_number = (max(array_column($replies, "average")));

        $message = $this->arraySearch($replies, "average", $maximum_number);

        return $message['reply'];
    }

    public function sendWhatsAppMessage(string $message, string $recipient)
    {
        $twilio_whatsapp_number = getenv('TWILIO_WHATSAPP_NUMBER');
        $account_sid = env("TWILIO_SID");
        $auth_token = env("TWILIO_AUTH_TOKEN");

        $client = new Client($account_sid, $auth_token);

        return $client->messages->create($recipient, array('from' => "whatsapp:$twilio_whatsapp_number", 'body' => $message));
    }
}
