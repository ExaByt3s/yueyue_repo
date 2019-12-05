/**
 * Created by ��Բ on 2015/9/22
 */
'use strict';

/**�����ļ���Ҫ��ע����ʹ��**/

/**
 * @require ./footer.scss
 **/
 var $ = require('zepto');


function footer(options)
{
    var self = this;
    self.options = options || {};
    self.$render_ele = options.ele || {};
    self.content = options.content || {};
    self.init();
}


footer.prototype =
{
    init : function()
    {
        var self = this;


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
        var template  = __inline('./footer.tmpl');
        self.view = self.$render_ele.html(template(self.content));

        self.view.find('li').eq(self.content.current).addClass('cur');

      

    },


    setup_event : function() 
    {
        var self = this;

    }


};

return footer;



