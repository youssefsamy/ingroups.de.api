<?php

namespace App\Http\Controllers\Api;

use App\Entities\Event;
use App\Entities\Respond;
use App\Events\MessageEvent;
use App\Entities\Message;
use App\Http\Controllers\Controller;
use App\Mail\ContactMail;
use App\Mail\NewEventMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Entities\Event_location;
use App\Entities\Event_location_locality;

class EventController extends Controller {

    public function createEvent(Request $request) {

        $user = Auth::user();

        $input = $request->input();

        $event = new Event();

        if(!empty($input['contact_email'])){
            $user_email = $input['contact_email'];
        }else{
            $user_email = $user->email;
        }

        if(!empty($input['position'])){
            $position = $input['position'];
        }else{
            $position = 0;
        }

        
        if($input['number'] > 0){
            $number = $input['number'];
            if($user->businessUser) {
                $upperlimit = $input['number'];
            }else{
                $upperlimit = -1;
            }            
        }else{
            $upperlimit = -1;
            $number = 0;
        }

        if($user->businessUser){
            $specialEvent = 1;
        }else{
            $specialEvent = 0;
        }

        $event->Titel = $input['title'];
        $event->Datum = $input['date'];
        $event->time = $input['time'];
        $event->Kontaktemail = $user_email;
        $event->Telefonnummer = $input['phonenumber'];
        $event->Ort = NULL;
        $event->category = $input['category'];
        $event->Anzahl = $number;
        $event->Beschreibung = $input['description'];
        $event->Aktiviert = 1;
        $event->position = $position;
        $event->image = $input['image'];
        $event->specialEvent = $specialEvent;
        $event->createrId = $user->id;

        $confirm_code = str_random(30);

        Mail::to($user_email)->send(new NewEventMail('', $event));

        $event->confirm_code = $confirm_code;
        $event->save();

        foreach ($input['location'] as $key => $place) 
        {
            $event_location = new Event_location();
            $event_location->location           = $place['location'];
            $event_location->location_id        = $place['id'];
            $event_location->eventId            = $event->id;
            $event_location->upperlimit         = $upperlimit;
            $event_location->created_date       = date("Y-m-d H:i:s");
            $event_location->modified_date      = date("Y-m-d H:i:s");
            $event_location->save();
        }

        foreach ($input['locality'] as $key => $area) 
        {
            $event_location_locality                        = new Event_location_locality();
            $event_location_locality->locality              = $area['locality'];
            $event_location_locality->location_id           = $area['location_id'];
            $event_location_locality->location_locality_id  = $area['id'];
            $event_location_locality->event_id              = $event->id;
            $event_location_locality->save();
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function loadEvent(Request $request) {

        $user = Auth::user();

        $input = $request->input();

        $categoryFilterInput = $input['categoryFilter'];
        $cityFilterInput = $input['cityFilter'];
        $dateFilter = $input['dateFilter'];
        $numberFilterInput = $input['numberFilter'];

        $categoryFilter = [];
        $cityFilter = [];
        $numberFilter = [];

        foreach ($categoryFilterInput as $category) {
            array_push($categoryFilter, $category['name']);
        }

        foreach ($cityFilterInput as $city) {
            array_push($cityFilter, $city['name']);
        }

        foreach ($numberFilterInput as $number) {
            array_push($numberFilter, $number['number']);
        }

        $events = DB::table('events')
                ->select('events.id','events.*','event_location.location','event_location.end','event_location.contact_count')
                ->join('event_location', 'events.id', '=', 'event_location.eventId')
                ->where('events.Aktiviert', 1)
                ->where('event_location.end','!=',1)
                ->whereDate('events.Datum', '>=', date('Y-m-d'));

        if (count($categoryFilter) > 0) {
            $events = $events->whereIn('events.category', $categoryFilter);
        }

        if (count($cityFilter) > 0) {
            $events = $events->whereIn('event_location.location', $cityFilter);
        }

        if (count($numberFilter) > 0) {
            $events = $events->whereIn('events.Anzahl', $numberFilter);
        }

        if ($dateFilter != '') {
            $events = $events->where('events.Datum', $dateFilter);
        }

        $events = $events->orderBy('events.Timestamp', 'desc')
                        ->groupBy('events.id')
                        ->get();        
        
        foreach($events as $key => $event)
        {
            $event_ort      = array();
            $upperLimit     = array();
            $contact_count  = array();
            $location_ids   = array();

            $event_loc = DB::table('event_location')
                        ->where('eventId', '=', $event->id)
                        ->where('end', '!=', 1)
                        ->get();
            foreach($event_loc as $loc)
            {
                array_push($event_ort,$loc->location);
                array_push($upperLimit, $loc->upperlimit);
                array_push($contact_count, $loc->contact_count);
                array_push($location_ids, $loc->id);
            }
            $event_place                    = implode(',',$event_ort);
            $location_upperlimit            = implode(',', $upperLimit);
            $event_location_contact_count   = implode(',', $contact_count);
            $event_location_ids             = implode(',', $location_ids);

            $events[$key]->Ort                          = $event_place;
            $events[$key]->event_location_upperlimit    = $location_upperlimit;
            $events[$key]->event_location_contact_count = $event_location_contact_count;
            $events[$key]->event_location_id            = $event_location_ids;
        }

        $pos2 = 0;
        $pos5 = 0;
        $position2 = array();
        $position5 = array();
        $data_array = array();
        $total_data_count = count($events);
        if($total_data_count >= 5){
            foreach($events as $event_key => $event) {
                if($event->position == 2 && $pos2 != 1) {
                //Get latest event for position2
                $position2 = $event;
                $pos2 = 1;
                } else if($event->position == 5 && $pos5 != 1) {
                    //Get latest event for position5
                    $position5 = $event;
                    $pos5 = 1;
                } else {
                    $data_array[] = $event;
                }
            }            
        }else if($total_data_count <= 4 && $total_data_count >= 2){
            foreach($events as $event_key => $event) {
                if($event->position == 2 && $pos2 != 1) {
                //Get latest event for position2
                $position2 = $event;
                $pos2 = 1;
                } else {
                    $data_array[] = $event;
                }
            }
        } else {
            $data_array[] = $event;
        }

        $final_array = array();
        $index_count = 0;
        foreach($data_array as $final_key => $final)
        {
            if($total_data_count == 2 && $pos2 == 1 && $final_key == 0){
                // For swap position 2 to position 3
                $final_array[$index_count] = $final;
                $index_count++;
                // For position 2
                $final_array[$index_count] = $position2;
                $index_count++;                
            } else if($pos2 == 1 && $final_key == 1){
                // For position 2
                $final_array[$index_count] = $position2;
                $index_count++;
                // For swap position 2 to position 3
                $final_array[$index_count] = $final;
                $index_count++;
            }
            else if($pos2 == 1 && $pos5 == 1 && $final_key == 3){
                // For position 5
                $final_array[$index_count] = $position5;
                $index_count++;
                // For swap position 4 to position 5
                $final_array[$index_count] = $final;
                $index_count++;
            }
            else if($pos2 != 1 && $pos5 == 1 && $final_key == 4){
                // For position 5
                $final_array[$index_count] = $position5;
                $index_count++;
                // For swap position 4 to position 5
                $final_array[$index_count] = $final;
                $index_count++;
            }
            else{
                $final_array[$index_count] = $final;
                $index_count++;
            }          
        }
        
        return response()->json([
            'success' => true,
            'data' => $final_array
        ]);
    }

    public function createContact(Request $request) {

        $user = Auth::user();
        $input = $request->input();
        $creater = Event::where('id', $input['eventId'])->first();

        $count = count(Respond::where([['contacterId', $user->id], ['eventId', $input['eventId']], ['createrId', $creater->createrId]])->get());

        if ($count > 0) {
            return response()->json([
                'success' => false,
                'error' => 'Du hast dich bereits auf dieses Event gemeldet'
            ]);
        }

        $respond = new Respond();

        $respond->contacterId = $user->id;
        $respond->Nachricht = $input['message'];
        $respond->eventId = $input['eventId'];
        $respond->Bild = $input['image'];
        $respond->location = $input['selectedLocation'];
        if ($input['image'] == '') $respond->Bild = $user->photo;
        $respond->createrId = $creater->createrId;
        $respond->createrImage = $input["createrImage"];
        $respond->event_location_id = $input["event_location_id"];

        Mail::to($user['email'])->send(new ContactMail('', $user, $respond));          //// send contact mail to user

        $respond->save();


        $message = new Message();

        $message->contactId = $respond->id;
        $message->eventId = $input['eventId'];
        $message->from = $user->id;
        $message->to = $creater->createrId;
        $message->type = 'text';
        $message->message = $input['message'];

        $message->save();



        event(new MessageEvent(array(
            'from' => $user->id,
            'eventId' => $input['eventId'],
            'type' => 'event_contact',
            'message' => $input['message']
        )));


        $event_location = Event_location::where('id', $input['event_location_id'])->first();

        $event_location->contact_count++;
        $event_location->save();



        return response()->json([
            'success' => true,
            'id' => $respond->id
        ]);
    }

    public function contact2User(Request $request) {

        $user = Auth::user();
        $input = $request->input();

        $count = count(Respond::where([['contacterId', $user->id], ['eventId', $input['eventId']], ['createrId', $input['createrId']]])->get());

        if ($count > 0) {
            return response()->json([
                'success' => false,
                'error' => 'Du hast diesem Nutzer bereits geschrieben'
            ]);
        }

        $respond = new Respond();

        $respond->contacterId = $user->id;
        $respond->Nachricht = $input['message'];
        $respond->eventId = $input['eventId'];
        $respond->Bild = $input['image'];
        $respond->location = $input['location'];
        if ($input['image'] == '') $respond->Bild = $user->photo;
        $respond->createrId = $input['createrId'];
        $respond->createrImage = $input["createrImage"];

        $respond->save();



        $message = new Message();

        $message->contactId = $respond->id;
        $message->eventId = $input['eventId'];
        $message->from = $user->id;
        $message->to = $input['createrId'];
        $message->type = 'text';
        $message->message = $input['message'];

        $message->save();



        return response()->json([
            'success' => true
        ]);
    }

    public function verifyNewEvent($confirm_code, Request $request) {

        $count = Event::where('confirm_code', $confirm_code)->count();

        if ($count == 0) {
            return 'Invalid code';
        } else {
            Event::where('confirm_code', $confirm_code)->update(['Aktiviert' => 1]);

            return ("<script>location.href = '".env('CLIENT_URL')."'</script>");
        }
    }

    public function endEvent(Request $request) {

        $event = Event_location::where('id', $request->input('id'))->first();
        $event->end = 1;
        $event->update();

        return response()->json([
            'success' => true
        ]);
    }

    public function getContactListByEvent(Request $request) {
        $input = $request->input();
        $creater = Event::where('id', $input['eventId'])->first();

        $contacts = DB::table('responds')->leftJoin('users', 'responds.contacterId', '=', 'users.id')->where([['responds.eventId', '=', $input['eventId']], ['responds.createrId', '=', $creater->createrId]])
        ->select('responds.*', 'users.name', 'users.birthday')->get();

        return response()->json([
            'success' => true,
            'data' => $contacts 
        ]);
    }

    /*
    * @ function name : getMyEvents()
    * @ Description   : Get loggin user's event for different locations
    * @ Created_by    : DB
    * @ Created_On    : 31-jul-2018
    */
    public function getMyEvents()
    {
        $events_location = DB::table('events')
                        ->join('event_location', 'events.id', '=', 'event_location.eventId')
                        ->where('events.createrId',Auth::user()->id)
                        ->whereDate('events.Datum', '>=', date('Y-m-d'))
                        ->select('events.*', 'event_location.location',
                        'event_location.id as event_location_id',
                        'event_location.end as event_location_end', 
                        'event_location.upperlimit as event_location_upperlimit')
                        ->get();

        return response()->json([
            'success' => true,
            'data' => $events_location 
        ]);
    }
}
