define('common/login_register_change_pw/login', function(require, exports, module){ 'use strict';

/**�����ļ���Ҫ��ע����ʹ��**/

/**
 * @require modules/common/login_register_change_pw/login.scss
**/

var $ = require('components/jquery/jquery.js');

var cookie = require('common/cookie/index');

module.exports = 
{
    render: function (dom) 
    {
        // tpl��׺���ļ�Ҳ��������ģ��Ƕ�룬���handlebars
        // tpl�ļ�������ģ��������ܣ�Ƕ���ֻ����Ϊ�ַ���ʹ
        // �ã�tpl�ļ�Ƕ��֮ǰ���Ա����ѹ���������С��
        // handlebars����ȱ����Ӧ��ѹ����������ʱ������Ԥ
        // ����׶���ѹ����ѡ��tpl����handlebars��������ѡ��            
        
        var self = this;

        var template  = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  


  return "<!-- ��½  -->\n<iframe id=\"form_iframe\" name=\"form_iframe\" style=\"display:none;\"></iframe>\n\n<form action=\"../action/general_login_op.php\" method=\"post\" name=\"login_form\" id=\"login_form\" target=\"form_iframe\">\n\n    <div class=\"login-page\">\n        <div class=\"err-tips fn-hide\" err_tips id=\"error_tips\"><i class=\"icon-error\"></i> <span err_tips_txt  id=\"err_tips_txt\"></span></div>\n        <div class=\"item-wrap\">\n            <div class=\"item mb20\"><input type=\"tel\"  name=\"phone\" id=\"phone\" class=\"ui-input ui-input-block ui-input-primary ui-input-size-middle f14 \" placeholder=\"�������ֻ�����\"  phone_number ></div>\n            <div class=\"item\"><input  type=\"password\" name=\"password\" id=\"password\" class=\"ui-input ui-input-block ui-input-primary ui-input-size-middle f14 \" placeholder=\"����������\" pass_word></div>\n        </div>\n<!--         <div class=\"checkbox-box cor-aaa\"><input type=\"checkbox\" name=\" \" id=\" \" class=\"input-checkbox\" /> �´��Զ���¼</div> -->\n\n        <div class=\"btn mt20\">\n            <a href=\"javascript:void(0)\"  class=\"tdn\">\n                <div  btn_submit class=\"ui-button ui-button-primary ui-button-block ui-button-100per  ui-button-size-s-large\">\n                    <span class=\"ui-button-content\"><em id=\"submit_btn_txt\">��¼</em></span>\n                </div>\n            </a>\n        </div>\n\n        <div class=\"des clearfix mt20\">\n            <span class=\"fl\"><a href=\"register.php\" class=\"cor-409\">���û�ע��</a></span>\n            <span class=\"fr\"><a href=\"change_pw.php\" class=\"cor-409\">��������</a></span>\n        </div>\n\n        <input type=\"hidden\" value=\"\" name=\"r_url\"></input>  <!--  ��Դ��ַ���� -->\n        <!-- <input type=\"hidden\" value=\"{r_url}\" name=\"r_url\"></input> -->\n        <input type=\"hidden\" value=\"form\" name=\"action_mode\"></input> \n\n    </div>\n\n</form>\n\n\n";
  });  

        dom.innerHTML = template({});        
        
        self.init();
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
        var r_url = '' ; //��Դ��ַ;

        self.submit.on('click', function() {

            var phone_number_val = self.phone_number.val().trim();
            var pass_word_val = self.pass_word.val().trim();
            if (!phone_number_val) 
            {
                self.err_tips.removeClass('fn-hide');
                self.err_tips.find('[err_tips_txt]').html('�������ֻ�����')
                return ;
            }

            // �ֻ�����ƥ��
            var phone_reg = new RegExp(/^[0-9]{11}$/);
            var new_phone_test = phone_reg.test(phone_number_val); 

            if (!new_phone_test) 
            {
                self.err_tips.removeClass('fn-hide');
                self.err_tips.find('[err_tips_txt]').html('�ֻ������������������');
                return ;
            }

            if (!pass_word_val) 
            {
                self.err_tips.removeClass('fn-hide');
                self.err_tips.find('[err_tips_txt]').html('����������')
                return ;
            }

            self.submit_btn_txt.html('��¼��...');
            $('#login_form').submit();

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