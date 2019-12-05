define('common/widget/header/main', function(require, exports, module){ /**
 * Created by hudingwen on 15/8/3.
 */
'use strict';

/**�����ļ���Ҫ��ע����ʹ��**/

/**
 * @require modules/common/widget/header/header.scss
 **/
var $ = require('components/zepto/zepto.js');
var ua = require('common/ua/index');

function header_class(options)
{
    var self = this;

    self.init(options);
}

header_class.prototype =
{
    init : function(options)
    {
        var self = this;
        self.options =  options;
        self.options.className = options.className || '';
        self.options.style = options.style || '';
        self.render_ele = options.ele;
        self.left_side_html =  $.trim(options.left_side_html);
        if (self.options.left_icon_show == null) 
        {
            self.options.left_icon_show = true ;
        }

        self.config = true ;


        //���ͷ�����أ�Ҫ�ѵ�ǰҳ�ڵ�margin-top��Ϊ0
        if (!options.header_show)
        {
            self.set_bar_css()
        }

        // �������Ϊ�գ������ȡ�ĵ�����
        if (!options.title.trim())
        {
            var document_title = document.title;
            self.options = $.extend(true,{},self.options,{title : document_title, left_side_html:self.left_side_html});
        }

        var cur_pathname = window.location.pathname;

        if(/test/.test(cur_pathname))
        {
            options.title = '����-'+options.title;
        }

        self.render(self.rend_ele);

        if(ua.is_yue_app || ua.is_yue_seller)
        {
            self.$el.addClass('fn-hide');
            self.set_bar_css()
        }

        return self;

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
  var buffer = "", stack1, functionType="function", self=this, escapeExpression=this.escapeExpression;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "\n    <!-- header start -->\n    <header class=\"global-header ";
  if (helper = helpers.className) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.className); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" style=\"";
  if (helper = helpers.style) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.style); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\">\n        <div class=\"wbox clearfix\">\n\n            ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.left_icon_show), {hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            \n\n            <h3 class=\"title\">";
  if (helper = helpers.title) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.title); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</h3>\n\n            ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.right_icon_show), {hash:{},inverse:self.noop,fn:self.program(7, program7, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n        </div>\n    </header>\n    <!-- header end -->\n";
  return buffer;
  }
function program2(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                ";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.left_side_html), {hash:{},inverse:self.program(5, program5, data),fn:self.program(3, program3, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            ";
  return buffer;
  }
function program3(depth0,data) {
  
  var buffer = "", stack1, helper;
  buffer += "     \n                    ";
  if (helper = helpers.left_side_html) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.left_side_html); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                ";
  return buffer;
  }

function program5(depth0,data) {
  
  
  return "\n                    <a href=\"javascript:void(0);\" >\n                        <div class=\"back\" data-role=\"page-back\">\n                            <i class=\"icon-back\"></i>\n                        </div>\n                    </a>\n                ";
  }

function program7(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n\n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.share_icon)),stack1 == null || stack1 === false ? stack1 : stack1.show), {hash:{},inverse:self.noop,fn:self.program(8, program8, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.omit_icon)),stack1 == null || stack1 === false ? stack1 : stack1.show), {hash:{},inverse:self.noop,fn:self.program(10, program10, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n        \n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.show_txt)),stack1 == null || stack1 === false ? stack1 : stack1.show), {hash:{},inverse:self.noop,fn:self.program(12, program12, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n\n                ";
  stack1 = helpers['if'].call(depth0, ((stack1 = (depth0 && depth0.search_icon)),stack1 == null || stack1 === false ? stack1 : stack1.show), {hash:{},inverse:self.noop,fn:self.program(14, program14, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n                    \n            ";
  return buffer;
  }
function program8(depth0,data) {
  
  
  return "\n                    <!-- ����ť -->\n                    <div class=\"share\" data-role=\"right-btn\">\n                        <i class=\"icon-share\"></i>\n                    </div>\n                    <!-- ����ť end -->\n                ";
  }

function program10(depth0,data) {
  
  
  return "\n                    <!-- ���� -->\n                    <div class=\"omit\" data-role=\"right-btn\">\n                        <i class=\"icon-omit\"></i>\n                    </div>\n                    <!-- ���� end -->\n                ";
  }

function program12(depth0,data) {
  
  var buffer = "", stack1;
  buffer += "\n                    <!-- ���� -->\n                    <div class=\"side-txt\" style=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.show_txt)),stack1 == null || stack1 === false ? stack1 : stack1.style)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" data-role=\"right-btn\">\n                        "
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.show_txt)),stack1 == null || stack1 === false ? stack1 : stack1.content)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\n                    </div>\n                    <!-- end -->\n                ";
  return buffer;
  }

function program14(depth0,data) {
  
  
  return "\n                    <div class=\"search\" data-role=\"right-btn\">\n                        <i class=\"icon-search\"></i>\n                    </div>\n                ";
  }

  buffer += "\n";
  stack1 = helpers['if'].call(depth0, (depth0 && depth0.header_show), {hash:{},inverse:self.noop,fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  return buffer;
  });
        self.render_ele.html(template(data));

        self.$el = self.render_ele;

        self.setup_event(self.$el);
    },
    setup_event : function($el)
    {
        var self = this;

        var use_page_back = self.options.use_page_back == null ? true : false;

        self.$el.find('[data-role="page-back"]').on('click',function()
        {


            if(use_page_back)
            {
                self.page_back();
            }
            else
            {
                self.$el.trigger('click:left_btn');
            }


        });

        self.$el.find('[data-role="right-btn"]').on('click',function()
        {
            self.$el.trigger('click:right_btn');
        });

    },

    
    page_back : function()
    {
        var self = this;

        if(document.referrer)
        {
            window.history.back();
            return false;
        }
        else
        {
            window.location.href = __index_url_link ;
        }



    },
    set_bar_css : function()
    {
        var self = this;
        $("[role=main]").css({
            paddingTop: '0'
        });
    }
};

exports.init = function(options)
{
    return new header_class(options);
};

 
});