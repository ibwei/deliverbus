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


    public function saveFormId(Request $request)
    {
        $res = DB::table('formid')->insert(
            ['user_id' => $request->user_id, 'form_id' => $request->form_id]
        );
        if ($res) {
            return 1;
        } else {
            return 0;
        }
    }

    public function payok(Request $request)
    {
        $config = config('wechat.mini_program.default');
        $app = Factory::miniProgram($config);
        $ticket = new Ticket;
        $trade_sn = $request->trade_sn;
        $ticket->trade_sn = $trade_sn;
        $result = \App\Models\Ticket::where('trade_sn', $trade_sn)
            ->update(['status' => 2]);

        if ($result) {
            $result = \App\Models\Ticket::where('trade_sn', $trade_sn)
                ->update(['status' => 2]);

            $r = DB::table('ticket')
                ->join('users', 'users.id', '=', 'ticket.user_id')
                ->join('bus', 'bus.id', '=', 'ticket.bus_id')
                ->join('addresses', 'addresses.id', '=', 'ticket.address_id')
                ->select('openid', 'ticket.price AS price', 'bus.driver_line AS driver_line', 'bus.end_time AS end_time', 'bus.start_time AS start_time', 'addresses.address AS address')
                ->where([
                    ['trade_sn', '=', $trade_sn]
                ])->get();

            //发送模板消息
            $app->template_message->send([
                'touser' => $r[0]->openid,
                'template_id' => 'caj3DZ8ycs0IjIbOZ7HX6Lj1fWXMISoCHE-yuMd4t60',
                'page' => '/pages/myticket/index',
                'form_id' => $request->formId,
                'data' => [
                    'keyword1' => $r[0]->price,
                    'keyword2' => '代领服务',
                    'keyword3' => $r[0]->driver_line,
                    'keyword4' => $r[0]->start_time,
                    'keyword5' => $r[0]->end_time,
                    'keyword6' => $r[0]->address
                ],
            ]);
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
            ->join('drivers', 'drivers.id', '=', 'bus.driver_id')
            ->select('ticket.id as id', 'ticket.price', 'ticket.trade_sn', 'ticket.type', 'ticket.deliver_number', 'ticket.express_name', 'consignee_name', 'ticket.status', 'ticket.received', 'driver_line', 'sites.name AS start_site', 'end_site', 'start_time', 'end_time', 'bus.status', 'drivers.name', 'drivers.tel')
            ->where([
                ['ticket.user_id', '=', $user_id],
                ['ticket.status', '=', 2]
            ])
            ->orderBy('trade_sn', 'desc')
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
