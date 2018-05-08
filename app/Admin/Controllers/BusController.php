<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\School;//引用模型
use App\Models\Bus;//引用模型
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\ModelForm;

class BusController extends Controller
{
    use ModelForm;

    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('Bus管理');

            $content->body($this->grid());
    });
    }

    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('Bus管理');
            $content->description('新增');
            $content->body($this->form());

        });
    }

    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header('Bus管理');
            $content->description();
            $content->body($this->form()->edit($id));
        });
    }

    protected function grid()
    {

        return Admin::grid(Bus::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->column('driver_id', '司机');
            $grid->column('school_id', '学校');
            $grid->column('site_id', '起点');
            $grid->column('address_id', '终点');
            $grid->column('small_price', '小件票价');
            $grid->column('normall_price', '中件票价');
            $grid->column('big_price', '大件票价');
            $grid->column('note', '备注');
            $grid->column('status', '状态')
                 ->select(Bus::getStateDispayMap());
            $grid->column('count','最大人数');
            $grid->column('start_time','开始时间');
            $grid->column('end_time','结束时间');
            $grid->created_at();
            $grid->updated_at();
        });

    }

    protected function form()
    {
        return Admin::form(Bus::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('driver_id', '司机id');
            $form->text('school_id', '学校id');
            $form->text('site_id', '站点id');
            $form->text('address_id', 'address_id');
            $form->currency('small_price', '小件票价')
                ->symbol('￥');
            $form->currency('normall_price', '中件票价')
                ->symbol('￥');
            $form->currency('big_price', '大件票价')
                ->symbol('￥');
            $form->text('note', '备注');
            $form->radio('status', '状态')
                ->options(Bus::getStateDispayMap())
                ->default(Bus::STATE_NO);
            $form->text('count','最大人数');
            $form->datetime('start_time','开始时间');
            $form->datetime('end_time','结束时间');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

}