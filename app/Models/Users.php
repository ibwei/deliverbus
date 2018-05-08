<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Users extends Model
{
    use SoftDeletes;
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $guarded = [
        'openid'
    ];

    protected $dates = ['deleted_at'];       //软删除字段

    //性别选择
    const STATE_FEMALE = 1;
    const STATE_MALE = 2;
    const STATE_FEMALE_STRING = '男';
    const STATE_MALE_STRING = '女';

    public static function getGender()
    {
        return [
            self::STATE_FEMALE => self::STATE_FEMALE_STRING,
            self::STATE_MALE => self::STATE_MALE_STRING,
        ];
    }

}