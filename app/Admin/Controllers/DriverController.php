<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Driver;//引用模型
use App\Models\School;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\ModelForm;

class DriverController extends Controller
{
    use ModelForm;

    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('老司机管理');

            $content->body($this->grid());
    });
    }

    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('老司机管理');
            $content->description('新增');
            $content->body($this->form());

        });
    }

    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header('老司机管理');
            $content->description();
            $content->body($this->form()->edit($id));
        });
    }

    protected function grid()
    {

        return Admin::grid(Driver::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->getUser()->nickname('用户名');
            $grid->getSchool()->name('学校');
            $grid->name('老司机名');
            $grid->address('宿舍楼和房间号');
            $grid->card_number('证件号码');
            $grid->card_img('证件图片')->image('',100,100);
            $grid->column('state', '状态')
                ->select(Driver::getStateDispayMap());
            $grid->point('司机积分');
            $grid->created_at();
            $grid->updated_at();
        });

    }

    protected function form()
    {
        return Admin::form(Driver::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('user_id', '用户id');
            $form->text('name', '老司机名');
            $form->radio('state', '状态')
                ->options(Driver::getStateDispayMap())
                ->default(Driver::STATE_WAIT);
//            $form->select('school_id', '学校')
//                ->rules('required')
//                ->options(School::getAllSchools());
            $form->text('school_id', '学校id');
            $form->text('address','宿舍楼和房间号');
            $form->text('card_number', '证件号码');
            $form->image('card_img', '证件图片')
                ->rules('required')
                ->uniqueName() ;
            $form->number('point','司机积分');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

}