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

    window.yue_upload_class = function(options)
    {
        this.init(options);
    };

    yue_upload_class.prototype =
    {
        init : function(options)
        {
            var self = this;

            self.options = options || {};

            self.init_img_group();
        },
        tpl : '<div class="ui-item">' +
                '<div class="upload-page-content" data-role="first-page" >'+
                '<div id="first_file_picker" class="choose-img-btn" data-role="choose-img-btn">ѡ��ͼƬ</div>'+
                '<div class="upload-img-btn" >'+
                '</div>'+
                '<p>��סCtrl�ɶ�ѡͼƬ</p>'+
                '</div>'+
                '<div class="webuploader-element-invisible" data-role="sec-page" id="go-on-add-page">' +
                '<div class="upload-page-content" style="padding: 10px 0 0 10px;" >'+

                '</div>'+
                '<div class="upload-start-continue">'+
                '<a class="upload-start upload-btn" id="start-upload-btn" href="javascript:void(0);">��ʼ�ϴ�</a>'+
                '<div style="display: inline-block;" id="page-show" data-role="go-on-add-btn">�������</div>'+
                '<div class="upload-img-btn2">'+
                '</div>'+
                '<span class="upload-tips" data-role="upload-tips"></span>'+
                '</div>'+
                '</div>'+
                '</div>',

        init_img_group : function()
        {
            var self = this;
            var options  = self.options;

            /***** ��ʼ�����ͼƬ��� *****/
            var $upload_container = options.upload_container || $('.upload-container');

            var imgs = options.imgs || [];

            self.add_item_img_obj = {};
            self.upload_total_limit = options.upload_total_limit || 9;


            // ִ�г�ʼ��ͼƬ���
            self.add_item_img_obj = self.add_item_img($upload_container,
            {
                imgs : imgs
            });
        },
        add_item_img : function($el,params)
        {
            var self = this;

            var imgs = params.imgs || [];
            var limit = params.limit || 9;
            var imgs_str = [];

            var add_img_tpl = '<div class="ui-btn-add ui-btn disable-drag" data-role="add-btn">'+
                    '<a href="javascript:void(0);" class="ui-bf" title="���ͼƬ" id="popup"></a>'+
                    '</div>';

            if(imgs.length>0)
            {
                for(var i=0;i<imgs.length;i++)
                {
                    var url = imgs[i];
                    var tpl = '<div class="ui-upload ui-btn bg-color" data-role="ui-success-upload-img"  >'+
                            '<a  class="">' +
                            '   <img style="width: 130px;height: 130px;" src="'+url+'" title=""/>' +
                            '</a>'+
                            '<div class="ui-upload-shadow"><a class="close-img"></a></div>'+
                            '</div>';

                    imgs_str[i] = tpl;
                }


            }
            if($el.length && !$el.find('[data-role="add-btn"]').length)
            {
                imgs_str.push(add_img_tpl);
            }



            var $temp = $(imgs_str.join(''));

            if($el.length)
            {
                if(!$el.children().length)
                {
                    $el.append($temp);
                }
                else
                {
                    $el.find('[data-role="add-btn"]').before($temp)
                }
            }



            // ��꾭����ʾԤ��ͼ��ť
            $el.length && $el.find('[data-role="ui-success-upload-img"]').hover(function()
            {
                $(this).addClass('show');
            },function()
            {
                $(this).removeClass('show');
            });

            // �󶨵���¼�
            $temp.find('.close-img').on('click',function(ev)
            {

                // ɾ���¼�
                var $cur_btn = $(ev.currentTarget);

                var $img = $cur_btn.parents('.ui-btn');

                $img.remove();

                refresh();
            });

            if($el.length)
            {
                refresh();
            }



            if(self.options.sortable)
            {
                self.options.upload_container.sortable
                ({
                    items: "div:not(.disable-drag)",
                    activate : function(event, ui)
                    {
                        console.log(ui);
                    }
                })
                    .bind(function(ev)
                    {
                        self.options.sortable_callback && self.options.sortable_callback.call(this,ev);
                    });

                self.options.upload_container.disableSelection();
            }





            function refresh()
            {
                if($el.length)
                {
                    var $add_btn = $el.find('[data-role="add-btn"]');

                    if(!$el.find('[data-role="add-btn"]').length)
                    {
                        // û�г������������
                        if($el.find('[data-role="ui-success-upload-img"] img').length < limit)
                        {
                            //$el.append(add_img_tpl);
                            $add_btn.removeClass('webuploader-element-invisible ');
                        }
                        else
                        {
                            $add_btn.addClass('webuploader-element-invisible');
                        }

                    }
                    else
                    {
                        if($el.find('[data-role="ui-success-upload-img"] img').length >= limit)
                        {
                            $add_btn.addClass('webuploader-element-invisible');
                        }
                        else
                        {
                            $add_btn.removeClass('webuploader-element-invisible ');
                        }
                    }
                }

            }


            return {
                get_cur_img_list : function()
                {
                    var _imgs = $el.find('[data-role="ui-success-upload-img"] img');
                    var _temp_arr = [];

                    for(var i=0;i<_imgs.length;i++)
                    {
                        _temp_arr[i] = _imgs.eq(i).attr('src');
                    }

                    return _temp_arr;
                },
                refresh : refresh()
            }
        },
        init_upload : function($el)
        {
            var self = this;
            var options  = self.options;

            /***** ��ʼ���ϴ���� *****/
            var uploader_obj = {};
            var first_layer = null;

            // �ϴ�ͼƬ����ģ��
            var init_upload_str = self.tpl;

            // �ϴ�ͼƬ����
            var item_img = function(params)
            {
                var tpl = '<div class="ui-upload ui-btn bg-color" data-file-id="'+params.id+'" id="upload-img-"'+params.id+'>'+
                        '<a href="javascript:void(0);" class="">' +
                        '   <img src="'+params.url+'" title=""/>' +
                        '<div class="ui-upload-progress" style="display: none;" data-role="progress">�ϴ���...</div>'+
                        '</a>'+
                        '<div class="ui-upload-shadow"><a  class="close-img"></a></div>'+
                        '</div>';

                var $temp = $(tpl);

                // ��꾭����ʾԤ��ͼ��ť
                $temp.hover(function()
                {
                    $(this).addClass('show');
                },function()
                {
                    $(this).removeClass('show');
                });

                // �󶨵���¼�
                $temp.find('.close-img').on('click',function(ev)
                {

                    // ɾ���¼�
                    var $cur_btn = $(ev.currentTarget);

                    var $img = $cur_btn.parents('[data-file-id]');

                    var file_id = $img.attr('data-file-id');

                    var file = uploader_obj.get_file_by_id(file_id);

                    uploader_obj.removeFile(file);

                    $img.remove();
                });


                return $temp;
            };

            var $temp_container = $(init_upload_str);

            var $first_page = $el.find('[data-role="first-page"]');
            var $sec_page = $el.find('[data-role="sec-page"]');
            var $preview_img_list = $sec_page.find('.upload-page-content');
            var $upload_tips = $sec_page.find('[data-role="upload-tips"]');

            var file_num_limit = self.upload_total_limit - self.add_item_img_obj.get_cur_img_list().length;

            if(!self.options.multiple)
            {
                file_num_limit = 1;
            }

            uploader_obj = yueyue_uploader($temp_container.find('#upload-wrapper'),
                    {
                        auto : false,
                        fileNumLimit : file_num_limit,
                        pick :
                        {
                            id : '#first_file_picker',
                            label : 'ѡ��ͼƬ'
                        },
                        start_upload :
                        {
                            $el : $sec_page.find('#start-upload-btn')
                        },
                        formData :
                        {
                            pocoid : self.options.user_id
                        }
                    });		

            uploader_obj.imgs = [];

            // ����ͼ��size
            var thumbnailWidth = 130;
            var thumbnailHeight = 130;

            // ��ʼ�ϴ���ť
            uploader_obj.on('startUpload',function()
            {
                if(uploader_obj.state == 'uploading')
                {
                    return;
                }

                uploader_obj.upload();
            });

            // ��ӡ�����ļ����İ�ť��
            uploader_obj.addButton
            ({
                id: '[data-role="go-on-add-btn"]',
                label: '�������'
            });

            // �����ļ���ӽ�����ʱ��
            uploader_obj.on('fileQueued', function(file)
            {
                console.log(file);

                if(uploader_obj.file_count >= 1)
                {
                    $first_page.addClass('webuploader-element-invisible ');
                    $sec_page.removeClass('webuploader-element-invisible ');

                    // ��ѡ����ļ�������1ʱ�����Խ��м���ѡ��
                    uploader_obj.refresh();

                }
                else
                {
                    // todo �쳣����
                    return;
                }


                // ����״̬
                uploader_obj.state = 'ready';

                update_status(uploader_obj.state,$upload_tips);

                // ��������ͼ
                // ���Ϊ��ͼƬ�ļ������Բ��õ��ô˷�����
                // thumbnailWidth x thumbnailHeight Ϊ 100 x 100
                uploader_obj.makeThumb(file, function( error, src )
                {
                    if(error)
                    {
                        // ����Ԥ��ʱ�Ĵ���
                        return;
                    }

                    // Ԥ��ͼƬ��·��
                    console.log(src);

                    var preview_img = item_img
                    ({
                        id : file.id,
                        url : src
                    });

                    $preview_img_list.append(preview_img);

                }, thumbnailWidth, thumbnailHeight );
            });

            // ���ļ����Ƴ����к󴥷���
            uploader_obj.on( 'fileDequeued', function(file)
            {
                if(!uploader_obj.file_count)
                {
                    // ����״̬
                    uploader_obj.state = 'pedding';

                    $first_page.removeClass('webuploader-element-invisible ');
                    $sec_page.addClass('webuploader-element-invisible ');

                    // ��ѡ����ļ�������1ʱ�����Խ��м���ѡ��
                    uploader_obj.refresh();
                }



                update_status(uploader_obj.state,$upload_tips);

                console.log('remove file');
            });

            // �ļ���ʼ�ϴ�ǰ������һ���ļ�ֻ�ᴥ��һ�Ρ�
            uploader_obj.on('uploadStart', function(file)
            {
                var file_id = file.id;

                var $img = $('[data-file-id="'+file_id+'"]');

                $img.find('[data-role="progress"]').show();
            });

            // �ļ��ϴ������д���������ʵʱ��ʾ��
            uploader_obj.on('uploadProgress', function(file, percentage)
            {
                console.log(percentage);
            });

            // �ļ��ϴ��ɹ���
            uploader_obj.on( 'uploadSuccess', function(file,response)
            {
                // response Ϊ����˷��ص�����
                console.log(response);

                var url = response.url[0] || '';

                var file_id = file.id;

                var $img = $('[data-file-id="'+file_id+'"]');

                $img.find('[data-role="progress"]').addClass('webuploader-element-invisible');

                uploader_obj.imgs.push(url);


            });

            // �ļ��ϴ�ʧ�ܣ���ʾ�ϴ�����
            uploader_obj.on( 'uploadError', function(file)
            {
                uploader_obj.imgs = [];
            });

            // �ļ��ϴ�ʧ�ܣ���ʾ�ϴ�����
            uploader_obj.on( 'uploadComplete', function(file)
            {
                //uploader_obj.imgs = [];
            });

            // ����ϴ�
            uploader_obj.on( 'uploadFinished', function(file)
            {
                alert('�ϴ��ɹ�');

                if(self.options.multiple)
                {
                    self.add_item_img_obj = self.add_item_img(self.options.upload_container,
                    {
                        imgs : uploader_obj.imgs
                    });

                }



            });

            uploader_obj.on( 'all', function( type )
            {
                var stats;
                switch( type )
                {
                    case 'uploadFinished':
                    case 'uploadError':
                        uploader_obj.state = 'confirm';
                        $('[data-role="go-on-add-btn"]').removeClass('webuploader-element-invisible ');
                        break;

                    case 'startUpload':
                        uploader_obj.state = 'uploading';

                        $('[data-role="go-on-add-btn"]').addClass('webuploader-element-invisible ');

                        break;

                    case 'stopUpload':
                        uploader_obj.state = 'paused';
                        break;


                }
            });

            /**
             * ����״̬
             * @param state
             * @param $el
             * @param options
             */
            function update_status(state,$el,options)
            {
                var text = '';
                var stats;
                var fileCount = uploader_obj.file_count;
                var fileSize = uploader_obj.file_size;

                if (state === 'ready')
                {
                    text = 'ѡ��' + uploader_obj.file_count + '��ͼƬ����' +
                            WebUploader.formatSize( fileSize ) + '��';
                }
                else if ( state === 'confirm' )
                {
                    stats = uploader_obj.getStats();

                    if ( stats.uploadFailNum )
                    {
                        text = '�ѳɹ��ϴ�' + stats.successNum+ '����Ƭ����ᣬ'+
                                stats.uploadFailNum + '����Ƭ�ϴ�ʧ�ܣ�<a class="retry" href="#">�����ϴ�</a>ʧ��ͼƬ��<a class="ignore" href="#">����</a>'
                    }

                }
                else
                {
                    stats = uploader_obj.getStats();
                    text = '��' + fileCount + '�ţ�' +
                            WebUploader.formatSize( fileSize )  +
                            '�������ϴ�' + stats.successNum + '��';

                    if ( stats.uploadFailNum )
                    {
                        text += '��ʧ��' + stats.uploadFailNum + '��';
                    }
                }

                $el.html( text );
            }

            return uploader_obj;





        }
    };

})(window,$);