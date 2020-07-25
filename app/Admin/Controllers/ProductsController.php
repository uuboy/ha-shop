<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProductsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '货物';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product);

        $grid->title('货品名称')->sortable();
        $grid->type('货品规格')->sortable();
        $grid->location('货架号')->sortable();
        $grid->stock('库存数量')->sortable();
        $grid->on_sale('已上架')->display(function ($value) {
            return $value ? '是' : '否';
        });

        $grid->actions(function ($actions) {
            $actions->disableView();
            // $actions->disableDelete();
        });
        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        return $grid;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Product);

        // 创建一个输入框，第一个参数 title 是模型的字段名，第二个参数是该字段描述
        $form->text('title', '货品名称')->rules('required');

        $form->text('type', '货品规格')->rules('required');

        $form->text('location', '货架号')->rules('required');


        $form->text('stock', '库存数量')->rules('required|numeric')->default(0);

        // 创建一组单选框
        $form->radio('on_sale', '上架')->options(['1' => '是', '0'=> '否'])->default('1');

        // 直接添加一对多的关联模型
        // $form->hasMany('skus', 'SKU 列表', function (Form\NestedForm $form) {
        //     $form->text('title', 'SKU 名称')->rules('required');
        //     $form->text('description', 'SKU 描述')->rules('required');
        //     $form->text('price', '单价')->rules('required|numeric|min:0.01');
        //     $form->text('stock', '剩余库存')->rules('required|integer|min:0');
        // });




        return $form;
    }
}
