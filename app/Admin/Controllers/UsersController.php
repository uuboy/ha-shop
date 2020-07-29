<?php

namespace App\Admin\Controllers;

use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Hash;

class UsersController extends AdminController
{
    protected $title = '操作员';

    protected function grid()
    {
        $grid = new Grid(new User);

        // 创建一个列名为 ID 的列，内容是用户的 id 字段
        $grid->id('ID');

        // 创建一个列名为 用户名 的列，内容是用户的 name 字段。下面的 email() 和 created_at() 同理
        $grid->name('姓名');

        $grid->email('用户名');

        $grid->created_at('注册时间');

        $grid->actions(function ($actions) {
            $actions->disableView();
            $actions->disableDelete();
        });

        $grid->filter(function($filter){

            // 去掉默认的id过滤器
            $filter->disableIdFilter();

            // 在这里添加字段过滤器
            $filter->like('name', '姓名');
            $filter->like('email', '用户名');


        });

        $grid->disableExport();

        $grid->disableRowSelector();

        return $grid;
    }

     protected function form()
    {
        $form = new Form(new User);

        // 创建一个输入框，第一个参数 title 是模型的字段名，第二个参数是该字段描述
        $form->text('name', '姓名')->rules('required');

        $form->text('email', '用户名')->rules('required');

        $form->password('password', '密码')->rules('required|min:6');

        $form->saving(function (Form $form) {
            $form->password=Hash::make($form->password);
        });


        return $form;
    }
}
