<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:68:"/var/www/tp5/public/../adminction/view/index/moduleAssignButton.html";i:1512457977;}*/ ?>
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
            <form class="layui-form" id="form" action="/admin.php/Index/assignButton" method="post">
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <input type="text" name="button_name" lay-verify="required" placeholder="可输入按钮名查询" autocomplete="off" class="layui-input">
                    </div>

                    <div class="layui-input-block assignButton">
                        <!--<button class="layui-btn  layui-btn-warm">添加</button>-->
                        <!--<button class="layui-btn layui-btn-warm">添加</button>-->
                        <!--<button class="layui-btn layui-btn-warm">添加</button>-->
                        <!--<button class="layui-btn layui-btn-warm">添加</button>-->
                        <!--<button class="layui-btn layui-btn-warm">添加</button>-->
                    </div>
                    <br>
                    <br>
                    <br>
                    <hr>
                    <div class="layui-input-block buttons">
                        <button class="layui-btn ok">确认</button>
                        <button class="layui-btn no">取消</button>
                    </div>
                    <input type="hidden" name="module_id" value="<?php echo $module_id; ?>">
                    <input type="hidden" name="button_id" value="<?php echo $selfButton; ?>">
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
    var module_id = parseInt($('input[name=module_id]').val());
    var button_name = $('input[name=button_name]').val();

    function checkData(){

    }

    $('input[name=button_name]').bind('input propertychange',function(){
        button_name = $(this).val();
        getButtonAjax();
    });

    $(function(){
        getButtonAjax();
    });

    //ajax获取按钮数据
    function getButtonAjax(){
        $.ajax({
            url:'/admin.php/Index/getButtonAjax',
            datatype:'json',
            type:'get',
            data:{module_id:module_id,button_name:button_name},
            success:function(res){
                if(res.state == 100){
                    var data = res.data;
                    var str = '';
                    for(i in data){
                        if(data[i].isYet == 1){
                            str += '<a href="jaavscript:void(0)" class="layui-btn  layui-btn-normal" data-buttonid="'+data[i].button_id+'">'+data[i].button_name+'</a>';
                        }else{
                            str += '<a href="jaavscript:void(0)"  class="layui-btn  layui-btn-warm" data-buttonid="'+data[i].button_id+'">'+data[i].button_name+'</a>';
                        }
                    }
                    $('.assignButton').empty();
                    $('.assignButton').append(str);
                }
            },
        });
    }

    //点击按钮取消或添加
    $('.assignButton').on('click','a',function(){
        var button_id = $(this).attr('data-buttonid');
        if($(this).hasClass('layui-btn-normal')){
            //删除本模块下的这个按钮
            $(this).removeClass('layui-btn-normal').addClass('layui-btn-warm');
            changeSelfButton(button_id,2);
        }else{
            //为本模块添加这个按钮
            $(this).removeClass('layui-btn-warm').addClass('layui-btn-normal');
            changeSelfButton(button_id,1);
        }
    })

    /**
     *
     * @param button_id
     * @param type 1表示添加到模块中2表示从模块中减去
     */
    function changeSelfButton(button_id,type){
        var selfButton = $('input[name=button_id]').val();
        if(selfButton){
            var selfButtonArray = selfButton.split(',');
            if(type == 1){
                joinToArray(selfButtonArray,button_id);
            }else{
                subToArray(selfButtonArray,button_id);
            }
            $('input[name=button_id]').val(selfButtonArray.join(','));
        }else{
            //为空，表示没有被选中的按钮，直接添加
            $('input[name=button_id]').val(button_id);
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