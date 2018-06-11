<?php
/**
 * Created by PhpStorm.
 * User: zhangbo
 * Date: 17-11-26
 * Time: 下午8:29
 */

namespace admin\controller;

use think\Db;
use think\Request;

class System extends Common{


    protected $request;

    public function _initialize()
    {
        parent::_initialize();
        $this->request = Request::instance();
        $this->filter();
    }

    public function test()
    {
        $module_code = $this->joinCode(124);
        dump($module_code);die;
    }

    public function joinCode($pid){
        static $module_code = [];
        $res = Db::name('module')
            ->field('module_id,module_pid,module_code')
            ->where(['module_id'=>$pid])
            ->find();
        $module_code[] = $res['module_id'];
        if($res['module_pid'] == 0){
            return $module_code;
        }else{
            return $this->joinCode($res['module_pid']);
        }

    }

    /************按钮部分***************/

    public function button()
    {
        $result = $this->systemModel->getButtonPage();
        //分配变量
        $this->assign('data',$result['data']);
        $this->assign('total',$result['total']);
        $this->assign('page',$result['page']);
        $this->assign('perPage',$result['perPage']);
        $this->assign('search',$result['search']);

        //渲染模板
        return $this->fetch('/system/button');
    }

    //删除按钮
    public function buttonDel()
    {
        $id = input('primary',0);
        $this->commonAjax('delete','button',['button_id',$id]);
    }

    //更改按钮数据
    public function buttonUpd()
    {
        if(Request::instance()->isPost()){

            $where['button_id'] = input('button_id/d',0);

            $data['button_name'] = input('button_name/s','');
            $data['button_event'] = input('button_event/s','');
            $data['button_sort'] = input('button_sort/d',0);
            $data['button_desc'] = input('button_desc/s','');

            $result = $this->systemModel->buttonUpdate('button',$where,$data);

            if($result){
                $this->result['status'] = 100;
                $this->result['msg'] = '修改成功';
            }else if($result == 0){
                $this->result['status'] = 99;
                $this->result['msg'] = '没有数据被修改';
            }else{
                $this->result['status'] = 98;
                $this->result['msg'] = '修改失败';
            }
            $this->returnAjax();

        }else{

            $button_id = input('get.buttonId/d',0);
            $info = $this->systemModel->getButtonInfo($button_id);
            $this->assign('buttonInfo',$info);
            return $this->fetch('/system/buttonUpd');

        }
    }

    //添加按钮数据
    public function buttonAdd()
    {
        if(Request::instance()->isPost()){

            $data['button_name'] = input('button_name/s','');
            $data['button_event'] = input('button_event/s','');
            $data['button_sort'] = input('button_sort/d',0);
            $data['button_desc'] = input('button_desc/s','');

            $result = $this->systemModel->buttonAdd('button',$data);

            if($result){
                $this->result['status'] = 100;
                $this->result['msg'] = '添加成功';
            }else{
                $this->result['status'] = 99;
                $this->result['msg'] = '添加失败';
            }
            $this->returnAjax();

        }else{

            return $this->fetch('/system/buttonAdd');

        }
    }



    /************模块部分***************/


    //模块管理
    public function module()
    {
        //树状菜单
        $module = $this->getModule(1);
        //分配变量
        $this->assign('data',$module);

        //渲染模板
        return $this->fetch('/system/module');
    }

    //添加模块
    public function moduleAdd()
    {
        if(Request::instance()->isPost()){

            $data['module_name'] = trim(input('module_name/s',''));
            $data['module_pid'] = input('module_pid/d',0);
            $data['module_sort'] = input('module_sort/d',0);
            $data['module_url'] = trim(input('module_url/s',''));
            $data['module_code'] = '';

            if(!$data['module_name']){
                $this->result['status'] = 99;
                $this->result['msg'] = '模块名为空';
                $this->returnAjax();
            }

            $result = $this->systemModel->moduleAdd('module',$data);

            if($result){
                $this->result['status'] = 100;
                $this->result['msg'] = '添加成功';
            }else{
                $this->result['status'] = 97;
                $this->result['msg'] = '添加失败';
            }

            $this->returnAjax();

        }else{

            //树状菜单
            $module = $this->getModule();
            //dump($res);die;
            $this->assign('data',$module);
            return $this->fetch('/system/moduleAdd');

        }
    }

