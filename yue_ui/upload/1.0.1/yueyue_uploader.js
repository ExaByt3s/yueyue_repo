/**
 * Created by hudingwen on 15/6/10.
 */
(function(window,undefined)
{
    var yueyue_uploader = function(element,options)
    {
        var self = this;

        options = options || {};

        // 参数设置
        var params = options.params || {};

        var file_count = 0;
        var file_size = 0;

		var form_data = 
		{
			poco_id : 100000+'',	
			sh_type : 'merchandise'				
		};

		form_data = $.extend(true,form_data,options.formData);
		form_data = JSON.stringify(form_data)	

        var default_params =
        {

            paste: '#uploader',
            swf: 'Uploader.swf',
            chunked: false,
            chunkSize: 512 * 1024,
            server: 'http://sendmedia.yueus.com:8079/upload.cgi',
            // auto 设置true，选择图片即为自动上传
            auto: false,
            cover_img : '',
            //runtimeOrder: 'html5',
            formData :
            {
                params : form_data
            },
            accept:
            {
                 title: 'Images',
                 extensions: 'gif,jpg,jpeg,bmp,png',
                 mimeTypes: 'image/*'
            },
            thumb :
            {
                crop : true
            },
            // 上传表单的name值
            fileVal : 'opus',
            // 禁掉全局的拖拽功能。这样不会出现图片拖进页面的时候，把图片打开。
            disableGlobalDnd: true,
            fileNumLimit: 20,
            fileSizeLimit: 200 * 1024 * 1024,    // 200 M
            fileSingleSizeLimit:200 * 1024 * 1024    // 50 M
        };



        params = $.extend(true,default_params,options);

		
		
        // 实例化
        var uploader = WebUploader.create(params);


        // 初始化文件数
        uploader.file_count = file_count;
        uploader.file_size = file_size;

        // 传图的状态 可能有pedding, ready, uploading, confirm, done.
        uploader.state = 'pedding';

        uploader.map_obj = {};

        uploader.on('fileQueued',function(file)
        {
            uploader.file_count++;
            uploader.file_size += file.size;

            uploader.map_obj[file.id] = file;

        });

        uploader.on('fileDequeued',function(file)
        {
            uploader.file_count--;
            uploader.file_size -= file.size;

            if(uploader.map_obj[file.id])
            {
                delete uploader.map_obj[file.id];
            }
        });

        uploader.on('error',function(err)
        {
            switch(err)
            {
                case 'Q_EXCEED_NUM_LIMIT':
                    alert('上传文件数量超出上限了');
                    break;
                case 'Q_EXCEED_SIZE_LIMIT':
                    alert('上传文件大小超出上限了');
                    break;
                case 'Q_TYPE_DENIED':
                    alert('上传文件类型格式错误')
                    break;
                case 'F_EXCEED_SIZE':
                    alert('上传文件大小超出上限了');
                    break;
            }
        });


        uploader.option('start_upload').$el.on('click',function(ev)
        {
            uploader.trigger('startUpload',ev);
        });

        uploader.get_file_by_id = function(file_id)
        {
            return uploader.map_obj[file_id] || '';
        };



        return uploader;
    };

    window.yueyue_uploader = yueyue_uploader;


})(window);

(function(window,$)
{

    

})(window,$);