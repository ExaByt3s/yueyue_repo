'use strict';

/**�����ļ���Ҫ��ע����ʹ��**/

/**
 * @require ./login.scss
**/

var $ = require('jquery');

var cookie = require('../cookie/index');

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

        var template  = __inline('./login.tmpl');  

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