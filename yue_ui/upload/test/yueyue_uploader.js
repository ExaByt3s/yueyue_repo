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

        var default_params =
        {
            pick:
            {
                id: '#filePicker',
                label: '点击选择图片'
            },
            dnd: '#dndArea',
            paste: '#uploader',
            swf: 'Uploader.swf',
            chunked: false,
            chunkSize: 512 * 1024,
            server: 'http://sendmedia.yueus.com:8079/upload.cgi',
            // auto 设置true，选择图片即为自动上传
            auto: true,
            cover_img : '',
            //runtimeOrder: 'flash',
            formData :
            {
                poco_id : 100000
            },
            accept:
            {
                 title: 'Images',
                 extensions: 'gif,jpg,jpeg,bmp,png',
                 mimeTypes: 'image/*'
            },
            // 上传表单的name值
            fileVal : 'opus',
            // 禁掉全局的拖拽功能。这样不会出现图片拖进页面的时候，把图片打开。
            disableGlobalDnd: true,
            fileNumLimit: 20,
            fileSizeLimit: 200 * 1024 * 1024,    // 200 M
            fileSingleSizeLimit: 50 * 1024 * 1024    // 50 M
        };



        params = $.extend(true,default_params,options);

        // 实例化
        var uploader = WebUploader.create(params);

        // 初始化文件数
        uploader.file_count = file_count;

        // 添加“添加文件”的按钮，
        uploader.addButton
        ({
            id: '#filePicker2',
            label: '继续添加'
        });

        uploader.on('fileQueued',function(file)
        {
            uploader.file_count++;

        });

        uploader.on('fileDequeued',function(file)
        {
            uploader.file_count--;
        });

        uploader.option('start_upload').$el.on('click',function(ev)
        {
            uploader.trigger('startUpload',ev);
        });



        return uploader;
    };


    window.yueyue_uploader = yueyue_uploader;


})(window);