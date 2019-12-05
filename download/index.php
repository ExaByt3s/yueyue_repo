<?php
include_once "../poco_app_common.inc.php";

$ios_app_version    = 'v3.2.0';
$ios_main_url       = 'http://app.yueus.com/download_iphone_plist.php';
$ios_backup_url     = 'http://app.yueus.com/download_iphone_plist_v2.php';

$android_app_version    = 'v3.2.0';
$android_main_url       = 'http://app.yueus.com/download_android.php';
$android_backup_url     = 'http://app.yueus.com/download_android_v2.php';



$version = $_GET['ver'];
if($version == '3.0.0')
{
    $ios_app_version = 'v3.2.0';
    $ios_main_url    .= '?ver=3.2.0';
    $ios_backup_url  .= '?ver=3.2.0';
    
    $android_app_version = 'v3.2.0';
    $android_main_url    .= '?ver=3.2.0';
    $android_backup_url  .= '?ver=3.2.0';
}

$result = mall_get_user_agent_arr();
if($result['is_pc']==1)
{
	header("Location:http://www.yueus.com/download.php");
}


?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="gb2312" />
    <meta http-equiv = "X-UA-Compatible" content ="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>ԼԼ--���Ч��ʱ�����ƽ̨</title>
    <meta name="keywords" content="ԼԼ��ģ�أ���Ӱʦ��Լ�ģ�˽�ģ����ģ���Ӱ������" />
    <meta name="Description" content="ԼԼ��������O2O��Ʒ�������ÿ����Ů�������Լ�������ʱ�䣬�����������֡����û����ý��׸����ģ�˽�Ļ���ȷ����Ӱʦ��ģ����ʵ��ͨ����ά����������Ӱʦ���Լ����ĵ�ģ�أ�֧��һ��Լ�����ĴӴ˴������ٹµ���" />
    <link href="css/wap.css?v11" type="text/css" rel="stylesheet" />
    <meta content="telephone=no" name="format-detection" />
</head>

<body>
    <img src="http://img16.poco.cn/yueyue/customer_icon.png" style="display:none;" />
<script>
    document.addEventListener('touchstart',function(){},false);
    function isWeiXin(){
        var ua = window.navigator.userAgent.toLowerCase();
        if(ua.match(/MicroMessenger/i) == 'micromessenger'){
            return true;
        }else{
            return false;
        }
    }

    function download_android_apk(){
        if(isWeiXin()){
            window.location.href = "http://a.app.qq.com/o/simple.jsp?pkgname=com.yueus.Yue";
	    return false;	
        }else{
	    return true;
	}
    }

    function download_ios_apk(){
        if(isWeiXin()){
            //alert('΢���û�������ҳ�����Ͻǡ�...����>����������д򿪡������а�װ��лл~');
        }

        
    }
