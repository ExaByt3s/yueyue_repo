define("common/login_register_change_pw/register",function(e,i,t){"use strict";var n=e("components/jquery/jquery.js"),r=(e("common/cookie/index"),e("common/countdown/countdown")),s=e("common/utility/index");t.exports={render:function(e,i){var t=this,n=Handlebars.template(function(e,i,t,n,r){this.compilerInfo=[4,">= 1.0.0"],t=this.merge(t,e.helpers),r=r||{};var s,a,o="",p="function",l=this.escapeExpression;return o+='<!-- 注册  -->\n<iframe id="form_iframe" name="form_iframe" style="display:none;"></iframe>\n\n\n<form action="../action/general_register_op.php" method="post" name="register_form" id="register_form" target="form_iframe">\n    <div class="register-page">\n    \n        <div class="err-tips fn-hide "  id="error_tips" err_tips><i class="icon-error"></i> <i err_tips_txt  id="err_tips_txt"></i></div>\n        <div class="item-wrap">\n\n            <div class="item mb20">\n                <input type="tel" phone_number name="phone" id="phone" class="ui-input ui-input-block ui-input-primary ui-input-size-middle f14 " placeholder="手机号码">\n                <p class="cor-aaa mt5   ">请输入中国大陆手机号，用于登录和密码找回</p>\n            </div>\n\n            <div class="item">\n                <input pass_word type="password" name="password" id="password" class="ui-input ui-input-block ui-input-primary ui-input-size-middle f14 " placeholder="密码">\n                <p class="cor-aaa mt5   ">密码由6-32个数字、字符或半角标点符号组成越复杂越安全，但不能使用中文</p>\n\n            </div>\n\n            <div class="item mt20 mb10 clearfix">\n                <div class="fl lbox"><input yzm_number  type="tel" name="active_word" id="active_word" class="ui-input ui-input-block ui-input-primary ui-input-size-middle f14 yzm " placeholder="验证码"></div>\n                <div class="fr rbox">\n                    <div class="btn-tel"  msg_num>获取短信验证码</div>\n                </div>\n            \n            </div>\n        </div>\n\n        <p class="xy"><label><input type="checkbox"  checked="checked" name="agreement" id="regist_agreement" class="input-checkbox" /> <span class="cor-aaa">我已阅读并同意</span> <a href="./agree.php" target="_blank" class="cor-409">《用户协议》</a></label></p>\n\n\n        <div class="btn mt10">\n            <a href="javascript:void(0)"  class="tdn">\n                <div  btn_submit  class="ui-button ui-button-primary ui-button-block ui-button-100per  ui-button-size-s-large">\n                    <span class="ui-button-content"> <em id=\'submit_btn\'>立即注册</em></span>\n                </div>\n            </a>\n        </div>\n\n        <div class="des clearfix mt20 tc">\n            <span class=""><a href="login.php?r_url=',(a=t.r_url)?s=a.call(i,{hash:{},data:r}):(a=i&&i.r_url,s=typeof a===p?a.call(i,{hash:{},data:r}):a),o+=l(s)+'" class="cor-409">已有账号?请直接登录</a></span>\n        </div>\n\n\n        <input type="hidden" value="',(a=t.r_url)?s=a.call(i,{hash:{},data:r}):(a=i&&i.r_url,s=typeof a===p?a.call(i,{hash:{},data:r}):a),o+=l(s)+'" name="r_url"></input>  <!--  来源地址配置 -->\n        <input type="hidden" value="form" name="action_mode"></input> \n        <input type="hidden" value="" name="role"></input> \n\n    </div>\n\n</form>\n\n'});e.innerHTML=n({r_url:i}),t.init(),s.fix_placeholder()},init:function(){var e=this;e.phone_number=n("[phone_number]"),e.yzm_number=n("[yzm_number]"),e.pass_word=n("[pass_word]"),e.submit=n("[btn_submit]"),e.err_tips=n("[err_tips]"),e.submit_btn_txt=n("#submit_btn"),e.msg_num=n("[msg_num]"),e.regist_agreement=n("#regist_agreement");new r({_phone_number_ele:e.phone_number,_time_limit:60,_ele:e.msg_num,_stop_class:"stop",_again_txt:"重新获取",_ajax:{url:"../action/get_active_word_ajax.php",dataType:"html",data:{action:"register"},beforeSend:function(){},callback:function(e){console.log(e)},error:function(){}}});e._setup_event()},_setup_event:function(){var e=this;n("[btn_submit]").on("click",function(){console.log("点击");var i=e.phone_number.val().trim(),t=e.pass_word.val().trim(),r=e.yzm_number.val().trim();if(!i)return e.err_tips.removeClass("fn-hide"),void e.err_tips.find("[err_tips_txt]").html("请输入手机号码");var s=new RegExp(/^[0-9]{11}$/),a=s.test(i);return a?t?t.length<6?(e.err_tips.removeClass("fn-hide"),void e.err_tips.find("[err_tips_txt]").html("密码不能少于6位")):t.length>=32?(e.err_tips.removeClass("fn-hide"),void e.err_tips.find("[err_tips_txt]").html("密码不能大于或等于32位")):e.isChina(t)?(e.err_tips.removeClass("fn-hide"),void e.err_tips.find("[err_tips_txt]").html("密码不能含有中文")):isNaN(t)?r?e.regist_agreement.is(":checked")?(e.submit_btn_txt.html("提交中..."),void n("#register_form").submit()):(e.err_tips.removeClass("fn-hide"),void e.err_tips.find("[err_tips_txt]").html("请勾选注册协议！")):(e.err_tips.removeClass("fn-hide"),void e.err_tips.find("[err_tips_txt]").html("请输入验证码")):(e.err_tips.removeClass("fn-hide"),void e.err_tips.find("[err_tips_txt]").html("密码不能全部为数字")):(e.err_tips.removeClass("fn-hide"),void e.err_tips.find("[err_tips_txt]").html("请输入密码")):(e.err_tips.removeClass("fn-hide"),void e.err_tips.find("[err_tips_txt]").html("手机号码错误，请重新输入"))})},isChina:function(e){var i=/[\u4E00-\u9FA5]|[\uFE30-\uFFA0]/gi;return i.exec(e)?!0:!1}}});