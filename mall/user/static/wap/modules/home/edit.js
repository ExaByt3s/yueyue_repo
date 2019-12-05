define('home/edit', function(require, exports, module){ 
var $ = require('components/zepto/zepto.js');
var header = require('common/widget/header/main');
var utility = require('common/utility/index');
var uploader = require('common/widget/uploader/index');
var App =  require('common/I_APP/I_APP');
var yue_ui = require('yue_ui/frozen');


//ͷ�����
$(document).ready(function() {
    header.init({
        ele        : $("#global-header"), //ͷ����Ⱦ�Ľڵ�
        title      : "�༭�ҵ�����",
        header_show: true, //�Ƿ���ʾͷ��
        mt_0_ele   : $("#seller-list-page"), //���ͷ�����أ�Ҫ�ѵ�ǰҳ�ڵ�margin-top��Ϊ0
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

    $('[data-role="right-btn"]').on('click',function() {
        var nickname = $('#name').val();
        var intro = $('#intro').val();
        var location_id = $('#location_id').val();
        var is_display_record;

        if($("#expense-calendar").attr("checked")){
            is_display_record = 1;
        }else{
            is_display_record = 0;
        }

        var showcase = {
            arr : "http://image16-d.poco.cn/yueyue/20141127/20141127202703_39_260.jpg?260x260_120"
        };

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
                nickname : nickname,
                intro : intro,
                location_id : location_id,
                is_display_record : is_display_record,
                showcase : showcase
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

                _self.$loading.loading("hide");
            },
            error  : function (err)
            {
                console.log(err);
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

    //�ǳ�
    $('[data-role="nickname"]').on('click',function(){
        var content = $(this).find('.tips-value').val();
        location.href='./input.php?input_title=%E6%98%B5%E7%A7%B0&type=text&input_content='+encodeURIComponent(content);
    });
    //���˼��
    $('[data-role="introduce"]').on('click',function(){
        var content = $(this).find('.tips-value').val();
        location.href='./input.php?limit_num=140&input_title=%E4%B8%AA%E4%BA%BA%E7%AE%80%E4%BB%8B&type=textarea&input_content='+encodeURIComponent(content);
    });
    //����
    $('[data-role="city"]').on('click',function(){
        var content = $(this).find('.tips-value').val();
        location.href='./input.php?input_title=%E9%80%89%E6%8B%A9%E5%9F%8E%E5%B8%82&type=city&input_content='+content;
    });

    var edit_pic_upload = function()
    {
        var self = this;
        self.init();
    };

    var _self = $({});
    edit_pic_upload.prototype =
    {
        init: function () {
            _self.$upload_confirm = $('[data-role="upload-confirm"]');
            _self.$uploader = $('[data-role="upload-img-container"]');

            if(_self.$uploader[0])
            {
                var self = this;

                // ��ʼ���ϴ����
                _self.uploader_obj = uploader.render(_self.$uploader[0],{});

                // ��װ�¼�
                self._setup_event();
            }
        },
        _setup_event : function()
        {
            var self = this;

        }
    };
    var edit_upload_obj = new edit_pic_upload();

//input��
    var limit = '{limit_num}';

    String.prototype.len = function()
    {
        return this.replace(/[^\x00-\xff]/g, "xx").length;
    };

    function numWord(num){
        var nowLength = Math.ceil($(num).val().len()/ 2);
        //�����������ƺ���
        if(nowLength>limit){
            $('#setNums').addClass('red');
        }
        else{
            $('#setNums').removeClass('red');
        }
        $('#setNums').html(nowLength);

        //������ʾ
        var nickname = $('#nickname').val();
        var introduce = $('#introduce').val();
        if(introduce==""||nickname==""){
            $('.tips').show();
            $('.icon-delete').hide();
            return false;
        }else{
            $('.tips').hide();
            $('.icon-delete').show();
            return true;
        }
    }
    //�ǳ������������շ���
    $('.icon-delete').on('click',function(){
        $('#nickname').val("").focus();
    });
    $('[data-role="text"]').on('focus',function(){
        numWord(this);
    });
    $('[data-role="text"]').on('input',function(){
        numWord(this);
    });


//�������
        // �������
        var location_class = require('common/widget/location/location_main');

        // ʵ����
        var location_obj = new location_class({
            ele : $('#location'), //��Ⱦ�����ܽڵ�
            // ���ų�������
            hot_city : {
                is_show : true , // �Ƿ���ʾ���ų��� true��ʾ
                data :
                    [
                        {
                            city : "ï��",
                            location_id : 101029010
                        }
                    ]
            },
            callback : function(res)
            {
                // ���ѡ�г��лص�
                console.log(res);

            },
            city_history_num: "3",  //���������ʷ��¼������Ĭ��12��
            is_search : false  // �Ƿ����������й��� Ĭ�ϲ�����

        });
});
 
});