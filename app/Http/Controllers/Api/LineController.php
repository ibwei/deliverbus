<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Client;
use Socialite;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client as HttpClient;
use App\User;
use Illuminate\Http\Request;
use Validator;
use EasyWeChat\Factory;


class LineController extends ApiController
{
    public function index()
    {
        $start = request('start_site');
        $end = request('end_site');
        $location_name = [
            'a' => "培训楼",
            'b' => "嘉风苑",
            'c' => "一食堂",
            'd' => "和风苑",
            'e' => "篮球/足球场",
            'f' => "南门",
            'g' => "清风苑",
            'h' => "北门",
            'i' => "二食堂",
            'j' => '校医院/游泳馆',
            'k' => '特教楼(励志楼)',
            'l' => '汇贤楼(学院楼)',
            'm' => '畅风苑',
            'n' => '雅风苑',
            'o' => '图书馆',
            'p' => '惠风苑',
            'q' => '三食堂',
            'r' => '弘德楼',
            's' => '知行楼(九教)',
            't' => '综合办公楼',
            'u' => '集贤楼',
            'v' => '校友会堂',
            'w' => '美术/音乐学院',
        ];
        $ljjz = [
            'a' => ['b' => 165, 'btime' => 3, 'i' => 302, 'itime' => 5,],
            'b' => ['a' => 165, 'atime' => 3, 'i' => 343, 'itime' => 5, 'c' => 360, 'ctime' => 5, 'h' => 300, 'htime' => '4',],
            'c' => ['b' => 360, 'btime' => 5, 'd' => 100, 'dtime' => 1, 'i' => 345, 'itime' => 5, 'o' => 230, 'otime' => 4, 'r' => 363, 'rtime' => 5,],
            'd' => ['h' => 193, 'htime' => 3, 'c' => 100, 'ctime' => 1, 'r' => 284, 'rtime' => 4, 'e' => 149, 'etime' => 3,],
            'e' => ['d' => 193, 'dtime' => 3, 'h' => 159, 'htime' => 3, 'r' => 300, 'rtime' => 4, 't' => 150, 'ttime' => 2,],
            'f' => ['p' => 209, 'ptime' => 3, 'w' => 160, 'wtime' => 2,],
            'g' => ['i' => 229, 'itime' => 3,],
            'h' => ['b' => 300, 'btime' => 4, 'd' => 193, 'dtime' => 3, 'e' => 159, 'etime' => 3,],
            'i' => ['a' => 301, 'atime' => 5, 'b' => 343, 'btime' => 5, 'c' => 345, 'ctime' => 5, 'o' => 190, 'otime' => 3, 'k' => 298, 'ktime' => 4, 'l' => 378, 'ltime' => 6, 'j' => 118, 'jtime' => 2, 'g' => 229, 'gtime' => 3,],
            'j' => ['i' => 188, 'itime' => 2, 'l' => 206, 'ltime' => 3,],
            'k' => ['i' => 298, 'itime' => 4, 'o' => 250, 'otime' => 3, 'q' => 340, 'qtime' => 5, 'n' => 244, 'ntime' => 4, 'm' => 254, 'mtime' => 4, 'l' => 233, 'ltime' => 4,],
            'l' => ['j' => 206, 'jtime' => 3, 'i' => 378, 'itime' => 6, 'k' => 233, 'ktime' => 4,],
            'm' => ['k' => 254, 'ktime' => 4, 'n' => 202, 'ntime' => 3,],
            'n' => ['k' => 244, 'ktime' => 4, 'm' => 202, 'mtime' => 3,],
            'o' => ['k' => 250, 'ktime' => 3, 'i' => 190, 'itime' => 3, 'c' => 230, 'ctime' => 4, 'r' => 409, 'rtime' => 6, 's' => 340, 'stime' => 4,],
            'p' => ['n' => 105, 'ntime' => 2, 'q' => 148, 'qtime' => 2, 'f' => 209, 'ftime' => 3, 'w' => 203, 'wtime' => 3,],
            'q' => ['k' => 340, 'ktime' => 5, 's' => 182, 'stime' => 3, 'v' => 506, 'vtime' => 7, 'w' => 148, 'wtime' => 2, 'p' => 148, 'ptime' => 2,],
            'r' => ['c' => 363, 'ctime' => 5, 'd' => 284, 'dtime' => 4, 'e' => 300, 'etime' => 4, 't' => 225, 'ttime' => 3, 'u' => 476, 'utime' => 7, 's' => 556, 'stime' => 8, 'o' => 409, 'otime' => 6],
            's' => ['o' => 340, 'otime' => 4, 'r' => 556, 'rtime' => 8, 'u' => 431, 'utime' => 6, 'q' => 182, 'qtime' => 3,],
            't' => ['e' => 150, 'etime' => 2, 'r' => 255, 'rtime' => 3, 'u' => 374, 'utime' => 5, 'v' => 210, 'vtime' => 4,],
            'u' => ['s' => 431, 'stime' => 6, 'r' => 476, 'rtime' => 7, 't' => 374, 'ttime' => 5, 'v' => 30, 'vtime' => 1,],
            'v' => ['q' => 506, 'qtime' => 7, 't' => 210, 'ttime' => 4, 'u' => 30, 'utime' => 1,],
            'w' => ['q' => 148, 'qtime' => 2, 'p' => 203, 'ptime' => 3, 'f' => 160, 'ftime' => 2,],
        ];

        $N = range('a', 'z');//未找到点

        $Y = [$start => 0];//已找到点

        $note = [$start => [$start]];//记录寻点过程中已找到的最小距离点

//过程计算

        $result=$this->djstl($Y, $note, $ljjz, $N, $end);
        return $result;
//输出结果


//        foreach ($note[$start] as $value) {
//            echo $location_name["$value"] . "--";
//        };//$NOTE的最后一个单元即为：起点到终点的所经过的点

//统计最短路径大概历时
        $length = count($note[$end]);
        $time = 0;
        for ($i = 0; $i < $length - 1; $i++) {
            $first = $note[$end][$i];
            $second = $note[$end][$i + 1];
            $time += $ljjz[$first][$second . 'time'];
        }
        //echo '大约历时' . $time . "分钟";
        //echo $Y['f'] . "米";//$Y的最后一个单元即为：起点到终点的最距离
        return $note[$end];

    }

