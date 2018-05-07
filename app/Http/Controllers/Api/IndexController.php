<?php

namespace App\Http\Controllers\Api;


class IndexController extends ApiController
{
    public function index()
    {
        return $this->message('请求成功');
    }

    /**
     * 店铺信息
     * @return mixed
     */
    public function storeInfo()
    {
        $singleInfo = [
            'creatAt' => "2018-03-2 08:06:55",
            "dateType" => 0,
            "id" => 612,
            "key" => "mallName",
            "remark" => "商城名称",
            "updateAt" => "2018-03-2 08:06:55",
            "userId" => 951,
            "value" => "小纸屋"
        ];
        return $this->success($singleInfo);
    }
}
