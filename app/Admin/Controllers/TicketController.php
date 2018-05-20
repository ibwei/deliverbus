<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\School;//引用模型
use App\Models\Bus;
use App\Models\Ticket;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\ModelForm;

class TicketController extends Controller
{
    use ModelForm;

    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('车票管理');

            $content->body($this->grid());
    });
    }

    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('车票管理');
            $content->description('新增');
            $content->body($this->form());

        });
    }

    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header('车票管理');
            $content->description();
            $content->body($this->form()->edit($id));
        });
    }

    protected function grid()
    {

        return Admin::grid(Ticket::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->column('bus_id', 'busId');
            $grid->getUser()->nickname('用户名');
            $grid->getAddress()->address('收货地址');
            $grid->column('type', '类型')
                ->select(Ticket::getTypeDispayMap());
            $grid->column('price', '票价');
            $grid->column('status', '状态')
                ->select(Ticket::getStateDispayMap());
            $grid->column('deliver_number', '取货码');
            $grid->column('express_name', '快递公司');
            $grid->column('consignee_name', '取货人姓名');
            $grid->column('consignee_tel', '取货人电话');
            $grid->column('memo', '备注');
            $grid->column('trade_sn', '平台订单号');
            $grid->column('pay_sn', '微信交易号');
            $grid->column('pay_date', '支付时间');
            $grid->created_at();
            $grid->updated_at();
        });

    }

    protected function form()
    {
        return Admin::form(Ticket::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('bus_id', 'busId');
            $form->text('user_id', '用户id');
            $form->text('address_id', '用户收货地址id');
            $form->radio('type', '类型')
                ->options(Ticket::getTypeDispayMap())
                ->default(Ticket::STATE_SMALL);
            $form->currency('price', '票价')
                ->symbol('￥');
            $form->radio('status', '状态')
                ->options(Ticket::getStateDispayMap())
                ->default(Ticket::STATE_WAIT);
            $form->text('deliver_number', '取货码');
            $form->text('express_name', '快递公司');
            $form->text('consignee_name', '取货人姓名');
            $form->text('consignee_tel', '取货人电话');
            $form->text('memo', '备注');
            $form->text('trade_sn', '平台订单号');
            $form->text('pay_sn', '微信交易号');
            $form->datetime('pay_date','支付时间');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

}