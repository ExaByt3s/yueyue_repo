/**
 * 主题详情视图
 *
 * hdw 2014.10.27
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var info_tpl = require('../tpl/info.handlebars');
    var main_info_tpl = require('../tpl/main_info.handlebars');
    var utility = require('../../common/utility');
    var Scroll = require('../../common/scroll');
    var WeixinApi = require('../../common/I_WX');
    var global_config = require('../../common/global_config');
    var WeiXinSDK = require('../../common/WX_JSSDK');
    var m_alert = require('../../ui/m_alert/view');

    var dialog = require('../../ui/dialog/index');
    var download_tips = require('../../ui/download_tips/download_tips.handlebars');

    var topic_list_view = View.extend(
    {
        attrs:
        {
            template: main_info_tpl
        },
        events:
        {
            'swiperight': function()
            {
                page_control.back();
            },
            'tap [data-role=page-back]': function(ev)
            {
                page_control.back();
            },
            'tap [data-big-img="big-img"]' : function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                if(!$cur_btn.attr('data-url'))
                {
                    // 当前图片
                    var cur_alumn_img = $cur_btn.attr('src');

                    var $total_alumn_img = self.$('[data-big-img="big-img"]');

                    var total_weixin_alumn_arr = [];

                    $total_alumn_img.each(function(i,obj)
                    {

                        total_weixin_alumn_arr.push($(obj).attr('src'));
                    });

                    console.log(cur_alumn_img,total_weixin_alumn_arr)

                    if(WeixinApi.isWexXin())
                    {

                        WeixinApi.imagePreview(cur_alumn_img,total_weixin_alumn_arr);

                        return;
                    }
                }


            },
            //导航跳转
            'tap [data-role="nav-page"]': function(ev)
            {
                var self = this;
                var $cur_btn = $(ev.currentTarget);
                var url_style =  $cur_btn.attr('data-url-style');
                var data_url =  $cur_btn.attr('data-url');
                var no_jump = $cur_btn.attr('data-no-jump');

                if(utility.is_other_browser_or_not_weixin_yueyue())
                {
                    return;
                }

                if(no_jump)
                {
                    if(!utility.user.get('role') == 'cameraman')
                    {
                        //return;
                    }


                }

                switch (url_style)
                {
                    case 'inside': 
                        page_control.navigate_to_page(data_url);
                        break;

                    case 'outside': 
                        window.location.href = data_url ;
                        break;

                    case 'ohter': 
                        //
                        break;
                }     
            },
            'tap [data-role="other_browser_tips"]' : function()
            {
                var self = this;

                self._run_jump_app();

            }
        },
        _run_jump_app : function()
        {
            var self = this;

            var no_wifi_url = encodeURIComponent('http://yp.yueus.com/mobile/app?from_app=1#topic/'+self.model.get('id'));
            var wifi_url = encodeURIComponent('http://yp-wifi.yueus.com/mobile/app?from_app=1#topic/'+self.model.get('id'));

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
                    self.dialog_tips.show();
                }
            });
        },
        /**
         * 安装事件
         * @private
         */
        _setup_events: function()
        {
            var self = this;

            self.model
            .on('before:info:fetch', function(response) {

            })
            .on('success:info:fetch', function(response)
            {
                self._render_topic_info();
            })
            .on('error:info:fetch', function(response) {

            });

            self.on('update_list', function()
            {


                if (!self.$scroll_view_obj)
                {
                    self._setup_scroll();
                    self.$scroll_view_obj.scrollTo(0, 0);
                    self.$scroll_view_obj.refresh();
                    return;
                }
                self.$scroll_view_obj.refresh();
            });
        },
        _set_WX_share : function(data)
        {
            var self = this;

            var loc = window.location;

            if(WeiXinSDK.isWeiXin() && global_config.WeiXin_Version >= "6.0.2")//(global_config.WeiXin_Version.indexOf('6.0.2') != -1|| global_config.WeiXin_Version.indexOf('6.1') != -1)
            {
                self.WeiXin_data =
                {
                    title: data.share_text.title,//"模特邀约第一移动平台", // 分享标题
                    desc: data.share_text.content,//"【" + data.title + "】火热报名中 | 约yue", // 分享描述
                    link: data.share_text.url+'?_from_source=normal',  // 分享链接
                    imgUrl: data.share_text.img,//'http://yp.yueus.com/m/images/weixi_yueyue_icon.png?' + new Date().getTime(), // 分享图标
                    type: 'link', // 分享类型,music、video或link，不填默认为link
                    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                    success: function () {
                        // 用户确认分享后执行的回调函数
                        //alert("分分分成功")
                        utility.stat_action({tj_point:'touch',tj_touch_type :'share',tj_extend_params :{ hash:loc.hash}});
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                        //alert("分分分失败")
                    }
                };
    
                self.WeiXin_data_Timeline =
                {
                    "title" : data.share_text.title,//"模特邀约第一移动平台",
                    "desc" : data.share_text.content,//"【" + data.title + "】火热报名中 | 约yue",
                    "link" :  data.share_text.url+'?_from_source=timeline',//loc.origin + loc.pathname + loc.search + "&for_share=timeline" + loc.hash,
                    "imgUrl" : data.share_text.img,//'http://yp.yueus.com/m/images/weixi_yueyue_icon.png?' + new Date().getTime(),
                    type: 'link', // 分享类型,music、video或link，不填默认为link
                    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                    success: function () {
                        // 用户确认分享后执行的回调函数
                        //alert("分分分成功")
                        utility.stat_action({tj_point:'touch',tj_touch_type :'share',tj_extend_params :{ hash:loc.hash}});
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                        //alert("分分分失败")
                    }
                };

                utility.wei_xin.set_share_new(loc.hash,self.WeiXin_data,self.WeiXin_data_Timeline);

                WeiXinSDK.ready(function()
                {
                    WeiXinSDK.ShareToFriend(self.WeiXin_data);

                    WeiXinSDK.ShareTimeLine(self.WeiXin_data_Timeline);

                    WeiXinSDK.ShareQQ(self.WeiXin_data);
                });
                //self.model.wx_sign(loc.origin + loc.pathname + loc.search); //新接口
            }
            else
            {
                if(WeixinApi.isWexXin())
                {
                    WeixinApi.ready(function(Api)
                    {
                        var wxData = {
                            "appId": "wx25fbf6e62a52d11e",
                            "imgUrl" : data.share_text.img,//'http://yp.yueus.com/m/images/weixi_yueyue_icon.png?' + new Date().getTime(),
                            "link" :  data.share_text.url+'?_from_source=normal',  //loc.href,
                            "desc" :  data.share_text.content,//"【" + data.title + "】火热报名中 | 约yue",
                            "title" : data.share_text.title//"模特邀约第一移动平台"
                        };

                        var wx_Data_Timeline  = {
                            "appId": "wx25fbf6e62a52d11e",
                            "imgUrl" : data.share_text.img,//'http://yp.yueus.com/m/images/weixi_yueyue_icon.png?' + new Date().getTime(),
                            "link" :  data.share_text.url+'?_from_source=timeline',
                            "desc" : data.share_text.content,//"【" + data.title + "】火热报名中 | 约yue",
                            "title" : data.share_text.title//"模特邀约第一移动平台"
                        };

                        // 分享的回调
                        var wxCallbacks = {
                            // 收藏操作不执行回调，默认是开启(true)的
                            favorite : false,

                            // 分享操作开始之前
                            ready : function() {
                                // 你可以在这里对分享的数据进行重组
                                //alert("准备分享");
                            },
                            // 分享被用户自动取消
                            cancel : function(resp) {
                                // 你可以在你的页面上给用户一个小Tip，为什么要取消呢？
                                //alert("分享被取消，msg=" + resp.err_msg);
                            },
                            // 分享失败了
                            fail : function(resp) {
                                // 分享失败了，是不是可以告诉用户：不要紧，可能是网络问题，一会儿再试试？
                                //alert("分享失败，msg=" + resp.err_msg);
                            },
                            // 分享成功
                            confirm : function(resp) {
                                // 分享成功了，我们是不是可以做一些分享统计呢？
                                utility.stat_action({tj_point:'touch',tj_touch_type :'share',tj_extend_params :{ hash:loc.hash}});
                                //alert("分享成功，msg=" + resp.err_msg);
                            },
                            // 整个分享过程结束
                            all : function(resp,shareTo) {
                                // 如果你做的是一个鼓励用户进行分享的产品，在这里是不是可以给用户一些反馈了？
                                //alert("分享" + (shareTo ? "到" + shareTo : "") + "结束，msg=" + resp.err_msg);
                            }
                        };

                        utility.wei_xin.set_share(loc.hash,wxData,wx_Data_Timeline,wxCallbacks);

                        // 安卓好友
                        Api.shareToFriend(wxData, wxCallbacks);

                        // 安卓朋友圈
                        Api.shareToTimeline(wx_Data_Timeline, wxCallbacks);

                        // 腾讯微博
                        //WeixinApi.shareToWeibo(wxData, wxCallbacks);

                        // iOS好友/朋友圈
                        Api.generalShare(wxData,wxCallbacks);
                    });
                }
            }




        },
        _render_topic_info: function()
        {
            var self = this;
            var data = self.model.toJSON();
            self.$title.html(data.title);
            var html_str = info_tpl(data);

            // 根据reset判断是否分页
            self.$info['html'](html_str);


            if(WeixinApi.isWexXin() || WeiXinSDK.isWeiXin())
            {
                self._set_WX_share(data);
            }

            self.$info.find('img').each(function(i,obj)
            {
                $(obj).attr('data-big-img',"big-img");
            });


            self.trigger('update_list');
        },
        _setup_scroll: function()
        {
            var self = this;
            var view_scroll_obj = Scroll(self.$container,
            {
                is_hide_dropdown: true
            });
            self.$scroll_view_obj = view_scroll_obj;
        },
        setup_dialog_tips : function()
        {
            var self = this;

            var str = '<div style="padding: 0 5px;">网页版暂不支持该功能，请使用约yue APP获得更佳体验</div>';

            if(utility.is_other_browser_or_not_weixin_yueyue())
            {
                str = '还没安装约yue APP?';
            }

            self.dialog_tips = new dialog({
                content: '<p class="wx_card_notice">'+str+'</p><button class="ui-button ui-button-primary ui-button-size-middle ui-button-block wx_card_login_btn"  data-role="button" data-name="wx_card_tips_btn"><span class="ui-button-content">立即下载</span></button>'
            })
                .on('tap:button:wx_card_tips_btn',function()
                {
                    this.hide();

                    window.location.href = 'http://app.yueus.com/';
                });

        },
        /**
         * 视图初始化入口
         */
        setup: function()
        {
            var self = this;
            //滚动容器
            self.$container = self.$('[data-role="container"]');
            self.$info = self.$('[data-role="info"]');
            self.$title = self.$('[data-role="title"]');
            //安装事件
            self._setup_events();
            self.refresh();

            self.setup_dialog_tips();

            if(utility.is_other_browser_or_not_weixin_yueyue())
            {

                self.$container.height(window.innerHeight -45)
                self.$container.after(download_tips());
                self.$('[data-role="page-back"]').addClass('fn-invisible');
                self.$('.right-button-wrap').addClass('fn-invisible');
                self.$('[data-role="other_browser_tips"]').removeClass("fn-hide");
            }


        },
        render: function()
        {
            var self = this;
            // 调用渲染函数
            View.prototype.render.apply(self);
            self.trigger('render');
            return self;
        },
        refresh: function()
        {
            var self = this;
            self.model.get_info(self.get('topic_id'));
        }
    });
    module.exports = topic_list_view;
});