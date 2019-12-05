define('common/login_register_change_pw/register', function(require, exports, module){ 'use strict';

/**�����ļ���Ҫ��ע����ʹ��**/

/**
 * @require modules/common/login_register_change_pw/login.scss
**/

var $ = require('components/jquery/jquery.js');

var cookie = require('common/cookie/index');

//2015-11-19���
var countdown = require('common/countdown_geetest/countdown_geetest');
//var countdown = require('../countdown/countdown');

var utility = require('common/utility/index'); 




module.exports = 
{
    
    render: function (dom,r_url) 
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
  var buffer = "", stack1, helper, functionType="function", escapeExpression=this.escapeExpression;


  buffer += "<!-- ע��  -->\n<iframe id=\"form_iframe\" name=\"form_iframe\" style=\"display:none;\"></iframe>\n\n\n<form action=\"../action/general_register_op.php\" method=\"post\" name=\"register_form\" id=\"register_form\" target=\"form_iframe\">\n    <div class=\"register-page\">\n    \n        <div class=\"err-tips fn-hide \"  id=\"error_tips\" err_tips><i class=\"icon-error\"></i> <i err_tips_txt  id=\"err_tips_txt\"></i></div>\n        <div class=\"item-wrap\">\n\n            <div class=\"item mb20\">\n                <input type=\"tel\" phone_number name=\"phone\" id=\"phone\" class=\"ui-input ui-input-block ui-input-primary ui-input-size-middle f14 \" placeholder=\"�ֻ�����\">\n                <p class=\"cor-aaa mt5   \">�������й���½�ֻ��ţ����ڵ�¼�������һ�</p>\n            </div>\n\n            <div class=\"item\">\n                <input pass_word type=\"password\" name=\"password\" id=\"password\" class=\"ui-input ui-input-block ui-input-primary ui-input-size-middle f14 \" placeholder=\"����\">\n                <p class=\"cor-aaa mt5   \">������6-32�����֡��ַ����Ǳ��������Խ����Խ��ȫ��������ʹ������</p>\n\n            </div>\n\n            <div class=\"item mt20 mb10 clearfix\">\n                <div class=\"fl lbox\"><input yzm_number  type=\"tel\" name=\"active_word\" id=\"active_word\" class=\"ui-input ui-input-block ui-input-primary ui-input-size-middle f14 yzm \" placeholder=\"��֤��\"></div>\n                <div class=\"fr rbox\">\n                    <div class=\"btn-tel\"  msg_num_geetest>��ȡ������֤��</div>\n                </div>\n            \n            </div>\n        </div>\n\n            <!--У��-->\n            <div id=\"div_geetest_lib\" style=\"margin-bottom:10px;\"><div class=\"box\" style=\"margin:0 auto;width:320px;text-align:center;\" id=\"div_id_embed\"></div></div>\n            <!--У��-->\n\n\n        <p class=\"xy\"><label><input type=\"checkbox\"  checked=\"checked\" name=\"agreement\" id=\"regist_agreement\" class=\"input-checkbox\" /> <span class=\"cor-aaa\">�����Ķ���ͬ��</span> <a href=\"./agree.php\" target=\"_blank\" class=\"cor-409\">���û�Э�顷</a></label></p>\n\n\n        <div class=\"btn mt10\">\n            <a href=\"javascript:void(0)\"  class=\"tdn\">\n                <div  btn_submit  class=\"ui-button ui-button-primary ui-button-block ui-button-100per  ui-button-size-s-large\">\n                    <span class=\"ui-button-content\"> <em id='submit_btn'>����ע��</em></span>\n                </div>\n            </a>\n        </div>\n\n        <div class=\"des clearfix mt20 tc\">\n            <span class=\"\"><a href=\"login.php?r_url=";
  if (helper = helpers.r_url) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.r_url); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" class=\"cor-409\">�����˺�?��ֱ�ӵ�¼</a></span>\n        </div>\n\n\n        <input type=\"hidden\" value=\"";
  if (helper = helpers.r_url) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.r_url); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "\" name=\"r_url\"></input>  <!--  ��Դ��ַ���� -->\n        <input type=\"hidden\" value=\"form\" name=\"action_mode\"></input> \n        <input type=\"hidden\" value=\"\" name=\"role\"></input> \n\n    </div>\n\n</form>\n\n";
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
        self.yzm_number = $('[yzm_number]');
        self.pass_word = $('[pass_word]');

        self.submit = $('[btn_submit]');
        self.err_tips = $('[err_tips]');
        self.submit_btn_txt = $('#submit_btn');
        self.msg_num = $('[msg_num_geetest]');

        self.regist_agreement = $('#regist_agreement');
        self.device = $("#J_is_pc").val();//2015-11-19���
        self.token = $("#J_token").val();//2015-11-19���


        // ʵ����1
        var obj_1 = new countdown({
            _phone_number_ele : self.phone_number , //Ҫ������֤����ֻ�����
            _time_limit : 60 , //����ʱ������
            _ele : self.msg_num, //Ҫ����ʱ�Ľڵ�
            _stop_class : 'stop', //ֹͣ����ʽ
            _again_txt : '���»�ȡ' ,//��ʾ������
            device : self.device,//2015-11-19���
            // ajax ����
            _ajax : 
            {
                //url : "../action/get_active_word_ajax.php",
                url : "../action/get_active_word_ajax_level.php",//2015-11-19���
                dataType : 'JSON',//2015-11-19���dataType : 'html',
                data : {
                    action : '9de4a97425678c5b1288aa70c1669a64',
                    token : self.token//2015-11-19���
                },
                beforeSend : function()
                {

                },
                callback : function(res)
                {
                    // ����ص�
                    //2015-11-19���
                    if(res.ajax_status==-2)
                    {
                        $("#error_tips").css("display","block");
                        $("#err_tips_txt").html(res.msg);

                    }
                    else if(res.ajax_status==1)
                    {
                        obj_1._count_down(60);
                    }
                    //2015-11-19���

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

        $('[btn_submit]').on('click', function() {
            console.log('���');
            var phone_number_val = self.phone_number.val().trim();
            var pass_word_val = self.pass_word.val().trim();
            var yzm_number_val = self.yzm_number.val().trim();

            if (!phone_number_val) 
            {
                self.err_tips.removeClass('fn-hide');
                self.err_tips.find('[err_tips_txt]').html('�������ֻ�����')
                return ;
            }


            // �ֻ�����ƥ��
            var phone_reg = new RegExp(/^[0-9]{11}$/);
            var new_phone_test = phone_reg.test(phone_number_val); //get_alipay ���ַ���

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

            if (pass_word_val.length < 6) 
            {
                self.err_tips.removeClass('fn-hide');
                self.err_tips.find('[err_tips_txt]').html('���벻������6λ');
                return ;
            }


            if(pass_word_val.length >= 32)
            {
               self.err_tips.removeClass('fn-hide');
                self.err_tips.find('[err_tips_txt]').html('���벻�ܴ��ڻ����32λ');
                return ;
            }


            if( self.isChina(pass_word_val) )
            { 
                self.err_tips.removeClass('fn-hide');
                self.err_tips.find('[err_tips_txt]').html('���벻�ܺ�������');
                return ;
            }

            if( !isNaN(pass_word_val) )
            { 
                self.err_tips.removeClass('fn-hide');
                self.err_tips.find('[err_tips_txt]').html('���벻��ȫ��Ϊ����');
                return ;
            }
            

            if (!yzm_number_val) 
            {
                self.err_tips.removeClass('fn-hide');
                self.err_tips.find('[err_tips_txt]').html('��������֤��');
                return ;
            }



            if (!self.regist_agreement.is(':checked') ) 
            {
                self.err_tips.removeClass('fn-hide');
                self.err_tips.find('[err_tips_txt]').html('�빴ѡע��Э�飡');
                return ;
            }

            self.submit_btn_txt.html('�ύ��...');
            $('#register_form').submit();

            
        });

    },

    isChina : function(s) 
    {
        var patrn=/[\u4E00-\u9FA5]|[\uFE30-\uFFA0]/gi;   
        // [\u4E00-\u9FA5]��ʾ���֣�[\uFE30-\uFFA0]��ʾȫ��  
        if(!patrn.exec(s))
        {   
            return false;   
        }  
        else
        {   
            return true;   
        }   
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