    //添加模块
    public function moduleUpd()
    {

        if(Request::instance()->isPost()){

            $where['module_id'] = input('module_id/d',0);

            $data['module_name'] = trim(input('module_name/s',''));
            $data['module_pid'] = input('module_pid/d',0);
            $data['module_sort'] = input('module_sort/d',0);
            $data['module_url'] = trim(input('module_url/s',''));

            if(!$where['module_id']){
                $this->result['status'] = 99;
                $this->result['msg'] = '模块id为空';
                $this->returnAjax();
            }

            if(!$data['module_name']){
                $this->result['status'] = 98;
                $this->result['msg'] = '模块名为空';
                $this->returnAjax();
            }

            $result = $this->systemModel->moduleUpdate('module', $where, $data);

            if($result){
                $this->result['status'] = 100;
                $this->result['msg'] = '编辑成功';
            }else{
                $this->result['status'] = 97;
                $this->result['msg'] = '编辑失败';
            }

            $this->returnAjax();

        }else{

            $module_id = input('module_id/d',0);

            //树状菜单
            $module = $this->getModule();

            //查询本分类信息
            foreach($module as $key=>$val){
                if($val['module_id'] == $module_id){
                    $val['module_name'] = str_replace('&nbsp;','',$val['module_name']);
                    $this->assign('moduleInfo',$val);
                }
            }

            $this->assign('data',$module);
            return $this->fetch('/system/moduleUpd');

        }
    }

    protected function getModule($isList = 0){
        $module = $this->systemModel->getModuleTree();
        foreach($module as $key=>$val){
            $prefix = '';
            if($isList){
                $module[$key]['module_name'] = '<img src="__PUBLIC__/admin/img/down.png" style="width: 20px;margin-right: 5px;margin-top: -5px;"/>'.$module[$key]['module_name'];
            }
            for($i = 0; $i < $val['level'] - 1; $i++){
                $prefix .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            }
            $module[$key]['module_name'] = $prefix.$module[$key]['module_name'];
        }

        return $module;
    }

    //删除按钮
    public function moduleDel()
    {
        $id = input('primary',0);
        $this->commonAjax('delete','module',['module_id',$id]);
    }

    //未模块分配按钮
    public function assignButton()
    {
        if(Request::instance()->isPost()){
            $module_id = input('module_id/d',0);
            if(!$module_id){
                $this->result['status'] = 99;
                $this->result['msg'] = '模块id不存在';
                $this->returnAjax();
            }
            $button_id = input('button_id/s','');

            $res = $this->systemModel->setButtonWithModule($module_id, $button_id);

            if($res){
                $this->result['status'] = 100;
                $this->result['msg'] = '修改成功';
            }else if($res == 0){
                $this->result['status'] = 97;
                $this->result['msg'] = '没有任何数据被修改';
            }else{
                $this->result['status'] = 96;
                $this->result['msg'] = '修改失败';
            }

            $this->returnAjax();

        }else{

            $module_id = input('module_id/d',0);
            $button_id = $this->systemModel->getButtonWithModule($module_id);
            $this->assign('module_id',$module_id);
            $this->assign('selfButton',$button_id);
            return $this->fetch('/system/moduleAssignButton');

        }
    }

    public function getButtonAjax()
    {
        $module_id = input('module_id/d',0);
        $where = [];
        if($module_name = trim(input('module_name/s',''))){
            $where['button_name'] = ['like','%'.$module_name.'%'];
        }
        $buttons = $this->systemModel->getButtonList($where);
        $buttonsIds = $this->systemModel->getButtonWithModule($module_id);

        if($buttonsIds){
            //存在自己的按钮
            $selfButton = explode(',',$buttonsIds);
            foreach($buttons as $key=>$val){
                if(in_array($val['button_id'],$selfButton)){
                    $buttons[$key]['isYet'] = 1;
                }
            }
        }else{
            $selfButton = '';
        }

        if($buttons){
            $this->result['status'] = 100;
            $this->result['msg'] = '请求成功';
            $this->result['data'] = $buttons;
            $this->result['selfButton'] = $selfButton;
            $this->returnAjax();
        }

    }


    /****************管理员管理*************/
    public function admin()
    {

        $admins = $this->systemModel->getAdminList();

        $this->assign('data',$admins);

        return $this->fetch('/system/admin');
    }

