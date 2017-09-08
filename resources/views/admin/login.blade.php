<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<!-- saved from url=(0031)http://freelancesuite.com/demo/ -->
<html>
<script src="chrome-extension://cmhomipkklckpomafalojobppmmidlgl/_locales/en/Kernel.js?0.12088552381221906"></script>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{--<link rel="shortcut icon" type="image/ico" href="http://freelancesuite.com/images/favicon.ico">--}}
    <title>后台管理系统</title>
    <link href="{{ asset('/css/login.css') }}" type="text/css" media="screen" rel="stylesheet">
    {{--<link type="text/css" rel="stylesheet" href="chrome-extension://cmhomipkklckpomafalojobppmmidlgl/_locales/en/main.css?0.7624704986222743"><script src="chrome-extension://cmhomipkklckpomafalojobppmmidlgl/_locales/en/foreground.js?0.5437889784925845"></script></head>--}}
<body id="login">
<div id="wrappertop"></div>
<div id="wrapper">
    <div id="content">
        <div id="header" style="font-size: 20px;">
            <h1>kakaqi.club后台管理系统</h1>
        </div>
        <div id="darkbanner" class="banner320">
            <h2>后台登录</h2>
        </div>
        <div id="darkbannerwrap">
        </div>
        <form name="form1" method="post">
            {{csrf_field()}}
            <fieldset class="form">
                <p>
                    <label for="user_name">登录名:</label>
                    <input name="user_name" id="user_name" type="text" value="">
                </p>
                <p>
                    <label for="password">密码:</label>
                    <input name="password" id="password" type="password">
                </p>
                <button type="button" class="positive" name="Submit" id="login-btn">
                    <img src="{{ asset('image/key.png') }}" alt="Announcement">登录
                </button>
                <ul id="forgottenpassword">
                    <li class="boldtext">|</li>
                    <li><a href="#">忘记密码?</a></li>
                </ul>
            </fieldset>


        </form></div>
</div>

<div id="wrapperbottom_branding">
    <div id="wrapperbottom_branding_text">
        <a href="#" style="text-decoration:none">kakaqi.club</a>
    </div>
</div>
</body>
<script src="{{ asset('js/app.js') }}"></script>
<script>
    $(function () {
        $('#login-btn').click(function () {
            var user_name = $('#user_name').val();
            var password = $('#password').val();
            if(  user_name == '') {
                alert('登录名不能为空');
                return
            }
            if( password == '' ){
                alert('密码不能为空');
                return
            }
            
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type:'post',
                dataType : 'json',
                url : '/admin/authorization',
                data : {
                    user_name : user_name,
                    password : password
                },
                success : function (re) {
                    if(re.code != 0){
                        alert(re.text)
                        return
                    }
                    location.href='/admin/index';
                }
                
            })
        })
    })
</script>
</html>