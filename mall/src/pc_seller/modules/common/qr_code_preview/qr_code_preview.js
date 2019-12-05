/**
 * Created by hudingwen on 15/8/3.
 */
'use strict';

/**依赖文件，要在注释上使用**/

/**
 * @require ./qr_code_preview.scss
 **/
var $ = require('jquery');



function qr_code_preview(options)
{
    var self = this;
    self.options = options || {};
    self.$render_ele = self.options.render_ele;

    self.init() ;
}


qr_code_preview.prototype =
{
    init : function()
    {
        var self = this;
        self.config = false ;
        self.ele_position()
        $(window).resize(function() {
            self.ele_position()
        });
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
        var template  = __inline('./qr_code_preview.tmpl');
        self.view = self.$render_ele.html(template({
            click_ele : self.options.click_ele
        }));

        self.phone = self.view.find('#phone');
        self.qr = self.view.find('#qr');
        self.img_url_ele = self.view.find('[data-role="img-url"]');

    },


    set_qr_img: function (src) 
    {
        // tpl后缀的文件也可以用于模板嵌入，相比handlebars
        // tpl文件不具有模板变量功能，嵌入后只是作为字符串使
        // 用，tpl文件嵌入之前可以被插件压缩，体积更小。
        // handlebars由于缺少相应的压缩插件因此暂时不能在预
        // 编译阶段做压缩。选择tpl还是handlebars可以自由选
        // 择

        var self = this;
        self.img_url_ele.attr('src', src);

       

    },

    setup_event : function() 
    {
        var self = this;

        // self.$render_ele.hover(function() {
        //     // self.phone.addClass('fn-hide');
        //     self.qr.removeClass('fn-hide');
        // }, function() {
        //     // self.phone.removeClass('fn-hide');
        //     self.qr.addClass('fn-hide');
        // });
        

    },


    ele_position : function() 
    {
        var self = this;
        // 处理偏移位置
        var content_left = ( $('.content').offset().left + $('.content').width() - 90 );
        self.$render_ele.css({
            left: content_left
        });
    },

    change_hide : function() 
    {
        var self = this;
        self.qr.toggleClass('fn-hide');
    }




};

return qr_code_preview;



