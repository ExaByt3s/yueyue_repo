'use strict';

/**�����ļ���Ҫ��ע����ʹ��**/

/**
 * @require ./login.scss
**/

var $ = require('jquery');

var cookie = require('../cookie/index');

var utility = require('../utility/index'); 



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

        var template  = __inline('./login.tmpl');  

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

            // �ֻ�����ƥ��  ���޸Ĵ�Ϊ����
            // var phone_reg = new RegExp(/^[0-9]{11}$/);
            // var new_phone_test = phone_reg.test(phone_number_val); 
            var is_nan_res = isNaN(phone_number_val);


            if (is_nan_res) 
            {
                self.err_tips.removeClass('fn-hide');
                self.err_tips.find('[err_tips_txt]').html('ֻ���Ǵ�����');
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