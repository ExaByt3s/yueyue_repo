define("home/edit",function(t){var e=t("components/zepto/zepto.js"),a=t("common/widget/header/main"),i=t("common/utility/index"),n=t("common/widget/uploader/index"),o=t("common/I_APP/I_APP"),l=(t("yue_ui/frozen"),t("home/input"));e(document).ready(function(){var d=e({});d.$edit_page=e('[data-role="edit"]'),d.$input_text_page=e('[data-role="input-text"]'),d.$input_textarea_page=e('[data-role="input-textarea"]'),d.$input_location_page=e('[data-role="input-location"]'),d.$name=e("#name"),d.$intro=e("#intro");var _=function(){var t=this;t.init()};_.prototype={init:function(){var t=this;t.setup_edit_page(),t.setup_location(),t.setup_input(d.$input_text_page,{id:"name",type:"text",limit:16,input_title:"昵称",page_title:"编辑昵称",input_content:d.$name.val()}),t.setup_input(d.$input_textarea_page,{id:"intro",type:"textarea",limit:140,input_title:"个人简介",page_title:"编辑个人简介",input_content:d.$intro.val()}),t.setup_header_click_event()},setup_edit_page:function(){var t=this;t.edit_page_header_obj=a.init({ele:d.$edit_page.find('[data-role="global-header"]'),title:"编辑我的资料",header_show:!0,right_icon_show:!0,share_icon:{show:!1,content:""},omit_icon:{show:!1,content:""},show_txt:{show:!0,content:"保存"}}),t.edit_page_header_obj.$el.on("click:right_btn",function(){var t,a=e.trim(e("#name").val()),n=e("#intro").val(),o=e("#location_id").val(),l=e("#city_id").val();a&&o||alert("昵称或地址不能为空"),t=e("#expense-calendar").attr("checked")?1:0;var _=["http://image16-d.poco.cn/yueyue/20141127/20141127202703_39_260.jpg?260x260_120"],r=this;r._sending=!1,r._sending||i.ajax_request_app({path:"customer/buyer_user_edit",data:{post_json_data:{nickname:a,intro:n,location_id:l,is_display_record:t,showcase:_}},beforeSend:function(){r._sending=!0,d.$loading=e.loading({content:"加载中..."})},success:function(t){r._sending=!1,d.$loading.loading("hide"),console.log(t)},error:function(){r._sending=!1,d.$loading.loading("hide"),e.tips({content:"网络异常",stayTime:3e3,type:"warn"})}})}),e('[data-role="nickname"]').on("click",function(){d.$input_text_page.removeClass("fn-hide"),d.$input_textarea_page.addClass("fn-hide"),d.$input_location_page.addClass("fn-hide"),d.$edit_page.addClass("fn-hide")}),e('[data-role="introduce"]').on("click",function(){d.$input_text_page.addClass("fn-hide"),d.$input_textarea_page.removeClass("fn-hide"),d.$input_location_page.addClass("fn-hide"),d.$edit_page.addClass("fn-hide")}),e('[data-role="city"]').on("click",function(){d.$input_text_page.addClass("fn-hide"),d.$input_textarea_page.addClass("fn-hide"),d.$input_location_page.removeClass("fn-hide"),d.$edit_page.addClass("fn-hide")}),t.setup_uploader(),t.setup_head_icon_upload()},setup_head_icon_upload:function(){var t=this;o.isPaiApp||(t.upload_head_obj=t.uploader_obj.init_webupload({picker_id:"#head-icon",auto:!0,server:"http://sendmedia.yueus.com:8078/icon.cgi",file_num_limit:1,fileSizeLimit:9437184,fileSingleSizeLimit:3145728,pick:{multiple:!1},formData:{hash:__icon_hash}}),t.upload_head_obj.on("startUpload",function(){t.$loading=e.loading({content:"头像上传中... 请勿关闭"})}).on("error",function(e){t.upload_head_obj.err=e}).on("uploadSuccess",function(a,i){0==i.code&&(e(".avatar").attr("src",i.url+"?"+(new Date).getTime()),e('[data-role="avatar"]').val(i.url)),t.upload_head_obj.err=""}).on("uploadError",function(a,i){t.$loading.loading("hide"),e.tips({content:i,stayTime:3e3,type:"warn"})}).on("uploadFinished",function(){return t.$loading.loading("hide"),t.upload_head_obj.err?(t.upload_head_obj.reset(),void e.tips({content:"上传失败",stayTime:3e3,type:"warn"})):void 0}))},set_upload_grid_margin:function(){var t=this;t.$plus_btn_tag=e('[data-role="plus"]'),e('[data-role="upload-items"]').each(function(a,i){t.$plus_btn_tag.hasClass("invisiable")?(a+2)%3==0?e(i).addClass("middle-margin"):e(i).removeClass("middle-margin"):a%3==0?e(i).addClass("middle-margin"):e(i).removeClass("middle-margin")})},setup_uploader:function(){var t=this;t.$uploader=e('[data-role="upload-img-container"]'),t.$cur_img_count=e('[data-role="cur-img-count"]'),t.$total_num_label=e('[data-role="total-num"]'),t.upload_art_list=[];var a=7,l=showcase_data.result_data||[];t.$total_num_label.html(a),n.init();for(var d=i.int((window.innerWidth-66)/3),_=0;_<l.length;_++)l[_].img_url=l[_].thumb;t.plus_btn_html=n.plus_btn({grid_size:d+"px"}),t.$uploader.append(t.plus_btn_html),t.has_init_puls_btn=!0,l.length<a&&0==l.length?t.$uploader.find('[data-role="plus"]').removeClass("invisiable"):t.$uploader.find('[data-role="plus"]').addClass("invisiable"),t.uploader_obj=n.add(t.$uploader,{list:l,total_limit:a,grid_size:d+"px"}),t.set_upload_grid_margin(),t.$cur_img_count.html(t.$uploader.find('[data-role="upload-items"]').length),t.uploader_obj.$el.on("delete:upload_items",function(){t.$cur_img_count.html(t.$uploader.find('[data-role="upload-items"]').length),t.has_init_puls_btn||(t.uploader_obj.total_count<a?(t.$uploader.find('[data-role="plus"]').removeClass("invisiable"),t.has_init_puls_btn=!0):(t.$uploader.find('[data-role="plus"]').addClass("invisiable"),t.has_init_puls_btn=!1));var e=t.upload_art_obj.option("fileNumLimit");t.upload_art_obj.option("fileNumLimit",e+1),t.set_upload_grid_margin()}),o.isPaiApp||(t.upload_art_obj=t.uploader_obj.init_webupload({picker_id:"#upload-flag",auto:!0,file_num_limit:a-t.$uploader.find('[data-role="upload-items"]').length,fileSizeLimit:9437184,fileSingleSizeLimit:3145728}),t.upload_art_obj.on("startUpload",function(){t.$loading=e.loading({content:"图片上传中... 请勿关闭"})}).on("fileQueued",function(){t.upload_art_list=[]}).on("fileDequeued",function(t){console.log(t)}).on("error",function(e){t.upload_art_obj.err=e}).on("uploadSuccess",function(e,a){0==a.code&&t.upload_art_list.push(a.url[0]);var i=t.upload_art_obj.option("fileNumLimit");t.upload_art_obj.option("fileNumLimit",i-1),t.upload_art_obj.err=""}).on("uploadError",function(e,a){t.$loading.loading("hide"),t.upload_art_list=[],console.log(a)}).on("uploadFinished",function(){if(t.$loading.loading("hide"),t.upload_art_obj.err)return t.upload_art_obj.reset(),void e.tips({content:"上传失败",stayTime:3e3,type:"warn"});for(var i=t.upload_art_list,o=[],l=0;l<i.length;l++)o[l]={img_url:i[l]};n.add(t.$uploader,{list:o,total_limit:a,grid_size:d+"px"}),t.$uploader.find('[data-role="upload-items"]').length>=a&&t.has_init_puls_btn&&(t.$uploader.find('[data-role="plus"]').addClass("invisiable"),t.has_init_puls_btn=!1),t.set_upload_grid_margin(),t.$cur_img_count.html(t.$uploader.find('[data-role="upload-items"]').length)}))},setup_location:function(){{var i=this,n=t("common/widget/location/location_main");new n({ele:d.$input_location_page.find("#location"),hot_city:{is_show:!0,data:[{city:"茂名",location_id:101029010}]},callback:function(t){e("#city_id").val(t.location_id),e("#location_id").val(t.city),i.show_edit_page(),console.log(t)},city_history_num:"3",is_search:!1})}i.location_page_header_obj=a.init({ele:d.$input_location_page.find('[data-role="global-header"]'),title:"选择地区",use_page_back:!1,header_show:!0,right_icon_show:!0,share_icon:{show:!1,content:""},omit_icon:{show:!1,content:""},show_txt:{show:!1,content:""}})},setup_input:function(t,e){var a=l.render(t,e);return d["header_"+e.id]=l.setup_header({title:e.page_title}),a},setup_header_click_event:function(){var t=this;d.header_name.$el.on("click:left_btn",function(){t.show_edit_page()}).on("click:right_btn",function(){t.show_edit_page();var a=e("#nickname").val();e("#name").val(a)}),d.header_intro.$el.on("click:left_btn",function(){t.show_edit_page()}).on("click:right_btn",function(){t.show_edit_page();var a=e("#introduce").val();e("#intro").val(a)})},show_edit_page:function(){d.$input_text_page.addClass("fn-hide"),d.$input_textarea_page.addClass("fn-hide"),d.$input_location_page.addClass("fn-hide"),d.$edit_page.removeClass("fn-hide")}};new _})});