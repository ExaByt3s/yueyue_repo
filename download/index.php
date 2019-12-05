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
    <title>约约--最高效的时间电商平台</title>
    <meta name="keywords" content="约约，模特，摄影师，约拍，私拍，外拍，摄影，交易" />
    <meta name="Description" content="约约是最美的O2O产品，在这里，每个美女都利用自己的零碎时间，用美丽来变现。信用机制让交易更安心，私聊机制确保摄影师与模特真实沟通，多维度搜索帮摄影师到自己想拍的模特，支持一人约多人拍从此创作不再孤单。" />
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
            //alert('微信用户，请点击页面右上角“...”—>“在浏览器中打开”，进行安装，谢谢~');
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
                        <p class="i-versions">iPhone版</p>
                        <p class="versions">v3.2.0</p>
                    </div>
                </a>
                
            </div>
           <div class="android-con clearfix">
            <a href="http://app.yueus.com/download_android.php" class="ui-btn android-btn" onclick="javascript:return download_android_apk();">
                <i class="icon"></i>
                <div class="txt">
                    <p class="i-versions">Android版</p>
                    <p class="versions">v3.2.0</p>
                </div>
            </a>
            <p class="tips"><a href="http://app.yueus.com/download_android_v2.php">Android版备用下载</a></p>
           </div>
        </div>
    </div>
    <div class="category-item">
        <h3 class="title">约约8大品类</h3>
        <div class="list-item clearfix">
            <ul>
                <li>
                    <div class="img-con"><img src="images/test/1-1.jpg"></div>
                    <div class="txt-con">
                        <h4 class="title">找模特</h4>
                        <p class="txt">约约在手，正妹我有</p>
                    </div>
                </li>
                <li>
                    <div class="img-con"><img src="images/test/1-2.png"></div>
                    <div class="txt-con">
                        <h4 class="title">找影棚</h4>
                        <p class="txt">约约在手，场地不愁</p>
                    </div>
                </li>
                <li>
                    <div class="img-con"><img src="images/test/1-3.png"></div>
                    <div class="txt-con">
                        <h4 class="title">找化妆</h4>
                        <p class="txt">约约在手，美丽永久</p>
                    </div>
                </li>
                <li>
                    <div class="img-con"><img src="images/test/1-4.png"></div>
                    <div class="txt-con">
                        <h4 class="title">找活动</h4>
                        <p class="txt">约约在手，说走就走</p>
                    </div>
                </li>


                <li>
                    <div class="img-con"><img src="images/test/1-5.png"></div>
                    <div class="txt-con">
                        <h4 class="title">找培训</h4>
                        <p class="txt">约约在手，学习无忧</p>
                    </div>
                </li>
                <li>
                    <div class="img-con"><img src="images/test/1-6.png"></div>
                    <div class="txt-con">
                        <h4 class="title">找摄影师</h4>
                        <p class="txt">约约在手，专属自由</p>
                    </div>
                </li>

                
                <li>
                    <div class="img-con"><img src="images/test/icon-meishi-50x50.jpg"></div>
                    <div class="txt-con">
                        <h4 class="title">找美食</h4>
                        <p class="txt">约约在手，滋味挚友</p>
                    </div>
                </li>
                <li>
                    <div class="img-con"><img src="images/test/icon-lequ-50x50.jpg"></div>
                    <div class="txt-con">
                        <h4 class="title">找乐趣</h4>
                        <p class="txt">约约在手，达人我有</p>
                    </div>
                </li>


            </ul>
        </div>
    </div>
    <div class="service-item">
        <h3 class="title">最高效的时间电商</h3>
        <div class="list-item clearfix">
            <ul>
                <li>
                    <div class="img-con"><img src="images/test/2-1.png"></div>
                        <div class="txt-con">
                            <h4 class="title">商家双重认证</h4>
                            
                        </div>
                </li>
                <li>
                    <div class="img-con"><img src="images/test/2-2.png"></div>
                        <div class="txt-con">
                            <h4 class="title">首创二维码签到</h4>
    
                        </div>
                </li>
                <li>
                    <div class="img-con"><img src="images/test/2-3.png"></div>
                        <div class="txt-con">
                            <h4 class="title">支付担保系统</h4>
                            
                        </div>
                </li>
                <li>
                    <div class="img-con"><img src="images/test/2-4.jpg"></div>
                        <div class="txt-con">
                            <h4 class="title">双向评价机制</h4>
                            
                        </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="info-item">
        <p>公司介绍：广州摩幻时信息技术有限公司，创办于2014年8月，已获得千万天使轮资金和3000多万Pre A轮融资。</p>
        <p>产品介绍：项目“约约”以打造时间电商为目标，帮助每一个人经营自己的业余时间，让兴趣和时间成为可变现的资源，为第三产业实现更个性化的供需对接。第一阶段，“约约”致力于打造摄影服务和模特邀约第一平台。未来，约约将扩张至技能、娱乐、商业服务等更多品类，让每个人都能找到价值变现的机会，同时满足用户个性化的需求。</p>
        <p>客服电话：4000-82-9003</p>
        <p>Copyright 2015 &copy Yueus.com</p>
    </div>
</div>
<script type="text/javascript">
    // 临时处理IOS9
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