define('qrcode', function(require, exports, module){ /**
 * 评价列表
 * 汤圆
 */
 /**
  * @require modules/qrcode/qrcode.scss
  **/

"use strict";


var utility = require('common/utility/index');
var $ = require('components/zepto/zepto.js');
var fastclick = require('components/fastclick/fastclick.js');
var yue_ui = require('yue_ui/frozen');
// var App =  require('../common/I_APP/I_APP');
var scroll = require('common/scroll/index');
var items_tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, functionType="function", escapeExpression=this.escapeExpression, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += " \n                    <div class=\"swiper-slide\"  data-role=\"swipe\">\n                        <img src=\"";
  if (helper = helpers.url_img) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.url_img); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" />\n                        <p class=\"p1 f18  tc\">签到码：<span class=\"color-ff6\">";
  if (helper = helpers.number) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.number); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</span></p>\n                    </div>\n                ";
  return buffer;
  }

  buffer += "<div class=\"fade\" id=\"fade\">\n    <div class=\"qrcode_mod\" >\n        <div class=\"close\" id=\"close\" data-role=\"close\"></div>\n        <div class=\"img-area\">\n            <div class=\"swiper-container\" >\n\n                <div class=\"swiper-wrapper\">\n                ";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.data_arr), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    \n                </div>\n                <!-- 如果需要分页器 -->\n                <div class=\"swiper-pagination\"></div>\n            </div>\n            <p class=\"p2 tc f10 color-999\">左右滑动查看上/下一张</p>\n        </div>\n        <div class=\"ui-user-info-mod \">\n            <div class=\"item\">\n               <div class=\"user-info-item\">\n                  <div class=\"ui-avatar-icon ui-avatar-icon-m\">\n                          <span style=\"background-image:url(";
  if (helper = helpers.user_icon) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.user_icon); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + ")\"></span>\n                  </div>\n                  <div class=\"user-info-con\">\n                    <h3 class=\"title\">";
  if (helper = helpers.name) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.name); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</h3>\n                    <p class=\"txt\">ID：";
  if (helper = helpers.user_id) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.user_id); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</p>\n                  </div>\n               </div>\n            </div>\n        </div>\n\n    </div>\n</div>\n";
  return buffer;
  });

var s = require('common/widget/swiper/1.0.0/swiper3.07.min');





if ('addEventListener' in document)
{
    document.addEventListener('DOMContentLoaded', function ()
    {
        fastclick.attach(document.body);
    }, false);
}

    var _self = $({});

    function qrcode(options) 
    {
        var self = this;
        self.options = options || {} ;
        self.render_ele = options.ele;
        self.data = options.data ;
        self.init()
        // 默认播放第0张
        self.play = options.play ? options.play : 0 ;
    }

    qrcode.prototype =
    {
        refresh : function()
        {
            var self = this;
            var html_str = items_tpl(self.data);
            var view = self.render_ele.html(html_str);

            self.close_ele = view.find('[data-role="close"]');
            self.swiper_container_ele = view.find('.swiper-container');
            //self.swiper_container_ele.height(window.innerHeight - 200);

            self.qrcode_main();

            self.close_ele.on('click', function(event) {
                event.preventDefault();
                self.hide();
            });
        },

        init : function()
        {

            var self = this;
            self.refresh();

        },

        qrcode_main : function()
        {
            var self = this;
            self.mySwiper = new Swiper ('.swiper-container', {
                direction: 'horizontal',
                loop: false,
                initialSlide : self.play ,
                // 如果需要分页器
                pagination: '.swiper-pagination'
            })
        },
        hide : function() 
        {
            var self = this;
            self.render_ele.html('');
        }
    };


return  qrcode;



 
});