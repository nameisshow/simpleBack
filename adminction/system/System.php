<?php
/**
 * Created by PhpStorm.
 * User: zhangbo
 * Date: 17-11-26
 * Time: 下午9:14
 */

namespace admin\system;

use admin\model\Common;
use think\cache\driver\Redis;
use think\Db;
use think\Model;
use think\Request;
use think\Session;

class System extends Common
{

    static $cache = false;

    private $user_id;


    public function __construct()
    {
        parent::__construct();
        $this->user_id = session('user_id');
    }

    /**
     * 获取用户信息
     * @return array|false|\PDOStatement|string|Model
     */
    public function getUserInfo()
    {

        $userInfo = Db::name('admin')
            ->where('user_id', $this->user_id)
            ->field('user_id,username,relaname,mobile,login_count,last_ip,last_time')
            ->cache(self::$cache)
            ->find();
        $userInfo['last_time'] = date('Y-m-d H:i:s', $userInfo['last_time']);

        return $userInfo;
    }

    /**
     * 获取用户的菜单信息
     * @return array
     */
    public function getMenu()
    {
        //获取本链接对应的顶级模块id
        //$selfModuleId = $this->getBaseModuleId();

        //获取模块id和按钮字符串
        $button_json = Db::name('role')
            ->alias('r')
            ->join('tp_admin u ', 'r.role_id = u.role_id')
            ->where('u.user_id', $this->user_id)
            ->field('r.button_json as bj,r.module_id as mi')
            ->cache(self::$cache)
            ->find();
        //菜单数组
        $menuArray = Db::name('module')
            ->where('module_id', 'in', $button_json['mi'])
            ->order('module_sort asc')
            ->field('module_id,module_code,module_pid,module_name,module_url')
            ->cache(self::$cache)
            ->select();
        //顶部菜单
        $topMenu = array();
        //左侧菜单
        $leftMenu = array();
        //子菜单
        $childMenu = array();
        foreach ($menuArray as $key => $val) {
            $codeArray = explode('-', $val['module_code']);
            if (count($codeArray) == 1) {
                $topMenu[] = $val;
            }
        }
        if ($topMenu) {
            $selfModuleId = $topMenu[0]['module_id'];
            $notTopMenu = $this->getNotTopMemu($menuArray, $selfModuleId);
            $leftMenu = $notTopMenu['leftMenu'];
            $childMenu = $notTopMenu['childMenu'];
        }

        //为顶部菜单指定一个默认链接
        $this->setUrlOfTop($topMenu);

        //查询按钮详细信息
        $buttonArray = json_decode($button_json['bj'], true);
        foreach ($buttonArray as $key => $val) {
            $idStr = implode(',', $val);
            $buttonArray[$key] = Db::name('button')
                ->where('button_id', 'in', $idStr)
                ->field('button_id,button_name,button_event')
                ->cache(self::$cache)
                ->select();

        }

        //将按钮信息整合到子菜单中
        foreach ($childMenu as $key => $val) {
            $childMenu[$key]['module_url'] = ADMIN_URL . $val['module_url'];//拼接url
            foreach ($buttonArray as $k => $v) {
                if ($k == $val['module_id']) {
                    $childMenu[$key]['buttons'] = $v;
                }
            }
        }

        //将子菜单整合到左侧菜单中
        foreach ($childMenu as $key => $val) {
            $leftMenuId = explode('-', $val['module_code'])[1];
            foreach ($leftMenu as $k => $v) {
                if ($leftMenuId == $v['module_id']) {
                    $leftMenu[$k]['child_module'][] = $val;
                }
            }
        }

        $menu = array('topMenu' => $topMenu, 'leftMenu' => $leftMenu);

        return $menu;
    }