</script>
<div class="download-page">
    <div class="download-item">
        <div class="download-btn-con">
            <div class="iphone-con">
                <a  class="ui-btn iphone-btn" href="http://a.app.qq.com/o/simple.jsp?pkgname=com.yueus.Yue">
                    <i class="icon"></i>
                    <div class="txt">
                        <p class="i-versions">iPhone��</p>
                        <p class="versions">v3.2.0</p>
                    </div>
                </a>
                
            </div>
           <div class="android-con clearfix">
            <a href="http://app.yueus.com/download_android.php" class="ui-btn android-btn" onclick="javascript:return download_android_apk();">
                <i class="icon"></i>
                <div class="txt">
                    <p class="i-versions">Android��</p>
                    <p class="versions">v3.2.0</p>
                </div>
            </a>
            <p class="tips"><a href="http://app.yueus.com/download_android_v2.php">Android�汸������</a></p>
           </div>
        </div>
    </div>
    <div class="category-item">
        <h3 class="title">ԼԼ8��Ʒ��</h3>
        <div class="list-item clearfix">
            <ul>
                <li>
                    <div class="img-con"><img src="images/test/1-1.jpg"></div>
                    <div class="txt-con">
                        <h4 class="title">��ģ��</h4>
                        <p class="txt">ԼԼ���֣���������</p>
                    </div>
                </li>
                <li>
                    <div class="img-con"><img src="images/test/1-2.png"></div>
                    <div class="txt-con">
                        <h4 class="title">��Ӱ��</h4>
                        <p class="txt">ԼԼ���֣����ز���</p>
                    </div>
                </li>
                <li>
                    <div class="img-con"><img src="images/test/1-3.png"></div>
                    <div class="txt-con">
                        <h4 class="title">�һ�ױ</h4>
                        <p class="txt">ԼԼ���֣���������</p>
                    </div>
                </li>
                <li>
                    <div class="img-con"><img src="images/test/1-4.png"></div>
                    <div class="txt-con">
                        <h4 class="title">�һ</h4>
                        <p class="txt">ԼԼ���֣�˵�߾���</p>
                    </div>
                </li>


                <li>
                    <div class="img-con"><img src="images/test/1-5.png"></div>
                    <div class="txt-con">
                        <h4 class="title">����ѵ</h4>
                        <p class="txt">ԼԼ���֣�ѧϰ����</p>
                    </div>
                </li>
                <li>
                    <div class="img-con"><img src="images/test/1-6.png"></div>
                    <div class="txt-con">
                        <h4 class="title">����Ӱʦ</h4>
                        <p class="txt">ԼԼ���֣�ר������</p>
                    </div>
                </li>

                
                <li>
                    <div class="img-con"><img src="images/test/icon-meishi-50x50.jpg"></div>
                    <div class="txt-con">
                        <h4 class="title">����ʳ</h4>
                        <p class="txt">ԼԼ���֣���ζֿ��</p>
                    </div>
                </li>
                <li>
                    <div class="img-con"><img src="images/test/icon-lequ-50x50.jpg"></div>
                    <div class="txt-con">
                        <h4 class="title">����Ȥ</h4>
                        <p class="txt">ԼԼ���֣���������</p>
                    </div>
                </li>


            </ul>
        </div>
    </div>
    <div class="service-item">
        <h3 class="title">���Ч��ʱ�����</h3>
        <div class="list-item clearfix">
            <ul>
                <li>
                    <div class="img-con"><img src="images/test/2-1.png"></div>
                        <div class="txt-con">
                            <h4 class="title">�̼�˫����֤</h4>
                            
                        </div>
                </li>
                <li>
                    <div class="img-con"><img src="images/test/2-2.png"></div>
                        <div class="txt-con">
                            <h4 class="title">�״���ά��ǩ��</h4>
    
                        </div>
                </li>
                <li>
                    <div class="img-con"><img src="images/test/2-3.png"></div>
                        <div class="txt-con">
                            <h4 class="title">֧������ϵͳ</h4>
                            
                        </div>
                </li>
                <li>
                    <div class="img-con"><img src="images/test/2-4.jpg"></div>
                        <div class="txt-con">
                            <h4 class="title">˫�����ۻ���</h4>
                            
                        </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="info-item">
        <p>��˾���ܣ�����Ħ��ʱ��Ϣ�������޹�˾��������2014��8�£��ѻ��ǧ����ʹ���ʽ��3000����Pre A�����ʡ�</p>
        <p>��Ʒ���ܣ���Ŀ��ԼԼ���Դ���ʱ�����ΪĿ�꣬����ÿһ���˾�Ӫ�Լ���ҵ��ʱ�䣬����Ȥ��ʱ���Ϊ�ɱ��ֵ���Դ��Ϊ������ҵʵ�ָ����Ի��Ĺ���Խӡ���һ�׶Σ���ԼԼ�������ڴ�����Ӱ�����ģ����Լ��һƽ̨��δ����ԼԼ�����������ܡ����֡���ҵ����ȸ���Ʒ�࣬��ÿ���˶����ҵ���ֵ���ֵĻ��ᣬͬʱ�����û����Ի�������</p>
        <p>�ͷ��绰��4000-82-9003</p>
        <p>Copyright 2015 &copy Yueus.com</p>
    </div>
</div>
<script type="text/javascript">
    // ��ʱ����IOS9
    function get_ua(){var d={};var f=window;var g=f.navigator;var b=g.appVersion;d.isMobile=(/(iphone|ipod|android|ios|ipad|nokia|blackberry|tablet|symbian)/).test(g.userAgent.toLowerCase());d.isAndroid=(/android/gi).test(b);d.isIDevice=(/iphone|ipad/gi).test(b);d.isTouchPad=(/hp-tablet/gi).test(b);d.isIpad=(/ipad/gi).test(b);d.otherPhone=!(d.isAndroid||d.isIDevice);d.is_uc=(/uc/gi).test(b);d.is_chrome=(/CriOS/gi).test(b)||(/Chrome/gi).test(b);d.is_qq=(/QQBrowser/gi).test(b);d.is_real_safari=(/safari/gi).test(b)&&!d.is_chrome&&!d.is_qq;d.is_standalone=(window.navigator.standalone)?true:false;d.window_width=window.innerWidth;d.window_height=window.innerHeight;if(d.isAndroid){var c=parseFloat(b.slice(b.indexOf("Android")+8));d.android_version=c}else{if(d.isIDevice){var a=(b).match(/OS (\d+)_(\d+)_?(\d+)?/);var e=a[1];if(a[2]){e+="."+a[2]}if(a[3]){e+="."+a[3]}d.ios_version=e}}d.is_iphone_safari_no_fullscreen=d.isIDevice&&d.ios_version<"7"&&!d.isIpad&&d.is_real_safari&&!d.is_standalone;d.is_yue_app=(/yue_pai/).test(b);d.is_weixin=(/MicroMessenger/gi).test(b);d.is_normal_wap=!d.is_yue_app&&!d.is_weixin;return d};
</script>
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "//hm.baidu.com/hm.js?9fa08589aee02a0d763d50fe4dca33cd";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>

</body>
</html>