    public function adminAdd()
    {
        if(Request::instance()->isPost()){
            //构造检验数据
            if($username = trim(input('username/s',''))){
                $data['username'] = $username;
            }else{
                $this->result['status'] = 99;
                $this->result['msg'] = '用户名为空';
                $this->returnAjax();
            }

            if($relaname = trim(input('relaname/s',''))){
                $data['relaname'] = $relaname;
            }else{
                $this->result['status'] = 98;
                $this->result['msg'] = '真实姓名为空';
                $this->returnAjax();
            }

            if($mobile = trim(input('mobile/s',''))){
                $data['mobile'] = $mobile;
            }else{
                $this->result['status'] = 97;
                $this->result['msg'] = '手机号为空';
                $this->returnAjax();
            }

            if(!preg_match('/^1[3458]\d{9}$',$data['mobile'])){
                $this->result['status'] = 93;
                $this->result['msg'] = '手机号格式不正确';
                $this->returnAjax();
            }

            if($role_id = input('role_id/d',0)){
                $data['role_id'] = $role_id;
            }else{
                $this->result['status'] = 96;
                $this->result['msg'] = '管理员类型为空';
                $this->returnAjax();
            }

            if($password = trim(input('password/s',''))){
                $data['password'] = $password;
                $data['salt'] = $this->getSalt();
                $data['password'] = md5($data['password'].$data['salt']);
                $data['addtime'] = time();
            }else{
                $this->result['status'] = 95;
                $this->result['msg'] = '密码为空';
                $this->returnAjax();
            }

            $result = $this->systemModel->addAdmin($data);

            if($result){
                $this->result['status'] = 100;
                $this->result['msg'] = '添加成功';
            }else{
                $this->result['status'] = 95;
                $this->result['msg'] = '添加失败';
            }

            $this->returnAjax();

        }else{

            $role = $this->systemModel->getRoleList();
            $this->assign('role',$role);

            return $this->fetch('/system/adminAdd');

        }
    }

    public function adminUpd()
    {
        $where['user_id'] = input('user_id/d',0);

        if(Request::instance()->isPost()){
            //构造检验数据
            if($username = trim(input('username/s',''))){
                $data['username'] = $username;
            }else{
                $this->result['status'] = 99;
                $this->result['msg'] = '用户名为空';
                $this->returnAjax();
            }

            if($relaname = trim(input('relaname/s',''))){
                $data['relaname'] = $relaname;
            }else{
                $this->result['status'] = 98;
                $this->result['msg'] = '真实姓名为空';
                $this->returnAjax();
            }

            if($mobile = trim(input('mobile/s',''))){
                $data['mobile'] = $mobile;
            }else{
                $this->result['status'] = 97;
                $this->result['msg'] = '手机号为空';
                $this->returnAjax();
            }

            if(!preg_match('/^1[3458]\d{9}$/',$data['mobile'])){
                $this->result['status'] = 93;
                $this->result['msg'] = '手机号格式不正确';
                $this->returnAjax();
            }

            if($role_id = input('role_id/d',0)){
                $data['role_id'] = $role_id;
            }else{
                $this->result['status'] = 96;
                $this->result['msg'] = '管理员类型为空';
                $this->returnAjax();
            }

            if($password = trim(input('password/s',''))){
                $data['password'] = $password;
                $data['salt'] = $this->getSalt();
                $data['password'] = md5($data['password'].$data['salt']);
                $data['addtime'] = time();
            }

            $result = $this->systemModel->updateAdmin($where, $data);

            if($result){
                $this->result['status'] = 100;
                $this->result['msg'] = '修改成功';
            }else if($result == 0){
                $this->result['status'] = 95;
                $this->result['msg'] = '没有任何数据被修改';
            }else{
                $this->result['status'] = 94;
                $this->result['msg'] = '修改失败';
            }

            $this->returnAjax();

        }else{

            $role = $this->systemModel->getRoleList();
            $this->assign('role',$role);

            $user_id = input('user_id/d',0);
            $userInfo = $this->systemModel->getAdmin($user_id);
            $this->assign('userInfo',$userInfo);

            return $this->fetch('/system/adminUpd');

        }
    }

    //删除按钮
    public function adminDel()
    {
        $id = input('primary',0);
        $this->commonAjax('delete','admin',['user_id',$id]);
    }




    /*********角色管理*********/
    public function role()
    {

        $data = $this->systemModel->getRoleList();

        //分配变量
        $this->assign('data',$data);

        //渲染模板
        return $this->fetch('/system/role');
    }

    public function roleAdd()
    {
        if(Request::instance()->isPost()){
            //构造检验数据
            if($role_name = trim(input('role_name/s',''))){
                $data['role_name'] = $role_name;
            }else{
                $this->result['status'] = 99;
                $this->result['msg'] = '角色名为空';
                $this->returnAjax();
            }

            if($role_desc = trim(input('role_desc/s',''))){
                $data['role_desc'] = $role_desc;
            }

            $data['addtime'] = time();

            $result = $this->systemModel->addRole($data);

            if($result){
                $this->result['status'] = 100;
                $this->result['msg'] = '添加成功';
            }else{
                $this->result['status'] = 98;
                $this->result['msg'] = '添加失败';
            }

            $this->returnAjax();

        }else{

            return $this->fetch('/system/roleAdd');

        }
    }

