<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Addresses;//引用模型
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\ModelForm;

class AddressesController extends Controller
{
    use ModelForm;

    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('收货地址表');//这里是页面标题
            $content->description('地址信息');//这里是详情描述

            $content->body($this->grid());//指向grid方法显示表格
        });
    }

    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('收货地址');
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

        return Admin::grid(Addresses::class, function (Grid $grid) {
            //grid显示表格内容，$grid->数据库中相应的字段（‘在页面上显示的名称’）->其他方法();或者$grid->column（‘数据库中相应的字段’，‘在页面上显示的名称’）->其他方法();

            // 第一列显示id字段，并将这一列设置为可排序列


            $grid->id('ID')->sortable();
            $grid->getUser()->nickname('用户名');
            $grid->consignee('收货人姓名');
            $grid->tel('电话');

            $grid->address('具体的宿舍楼栋号');
            $grid->is_default('是否是默认地址')->display(function ($is_default) {
                return $is_default ? '是' : '否';
            });
            $grid->column('updated_at','更新');
            $grid->column('created_at','创建');
        });

    }

    protected function form()
    {
        return Admin::form(Addresses::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('user_id', '用户id');
            $form->text('consignee', '收货人姓名');
            $form->text('tel', '电话');
            $form->text('address','具体的宿舍楼栋号');
            $form->radio('is_default', '是否是默认地址')
                ->options(['1'=>'是', '0'=>'否'])
                ->default('0');
        });
    }


}
