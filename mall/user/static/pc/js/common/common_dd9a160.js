define("common/cookie/index",function(){function e(e){return encodeURIComponent(e)}function n(e){return decodeURIComponent(e.replace(/\+/g," "))}function t(e){return"string"==typeof e&&""!==e}var o={get:function(e,o){o=o||{};var i,c;return t(e)&&(c=String(r.cookie).match(new RegExp("(?:^| )"+e+"(?:(?:=([^;]*))|;|$)")))&&c[1]&&(i=o.decode?n(c[1]):c[1]),i},set:function(n,o,c){c=c||{};var u=String(c.encode?e(o):o),a=c.expires,d=c.domain,p=c.path,s=c.secure;return"number"==typeof a&&(a=new Date,a.setTime(a.getTime()+c.expires*i)),a instanceof Date&&(u+="; expires="+a.toUTCString()),t(d)&&(u+="; domain="+d),t(p)&&(u+="; path="+p),s&&(u+="; secure"),r.cookie=n+"="+u,u},del:function(e,n){return this.set(e,"",{expires:-1,domain:n.domain,path:n.path,secure:n.secure})}},r=window.document,i=864e5;return o});
;define("common/ua/index",function(){var i={},e=window,s=e.navigator,n=s.appVersion;if(i.isMobile=/(iphone|ipod|android|ios|ipad|nokia|blackberry|tablet|symbian)/.test(s.userAgent.toLowerCase()),i.isAndroid=/android/gi.test(n),i.isIDevice=/iphone|ipad/gi.test(n),i.isTouchPad=/hp-tablet/gi.test(n),i.isIpad=/ipad/gi.test(n),i.otherPhone=!(i.isAndroid||i.isIDevice),i.is_uc=/uc/gi.test(n),i.is_chrome=/CriOS/gi.test(n)||/Chrome/gi.test(n),i.is_qq=/QQBrowser/gi.test(n),i.is_real_safari=/safari/gi.test(n)&&!i.is_chrome&&!i.is_qq,i.is_standalone=window.navigator.standalone?!0:!1,i.window_width=window.innerWidth,i.window_height=window.innerHeight,i.isAndroid){var a=parseFloat(n.slice(n.indexOf("Android")+8));i.android_version=a}else if(i.isIDevice){var o=n.match(/OS (\d+)_(\d+)_?(\d+)?/),t=o[1];o[2]&&(t+="."+o[2]),o[3]&&(t+="."+o[3]),i.ios_version=t}return i.is_iphone_safari_no_fullscreen=i.isIDevice&&i.ios_version<"7"&&!i.isIpad&&i.is_real_safari&&!i.is_standalone,i.is_yue_app=/yue_pai/.test(n),i.is_weixin=/MicroMessenger/gi.test(n),i.is_normal_wap=!i.is_yue_app&&!i.is_weixin,i});
;define("common/utility/index",function(p){function n(p,n){var i="";n=n||"",i=-1!=e.inArray(n,[120,320,165,640,600,145,440,230,260])?"_"+n:"";var g=/^http:\/\/(img|image)\d*(-c|-wap|-d)?\.poco\.cn.*\.jpg|gif|png|bmp/i,r=/_165.jpg|_320.jpg|_640.jpg|_120.jpg|_600.jpg|_145.jpg|_260.jpg|_440.jpg/i;return g.test(p)&&(p=r.test(p)?p.replace(r,i+".jpg"):p.replace("/(.d*).jpg|.jpg|.gif|.png|.bmp/i",i+".jpg")),p}var e=p("components/jquery/jquery.js"),i=(e(window),p("common/placeholder/index"),{fix_placeholder:function(){e("input").placeholder()},matching_img_size:function(p,e){var i=e;return n(p,i)}});return i});
;define("common/placeholder/index",function(e){var t=e("components/jquery/jquery.js");!function(e){e(t)}(function(e){function t(t){var a={},l=/^jQuery\d+$/;return e.each(t.attributes,function(e,t){t.specified&&!l.test(t.name)&&(a[t.name]=t.value)}),a}function a(t,a){var l=this,o=e(l);if(l.value===o.attr("placeholder")&&o.hasClass(p.customClass))if(l.value="",o.removeClass(p.customClass),o.data("placeholder-password")){if(o=o.hide().nextAll('input[type="password"]:first').show().attr("id",o.removeAttr("id").data("placeholder-id")),t===!0)return o[0].value=a,a;o.focus()}else l==r()&&l.select()}function l(l){var r,o=this,s=e(o),n=o.id;if(l&&"blur"===l.type){if(s.hasClass(p.customClass))return;if("password"===o.type&&(r=s.prevAll('input[type="text"]:first'),r.length>0&&r.is(":visible")))return}if(""===o.value){if("password"===o.type){if(!s.data("placeholder-textinput")){try{r=s.clone().prop({type:"text"})}catch(d){r=e("<input>").attr(e.extend(t(this),{type:"text"}))}r.removeAttr("name").data({"placeholder-enabled":!0,"placeholder-password":s,"placeholder-id":n}).bind("focus.placeholder",a),s.data({"placeholder-textinput":r,"placeholder-id":n}).before(r)}o.value="",s=s.removeAttr("id").hide().prevAll('input[type="text"]:first').attr("id",s.data("placeholder-id")).show()}else{var c=s.data("placeholder-password");c&&(c[0].value="",s.attr("id",s.data("placeholder-id")).show().nextAll('input[type="password"]:last').hide().removeAttr("id"))}s.addClass(p.customClass),s[0].value=s.attr("placeholder")}else s.removeClass(p.customClass)}function r(){try{return document.activeElement}catch(e){}}var o,s,n="[object OperaMini]"===Object.prototype.toString.call(window.operamini),d="placeholder"in document.createElement("input")&&!n,c="placeholder"in document.createElement("textarea")&&!n,i=e.valHooks,u=e.propHooks,p={};d&&c?(s=e.fn.placeholder=function(){return this},s.input=!0,s.textarea=!0):(s=e.fn.placeholder=function(t){var r={customClass:"placeholder"};return p=e.extend({},r,t),this.filter((d?"textarea":":input")+"[placeholder]").not("."+p.customClass).bind({"focus.placeholder":a,"blur.placeholder":l}).data("placeholder-enabled",!0).trigger("blur.placeholder")},s.input=d,s.textarea=c,o={get:function(t){var a=e(t),l=a.data("placeholder-password");return l?l[0].value:a.data("placeholder-enabled")&&a.hasClass(p.customClass)?"":t.value},set:function(t,o){var s,n,d=e(t);return""!==o&&(s=d.data("placeholder-textinput"),n=d.data("placeholder-password"),s?(a.call(s[0],!0,o)||(t.value=o),s[0].value=o):n&&(a.call(t,!0,o)||(n[0].value=o),t.value=o)),d.data("placeholder-enabled")?(""===o?(t.value=o,t!=r()&&l.call(t)):(d.hasClass(p.customClass)&&a.call(t),t.value=o),d):(t.value=o,d)}},d||(i.input=o,u.value=o),c||(i.textarea=o,u.value=o),e(function(){e(document).delegate("form","submit.placeholder",function(){var t=e("."+p.customClass,this).each(function(){a.call(this,!0,"")});setTimeout(function(){t.each(l)},10)})}),e(window).bind("beforeunload.placeholder",function(){e("."+p.customClass).each(function(){this.value=""})}))})});
;define("common/go_top/go_top",function(e){"use strict";function n(e){var n=this,e=e||{};n.$render_ele=e.render_ele,n.init()}var t=e("components/jquery/jquery.js");return n.prototype={init:function(){var e=this;e.render(),e.setup_event()},render:function(){var e=this,n=Handlebars.template(function(e,n,t,o,r){return this.compilerInfo=[4,">= 1.0.0"],t=this.merge(t,e.helpers),r=r||{},'<div id="go-top" class="go-top-mod" style="display:none;">\n    <a href="javascript:;"></a>\n</div>'}),o=e.$render_ele.append(n());e.ele=t(o).find("#go-top"),e.ele.on("click",function(e){e.preventDefault(),t("html,body").animate({scrollTop:0},200)})},setup_event:function(){var e=this;t(window).scroll(function(){t(window).scrollTop()>150?e.ele.fadeIn(500):e.ele.fadeOut(300)})}},n});
;define("common/lazyload/lazyload",function(e,t,r){function n(e){var t=this,e=e||{};t.img_center=e.img_center||{},t.img_center_width=e.img_center.width||0,t.img_center_height=e.img_center.height||0,t.pre_load_165=e.pre_load_165||!1;var r=e.contain;if(!r)throw"��δ��ʼ����lazyload�ĸ�����";return t.init(r,e),t}var i=e("components/jquery/jquery.js"),o=e("common/utility/index");n.prototype={init:function(e,t){var r=this;r.options=t,r.size=r.options.size,r.currentELE={},e?(this.container=e,r.freshELE(this.container),r.engine(),i(window).scroll(function(){r.engine()})):this.container=null},freshELE:function(e){var t=this;e=e||t.container;var r=i(e).find("[data-lazyload-url]"),n=[];r.each(function(e,r){var a=i(r).attr("data-lazyload-url");n[e]={url:a,obj:r},t.pre_load_165&&(n[e]=i.extend(!0,{},n[e],{url_165:o.matching_img_size(a,165)}))}),t.currentELE=n},engine:function(){for(var e=this,t=0;t<e.currentELE.length;t++)e.elementInViewport(e.currentELE[t].obj)&&e.loadImage(e.currentELE[t].obj,{size:e.size,callback:function(r){r.src==e.currentELE[t]&&e.currentELE[t].url&&e.currentELE.splice(t,1)},url_165:e.currentELE[t].url_165})},refresh:function(){var e=this;e.freshELE(),e.engine()},img_ready:function(e,t){var r=this,n=new Image;return n.src=e,n.complete?void(r.isFunction(t.load)&&t.load.call(n)):(n.onerror=function(){r.isFunction(t.error)&&t.error.call(n),n=n.onload=n.onerror=null},void(n.onload=function(){r.isFunction(t.load)&&t.load.call(n),n=n.onload=n.onerror=null}))},loadImage:function(e,t){var r=this,n=(new Image,e.getAttribute("data-lazyload-url"));t=t||{},n&&r.img_ready(n,{load:function(){console.log("ͼƬ�������");var o=this;i(e).hasClass("error")&&i(e).removeClass("error refresh");var a=e.getAttribute("data-size")||t.size,l=o.width,c=o.height;if(r.img_center.is_open)r.set_img_center(e,r.img_center_width,r.img_center_height);else if(a){var s=r.setImageSize(a,l,c);e.style.height=s+"px"}r.pre_load_165&&t.url_165&&(e.src=t.url_165),r._st=setTimeout(function(){"IMG"==e.tagName?e.src=n:e.style.backgroundImage="url("+n+")",i(e).addClass("loaded").removeAttr("data-lazyload-url")},500),"function"==typeof t.callback&&t.callback.call(r,o)},error:function(){console.log(n+"ͼƬ����ʧ��");var t=this;i(e).addClass("error"),i(e).one("click",function(e){e.stopPropagation(),e.preventDefault(),t.retry=1,r.loadImage(t)}).addClass("refresh")}})},setImageSize:function(e,t,r){var n=parseInt(e),t=parseInt(t),r=parseInt(r),i=n*r/t;return i=parseInt(i)},elementInViewport:function(e){var t=e.getBoundingClientRect();return t.top>=0&&t.left>=0&&t.top<=(window.innerHeight||document.documentElement.clientHeight)},isFunction:function(e){return"function"==typeof e},set_img_center:function(e,t,r){var n=this,i=e.getAttribute("data-lazyload-url"),o=n.get_yue_img_size_from_url(i);t=parseInt(t),r=parseInt(r);var a=o.width,l=o.height;if(a/t>l/r){var c=r*a/l;e.style.width=c,e.style.height=r+"px",e.style.marginLeft=-(c-t)/2+"px"}else{var s=t*l/a;e.style.height=s,e.style.width=t+"px",e.style.marginTop=0>s-r-80?-(s-r)/2+"px":-(s-r)/2+50+"px"}},get_yue_img_size_from_url:function(e){var t=e.match(/\?(.*)/),r="",n="";if(t[1]){var i=t[1],o=i.match(/(\d+)x(\d+)_(\d+)/);return o[1]&&(r=o[1]),o[2]&&(n=o[2]),{width:r,height:n}}return{width:r,height:n}}},r.exports=n});
;define("common/widget/page_control/page-control",function(a){function n(a){var n=this,a=a||{};n.render_ele=a.container||document.body,n.render_index_ele=a.index_btn_container||document.body,n.render_data=a.data||{},n.after_next_act=a.after_next_act||function(){},n.after_render=a.after_render||function(){},n.after_pre_act=a.after_pre_act||function(){},n.btn_index_max=a.btn_index_max||5,n.default_index=a.default_index||0,n.index_btn_trigger_show_lr_btn=a.index_btn_trigger_show_lr_btn||5,n.index_btn_temp=a.index_btn_temp||Handlebars.template(function(a,n,e,t,r){function i(){return'\n    <a title="��һҳ" data-index="pre">&lt;</a>\n'}function s(a,n){var t,r,i="";return i+="\n    <a ",t=e["if"].call(a,a&&a.cur,{hash:{},inverse:c.noop,fn:c.program(4,d,n),data:n}),(t||0===t)&&(i+=t),i+=' data-index="',(r=e.dex)?t=r.call(a,{hash:{},data:n}):(r=a&&a.dex,t=typeof r===h?r.call(a,{hash:{},data:n}):r),(t||0===t)&&(i+=t),i+='">',(r=e.num)?t=r.call(a,{hash:{},data:n}):(r=a&&a.num,t=typeof r===h?r.call(a,{hash:{},data:n}):r),i+=p(t)+"</a>\n"}function d(){return'class="click"'}function o(){return'\n    <a title="��һҳ" data-index="next">&gt;</a>\n'}this.compilerInfo=[4,">= 1.0.0"],e=this.merge(e,a.helpers),r=r||{};var l,_="",c=this,h="function",p=this.escapeExpression;return l=e["if"].call(n,(l=n&&n.data,null==l||l===!1?l:l.has_pre),{hash:{},inverse:c.noop,fn:c.program(1,i,r),data:r}),(l||0===l)&&(_+=l),_+="\n",l=e.each.call(n,(l=n&&n.data,null==l||l===!1?l:l.list),{hash:{},inverse:c.noop,fn:c.program(3,s,r),data:r}),(l||0===l)&&(_+=l),_+="\n",l=e["if"].call(n,(l=n&&n.data,null==l||l===!1?l:l.has_next),{hash:{},inverse:c.noop,fn:c.program(6,o,r),data:r}),(l||0===l)&&(_+=l),_+="\n"}),n.list_temp=a.list_temp||Handlebars.template(function(a,n,e,t,r){function i(a,n){var t,r,i="";return i+='\n<div class="item clearfix">\n    <div class="user-img">\n        <div class="img"><a href="#"><img src="',(r=e.avatar)?t=r.call(a,{hash:{},data:n}):(r=a&&a.avatar,t=typeof r===d?r.call(a,{hash:{},data:n}):r),(t||0===t)&&(i+=t),i+='" /></a></div>\n        <p class="id-num">ID:',(r=e.from_user_id)?t=r.call(a,{hash:{},data:n}):(r=a&&a.from_user_id,t=typeof r===d?r.call(a,{hash:{},data:n}):r),(t||0===t)&&(i+=t),i+='</p>\n    </div>\n    <div class="item-main">\n        <div class="mod-star-item" data-role="commit_stars" data-stars="',(r=e.rating)?t=r.call(a,{hash:{},data:n}):(r=a&&a.rating,t=typeof r===d?r.call(a,{hash:{},data:n}):r),(t||0===t)&&(i+=t),i+='">\n            <div class="icon none-star" data-role="stars">\n                <div class="yellow none"></div>\n            </div>\n            <div class="icon none-star" data-role="stars">\n                <div class="yellow none"></div>\n            </div>\n            <div class="icon none-star" data-role="stars">\n                <div class="yellow none"></div>\n            </div>\n            <div class="icon none-star" data-role="stars">\n                <div class="yellow none"></div>\n            </div>\n            <div class="icon none-star" data-role="stars">\n                <div class="yellow none"></div>\n            </div>\n        </div>\n        <div class="text-item">\n            <p>',(r=e.comment)?t=r.call(a,{hash:{},data:n}):(r=a&&a.comment,t=typeof r===d?r.call(a,{hash:{},data:n}):r),(t||0===t)&&(i+=t),i+='</p>\n        </div>\n        <div class="info-item"><span class="name">',(r=e.customer)?t=r.call(a,{hash:{},data:n}):(r=a&&a.customer,t=typeof r===d?r.call(a,{hash:{},data:n}):r),(t||0===t)&&(i+=t),i+='</span><span class="date">',(r=e.add_time)?t=r.call(a,{hash:{},data:n}):(r=a&&a.add_time,t=typeof r===d?r.call(a,{hash:{},data:n}):r),(t||0===t)&&(i+=t),i+="</span> </div>\n    </div>\n</div>\n"}this.compilerInfo=[4,">= 1.0.0"],e=this.merge(e,a.helpers),r=r||{};var s,d="function",o=this;return s=e.each.call(n,n&&n.data,{hash:{},inverse:o.noop,fn:o.program(1,i,r),data:r}),s||0===s?s:""}),n.per_page_amount=a.per_page_amount||10,n.current_index=n.default_index,n.init()}{var e=a("components/jquery/jquery.js");a("common/utility/index")}return n.prototype={init:function(){var a=this;a.render(),a.setup_event()},setup_event:function(){},after_show_event:function(){var a=this;e("[data-index]").on("click",function(){var n=e(this),t=n.attr("data-index");switch(t){case"pre":a.pre_page();break;case"next":a.next_page();break;default:a.current_index=parseInt(t),a.show_page(t)}})},render:function(){var a=this;a.data_combine=a._data_init(),a.index_data=a._index_init(),a.show_page(a.current_index)},show_page:function(a){var n=this;n.index_data=n._index_init();var e="",t="";0!=n.data_combine.length&&(e=n.list_temp({data:n.data_combine[a].list}),t=n.index_btn_temp({data:n.index_data})),n.view=n.render_ele.html(e),n.index_view=n.render_index_ele.html(t),n.after_show_event(),n.after_render(n)},next_page:function(){var a=this;a.show_page(++a.current_index),a.after_next_act(a)},pre_page:function(){var a=this;a.show_page(--a.current_index),a.after_pre_act(a)},_index_init:function(){var a=this,n=a.total_pages,e=!0,t=!0;a.current_index>n&&(a.current_index=0),0==a.current_index&&(e=!1,t=!0),a.current_index==n-1&&(e=!0,t=!1),a.index_btn_trigger_show_lr_btn>n-1&&(e=!1,t=!1);for(var r={has_pre:e,has_next:t},i=[],s=0;n>s;s++)i.push(s==a.current_index?{dex:s,num:s+1,cur:!0}:{dex:s,num:s+1,cur:!1});return r.list=i,r},_data_init:function(){var a=this;a.grouping_arr=[];var n=a.render_data;a.total_pages=Math.ceil(n.length/a.per_page_amount);for(var e=0;e<a.total_pages;e++){var t,r={list:[]};t=n.length<a.per_page_amount?n.length:a.per_page_amount;for(var i=0;i<a.per_page_amount;i++)for(;t;)r.list.push(n[0]),n=n.slice(1,n.length),t--;a.grouping_arr.push(r)}return a.grouping_arr}},n});