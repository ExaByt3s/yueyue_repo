<?php
$type = $_GET['type']; 
if($type=='no_login')
{
	$text = "请先登录";
}
elseif($type=='hash_error')
{
	$text = "HASH验证错误";
}
elseif($type=='check_error')
{
	$text = "验证码无效或已被使用";
}
elseif($type=='not_publish_user')
{
	$text = "你不是活动发布人";
}
elseif($type=='act_end')
{
	$text = "活动已结束，验证失败";
}
elseif($type=='error_many_times')
{
	$text = "活动码输入错误过多，请稍后再试";
}
else
{
	$text = "活动码不正确或已过期";
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="gbk" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no, email=no">
<title>
	页面找不到
</title>

<script type="text/javascript">
    document.addEventListener('touchstart',function(){},false);
</script>

<script>!function(){function e(e){d=e.createElement("iframe"),d.style.display="none",e.documentElement.appendChild(d)}function a(e){if(PocoWebViewJavascriptBridge._messageHandler)throw Error("WebViewJavascriptBridge.init called twice");PocoWebViewJavascriptBridge._messageHandler=e;var a=v;v=null;for(var i=0;i<a.length;i++)c(a[i])}function i(e,a){t({data:e},a)}function n(e,a){u[e]=a}function r(e,a,i){t({handlerName:e,data:a},i)}function t(e,a){if(a){var i="cb_"+g++ +"_"+(new Date).getTime();w[i]=a,e.callbackId=i}l.push(e),d.src=f+"://"+p}function o(){var e=JSON.stringify(l);return l=[],/android/gi.test(window.navigator.userAgent.toLowerCase())?void window.wst.fetchQueue(e):e}function c(e){setTimeout(function(){var a=JSON.parse(e);if(a.responseId){var i=w[a.responseId];if(!i)return;i(a.responseData),delete w[a.responseId]}else{var i;if(a.callbackId){var n=a.callbackId;i=function(e){t({responseId:n,responseData:e})}}var r=PocoWebViewJavascriptBridge._messageHandler;a.handlerName&&(r=u[a.handlerName]);try{r(a.data,i)}catch(o){"undefined"!=typeof console&&console.log("WebViewJavascriptBridge: WARNING: javascript handler threw.",a,o)}}})}function s(e){v?v.push(e):c(e)}if(!window.PocoWebViewJavascriptBridge){var d,l=[],v=[],u={},f="wvjbscheme",p="__WVJB_QUEUE_MESSAGE__",w={},g=1;window.PocoWebViewJavascriptBridge={init:a,send:i,registerHandler:n,callHandler:r,_fetchQueue:o,_handleMessageFromObjC:s};var b=document;e(b);var h=b.createEvent("Events");h.initEvent("WebViewJavascriptBridgeReady"),h.bridge=PocoWebViewJavascriptBridge,b.dispatchEvent(h)}}();</script>


<style type="text/css">
	html{color:#000;background:#fff;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}body,div,span,applet,object,iframe,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,b,u,i,center,fieldset,form,label,legend,input,textarea,button,hr,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,embed,figure,figcaption,footer,header,hgroup,menu,nav,output,ruby,section,summary,time,mark,audio,video{margin:0;padding:0;border:0;font:inherit;font-size:100%;vertical-align:baseline}article,aside,details,figure,figcaption,footer,header,hgroup,menu,nav,section,summary{display:block}audio,canvas,video{display:inline-block;*display:inline;*zoom:1}audio:not([controls]){display:none;height:0}[;idden]{display:none}table{border-collapse:collapse;border-spacing:0}caption,th,td{text-align:left;font-weight:normal;vertical-align:middle}th{text-align:inherit}iframe{display:block}abbr,acronym{font-variant:normal}del{text-decoration:line-through}address,caption,cite,code,dfn,em,th,var{font-style:normal;font-weight:400}ol,ul{list-style:none}h1,h2,h3,h4,h5,h6{font-weight:400}q,blockquote{quotes:none}q:before,q:after,blockquote:before,blockquote:after{content:"";content:none}a img{border:none}sup{top:-0.5em}sub{bottom:-0.25em}sup,sub{font-size:75%;line-height:0;position:relative;vertical-align:baseline}a{text-decoration:none}a:hover,a:focus{text-decoration:underline}ins{text-decoration:none}code,kbd,pre,samp{font-family:monospace, serif;font-size:1em}body{-webkit-backface-visibility:hidden}body,button,input,select,textarea{font:400 14px/normal Helvetica Neue, Helvetica, arial, sans-serif}body{background-color:#fff;color:#000}a{color:#000}a:hover{color:black}.fn-clear{overflow:hidden}.fn-left{float:left}.fn-right{float:right}.fn-hide{display:none !important;visibility:hidden !important}.fn-invisible{visibility:hidden}
	html, body {
	width: 100%;
	height: 100%;
	overflow: hidden;
	word-wrap: break-word;
	}
	.err_msg{
		margin-bottom: 20px;
	}
	.btn{
		width: 100px;
	    height: 100px;
	    border-radius: 50%;
	    background-color: #ff4978;
	    line-height: 100px;
	    margin: 0 auto;
	    color: #fff;
	    font-size: 14px;
	}
	.btn:active{
		background-color: #ff7fa8;
	}
</style>
</head>
<body>


<table cellspacing="0" cellpadding="0" border="0" style="width:100%;height:100%;">
	<tr>
		<td style="text-align:center;vertical-align:middle;">			
			<div class="err_msg">
				<?php echo $text;?>
			</div>
			<div class="btn" id="back_btn">
				点击返回
			</div>	
		</td>
	</tr>

</table>

</body>
</html>
<script>
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

				PocoWebViewJavascriptBridge.callHandler('poco.yuepai.function.back',params,function(data)
				{

					//console.log("code is :" +data.code);
					//-- code is 0000  --//
				});
		}

		init_world_app_bridge(function()
		{
			var back_btn = document.getElementById('back_btn');

			PocoWebViewJavascriptBridge.callHandler('poco.yuepai.function.closeloading',{},function(data)
			{
			});
		 
			back_btn.onclick = function()
			{
				back();
			}
		});
		

  })();
</script>