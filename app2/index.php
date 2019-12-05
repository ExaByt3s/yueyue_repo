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
    <title>ԼԼ--ģ����Լ��һ�ƶ�ƽ̨</title>
    <meta name="keywords" content="ԼԼ��ģ�أ���Ӱʦ��Լ�ģ�˽�ģ����ģ���Ӱ������" />
    <meta name="Description" content="ԼԼ��������O2O��Ʒ�������ÿ����Ů�������Լ�������ʱ�䣬�����������֡����û����ý��׸����ģ�˽�Ļ���ȷ����Ӱʦ��ģ����ʵ��ͨ����ά����������Ӱʦ���Լ����ĵ�ģ�أ�֧��һ��Լ�����ĴӴ˴������ٹµ���" />
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
  <!--����-->
  <div class="nav-bar">
    <div class="container w1000 clearfix">
      <h2 class="logo" id="logo"><a href="javascript:void(0)"></a></h2>
      <div class="contact">
        
        <span>�ͷ��绰��4000-82-9003</span>
        |
        <span class="code-nav-item"><a href="#this">΢�Ź��ں�</a>
               <!--΢�ŵ�����-->
               <div class="pop-weixin"> <i class="top-icon"></i>
                 <div class="pop-con font_wryh circle-5px clearfix">
                   <div class="s-item item">
                     <div class="img"><img src="http://www.yueus.com/images/s-code-img-150x150.png" /></div>
                     <p>������Ӱʦ����ҪԼ��!</p>
                   </div>
                   <div class="m-item item">
                     <div class="img"><img src="http://www.yueus.com/images/m-code-img-150x150.png" /></div>
                     <p>����ģ�أ���Ҫ����!</p>
                   </div>
                 </div>
               </div>
               <!--΢�ŵ����� end-->
        </span>
       <!--  <span><a href="#">����ʦ��̳</a></span>|
        <span><a href="#">΢�Ź��ں�</a> 
        </span> -->
      </div>
    </div>
  </div>
  <!--���� end-->
  <div class="yue-wrapper" id="dowebok">
    <div class="section section1" >
      <div class="container">
        <div class="content">
          <!--����-->
          <div class="path1 clearfix">
            <div class="iphone-item"></div>
            <div class="text-item">
              <div class="listItem"></div>
              <div class="download-item">
                <h3 class="title">ɨ���ά�룬����������O2O��Ʒ</h3>
                <div class="code-item clearfix">
                  <div class="code-img circle-5px"><img src="http://www.yueus.com/images/code-img-116x116.png" /></div>
                  <div class="btn-item"><a href="https://itunes.apple.com/cn/app/yueyue/id935185009?l=zh&ls=1&mt=8" class="ui-btn ui-btn-bf iphone-btn"><i class="icon"></i><em>iPhone������</em></a><a href="http://app.yueus.com/download_android.php" class="ui-btn ui-btn-bf android-btn"><i class="icon"></i><em>Android������</em></a></div>
                </div>
              </div>
            </div>
          </div>
          <!--���� end-->


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
          <!--����-->
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
                        <dt>100000+ģ������Լ</dt>
                        <dd>�й���һ��Ӱ����POCO.cn����ʮ����Ӱƽ̨<br />
                          ��Ϊս�Ի�飬��Ӱʦ��ģ�����Լ��ƽ̨</dd>
                      </dl>
                    </div>
                    </a> </div>
                </li>
                <li>
                  <div class="item-2 clearfix"> <a href="#this" class="clearfix"> <i class="icon common-2-bg"></i>
                    <div class="txt-item">
                      <dl>
                        <dt>98% ��Ӱʦ���޵�Լ�Ĺ���</dt>
                        <dd>������ݣ���ά�ȵ�����������������̵�ʱ���ҵ����Ů��</dd>
                      </dl>
                    </div>
                    </a> </div>
                </li>
                <li>
                  <div class="item-3"> <a href="#this" class="clearfix"> <i class="icon common-2-bg"></i>
                    <div class="txt-item">
                      <dl>
                        <dt>������͸����Լ������</dt>
                        <dd>�״���˽��ģʽ�����������ʵ��ģ�أ�˫����������������������һɨ����</dd>
                      </dl>
                    </div>
                    </a> </div>
                </li>
                <li>
                  <div class="item-4"> <a href="#this" class="clearfix"> <i class="icon common-2-bg"></i>
                    <div class="txt-item">
                      <dl>
                        <dt>1��1���㲻������</dt>
                        <dd>����Լ��ģʽ����������ʵ��1��1����</dd>
                      </dl>
                    </div>
                    </a> </div>
                </li>
                <li class="last">
                  <div class="item-5"> <a href="#" class="clearfix"> <i class="icon common-2-bg"></i>
                    <div class="txt-item">
                      <dl>
                        <dt>��𱬵����Ļ����</dt>
                        <dd>�й���𱬵�POCO��Ӱ���ģ�����ͨ��APP�������ɲ���</dd>
                      </dl>
                    </div>
                    </a> </div>
                </li>
              </ul>
            </div>
            </div>
          </div>
          <!--���� end-->
        </div>
      </div>
    </div>
    <div class="section section3">
      <div class="container">
        <div class="content">
          <!--����-->
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
                  <div class="item item-1"><a href="#this"><i class="icon common-3-bg"></i><em>���������֤</em></a></div>
                </li>
                <li>
                  <div class="item item-2"><a href="#this"><i class="icon common-3-bg"></i><em>�״���ά��ǩ��</em></a></div>
                </li>
                <li>
                  <div class="item item-3"><a href="#this"><i class="icon common-3-bg"></i><em>֧������ϵͳ</em></a></div>
                </li>
                <li>
                  <div class="item item-4"><a href="#this"><i class="icon common-3-bg"></i><em>˫�����ۻ���</em></a></div>
                </li>
              </ul>
            </div>
          </div>
          </div>
          <!--���� end-->
        </div>
      </div>
    </div>
    <div class="section section4" >
      <div class="container">
        <div class="content">
          <!--����-->
          <div class="path4">
            <div class="listItem"></div>
            <div class="download-item">
              <div class="code-item clearfix">
                <div class="code-img circle-5px"><img src="http://www.yueus.com/images/code-img-145x145.png" /></div>
                <div class="btn-item"><a href="https://itunes.apple.com/cn/app/yueyue/id935185009?l=zh&ls=1&mt=8" class="ui-btn ui-btn-fb iphone-btn"><i class="icon"></i><em>iPhone������</em></a><a href="http://app.yueus.com/download_android.php" class="ui-btn ui-btn-fb android-btn"><i class="icon"></i><em>Android������</em></a></div>
              </div>
            </div>
          </div>
          <!--���� end-->
        </div>
        <div class="footer"><a href="http://www.yueus.com/about.php">��˾����</a>|<a href="http://www.yueus.com/about.php#lxwm">��ϵ����</a>|<!-- <a href="#">����ʦ��̳</a>| --><span>Copyright&copy; 2003-2015 YUEUS.COM </span></div>
      </div>
    </div>
  </div>
  <!--��ҳ-->
  <ol class="page" id="page">
    <li data-menuanchor="section1" class="active"><a href="#section1"><span class="dot">1</span></a></li>
    <li data-menuanchor="section2" class=""><a href="#section2"><span class="dot">2</span></a></li>
    <li data-menuanchor="section3" class=""><a href="#section3"><span class="dot">3</span></a></li>
    <li data-menuanchor="section4" class=""><a href="#section4"><span class="dot">4</span></a></li>
  </ol>
  <!--��ҳ end-->
</div>

<script type="text/javascript">

$(document).ready(function(b) {


    //�ж�ua  ��ʾ�·��ļ�ͷ��ť
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


    //��2���Զ������л�
    var $ele = $('#hl_mt');
    var $hl_li = $('#hl_mt li');
    var init =  0 ;

    //�Զ�ִ��
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


    //��3���Զ������л�
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

    //�Զ�ִ��
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

    //��������
    $('#logo').click(function() {
        b.fn.fullpage.moveTo(1);
    });

    $('.next_btn').click(function() {
        b.fn.fullpage.moveTo(2);
    });

    //΢�Ź��ںŵ����� 
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