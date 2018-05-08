<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Ticket extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];       //软删除字段
    protected $table = 'ticket';


    const STATE_WAIT = 1;
    const STATE_OK = 2;
    const STATE_NO = 0;
    const STATE_NO_STRING = '支付失败';
    const STATE_WAIT_STRING = '待支付';
    const STATE_OK_STRING = '支付成功';
    public static function getStateDispayMap()
    {
        return [
            self::STATE_WAIT => self::STATE_WAIT_STRING,
            self::STATE_NO => self::STATE_NO_STRING,
            self::STATE_OK => self::STATE_OK_STRING,
        ];
    }

    const STATE_SMALL = 1;
    const STATE_NOMALL = 2;
    const STATE_BIG = 3;
    const STATE_SMALL_STRING = '小件';
    const STATE_BIG_STRING = '大件';
    const STATE_NOMALL_STRING = '中件';
    public static function getTypeDispayMap()
    {
        return [
            self::STATE_SMALL => self::STATE_SMALL_STRING,
            self::STATE_NOMALL => self::STATE_SMALL_STRING,
            self::STATE_BIG => self::STATE_BIG_STRING,
        ];
    }

}