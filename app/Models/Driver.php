<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Users;
use App\Models\School;
class Driver extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];       //软删除字段

    const STATE_NO = 0;
    const STATE_WAIT = 1;
    const STATE_OK = 2;
    const STATE_NO_STRING = '黑名单';
    const STATE_WAIT_STRING = '等待审核';
    const STATE_OK_STRING = '已审核';
    public static function getStateDispayMap()
    {
        return [
            self::STATE_NO => self::STATE_NO_STRING,
            self::STATE_WAIT => self::STATE_WAIT_STRING,
            self::STATE_OK => self::STATE_OK_STRING,
        ];
    }


    public function getUser()
    {
        return $this->belongsTo(Users::class, 'user_id', 'id');
    }

    public function getSchool()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }
}