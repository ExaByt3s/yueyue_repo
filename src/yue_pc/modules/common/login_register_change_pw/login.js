'use strict';

/**依赖文件，要在注释上使用**/

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
        // tpl后缀的文件也可以用于模板嵌入，相比handlebars
        // tpl文件不具有模板变量功能，嵌入后只是作为字符串使
        // 用，tpl文件嵌入之前可以被插件压缩，体积更小。
        // handlebars由于缺少相应的压缩插件因此暂时不能在预
        // 编译阶段做压缩。选择tpl还是handlebars可以自由选择            
        
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