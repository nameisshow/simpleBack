<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:59:"/var/www/tp5/public/../adminction/view/index/buttonAdd.html";i:1512457815;}*/ ?>
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
            <form class="layui-form" id="form" action="/admin.php/Index/buttonAdd" method="post">
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <input type="text" name="button_name" lay-verify="required" placeholder="请输入按钮名" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-input-block">
                        <input type="text" name="button_event" lay-verify="required" placeholder="请输入按钮事件" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-input-block">
                        <select name="button_type" required lay-verify="required">
                            <option value="">请选择按钮类型</option>
                            <option value="ajax">ajax</option>
                            <option value="iframe">iframe</option>
                            <option value="href">href</option>
                        </select>
                    </div>
                    <div class="layui-input-block">
                        <input type="text" name="button_sort"  lay-verify="required" placeholder="请输入按钮排序" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-input-block">
                        <input type="text" name="button_desc"  lay-verify="required" placeholder="请输入按钮描述" autocomplete="off" class="layui-input">
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
        var button_name = $('input[name=button_name]').val();
        if(!button_name){
            return '请填写按钮名称';
        }
        var button_event = $('input[name=button_event]').val();
        if(!button_name){
            return '请填写按钮事件';
        }
        var type = $('select[name=button_type]').val();
        if(!type){
            return '请选择按钮类型';
        }
    }
</script>