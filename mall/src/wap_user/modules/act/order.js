define('act/order', function(require, exports, module){ /**
 * Created by hudingwen on 15/7/20.
 */
"use strict";


    var utility = require('common/utility/index');
    var $ = require('components/zepto/zepto.js');
    var fastclick = require('components/fastclick/fastclick.js');
    var yue_ui = require('yue_ui/frozen');
    var App =  require('common/I_APP/I_APP');
    var menu = require('menu/index');
    var header = require('common/widget/header/main');

    if ('addEventListener' in document)
    {
        document.addEventListener('DOMContentLoaded', function ()
        {
            fastclick.attach(document.body);
        }, false);
    }

    exports.init = function(page_data)
    {
        (function($,window)
        {
            var _self = $({});

            _self.$group_item = $('[data-role="table-items"]').find('button');
            _self.$go_pay_btn = $('[data-role="go-pay-btn"]');
            _self.enroll_id = 0;
            _self.people = people;

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

            var w_height = window.innerHeight;
            window.onresize = function()
            {
                if(window.innerHeight < w_height)
                {
                    $('footer').addClass('fn-hide');
                    return false;
                }
                else
                {
                    $('footer').removeClass('fn-hide');
                    return false;
                }
            };

            // ѡ�񳡴�
            _self.$group_item.on('click',function(ev)
            {
                var $cur_btn = $(ev.currentTarget);

                if($cur_btn.attr('data-jump-to-pay') == 1)
                {
                    _self.table_id = $cur_btn.find('[data-role="table_id"]').val();
                    _self.enroll_id = $cur_btn.find('[data-role="enroll_id"]').val();
                    _self.tel = '';

                    var enroll_num = '';
                    var event_id = $('#event_id').val();

                    window.location.href = './pay.php?enroll_id='+_self.enroll_id+'&tel='+_self.tel+'&enroll_num='+enroll_num+'&event_id='+event_id+'&table_id='+_self.table_id;

                    return;
                }

                _self.$group_item.addClass('ui-button-bg-fff').removeClass('ui-button-bg-ff6');

                if($cur_btn.hasClass('ui-button-bg-fff'))
                {
                    $cur_btn.removeClass('ui-button-bg-fff').addClass('ui-button-bg-ff6');
                }
                else
                {
                    $cur_btn.addClass('ui-button-bg-fff').removeClass('ui-button-bg-ff6');
                }



            });

            // ȥ֧��

            _self.$go_pay_btn.on('click',function()
            {
                var $select_btn = $('[data-role="table-items"]').find('.ui-button-bg-ff6');

                _self.table_id = $select_btn.find('[data-role="table_id"]').val();
                _self.enroll_id = $select_btn.find('[data-role="enroll_id"]').val();
                _self.tel = $('#tel').val();

                var enroll_num = _self.people.get_val();
                var event_id = $('#event_id').val();

                var error_tips = '';

                if(!_self.table_id)
                {
                    error_tips = '��ѡ�񳡴�';
                }

                if(!_self.tel)
                {
                    error_tips = '�������ֻ�����';
                }

                if(!enroll_num)
                {
                    error_tips = '��ѡ������';
                }

                if(error_tips)
                {
                    $.tips
                    ({
                        content:error_tips,
                        stayTime:3000,
                        type:'warn'
                    });
                    return;
                }

                $.tips
                ({
                    content:'������ת֧��',
                    stayTime:3000,
                    type:'success'
                });

                window.location.href = './pay.php?enroll_id='+_self.enroll_id+'&tel='+_self.tel+'&enroll_num='+enroll_num+'&event_id='+event_id+'&table_id='+_self.table_id;


            });


        })($,window);
    };


});