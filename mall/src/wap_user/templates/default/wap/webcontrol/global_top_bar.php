<?php

/** 
  * 头部 bar
  * 汤圆
  * 2015-6-5
  * 引用资源文件定位，注意！确保引用路径争取
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

function _get_wbc_global_top_bar($attribs)
{   

    $file_dir = dirname(__FILE__);

    global $my_app_pai;
    global $yue_login_id;
    global $__yue_user_info;

    $user_id = $yue_login_id;

    //  用户信息
    $mall_obj = POCO::singleton('pai_mall_seller_class');
    $seller_info=$mall_obj->get_seller_info($user_id,2);
    $seller_name=$seller_info['seller_data']['name'];

    if(preg_match('/yueus.com/',$_SERVER['HTTP_HOST']))
    {
        // 空为消费者
        if (empty($seller_name)) 
        {
            $tpl     = $my_app_pai->getView($file_dir . "/global-top-bar-consumers.tpl.htm",true);
        }
        else
        {
            $tpl     = $my_app_pai->getView($file_dir . "/global-top-bar-seller.tpl.htm",true);
        }

    }
    else
    {
        // 空为消费者
        if (empty($seller_name)) 
        {
            $tpl     = $my_app_pai->getView($file_dir . "/global-top-bar-consumers.tpl.htm",true);
        }
        else
        {
            $tpl     = $my_app_pai->getView($file_dir . "/global-top-bar-seller.tpl.htm",true);
        }

    }   

    // 如果未登录，显示登录注册
    if (empty($yue_login_id)) 
    {
        $tpl->assign('no_login', 1);

        // 回链
        $r_url_prev = $_SERVER['SERVER_NAME'];
        $r_url_next = $_SERVER['REQUEST_URI'];
        $r_url = "http://".$r_url_prev.$r_url_next;
        $tpl->assign('r_url', urlencode($r_url));
        // print_r($r_url);

    }

    // 账号余额
    $pai_payment_obj = POCO::singleton('pai_payment_class');
    $user_available_balance = $pai_payment_obj->get_user_available_balance($user_id);
    $tpl->assign('user_available_balance', $user_available_balance);

	$tpl->assign('app_bridge_script', '!function(){function e(e){d=e.createElement("iframe"),d.style.display="none",e.documentElement.appendChild(d)}function a(e){if(PocoWebViewJavascriptBridge._messageHandler)throw Error("WebViewJavascriptBridge.init called twice");PocoWebViewJavascriptBridge._messageHandler=e;var a=v;v=null;for(var i=0;i<a.length;i++)c(a[i])}function i(e,a){t({data:e},a)}function n(e,a){u[e]=a}function r(e,a,i){t({handlerName:e,data:a},i)}function t(e,a){if(a){var i="cb_"+g++ +"_"+(new Date).getTime();w[i]=a,e.callbackId=i}l.push(e),d.src=f+"://"+p}function o(){var e=JSON.stringify(l);return l=[],/android/gi.test(window.navigator.userAgent.toLowerCase())?void window.wst.fetchQueue(e):e}function c(e){setTimeout(function(){var a=JSON.parse(e);if(a.responseId){var i=w[a.responseId];if(!i)return;i(a.responseData),delete w[a.responseId]}else{var i;if(a.callbackId){var n=a.callbackId;i=function(e){t({responseId:n,responseData:e})}}var r=PocoWebViewJavascriptBridge._messageHandler;a.handlerName&&(r=u[a.handlerName]);try{r(a.data,i)}catch(o){"undefined"!=typeof console&&console.log("WebViewJavascriptBridge: WARNING: javascript handler threw.",a,o)}}})}function s(e){v?v.push(e):c(e)}if(!window.PocoWebViewJavascriptBridge){var d,l=[],v=[],u={},f="wvjbscheme",p="__WVJB_QUEUE_MESSAGE__",w={},g=1;window.PocoWebViewJavascriptBridge={init:a,send:i,registerHandler:n,callHandler:r,_fetchQueue:o,_handleMessageFromObjC:s};var b=document;e(b);var h=b.createEvent("Events");h.initEvent("WebViewJavascriptBridgeReady"),h.bridge=PocoWebViewJavascriptBridge,b.dispatchEvent(h)}}();'); 

    // 空为消费者
    // if (empty($seller_name)) 
    // {
    //     $user_name = $__yue_user_info['nickname'];
    // }
    // else
    // {
    //     $user_name = $seller_name ;
    // }
    // 
    $user_name = $__yue_user_info['nickname'];

    //  显示用户名
    $tpl->assign('user_name', $user_name);

    //  项目路径
    $tpl->assign('project_root', TASK_PROJECT_ROOT);    
    
    $tpl_html = $tpl->result();

    return $tpl_html;
}


?>