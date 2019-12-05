<?php

/** 
 * footer
 * 汤圆
 * 2015-6-5
 * 引用资源文件定位，注意！确保引用路径争取
 */


include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

define('MALL_USER_DIR_APP',"../../../../");
include_once MALL_USER_DIR_APP.'common.inc.php';
function _get_wbc_footer($attribs)
{	

	$file_dir = dirname(__FILE__);
	
    global $my_app_pai;
    global $yue_login_id;

	if(preg_match('/yueus.com/',$_SERVER['HTTP_HOST']))
	{
		$tpl	 = $my_app_pai->getView($file_dir . "/footer.tpl.html",true);
	}
	else
	{
		$tpl = new SmartTemplate($file_dir . "/footer.tpl.html");
	}
	
	$tpl->assign('app_bridge_script', '!function(){function e(e){d=e.createElement("iframe"),d.style.display="none",e.documentElement.appendChild(d)}function a(e){if(PocoWebViewJavascriptBridge._messageHandler)throw Error("WebViewJavascriptBridge.init called twice");PocoWebViewJavascriptBridge._messageHandler=e;var a=v;v=null;for(var i=0;i<a.length;i++)c(a[i])}function i(e,a){t({data:e},a)}function n(e,a){u[e]=a}function r(e,a,i){t({handlerName:e,data:a},i)}function t(e,a){if(a){var i="cb_"+g++ +"_"+(new Date).getTime();w[i]=a,e.callbackId=i}l.push(e),d.src=f+"://"+p}function o(){var e=JSON.stringify(l);return l=[],/android/gi.test(window.navigator.userAgent.toLowerCase())?void window.wst.fetchQueue(e):e}function c(e){setTimeout(function(){var a=JSON.parse(e);if(a.responseId){var i=w[a.responseId];if(!i)return;i(a.responseData),delete w[a.responseId]}else{var i;if(a.callbackId){var n=a.callbackId;i=function(e){t({responseId:n,responseData:e})}}var r=PocoWebViewJavascriptBridge._messageHandler;a.handlerName&&(r=u[a.handlerName]);try{r(a.data,i)}catch(o){"undefined"!=typeof console&&console.log("WebViewJavascriptBridge: WARNING: javascript handler threw.",a,o)}}})}function s(e){v?v.push(e):c(e)}if(!window.PocoWebViewJavascriptBridge){var d,l=[],v=[],u={},f="wvjbscheme",p="__WVJB_QUEUE_MESSAGE__",w={},g=1;window.PocoWebViewJavascriptBridge={init:a,send:i,registerHandler:n,callHandler:r,_fetchQueue:o,_handleMessageFromObjC:s};var b=document;e(b);var h=b.createEvent("Events");h.initEvent("WebViewJavascriptBridgeReady"),h.bridge=PocoWebViewJavascriptBridge,b.dispatchEvent(h)}}();'); 

	$tpl_html = $tpl->result();

	return $tpl_html;
}


?>