define("common/I_APP/I_APP",function(n){var o=n("components/zepto/zepto.js"),c=n("common/utility/index"),a=window.PocoWebViewJavascriptBridge,i="undefined"!=typeof a,l="yueyue://goto",t="poco.yuepai.";if(i){a.init();var e=o({});window.pocoAppEventTrigger=function(){return e.trigger.apply(e,arguments)}}var u={isPaiApp:i,on:function(){return u.isPaiApp?e.on.apply(e,arguments):void 0},off:function(){return u.isPaiApp?e.off.apply(e,arguments):void 0},once:function(){return u.isPaiApp?e.once.apply(e,arguments):void 0},chat:function(n){a.callHandler(t+"function.chat",n,function(){"function"==typeof n.callback&&n.callback.call(this)})},qrcodescan:function(n){var n=n||{},o=(null==n.is_nav_page?!0:!1,n.success||function(){});a.callHandler(t+"function.qrcodescan",n,function(n){if("0000"==n.code){var a=document.createElement("a");a.href=n.scanresult,window.location.origin||(window.location.origin=window.location.protocol+"//"+window.location.hostname+(window.location.port?":"+window.location.port:""));var i=a.href.replace(a.origin,window.location.origin);console.log(i),c.ajax_request({url:i,beforeSend:function(){m_alert.show("发送中","right")},success:function(n){var c=n.result_data;m_alert.show(c.msg,"right"),c.code>0&&o.call(this,c)},error:function(){m_alert.show("网络异常","error")}})}})},check_login:function(n){a.callHandler(t+"info.login",{},function(o,c){n.call(this,o,c)})},login:function(n){var n=n||{};a.callHandler(t+"function.login",{pocoid:n.pocoid,username:n.username,icon:n.icon,token:n.token,token_expirein:n.token_expirein},function(){})},logout:function(){console.log("APP logout"),a.callHandler(t+"function.logout",{},function(){})},save_device:function(n,i){var n=n||!1;a.callHandler(t+"info.device",{},function(a){console.log(a),a=o.extend(a,{user_id:c.login_id}),n?"function"==typeof i&&i.call(this,a):c.ajax_request({url:global_config.ajax_url.a_img,data:a})})},get_chat_info:function(n){a.callHandler(t+"info.chat",{},function(o){"function"==typeof n&&n.call(this,o)})},get_login_info:function(n){a.callHandler(t+"info.login",{},function(o){"function"==typeof n&&n.call(this,o)})},alipay:function(n,o){return n?(console.log(n),void a.callHandler(t+"function.alipay",n,function(n){console.log("======支付宝支付回调参数======"),console.log(n),"function"==typeof o&&o.call(this,n)})):void alert("no params")},wxpay:function(n,o){return n?(console.log(n),void a.callHandler(t+"function.wxpay",n,function(n){console.log("======微信支付回调参数======"),console.log(n),"function"==typeof o&&o.call(this,n)})):void alert("no params")},upload_img:function(n,c,i){console.log("upload img");var l="",e=c.photosize||640,u="wifi"==window.APP_NET_STATUS?"-wifi":"";console.log("net status:"+window.APP_NET_STATUS);var r="http://sendmedia"+u+".yueus.com:8078/",f="http://sendmedia"+u+".yueus.com:8079/";if(!c)return void alert("no params");if(!n)return void alert("no type");var s="",p="";switch(n){case"header_icon":l=r+"icon.cgi",s="modify_headicon",p="camera_album";break;case"single_img":l=f+"upload.cgi",p="camera_album";break;case"modify_cardcover":l=f+"upload.cgi",s="modify_cardcover",p="camera_album";break;case"multi_img":l=f+"upload.cgi",p="camera_album"}c=o.extend(c,{url:l,photosize:e,operation:s,src_opts:p}),console.log("-----upload img params-----"),console.log(c),a.callHandler(t+"function.uploadpic",c,function(n){i.call(this,n)})},show_chat_list:function(){a.callHandler(t+"function.openchatlist",{},function(){})},debug:function(n){var o=o||{};o.url=n.cache?global_config.romain:global_config.debug_romain,o.debug=n.debug?1:0,o.cache_onoff=n.cache?1:0,a.callHandler(t+"function.debug",o,function(){})},show_alumn_imgs:function(n){var n=n||{};a.callHandler(t+"function.show_album_imgs",n,function(){})},get_netstatus:function(n){a.callHandler(t+"info.netstatus",{},function(o){console.log("===========调用 App 获取网络状态==========="),console.log(o),"function"==typeof n&&n.call(this,o)})},sso_login:function(n,o){a.callHandler(t+"function.bind_account",n,function(n){"function"==typeof o&&o.call(this,n)})},call_phone:function(n,o){a.callHandler(t+"function.callphone",n,function(n){"function"==typeof o&&o.call(this,n)})},get_gps:function(n,o){a.callHandler(t+"function.getgps",n,function(n){"function"==typeof o&&o.call(this,n)})},set_setting:function(n,o){a.callHandler(t+"function.setting",n,function(n){"function"==typeof o&&o.call(this,n)})},get_setting:function(n,o){a.callHandler(t+"info.setting",n,function(n){console.log(n),"function"==typeof o&&o.call(this,n)})},clear_cache:function(){a.callHandler(t+"function.clearcache",{},function(){})},show_bottom_bar:function(n){console.log("function.showbottombar"),a.callHandler(t+"function.showbottombar",{show:n},function(){})},check_update:function(){a.callHandler(t+"function.checkupdate",{},function(){})},app_back:function(){console.log("调用 App Back Function"),window.AppCanPageBack=!0,a.callHandler(t+"function.back",{},function(){})},app_info:function(n){console.log("App app-info"),a.callHandler(t+"info.app",{},function(o){console.log(o),"function"==typeof n&&n.call(this,o)})},switchtopage:function(n,o){console.log("App switchtopage"),a.callHandler(t+"function.switchtopage",n,function(n){console.log(n),"function"==typeof o&&o.call(this,n)})},share_card:function(n,o){var c=n||{};console.log("share_card请求前参数："+c),a.callHandler(t+"function.sharecard",{url:c.url,pic:c.pic,content:c.content,title:c.title,sinacontent:c.sinacontent,userid:c.userid,qrcodeurl:c.qrcodeurl,jscbfunc:c.jscbfunc||function(){},sourceid:c.sourceid||0},function(n){console.log("share_card回调："+n),"function"==typeof o&&o.call(this,n)})},analysis:function(n,o,c){var o=o||{},n=n||"";switch(console.log("analysis 请求前参数:"+o),n){case"eventtongji":a.callHandler(t+"function.eventtongji",o,function(n){console.log("analysis 回调数据:"+n),"function"==typeof c&&c.call(this,n)});break;case"moduletongji":a.callHandler(t+"function.moduletongji",o,function(n){console.log("analysis 回调数据:"+n),"function"==typeof c&&c.call(this,n)})}},nav_to_app_page:function(n){var n=n||{},c=n.page_type||"";n.type=n.jump_type||"inner_app";var i=0;switch(c){case"model_card":var i=global_config.analysis_page.model_card.pid;break;case"cameraman_card":var i=global_config.analysis_page.cameraman_card.pid}if(n.pid=i,!i)return void alert("非法页面参数！无法跳转");delete n.page_type,params_str=o.param(n);var e=l+"?"+params_str;console.log("======== jump app protocol params ========"),console.log(params_str),a.callHandler(t+"function.openlink",{url:e},function(n){console.log("nav_to_app_page 回调数据:"+n),"function"==typeof callback&&callback.call(this,n)})},openloginpage:function(n){console.log("App openloginpage"),a.callHandler(t+"function.openloginpage",{},function(o){console.log(o),"function"==typeof n&&n.call(this,o)})}};return u});