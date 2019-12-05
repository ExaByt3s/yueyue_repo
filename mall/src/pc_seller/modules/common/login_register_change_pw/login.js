'use strict';

/**依赖文件，要在注释上使用**/

/**
 * @require ./login.scss
**/

var $ = require('jquery');

var cookie = require('../cookie/index');

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

            // 手机号码匹配
            var phone_reg = new RegExp(/^[0-9]{11}$/);
            var new_phone_test = phone_reg.test(phone_number_val); 

            if (!new_phone_test) 
            {
                self.err_tips.removeClass('fn-hide');
                self.err_tips.find('[err_tips_txt]').html('手机号码错误，请重新输入');
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