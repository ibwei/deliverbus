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
use function GuzzleHttp\json_encode;
//use Ucpaas;

class UserInfoController extends ApiController
{

    use AuthenticatesUsers;
    //验证当前用户是否和前台传来的user_id相等
    public function ifUser($user_id)
    {
        $current_id = \Auth::user()->id;
        if ($current_id === $user_id) {
            return 1;
        } else {
            return 0;
        }
    }
    //获取用户的收获地址
    public function getMyAddresses(Request $request)
    {
        $user_id = $request->user_id;
        // $res = $this->ifUser($user_id);
        // if (!$res) {
        //     return $this->failed('非法请求', 500);
        // }
        $addresses = DB::table('users')
            ->join('addresses', 'user_id', '=', 'users.id')
            ->join('schools', 'schools.id', '=', 'addresses.school_id')
            ->select('addresses.*', 'schools.name as schoolName')
            ->where('users.id', $user_id)->orderBy('is_default', 'DESC')
            ->get();
        return $addresses;
    }
    //设置用户的默认地址
    public function setDefaultAddress()
    {
        $uid = request('user_id');
        $ad_id = request('address_id');
        $res = $this->ifUser($uid);
        if (!$res) {
            return $this->failed('非法请求', 500);
        }
        $result = DB::table('addresses')->where('user_id', $uid)->update(['is_default' => 0]);
        $result1 = DB::table('addresses')->where('id', $ad_id)->update(['is_default' => 1]);
        if ($result && $result1) {
            return 'ok';
        } else {
            return 'setDefaultAddress Fail';
        }
    }
    //编辑或者新增用户收获地址
    public function saveAddress(Request $request)
    {
        $para = request();
        $user_id = $para->user_id;
        $res = $this->ifUser($user_id);
        if (!$res) {
            return $this->failed('非法请求', 500);
        }
        $info = [
            'user_id' => $user_id,
            'consignee' => $para->consignee,
            'school_id' => $para->school,
            'tel' => $para->tel,
            'address' => $para->address,
        ];
        if ($para->address_id == '') {
            $result = DB::table('addresses')->where([
                ['is_default', 1],
                ['user_id', $user_id],
            ])->count();
            if (!$result) {
                $info['is_default'] = 1;
            } else {
                $info['is_default'] = 0;
            }
            $save = DB::table('addresses')->insert($info);
            if ($save) {
                return 'ok';
            } else {
                return 'err';
            }
        } else {
            $edit = DB::table('addresses')->where([
                ['user_id', $user_id],
                ['id', $para->address_id],
            ])->update($info);
            if ($edit) {
                return 'ok';
            } else {
                return "err";
            }
        }
    }
    //删除用户的收获地址
    public function delAddress()
    {
        $para = request();
        $uid = $para->user_id;
        $res = $this->ifUser($uid);
        if (!$res) {
            return $this->failed('非法请求', 500);
        }
        $address_id = $para->address_id;
        $result = DB::table('addresses')->where([
            ['id', $address_id],
            ['user_id', $uid]
        ])->delete();
        if ($result) {
            return 'ok';
        } else {
            return "Delete Fail";
        }
    }
    //申请老司机
    public function newDriver()
    {
        $para = request();
        $user_id = $para->user_id;
        $res = $this->ifUser($user_id);
        if (!$res) {
            return $this->failed('非法请求', 500);
        }
        $name = $para->name;
        $path = $para->path;
        $school = $para->school;
        $card_number = $para->card_number;
        $address = $para->address;
        $tel = $para->tel;
        $info = [
            'user_id' => $user_id,
            'name' => $name,
            'school_id' => $school,
            'card_number' => $card_number,
            'address' => $address,
            'card_img' => $path,
            'tel' => $tel,
        ];
        $save = DB::table('drivers')->insert($info);
        if ($save) {
            return 'ok';
        } else {
            return 'Save Failed';
        }
    }

