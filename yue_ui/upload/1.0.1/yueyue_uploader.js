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
            // auto ����true��ѡ��ͼƬ��Ϊ�Զ��ϴ�
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
            // �ϴ�����nameֵ
            fileVal : 'opus',
            // ����ȫ�ֵ���ק���ܡ������������ͼƬ�Ͻ�ҳ���ʱ�򣬰�ͼƬ�򿪡�
            disableGlobalDnd: true,
            fileNumLimit: 20,
            fileSizeLimit: 200 * 1024 * 1024,    // 200 M
            fileSingleSizeLimit:200 * 1024 * 1024    // 50 M
        };



        params = $.extend(true,default_params,options);

		
		
        // ʵ����
        var uploader = WebUploader.create(params);


        // ��ʼ���ļ���
        uploader.file_count = file_count;
        uploader.file_size = file_size;

        // ��ͼ��״̬ ������pedding, ready, uploading, confirm, done.
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
                    alert('�ϴ��ļ���������������');
                    break;
                case 'Q_EXCEED_SIZE_LIMIT':
                    alert('�ϴ��ļ���С����������');
                    break;
                case 'Q_TYPE_DENIED':
                    alert('�ϴ��ļ����͸�ʽ����')
                    break;
                case 'F_EXCEED_SIZE':
                    alert('�ϴ��ļ���С����������');
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