    public function roleUpd()
    {

        if(Request::instance()->isPost()){

            $where['role_id'] = input('role_id/d',0);

            if($role_name = trim(input('role_name/s',''))){
                $data['role_name'] = $role_name;
            }else{
                $this->result['status'] = 99;
                $this->result['msg'] = '角色名为空';
                $this->returnAjax();
            }

            if($role_desc = trim(input('role_desc/s',''))){
                $data['role_desc'] = $role_desc;
            }

            //$data['addtime'] = time();


            $result = $this->systemModel->updateRole($where, $data);


            if($result){
                $this->result['status'] = 100;
                $this->result['msg'] = '修改成功';
            }else if($result == 0){
                $this->result['status'] = 95;
                $this->result['msg'] = '没有任何数据被修改';
            }else{
                $this->result['status'] = 94;
                $this->result['msg'] = '修改失败';
            }

            $this->returnAjax();

        }else{

            $role_id = input('role_id/d',0);

            $roleInfo = $this->systemModel->getRole($role_id);

            $this->assign('roleInfo',$roleInfo);
            return $this->fetch('/system/roleUpd');

        }
    }

    //删除按钮
    public function roleDel()
    {
        $id = input('primary',0);
        $this->commonAjax('delete','role',['role_id',$id]);
    }




    /****************模块权限*************/
    public function rolePrevm()
    {
        if(Request::instance()->isPost()){

            $where['role_id'] = input('role_id/d',0);
            if(!$where['role_id']){
                $this->result['status'] = 99;
                $this->result['msg'] = '角色id不存在';
                $this->returnAjax();
            }

            $data['module_id'] = input('module_id/s','');
            if(!$data['module_id']){
                $this->result['status'] = 98;
                $this->result['msg'] = '模块id不存在';
                $this->returnAjax();
            }


            $result = $this->systemModel->setRolePrevm($where, $data);

            if($result){
                $this->result['status'] = 100;
                $this->result['msg'] = '模块权限分配成功';
            }else if($result == 0){
                $this->result['status'] = 97;
                $this->result['msg'] = '没有任何数据被修改';
            }else{
                $this->result['status'] = 96;
                $this->result['msg'] = '模块权限分配失败';
            }

            $this->returnAjax();

        }else{

            //模块
            $res = $this->systemModel->getModuleTree();

            //当前角色
            $role_id = input('role_id/d',0);
            $moduleIds = $this->systemModel->getModuleWithRole($role_id);


            //当前角色已选择的模块
            $selfModule = explode(',',$moduleIds);
            $checked = '<input type="checkbox" lay-skin="primary" checked>';
            $nocChecked = '<input type="checkbox" lay-skin="primary">';
            foreach($res as $key=>$val){
                if(in_array($val['module_id'],$selfModule)){
                    $res[$key]['module_name'] = $checked.$res[$key]['module_name'];
                }else{
                    $res[$key]['module_name'] = $nocChecked.$res[$key]['module_name'];
                }
                $prefix = '';
                for($i = 0; $i < $val['level'] - 1; $i++){
                    $prefix .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                }
                $res[$key]['module_name'] = $prefix . $res[$key]['module_name'];
            }


            $this->assign('data',$res);
            $this->assign('role_id',$role_id);
            $this->assign('selfModule',$moduleIds);
            return $this->fetch('/system/rolePrevm');

        }
    }


    /****************按钮权限*************/
    public function rolePrevb()
    {
        if(Request::instance()->isPost()){

            //当前角色
            $role_id = input('role_id/d',0);
            if(!$role_id){
                $this->result['status'] = 99;
                $this->result['msg'] = '角色id不存在';
                $this->returnAjax();
            }
            $moduleArray = $_POST['modules'] ? $_POST['modules'] : [];
            if(!$moduleArray){
                $this->result['status'] = 98;
                $this->result['msg'] = '模块id组为空';
                $this->returnAjax();
            }
            $buttonArray = $_POST['buttons'] ? $_POST['buttons'] : [];
            if(!$buttonArray){
                $this->result['status'] = 97;
                $this->result['msg'] = '按钮组为空';
                $this->returnAjax();
            }

            $button_json = [];

            foreach($moduleArray as $key=>$val){
                if($buttonArray[$key]){
                    //该模块下呗选择的按钮不为空
                    $button_json[$val] = explode(',',$buttonArray[$key]);
                }
            }

            if(!$button_json){
                $button_json = '';
            }else{
                $button_json = json_encode($button_json);
            }

            $result = $this->systemModel->setButtonJson($role_id, $button_json);

            if($result){
                $this->result['status'] = 100;
                $this->result['msg'] = '按钮分配成功';
            }else if($result == 0){
                $this->result['status'] = 95;
                $this->result['msg'] = '没有任何数据被修改';
            }else{
                $this->result['status'] = 96;
                $this->result['msg'] = '按钮分配失败';
            }

            $this->returnAjax();


        }else{
            //当前角色
            $role_id = input('role_id/d',0);
            $moduleIds = $this->systemModel->getModuleWithRole($role_id);
            //模块
            $res = $this->systemModel->getModuleTree();

            foreach($res as $key=>$val){
                if(strpos($moduleIds,(string)$val['module_id']) === false){
                    unset($res[$key]);
                    continue;
                }
                $prefix = '';
                for($i = 0; $i < $val['level'] - 1; $i++){
                    $prefix .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                }
                $res[$key]['module_name'] = $prefix . $res[$key]['module_name'];
            }

            if($res){
                $res = $this->assignYet($role_id,$res);
                $this->assign('data',$res);
            }
            //dump($res);die;

            $this->assign('role_id',$role_id);

            return $this->fetch('/system/rolePrevb');
        }
    }

