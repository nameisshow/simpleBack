<?php
namespace app\index\controller;

use app\index\controller\Base;
use think\Db;

class Index  extends Base
{

    public function index()
    {
        return 'hello,worlds';
    }

    public function login()
    {
        return $this->fetch('/index/login');
    }

    //登录
    public function loginAjax()
    {
        //header("Access-Control-Allow-Origin: *");

        $request = $this->request;
        $userName = trim($request->param('username'));
        $userPass = trim($request->param('userpass'));
        if(!$userPass || !$userName){
            $res['state'] = 98;
            $res['msg'] = 'ssss请输入用户或密码';
            echo json_encode($res);
            die;
        }
        $result = Db::name('user')->where(['user_name'=>$userName,'user_pass'=>md5($userPass)])->find();
        if(!$result){
            $res['state'] = 99;
            $res['msg'] = '没有该用户';
            echo json_encode($res);
            die;
        }

        $res['state'] = 100;
        $res['msg'] = '登录成功';
        $res['data'] = ['user_id'=>$result['user_id']];
        echo json_encode($res);
        die;
    }

    public function im()
    {
        $request = $this->request;
        $user_id = intval(trim($request->param('user_id')));
        if(!$user_id){
            header('Location:/index/index/login');die;
        }
        //用户信息
        $userInfo = Db::name('user')
            ->where(['user_id'=>$user_id])
            ->field('user_id,user_name')
            ->find();



        $this->assign('userInfo',$userInfo);
        return $this->fetch('/index/im');
    }

    //获取聊天列表
    public function getListAjax()
    {
        $request = $this->request;
        $user_id = intval(trim($request->param('user_id')));
        if(!$user_id){
            $res['state'] = 99;
            $res['msg'] = '用户id为空';
            echo json_encode($res);die;
        }

        //聊天列表
        $list = Db::name('list')
            ->alias('l')
            ->join('user u','l.friend_id = u.user_id','left')
            ->where(['master_id'=>$user_id])
            ->order('update_time desc')
            ->field('l.master_id,l.friend_id,u.user_name as friend_name,l.update_time')
            ->select();

        foreach($list as $key=>$val){
            $list[$key]['update_time'] = date('H:i',$val['update_time']);
            $content = Db::name('talker')
                ->where(['send_id'=>$val['friend_id'],'receive_id'=>$user_id])
                ->order('send_time desc')
                ->limit(1)
                ->field('content')
                ->find();
            $list[$key]['content'] = $content['content'];
        }


        //未读数量
        $unReadNum = Db::query('select send_id,receive_id,count(send_id) as unReadNum from talker where receive_id = 1 and is_read = 0 group by send_id');


        foreach($list as $key=>$val){
            $list[$key]['send_time'] = date('H:i:s');
            foreach($unReadNum as $k=>$v){
                if($v['send_id'] == $val['friend_id']){
                    $list[$key]['unReadNum'] = $v['unReadNum'];
                }
            }
        }


        $res['state'] = 100;
        $res['msg'] = '请求成功';
        $res['data'] = $list;
        echo json_encode($res);die;
    }

    //废弃
    //public function getGroup($group_id)
    //{
    //    $group = Db::name('group')
    //        ->where(['group_id'=>$group_id])
    //        ->find();
    //
    //    $content = Db::name('talker')
    //        ->where(['group_id'=>$group_id])
    //        ->order('send_time desc')
    //        ->limit(1)
    //        ->field('content')
    //        ->find();
    //
    //    $group['content'] = $content['content'];
    //
    //    return $group;
    //}

    //获取好友列表
    public function getFriendAjax()
    {
        $request = $this->request;
        $user_id = intval(trim($request->param('user_id')));
        if(!$user_id){
            $res['state'] = 99;
            $res['msg'] = '用户id为空';
            echo json_encode($res);die;
        }

        //获取聊天组
        //废弃

        //获取好友
        $friend = Db::name('friend')
            ->alias('f')
            ->join('user u','f.friend_id = u.user_id','left')
            ->where(['master_id'=>$user_id])
            ->field('f.master_id,f.friend_id,u.user_name as friend_name')
            ->select();

        $res['state'] = 100;
        $res['msg'] = '请求成功';
        $res['data'] = $friend;
        echo json_encode($res);die;

    }

    //初始化信息
    public function getMessageAjax()
    {
        $request = $this->request;
        $user_id = intval(trim($request->param('user_id')));
        $friend_id = intval(trim($request->param('friend_id')));

        if(!$user_id || !$friend_id){
            $res['state'] = 99;
            $res['msg'] = '数据缺失';
            echo json_encode($res);die;
        }

        $message = Db::query("select * from talker where (send_id = $user_id and receive_id = $friend_id) or (receive_id = $user_id and send_id = $friend_id) order by send_time asc");

        $friend_name = Db::name('user')
            ->where(['user_id'=>$friend_id])
            ->field('user_name')
            ->find();

        Db::query("update talker set is_read = 1 where (send_id = $user_id and receive_id = $friend_id and is_read = 0) or (receive_id = $user_id and send_id = $friend_id and is_read = 0)");

        $res['state'] = 100;
        $res['msg'] = '请求成功';
        $res['data'] = ['message'=>$message,'friend_name'=>$friend_name['user_name']];
        echo json_encode($res);die;

    }


}