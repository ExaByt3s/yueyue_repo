<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
    <meta http-equiv="Content-Type" content="text/html; charset=GBK">
    <script type="text/javascript" src="../resources/js/jquery.min.js"></script>
    <script type="text/javascript" src="../resources/js/admin.js?x"></script>
    <link rel="stylesheet" type="text/css" href="../resources/css/style.css">
    <title>供应商管理后台系统</title>
</head>
<body style="background:#E2E9EA;overflow:hidden;">
<!--header-->
<div id="header" class="header">
    <div class="logo">
        <a href="http://www.poco.cn" target="_blank"><img src="../resources/images/permission_logo.gif" width="180"></a>
    </div>
    <div class="nav f_r" style="display:none">
        <a href="http://www.poco.cn" target="_blank">官方网站</a> <i>|</i>&nbsp;&nbsp;
    </div>
    <div class="nav">&nbsp;&nbsp;&nbsp;&nbsp;欢迎你！
        <?php
        $nickname = get_user_nickname_by_user_id($yue_login_id);
        echo $nickname;
        ?>	<i>|</i><a href="logout.php">[退出]</a> </div>
    <div class="topmenu" id="top_tag">
        <ul>
            <li><span id="top_menu_1" role-data="1"><a href="#this">供应商</a></span></li>
        </ul>
    </div>
    <div class="header_footer"></div>
</div>
<script>
    switch_tab2('top_tag','top_menu_','nav_','current',9999);
</script>
<!--header//-->
<!--main-->
<style type="text/css">
    #leftMenu dd i
    {
        width: 155px;
        height: 29px;
        padding-left: 35px;
        line-height: 29px;
        text-align: left;
    }
</style>
<!--main-->
<div id="Main_content">
    <div id="MainBox">
        <div class="main_box">
            <iframe name="main" id="Main" src="main.htm" frameborder="false" scrolling="hidden" width="100%" allowtransparency="true" height="700"></iframe>
        </div>
    </div>
    <div id="leftMenuBox">
        <div id="leftMenu" style="height: 231px; display: block;">
            <div style="padding-left:12px;_padding-left:10px;">
                <dl id="nav_1" class="nav_info">
                    <dt>供应商</dt>
                    <dd class="off">
                        <span><a href="order.php?action=detail" target="main">签到</a></span>
                    </dd>
                    <dd class="off">
                        <span><a href="order.php?action=list" target="main">订单列表</a></span>
                    </dd>
                    <?php
                        include_once 'common.inc.php';
                        $task_supplier_obj = POCO::singleton('pai_mall_supplier_class');
                        $supplier_info = $task_supplier_obj->get_supplier_info($yue_login_id);
                        if($supplier_info['purview_level']==2)
                        {
                    ?>
                    <dd class="off">
                        <span><a href="order.php?action=supplier_list" target="main">供应商列表</a></span>
                    </dd>
                    <?php } ?>
                </dl>
            </div>
        </div>
        <div id="Main_drop">
            <a href="javascript:toggle_leftbar(1);" class="on" style="display: inline;"><img src="../resources/images/admin_barclose.gif" width="11" height="60" border="0"></a>
            <a href="javascript:toggle_leftbar(0);" class="off" style="display: none;"><img src="../resources/images/admin_baropen.gif" width="11" height="60" border="0"></a>
        </div>
    </div>
</div>
<!--main-->
<script type="text/javascript">
    $(function(){
        //选择那一个三级菜单适用
        /*$(".nav_info").find("a").click(function(){
         $(".nav_info a").removeClass('current_red');
         $(this).addClass('current_red');
         });*/

        //二级菜单适用
        $(".nav_info").find("a").click(function(){
            $(".nav_info dd").addClass('off').removeClass('on');
            $(this).parent("span").parent("dd").removeClass('off').addClass('on');
        });
    });
    if(!Array.prototype.map){
        Array.prototype.map = function(fn,scope) {
            var result = [],ri = 0;
            for (var i = 0,n = this.length; i < n; i++){
                if(i in this){
                    result[ri++]  = fn.call(scope ,this[i],i,this);
                }
            }
            return result;
        };
    }
    var getWindowWH = function(){
        return ["Height","Width"].map(function(name){
            return window["inner"+name] ||
            document.compatMode === "CSS1Compat" && document.documentElement[ "client" + name ] || document.body[ "client" + name ]
        });
    }

    window.onload = function (){
        if(!+"\v1" && !document.querySelector) {
            //IE6 IE7
            document.body.onresize = resize;

        }
        else {

            window.onresize = resize;

        }
        function resize() {

            wSize();
            return false;

        }
    }
    function wSize(){
        var str=getWindowWH();
        var strs= new Array();
        strs=str.toString().split(","); //字符串分割
        var h = strs[0] - 95-30;
        $('#leftMenu').height(h);
        $('#Main').height(h);
    }
    wSize();

</script>
</body>
</html>