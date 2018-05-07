<?php
/**
 * sqc @小T科技 2018.03.07
 *
 *
 */
namespace App\Logic;

use App\Models\Address;
use App\Models\Good;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use EasyWeChat\Factory;
use function EasyWeChat\Kernel\Support\generate_sign;

class Buy
{

    /**
     * 会员信息id
     * @var string
     */
    private $_user_id = '';

    /**
     * 下单数据
     * @var array
     */
    private $_order_data = array();

    /**
     * 下单地区数据
     * @var array
     */
    private $_address_data = array();

    /**
     * 表单数据
     * @var array
     */
    private $_post_data = array();

    public function __construct()
    {
    }

    /**
     * 获取用户的收货地址
     * @param member_id 用户的id
     * @param default_id 默认收货地址id
     * @return array  收货地址相关的数组
     */
    public function getAddress($member_id, $default_id = '')
    {
        $where['uid'] = $member_id;
        if ($default_id) {
            $where['id'] = $default_id;
        }
        $Addresseses = Address::where($where)->get();
        if ($Addresseses->toArray()) {
            foreach ($Addresseses as $ar_key => $ar_value) {
                $ar_value->get_school;
                $ar_value->get_dorm;
            }
            return $Addresseses->toArray();
        } else {
            return false;
        }
    }

    /**
     * 第一步：处理购物中的商品
     *
     * @param string $checked_goods 购物车信息
     */
    public function getCartGoodsList($goodsJsonStr)
    {
        if (empty($goodsJsonStr)) {
            return false;
        }
        $goodsCartList = \GuzzleHttp\json_decode($goodsJsonStr, true);
        $goodsIds = array_column($goodsCartList, 'goodsId');
        $goodsNums = array_column($goodsCartList, 'number', 'goodsId');
        $goodsArr = Good::whereIn('id', $goodsIds)->where('goods_state', '=', 1)->get();// 获取商品的详细信息
        $all_price = 0.00;// 订单总价格
        foreach ($goodsArr as $gc_key => &$gc_val) {
            $gc_val['number'] = $goodsNums[$gc_val['id']];
            $tmp = $this->PriceCalculate($gc_val['goods_price'], '*', $goodsNums[$gc_val['id']]);
            $all_price = $this->PriceCalculate($all_price, "+", $tmp);
        }
        return [
            'goodsList' => $goodsArr,
            'g_nums' => $goodsNums,
            'all_price' => $all_price,
            'freight_price' => 0.00,
        ];
    }

    /**
     * 执行购买
     * @param int $user_id // 购买者
     * @param array $buydata // 购买的商品
     * @param array $address //收货地址
     * @param object $request // 表单数据
     * @return array
     */
    public function buyStep($request, $buy_info, $address, $user_id)
    {
        $this->_user_id = $user_id;
        $this->_order_data = $buy_info;
        $this->_post_data = $request;
        $this->_address_data = $address;
        try {
            DB::beginTransaction();
            //第1步 执行下单
            $order_info = $this->_createOrderStep1();
            return $order_info;
        } catch (Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }

    }


    /**
     * 生成订单
     * @param array $input
     * @throws Exception
     * @return array array(支付单sn,订单列表)
     */
    private function _createOrderStep1()
    {
        $paycode = $this->makePaySn($this->_user_id);
        $order_insert = array();//订单数据
        $order_insert['paycode'] = $paycode; // 支付单号
        $order_insert['order_sn'] = $this->makeOrderSn($paycode); // 订单编号
        $order_insert['uid'] = $this->_user_id;
        $order_insert['pay_method'] = 1;// 微信支付
        $order_insert['lock_state'] = 1;// 待支付
        $order_insert['status'] = 10;// 待支付

        $order_goods_insert = array(); // 订单附表的数据

        // 收货地址
        $order_insert['aid_p'] = $this->_address_data[0]['aid_p'];
        $order_insert['aid_c'] = $this->_address_data[0]['aid_c'];
        $order_insert['aid_a'] = $this->_address_data[0]['aid_a'];
        $order_insert['addr_prov'] = $this->_address_data[0]['province'];
        $order_insert['addr_city'] = $this->_address_data[0]['city'];
        $order_insert['addr_area'] = $this->_address_data[0]['area'];
        $order_insert['addr_detail'] = $this->_address_data[0]['addr'];
        $order_insert['user_name'] = $this->_address_data[0]['true_name'];
        $order_insert['user_mobile'] = $this->_address_data[0]['mobile'];

        // 学校内
        if ($this->_address_data[0]['sid'] && $this->_address_data[0]['get_school']) {
            $order_insert['sid'] = $this->_address_data[0]['get_school']['id'];// 学校id
            $order_insert['did'] = $this->_address_data[0]['get_dorm']['id'];//宿舍楼id
            $order_insert['school_name'] = $this->_address_data[0]['get_school']['school_name'];//学校名称
            $order_insert['dorm_name'] = $this->_address_data[0]['get_dorm']['dorm_name'];//宿舍楼名称
        }

        //用户留言
        $order_insert['msg'] = empty($this->_post_data->remark) ? '用户无留言' : $this->_post_data->remark;
        // 购买的商品信息
        $order_insert['total_price'] = $this->_order_data['all_price'];// 订单总金额
        $order_insert['pay_price'] = $this->_order_data['all_price'];// 支付的金额
        $order_insert['addr_price'] = $this->_order_data['freight_price'];// 运费
        // 执行插入
        $order_model = Order::create($order_insert);
        $this->pay_log($order_model->id);
        if (!$order_model->id) {
            throw new Exception('订单保存失败[未生成支付单]');
        }
        foreach ($this->_order_data['goodsList'] as $k => $va) {
            if ($va['number']) {
                $order_goods_insert[] = [
                    'uid' => $this->_user_id,
                    'oid' => $order_model->id,
                    'goods_id' => $va['id'],
                    'goods_name' => $va['goods_name'],
                    'goods_price' => $va['goods_price'],
                    'goods_image' => $va['goods_main_image'] ? $va['goods_main_image'] : '',
                    'goods_desc' => $va['goods_desc'] ? $va['goods_desc'] : '',
                    'num' => $va['number'],
                ];
                $this->addSaleNum($va['id'],$va['number'] );
            }
        }
        $order_goods_model = DB::table('order_goods')->insert($order_goods_insert);
        $this->pay_log(var_export($order_goods_insert, true));
        if ($order_goods_model) {
            DB::commit();
            return $order_model->toArray();
        } else {
            throw new Exception('订单商品保存失败');
        }
    }

