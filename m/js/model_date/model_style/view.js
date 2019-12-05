/**
 * 我要约拍
 * nolestLam 2014.8.
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var model_style = require('../model_style/tpl/main.handlebars');
    var Scroll = require('../../common/scroll');
    var choosen_view = require('./choosen_view');
    var utility = require('../../common/utility');
    var m_alert = require('../../ui/m_alert/view');
    var model_card_model = require('../model_card/model');

    var model_style_view = View.extend
    ({

        attrs:
        {
            template: model_style
        },
        events :
        {
            'tap [data-role="page-back"]' : function()
            {
                var self = this;

                page_control.back();
            },
            'swiperight' : function()
            {
                page_control.back();
            },
            'tap [data-role=choosen-style-tap]' : function(ev)
            {
                var self = this;

                if(utility.login_id == self.get('user_id'))
                {
                    return;
                }


                var $choosen_btn = $(ev.currentTarget);

                var id = $choosen_btn.attr('data-id');
                var style = $choosen_btn.attr('data-style');
                var price = $choosen_btn.attr('data-price');
                var params = $choosen_btn.attr('data-params');

                var data = self._search_data(style);

                // 用于打折价格，特殊处理
                var is_discount = params == 'discount'?1:0;

                console.log(data)
                self.model.set('model_selected_info',data);
                //{style : style,price : price ,is_discount : is_discount},
                self.model_card_model_obj.judge_can_date({ model_user_id : self.model.get('user_id')})

            }
        },
        _search_data : function(style)
        {
            var self = this;

            var data;

            $.each(self.cache_data.model_style_v2,function(i,obj)
            {
                if(obj.text == style)
                {
                    data =  obj;

                    return false;
                }
            });

            return data;
        },
        _setup_events : function()
        {
            var self = this;

            self.model
                .on('before:fetch',function()
                {
                    m_alert.show('加载中...','loading',{delay:-1});
                })
                .on('success:fetch',function(response)
                {
                    m_alert.hide();

                    self._fetch_render(self.preview_data);

                }).on('error:fetch',function()
                {
                    m_alert.show('网络异常','error');
                });


            // 判断是否约拍
            self.model_card_model_obj.on('before:judge_can_date',function()
            {
                m_alert.show('加载中...','loading',{delay:-1});


            })
            .on('success:judge_can_date',function(response,options)
            {
                var response = response.result_data;

                var cache = self.model.toJSON();

                //设置缓存 用于其他页面跳转表单页获取数据 2014-12-4
                utility.storage.set("submit_cache_" + utility.user.get("user_id"),cache);

                var location = window.location;

                // 修改成直接去到表单页面
                // modify by hudw 2014.12.9
                var url = encodeURIComponent(location.origin+"/mobile/"+window._page_mode+"#model_date/submit_application");

                if(response.code !=1)
                {
                    m_alert.show(response.msg,'error',{delay:3000});

                    switch (utility.int(response.model_level_require))
                    {
                        case 2:

                            page_control.navigate_to_page('mine/authentication/v2/from_date_'+ url);

                            break;
                        case 3:

                            page_control.navigate_to_page('mine/authentication/v3/from_date_'+ url);

                            break;
                    }

                    return;
                }
                m_alert.hide();

                page_control.navigate_to_page('model_date/submit_application',self.model);

            })
            .on('error:judge_can_date',function()
            {
                m_alert.show('网络异常','error');
            });

            self.on('update_list',function()
            {
                if(!self.view_scroll_obj)
                {
                    self._setup_scroll();

                    self.view_scroll_obj.refresh();

                    return;
                }

                self.view_scroll_obj.refresh();
            });
        },
        _fetch_render : function(data)
        {
            var self = this;

            var render_data = self.model.toJSON();
            //预览的情况
            data && (render_data = data);

            self.cache_data = render_data;

            self.$choosen_contenter.html('');

            //循环生成按钮
            $.each(render_data.model_style_v2,function(i,item)
            {
                data && (render_data.model_style_v2[i].is_preview = true);//用于预览
                var choosen_btn = new choosen_view
                ({
                    templateModel : render_data.model_style_v2[i],
                    parentNode : self.$choosen_contenter
                }).render();
            });

            self.trigger('update_list');

            self._drop_reset();
        },
        _setup_scroll : function()
        {
            var self = this;
            var view_scroll_obj = Scroll(self.$container,
                {
                    lazyLoad : true,
                    is_hide_dropdown : false
                });
            self.view_scroll_obj = view_scroll_obj;

            // 下拉完成
            self.view_scroll_obj.on('dropload',function(e)
            {
                self.model.get_info();

            });
        },
        _drop_reset : function()
        {
            var self = this;

            self.view_scroll_obj && self.view_scroll_obj.resetload();
        },
        setup : function()
        {
            var self = this;

            self.model_card_model_obj = new model_card_model();

            self.$container = self.$('[data-role=container]');

            self.$container_img_list = self.$('[data-role=container-img-list]');

            self.$choosen_contenter = self.$('[data-role="choosen-contenter"]');

            //self.$commit_fee = self.$('[data-role="commit_fee"]');

            self._setup_events();



            // 如果model是传递过来的，执行_fetch_render，否则请求获取新的model
            if(!self.get('is_new_fetch'))
            {
                // 大视图渲染后执行渲染列表内容
                self.once('render',function()
                {
                    self._fetch_render();
                });
            }
            else
            {
                self.model.get_info();
            }
        },
        set_preview_data : function(data){
            var self = this;
            self.preview_data = data || null;
            return self;
        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        }
    });

    module.exports = model_style_view;
});