/**
 * Created by nolest on 2014/9/11.
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var View = require('../../../common/view');
    var refuse_tpl = require('./tpl/main.handlebars');
    var utility = require('../../../common/utility');
    var Scroll = require('../../../common/scroll');
    var choosen_group_view = require('../../../widget/choosen_group/view');
    var m_alert = require('../../../ui/m_alert/view');
    var App = require('../../../common/I_APP');

    var receive_view = View.extend
    ({
        attrs:
        {
            template: refuse_tpl
        },
        events :
        {
            'tap [data-role=page-back]' : function (ev)
            {
                page_control.back();
            },
            'tap [data-role="btn"]' :function()
            {
                var self = this;

                if(!self.choosen_style_group.get_value()[0])
                {
                    m_alert.show('请选择一个原因哦!','error',{delay:1000});
                    return ;
                }
                var data =
                {
                    user_id : utility.login_id,
                    date_id : self.get("templateModel").date_id,
                    status : 'cancel',
                    cancel_reason : self.choosen_style_group.get_value()[0].text,
                    remark : self.$('[data-role="cancel_remark"]').val()
                };

                m_alert.show('提交中','loading');

                self.model.send_request(data);
            }

        },
        _setup_events : function()
        {
            var self = this;

            self.model
                .on('success:fetch',function(response,options)
                {
                    m_alert.hide();


                    if(response.result_data.data == 1)
                    {
                        m_alert.show(response.result_data.msg,'right',{delay:1000});

                        console.log(self.from_app);

                        if(self.from_app)
                        {
                            App.app_back();
                        }
                        else
                        {
                            page_control.navigate_to_page('mine/consider/can_back_to_mine');
                        }
                    }
                })
                .on('error:fetch',function(response,options)
                {
                    m_alert.show('提交失败',{delay:1000});
                })

        },
        _setup_scroll : function()
        {
            var self = this;

            var view_scroll_obj = Scroll(self.$container,
                {
                    is_hide_dropdown : true
                });

            self.view_scroll_obj = view_scroll_obj;

            self.view_scroll_obj.refresh();

        },
        _setup_choosen_group : function()
        {
            var self = this;

            var list = [{text : '价格原因'},{text : '时间原因'},{ text : '地点原因'},{ text : '风格原因'},{ text : '摄影水平'},{ text : '信用金'}];

            self.choosen_style_group = new choosen_group_view
            ({
                templateModel :
                {
                    list : list
                },
                btn_per_line : 3, //每行按钮个数
                line_margin : '0px 0px 10px 0px', //行间距
                btn_width : '80px', //按钮宽度
                is_multiply : false, //是否多选
                parentNode: self.$choosen_group,
                css : '' //按钮额外CSS 默认为空
            }).render();


        },
        setup : function()
        {
            var self = this;

            self.$container = self.$('[data-role="yuepai-refuse"]');

            self.templateModel = self.get("templateModel");

            self.$choosen_group = self.$('[data-role="choosen_group"]');

            self.from_app = self.get("from_app");

            self._setup_events();

            self._setup_scroll();

            self._setup_choosen_group();

        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        reset_viewport_height : function()
        {
            return utility.get_view_port_height('nav') -24;
        }
    });

    module.exports = receive_view;
});