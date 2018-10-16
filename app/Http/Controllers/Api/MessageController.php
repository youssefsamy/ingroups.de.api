<?php

namespace App\Http\Controllers\Api;

use App\Entities\Event;
use App\Entities\Message;
use App\Entities\Respond;
use App\Events\MessageEvent;
use App\Http\Controllers\Controller;
use App\Mail\NewEventMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\User;

class MessageController extends Controller {
    public function sendMessage(Request $request) {
        $input = $request->input();

        $user = Auth::user();

        $message = new Message();

        $message->contactId = $input['contactId'];
        $message->eventId = $input['eventId'];
        $message->from = $user->id;
        $message->to = $input['to'];
        $message->type = 'text';
        $message->message = $input['message'];

        $message->save();



        event(new MessageEvent($message));

        return response()->json([
            'success' => true,
            'data' => $message
        ]);
    }

    public function loadMessage(Request $request) {
        $input = $request->input();

        $messages = Message::where('contactId', $input['contactId'])->get();

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    public function readMessage(Request $request) {
        $input = $request->input();

        $user = Auth::user();

        Message::where([['contactId', $input['contactId']], ['to', $user->id]])->update(['read' => 1]);

        return response()->json([
            'success' => true,
        ]);
    }
}