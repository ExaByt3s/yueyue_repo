define('home/edit', function(require, exports, module){ 
var $ = require('components/zepto/zepto.js');
var header = require('common/widget/header/main');
var utility = require('common/utility/index');
var uploader = require('common/widget/uploader/index');
var App =  require('common/I_APP/I_APP');
var yue_ui = require('yue_ui/frozen');


//头部插件
$(document).ready(function() {
    header.init({
        ele        : $("#global-header"), //头部渲染的节点
        title      : "编辑我的资料",
        header_show: true, //是否显示头部
        mt_0_ele   : $("#seller-list-page"), //如果头部隐藏，要把当前页节点margin-top改为0
        right_icon_show: true, //是否显示右边的按钮
        share_icon : {
            show   : false,  //是否显示分享按钮icon
            content: ""
        },
        omit_icon  : {
            show   : false,  //是否显示三个圆点icon
            content: ""
        },
        show_txt   : {
            show   : true,  //是否显示文字
            content: "保存"  //显示文字内容
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
                    content:'加载中...'
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
                    content:'网络异常',
                    stayTime:3000,
                    type:'warn'
                });
            }
        });
    });

    //昵称
    $('[data-role="nickname"]').on('click',function(){
        var content = $(this).find('.tips-value').val();
        location.href='./input.php?input_title=%E6%98%B5%E7%A7%B0&type=text&input_content='+encodeURIComponent(content);
    });
    //个人简介
    $('[data-role="introduce"]').on('click',function(){
        var content = $(this).find('.tips-value').val();
        location.href='./input.php?limit_num=140&input_title=%E4%B8%AA%E4%BA%BA%E7%AE%80%E4%BB%8B&type=textarea&input_content='+encodeURIComponent(content);
    });
    //城市
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

                // 初始化上传组件
                _self.uploader_obj = uploader.render(_self.$uploader[0],{});

                // 安装事件
                self._setup_event();
            }
        },
        _setup_event : function()
        {
            var self = this;

        }
    };
    var edit_upload_obj = new edit_pic_upload();

//input层
    var limit = '{limit_num}';

    String.prototype.len = function()
    {
        return this.replace(/[^\x00-\xff]/g, "xx").length;
    };

    function numWord(num){
        var nowLength = Math.ceil($(num).val().len()/ 2);
        //字数超出限制后变红
        if(nowLength>limit){
            $('#setNums').addClass('red');
        }
        else{
            $('#setNums').removeClass('red');
        }
        $('#setNums').html(nowLength);

        //空文提示
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
    //昵称输入框，内容清空符合
    $('.icon-delete').on('click',function(){
        $('#nickname').val("").focus();
    });
    $('[data-role="text"]').on('focus',function(){
        numWord(this);
    });
    $('[data-role="text"]').on('input',function(){
        numWord(this);
    });


//地区组件
        // 载入组件
        var location_class = require('common/widget/location/location_main');

        // 实例化
        var location_obj = new location_class({
            ele : $('#location'), //渲染地区总节点
            // 热门城市配置
            hot_city : {
                is_show : true , // 是否显示热门城市 true显示
                data :
                    [
                        {
                            city : "茂名",
                            location_id : 101029010
                        }
                    ]
            },
            callback : function(res)
            {
                // 点击选中城市回调
                console.log(res);

            },
            city_history_num: "3",  //控制浏览历史记录个数，默认12个
            is_search : false  // 是否开启搜索城市功能 默认不开启

        });
});
 
});