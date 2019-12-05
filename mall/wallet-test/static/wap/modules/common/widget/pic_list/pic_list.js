define('common/widget/pic_list/pic_list', function(require, exports, module){ /**
 *  汤圆
 *  2015-7-13
 *  地区组件
 */
 /**
  * @require modules/common/widget/pic_list/pic_list.scss
 **/


var $ = require('components/zepto/zepto.js');
var utility = require('common/utility/index');


// 定义函数对象
function pic_list_fn(options) 
{
    var self = this;
    var options = options || {} ;
    self.render_ele = options.ele || {} ;

    //  初始化
    self.init();
}

pic_list_fn.prototype = 
{
    init : function() 
    {
        var self = this;
        self.render();
        // 安装事件
        self.setup_event();
    },

    setup_event : function() 
    {
        var self = this;

    },

    render : function() 
    {
        var self = this;
        var template  = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  


  return "<!-- 图片列表模块 start -->\n    <div class=\"list-max-img-mod f14 color-666\">\n        <div class=\"item-wrap clearfix \">\n            \n            <div class=\"render_item_ele\" data-role=\"render_item_ele\" id=\"render_item_ele\"></div>\n\n        </div>\n        \n    </div>\n\n<!-- 图片列表模块 end -->";
  });  
        self.view = self.render_ele.html(template({}));

        self.render_item_ele = self.view.find('[data-role="item-ele"]');
        self.render_item_ele = self.view.find('[data-role="render_item_ele"]');

    },
    render_html : function(data,operate) 
    {
        var self = this;
        var template  = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n    <div class=\"item\">\n        <div class=\"item-area\">\n            <a href=\"";
  if (helper = helpers.link_1) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.link_1); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n                <div class=\"img-box\">\n                    <i style=\"background-image:url(";
  if (helper = helpers.img_1) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.img_1); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + ")\"></i>\n                </div>\n            </a> \n            <div class=\"txt-area\">\n                <p class=\"price\">";
  if (helper = helpers.txt_2) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.txt_2); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</p>\n                <p class=\"des\">";
  if (helper = helpers.txt_3) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.txt_3); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</p>\n            </div>\n        </div>\n    </div>\n";
  return buffer;
  }

  buffer += "\n";
  stack1 = helpers.each.call(depth0, (depth0 && depth0.data), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  return buffer;
  });
        var view = template({
            data : data
        })
        self.render_item_ele[operate](view);
    }
}


return pic_list_fn;
 
});