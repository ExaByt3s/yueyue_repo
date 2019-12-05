define("appmsg/report_and_source.js",["biz_common/utils/string/html.js","biz_common/dom/event.js","biz_wap/utils/ajax.js","biz_wap/jsapi/core.js"],function(require,exports,module){
"use strict";
function viewSource(){
var redirectUrl=sourceurl.indexOf("://")<0?"http://"+sourceurl:sourceurl;
redirectUrl="http://"+location.host+"/mp/redirect?url="+encodeURIComponent(sourceurl);
var opt={
url:"/mp/advertisement_report"+location.search+"&report_type=3&action_type=0&url="+encodeURIComponent(sourceurl)+"&__biz="+biz+"&r="+Math.random(),
type:"GET",
async:!1
};
return tid?opt.success=function(res){
try{
res=eval("("+res+")");
}catch(e){
res={};
}
res&&0==res.ret?location.href=redirectUrl:viewSource();
}:(opt.timeout=2e3,opt.complete=function(){
location.href=redirectUrl;
}),ajax(opt),!1;
}
require("biz_common/utils/string/html.js");
var DomEvent=require("biz_common/dom/event.js"),ajax=require("biz_wap/utils/ajax.js"),title=msg_title.htmlDecode(),sourceurl=msg_source_url.htmlDecode(),js_report_article=document.getElementById("js_report_article"),JSAPI=require("biz_wap/jsapi/core.js");
DomEvent.tap(js_report_article,function(){
var e=["/mp/infringement?url=",encodeURIComponent(location.href),"&title=",encodeURIComponent(title),"&__biz=",biz].join("");
return location.href=e+"#wechat_redirect",!1;
});
var js_view_source=document.getElementById("js_view_source");
DomEvent.on(js_view_source,"click",function(){
return viewSource(),!1;
});
});define("appmsg/page_pos.js",["biz_common/utils/string/html.js","biz_common/dom/event.js","biz_wap/utils/ajax.js","biz_common/utils/cookie.js","appmsg/cdn_img_lib.js"],function(e){
"use strict";
function t(e){
for(var t=5381,n=0;n<e.length;n++)t=(t<<5)+t+e.charCodeAt(n),t&=2147483647;
return t;
}
function n(e,t){
if(e&&!(e.length<=0))for(var n,o,i,a=/http(s)?\:\/\/([^\/]*)(\?|\/)?/,l=0,m=e.length;m>l;++l)n=e[l],
n&&(o=n.getAttribute(t),o&&(i=o.match(a),i&&i[2]&&(s[i[2]]=!0)));
}
function o(){
s={},n(document.getElementsByTagName("a"),"href"),n(document.getElementsByTagName("link"),"href"),
n(document.getElementsByTagName("iframe"),"src"),n(document.getElementsByTagName("script"),"src"),
n(document.getElementsByTagName("img"),"src");
var e=[];
for(var t in s)s.hasOwnProperty(t)&&e.push(t);
return s={},e.join(",");
}
function i(){
var e,t=window.pageYOffset||document.documentElement.scrollTop,n=document.getElementById("js_content"),i=document.documentElement.clientHeight||window.innerHeight,a=document.body.scrollHeight,l=Math.ceil(a/i),s=(window.logs.read_height||t)+i,r=document.getElementById("js_toobar").offsetTop,g=n.getElementsByTagName("img")||[],c=Math.ceil(s/i)||1,w=document.getElementById("media"),_=50,f=0,u=0,h=0,p=0,v=s+_>r?1:0;
c>l&&(c=l);
var y=function(t){
if(t)for(var n=0,o=t.length;o>n;++n){
var i=t[n];
if(i){
f++;
var a=i.getAttribute("src"),l=i.getAttribute("data-type");
a&&0==a.indexOf("http")&&(u++,a.isCDN()&&(h++,-1!=a.indexOf("?tp=webp")&&p++),l&&(e["img_"+l+"_cnt"]=e["img_"+l+"_cnt"]||0,
e["img_"+l+"_cnt"]++));
}
}
e.download_cdn_webp_img_cnt=p||0,e.download_img_cnt=u||0,e.download_cdn_img_cnt=h||0;
},b=window.appmsgstat||{},T=window.logs.img||{},O=window.logs.pagetime||{},x=T.load||{},j=T.read||{},E=[],B=[],N=0,S=0,z=0;
for(var D in j)D&&0==D.indexOf("http")&&j.hasOwnProperty(D)&&B.push(D);
for(var D in x)D&&0==D.indexOf("http")&&x.hasOwnProperty(D)&&E.push(D);
for(var I=0,k=E.length;k>I;++I){
var M=E[I];
M&&M.isCDN()&&(-1!=M.indexOf("/0")&&N++,-1!=M.indexOf("/640")&&S++,-1!=M.indexOf("/300")&&z++);
}
var e={
__biz:biz,
title:msg_title.htmlDecode(),
mid:mid,
idx:idx,
read_cnt:b.read_num||0,
like_cnt:b.like_num||0,
screen_height:i,
screen_num:l,
video_cnt:window.logs.video_cnt||0,
img_cnt:f||0,
read_screen_num:c||0,
is_finished_read:v,
scene:source,
content_len:d.content_length||0,
start_time:page_begintime,
end_time:(new Date).getTime(),
img_640_cnt:S,
img_0_cnt:N,
img_300_cnt:z,
wtime:O.wtime||0,
ftime:O.ftime||0,
ptime:O.ptime||0,
reward_heads_total:window.logs.reward_heads_total||0,
reward_heads_fail:window.logs.reward_heads_fail||0
};
if(window.networkType&&"wifi"==window.networkType&&(e.wifi_all_imgs_cnt=E.length,
e.wifi_read_imgs_cnt=B.length),window.logs.webplog&&4==window.logs.webplog.total){
var Y=window.logs.webplog;
e.webp_total=1,e.webp_lossy=Y.lossy,e.webp_lossless=Y.lossless,e.webp_alpha=Y.alpha,
e.webp_animation=Y.animation;
}
y(!!w&&w.getElementsByTagName("img")),y(g);
var P=(new Date).getDay();
0!==user_uin&&Math.floor(user_uin/100)%7==P&&(e.domain_list=o()),m({
url:"/mp/appmsgreport?action=page_time",
type:"POST",
data:e,
async:!1,
timeout:2e3
});
}
function a(e,t){
try{
localStorage.setItem(e,t);
}catch(n){
for(var o=localStorage.length-1;o>=0;){
var i=localStorage.key(o);
i.match(/^\d+$/)&&localStorage.removeItem(i),o--;
}
}
}
e("biz_common/utils/string/html.js");
{
var l=e("biz_common/dom/event.js"),m=e("biz_wap/utils/ajax.js");
e("biz_common/utils/cookie.js");
}
e("appmsg/cdn_img_lib.js");
var d={};
!function(){
var e=document.getElementsByTagName("html");
if(e&&1==!!e.length){
e=e[0].innerHTML;
var t=e.replace(/[\x00-\xff]/g,""),n=e.replace(/[^\x00-\xff]/g,"");
d.content_length=1*n.length+3*t.length+"<!DOCTYPE html><html></html>".length;
}
window.logs.pageinfo=d;
}();
var s={},r=null,g=0,c=msg_link.split("?").pop(),w=t(c);
window.localStorage&&(l.on(window,"load",function(){
g=1*localStorage.getItem(w);
var e=location.href.indexOf("scrolltodown")>-1?!0:!1,t=(document.getElementById("img-content"),
document.getElementById("js_cmt_area"));
if(e&&t&&t.offsetTop){
var n=t.offsetTop;
window.scrollTo(0,n-25);
}else window.scrollTo(0,g);
}),l.on(window,"unload",function(){
if(a(n,g),window._adRenderData&&"undefined"!=typeof JSON&&JSON.stringify){
var e=JSON.stringify(window._adRenderData),t=+new Date,n=[biz,sn,mid,idx].join("_");
localStorage.setItem("adinfo_"+n,e),localStorage.setItem("adinfo_time_"+n,t);
}
i();
}),window.logs.read_height=0,l.on(window,"scroll",function(){
var e=window.pageYOffset||document.documentElement.scrollTop;
window.logs.read_height=Math.max(window.logs.read_height,e),clearTimeout(r),r=setTimeout(function(){
g=window.pageYOffset,a(w,g);
},500);
}),l.on(document,"touchmove",function(){
var e=window.pageYOffset||document.documentElement.scrollTop;
window.logs.read_height=Math.max(window.logs.read_height,e),clearTimeout(r),r=setTimeout(function(){
g=window.pageYOffset,a(w,g);
},500);
}));
});define("appmsg/cdn_speed_report.js",["biz_common/dom/event.js","biz_wap/jsapi/core.js","biz_wap/utils/ajax.js"],function(e){
"use strict";
function n(){
function e(e){
var n=[];
for(var i in e)n.push(i+"="+encodeURIComponent(e[i]||""));
return n.join("&");
}
if(networkType){
var n=window.performance||window.msPerformance||window.webkitPerformance;
if(n&&"undefined"!=typeof n.getEntries){
var i,t,a=100,o=document.getElementsByTagName("img"),s=o.length,p=navigator.userAgent,m=!1;
/micromessenger\/(\d+\.\d+)/i.test(p),t=RegExp.$1;
for(var g=0,w=o.length;w>g;g++)if(i=parseInt(100*Math.random()),!(i>a)){
var d=o[g].getAttribute("src");
if(d&&!(d.indexOf("mp.weixin.qq.com")>=0)){
for(var f,c=n.getEntries(),_=0;_<c.length;_++)if(f=c[_],f.name==d){
r({
type:"POST",
url:"/mp/appmsgpicreport?__biz="+biz+"#wechat_redirect",
data:e({
rnd:Math.random(),
uin:uin,
version:version,
client_version:t,
device:navigator.userAgent,
time_stamp:parseInt(+new Date/1e3),
url:d,
img_size:o[g].fileSize||0,
user_agent:navigator.userAgent,
net_type:networkType,
appmsg_id:window.appmsgid||"",
sample:s>100?100:s,
delay_time:parseInt(f.duration)
})
}),m=!0;
break;
}
if(m)break;
}
}
}
}
}
var i=e("biz_common/dom/event.js"),t=e("biz_wap/jsapi/core.js"),r=e("biz_wap/utils/ajax.js"),a={
"network_type:fail":"fail",
"network_type:edge":"2g/3g",
"network_type:wwan":"2g/3g",
"network_type:wifi":"wifi"
};
t.invoke("getNetworkType",{},function(e){
networkType=a[e.err_msg],n();
}),i.on(window,"load",n,!1);
});define("appmsg/iframe.js",[],function(){
"use strict";
function e(e){
var t=0;
e.contentDocument&&e.contentDocument.body.offsetHeight?t=e.contentDocument.body.offsetHeight:e.Document&&e.Document.body&&e.Document.body.scrollHeight?t=e.Document.body.scrollHeight:e.document&&e.document.body&&e.document.body.scrollHeight&&(t=e.document.body.scrollHeight);
var i=e.parentElement;
if(i&&(e.style.height=t+"px"),/MSIE\s(7|8)/.test(navigator.userAgent)&&e.contentWindow&&e.contentWindow.document){
var n=e.contentWindow.document.getElementsByTagName("html");
n&&n.length&&(n[0].style.overflow="hidden");
}
}
for(var t,i=document.getElementsByTagName("iframe"),n=document.getElementById("js_content"),o=document.getElementById("msg_page"),d=n?n.offsetWidth:o.offsetWidth,c=0,r=i.length;r>c;++c){
t=i[c];
var s=t.getAttribute("data-src"),m=t.className||"";
if(s&&(s.indexOf("newappmsgvote")>-1&&m.indexOf("js_editor_vote_card")>=0||0==s.indexOf("http://mp.weixin.qq.com/bizmall/appmsgcard")&&m.indexOf("card_iframe")>=0||s.indexOf("appmsgvote")>-1||s.indexOf("mp.weixin.qq.com/mp/getcdnvideourl")>-1)){
if(s=s.replace(/^http:/,location.protocol),m.indexOf("card_iframe")>=0)t.setAttribute("src",s.replace("#wechat_redirect",["&uin=",uin,"&key=",key,"&pass_ticket=",pass_ticket,"&scene=",source,"&msgid=",appmsgid,"&msgidx=",itemidx||idx,"&version=",version,"&devicetype=",window.devicetype||""].join("")));else{
var a=s.indexOf("#wechat_redirect")>-1,p=["&uin=",uin,"&key=",key,"&pass_ticket=",pass_ticket].join("");
m.indexOf("vote_iframe")>=0&&(p+=["&appmsgid=",mid,"&appmsgidx=",idx].join(""));
var l=a?s.replace("#wechat_redirect",p):s+p;
t.setAttribute("src",l);
}
-1==s.indexOf("mp.weixin.qq.com/mp/getcdnvideourl")&&!function(t){
t.onload=function(){
e(t);
};
}(t),t.appmsg_idx=c;
}
if(s&&s.indexOf("mp.weixin.qq.com/mp/getcdnvideourl")>-1&&d>0){
var f=d,g=3*f/4;
t.width=f,t.height=g,t.style.setProperty&&(t.style.setProperty("width",f+"px","important"),
t.style.setProperty("height",g+"px","important"));
}
}
if(window.iframe_reload=function(){
for(var n=0,o=i.length;o>n;++n){
t=i[n];
var d=t.getAttribute("src");
d&&(d.indexOf("newappmsgvote")>-1||d.indexOf("appmsgvote")>-1)&&e(t);
}
},"getElementsByClassName"in document)for(var u,y=document.getElementsByClassName("video_iframe"),c=0;u=y.item(c++);)u.setAttribute("scrolling","no"),
u.style.overflow="hidden";
});define("appmsg/review_image.js",["biz_common/dom/event.js","biz_wap/jsapi/core.js","biz_common/utils/url/parse.js","appmsg/cdn_img_lib.js"],function(e){
"use strict";
function t(e,t){
r.invoke("imagePreview",{
current:e,
urls:t
});
}
function i(e){
var i=[],r=e.container;
r=r?r.getElementsByTagName("img"):[];
for(var n=0,p=r.length;p>n;n++){
var m=r.item(n),c=m.getAttribute("data-src")||m.getAttribute("src"),o=m.getAttribute("data-type");
if(c){
for(;-1!=c.indexOf("?tp=webp");)c=c.replace("?tp=webp","");
m.dataset&&m.dataset.s&&c.isCDN()&&(c=c.replace(/\/640$/,"/0"),c=c.replace(/\/640\?/,"/0?")),
c.isCDN()&&(c=s.addParam(c,"wxfrom","3",!0)),e.is_https_res&&(c=c.http2https()),
o&&(c=s.addParam(c,"wxtype",o,!0)),i.push(c),function(e){
a.on(m,"click",function(){
return t(e,i),!1;
});
}(c);
}
}
}
var a=e("biz_common/dom/event.js"),r=e("biz_wap/jsapi/core.js"),s=e("biz_common/utils/url/parse.js");
return e("appmsg/cdn_img_lib.js"),i;
});define("appmsg/outer_link.js",["biz_common/dom/event.js"],function(e){
"use strict";
function n(e){
var n=e.container;
if(!n)return!1;
for(var i=n.getElementsByTagName("a")||[],r=0,o=i.length;o>r;++r)!function(n){
var r=i[n],o=r.getAttribute("href");
if(!o)return!1;
0!=o.indexOf("http://mp.weixin.qq.com")&&0!=o.indexOf("http://mp.weixin.qq.com");
var c=0,f=r.innerHTML;
/^[^<>]+$/.test(f)?c=1:/^<img[^>]*>$/.test(f)&&(c=2),!!e.changeHref&&(o=e.changeHref(o,c)),
t.on(r,"click",function(){
return location.href=o,!1;
},!0);
}(r);
}
var t=e("biz_common/dom/event.js");
return n;
});define("biz_wap/jsapi/core.js",[],function(){
"use strict";
var e={
ready:function(e){
"undefined"!=typeof WeixinJSBridge&&WeixinJSBridge.invoke?e():document.addEventListener?document.addEventListener("WeixinJSBridgeReady",e,!1):document.attachEvent&&(document.attachEvent("WeixinJSBridgeReady",e),
document.attachEvent("onWeixinJSBridgeReady",e));
},
invoke:function(e,i,n){
this.ready(function(){
return"object"!=typeof WeixinJSBridge?(alert("请在微信中打开此链接！"),!1):void WeixinJSBridge.invoke(e,i,n);
});
},
call:function(e){
this.ready(function(){
return"object"!=typeof WeixinJSBridge?!1:void WeixinJSBridge.call(e);
});
},
on:function(e,i){
this.ready(function(){
return"object"==typeof WeixinJSBridge&&WeixinJSBridge.on?void WeixinJSBridge.on(e,i):!1;
});
}
};
return e;
});define("biz_common/dom/event.js",[],function(){
"use strict";
function t(t,e,n,i){
a.isPc||a.isWp?o(t,"click",i,e,n):o(t,"touchend",i,function(t){
var n=t.changedTouches[0];
return Math.abs(a.y-n.clientY)<=5&&Math.abs(a.x-n.clientX)<=5?e.call(this,t):void 0;
},n);
}
function e(t,e){
if(!t||!e||t.nodeType!=t.ELEMENT_NODE)return!1;
var n=t.webkitMatchesSelector||t.msMatchesSelector||t.matchesSelector;
return n?n.call(t,e):(e=e.substr(1),t.className.indexOf(e)>-1);
}
function n(t,n,o){
for(;t&&!e(t,n);)t=t!==o&&t.nodeType!==t.DOCUMENT_NODE&&t.parentNode;
return t;
}
function o(e,o,i,r,c){
var s,d,u;
return"input"==o&&a.isPc,e?("function"==typeof i&&(c=r,r=i,i=""),"string"!=typeof i&&(i=""),
e==window&&"load"==o&&/complete|loaded/.test(document.readyState)?r({
type:"load"
}):"tap"==o?t(e,r,c,i):(s=function(t){
var e=r(t);
return e===!1&&(t.stopPropagation&&t.stopPropagation(),t.preventDefault&&t.preventDefault()),
e;
},i&&"."==i.charAt(0)&&(u=function(t){
var o=t.target||t.srcElement,r=n(o,i,e);
return r?(t.delegatedTarget=r,s(t)):void 0;
}),d=u||s,r[o+"_handler"]=d,e.addEventListener?void e.addEventListener(o,d,!!c):e.attachEvent?void e.attachEvent("on"+o,d,!!c):void 0)):void 0;
}
function i(t,e,n,o){
if(t){
var i=n[e+"_handler"]||n;
return t.removeEventListener?void t.removeEventListener(e,i,!!o):t.detachEvent?void t.detachEvent("on"+e,i,!!o):void 0;
}
}
var r=navigator.userAgent,a={
isPc:/(WindowsNT)|(Windows NT)|(Macintosh)/i.test(navigator.userAgent),
isWp:/Windows\sPhone/i.test(r)
};
return a.isPc||o(document,"touchstart",function(t){
var e=t.changedTouches[0];
a.x=e.clientX,a.y=e.clientY;
}),{
on:o,
off:i,
tap:t
};
});define("appmsg/async.js",["biz_common/utils/string/html.js","biz_common/dom/event.js","biz_wap/utils/ajax.js","biz_common/dom/class.js","biz_common/tmpl.js","appmsg/cdn_img_lib.js","biz_common/utils/url/parse.js","appmsg/a.js","appmsg/like.js","appmsg/comment.js","appmsg/reward_entry.js"],function(require,exports,module){
"use strict";
function saveCopy(e){
var t={};
for(var a in e)if(e.hasOwnProperty(a)){
var n=e[a],i=typeof n;
n="string"==i?n.htmlDecode():n,"object"==i&&(n=saveCopy(n)),t[a]=n;
}
return t;
}
function fillVedio(e){
if(vedio_iframes&&vedio_iframes.length>0)for(var t,a,n,i=0,r=vedio_iframes.length;r>i;++i)t=vedio_iframes[i],
a=t.iframe,n=t.src,e&&(n=n+"&encryptVer=6.0&platform=61001&cKey="+e),a.setAttribute("src",n);
}
function fillData(e){
var t=e.adRenderData||{
advertisement_num:0
};
if(!t.flag&&t.advertisement_num>0){
var a=t.advertisement_num,n=t.advertisement_info;
window.adDatas.num=a;
for(var i=0;a>i;++i){
var r=null,o=n[i];
if(o.biz_info=o.biz_info||{},o.app_info=o.app_info||{},o.pos_type=o.pos_type||0,
o.logo=o.logo||"",100==o.pt)r={
usename:o.biz_info.user_name,
pt:o.pt,
url:o.url,
traceid:o.traceid,
adid:o.aid,
is_appmsg:!0
};else if(102==o.pt)r={
appname:o.app_info.app_name,
versioncode:o.app_info.version_code,
pkgname:o.app_info.apk_name,
androiddownurl:o.app_info.apk_url,
md5sum:o.app_info.app_md5,
signature:o.app_info.version_code,
rl:o.rl,
traceid:o.traceid,
pt:o.pt,
type:o.type,
adid:o.aid,
is_appmsg:!0
};else if(101==o.pt)r={
appname:o.app_info.app_name,
app_id:o.app_info.app_id,
icon_url:o.app_info.icon_url,
appinfo_url:o.app_info.appinfo_url,
rl:o.rl,
traceid:o.traceid,
pt:o.pt,
ticket:o.ticket,
type:o.type,
adid:o.aid,
is_appmsg:!0
};else if(103==o.pt||104==o.pt){
var d=o.app_info.down_count||0,s=o.app_info.app_size||0,m=o.app_info.app_name||"",p=o.app_info.category,_=["万","百万","亿"];
if(d>=1e4){
d/=1e4;
for(var l=0;d>=10&&2>l;)d/=100,l++;
d=d.toFixed(1)+_[l]+"次";
}else d=d.toFixed(1)+"次";
s>=1024?(s/=1024,s=s>=1024?(s/1024).toFixed(2)+"MB":s.toFixed(2)+"KB"):s=s.toFixed(2)+"B",
p=p?p[0]||"其他":"其他";
for(var c=["-","(",":",'"',"'","：","（","—","“","‘"],f=-1,u=0,g=c.length;g>u;++u){
var w=c[u],v=m.indexOf(w);
-1!=v&&(-1==f||f>v)&&(f=v);
}
-1!=f&&(m=m.substring(0,f)),o.app_info._down_count=d,o.app_info._app_size=s,o.app_info._category=p,
o.app_info.app_name=m,r={
appname:o.app_info.app_name,
app_rating:o.app_info.app_rating||0,
app_id:o.app_info.app_id,
channel_id:o.app_info.channel_id,
md5sum:o.app_info.app_md5,
rl:o.rl,
pkgname:o.app_info.apk_name,
androiddownurl:o.app_info.apk_url,
versioncode:o.app_info.version_code,
appinfo_url:o.app_info.appinfo_url,
traceid:o.traceid,
pt:o.pt,
ticket:o.ticket,
type:o.type,
adid:o.aid,
is_appmsg:!0
};
}
var y=o.image_url;
require("appmsg/cdn_img_lib.js");
var h=require("biz_common/utils/url/parse.js");
y&&y.isCDN()&&(y=y.replace(/\/0$/,"/640"),y=y.replace(/\/0\?/,"/640?"),o.image_url=h.addParam(y,"wxfrom","50",!0)),
adDatas.ads["pos_"+o.pos_type]={
a_info:o,
adData:r
};
}
var b=function(e){
var t=window.pageYOffset||document.documentElement.scrollTop||document.body.scrollTop;
"undefined"!=typeof e&&(t=e);
10>=t&&(k.style.display="block",DomEvent.off(window,"scroll",b));
},j=document.getElementById("js_bottom_ad_area"),k=document.getElementById("js_top_ad_area"),I=adDatas.ads;
for(var z in I)if(0==z.indexOf("pos_")){
var r=I[z],o=!!r&&r.a_info;
if(r&&o)if(0==o.pos_type)j.innerHTML=TMPL.render("t_ad",o);else if(1==o.pos_type){
k.style.display="none",k.innerHTML=TMPL.render("t_ad",o),DomEvent.on(window,"scroll",b);
var D=0;
window.localStorage&&(D=1*localStorage.getItem(z)||0),window.scrollTo(0,D),b(D);
}
}
require("appmsg/a.js");
}
var x=e.appmsgstat||{};
window.appmsgstat||(window.appmsgstat=x),x.show&&(!function(){
var e=document.getElementById("js_read_area"),t=document.getElementById("like");
e.style.display="block",t.style.display="inline",x.liked&&Class.addClass(t,"praised"),
t.setAttribute("like",x.liked?"1":"0");
var a=document.getElementById("likeNum"),n=document.getElementById("readNum"),i=x.read_num,r=x.like_num;
i||(i=1),r||(r="赞"),parseInt(i)>1e5?i="100000+":"",parseInt(r)>1e5?r="100000+":"",
n&&(n.innerHTML=i),a&&(a.innerHTML=r);
}(),require("appmsg/like.js")),1==e.comment_enabled&&require("appmsg/comment.js"),
-1!=ua.indexOf("MicroMessenger")&&e.reward&&(rewardEntry=require("appmsg/reward_entry.js"),
rewardEntry.handle(e.reward,getCountPerLine()));
}
function getAsyncData(){
var is_need_ticket="";
vedio_iframes&&vedio_iframes.length>0&&(is_need_ticket="&is_need_ticket=1");
var is_need_ad=1,_adInfo=null;
if(window.localStorage)try{
var key=[biz,sn,mid,idx].join("_");
_adInfo=localStorage.getItem("adinfo_"+key);
try{
_adInfo=eval("("+_adInfo+")");
}catch(e){
_adInfo=null;
}
var _adInfoSaveTime=1*localStorage.getItem("adinfo_time_"+key),_now=+new Date;
_adInfo&&18e4>_now-1*_adInfoSaveTime&&1*_adInfo.advertisement_num>0?is_need_ad=0:(localStorage.removeItem("adinfo_"+key),
localStorage.removeItem("adinfo_time_"+key));
}catch(e){
is_need_ad=1,_adInfo=null;
}
document.getElementsByClassName&&-1!=navigator.userAgent.indexOf("MicroMessenger")||(is_need_ad=0);
var screen_num=Math.ceil(document.body.scrollHeight/(document.documentElement.clientHeight||window.innerHeight)),both_ad=screen_num>=2?1:0;
ajax({
url:"/mp/getappmsgext?__biz="+biz+"&mid="+mid+"&sn="+sn+"&idx="+idx+"&scene="+source+"&title="+encodeURIComponent(msg_title.htmlDecode())+"&ct="+ct+"&devicetype="+devicetype.htmlDecode()+"&version="+version.htmlDecode()+"&f=json&r="+Math.random()+is_need_ticket+"&is_need_ad="+is_need_ad+"&comment_id="+comment_id+"&is_need_reward="+is_need_reward+"&both_ad="+both_ad+"&reward_uin_count="+(is_need_reward?3*getCountPerLine():0),
type:"GET",
async:!0,
success:function(ret){
var tmpret=ret;
if(ret)try{
try{
ret=eval("("+tmpret+")");
}catch(e){
var img=new Image;
return void(img.src=("http://mp.weixin.qq.com/mp/jsreport?1=1&key=3&content=biz:"+biz+",mid:"+mid+",uin:"+uin+"[key3]"+encodeURIComponent(tmpret)+"&r="+Math.random()).substr(0,1024));
}
if(fillVedio(ret.appmsgticket?ret.appmsgticket.ticket:""),ret.ret)return;
var adRenderData={};
if(0==is_need_ad)adRenderData=_adInfo,adRenderData||(adRenderData={
advertisement_num:0
});else{
if(ret.advertisement_num>0&&ret.advertisement_info){
var d=ret.advertisement_info;
adRenderData.advertisement_info=saveCopy(d);
}
adRenderData.advertisement_num=ret.advertisement_num;
}
1==is_need_ad&&(window._adRenderData=adRenderData),fillData({
adRenderData:adRenderData,
appmsgstat:ret.appmsgstat,
comment_enabled:ret.comment_enabled,
reward:{
reward_total:ret.reward_total_count,
self_head_img:ret.self_head_img,
reward_head_imgs:ret.reward_head_imgs||[],
can_reward:ret.can_reward,
timestamp:ret.timestamp
}
});
}catch(e){
var img=new Image;
return img.src=("http://mp.weixin.qq.com/mp/jsreport?1=1&key=1&content=biz:"+biz+",mid:"+mid+",uin:"+uin+"[key1]"+encodeURIComponent(e.toString())+"&r="+Math.random()).substr(0,1024),
void(console&&console.error(e));
}
},
error:function(){
var e=new Image;
e.src="http://mp.weixin.qq.com/mp/jsreport?1=1&key=2&content=biz:"+biz+",mid:"+mid+",uin:"+uin+"[key2]ajax_err&r="+Math.random();
}
});
}
function getCountPerLine(){
return window.onresize=function(){
console.log("resized"),onResize(),rewardEntry&&rewardEntry.render(getCountPerLine());
},onResize();
}
function onResize(){
var e=window.innerWidth||document.documentElement.clientWidth;
try{
e=document.getElementById("page-content").getBoundingClientRect().width;
}catch(t){}
var a=30,n=34,i=Math.floor(.9*(e-a)/n);
return document.getElementById("js_reward_inner")&&(document.getElementById("js_reward_inner").style.width=i*n+"px"),
getCountPerLine=function(){
return i;
},i;
}
require("biz_common/utils/string/html.js");
var iswifi=!1,ua=navigator.userAgent,DomEvent=require("biz_common/dom/event.js"),offset=200,ajax=require("biz_wap/utils/ajax.js"),Class=require("biz_common/dom/class.js"),TMPL=require("biz_common/tmpl.js"),rewardEntry,iframes=document.getElementsByTagName("iframe"),iframe,js_content=document.getElementById("js_content"),vedio_iframes=[],w=js_content.offsetWidth,h=3*w/4;
window.logs.video_cnt=0;
for(var i=0,len=iframes.length;len>i;++i){
iframe=iframes[i];
var src=iframe.getAttribute("data-src"),realsrc=iframe.getAttribute("src")||src;
!realsrc||0!=realsrc.indexOf("http://v.qq.com/iframe/player.html")&&0!=realsrc.indexOf("http://z.weishi.com/weixin/player.html")||(realsrc=realsrc.replace(/width=\d+/g,"width="+w),
realsrc=realsrc.replace(/height=\d+/g,"height="+h),0==realsrc.indexOf("http://v.qq.com/iframe/player.html")?vedio_iframes.push({
iframe:iframe,
src:realsrc
}):iframe.setAttribute("src",realsrc),iframe.width=w,iframe.height=h,iframe.style.setProperty&&(iframe.style.setProperty("width",w+"px","important"),
iframe.style.setProperty("height",h+"px","important")),window.logs.video_cnt++);
}
window.adDatas={
ads:{},
num:0
};
var js_toobar=document.getElementById("js_toobar"),innerHeight=window.innerHeight||document.documentElement.clientHeight,onScroll=function(){
var e=window.pageYOffset||document.documentElement.scrollTop,t=js_toobar.offsetTop;
e+innerHeight+offset>=t&&(getAsyncData(),DomEvent.off(window,"scroll",onScroll));
};
iswifi?(DomEvent.on(window,"scroll",onScroll),onScroll()):getAsyncData();
});define("biz_wap/ui/lazyload_img.js",["biz_wap/utils/mmversion.js","biz_common/dom/event.js","biz_common/dom/attr.js","biz_common/ui/imgonepx.js"],function(t){
"use strict";
function i(){
var t=this.images;
if(!t||t.length<=0)return!1;
var i=window.pageYOffset||document.documentElement.scrollTop,e=window.innerHeight||document.documentElement.clientHeight,o=e+40,n=this.offset||20,a=0;
if("wifi"==window.networkType){
var s={
bottom:1,
top:1
};
this.lazyloadHeightWhenWifi&&(s=this.lazyloadHeightWhenWifi()),n=Math.max(s.bottom*e,n),
a=Math.max(s.top*e,a);
}
for(var l=+new Date,d=[],c=this.sw,u=0,w=t.length;w>u;u++){
var p=t[u],f=p.el.offsetTop;
if(!p.show&&(i>=f&&i<=f+p.height+a||f>i&&i+o+n>f)){
var g=p.src,v=this;
this.inImgRead&&(i>=f&&i<=f+p.height||f>i&&i+o>f)&&this.inImgRead(g,networkType),
this.changeSrc&&(g=this.changeSrc(p.el,g)),p.el.onerror=function(){
!!v.onerror&&v.onerror(g);
},p.el.onload=function(){
var t=this;
m(t,"height","auto","important"),t.getAttribute("_width")?m(t,"width",t.getAttribute("_width"),"important"):m(t,"width","auto","important");
},h(p.el,"src",g),d.push(g),p.show=!0,m(p.el,"visibility","visible","important");
}
r.isWp&&1*p.el.width>c&&(p.el.width=c);
}
d.length>0&&this.detect&&this.detect({
time:l,
loadList:d,
scrollTop:i
});
}
function e(){
var t=document.getElementsByTagName("img"),e=[],o=this.container,n=this.attrKey||"data-src",r=o.offsetWidth,a=0;
o.currentStyle?a=o.currentStyle.width:"undefined"!=typeof getComputedStyle&&(a=getComputedStyle(o).width),
this.sw=1*a.replace("px","");
for(var s=0,d=t.length;d>s;s++){
var c=t.item(s),u=h(c,n);
if(u){
var w=100;
if(c.dataset&&c.dataset.ratio){
var p=1*c.dataset.ratio,f=1*c.dataset.w||r;
"number"==typeof p&&p>0?(f=r>=f?f:r,w=f*p,c.style.width&&c.setAttribute("_width",c.style.width),
m(c,"width",f+"px","important"),m(c,"visibility","visible","important"),c.setAttribute("src",l)):m(c,"visibility","hidden","important");
}else m(c,"visibility","hidden","important");
m(c,"height",w+"px","important"),e.push({
el:c,
src:u,
height:w,
show:!1
});
}
}
this.images=e,i.call(this);
}
function o(t){
var e=this,o=e.timer;
clearTimeout(o),e.timer=setTimeout(function(){
i.call(e,t);
},300);
}
function n(t){
a.on(window,"scroll",function(i){
o.call(t,i);
}),a.on(window,"load",function(i){
e.call(t,i);
}),a.on(document,"touchmove",function(i){
o.call(t,i);
});
}
var r=t("biz_wap/utils/mmversion.js"),a=t("biz_common/dom/event.js"),s=t("biz_common/dom/attr.js"),h=s.attr,m=s.setProperty,l=t("biz_common/ui/imgonepx.js");
return n;
});define("biz_common/log/jserr.js",[],function(){
function e(e,n){
return e?(r.replaceStr&&(e=e.replace(r.replaceStr,"")),n&&(e=e.substr(0,n)),encodeURIComponent(e.replace("\n",","))):"";
}
var r={};
return window.onerror=function(n,o,t,c,i){
return"Script error."==n||o?"undefined"==typeof r.key||"undefined"==typeof r.reporturl?!0:(setTimeout(function(){
c=c||window.event&&window.event.errorCharacter||0;
var l=[];
if(l.push("msg:"+e(n,100)),o&&(o=o.replace(/[^\,]*\/js\//g,"")),l.push("url:"+e(o,200)),
l.push("line:"+t),l.push("col:"+c),i&&i.stack)l.push("info:"+e(i.stack.toString(),200));else if(arguments.callee){
for(var s=[],u=arguments.callee.caller,a=3;u&&--a>0&&(s.push(u.toString()),u!==u.caller);)u=u.caller;
s=s.join(","),l.push("info:"+e(s,200));
}
var p=new Image;
if(p.src=(r.reporturl+"&key="+r.key+"&content="+l.join("||")).substr(0,1024),window.console&&window.console.log){
var f=l.join("\n");
try{
f=decodeURIComponent(f);
}catch(d){}
console.log(f);
}
},0),!0):!0;
},function(e){
r=e;
};
});define("appmsg/share.js",["biz_common/utils/string/html.js","appmsg/cdn_img_lib.js","biz_common/dom/event.js","biz_common/utils/url/parse.js","biz_wap/utils/mmversion.js","biz_wap/utils/ajax.js","biz_wap/jsapi/core.js"],function(i){
"use strict";
function e(i,e){
var n="";
""!=tid&&(n="tid="+tid+"&aid=54");
var t=i.split("?")[1]||"";
if(t=t.split("#")[0],""!=t){
var s=[t,"scene="+e];
return""!=n&&s.push(n),t=s.join("&"),i.split("?")[0]+"?"+t+"#"+(i.split("#")[1]||"");
}
}
function n(i,e,n){
var t=i.split("?").pop();
if(t=t.split("#").shift(),""!=t){
var s=[t,"action_type="+n,"uin="+e].join("&");
m({
url:"/mp/appmsg/show",
type:"POST",
timeout:2e3,
data:s
});
}
}
function t(i,e){
return i.isCDN()&&(i=s.addParam(i,"wxfrom",e,!0)),i;
}
i("biz_common/utils/string/html.js"),i("appmsg/cdn_img_lib.js");
var s=(i("biz_common/dom/event.js"),i("biz_common/utils/url/parse.js")),o=i("biz_wap/utils/mmversion.js"),m=i("biz_wap/utils/ajax.js"),a=i("biz_wap/jsapi/core.js");
a.call("hideToolbar");
var l=msg_title.htmlDecode(),r=(msg_source_url.htmlDecode(),""),u=msg_cdn_url,c=msg_link.htmlDecode(),l=msg_title.htmlDecode(),_=msg_desc.htmlDecode();
_=_||c,u.isCDN()&&(u=u.replace(/\/0$/,"/300")),"1"==is_limit_user&&a.call("hideOptionMenu"),
a.on("menu:share:appmessage",function(i){
var s=1,o=t(u,"1");
i&&"favorite"==i.scene&&(s=4,o=t(u,"4")),a.invoke("sendAppMessage",{
appid:r,
img_url:o,
img_width:"640",
img_height:"640",
link:e(c,s),
desc:_,
title:l
},function(){
n(c,fakeid,s);
});
}),a.on("menu:share:timeline",function(){
var i=u;
o.isIOS||(i=t(u,"2")),n(c,fakeid,2),a.invoke("shareTimeline",{
img_url:i,
img_width:"640",
img_height:"640",
link:e(c,2),
desc:_,
title:l
},function(){});
});
a.on("menu:share:weibo",function(){
a.invoke("shareWeibo",{
content:l+e(c,3),
url:e(c,3)
},function(){
n(c,fakeid,3);
});
}),a.on("menu:share:facebook",function(){
n(c,fakeid,4),a.invoke("shareFB",{
img_url:u,
img_width:"640",
img_height:"640",
link:e(c,4),
desc:_,
title:l
},function(){});
}),a.on("menu:share:QZone",function(){
var i=t(u,"6");
n(c,fakeid,5),a.invoke("shareQZone",{
img_url:i,
img_width:"640",
img_height:"640",
link:e(c,5),
desc:_,
title:l
},function(){});
}),a.on("menu:share:qq",function(){
var i=t(u,"7");
n(c,fakeid,5),a.invoke("shareQQ",{
img_url:i,
img_width:"640",
img_height:"640",
link:e(c,5),
desc:_,
title:l
},function(){});
}),a.on("menu:share:email",function(){
n(c,fakeid,5),a.invoke("sendEmail",{
content:e(c,5),
title:l
},function(){});
});
});define("biz_wap/utils/mmversion.js",[],function(){
"use strict";
function n(){
var n=/MicroMessenger\/([\d\.]+)/i,t=s.match(n);
return t&&t[1]?t[1]:!1;
}
function t(t,r,i){
var e=n();
if(e){
e=e.split("."),t=t.split("."),e.pop();
for(var o,s,u=f["cp"+r],c=0,a=Math.max(e.length,t.length);a>c;++c){
o=e[c]||0,s=t[c]||0,o=parseInt(o)||0,s=parseInt(s)||0;
var p=f.cp0(o,s);
if(!p)return u(o,s);
}
return i||0==r?!0:!1;
}
}
function r(n){
return t(n,0);
}
function i(n,r){
return t(n,1,r);
}
function e(n,r){
return t(n,-1,r);
}
function o(){
return u?"ios":a?"android":"unknown";
}
var s=navigator.userAgent,u=/(iPhone|iPad|iPod|iOS)/i.test(s),c=/Windows\sPhone/i.test(s),a=/(Android)/i.test(s),f={
"cp-1":function(n,t){
return t>n;
},
cp0:function(n,t){
return n==t;
},
cp1:function(n,t){
return n>t;
}
};
return{
get:n,
cpVersion:t,
eqVersion:r,
gtVersion:i,
ltVersion:e,
getPlatform:o,
isWp:c,
isIOS:u,
isAndroid:a
};
});define("appmsg/cdn_img_lib.js",[],function(){
"use strict";
String.prototype.http2https=function(){
return this.replace(/http:\/\/mmbiz\.qpic\.cn\//g,"https://mmbiz.qlogo.cn/");
},String.prototype.https2http=function(){
return this.replace(/https:\/\/mmbiz\.qlogo\.cn\//g,"http://mmbiz.qpic.cn/");
},String.prototype.isCDN=function(){
return 0==this.indexOf("http://mmbiz.qpic.cn/")||0==this.indexOf("https://mmbiz.qlogo.cn/");
};
});define("biz_common/utils/url/parse.js",[],function(){
"use strict";
function r(r){
var n=r.length,e=r.indexOf("?"),t=r.indexOf("#");
t=-1==t?n:t,e=-1==e?t:e;
var s=r.substr(0,e),a=r.substr(e+1,t-e-1),i=r.substr(t+1);
return{
host:s,
query_str:a,
hash:i
};
}
function n(n,e){
var t=r(n),s=t.query_str,a=[];
for(var i in e)e.hasOwnProperty(i)&&a.push(i+"="+encodeURIComponent(e[i]));
return a.length>0&&(s+=(""!=s?"&":"")+a.join("&")),t.host+(""!=s?"?"+s:"")+(""!=t.hash?"#"+t.hash:"");
}
function e(r,n,e,t){
r=r||location.href,-1!=r.indexOf("&")&&-1==r.indexOf("?")&&(r=r.replace("&","?"));
var s=new RegExp("([\\?&]"+n+"=)[^&#]*");
return r.match(s)?t===!0?r.replace(s,"$1"+e):r:-1==r.indexOf("?")?r+"?"+n+"="+e:r+"&"+n+"="+e;
}
return{
parseUrl:r,
join:n,
addParam:e
};
});define("appmsg/index.js",["biz_common/utils/url/parse.js","appmsg/cdn_img_lib.js","biz_wap/utils/mmversion.js","appmsg/share.js","biz_common/log/jserr.js","biz_wap/ui/lazyload_img.js","appmsg/async.js","biz_common/dom/event.js","biz_wap/jsapi/core.js","appmsg/outer_link.js","appmsg/review_image.js","appmsg/iframe.js","appmsg/cdn_speed_report.js","appmsg/page_pos.js","appmsg/report_and_source.js","biz_common/dom/class.js","appmsg/report.js"],function(e){
"use strict";
function t(e,t){
var o={
lossy:"UklGRiIAAABXRUJQVlA4IBYAAAAwAQCdASoBAAEADsD+JaQAA3AAAAAA",
lossless:"UklGRhoAAABXRUJQVlA4TA0AAAAvAAAAEAcQERGIiP4HAA==",
alpha:"UklGRkoAAABXRUJQVlA4WAoAAAAQAAAAAAAAAAAAQUxQSAwAAAARBxAR/Q9ERP8DAABWUDggGAAAABQBAJ0BKgEAAQAAAP4AAA3AAP7mtQAAAA==",
animation:"UklGRlIAAABXRUJQVlA4WAoAAAASAAAAAAAAAAAAQU5JTQYAAAD/////AABBTk1GJgAAAAAAAAAAAAAAAAAAAGQAAABWUDhMDQAAAC8AAAAQBxAREYiI/gcA"
},n=new Image;
n.onload=function(){
var o=n.width>0&&n.height>0;
t(e,o);
},n.onerror=function(){
t(e,!1);
},n.src="data:image/webp;base64,"+o[e];
}
var o=document.getElementsByTagName("body");
if(!o||!o[0])return!1;
o=o[0];
var n=/^http(s)?:\/\/mp\.weixin\.qq\.com\//g;
try{
if(top!=window&&(!top||top&&top.location.href&&n.test(top.location.href)))throw new Error("in iframe");
}catch(i){
var r="",s=new Image;
s.src=("http://mp.weixin.qq.com/mp/jsreport?key=4&content=biz:"+biz+",mid:"+mid+",uin:"+uin+"[key4]"+r+"&r="+Math.random()).substr(0,1024);
}
/MicroMessenger/.test(navigator.userAgent)&&/#rd$/.test(location.href)&&location.replace(location.href.replace(/#rd$/,"#wechat_redirect"));
var a=e("biz_common/utils/url/parse.js");
e("appmsg/cdn_img_lib.js"),window.page_endtime=+new Date;
var m=e("biz_wap/utils/mmversion.js"),c=!m.isWp&&-1==navigator.userAgent.indexOf("MicroMessenger");
if(e("appmsg/share.js"),window.logs={},"mp.weixin.qq.com"==location.host){
var p=e("biz_common/log/jserr.js");
p({
key:0,
reporturl:"http://mp.weixin.qq.com/mp/jsreport?1=1",
replaceStr:/http(s)?:(.*?)js\//g
});
}
window.logs.webplog={
lossy:0,
lossless:0,
alpha:0,
animation:0,
total:0
};
var l=-1!=navigator.userAgent.indexOf("TBS/"),d=function(e,o){
t(e,function(e,t){
if(window.logs.webplog[e]=t?1:0,window.logs.webplog.total++,4==window.logs.webplog.total){
var n=window.logs.webplog,i=Math.random();
l&&1>=i&&(n.lossy=n.lossless=n.alpha=1,window.logs.webplog=n);
var r=n.lossy&n.lossless&n.alpha;
o(!!r);
}
});
},A=function(e){
d("lossy",e),d("lossless",e),d("alpha",e),d("animation",e);
};
window.webp=!1,A(function(t){
window.webp=t,t&&window.localStorage&&window.localStorage.setItem&&window.localStorage.setItem("webp","1"),
window.logs.img={
download:{},
read:{},
load:{}
};
var o=document.getElementById("js_cover");
if(o){
var n=o.getAttribute("data-src");
if(n){
if(n.isCDN()){
var i=new Date;
for(i.setFullYear(2014,9,1);-1!=n.indexOf("?tp=webp");)n=n.replace("?tp=webp","");
for(;-1!=n.indexOf("&tp=webp");)n=n.replace("&tp=webp","");
1e3*ct>=i.getTime()&&""!=img_format&&"gif"!=img_format&&(n=n.replace(/\/0$/,"/640"),
n=n.replace(/\/0\?/,"/640?"),o.dataset&&(o.dataset.s="300,640")),t&&(n=a.addParam(n,"tp","webp",!0)),
n=a.addParam(n,"wxfrom","5",!0),is_https_res&&(n=n.http2https());
}
o.setAttribute("src",n),window.logs.img.read[n]=!0,window.logs.img.load[n]=!0,o.removeAttribute("data-src");
}
}
var r=e("biz_wap/ui/lazyload_img.js");
new r({
attrKey:"data-src",
lazyloadHeightWhenWifi:function(){
var e,t=1,o=1;
e=window.svr_time?new Date(1e3*window.svr_time):new Date;
var n=e.getHours();
return n>=20&&23>n&&(t=.5,o=0),{
bottom:t,
top:o
};
},
inImgRead:function(e){
e&&(window.logs.img.read[e]=!0);
},
changeSrc:function(e,t){
if(!t)return"";
for(var o=t;-1!=o.indexOf("?tp=webp");)o=o.replace("?tp=webp","");
for(;-1!=o.indexOf("&tp=webp");)o=o.replace("&tp=webp","");
t.isCDN()&&(e.dataset&&e.dataset.s&&(o=o.replace(/\/0$/,"/640"),o=o.replace(/\/0\?/,"/640?")),
window.webp&&(o=a.addParam(o,"tp","webp",!0)),o=a.addParam(o,"wxfrom","5",!0),is_https_res&&(o=o.http2https()));
var n=/^http\:\/\/(a|b)(\d)+\.photo\.store\.qq\.com/g;
return o=o.replace(n,"http://m.qpic.cn"),window.logs.img.load[o]=!0,o;
},
onerror:function(e){
if(e&&e.isCDN()){
var t=10;
/tp\=webp/.test(e)&&(t=11);
var o=new Image;
o.src="http://mp.weixin.qq.com/mp/jsreport?key="+t+"&content="+encodeURIComponent(e)+"&r="+Math.random();
}
},
detect:function(e){
if(e&&e.time&&e.loadList){
var t=e.time,o=e.loadList;
window.logs.img.download[t]=o;
}
},
container:document.getElementById("page-content")
});
}),e("appmsg/async.js");
var g=e("biz_common/dom/event.js"),u=e("biz_wap/jsapi/core.js");
!function(){
var e=document.getElementById("post-user"),t=document.getElementById("copyright_info"),o=[];
e&&o.push({
dom:e,
username:user_name_new||user_name
}),t&&source_username&&o.push({
dom:t,
username:source_username
});
for(var n=0,i=o.length;i>n;n++)!function(e){
g.on(e.dom,"click",function(){
if(u.invoke("profile",{
username:e.username,
scene:"57"
}),"copyright_info"==e.dom.id&&source_username){
var t=["/mp/copyrightreport?action=report&biz=",biz,"&ori_username=",source_username,"&user_uin=",user_uin,"&uin=",uin,"&key=",key,"&pass_ticket=",pass_ticket].join(""),o=new Image;
o.src=t;
}
return!1;
}),m.isWp&&e.dom.setAttribute("href","weixin://profile/"+e.username);
}(o[n]);
}(),function(){
location.href.match(/fontScale=\d+/)&&m.isIOS&&u.on("menu:setfont",function(e){
e.fontScale<=0&&(e.fontScale=100),document.getElementsByTagName("html").item(0).style.webkitTextSizeAdjust=e.fontScale+"%",
document.getElementsByTagName("html").item(0).style.lineHeight=160/e.fontScale;
});
}();
var w=e("appmsg/outer_link.js");
if(new w({
container:document.getElementById("js_content"),
changeHref:function(e,t){
if(e&&0==e.indexOf("http://mp.weixin.qq.com/s"))e=e.replace(/#rd\s*$/,"#wechat_redirect");else if(0!=e.indexOf("http://mp.weixin.qq.com/mp/redirect"))return"http://"+location.host+"/mp/redirect?url="+encodeURIComponent(e)+"&action=appmsg_redirect&uin="+uin+"&biz="+biz+"&mid="+mid+"&idx="+idx+"&type="+t+"&scene=0";
return e;
}
}),!c){
var f=e("appmsg/review_image.js");
new f({
container:document.getElementById("js_content"),
is_https_res:is_https_res
});
}
e("appmsg/iframe.js"),e("appmsg/cdn_speed_report.js"),e("appmsg/page_pos.js"),setTimeout(function(){
g.tap(document.getElementById("copyright_logo"),function(){
location.href="http://kf.qq.com/touch/sappfaq/150211YfyMVj150326iquI3e.html";
}),e("appmsg/report_and_source.js"),function(){
if(c){
var t=e("biz_common/dom/class.js");
t.addClass(o,"not_in_mm");
var n=document.createElement("link");
n.rel="stylesheet",n.type="text/css",n.async=!0,n.href=not_in_mm_css;
var i=document.getElementsByTagName("head")[0];
i.appendChild(n);
var r=document.getElementById("js_pc_qr_code_img");
if(r){
var s=10000004,a=document.referrer;
0==a.indexOf("http://weixin.sogou.com")?s=10000001:0==a.indexOf("https://wx.qq.com")&&(s=10000003),
r.setAttribute("src","/mp/qrcode?scene="+s+"&size=102&__biz="+biz),document.getElementById("js_pc_qr_code").style.display="block";
var m=new Image;
m.src="/mp/report?action=pcclick&__biz="+biz+"&uin="+uin+"&scene="+s+"&r="+Math.random();
}
var p=document.getElementById("js_profile_qrcode"),l=document.getElementById("js_profile_arrow_wrp"),d=document.getElementById("post-user");
if(p&&d&&l){
var A=function(){
var e=10000005,t=document.referrer;
0==t.indexOf("http://weixin.sogou.com")?e=10000006:0==t.indexOf("https://wx.qq.com")&&(e=10000007);
var o=document.getElementById("js_profile_qrcode_img");
o&&o.setAttribute("src","/mp/qrcode?scene="+e+"&size=102&__biz="+biz),p.style.display="block";
var n=new Image;
return n.src="/mp/report?action=pcclick&__biz="+biz+"&uin="+uin+"&scene="+e+"&r="+Math.random(),
l.style.left=d.offsetLeft-p.offsetLeft+d.offsetWidth/2-8+"px",!1;
};
g.on(d,"click",A),g.on(p,"click",A),g.on(document,"click",function(e){
var t=e.target||e.srcElement;
t!=d&&t!=p&&(p.style.display="none");
});
}
}else{
var u=document.getElementById("js_report_article");
!!u&&(u.style.display="");
}
}(),function(){
var e=location.href.indexOf("scrolltodown")>-1?!0:!1,t=document.getElementById("img-content");
if(e&&t&&t.getBoundingClientRect){
var o=t.getBoundingClientRect().height;
window.scrollTo(0,o);
}
}(),e("appmsg/report.js");
for(var t=document.getElementsByTagName("map"),n=0,i=t.length;i>n;++n)t[n].parentNode.removeChild(t[n]);
},1e3);
});