<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:62:"/var/www/tp5/public/../application/index/view/index/login.html";i:1520137136;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="__PUBLIC__/talker/css/style.css">
    <title>Document</title>
</head>
<body>
    <div class="loginDiv">
        <div class="username">
            <div class="loginLeft">用户名:</div>
            <div><input type="text" name="username" placeholder="请输入用户名"></div>
        </div>
        <div class="userpass">
            <div class="loginLeft">密&nbsp;&nbsp;&nbsp;&nbsp;码:</div>
            <div><input type="password" name="userpass" placeholder="请输入密码"></div>
        </div>
        <div class="loginButton">
            <button>登录</button>
        </div>
    </div>
</body>
<script src="__PUBLIC__/talker/js/jquery.min.js"></script>
<script>

    //点击登录按钮
    $('.loginButton button').click(function(){

        setButton();

        var data = checkData();

        if(data == "" || data == undefined){
            alert('请输入用户名或密码');
            setButton();
            return false;
        }

        toLogin(data);

    });

    //检查数据
    function checkData(){
        var username = $('input[name=username]').val();
        var userpass = $('input[name=userpass]').val();
        if(!username || !userpass){
            return false;
        }
        return {"username":username,"userpass":userpass};
    }

    //设置按钮是否可操作
    function setButton(){
        if($('.loginButton button').attr('disabled')){
            $('.loginButton button').attr('disabled',false);
        }else{
            $('.loginButton button').attr('disabled',true);
        }
    }

    //发起ajax登录请求
    function toLogin(userdata){
        $.ajax({
            url:'/index/index/loginAjax',
            datatype:'json',
            type:'post',
            data:userdata,
            success:function(res){
                var res = eval('('+res+')');
                if(res.state != 100){
                    alert(res.msg);
                    setButton();
                }else{
                    var userId = res.data.user_id;
                    window.location = '/index/index/im?user_id='+userId;
                }
            },
            error:function(){
                alert('请求错误');
            }
        });
    }




</script>
</html>
