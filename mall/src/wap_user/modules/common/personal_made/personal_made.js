/**
 * Created by 汤圆 on 2015/9/22
 */
'use strict';

/**依赖文件，要在注释上使用**/

/**
 * @require ./personal_made.scss
 **/
 var $ = require('zepto');


function personal_made(options)
{
    var self = this;
    self.options = options || {};
    self.$render_ele = options.ele || {};
    self.content = options.content || {};
    self.init();

}


personal_made.prototype =
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
        var template  = __inline('./personal_made.tmpl');
        self.view = self.$render_ele.html(template(self.content));
    },


    setup_event : function() 
    {
        var self = this;

    }


};

return personal_made;



