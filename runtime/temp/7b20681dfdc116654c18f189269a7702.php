<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:62:"D:\phpStudy\WWW\tp5\public/../adminction/view\index\index.html";i:1511759303;s:61:"D:\phpStudy\WWW\tp5\public/../adminction/view\public\nav.html";i:1512476322;s:64:"D:\phpStudy\WWW\tp5\public/../adminction/view\public\footer.html";i:1511749595;}*/ ?>
<!--加载头部-->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>layout 后台大布局 - Layui</title>
    <link rel="stylesheet" href="__PUBLIC__/layui/css/layui.css">
    <link rel="stylesheet" href="__PUBLIC__/admin/css/style.css">
</head>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
    <div class="layui-header">
        <div class="layui-logo">layui 后台布局</div>
        <!-- 头部区域（可配合layui已有的水平导航） -->
        <ul class="layui-nav layui-layout-left">
            <li class="layui-nav-item"><a href="javascript:void(0)">控制台</a></li>
            <li class="layui-nav-item"><a href="javascript:void(0)">商品管理</a></li>
            <li class="layui-nav-item"><a href="javascript:void(0)">用户</a></li>
            <?php if(is_array($topMenu) || $topMenu instanceof \think\Collection || $topMenu instanceof \think\Paginator): $i = 0; $__LIST__ = $topMenu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                <li class="layui-nav-item <?php if($topNav == $v['module_id']): ?>layui-this<?php endif; ?>"><a href="<?php echo $v['module_url']; ?>"><?php echo $v['module_name']; ?></a></li>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
        <ul class="layui-nav layui-layout-right">
            <li class="layui-nav-item">
                <a href="javascript:;">
                    <img src="http://t.cn/RCzsdCq" class="layui-nav-img">
                    <?php echo $userInfo['username']; ?>
                </a>
                <dl class="layui-nav-child">
                    <dd><a href="">基本资料</a></dd>
                    <dd><a href="">安全设置</a></dd>
                </dl>
            </li>
            <li class="layui-nav-item"><a href="/admin.php/Login/logout">退了</a></li>
        </ul>
    </div>

    <!--左侧导航区-->
    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll">
            <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
            <ul class="layui-nav layui-nav-tree"  lay-filter="test">
                <?php if(is_array($leftMenu) || $leftMenu instanceof \think\Collection || $leftMenu instanceof \think\Paginator): $i = 0; $__LIST__ = $leftMenu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$left): $mod = ($i % 2 );++$i;?>
                <li class="layui-nav-item">
                    <a class="" href="javascript:;"><?php echo $left['module_name']; ?></a>
                    <dl class="layui-nav-child">
                        <?php if(is_array($left['child_module']) || $left['child_module'] instanceof \think\Collection || $left['child_module'] instanceof \think\Paginator): $i = 0; $__LIST__ = $left['child_module'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$child): $mod = ($i % 2 );++$i;?>
                        <dd class="<?php if($leftNav == $child['module_id']): ?>layui-this<?php endif; ?>"><a href="<?php echo $child['module_url']; ?>"><?php echo $child['module_name']; ?></a></dd>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </dl>
                </li>
                <?php endforeach; endif; else: echo "" ;endif; ?>
                <!--<li class="layui-nav-item">-->
                <!--<a href="javascript:;">解决方案</a>-->
                <!--<dl class="layui-nav-child">-->
                <!--<dd><a href="javascript:;">列表一</a></dd>-->
                <!--<dd><a href="javascript:;">列表二</a></dd>-->
                <!--<dd><a href="">超链接</a></dd>-->
                <!--</dl>-->
                <!--</li>-->
            </ul>
        </div>
    </div>

<!--<div class="layui-body">-->
    <!--&lt;!&ndash; 内容主体区域 &ndash;&gt;-->
    <!--<div style="padding: 15px;">内容主体区域</div>-->
<!--</div>-->

<div class="layui-body">
    <!-- 内容主体区域 -->
    <div style="padding: 15px;">您好<b><?php echo $userInfo['username']; ?></b>，欢迎您登陆XX后台管理系统</div>
</div>

<!--加载底部-->
            <div class="layui-footer">
                <!-- 底部固定区域 -->
                © layui.com - 底部固定区域
            </div>
        </div>
    </body>
</html>

<!--js代码-->
<script src="__PUBLIC__/layui/layui.js"></script>
<script>
layui.config({
    base: '__STATIC__/admin/js/' //你存放新模块的目录，注意，不是layui的模块目录
}).use('project'); //加载入口
</script>