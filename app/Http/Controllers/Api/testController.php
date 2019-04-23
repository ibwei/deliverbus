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
use Ucpaas;
use App\Models\School;
use App\Models\Bus;
use App\Models\Ticket;

class testController extends ApiController
{

    public function testupload(Request $request)
    {
        $img_content = $request->url; // 图片内容
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img_content, $result)) {
            $type = $result[2];

            $new_file = "./images/test.{$type}";


            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $img_content)))) {

                return  "http://127.0.0.1/images/test.".$type;
            }
        };
    }

    public function test18()
    {
        $userInfo = array(
            "userId" => "00001",
            "userName"



            => "ibwei"
        );
        return json_encode($userInfo);
    }

    public function test17()
    {
        $r = array("code" => 1);
        return json_encode($r);
    }

    public function test16()
    {

        $tableData = array(
            0 => array(
                "projectId" => '20181127001',
                "projectName" => "重庆师范大学蜂场站第二批产卵力检测",
                "beeSiteName" => "重庆师范大学蜂场站",
                "beeType" => "西方蜜蜂",
                "beeBinCount" => 45,
                "honeycombCount" => 65,
                "address" => "重庆",
                "category" => "产卵力研究",
                "memo" => "无",
                "editTime" => "2018-11-28 21:12:11"




            )
        );
        return json_encode($tableData);
    }

    public function test15()
    {
        $projectNameList = array('重师第一批单育研究', '重师第二批产与研');
        return json_encode($projectNameList);
    }

    public function test14()
    {
        $projectNameList = array(
            "code" => 1,
        );
        return json_encode($projectNameList);
    }


    public function test13()
    {

        $userInfo = array(
            "userId" => "00001",
            "userName" => "ibwei",
            "openId" => "456456df5sd5fd",
            "avatarUrl" => "https://www.ibwei.com/images/que
     
     
      
                stion.png"
        );
        return json_encode($userInfo);
    }

    public function test11(Request $request)
    {


        $checkType = "封盖率";

        $reportData = array(
            "checkId" => "20181127001",
            "projectName" => "重师蜂场第二批产育研究",
            "beeSiteName" => "重庆大学蜂场站",
            "beeType" => "西方蜜蜂",
            "honeycombType" => "意标框",
            "beeBin" => "2号箱",
            "checkType" => "封盖率",
            "aCoverRate" => 0.5,
            "bCoverRate" => 0.3,
            "birthday" => "2018-11-28 21:12:11",
            "start_time" => "2018-11-28 21:12:11",
            "generation" => "一代",
            'aSmallPictureUrl' => "https://farm6.staticflickr.com/5591/15008867125_68a8ed88cc_m.jpg",
            'bSmallPictureUrl' => "https://farm6.staticflickr.com/5591/15008867125_68a8ed88cc_m.jpg",
            'aPictureUrl' => "https://farm6.staticflickr.com/5591/15008867125_68a8ed88cc_b.jpg",
            'bPictureUrl' => "https://farm4.staticflickr.com/3902/14985871946_86abb8c56f_b.jpg",
            "fertilization" => "人工授精",
            "checkUser" => '管理员1',
            "checkTime" => "2018-11-28 21:12:11",
            "pdfUrl" => "http://first.ibwei.com/other/test.pdf"

        );
        $reportData1 = array(
            "checkId" => "20181127001",
            "projectName" => "重师蜂场第二批产育研究",
            "beeSiteName" => "重庆大学蜂场站",
            "beeType" => "西方蜜蜂",
            "honeycombType" => "意标框",
            "beeBin" => "2号箱",
            "checkType" => "产卵力",
            "aBornCount" => 65,
            "bBornCount" => 35,
            "bornSpend" => 60,
            "birthday" => "2018-11-28 21:12:11",
            "start_time" => "2018-11-28 21:12:11",
            'aSmallPictureUrl' => "https://farm6.staticflickr.com/5591/15008867125_68a8ed88cc_m.jpg",
            'bSmallPictureUrl' => "https://farm6.staticflickr.com/5591/15008867125_68a8ed88cc_m.jpg",
            'aPictureUrl' => "https://farm6.staticflickr.com/5591/15008867125_68a8ed88cc_b.jpg",
            'bPictureUrl' => "https://farm4.staticflickr.com/3902/14985871946_86abb8c56f_b.jpg",
            "generation" => "一代",
            "fertilization" => "人工授精",
            "checkUser" => '管理员1',
            "checkTime" => "2018-11-28 21:12:11",
            "pdfUrl" => "http://first.ibwei.com/other/test.pdf"

        );
        if ($checkType == "封盖率") {
            return json_encode($reportData);
        } else {
            return json_encode($reportData1);
        }
    }

    public function test10()
    {
        sleep(1);
        return 1;
    }


    public function test9()
    {

        sleep(1);
        return '2018555169201';
    }

    public function test8()
    {

        return 1;
    }

    public function test0()
    {
        $data = array('项目一', '项目二');
        return json_encode($data);
    }

    public function test6()
    {

        $tableData = array(

            0 => array(
                "checkId" => "20181127001",
                "projectName" => "重庆师范大学蜂场第二批检测",
                "beeType" => "西方蜜蜂",
                "honeycombType" => "意标框",
                "beeBin" => "2号箱",
                "checkType" => "封盖率",
                "aCoverRate" => 23,
                "bCoverRate" => 12,
                "coverRateAverage" => 45,
                "aBornCount" => 78,
                "bBornCount" => 45,
                "bornAverage" => 56,
                "pdfUrl" => "http://first.ibwei.com/other/test.pdf",
                "checkTime" => "2018-11-28
         
         
          
                     21:12:11"
            ),

            1 => array(
                "checkId" => "20181127001",
                "projectName" => "重庆师范大学蜂场第二批检测",
                "beeType" => "西方蜜蜂",
                "honeycombType" => "意标框",
                "beeBin" => "2号箱",
                "checkType" => "封盖率",
                "aCoverRate" => 23,
                "bCoverRate" => 12,
                "coverRateAverage" => 45,
                "aBornCount" => 78,
                "bBornCount" => 45,
                "bornAverage" => 56,
                "pdfUrl" => "http://first.ibwei.com/other/test.pdf",
                "checkTime" => "2018-11-28
         
         
          
                     21:12:11"
            ),

            2 => array(
                "checkId" => "20181127001",
                "projectName" => "重庆师范大学蜂场第二批检测",
                "beeType" => "西方蜜蜂",
                "honeycombType" => "意标框",
                "beeBin" => "2号箱",
                "checkType" => "封盖率",
                "aCoverRate" => 23,
                "bCoverRate" => 12,
                "coverRateAverage" => 45,
                "aBornCount" => 78,
                "bBornCount" => 45,
                "bornAverage" => 56,
                "pdfUrl" => "http://first.ibwei.com/other/test.pdf",
                "checkTime" => "2018-11-28
         
         
          
                     21:12:11"
            ),

            3 => array(
                "checkId" => "20181127001",
                "projectName" => "重庆师范大学蜂场第二批检测",
                "beeType" => "西方蜜蜂",
                "honeycombType" => "意标框",
                "beeBin" => "2号箱",
                "checkType" => "封盖率",
                "aCoverRate" => 23,
                "bCoverRate" => 12,
                "coverRateAverage" => 45,
                "aBornCount" => 78,
                "bBornCount" => 45,
                "bornAverage" => 56,
                "pdfUrl" => "http://first.ibwei.com/other/test.pdf",
                "checkTime" => "2018-11-28
         
         
          
                     21:12:11"
            ),
            4 => array(
                "checkId" => "20181127001",
                "projectName" => "重庆师范大学蜂场第二批检测",
                "beeType" => "西方蜜蜂",
                "honeycombType" => "意标框",
                "beeBin" => "2号箱",
                "checkType" => "封盖率",
                "aCoverRate" => 23,
                "bCoverRate" => 12,
                "coverRateAverage" => 45,
                "aBornCount" => 78,
                "bBornCount" => 45,
                "bornAverage" => 56,
                "pdfUrl" => "http://first.ibwei.com/other/test.pdf",
                "checkTime" => "2018-11-28
         
         
          
                     21:12:11"
            ),

            5 => array(
                "checkId" => "20181127001",
                "projectName" => "重庆师范大学蜂场第二批检测",
                "beeType" => "西方蜜蜂",
                "honeycombType" => "意标框",
                "beeBin" => "2号箱",
                "checkType" => "封盖率",
                "aCoverRate" => 23,
                "bCoverRate" => 12,
                "coverRateAverage" => 45,
                "aBornCount" => 78,
                "bBornCount" => 45,
                "bornAverage" => 56,
                "pdfUrl" => "http://first.ibwei.com/other/test.pdf",
                "checkTime" => "2018-11-28
         
         
          
                     21:12:11"
            ),

            6 => array(
                "checkId" => "20181127001",
                "projectName" => "重庆师范大学蜂场第二批检测",
                "beeType" => "西方蜜蜂",
                "honeycombType" => "意标框",
                "beeBin" => "2号箱",
                "checkType" => "封盖率",
                "aCoverRate" => 23,
                "bCoverRate" => 12,
                "coverRateAverage" => 45,
                "aBornCount" => 78,
                "bBornCount" => 45,
                "bornAverage" => 56,
                "pdfUrl" => "http://first.ibwei.com/other/test.pdf",
                "checkTime" => "2018-11-28
         
         
          
                     21:12:11"
            ),

            7 => array(
                "checkId" => "20181127001",
                "projectName" => "重庆师范大学蜂场第二批检测",
                "beeType" => "西方蜜蜂",
                "honeycombType" => "意标框",
                "beeBin" => "2号箱",
                "checkType" => "封盖率",
                "aCoverRate" => 23,
                "bCoverRate" => 12,
                "coverRateAverage" => 45,
                "aBornCount" => 78,
                "bBornCount" => 45,
                "bornAverage" => 56,
                "pdfUrl" => "http://first.ibwei.com/other/test.pdf",
                "checkTime" => "2018-11-28
         
         
          
                     21:12:11"
            ),

            8 => array(
                "checkId" => "20181127001",
                "projectName" => "重庆师范大学蜂场第二批检测",
                "beeType" => "西方蜜蜂",
                "honeycombType" => "意标框",
                "beeBin" => "2号箱",
                "checkType" => "封盖率",
                "aCoverRate" => 23,
                "bCoverRate" => 12,
                "coverRateAverage" => 45,
                "aBornCount" => 78,
                "bBornCount" => 45,
                "bornAverage" => 56,
                "pdfUrl" => "http://first.ibwei.com/other/test.pdf",
                "checkTime" => "2018-11-28
         
         
          
                     21:12:11"
            ),

            9 => array(
                "checkId" => "20181127001",
                "projectName" => "重庆师范大学蜂场第二批检测",
                "beeType" => "西方蜜蜂",
                "honeycombType" => "意标框",
                "beeBin" => "2号箱",
                "checkType" => "封盖率",
                "aCoverRate" => 23,
                "bCoverRate" => 12,
                "coverRateAverage" => 45,
                "aBornCount" => 78,
                "bBornCount" => 45,
                "bornAverage" => 56,
                "pdfUrl" => "http://first.ibwei.com/other/test.pdf",
                "checkTime" => "2018-11-28
         
         
          
                     21:12:11"
            ),


            10 => array(
                "checkId" => "20181127001",
                "projectName" => "重庆师范大学蜂场第二批检测",
                "beeType" => "西方蜜蜂",
                "honeycombType" => "意标框",
                "beeBin" => "2号箱",
                "checkType" => "封盖率",
                "aCoverRate" => 23,
                "bCoverRate" => 12,
                "coverRateAverage" => 45,
                "aBornCount" => 78,
                "bBornCount" => 45,
                "bornAverage" => 56,
                "pdfUrl" => "http://first.ibwei.com/other/test.pdf",
                "checkTime" => "2018-11-28
         
         
          
                     21:12:11"
            ),

            11 => array(
                "checkId" => "20181127001",
                "projectName" => "重庆师范大学蜂场第二批检测",
                "beeType" => "西方蜜蜂",
                "honeycombType" => "意标框",
                "beeBin" => "2号箱",
                "checkType" => "封盖率",
                "aCoverRate" => 23,
                "bCoverRate" => 12,
                "coverRateAverage" => 45,
                "aBornCount" => 78,
                "bBornCount" => 45,
                "bornAverage" => 56,
                "pdfUrl" => "http://first.ibwei.com/other/test.pdf",
                "checkTime" => "2018-11-28
         
         
          
                     21:12:11"
            ),

            12 => array(
                "checkId" => "20181127001",
                "projectName" => "重庆师范大学蜂场第二批检测",
                "beeType" => "西方蜜蜂",
                "honeycombType" => "意标框",
                "beeBin" => "2号箱",
                "checkType" => "封盖率",
                "aCoverRate" => 23,
                "bCoverRate" => 12,
                "coverRateAverage" => 45,
                "aBornCount" => 78,
                "bBornCount" => 45,
                "bornAverage" => 56,
                "pdfUrl" => "http://first.ibwei.com/other/test.pdf",
                "checkTime" => "2018-11-28
         
         
          
                     21:12:11"
            ),

            13 => array(
                "checkId" => "20181127001",
                "projectName" => "重庆师范大学蜂场第二批检测",
                "beeType" => "西方蜜蜂",
                "honeycombType" => "意标框",
                "beeBin" => "2号箱",
                "checkType" => "封盖率",
                "aCoverRate" => 23,
                "bCoverRate" => 12,
                "coverRateAverage" => 45,
                "aBornCount" => 78,
                "bBornCount" => 45,
                "bornAverage" => 56,
                "pdfUrl" => "http://first.ibwei.com/other/test.pdf",
                "checkTime" => "2018-11-28
         
         
          
                     21:12:11"
            ),
        );
        return json_encode($tableData);
    }

    public function test7()
    {

        $tableData = array(

            0 => array(
                "checkId" => "20181127001",
                "projectName" => "重庆师范大学蜂场第二批检测",
                "beeType" => "西方蜜蜂",
                "honeycombType" => "意标框",
                "beeBin" => "2号箱",
                "checkType" => "封盖率",
                "aCoverRate" => 23,
                "bCoverRate" => 12,
                "coverRateAverage" => 45,
                "aBornCount" => 78,
                "bBornCount" => 45,
                "bornAverage" => 56,
                "checkTime" => "2018-11-28 21:12:11",
                "pdfUrl" => "http://first.ibwei.com/other/test.pdf"
            ),
            1 => array(
                "checkId" => "20181127002",
                "projectName" => "重庆师范大学蜂场第二批检测",
                "beeType" => "西方蜜蜂",
                "honeycombType" => "意标框",
                "beeBin" => "2号箱",
                "checkType" => "封盖率",
                "aCoverRate" => 23,
                "bCoverRate" => 12,
                "coverRateAverage" => 45,
                "aBornCount" => 78,
                "bBornCount" => 45,
                "bornAverage" => 56,
                "checkTime" => "2018-11-28 21:12:11",
                "pdfUrl" => "http://first.ibwei.com/other/test.pdf"
            ),
            2 => array(
                "checkId" => "20181127003",
                "projectName" => "重庆师范大学蜂场第二批检测",
                "beeType" => "西方蜜蜂",
                "honeycombType" => "意标框",
                "beeBin" => "2号箱",
                "checkType" => "封盖率",
                "aCoverRate" => 23,
                "bCoverRate" => 12,
                "coverRateAverage" => 45,
                "aBornCount" => 78,
                "bBornCount" => 45,
                "bornAverage" => 56,
                "checkTime" => "2018-11-28 21:12:11",
                "pdfUrl" => "http://first.ibwei.com/other/test.pdf"
            )

        );
        return json_encode($tableData);
    }

    public function test5()
    {

        return 1;
    }

    public function test4()
    {

        return array('科学研究', '生产检测');
    }

    public function test3()
    {

        return array('重庆师范大学蜂场', '重庆大学蜂场');
    }

    public function test2()
    {
        return 1;
    }

    public function test1()
    {

        $tableData = array(
            0 => array(
                "projectId" => '20181127001',
                "projectName" => "重庆师范大学蜂场站第二批产卵力检测",
                "beeSiteName" => "重庆师范大学蜂场站",
                "beeType" => "西方蜜蜂",
                "beeBinCount" => 45,
                "honeycombCount" => 65,
                "address" => "重庆",
                "category" => "产卵力研究",
                "memo" => "无",
                "editTime" => "2018-11-28 21:12:11"
            ),
            1 => array(
                "projectId" => '20181127002',
                "projectName" => "重庆师范大学蜂场站第5656批产卵力检测",
                "beeSiteName" => "重庆大学蜂场站",
                "beeType" => "西方蜜蜂",
                "beeBinCount" => 56,
                "honeycombCount" => 65,
                "address" => "稀释南放假啊速度快静安寺的框架",
                "category" => "产卵力研究",
                "memo" => "无",
                "editTime" => "2018-11-28 21:12:11",
            ), 2 => array(
                "projectId" => '20181127002',
                "projectName" => "重庆师范大学蜂场站第5122",
                "beeSiteName" => "重庆大学蜂场站",
                "beeType" => "西方蜜蜂",
                "beeBinCount" => 56,
                "honeycombCount" => 65,
                "address" => "稀释南放假啊速度快静安寺的框架",
                "category" => "产卵力研究",
                "memo" => "无",
                "editTime" => "2018-11-28 21:12:11",
            ), 3 => array(
                "projectId" => '20181127002',
                "projectName" => "重庆师范大学蜂场站第5656批产卵力检测",
                "beeSiteName" => "重庆大学蜂场站",
                "beeType" => "西方蜜蜂",
                "beeBinCount" => 56,
                "honeycombCount" => 65,
                "address" => "稀释南放假啊速度快静安寺的框架",
                "category" => "产卵力研究",
                "memo" => "无",
                "editTime" => "2018-11-28 21:12:11",
            ), 4 => array(
                "projectId" => '20181127002',
                "projectName" => "重庆师范大学蜂场站第5656批产卵力检测",
                "beeSiteName" => "重庆大学蜂场站",
                "beeType" => "西方蜜蜂",
                "beeBinCount" => 56,
                "honeycombCount" => 65,
                "address" => "稀释南放假啊速度快静安寺的框架",
                "category" => "产卵力研究",
                "memo" => "无",
                "editTime" => "2018-11-28 21:12:11",
            ), 5 => array(
                "projectId" => '20181127002',
                "projectName" => "重庆师范大学蜂场站第5656批产卵力检测",
                "beeSiteName" => "重庆大学蜂场站",
                "beeType" => "西方蜜蜂",
                "beeBinCount" => 56,
                "honeycombCount" => 65,
                "address" => "稀释南放假啊速度快静安寺的框架",
                "category" => "产卵力研究",
                "memo" => "无",
                "editTime" => "2018-11-28 21:12:11",
            ), 6 => array(
                "projectId" => '20181127002',
                "projectName" => "重庆师范大学蜂场站第5656批产卵力检测",
                "beeSiteName" => "重庆大学蜂场站",
                "beeType" => "西方蜜蜂",
                "beeBinCount" => 56,
                "honeycombCount" => 65,
                "address" => "稀释南放假啊速度快静安寺的框架",
                "category" => "产卵力研究",
                "memo" => "无",
                "editTime" => "2018-11-28 21:12:11",
            ), 7 => array(
                "projectId" => '20181127002',
                "projectName" => "重庆师范大学蜂场站第5656批产卵力检测",
                "beeSiteName" => "重庆大学蜂场站",
                "beeType" => "西方蜜蜂",
                "beeBinCount" => 56,
                "honeycombCount" => 65,
                "address" => "稀释南放假啊速度快静安寺的框架",
                "category" => "产卵力研究",
                "memo" => "无",
                "editTime" => "2018-11-28 21:12:11",
            ), 8 => array(
                "projectId" => '20181127002',
                "projectName" => "重庆师范大学蜂场站第5656批产卵力检测",
                "beeSiteName" => "重庆大学蜂场站",
                "beeType" => "西方蜜蜂",
                "beeBinCount" => 56,
                "honeycombCount" => 65,
                "address" => "稀释南放假啊速度快静安寺的框架",
                "category" => "产卵力研究",
                "memo" => "无",
                "editTime" => "2018-11-28 21:12:11",
            ), 9 => array(
                "projectId" => '20181127002',
                "projectName" => "重庆师范大学蜂场站第5656批产卵力检测",
                "beeSiteName" => "重庆大学蜂场站",
                "beeType" => "西方蜜蜂",
                "beeBinCount" => 56,
                "honeycombCount" => 65,
                "address" => "稀释南放假啊速度快静安寺的框架",
                "category" => "产卵力研究",
                "memo" => "无",
                "editTime" => "2018-11-28 21:12:11",
            ), 10 => array(
                "projectId" => '20181127002',
                "projectName" => "重庆师范大学蜂场站第5656批产卵力检测",
                "beeSiteName" => "重庆大学蜂场站",
                "beeType" => "西方蜜蜂",
                "beeBinCount" => 56,
                "honeycombCount" => 65,
                "address" => "稀释南放假啊速度快静安寺的框架",
                "category" => "产卵力研究",
                "memo" => "无",
                "editTime" => "2018-11-28 21:12:11",
            ), 11 => array(
                "projectId" => '20181127002',
                "projectName" => "重庆师范大学蜂场站第5656批产卵力检测",
                "beeSiteName" => "重庆大学蜂场站",
                "beeType" => "西方蜜蜂",
                "beeBinCount" => 56,
                "honeycombCount" => 65,
                "address" => "稀释南放假啊速度快静安寺的框架",
                "category" => "产卵力研究",
                "memo" => "无",
                "editTime" => "2018-11-28 21:12:11",
            )

        );
        return json_encode($tableData);
    }
}
