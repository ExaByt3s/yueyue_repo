/**
 * Created by nolest on 2014/9/1.
 *
 *
 *
 *  活动详细页视图
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var tpl = require('./tpl/main.handlebars');
    var utility = require('../../common/utility');
    var m_alert = require('../../ui/m_alert/view');
    var mine_popup = require('../../ui/mine-popup/index');
    var introView = require('./introView');
    var infoView = require('./infoView');
    var arrangeView = require('./arrangeView');
    var App = require('../../common/I_APP');
    var global_config = require('../../common/global_config');


    var model_card_view = View.extend
    ({

        attrs:
        {
            template: tpl
        },
        events :
        {
            
            'swiperight' : function ()
            {
                page_control.back();
            },
            'tap [data-role="choosen-container"] i' : function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var title = $cur_btn.attr('data-title');

                self.$('[data-role="choosen-container"] i').removeClass('cur');

                self.hide_all_container();

                self.show_container_by_name(title);
            },
            'tap [data-role="btn-next"]' : function()
            {
                var self = this;

                self.model.submit_obj(self.submit_obj);

                m_alert.show('提交中...','loading');
            },
            'tap [data-role="check_list"]' : function()
            {
                var self = this;

                page_control.navigate_to_page("act/signin/"+self.model.get("event_id"),
                {
                    model : self.model
                });
                //console.log(self.model.get("event_id")); 查看列表操作
            },
            'tap [data-role="btn_success"]' : function()
            {
                page_control.navigate_to_page("hot")
            },
            'tap [data-role="item_more"]' : function()
            {
                // 弹出层设置

                var self = this;

                if(App.isPaiApp)
                {
                    App.share_card(self.share_data,
                        function(data)
                        {
                            console.log(data);

                        }
                    )
                }

            },
            'tap [data-role="page-back"]' :function()
            {
                var self = this;

                var event_id = utility.int(self.model.get('event_id'));

                if(event_id)
                {
                    page_control.back();
                }
                else
                {
                    // 活动发布后的返回
                    page_control.navigate_to_page('mine');
                }


            },
            'tap [data-role="join-in"]' :function()
            {
                var self = this;

                if(!utility.auth.is_login())
                {
                    if(App.isPaiApp)
                    {
                        App.openloginpage(function(data)
                        {
                            if(data.code == '0000')
                            {
                                utility.refresh_page();
                            }
                        });
                    }
                    else
                    {
                        page_control.navigate_to_page('account/login');
                    }



                    return;
                }

                page_control.navigate_to_page("act/apply/"+self.model.get("event_id"), self.model);
            },
            'tap [data-role="succeed-notice-delete"]' :function()
            {
                var self = this;

                self.success_notice.addClass('fn-hide');

                //重置三个view的高度
                self._reset_views_height(self.reset_viewport_height());
            },
            'tap [data-role="follow"]' : function(ev)
            {
                var self = this;

                self.$('[data-role="follow"]').toggleClass("followed")

                var a = self.$('[data-role="follow"]').hasClass("followed");

                if(a)
                {
                    a = 1;
                    self.model.set("is_follow",true);
                }
                else
                {
                    a = 0;
                    self.model.set("is_follow",false);
                }
                console.log(a);
                self.model.relation(a);



            },
            'tap [data-role="notice-exp"]' : function()
            {
                var self = this;

                if(!App.isPaiApp)
                {
                    console.warn('no App');

                    return;
                }

                if(!utility.auth.is_login())
                {
                    if(App.isPaiApp)
                    {
                        App.openloginpage(function(data)
                        {
                            if(data.code == '0000')
                            {
                                utility.refresh_page();
                            }
                        });
                    }
                    else
                    {
                        page_control.navigate_to_page('account/login');
                    }

                    return;
                }

                App.chat(self.chat_obj);
            },
            'tap [data-role="go-comment"]' : function()
            {
                var self = this;

                page_control.navigate_to_page('comment/list/event/'+self.model.get("event_id")+'/'+self.model.get('act_info').event_organizers);
            },
            'tap [data-role="img-tap"]':function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var $total_alumn_img = self.$('[data-role="img-tap"]');

                var total_alumn_img_arr = [];

                $total_alumn_img.each(function(i,obj)
                {
                    total_alumn_img_arr.push
                    ({
                        url : $(obj).attr('data-url'),
                        text : ''
                    });
                });

                // 当前图片索引
                var index = $total_alumn_img.index($cur_btn);

                var data =
                {
                    img_arr : total_alumn_img_arr,
                    index : index
                };

                if(!App.isPaiApp)
                {
                    console.log('no App');

                    return;
                }

                console.log(data);
                
                App.show_alumn_imgs(data);


            }
        },
        /**
         * 安装事件
         * @private
         */
        _setup_events : function()
        {
            var self = this;

            self.listenTo(self.model,'change',self._render_follow);
            self.model

                .on("before:fetch",function(response,options)
                {
                    //m_alert.show('加载中...','loading');
                })
                .on("success:fetch",function(response,options)
                {
                    m_alert.hide();

                    self.model.set("is_follow",response.result_data.data.is_follow);

                    //自己发布的活动隐藏报名按钮
                    if(response.result_data.data.pub_user_id == utility.user.get('user_id'))
                    {
                        self._is_self = true;

                        self.$('[data-role="apply-footer"]').addClass('fn-hide');
                    }

                    //参加了的活动隐藏报名按钮
                    if(self.get('templateModel').form_info_title || response.result_data.data.act_info.event_status == 2)
                    {
                        self.$('[data-role="apply-footer"]').addClass('fn-hide');
                    }

                    //防止其他页面操作此model重复渲染
                    if(!self.intro_view)
                    {
                        self._setup_content(response,options);
                    }


                })
                .on('success:submit',function(response,options)
                {
                    if(response.result_data.code > 1)
                    {
                        m_alert.show(response.result_data.msg,'right',{delay:1000});

                        self.$bottom_btn_container.addClass('fn-hide');

                        self.success_btn.removeClass('fn-hide');

                        self.success_notice.removeClass('fn-hide');

                        self._reset_views_height(self.reset_succeed_viewport_height());

                    }
                })
                .on('error:submit',function(response,options)
                {
                    m_alert.show("网络异常",'error');

                })
                .on('complete:submit',function(response,options)
                {
                    //m_alert.hide();
                });

            self
                .on('update_content',function(pub_user_id)
                {
                    self.$bottom_btn_container.removeClass('fn-hide');
                    // 特殊处理，因为导入摄影内容导致高度变化不确定 hudw 2014.9.20
                    setTimeout(function()
                    {
                        self.intro_view && self.intro_view.view_scroll_obj.refresh();
                    },1000);
                })

        },
        _parseObj : function(obj)
        {
            var self = this;

            var fake_response =
            {
                result_data :
                {
                    data :
                    {
                        act_intro : obj,
                        act_info : obj,
                        act_arrange : obj
                    }

                }
            };

            self.submit_obj = obj;

            self._setup_content(fake_response);

        },
        _render_follow : function()
        {
            var self = this;

            var a = self.model.get("is_follow");
            //var a = self.$('[data-role="follow"]').hasClass("followed");

            if(a)
            {
                self.$('[data-role="follow"]').addClass("followed")
            }
            else
            {
                self.$('[data-role="follow"]').removeClass("followed")
            }




        },
        /**
         * 安装内容
         * @private
         */
        _setup_content : function(response,options)
        {
            var self = this;

            //console.log(self.model.toJSON(),response.result_data);

            var intro_res = response.result_data.data.act_intro;

            var info_res = response.result_data.data.act_info;

            var arrange_res = response.result_data.data.act_arrange;

            var viewport_height;

            self.chat_obj =
            {
                senderid : utility.login_id,
                receiverid : utility.int( response.result_data.data.act_info.event_organizers),
                sendername : utility.user.get('nickname'),
                receivername : response.result_data.data.act_info.nickname,
                sendericon : utility.user.get('user_icon'),
                receivericon : response.result_data.data.act_info.user_icon
            };

            if(self._is_self || response.result_data.data.act_info.event_status == 0 || self.get('templateModel').form_info_title || response.result_data.data.act_info.event_status == 2)
            {
                viewport_height = self.reset_viewport_height() + 50
            }
            else
            {
                viewport_height = self.reset_viewport_height()
            }
            console.log(viewport_height)

            var intro_view = new introView
            ({
                parentNode: self.$select_container,
                templateModel : intro_res,
                viewport_height : viewport_height
            }).render();

            var info_view = new infoView
            ({
                parentNode: self.$select_container,
                templateModel : info_res,
                viewport_height : viewport_height
            }).render();

            var arrange_view = new arrangeView
            ({
                parentNode: self.$select_container,
                templateModel : arrange_res,
                viewport_height : viewport_height
            }).render();

            self.intro_view = intro_view;

            self.info_view = info_view;

            self.arrange_view = arrange_view;




            if(!(response.result_data.data.pub_user_id == utility.user.id) && response.result_data.data.act_info.event_status == 0)
            {
                self.$('[data-role="join-in"]').removeClass("fn-hide");

                self._reset_views_height(self.reset_viewport_height());
            }
            if(self.get('templateModel').form_info_title)
            {
                self._reset_views_height(self.reset_viewport_height() + 50);
            }
            // 此处应可以通过路由参数设置显示对应的项目
            self.show_container_by_name(self.get('type'));

            self.trigger('update_content',response.result_data.data.pub_user_id);

            $_AppCallJSObj.off('onShare');
            $_AppCallJSObj.on('onShare',function(event,data)
            {

                console.log('share success')
                console.log(JSON.stringify(data));

                // 用户确认分享后执行的回调函数
                utility.ajax_request
                ({
                    url : global_config.ajax_url.send_share_coupon,
                    type : 'POST',
                    data :
                    {
                        event_id : self.model.get("event_id"),
                        url : share.url
                    }
                });
            });


            if(response.result_data.data.share_text)
            {

                var share = response.result_data.data.share_text;

                console.log(share);
                self.share_data =
                {
                    url : share.url,
                    pic : share.img,
                    userid : share.user_id,
                    title: share.title,
                    content: share.content,
                    sinacontent : share.sina_content,
                    qrcodeurl: share.qrcodeurl,
                    jscbfunc : 'onShare',
                    sourceid : global_config.analysis_page['act_details'].pid

                };

                console.log(self.share_data)
            }

            //hudw 2015.1.3
            //内容加载后滚动条置顶
            self.intro_view.view_scroll_obj && self.intro_view.view_scroll_obj.reset_top();

        },
        setup : function()
        {
            var self = this;
            // 配置交互对象
            self.$container = self.$('[data-role="pub-preview-content"]'); // 滚动容器

            self.$select_container = self.$('[data-role="select-con"]');

            self.$bottom_btn_container = self.$('[data-role="bottom-btn-container"]');

            self.success_btn = self.$('[data-role="btn_success"]');

            self.success_notice = self.$('[data-role="success_notice"]');

            // 安装事件
            self._setup_events();

            self.submit_obj = null;

            if(self.get('templateModel').form_info_title)
            {
                self.$('[data-role="apply-footer"]').addClass('fn-hide');
            }
            if(self.get('templateModel').is_preview)
            {

                self._parseObj(self.get("preview_obj"));

                self.$('[data-role="preview-hide"]').hide();
            }
            else
            {

                self.model.get_list();
            }

        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        /**
         * 隐藏所有子容器
         */
        hide_all_container : function()
        {
            var self = this;

            self.$('[data-role="act-detail-container"]').addClass("fn-hide");
        },
        /**
         * 根据名称显示容器
         * @param name
         */
        show_container_by_name : function(name)
        {
            var self = this;

            switch (name)
            {
                case 'intro' :
                    self.intro_view && self.intro_view.show();
                    break;
                case 'info'  :
                    self.info_view && self.info_view.show();
                    break;
                case 'arrange' :
                    self.arrange_view && self.arrange_view.show();
                    break;
            }

            self.$('[data-role="act-'+name+'"]').addClass('cur');

        },
        reset_viewport_height : function()
        {
            return utility.get_view_port_height('nav') -41-50; // 减去头部选项高度和底部按钮高度
        },
        reset_succeed_viewport_height : function()
        {
            return utility.get_view_port_height('nav') -41-50-49;
        },
        _reset_views_height : function(height)
        {
            var self = this;
            //重置三个view的高度
            self.intro_view.$container.height(height);

            self.intro_view.view_scroll_obj.refresh();


            self.info_view.$container.height(height);

            self.info_view.view_scroll_obj.refresh();

            self.arrange_view.$container.height(height);

            self.arrange_view.view_scroll_obj.refresh();
        }

    });

    module.exports = model_card_view;
});
