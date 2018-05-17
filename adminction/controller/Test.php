<?php
namespace admin\controller;

use admin\index\model\Role;
use think\Controller;
use think\Db;
use think\db\Query;
use think\Loader;
use think\Request;
use think\View;

class Test extends Controller
{
    public $request;
    protected $Role;

    public function __construct(Request $request)
    {
        parent::__construct();
        $this->request = $request;
        $this->Role = model('Role');
    }
    public function index1()
    {
        dump($this->request);
        return 'this is admin';
    }

    public function index2()
    {
        //$result = Db::name('role')->select();
        $result = db('role')->select();//5.09后db函数不再强制重连数据库
        dump($result);
    }

    public function index3()
    {
        $query = new Query();
        //$result = $query->name('role')->value('role_name');
        $result = Db::name('role')->column('role_name','role_id');
        dump($result);
    }

    public function index4()
    {
        $array = [
            '12'=>[
                2,3,4
            ],
            '13'=>[
                5,6,7
            ]
        ];
        $data['role_name'] = '行政';
        $data['role_desc'] = '负责公司行政事务';
        $data['button_json'] = json_encode($array);

        $result = Db::name('role')->insertGetId($data);

        if($result){
            return $result;
        }else{
            return '插入失败';
        }
    }

    public function index5()
    {
        //$result = Db::name('role')->where('role_name|role_desc','like','%公司%')->column('role_name');
        $result = Db::name('role')
            ->where('role_desc','like','%公司%')
            ->whereOr('role_name','like','%超级%')
            ->column('role_name','role_id');
        dump($result);
    }

    public function index6()
    {
        dump(Db::getTableInfo('tp_role'));
    }

    public function index7()
    {
        $where['role_name'] = ['like','%超级%'];
        $where2['role_desc'] = ['like','%公司%'];
        $result = Db::name('role')->where($where)->whereOr($where2)->column('role_name','role_id');
        dump($result);
    }

    public function index8()
    {
        $Role = new Role();//
        $Role = Loader::model('Role');//单例模式
        $Role = model('Role');//同样是单例模式
        //dump($Role->findAll());
        $Role->role_name = '前端';
        $Role->role_desc = '写页面啊';
        $Role->module_id = '12';
        $Role->asd = '哈哈';
        dump($Role->allowField(true)->save());
    }

    public function index9()
    {
        $role = $this->Role;
        dump($role::get(['role_id','1']));
    }

    public function index10()
    {
        $data['role_name'] = '设计';
        $data['role_desc'] = '负责公司设计';
        $data['module_id'] = '66,77';
        $this->Role->allowField(true)->insertTo($data);
    }

    public function index()
    {
        $data = array(
            ['name'=>'zhangbo','age'=>18],
            ['name'=>'liudong','age'=>19],
            ['name'=>'sunrui','age'=>20]
        );
        $empty = '<img src="/static/admin/img/001.png" style="width: 100px;height: 100px;" />';
        $this->assign('data',$data);
        $this->assign('empty',$empty);

        return $this->fetch('/index/index');
    }
}
