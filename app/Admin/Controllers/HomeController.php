<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('辉昂仓库管理系统后台')
            ->description('辉昂仓库管理系统后台')
            ->row('<h1 class="text-center">欢迎来到辉昂仓库管理系统后台</h1>');

    }
}
