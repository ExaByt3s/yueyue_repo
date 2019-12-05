define('list/index', function(require, exports, module){ /**
 * 列表页面
 * 2015.5.11
 **/
"use strict";

var utility = require('common/utility/index');
var $ = require('components/zepto/zepto.js');
var fastclick = require('components/fastclick/fastclick.js');
var yue_ui = require('yue_ui/frozen');
var pager = require('common/pager/index');

if ('addEventListener' in document) {
    document.addEventListener('DOMContentLoaded', function() {
        fastclick.attach(document.body);
    }, false);
}

(function($,window)
{

    var _self = {};

    // 初始化dom
    _self.$page_container = $('[data-role="page-container"]');
    _self.searchbar = $('.ui-searchbar');
    _self.search_btn = $('[data-role="search-btn"]');
    _self.searchbar_cancel = $('.ui-searchbar-cancel');
    _self.searchbar_close  = $('.ui-icon-close');
    _self.$active_and_disable_btn = $('[data-card-no]');

    /************ 处理搜索框的交互 ************/
    _self.searchbar.tap(function()
    {
        $('.ui-searchbar-wrap').addClass('focus');
        $('.ui-searchbar-input input').focus();
    });

    $('.ui-searchbar-input input').on('change',function()
    {
        if($('.ui-searchbar-input input').val() !="")
        {
            $('#search-form').submit();
        }
    });



    _self.searchbar_cancel.tap(function(){
        $('.ui-searchbar-wrap').removeClass('focus');
    });

    _self.searchbar_close.tap(function()
    {
        $('.ui-searchbar-input input').val('');
    });

    _self.search_btn.tap(function()
    {
        if(!$('.ui-searchbar-input input').val())
        {
            return;
        }

        $('#search-form').submit();
    });
    /************ 处理搜索框的交互 ************/

    // 分页处理
    var pager = window._Pager;
    var pager_obj = new pager($('[data-role="pager-container"]'),{
        total_page : window.__page_config.total_page,
        pre : window.__page_config.pre,
        next : window.__page_config.next,
        cur_page : window.__page_config.cur_page
    });

    // 激活和作废事件
    _self.$active_and_disable_btn.tap(function(ev)
    {
        var $cur_btn = $(ev.currentTarget);

        var card_no = $cur_btn.attr('data-card-no');

        var type = $cur_btn.attr('data-role');
        var $loading = {};

        if(type == 'active')
        {
            if(!confirm('确定激活？'))
            {
                return;
            }
        }

        if(type == 'disable')
        {
            if(!confirm('确定作废？'))
            {
                return;
            }
        }

        utility.ajax_request
        ({
            url : window.$__config.ajax_url.active_and_disable,
            data :
            {
                card_no : card_no,
                type : type
            },
            beforeSend : function()
            {
                $loading=$.loading
                ({
                    content:'发送中...'
                })
            },
            success : function(res)
            {

                $loading.loading("hide");

                var res = res.result_data;

                if(res.code == '-1')
                {
                    $.tips
                    ({
                        content : res.message,
                        stayTime:3000,
                        type:"warn"
                    });

                    window.location.href = 'login.php';

                    return;
                }

                if(res.code>0)
                {
                    $.tips
                    ({
                        content : res.message,
                        stayTime:3000,
                        type:"success"
                    });

                    window.location.href = window.location.href;
                }
                else
                {
                    $.tips
                    ({
                        content : res.message,
                        stayTime:3000,
                        type:"warn"
                    });
                }

            },
            error : function()
            {
                $loading.loading("hide");

                $.tips
                ({
                    content : '网络异常',
                    stayTime:3000,
                    type:"warn"
                });
            }
        });

        /*var dialog_tpl = __inline('./dialog.tmpl');

        if(card_no)
        {
            card_no = card_no.replace(/(.{4})/,'$1 ');
        }

        var content = dialog_tpl
        ({
            card_no : card_no
        });

        var dia=$.dialog({
            title:'',
            content:content,
            button:["取消","确认"]
        });

        // 确认按钮
        dia.on("dialog:action",function(e)
        {
            console.log(e.index)
        });

        // 取消按钮
        dia.on("dialog:hide",function(e)
        {
            console.log("dialog hide")
        });*/
    });


})($,window);



 
});