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
        $condition = $this->condition;
        $condition['table'] = 'user';
        $condition['field'] = '*';
        $condition['where']['username'] = $username;

        $sysModel = $this->systemModel;
        $userInfo = $sysModel->getOne($condition);
        if(!$userInfo){
            $res['state'] = 98;
            $res['msg'] = '用户bu存在';
            return json($res);
        }

        if($userInfo['password'] != md5($password.$userInfo['salt'])){
            $res['state'] = 96;
            $res['msg'] = '用户名或密码错误';
            return json($res);
        }

        Session::set('user_id',$userInfo['user_id']);

        $data['last_time'] = $userInfo['login_time'];
        $data['last_ip'] = $userInfo['login_ip'];
        $data['login_time'] = time();
        $data['login_ip'] = GetIp();
        $data['login_count'] = $userInfo['login_count'] + 1;

        $condition['where']['user_id'] = $userInfo['user_id'];
        unset($condition['where']['username']);
        $condition['data'] = $data;
        $result = $sysModel->getUpd($condition);

        if($result){
           $res['state'] = 100;
           $res['msg'] = '登录成功';
        }else{
            $res['state'] = 95;
            $res['msg'] = '登录失败';
        }

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