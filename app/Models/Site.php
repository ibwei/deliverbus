<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\School;
class Site extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];       //软删除字段

    public function getSchool()
    {
        return $this->belongsTo(School::class, 'school_id', 'id');
    }
}