<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:57:"/var/www/tp5/public/../adminction/view/index/roleAdd.html";i:1512457816;}*/ ?>
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
            <form class="layui-form" id="form" action="/admin.php/Index/roleAdd" method="post">
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <input type="text" name="role_name" lay-verify="required" placeholder="请输入角色名" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-input-block">
                        <input type="text" name="role_desc" lay-verify="required" placeholder="请输入角色描述" autocomplete="off" class="layui-input">
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
        var role_name = $('input[name=role_name]').val();
        if(!role_name){
            return '请填写角色名称';
        }
    }
</script>