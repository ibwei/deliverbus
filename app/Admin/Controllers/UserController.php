<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Users;//引用模型
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\ModelForm;

class UserController extends Controller
{
    use ModelForm;

    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('用户表');//这里是页面标题
            $content->description('用户信息');//这里是详情描述

            $content->body($this->grid());//指向grid方法显示表格
        });
    }

    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('用户信息');
            $content->description('新增');
            $content->body($this->form());//调用form方法，显示表单

        });
    }

    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header('列表');
            $content->description();
            $content->body($this->form()->edit($id));//将id传给form，form的模型就是以id为查询条件的
        });
    }

    protected function grid()
    {//页面显示的表格

        return Admin::grid(Users::class, function (Grid $grid) {
            //grid显示表格内容，$grid->数据库中相应的字段（‘在页面上显示的名称’）->其他方法();或者$grid->column（‘数据库中相应的字段’，‘在页面上显示的名称’）->其他方法();

            // 第一列显示id字段，并将这一列设置为可排序列
            $grid->id('ID')->sortable();

            $grid->nickname('用户名')->label('info')->sortable();
            $grid->gender('性别')->select([
                1 => '男',
                2 => '女',
            ]);;
            $grid->avatar('	微信头像')->image('', 75, 75);
            $grid->tel('电话');
            $grid->description('个人介绍');
            $grid->point('个人积分');
            $grid->unionid('微信的unionid')->label();
            $grid->openid('用户唯一标识openid')->label();
//            $grid->column('login_time', '最近登陆时间')->display(function ($login_time) {
//                return date('Y-m-d H:i:s',$login_time);}


            $grid->created_at();
            $grid->updated_at();

            // $filter->like('name', '名称'));//用名称作为条件模糊查询

        });

    }

    protected function form()
    {
        return Admin::form(Users::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('nickname','用户名')->rules('required')->placeholder('请输入用户名');
            $form->radio('gender', '性别')
                ->options(Users::getGender())
                ->default(Users::STATE_FEMALE);
            $form->image('avatar','用户头像');
            $form->mobile('tel','手机')->placeholder('请输入用户手机号');
            $form->text('description', '用户个人介绍');
            $form->number('point','用户个人积分');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

}