<?php
/**
 * Created by PhpStorm.
 * User: zhangbo
 * Date: 17-11-26
 * Time: 下午8:29
 */

namespace admin\controller;

use think\Request;

class Index extends Common
{
    protected $request;

    public function _initialize()
    {
        parent::_initialize();
        $this->request = Request::instance();
    }

    //欢迎页面
    public function index()
    {
        $this->assign('leftNav',0);
        $this->assign('topNav',0);

        return $this->fetch('/index/index');
    }


}