    public function djstl(&$y, &$note, $data, $n, $end_site)
    {

        $end = $end_site; //定义终点
        //1.计算所有已找到点到所有未找到点的距离,并存储"最小点","最小值"和"父顶点"
        $yn = [];//存储$y点到$n点的距离
        $min_key = '';//定义找到的"最小距离"
        $min_val = 123456789;//定义找到的"最小距离"
        $parent_key = '';//定义找到的"最小点"的父点(即最小点相连的点)

        foreach ($y as $key => $k_value) {
            foreach ($n as $n_value) {
                //在数据中查找y对应的n点:有对应的值则判断记录
                if (isset($data[$key][$n_value])) {
                    //取出y到n的距离
                    $range = $data[$key][$n_value];
                    //得到"y到起点"+n的距离
                    $range = $k_value + $range;
                    //yn距离中没有存储n的距离,则直接加入;
                    //否则判断是否比其小;晓得替换之前的值加入
                    if (isset($yn[$n_value])) {
                        if ($range < $yn[$n_value]) {
                            $yn[$n_value] = $range;//将n设为更小的加入$yn
                        }
                    } else {
                        $yn[$n_value] = $range;
                    }
                    //存储"最小点","最小值","父顶点"
                    if ($range < $min_val) {
                        $min_val = $range;
                        $min_key = $n_value;
                        $parent_key = $key;
                    }
                } else {
                    //在数据中查找y对应的n点:并且$yn中也没有,如果有则为上个点找到的点,否则将值设为无穷大
                    if (!isset($yn[$n_value])) {
                        $yn[$n_value] = 123456789;
                    }
                }

            }//$n结束
        }//$y结束

        //2.生成新note
        //将找到的最小点,加入$note,其内容为他的父点加上它本身

        $_find_new = $note[$parent_key];
        array_push($_find_new, $min_key);
        $note[$min_key] = $_find_new;

        //3.生成新y
        $y[$min_key] = $min_val;

        //4.生成新n
        $k = array_search($min_key, $n);
        unset($n[$k]);
        if ($min_key != $end) {
            $this->djstl($y,$note, $data, $n, $end);
        } else {
            return $note[$end];
        };
    }
}