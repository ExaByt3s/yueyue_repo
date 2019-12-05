define('common/widget/header/main', function(require, exports, module){ 'use strict';

/**�����ļ���Ҫ��ע����ʹ��**/

/**
 * @require modules/common/widget/header/header.scss
**/
var $ = require('components/zepto/zepto.js');
var ua = require('common/ua/index');
module.exports = 
{
    init : function(options) 
    {
        var self = this;
        self.options =  options;
        self.render_ele = options.ele;

        //���ͷ�����أ�Ҫ�ѵ�ǰҳ�ڵ�margin-top��Ϊ0
        if (!options.header_show) 
        {
            self.set_bar_css()
        }

        // �������Ϊ�գ������ȡ�ĵ�����
        if (!options.title.trim()) 
        {
            var document_title = document.title;
            self.options = $.extend(true,{},self.options,{title : document_title });
        }

        self.render(self.rend_ele);

        if(ua.is_yue_app)
        {
            self.$el.addClass('fn-hide');
            self.set_bar_css()
        }

    },
    render: function (dom) {
        // tpl��׺���ļ�Ҳ��������ģ��Ƕ�룬���handlebars
        // tpl�ļ�������ģ��������ܣ�Ƕ���ֻ����Ϊ�ַ���ʹ
        // �ã�tpl�ļ�Ƕ��֮ǰ���Ա����ѹ���������С��
        // handlebars����ȱ����Ӧ��ѹ����������ʱ������Ԥ
        // ����׶���ѹ����ѡ��tpl����handlebars��������ѡ
        // ��		
		
        var self = this;
		var data = self.options;
		var template  = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, functionType="function", escapeExpression=this.escapeExpression, self=this;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n    <!-- header start -->\n    <header class=\"global-header\">\n        <div class=\"wbox clearfix\">\n            <a href=\"javascript:void(0);\" data-role=\"page-back\">\n                <div class=\"back\">\n                    <i class=\"icon-back\"></i>\n                </div>\n            </a>\n\n            <h3 class=\"title\">";
  if (helper = helpers.title) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.title); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</h3>\n\n            ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.right_icon_show), {hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n        </div>\n    </header>\n    <!-- header end -->\n";
  return buffer;
  }
function program2(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n\n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.share_icon)),stack1 == null || stack1 === false ? stack1 : stack1.show), {hash:{},inverse:self.noop,fn:self.program(3, program3, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.omit_icon)),stack1 == null || stack1 === false ? stack1 : stack1.show), {hash:{},inverse:self.noop,fn:self.program(5, program5, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        \n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.show_txt)),stack1 == null || stack1 === false ? stack1 : stack1.show), {hash:{},inverse:self.noop,fn:self.program(7, program7, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                    \n            ";
  return buffer;
  }
function program3(depth0,data) {
  
  
  return "\n                    <!-- ����ť -->\n                    <div class=\"share\" data-role=\"right-btn\">\n                        <i class=\"icon-share\"></i>\n                    </div>\n                    <!-- ����ť end -->\n                ";
  }

function program5(depth0,data) {
  
  
  return "\n                    <!-- ���� -->\n                    <div class=\"omit\" data-role=\"right-btn\">\n                        <i class=\"icon-omit\"></i>\n                    </div>\n                    <!-- ���� end -->\n                ";
  }

function program7(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                    <!-- ���� -->\n                    <div class=\"side-txt\" style=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.show_txt)),stack1 == null || stack1 === false ? stack1 : stack1.style)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" data-role=\"right-btn\">\n                        "
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.show_txt)),stack1 == null || stack1 === false ? stack1 : stack1.content)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\n                    </div>\n                    <!-- end -->\n                ";
  return buffer;
  }

  buffer += "\n";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.header_show), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  return buffer;
  });	
        self.render_ele.html(template(data));

        self.$el = self.render_ele;

        self.setup_event();
    },
    setup_event : function()
    {
        var self = this;

        self.$el.find('[data-role="page-back"]').on('click',function()
        {
            self.page_back();     
        });

        self.$el.find('[data-role="right-btn"]').on('click',function()
        {
            self.$el.trigger('click:right_btn');
        });
    },
    page_back : function()
    {
        var self = this;   
        window.history.back();
        
        // window.location.reload();
    },
    set_bar_css : function()
    {
        var self = this;   
        $("[role=main]").css({
            paddingTop: '0'
        });
    }
};

 
});