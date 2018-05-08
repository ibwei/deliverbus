<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Bus extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];       //软删除字段
    protected $table = 'bus';

    const STATE_NO = 0;
    const STATE_WAIT = 1;
    const STATE_OVER = 2;
    const STATE_OK = 3;
    const STATE_NO_STRING = '未发车';
    const STATE_WAIT_STRING = '发车中';
    const STATE_OVER_STRING = '已到站';
    const STATE_OK_STRING = '全部确认';
    public static function getStateDispayMap()
    {
        return [
            self::STATE_NO => self::STATE_NO_STRING,
            self::STATE_WAIT => self::STATE_WAIT_STRING,
            self::STATE_OK => self::STATE_OK_STRING,
            self::STATE_OVER => self::STATE_OVER_STRING,
        ];
    }

}