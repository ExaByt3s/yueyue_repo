define(function(require, exports, module) {
var $ = require('jquery'),JSON = require('utility/json/1.0.3/json');
var Cookie = require('matcha/cookie/1.0.0/cookie');
var SWFUpload = require('utility/swfupload/2.2.0/swfupload');


    exports.get_flash = function(_swfPlaceholderId)
    {
           
        //对删除按钮进行取消上传绑定
       
        getSwfupload(_swfPlaceholderId,{
            buttonWidth: 85, buttonHeight: 80
        });


        
    }
    
    
    
    function getSwfupload(_swfPlaceholderId,options) {
        options = options || {};
        //var buttom_img = "http://event.poco.cn/images/add-img-80x240.png";
        var img_mark = img_mark;//标记图片
        var construct_img_mark = construct_img_mark;//记录结构块
        
        var postParams = {
        member_id: Cookie.get('member_id'),
        g_session_id: Cookie.get('g_session_id'),
        pass_hash: Cookie.get('pass_hash')
        };
        

        
        var error_id_arr = new Array();
        
        var _swfupload = new SWFUpload({
            upload_url: 'http://imgup-yue.yueus.com/ultra_upload_service/yueyue_upload_act.php?poco_id=57165138&hash=3e671259ab8647ee115cc9556a9ab0bc',
            flash_url: 'http://yp.yueus.com/topic/user/swfupload.swf',
                file_post_name: 'opus',
                post_params: postParams,
                file_types: '*.jpg;*.png;*.gif;*.jpeg;*.bmp;*.JPG;*.PNG;*.GIF;*.JPEG;*.BMP;*.Jpg;*.Png;*.Gif;*.Jpeg;*.Bmp',
                file_types_description: 'Images',
                file_size_limit: '5MB',
                file_upload_limit: 0,
                file_queue_limit: 5,//最大队列数

                prevent_swf_caching: true,
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

                            this.startUpload();
                                //this.startUpload();
                            
                            
                        } catch (ex)  {
                            this.debug(ex);
                        }

  
                    }
                    
                    
                   
                },

                upload_start_handler: function (file) {

                    
                    
                },

                
                
                upload_progress_handler: function (file, bytesComplete, bytesTotal) {
                    
                        if(bytesComplete>0)
                        { 
                            
                            //$("#img_src").attr("src","./images/publish_loading.gif"); 
                             
                        }

                },

                    
                upload_success_handler: function (file, serverData, responseReceived) {
                    

                        var response  = JSON.parse(serverData);
                    
                    
                        
                    

                        if( response != '' )
                        {
                            //对返回图片进行处理
                            
                            var response_json =response.data;
                            var simpleimg = response_json.mediumThumbnailUrl.replace("_640", "");
                            var small_img = response_json.thumbnailUrl.replace("_165", "_145");
                            var img_item_id = response_json.itemId;
                            
                            var insert_html = '<li><img src="'+small_img+'" data-item-id="'+img_item_id+'" style="width:145px"></li>';
                            $("#location_li").before(insert_html);
                            
                        
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
    
    
    
    
   

});