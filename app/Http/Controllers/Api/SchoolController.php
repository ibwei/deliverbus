<?php

namespace App\Http\Controllers\Api;
use App\Feedback;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class SchoolController extends ApiController
{
    public function index()
    {
        return $this->message('请求成功');
    }

    /**
     * 店铺信息
     * @return mixed
     */
    //选择所有学校
    public function selectAllSchool()
    {
        $shoolList = School::select('id AS value', 'name AS title')->get();
        return json_encode($shoolList);
    }

    //获取当前学校的所有站点
    public function getSite(Request $request)
    {
        $school_id = $request->school_id;
        $sites = DB::table('sites')
            ->join('schools', 'schools.id', '=', 'sites.school_id')
            ->where('school_id', $school_id)
            ->select('sites.id AS value','sites.name AS title')
            ->get();
        return json_encode($sites);
    }

    //根据关键字查询学校
    public function searchSchool(Request $request)
    {
        $keywords = '%' . $request->name . '%';
        $schoolList = DB::table('schools')
            ->select('id AS value', 'name AS title')
            ->where('name', 'like', $keywords)
            ->orderBy('id')
            ->get();
        return json_encode($schoolList);
    }

    //保存反馈信息表
    public function sendFeedback(Request $request)
    {
        $feedback = new Feedback;
        $feedback->user_id = $request->user_id;
        $feedback->message = $request->message;
        if ($feedback->save()) {
            return 1;
        } else {
            return 0;
        }
    }
}
