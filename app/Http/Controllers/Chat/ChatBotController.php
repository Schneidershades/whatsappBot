<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Twilio\Rest\Client;
use App\Models\Chat;

class ChatBotController extends Controller
{   
    public function index()
    {
        return Chat::all();
    }

    public function store(Request $request)
    {
        $newChat = new Chat;
        $newChat->incoming_message = $request->in;
        $newChat->outgoing_message = $request->out;
        $newChat->save();

        return "saved";
    }


    public function show()
    {

    }

    public function update(Request $request, $id)
    {
        $chat = Chat::find($id);
        $chat->outgoing_message = $request->in;
        $chat->save();
        return $chat;
    }

    public function destroy($id)
    {
        $chat = Chat::find($id);
        $chat->delete();
        return 'deleted';
    }

}
