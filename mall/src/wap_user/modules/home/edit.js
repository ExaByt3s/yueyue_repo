
var $ = require('zepto');
var header = require('../common/widget/header/main');
var utility = require('../common/utility/index');
var uploader = require('../common/widget/uploader/index');
var App =  require('../common/I_APP/I_APP.js');
var yue_ui = require('../yue_ui/frozen');
var input_page = require('./input');

//ͷ�����
$(document).ready(function()
{

    var _self = $({});

    _self.$edit_page = $('[data-role="edit"]');
    _self.$input_text_page = $('[data-role="input-text"]');
    _self.$input_textarea_page = $('[data-role="input-textarea"]');
    _self.$input_location_page = $('[data-role="input-location"]');
    _self.$name = $('#name');
    _self.$intro = $('#intro');

    var edit_page_class = function()
    {
        var self = this;

        self.init();
    };

    edit_page_class.prototype =
    {
        init : function()
        {
            var self = this;

            self.setup_edit_page();
            self.setup_location();
            self.setup_input
            (
                _self.$input_text_page,
                {
                    id :'name',
                    type:'text',
                    limit:16,
                    input_title : '�ǳ�',
                    page_title : '�༭�ǳ�',
                    input_content : _self.$name.val()
                }
            );
            self.setup_input
            (
                _self.$input_textarea_page,
                {
                    id :'intro',
                    type:'textarea',
                    limit:140,
                    input_title : '���˼��',
                    page_title : '�༭���˼��',
                    input_content : _self.$intro.val()
                }
            );

            self.setup_header_click_event();
        },
        setup_edit_page : function()
        {
            var self = this;

            _self.edit_page_header_obj = header.init({
                ele        : _self.$edit_page.find('[data-role="global-header"]'), //ͷ����Ⱦ�Ľڵ�
                title      : "�༭�ҵ�����",
                header_show: true, //�Ƿ���ʾͷ��
                right_icon_show: true, //�Ƿ���ʾ�ұߵİ�ť
                share_icon : {
                    show   : false,  //�Ƿ���ʾ����ťicon
                    content: ""
                },
                omit_icon  : {
                    show   : false,  //�Ƿ���ʾ����Բ��icon
                    content: ""
                },
                show_txt   : {
                    show   : true,  //�Ƿ���ʾ����
                    content: "����"  //��ʾ��������
                }
            });



            //�ǳ�
            $('[data-role="nickname"]').on('click',function()
            {
                _self.$input_text_page.removeClass('fn-hide');
                _self.$input_textarea_page.addClass('fn-hide');
                _self.$input_location_page.addClass('fn-hide');
                _self.$edit_page.addClass('fn-hide');
            });
            //���˼��
            $('[data-role="introduce"]').on('click',function()
            {
                _self.$input_text_page.addClass('fn-hide');
                _self.$input_textarea_page.removeClass('fn-hide');
                _self.$input_location_page.addClass('fn-hide');
                _self.$edit_page.addClass('fn-hide');
            });
            //����
            $('[data-role="city"]').on('click',function()
            {
                _self.$input_text_page.addClass('fn-hide');
                _self.$input_textarea_page.addClass('fn-hide');
                _self.$input_location_page.removeClass('fn-hide');
                _self.$edit_page.addClass('fn-hide');


            });
            /*�ϴ�ͼƬ*/
            /*setTimeout(function()
             {
             self.setup_uploader();
             self.setup_head_icon_upload();
             },500);*/

        },
        setup_head_icon_upload : function()
        {
            var self = this;

            if(!App.isPaiApp)
            {
                console.log(self.uploader_obj);
                self.upload_head_obj = self.uploader_obj.init_webupload
                ({
                    picker_id : '#head-icon',
                    auto : true,
                    server : "http://sendmedia-w.yueus.com:8078/icon.cgi",
                    file_num_limit : 1,
                    fileSizeLimit: 9 * 1024 * 1024,    // 9 M ���ļ���С
                    fileSingleSizeLimit: 3 * 1024 * 1024,    // 3 M ���ļ���С
                    pick :
                    {
                        multiple : false
                    },
                    formData :
                    {
                        hash: __icon_hash
                    }
                });

                // ��װ�ϴ�����ĸ����¼�
                self.upload_head_obj.on('startUpload',function()
                {
                    self.$loading = $.loading
                    ({
                        content:'ͷ���ϴ���... ����ر�'
                    });
                })
                    .on('error',function(err)
                    {
                        self.upload_head_obj.err = err;
                    })
                    .on('uploadSuccess',function(file,response)
                    {
                        // response ����˷�������

                        if(response.code == 0)
                        {
                            $('.avatar').attr('src',response.url+"?"+new Date().getTime());
                            $('[data-role="avatar"]').val(response.url);
                        }

                        self.upload_head_obj.err = '';

                    })
                    .on('uploadError',function(file,reason)
                    {
                        self.$loading.loading("hide");

                        $.tips
                        ({
                            content:reason,
                            stayTime:3000,
                            type:'warn'
                        });
                    })
                    .on('uploadFinished',function()
                    {
                        self.$loading.loading("hide");

                        if(self.upload_head_obj.err)
                        {
                            self.upload_head_obj.reset();

                            $.tips
                            ({
                                content:'�ϴ�ʧ��',
                                stayTime:3000,
                                type:'warn'
                            });

                            return;
                        }


                    })
            }
        },
        /**
         * ����ͼƬ���
         */
        set_upload_grid_margin : function()
        {
            var self = this;

            self.$plus_btn_tag = $('[data-role="plus"]');

            $('[data-role="upload-items"]').each(function(i,obj)
            {
                if(!self.$plus_btn_tag.hasClass('invisiable'))
                {
                    if(i % 3 ==0)
                    {
                        $(obj).addClass('middle-margin');
                    }
                    else
                    {
                        $(obj).removeClass('middle-margin');
                    }

                }
                else
                {
                    if((i+2) % 3 ==0)
                    {
                        $(obj).addClass('middle-margin');
                    }
                    else
                    {
                        $(obj).removeClass('middle-margin');
                    }

                }
            });

        },
        setup_uploader : function()
        {
            var self = this;


            self.$uploader = $('[data-role="upload-img-container"]');
            self.$cur_img_count = $('[data-role="cur-img-count"]');
            self.$total_num_label = $('[data-role="total-num"]');
            self.upload_art_list = [];

            var total_limit = 15;

            var img_list = showcase_data.result_data || [];

            self.$total_num_label.html(total_limit);

            // ��ʼ���ϴ����
            uploader.init();

            var grid_size = utility.int((window.innerWidth -66)/3);

            for(var i = 0;i<img_list.length;i++)
            {
                img_list[i].img_url = img_list[i].thumb;
            }

            self.plus_btn_html =  uploader.plus_btn({grid_size : grid_size+'px'});

            self.$uploader.append(self.plus_btn_html);

            self.has_init_puls_btn = true;


            if(img_list.length < total_limit || img_list.length == 0)
            {
                // �ж��Ƿ��ʼ����+�Ű�ť
                self.$uploader.find('[data-role="plus"]').removeClass('invisiable');
            }
            else
            {
                self.$uploader.find('[data-role="plus"]').addClass('invisiable');
            }


            // ���ͼƬ
            self.uploader_obj = uploader.add(self.$uploader,
                {
                    list:img_list,
                    total_limit : total_limit,
                    grid_size : grid_size+'px'
                });

            self.set_upload_grid_margin();

            self.$cur_img_count.html(self.$uploader.find('[data-role="upload-items"]').length);

            // ɾ��ͼƬ�¼�
            self.uploader_obj.$el.on('delete:upload_items',function()
            {
                self.$cur_img_count.html(self.$uploader.find('[data-role="upload-items"]').length);

                if(!self.has_init_puls_btn)
                {
                    if(self.uploader_obj.total_count < total_limit)
                    {
                        // �ж��Ƿ��ʼ����+�Ű�ť
                        self.$uploader.find('[data-role="plus"]').removeClass('invisiable');

                        self.has_init_puls_btn = true;
                    }
                    else
                    {
                        self.$uploader.find('[data-role="plus"]').addClass('invisiable');

                        self.has_init_puls_btn = false;
                    }
                }

                var limit = self.upload_art_obj.option('fileNumLimit')

                self.upload_art_obj.option('fileNumLimit',limit+1);

                self.set_upload_grid_margin();
            });


            if(!App.isPaiApp)
            {
                self.upload_art_obj = self.uploader_obj.init_webupload
                ({
                    picker_id : '#upload-flag',
                    auto : true,
                    file_num_limit : total_limit - self.$uploader.find('[data-role="upload-items"]').length,
                    fileSizeLimit: 9 * 1024 * 1024,    // 9 M ���ļ���С
                    fileSingleSizeLimit: 3 * 1024 * 1024    // 3 M ���ļ���С
                });

                // ��װ�ϴ�����ĸ����¼�
                self.upload_art_obj.on('startUpload',function()
                {
                    self.$loading = $.loading
                    ({
                        content:'ͼƬ�ϴ���... ����ر�'
                    });
                })
                    .on('fileQueued',function(file)
                    {

                        self.upload_art_list = [];

                    })
                    .on('fileDequeued',function(file)
                    {
                        console.log(file)
                    })
                    .on('error',function(err)
                    {
                        self.upload_art_obj.err = err;
                    })
                    .on('uploadSuccess',function(file,response)
                    {
                        // response ����˷�������

                        if(response.code == 0)
                        {
                            self.upload_art_list.push(response.url[0]);

                        }

                        var limit = self.upload_art_obj.option('fileNumLimit')

                        self.upload_art_obj.option('fileNumLimit',limit-1);

                        self.upload_art_obj.err = '';

                    })
                    .on('uploadError',function(file,reason)
                    {
                        self.$loading.loading("hide");

                        self.upload_art_list = [];

                        console.log(reason);
                    })
                    .on('uploadFinished',function()
                    {
                        self.$loading.loading("hide");

                        if(self.upload_art_obj.err)
                        {
                            self.upload_art_obj.reset();

                            $.tips
                            ({
                                content:'�ϴ�ʧ��',
                                stayTime:3000,
                                type:'warn'
                            });

                            return;
                        }

                        var arr = self.upload_art_list;

                        var temp = [];

                        for(var i =0;i<arr.length;i++)
                        {
                            temp[i] =
                            {
                                img_url : arr[i]
                            };
                        }

                        // ���ͼƬ
                        uploader.add(self.$uploader,
                            {
                                list:temp,
                                total_limit :total_limit,
                                grid_size : grid_size+'px'
                            });

                        // �ж��Ƿ��ʼ����+�Ű�ť
                        if(self.$uploader.find('[data-role="upload-items"]').length >= total_limit && self.has_init_puls_btn)
                        {
                            self.$uploader.find('[data-role="plus"]').addClass('invisiable');

                            self.has_init_puls_btn = false;
                        }

                        self.set_upload_grid_margin();


                        self.$cur_img_count.html(self.$uploader.find('[data-role="upload-items"]').length);






                    })
            }


        },
        setup_location : function()
        {
            var self = this;

            // �������
            // �������
            var location_class = require('../common/widget/location/location_main');

            // ʵ����
            var location_obj = new location_class
            ({
                ele : _self.$input_location_page.find('#location'), //��Ⱦ�����ܽڵ�
                // ���ų�������
                hot_city : {
                    is_show : true , // �Ƿ���ʾ���ų��� true��ʾ
                    data :
                        [
                            // {
                            //     city : "ï��",
                            //     location_id : 101029010
                            // }
                        ]
                },
                callback : function(res)
                {
                    // ���ѡ�г��лص�
                    $('#city_id').val(res.location_id);
                    $('#location_id').val(res.city);
                    self.show_edit_page();
                    console.log(res);

                },
                city_history_num: "3",  //���������ʷ��¼������Ĭ��12��
                is_search : false , // �Ƿ����������й��� Ĭ�ϲ�����
                verson : 2 //�汾�ţ�Ĭ��Ϊ0

            });

            self.location_page_header_obj = header.init
            ({
                ele        : _self.$input_location_page.find('[data-role="global-header"]'), //ͷ����Ⱦ�Ľڵ�
                title      : "ѡ�����",
                use_page_back : false ,
                header_show: true, //�Ƿ���ʾͷ��
                right_icon_show: true, //�Ƿ���ʾ�ұߵİ�ť
                share_icon :
                {
                    show   : false,  //�Ƿ���ʾ����ťicon
                    content: ""
                },
                omit_icon  :
                {
                    show   : false,  //�Ƿ���ʾ����Բ��icon
                    content: ""
                },
                show_txt   :
                {
                    show   : false,  //�Ƿ���ʾ����
                    content: ""  //��ʾ��������
                }
            });
            self.location_page_header_obj.$el.on('click:left_btn',function()
            {
                self.show_edit_page();
            });
        },
        setup_input : function($el,options)
        {
            var self = this;

            var input_page_obj = input_page.render($el,options);

            _self['header_'+options.id] = input_page.setup_header({title:options.page_title,$el:$el});

            return input_page_obj;
        },
        setup_header_click_event : function()
        {
            var self = this;

            _self.header_name.$el.on('click:left_btn',function()
            {
                self.show_edit_page();

            }).on('click:right_btn',function()
            {
                // ���水ť
                self.show_edit_page();
                var name_page_val = $('#nickname').val();
                $('#name').val(name_page_val);
            });

            _self.header_intro.$el.on('click:left_btn',function()
            {
                self.show_edit_page();

            }).on('click:right_btn',function()
            {
                // ���水ť
                self.show_edit_page();
                var intro_page_val = $('#introduce').val();
                $('#intro').val(intro_page_val);
            });

            _self.edit_page_header_obj.$el.on('click:right_btn',function()
            {

                var nickname = $.trim($('#name').val());
                var intro = $('#intro').val();
                var location_name = $('#location_id').val();
                var location_id = $('#city_id').val();
                var is_display_record;

                if(!nickname||!location_name){
                    alert("�ǳƻ��ַ����Ϊ��");
                    return false;
                }
                if($("#expense-calendar").attr("checked"))
                {
                    is_display_record = 1;
                }else{
                    is_display_record = 0;
                }

                var showcase = [];
                $('[data-role="img-input-url"]').each(function(i,obj){
                    showcase.push($(obj).val());
                });
                //console.log(showcase);

                var self = this;

                self._sending = false;

                if(self._sending)
                {
                    return;
                }
                utility.ajax_request_app
                ({
                    path   : 'customer/buyer_user_edit',
                    data   :
                    {
                        access_token : '11741211293062033762',
                        post_json_data :
                        {
                            nickname : nickname,
                            intro : intro,
                            location_id : location_id,
                            is_display_record : is_display_record,
                            showcase : showcase

                        }
                    },
                    beforeSend: function ()
                    {
                        self._sending = true;

                        _self.$loading = $.loading
                        ({
                            content:'������...'
                        });
                    },
                    success: function (data)
                    {
                        self._sending = false;
                        var result = data.data.result;
                        if(result = 1)
                        {
                            $.tips
                            ({
                                content: "����ɹ�",
                                stayTime:3000,
                                type:'warn'
                            });
                            setTimeout(function()
                            {
                                location.href = './user_info.php';
                            },3000);
                        }else
                        {
                            $.tips
                            ({
                                content: "����ʧ��",
                                stayTime:3000,
                                type:'warn'
                            });
                        }

                        _self.$loading.loading("hide");
                        console.log(data);
                    },
                    error  : function (err)
                    {
                        self._sending = false;
                        _self.$loading.loading("hide");
                        $.tips
                        ({
                            content:'�����쳣',
                            stayTime:3000,
                            type:'warn'
                        });
                    }
                });
            });



        },
        show_edit_page : function()
        {
            _self.$input_text_page.addClass('fn-hide');
            _self.$input_textarea_page.addClass('fn-hide');
            _self.$input_location_page.addClass('fn-hide');
            _self.$edit_page.removeClass('fn-hide');
        }
    };


    var edit_page_class_obj = new edit_page_class();

});
