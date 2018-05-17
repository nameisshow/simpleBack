<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:54:"/var/www/tp5/public/../adminction/view/index/role.html";i:1512385839;s:54:"/var/www/tp5/public/../adminction/view/public/nav.html";i:1512910228;s:57:"/var/www/tp5/public/../adminction/view/public/button.html";i:1511967372;s:57:"/var/www/tp5/public/../adminction/view/public/footer.html";i:1511749595;}*/ ?>
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
                <!--<dl class="layui-nav-child">-->
                    <!--<dd><a href="">基本资料</a></dd>-->
                    <!--<dd><a href="">安全设置</a></dd>-->
                <!--</dl>-->
            </li>
            <li class="layui-nav-item"><a href="/admin.php/Login/clearRedis">清除缓存</a></li>
            <li class="layui-nav-item"><a href="/admin.php/Login/logout">登出</a></li>
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

<div class="layui-body mycontainer">
    <!-- 内容主体区域 -->
    <!--百分百适配-->
    <div class="layui-fluid">
        <!--位置显示-面包屑导航-->
        <div class="layui-row layui-bg-gray button_nav">
            <span><b>位置：</b></span>
            <span class="layui-breadcrumb">
                  <a href="javascript:void(0)">系统管理</a>
                  <a href="javascript:void(0)">权限管理</a>
                  <a href="javascript:void(0)"><cite>角色管理</cite></a>
            </span>
        </div>
        <!--操作栏-->
        <table class="layui-table button_opear" lay-skin="line">
            <tr>
                <!--加载按钮-->
                <td>
    <div class="layui-btn-group" id="opearGroup">
        <?php if(is_array($button) || $button instanceof \think\Collection || $button instanceof \think\Paginator): $i = 0; $__LIST__ = $button;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
        <button class="layui-btn layui-btn-normal" data-name="<?php echo $v['button_name']; ?>" data-event="<?php echo $v['button_event']; ?>" data-url="<?php echo $v['button_url']; ?>" data-type="<?php echo $v['button_type']; ?>"><?php echo $v['button_name']; ?></button>
        <?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
</td>
                <!--查询条件-->

            </tr>
        </table>
        <!--分割线-->
        <hr>
        <!--数据集-->
        <table lay-filter="dataSet" class="dataSet" lay-data="{id:'dataSet'}">
            <thead>
                <tr>
                    <td lay-data="{type:'checkbox',width:80}"></td>
                    <td lay-data="{field:'role_id'}">角色id</td>
                    <td lay-data="{field:'role_name'}">角色名</td>
                    <td lay-data="{field:'addtime'}">添加时间</td>
                    <td lay-data="{field:'role_desc'}">角色描述</td>
                </tr>
            </thead>
            <tbody>
            <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                <tr data-id="<?php echo $v['role_id']; ?>">
                    <td><input type="checkbox" lay-skin="primary"></td>
                    <td><?php echo $v['role_id']; ?></td>
                    <td><?php echo $v['role_name']; ?></td>
                    <td><?php echo date('y-m-d H:i:s',$v['addtime']); ?></td>
                    <td><?php echo $v['role_desc']; ?></td>
                </tr>
            <?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
        <!--分割线-->
        <hr>
        <!--分页-->

    </div>
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
<script src="__PUBLIC__/admin/js/jquery.min.js"></script>
<script src="__PUBLIC__/admin/js/project.js"></script>
<script>

</script>