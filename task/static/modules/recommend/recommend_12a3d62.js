define("recommend",function(t){"use strict";var e=t("common/widget/header/main"),a=t("components/jquery/jquery.js"),i=t("common/widget/selectlocal/selectlocal");a(function(){var t=a("#yue-topbar-userinfo-container");e.render(t[0]),a(".item-title ").click(function(){a(this).next().hasClass("list")&&(a(this).next().toggleClass("dn"),a(this).toggleClass("item-title-cur"))}),a("[selectlocal_area]").each(function(){var t=a(this).attr("selectlocal_area");new i("#province-"+t,"#city-"+t)});a("#question_id").val(),a("#service_id").val(),a("#version").val();a("#submit").click(function(){var t=[];a("[question_titleid]").each(function(){var e=a(this).attr("question_titleid"),i=a(this).attr("type");switch(i){case"1":var s=[],n=a(this).find("input[type=radio]:checked").val();s.message=1,s.anwserid=n;break;case"2":var s=[];s.message=1,a(this).find("input[type=checkbox]:checked").each(function(){s.push({anwserid:a(this).val(),message:1})});break;case"4":var s=[],n=a(this).attr("question_titleid");s.message=a(this).find("textarea").val().toString(),s.anwserid=n;break;case"6":var s=[],l=a(this).find(".town").val();s.message=l,s.anwserid=a(this).attr("selectlocal_area");break;case"7":var s=[];a(this).find("[data-style-data]").length>0&&s.push({message:a(this).find("[data-style-data]").val(),anwserid:a(this).find("[data-style-data]").attr("data-style-data")}),a(this).find("[data-style-time]").length>0&&s.push({message:a(this).find("[data-style-time]").find("#data-style-time-1").val()+a(this).find("[data-style-time]").find("#data-style-time-2").val(),anwserid:a(this).find("[data-style-time]").attr("data-style-time")}),a(this).find("[data-style-long]").length>0&&s.push({message:a(this).find("[data-style-long]").val(),anwserid:a(this).find("[data-style-long]").attr("data-style-long")})}t.push({question_titleid:e,data:s})}),console.log(t)})})});