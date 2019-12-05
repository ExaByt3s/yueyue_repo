define('common/widget/abnormal/index', function(require, exports, module){ /**
 * Created by hudingwen on 15/6/2.
 * 通用提示页
 */

/**
 * @require modules/common/widget/abnormal/main.scss
 *
 */

var $ = require('components/zepto/zepto.js');

module.exports =
{
    render: function (dom,options) {
        // tpl后缀的文件也可以用于模板嵌入，相比handlebars
        // tpl文件不具有模板变量功能，嵌入后只是作为字符串使
        // 用，tpl文件嵌入之前可以被插件压缩，体积更小。
        // handlebars由于缺少相应的压缩插件因此暂时不能在预
        // 编译阶段做压缩。选择tpl还是handlebars可以自由选
        // 择

        //console.log(css);


        options = options || {};

        var template  = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, self=this;

function program1(depth0,data) {
  
  
  return "data-role=\"refresh-page\"";
  }

function program3(depth0,data) {
  
  
  return "stream-network-error";
  }

function program5(depth0,data) {
  
  
  return "stream-empty";
  }

function program7(depth0,data) {
  
  
  return "icon-stream-network-error";
  }

function program9(depth0,data) {
  
  
  return "icon-stream-empty";
  }

function program11(depth0,data) {
  
  
  return "当前网络不可用";
  }

function program13(depth0,data) {
  
  
  return "暂无数据";
  }

function program15(depth0,data) {
  
  
  return "<p>请检查网络后，轻触屏幕重新加载</p>";
  }

  buffer += "<div style=\"padding-top: 50%;\">\n    <div ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.broken_network), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += " class=\"stream-abnormal ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.broken_network), {hash:{},inverse:self.program(5, program5, data),fn:self.program(3, program3, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\" data-role=\"tap-screen\" >\n        <i class=\"icon ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.broken_network), {hash:{},inverse:self.program(9, program9, data),fn:self.program(7, program7, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\"></i>\n        <h4 >";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.broken_network), {hash:{},inverse:self.program(13, program13, data),fn:self.program(11, program11, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "</h4>\n        ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.broken_network), {hash:{},inverse:self.noop,fn:self.program(15, program15, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n    </div>\n</div>";
  return buffer;
  });

        dom.innerHTML = template(options);

        $(dom).find('[data-role="refresh-page"]').on('click',function()
        {
            window.location.href = window.location.href;
        });


    }
}; 
});