<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class School extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];       //软删除字段

    public static function getAllSchools()
    {
        $schools= School::all(['id', 'name']);
        $result = [];
        foreach ($schools as $eachSchool) {
            $result[$eachSchool->id] = $eachSchool->name;
        }

        return $result;
    }

}