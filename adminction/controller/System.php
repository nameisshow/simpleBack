<?php
/**
 * Created by PhpStorm.
 * User: zhangbo
 * Date: 17-11-26
 * Time: 下午8:29
 */

namespace admin\controller;

use think\Request;

class System extends Common{


    protected $request;

    public function _initialize()
    {
        parent::_initialize();
        $this->request = Request::instance();
    }

    public function test()
    {
        $str = 'SELECT count(*) AS count FROM `tp_button` LIMIT 2,2';
        echo $str;
    }

    //按钮管理
    public function button()
    {
        //分页查询条件
        $condition = $this->condition;
        $condition['nowPage'] = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $condition['perPage'] = 2;//此处有问题，查询时分页总会多出一页
        $condition['table'] = 'button';
        //用户输入条件
        $request = $this->request;
        $where = [];
        if($request->param('button_name')){
            $button_name = $request->param('button_name');
            $where['button_name'] = $button_name;
            $condition['where']['button_name'] = ['like','%'.$button_name.'%'];
        }else{
            $where['button_name'] = '';
        }
        if($request->param('button_event')){
            $button_event = $request->param('button_event');
            $where['button_event'] = $button_event;
            $condition['where']['button_event'] = ['like','%'.$button_event.'%'];
        }else{
            $where['button_event'] = '';
        }
        if($request->param('button_type')){
            $button_type = $request->param('button_type');
            $where['button_type'] = $button_type;
            $condition['where']['button_type'] = ['like','%'.$button_type.'%'];
        }else{
            $where['button_type'] = '';
        }

        //开始查询
        $data  = $this->systemModel->pageData($condition);

        //分配变量
        $this->assign('data',$data['data']);
        $this->assign('total',$data['total']);
        $this->assign('nowPage',$data['nowPage']);
        $this->assign('perPage',$data['perPage']);
        $this->assign('where',$where);
        $this->assign('leftNav',3);
        $this->assign('topNav',1);
        //渲染模板
        return $this->fetch('/system/button');
    }

    //添加按钮数据
    public function buttonAdd()
    {
        $request = $this->request;
        if($request->isPost()){
            //构造检验数据
            $data = $this->createButtonData($request);
            //继承系统模型
            $sysModel = $this->systemModel;
            //插入数据
            $condition = $this->condition;
            $condition['table'] = 'button';
            $condition['data'] = $data;
            $insertId = $sysModel->getadd($condition);

            if($insertId){
                $res['state'] = 100;
                $res['msg'] = '添加成功';
            }else{
                $res['state'] = 96;
                $res['msg'] = '添加失败';
            }

            return json($res);

        }else{

            return $this->fetch('/index/buttonAdd');

        }
    }

    //修改按钮数据
    public function buttonUpd()
    {
        $condition = $this->condition;
        $request = $this->request;
        if($request->isPost()){
            //构造检验数据
            $data = $this->createButtonData($request);
            //继承系统模型
            $sysModel = $this->systemModel;
            //构造条件
            $where['button_id'] =  $request->param('button_id') ? intval($request->param('button_id')) : 0;
            //修改数据
            $condition['table'] = 'button';
            $condition['where'] = $where;
            $condition['data'] = $data;
            $updateRes = $sysModel->getUpd($condition);

            if($updateRes == 0){
                $res['state'] = 100;
                $res['msg'] = '没有数据改变';

            }else if($updateRes){
                $res['state'] = 100;
                $res['msg'] = '修改成功';
            }else{
                $res['state'] = 96;
                $res['msg'] = '修改失败';
            }

            return json($res);

        }else{

            if($request->param('button_id')){
                $button_id = trim($request->param('button_id'));
            }else{
                $button_id = 0;
            }

            $condition['table'] = 'button';
            $condition['where']['button_id'] = $button_id;

            $buttonInfo = $this->systemModel->getOne($condition);

            $this->assign('buttonInfo',$buttonInfo);

            return $this->fetch('/index/buttonUpd');

        }
    }
    //按钮删除
    public function buttonDel()
    {
        //接受参数
        $ids = $this->request->param('ids');
        $button_event = $this->request->param('button_event');
        $primaryKey = $this->request->param('primaryKey');
        //继承ajaxParam
        $ajaxParam = $this->ajaxParam;
        //设置参数
        $ajaxParam['table'] = 'button';
        $ajaxParam['button_event'] = $button_event;
        $ajaxParam['primaryKey'] = $primaryKey;
        $ajaxParam['primaryVal'] = $ids;
        //发起操作
        $res = $this->commonAjax($ajaxParam);
        //返回json
        return json($res);
    }
    //构建检验按钮数据
    public function createButtonData($request)
    {
        if($request->param('button_name')){
            $data['button_name'] = $request->param('button_name');
        }else{
            $res['state'] = 99;
            $res['msg'] = '按钮名为空';
            return json($res);
        }
        if($request->param('button_event')){
            $data['button_event'] = $request->param('button_event');
        }else{
            $res['state'] = 98;
            $res['msg'] = '按钮事件为空';
            return json($res);
        }
        if($request->param('button_type')){
            $data['button_type'] = $request->param('button_type');
        }else{
            $res['state'] = 97;
            $res['msg'] = '按钮类型为空';
            return json($res);
        }
        if($request->param('button_desc')){
            $data['button_desc'] = $request->param('button_desc');
        }else{
            $data['button_desc'] = '';
        }
        if($request->param('button_sort')){
            $data['button_sort'] = $request->param('button_sort');
        }else{
            $data['button_sort'] = 0;
        }

        return $data;
    }


