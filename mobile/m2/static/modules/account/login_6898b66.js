define("account/login",function(t){"use strict";var n=t("common/utility/index"),e=t("components/zepto/zepto.js"),o=t("components/fastclick/fastclick.js"),a=(t("yue_ui/frozen"),t("common/ua/index"));"addEventListener"in document&&document.addEventListener("DOMContentLoaded",function(){o.attach(document.body)},!1),function(t,e){var o={};o.$page_container=t('[data-role="page-container"]'),o.$confirm_btn=t('[data-role="confrim"]'),o.$account_id=t('[data-role="account-number"]'),o.$account_pwd=t('[data-role="account-pwd"]'),o.$reg_go_btn=t('[data-role="reg"]'),o.$reg_go_btn.on("click",function(){var t=n.get_url_params(e.location.href,"redirect_url");e.location.href=t?"../account/reg.php?redirect_url="+t:"../account/reg.php"}),o.$confirm_btn.on("click",function(c){t(c.currentTarget);if(n.is_empty(o.$account_id.val()))return void t.tips({content:"�˺Ų���Ϊ��",stayTime:3e3,type:"warn"});if(n.is_empty(o.$account_pwd.val()))return void t.tips({content:"���벻��Ϊ��",stayTime:3e3,type:"warn"});var r={},i="login.php";a.is_weixin&&(i="http://yp.yueus.com/m/action/login.php"),n.ajax_request({url:i,type:"POST",data:{account:o.$account_id.val(),password:o.$account_pwd.val()},beforeSend:function(){r=t.loading({content:"������..."})},success:function(o){r.loading("hide");var o=o.result_data,a=o.msg;if("200"==o.result){var c=n.get_url_params(e.location.href,"redirect_url");c?(c=decodeURIComponent(c),e.location.href=c):e.location.href="../recharge/card.php"}else t.tips({content:a,stayTime:3e3,type:"warn"})},error:function(){r.loading("hide"),t.tips({content:"�����쳣",stayTime:3e3,type:"warn"})}})})}(e,window)});