    //获取相应模块的按钮
    public function getButtonOfRoleAjax()
    {
        $role_id = input('role_id/d',0);
        if(!$role_id){
            $this->result['status'] = 99;
            $this->result['msg'] = '角色ID为空';
            $this->returnAjax();
        }
        $module_id = input('module_id/d',0);
        if(!$module_id){
            $this->result['status'] = 98;
            $this->result['msg'] = '模块id为空';
            $this->returnAjax();
        }

        //获取改模块的所有按钮id
        $button_ids = $this->systemModel->getButtonWithModule($module_id);

        if($button_ids){
            $where['button_id'] = ['in',$button_ids];
            $buttons = $this->systemModel->getButtonList($where);
        }else{
            $buttons = [];
        }

        //获取当前角色在模块下拥有的按钮
        $result = $this->setYet($role_id,$module_id,$buttons);
        if($result){
            $buttons = $result;
        }

        $this->result['status'] = 100;
        $this->result['msg'] = '获取成功';
        $this->result['data'] = $buttons;
        $this->returnAjax();
    }

    //从所有模块中标记出已有的按钮
    public function assignYet($role_id,$module)
    {
        $button_json = $this->systemModel->getButtonJson($role_id);

        //该用户没有button_json,那么所有的value值都不存在
        if(!$button_json){
            foreach($module as $key=>$val){
                $module[$key]['button_id'] = '';
            }
            return $module;
        }

        //该用户有button_json，将buttons_json转化成数组
        //button_json中的按钮数组转成字符串覆盖相应模块
        //这样就构造了真实的模块--按钮对应情况
        $buttonArray = json_decode($button_json,true);
        foreach($module as $key=>$val){
            //判断在button_json中该模块下是否有数据存在
            $flag = 0;
            $index = 0;
            foreach($buttonArray as $k=>$v){
                if($val['module_id'] == $k){
                    $flag = 1;
                    $index = $k;
                }
            }
            //没有该模块数据，直接将对应模块的button_id值设为空
            if($flag == 0){
                $module[$key]['button_id'] = '';
            }else{
                //有该模块数据

                //在json中这个模块下是否是空数组
                if(count($buttonArray[$val['module_id']]) > 0 ){
                    $module[$key]['button_id'] = implode(',',$buttonArray[$val['module_id']]);
                }else{
                    $module[$key]['button_id'] = '';
                }

            }
        }


        return $module;
    }

    //从json中找出和当前模块所有按钮的重合
    public function setYet($role_id,$module_id,$buttons)
    {

        if(!$buttons){
            return [];
        }

        $button_json = $this->systemModel->getButtonJson($role_id);
        $allButton = json_decode($button_json,true);

        if($allButton){
            //存在button_json

            //判断button_json中是否有该模块
            $flag = 0;
            foreach($allButton as $key=>$val){
                if($module_id == $key){
                    $flag = 1;
                }
            }

            if($flag == 0){
                //button_json中不存在这个模块
                return false;
            }

            //button_json存在这个模块
            $selfButton = $allButton[$module_id];
            if(!$selfButton){
                //改模块下没有按钮
                return false;
            }
            //该模块下有按钮
            foreach($buttons as $key=>$val){
                if(in_array($val['button_id'],$selfButton)){
                    $buttons[$key]['isYet'] = 1;
                }else{
                    $buttons[$key]['isYet'] = 0;
                }
            }
            return $buttons;
        }else{
            //不存在button_json

            return false;
        }
    }

}