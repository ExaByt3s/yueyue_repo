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
	 * Ĭ������
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
                     * ��ʼ��
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

                        self._initEditor(); // ��ʼ���༭��
                        self.editor.render(editorContainer); // ��ʾ

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


                            //ע�ᰴťִ��ʱ��command���ʹ������Ĭ�Ͼͻ���л��˲���
                            editor.registerCommand(uiName,
                                {
                                    execCommand: function()
                                    {

                                    }
                                });
                            //����һ��button
                            var btn = new UE.ui.Button
                            ({
                                //��ť������
                                name: 'videoupload',
                                //��ʾ
                                title: '��Ƶ�ϴ�',
                                //label : '<div class="editor-upload-btn" style="height: 20px !important;width: 20px !important;position: absolute;top: 0;left: 0;"></div>',
                                //��Ӷ�����ʽ��ָ��iconͼ�꣬����Ĭ��ʹ��һ���ظ���icon
                                //cssRules: 'background-position: -380px 0px;',
                                //���ʱִ�е�����
                                onclick: function()
                                {
                                    layer.open
                                    ({
                                        type: 1,
                                        area: ['600px', '430px'],
                                        fix: false, //���̶�
                                        title:'�ϴ���Ƶ',
                                        maxmin: false,
                                        content : '<div class="edit-video-wrapper "><div id="videoTab"><div id="tabBodys" class="tabbody"><div id="video" class="panel focus"><table><tbody><tr><td><label for="videoUrl" class="url">��Ƶ��ַ</label></td><td><input id="videoUrl" type="text" /></td></tr></tbody></table><div id="preview"></div><div id="videoInfo"><fieldset><legend>��Ƶ�ߴ�</legend><table><tbody><tr><td><label for="videoWidth">���</label></td><td><input class="txt" id="videoWidth" type="text" /></td></tr><tr><td><label for="videoHeight">�߶�</label></td><td><input class="txt" id="videoHeight" type="text" /></td></tr></tbody></table></fieldset></div></div></div><div class="video-upload-btn-container clearfix"><button class="upload-btn confirm" data-role="confirm-video">ȷ��</button><button class="upload-btn cancel" data-role="close-video">�ر�</button></div></div></div>',
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
                                                    alert('��������Ƶ��ַ');
                                                    return;
                                                }

                                                if(!/http:\/\/[A-Za-z0-9\.-]{3,}\.[A-Za-z]{3}/.test(url))
                                                {
                                                    alert('��������Ƿ���ַ');
                                                    return;
                                                }

                                                var videoAttr =
                                                    {
                                                        //��Ƶ��ַ
                                                        url: url,
                                                        //��Ƶ���ֵ�� ��λpx
                                                        width: parseInt(width) || 420,
                                                        height: parseInt(height) || 280
                                                    };
                                                //������Բ���ִ������,�����Լ��Ĳ���Ҳ��
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


                    //ע�ᰴťִ��ʱ��command���ʹ������Ĭ�Ͼͻ���л��˲���
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
                                    fileSizeLimit: 27 * 1024 * 1024,    // 9 M ���ļ���С
                                    fileSingleSizeLimit: 3 * 1024 * 1024,    // 3 M ���ļ���С
                                    gid : 1
                                });

                                var upload_obj = {};
                                first_layer = layer.open
                                ({
                                    type: 1,
                                    area: ['800px', '450px'],
                                    fix: false, //���̶�
                                    title:'�ϴ�ͼƬ',
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
                                                        case 'F_DUPLICATE':
                                                            alert('�ظ��ϴ���');
                                                            upload_obj.reset();
                                                            break;
                                                        default:
                                                            alert('�ϴ�����');
                                                    }
                                                })
                                                .on('uploadSuccess',function(file,response)
                                                {
                                                    // response Ϊ����˷��ص�����
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
                                        if(!confirm('ע�⣡�رյ�������ֹ�ϴ�ͼƬ'))
                                        {
                                            return false;
                                        }

                                        upload_obj && upload_obj && upload_obj.stop(true);
                                    }
                                });
                            }
                        });
                    //����һ��button
                    var btn = new UE.ui.Button
                    ({
                        //��ť������
                        name: 'simpleupload',
                        //��ʾ
                        title: 'ͼƬ�ϴ�',
                        //label : '<div class="editor-upload-btn" style="height: 20px !important;width: 20px !important;position: absolute;top: 0;left: 0;"></div>',
                        //��Ӷ�����ʽ��ָ��iconͼ�꣬����Ĭ��ʹ��һ���ظ���icon
                        //cssRules: 'background-position: -380px 0px;',
                        //���ʱִ�е�����
                        onclick: function()
                        {
                            //������Բ���ִ������,�����Լ��Ĳ���Ҳ��
                            editor.execCommand(uiName);
                        }
                    });

                    return btn;
                });
            },
                    /**
                     * ��ʼ���༭��
                     */
                    _initEditor: function() {
                        var self = this, cfg = self.config;

                        var default_config = {
                            initialContent:cfg.content, // ��ʼ���༭��������
                            //initialStyle:WO_STYLE, // �༭���ڲ���ʽ
                            enterTag:'p', // �༭���س���ǩ��p��br
                            toolbars:cfg.toolbars, // �������ϵ����еĹ��ܰ�ť��������
                            initialFrameHeight : 700,
                            minFrameHeight:700, // ��С�߶�
                            autoHeightEnabled:cfg.autoHeight, // �Ƿ��Զ�����cfg.autoHeight
                            autoFloatEnabled:true, // �Ƿ񱣳�toolbar��λ�ò���
                            serialize: function() { // ���ù��˱�ǩ
                                return self._serializeConfig();
                            },
                            elementPathEnabled:false, // �Ƿ�����elementPath
                            wordCount:false, // �Ƿ�������ͳ��
                            sourceEditor:'textarea', // Դ��Ĳ鿴��ʽ��codemirror �Ǵ��������textarea���ı���
                            imagePopup:false, // ͼƬ�����ĸ��㿪�أ�Ĭ�ϴ�
                            focus:cfg.autoFocus, // ��ʼ��ʱ���Ƿ��ñ༭����ý���true��false
                            zIndex:cfg.zIndex,
                            readonly : cfg.readonly
                        };

                        //default_config = $.extend(default_config,cfg);


                        self.editor = new baidu.editor.ui.Editor(default_config);


                    }



                };

    /**
     * ����������Ƶ�ַ���
     * @param url ��Ƶ��ַ
     * @param width ��Ƶ���
     * @param height ��Ƶ�߶�
     * @param align ��Ƶ����
     * @param toEmbed �Ƿ���flash������ʾ
     * @param addParagraph  �Ƿ���Ҫ���P ��ǩ
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
                str = '<div class="previewMsg"><span>�������Ƶ��ַ���������������</span></div><embed type="application/x-shockwave-flash" class="previewVideo" pluginspage="http://www.macromedia.com/go/getflashplayer"' +
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
     * ��Ƶ��ַת������
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
	 * ���ݷ����༭��
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

		// ��ʼ��
		self._init();
	}


    function img_ready(imgUrl, options)
    {
        var img = new Image();

        img.src = imgUrl;

        // ���ͼƬ�����棬��ֱ�ӷ��ػ�������
        if (img.complete) {
            isFunction(options.load) && options.load.call(img);
            return;
        }

        // ���ش������¼�
        img.onerror = function () {
            isFunction(options.error) && options.error.call(img);
            img = img.onload = img.onerror = null;

            //delete img;
        };

        // ��ȫ������ϵ��¼�
        img.onload = function () {
            isFunction(options.load) && options.load.call(img);

            // IE gif������ѭ��ִ��onload���ÿ�onload����
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