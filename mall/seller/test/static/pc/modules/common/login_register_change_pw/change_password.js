define('common/login_register_change_pw/change_password', function(require, exports, module){ 'use strict';

/**依赖文件，要在注释上使用**/

/**
 * @require modules/common/login_register_change_pw/login.scss
**/

var $ = require('components/jquery/jquery.js');

var cookie = require('common/cookie/index');

var countdown = require('common/countdown/countdown');

module.exports = 
{
    render: function (dom) 
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
  


  return "<!-- 修改密码  -->\n<iframe id=\"form_iframe\" name=\"form_iframe\" style=\"display:none;\"></iframe>\n\n<form action=\"../action/general_reset_op.php\" method=\"post\" name=\"change_pw_form\" id=\"change_pw_form\" target=\"form_iframe\">\n    <div class=\"change-password-page\">\n        <div class=\"err-tips fn-hide\" err_tips id=\"error_tips\"><i class=\"icon-error\"></i> <span err_tips_txt  id=\"err_tips_txt\"> </span></div>\n        <div class=\"item-wrap\">\n\n            <div class=\"item mb20\">\n                <input phone_number type=\"tel\" name=\"phone\" id=\"phone\" class=\"ui-input ui-input-block ui-input-primary ui-input-size-middle f14 \" placeholder=\"请输入注册手机号码\">\n            </div>\n\n            \n\n            <div class=\"item mt20 mb20 clearfix\">\n                <div  class=\"fl lbox\"><input yzm_number type=\"tel\" name=\"active_word\" id=\"active_word\" class=\"ui-input ui-input-block ui-input-primary ui-input-size-middle f14 yzm \" placeholder=\"验证码\"></div>\n                <div class=\"fr rbox\">\n                    <div class=\"btn-tel\" msg_num>获取短信验证码</div>\n                </div>\n            \n            </div>\n\n\n            <div class=\"item\">\n                <input  pass_word  type=\"password\" name=\"password\" id=\"password\" class=\"ui-input ui-input-block ui-input-primary ui-input-size-middle f14 \" placeholder=\"请输入新的密码\">\n            </div>\n        </div>\n\n\n        <div class=\"btn mt20\">\n            <a href=\"javascript:void(0)\"  class=\"tdn\">\n                <div btn_submit class=\"ui-button ui-button-primary ui-button-block ui-button-100per  ui-button-size-s-large\">\n                    <span class=\"ui-button-content\"><em id=\"submit_btn\">立即修改</em></span>\n                </div>\n            </a>\n        </div>\n\n        <input type=\"hidden\" value=\"\" name=\"r_url\"></input>  <!--  来源地址配置 -->\n        <input type=\"hidden\" value=\"form\" name=\"action_mode\"></input> \n        \n\n\n    </div>\n\n</form>";
  });  

        dom.innerHTML = template({});        
        
        self.init();
    },

    init : function () 
    {
        var self = this;

        self.phone_number = $('[phone_number]');
        self.yzm_number = $('[yzm_number]');
        self.pass_word = $('[pass_word]');
        self.submit = $('[btn_submit]');
        self.err_tips = $('[err_tips]');
        self.submit_btn_txt = $('#submit_btn');
        self.msg_num = $('[msg_num]');
        self.config = false ;

        // 实例化1
        var obj_1 = new countdown({
            _phone_number_ele : self.phone_number , //要接受验证码的手机号码
            _time_limit : 60 , //倒计时多少秒
            _ele : self.msg_num, //要倒计时的节点
            _stop_class : 'stop', //停止的样式
            _again_txt : '重新获取' ,//显示的文字
            // ajax 请求
            _ajax : 
            {
                url : "../action/get_active_word_ajax.php",  
                dataType: 'html',
                beforeSend : function()
                {

                },
                callback : function(res)
                {
                    // 请求回调
                    console.log(res);

                },
                error : function()
                {
                   
                }
            }
        })


        self._setup_event();

    },

    _setup_event: function () 
    {

        var self = this;


        self.submit.on('click', function() {

            var phone_number_val = self.phone_number.val().trim();
            var pass_word_val = self.pass_word.val().trim();
            var yzm_number_val = self.yzm_number.val().trim();

            if (!phone_number_val) 
            {
                self.err_tips.removeClass('fn-hide');
                self.err_tips.find('[err_tips_txt]').html('请输入手机号码')
                return ;
            }


            // 手机号码匹配
            var phone_reg = new RegExp(/^[0-9]{11}$/);
            var new_phone_test = phone_reg.test(phone_number_val); //get_alipay 是字符串

            if (!new_phone_test) 
            {
                self.err_tips.removeClass('fn-hide');
                self.err_tips.find('[err_tips_txt]').html('手机号码错误，请重新输入');
                return ;
            }

            if (!yzm_number_val) 
            {
                self.err_tips.removeClass('fn-hide');
                self.err_tips.find('[err_tips_txt]').html('请输入验证码');
                return ;
            }

            if (!pass_word_val) 
            {
                self.err_tips.removeClass('fn-hide');
                self.err_tips.find('[err_tips_txt]').html('请输入新的密码')
                return ;
            }

            self.submit_btn_txt.html('提交中...');

            $('#change_pw_form').submit();

            // $.ajax({
            //     url: '../../../action/general_login_op.php',
            //     data: {
            //         action_mode : 'form',
            //         phone : phone_number_val,
            //         password : pass_word_val,
            //         r_url : r_url
            //     },
            //     dataType: 'html',
            //     type: 'POST',
            //     cache: false,
            //     beforeSend: function() 
            //     {
                    
            //     },
            //     success: function(data) 
            //     {
                    
            //     },    
            //     error: function() 
            //     {
                    
            //     },    
            //     complete: function() 
            //     {
                    
            //     } 
            // });

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