<?php
/**
 *
 */

/**
 * 引入文件
 */
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/m/poco_pai_common.inc.php');

/**
 * 初始化
 */
$page = array(
    'mode' => G_PAI_APP_PAGE_MODE, // 页面模式, 开发(dev) or 测试(beta) or 线上(archive)
    'scene' => G_PAI_APP_PAGE_SCENE, // 页面环境, web浏览器(web) or APP应用(app)
    'time' => $_SERVER['REQUEST_TIME'],
    'domain' => G_PAI_APP_DOMAIN,// 域名
);

/**
 * 载入模板
 */
if (G_PAI_APP_PAGE_SCENE === 'app') 
{
    $main_page_filename = 'page_app.tpl.html';
} else 
{
    $main_page_filename = 'page.tpl.html';
}

$tpl = get_view('layouts/'.$main_page_filename);

/**
 * 数据处理
 */
// 版本控制
// ------------------------------
$version = include('config/version_control.conf.php');
$version = $version[$page['mode']];
$tpl->assign('version', $version);


// 加载头部模块
// ------------------------------
$head_mod_tpl = get_view('modules/head.tpl.html');
$head_mod_tpl->assign('page', $page);
$head_mod_tpl->assign('version', $version);
$tpl->assign('head_mod_html', $head_mod_tpl->result());


// 加载脚本模块
// ------------------------------
$script_mod_tpl = get_view('modules/script.tpl.html');
$script_mod_tpl->assign('login_id', (int)$yue_login_id);
$script_mod_tpl->assign('page', $page);
$script_mod_tpl->assign('version', $version);
$script_mod_tpl->assign('server_time', $_SERVER['REQUEST_TIME'] * 1000);
$script_mod_tpl->assign('jstracker_code', '(function(a){function e(){var a,c;return(c=(b.cookie+"").match(RegExp("(?:^| )member_id(?:(?:=([^;]*))|;|$)")))&&c[1]&&(a=c[1]),a}function f(b){d>c&&(a.onerror=null);var e=a.g_jsTracker.sendLog[d++]=new Image;return e.src=b}function g(c,d,f){c=c||"",d=d||"",f=f||"";var g="http://imgtj.poco.cn/pocotj_touch.css?touch=1&ip_addr=&tmp="+(new Date).getTime(),h=a.screen,i=["msg="+c,"file="+d,"line="+f,"scrolltop="+(b.body||b.documentElement).scrollTop,"screen="+(h.width+"x"+h.height),"islogin="+(e()?1:0)];return g+="&url=touch://Errorlistener/js/err_log"+encodeURIComponent("?"+i.join("&"))}var b=a.document,c=10,d=0;a.g_jsTracker={sendLog:{}},a.onerror=function(a,b,c){f(g.call(this,a,b,c))}})(window);');
$tpl->assign('script_mod_html', $script_mod_tpl->result());

$tpl->assign('app_bridge_script', '!function(){function e(e){d=e.createElement("iframe"),d.style.display="none",e.documentElement.appendChild(d)}function a(e){if(PocoWebViewJavascriptBridge._messageHandler)throw Error("WebViewJavascriptBridge.init called twice");PocoWebViewJavascriptBridge._messageHandler=e;var a=v;v=null;for(var i=0;i<a.length;i++)c(a[i])}function i(e,a){t({data:e},a)}function n(e,a){u[e]=a}function r(e,a,i){t({handlerName:e,data:a},i)}function t(e,a){if(a){var i="cb_"+g++ +"_"+(new Date).getTime();w[i]=a,e.callbackId=i}l.push(e),d.src=f+"://"+p}function o(){var e=JSON.stringify(l);return l=[],/android/gi.test(window.navigator.userAgent.toLowerCase())?void window.wst.fetchQueue(e):e}function c(e){setTimeout(function(){var a=JSON.parse(e);if(a.responseId){var i=w[a.responseId];if(!i)return;i(a.responseData),delete w[a.responseId]}else{var i;if(a.callbackId){var n=a.callbackId;i=function(e){t({responseId:n,responseData:e})}}var r=PocoWebViewJavascriptBridge._messageHandler;a.handlerName&&(r=u[a.handlerName]);try{r(a.data,i)}catch(o){"undefined"!=typeof console&&console.log("WebViewJavascriptBridge: WARNING: javascript handler threw.",a,o)}}})}function s(e){v?v.push(e):c(e)}if(!window.PocoWebViewJavascriptBridge){var d,l=[],v=[],u={},f="wvjbscheme",p="__WVJB_QUEUE_MESSAGE__",w={},g=1;window.PocoWebViewJavascriptBridge={init:a,send:i,registerHandler:n,callHandler:r,_fetchQueue:o,_handleMessageFromObjC:s};var b=document;e(b);var h=b.createEvent("Events");h.initEvent("WebViewJavascriptBridgeReady"),h.bridge=PocoWebViewJavascriptBridge,b.dispatchEvent(h)}}();'); 


/**
 * 输出
 */
$result_html = $tpl->result();
if (G_PAI_APP_PAGE_MODE !== 'dev') 
{
    $result_html = str_replace("\t", '', $result_html);
    $result_html = str_replace("\r\n", '', $result_html);
    $result_html = str_replace("\n", '', $result_html);
    $result_html = str_replace("\r", '', $result_html);
    $result_html = str_replace("    ", '', $result_html);
}

echo $result_html;
?>