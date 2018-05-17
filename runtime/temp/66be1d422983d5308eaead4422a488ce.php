<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:59:"/var/www/tp5/public/../adminction/view/index/adminsAdd.html";i:1512457694;}*/ ?>
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
            <form class="layui-form" id="form" action="/admin.php/Index/adminsAdd" method="post">
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <input type="text" name="username" lay-verify="required" placeholder="请输入用户名" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-input-block">
                        <input type="text" name="relaname" lay-verify="required" placeholder="请输入真实姓名" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-input-block">
                        <input type="text" name="mobile" lay-verify="required" placeholder="请输入手机号" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-input-block">
                        <select name="role_id" required lay-verify="required">
                            <option value="">请选择按钮类型</option>
                            <?php if(is_array($role) || $role instanceof \think\Collection || $role instanceof \think\Paginator): $i = 0; $__LIST__ = $role;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                            <option value="<?php echo $v['role_id']; ?>"><?php echo $v['role_name']; ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </div>
                    <div class="layui-input-block">
                        <input type="text" name="password"  lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input">
                    </div>
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
        var username = $('input[name=username]').val();
        if(!username){
            return '请填写用户名';
        }
        var relaname = $('input[name=relaname]').val();
        if(!relaname){
            return '请填写真实姓名';
        }
        var mobile = $('input[name=mobile]').val();
        if(!mobile){
            return '请填写手机号码';
        }
        if(!/1[3|4|5|8]\d{9}/.test(mobile)){
            return '请填写正确的手机号码';
        }
        var role_id = $('select[name=role_id]').val();
        if(!role_id){
            return '请选择角色类型';
        }
        var password = $('input[name=password]').val();
        if(!password){
            return '请填写密码';
        }
    }
</script>