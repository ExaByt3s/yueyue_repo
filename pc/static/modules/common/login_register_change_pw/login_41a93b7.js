define("common/login_register_change_pw/login",function(i,n,t){"use strict";{var e=i("components/jquery/jquery.js");i("common/cookie/index")}t.exports={render:function(i,n){var t=this,e=Handlebars.template(function(i,n,t,e,r){this.compilerInfo=[4,">= 1.0.0"],t=this.merge(t,i.helpers),r=r||{};var s,a,p="",o="function",u=this.escapeExpression;return p+='<!-- ��½  -->\n<iframe id="form_iframe" name="form_iframe" style="display:none;"></iframe>\n\n<form action="../action/general_login_op.php" method="post" name="login_form" id="login_form" target="form_iframe">\n\n    <div class="login-page">\n        <div class="err-tips fn-hide" err_tips id="error_tips"><i class="icon-error"></i> <span err_tips_txt  id="err_tips_txt"></span></div>\n        <div class="item-wrap">\n            <div class="item mb20"><input type="tel"  name="phone" id="phone" class="ui-input ui-input-block ui-input-primary ui-input-size-middle f14 " placeholder="�������ֻ�����"  phone_number ></div>\n            <div class="item"><input  type="password" name="password" id="password" class="ui-input ui-input-block ui-input-primary ui-input-size-middle f14 " placeholder="����������" pass_word></div>\n        </div>\n<!--         <div class="checkbox-box cor-aaa"><input type="checkbox" name=" " id=" " class="input-checkbox" /> �´��Զ���¼</div> -->\n\n        <div class="btn mt20">\n            <a href="javascript:void(0)"  class="tdn">\n                <div  btn_submit class="ui-button ui-button-primary ui-button-block ui-button-100per  ui-button-size-s-large">\n                    <span class="ui-button-content"><em id="submit_btn_txt">��¼</em></span>\n                </div>\n            </a>\n        </div>\n\n        <div class="des clearfix mt20">\n            <span class="fl"><a href="register.php" class="cor-409">���û�ע��</a></span>\n            <span class="fr"><a href="change_pw.php" class="cor-409">��������</a></span>\n        </div>\n\n        <input type="hidden" value="',(a=t.r_url)?s=a.call(n,{hash:{},data:r}):(a=n&&n.r_url,s=typeof a===o?a.call(n,{hash:{},data:r}):a),p+=u(s)+'" name="r_url"></input>  <!--  ��Դ��ַ���� -->\n        <!-- <input type="hidden" value="{r_url}" name="r_url"></input> -->\n        <input type="hidden" value="form" name="action_mode"></input> \n\n    </div>\n\n</form>\n\n\n'});i.innerHTML=e({r_url:n}),t.init()},init:function(){var i=this;i.phone_number=e("[phone_number]"),i.pass_word=e("[pass_word]"),i.submit=e("[btn_submit]"),i.err_tips=e("[err_tips]"),i.submit_btn_txt=e("#submit_btn_txt"),i._setup_event()},_setup_event:function(){var i=this;i.submit.on("click",function(){var n=i.phone_number.val().trim(),t=i.pass_word.val().trim();if(!n)return i.err_tips.removeClass("fn-hide"),void i.err_tips.find("[err_tips_txt]").html("�������ֻ�����");var r=new RegExp(/^[0-9]{11}$/),s=r.test(n);return s?t?(i.submit_btn_txt.html("��¼��..."),void e("#login_form").submit()):(i.err_tips.removeClass("fn-hide"),void i.err_tips.find("[err_tips_txt]").html("����������")):(i.err_tips.removeClass("fn-hide"),void i.err_tips.find("[err_tips_txt]").html("�ֻ������������������"))})}}});