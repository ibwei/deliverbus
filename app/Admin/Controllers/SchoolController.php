<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\School;//引用模型
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\ModelForm;

class SchoolController extends Controller
{
    use ModelForm;

    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('学校管理');

            $content->body($this->grid());
        });
    }

    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('学校管理');
            $content->description('新增');
            $content->body($this->form());

        });
    }

    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header('学校管理');
            $content->description();
            $content->body($this->form()->edit($id));
        });
    }

    protected function grid()
    {

        return Admin::grid(School::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->name('学校名称');
            $grid->created_at();
            $grid->updated_at();
        });

    }

    protected function form()
    {
        return Admin::form(School::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('name', '学校名称');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

}