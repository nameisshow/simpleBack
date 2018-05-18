<?php
/**
 * Created by PhpStorm.
 * User: zhangbo
 * Date: 17-11-26
 * Time: 下午8:29
 */

namespace admin\controller;

use think\cache\driver\Redis;
use think\Request;
use think\Controller;
use think\Session;

class Login extends Controller{
    
    protected $request;

    //系统模型
    protected $systemModel;

    //
    protected $condition;

    public function _initialize()
    {
        parent::_initialize();
        $this->systemModel = model('\admin\system\System');
        $this->request = Request::instance();
        $this->createCondition();
    }

    //构建查询条件
    public function createCondition()
    {
        $this->condition = [
            'table'=>'',
            'join'=>'',
            'where'=>'',
            'field'=>'*',
            'order'=>'',
            'nowPage'=>1,
            'perPage'=>10,
            'data'=>''
        ];
    }

    public function toLogin()
    {
        return $this->fetch('/login/toLogin');
    }

    public function toLoginAjax()
    {
        $request = $this->request;
        $username = $request->param('username') ? trim($request->param('username')) : '';
        if(!$username){
            $res['state'] = 99;
            $res['msg'] = '用户名为空';
            return json($res);
        }
        $password = $request->param('password') ? trim($request->param('password')) : '';
        if(!$password){
            $res['state'] = 97;
            $res['msg'] = '密码为空';
            return json($res);
        }

        $res = $this->systemModel->toLogin($username,$password);

        return json($res);
    }

    //登出
    public function logout()
    {
        Session::delete('user_id');
        header('Location:/admin.php/Login/toLogin');
        die;
    }

    //清除缓存
    public function clearRedis()
    {
        $redis = $this->createRedis();
        $redis->clear();
        $this->logout();
    }

    private function createRedis()
    {
        $config = [
            'host'       => '127.0.0.1',
            'port'       => 6379,
            'password'   => 'zhangbo',
            'select'     => 0,
            'timeout'    => 0,
            'expire'     => 0,
            'persistent' => false,
            'prefix'     => '',
        ];
        return new Redis($config);
    }
}