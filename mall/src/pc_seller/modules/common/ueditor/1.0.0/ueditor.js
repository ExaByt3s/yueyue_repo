(function()
{
	require('./ueditor.config');
	require('./ueditor.all');
    require('../../upload/1.0.0/yueyue_uploader');

    var utility = require('../../utility/index');
    var cookie = require('../../cookie/index');

    //var current_url = __uri('./ueditor').split('/');
	if(/test/.test(window.location.href))
	{
		var current_url = 'http://static.yueus.com/mall/seller/test/static/pc/modules/common/ueditor/1.0.0/ueditor.js'.split('/');
	}
	else
	{
		var current_url = 'http://static-c.yueus.com/mall/seller/static/pc/modules/common/ueditor/1.0.0/ueditor.js'.split('/');
	}
    current_url.pop();
    current_url = current_url.join('/');

	var doc = document;
	/**
	 * 默认配置
	 */
	var defaultConfig = {
		target: '',
		toolbars: [
						//'fontsize','|',
						//'undo', 'redo', '|',
						//'bold', 'italic', 'underline', 'strikethrough', '|',
						//'insertvideo','|',
						//'fullscreen'
					],
		autoFocus: false,
		autoHeight: false,
		zIndex: 1
	},

		extendFun = {
                    /**
                     * 初始化
                     */
                    _init: function() {
                        var self = this, cfg = self.config,
                            editorContainer = doc.createElement('div'),
                            isImageBtn = $.inArray('simpleupload', cfg.toolbars);

                        self._setup_btn();


                        if (cfg.toolbars) {

                            cfg.toolbars = [cfg.toolbars];
                        }

						if(!cfg.content)
                        {
                            cfg.content = $(cfg.target).html();
                        }

                        $(editorContainer).insertBefore(cfg.target);

                        self._initEditor(); // 初始化编辑器
                        self.editor.render(editorContainer); // 显示

                        cfg.target.style.display = 'none';


                    },
                    _setup_btn : function()
                    {
                        var self = this;

                        self._setup_simple_upload_btn();
                        //self._setup_video_upload_btn();


                    },
                    _setup_video_upload_btn :  function()
                    {
                        var self = this;

                        UE.registerUI('video-upload', function(editor, uiName) {


                            //注册按钮执行时的command命令，使用命令默认就会带有回退操作
                            editor.registerCommand(uiName,
                                {
                                    execCommand: function()
                                    {

                                    }
                                });
                            //创建一个button
                            var btn = new UE.ui.Button
                            ({
                                //按钮的名字
                                name: 'videoupload',
                                //提示
                                title: '视频上传',
                                //label : '<div class="editor-upload-btn" style="height: 20px !important;width: 20px !important;position: absolute;top: 0;left: 0;"></div>',
                                //添加额外样式，指定icon图标，这里默认使用一个重复的icon
                                //cssRules: 'background-position: -380px 0px;',
                                //点击时执行的命令
                                onclick: function()
                                {
                                    layer.open
                                    ({
                                        type: 1,
                                        area: ['600px', '430px'],
                                        fix: false, //不固定
                                        title:'上传视频',
                                        maxmin: false,
                                        content : '<div class="edit-video-wrapper "><div id="videoTab"><div id="tabBodys" class="tabbody"><div id="video" class="panel focus"><table><tbody><tr><td><label for="videoUrl" class="url">视频网址</label></td><td><input id="videoUrl" type="text" /></td></tr></tbody></table><div id="preview"></div><div id="videoInfo"><fieldset><legend>视频尺寸</legend><table><tbody><tr><td><label for="videoWidth">宽度</label></td><td><input class="txt" id="videoWidth" type="text" /></td></tr><tr><td><label for="videoHeight">高度</label></td><td><input class="txt" id="videoHeight" type="text" /></td></tr></tbody></table></fieldset></div></div></div><div class="video-upload-btn-container clearfix"><button class="upload-btn confirm" data-role="confirm-video">确定</button><button class="upload-btn cancel" data-role="close-video">关闭</button></div></div></div>',
                                        success : function(obj,index)
                                        {
                                            console.log(obj)

                                            obj.find('[data-role="confirm-video"]').on('click',function()
                                            {
                                                var url = obj.find('#videoUrl').val();
                                                var width  = obj.find('#videoWidth').val();
                                                var height  = obj.find('#videoHeight').val();

                                                if(!url)
                                                {
                                                    alert('请输入视频地址');
                                                    return;
                                                }

                                                if(!/http:\/\/[A-Za-z0-9\.-]{3,}\.[A-Za-z]{3}/.test(url))
                                                {
                                                    alert('不能输入非法地址');
                                                    return;
                                                }

                                                var videoAttr =
                                                    {
                                                        //视频地址
                                                        url: url,
                                                        //视频宽高值， 单位px
                                                        width: parseInt(width) || 420,
                                                        height: parseInt(height) || 280
                                                    };
                                                //这里可以不用执行命令,做你自己的操作也可
                                                editor.execCommand('insertvideo',videoAttr);

                                                layer.close(index);
                                            });
                                            obj.find('#videoUrl').on('propertychange input',function()
                                            {
                                                var url = obj.find('#videoUrl').val();

                                                if(!url)
                                                {
                                                    return;
                                                }

                                                url = convert_url(url);

                                                obj.find('#preview').html(creatInsertStr(url,420,280,'','','','embed'))
                                            });

                                            obj.find('[data-role="close-video"]').on('click',function()
                                            {
                                                layer.close(index);
                                            });
                                        },
                                        cancle : function()
                                        {

                                        }
                                    });



                                }
                            });



                            return btn;
                        });
                    },
            _setup_simple_upload_btn : function()
            {
                var self = this;

                UE.registerUI('simple-upload', function(editor, uiName)
                {


                    //注册按钮执行时的command命令，使用命令默认就会带有回退操作
                    editor.registerCommand(uiName,
                        {
                            execCommand: function()
                            {
                                var yue_upload_obj = new yue_upload_class
                                ({
                                    upload_total_limit :9,
                                    upload_container : false,
                                    user_id : cookie.get('yue_member_id'),
                                    multiple : true,
                                    imgs : [],
                                    sortable : false,
                                    fileSizeLimit: 27 * 1024 * 1024,    // 9 M 总文件大小
                                    fileSingleSizeLimit: 3 * 1024 * 1024,    // 3 M 单文件大小
                                    gid : 1
                                });

                                var upload_obj = {};
                                first_layer = layer.open
                                ({
                                    type: 1,
                                    area: ['800px', '450px'],
                                    fix: false, //不固定
                                    title:'上传图片',
                                    maxmin: false,
                                    content: yue_upload_obj.tpl,
                                    success : function($el,index)
                                    {
                                        var sec_layer = null;
                                        var index = index;


                                        setTimeout(function()
                                        {
                                            upload_obj = yue_upload_obj.init_upload($el);

                                            upload_obj.on('uploadProgress',function()
                                            {

                                            })
                                                .on('fileQueued',function(file)
                                                {

                                                })
                                                .on('error',function(err)
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
                                                        case 'F_DUPLICATE':
                                                            alert('重复上传了');
                                                            upload_obj.reset();
                                                            break;
                                                        default:
                                                            alert('上传错误');
                                                    }
                                                })
                                                .on('uploadSuccess',function(file,response)
                                                {
                                                    // response 为服务端返回的数据
                                                    try{
                                                        console.log(response);

                                                        var url =  response.url[0] || '';

                                                        if(url)
                                                        {
                                                            url = utility.matching_img_size(url,'');
                                                        }

                                                        var file_id = file.id;


                                                        self.editor.execCommand('inserthtml', '<img class="loadingclass" id="' + file_id + '" src="'+__uri('./themes/default/images/spacer.gif')+'" >');

                                                        var loader = self.editor.document.getElementById(file_id);

                                                        img_ready(url,
                                                        {
                                                            load : function()
                                                            {
                                                                if(!loader)
                                                                {
                                                                    upload_obj.stop(true);
                                                                    return;
                                                                }

                                                                loader.setAttribute('src', url);
                                                                loader.setAttribute('_src', url);
                                                                loader.removeAttribute('id');
                                                                $(loader).removeClass('loadingclass');                
                                                            },
                                                            error : function()
                                                            {

                                                            }

                                                        });

                                                       
                                                    }catch(e)
                                                    {
                                                        console.log(e)
                                                    }

                                                })
                                                .on('uploadError',function()
                                                {

                                                })
                                                .on('uploadFinished',function()
                                                {
                                                    upload_obj.reset();
                                                    layer.close(index);
                                                })
                                        },300);
                                    },
                                    cancel : function()
                                    {
                                        if(!confirm('注意！关闭弹窗会终止上传图片'))
                                        {
                                            return false;
                                        }

                                        upload_obj && upload_obj && upload_obj.stop(true);
                                    }
                                });
                            }
                        });
                    //创建一个button
                    var btn = new UE.ui.Button
                    ({
                        //按钮的名字
                        name: 'simpleupload',
                        //提示
                        title: '图片上传',
                        //label : '<div class="editor-upload-btn" style="height: 20px !important;width: 20px !important;position: absolute;top: 0;left: 0;"></div>',
                        //添加额外样式，指定icon图标，这里默认使用一个重复的icon
                        //cssRules: 'background-position: -380px 0px;',
                        //点击时执行的命令
                        onclick: function()
                        {
                            //这里可以不用执行命令,做你自己的操作也可
                            editor.execCommand(uiName);
                        }
                    });

                    return btn;
                });
            },
                    /**
                     * 初始化编辑器
                     */
                    _initEditor: function() {
                        var self = this, cfg = self.config;

                        var default_config = {
                            initialContent:cfg.content, // 初始化编辑器的内容
                            //initialStyle:WO_STYLE, // 编辑器内部样式
                            enterTag:'p', // 编辑器回车标签。p或br
                            toolbars:cfg.toolbars, // 工具栏上的所有的功能按钮和下拉框
                            initialFrameHeight : 700,
                            minFrameHeight:700, // 最小高度
                            autoHeightEnabled:cfg.autoHeight, // 是否自动长高cfg.autoHeight
                            autoFloatEnabled:true, // 是否保持toolbar的位置不动
                            serialize: function() { // 配置过滤标签
                                return self._serializeConfig();
                            },
                            elementPathEnabled:false, // 是否启用elementPath
                            wordCount:false, // 是否开启字数统计
                            sourceEditor:'textarea', // 源码的查看方式，codemirror 是代码高亮，textarea是文本框
                            imagePopup:false, // 图片操作的浮层开关，默认打开
                            focus:cfg.autoFocus, // 初始化时，是否让编辑器获得焦点true或false
                            zIndex:cfg.zIndex,
                            readonly : cfg.readonly
                        };

                        //default_config = $.extend(default_config,cfg);


                        self.editor = new baidu.editor.ui.Editor(default_config);


                    }



                };

    /**
     * 创建插入视频字符窜
     * @param url 视频地址
     * @param width 视频宽度
     * @param height 视频高度
     * @param align 视频对齐
     * @param toEmbed 是否以flash代替显示
     * @param addParagraph  是否需要添加P 标签
     */
    function creatInsertStr(url,width,height,id,align,classname,type)
    {
        var str;
        switch (type){
            case 'image':
                str = '<img ' + (id ? 'id="' + id+'"' : '') + ' width="'+ width +'" height="' + height + '" _url="'+url+'" class="' + classname.replace(/\bvideo-js\b/, '') + '"'  +
                    ' src="' + me.options.UEDITOR_HOME_URL+'themes/default/images/spacer.gif" style="background:url('+me.options.UEDITOR_HOME_URL+'themes/default/images/videologo.gif) no-repeat center center; border:1px solid gray;'+(align ? 'float:' + align + ';': '')+'" />'
                break;
            case 'embed':
                str = '<div class="previewMsg"><span>输入的视频地址有误，请检查好再重试</span></div><embed type="application/x-shockwave-flash" class="previewVideo" pluginspage="http://www.macromedia.com/go/getflashplayer"' +
                    ' src="' +  url + '" width="' + width  + '" height="' + height  + '"'  + (align ? ' style="float:' + align + '"': '') +
                    ' wmode="transparent" play="true" loop="false" menu="false" allowscriptaccess="never" allowfullscreen="true" >';
                break;
            case 'video':
                var ext = url.substr(url.lastIndexOf('.') + 1);
                if(ext == 'ogv') ext = 'ogg';
                str = '<video' + (id ? ' id="' + id + '"' : '') + ' class="' + classname + ' video-js" ' + (align ? ' style="float:' + align + '"': '') +
                    ' controls preload="none" width="' + width + '" height="' + height + '" src="' + url + '" data-setup="{}">' +
                    '<source src="' + url + '" type="video/' + ext + '" /></video>';
                break;
        }
        return str;
    }

    /**
     * 视频地址转换函数
     *
     **/
    function convert_url(url)
    {
        if ( !url ) return '';
        url = UE.utils.trim(url)
            .replace(/v\.youku\.com\/v_show\/id_([\w\-=]+)\.html/i, 'player.youku.com/player.php/sid/$1/v.swf')
            .replace(/(www\.)?youtube\.com\/watch\?v=([\w\-]+)/i, "www.youtube.com/v/$2")
            .replace(/youtu.be\/(\w+)$/i, "www.youtube.com/v/$1")
            .replace(/v\.ku6\.com\/.+\/([\w\.]+)\.html.*$/i, "player.ku6.com/refer/$1/v.swf")
            .replace(/www\.56\.com\/u\d+\/v_([\w\-]+)\.html/i, "player.56.com/v_$1.swf")
            .replace(/www.56.com\/w\d+\/play_album\-aid\-\d+_vid\-([^.]+)\.html/i, "player.56.com/v_$1.swf")
            .replace(/v\.pps\.tv\/play_([\w]+)\.html.*$/i, "player.pps.tv/player/sid/$1/v.swf")
            .replace(/www\.letv\.com\/ptv\/vplay\/([\d]+)\.html.*$/i, "i7.imgs.letv.com/player/swfPlayer.swf?id=$1&autoplay=0")
            .replace(/www\.tudou\.com\/programs\/view\/([\w\-]+)\/?/i, "www.tudou.com/v/$1")
            .replace(/v\.qq\.com\/cover\/[\w]+\/[\w]+\/([\w]+)\.html/i, "static.video.qq.com/TPout.swf?vid=$1")
            .replace(/v\.qq\.com\/.+[\?\&]vid=([^&]+).*$/i, "static.video.qq.com/TPout.swf?vid=$1")
            .replace(/my\.tv\.sohu\.com\/[\w]+\/[\d]+\/([\d]+)\.shtml.*$/i, "share.vrs.sohu.com/my/v.swf&id=$1");

        return url;
    }

	/**
	 * 内容发布编辑器
	 * @constructor
	 */
	function Ueditor(config) {
		var self = this;

		// factory or constructor
		if (!(self instanceof Ueditor)) {
			return new Ueditor(config);
		}

		if (typeof config.target === 'string') {
			config.target = $(config.target)[0];
		}

		// mix config
		self.config = $.extend(true, {}, defaultConfig, config);

		self.editor;

		// 初始化
		self._init();
	}


    function img_ready(imgUrl, options)
    {
        var img = new Image();

        img.src = imgUrl;

        // 如果图片被缓存，则直接返回缓存数据
        if (img.complete) {
            isFunction(options.load) && options.load.call(img);
            return;
        }

        // 加载错误后的事件
        img.onerror = function () {
            isFunction(options.error) && options.error.call(img);
            img = img.onload = img.onerror = null;

            //delete img;
        };

        // 完全加载完毕的事件
        img.onload = function () {
            isFunction(options.load) && options.load.call(img);

            // IE gif动画会循环执行onload，置空onload即可
            img = img.onload = img.onerror = null;

            //delete img;
        };
    };

    function isFunction(o) {
        return typeof o === 'function';
    }

	$.extend(Ueditor.prototype, extendFun);

	window.Ueditor = Ueditor;
})();