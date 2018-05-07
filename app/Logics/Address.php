<?php
/**
 * sqc
 * xiaoT科技
 */
namespace App\Logic;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Addresses;
use App\Models\School;
use App\Models\Addrjson;

class Address
{

    /**
     * 根据省区县 获取宿舍和学校信息
     * @param  [int] $aid_p 一级地区
     * @param  [int] $aid_c 二级级地区
     * @param  [int] $aid_a 三级级地区
     * @return [json]        返回关系式
     */
    public function getShoolDorm($aid_p, $aid_c, $aid_a)
    {
        $where['status'] = 2;
        if ($aid_p) $where['aid_p'] = $aid_p;
        if ($aid_c) $where['aid_c'] = $aid_c;
        if ($aid_a) $where['aid_a'] = $aid_a;
        if (empty($where)) {
            return false;
        }
        $schools = School::with('dorm')->where($where)->get();
        $schools_arr = array();
        $schools_data = array();
        foreach ($schools as $s_k => $s_val) {
            $tmp = array();
            $schools_arr['school_name'] = $s_val['school_name'];
            $schools_arr['id'] = $s_val['id'];
            $dorm = $s_val['dorm']->toArray();
            if ($dorm) {
                foreach ($dorm as $dk => $dv) {
                    $tmp[] = [
                        'dorm_name' => $dv['dorm_name'],
                        'id' => $dv['id'],
                    ];
                }
                $schools_arr['sub'] = $tmp;
            }
            $schools_data[] = $schools_arr;

        }

        return $schools_data;

    }
}
