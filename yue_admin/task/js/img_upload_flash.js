define(function(require, exports, module) {
    var $ = require('jquery'),JSON = require('utility/json/1.0.3/json');
    var Cookie = require('matcha/cookie/1.0.0/cookie');
    var SWFUpload = require('utility/swfupload/2.2.0/swfupload');


    exports.get_flash = function(_swfPlaceholderId,flash_type,id_num,img_mark,construct_img_mark)
    {

        //对删除按钮进行取消上传绑定

        var _swfUploadBig = getSwfupload(_swfPlaceholderId,flash_type,id_num,{
            buttonWidth: 85, buttonHeight: 80
        },img_mark,construct_img_mark);


        //绑定每个flash删除情况
        bind_click_depose(_swfUploadBig,id_num,flash_type);

    }



    function getSwfupload(_swfPlaceholderId,flash_type,id_num,options,img_mark,construct_img_mark) {
        options = options || {};
        //var buttom_img = "http://event.poco.cn/images/add-img-80x240.png";
        var img_mark = img_mark;//标记图片
        var construct_img_mark = construct_img_mark;//记录结构块

        /* var postParams = {
         member_id: Cookie.get('member_id'),
         g_session_id: Cookie.get('g_session_id'),
         pass_hash: Cookie.get('pass_hash')
         };*/
        if(flash_type=="cover_img_btn")
        {
            var max_queue_limit = 1;
        }
        else if(flash_type=="model_btn")
        {
            var max_queue_limit = 20;
        }



        if(navigator.userAgent.toLowerCase().match("msie"))
        {
            var prevent_cache = true;
        }
        else
        {
            var prevent_cache = false;
        }


        var error_id_arr = new Array();

        var _swfupload = new SWFUpload({
            //upload_url: 'http://imgup-s.poco.cn/ultra_upload_service/upload.mypoco_items.php?only_alert=1&utf_8=1&return_json=2&item_type=photo',
            upload_url:'http://sendmedia-w.yueus.com:8079/upload.cgi',//返回260的图，入库是320的图
            flash_url: 'http://my.poco.cn/module_common/swfupload/2.2.0/swfupload.swf',
            file_post_name: 'opus',
            post_params: {  
				params: '{"poco_id":"10002","sh_type":"cms"}'
			},
            file_types: '*.jpg;*.png;*.gif;*.jpeg;*.bmp;*.JPG;*.PNG;*.GIF;*.JPEG;*.BMP;*.Jpg;*.Png;*.Gif;*.Jpeg;*.Bmp',
            file_types_description: 'Images',
            file_size_limit: '5MB',
            file_upload_limit: 0,
            file_queue_limit: max_queue_limit,
            custom_settings:{
                static_mark:img_mark,//编辑情况，起始占位值
                construct_img_mark:construct_img_mark,//结构块
                error_id_arr:error_id_arr//记录错误file_id的数组
            },//记录图片切换的ID

            prevent_swf_caching: prevent_cache,
            debug: (window.location.href.indexOf('swfupload-debug') > -1 ? true : false),

            button_action: SWFUpload.BUTTON_ACTION.SELECT_FILES,
            button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
            button_cursor: SWFUpload.CURSOR.HAND,
            button_placeholder_id:_swfPlaceholderId,
            button_image_url:"http://event.poco.cn/images/add-img-80x240.png",


            button_width: options.buttonWidth,
            button_height: options.buttonHeight,

            swfupload_loaded_handler:function () {
            },

            //对弹框出现
            file_dialog_start_handler:function () {

            },



            //对话框小消失，每个成功入队的触发一次
            file_queued_handler: function (file) {
                //alert("成功入队一个");

            },

            //选图后，对话框消失触发
            file_dialog_complete_handler: function (numFilesSelected, numFilesQueued) {


                //alert("此时队列个数为"+numFilesSelected);
                if (numFilesSelected == 0)
                {
                    return false;
                }
                else if(numFilesQueued>0)
                {

                    try
                    {
                        if(flash_type=="cover_img_btn")
                        {



                            var insert_html = '<li class="J_upload_cover_img_li" style="display:block" data-mark="1">'+
                                '<img class="J_upload_cover_img J_progress" id="J_upload_cover_img" src="http://photo.poco.cn/dianping/images/new_loading.gif" style="width:80px;" data-mark="1"/>'+
                                '<input class="J_upload_cover_img_input_item" id="J_upload_cover_img_input_item" type="hidden" value="" name="cover_image_item_id" data-mark="1"></input>'+
                                '<input class="J_upload_cover_img_input" id="J_upload_cover_img_input" type="hidden" value="" name="cover_image" data-mark="1"></input>'+
                                '<a href="javascript:void(0);" class="cancle" data-class="J_delete_upload_cover_img" data-mark="1">删除</a>'+
                                '</li>';

                            $(".J_add_cover_img[data-mark='"+id_num+"']").before(insert_html);
                            $(".J_add_cover_img[data-mark='"+id_num+"']").css("width","1px");
                            $(".J_add_cover_img[data-mark='"+id_num+"']").css("height","1px");


                        }
                        else if(flash_type=="img_btn")
                        {



                            var construct_img_mark = _swfupload.customSettings.construct_img_mark;//设置标示
                            var error_id_arr = _swfupload.customSettings.error_id_arr;//错误ID的数组

                            //console.log(error_id_arr);
                            //alert(img_mark);

                            var cur_li_len = $(".J_li_ul[data-mark='"+id_num+"']").children(".J_upload_img_li").length;
                            var left_can_queue_num = 20-cur_li_len;
                            if(numFilesSelected<left_can_queue_num)
                            {
                                var construct_count = numFilesSelected;
                            }
                            else
                            {
                                var construct_count = left_can_queue_num;
                            }

                            function construct_insert_html(id_num,construct_img_mark,swf_movie_name)
                            {



                                if(($.inArray(construct_img_mark,error_id_arr))>=0)
                                {
                                    var match_img = "http://event.poco.cn/images/flash_upload_error.png";
                                    var match_input_html = "";
                                }
                                else
                                {
                                    var match_img = "http://photo.poco.cn/dianping/images/new_loading.gif";
                                    var match_input_html = '<input class="J_upload_img_input" type="hidden" name="upload_imgs_'+id_num+'[]" value="" data-mark="'+id_num+'_'+construct_img_mark+'"/>';
                                }



                                var data_index = swf_movie_name+"_"+construct_img_mark;
                                var insert_html = '<li class="J_upload_img_li" data-mark="'+id_num+'_'+construct_img_mark+'" data-index="'+data_index+'">'+
                                    '<img class="J_upload_img J_progress" src="'+match_img+'" style="width:80px;" data-mark="'+id_num+'_'+construct_img_mark+'"/>'+
                                    '<a href="javascript:void(0);" class="cancle" data-class="J_delete_upload_img" data-mark="'+id_num+'_'+construct_img_mark+'" data-mark-par="'+id_num+'" data-index="'+data_index+'">删除</a>'+match_input_html+'</li>';

                                return insert_html;
                            }


                            var total_insert_html = "";
                            for(var i=0;i<construct_count;i++)
                            {
                                total_insert_html += construct_insert_html(id_num,construct_img_mark,this.movieName);
                                construct_img_mark++;
                            }

                            _swfupload.customSettings.construct_img_mark = parseInt(construct_img_mark);
                            _swfupload.customSettings.error_id_arr = new Array();//清数组

                            $(".J_add_btn[data-mark='"+id_num+"']").before(total_insert_html);


                            var li_len = $(".J_li_ul[data-mark='"+id_num+"']").children(".J_upload_img_li").length;
                            if(li_len>=20)
                            {

                                $(".J_add_btn[data-mark='"+id_num+"']").css("width","1px");
                                $(".J_add_btn[data-mark='"+id_num+"']").css("height","1px");

                            }
                            //var left_limit = max_queue_limit-li_len;
                            //_swfupload.setFileQueueLimit(left_limit);//动态更改队列数


                        }

                        this.startUpload();
                        //this.startUpload();


                    } catch (ex)  {
                        this.debug(ex);
                    }


                }



            },

            upload_start_handler: function (file) {
                var file_id = file.id;
                var static_mark = _swfupload.customSettings.static_mark;//静态记录编辑的数量
                var replace_value = (this.movieName)+"_";
                var tmp_file_id = parseInt(file_id.replace(replace_value,""))+static_mark;//应对编辑情况时候的临时结构ID


                //alert("此时文件swf文件临时ID为"+tmp_file_id);
                //alert("此时文件swf文件临时index为"+file.index);


                if(flash_type=="img_btn")
                {
                    var new_data_index = (this.movieName)+"_"+tmp_file_id;
                    if(($(".J_upload_img_li[data-index='"+new_data_index+"']").length)==0)
                    {//如果图片li没有建立则不能上传（取消）

                        //alert("没有HTML结构块，不予上传，此时文件swf文件标注ID为"+tmp_file_id+"队列实际ID为"+file_id);

                        this.cancelUpload(file.id,false);
                        //取消后，跟距情况，标记的结构块加1，图片块加1
                        if(tmp_file_id>=(_swfupload.customSettings.construct_img_mark))
                        {
                            _swfupload.customSettings.construct_img_mark = parseInt(_swfupload.customSettings.construct_img_mark)+1;//2015-4-27
                        }

                    }
                }
            },



            upload_progress_handler: function (file, bytesComplete, bytesTotal) {

                if(bytesComplete>0)
                {

                    //$("#img_src").attr("src","./images/publish_loading.gif");
                    if(flash_type=="cover_img_btn")
                    {
                        $(".cancle[data-class='J_delete_upload_cover_img']").html("删除-上传"+parseInt(bytesComplete/bytesTotal*100) + "%");
                        if((parseInt(bytesComplete/bytesTotal*100) + "%")=="100%")
                        {
                            $(".cancle[data-class='J_delete_upload_cover_img']").html("删除-加载中");
                        }

                    }
                    else if(flash_type=="img_btn")
                    {
                        var file_id = file.id;
                        //2015-5-21
                        var static_mark = _swfupload.customSettings.static_mark;//静态记录编辑的数量
                        var replace_value = (this.movieName)+"_";
                        var tmp_file_id = parseInt(file_id.replace(replace_value,""))+static_mark;//应对编辑情况时候的临时结构ID
                        var data_index_value = (this.movieName)+"_"+tmp_file_id;

                        $(".cancle[data-index='"+data_index_value+"']").html("删除-上传"+parseInt(bytesComplete/bytesTotal*100) + "%");
                        if((parseInt(bytesComplete/bytesTotal*100) + "%")=="100%")
                        {
                            $(".cancle[data-index='"+data_index_value+"']").html("删除-加载中");
                        }
                        //$("#img_src").attr("src","./images/publish_loading.gif");

                    }

                }

            },


            upload_success_handler: function (file, serverData, responseReceived) {


                var response  = JSON.parse(serverData);





                if( response != '' )
                {
                    //对返回图片进行处理

                    var response_json =response;
                    var big_img = response_json.url[0];
					
                    big_img = big_img.replace("_260", "_320");
                    if(flash_type=="cover_img_btn")
                    {
                        if(response_json.message == "Success")
                        {
							
                            $(".J_upload_cover_img[data-mark='1']").attr("src",big_img);
                            $(".J_upload_cover_img_input[data-mark='1']").val(big_img);
                            $(".J_upload_cover_img[data-mark='1']").removeClass("J_progress");

                            //删除按钮处理2015-4-28
                            $(".cancle[data-class='J_delete_upload_cover_img']").html("删除");
                            //删除按钮处理2015-4-28

                            $("#J_upload_cover_img_error").html("");
                            $("#J_upload_cover_img_error").attr("class","alert_error");
                        }
                    }
                    else if(flash_type=="img_btn")
                    {

                        if(response_json.message == "Success")
                        {
                            var file_id = file.id;
                            var static_mark = _swfupload.customSettings.static_mark;//静态记录编辑的数量
                            var replace_value = (this.movieName)+"_";
                            var tmp_file_id = parseInt(file_id.replace(replace_value,""))+static_mark;//应对编辑情况时候的临时结构ID
                            var data_index_value = (this.movieName)+"_"+tmp_file_id;


                            //alert(img_mark);
                            $(".J_upload_img[data-mark='"+id_num+"_"+tmp_file_id+"']").attr("src",big_img);
                            $(".J_upload_img_input[data-mark='"+id_num+"_"+tmp_file_id+"']").val(big_img);
                            $(".J_upload_img[data-mark='"+id_num+"_"+tmp_file_id+"']").removeClass("J_progress");

                            //删除按钮处理2015-4-28
                            $(".cancle[data-index='"+data_index_value+"']").html("删除");
                            //删除按钮处理2015-4-28


                            var li_len = $(".J_li_ul[data-mark='"+id_num+"']").children(".J_upload_img_li").length;
                            if(li_len>=20)
                            {

                                $(".J_add_btn[data-mark='"+id_num+"']").css("width","1px");
                                $(".J_add_btn[data-mark='"+id_num+"']").css("height","1px");

                            }
                        }




                    }

                }


            },





            //当文件上传的处理已经完成

            upload_error_handler: function (file, errorCode, message) {
                /* if(this.getStats().files_queued>0)
                 {

                 this.startUpload();
                 } */

            },



            upload_complete_handler: function (file) {

                if(parseInt(this.getStats().files_queued)>0)
                {

                    this.startUpload();
                }
            },




            //队列错误
            file_queue_error_handler : function(file, errorCode, message){
                try
                {
                    if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED)
                    {


                        alert("您正在上传的文件队列过多.\n" + (message === 0 ? "您已达到上传限制" : "您最多能选择 " + (message > 1 ? "上传 " + message + " 文件." : "一个文件.")));

                        return false;
                    }


                    if(flash_type=="img_btn")
                    {
                        var file_id = file.id;
                        var static_mark = _swfupload.customSettings.static_mark;//静态记录编辑的数量
                        var replace_value = (this.movieName)+"_";
                        var tmp_file_id = parseInt(file_id.replace(replace_value,""))+static_mark;//应对编辑情况时候的临时结构ID
                        var error_id_arr = _swfupload.customSettings.error_id_arr;
                        error_id_arr.push(tmp_file_id);


                    }



                    switch (errorCode) {
                        case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
                            //文件尺寸过大
                            alert('文件大小不可以超过5M');
                            //
                            break;
                        case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
                            //无法上传零字节文件
                            alert('上传文件为零字节');
                            //
                            break;
                        case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
                            //不支持的文件类型
                            alert('所选内容有上传格式错误，请重新选择');
                            //
                            break;
                        default:
                            break;
                    }
                }
                catch (ex)
                {

                    this.debug(ex);

                }

            }
        });
        return _swfupload;
    }

    function bind_click_depose(cur_swf,id_num,flash_type)
    {
        var swf_movie_name = cur_swf.movieName;
        if(flash_type=="cover_img_btn")
        {

            var new_add_class = $(".J_cover_img_ul[data-mark='"+id_num+"']").attr("class")+" "+swf_movie_name;
            $(".J_cover_img_ul[data-mark='"+id_num+"']").attr("class",new_add_class);
            $(".J_cover_img_ul[data-mark='"+id_num+"']").click(function(e){

                //alert(swf_movie_name);

                var target = e.target;
                var data_class = $(target).attr("data-class");
                var data_mark = $(target).attr("data-mark");
                if(data_class=="J_delete_upload_cover_img")
                {

                    $(".J_upload_cover_img_li[data-mark='"+data_mark+"']").remove();
                    $(".J_add_cover_img[data-mark='"+data_mark+"']").css("width","85px");
                    $(".J_add_cover_img[data-mark='"+data_mark+"']").css("height","100px");
                    cur_swf.cancelUpload("",false);
                }


            });
        }
        else if(flash_type=="img_btn")
        {

            var new_add_class = $(".J_li_ul[data-mark='"+id_num+"']").attr("class")+" "+swf_movie_name;
            $(".J_li_ul[data-mark='"+id_num+"']").attr("class",new_add_class);
            //alert(cur_swf.movieName);
            $(".J_li_ul[data-mark='"+id_num+"']").click(function(e){

                //alert(swf_movie_name);

                var target = e.target;
                var data_class = $(target).attr("data-class");//该模块标记
                var cur_data_index = $(target).attr("data-index");
                var data_mark = $(target).attr("data-mark");
                var data_mark_par = $(target).attr("data-mark-par");
                if(data_class=="J_delete_upload_img")
                {
                    //var mathc_progress = $(".J_upload_img[data-mark='"+data_mark+"']").attr("class").match("J_progress");

                    $(".J_upload_img_li[data-mark='"+data_mark+"']").remove();
                    $(".J_add_btn[data-mark='"+data_mark_par+"']").css("width","85px");
                    $(".J_add_btn[data-mark='"+data_mark_par+"']").css("height","100px");


                    //console.log(cur_data_index);
                    //console.log(cur_swf);

                    var static_mark = cur_swf.customSettings.static_mark;//静态记录编辑的数量
                    var replace_value = (cur_swf.movieName)+"_";
                    var tmp_file_id = parseInt(cur_data_index.replace(replace_value,""))-static_mark;//应对编辑情况时候的临时结构ID

                    if(tmp_file_id>=0)
                    {
                        var cur_match_file = cur_swf.getFile(tmp_file_id);
                        if(cur_match_file.filestatus==-2)//如果正在上传中，则取消
                        {
                            //正在上传就取消
                            //alert("我点了取消，这里是取消了上传，复位了上传ID")
                            cur_swf.cancelUpload(cur_match_file.id,false);
                        }
                    }




                }

            });
        }

    }




});