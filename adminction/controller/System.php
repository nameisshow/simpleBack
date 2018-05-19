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


    //分配按钮
    public function moduleAssignButton()
    {
        $module_id = intval($this->request->param('module_id'));
        $sysModel = $this->systemModel;
        $condition = $this->condition;
        $condition['table'] = 'module';
        $condition['field'] = 'button_id';
        $condition['where']['module_id'] = $module_id;
        $button_id = $sysModel->getOne($condition)['button_id'];
        $this->assign('module_id',$module_id);
        $this->assign('selfButton',$button_id);
        return $this->fetch('/index/moduleAssignButton');
    }

    //获取按钮ajax
    public function getButtonAjax()
    {
        //输入
        $request = $this->request;
        //继承系统模型
        $sysModel = $this->systemModel;
        //条件
        $condition = $this->condition;

        $condition['table'] = 'button';

        $module_id = intval($request->param('module_id'));

        if($request->param('button_name')){
            $condition['where']['button_name'] = ['like','%'.trim($request->param('button_name')).'%'];
        }
        //获取所有按钮
        $buttons = $sysModel->getAll($condition);

        //获取当前模块按钮
        $condForModule = $this->condition;
        $condForModule['table'] = 'module';
        $condForModule['field'] = 'button_id';
        $condForModule['where']['module_id'] = $module_id;

        $buttonsIds = $sysModel->getOne($condForModule)['button_id'];

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
            $res['state'] = 100;
            $res['msg'] = '请求成功';
            $res['data'] = $buttons;
            $res['selfBUtton'] = $selfButton;
            return json($res);
        }
    }

    public function assignButton()
    {
        //输入
        $request = $this->request;
        //条件
        $condition = $this->condition;

        $module_id = $request->param('module_id') ? intval($request->param('module_id')) : 0;

        if(!$module_id){
            $res['state'] = 99;
            $res['msg'] = '模块id不存在';
            return json($res);
        }

        $condition['where']['module_id'] = $module_id;

        $button_id = $request->param('button_id') ? $request->param('button_id') : '';

        $condition['data']['button_id'] = $button_id;

        $condition['table'] = 'module';

        //继承系统模型
        $sysModel = $this->systemModel;

        $insertId = $sysModel->getUpd($condition);

        if($insertId){
            $res['state'] = 100;
            $res['msg'] = '修改成功';
        }else{
            $res['state'] = 98;
            $res['msg'] = '修改失败';
        }

        return json($res);

    }


    /*********角色管理*********/
    public function role()
    {
        //分页查询条件
        $condition = $this->condition;
        $condition['table'] = 'role';

        //继承系统模型
        $sysModel = $this->systemModel;
        //开始查询
        $data = $sysModel->getAll($condition);

        //分配变量
        $this->assign('data',$data);
        $this->assign('leftNav',5);
        $this->assign('topNav',1);

        //渲染模板
        return $this->fetch('/index/role');
    }

    public function roleAdd()
    {
        //参数对象
        $request = $this->request;
        //继承系统模型
        $sysModel = $this->systemModel;
        if($request->isPost()){
            //构造检验数据
            $data = $this->createRoleData($request);
            //继承系统模型
            $sysModel = $this->systemModel;
            //插入数据
            $condition = $this->condition;
            $condition['table'] = 'role';
            $condition['data'] = $data;
            //dump($condition);die;
            $result = $sysModel->getAdd($condition);
            if($result){
                $res['state'] = 100;
                $res['msg'] = '添加成功';
            }else{
                $res['state'] = 96;
                $res['msg'] = '添加失败';
            }

            return json($res);

        }else{

            return $this->fetch('/index/roleAdd');

        }
    }

    public function roleUpd()
    {
        //参数对象
        $request = $this->request;
        //继承系统模型
        $sysModel = $this->systemModel;
        if($request->isPost()){
            //构造检验数据
            $data = $this->createRoleData($request);
            //继承系统模型
            $sysModel = $this->systemModel;
            //插入数据
            $condition = $this->condition;
            $condition['table'] = 'role';
            $condition['data'] = $data;
            $role_id = $request->param('role_id');
            $condition['where']['role_id'] = $role_id;
            //dump($condition);die;
            $result = $sysModel->getUpd($condition);
            if($result){
                $res['state'] = 100;
                $res['msg'] = '修改成功';
            }else{
                $res['state'] = 96;
                $res['msg'] = '修改失败';
            }

            return json($res);

        }else{

            $role_id = $request->param('role_id') ? intval($request->param('role_id')) : 0;
            $condition = $this->condition;
            $condition['table'] = 'role';
            $condition['where']['role_id'] = $role_id;
            $sysModel = $this->systemModel;
            $roleInfo = $sysModel->getOne($condition);
            $this->assign('roleInfo',$roleInfo);
            return $this->fetch('/index/roleUpd');

        }
    }

    public function createRoleData($request)
    {
        if($request->param('role_name')){
            $data['role_name'] = trim($request->param('role_name'));
        }else{
            $res['state'] = 99;
            $res['msg'] = '角色名为空';
            return json($res);
        }

        if($request->param('role_desc')){
            $data['role_desc'] = trim($request->param('role_desc'));
        }

        $data['addtime'] = time();

        return $data;
    }

    //按钮删除
    public function roleDel()
    {
        //接受参数
        $ids = $this->request->param('ids');
        $button_event = $this->request->param('button_event');
        $primaryKey = $this->request->param('primaryKey');
        //继承ajaxParam
        $ajaxParam = $this->ajaxParam;
        //设置参数
        $ajaxParam['table'] = 'role';
        $ajaxParam['button_event'] = $button_event;
        $ajaxParam['primaryKey'] = $primaryKey;
        $ajaxParam['primaryVal'] = $ids;
        //发起操作
        $res = $this->commonAjax($ajaxParam);
        //返回json
        return json($res);
    }




    /****************模块权限*************/
    public function rolePrevm()
    {
        //输入对象
        $request = $this->request;
        //sys模型
        $sysModel = $this->systemModel;
        //查询条件
        $condition = $this->condition;
        $condition['table'] = 'module';
        //开始查询
        $data = $sysModel->getAll($condition);
        $res = $this->treeForModuleThree($data,0,0);

        //当前角色
        $role_id = $request->param('role_id');
        $condition['table'] = 'role';
        $condition['field'] = 'module_id';
        $condition['where']['role_id'] = $role_id;
        $moduleIds = $sysModel->getOne($condition)['module_id'];

        //当前角色已选择的模块
        if($moduleIds){
            $selfModule = explode(',',$moduleIds);
            $search = '<input type="checkbox" lay-skin="primary">';
            $replace = '<input type="checkbox" lay-skin="primary" checked>';
            foreach($res as $key=>$val){
                if(in_array($val['module_id'],$selfModule)){
                    $res[$key]['module_name'] = str_replace($search,$replace,$val['module_name']);
                }
            }
        }


        //dump($res);die;
        $this->assign('data',$res);
        $this->assign('role_id',$role_id);
        $this->assign('selfModule',$moduleIds);
        return $this->fetch('/index/rolePrevm');
    }
    //修改角色的模块权限
    public function rolePrevmAjax()
    {
        //输入
        $request = $this->request;
        //条件
        $condition = $this->condition;

        $role_id = $request->param('role_id') ? intval($request->param('role_id')) : 0;
        if(!$role_id){
            $res['state'] = 99;
            $res['msg'] = '角色id不存在';
            return json($res);
        }

        $module_id = $request->param('module_id') ? $request->param('module_id') : '';
        if(!$module_id){
            $res['state'] = 98;
            $res['msg'] = '模块id不存在';
            return json($res);
        }

        $condition['table'] = 'role';
        $condition['where']['role_id'] = $role_id;
        $condition['data']['module_id'] = $module_id;

        $sysModel = $this->systemModel;
        $result = $sysModel->getUpd($condition);

        if($result){
            $res['state'] = 100;
            $res['msg'] = '模块权限分配成功';
            return json($res);
        }else{
            $res['state'] = 97;
            $res['msg'] = '模块权限分配失败';
            return json($res);
        }
    }



    /****************按钮权限*************/
    public function rolePrevb()
    {
        //输入对象
        $request = $this->request;
        //sys模型
        $sysModel = $this->systemModel;
        //查询数组
        $condition = $this->condition;
        //当前角色
        $role_id = $request->param('role_id');

        //查询本角色所有模块
        $condition['table'] = 'role';
        $condition['where']['role_id'] = $role_id;
        $condition['field'] = 'module_id';
        $moduleIds = $sysModel->getOne($condition)['module_id'];

        //在module表中查找完整模块信息，并将其tree化
        unset($condition['where']['role_id']);
        $condition['table'] = 'module';
        $condition['where']['module_id'] = ['in',$moduleIds];
        $condition['field'] = '*';
        $modules = $sysModel->getAll($condition);
        $res = $this->treeForModuleFour($modules,0,0);

        if($res){
            $res = $this->assignYet($role_id,$res);
            $this->assign('data',$res);
        }
        //dump($res);die;

        $this->assign('role_id',$role_id);

        return $this->fetch('/index/rolePrevb');
    }
    //获取相应模块的按钮
    public function getButtonOfRoleAjax()
    {
        $request = $this->request;
        $role_id = $request->param('role_id') ? intval($request->param('role_id')) : 0;
        if(!$role_id){
            $res['state'] = 99;
            $res['msg'] = '角色Id为空';
            return json($res);
        }
        $module_id = $request->param('module_id') ? intval($request->param('module_id')) : 0;
        if(!$module_id){
            $res['state'] = 98;
            $res['msg'] = '模块id为空';
            return json($res);
        }
        $condition = $this->condition;
        $condition['table'] = 'module';
        $condition['field'] = 'button_id';
        $condition['where']['module_id'] = $module_id;

        //获取改模块的所有按钮id
        $sysModel = $this->systemModel;
        $button_ids = $sysModel->getOne($condition)['button_id'];

        if($button_ids){
            //获取按钮详细信息
            $condition['table'] = 'button';
            $condition['field'] = '*';
            $condition['where']['button_id'] = ['in',$button_ids];
            unset($condition['where']['module_id']);
            $buttons = $sysModel->getAll($condition);
        }else{
            $buttons = [];
        }
        //获取当前角色在模块下拥有的按钮
        $result = $this->setYet($role_id,$module_id,$buttons);
        if($result){
            $buttons = $result;
        }

        $res['state'] = 100;
        $res['msg'] = '请求成功';
        $res['data'] = $buttons;
        return json($res);
    }

    //从所有模块中标记出已有的按钮
    public function assignYet($role_id,$module)
    {
        $sysModel = $this->systemModel;
        $condition = $this->condition;

        $condition['table'] = 'role';
        $condition['field'] = 'button_json';
        $condition['where']['role_id'] = $role_id;
        $button_json = $sysModel->getOne($condition)['button_json'];

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
        $sysModel = $this->systemModel;
        $condition = $this->condition;
        //获取当前角色在模块下拥有的按钮
        $condition['table'] = 'role';
        $condition['where']['role_id'] = $role_id;
        $condition['field'] = 'button_json';
        $button_json = $sysModel->getOne($condition)['button_json'];
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

    //正式修改role的button_json
    public function rolePrevbAjax()
    {
        $request = $this->request;
        $role_id = $request->param('role_id') ? intval($request->param('role_id')) : 0;
        if(!$role_id){
            $res['state'] = 99;
            $res['msg'] = '角色id不存在';
            return $res;
        }
        $moduleArray = $_POST['modules'] ? $_POST['modules'] : [];
        if(!$moduleArray){
            $res['state'] = 98;
            $res['msg'] = '模块id组为空';
            return $res;
        }
        $buttonArray = $_POST['buttons'] ? $_POST['buttons'] : [];
        if(!$buttonArray){
            $res['state'] = 97;
            $res['msg'] = '按钮组为空';
            return $res;
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

        $condition = $this->condition;
        $condition['table'] = 'role';
        $condition['where']['role_id'] = $role_id;
        $condition['data']['button_json'] = $button_json;

        $sysModel = $this->systemModel;
        $result = $sysModel->getUpd($condition);
        if($result){
            $res['state'] = 100;
            $res['msg'] = '修改成功';
            return $res;
        }else{
            $res['state'] = 96;
            $res['msg'] = '修改失败';
            return $res;
        }
    }


    /****************管理员管理*************/
    public function admins()
    {
        $condtion = $this->condition;
        $condtion['table'] = 'u.user';
        $condtion['join'] = ['tp_role r','u.role_id = r.role_id'];
        $condtion['field'] = 'u.*,r.role_name';
        $sysModel = $this->systemModel;
        $allUser = $sysModel->getAll($condtion);
        $this->assign('data',$allUser);
        $this->assign('leftNav',6);
        $this->assign('topNav',1);
        return $this->fetch('/index/admins');
    }

    public function adminsAdd()
    {
        //参数对象
        $request = $this->request;
        //继承系统模型
        $sysModel = $this->systemModel;
        //查询数组
        $condition = $this->condition;

        if($request->isPost()){
            //构造检验数据
            $data = $this->createUserData($request);
            //继承系统模型
            $sysModel = $this->systemModel;
            //插入数据
            $condition['table'] = 'user';
            $condition['data'] = $data;
            //dump($condition);die;
            $result = $sysModel->getAdd($condition);
            if($result){
                $res['state'] = 100;
                $res['msg'] = '添加成功';
            }else{
                $res['state'] = 96;
                $res['msg'] = '添加失败';
            }

            return json($res);

        }else{

            $condition['table'] = 'role';
            $role = $sysModel->getAll($condition);
            $this->assign('role',$role);

            return $this->fetch('/index/adminsAdd');

        }
    }

    public function adminsUpd()
    {
        //参数对象
        $request = $this->request;
        //继承系统模型
        $sysModel = $this->systemModel;
        //查询数组
        $condition = $this->condition;

        $user_id = $request->param('user_id') ? intval($request->param('user_id')) : 0;

        if($request->isPost()){
            //构造检验数据
            $data = $this->createUserData($request);
            //继承系统模型
            $sysModel = $this->systemModel;

            //修改数据
            $condition['table'] = 'user';
            $condition['where']['user_id'] = $user_id;
            $condition['data'] = $data;
            //dump($condition);die;
            $result = $sysModel->getUpd($condition);
            if($result){
                $res['state'] = 100;
                $res['msg'] = '添加成功';
            }else{
                $res['state'] = 96;
                $res['msg'] = '添加失败';
            }

            return json($res);

        }else{

            $condition['table'] = 'role';
            $role = $sysModel->getAll($condition);
            $this->assign('role',$role);

            $condition['table'] = 'user';
            $condition['where']['user_id'] = $user_id;
            $userInfo = $sysModel->getOne($condition);
            $this->assign('userInfo',$userInfo);

            return $this->fetch('/index/adminsUpd');

        }
    }

    public function createUserData($request)
    {
        if($request->param('username')){
            $data['username'] = trim($request->param('username'));
        }else{
            $res['state'] = 99;
            $res['msg'] = '用户名为空';
            return json($res);
        }

        if($request->param('relaname')){
            $data['relaname'] = trim($request->param('relaname'));
        }else{
            $res['state'] = 98;
            $res['msg'] = '真实姓名为空';
            return join($res);
        }

        if($request->param('mobile')){
            $data['mobile'] = trim($request->param('mobile'));
        }else{
            $res['state'] = 97;
            $res['msg'] = '手机号为空';
            return join($res);
        }

        if($request->param('role_id')){
            $data['role_id'] = trim($request->param('role_id'));
        }else{
            $res['state'] = 96;
            $res['msg'] = '管理员类型为空';
            return join($res);
        }

        if($request->param('password')){
            $data['password'] = trim($request->param('password'));
            $data['salt'] = $this->getSalt();
            $data['password'] = md5($data['password'].$data['salt']);
            $data['addtime'] = time();
        }

        return $data;
    }

    //按钮删除
    public function adminsDel()
    {
        //接受参数
        $ids = $this->request->param('ids');
        $button_event = $this->request->param('button_event');
        $primaryKey = $this->request->param('primaryKey');
        //继承ajaxParam
        $ajaxParam = $this->ajaxParam;
        //设置参数
        $ajaxParam['table'] = 'user';
        $ajaxParam['button_event'] = $button_event;
        $ajaxParam['primaryKey'] = $primaryKey;
        $ajaxParam['primaryVal'] = $ids;
        //发起操作
        $res = $this->commonAjax($ajaxParam);
        //返回json
        return json($res);
    }

    //这里是seo
    public function seo()
    {
        return '这里是seo';
    }


}