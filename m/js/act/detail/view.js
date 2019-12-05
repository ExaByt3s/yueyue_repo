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
    var WeiXinSDK = require('../../common/WX_JSSDK');
    var WeixinApi = require('../../common/I_WX');

    var dialog = require('../../ui/dialog/index');
    var download_tips = require('../../ui/download_tips/download_tips.handlebars');
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

                if(utility.is_other_browser_or_not_weixin_yueyue())
                {
                    self._run_jump_app();

                    return;
                }

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
            'tap [data-role="share"]' : function()
            {
                // 弹出层设置

                var self = this;

                if(!self.mine_Popup){
                    self.mine_Popup = new mine_popup({
                        uid:utility.user.get('user_id'),
                        items : {
                            edit:false,
                            report:true
                        }
                    }).show();
                }else{
                    self.mine_Popup.show();
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

                if(utility.is_other_browser_or_not_weixin_yueyue())
                {
                    self._run_jump_app();

                    return;
                }

                if(!utility.auth.is_login())
                {
                    var loc = window.location;

                    var redirect_url = loc.origin+"/m/"+window._page_mode+loc.search+"#act/detail/"+self.model.get("event_id");

                    window._force_jump_link = redirect_url;

                    page_control.navigate_to_page('account/login');

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

                if(utility.is_other_browser_or_not_weixin_yueyue())
                {
                    self.dialog_down_app_v2.show();

                    return;
                }

                if(WeixinApi.isWexXin())
                {
                    console.log("is weixin");
                    self.dialog_down_app.show();
                }
                else
                {
                    console.log("not weixin");
                    self.dialog_down_app.show();
                }


                /*
                if(!App.isPaiApp)
                {
                    console.warn('no App');

                    return;
                }


                App.chat(self.chat_obj);
                 */
            },
            'tap [data-role="go-comment"]' : function()
            {
                var self = this;

                if(utility.is_other_browser_or_not_weixin_yueyue())
                {
                    self._run_jump_app();

                    return;
                }

                page_control.navigate_to_page('comment/list/event/'+self.model.get("event_id")+'/0');
            },
            'tap [data-role="img-tap"]':function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var $total_alumn_img = self.$('[data-role="img-tap"]');

                var total_alumn_img_arr = [];

                $total_alumn_img.each(function(i,obj)
                {
                    total_alumn_img_arr.push(utility.matching_img_size($(obj).attr('data-url'),640));
                });

                // 当前图片索引
                var index = $total_alumn_img.index($cur_btn);

                var data =
                {
                    img_arr : total_alumn_img_arr,
                    index : index
                };

                console.log($cur_btn.attr('data-url'),data);

                if(WeixinApi.isWexXin())
                {

                    WeixinApi.imagePreview($cur_btn.attr('data-url'),total_alumn_img_arr);

                    return;
                }

            },
            'tap [data-role="other_browser_tips"]' : function()
            {
                var self = this;

                self._run_jump_app(true);

            }


        },
        _run_jump_app : function(tag)
        {
            var self = this;

            var no_wifi_url = encodeURIComponent('http://yp.yueus.com/mobile/app?from_app=1#act/detail/'+self.model.get('event_id'));
            var wifi_url = encodeURIComponent('http://yp-wifi.yueus.com/mobile/app?from_app=1#act/detail/'+self.model.get('event_id'));

            utility.judge_go_to_app
            ({
                url : 'yueyue://goto?type=inner_web&url='+no_wifi_url+'&wifi_url='+wifi_url,
                before_send: function()
                {
                    m_alert.show('正在进行跳转到约yue App...','loading',{delay:3000});
                },
                has_app_callback : function()
                {

                },
                has_not_app_callback : function()
                {
                    m_alert.hide();

                    if(tag)
                    {
                        self.dialog_down_app.show();
                    }
                    else
                    {
                        self.dialog_down_app_v2.show();
                    }


                }
            });
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

                    m_alert.show('加载中...','loading');
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


                    //取消了的活动隐藏报名按钮
                    if(response.result_data.data.act_info.event_status == 2/*self.get('templateModel').form_info_title || */)
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

            if(self._is_self || response.result_data.data.act_info.event_status == 0 || /*self.get('templateModel').form_info_title || */response.result_data.data.act_info.event_status == 2)
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
            /*
            if(self.get('templateModel').form_info_title)
            {
                self._reset_views_height(self.reset_viewport_height() + 50);
            }
            */
            // 此处应可以通过路由参数设置显示对应的项目
            self.show_container_by_name(self.get('type'));

            self.trigger('update_content',response.result_data.data.pub_user_id);


            if(WeixinApi.isWexXin() || WeiXinSDK.isWeiXin())
            {
                self._set_WX_share(response.result_data.data);//旧接口
            }

            //hudw 2015.1.3
            //内容加载后滚动条置顶
            self.intro_view.view_scroll_obj && self.intro_view.view_scroll_obj.reset_top();

        },
        _set_WX_share : function(data) {
            var self = this;

            console.log(data);

            if(WeiXinSDK.isWeiXin() && global_config.WeiXin_Version >= "6.0.2")//(global_config.WeiXin_Version.indexOf('6.0.2') != -1|| global_config.WeiXin_Version.indexOf('6.1') != -1)
            {

                var loc = window.location;

                self.WeiXin_data =
                {
                    title: data.share_text.title,//"模特邀约第一移动平台", // 分享标题
                    desc: data.share_text.content,//"我是约女神【" + data.user_name + "】，等你约拍哦~", // 分享描述
                    link: data.share_text.url + '&_from_source=normal', // 分享链接
                    imgUrl: data.share_text.img,//data.user_icon, // 分享图标
                    type: 'link', // 分享类型,music、video或link，不填默认为link
                    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                    success: function () {
                        // 用户确认分享后执行的回调函数
                        utility.ajax_request
                        ({
                            url : global_config.ajax_url.send_share_coupon,
                            type : 'POST',
                            data :
                            {
                                event_id : self.model.get("event_id"),
                                url : window.location.href
                            }
                        });
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                        //alert("分分分失败")
                    }
                }

                self.WeiXin_data_Timeline =
                {
                    desc: data.share_text.title,//"模特邀约第一移动平台", // 分享标题
                    title: data.share_text.content,//"我是约女神【" + data.user_name + "】，等你约拍哦~", // 分享描述
                    link: data.share_text.url + '&_from_source=timeline', // 分享链接
                    imgUrl: data.share_text.img,//data.user_icon, // 分享图标
                    type: 'link', // 分享类型,music、video或link，不填默认为link
                    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                    success: function () {
                        // 用户确认分享后执行的回调函数

                        utility.ajax_request
                        ({
                            url : global_config.ajax_url.send_share_coupon,
                            type : 'POST',
                            data :
                            {
                                event_id : self.model.get("event_id"),
                                url : window.location.href
                            }
                        });

                        utility.stat_action({tj_point: 'touch', tj_touch_type: 'share', tj_extend_params: { hash: loc.hash}});
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                        //alert("分分分失败")
                    }
                }

                utility.wei_xin.set_share_new(loc.hash, self.WeiXin_data, self.WeiXin_data_Timeline);

                WeiXinSDK.ready(function () {
                    WeiXinSDK.ShareToFriend(self.WeiXin_data);

                    WeiXinSDK.ShareTimeLine(self.WeiXin_data_Timeline);

                    WeiXinSDK.ShareQQ(self.WeiXin_data);
                });
                console.log(self.WeiXin_data,self.WeiXin_data_Timeline)
                //self.model.wx_sign(loc.origin + loc.pathname + loc.search); //新接口
            }
            else {
                if (WeixinApi.isWexXin()) {
                    WeixinApi.ready(function (Api) {
                        var loc = window.location;

                        var wxData = {
                            "appId": "wx25fbf6e62a52d11e",
                            "imgUrl": data.share_text.img,//data.user_icon,
                            "link": data.share_text.url + '?_from_source=normal',
                            "desc": data.share_text.content,//"我是约女神【" + data.user_name + "】，等你约拍哦~",
                            "title": data.share_text.title//"模特邀约第一移动平台"
                        };

                        var wx_Data_Timeline = {
                            "appId": "wx25fbf6e62a52d11e",
                            "imgUrl": data.share_text.img,//data.user_icon,
                            "link": data.share_text.url + '?_from_source=timeline',
                            //"link" : loc.origin + loc.pathname + loc.search + "&for_share=timeline" + loc.hash,
                            "desc": data.share_text.title,//"我是约女神【" + data.user_name + "】，等你约拍哦~",
                            "title": data.share_text.content//"模特邀约第一移动平台"
                        };

                        // 分享的回调
                        var wxCallbacks = {
                            // 收藏操作不执行回调，默认是开启(true)的
                            favorite: false,

                            // 分享操作开始之前
                            ready: function () {
                                // 你可以在这里对分享的数据进行重组
                                //alert("准备分享");
                            },
                            // 分享被用户自动取消
                            cancel: function (resp) {
                                // 你可以在你的页面上给用户一个小Tip，为什么要取消呢？
                                //alert("分享被取消，msg=" + resp.err_msg);
                            },
                            // 分享失败了
                            fail: function (resp) {
                                // 分享失败了，是不是可以告诉用户：不要紧，可能是网络问题，一会儿再试试？
                                //alert("分享失败，msg=" + resp.err_msg);
                            },
                            // 分享成功
                            confirm: function (resp) {
                                // 分享成功了，我们是不是可以做一些分享统计呢？

                                //alert(JSON.stringify(resp));

                                utility.stat_action({tj_point: 'touch', tj_touch_type: 'share', tj_extend_params: { hash: loc.hash}});
                                //alert("分享成功，msg=" + resp.err_msg);
                            },
                            // 整个分享过程结束
                            all: function (resp, shareTo) {
                                // 如果你做的是一个鼓励用户进行分享的产品，在这里是不是可以给用户一些反馈了？
                                //alert("分享" + (shareTo ? "到" + shareTo : "") + "结束，msg=" + resp.err_msg);
                            }
                        };

                        utility.wei_xin.set_share(loc.hash, wxData, wx_Data_Timeline, wxCallbacks);

                        // 安卓好友
                        Api.shareToFriend(wxData, wxCallbacks);

                        // 安卓朋友圈
                        Api.shareToTimeline(wx_Data_Timeline, wxCallbacks);

                        // 腾讯微博
                        //WeixinApi.shareToWeibo(wxData, wxCallbacks);

                        // iOS好友/朋友圈
                        Api.generalShare(wxData, wxCallbacks);
                    });
                }
            }
        },
        setup_dialog_down_app : function()
        {
            var self = this;

            var str = '需要和组织者私聊，可拨打客服电话<br>4000-82-9003或直接下载APP';

            if(utility.is_other_browser_or_not_weixin_yueyue())
            {
                str = '还没安装约yue APP?';
            }

            // 已注册过的提示层安装 hudw 2014.11.21
            self.dialog_down_app = new dialog({
                content: '<p class="wx_card_notice">'+str+'<button class="ui-button ui-button-primary ui-button-size-middle ui-button-block wx_card_login_btn"  data-role="button" data-name="wx_card_download_btn"><span class="ui-button-content">下载APP</span></button>'
            });

            self.dialog_down_app
                .on('tap:button:wx_card_download_btn',function()
                {
                    this.hide();

                    window.location.href = 'http://app.yueus.com/';
                });
            //self.dialog_down_app.show();
        },
        setup_dialog_down_app_v2 : function()
        {
            var self = this;

            var str = '<div style="padding: 0 5px;">网页版暂不支持该功能，请使用约yue APP获得更佳体验</div>';

            // 已注册过的提示层安装 hudw 2014.11.21
            self.dialog_down_app_v2 = new dialog({
                content: '<p class="wx_card_notice">'+str+'<button class="ui-button ui-button-primary ui-button-size-middle ui-button-block wx_card_login_btn"  data-role="button" data-name="wx_card_download_btn"><span class="ui-button-content">下载APP</span></button>'
            });

            self.dialog_down_app_v2
                .on('tap:button:wx_card_download_btn',function()
                {
                    this.hide();

                    window.location.href = 'http://app.yueus.com/';
                });
            //self.dialog_down_app.show();
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

            /*
            if(self.get('templateModel').form_info_title)
            {
                self.$('[data-role="apply-footer"]').addClass('fn-hide');
            }
            */
            if(self.get('templateModel').is_preview)
            {

                self._parseObj(self.get("preview_obj"));

                self.$('[data-role="preview-hide"]').hide();
            }
            else
            {

                self.model.get_list();


            }

            self.setup_dialog_down_app();
            self.setup_dialog_down_app_v2();

            if(utility.is_other_browser_or_not_weixin_yueyue())
            {
                self.$container.append(download_tips());
                self.$('[data-role="page-back"]').addClass('fn-invisible');
                self.$('.right-button-wrap').addClass('fn-invisible');
                self.$('[data-role="other_browser_tips"]').removeClass("fn-hide");
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
