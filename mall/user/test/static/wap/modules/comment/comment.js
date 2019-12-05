define('comment', function(require, exports, module){ /**
 * Created by hudingwen on 15/7/13.
 */
"use strict";


var $ = require('components/zepto/zepto.js');
var fastclick = require('components/fastclick/fastclick.js');
var yue_ui = require('yue_ui/frozen');
var App =  require('common/I_APP/I_APP');
var utility = require('common/utility/index');
var header = require('common/widget/header/main');

(function($,window)
{
    var $star = $('[data-star-list]').find('i');
    var $textarea = $('[data-role="textarea"]');
    var $btn_submit = $('.ui-button-submit');
    var $niming_btn = $('[data-role="data-name"]');

    var self = $({});

    App.isPaiApp && App.showtopmenu(false);

    // ��Ⱦͷ��
    header.init({
        ele : $("#global-header"), //ͷ����Ⱦ�Ľڵ�
        title:"����",
        header_show : true , //�Ƿ���ʾͷ��
        right_icon_show : false, //�Ƿ���ʾ�ұߵİ�ť

        share_icon :
        {
            show :false,  //�Ƿ���ʾ����ťicon
            content:""
        },
        omit_icon :
        {
            show :false,  //�Ƿ���ʾ����Բ��icon
            content:""
        },
        show_txt :
        {
            show :false,  //�Ƿ���ʾ����
            content:"�༭"  //��ʾ��������
        }
    });

    // ���ǵ��
    $star.on('click',function(ev)
    {
        var $cur_btn = $(ev.currentTarget);

        var $star_list = $cur_btn.parent().find('i');

        var index = $star_list.index($cur_btn);

        // ��������
        $star_list.removeClass('icon-star-active');

        for(var i = 0;i<(index+1);i++)
        {
            $star_list.eq(i).addClass('icon-star-active');
        }


    });



    // �ύ��ť
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
                error_msg = '�������ۻ�û��ѡŶ';
            }
            else if(!match_score)
            {
                error_msg = '���������û��ѡŶ';
            }
            else if(!manner_score)
            {
                error_msg = '����̬�Ȼ�û��ѡŶ';
            }
            else if(!quality_score)
            {
                error_msg = '����������û��ѡŶ';

            }
        }
        else if(table_id)
        {
            if(!overall_score)
            {
                error_msg = '�������ۻ�û��ѡŶ';
            }
            else if(!match_score)
            {
                error_msg = '��֯������û��ѡŶ';
            }
            else if(!quality_score)
            {
                error_msg = 'ģ��ˮƽ��û��ѡŶ';
            }
        }

        if(!comment)
        {
            error_msg = '�������ݲ���Ϊ��Ŷ';

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
                    content:'������...'
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
                    content:'�����쳣',
                    stayTime:3000,
                    type:'warn'
                });
            }
        });

    });


})($,window); 
});