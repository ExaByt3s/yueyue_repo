define('common/widget/header/main', function(require, exports, module){ 'use strict';

/**�����ļ���Ҫ��ע����ʹ��**/

/**
 * @require modules/common/widget/header/header.scss
**/

var $ = require('components/jquery/jquery.js');
var cookie = require('common/cookie/index');

module.exports = 
{
    render: function (dom,user_role) {
        // tpl��׺���ļ�Ҳ��������ģ��Ƕ�룬���handlebars
        // tpl�ļ�������ģ��������ܣ�Ƕ���ֻ����Ϊ�ַ���ʹ
        // �ã�tpl�ļ�Ƕ��֮ǰ���Ա����ѹ���������С��
        // handlebars����ȱ����Ӧ��ѹ����������ʱ������Ԥ
        // ����׶���ѹ����ѡ��tpl����handlebars��������ѡ
        // ��			
		
		var self = this;

		if(cookie.get('yue_member_id')>0)
		{
			self.get_user_info
			({
				beforeSend : function()
				{
					dom.innerHTML = '<a class="rbox frdi header-ajax-text" href="javascript:void();">������...</a>';
				},
				callback : function(res)
				{
					var template  = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, self=this, helperMissing=helpers.helperMissing, functionType="function", escapeExpression=this.escapeExpression;

function program1(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n\n<!--  �̼���Ϣ��  -->\n\n<div class=\"seller-info\">\n    <div class=\"login-out frdi\"><span class=\"name fldi\"><a href='logout.php'>�˳�</a></span></div>   \n    <span class=\"frdi mt20 ml10 pt5\">|</span>   \n    <div class=\"rbox frdi\">\n\n        <a href=\"person_center.php\" >\n            ";
  stack1 = (helper = helpers.if_equal || (depth0 && depth0.if_equal),options={hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data},helper ? helper.call(depth0, ((stack1 = (depth0 && depth0.user)),stack1 == null || stack1 === false ? stack1 : stack1.is_vip), "1", options) : helperMissing.call(depth0, "if_equal", ((stack1 = (depth0 && depth0.user)),stack1 == null || stack1 === false ? stack1 : stack1.is_vip), "1", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            <span class=\"img fldi\"><img src=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.user)),stack1 == null || stack1 === false ? stack1 : stack1.avatar)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" /></span>\n            <span class=\"name fldi\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.user)),stack1 == null || stack1 === false ? stack1 : stack1.nickname)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span>\n            \n            <!-- <span class=\"allow fldi\"></span> -->\n\n        </a> \n    </div>\n</div>\n\n\n";
  return buffer;
  }
function program2(depth0,data) {
  
  
  return "\n            <!-- IF is_vip=\"1\" -->\n            <div class=\"icon-v\"></div>\n            <!-- ENDIF -->\n            ";
  }

function program4(depth0,data) {
  
  var buffer = "", stack1, helper, options;
  buffer += "\n\n\n<!--  ��������Ϣ��  -->\n<div class=\"consumers-info\">\n    <div class=\"login-out frdi\"><span class=\"name fldi\"><a href='logout.php'>�˳�</a></span></div>      \n    <div class=\"rbox frdi\">\n\n        <a href=\"#\" >\n            ";
  stack1 = (helper = helpers.if_equal || (depth0 && depth0.if_equal),options={hash:{},inverse:self.noop,fn:self.program(2, program2, data),data:data},helper ? helper.call(depth0, ((stack1 = (depth0 && depth0.user)),stack1 == null || stack1 === false ? stack1 : stack1.is_vip), "1", options) : helperMissing.call(depth0, "if_equal", ((stack1 = (depth0 && depth0.user)),stack1 == null || stack1 === false ? stack1 : stack1.is_vip), "1", options));
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n            <span class=\"img fldi\"><img src=\""
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.user)),stack1 == null || stack1 === false ? stack1 : stack1.avatar)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "\" /></span>\n            <span class=\"name fldi\">"
    + escapeExpression(((stack1 = ((stack1 = (depth0 && depth0.user)),stack1 == null || stack1 === false ? stack1 : stack1.nickname)),typeof stack1 === functionType ? stack1.apply(depth0) : stack1))
    + "</span>\n            <span class=\"allow fldi\"></span>\n\n        </a> \n    </div>\n\n    ";
  return buffer;
  }

  stack1 = helpers['if'].call(depth0, (depth0 && depth0.user_role), {hash:{},inverse:self.program(4, program4, data),fn:self.program(1, program1, data),data:data});
  if(stack1 || stack1 === 0) { buffer += stack1; }
  buffer += "\n</div>";
  return buffer;
  });	

					dom.innerHTML = '';

					dom.innerHTML = template(
						{
							user_role : user_role ,
							user : res.data.user_info ,
							login : 1
						}, 
						{
							helpers : 
							{
								if_equal:is_equal
							}
						}
					);			
				},
				error : function()
				{
					dom.innerHTML = '<a class="rbox frdi" id="header-fail-load-btn" href="javascript:void();">�����쳣</a>';
				}
			});
		}		
		

		
    },
	get_user_info : function(options)
	{
		$.ajax
		({
			url : 'http://www.yueus.com/task/module_common/get_login_info.header.json.php?callback=?',
			type : 'GET',
			data : 
			{
				user_id : cookie.get('yue_member_id')
			},
			dataType : 'JSONP',
			cache : true,
			beforeSend : function()
			{
				options.beforeSend && options.beforeSend.call(this);
			},
			success : function(res)
			{

				options.callback && options.callback.call(this,res);
					
			},
			error : function()
			{
				options.error && options.error.call(this);
			}
		});			
	}
};

/*
  �ж��Ƿ���ȵ�ģ�庯��
*/
function is_equal(a,b,options)
{
	if(a == b)
	{
		return options.fn(this);
	}
	else
	{
		return options.inverse(this);
	}
} 
});