    //上传证件照
    public function uploadCardImg()
    {
        $Path = "/uploads/images/";
        $mypath = "images/";
        if (!empty($_FILES['photo'])) {
            //获取扩展名
            $exename = $this->getExeName($_FILES['photo']['name']);
            if ($exename != 'png' && $exename != 'jpg' && $exename != 'gif') {
                exit('不允许的扩展名');
            }
            $fileName = $_SERVER['DOCUMENT_ROOT'] . $Path . date('Ym'); //文件路径
            $upload_name = '/img_' . date("YmdHis") . rand(0, 100) . '.' . $exename; //文件名加后缀
            if (!file_exists($fileName)) {
                //进行文件创建
                mkdir($fileName, 0777, true);
            }
            $imageSavePath = $fileName . $upload_name;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $imageSavePath)) {
                $path = $mypath . date('Ym') . $upload_name;
                return response()->json(['err' => 0, 'path' => $path]);
            }
        }
        return 'err';
    }
    public function getExeName($fileName)
    {
        $pathinfo = pathinfo($fileName);
        return strtolower($pathinfo['extension']);
    }

    //判断是不是老司机
    public function ifDriver()
    {
        $user_id = request('user_id');
        $res = DB::table('drivers')->where('user_id', '=', $user_id)->get()->toArray();
        if (count($res)) {
            if ($res[0]->state == 1) {
                return 'wait';
            } else if ($res[0]->state == 2) {
                return $res;
            } else {
                return 'no';
            }
        } else {
            return 'dont';
        }
    }
    //判断是不是老司机
    public function ifSiJi()
    {
        $user_id = request('user_id');
        $result = $this->ifUser($user_id);
        if (!$result) {
            return $this->failed('非法请求', 500);
        }
        $res = DB::table('drivers')->where('user_id', '=', $user_id)->get()->toArray();
        if (count($res)) {
            return response()->json(['message' => 'yes', 'data' => $res]);
        } else {
            return response()->json(['message' => 'dont', 'data' => '']);
        }
    }

    //获取bus信息
    public function getPassenger()
    {
        $user_id = request('user_id');
        $result = $this->ifUser($user_id);
        if (!$result) {
            return $this->failed('非法请求', 500);
        }
        $driver_id = request('driver_id');
        $res = DB::table('drivers')->where('user_id', '=', $user_id)->orderBy('created_at')->get()->toArray();
        $myDriverId = $res[0]->id;
        if ($driver_id == $myDriverId) {
            $bus = DB::table('bus')->where('driver_id', '=', $driver_id)
                ->join('sites', 'bus.site_id', 'sites.id')
                ->select('bus.*', 'sites.name as start_site')
                ->get()->toArray();
            return $bus;
        } else {
            return 'err';
        }
    }
    //乘客详情
    public function passengerDetail()
    {
        $bus_id = request('bus_id');
        $passenger = DB::table('ticket')->where('bus_id', '=', $bus_id)
            ->join('users', 'ticket.user_id', 'users.id')
            ->join('addresses', 'ticket.address_id', 'addresses.id')
            ->select('ticket.*', 'addresses.consignee', 'addresses.tel', 'addresses.address')
            ->get()->toArray();
        return $passenger;
    }
    //改变bus状态
    public function changeStatus()
    {
        $config = config('wechat.mini_program.default');
        $app = Factory::miniProgram($config);
        $user_id = request('user_id');
        $res = $this->ifUser($user_id);
        if (!$res) {
            return $this->failed('非法请求', 500);
        }
        $bus_id = request('bus_id');
        $status = request('status') + 1;
        // $driver_id = 1;
        $driver_id = request('driver_id');
        if ($status > 0 && $status < 4) {
            //推送东西
            $res = DB::table('bus')->where([
                ['driver_id', '=', $driver_id],
                ['id', '=', $bus_id]
            ])->update(['status' => $status]);
            if ($status == 1) {
                $template = DB::table('ticket')
                    ->join('users', 'users.id', '=', 'ticket.user_id')
                    ->join('bus', 'bus.id', '=', 'ticket.bus_id')
                    ->select('users.id AS userid', 'bus.end_time AS endtime', 'bus.start_time AS starttime', 'users.openid AS openid', 'ticket.express_name AS express_name')
                    ->where([
                        ['bus.id', '=', $bus_id]
                    ])->distinct()
                    ->get();
                for ($i = 0; $i < count($template); $i++) {
                    $formid = DB::table('formid')->where('user_id', $template[$i]->userid)->orderBy('id', 'desc')->value('form_id');
                    DB::table('formid')->where('form_id', '=', $formid)->delete();
                    $app->template_message->send([
                        'touser' => $template[$i]->openid,
                        'template_id' => '3i-iZk99MNE1SNwLOjTyVwOw-ScJUh2ev6qw6xxDmow',
                        'page' => '/pages/myticket/index',
                        'form_id' => $formid,
                        'data' => [
                            'keyword1' => $template[$i]->express_name,
                            'keyword2' => $template[$i]->starttime,
                            'keyword3' => $template[$i]->endtime,
                            'keyword4' => "请按约定时间地点等候派件",
                            'keyword5' => "正在派送,请稍后..."
                        ],
                    ]);
                }
            }



            if ($res) {
                $bus = DB::table('bus')->where('driver_id', '=', $driver_id)
                    ->join('sites', 'bus.site_id', 'sites.id')
                    ->select('bus.*', 'sites.name as start_site')
                    ->get()->toArray();
                return $bus;
            } else {
                return 'no';
            }
        }
    }
}
