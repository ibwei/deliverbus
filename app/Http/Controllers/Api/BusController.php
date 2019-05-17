<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Client;
use Socialite;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client as HttpClient;
use App\User;
use Illuminate\Http\Request;
use Validator;
use EasyWeChat\Factory;
use Ucpaas;
use App\Models\School;
use App\Models\Bus;
use App\Models\Ticket;

class BusController extends ApiController
{

    use AuthenticatesUsers;


    public function newBus(Request $request)
    {
        $bus = new \App\Models\Bus;
        $bus->driver_id = $request->driver_id;
        $bus->school_id = $request->school_id;
        $bus->driver_line = $request->driver_line;
        $bus->site_id = $request->site_id;
        $bus->end_site = $request->end_site;
        $bus->small_price = $request->small_price;
        $bus->normall_price = $request->normall_price;
        $bus->big_price = $request->big_price;
        $bus->small_count = $request->small_count;
        $bus->normall_count = $request->normall_count;
        $bus->big_count = $request->big_count;
        $bus->note = "none";

        $start = strtotime(date('Y-m-d' . $request->start_time . ':00', time()));
        $end = strtotime(date('Y-m-d' . $request->end_time . ':00', time()));
        $start_time = date("Y-m-d H:i:s", $start);
        $end_time = date("Y-m-d H:i:s", $end);
        $bus->start_time = $start_time;
        $bus->end_time = $end_time;
        if ($bus->save()) {
            return 1;
        } else {
            return 0;
        }
    }

    //按起点和重点查询  待优化
    public function searchBus1(Request $request)
    {
        $start = strtotime(date('Y-m-d' . '00:00:00', time()));
        $end = strtotime(date('Y-m-d' . '00:00:00', time() + 3600 * 24));
        $start_time = date("Y-m-d H:i:s", $start);
        $end_time = date("Y-m-d H:i:s", $end);
        $start_site = $request->start_site;
        $end_site = '%' . $request->end_site . '%';
        $school_id = $request->school_id;
        $limit = $request->limit;
        $busList = DB::table('bus')
            ->join('drivers', 'drivers.id', '=', 'bus.driver_id')
            ->join('schools', 'schools.id', '=', 'bus.school_id')
            ->join('sites', 'sites.id', '=', 'bus.site_id')
            ->select('bus.id AS id', 'driver_line', 'sites.name AS start_site', 'end_site', 'small_price', 'small_count', 'big_price', 'big_count', 'normall_count', 'normall_price', 'note', 'start_time', 'end_time')
            ->where([
                ['sites.name', '=', $start_site],
                ['schools.id', '=', $school_id],
                ['end_site', 'like', $end_site]
                // ['start_time', '>=', $start_time],
                // ['end_time', '<=', $end_time]
            ])
            ->orderBy('start_time')
            ->limit($limit)
            ->get();
        return $this->returnBusList($busList, $limit);
    }

    //找出已有票数,合并查询,并输出
    public function returnBusList($busList, $limit)
    {
        if (count($busList)) {
            foreach ($busList as $bus) {
                $busIdList[] = $bus->id;
            }
            $small_already_List = DB::table('bus')
                ->join('ticket', 'ticket.bus_id', '=', 'bus.id')
                ->select('ticket.bus_id', DB::raw('count("type") as small_already_person'))
                ->whereIn('ticket.bus_id', $busIdList)
                ->where('type', '=', 1)
                ->groupBy('ticket.bus_id')
                ->orderBy('ticket.bus_id')
                ->limit($limit)
                ->get();
            $normall_already_List = DB::table('bus')
                ->join('ticket', 'ticket.bus_id', '=', 'bus.id')
                ->select('ticket.bus_id', DB::raw('count("type") as normall_already_person'))
                ->whereIn('ticket.bus_id', $busIdList)
                ->where('type', '=', 2)
                ->groupBy('ticket.bus_id')
                ->orderBy('ticket.bus_id')
                ->limit($limit)
                ->get();
            $big_already_List = DB::table('bus')
                ->join('ticket', 'ticket.bus_id', '=', 'bus.id')
                ->select('ticket.bus_id', DB::raw('count("type") as big_already_person'))
                ->whereIn('ticket.bus_id', $busIdList)
                ->where('type', '=', 3)
                ->groupBy('ticket.bus_id')
                ->orderBy('ticket.bus_id')
                ->limit($limit)
                ->get();
            for ($i = 0; $i < count($busList); $i++) {
                if (isset($small_already_List[$i]->small_already_person)) {
                    $busList[$i]->small_already_person = $small_already_List[$i]->small_already_person;
                } else {
                    $busList[$i]->small_already_person = 0;
                }
                if (isset($normall_already_List[$i]->normall_already_person)) {
                    $busList[$i]->normall_already_person = $normall_already_List[$i]->normall_already_person;
                } else {
                    $busList[$i]->normall_already_person = 0;
                }
                if (isset($big_already_List[$i]->big_already_person)) {
                    $busList[$i]->big_already_person = $big_already_List[$i]->big_already_person;
                } else {
                    $busList[$i]->big_already_person = 0;
                }
            }
            return json_encode($busList);
        } else {
            return 0;
        }
    }

