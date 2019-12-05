define("common/lazyload/lazyload",function(t,n,r){var e=t("components/zepto/zepto.js"),o=t("common/utility/index");!function(t,n){function e(t,n){var r=this;if(!t)throw"��δ��ʼ����lazyload�ĸ�����";return n=n||{},r.init(t,n),r}function a(n,r){var e=(new Image,n.getAttribute("data-lazyload-url")),o=this;r=r||{},e&&u(e,{load:function(){var a=this;t(n).hasClass("error")&&t(n).removeClass("error refresh");var l=n.getAttribute("data-size")||r.size,c=a.width,u=a.height;if(l){var s=i(l,c,u);n.style.height=s+"px"}o._st=setTimeout(function(){"IMG"==n.tagName?n.src=e:n.style.backgroundImage="url("+e+")","IMG"==n.tagName&&n.hasAttribute("height")&&n.removeAttribute("height"),t(n).addClass("loaded").removeAttr("data-lazyload-url")},500),"function"==typeof r.callback&&r.callback.call(o,a)},error:function(){var r=this;t(n).addClass("error"),t(n).one("click",function(t){t.stopPropagation(),t.preventDefault(),r.retry=1,a(r)}).addClass("refresh")}})}function i(t,n,r){var e=o.int(t),n=o.int(n),r=o.int(r),a=e*r/n;return a=o.int(a)}function l(t){var r=t.getBoundingClientRect();return r.top>=0&&r.left>=0&&r.top<=(n.innerHeight||document.documentElement.clientHeight)}function c(t){return"function"==typeof t}function u(t,n){var r=new Image;return r.src=t,r.complete?void(c(n.load)&&n.load.call(r)):(r.onerror=function(){c(n.error)&&n.error.call(r),r=r.onload=r.onerror=null},void(r.onload=function(){c(n.load)&&n.load.call(r),r=r.onload=r.onerror=null}))}e.prototype={init:function(r,e){var o=this;o.options=e,o.size=o.options.size,o.currentELE={},r?(this.container=r,o.freshELE(this.container),o.engine(),t(n).scroll(function(){o.engine()})):this.container=null},freshELE:function(n){var r=this;n=n||r.container;var e=t(n).find("[data-lazyload-url]"),o={};e.each(function(n,r){var e=t(r).attr("data-lazyload-url");o[e]={url:e,obj:r}}),r.currentELE=o},engine:function(){var t=this;for(var n in t.currentELE)l(t.currentELE[n].obj)&&a(t.currentELE[n].obj,{size:t.size,callback:function(n){n.src==t.currentELE[n.src]&&t.currentELE[n.src].url&&delete t.currentELE[n.src]}})},refresh:function(){var t=this;t.freshELE(),t.engine()}},r.exports=e}(e,window)});