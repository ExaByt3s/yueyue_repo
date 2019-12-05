define("a/gotoappdetail.js",["biz_common/dom/event.js","biz_common/utils/report.js","biz_wap/utils/ajax.js","biz_common/dom/class.js","biz_wap/jsapi/core.js"],function(t){
"use strict";
function a(t){
"undefined"!=typeof d&&d.log&&d.log(t);
}
function e(t,a){
o("http://mp.weixin.qq.com/mp/ad_report?action=follow&type="+t+a.report_param);
}
function n(t){
var n=t.btn,o=t.js_app_rating;
if(!n)return!1;
var _={},u=t.adData,f="",v="",g=u.md5sum,j="",b=t.pos_type||0;
if(function(){
var t=1*u.app_rating;
t>5&&(t=5),0>t&&(t=0);
var a=["","one","two","three","four","five"],e="",n=Math.floor(t);
if(e="star_"+a[n],t>n&&(t=n+.5,e+="_half"),o&&t>0){
var i=o.getElementsByClassName("js_stars"),r=o.getElementsByClassName("js_scores");
i&&r&&i[0]&&r[0]&&(i=i[0],r=r[0],r.innerHTML=t,i.style.display="inline-block",s.addClass(i,e));
}
}(),"104"==u.pt){
var h=u.androiddownurl;
if(v=f=u.channel_id||"",h&&h.match){
var y=/&channelid\=([^&]*)/,w=h.match(y);
w&&w[1]&&(f=w[1],u.androiddownurl=h.replace(y,""));
}
f&&(f="&channelid="+f),t.via&&(j=["&via=ANDROIDWX.YYB.WX.ADVERTISE",t.via].join("."));
}
d.ready(function(){
"104"==u.pt&&(d.invoke("getInstallState",{
packageName:c
},function(t){
var e=t.err_msg;
a("getInstallState @yingyongbao : "+e);
var n=e.lastIndexOf("_")+1,i=e.substring(n);
1*i>=m&&e.indexOf("get_install_state:yes")>-1&&(l=!0);
}),d.invoke("getInstallState",{
packageName:u.pkgname
},function(t){
var e=t.err_msg;
a("getInstallState @"+u.pkgname+" : "+e);
var i=e.lastIndexOf("_")+1,o=e.substring(i);
1*o>=u.versioncode&&e.indexOf("get_install_state:yes")>-1&&(p=!0,n.innerHTML="已安装",
s.removeClass(n,"btn_download"),s.addClass(n,"btn_installed"));
})),i.on(n,"click",function(){
if(a("click @js_app_action"),p&&"104"==u.pt)return!1;
var n=function(){
if("104"==u.pt)return l?(e(24,t),void(location.href="tmast://download?oplist=1;2&pname="+u.pkgname+f+j)):(e(25,t),
void(location.href="http://mp.weixin.qq.com/mp/ad_app_info?t=ad/app_detail&app_id="+u.app_id+(t.appdetail_params||"")+"&channel_id="+v+"&md5sum="+g+"&auto=1#wechat_redirect"));
if("103"==u.pt){
e(23,t);
var a="http://"+location.host+"/mp/ad_redirect?url="+encodeURIComponent(u.appinfo_url)+"&uin="+uin+"&ticket="+(t.ticket||window.ticket);
location.href=a;
}
};
return u.rl&&u.traceid?_[u.traceid]||(_[u.traceid]=!0,r({
url:"/mp/advertisement_report?report_type=2&type="+u.type+"&url="+encodeURIComponent(u.androiddownurl)+"&tid="+u.traceid+"&rl="+encodeURIComponent(u.rl)+"&pos_type="+b+"&__biz="+biz+"&pt="+u.pt+"&r="+Math.random(),
type:"GET",
timeout:1e3,
complete:function(){
_[u.traceid]=!1,n();
},
async:!0
})):n(),!1;
});
});
}
var i=t("biz_common/dom/event.js"),o=t("biz_common/utils/report.js"),r=t("biz_wap/utils/ajax.js"),s=t("biz_common/dom/class.js"),p=!1,d=t("biz_wap/jsapi/core.js"),l=!1,c="com.tencent.android.qqdownloader",m=1060125;
return n;
});define("a/ios.js",["biz_common/dom/event.js","biz_common/utils/report.js","biz_wap/utils/ajax.js","biz_wap/jsapi/core.js"],function(t){
"use strict";
function i(t){
"undefined"!=typeof WeixinJSBridge&&WeixinJSBridge.log&&WeixinJSBridge.log(t);
}
function o(t,i){
n("http://mp.weixin.qq.com/mp/ad_report?action=follow&type="+t+i.report_param);
}
function e(t){
var e=t.btn;
if(!e)return!1;
var n=t.adData,p=!1,c={};
t.report_param=t.report_param||"";
var d="http://"+location.host+"/mp/ad_redirect?url="+encodeURIComponent(n.appinfo_url)+"&uin"+uin+"&ticket="+(t.ticket||window.ticket);
r.on(e,"click",function(){
if(i("click @js_app_action"),p)return i("is_app_installed"),o(n.is_appmsg?17:13,t),
void(location.href=n.app_id+"://");
var e=function(){
i("download"),o(n.is_appmsg?15:11,t),i("go : "+d),location.href=d;
};
return i("download"),n.rl&&n.traceid?c[n.traceid]||(c[n.traceid]=!0,a({
url:"/mp/advertisement_report?report_type=2&type="+n.type+"&url="+encodeURIComponent(n.appinfo_url)+"&tid="+n.traceid+"&rl="+encodeURIComponent(n.rl)+"&pt="+n.pt+t.report_param,
type:"GET",
timeout:1e3,
complete:function(){
i("ready to download"),c[n.traceid]=!1,e();
},
async:!0
})):e(),!1;
});
}
{
var r=t("biz_common/dom/event.js"),n=t("biz_common/utils/report.js"),a=t("biz_wap/utils/ajax.js");
t("biz_wap/jsapi/core.js");
}
return e;
});define("a/android.js",["biz_common/dom/event.js","biz_common/utils/report.js","biz_wap/utils/ajax.js","biz_wap/jsapi/core.js"],function(n){
"use strict";
function a(n){
"undefined"!=typeof d&&d.log&&d.log(n);
}
function e(n,a){
o("http://mp.weixin.qq.com/mp/ad_report?action=follow&type="+n+a.report_param);
}
function t(n){
function t(){
d.invoke("getInstallState",{
packageName:s.pkgname
},function(n){
var a=n.err_msg;
a.indexOf("get_install_state:yes")>-1&&(window.clearInterval(y),g=!0,r.innerHTML=T.installed);
});
}
function o(){
j&&d.invoke("queryDownloadTask",{
download_id:j
},function(t){
if(t&&t.state){
if("download_succ"==t.state){
a("download_succ"),e(s.is_appmsg?18:14,n),window.clearInterval(b),k=!1,I=!0,r.innerHTML=T.downloaded;
var o=document.createEvent("MouseEvents");
o.initMouseEvent("click",!0,!0,window,0,0,0,0,0,!1,!1,!1,!1,0,null),r.dispatchEvent(o);
}
if("downloading"==t.state)return;
("download_fail"==t.state||"default"==t.state)&&(a("fail, download_state : "+t.state),
window.clearInterval(b),k=!1,alert("下载失败"),r.innerHTML=T.download);
}
});
}
var r=n.btn;
if(!r)return!1;
var l={},s=n.adData,c="",u="",m=s.androiddownurl;
if(m&&m.match){
var _=/&channelid\=([^&]*)/,p=m.match(_);
p&&p[1]&&(c="&channelid="+p[1],s.androiddownurl=m.replace(_,""));
}
n.via&&(u=["&via=ANDROIDWX.YYB.WX.ADVERTISE",n.via].join("."));
var f=!1,w="com.tencent.android.qqdownloader",v=1060125,g=!1,k=!1,I=!1,j=0,b=null,y=null,T={
download:"下载",
downloading:"下载中",
downloaded:"安装",
installed:"已安装"
};
r.innerHTML=T.download,d.ready(function(){
d.invoke("getInstallState",{
packageName:w
},function(n){
var e=n.err_msg;
a("getInstallState @yingyongbao : "+e);
var t=e.lastIndexOf("_")+1,o=e.substring(t);
1*o>=v&&e.indexOf("get_install_state:yes")>-1&&(f=!0);
}),d.invoke("getInstallState",{
packageName:s.pkgname
},function(n){
var e=n.err_msg;
a("getInstallState @"+s.pkgname+" : "+e);
var t=e.lastIndexOf("_")+1,o=e.substring(t);
1*o>=s.versioncode&&e.indexOf("get_install_state:yes")>-1&&(g=!0,r.innerHTML=T.installed);
}),r.addEventListener("click",function(){
if(a("click @js_app_action"),!k){
if(g)return!1;
if(I)return d.invoke("installDownloadTask",{
download_id:j,
file_md5:s.md5sum
},function(n){
var e=n.err_msg;
a("installDownloadTask : "+e),e.indexOf("install_download_task:ok")>-1?y=setInterval(t,1e3):alert("安装失败！");
}),!1;
var m=function(){
return f?(e(s.is_appmsg?16:12,n),void(location.href="tmast://download?oplist=1,2&pname="+s.pkgname+c+u)):void d.invoke("addDownloadTask",{
task_name:s.appname,
task_url:s.androiddownurl,
extInfo:n.task_ext_info,
file_md5:s.md5sum
},function(t){
var i=t.err_msg;
a("addDownloadTask : "+i),i.indexOf("add_download_task:ok")>-1?(e(s.is_appmsg?15:11,n),
k=!0,j=t.download_id,a("download_id : "+j),r.innerHTML=T.downloading,b=setInterval(o,1e3)):alert("调用下载器失败！");
});
};
return s.rl&&s.traceid?l[s.traceid]||(l[s.traceid]=!0,i({
url:"/mp/advertisement_report?report_type=2&type="+s.type+"&url="+encodeURIComponent(s.androiddownurl)+"&tid="+s.traceid+"&rl="+encodeURIComponent(s.rl)+"&__biz="+biz+"&pt="+s.pt+"&r="+Math.random(),
type:"GET",
timeout:1e3,
complete:function(){
l[s.traceid]=!1,m();
},
async:!0
})):m(),!1;
}
});
});
}
var o=(n("biz_common/dom/event.js"),n("biz_common/utils/report.js")),i=n("biz_wap/utils/ajax.js"),d=n("biz_wap/jsapi/core.js");
return t;
});define("a/profile.js",["biz_common/dom/event.js","biz_common/utils/report.js","biz_wap/utils/ajax.js","biz_wap/jsapi/core.js"],function(e){
"use strict";
function t(e,t){
a("http://mp.weixin.qq.com/mp/ad_report?action=follow&type="+e+t.report_param);
}
function n(e){
location.href=e;
}
function i(e){
var i=e.adData,s=e.pos_type||0,c={};
e.report_param=e.report_param||"",function(){
function m(){
var e=u.dataset;
if(e.rl&&e.url&&e.type&&e.tid){
var t=e.tid,n=e.type,i=e.url,o=e.rl;
c[t]||(c[t]=!0,r({
url:"/mp/advertisement_report?report_type=2&type="+n+"&url="+encodeURIComponent(i)+"&tid="+t+"&pos_type="+s+"&rl="+encodeURIComponent(o)+"&uin="+uin+"&key="+key+"&__biz="+biz+"&pt=100&r="+Math.random(),
type:"GET",
timeout:1e3,
complete:function(){
c[t]=!1,_();
},
async:!0
}));
}else _();
}
var u=e.btnAddContact,d=e.btnViewProfile;
if(u&&u.dataset){
var l=function(o,r){
var s=o.err_msg,c=i.is_appmsg?6:1;
-1!=s.indexOf("ok")?(d.style.display="inline-block",u.style.display="none",c=i.is_appmsg?9:4):"add_contact:added"==s?c=i.is_appmsg?7:2:"add_contact:cancel"==s?c=i.is_appmsg?8:3:(--r,
r>=0?p.invoke("addContact",{
scene:scene,
webtype:"1",
username:i.usename
},function(e){
l(e,r);
}):(s="addContact:fail|msg:"+s+"|uin:"+uin+"|biz:"+biz,a("http://mp.weixin.qq.com/mp/jsreport?key=13&content="+s+"&r="+Math.random()),
n(i.url))),t(c,e);
},_=function(){
t(i.is_appmsg?10:5,e),p.invoke("addContact",{
scene:scene,
webtype:"1",
username:i.usename
},function(e){
l(e,1);
});
};
o.on(u,"click",m);
}
}(),function(){
var t=e.btnViewProfile;
t&&o.on(t,"click",function(){
return n(i.url),!1;
});
}();
}
var o=e("biz_common/dom/event.js"),a=e("biz_common/utils/report.js"),r=e("biz_wap/utils/ajax.js"),p=e("biz_wap/jsapi/core.js");
return i;
});define("biz_common/utils/report.js",[],function(){
"use strict";
return function(n){
var e=new Image;
e.src=n;
};
});define("biz_common/utils/cookie.js",[],function(){
"use strict";
var e={
get:function(e){
if(""==e)return"";
var t=new RegExp(e+"=([^;]*)"),n=document.cookie.match(t);
return n&&n[1]||"";
},
set:function(e,t){
var n=new Date;
n.setDate(n.getDate()+1);
var r=n.toGMTString();
return document.cookie=e+"="+t+";expires="+r,!0;
}
};
return e;
});define("appmsg/reward_entry.js",["biz_common/dom/event.js","biz_wap/utils/ajax.js"],function(e){
"use strict";
function n(e){
e&&(e.style.display="block");
}
function t(e){
e&&(e.style.display="none");
}
function o(e){
var o=window.innerWidth||document.documentElement.innerWidth,r=(Math.ceil((m-188)/42)+1)*Math.floor((o-15)/42);
l="/mp/reward?act=getrewardheads&__biz="+biz+"&appmsgid="+mid+"&idx="+idx+"&sn="+sn+"&offset=0&count="+r+"#wechat_redirect";
var i="&uin="+encodeURIComponent(window.uin)+"&key="+encodeURIComponent(window.key)+"&pass_ticket="+encodeURIComponent(window.pass_ticket),w=document.getElementById("js_reward_link");
w&&(w.href="https://mp.weixin.qq.com/bizmall/reward?__biz="+biz+"&appmsgid="+mid+"&idx="+idx+"&sn="+sn+"&timestamp="+e.timestamp+"&showwxpaytitle=1"+i),
u=e.reward_head_imgs,_=e.self_head_img;
var p=d();
c.reward&&1==e.can_reward?(n(c.reward),s.on(window,"load",function(){
s.on(window,"scroll",a);
})):t(c.reward);
var f=document.getElementById("js_reward_inner");
f&&p>0&&n(f);
var g=document.getElementById("js_reward_total");
g&&(g.innerText=e.reward_total,g.setAttribute("href",l));
}
function r(e,n){
var t=document.createElement("span");
t.className="reward_user_avatar";
var o=new Image;
return o.onload=function(){
window.logs&&window.logs.reward_heads_total++,o.onload=o.onerror=null;
},o.onerror=function(){
window.logs&&window.logs.reward_heads_total++,window.logs&&window.logs.reward_heads_fail++,
o.onload=o.onerror=null;
},o.src=n,t.appendChild(o),e.appendChild(t),t;
}
function d(){
if(u.length||_){
var e=document.getElementById("js_reward_list"),n=0,t=document.createDocumentFragment();
if(e){
_&&(n++,r(t,_));
for(var o=0,d=u.length;d>o&&(n++,r(t,u[o]),n!=3*i);++o);
n>i&&(e.className+=" tl"),e.innerHTML="",e.appendChild(t);
}
return n;
}
}
function a(){
var e=window.pageYOffset||document.documentElement.scrollTop;
e+m>c.reward.offsetTop&&(w({
type:"GET",
url:"/bizmall/reward?act=report&__biz="+biz+"&appmsgid="+mid+"&idx="+idx,
async:!0
}),s.off(window,"scroll",a),a=null);
}
var i,l,s=e("biz_common/dom/event.js"),w=e("biz_wap/utils/ajax.js"),m=window.innerHeight||document.documentElement.clientHeight,c={
reward:document.getElementById("js_reward_area")
},u=[],_=null;
return window.logs&&(window.logs.reward_heads_total=0,window.logs.reward_heads_fail=0),
{
handle:function(e,n){
i=n,o(e);
},
render:function(e){
i=e,d();
}
};
});define("appmsg/comment.js",["biz_common/dom/event.js","biz_common/dom/class.js","biz_wap/utils/ajax.js","biz_common/utils/string/html.js","biz_common/tmpl.js"],function(e){
"use strict";
function t(e,t){
e.style.display=t?t:"block";
}
function n(e){
e.style.display="none";
}
function m(){
setTimeout(function(){
t(z.toast);
},750),setTimeout(function(){
n(z.toast);
},1500);
}
function o(e){
return e.replace(/^\s+|\s+$/g,"");
}
function i(){
clearTimeout(k),k=setTimeout(function(){
if(!v&&-1!=I){
var e=window.innerHeight||document.documentElement.clientHeight,m=window.pageYOffset||document.documentElement.scrollTop,o=document.documentElement.scrollHeight;
if(!(I>0&&o-m-e>500)){
v=!0,n(z.tips),t(z.loading);
var i="/mp/appmsg_comment?action=getcomment&__biz="+biz+"&appmsgid="+appmsgid+"&idx="+idx+"&comment_id="+comment_id+"&offset="+I+"&limit="+E;
try{
N++,N>1&&((new Image).src="http://mp.weixin.qq.com/mp/jsreport?key=27&content="+encodeURIComponent(i)),
D.indexOf(i)>-1&&((new Image).src="http://mp.weixin.qq.com/mp/jsreport?key=25&content="+encodeURIComponent(i)),
D.push(i);
}catch(s){}
b({
url:i,
type:"get",
success:function(e){
var t={};
try{
t=window.eval.call(window,"("+e+")");
}catch(n){}
var m=t.base_resp&&t.base_resp.ret;
0==m?c(t):w.src="http://mp.weixin.qq.com/mp/jsreport?key=18&content=type:resperr;url:"+encodeURIComponent(i)+";ret="+m+"&r="+Math.random();
},
error:function(){
w.src="http://mp.weixin.qq.com/mp/jsreport?key=18&content=type:ajaxerr;url:"+encodeURIComponent(i)+"&r="+Math.random();
},
complete:function(){
v=!1,n(z.loading);
}
});
}
}
},50);
}
function c(e){
var m,o=document.createDocumentFragment();
O++,O>1&&(M.src="http://mp.weixin.qq.com/mp/jsreport?key=26&content="+encodeURIComponent(JSON.stringify({
comment_id:comment_id,
offset:I,
url:location.href
}))),0==I?(C=e.logo_url,B=e.nick_name,m=e.elected_comment,m&&m.length?(l(m,o,"elected"),
z.list.appendChild(o),t(z.main),1!=e.is_fans?t(document.getElementById("js_cmt_nofans1"),"block"):t(document.getElementById("js_cmt_addbtn1")),
e.elected_comment_total_cnt<=10&&t(document.getElementById("js_cmt_statement"))):(n(z.main),
t(1!=e.is_fans?document.getElementById("js_cmt_nofans2"):document.getElementById("js_cmt_addbtn2"))),
function(){
var e=location.href.indexOf("scrolltodown")>-1?!0:!1,t=(document.getElementById("img-content"),
document.getElementById("js_cmt_area"));
if(e&&t&&t.offsetTop){
var n=t.offsetTop;
window.scrollTo(0,n-25);
}
}()):(m=e.elected_comment,m&&m.length&&(l(m,o,"elected"),z.list.appendChild(o))),
0==e.elected_comment_total_cnt?(I=-1,y.off(window,"scroll",i),n(document.getElementById("js_cmt_loading")),
n(document.getElementById("js_cmt_statement"))):I+E>=e.elected_comment_total_cnt?(I=-1,
y.off(window,"scroll",i),n(document.getElementById("js_cmt_loading")),t(document.getElementById("js_cmt_statement"))):I+=e.elected_comment.length;
}
function s(){
var e=o(z.input.value);
if(!h.hasClass(z.submit,"btn_disabled")){
if(e.length<1)return d("评论不能为空");
if(e.length>600)return d("字数不能多于600个");
h.addClass(z.submit,"btn_disabled");
var n=document.getElementById("activity-name"),i="/mp/appmsg_comment?action=addcomment&comment_id="+comment_id+"&__biz="+biz+"&idx="+idx+"&appmsgid="+appmsgid+"&sn="+sn;
b({
url:i,
data:{
content:e,
title:n&&o(n.innerText),
head_img:C,
nickname:B
},
type:"POST",
success:function(n){
var o={},c=document.createDocumentFragment();
try{
o=window.eval.call(window,"("+n+")");
}catch(s){}
switch(+o.ret){
case 0:
m(),l([{
content:e,
nick_name:B,
create_time:(new Date).getTime()/1e3|0,
is_elected:0,
logo_url:C,
like_status:0,
content_id:0,
like_num_format:0,
like_num:0,
is_from_friend:0,
is_from_me:1,
my_id:o.my_id
}],c,"mine"),z.mylist.insertBefore(c,z.mylist.firstChild),t(z.mylist.parentNode),
z.input.value="";
break;

case-6:
d("你评论的太频繁了，休息一下吧");
break;

case-7:
d("你还未关注该公众号，不能参与评论");
break;

case-10:
d("字数不能多于600个");
break;

case-15:
d("评论已关闭");
break;

default:
d("系统错误，请重试");
}
0!=o.ret&&(w.src="http://mp.weixin.qq.com/mp/jsreport?key=19&content=type:resperr;url:"+encodeURIComponent(i)+";ret="+o.ret+"&r="+Math.random());
},
error:function(){
w.src="http://mp.weixin.qq.com/mp/jsreport?key=19&content=type:ajaxerr;url:"+encodeURIComponent(i)+"&r="+Math.random();
},
complete:function(){
""!=z.input.value&&h.removeClass(z.submit,"btn_disabled");
}
});
}
}
function a(){
if(0==x){
var e="/mp/appmsg_comment?action=getmycomment&__biz="+biz+"&appmsgid="+appmsgid+"&idx="+idx+"&comment_id="+comment_id,m=document.getElementById("js_mycmt_loading");
x=1,t(m),b({
url:e,
type:"get",
success:function(n){
var m={};
try{
m=window.eval.call(window,"("+n+")");
}catch(o){}
var i=m.base_resp&&m.base_resp.ret;
if(0==i){
var c=m.my_comment,s=document.createDocumentFragment();
c&&c.length&&(l(c,s,"mine"),z.mylist.appendChild(s),t(z.mylist.parentNode)),x=2;
}else x=0,w.src="http://mp.weixin.qq.com/mp/jsreport?key=18&content=type:resperr;url:"+encodeURIComponent(e)+";ret="+i+"&r="+Math.random();
},
error:function(){
x=0,w.src="http://mp.weixin.qq.com/mp/jsreport?key=18&content=type:ajaxerr;url:"+encodeURIComponent(e)+"&r="+Math.random();
},
complete:function(){
n(m);
}
});
}
}
function r(e){
var t=(new Date).getTime(),n=new Date;
n.setDate(n.getDate()+1),n.setHours(0),n.setMinutes(0),n.setSeconds(0),n=n.getTime();
var m=t/1e3-e,o=n/1e3-e,i=new Date(n).getFullYear(),c=new Date(1e3*e);
return 3600>m?Math.ceil(m/60)+"分钟前":86400>o?Math.floor(m/60/60)+"小时前":172800>o?"昨天":604800>o?Math.floor(o/24/60/60)+"天前":c.getFullYear()==i?c.getMonth()+1+"月"+c.getDate()+"日":c.getFullYear()+"年"+(c.getMonth()+1)+"月"+c.getDate()+"日";
}
function l(e,t,n){
var m,o="",i=document.createElement("div"),c="http://mmbiz.qpic.cn/mmbiz/ByCS3p9sHiak6fjSeA7cianwo25C0CIt5ib8nAcZjW7QT1ZEmUo4r5iazzAKhuQibEXOReDGmXzj8rNg/0";
q={};
for(var s,a=0;s=e[a];++a){
s.time=r(s.create_time),s.status="",s.logo_url=s.logo_url||c,s.logo_url=-1!=s.logo_url.indexOf("wx.qlogo.cn")?s.logo_url.replace(/\/132$/,"/96"):s.logo_url,
s.content=s.content.htmlDecode().htmlEncode(),s.nick_name=s.nick_name.htmlDecode().htmlEncode(),
s.like_num_format=parseInt(s.like_num)>=1e4?(s.like_num/1e4).toFixed(1)+"万":s.like_num,
s.is_from_friend=s.is_from_friend||0,s.is_from_me="mine"==n?1:s.is_from_me||0,s.reply=s.reply||{
reply_list:[]
},s.is_mine=n?!1:!0,s.is_elected="elected"==n?1:s.is_elected,s.reply.reply_list.length>0&&(s.reply.reply_list[0].time=r(s.reply.reply_list[0].create_time)),
o+=j.render("t_cmt",s);
try{
var l=s.nick_name+s.content,d=!1,_=23;
q[l]&&(d=!0,_=24),T.indexOf(s.content_id)>-1&&(d=!0,_=23),T.push(s.content_id),q[l]=!0,
d&&(M.src="http://mp.weixin.qq.com/mp/jsreport?key="+_+"&content="+encodeURIComponent(JSON.stringify({
comment_id:comment_id,
content_id:s.content_id,
offset:I,
length:e.length,
url:location.href
})));
}catch(p){}
}
for(i.innerHTML=o;m=i.children.item(0);)t.appendChild(m);
}
function d(e){
return setTimeout(function(){
alert(e);
});
}
function _(e){
"#comment"==location.hash?(n(z.article),t(z.mine),window.scrollTo(0,0),a()):"onload"!=e&&(n(z.mine),
t(z.article),window.scrollTo(0,document.documentElement.scrollHeight)),z.input.blur();
}
function p(e){
var t=e.target||e.srcElement,n=null;
if(h.hasClass(t,"js_comment_praise")&&(n=t),h.hasClass(t,"icon_praise_gray")&&"i"==t.nodeName.toLowerCase()&&(n=t.parentElement),
h.hasClass(t,"praise_num")&&"span"==t.nodeName.toLowerCase()&&(n=t.parentElement),
n){
var m=parseInt(n.dataset.status),o=0==m?1:0,i=n.dataset.contentId,c="/mp/appmsg_comment?action=likecomment&&like="+o+"&__biz="+biz+"&appmsgid="+appmsgid+"&comment_id="+comment_id+"&content_id="+i;
u(n),b({
url:c,
type:"GET"
});
}
}
function u(e){
var t=h.hasClass(e,"praised"),n=e.querySelector(".praise_num"),m=n.innerHTML,o=m.indexOf("万"),i=parseInt(m)?parseInt(m):0;
t?(-1==o&&(n.innerHTML=i-1>0?i-1:""),h.removeClass(e,"praised"),e.dataset.status=0):(-1==o&&(n.innerHTML=i+1),
h.addClass(e,"praised"),e.dataset.status=1);
}
function g(e){
var m=e.delegatedTarget,o=m.getAttribute("data-my-id"),i="/mp/appmsg_comment?action=delete&__biz="+biz+"&appmsgid="+appmsgid+"&comment_id="+comment_id+"&my_id="+o;
confirm("确定删除吗？")&&(b({
url:i,
success:function(e){
var i,c=m;
try{
e=JSON.parse(e);
}catch(s){
e={};
}
if(0==e.ret){
for(;c&&(c.nodeType!=c.ELEMENT_NODE||"li"!=c.tagName.toLowerCase());)c=c.parentNode;
c&&(c.parentNode.removeChild(c),i=document.getElementById("cid"+o),i&&i.parentNode.removeChild(i),
0==z.list.children.length&&(n(z.main),n(document.getElementById("js_cmt_statement")),
t(document.getElementById("js_cmt_addbtn2"))),0==z.mylist.children.length&&n(z.mylist.parentNode));
}else alert("删除失败，请重试");
},
error:function(){
alert("网络错误，请重试");
}
}),z.input.focus());
}
var f=document.getElementById("js_cmt_area");
if(0!=comment_id&&uin&&key){
if(-1==navigator.userAgent.indexOf("MicroMessenger"))return void(f&&(f.style.display="none"));
f&&(f.style.display="block");
var y=e("biz_common/dom/event.js"),h=e("biz_common/dom/class.js"),b=e("biz_wap/utils/ajax.js"),j=(e("biz_common/utils/string/html.js"),
e("biz_common/tmpl.js")),w=new Image,I=0,E=50,v=!1,k=null,C="",B="我",x=0,z={
article:document.getElementById("js_article"),
more:document.getElementById("js_cmt_more"),
mine:document.getElementById("js_cmt_mine"),
main:document.getElementById("js_cmt_main"),
input:document.getElementById("js_cmt_input"),
submit:document.getElementById("js_cmt_submit"),
addbtn:document.getElementById("js_cmt_addbtn"),
list:document.getElementById("js_cmt_list"),
mylist:document.getElementById("js_cmt_mylist"),
morelist:document.getElementById("js_cmt_morelist"),
toast:document.getElementById("js_cmt_toast"),
tips:document.getElementById("js_cmt_tips"),
loading:document.getElementById("js_cmt_loading")
},T=[],q={},M=new Image,D=[],N=0,O=0;
!function(){
i(),_("onload");
}(),y.on(window,"hashchange",_),y.on(z.input,"input",function(){
var e=o(z.input.value);
e.length<1?h.addClass(z.submit,"btn_disabled"):h.removeClass(z.submit,"btn_disabled");
}),y.on(z.more,"touchend",p),y.on(z.list,"touchend",p),y.on(z.mylist,"touchend",p),
y.on(z.list,"tap",".js_del",g),y.on(z.mylist,"tap",".js_del",g),y.on(z.submit,"touchend",s);
}
});define("appmsg/like.js",["biz_common/dom/event.js","biz_common/dom/class.js","biz_wap/utils/ajax.js"],function(require,exports,module){
"use strict";
function like_report(e){
var tmpAttr=el_like.getAttribute("like"),tmpHtml=el_likeNum.innerHTML,isLike=parseInt(tmpAttr)?parseInt(tmpAttr):0,like=isLike?0:1,likeNum=parseInt(tmpHtml)?parseInt(tmpHtml):0;
ajax({
url:"/mp/appmsg_like?__biz="+biz+"&mid="+mid+"&idx="+idx+"&like="+like+"&f=json&appmsgid="+appmsgid+"&itemidx="+itemidx,
type:"GET",
timeout:2e3,
success:function(res){
var data=eval("("+res+")");
0==data.base_resp.ret&&(isLike?(Class.removeClass(el_like,"praised"),el_like.setAttribute("like",0),
likeNum>0&&"100000+"!==tmpHtml&&(el_likeNum.innerHTML=likeNum-1==0?"赞":likeNum-1)):(el_like.setAttribute("like",1),
Class.addClass(el_like,"praised"),"100000+"!==tmpHtml&&(el_likeNum.innerHTML=likeNum+1)));
},
async:!0
});
}
var DomEvent=require("biz_common/dom/event.js"),Class=require("biz_common/dom/class.js"),ajax=require("biz_wap/utils/ajax.js"),el_toolbar=document.getElementById("js_toobar"),el_like=el_toolbar.querySelector("#like"),el_likeNum=el_toolbar.querySelector("#likeNum"),el_readNum=el_toolbar.querySelector("#readNum");
DomEvent.on(el_like,"click",function(e){
return like_report(e),!1;
});
});define("appmsg/a.js",["biz_common/dom/event.js","biz_common/utils/url/parse.js","biz_wap/utils/ajax.js","a/profile.js","a/android.js","a/ios.js","a/gotoappdetail.js"],function(require,exports,module){
"use strict";
function ad_click(e,a,t,i,o,n,p,s,d,r,_,l){
if(!has_click[o]){
has_click[o]=!0;
var c=document.getElementById("loading_"+o);
c&&(c.style.display="inline"),ajax({
url:"/mp/advertisement_report?report_type=2&type="+e+"&url="+encodeURIComponent(a)+"&tid="+o+"&rl="+encodeURIComponent(t)+"&__biz="+biz+"&pos_type="+r+"&pt="+d+"&r="+Math.random(),
type:"GET",
timeout:1e3,
complete:function(){
if(has_click[o]=!1,c&&(c.style.display="none"),"5"==e)location.href="/mp/profile?source=from_ad&tousername="+a+"&ticket="+n+"&uin="+uin+"&key="+key+"&__biz="+biz+"&mid="+mid+"&idx="+idx+"&tid="+o;else{
if(0==a.indexOf("https://itunes.apple.com/")||0==a.indexOf("http://itunes.apple.com/"))a="http://"+location.host+"/mp/ad_redirect?url="+encodeURIComponent(a)+"&ticket="+n+"&uin="+uin;else if(-1==a.indexOf("mp.weixin.qq.com"))a="http://"+location.host+"/mp/redirect?url="+encodeURIComponent(a);else if(-1==a.indexOf("mp.weixin.qq.com/s")&&-1==a.indexOf("mp.weixin.qq.com/mp/appmsg/show")){
var t={
source:4,
tid:o,
idx:idx,
mid:mid,
appuin:biz,
pt:d,
aid:s,
ad_engine:_,
pos_type:r
};
if("104"==d&&l){
var i=l.pkgname&&l.pkgname.replace(/\./g,"_");
t={
source:4,
traceid:o,
mid:mid,
idx:idx,
appuin:biz,
pt:d,
aid:s,
engine:_,
pos_type:r,
pkgname:i
};
}
a=URL.join(a,t);
}
location.href=a;
}
},
async:!0
});
}
}
var js_bottom_ad_area=document.getElementById("js_bottom_ad_area"),js_top_ad_area=document.getElementById("js_top_ad_area"),pos_type=window.pos_type||0,adDatas=window.adDatas,total_pos_type=2,el_gdt_areas={
pos_1:js_top_ad_area,
pos_0:js_bottom_ad_area
},gdt_as={
pos_1:js_top_ad_area.getElementsByClassName("js_ad_link"),
pos_0:js_bottom_ad_area.getElementsByClassName("js_ad_link")
};
if(!document.getElementsByClassName||-1==navigator.userAgent.indexOf("MicroMessenger"))return js_top_ad_area.style.display="none",
js_bottom_ad_area.style.display="none",!1;
var has_click={},DomEvent=require("biz_common/dom/event.js"),URL=require("biz_common/utils/url/parse.js"),ajax=require("biz_wap/utils/ajax.js"),ping_apurl={
pos_0:!1,
pos_1:!1
},innerHeight=window.innerHeight||document.documentElement.clientHeight,ad_engine=0;
if(adDatas.num>0){
var onScroll=function(){
for(var scrollTop=window.pageYOffset||document.documentElement.scrollTop,i=0;total_pos_type>i;++i)!function(i){
var pos_key="pos_"+i;
if(!ping_apurl[pos_key]){
var gdt_a=gdt_as[pos_key];
if(gdt_a=!!gdt_a&&gdt_a[0],gdt_a&&gdt_a.dataset&&gdt_a.dataset.apurl){
var gid=gdt_a.dataset.gid,tid=gdt_a.dataset.tid,apurl=gdt_a.dataset.apurl,pos_type=adDatas.ads[pos_key].a_info.pos_type,gdt_area=el_gdt_areas[pos_key],offsetTop=gdt_area.offsetTop;
adDatas.ads[pos_key].ad_engine=0,-1!=apurl.indexOf("ad.wx.com")&&(adDatas.ads[pos_key].ad_engine=1),
(0==pos_type&&scrollTop+innerHeight>offsetTop||1==pos_type&&(10>=scrollTop||scrollTop-10>=offsetTop))&&(ping_apurl[pos_key]=!0,
ajax({
url:"/mp/advertisement_report?report_type=1&tid="+tid+"&adver_group_id="+gid+"&apurl="+encodeURIComponent(apurl)+"&__biz="+biz+"&pos_type="+pos_type+"&r="+Math.random(),
success:function(res){
try{
res=eval("("+res+")");
}catch(e){
res={};
}
res&&0!=res.ret?ping_apurl[pos_key]=!1:ping_apurl.pos_0&&ping_apurl.pos_1&&DomEvent.off(window,"scroll",onScroll);
},
async:!0
}));
}
}
}(i);
};
DomEvent.on(window,"scroll",onScroll),onScroll();
}
for(var i=0;total_pos_type>i;++i)!function(e){
var a="pos_"+e,t=el_gdt_areas[a];
if(!t.getElementsByClassName)return t.style.display="none",!1;
var i=t.getElementsByClassName("js_ad_link")||[],o=adDatas.ads[a];
if(o){
for(var n=o.adData,p=o.a_info,s=p.pos_type,d=o.ad_engine,r=0,_=i.length;_>r;++r)!function(e,a){
var t=i[e],o=t.dataset,n=o.type,p=o.url,r=o.rl,_=o.apurl,l=o.tid,c=o.ticket,m=o.group_id,u=o.aid,g=o.pt;
DomEvent.on(t,"click",function(e){
var t=!!e&&e.target;
return t&&t.className&&-1!=t.className.indexOf("js_ad_btn")?void 0:(ad_click(n,p,r,_,l,c,m,u,g,s,d,a),
!1);
},!0);
}(r,n);
if(n){
n.adid=window.adid||n.adid;
var l="&tid="+n.traceid+"&uin="+uin+"&key="+key+"&__biz="+biz+"&source="+source+"&scene="+scene+"&appuin="+biz+"&aid="+n.adid+"&ad_engine="+d+"&pos_type="+s+"&r="+Math.random();
if("100"==n.pt){
var c=require("a/profile.js");
return void new c({
btnViewProfile:document.getElementById("js_view_profile_"+s),
btnAddContact:document.getElementById("js_add_contact_"+s),
adData:n,
pos_type:s,
report_param:l
});
}
if("102"==n.pt){
var m=require("a/android.js"),u=15,g=n.pkgname&&n.pkgname.replace(/\./g,"_");
return void new m({
btn:document.getElementById("js_app_action_"+s),
adData:n,
report_param:l,
task_ext_info:[n.adid,n.traceid,g,source,u,d].join("."),
via:[n.traceid,n.adid,g,source,u,d].join(".")
});
}
if("101"==n.pt){
var y=require("a/ios.js");
return void new y({
btn:document.getElementById("js_app_action_"+s),
adData:n,
ticket:n.ticket,
report_param:l
});
}
if("103"==n.pt||"104"==n.pt){
var f=require("a/gotoappdetail.js"),u=15,g=n.pkgname&&n.pkgname.replace(/\./g,"_");
return void new f({
btn:document.getElementById("js_appdetail_action_"+s),
js_app_rating:document.getElementById("js_app_rating_"+s),
adData:n,
report_param:l,
pos_type:s,
via:[n.traceid,n.adid,g,source,u,d].join("."),
ticket:n.ticket,
appdetail_params:["&aid="+n.adid,"traceid="+n.traceid,"pkgname="+g,"source="+source,"type="+u,"engine="+d,"appuin="+biz,"pos_type="+s,"ticket="+n.ticket,"scene="+scene].join("&")
});
}
}
}
}(i);
});define("biz_common/tmpl.js",[],function(){
"use strict";
var n=function(n,t){
var r=new Function("obj","var p=[],print=function(){p.push.apply(p,arguments);};with(obj){p.push('"+n.replace(/[\r\t\n]/g," ").split("<#").join("	").replace(/((^|#>)[^\t]*)'/g,"$1\r").replace(/\t=(.*?)#>/g,"',$1,'").split("	").join("');").split("#>").join("p.push('").split("\r").join("\\'")+"');}return p.join('');");
return r(t);
},t=function(t,r){
return n(document.getElementById(t).innerHTML,r);
};
return{
render:t,
tmpl:n
};
});define("biz_common/ui/imgonepx.js",[],function(){
"use strict";
return"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkJDQzA1MTVGNkE2MjExRTRBRjEzODVCM0Q0NEVFMjFBIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkJDQzA1MTYwNkE2MjExRTRBRjEzODVCM0Q0NEVFMjFBIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6QkNDMDUxNUQ2QTYyMTFFNEFGMTM4NUIzRDQ0RUUyMUEiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6QkNDMDUxNUU2QTYyMTFFNEFGMTM4NUIzRDQ0RUUyMUEiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz6p+a6fAAAAD0lEQVR42mJ89/Y1QIABAAWXAsgVS/hWAAAAAElFTkSuQmCC";
});define("biz_common/dom/attr.js",[],function(){
"use strict";
function t(t,e,n){
return"undefined"==typeof n?t.getAttribute(e):t.setAttribute(e,n);
}
function e(t,e,n,r){
t.style.setProperty?(r=r||null,t.style.setProperty(e,n,r)):"undefined"!=typeof t.style.cssText&&(r=r?"!"+r:"",
t.style.cssText+=";"+e+":"+n+r+";");
}
return{
attr:t,
setProperty:e
};
});define("biz_wap/utils/ajax.js",["biz_common/utils/url/parse.js"],function(e){
"use strict";
function t(e){
var t={};
return"undefined"!=typeof uin&&(t.uin=uin),"undefined"!=typeof key&&(t.key=key),
"undefined"!=typeof pass_ticket&&(t.pass_ticket=pass_ticket),o.join(e,t);
}
function n(e){
var n=(e.type||"GET").toUpperCase(),o=t(e.url),r="undefined"==typeof e.async?!0:e.async,s=new XMLHttpRequest,a=null,u=null;
if("object"==typeof e.data){
var i=e.data;
u=[];
for(var c in i)i.hasOwnProperty(c)&&u.push(c+"="+encodeURIComponent(i[c]));
u=u.join("&");
}else u="string"==typeof e.data?e.data:null;
s.open(n,o,r),s.onreadystatechange=function(){
3==s.readyState&&e.received&&e.received(s),4==s.readyState&&(s.onreadystatechange=null,
s.status>=200&&s.status<400?e.success&&e.success(s.responseText):e.error&&e.error(s),
clearTimeout(a),e.complete&&e.complete(),e.complete=null);
},"POST"==n&&s.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8"),
s.setRequestHeader("X-Requested-With","XMLHttpRequest"),"undefined"!=typeof e.timeout&&(a=setTimeout(function(){
s.abort("timeout"),e.complete&&e.complete(),e.complete=null;
},e.timeout));
try{
s.send(u);
}catch(p){
e.error&&e.error();
}
}
var o=e("biz_common/utils/url/parse.js");
return n;
});define("biz_common/utils/string/html.js",[],function(){
"use strict";
return String.prototype.html=function(t){
var e=["&#39;","'","&quot;",'"',"&nbsp;"," ","&gt;",">","&lt;","<","&amp;","&","&yen;","¥"];
t&&e.reverse();
for(var n=0,r=this;n<e.length;n+=2)r=r.replace(new RegExp(e[n],"g"),e[n+1]);
return r;
},String.prototype.htmlEncode=function(){
return this.html(!0);
},String.prototype.htmlDecode=function(){
return this.html(!1);
},String.prototype.getPureText=function(){
return this.replace(/<\/?[^>]*\/?>/g,"");
},{
htmlDecode:function(t){
return t.htmlDecode();
},
htmlEncode:function(t){
return t.htmlEncode();
},
getPureText:function(t){
return t.getPureText();
}
};
});define("appmsg/report.js",["biz_common/dom/event.js","appmsg/cdn_img_lib.js","biz_wap/utils/mmversion.js","biz_common/utils/report.js"],function(e){
"use strict";
function t(){
var t=e("biz_wap/utils/mmversion.js"),o=e("biz_common/utils/report.js"),r=!1,a=window.performance||window.msPerformance||window.webkitPerformance;
return function(){
if(Math.random()<.1){
var e=window.webp?2e3:1e3,n=[];
n.push("1="+e),t.isIOS&&n.push("2="+e),t.isAndroid&&n.push("3="+e);
var i=window.logs.pageinfo.content_length;
if(i&&n.push("4="+i),e=a?2e3:1e3,n.push("5="+e),t.isIOS&&n.push("6="+e),t.isAndroid&&n.push("7="+e),
a){
if(a.memory){
var r=a.memory;
!!r.jsHeapSizeLimit&&n.push("8="+r.jsHeapSizeLimit/1e3),!!r.totalJSHeapSize&&n.push("9="+r.totalJSHeapSize/1e3),
!!r.usedJSHeapSize&&n.push("10="+r.usedJSHeapSize/1e3);
}
if(a.timing){
var s=a.timing,p=s.navigationStart,d=s.responseEnd,g=d-p,m=s.connectEnd==s.fetchStart;
n.push("11="+(m?2e3:1e3)),n.push("12="+g),"wifi"==networkType?n.push("13="+g):"2g/3g"==networkType&&n.push("14="+g);
}
}
o("http://isdspeed.qq.com/cgi-bin/r.cgi?flag1=7839&flag2=7&flag3=8&"+n.join("&"));
}
}(),a&&a.timing?(r=a.timing.navigationStart,function(){
if(!(Math.random()>.5)&&a.getEntries){
for(var e=[],t=a.getEntries(),n=[],i=0,r=t.length;r>i;++i){
var s=t[i],p=s.name;
if(p&&"script"==s.initiatorType&&/^.*(res\.wx\.qq\.com)(.*)\.js$/g.test(p)){
{
var d=s.duration;
s.startTime,s.responseEnd;
}
-1!=p.indexOf("/js/biz_wap/moon")?(d=Math.round(d),e.push("1="+d),"wifi"==networkType?e.push("2="+d):"2g/3g"==networkType&&e.push("3="+d),
e.push("4="+(10>=d?2e3:1e3))):n.push({
s:s.startTime,
e:s.responseEnd,
t:s.duration
});
}else;
}
if(n=n.sort(function(e){
return e.s<e.s?-1:1;
}),n&&n.length>0){
for(var g=0,m=0,u=0,i=0,f=n.length;f>i;++i){
var s=n[i],h=m-s.s;
h>0&&(s.t-=h),h>0&&s.e>m&&(u+=h),g=s.s,m=s.e;
}
u=Math.round(u),e.push("5="+u),"wifi"==networkType?e.push("6="+u):"2g/3g"==networkType&&e.push("7="+u);
}
if("undefined"!=typeof moon){
var c=moon.hit_num,w=moon.mod_num;
e.push("8="+Math.round(1e3+1e3*(c/w)));
}
o("http://isdspeed.qq.com/cgi-bin/r.cgi?flag1=7839&flag2=7&flag3=11&"+e.join("&"));
}
}(),function(){
function e(){
if(-1==i.indexOf("NetType/"))return!1;
for(var e=["2G","cmwap","cmnet","uninet","uniwap","ctwap","ctnet"],t=0,n=e.length;n>t;++t)if(-1!=i.indexOf(e[t]))return!0;
return!1;
}
var t=write_sceen_time-r,n=first_sceen__time-r,a=page_endtime-r;
if(window.logs.pagetime={
wtime:t,
ftime:n,
ptime:a
},!(Math.random()>.5)){
var s=["navigationStart","unloadEventStart","unloadEventEnd","redirectStart","redirectEnd","fetchStart","domainLookupStart","domainLookupEnd","connectStart","connectEnd","requestStart","responseStart","responseEnd","domLoading","domInteractive","domContentLoadedEventStart","domContentLoadedEventEnd","domComplete","loadEventStart","loadEventEnd","secureConnectionStart"],p=[],d=[];
p.push("flag1=7839&flag2=7&flag3=9"),d.push(e()?"flag1=7839&flag2=7&flag3=12":"wifi"==networkType?"flag1=7839&flag2=7&flag3=5":"2g/3g"==networkType?"flag1=7839&flag2=7&flag3=6":"flag1=7839&flag2=7&flag3=7");
for(var g=0,m=s.length;m>g;++g){
s[g]=window.performance.timing[s[g]];
var u=s[g]-s[0];
u>0&&(p.push(g+"="+u),d.push(g+"="+u));
}
-1!=i.indexOf("MicroMessenger")?(p.push("21="+t+"&22="+n+"&23="+a),d.push("21="+t+"&22="+n+"&23="+a)):(p.push("24="+t+"&25="+n+"&26="+a),
d.push("24="+t+"&25="+n+"&26="+a)),p.push("27="+t+"&28="+n+"&29="+a),d.push("27="+t+"&28="+n+"&29="+a),
o("http://isdspeed.qq.com/cgi-bin/r.cgi?"+p.join("&")),o("http://isdspeed.qq.com/cgi-bin/r.cgi?"+d.join("&"));
}
}(),void function(){
var e=document.getElementById("js_toobar"),t=document.getElementById("page-content"),i=window.innerHeight||document.documentElement.clientHeight;
if(t&&!(Math.random()>.1)){
var r=function(){
var s=window.pageYOffset||document.documentElement.scrollTop,p=e.offsetTop;
if(s+i>=p){
for(var d,g,m=t.getElementsByTagName("img"),u={},f=[],h=0,c=0,w=0,l=0,v=m.length;v>l;++l){
var E=m[l];
d=E.getAttribute("data-src")||E.getAttribute("src"),g=E.getAttribute("src"),d&&(d.isCDN()?c++:w++,
h++,u[g]={});
}
if(f.push("1="+1e3*h),f.push("2="+1e3*c),f.push("3="+1e3*w),a.getEntries){
var y=a.getEntries(),S=window.logs.img.download,T=[0,0,0],_=[0,0,0];
h=c=0;
for(var l=0,k=y.length;k>l;++l){
var b=y[l],j=b.name;
j&&"img"==b.initiatorType&&u[j]&&(j.isCDN()&&(_[0]+=b.duration,c++),T[0]+=b.duration,
h++,u[j]={
startTime:b.startTime,
responseEnd:b.responseEnd
});
}
T[0]>0&&h>0&&(T[2]=T[0]/h),_[0]>0&&c>0&&(_[2]=_[0]/c);
for(var l in S)if(S.hasOwnProperty(l)){
for(var M=S[l],q=0,z=0,O=0,x=0,H=0,v=M.length;v>H;++H){
var d=M[H];
if(u[d]&&u[d].startTime&&u[d].responseEnd){
var A=u[d].startTime,C=u[d].responseEnd;
q=Math.max(q,C),z=z?Math.min(z,A):A,d.isCDN()&&(O=Math.max(q,C),x=z?Math.min(z,A):A);
}
}
T[1]+=Math.round(q-z),_[1]+=Math.round(O-x);
}
for(var L=4,I=7,l=0;3>l;l++)T[l]=Math.round(T[l]),_[l]=Math.round(_[l]),T[l]>0&&(f.push(L+l+"="+T[l]),
"wifi"==networkType?f.push(L+l+6+"="+T[l]):"2g/3g"==networkType&&f.push(L+l+12+"="+T[l])),
_[l]>0&&(f.push(I+l+"="+_[l]),"wifi"==networkType?f.push(I+l+6+"="+_[l]):"2g/3g"==networkType&&f.push(I+l+12+"="+_[l]));
}
o("http://isdspeed.qq.com/cgi-bin/r.cgi?flag1=7839&flag2=7&flag3=10&"+f.join("&")),
n.off(window,"scroll",r,!1);
}
};
n.on(window,"scroll",r,!1);
}
}()):!1;
}
var n=e("biz_common/dom/event.js"),i=navigator.userAgent;
e("appmsg/cdn_img_lib.js"),n.on(window,"load",function(){
if(""==networkType&&-1!=i.indexOf("MicroMessenger")){
var e={
"network_type:fail":"fail",
"network_type:edge":"2g/3g",
"network_type:wwan":"2g/3g",
"network_type:wifi":"wifi"
};
JSAPI.invoke("getNetworkType",{},function(n){
networkType=e[n.err_msg],t();
});
}else t();
},!1);
});define("biz_common/dom/class.js",[],function(){
"use strict";
function s(s,a){
return s.classList?s.classList.contains(a):s.className.match(new RegExp("(\\s|^)"+a+"(\\s|$)"));
}
function a(s,a){
s.classList?s.classList.add(a):this.hasClass(s,a)||(s.className+=" "+a);
}
function e(a,e){
if(a.classList)a.classList.remove(e);else if(s(a,e)){
var c=new RegExp("(\\s|^)"+e+"(\\s|$)");
a.className=a.className.replace(c," ");
}
}
function c(c,l){
s(c,l)?e(c,l):a(c,l);
}
return{
hasClass:s,
addClass:a,
removeClass:e,
toggleClass:c
};
});