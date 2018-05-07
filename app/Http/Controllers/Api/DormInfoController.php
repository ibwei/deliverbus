<?php
/**
 * User: liuhao
 * Date: 18-3-7
 * Time: 下午4:01
 */

namespace App\Http\Controllers\Api;


use App\Models\Dorm;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DormInfoController extends ApiController
{
    public function getLikeDormList(Request $request)
    {
        $q = $request->get('q');

        return Dorm::where('sid', $q)->get(['id', DB::raw('dorm_name as text')]);
    }

    public function getLikeSchoolList(Request $request)
    {
        $q = $request->get('q');
        return School::where('school_name', 'like', "%$q%")->paginate(null, ['id', 'school_name as text']);
    }

    public function getAllSchoolList(Request $request)
    {
        $data = [];

        $data = School::select('id', 'school_name as name')->get();

        return $this->success($data);
    }

    public function getDormListBySchoolID(Request $request)
    {
        $sid = $request->get('sid');
        $data = [];
        if (empty($sid)) {
            return $this->failed('sid参数不能为空', 402);
        }
        $data = Dorm::select('id', 'dorm_name as name')->where('sid', $sid)->get();

        return $this->success($data);
    }
}