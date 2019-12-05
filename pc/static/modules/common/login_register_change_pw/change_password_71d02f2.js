define("common/login_register_change_pw/change_password",function(n,i,t){"use strict";var e=n("components/jquery/jquery.js"),r=(n("common/cookie/index"),n("common/countdown/countdown"));t.exports={render:function(n){var i=this,t=Handlebars.template(function(n,i,t,e,r){return this.compilerInfo=[4,">= 1.0.0"],t=this.merge(t,n.helpers),r=r||{},'<!-- 修改密码  -->\n<iframe id="form_iframe" name="form_iframe" style="display:none;"></iframe>\n\n<form action="../action/general_reset_op.php" method="post" name="change_pw_form" id="change_pw_form" target="form_iframe">\n    <div class="change-password-page">\n        <div class="err-tips fn-hide" err_tips id="error_tips"><i class="icon-error"></i> <span err_tips_txt  id="err_tips_txt"> </span></div>\n        <div class="item-wrap">\n\n            <div class="item mb20">\n                <input phone_number type="tel" name="phone" id="phone" class="ui-input ui-input-block ui-input-primary ui-input-size-middle f14 " placeholder="请输入注册手机号码">\n            </div>\n\n            \n\n            <div class="item mt20 mb20 clearfix">\n                <div  class="fl lbox"><input yzm_number type="tel" name="active_word" id="active_word" class="ui-input ui-input-block ui-input-primary ui-input-size-middle f14 yzm " placeholder="验证码"></div>\n                <div class="fr rbox">\n                    <div class="btn-tel" msg_num>获取短信验证码</div>\n                </div>\n            \n            </div>\n\n\n            <div class="item">\n                <input  pass_word  type="password" name="password" id="password" class="ui-input ui-input-block ui-input-primary ui-input-size-middle f14 " placeholder="请输入新的密码(密码不能少于6位)">\n            </div>\n        </div>\n\n\n        <div class="btn mt20">\n            <a href="javascript:void(0)"  class="tdn">\n                <div btn_submit class="ui-button ui-button-primary ui-button-block ui-button-100per  ui-button-size-s-large">\n                    <span class="ui-button-content"><em id="submit_btn">立即修改</em></span>\n                </div>\n            </a>\n        </div>\n\n        <input type="hidden" value="" name="r_url"></input>  <!--  来源地址配置 -->\n        <input type="hidden" value="form" name="action_mode"></input> \n        \n\n\n    </div>\n\n</form>'});n.innerHTML=t({}),i.init()},init:function(){var n=this;n.phone_number=e("[phone_number]"),n.yzm_number=e("[yzm_number]"),n.pass_word=e("[pass_word]"),n.submit=e("[btn_submit]"),n.err_tips=e("[err_tips]"),n.submit_btn_txt=e("#submit_btn"),n.msg_num=e("[msg_num]"),n.config=!1;new r({_phone_number_ele:n.phone_number,_time_limit:60,_ele:n.msg_num,_stop_class:"stop",_again_txt:"重新获取",_ajax:{url:"../action/get_active_word_ajax.php",dataType:"html",data:{action:"change_pwd"},beforeSend:function(){},callback:function(n){console.log(n)},error:function(){}}});n._setup_event()},_setup_event:function(){var n=this;n.submit.on("click",function(){var i=n.phone_number.val().trim(),t=n.pass_word.val().trim(),r=n.yzm_number.val().trim();if(!i)return n.err_tips.removeClass("fn-hide"),void n.err_tips.find("[err_tips_txt]").html("请输入手机号码");var s=new RegExp(/^[0-9]{11}$/),a=s.test(i);return a?r?t?t.length<6?void n.err_tips.find("[err_tips_txt]").html("密码不能少于6位"):(n.submit_btn_txt.html("提交中..."),void e("#change_pw_form").submit()):(n.err_tips.removeClass("fn-hide"),void n.err_tips.find("[err_tips_txt]").html("请输入新的密码")):(n.err_tips.removeClass("fn-hide"),void n.err_tips.find("[err_tips_txt]").html("请输入验证码")):(n.err_tips.removeClass("fn-hide"),void n.err_tips.find("[err_tips_txt]").html("手机号码错误，请重新输入"))})}}});