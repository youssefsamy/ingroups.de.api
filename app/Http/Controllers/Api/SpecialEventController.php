<?php

namespace App\Http\Controllers\Api;

use App\Entities\Event;
use App\Entities\Respond;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Entities\Event_location;
use App\Entities\Location;
use App\Entities\Locality;

class SpecialEventController extends Controller {

    public function getSpecialEvent(Request $request) {

        $user = Auth::user();
        $input = $request->input();

        $categoryFilterInput = $input['categoryFilter'];
        $cityFilterInput = $input['cityFilter'];
        $dateFilter = $input['dateFilter'];
        $numberFilterInput = $input['numberFilter'];
        $createrId = $input['createrId'];

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

        $events = $events->where('events.createrId', $createrId)
                        ->orderBy('events.specialEvent', 'desc')
                        ->orderBy('events.Timestamp', 'desc')
                        ->groupBy('events.id')
                        ->get();

        $event_ort      = array();
        $upperLimit     = array();
        $contact_count  = array();
        $location_ids   = array();

        foreach($events as $key => $event)
        {
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

            $event_ort      = array();
            $upperLimit     = array();
            $contact_count  = array();
            $location_ids   = array();
        }
        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    public function updateTelefonnummer(Request $request) {
        $event = Event::where('id', $request->input('id'))->first();
        $event->Telefonnummer = $request->input('Telefonnummer');
        $event->update();
        return response()->json([
            'success' => true
        ]);
    }

    public function updateUpperlimit(Request $request) {
        $event = Event_location::where('id', $request->input('id'))->first();
        $event->upperlimit = $request->input('upperlimit');
        $event->update();
        return response()->json([
            'success' => true
        ]);
    }


    /*
    * @ function name : getAllLocations()
    * @ Description   : Get All location using this method
    * @ Created_by    : DB
    * @ Created_On    : 10-Aug-2018
    * @ Return        : JSON
    */
    public function getAllLocations() {
        $locations = Location::get();
        return response()->json([
            'success' => true,
            'data' => $locations
        ]);
    }

    /*
    * @ function name : getLocalityByLocationId()
    * @ Description   : Get All locality using this method
    * @ Created_by    : DB
    * @ Created_On    : 25-Aug-2018
    * @ Return        : JSON
    */
    public function getAllLocality(Request $request) {
        $localities = Locality::get();
        return response()->json([
            'success' => true,
            'data' => $localities
        ]);
    }

    /*
    * @ function name : getLocalityById()
    * @ Description   : Get locality by id
    * @ Created_by    : DB
    * @ Created_On    : 25-Aug-2018
    * @ Return        : JSON
    */
    public function getLocalityById(Request $request) {
        $localities = Locality::whereIn('id', $request->input('id'))->get();
        return response()->json([
            'success' => true,
            'data' => $localities
        ]);
    }
}