define('common/go_top/go_top', function(require, exports, module){ /**
 * Created by hudingwen on 15/8/3.
 */
'use strict';

/**依赖文件，要在注释上使用**/

/**
 * @require modules/common/go_top/go_top.scss
 **/
var $ = require('components/jquery/jquery.js');



function go_top(options)
{
    var self = this;
    var options = options || {};
    self.$render_ele = options.render_ele;
    self.init() ;
}


go_top.prototype =
{
    init : function()
    {
        var self = this;
        self.render();
        self.setup_event();
    },

    render: function () 
    {
        // tpl后缀的文件也可以用于模板嵌入，相比handlebars
        // tpl文件不具有模板变量功能，嵌入后只是作为字符串使
        // 用，tpl文件嵌入之前可以被插件压缩，体积更小。
        // handlebars由于缺少相应的压缩插件因此暂时不能在预
        // 编译阶段做压缩。选择tpl还是handlebars可以自由选
        // 择

        var self = this;
        var template  = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  


  return "<div id=\"go-top\" class=\"go-top-mod\" style=\"display:none;\">\n    <a href=\"javascript:;\"></a>\n</div>";
  });
        var view = self.$render_ele.append(template());
        self.ele = $(view).find('#go-top');
        self.ele.on('click', function(event) {
            event.preventDefault();
            $("html,body").animate({scrollTop:0},200);

        });


    },

    setup_event : function() 
    {
        var self = this;

        $(window).scroll(function(){
            if($(window).scrollTop() > 150)
            {
                self.ele.fadeIn(500);
            }
            else
            {
                self.ele.fadeOut(300);  
            }
        });
    }


};

return go_top;



 
});