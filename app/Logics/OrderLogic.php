<?php
/**
 * User: liuhao
 * Date: 18-3-8
 * Time: 下午5:12
 */

namespace App\Logics;


use App\Models\Delivery;
use App\Models\Order;
use App\User;
use Illuminate\Support\Facades\DB;

class OrderLogic
{
    public $isInSchool = null;
    public $optionUserID = null;

    //接收单子
    public function receiveOrder($orderID)
    {
        $resaultFlag = false;
        $errMsg = '';

        $orderModel = Order::find($orderID);
        if ($orderModel) {
            try {
                DB::beginTransaction();
                if ($this->inSchoolOrder($orderModel)) {
                    $this->inSchoolReceiveOrder($orderModel);
                } else {
                    $this->outSchoolReceiveOrder($orderModel);
                }
                DB::commit();
                $resaultFlag = true;
            } catch (\Exception $e) {
                DB::rollBack();
                $errMsg = $e->getMessage();
            }
        }

        return [$resaultFlag, $errMsg];
    }

    //配送单子
    public function deliveryOrder($orderID)
    {
        $resaultFlag = false;
        $errMsg = '';

        $orderModel = Order::find($orderID);
        if ($orderModel) {
            try {
                DB::beginTransaction();
                if ($this->inSchoolOrder($orderModel)) {
                    $this->inSchoolDeliveryOrder($orderModel);
                } else {
                    $this->outSchoolDeliveryOrder($orderModel);
                }
                DB::commit();
                $resaultFlag = true;
            } catch (\Exception $e) {
                DB::rollBack();
                $errMsg = $e->getMessage();
            }
        }
        return [$resaultFlag, $errMsg];
    }

    public function inSchoolOrder(Order $orderModel)
    {
        if (!is_null($this->isInSchool)) {
            return $this->isInSchool;
        }
        if (empty($orderModel->sid)) {
            return false;
        }
        return true;
    }

    protected function inSchoolReceiveOrder(Order $orderModel)
    {
        if ($orderModel->lock_state != 0) {
            throw new \Exception('402|该订单已被锁定');
        }
        if ($orderModel->status != Order::STATUS_ALREADY_PAID) {
            throw new \Exception('402|必须是已支付状态！');
        }

        $orderModel->status = Order::STATUS_RECEIVED;

        if (!$orderModel->save()) {
            throw new \Exception('402|接单失败！');
        }

        $userModel = User::find($this->optionUserID);
        $deliveryModel = new Delivery();
        $deliveryModel->oid = $orderModel->id;
        $deliveryModel->uid = $userModel->id;
        $deliveryModel->delivery_name = $userModel->name;
        $deliveryModel->delivery_mobile = $userModel->mobile;
        if (!$deliveryModel->save()) {
            throw new \Exception('402|接单失败');
        }
    }

    protected function outSchoolReceiveOrder(Order $orderModel)
    {
        if ($orderModel->lock_state != 0) {
            throw new \Exception('该订单已被锁定');
        }
        if ($orderModel->status != Order::STATUS_ALREADY_PAID) {
            throw new \Exception('必须是已支付状态！');
        }

        $orderModel->status = Order::STATUS_RECEIVED;

        if (!$orderModel->save()) {
            throw new \Exception('接单失败！');
        }
    }

    protected function inSchoolDeliveryOrder(Order $orderModel)
    {
        if ($orderModel->lock_state != 0) {
            throw new \Exception('402|该订单已被锁定!');
        }
        if ($orderModel->status != Order::STATUS_RECEIVED) {
            throw new \Exception('402|必须是已接单状态!');
        }

        $orderModel->status = Order::STATUS_DELIVERING;

        if (!$orderModel->save()) {
            throw new \Exception('402|配送失败！');
        }
    }

    protected function outSchoolDeliveryOrder(Order $orderModel)
    {
        if ($orderModel->lock_state != 0) {
            throw new \Exception('该订单已被锁定!');
        }
        if ($orderModel->status != Order::STATUS_RECEIVED) {
            throw new \Exception('必须是已接单状态!');
        }

        if (empty($orderModel->logistics_num)) {
            throw new \Exception('请先填写物流单号');
        }
        $orderModel->status = Order::STATUS_DELIVERING;

        if (!$orderModel->save()) {
            throw new \Exception('配送失败！');
        }
    }


