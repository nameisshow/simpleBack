<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:59:"/var/www/tp5/public/../adminction/view/index/moduleAdd.html";i:1512457816;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>layout 后台大布局 - Layui</title>
    <link rel="stylesheet" href="__PUBLIC__/layui/css/layui.css">
    <link rel="stylesheet" href="__PUBLIC__/admin/css/style.css">
    <style>
        .mycontainer{
            left: 0px;
        }
        .layui-fluid{
            margin-top:10px;
        }
        .layui-input-block{
            margin-left:0px;
            margin-bottom: 10px;
        }
        .buttons{
            margin-bottom: -10px;
        }
    </style>
</head>
<body class="layui-layout-body">
    <div class="layui-body mycontainer">
        <!-- 内容主体区域 -->
        <!--百分百适配-->
        <div class="layui-fluid">
            <form class="layui-form" id="form" action="/admin.php/Index/moduleAdd" method="post">
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <input type="text" name="module_name" lay-verify="required" placeholder="请输入模块名" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-input-block">
                        <select name="module_pid" required lay-verify="required">
                            <option value="0">请选择父级模块</option>
                            <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                            <option value="<?php echo $v['module_id']; ?>"><?php echo $v['module_name']; ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </div>
                    <div class="layui-input-block">
                        <input type="text" name="module_sort"  lay-verify="required" placeholder="请输入模块排序" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-input-block">
                        <input type="text" name="module_url"  lay-verify="required" placeholder="请输入模块链接，如：/index/Index/index" autocomplete="off" class="layui-input">
                    </div>
                    <input type="hidden" name="isTree" value="1">
                    <div class="layui-input-block buttons">
                        <button class="layui-btn ok">确认</button>
                        <button class="layui-btn no">取消</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>

<!--js代码-->
<script src="__PUBLIC__/layui/layui.js"></script>
<script src="__PUBLIC__/admin/js/jquery.min.js"></script>
<script src="__PUBLIC__/admin/js/project.js"></script>
<script>
    function checkData(){
        var button_name = $('input[name=module_name]').val();
        if(!button_name){
            return '请填写模块名称';
        }
//        var type = $('select[name=module_pid]').val();
//        if(!type){
//            return '请选择按钮类型';
//        }
    }
</script>