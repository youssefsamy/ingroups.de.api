<?php

namespace App\Http\Controllers\Api;

use App\Entities\Event;
use App\Entities\Respond;
use App\Entities\User_location;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\User;

class AccountController extends Controller {

    public function getAccount(Request $request) {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'birthday' => $user->birthday,
            'site' => $user->site,
            'address' => $user->address,
            'phone' => $user->phone,
            'gender' => $user->gender,
            'photo' => $user->photo,
            'verified' => $user->verified,
        ]);
    }

    public function getProfile(Request $request) {
        $user = Auth::user();
        
        $user_location_id = array();
        $user_location_locality_id = array();
        
        $user_location = User_location::where('user_id',$user->id)->first();

        if(!empty($user_location)){
            $user_location_id = explode(',',$user_location->location_id);
            $user_location_locality_id = explode(',',$user_location->location_locality_id);
            $user['location_id'] = $user_location_id;
            $user['location_locality_id'] = $user_location_locality_id;
        }else{
            $user['location_id'] = $user_location_id;
            $user['location_locality_id'] = $user_location_locality_id;
        }
        
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function saveProfile(Request $request) {
        $input = $request->input();

        $user = Auth::user();

        $user = User::where('id', $user->id)->first();

        $user->name = $input['name'];
        $user->phone = $input['phone'];
        $user->address = $input['address'];
        $user->site = $input['site'];
        $user->birthday = $input['birthday'];
        $user->gender = $input['gender'];
        $user->photo = $input['photo'];
        if ($input['photo'] == '') $user->photo = env('APP_URL').'/storage/profile.jpg';

        $user->save();

        return response()->json([
            'success' => true,
            'data' => $user
        ]);

    }

    public function getContactList(Request $request) {
        $user = Auth::user();

        $data = array();

        $query1 = "SELECT responds.*,contacts.created_at, IFNULL(contacts.counts, 0) unread, events.Titel as eventName, events.specialEvent as specialEvent, users.name as partnerName, users.id as partnerId, responds.Bild as partnerPhoto, users.birthday as partnerBirthday FROM responds".
                " LEFT JOIN events ON responds.eventId = events.id".
                " LEFT JOIN users ON responds.contacterId = users.id".
                " LEFT JOIN (SELECT contactId,created_at, COUNT(contactId) counts FROM messages WHERE `read` = 0 and `to` = ".$user->id." GROUP BY contactId) AS contacts on responds.id = contacts.contactId".
                " WHERE responds.createrId = ".$user->id.
                " ORDER BY contacts.created_at DESC ";

        $result1 = \DB::select($query1);

        $query2 = "SELECT responds.*,contacts.created_at, IFNULL(contacts.counts, 0) unread, events.Titel as eventName, events.specialEvent as specialEvent, users.name as partnerName, users.id as partnerId, responds.createrImage as partnerPhoto, users.birthday as partnerBirthday FROM responds".
            " LEFT JOIN events ON responds.eventId = events.id".
            " LEFT JOIN users ON responds.createrId = users.id".
            " LEFT JOIN (SELECT contactId,created_at, COUNT(contactId) counts FROM messages WHERE `read` = 0 and `to` = ".$user->id." GROUP BY contactId) AS contacts on responds.id = contacts.contactId".
            " WHERE responds.contacterId = ".$user->id.
            " ORDER BY contacts.created_at DESC ";

        $result2 = \DB::select($query2);

        return response()->json([
            'success' => true,
            'data' => array_merge($result1, $result2)
        ]);
    }
}