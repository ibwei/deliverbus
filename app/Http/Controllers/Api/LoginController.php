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
use function GuzzleHttp\json_encode;

class LoginController extends ApiController
{

    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('auth:api')->only([
            'logout'
        ]);
    }
    public function username()
    {
        return 'openid';
    }

    public function test()
    {
        return "1";
    }

    public function easyWechatGetSession($code)
    {
        $config = config('wechat.mini_program.default');
        $app = Factory::miniProgram($config);
        return $app->auth->session($code);
    }

    /**
     * 处理小程序的自动登陆和注册
     * @param $oauth
     */
    public function login(Request $request)
    {

        // 获取openid
        if ($request->code) {
            $wx_info = $this->easyWechatGetSession($request->code);
        }

        if (empty($wx_info['openid'])) {
            $this->failed('用户openid没有获取到', 6);
        }
        $openid = $wx_info['openid'];
        $info = User::where('openid', $openid)->first();
        if ($info && $info->toArray()) {
            //执行登录
            $info->login_ip = $this->getClientIP();
            $info->login_time = Carbon::now();
            $info->save();
            // 直接创建token
            $token = $info->createToken($openid)->accessToken;
            $uid = $info->id;
            return $this->success(compact('token', 'uid'));
        } else {
            //执行注册
            return $this->register($request, $openid);
        }
    }
    /*
     * 用户注册
    * @param Request $request
    */
    public function register($request, $openid)
    {
        //进行基本验证
        $newUser = [
            'openid' => $openid, //openid
            'nickname' => $request->nickName, // 昵称
            'avatar' => $request->avatarUrl, //头像
            'unionid' => '', // unionid (可空)
            'login_ip' => $this->getClientIP(),
            'gender' => $request->gender,
            'point' => 0,
            'description' => '太懒了,什么也没写',
            'login_time' => Carbon::now()
        ];
        $n_user = User::create($newUser);
        $uid = $n_user->id;
        // 直接创建token
        $token = $n_user->createToken($openid)->accessToken;
        $isDriver = false;
        return $this->success(compact('token', 'uid'));
    }
    //返回登录状态
    protected function sendFailedLoginResponse(Request $request)
    {
        $msg = $request['errors'];
        $code = $request['code'];
        return $this->setStatusCode($code)->failed($msg);
    }
    //获取用户是否绑定手机号
    public function getMobile(Request $request)
    {
        $para = request('data');
        $para1 = json_decode($para);

        $a1 = \App\User::select('mobile')->find($para1->unionId);

        $a1 = \App\User::select('mobile')->find($para1->unionId);

        if ($a1->mobile) {
            return $para1->unionId;
        }
        return 0;
    }
    //校对验证码,并且修改到表
    public function saveMobile(Request $request)
    {
        //处理前端数据,并且验证
        $para = request('data');
        $a = json_decode($para, true);
        $mobile = $a['phone'];
        $id = $a['uid']['data']['uid'];
        $code = $a['yzm'];
        $mycode = Cache::get('code');
        //如果为空,则返回错误,不为空,校对验证码,讲手机号存入用户信息
        if ($id && $code && $mycode) {
            if ($code == $mycode) {
                $result = DB::table('users')
                    ->where('id', $id)
                    ->update(['mobile' => $mobile]);
                if ($result) {
                    $data1 = ["status" => 1, "msg" => "已绑定", "uid" => $id];
                    Cache::forget('code');
                    return json_encode($data1);
                } else {
                    return 0;
                }
            } else {
                $result = ["status" => 0, "msg" => "验证码错误,请重新输入", "uid" => $id];
                return json_encode($result);
            }
        } else {  //如果有选项没有输入,就返回状态
            $result = ["status" => 0, "msg" => "请确认输入所有选项", "uid" => $id];
            return json_encode($result);
        }
    }
    //发送验证码
    public function sendCode(Request $request)
    {
        $mobile1 = request('data');
        $mobile2 = json_decode($mobile1);
        $mobile = $mobile2->phone;
        $code = $this->GetRandStr(4);
        Cache::put("code", $code, 5);
        $param = $code;      //参数
        $uid = "";

        $appid = "6745552134604ec8854f5216474b2c02";    //应用的ID，可在开发者控制台内的短信产品下查看
        $templateid = "309332";    //可在后台短信产品→选择接入的应用→短信模板-模板ID，查看该模板ID
        $options['accountsid'] = '9374a8e19317dc6af77623a147155cb1';
        $options['token'] = 'f6e6c3099c56f61407b913d033dce08b';
        $appid = "6745552134604ec8854f5216474b2c02";    //应用的ID，可在开发者控制台内的短信产品下查
        $templateid = "309332";    //可在后台短信产品→选择接入的应用→短信模板-模板ID，查看该模板ID
        $options['accountsid'] = '9374a8e19317dc6af77623a147155cb1';
        $options['token'] = 'f6e6c3099c56f61407b913d033dce08b';


        $ucpass = new Ucpaas($options);
        $res = $ucpass->SendSms($appid, $templateid, $param, $mobile, $uid);
        return $res;
    }

    //生成随机数
    function GetRandStr($len)
    {
        $chars = array(
            "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"
        );
        $charsLen = count($chars) - 1;
        shuffle($chars);
        $output = "";
        for ($i = 0; $i < $len; $i++) {
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }
}
