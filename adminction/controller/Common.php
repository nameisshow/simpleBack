<?php
/**
 * Created by PhpStorm.
 * User: zhangbo
 * Date: 17-11-26
 * Time: 下午8:36
 */

namespace admin\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class Common extends Controller
{
    //系统模型
    protected $systemModel;

    //查询条件
    protected $condition = [];
    //ajax参数
    protected $ajaxParam = [];

    //return的参数
    protected $result = [
        'status' => 1,
        'msg' => '未操作',
        'data' => null,
    ];

    public function _initialize()
    {
        parent::_initialize();
        $this->isLogin();
        $this->systemModel = model('\admin\system\System');
        $this->getUserInfo();
        $this->getMenu();
        $this->getButton();
        $this->getNav();
        //$this->createCondition();
        //$this->createAjaxParam();
    }


    protected function returnAjax()
    {
        echo json_encode($this->result);die;
    }

    //是否登陆
    public function isLogin()
    {
        $user_id = Session::get('user_id');
        if (!isset($user_id)) {
            header('Location:/admin.php/Login/toLogin');
            die;
        }
    }

    protected function filter(){
        Request::instance()->filter(['strip_tags','htmlspecialchars']);
    }

    //构建查询条件
    public function createCondition()
    {
        $this->condition = [
            'table' => '',
            'join' => '',
            'where' => '',
            'field' => '*',
            'order' => '',
            'nowPage' => 1,
            'perPage' => 10,
            'data' => ''
        ];
    }

    //构建ajax参数
    public function createAjaxParam()
    {
        $this->ajaxParam = [
            'table' => '',//表名
            'button_event' => '',//按钮类型del,upd....
            'primaryKey' => '',//表主键
            'primaryVal' => '',//被操作的所有主键，以逗号拼接的字符串
            'field' => '',//要修改的字段
            'value' => ''//要修改的值
        ];
    }


    /********初始化方法组***********/
    //获取用户信息
    public function getUserInfo()
    {
        $userInfo = $this->systemModel->getUserInfo();
        $this->assign('adminInfo', $userInfo);
    }

    //获取用户菜单信息
    public function getMenu()
    {
        $menu = $this->systemModel->getMenu();
        $this->assign('topMenu', $menu['topMenu']);
        $this->assign('leftMenu', $menu['leftMenu']);
    }

    //获取用户按钮信息
    public function getButton()
    {
        $module_url = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $button = $this->systemModel->getButton($module_url);
        $this->assign('button', $button);
    }

    //获取curr，识别导航信息
    public function getNav()
    {
        $module_url = Request::instance()->path();
        if(strpos($module_url,'/') !== 0){
            $module_url = '/'.$module_url;
        }
        $code = Db::name('module')
            ->field('module_code')
            ->where(['module_url'=>$module_url])
            ->find()['module_code'];
        if($code){
            $code = explode('-',$code);
            $this->assign('topNav',$code[0]);
            $this->assign('leftNav',$code[2]);
        }
    }

    //获取六位随机字符串
    function getSalt()
    {
        $arr = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
        $count = 0;
        $salt = '';
        while ($count < 6) {
            $salt .= $arr[rand(0, count($arr) - 1)];
            $count++;
        }
        return $salt;
    }


















    //公共ajax方法，可用于删除，更改单个字段
    public function commonAjax($type, $table, $condition = [], $data = [])
    {
        if (!$type) {
            $this->result['status'] = 99;
            $this->result['msg'] = '请指定操作类型';
            $this->returnAjax();
        }
        if($type != 'delete'){
            if (!$data) {
                $this->result['status'] = 98;
                $this->result['msg'] = '请指定操作键和值';
                $this->returnAjax();
            }
            if (!$condition[0]) {
                $this->result['status'] = 97;
                $this->result['msg'] = '键为空';
                $this->returnAjax();
            }
            //if (!$condition[1]) {
            //    $this->result['status'] = 93;
            //    $this->result['msg'] = '值为空';
            //    $this->returnAjax();
            //}
        }
        if (!$table) {
            $this->result['status'] = 96;
            $this->result['msg'] = '请指定操作表格';
            $this->returnAjax();
        }
        if (!$condition) {
            $this->result['status'] = 95;
            $this->result['msg'] = '请指定操作主键和主键值';
            $this->returnAjax();
        }
        if (!$condition[0]) {
            $this->result['status'] = 94;
            $this->result['msg'] = '主键为空';
            $this->returnAjax();
        }
        if (!$condition[1]) {
            $this->result['status'] = 93;
            $this->result['msg'] = '主键值为空';
            $this->returnAjax();
        }
        if (strpos($condition[1], ',') !== false) {
            $where[$condition[0]] = ['IN', explode(',', $condition[1])];
        } else {
            $where[$condition[0]] = $condition[1];
        }
        if($type == 'delete'){
            Db::startTrans();
            try{
                $result = Db::name($table)->where($where)->delete();
                $this->delRoleField($where);
                //提交事务
                Db::commit();

            }catch(\Exception $e){
                //回滚事务
                Db::rollback();

                $this->result['status'] = 92;
                $this->result['msg'] = '删除失败';
                $this->returnAjax();
            }

        }else{
            $result = Db::name($table)->where($where)->update($data);
        }


        if ($result) {
            $this->result['status'] = 100;
            $this->result['msg'] = '操作成功';
        } else {
            $this->result['status'] = 91;
            $this->result['msg'] = '操作失败';
        }
        $this->returnAjax();
    }

    //删除button和模块时顺便将role中module_id和button_json删掉
    protected function delRoleField($where){

        $where['module_id'] && ($modules = $where['module_id']);
        $where['button_id'] && ($buttons = $where['button_id']);

        if($modules){
            if(!is_array($modules)){
                $modules = [$modules];
            }
        }else if($buttons){
            if(!is_array($buttons)){
                $buttons = [$buttons];
            }
        }else{
            return false;
        }

        $roles = Db::name('role')
            ->select();

        foreach($roles as $key=>$val){
            if($modules){
                $moduleArray = explode(',', $val['module_id']);
                foreach($moduleArray as $k=>$v){
                    foreach($modules as $kk=>$vv){
                        if(in_array($vv, $moduleArray)){
                            unset($moduleArray[$k]);
                        }
                    }
                }
                //TODO
                //删除子模块
                Db::name('role')->where(['role_id'=>$val['role_id']])->update(['module_id'=>implode(',', $moduleArray)]);
            }else if($buttons){
                $buttonArray = json_decode($val['button_json'],true);
                foreach($buttonArray as $key=>$val){
                    foreach($val as $k=>$v){
                        foreach($buttons as $kk=>$vv){
                            if(in_array($vv, $buttonArray)){
                                unset($buttonArray[$key][$k]);
                            }
                        }
                    }
                }
                Db::name('role')->where(['role_id'=>$val['role_id']])->update(['module_id'=>json_encode($buttonArray)]);
            }
        }
    }


}