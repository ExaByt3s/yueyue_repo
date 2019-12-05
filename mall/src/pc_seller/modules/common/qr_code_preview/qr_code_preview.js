/**
 * Created by hudingwen on 15/8/3.
 */
'use strict';

/**�����ļ���Ҫ��ע����ʹ��**/

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
        // tpl��׺���ļ�Ҳ��������ģ��Ƕ�룬���handlebars
        // tpl�ļ�������ģ��������ܣ�Ƕ���ֻ����Ϊ�ַ���ʹ
        // �ã�tpl�ļ�Ƕ��֮ǰ���Ա����ѹ���������С��
        // handlebars����ȱ����Ӧ��ѹ����������ʱ������Ԥ
        // ����׶���ѹ����ѡ��tpl����handlebars��������ѡ
        // ��

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
        // tpl��׺���ļ�Ҳ��������ģ��Ƕ�룬���handlebars
        // tpl�ļ�������ģ��������ܣ�Ƕ���ֻ����Ϊ�ַ���ʹ
        // �ã�tpl�ļ�Ƕ��֮ǰ���Ա����ѹ���������С��
        // handlebars����ȱ����Ӧ��ѹ����������ʱ������Ԥ
        // ����׶���ѹ����ѡ��tpl����handlebars��������ѡ
        // ��

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
        // ����ƫ��λ��
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



