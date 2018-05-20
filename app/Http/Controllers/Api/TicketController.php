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
use App\Models\Ticket;

class TicketController extends ApiController
{

    use AuthenticatesUsers;

    public function pay(Request $request)
    {
        //验证
        $trade_sn = date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $ticket = new Ticket;
        $ticket->bus_id = $request->bus_id;
        $ticket->user_id = $request->user_id;
        $ticket->address_id = $request->address_id;
        $ticket->type = $request->type;
        $ticket->price = $request->price;
        $ticket->trade_sn = $trade_sn;
        $ticket->pay_sn = $trade_sn;
        $ticket->deliver_number = $request->deliver_number;
        $ticket->express_name = $request->express_name;
        $ticket->consignee_name = $request->consignee_name;
        $ticket->consignee_tel = $request->consignee_tel;
        $ticket->pay_date = date('Y-m-d H:i:s');
        if ($ticket->save()) {
            return $trade_sn;
        } else {
            return 0;
        }
    }

    public function payok(Request $request)
    {
        $ticket = new Ticket;
        $trade_sn = $request->trade_sn;
        $ticket->trade_sn = $trade_sn;
        $result = \App\Models\Ticket::where('trade_sn', $trade_sn)
            ->update(['status' => 2]);
        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getTicket(Request $request)
    {
        $user_id = $request->user_id;
        $busList = DB::table('ticket')
            ->join('bus', 'bus.id', '=', 'ticket.bus_id')
            ->join('schools', 'schools.id', '=', 'bus.school_id')
            ->join('sites', 'sites.id', '=', 'bus.site_id')
            ->join('addresses', 'addresses.id', '=', 'ticket.address_id')
            ->select('ticket.id as id', 'ticket.price', 'ticket.trade_sn','ticket.type', 'ticket.deliver_number', 'ticket.express_name', 'consignee_name', 'ticket.status', 'ticket.received', 'driver_line', 'sites.name AS start_site', 'end_site', 'start_time', 'end_time','bus.status')
            ->where([
                ['ticket.user_id', '=', $user_id],
                ['ticket.status', '=', 2]
            ])
            ->orderBy('trade_sn','desc')
            ->get();
        return json_encode($busList);
    }

    public function confirmReceived(Request $request)
    {

        $ticket = new Ticket;
        $trade_sn = $request->trade_sn;
        $ticket->trade_sn = $trade_sn;
        $result = \App\Models\Ticket::where('trade_sn', $trade_sn)
            ->update(['received' => 1]);
        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }
}