    //获取本链接对应的顶级模块id
    //用作取相应左侧模块的条件
    public function getBaseModuleId()
    {
        $request = Request::instance();
        $pattern = '/\w+\.php(\S+)/';
        $str = $request->baseUrl();
        preg_match($pattern, $str, $match);

        if (!$match) {
            //匹配不到，说明路由形式是/admin.php
            //直接在数据库中找第一个最顶层的模块id
            $selfModuleId = Db::name('module')
                ->field('module_id')
                ->where('module_pid', 0)
                ->order('module_sort asc')
                ->find()['module_id'];
            return $selfModuleId;
        }
        $module_url = $match[1];
        $moduleCode = Db::name('module')
            ->field('module_code')
            ->where('module_url', $module_url)
            ->find()['module_code'];
        //如果code存在，可以直接寻找
        if ($moduleCode) {
            $selfModuleId = explode('-', $moduleCode)[0];
        } else {
            //该code不存在，寻找第一个最顶级按钮id
            $selfModuleId = Db::name('module')
                ->field('module_id')
                ->where('module_pid', 0)
                ->order('module_sort asc')
                ->find()['module_id'];
        }

        return $selfModuleId;
    }

    public function getNotTopMemu($menuArray, $selfModuleId)
    {
        foreach ($menuArray as $key => $val) {
            $codeArray = explode('-', $val['module_code']);
            //if(count($codeArray) == 1){
            //    $topMenu[] = $val;
            //}
            if ($codeArray[0] == $selfModuleId) {
                if (count($codeArray) == 2) {
                    $leftMenu[] = $val;
                }
                if (count($codeArray) == 3) {
                    $childMenu[] = $val;
                }
            }
        }

        return array('leftMenu' => $leftMenu, 'childMenu' => $childMenu);
    }

    //为顶部菜单指定一个默认链接
    public function setUrlOfTop(&$topMenu)
    {
        foreach ($topMenu as $key => $val) {
            $topMenu[$key]['module_url'] = ADMIN_URL . Db::name('module')
                    ->cache(self::$cache)
                    ->field('module_url')
                    ->where('module_code', 'like', '%' . $val['module_id'] . '%')
                    ->where('module_url', 'neq', '')
                    ->find()['module_url'];
        }
    }


    /**
     * @param string $module_url
     * @return array|false|\PDOStatement|string|\think\Collection
     */
    function getButton($module_url = '')
    {
        if (!$module_url) {
            return [];
        }


        $module_id = Db::name('module')
            ->where('module_url', $module_url)
            ->field('module_id')
            ->cache(self::$cache)
            ->find()['module_id'];
        if (!$module_id) {
            return [];
        }
        $button_json = Db::name('admin')
            ->alias('u')
            ->join('tp_role r', 'u.role_id = r.role_id')
            ->where('u.user_id', $this->user_id)
            ->field('r.button_json as bj')
            ->cache(self::$cache)
            ->find();
        $buttonIds = '';

        foreach (json_decode($button_json['bj'], true) as $key => $val) {
            if ($key == $module_id) {
                foreach ($val as $k => $v) {
                    $buttonIds .= $v . ',';
                }
            }
        }

        $buttonArray = Db::name('button')
            ->where('button_id', 'in', substr($buttonIds, 0, -1))
            ->field('button_id,button_event,button_name,button_type')
            ->order('button_sort asc')
            ->cache(self::$cache)
            ->select();

        if ($buttonArray) {
            foreach ($buttonArray as $key => $val) {
                /*switch($val['button_event']){
                    case 'add':
                        $buttonArray[$key]['button_url'] = $this->joint($val['button_event']);
                        break;
                    case 'upd';
                        $buttonArray[$key]['button_url'] = $this->joint($val['button_event']);
                        break;
                    default :
                        $buttonArray[$key]['button_url'] = '';
                }*/
                $buttonArray[$key]['button_url'] = $this->joint($val['button_event']);
            }
        }

        return $buttonArray;
    }

    //拼接按钮url
    function joint($button_event)
    {
        $action = Request::instance()->action();
        $pattern = '/^(\S+)(?=\/)\/(\w+)$/';
        $request_url = ADMIN_URL . $_SERVER['PATH_INFO'];
        preg_match($pattern, $request_url, $match);
        return $match[1] . '/' . $action . ucwords($button_event);
    }


    public function createModuleCode($module_code, $module_pid)
    {
        static $newCode = '';
        $pid = Db::name('module')
            ->cache(self::$cache)
            ->where('module_id', $module_pid)
            ->field('module_id,module_pid')
            ->find();
        if ($pid) {
            $newCode = $pid['module_id'] . '-' . $module_code;
            $this->createModuleCode($newCode, $pid['module_pid']);
        }
        return $newCode;


    }

