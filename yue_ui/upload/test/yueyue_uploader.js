/**
 * Created by hudingwen on 15/6/10.
 */
(function(window,undefined)
{
    var yueyue_uploader = function(element,options)
    {
        var self = this;

        options = options || {};

        // ��������
        var params = options.params || {};

        var file_count = 0;

        var default_params =
        {
            pick:
            {
                id: '#filePicker',
                label: '���ѡ��ͼƬ'
            },
            dnd: '#dndArea',
            paste: '#uploader',
            swf: 'Uploader.swf',
            chunked: false,
            chunkSize: 512 * 1024,
            server: 'http://sendmedia.yueus.com:8079/upload.cgi',
            // auto ����true��ѡ��ͼƬ��Ϊ�Զ��ϴ�
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
            // �ϴ�����nameֵ
            fileVal : 'opus',
            // ����ȫ�ֵ���ק���ܡ������������ͼƬ�Ͻ�ҳ���ʱ�򣬰�ͼƬ�򿪡�
            disableGlobalDnd: true,
            fileNumLimit: 20,
            fileSizeLimit: 200 * 1024 * 1024,    // 200 M
            fileSingleSizeLimit: 50 * 1024 * 1024    // 50 M
        };



        params = $.extend(true,default_params,options);

        // ʵ����
        var uploader = WebUploader.create(params);

        // ��ʼ���ļ���
        uploader.file_count = file_count;

        // ��ӡ�����ļ����İ�ť��
        uploader.addButton
        ({
            id: '#filePicker2',
            label: '�������'
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