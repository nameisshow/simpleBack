<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:59:"/var/www/tp5/public/../adminction/view/index/rolePrevm.html";i:1512912655;}*/ ?>
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
        .layui-form-item .layui-form-checkbox[lay-skin=primary]{
            margin-top: 0;
        }
    </style>
</head>
<body class="layui-layout-body">
    <div class="layui-body mycontainer">
        <!-- 内容主体区域 -->
        <!--百分百适配-->
        <div class="layui-fluid">
            <form class="layui-form" id="form" action="/admin.php/Index/rolePrevmAjax" method="post">
                <div class="layui-form-item">
                    <div class="layui-input-block moduleTreeInRole">
                        <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                        <div class="layui-input-block" data-mid="<?php echo $v['module_id']; ?>" data-pid="<?php echo $v['module_pid']; ?>"><?php echo $v['module_name']; ?></div>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </div>
                    <input type="hidden" name="module_id" value="<?php echo $selfModule; ?>">
                    <input type="hidden" name="role_id" value="<?php echo $role_id; ?>">
                    <hr>
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

    }

    $('.moduleTreeInRole').on('click','.layui-input-block',function(){
        var module_id = parseInt($(this).attr('data-mid'));
        var module_pid = parseInt($(this).attr('data-pid'));
        console.log(module_id);
        console.log(module_pid);

        toSelect(module_id,$(this));
        setModuleIds();
    });
    //复选框的选择
    function toSelect(module_id,obj){
        if(obj){
            var finalObj;
            var final_module_id;
            obj.nextAll().each(function(index){
                var pid = parseInt($(this).attr('data-pid'));
                if(pid == module_id){
                    //获取当前层级最后一个tr的module_id
                    final_module_id = parseInt($(this).attr('data-mid'));
                    //获取当前层级最后一个tr的点击对象
                    finalObj = $(this);
                    $(this).find('.layui-form-checkbox').toggleClass('layui-form-checked');
                    toSelect(final_module_id,finalObj);
                }
            });
        }else{
            return false;
        }
    }
    //收集所有被选择的模块
    function setModuleIds(){
        var moduleIdArray = new Array();
        $('.moduleTreeInRole .layui-input-block').each(function(index){
            if($(this).find('.layui-form-checkbox').hasClass('layui-form-checked')){
                var module_id = $(this).attr('data-mid');
                moduleIdArray.push(module_id);
            }
        });
        $('input[name=module_id]').val(moduleIdArray.join(','));
    }


</script>