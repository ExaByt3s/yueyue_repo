define("home/edit",function(n){{var t=n("components/zepto/zepto.js"),e=n("common/widget/header/main"),o=n("common/utility/index"),i=n("common/widget/uploader/index");n("common/I_APP/I_APP"),n("yue_ui/frozen")}t(document).ready(function(){function a(n){var e=Math.ceil(t(n).val().len()/2);e>r?t("#setNums").addClass("red"):t("#setNums").removeClass("red"),t("#setNums").html(e);var o=t("#nickname").val(),i=t("#introduce").val();return""==i||""==o?(t(".tips").show(),t(".icon-delete").hide(),!1):(t(".tips").hide(),t(".icon-delete").show(),!0)}e.init({ele:t("#global-header"),title:"�༭�ҵ�����",header_show:!0,mt_0_ele:t("#seller-list-page"),right_icon_show:!0,share_icon:{show:!1,content:""},omit_icon:{show:!1,content:""},show_txt:{show:!0,content:"����"}}),t('[data-role="right-btn"]').on("click",function(){var n,e=t("#name").val(),i=t("#intro").val(),a=t("#location_id").val();n=t("#expense-calendar").attr("checked")?1:0;var c={arr:"http://image16-d.poco.cn/yueyue/20141127/20141127202703_39_260.jpg?260x260_120"},r=this;r._sending=!1,r._sending||o.ajax_request_app({path:"customer/buyer_user_edit",data:{nickname:e,intro:i,location_id:a,is_display_record:n,showcase:c},beforeSend:function(){r._sending=!0,l.$loading=t.loading({content:"������..."})},success:function(){r._sending=!1,l.$loading.loading("hide")},error:function(n){console.log(n),r._sending=!1,l.$loading.loading("hide"),t.tips({content:"�����쳣",stayTime:3e3,type:"warn"})}})}),t('[data-role="nickname"]').on("click",function(){var n=t(this).find(".tips-value").val();location.href="./input.php?input_title=%E6%98%B5%E7%A7%B0&type=text&input_content="+encodeURIComponent(n)}),t('[data-role="introduce"]').on("click",function(){var n=t(this).find(".tips-value").val();location.href="./input.php?limit_num=140&input_title=%E4%B8%AA%E4%BA%BA%E7%AE%80%E4%BB%8B&type=textarea&input_content="+encodeURIComponent(n)}),t('[data-role="city"]').on("click",function(){var n=t(this).find(".tips-value").val();location.href="./input.php?input_title=%E9%80%89%E6%8B%A9%E5%9F%8E%E5%B8%82&type=city&input_content="+n});var c=function(){var n=this;n.init()},l=t({});c.prototype={init:function(){if(l.$upload_confirm=t('[data-role="upload-confirm"]'),l.$uploader=t('[data-role="upload-img-container"]'),l.$uploader[0]){var n=this;l.uploader_obj=i.render(l.$uploader[0],{}),n._setup_event()}},_setup_event:function(){}};var r=(new c,"{limit_num}");String.prototype.len=function(){return this.replace(/[^\x00-\xff]/g,"xx").length},t(".icon-delete").on("click",function(){t("#nickname").val("").focus()}),t('[data-role="text"]').on("focus",function(){a(this)}),t('[data-role="text"]').on("input",function(){a(this)});{var d=n("common/widget/location/location_main");new d({ele:t("#location"),hot_city:{is_show:!0,data:[{city:"ï��",location_id:101029010}]},callback:function(n){console.log(n)},city_history_num:"3",is_search:!1})}})});