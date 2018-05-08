<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Bus extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];       //软删除字段
    protected $table = 'bus';

}