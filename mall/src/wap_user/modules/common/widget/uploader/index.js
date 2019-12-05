/**
 * Created by hudingwen on 15/6/4.
 * 上传组件
 */


/**
 * @require ./main.scss
 *
 */

var $ = require('zepto');
var App = require('../../I_APP/I_APP');
var utility = require('../../utility/index');
var cookie = require('../../cookie/index');
var template  = __inline('./main.tmpl');
var LZ = require('../../lazyload/lazyload');


(function(window,undefined)
{
    var yueyue_uploader = function(options)
    {
        var self = this;

        options = options || {};

        // 参数设置
        var params = options.params || {};

        var file_count = 0;
        var file_size = 0;

        var form_data =
            {
                poco_id : cookie.get('yue_member_id'),
                sh_type : 'merchandise'
            };

        form_data = $.extend(true,form_data,options.formData);
        form_data = JSON.stringify(form_data);

        var default_params =
            {

                chunked: false,
                chunkSize: 512 * 1024,
                server: 'http://sendmedia-w.yueus.com:8079/upload.cgi',
                // auto 设置true，选择图片即为自动上传
                auto: false,
                cover_img : '',
                runtimeOrder: 'html5',
                formData :
                {
                    params :  form_data
                },
                accept:
                {
                    title: 'Images',
                    extensions: 'jpg,jpeg,png',
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

        // 判断浏览器是否支持图片的base64
        var isSupportBase64 = ( function() {
            var data = new Image();
            var support = true;
            data.onload = data.onerror = function() {
                if( this.width != 1 || this.height != 1 ) {
                    support = false;
                }
            }
            data.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
            return support;
        } )();

        // 实例化
        var uploader = WebUploader.create(params);

        // 初始化文件数
        uploader.file_count = file_count;
        uploader.file_size = file_size;

        // 传图的状态 可能有pedding, ready, uploading, confirm, done.
        uploader.state = 'pedding';

        uploader.map_obj = {};

        uploader.$el = $(params.pick.id);

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



        uploader.get_file_by_id = function(file_id)
        {
            return uploader.map_obj[file_id] || '';
        };

        uploader.on('error',function(err)
        {
            console.log('%c'+err,'color:red');

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



        return uploader;
    };

    window.yueyue_uploader = yueyue_uploader;


})(window);

module.exports =
{
    init : function(options)
    {
        var self = this;

        self.total_count = 0;


        return self;
    },
    plus_btn : function(options)
    {
        var self = this;

        var add_obj = {};

        add_obj.default_img_url = __inline('../../../../image/pai/add.png');
        add_obj.width = options.grid_size;
        add_obj.height = add_obj.width;
        add_obj.is_add_btn = "1";
        add_obj.is_active = 'active';

        self.has_init_puls_btn = true;

        var html_str = template({list:[add_obj]});

        return html_str;
    },
    add: function ($el,options)
    {
        // tpl后缀的文件也可以用于模板嵌入，相比handlebars
        // tpl文件不具有模板变量功能，嵌入后只是作为字符串使
        // 用，tpl文件嵌入之前可以被插件压缩，体积更小。
        // handlebars由于缺少相应的压缩插件因此暂时不能在预
        // 编译阶段做压缩。选择tpl还是handlebars可以自由选
        // 择

        //console.log(css);

        var self = this;

        var list = options.list;

        for(var i=0;i<list.length;i++)
        {
            list[i].is_add_btn = "0";
            list[i].is_active = '';
            list[i].width = options.grid_size;
            list[i].height = list[i].width;

            self.total_count ++;


        }

        /*if(self.total_count < options.total_limit )
        {
            var add_obj = {};

            add_obj.default_img_url = __inline('../../../../image/pai/add.png');
            add_obj.width = options.grid_size;
            add_obj.height = add_obj.width;
            add_obj.is_add_btn = "1";
            add_obj.is_active = 'active';

            list.unshift(add_obj);
        }*/

        var html_str = template(options);

        self.$el = $el;

        self.$el.append(html_str);

        self._setup_event();

        //后加载
        if(!self.lazyloading)
        {
            self.lazyloading = new LZ(self.$el,{});
        }
        else
        {
            self.lazyloading.refresh();
        }


        return self;

    },
    _setup_event : function()
    {
        var self = this;

        self.$el.find('[data-role="del-pic"]').one('click',function(ev)
        {
            self.total_count--;

            var $cur_btn = $(ev.currentTarget);

            $cur_btn.parents('.ui-uploader-items-container').remove();

            self.$el.trigger('delete:upload_items');
        });

        self.$el.find('[data-role="upload-flag"]').on('click',function()
        {
            self.$el.trigger('click:upload_flag');
        });
    },
    show : function()
    {
        var self = this;
    },
    hide : function()
    {
        var self = this;
    },
    reset : function()
    {
        var self = this;

        self.render(self.$el,{list:[]});

    },
    upload_action : function(options)
    {
        var self = this;
        if(App.isPaiApp)
        {

            App.upload_img
            ('multi_img',{
                is_async_upload : 0,
                max_selection : 1,
                is_zip : 1

            },function(data)
            {
                var pic_list=[];

                self.pic_list = [];

                if(data.imgs.length>0)
                {
                    for(var i = 0;i<data.imgs.length;i++)
                    {

                        var img = utility.matching_img_size(data.imgs[i].url,165);

                        pic_list[0] = img;
                    }


                }

                self.pic_list = pic_list;

                options.callback && options.callback.call(this,pic_list);
            });


        }
        else
        {
            // wap版传图功能

            self.pic_list = [];

            var pic_list =
                [
                    'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_640.jpg'
                ];

            self.pic_list = pic_list;

            options.callback && options.callback.call(this,pic_list);

        }

    },
    init_webupload : function(options)
    {
        var self = this;

        options = options || {};

        var uploader_obj = null;

        var file_num_limit = options.file_num_limit;

        var picker_id = options.picker_id;



        if(!picker_id)
        {
            alert('没有传递初始化picker_id');
        }

        var details_params = {
            auto : false,
            fileNumLimit : file_num_limit,
            pick :
            {
                id : picker_id,
                label : ''
            },

            formData : options.form_data || {}
        };

        details_params = $.extend(true,details_params,options);

        uploader_obj = yueyue_uploader(details_params);

        uploader_obj.imgs = [];

        // 缩略图的size
        var thumbnailWidth = 130;
        var thumbnailHeight = 130;

        // 开始上传按钮
        uploader_obj.on('startUpload',function()
        {
            if(uploader_obj.state == 'uploading')
            {
                return;
            }

            uploader_obj.upload();
        });

        // 当有文件添加进来的时候
        uploader_obj.on('fileQueued', function(file)
        {
            console.log(file);

            if(uploader_obj.file_count >= 1)
            {
                // 当选择的文件数等于1时，可以进行继续选择
                uploader_obj.refresh();

            }
            else
            {
                // todo 异常处理
                return;
            }


            // 更新状态
            uploader_obj.state = 'ready';


            // 创建缩略图
            // 如果为非图片文件，可以不用调用此方法。
            // thumbnailWidth x thumbnailHeight 为 100 x 100
            uploader_obj.makeThumb(file, function( error, src )
            {
                if(error)
                {
                    // 不能预览时的处理
                    return;
                }

                // 预览图片的路径
                console.log(src);

            }, thumbnailWidth, thumbnailHeight );
        });

        // 当文件被移除队列后触发。
        uploader_obj.on( 'fileDequeued', function(file)
        {
            if(!uploader_obj.file_count)
            {
                // 更新状态
                uploader_obj.state = 'pedding';

                // 当选择的文件数等于1时，可以进行继续选择
                uploader_obj.refresh();
            }



            update_status(uploader_obj.state,$upload_tips);

            console.log('remove file');
        });

        // 文件开始上传前触发，一个文件只会触发一次。
        uploader_obj.on('uploadStart', function(file)
        {
            var file_id = file.id;


        });

        // 文件上传过程中创建进度条实时显示。
        uploader_obj.on('uploadProgress', function(file, percentage)
        {
            console.log(percentage);
        });

        // 文件上传成功。
        uploader_obj.on( 'uploadSuccess', function(file,response)
        {
            // response 为服务端返回的数据
            console.log(response);

            if(response.url instanceof Array)
            {
                var url = response.url[0] || '';
            }
            else
            {
                var url = response.url+"?"+new Date().getTime() || '';
            }

            var file_id = file.id;


        });

        // 文件上传失败，显示上传出错。
        uploader_obj.on( 'uploadError', function(file)
        {
            uploader_obj.imgs = [];
        });

        // 文件上传失败，显示上传出错。
        uploader_obj.on( 'uploadComplete', function(file)
        {
            //uploader_obj.imgs = [];
        });

        // 完成上传
        uploader_obj.on( 'uploadFinished', function(file)
        {


        });

        uploader_obj.on( 'all', function( type )
        {
            var stats;
            switch( type )
            {
                case 'uploadFinished':
                case 'uploadError':
                    uploader_obj.state = 'confirm';
                    break;

                case 'startUpload':
                    uploader_obj.state = 'uploading';


                    break;

                case 'stopUpload':
                    uploader_obj.state = 'paused';
                    break;


            }
        });

        return uploader_obj;
    }


};