    //添加模块方法，使用事务
    public function insertModule($condition)
    {
        if (!$condition) {
            return false;
        }
        $table = $condition['table'];
        if (!$table) {
            return false;
        }
        $data = $condition['data'];
        if (!$data) {
            return false;
        }

        Db::startTrans();
        try {
            //先插入数据，获取模块id
            $insertId = Db::name($table)
                ->insertGetId($data);

            //获取module_code
            $module_code = $this->createModuleCode($insertId, $data['module_pid']);
            //修改表module_code
            Db::name($table)
                ->cache(self::$cache)
                ->where('module_id', $insertId)
                ->update(['module_code' => $module_code]);
            //提交事务
            Db::commit();

            return true;
        } catch (\Exception $e) {
            //回滚事务
            Db::rollback();

            return false;
        }
    }

    //编辑模块信息
    public function updateModule($condition)
    {
        if (!$condition) {
            return false;
        }
        $table = $condition['table'];
        if (!$table) {
            return false;
        }
        $data = $condition['data'];
        if (!$data) {
            return false;
        }
        $where = $condition['where'];
        if (!$where) {
            return false;
        }

        if ($data['module_pid']) {
            $data['module_code'] = $this->createModuleCode($where['module_id'], $data['module_pid']);
        }
        //dump($data);die;
        $result = Db::name($table)
            ->cache(self::$cache)
            ->where($where)
            ->update($data);

        return $result;
    }


    /****************NEW*****************/
    //登录方法
    public function toLogin($username, $password)
    {
        $userInfo = Db::name('admin')
            ->where(['username' => $username])
            ->find();
        if (!$userInfo) {
            $res['state'] = 98;
            $res['msg'] = '用户bu存在';
            return $res;
        }

        if ($userInfo['password'] != md5($password . $userInfo['salt'])) {
            $res['state'] = 96;
            $res['msg'] = '用户名或密码错误';
            return $res;
        }

        session('user_id', $userInfo['user_id']);

        $data['last_time'] = $userInfo['login_time'];
        $data['last_ip'] = $userInfo['login_ip'];
        $data['login_time'] = time();
        $data['login_ip'] = GetIp();
        $data['login_count'] = $userInfo['login_count'] + 1;


        $result = Db::name('admin')
            ->where(['user_id' => $userInfo['user_id']])
            ->update($data);

        if ($result) {
            $res['state'] = 100;
            $res['msg'] = '登录成功';
        } else {
            $res['state'] = 95;
            $res['msg'] = '登录失败';
        }

        return $res;
    }


    /**************按钮部分****************/

    public function getButtonPage()
    {
        $page = input('page/d', 1);
        $perPage = 5;
        $where = [];
        $search = [];

        if ($button_name = input('button_name/s', '')) {
            $where['button_name'] = ['like', '%' . $button_name . '%'];
            $search['button_name'] = $button_name;
        }
        if ($button_event = input('button_event/s', '')) {
            $where['button_event'] = ['like', '%' . $button_event . '%'];
            $search['button_event'] = $button_event;
        }

        $data = Db::name('button')
            ->where($where)
            ->page($page, $perPage)
            ->order('button_id desc')
            ->cache(self::$cache)
            ->select();
        $total = Db::name('button')
            ->where($where)
            ->cache(self::$cache)
            ->count();
        return ['data' => $data, 'total' => $total, 'page' => $page, 'perPage' => $perPage, 'search' => $search];
    }

    //获取单个按钮信息
    public function getButtonInfo($button_id)
    {
        $result = Db::name('button')
            ->where(['button_id' => $button_id])
            ->cache(self::$cache)
            ->find();
        return $result;
    }

    //更新按钮信息
    public function buttonUpdate($table, $where, $data)
    {
        $result = Db::name($table)
            ->where($where)
            ->update($data);
        return $result;
    }

    //添加按钮数据
    public function buttonAdd($table, $data)
    {
        $result = Db::name($table)
            ->insertGetId($data);
        return $result;
    }

    /*************模块部分************/
    public function getModuleTree()
    {
        $module = Db::name('module')
            ->order('module_id desc')
            ->cache(self::$cache)
            ->select();

        $tree = $this->treeForModuleTwo($module, 0, 0);

        return $tree;
    }

