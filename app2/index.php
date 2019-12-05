<?php

$is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android') ? true : false;
$is_iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') ? true : false;  

if($is_android || $is_iphone)
{
	$url = "http://app2.yueus.com/mobile/";
	header("Location:{$url}");
	exit;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta property="qc:admins" content="34114622376155536375" /> 
    <meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
    <title>约约--模特邀约第一移动平台</title>
    <meta name="keywords" content="约约，模特，摄影师，约拍，私拍，外拍，摄影，交易" />
    <meta name="Description" content="约约是最美的O2O产品，在这里，每个美女都利用自己的零碎时间，用美丽来变现。信用机制让交易更安心，私聊机制确保摄影师与模特真实沟通，多维度搜索帮摄影师到自己想拍的模特，支持一人约多人拍从此创作不再孤单。" />
    <link href="http://www.yueus.com/css/zt.css?3" type="text/css" rel="stylesheet" />

    <script src="http://www.yueus.com/js/jquery-1.8.3.min.js"></script>
    <script src="http://www.yueus.com/js/jquery.fullPage.min.js"></script>
    <script src="http://www.yueus.com/js/jquery.easing.min.js"></script>
    <script>
    $(function(){
        $('#dowebok').fullpage({
            sectionsColor: ['#73498a', '#dde6ec', '#fee6e6', '#202428'],
            anchors: ['section1', 'section2', 'section3', 'section4'],
            menu: '#page'
        });
    });
    </script>

</head>



<body>

<div class="yue-page font_wryh">
  <!--导航-->
  <div class="nav-bar">
    <div class="container w1000 clearfix">
      <h2 class="logo" id="logo"><a href="javascript:void(0)"></a></h2>
      <div class="contact">
        
        <span>客服电话：4000-82-9003</span>
        |
        <span class="code-nav-item"><a href="#this">微信公众号</a>
               <!--微信弹出层-->
               <div class="pop-weixin"> <i class="top-icon"></i>
                 <div class="pop-con font_wryh circle-5px clearfix">
                   <div class="s-item item">
                     <div class="img"><img src="http://www.yueus.com/images/s-code-img-150x150.png" /></div>
                     <p>我是摄影师，我要约拍!</p>
                   </div>
                   <div class="m-item item">
                     <div class="img"><img src="http://www.yueus.com/images/m-code-img-150x150.png" /></div>
                     <p>我是模特，我要变现!</p>
                   </div>
                 </div>
               </div>
               <!--微信弹出层 end-->
        </span>
       <!--  <span><a href="#">体验师论坛</a></span>|
        <span><a href="#">微信公众号</a> 
        </span> -->
      </div>
    </div>
  </div>
  <!--导航 end-->
  <div class="yue-wrapper" id="dowebok">
    <div class="section section1" >
      <div class="container">
        <div class="content">
          <!--内容-->
          <div class="path1 clearfix">
            <div class="iphone-item"></div>
            <div class="text-item">
              <div class="listItem"></div>
              <div class="download-item">
                <h3 class="title">扫码二维码，体验最美的O2O产品</h3>
                <div class="code-item clearfix">
                  <div class="code-img circle-5px"><img src="http://www.yueus.com/images/code-img-116x116.png" /></div>
                  <div class="btn-item"><a href="https://itunes.apple.com/cn/app/yueyue/id935185009?l=zh&ls=1&mt=8" class="ui-btn ui-btn-bf iphone-btn"><i class="icon"></i><em>iPhone版下载</em></a><a href="http://app.yueus.com/download_android.php" class="ui-btn ui-btn-bf android-btn"><i class="icon"></i><em>Android版下载</em></a></div>
                </div>
              </div>
            </div>
          </div>
          <!--内容 end-->


        </div>
        <div class="next_btn">
            <span class=""  style="display:none" id="ie_allow"><img src="http://www.yueus.com/images/allow.gif" /></span>
            <span class="allow "  style="display:none"  id="gg_allow" style="display:none"><img src="http://www.yueus.com/images/next-btn-42x22.png" /></span>
        </div>
      </div>
    </div>
    <div class="section section2" >
      <div class="container">
        <div class="content">
          <!--内容-->
          <div class="path2 clearfix">
            <div class="img-item">
          <div class="img-list">
              <ul id="hl_mt_img">
                <li style="display:"><img src="http://www.yueus.com/images/hl_mt_1.jpg" /></li>
                <li style="display:none"><img src="http://www.yueus.com/images/hl_mt_2.jpg" /></li>
                <li style="display:none"><img src="http://www.yueus.com/images/hl_mt_3.jpg" /></li>
                <li style="display:none"><img src="http://www.yueus.com/images/hl_mt_4.jpg" /></li>
                <li style="display:none"><img src="http://www.yueus.com/images/hl_mt_5.jpg" /></li>
              </ul>
            </div>
            </div>
            <div class="list-item">
            <div class="listItem"></div>
            <div class="list circle-5px">
              <ul id="hl_mt">
                <li class="first cur">
                  <div class="item-1"> <a href="#this" class="clearfix"> <i class="icon common-2-bg"></i>
                    <div class="txt-item">
                      <dl>
                        <dt>100000+模特随心约</dt>
                        <dd>中国第一摄影社区POCO.cn等数十个摄影平台<br />
                          结为战略伙伴，摄影师、模特最佳约拍平台</dd>
                      </dl>
                    </div>
                    </a> </div>
                </li>
                <li>
                  <div class="item-2 clearfix"> <a href="#this" class="clearfix"> <i class="icon common-2-bg"></i>
                    <div class="txt-item">
                      <dl>
                        <dt>98% 摄影师点赞的约拍工具</dt>
                        <dd>操作便捷，多维度的搜索功能让你在最短的时间找到灵感女神</dd>
                      </dl>
                    </div>
                    </a> </div>
                </li>
                <li>
                  <div class="item-3"> <a href="#this" class="clearfix"> <i class="icon common-2-bg"></i>
                    <div class="txt-item">
                      <dl>
                        <dt>这是最透明的约拍神器</dt>
                        <dd>首创的私聊模式让你见到最真实的模特，双向评价让外拍中所有问题一扫而光</dd>
                      </dl>
                    </div>
                    </a> </div>
                </li>
                <li>
                  <div class="item-4"> <a href="#this" class="clearfix"> <i class="icon common-2-bg"></i>
                    <div class="txt-item">
                      <dl>
                        <dt>1对1拍摄不再尴尬</dt>
                        <dd>多种约拍模式，让你轻松实现1对1拍摄</dd>
                      </dl>
                    </div>
                    </a> </div>
                </li>
                <li class="last">
                  <div class="item-5"> <a href="#" class="clearfix"> <i class="icon common-2-bg"></i>
                    <div class="txt-item">
                      <dl>
                        <dt>最火爆的外拍活动集合</dt>
                        <dd>中国最火爆的POCO摄影外拍，现在通过APP搜索即可参与</dd>
                      </dl>
                    </div>
                    </a> </div>
                </li>
              </ul>
            </div>
            </div>
          </div>
          <!--内容 end-->
        </div>
      </div>
    </div>
    <div class="section section3">
      <div class="container">
        <div class="content">
          <!--内容-->
          <div class="path3 clearfix">
          <div class="img-item">
          <div class="img-list">
                <ul id="shenfen_wrap_img">
                  <li ><img src="http://www.yueus.com/images/shenfen_1.jpg" /></li>
                  <li style="display:none"><img src="http://www.yueus.com/images/shenfen_2.jpg" /></li>
                  <li style="display:none"><img src="http://www.yueus.com/images/shenfen_3.jpg" /></li>
                  <li style="display:none"><img src="http://www.yueus.com/images/shenfen_4.jpg" /></li>
                </ul>
              </div>
            </div>
            <div class="list-item">
            <div class="listItem"></div>
            <div class="list clearfix circle-5px">
              <ul id="shenfen_wrap">
                <li class="cur">
                  <div class="item item-1"><a href="#this"><i class="icon common-3-bg"></i><em>三重身份认证</em></a></div>
                </li>
                <li>
                  <div class="item item-2"><a href="#this"><i class="icon common-3-bg"></i><em>首创二维码签到</em></a></div>
                </li>
                <li>
                  <div class="item item-3"><a href="#this"><i class="icon common-3-bg"></i><em>支付担保系统</em></a></div>
                </li>
                <li>
                  <div class="item item-4"><a href="#this"><i class="icon common-3-bg"></i><em>双向评价机制</em></a></div>
                </li>
              </ul>
            </div>
          </div>
          </div>
          <!--内容 end-->
        </div>
      </div>
    </div>
    <div class="section section4" >
      <div class="container">
        <div class="content">
          <!--内容-->
          <div class="path4">
            <div class="listItem"></div>
            <div class="download-item">
              <div class="code-item clearfix">
                <div class="code-img circle-5px"><img src="http://www.yueus.com/images/code-img-145x145.png" /></div>
                <div class="btn-item"><a href="https://itunes.apple.com/cn/app/yueyue/id935185009?l=zh&ls=1&mt=8" class="ui-btn ui-btn-fb iphone-btn"><i class="icon"></i><em>iPhone版下载</em></a><a href="http://app.yueus.com/download_android.php" class="ui-btn ui-btn-fb android-btn"><i class="icon"></i><em>Android版下载</em></a></div>
              </div>
            </div>
          </div>
          <!--内容 end-->
        </div>
        <div class="footer"><a href="http://www.yueus.com/about.php">公司介绍</a>|<a href="http://www.yueus.com/about.php#lxwm">联系我们</a>|<!-- <a href="#">体验师论坛</a>| --><span>Copyright&copy; 2003-2015 YUEUS.COM </span></div>
      </div>
    </div>
  </div>
  <!--翻页-->
  <ol class="page" id="page">
    <li data-menuanchor="section1" class="active"><a href="#section1"><span class="dot">1</span></a></li>
    <li data-menuanchor="section2" class=""><a href="#section2"><span class="dot">2</span></a></li>
    <li data-menuanchor="section3" class=""><a href="#section3"><span class="dot">3</span></a></li>
    <li data-menuanchor="section4" class=""><a href="#section4"><span class="dot">4</span></a></li>
  </ol>
  <!--翻页 end-->
</div>

<script type="text/javascript">

$(document).ready(function(b) {


    //判断ua  显示下方的箭头按钮
    var Sys = {};
    var ua = navigator.userAgent.toLowerCase();
    var s;
    (s = ua.match(/rv:([\d.]+)\) like gecko/)) ? Sys.ie = s[1] :
    (s = ua.match(/msie ([\d.]+)/)) ? Sys.ie = s[1] :
    (s = ua.match(/firefox\/([\d.]+)/)) ? Sys.firefox = s[1] :
    (s = ua.match(/chrome\/([\d.]+)/)) ? Sys.chrome = s[1] :
    (s = ua.match(/opera.([\d.]+)/)) ? Sys.opera = s[1] :
    (s = ua.match(/version\/([\d.]+).*safari/)) ? Sys.safari = s[1] : 0;
    
    if(Sys.ie)
    {
        $('#ie_allow').show();
        $('.next_btn').css({
            bottom: '5px'
        });
    }
    else
    {
        $('#gg_allow').show();
    }


    // var $hl_li = $('#hl_mt li');
    // $hl_li.click(function() {
    //     $hl_li.removeClass('cur');
    //     $(this).addClass('cur');
    //     var index = $hl_li.index($(this));
    //     $('#hl_mt_img li').hide();
    //     $('#hl_mt_img li').eq(index).show();
    // });


    //第2屏自动播放切换
    var $ele = $('#hl_mt');
    var $hl_li = $('#hl_mt li');
    var init =  0 ;

    //自动执行
    function autoExt()
    {
        $hl_li.removeClass('cur');
        $('#hl_mt_img li').hide();
        if ( init >= $hl_li.length) 
        {
            init = 0 ;
        } 
        init++ ; 
        $hl_li.eq(init-1).addClass('cur');
        $('#hl_mt_img li').eq(init-1).show();
        autoTime = setTimeout(autoExt, 3000);
    } 
    autoExt();

    $ele.on('mouseover',function(){
        clearTimeout(autoTime);
    });

    $ele.on('mouseout',function(){
        clearTimeout(autoTime);
        autoTime = setTimeout(autoExt, 3000);
    });

    $hl_li.click(function() {
        var index = $hl_li.index($(this));
        $hl_li.removeClass('cur');
        $(this).addClass('cur');
        $('#hl_mt_img li').hide();
        $('#hl_mt_img li').eq(index).show();
        init = index ;
    });


    //第3屏自动播放切换
    var $shenfen_wrap_li = $('#shenfen_wrap li');
    $shenfen_wrap_li.click(function() {
        $shenfen_wrap_li.removeClass('cur');
        $(this).addClass('cur');
        var index = $shenfen_wrap_li.index($(this));
        $('#shenfen_wrap_img li').hide();
        $('#shenfen_wrap_img li').eq(index).show();
    });

    var $ele2 = $('#shenfen_wrap');
    var $hl_li2 = $('#shenfen_wrap li');
    var init2 =  0 ;

    //自动执行
    function autoExt2()
    {
        $hl_li2.removeClass('cur');
        $('#shenfen_wrap_img li').hide();
        if ( init2 >= $hl_li2.length) 
        {
            init2 = 0 ;
        } 
        init2++ ; 
        $hl_li2.eq(init2-1).addClass('cur');
        $('#shenfen_wrap_img li').eq(init2-1).show();

        autoTime2 = setTimeout(autoExt2, 3000);
    } 
    autoExt2();

    $ele2.on('mouseover',function(){
        clearTimeout(autoTime2);
    });

    $ele2.on('mouseout',function(){
        clearTimeout(autoTime2);
        autoTime2 = setTimeout(autoExt2, 3000);
    });

    $hl_li2.click(function() {
        var index = $hl_li2.index($(this));
        $hl_li2.removeClass('cur');
        $(this).addClass('cur');
        $('#shenfen_wrap_img li').hide();
        $('#shenfen_wrap_img li').eq(index).show();
        init2 = index ;
    });

    //跳到顶部
    $('#logo').click(function() {
        b.fn.fullpage.moveTo(1);
    });

    $('.next_btn').click(function() {
        b.fn.fullpage.moveTo(2);
    });

    //微信公众号弹窗口 
    $('.code-nav-item').hover(function()
    {
        $(this).addClass('code-nav-item-cur');
    },function()
    {
        $(this).removeClass('code-nav-item-cur');
    });



    $('.bigshow-nav-item').hover(function()
    {
        $(this).addClass('bigshow-nav-item-cur');
    },function()
    {
        $(this).removeClass('bigshow-nav-item-cur');
    });

});

</script>





</body>
</html>   