    public function searchBus2(Request $request)
    {
        $start = strtotime(date('Y-m-d' . '00:00:00', time()));
        $end = strtotime(date('Y-m-d' . '00:00:00', time() + 3600 * 24));
        $start_time = date("Y-m-d H:i:s", $start);
        $end_time = date("Y-m-d H:i:s", $end);
        $school_id = $request->school_id;
        $limit = $request->limit;
        $busList = DB::table('bus')
            ->join('drivers', 'drivers.id', '=', 'bus.driver_id')
            ->join('schools', 'schools.id', '=', 'bus.school_id')
            ->join('sites', 'sites.id', '=', 'bus.site_id')
            ->select('bus.id AS id', 'driver_line', 'sites.name AS start_site', 'end_site', 'small_price', 'small_count', 'big_price', 'big_count', 'normall_count', 'normall_price', 'note', 'start_time', 'end_time')
            ->where([
                ['schools.id', '=', $school_id]
                //['start_time', '>=', $start_time],
                //['end_time', '<=', $end_time]
            ])
            ->orderBy('start_time')
            ->limit($limit)
            ->get();
        return $this->returnBusList($busList, $limit);
    }

    public function searchBus3(Request $request)
    {
        $start = strtotime(date('Y-m-d' . '00:00:00', time()));
        $end = strtotime(date('Y-m-d' . '00:00:00', time() + 3600 * 24));
        $start_time = date("Y-m-d H:i:s", $start);
        $end_time = date("Y-m-d H:i:s", $end);
        $start_site = $request->start_site;
        $school_id = $request->school_id;
        $limit = $request->limit;
        $busList = DB::table('bus')
            ->join('drivers', 'drivers.id', '=', 'bus.driver_id')
            ->join('schools', 'schools.id', '=', 'bus.school_id')
            ->join('sites', 'sites.id', '=', 'bus.site_id')
            ->select('bus.id AS id', 'driver_line', 'sites.name AS start_site', 'end_site', 'small_price', 'small_count', 'big_price', 'big_count', 'normall_count', 'normall_price', 'note', 'start_time', 'end_time')
            ->where([
                ['sites.name', '=', $start_site],
                ['schools.id', '=', $school_id]
                // ['start_time', '>=', $start_time],
                //['end_time', '<=', $end_time]
            ])
            ->orderBy('start_time')
            ->limit($limit)
            ->get();
        return $this->returnBusList($busList, $limit);
    }

    /*
     *
     * timeInternal: [{
                index: 1,
                time: '08:00-12:00'
            }, {
                index: 2,
                time: '12:00-16:00'
            }, {
                index: 3,
                time: '16:00-20:00'
            }, {
                index: 4,
                time: '20:00-24:00'
            }],
     */
    public function searchBus4(Request $request)
    {
        $index = $request->time_index;
        $time = ["08:00:00", "12:00:00", "16:00:00", "20:00:00", "24:00:00"];
        $start = strtotime(date('Y-m-d' . $time[$index], time()));
        $end = strtotime(date('Y-m-d' . $time[$index + 1], time()));
        $start_time = date("Y-m-d H:i:s", $start);
        $end_time = date("Y-m-d H:i:s", $end);
        $school_id = $request->school_id;
        $limit = $request->limit;
        $busList = DB::table('bus')
            ->join('drivers', 'drivers.id', '=', 'bus.driver_id')
            ->join('schools', 'schools.id', '=', 'bus.school_id')
            ->join('sites', 'sites.id', '=', 'bus.site_id')
            ->select('bus.id AS id', 'driver_line', 'sites.name AS start_site', 'end_site', 'small_price', 'small_count', 'big_price', 'big_count', 'normall_count', 'normall_price', 'note', 'start_time', 'end_time')
            ->where([
                ['schools.id', '=', $school_id]
                // ['start_time', '>=', $start_time],
                // ['end_time', '<=', $end_time]
            ])
            ->orderBy('start_time')
            ->limit($limit)
            ->get();
        return $this->returnBusList($busList, $limit);
    }
}
