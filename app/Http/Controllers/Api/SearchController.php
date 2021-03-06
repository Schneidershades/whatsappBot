<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Year;
use App\Models\Make;
use App\Models\CarModel;
use App\Models\Component;
use App\Models\Search;
use App\Models\Chat;
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
            if($body == "menu"){
                $phone->stage_model = 'yearShortList';
                $phone->save();
                return $this->yearShortList($from, $body);
            }
        }

        if($body == 'cancel'){
            $phone->terminate = true;
            $phone->finished = true;
            $phone->save();

            $message .= "Search Session was cancelled. Type menu to proceed";
        }

        if($phone->stage_model == 'yearShortList' || $phone->stage_model == 'yearFullList' && $phone->year == null){
            return $this->yearResponseToMakeTable($from, $body);
        }

        if($phone->stage_model == 'makeShortList' || $phone->stage_model == 'makeFullList' && $phone->make == null){
            return $this->makeResponseToModelTable($from, $body);
        }

        if($phone->stage_model == 'modelShortList' || $phone->stage_model == 'modelFullList' && $phone->car_model == null){
            return $this->modelResponseToComponentTable($from, $body);
        }

        if($phone->stage_model == 'componentShortList' || $phone->stage_model == 'componentFullList' && $phone->component == null){
            return $this->componentResponse($from, $body);
        }

        $message = $this->chatModel($from, $body);
        

        return $this->sendWhatsAppMessage($message, $from);
        

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

        $message .= "Please Press *f8* to view full list \n ";
        $message .= "Press *f9* to go to previous \n ";
        $message .= "Press *x* to cancel session \n ";

        // return $message;
        return $this->sendWhatsAppMessage($message, $from);
    }

    public function yearFullList($from, $body)
    {
        $message = null;

        $phone = $this->dbSavedRequest($from, $body);
        $phone->stage_model = 'yearFullList';
        $phone->save();

        $years = $this->fullCarYearsList();

        if($years){
            $message .= "Please Select a year \n ";
            
            foreach($years as $year){
                $message .= $year ." \n ";
            }

            $message .= "Please Press *f8* to view full list \n ";
            $message .= "Press *f9* to go to previous \n ";
            $message .= "Press *x* to cancel session \n ";

        }else{

            $message .= "No year found at this moment \n ";

        }

        // return $message;
        return $this->sendWhatsAppMessage($message, $from);
    }

    public function yearResponseToMakeTable($from, $body)
    {   
        $message = null;

        $phone = $this->dbSavedRequest($from, $body);

        if($body == 'f8' && $phone->stage_model = 'yearShortList'){
            $message =  $this->yearFullList($from, $body);

            return $this->sendWhatsAppMessage($message, $from);
        }

        if($body == 'f9' && $phone->stage_model = 'yearShortList'){
            $phone->stage_model = 'random';
            $phone->make = null;
            $phone->make_id = null;
            $phone->save();
        }

        

        if(is_numeric($body)){
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

                $message .= "Please Press *f8* to view full list \n ";
                $message .= "Press *f9* to go to previous \n ";
                $message .= "Press *x* to cancel session \n ";

                $phone->stage_model = 'makeShortList';
                $phone->save();
            }else{
                $message .= "Year Input not found \n ";
                $message .= $this->yearShortList($from, $body);
            }

                
        }else{
            $message .= "Invalid Input \n ";
            $message .= $this->yearShortList($from, $body);
        }

        // return $message;
        return $this->sendWhatsAppMessage($message, $from);
    }

    public function makeShortTable($from, $body)
    {
        $phone = $this->dbSavedRequest($from, $body);

        $makeid = $phone ? $phone->make_id : $body;

        $message = null;

         $yearItems = Year::where('year', $phone->year)
                    ->select('makeid')
                    ->distinct()
                    ->limit(8)
                    ->pluck('makeid')
                    ->toArray();

        if($yearItems){

            $message .= "Year selected : $phone->year \n ";
            $message .= "Please Select a your company manufacturer \n ";

            $makeids = Make::whereIn('makeid', $yearItems)->orderBy('company', 'asc')->get();

            // dd($makeids);

            foreach($makeids as $make){
                $message .= $make->makeid . " - " . $make->company . " \n ";
            }

            $phone->stage_model = 'makeShortList';
            $phone->save();

            $message .= "Please Press *f8* to view full list \n ";
            $message .= "Press *f9* to go to previous \n ";
            $message .= "Press *x* to cancel session \n ";

        }else{
            $message .= "No car was found for the selected car manufacturer \n ";

            $phone->stage_model = 'yearShortList';
            $phone->year = null;
            $phone->make_id = null;
            $phone->make = null;
            $phone->save();

            $message .= $this->yearShortList($from, $body);
        }

        // return $message;
        return $this->sendWhatsAppMessage($message, $from);
    }

    public function makeFullList($from, $body)
    {
        $phone = $this->dbSavedRequest($from, $body);

        $message = null;

        // colleting f9 to view all the list available under the year
        $yearItems = Year::where('year', $phone->year)
          ->select('makeid')
          ->distinct()
          ->get()
          ->pluck('makeid')
          ->toArray();

        if($yearItems){

            $message .= "Year selected : $phone->year \n ";
            $message .= "Please Select a your company manufacturer \n ";

            $makeids = Make::whereIn('makeid', $yearItems)->orderBy('company', 'asc')->get();

            foreach($makeids as $make){
                $message .= $make->makeid . " - " . $make->company . " \n ";
            }

            $message .= "Press *f9* to go to previous \n ";
            $message .= "Press *x* to cancel session \n ";

            $phone->stage_model = 'makeFullList';
            $phone->save();
        }
        
        // return $message;
        return $this->sendWhatsAppMessage($message, $from);
    }

    public function makeResponseToModelTable($from, $body)
    {
        $message = null;
        
        $phone = $this->dbSavedRequest($from, $body);

        if($body == 'f8'){
            return $this->makeFullList($from, $body);
        }

        if($body == 'f9'){
            $phone->stage_model = 'yearShortList';
            $phone->year = null;
            $phone->make = null;
            $phone->make_id = null;
            $phone->save();
            $message = $this->yearShortList($from, $body);
            return $this->sendWhatsAppMessage($message, $from);
        }

        $yearItems = Year::where('year', $phone->year)
              ->where('makeid', $body)->first();

        if($yearItems){ 

            $make = Make::where('makeid', $yearItems->makeid)->first();
            $phone->make = $make->company;
            $phone->make_id = $make->makeid;
            $phone->save();

            $message .= $this->modelShortList($from, $body);
            // return $phone;
        }else{
            $message .= "Item Not found \n ";
            $message .= $this->modelShortList($from, $body);
        }
        
        // return $message;
        return $this->sendWhatsAppMessage($message, $from);
    }

    public function modelShortList($from, $body)
    {
        $phone = $this->dbSavedRequest($from, $body);

        $message = null;
        $message .= "Year selected : $phone->year \n ";
        $message .= "Car Manufacturer Selection : $phone->make\n \n";
        $message .= "Please Select the $phone->make Model \n ";

        $items = Year::where('year', $phone->year)
              ->where('makeid', $phone->make_id)
              ->select('modelid')
              ->distinct()
              ->limit(8)
              ->pluck('modelid')
              ->toArray();

        if($items){
            $models = CarModel::whereIn('modelid', $items)->get();

            foreach($models as $model){
                $message .= $model->modelid . " - " . $model->model . " \n ";
            }
            
            $message .= "Please Press *f8* to view full list \n ";
            $message .= "Press *f9* to go to previous \n ";
            $message .= "Press *x* to cancel session \n ";

            $phone->stage_model = 'modelShortList';

            $phone->save();
        } 
        // return $message;
        return $this->sendWhatsAppMessage($message, $from);
    }

    public function modelFullList($from, $body)
    {
        $phone = $this->dbSavedRequest($from, $body);

        $message = null;
        $message .= "Year selected : $phone->year \n ";
        $message .= "Car Manufacturer Selection : $phone->make\n \n";
        $message .= "Please Select the $phone->make Model \n ";

        $items = Year::where('year', $phone->year)
              ->where('makeid', $phone->make_id)
              ->select('modelid')
              ->distinct()
              ->get()
              ->pluck('modelid')
              ->toArray();


        if($items){
            $models = CarModel::whereIn('modelid', $items)->limit(10)->get();

            foreach($models as $model){
                $message .= $model->modelid . " - " . $model->model . " \n ";
            }
            
            $message .= "Press *f9* to go to previous \n ";
            $message .= "Press *x* to cancel session \n ";

            $phone->stage_model = 'modelFullList';

            $phone->save();
        } 

        // return $message;
        return $this->sendWhatsAppMessage($message, $from);
    }

    public function modelResponseToComponentTable($from, $body)
    {
        $message = null;

        $phone = $this->dbSavedRequest($from, $body);

        if($body == 'f8'){
            $message = $this->modelFullList($from, $body);
            return $this->sendWhatsAppMessage($message, $from);
        }

        if($body == 'f9'){
            $phone->stage_model = 'makeShortList';
            $phone->make_id = null;
            $phone->make = null;
            $phone->car_model_id = null;
            $phone->car_model = null;
            $phone->save();
            $message = $this->makeShortTable($from, $body);
            return $this->sendWhatsAppMessage($message, $from);
        }

        if(is_numeric($body)){

            $carModel = CarModel::where('modelid', $body)->first();

            if($carModel == null){
                $message .= "Car models not found \n ";
                $message .= $this->modelShortList($from, $body);
            }

            $phone->car_model_id = $carModel->modelid;
            $phone->car_model = $carModel->model;

            $phone->stage_model = 'componentShortList';

            $phone->save();

            $message .= $this->componentShortList($from, $body);

        }else{
            $message .= "Invalid Input \n ";
            $message .= $this->makeShortTable($from, $body);
        }
        

        return $this->sendWhatsAppMessage($message, $from);
    }

    public function componentShortList($from, $body)
    {
        $message = null;

        $phone = $this->dbSavedRequest($from, $body);

        $message = null;
        $message .= "Year selected : $phone->year \n ";
        $message .= "Car Manufacturer Selection : $phone->make\n";
        $message .= "$phone->make Model Selected $phone->car_model \n ";
        $message .= "Please Select a car component \n ";

        $components = Component::limit(8)->get();

        foreach($components as $component){
            $message .= $component->component_id . " - " . $component->component . " \n ";
        }

        $message .= "Please Press *f8* to view full list \n ";
        $message .= "Press *f9* to go to previous \n ";
        $message .= "Press *x* to cancel session \n ";

        return $this->sendWhatsAppMessage($message, $from);
    }

    public function componentFullList($from, $body)
    {
        $message = null;

        $phone = $this->dbSavedRequest($from, $body);
        $phone->stage_model = 'componentFullList';
        $phone->save();

        $message = null;
        $message .= "Year selected : $phone->year \n ";
        $message .= "Car Manufacturer Selection : $phone->make\n";
        $message .= "$phone->make Model Selected $phone->car_model \n ";
        $message .= "Please Select a car component \n ";

        $components = Component::all();

        foreach($components as $component){
            $message .= $component->component_id . " - " . $component->component . " \n ";
        }

        $message .= "Please Press *f8* to view full list \n ";
        $message .= "Press *f9* to go to previous \n ";
        $message .= "Press *x* to cancel session \n ";

        return $this->sendWhatsAppMessage($message, $from);
    }

    public function componentResponse($from, $body)
    {
        $message = null;

        $phone = $this->dbSavedRequest($from, $body);

        if($body == 'f8'){
            $message =  $this->componentFullList($from, $body);
            return $this->sendWhatsAppMessage($message, $from);
        }

        if($body == 'f9'){
            $phone->stage_model = 'modelShortList';
            $phone->car_model_id = null;
            $phone->car_model = null;
            $phone->save();
            $message = $this->modelShortList($from, $body);
            return $this->sendWhatsAppMessage($message, $from);
        }

        if(is_numeric($body)){
            $component = Component::where('component_id', $body)->first();

            if($component == null){
                $message .= "Car component not found \n ";
                $message .= $this->componentShortList($from, $body);
            }

            $phone->component_id = $component->component_id;
            $phone->component = $component->component;
            $phone->terminate = true;
            $phone->finished = true;
            $phone->save();

            $message .= "Thank you!! Your request has been received. Our agent would contact you in 1 business day \n \n";
            $message .= "Car Specification - $phone->year $phone->make $phone->car_model - $phone->component \n ";

        }else{
            $message .= "Invalid Input \n ";
            $message .= $this->componentShortList($from, $body);
        }
        
        // return $message;
        return $this->sendWhatsAppMessage($message, $from);
    }

    public function chatModel($from, $body)
    {
        $array1 = explode(" ", $body);

        $chats = Chat::all();

        $replies = [];

        foreach($chats as $chat){

            $array2 = explode(" ", strtolower($chat['incoming_message']));

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
            // $newChat = new Chat;
            // $newChat->incoming_message = $body;
            // $newChat->phone = $from;
            // $newChat->save();

            $message = "*Welcome To AutoPartz!!!*\n";
            $message .= "I am here to assist you\n";
            $message .= "Please kindly press *menu* to access our support features\n";

            // return $message;
            return $this->sendWhatsAppMessage($message, $from);
        }

        $maximum_number = (max(array_column($replies, "average")));

        $message = $this->arraySearch($replies, "average", $maximum_number);

        return $message['reply'];
    }

    public function arraySearch($products, $field, $value)
    {
       foreach($products as $key => $product)
       {
            if ( $product[$field] === $value ){
                return($product);
            }

            return false;
       }
       return false;
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
