define("common/I_APP/I_APP",function(n){var o=n("components/zepto/zepto.js"),c=n("common/utility/index"),l=n("common/ua/index"),t=(n("yue_ui/frozen"),window.PocoWebViewJavascriptBridge),i="undefined"!=typeof t;l.is_yue_app||(i=!1);var e="yueyue://goto",a="poco.yuepai.";if(i){t.init();var u=o({});window.pocoAppEventTrigger=function(){return u.trigger.apply(u,arguments)}}var s={isPaiApp:i,on:function(){return s.isPaiApp?u.on.apply(u,arguments):void 0},off:function(){return s.isPaiApp?u.off.apply(u,arguments):void 0},once:function(){return s.isPaiApp?u.once.apply(u,arguments):void 0},chat:function(n){t.callHandler(a+"function.chat",n,function(){"function"==typeof n.callback&&n.callback.call(this)})},qrcodescan:function(n){var c=this,n=n||{},l=(null==n.is_nav_page?!0:!1,n.success||function(){});t.callHandler(a+"function.qrcodescan",n,function(n){if("0000"==n.code){var t=n.scanresult;__Util_Tools.ajax_request_app({path:"customer/code_processing.php",data:{access_token:window.__YUE_APP_USER_INFO__.token,code_data:t},beforeSend:function(){c.$loading=o.loading({content:"加载中..."})},success:function(n){var t=n;c.$loading.loading("hide"),o.tips({content:t.data.msg,stayTime:3e3,type:"warn"}),200==t.code&&(l.call(this,t),t.data.url&&(window.location.href=t.data.url))},error:function(){c.$loading.loading("hide"),o.tips({content:"网络异常",stayTime:3e3,type:"warn"})}})}})},check_login:function(n){t.callHandler(a+"info.login",{},function(o,c){n.call(this,o,c)})},login:function(n){var n=n||{};t.callHandler(a+"function.login",{pocoid:n.pocoid,username:n.username,icon:n.icon,token:n.token,token_expirein:n.token_expirein},function(){})},logout:function(){console.log("APP logout"),t.callHandler(a+"function.logout",{},function(){})},save_device:function(n,l){var n=n||!1;t.callHandler(a+"info.device",{},function(t){console.log(t),t=o.extend(t,{user_id:c.login_id}),n?"function"==typeof l&&l.call(this,t):c.ajax_request({url:global_config.ajax_url.a_img,data:t})})},get_chat_info:function(n){t.callHandler(a+"info.chat",{},function(o){"function"==typeof n&&n.call(this,o)})},get_login_info:function(n){t.callHandler(a+"info.login",{},function(o){"function"==typeof n&&n.call(this,o)})},alipay:function(n,o){return n?(console.log(n),void t.callHandler(a+"function.alipay",n,function(n){console.log("======支付宝支付回调参数======"),console.log(n),"function"==typeof o&&o.call(this,n)})):void alert("no params")},wxpay:function(n,o){return n?(console.log(n),void t.callHandler(a+"function.wxpay",n,function(n){console.log("======微信支付回调参数======"),console.log(n),"function"==typeof o&&o.call(this,n)})):void alert("no params")},upload_img:function(n,c,l){console.log("upload img");var i="",e=c.photosize||640,u="wifi"==window.APP_NET_STATUS?"-wifi":"";console.log("net status:"+window.APP_NET_STATUS);var s="http://sendmedia-w"+u+".yueus.com:8078/",f="http://sendmedia-w"+u+".yueus.com:8079/";if(!c)return void alert("no params");if(!n)return void alert("no type");var r="",p="";switch(n){case"header_icon":i=s+"icon.cgi",r="modify_headicon",p="camera_album";break;case"single_img":i=f+"upload.cgi",p="camera_album";break;case"modify_cardcover":i=f+"upload.cgi",r="modify_cardcover",p="camera_album";break;case"multi_img":i=f+"upload.cgi",p="camera_album"}c=o.extend(c,{url:i,photosize:e,operation:r,src_opts:p}),console.log("-----upload img params-----"),console.log(c),t.callHandler(a+"function.uploadpic",c,function(n){l.call(this,n)})},show_chat_list:function(){t.callHandler(a+"function.openchatlist",{},function(){})},debug:function(n){var o=o||{};o.url=n.cache?global_config.romain:global_config.debug_romain,o.debug=n.debug?1:0,o.cache_onoff=n.cache?1:0,t.callHandler(a+"function.debug",o,function(){})},show_alumn_imgs:function(n){var n=n||{};t.callHandler(a+"function.show_album_imgs",n,function(){})},get_netstatus:function(n){t.callHandler(a+"info.netstatus",{},function(o){console.log("===========调用 App 获取网络状态==========="),console.log(o),"function"==typeof n&&n.call(this,o)})},sso_login:function(n,o){t.callHandler(a+"function.bind_account",n,function(n){"function"==typeof o&&o.call(this,n)})},call_phone:function(n,o){t.callHandler(a+"function.callphone",n,function(n){"function"==typeof o&&o.call(this,n)})},get_gps:function(n,o){t.callHandler(a+"function.getgps",n,function(n){"function"==typeof o&&o.call(this,n)})},set_setting:function(n,o){t.callHandler(a+"function.setting",n,function(n){"function"==typeof o&&o.call(this,n)})},get_setting:function(n,o){t.callHandler(a+"info.setting",n,function(n){console.log(n),"function"==typeof o&&o.call(this,n)})},clear_cache:function(){t.callHandler(a+"function.clearcache",{},function(){})},show_bottom_bar:function(n){console.log("function.showbottombar"),t.callHandler(a+"function.showbottombar",{show:n},function(){})},check_update:function(){t.callHandler(a+"function.checkupdate",{},function(){})},app_back:function(){console.log("调用 App Back Function"),window.AppCanPageBack=!0,t.callHandler(a+"function.back",{},function(){})},app_info:function(n){console.log("App app-info"),t.callHandler(a+"info.app",{},function(o){console.log(o),"function"==typeof n&&n.call(this,o)})},switchtopage:function(n,o){console.log("App switchtopage"),t.callHandler(a+"function.switchtopage",n,function(n){console.log(n),"function"==typeof o&&o.call(this,n)})},share_card:function(n,o){var c=n||{};console.log("share_card请求前参数："+c),t.callHandler(a+"function.sharecard",{url:c.url,pic:c.pic||c.img,content:c.content,title:c.title,sinacontent:c.sinacontent,userid:c.userid,qrcodeurl:c.qrcodeurl,jscbfunc:c.jscbfunc||function(){},sourceid:c.sourceid||0},function(n){console.log("share_card回调："+n),"function"==typeof o&&o.call(this,n)})},analysis:function(n,o,c){var o=o||{},n=n||"";switch(console.log("analysis 请求前参数:"+JSON.stringify(o)),n){case"eventtongji":t.callHandler(a+"function.eventtongji",o,function(n){console.log("analysis 回调数据:"+n),"function"==typeof c&&c.call(this,n)});break;case"moduletongji":t.callHandler(a+"function.moduletongji",o,function(n){console.log("analysis 回调数据:"+n),"function"==typeof c&&c.call(this,n)})}},nav_to_app_page:function(n){var n=n||{},l=n.page_type||"";n.type=n.jump_type||"inner_app";var i=0;switch(l){case"comment":n.goods_id=n.goods_id||"",n.order_sn=n.order_sn||"";var i="1220121";break;case"seller_user":n.seller_user_id=n.seller_user_id||"",n.user_id=c.login_id||"";var i="1220103";break;case"goods":n.goods_id=n.goods_id||"",n.user_id=c.login_id||"";var i="1220102"}if(n.pid=i,!i)return void alert("非法页面参数！无法跳转");delete n.page_type,params_str=o.param(n);var u=e+"?"+params_str;console.log("======== jump app protocol params ========"),console.log(params_str),t.callHandler(a+"function.openlink",{url:u},function(n){console.log("nav_to_app_page 回调数据:"+n),"function"==typeof callback&&callback.call(this,n)})},openloginpage:function(n){console.log("App openloginpage"),t.callHandler(a+"function.openloginpage",{},function(o){console.log(o),"function"==typeof n&&n.call(this,o)})},openttpayfinish:function(n){console.log("App ttpayfinish"),t.callHandler(a+"function.ttpayfinish",{},function(o){console.log(o),"function"==typeof n&&n.call(this,o)})},close_webview:function(n){console.log("App close_webview"),t.callHandler(a+"function.close",{},function(o){console.log(o),"function"==typeof n&&n.call(this,o)})},closeloading:function(n,o){console.log("App closeloading"),t.callHandler(a+"function.closeloading",{},function(n){console.log(n),"function"==typeof o&&o.call(this,n)})},show_selectcity:function(n,o){console.log("App selectcity"),t.callHandler(a+"function.selectcity",{},function(n){console.log(n),"function"==typeof o&&o.call(this,n)})},qrcodeshow:function(n,o,c){console.log("App qrcodeshow"),o=o||0,t.callHandler(a+"function.qrcodeshow",{qrcodes:n,index:o},function(n){console.log(n),"function"==typeof c&&c.call(this,n)})},showtopmenu:function(n,o,c){console.log("App showtopbar"),o=o||{},o.type=n?2:1,o.show_bar&&(o.type=0),t.callHandler(a+"function.showtopbar",o,function(n){console.log(n),"function"==typeof c&&c.call(this,n)})}};return s});