    public function getDeliveryOrderList($userID, $orderStatus = null)
    {
        $resaultFlag = false;
        $resaultData = '';

        try {
            $canDeliveryStatusList = [
                Order::STATUS_ALREADY_PAID,
                Order::STATUS_RECEIVED,
                Order::STATUS_DELIVERING,
                Order::STATUS_COMPLETED,
            ];
            if (!is_null($orderStatus)) {
                if (!in_array($orderStatus, $canDeliveryStatusList)) {
                    throw new \Exception('状态错误');
                }
            } else {
                $orderStatus = $canDeliveryStatusList;
            }

            $serviceRangeLogic = new ServiceRangeLogic();
            $dormIDList = $serviceRangeLogic->getServiceRangeListByUserID($userID);

            $orderQuery = Order::select([
                'order.id',
                'order.status',
                'order.order_sn',
                'order.pay_price',
                'order.msg',
                'order.dorm_name',
                'order.user_name',
                'order.user_mobile',
                'order_goods.goods_name',
                'order_goods.goods_price',
                'order_goods.num'
            ])->whereIn('did', $dormIDList);

            if (!is_array($orderStatus)) {
                $orderQuery = $orderQuery->where('status', '=', $orderStatus);
            } else {
                $orderQuery = $orderQuery->whereIn('status', $orderStatus);
            }

            $orderQuery->leftJoin('order_goods', 'order.id', '=', 'order_goods.oid');
            $orderList = $orderQuery->get()->toArray();

            $finalOrderList = [];
            $goodsList = [];

            if ($orderList) {
                foreach ($orderList as $eachOrder) {
                    $finalOrderList[$eachOrder['id']] = [
                        'id' => $eachOrder['id'],
                        'status' => $eachOrder['status'],
                        'orderNumber' => $eachOrder['order_sn'],
                        'amountReal' => $eachOrder['pay_price'],
                        'remark' => $eachOrder['msg'],
                        'userName' => $eachOrder['user_name'],
                        'userMobile' => $eachOrder['user_mobile'],
                        'dormName' => $eachOrder['dorm_name'],
                    ];

                    $goodsList[$eachOrder['id']][] = [
                        'name' => $eachOrder['goods_name'],
                        'price' => $eachOrder['goods_price'],
                        'num' => $eachOrder['num'],
                    ];
                }
            }
            $resaultFlag = true;
            $resaultData = [
                'orderList' => $finalOrderList,
                'goodsMap' => $goodsList,
            ];
        } catch(\Exception $e) {
            $resaultData = $e->getMessage();
        }

        return [$resaultFlag, $resaultData];
    }

    public function getOrderDetail($orderID)
    {
        $resaultFlag = false;
        $resaultData = '';

        try {
            $orderList = Order::select([
                'order.id',
                'order.status',
                'order.order_sn',
                'order.pay_price',
                'order.msg',
                'order.dorm_name',
                'order.user_name',
                'order.user_mobile',
                'order.addr_price',
                'order_goods.goods_name',
                'order_goods.goods_price',
                'order_goods.num',
            ])
                ->where('order.id', '=',  $orderID)
                ->leftJoin('order_goods', 'order.id', '=', 'order_goods.oid')
                ->get()
                ->toArray();
            $finalOrderList = [];
            $goodsList = [];
            $totalPrice = 0;
            if ($orderList) {
                foreach ($orderList as $eachOrder) {

                    if (empty($finalOrderList)) {
                        $finalOrderList = [
                            'id' => $eachOrder['id'],
                            'status' => $eachOrder['status'],
                            'statusStr' => Order::getStatusDisplayMap()[$eachOrder['status']] ?? '',
                            'orderNumber' => $eachOrder['order_sn'],
                            'amountReal' => $eachOrder['pay_price'],
                            'amountLogistics' => $eachOrder['addr_price'],
                            'remark' => $eachOrder['msg'],
                            'userName' => $eachOrder['user_name'],
                            'userMobile' => $eachOrder['user_mobile'],
                            'dormName' => $eachOrder['dorm_name'],
                        ];
                    }

                    $goodsList[] = [
                        'goodsName' => $eachOrder['goods_name'],
                        'amount' => $eachOrder['goods_price'],
                        'number' => $eachOrder['num'],
                    ];

                    $totalPrice += $eachOrder['goods_price'] * $eachOrder['num'];
                }
            }
            $resaultFlag = true;
            $delivery = Delivery::where('oid',$orderID)->first();
            $finalOrderList['amount'] = $totalPrice;
            $resaultData = [
                'orderInfo' => $finalOrderList,
                'goodsList' => $goodsList,
                'delivery' => empty($delivery->delivery_mobile)?'':$delivery->toArray(),
                'kefu' =>[
                    'phone' => '13527589747'
                ]
            ];


        } catch (\Exception $e) {
            $resaultData = $e->getMessage();
        }
        return [$resaultFlag, $resaultData];
    }

    public function completeOrder($orderID)
    {
        $resaultFlag = false;
        $resaultData = '';
        try {
            $orderModel = Order::find($orderID);
            if ($orderModel) {
                $orderModel->status = Order::STATUS_COMPLETED;

                $orderModel->save();
                $resaultFlag = true;
            }
        } catch (\Exception $e) {
            $resaultData = $e->getMessage();
        }
        return [$resaultFlag, $resaultData];
    }
}