define('common/login_register_change_pw/login', function(require, exports, module){ 'use strict';

/**依赖文件，要在注释上使用**/

/**
 * @require modules/common/login_register_change_pw/login.scss
**/

var $ = require('components/jquery/jquery.js');

var cookie = require('common/cookie/index');

var utility = require('common/utility/index'); 



module.exports = 
{
    render: function (dom,r_url) 
    {
        // tpl后缀的文件也可以用于模板嵌入，相比handlebars
        // tpl文件不具有模板变量功能，嵌入后只是作为字符串使
        // 用，tpl文件嵌入之前可以被插件压缩，体积更小。
        // handlebars由于缺少相应的压缩插件因此暂时不能在预
        // 编译阶段做压缩。选择tpl还是handlebars可以自由选择            
        
        var self = this;

        var template  = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, functionType="function", escapeExpression=this.escapeExpression;


  buffer += "<!-- 登陆  -->\n<iframe id=\"form_iframe\" name=\"form_iframe\" style=\"display:none;\"></iframe>\n\n<form action=\"../action/general_login_op.php\" method=\"post\" name=\"login_form\" id=\"login_form\" target=\"form_iframe\">\n\n    <div class=\"login-page\">\n        <div class=\"err-tips fn-hide\" err_tips id=\"error_tips\"><i class=\"icon-error\"></i> <span err_tips_txt  id=\"err_tips_txt\"></span></div>\n        <div class=\"item-wrap\">\n            <div class=\"item mb20\"><input type=\"tel\"  name=\"phone\" id=\"phone\" class=\"ui-input ui-input-block ui-input-primary ui-input-size-middle f14 \" placeholder=\"请输入手机号码\"  phone_number ></div>\n            <div class=\"item\"><input  type=\"password\" name=\"password\" id=\"password\" class=\"ui-input ui-input-block ui-input-primary ui-input-size-middle f14 \" placeholder=\"请输入密码\" pass_word></div>\n        </div>\n<!--         <div class=\"checkbox-box cor-aaa\"><input type=\"checkbox\" name=\" \" id=\" \" class=\"input-checkbox\" /> 下次自动登录</div> -->\n\n        <div class=\"btn mt20\">\n            <a href=\"javascript:void(0)\"  class=\"tdn\">\n                <div  btn_submit class=\"ui-button ui-button-primary ui-button-block ui-button-100per  ui-button-size-s-large\">\n                    <span class=\"ui-button-content\"><em id=\"submit_btn_txt\">登录</em></span>\n                </div>\n            </a>\n        </div>\n\n        <div class=\"des clearfix mt20\">\n            <span class=\"fl\"><a href=\"register.php?r_url=";
  if (helper = helpers.r_url) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.r_url); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" class=\"cor-409\">新用户注册</a></span>\n            <span class=\"fr\"><a href=\"change_pw.php?r_url=";
  if (helper = helpers.r_url) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.r_url); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" class=\"cor-409\">忘记密码</a></span>\n        </div>\n\n        <input type=\"hidden\" value=\"";
  if (helper = helpers.r_url) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.r_url); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" name=\"r_url\"></input>  <!--  来源地址配置 -->\n        <!-- <input type=\"hidden\" value=\"{r_url}\" name=\"r_url\"></input> -->\n        <input type=\"hidden\" value=\"form\" name=\"action_mode\"></input> \n\n    </div>\n\n</form>\n\n\n";
  return buffer;
  });  

        dom.innerHTML = template({
            r_url : r_url
        });        
         
        self.init();

		// fix placeholder
		utility.fix_placeholder();
    },

    init : function () 
    {
        var self = this;
        self.phone_number = $('[phone_number]');
        self.pass_word = $('[pass_word]');
        self.submit = $('[btn_submit]');
        self.err_tips = $('[err_tips]');
        self.submit_btn_txt = $('#submit_btn_txt');

        self._setup_event();

    },

    _setup_event: function () 
    {

        var self = this;
        var r_url = '' ; //来源地址;

        self.submit.on('click', function() {

            var phone_number_val = self.phone_number.val().trim();
            var pass_word_val = self.pass_word.val().trim();
            if (!phone_number_val) 
            {
                self.err_tips.removeClass('fn-hide');
                self.err_tips.find('[err_tips_txt]').html('请输入手机号码')
                return ;
            }

            // 手机号码匹配  后修改纯为数字
            // var phone_reg = new RegExp(/^[0-9]{11}$/);
            // var new_phone_test = phone_reg.test(phone_number_val); 
            var is_nan_res = isNaN(phone_number_val);


            if (is_nan_res) 
            {
                self.err_tips.removeClass('fn-hide');
                self.err_tips.find('[err_tips_txt]').html('只能是纯数字');
                return ;
            }

            if (!pass_word_val) 
            {
                self.err_tips.removeClass('fn-hide');
                self.err_tips.find('[err_tips_txt]').html('请输入密码')
                return ;
            }

            self.submit_btn_txt.html('登录中...');
            $('#login_form').submit();

        

        });

    }
   
};

/*
  判断是否相等的模板函数
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