define("lead_list",function(e){"use strict";var a=e("common/widget/header/main"),i=e("components/jquery/jquery.js");i(function(){var e=i("#yue-topbar-userinfo-container");a.render(e[0]),i("[go_url_lead_id]").click(function(e){var a=i(e.target).attr("action");if(!a){var t=i(this).attr("go_url_lead_id"),n="./lead_detail.php?lead_id="+t;window.location.href=n}}),i("[lead_id]").click(function(){var e=i(this).attr("lead_id"),a={lead_id:e};confirm("ȷ��ɾ��ô��")&&i.ajax({url:window.$__config.ajax_url+"del_lead_list.php",data:a,type:"POST",cache:!1,beforeSend:function(){},success:function(a){1==parseInt(a)&&(i(["data-list-"+e]).remove(),window.location.reload())},error:function(){},complete:function(){}})})})});