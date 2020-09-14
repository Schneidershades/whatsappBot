<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Twilio\Rest\Client;
use App\Models\Chat;


class ChatBotController extends Controller
{
    public function listenToReplies(Request $request)
    {
        $from = $request->input('From');
        $body = $request->input('Body');

        $client = new \GuzzleHttp\Client();

        $array1 = explode(" ", $body);

        $chats = Chat::all();

        $replies = [];

        foreach($chats as $chat){

            $array2 = explode(" ", $chat['incoming_message']);

            $similar = array_intersect($array1, $array2);

            $a = round(count($similar));

            $b = count($array1);

            $average = $a/$b*100;

            if($average > 60 && $chat['outgoing_message'] != null){
                $newdata = array (
                    'average' => $average,
                    'reply' => $chat['outgoing_message']
                );

                array_push($replies, $newdata);
            }
        }

        if($replies == null || $replies == []){
            $newChat = new Chat;
            $newChat->incoming_message = $body;
            $newChat->phone = $from;
            $newChat->save();

            return $this->sendWhatsAppMessage('I am sorry!! I seem not to understand you', $from);
        }

        $maximum_number = (max(array_column($replies, "average")));

        $message = $this->arraySearch($replies, "average", $maximum_number);

        

        return $this->sendWhatsAppMessage($message['outgoing_message'], $from);
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
