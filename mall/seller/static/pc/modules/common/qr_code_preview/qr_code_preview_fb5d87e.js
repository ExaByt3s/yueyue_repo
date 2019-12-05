define("common/qr_code_preview/qr_code_preview",function(e){"use strict";function i(e){var i=this;i.options=e||{},i.$render_ele=i.options.render_ele,i.init()}var n=e("components/jquery/jquery.js");return i.prototype={init:function(){var e=this;e.config=!1,e.ele_position(),n(window).resize(function(){e.ele_position()}),e.render(),e.setup_event()},render:function(){var e=this,i=Handlebars.template(function(e,i,n,t,r){this.compilerInfo=[4,">= 1.0.0"],n=this.merge(n,e.helpers),r=r||{};var o,s,c="",l="function",a=this.escapeExpression;return c+='\n<div data-role="qr_code_preview"  class="qr_code_preview font_wryh">     \n    <div class="phone ',(s=n.click_ele)?o=s.call(i,{hash:{},data:r}):(s=i&&i.click_ele,o=typeof s===l?s.call(i,{hash:{},data:r}):s),c+=a(o)+'" id="phone" data-type="preview">\n        <div class="img"></div>\n        <p class="p1 cor-409 pt10">点击预览</p>\n    </div>\n\n    <div class="qr fn-hide" id="qr">\n        <div class="img"> <img  data-role="img-url" /></div>\n        <p class="p1  cor-333">\n            扫一扫 <br />\n            在手机上预览编辑效果\n        </p>\n    </div>\n</div>'});e.view=e.$render_ele.html(i({click_ele:e.options.click_ele})),e.phone=e.view.find("#phone"),e.qr=e.view.find("#qr"),e.img_url_ele=e.view.find('[data-role="img-url"]')},set_qr_img:function(e){var i=this;i.img_url_ele.attr("src",e)},setup_event:function(){},ele_position:function(){var e=this,i=n(".content").offset().left+n(".content").width()-90;e.$render_ele.css({left:i})},change_hide:function(){var e=this;e.qr.toggleClass("fn-hide")}},i});