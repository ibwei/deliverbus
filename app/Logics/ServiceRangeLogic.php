<?php
/**
 * User: liuhao
 * Date: 18-3-9
 * Time: 上午10:35
 */

namespace App\Logics;


use App\Models\ServiceRange;

class ServiceRangeLogic
{
    public function getServiceRangeListByUserID($userID)
    {
        $serviceRange = ServiceRange::select('did')->where('delivery_uid', $userID)
            ->get()
            ->toArray();

        return $serviceRange ?? [];
    }
}