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
                ['end_site', 'like', $end_site],
                ['start_time', '>=', $start_time],
                ['end_time', '<=', $end_time]
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
                ['schools.id', '=', $school_id],
                ['start_time', '>=', $start_time],
                ['end_time', '<=', $end_time]
            ])
            ->orderBy('start_time')
            ->limit($limit)
            ->get();
        return $this->returnBusList($busList, $limit);

    }
    //获取用户的个人信息
//    public function getUserInfo(Request $request)
//    {
//        $data = json_decode(request('data'));
//        $id = $data->uid;
//        $data = \App\User::select('avatar', 'nickname', 'point', 'level')->find($id);
//        $role = ["白银", "黄金", "铂金", "钻石"];
//        $data["identy"] = $role[$data["level"]];
//        $data["groupnum"] = DB::table('group_menbers')->select('user_id')->where('user_id', $id)->count();
//        return $data;
//    }
//
//    //获取用户的所在与参与的团
//    public function getMyGroup(Request $request)
//    {
//        $para = json_decode(request('data'));
//        $id = $para->uid;
//        $groupList = DB::table('groups')
//            ->join('goods', 'groups.goods_id', '=', 'goods.id')
//            ->join('group_menbers', 'group_menbers.group_id', '=', 'groups.id')
//            ->join('users', 'group_menbers.user_id', 'users.id')
//            ->where('group_menbers.user_id', $id)
//            ->select('goods.*', 'users.id', 'group_menbers.*', 'groups.*')
//            ->get();
//        $arr = [];
//        foreach ($groupList as $eachGroup) {
//            $eachGroup->cat_id = $eachGroup->class_id;
//            $eachGroup->order_img = 'http://pintuan.com/uploads/' . $eachGroup->goods_logo;
//            $eachGroup->goods_amount = $eachGroup->goods_price;
//            $eachGroup->status = \App\Models\Groups::getStatus($eachGroup->status);
//            $end = $eachGroup->group_end_time;
//            $eachGroup->group_off_time = strtotime($end);
//            $arr[] = $eachGroup;
//        }
//
//        $groups = json_encode($arr);
//        return $groupList;
//    }
//
//
//    //获取用户的优惠券
//    public function getMyCoupons()
//    {
//        $para = json_decode(request('data'));
//        $id = $para->uid;
//        $coupons = DB::table('coupons')
//            ->join('user_coupons', 'coupon_id', '=', 'coupons.id')
//            ->select('user_coupons.*', 'coupons.*')
//            ->where([
//                ['coupons.status', 1],              //优惠卷还可用
//                ['user_coupons.status', '<>', 2],    //未消费
//                ['user_coupons.count', '>', 0]       //数量要大于0
//            ])
//            ->get();
//        return $coupons;
//
//    }
//
//    //获取用户的收获地址
//    public function getMyAddresses(User $user)
//    {
//        $para = json_decode(request('data'));
//        $id = $para->uid;
//        $addresses = DB::table('users')
//            ->join('addresses', 'user_id', '=', 'users.id')
//            ->select('addresses.*', 'addresses.id AS address_id')
//            ->where('users.id', $id)->orderBy('selected', 'DESC')
//            ->get();
//        return $addresses;
//
//    }
//
//    //设置用户的默认地址
//    public function setDefaultAddress()
//    {
//        $para = json_decode(request('data'));
//        $uid = $para->uid;
//        $ad_id = $para->address_id;
//        $result = DB::table('addresses')->where('user_id', $uid)->update(['selected' => 0]);
//        $result1 = DB::table('addresses')->where('id', $ad_id)->update(['selected' => 1]);
//        if ($result && $result1) {
//            return 1;
//        } else {
//            return 'setDefaultAddress Fail';
//        }
//    }
//
//    //删除用户的收获地址
//    public function delAddress()
//    {
//        $para = json_decode(request('data'));
//        $uid = $para->uid;
//        $address_id = $para->address_id;
//        $result = DB::table('addresses')->where([
//            ['id', $address_id],
//            ['user_id', $uid]
//        ])->delete();
//        if ($result) {
//            return 1;
//        } else {
//            return "Delete Fail";
//        }
//    }
//
//    public function editAddress()
//    {
//        return "editAddress";
//    }
//
//    //编辑或者新增用户收获地址
//    public function saveAddress(Request $request)
//    {
//        $para = json_decode(request('data'));
//        $info = [
//            'user_id' => $para->uid,
//            'consignee' => $para->consignee,
//            'tel' => $para->tel,
//            'province' => $para->province,
//            'city' => $para->city,
//            'district' => $para->district,
//            'address' => $para->address,
//        ];
//
//        if ($para->address_id == '') {
//            $result = DB::table('addresses')->where([
//                ['selected', 1],
//                ['user_id', $para->uid],
//            ])->count();
//            if (!$result) {
//                $info['selected'] = 1;
//            } else {
//                $info['selected'] = 0;
//            }
//            $save = DB::table('addresses')->insert($info);
//            if ($save) {
//                return 'Save Ok';
//            } else {
//                return 'Save Failed';
//            }
//        } else {
//            $edit = DB::table('addresses')->where([
//                ['user_id', 1],
//                ['id', $para->address_id],
//            ])->update($info);
//            if ($edit) {
//                return 'Edit Ok';
//            } else {
//                return "Edit Failed!";
//            }
//        }
//    }
//    //获取用户的订单列表
//    public function getOrderList(){
//        $para = json_decode(request('data'));
//        $uid=$para->uid;
//        $type=$para->type;
//        if($type==0){
//            $type=[0];
//        }elseif($type==1){
//            $type=[1];
//        }else{
//            $type=[1,0];
//        }
//        $orders=DB::table('orders')
//            ->join('goods','goods.id','=','orders.goods_id')
//            ->join('addresses','addresses.id','=','orders.address_id')
//            ->where('orders.user_id',$uid)
//            ->whereIn('order_status',$type)
//            ->get();
//        foreach($orders as $order){
//            $order->order_img="http://pintuan.com/uploads/".$order->goods_logo;
//            $order->pay_status=$order->order_status;
//            $order->order_sn=$order->order_id;
//            $order->address=$order->province.$order->city.$order->district.$order->address;
//        }
//        return json_encode($orders);
//
//    }
//
//    //删除用户的某一订单
//    public function delOrder(){
//        $para = json_decode(request('data'));
//        $order_id=$para->order_sn;
//        $uid=$para->uid;
//        $result=DB::table('orders')->where([
//            ['user_id',$uid],
//            ['order_id',$order_id]
//        ])->delete();
//        if($result){
//            $a["status"]=200;
//            return $a;
//        }
//    }
//
//    //获取某用户某订单的具体信息
//    public function getOrderInfo(){
//        $para = json_decode(request('data'));
//        $order_id=$para->order_id;
//        $uid=$para->uid;
//        $orders=DB::table('orders')
//            ->join('goods','goods.id','=','orders.goods_id')
//            ->join('addresses','addresses.id','=','orders.address_id')
//            ->where([
//                ['orders.user_id',$uid],
//                ['orders.id',$order_id]
//            ])->get();
//        foreach($orders as $order){
//            $order->order_img="http://pintuan.com/uploads/".$order->goods_logo;
//            $order->pay_status=$order->order_status;
//            $order->order_sn=$order->order_id;
//            $order->address=$order->province.$order->city.$order->district.$order->address;
//            $order->money_paid=$order->price*$order->group_num+$order->addr_price;
//            $order->goods_amount=$order->price*$order->group_num;
//        }
//        return json_encode($orders);
//
//
//    }
//
//    //积分商品模块并未开发,推假数据
//    public function getPointGoods()
//    {
//
//        $data = [
//            0 => [
//                'goods_level' => '3',
//                'info_img' => 'https://img14.360buyimg.com/n0/jfs/t17086/360/1163764245/333102/75336402/5abdf74fN6794d754.jpg',
//                'goods_name' => '零食大礼包一整箱组合装送女友生日 好吃的混装休闲进口零食 超大特产食品小吃送女生儿童团购 我的女神+娃娃',
//                'goods_sale_num' => '138',
//                'goods_num' => '200',
//                'goods_point' => '300'
//
//            ],
//            1 => [
//                'goods_level' => '2',
//                'info_img' => 'https://img14.360buyimg.com/n0/jfs/t16813/364/1126794051/105586/1c8246e1/5abb6493N12404384.jpg',
//                'goods_name' => '广州酒家 金装手信礼盒424g/盒 广东特产利口福广式饼酥糕点下午茶点心 中华老字号手信',
//                'goods_sale_num' => '61',
//                'goods_num' => '999',
//                'goods_point' => '10'
//
//            ],
//            2 => [
//                'goods_level' => '1',
//                'info_img' => 'https://img14.360buyimg.com/n0/jfs/t19063/253/945245218/468655/74e68d12/5ab35162N19f35fdd.jpg',
//                'goods_name' => '廷妃 零食大礼包组合送女友女生日一箱整箱超大吃货吃的混合装小吃进口 128元25种零食世界那么大礼盒',
//                'goods_sale_num' => '9999',
//                'goods_num' => '100',
//                'goods_point' => '300'
//
//            ]
//        ];
//        return $data;
//    }
//
//    //获取分类 (测试)
//    public
//    function getCates()
//    {
//        $cate = new \App\Models\Cate;
//        return $cate->getAllClasses()->toArray();
//    }

}

