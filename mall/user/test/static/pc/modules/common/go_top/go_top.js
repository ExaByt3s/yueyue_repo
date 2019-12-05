define('common/go_top/go_top', function(require, exports, module){ /**
 * Created by hudingwen on 15/8/3.
 */
'use strict';

/**�����ļ���Ҫ��ע����ʹ��**/

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
        // tpl��׺���ļ�Ҳ��������ģ��Ƕ�룬���handlebars
        // tpl�ļ�������ģ��������ܣ�Ƕ���ֻ����Ϊ�ַ���ʹ
        // �ã�tpl�ļ�Ƕ��֮ǰ���Ա����ѹ���������С��
        // handlebars����ȱ����Ӧ��ѹ����������ʱ������Ԥ
        // ����׶���ѹ����ѡ��tpl����handlebars��������ѡ
        // ��

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