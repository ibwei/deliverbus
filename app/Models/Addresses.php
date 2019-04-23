<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Users;
class Addresses extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];       //软删除字段

    public function getUser()
    {
        return $this->belongsTo(Users::class, 'user_id', 'id');
    }
}