    //模块管理
    public function module()
    {
        //分页查询条件
        $condition = $this->condition;
        $condition['table'] = 'module';

        //继承系统模型
        $sysModel = $this->systemModel;
        //开始查询
        $data = $sysModel->getAll($condition);
        $res = $this->treeForModuleTwo($data,0,0);

        //分配变量
        $this->assign('data',$res);
        $this->assign('leftNav',4);
        $this->assign('topNav',1);

        //渲染模板
        return $this->fetch('/index/module');
    }

    //添加模块
    public function moduleAdd()
    {
        //参数对象
        $request = $this->request;
        //继承系统模型
        $sysModel = $this->systemModel;
        if($request->isPost()){
            //构造检验数据
            $data = $this->createModuleData($request);
            //继承系统模型
            $sysModel = $this->systemModel;
            //插入数据
            $condition = $this->condition;
            $condition['table'] = 'module';
            $condition['data'] = $data;
            //dump($condition);die;
            $result = $sysModel->insertModule($condition);
            if($result){
                $res['state'] = 100;
                $res['msg'] = '添加成功';
            }else{
                $res['state'] = 96;
                $res['msg'] = '添加失败';
            }

            return json($res);

        }else{
            //查询条件
            $condition = $this->condition;
            $condition['table'] = 'module';
            //开始查询
            $data = $sysModel->getAll($condition);
            $res = $this->treeForModuleTwo($data,0,0);
            //dump($res);die;
            $this->assign('data',$res);
            return $this->fetch('/index/moduleAdd');

        }
    }

    //添加模块
    public function moduleUpd()
    {
        //参数对象
        $request = $this->request;
        //继承系统模型
        $sysModel = $this->systemModel;
        if($request->isPost()){
            //构造检验数据
            $data = $this->createModuleData($request);
            //继承系统模型
            $sysModel = $this->systemModel;
            //条件
            $where['module_id'] = $request->param('module_id') ? intval($request->param('module_id')) : 0;
            //插入数据
            $condition = $this->condition;
            $condition['table'] = 'module';
            $condition['data'] = $data;
            $condition['where'] = $where;
            //dump($condition);die;
            $result = $sysModel->updateModule($condition);
            if($result){
                $res['state'] = 100;
                $res['msg'] = '编辑成功';
            }else{
                $res['state'] = 96;
                $res['msg'] = '编辑失败';
            }

            return json($res);

        }else{

            if($request->param('module_id')){
                $module_id = trim($request->param('module_id'));
            }else{
                $module_id = 0;
            }

            //查询条件
            $condition = $this->condition;
            $condition['table'] = 'module';
            //开始查询所有
            $data = $sysModel->getAll($condition);
            $res = $this->treeForModuleTwo($data,0,0);
            //查询本分类信息
            foreach($data as $key=>$val){
                if($val['module_id'] == $module_id){
                    $this->assign('moduleInfo',$val);
                }
            }
            //dump($res);die;
            $this->assign('data',$res);
            return $this->fetch('/index/moduleUpd');

        }
    }


    public function createModuleData($request)
    {
        //模块名
        if($request->param('module_name')){
            $data['module_name'] = trim($request->param('module_name'));
        }else{
            $res['state'] = 99;
            $res['msg'] = '模块名为空';
            return json($res);
        }
        //父级模块
        if($request->param('module_pid')){
            $data['module_pid'] = intval($request->param('module_pid'));
        }else{
            $data['module_pid'] = 0;
        }

        //模块排序
        if($request->param('module_sort')){
            $data['module_sort'] = intval($request->param('module_sort'));
        }

        //模块链接
        if($request->param('module_url')){
            $data['module_url'] = trim($request->param('module_url'));
        }

        //模块code
        //if($request->param('module_code')){
        //    $data['module_code'] = trim($request->param('module_url'));
        //}else{
        //    $data['module_code'] = '0';
        //}

        //模块按钮
        //添加修改时不涉及

        return $data;
    }


    //按钮删除
    public function moduleDel()
    {
        //接受参数
        $ids = $this->request->param('ids');
        $button_event = $this->request->param('button_event');
        $primaryKey = $this->request->param('primaryKey');
        //继承ajaxParam
        $ajaxParam = $this->ajaxParam;
        //设置参数
        $ajaxParam['table'] = 'module';
        $ajaxParam['button_event'] = $button_event;
        $ajaxParam['primaryKey'] = $primaryKey;
        $ajaxParam['primaryVal'] = $ids;
        //发起操作
        $res = $this->commonAjax($ajaxParam);
        //返回json
        return json($res);
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