<!DOCTYPE html>
<html lang="zh">
	<head>
		<title>登录跳转中...</title>
		<meta charset="gbk">
		<meta name="HandheldFriendly" content="true"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.3, user-scalable=0"/>
		<meta name="apple-mobile-web-app-capable" content="yes"/>
		<meta name="format-detection" content="telephone=no"/>
		<?php
		$r_url = trim($_GET['r_url']);
		echo "<script >window.r_url = '{$r_url}';</script>";
		?>
		<script type="text/javascript" src="http://static.yueus.com/yue_ui/js/jquery.js"></script>
		<!--基础样式 START-->
		<link type="text/css" rel="stylesheet" href="http://static-c.yueus.com/mall/user/static/wap/style/libs/common_18ce95a.css">
		<!-- 通用 -->
		<!-- 颜色配置 -->
		<!-- 按钮配置 -->
		<!--基础样式 END-->
		<!--App bridge-->
		<script>
		!function(){function e(e){d=e.createElement("iframe"),d.style.display="none",e.documentElement.appendChild(d)}function a(e){if(PocoWebViewJavascriptBridge._messageHandler)throw Error("WebViewJavascriptBridge.init called twice");PocoWebViewJavascriptBridge._messageHandler=e;var a=v;v=null;for(var i=0;i<a.length;i++)c(a[i])}function i(e,a){t({data:e},a)}function n(e,a){u[e]=a}function r(e,a,i){t({handlerName:e,data:a},i)}function t(e,a){if(a){var i="cb_"+g++ +"_"+(new Date).getTime();w[i]=a,e.callbackId=i}l.push(e),d.src=f+"://"+p}function o(){var e=JSON.stringify(l);return l=[],/android/gi.test(window.navigator.userAgent.toLowerCase())?void window.wst.fetchQueue(e):e}function c(e){setTimeout(function(){var a=JSON.parse(e);if(a.responseId){var i=w[a.responseId];if(!i)return;i(a.responseData),delete w[a.responseId]}else{var i;if(a.callbackId){var n=a.callbackId;i=function(e){t({responseId:n,responseData:e})}}var r=PocoWebViewJavascriptBridge._messageHandler;a.handlerName&&(r=u[a.handlerName]);try{r(a.data,i)}catch(o){"undefined"!=typeof console&&console.log("WebViewJavascriptBridge: WARNING: javascript handler threw.",a,o)}}})}function s(e){v?v.push(e):c(e)}if(!window.PocoWebViewJavascriptBridge){var d,l=[],v=[],u={},f="wvjbscheme",p="__WVJB_QUEUE_MESSAGE__",w={},g=1;window.PocoWebViewJavascriptBridge={init:a,send:i,registerHandler:n,callHandler:r,_fetchQueue:o,_handleMessageFromObjC:s};var b=document;e(b);var h=b.createEvent("Events");h.initEvent("WebViewJavascriptBridgeReady"),h.bridge=PocoWebViewJavascriptBridge,b.dispatchEvent(h)}}();
			</script>
			<body class="">
				<main role="main">
				<div class="page-view notice-page fn-hide" data-role="page-container" id="no-login">
						<div data-role="abnormal-container"><div style="padding-top: 150px;">
							<div class="stream-abnormal stream-empty" data-role="tap-screen">
								<i class="icon icon-stream-empty"></i>
								<h4>													
								若取消登录，请点击返回按钮													
								</h4>							
							</div>
						</div>
					</div>
					<div class="btn-wrapper" style="padding: 0 15px;">
						<button class="ui-button  ui-button-block ui-button-100per ui-button-size-l ui-button-bg-btn-555" id="btn-back">
						<span class="ui-button-content">返回</span>
						</button>
					</div>
				</div>
				<div class="page-view notice-page fn-hide" data-role="page-container" id="has-login">
						<div data-role="abnormal-container"><div style="padding-top: 150px;">
							<div class="stream-abnormal stream-empty" data-role="tap-screen">
								<i class="icon icon-stream-empty"></i>
								<h4>													
								你已经成功登录了，请点击返回按钮													
								</h4>							
							</div>
						</div>
					</div>
					<div class="btn-wrapper" style="padding: 0 15px;">
						<button class="ui-button  ui-button-block ui-button-100per ui-button-size-l ui-button-bg-btn-555" id="btn-back">
						<span class="ui-button-content">返回</span>
						</button>
					</div>
				</div>
			</main>
		</body>
		<script>
		$(function()
		{
			(function()
		{
				
				/**
				* 初始化app的通讯通道
				* @param callback
				*/
				function init_world_app_bridge(callback)
				{
					if (window.PocoWebViewJavascriptBridge)
					{
						PocoWebViewJavascriptBridge.init();
						if(typeof callback == 'function')
						{
						
							callback.call(this);
						}
					}
					else
					{
						document.addEventListener('WebViewJavascriptBridgeReady', function()
						{
							PocoWebViewJavascriptBridge.init();
							if(typeof callback == 'function')
							{
								callback.call(this);
							}
						}, false)
					}
					
					
				}
				function back()
				{
					var params =
							{
								
							};
						PocoWebViewJavascriptBridge.callHandler('poco.yuepai.function.close',params,function(data)
						{
							//console.log("code is :" +data.code);
							//-- code is 0000  --//
						});
				}
				function showtopbar()
				{
					var params =
							{
								type : 1
							};
						PocoWebViewJavascriptBridge.callHandler('poco.yuepai.function.showtopbar',params,function(data)
						{
							//console.log("code is :" +data.code);
							//-- code is 0000  --//
						});
				}
				function show_login()
				{
					var params =
							{
								
							};
						PocoWebViewJavascriptBridge.callHandler('poco.yuepai.function.openloginpage',params,function(data)
						{
							
							if(data.code == '0000')
							{
								var loc = window.location;
								var url = JSON.stringify(window.__YUE_APP_USER_INFO__);
								window.r_url = window.r_url.replace(/login_info=%7B.*%7D/,"login_info="+encodeURIComponent(url))
								
								loc.href = window.r_url;
							}
							else if(data.code == '1001')
							{
								setTimeout(function()
								{
									$('#no-login').removeClass('fn-hide');
									$('#has-login').addClass('fn-hide');
								},0)
							}
							
						});
				}
				init_world_app_bridge(function()
				{
					showtopbar();

					setTimeout(function()
					{
						if(window.__YUE_APP_USER_INFO__)
						{
							if(!window.__YUE_APP_USER_INFO__.token || !parseInt(window.__YUE_APP_USER_INFO__.token))
							{
								show_login();
							}
							else
							{
								var url = JSON.stringify(window.__YUE_APP_USER_INFO__);

								window.r_url = window.r_url+"&login_info="+encodeURIComponent(url);

								window.location.href = window.r_url;
							}
						}

					},300);
					

					$('#btn-back').on('click',function()
					{
						back();
					});
					
				});
				
		})();
		})
		</script>
	</html>