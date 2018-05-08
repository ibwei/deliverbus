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
            $form->text('address_id', '用户收货地址id');
            $form->currency('price', '票价')
                ->symbol('￥');
            $form->currency('goods_now_price', '商品现价')
                ->symbol('￥');
            $form->display('note', '备注');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

}