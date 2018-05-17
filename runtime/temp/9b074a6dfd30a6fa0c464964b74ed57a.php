<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:59:"/var/www/tp5/public/../adminction/view/index/rolePrevb.html";i:1512457977;}*/ ?>
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
            <form class="layui-form" id="form" action="/admin.php/Index/rolePrevbAjax" method="post">
                <div class="layui-form-item prevButton">
                    <div class="layui-input-inline moduleTreeInRole">
                        <?php if(is_array($data) || $data instanceof \think\Collection || $data instanceof \think\Paginator): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                        <div class="layui-input-block" data-mid="<?php echo $v['module_id']; ?>" data-url="<?php echo $v['module_url']; ?>"><?php echo $v['module_name']; ?></div>
                        <input type="hidden" name="modules[]" value="<?php echo $v['module_id']; ?>">
                        <input type="hidden" name="buttons[]" value="<?php echo $v['button_id']; ?>">
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </div>
                    <div class="layui-input-inline buttonBlock">
                        <!--<a href="javascript:void(0)" class="layui-btn primary">添加</a>-->
                    </div>
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

    var role_id;

    function checkData(){

    }

    $(function(){
        //设置宽度并设置lfet居中
        setWaL();
        role_id = parseInt($('input[name=role_id]').val());

    });

    //layui插件情况下获取最新iframe
    function getFirstIframe(){
        return $($(window.parent.document).find("iframe")[0]).parents('.layui-layer-iframe');
    }
    //设置宽度并设置lfet居中
    function setWaL(){
        //获取本iframe
        var iframe = getFirstIframe();
        //设置宽度并设置lfet居中
        var oldWidth = iframe.css('width');
        var oldLeft = iframe.css('left');
        var newWidth = '600px';
        iframe.css('width',newWidth);
        iframe.css('margin','auto');
        var newLeft = parseInt(oldLeft) - (parseInt(newWidth) - parseInt(oldWidth))/2;
        iframe.css('left',newLeft+'px');
    }

    //点击左边模块
    $('.moduleTreeInRole .layui-input-block').click(function(){
        if(!$(this).attr('data-url')){
            return false;
        }
        var module_id = $(this).attr('data-mid');
        //字体变色
        changeColor($(this));
        //ajax请求按钮数据
        getButtonOfRole(module_id)
    });
    //改变颜色
    function changeColor(obj){
        obj.addClass('whiteFont').siblings().removeClass('whiteFont');
    }
    //ajax请求按钮数据
    function getButtonOfRole(module_id){
        $.ajax({
            url:'/admin.php/Index/getButtonOfRoleAjax',
            datatype:'json',
            type:'get',
            data:{module_id:module_id,role_id:role_id},
            success:function(res){
                if(res.state == 100){
                    var data = res.data;
                    if(!data){
                        return false;
                    }
                    var str = '';
                    for(i in data){
                        if(data[i].isYet == 1){
                            str += '<a href="jaavscript:void(0)" class="layui-btn  layui-btn-normal" data-buttonid="'+data[i].button_id+'">'+data[i].button_name+'</a>';
                        }else{
                            str += '<a href="jaavscript:void(0)"  class="layui-btn  layui-btn-warm" data-buttonid="'+data[i].button_id+'">'+data[i].button_name+'</a>';
                        }
                    }
                    $('.buttonBlock').empty();
                    $('.buttonBlock').append(str);
                }
            }
        });
    }

    //点击按钮改变状态
    $('.buttonBlock').on('click','a',function(){
        var button_id = $(this).attr('data-buttonid');
        if($(this).hasClass('layui-btn-normal')){
            //删除本模块下的这个按钮
            $(this).removeClass('layui-btn-normal').addClass('layui-btn-warm');
            changeButtons(button_id,2);
        }else{
            //为本模块添加这个按钮
            $(this).removeClass('layui-btn-warm').addClass('layui-btn-normal');
            changeButtons(button_id,1);
        }
    });

    function changeButtons(button_id,type){
        var buttons;
        var nowObj;
        $('.moduleTreeInRole .layui-input-block').each(function(index){
            if($(this).hasClass('whiteFont')){
//                buttons = $(this).next().next().val();
                nowObj = $(this).next().next();
                buttons = nowObj.val();
            }
        });
        //之前就存在分配过的按钮
        if(buttons){
            var buttonArray = buttons.split(',');
            if(type == 1){
                joinToArray(buttonArray,button_id);
            }else{
                subToArray(buttonArray,button_id);
            }
            nowObj.val(buttonArray.join(','));
        }else{
            //之前不存在分配过的安阿牛
            nowObj.val(button_id);
        }
    }

    //判断一个元素是否在一个数组中，不在的话就将它加入到数组中去
    function joinToArray(array,elem){
        var flag = 0;
        for(i in array){
            if(array[i] == elem){
                flag = 1;
            }
        }
        if(flag == 0){
            array.push(elem);
        }
        //无需return，push方法直接操作原数据
    }
    //判断一个元素是否在一个数组中，在的话就将它截去
    function subToArray(array,elem){
        var flag = 0;
        var index = 0;
        for(i in array){
            if(array[i] == elem){
                flag = 1;
                index = i;
            }
        }
        if(flag == 1){
            array.splice(index,1);
        }
    }
</script>