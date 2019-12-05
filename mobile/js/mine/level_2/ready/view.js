/**
 * Created by nolestLam on 2015/3/31.
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var View = require('../../../common/view');
    var utility = require('../../../common/utility');
    var page_control = require('../../../frame/page_control');
    var level_2_ready_tpl = require('./tpl/main.handlebars');
    var m_alert = require('../../../ui/m_alert/view');
    var coupon_per = require('../../../widget/coupon/view');
    var Scroll = require('../../../common/scroll');
    var abnormal = require('../../../widget/abnormal/view');
    var global_config = require('../../../common/global_config');
    var app = require('../../../common/I_APP');


    var level_2_ready_view = View.extend
    ({
        attrs:
        {
            template: level_2_ready_tpl,
            coupon_map : {}
        },
        events :
        {
            'tap [data-role="page-back"]' : function()
            {
                var self = this;

                page_control.back();
            },
            'tap [data-role="re_take_photo"]' : function()
            {
                var self = this;

                page_control.back();
            },
            'tap [data-role="submit"]' : function()
            {
                var self = this;

                console.log("提交审核");

                if(window.confirm("确认提交？"))
                {
                    if(self.$('[data-role="id"]').val().trim() == "" || self.$('[data-role="name"]').val().trim() == "")
                    {
                        m_alert.show('请正确填写个人信息','error');
                    }
                    else
                    {
                        self.model.add_authentication_pic({img : self.upload_img,id_code:self.$('[data-role="id"]').val(),name:self.$('[data-role="name"]').val()})
                    }
                }
            }
        },
        _get_img_w_h : function(){
            var self = this;
            var w_h ={};
            w_h.width = utility.get_view_port_width() - 30;
            w_h.height = 580 / 400 * w_h.width;
            return w_h;
        },
        _render_items : function(data)
        {
            var self = this;

            self.view_scroll_obj.refresh();
        },
        _setup_scroll : function()
        {
            var self = this;

            var view_scroll_obj = Scroll(self.$scroll_container,{
                lazyLoad : false,
                _startY : 45,
                prevent_tag : 'slider',
                is_hide_dropdown : true,
                down_direction : 'down',
                down_direction_distance :50
            });

            self.view_scroll_obj = view_scroll_obj;

            // 上拉刷新
            self.view_scroll_obj.on('dropload',function(e)
            {
                self.refresh();

                console.log("刷新");

            });

            // 下拉加载更多
            self.view_scroll_obj.on('pullload',function(e)
            {

            });
        },
        refresh : function()
        {
            var self = this;

            //self.model.get_coupon_list(self.submit_data);
        },
        _setup_events : function()
        {
            var self = this;

            self.on('render',function()
            {
                //self.model.get_coupon_list(self.submit_data);

                self._setup_scroll();
            });
            self.model
                .on('before:get_coupon_list:fetch',function(response,options)
                {

                })
                .on('success:get_coupon_list:fetch',function(response,options)
                {

                })
                .on('error:get_coupon_list:fetch',function(response,options)
                {
                    m_alert.show('请求失败，请重试','error');
                })
                .on('complete:get_coupon_list:fetch',function(response,options)
                {

                    m_alert.hide();

                    self._drop_reset();
                })
                .on('before:authentication_pic:fetch',function(response,options)
                {

                })
                .on('success:authentication_pic:fetch',function(response,options)
                {
                    console.log(response);

                    m_alert.show(response.result_data.msg,'error');

                    page_control.navigate_to_page("mine/level_2/status");
                })
                .on('error:authentication_pic:fetch',function(response,options)
                {
                    m_alert.show('请求失败，请重试','error');
                })
                .on('complete:authentication_pic:fetch',function(response,options)
                {

                    self._drop_reset();
                })

            //
        },
        setup : function()
        {
            var self = this;

            self.$scroll_container = self.$('[data-role="content-body"]');

            self.$container = self.$('[data-role="list-container"]');

            self.fetching = false;

            self.submit_data =
            {
                page : 1
            };

            //测试img值，需要删除
            var img = 'https://ss0.baidu.com/73F1bjeh1BF3odCf/it/u=3873076565,1085937513&fm=96&s=A9987B954A207417DA28C06A0300F031';

            self.upload_img = self.get("img").img;

            self.$('[data-role="images"]').attr("src",self.upload_img);

            self._setup_events();
        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        _drop_reset : function()
        {
            var self = this;

            self.view_scroll_obj && self.view_scroll_obj.resetload();
        },
        show : function()
        {
            var self = this;

            self.$el.removeClass('fn-hide');
        },
        hide : function()
        {
            var self = this;

            self.$el.addClass('fn-hide');
        }
    });

    module.exports = level_2_ready_view;
});