    //添加模块数据
    public function moduleAdd($table, $data)
    {
        $pid = $data['module_pid'];
        if ($pid != 0) {
            $data['module_code'] = $this->joinCode($pid);
        }
        $data['module_code'] = implode('-', array_reverse($data['module_code']));
        if (!$data['module_code']) {
            $data['module_code'] = '';
        }
        $result = Db::name($table)
            ->insertGetId($data);

        if ($data['module_code']) {
            $update['module_code'] = $data['module_code'] . '-' . $result;
            $result = Db::name($table)
                ->where(['module_id' => $result])
                ->update($update);

        }
        return $result;

    }

    //递归拼接module_ocde
    protected function joinCode($pid)
    {
        static $module_code = [];
        $res = Db::name('module')
            ->field('module_id,module_pid')
            ->where(['module_id' => $pid])
            ->find();
        $module_code[] = $res['module_id'];
        if ($res['module_pid'] == 0) {
            return $module_code;
        } else {
            return $this->joinCode($res['module_pid']);
        }
    }

    //更新模块信息
    public function moduleUpdate($table, $where, $data)
    {
        $pid = $data['module_pid'];
        if ($pid != 0) {
            $data['module_code'] = $this->joinCode($pid);
        }
        array_unshift($data['module_code'], $where['module_id']);
        $data['module_code'] = implode('-', array_reverse($data['module_code']));
        $result = Db::name($table)
            ->where($where)
            ->update($data);
        return $result;
    }

    //获取某个模块的button_id
    public function getButtonWithModule($module_id)
    {
        $button_id = Db::name('module')
            ->field('button_id')
            ->where(['module_id' => $module_id])
            ->find()['button_id'];
        return $button_id;
    }

    //设置某个模块的button_id
    public function setButtonWithModule($module_id, $button_id)
    {
        $res = Db::name('module')
            ->where(['module_id' => $module_id])
            ->update(['button_id' => $button_id]);
        return $res;
    }

    //获取所有按钮
    public function getButtonList($where = array())
    {
        $res = Db::name('button')
            ->where($where)
            ->select();
        return $res;
    }


    /*************管理员部分************/
    public function getAdminList()
    {
        $res = Db::name('admin')
            ->alias('admin')
            ->join('__ROLE__ role', 'admin.role_id = role.role_id')
            ->field('admin.*, role.role_name')
            ->select();
        return $res;
    }

    //获取所有身份
    public function getRoleList()
    {
        $res = Db::name('role')
            ->select();
        return $res;
    }

    //添加管理员
    public function addAdmin($data)
    {
        $insertId = Db::name('admin')
            ->insertGetId($data);
        return $insertId;
    }

    //获取某个管理员的数据
    public function getAdmin($user_id)
    {
        $info = DB::name('admin')
            ->where(['user_id' => $user_id])
            ->find();
        return $info;
    }

    //修改管理员数据
    public function updateAdmin($where, $data)
    {
        $res = Db::name('admin')
            ->where($where)
            ->update($data);
        return $res;
    }


    /*************角色部分************/

    public function addRole($data)
    {
        $insertId = Db::name('role')
            ->insertGetId($data);
        return $insertId;
    }

    public function getRole($role_id)
    {
        $info = Db::name('role')
            ->where(['role_id' => $role_id])
            ->find();
        return $info;
    }

    public function updateRole($where, $data)
    {
        $res = Db::name('role')
            ->where($where)
            ->update($data);
        return $res;
    }

    //获取某个角色当前的模块
    public function getModuleWithRole($role_id)
    {
        $module_id = Db::name('role')
            ->field('module_id')
            ->where(['role_id' => $role_id])
            ->find()['module_id'];
        return $module_id;
    }

    //设置某个角色模块权限
    public function setRolePrevm($where, $data)
    {
        $res = Db::name('role')
            ->where($where)
            ->update($data);
        return $res;
    }

    //获取某个角色的button_json
    public function getButtonJson($role_id)
    {
        $button_json = Db::name('role')
            ->field('button_json')
            ->where(['role_id' => $role_id])
            ->find()['button_json'];
        return $button_json;
    }

    //设置某个角色的button_json
    public function setButtonJson($role_id, $button_json)
    {
        $res = Db::name('role')
            ->where(['role_id'=>$role_id])
            ->update(['button_json'=>$button_json]);
        return $res;
    }


}