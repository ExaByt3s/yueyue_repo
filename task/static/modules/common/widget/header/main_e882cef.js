define("common/widget/header/main",function(e,n,a){"use strict";function i(e,n,a){return e==n?a.fn(this):a.inverse(this)}var r=e("components/jquery/jquery.js"),s=e("common/cookie/index");a.exports={render:function(e){var n=this;s.get("yue_member_id")>0&&n.get_user_info({beforeSend:function(){e.innerHTML='<a class="rbox frdi header-ajax-text" href="javascript:void();">������...</a>'},callback:function(n){var a=Handlebars.template(function(e,n,a,i,r){function s(){return'\n        <!-- IF is_vip="1" -->\n        <div class="icon-v"></div>\n        <!-- ENDIF -->\n        '}this.compilerInfo=[4,">= 1.0.0"],a=this.merge(a,e.helpers),r=r||{};var o,l,t,c="",u=this,f=a.helperMissing,p="function",d=this.escapeExpression;return c+='<div class="login-out frdi"><span class="name fldi"><a href=\'logout.php\'>�˳�</a></span></div>      \n<div class="rbox frdi">\n\n    <a href="person_center.php" >\n        ',l=a.if_equal||n&&n.if_equal,t={hash:{},inverse:u.noop,fn:u.program(1,s,r),data:r},o=l?l.call(n,(o=n&&n.user,null==o||o===!1?o:o.is_vip),"1",t):f.call(n,"if_equal",(o=n&&n.user,null==o||o===!1?o:o.is_vip),"1",t),(o||0===o)&&(c+=o),c+='\n        <span class="img fldi"><img src="'+d((o=n&&n.user,o=null==o||o===!1?o:o.avatar,typeof o===p?o.apply(n):o))+'" /></span>\n        <span class="name fldi">'+d((o=n&&n.user,o=null==o||o===!1?o:o.nickname,typeof o===p?o.apply(n):o))+'</span>\n        <span class="allow fldi"></span>\n\n    </a> \n</div>'});e.innerHTML="",e.innerHTML=a({user:n.data.user_info,login:1},{helpers:{if_equal:i}})},error:function(){e.innerHTML='<a class="rbox frdi" id="header-fail-load-btn" href="javascript:void();">�����쳣</a>'}})},get_user_info:function(e){r.ajax({url:"http://www.yueus.com/task/module_common/get_login_info.header.json.php?callback=?",type:"GET",data:{user_id:s.get("yue_member_id")},dataType:"JSONP",cache:!0,beforeSend:function(){e.beforeSend&&e.beforeSend.call(this)},success:function(n){e.callback&&e.callback.call(this,n)},error:function(){e.error&&e.error.call(this)}})}}});