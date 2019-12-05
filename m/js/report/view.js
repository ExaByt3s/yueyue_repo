/**
 * Created by 汤圆 2014.11.19
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../frame/page_control');
    var View = require('../common/view');
    var refuse_tpl = require('./tpl/main.handlebars');
    var utility = require('../common/utility');
    var Scroll = require('../common/scroll');
    var choosen_group_view = require('../widget/choosen_group/view');
    var m_alert = require('../ui/m_alert/view');
    var item_tpl = require('./tpl/item.handlebars');
    var receive_view = View.extend(
    {
        attrs:
        {
            template: refuse_tpl
        },
        events:
        {
            'swiperight' : function (){
                page_control.back();
            },  //手机手势返回        
            'tap [data-role=page-back]': function(ev)
            {
                page_control.back();
            },

            'tap [data-role=item]': function(ev)
            {
                var self = this;
                var $cur_btn = $(ev.currentTarget);
                $cur_btn.addClass('cur').siblings().removeClass('cur');
                self.sec_data_txt = $cur_btn.find('.txt').html();
            },

            'tap [data-role="btn"]': function(ev)
            {
                var self = this;
                var detail = self.$('[data-role="cancel_remark"]').val().trim();

                if(!utility.login_id)
                {
                    m_alert.show('尚未登录','error',{delay:1000});

                    page_control.navigate_to_page('account/login');

                    return;
                }

                if (!self.sec_data_txt && detail=='')
                {
                    m_alert.show('请完善投诉信息', 'error');
                    return;
                }
                var send_data_txt = encodeURIComponent(self.sec_data_txt)+"\n"+encodeURIComponent(detail);

                // 收集数据 
                var data = 
                {
                    data:send_data_txt,
                    by_informer : self.get('report_id')
                }
                self.model.send_report(data)
            }
        },
        _setup_events: function()
        {
            var self = this;

            self.model
                .on('before:fetch',function(response,options)
                {
                    m_alert.show('加载中...','loading',{delay:1000});
                })
                .on('success:fetch',function(response,options)
                {
                    var data = response.result_data;

                    if( data.code == 1 )
                    {
                        m_alert.show('举报成功！','right',{delay:1000});
                        page_control.back();
                    }
                    else
                    {
                        m_alert.show('举报失败，请返回重试!','loading',{delay:1000});
                    }
                    
                })
                .on('error:fetch',function()
                {
                    m_alert.show('网络不给力,请返回重试！','error')
                })
                .on('complete:fetch',function(response,options)
                {
                    //m_alert.hide();
                })

        },
        _setup_scroll: function()
        {
            var self = this;
            var view_scroll_obj = Scroll(self.$container,
                {
                    is_hide_dropdown : true
                });
            self.view_scroll_obj = view_scroll_obj;
            self.view_scroll_obj.refresh();
        },

        //渲染模板
        render_item: function()
        {
            var self = this;
            var list = [
            {
                text: '垃圾营销',
                'data-id': 'item_0'
            },
            {
                text: '淫秽色情',
                'data-id': 'item_1'
            },
            {
                text: '不实内容',
                'data-id': 'item_2'
            },
            {
                text: '敏感信息',
                'data-id': 'item_3'
            },
            {
                text: '虚假中奖',
                'data-id': 'item_4'
            },
            {
                text: '抄袭内容',
                'data-id': 'item_5'
            }];
            self.item_list = item_tpl(
            {
                data: list
            });
            self.$item_list.html(self.item_list);
        },
        setup: function()
        {
            var self = this;
            self.$container = self.$('[data-role="yuepai-refuse"]');
            self.$choosen_group = self.$('[data-role="choosen_group"]');
            self.$item_list = self.$('[data-role="item-list"]');
            self._setup_events();
            self._setup_scroll();
            self.render_item()
        },
        render: function()
        {
            var self = this;
            View.prototype.render.apply(self);
            self.trigger('render');
            return self;
        },
        reset_viewport_height: function()
        {
            return utility.get_view_port_height('nav') - 24;
        }
    });
    module.exports = receive_view;
});