    public function addSaleNum($id,$num = 1){
        DB::table('goods')->where('id',$id)->increment('goods_salenum', $num);
    }

    /**
     * 价格格式化
     *
     * @param int $price
     * @return string    $price_format
     */
    function PriceFormat($price)
    {
        $price_format = number_format($price, 2, '.', '');
        return $price_format;
    }

    /**
     * PHP精确计算  主要用于货币的计算用法
     * @param $n1 第一个数
     * @param $symbol 计算符号 + - * / %
     * @param $n2 第二个数
     * @param string $scale 精度 默认为小数点后两位
     * @return  string
     */
    function PriceCalculate($n1, $symbol, $n2, $scale = '2')
    {
        $res = "";
        if (function_exists("bcadd")) {
            switch ($symbol) {
                case "+"://加法
                    $res = bcadd($n1, $n2, $scale);
                    break;
                case "-"://减法
                    $res = bcsub($n1, $n2, $scale);
                    break;
                case "*"://乘法
                    $res = bcmul($n1, $n2, $scale);
                    break;
                case "/"://除法
                    $res = bcdiv($n1, $n2, $scale);
                    break;
                case "%"://求余、取模
                    $res = bcmod($n1, $n2, $scale);
                    break;
                default:
                    $res = "";
                    break;
            }
        } else {
            switch ($symbol) {
                case "+"://加法
                    $res = $n1 + $n2;
                    break;
                case "-"://减法
                    $res = $n1 - $n2;
                    break;
                case "*"://乘法
                    $res = $n1 * $n2;
                    break;
                case "/"://除法
                    $res = $n1 / $n2;
                    break;
                case "%"://求余、取模
                    $res = $n1 % $n2;
                    break;
                default:
                    $res = "";
                    break;
            }

        }
        return $res;

    }

    /**
     * 生成支付单编号(两位随机 + 从2000-01-01 00:00:00 到现在的秒数+微秒+会员ID%1000)，该值会传给第三方支付接口
     * 长度 =2位 + 10位 + 3位 + 3位  = 18位
     * 1000个会员同一微秒提订单，重复机率为1/100
     * @return string
     */
    public function makePaySn($member_id)
    {
        return mt_rand(10, 99)
        . sprintf('%010d', time() - 946656000)
        . sprintf('%03d', (float)microtime() * 1000)
        . sprintf('%03d', (int)$member_id % 1000);
    }

    /**
     * 订单编号生成规则
     * 生成订单编号(年取1位 + $pay_id取13位 + 第N个子订单取2位)
     * 1000个会员同一微秒提订单，重复机率为1/100
     * @param $pay_sn
     * @return string
     */
    public function makeOrderSn($pay_sn)
    {
        //记录生成子订单的个数，如果生成多个子订单，该值会累加
        static $num;
        if (empty($num)) {
            $num = 1;
        } else {
            $num++;
        }
        return (date('y', time()) % 9 + 1) . sprintf('%013d', $pay_sn) . sprintf('%02d', $num);
    }




    // 支付第一步
    public function pay_step1($attributes, $openid)
    {
        $time = time();
        $app = Factory::payment(config('wechat.payment.default'));
        $result = $app->order->unify([
            'body' => '卖纸的小超市',
            'detail' => '卖纸的小超市的订单',
            'out_trade_no' => $attributes['order_sn'],
            'total_fee' =>  $attributes['pay_price'] * 100,
            'trade_type' => 'JSAPI',
            'openid' => $openid,
        ]);
        $this->pay_log('order:'.var_export($attributes, true).'  result:' . var_export($result, true));
        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
            // 如果成功生成统一下单的订单，那么进行二次签名 二次签名的参数必须与下面相同
            $params = [
                'appId'     => config('wechat.payment.default.app_id'),
                'timeStamp' => time(),
                'nonceStr'  => $result['nonce_str'],
                'package'   => 'prepay_id=' . $result['prepay_id'],
                'signType'  => 'MD5',
            ];
            $params['paySign'] = generate_sign($params, config('wechat.payment.default.key'));
            return $params;
        } else {
            // 返回错误信息
            $this->pay_log('json_prepare' . var_export($result, true));
            return false;
        }
    }

    /**
     * 记录日志
     */
    private function pay_log($msg)
    {
        $msg = date('H:i:s') . "|" . $msg . "\r\n";
        $msg .= '| GET:' . var_export($_GET, true) . "\r\n";
        file_put_contents('./log/pay' . date('Y-m-d') . ".log", $msg, FILE_APPEND);
    }
    

}
