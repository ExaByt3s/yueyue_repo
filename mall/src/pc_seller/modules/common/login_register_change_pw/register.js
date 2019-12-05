'use strict';

/**依赖文件，要在注释上使用**/

/**
 * @require ./login.scss
**/

var $ = require('jquery');

var cookie = require('../cookie/index');

var countdown = require('../countdown/countdown');

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

        var template  = __inline('./register.tmpl');  

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

        self.regist_agreement = $('#regist_agreement');


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

        $('[btn_submit]').on('click', function(event) {

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

            if (!pass_word_val) 
            {
                self.err_tips.removeClass('fn-hide');
                self.err_tips.find('[err_tips_txt]').html('请输入密码')
                return ;
            }

            if (!yzm_number_val) 
            {
                self.err_tips.removeClass('fn-hide');
                self.err_tips.find('[err_tips_txt]').html('请输入验证码');
                return ;
            }



            if (!self.regist_agreement.is(':checked') ) 
            {
                self.err_tips.removeClass('fn-hide');
                self.err_tips.find('[err_tips_txt]').html('请勾选注册协议！');
                return ;
            }

            self.submit_btn_txt.html('提交中...');
            $('#register_form').submit();

            
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