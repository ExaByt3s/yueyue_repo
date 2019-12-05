define("common/widget/header/main",function(n,e){"use strict";function t(n){var e=this;e.init(n)}var a=n("components/zepto/zepto.js"),i=n("common/ua/index");t.prototype={init:function(n){var e=this;if(e.options=n,e.render_ele=n.ele,e.left_side_html=a.trim(n.left_side_html),e.config=!0,n.header_show||e.set_bar_css(),!n.title.trim()){var t=document.title;e.options=a.extend(!0,{},e.options,{title:t,left_side_html:e.left_side_html})}return e.render(e.rend_ele),i.is_yue_app&&(e.$el.addClass("fn-hide"),e.set_bar_css()),e},render:function(){var n=this,e=n.options,t=Handlebars.template(function(n,e,t,a,i){function r(n,e){var a,i,r="";return r+='\n    <!-- header start -->\n    <header class="global-header">\n        <div class="wbox clearfix">\n\n            ',a=t["if"].call(n,n&&n.left_side_html,{hash:{},inverse:m.program(4,l,e),fn:m.program(2,o,e),data:e}),(a||0===a)&&(r+=a),r+='\n\n            <h3 class="title">',(i=t.title)?a=i.call(n,{hash:{},data:e}):(i=n&&n.title,a=typeof i===u?i.call(n,{hash:{},data:e}):i),r+=_(a)+"</h3>\n\n            ",a=t["if"].call(n,n&&n.right_icon_show,{hash:{},inverse:m.noop,fn:m.program(6,s,e),data:e}),(a||0===a)&&(r+=a),r+="\n\n        </div>\n    </header>\n    <!-- header end -->\n"}function o(n,e){var a,i,r="";return r+="     \n                ",(i=t.left_side_html)?a=i.call(n,{hash:{},data:e}):(i=n&&n.left_side_html,a=typeof i===u?i.call(n,{hash:{},data:e}):i),(a||0===a)&&(r+=a),r+="\n            "}function l(){return'\n                <a href="javascript:void(0);" >\n                    <div class="back" data-role="page-back">\n                        <i class="icon-back"></i>\n                    </div>\n                </a>\n            '}function s(n,e){var a,i="";return i+="\n\n                ",a=t["if"].call(n,(a=n&&n.share_icon,null==a||a===!1?a:a.show),{hash:{},inverse:m.noop,fn:m.program(7,c,e),data:e}),(a||0===a)&&(i+=a),i+="\n\n                ",a=t["if"].call(n,(a=n&&n.omit_icon,null==a||a===!1?a:a.show),{hash:{},inverse:m.noop,fn:m.program(9,h,e),data:e}),(a||0===a)&&(i+=a),i+="\n        \n                ",a=t["if"].call(n,(a=n&&n.show_txt,null==a||a===!1?a:a.show),{hash:{},inverse:m.noop,fn:m.program(11,d,e),data:e}),(a||0===a)&&(i+=a),i+="\n                    \n            "}function c(){return'\n                    <!-- 分享按钮 -->\n                    <div class="share" data-role="right-btn">\n                        <i class="icon-share"></i>\n                    </div>\n                    <!-- 分享按钮 end -->\n                '}function h(){return'\n                    <!-- 三点 -->\n                    <div class="omit" data-role="right-btn">\n                        <i class="icon-omit"></i>\n                    </div>\n                    <!-- 三点 end -->\n                '}function d(n){var e,t="";return t+='\n                    <!-- 文字 -->\n                    <div class="side-txt" style="'+_((e=n&&n.show_txt,e=null==e||e===!1?e:e.style,typeof e===u?e.apply(n):e))+'" data-role="right-btn">\n                        '+_((e=n&&n.show_txt,e=null==e||e===!1?e:e.content,typeof e===u?e.apply(n):e))+"\n                    </div>\n                    <!-- end -->\n                "}this.compilerInfo=[4,">= 1.0.0"],t=this.merge(t,n.helpers),i=i||{};var f,p="",u="function",_=this.escapeExpression,m=this;return p+="\n",f=t["if"].call(e,e&&e.header_show,{hash:{},inverse:m.noop,fn:m.program(1,r,i),data:i}),(f||0===f)&&(p+=f),p});n.render_ele.html(t(e)),n.$el=n.render_ele,n.setup_event(n.$el)},setup_event:function(){var n=this,e=null==n.options.use_page_back?!0:!1;n.$el.find('[data-role="page-back"]').on("click",function(){e?n.page_back():n.$el.trigger("click:left_btn")}),n.$el.find('[data-role="right-btn"]').on("click",function(){n.$el.trigger("click:right_btn")})},page_back:function(){window.history.back(),window.location.href="http://yp.yueus.com/mall/user/"},set_bar_css:function(){a("[role=main]").css({paddingTop:"0"})}},e.init=function(n){return new t(n)}});