<?php
namespace app\index\controller;

use app\BaseController;

class Index extends BaseController
{
    public function index()
    {
        return json([1,2,3]);
    }

    public function hello($name = 'ThinkPHP6')
    {
        return 'hello,' . $name;
    }
}
