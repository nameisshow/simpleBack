<?php
/**
 * Created by PhpStorm.
 * User: zhangbo
 * Date: 17-11-26
 * Time: 下午9:14
 */
namespace admin\model;

use think\Db;
use think\Model;

class Common extends Model
{
    protected $connection = [

        'type' => 'mysql',

        'hostname' => '127.0.0.1',

        'database' => 'tp5',

        'username' => 'root',

        'password' => 'root',

        'charset' => 'utf8',

        'prefix' => 'tp_',

        'debug' => true,
    ];

    public function __construct()
    {
        parent::__construct();
    }



    /**
     * 返回分页数组
     * @param array $condition
     * @return array
     */
    public function pageData($condition = [])
    {
        if(!$condition){
            return [];
        }
        if(!$condition['table']){
            return [];
        }

        //表名
        $table = $condition['table'];
        if(strpos($table,'.') !== false){
            $alias = explode('.',$table)[0];
            $table = explode('.',$table)[1];
            $modelObj = Db::name($table)->alias($alias);
        }else{
            $modelObj = Db::name($table);
        }

        if($condition['join']){
            foreach($condition['join'] as $key=>$val){
                if(is_array($val)){
                    $modelObj->join($val[0],$val[1]);
                }else{
                    $modelObj->join($condition['join'][0],$condition['join'][1]);
                    break;
                }
            }
        }

        $condition['where'] && ($modelObj->where($condition['where']));
        $condition['field'] && ($modelObj->field($condition['field']));
        $condition['order'] && ($modelObj->order($condition['order']));

        //首先记录最后一条sql
        //$countSql = preg_replace("/SELECT(.*)FROM/", "SELECT count(*) AS count FROM", $modelObj->getLastSql());

        //分页数据
        if($condition['nowPage'] && $condition['perPage']){
            $modelObj->page($condition['nowPage'].','.$condition['perPage']);
        }

        //最终查询
        $data = $modelObj->select();
        //最后一条语句
        $lastSql = $modelObj->getLastSql();
        $lastSql = preg_replace("/^SELECT(.*)FROM/", "SELECT count(*) AS count FROM", $lastSql);
        if($condition['nowPage'] && $condition['perPage']){
            dump($lastSql);die;
        }
        //查询数量
        $count = $modelObj->query($lastSql);
        return ['data'=>$data,'total'=>$count,'nowPage'=>$condition['nowPage'],'perPage'=>$condition['perPage']];
    }


    //向某张表中插入数据
    public function getAdd($condition = [])
    {
        if(!$condition){
            return false;
        }
        $table = $condition['table'];
        if(!$table){
            return false;
        }
        $data = $condition['data'];
        if(!$data){
            return false;
        }

        $insertId = Db::name($table)->insertGetId($data);

        return $insertId;
    }
    //更改某张表的数据
    public function getUpd($condition = [])
    {
        if(!$condition){
            return false;
        }
        $table = $condition['table'];
        if(!$table){
            return false;
        }
        $where = $condition['where'];
        if(!$where){
            return false;
        }
        $data = $condition['data'];
        if(!$data){
            return false;
        }

        $updateRes = Db::name($table)->where($where)->update($data);

        return $updateRes;
    }
    //获取单组数据
    public function getOne($condition = [])
    {
        if(!$condition){
            return [];
        }
        if(!$condition['table']){
            return [];
        }

        //表名
        $table = $condition['table'];
        if(strpos($table,'.') !== false){
            $alias = explode('.',$table)[0];
            $table = explode('.',$table)[1];
            $modelObj = Db::name($table)->alias($alias);
        }else{
            $modelObj = Db::name($table);
        }

        if($condition['join']){
            foreach($condition['join'] as $key=>$val){
                if(is_array($val)){
                    $modelObj->join($val[0],$val[1]);
                }else{
                    $modelObj->join($condition['join'][0],$condition['join'][1]);
                    break;
                }
            }
        }
        $condition['where'] && ($modelObj->where($condition['where']));
        $condition['field'] && ($modelObj->field($condition['field']));
        $condition['order'] && ($modelObj->order($condition['order']));

        $data = $modelObj->find();

        return $data;
    }
    //获取数据组
    public function getAll($condition = [])
    {
        if(!$condition){
            return [];
        }
        if(!$condition['table']){
            return [];
        }

        //表名
        $table = $condition['table'];
        if(strpos($table,'.') !== false){
            $alias = explode('.',$table)[0];
            $table = explode('.',$table)[1];
            $modelObj = Db::name($table)->alias($alias);
        }else{
            $modelObj = Db::name($table);
        }

        //$condition['join'] && ($modelObj->join($condition['join']));
        if($condition['join']){
            foreach($condition['join'] as $key=>$val){
                if(is_array($val)){
                    //存在多组join
                    $modelObj->join($val[0],$val[1]);
                }else{
                    //只有一组join
                    $modelObj->join($condition['join'][0],$condition['join'][1]);
                    break;
                }
            }
        }
        $condition['where'] && ($modelObj->where($condition['where']));
        $condition['field'] && ($modelObj->field($condition['field']));
        $condition['order'] && ($modelObj->order($condition['order']));
        //dump($condition);die;
        $data = $modelObj->select();
        //dump($data);die;
        return $data;
    }

    /**
     * 删除，修改字段用ajax方法
     * @param $ajaxParam
     * @return mixed
     */
    public function commonAjax($ajaxParam)
    {
        $table = $ajaxParam['table'];
        if(!$table){
            $res['state'] = 99;
            $res['msg'] = '操作模型不存在';
            return $res;
        }
        $button_event = $ajaxParam['button_event'];
        if(!$button_event){
            $res['state'] = 98;
            $res['msg'] = '操作事件为空';
            return $res;
        }
        $primaryKey = $ajaxParam['primaryKey'];
        if(!$primaryKey){
            $res['state'] = 97;
            $res['msg'] = '操作对象不明确';
            return $res;
        }
        $primaryVal = $ajaxParam['primaryVal'];
        if(!$primaryVal){
            $res['state'] = 96;
            $res['msg'] = '操作对象为空';
            return $res;
        }

        if($button_event == 'upd'){
            $field = $ajaxParam['field'];
            if(!$field){
                $res['state'] = 95;
                $res['msg'] = '操作项不存在';
                return $res;
            }
            $value = $ajaxParam['value'];
            if(!$value){
                $res['state'] = 94;
                $res['msg'] = '操作值为空';
                return $res;
            }
        }

        $modelObj = DB::name($table)
            ->where($primaryKey,'in',$primaryVal);

        if($button_event == 'del'){
            $delRes = $modelObj->delete();
            if($delRes != 0){
                $res['state'] = 100;
                $res['msg'] = '删除成功';
                return $res;
            }else{
                $res['state'] = 92;
                $res['msg'] = '删除失败';
                return $res;
            }
        }
        if($button_event == 'upd'){
            $updateRes = $modelObj->update([$field,$value]);
            if($updateRes != 0){
                $res['state'] = 100;
                $res['msg'] = '修改成功';
                return $res;
            }else{
                $res['state'] = 93;
                $res['msg'] = '修改失败';
                return $res;
            }
        }
    }
}