/**
 * Created by hudingwen on 15/7/13.
 */
"use strict";


var $ = require('zepto');
var fastclick = require('fastclick');
var yue_ui = require('../yue_ui/frozen');
var App =  require('../common/I_APP/I_APP');
var utility = require('../common/utility/index');
var header = require('../common/widget/header/main');

(function($,window)
{
    var $star = $('[data-star-list]').find('i');
    var $textarea = $('[data-role="textarea"]');
    var $btn_submit = $('.ui-button-submit');
    var $niming_btn = $('[data-role="data-name"]');

    var self = $({});

    App.isPaiApp && App.showtopmenu(false);

    // 渲染头部
    header.init({
        ele : $("#global-header"), //头部渲染的节点
        title:"评价",
        header_show : true , //是否显示头部
        right_icon_show : false, //是否显示右边的按钮

        share_icon :
        {
            show :false,  //是否显示分享按钮icon
            content:""
        },
        omit_icon :
        {
            show :false,  //是否显示三个圆点icon
            content:""
        },
        show_txt :
        {
            show :false,  //是否显示文字
            content:"编辑"  //显示文字内容
        }
    });

    // 星星点击
    $star.on('click',function(ev)
    {
        var $cur_btn = $(ev.currentTarget);

        var $star_list = $cur_btn.parent().find('i');

        var index = $star_list.index($cur_btn);

        // 重置星星
        $star_list.removeClass('icon-star-active');

        for(var i = 0;i<(index+1);i++)
        {
            $star_list.eq(i).addClass('icon-star-active');
        }


    });



    // 提交按钮
    $btn_submit.on('click',function()
    {


        var overall_score = $('[data-role="overall_score"]').find('.icon-star-active').length;
        var match_score = $('[data-role="match_score"]').find('.icon-star-active').length;
        var manner_score = $('[data-role="manner_score"]').find('.icon-star-active').length;
        var quality_score = $('[data-role="quality_score"]').find('.icon-star-active').length;

        var comment = $textarea.val();
        var is_anonymous = $('[data-role="niming"]').attr('checked')?1:0;

        var error_msg = '';
        var order_sn = $('#order_sn').val() || 0;
        var table_id = $('#table_id').val() || 0;

        if(order_sn)
        {
            if(!overall_score)
            {
                error_msg = '总体评价还没有选哦';
            }
            else if(!match_score)
            {
                error_msg = '描述相符还没有选哦';
            }
            else if(!manner_score)
            {
                error_msg = '服务态度还没有选哦';
            }
            else if(!quality_score)
            {
                error_msg = '服务质量还没有选哦';

            }
        }
        else if(table_id)
        {
            if(!overall_score)
            {
                error_msg = '总体评价还没有选哦';
            }
            else if(!match_score)
            {
                error_msg = '组织能力还没有选哦';
            }
            else if(!quality_score)
            {
                error_msg = '模特水平还没有选哦';
            }
        }

        if(!comment)
        {
            error_msg = '好评内容不能为空哦';

        }

        if(error_msg)
        {
            $.tips
            ({
                content:error_msg,
                stayTime:3000,
                type:'warn'
            });
            return;
        }

        self.$loading = {};

        utility.ajax_request
        ({
            url : window.$__config.ajax_url.submit_comment,
            type : 'POST',
            data :
            {
                overall_score : overall_score,
                match_score : match_score,
                manner_score : manner_score,
                quality_score : quality_score,
                order_sn : order_sn,
                table_id : table_id,
                comment : comment,
                is_anonymous:is_anonymous,
                redirect_url : window.__redirect_url

            },
            cache : false,
            beforeSend : function()
            {
                self.$loading=$.loading
                ({
                    content:'发送中...'
                });
            },
            success : function(res)
            {
                self.$loading.loading("hide");

                if(res.result_data.code == 1)
                {
                    var type = 'success';

                    $.tips
                    ({
                        content:res.result_data.msg,
                        stayTime:3000,
                        type:type
                    });

                    if(res.result_data.data.url)
                    {
                        window.location.href = res.result_data.data.url;
                    }
                    else
                    {
                        if(App.isPaiApp)
                        {
                            App.switchtopage({page:'mine'});
                        }
                        else
                        {
                            window.location.href = window.__index_url_link;
                        }
                    }

                }
                else
                {
                    var type = 'warn';

                    $.tips
                    ({
                        content:res.result_data.msg,
                        stayTime:3000,
                        type:type
                    });
                }
            },
            error : function()
            {
                self.$loading.loading("hide");

                $.tips
                ({
                    content:'网络异常',
                    stayTime:3000,
                    type:'warn'
                });
            }
        });

    });


})($,window);