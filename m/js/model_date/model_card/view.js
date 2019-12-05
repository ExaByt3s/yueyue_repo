/**
 * 首页 - 模特卡
 * 汤圆 2014.8.21
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var model_card = require('../model_card/tpl/main.handlebars');
    var items_tpl = require('../model_card/tpl/items.handlebars');
    var starts_tpl = require('./tpl/starts.handlebars');
    var utility = require('../../common/utility');
    var Scroll = require('../../common/scroll');
    var IScroll = require('../../common/new_iscroll');
    var slide_tpl = require('../../widget/slide_v2/tpl/slide.handlebars');
    //var slide_v2 = require('../../widget/slide_v2/view');
    var grid = require('../../widget/model_pic/view');
    var App = require('../../common/I_APP');
    var m_alert = require('../../ui/m_alert/view');

    var mine_popup = require('../../ui/mine-popup/index');
    var WeixinApi = require('../../common/I_WX');
    var dialog = require('../../ui/dialog/index');
    var global_config = require('../../common/global_config');
    var WeiXinSDK = require('../../common/WX_JSSDK');
    var ua = require('../../frame/ua');

    var download_tips = require('../../ui/download_tips/download_tips.handlebars');

    var model_card_view = View.extend
    ({

        attrs:
        {
            template: model_card
        },
        events :
        {
            /*'swiperight' : function (){
                page_control.back();
            },*/
            /**
             * 约拍按钮
             */
            'tap [data-role="yuepai"]' : function()
            {
                var self = this;

				new dialog({
                content: '<p class="wx_card_notice p5">“亲，由于约约华丽升级，微信端的模特邀约功能暂停使用，预计将会在一周内恢复。如您已安装约约APP，请升级至最新版。升级期间造成的不便，敬请谅解！”</p>'
	            })
                .on('tap:button:wx_card_login_btn',function()
                {
                    this.hide();

                    // modify by hudw 2014.12.23
                    page_control.navigate_to_page('account/register/reg');
                }).show();


				return;

                if(utility.is_other_browser_or_not_weixin_yueyue())
                {
                    return;
                }



                utility.page_pv_stat_action({tj_point:'yuepai'});

                if(!utility.auth.is_login())
                {
                    console.log("no_login");

                    self.dialog_login.show();

                    return;

                }


                //用于预览跳过来
                if(self.preview_data && self.preview_data.is_preview){
                    self.model.is_preview = true;
                    page_control.navigate_to_page("model_style/" + self.model.user_id,{data:self.preview_data});
                    return;
                }

                if(utility.login_id == self.model.get('user_id'))
                {
                    m_alert.show("不要刷单啊，自己不能约自己",'error');
                    return;
                }

                if(utility.login_id && utility.user.get('role') == 'model' )
                {
                    m_alert.show("我们暂时不支持模特约拍模特功能。",'error');

                    return;
                }

                // 直接跳过去选择风格
                page_control.navigate_to_page("model_style/" + self.model.get('user_id'));


            },
            'swiperight' : function (){
                //page_control.back();
            },
            /**
             * 私聊按钮
             * @param ev
             */
            'tap [data-role=msg]' : function (ev)
            {
                var self = this;

                utility.stat_action({tj_point:'touch',tj_touch_type :'chat'});

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
                return;

                if(!utility.auth.is_login())
                {
                    page_control.navigate_to_page('account/login');

                    return;
                }

                if(self.model.toJSON().user_id == utility.user.get('user_id'))
                {
                    m_alert.show("不要和自己聊天。",'error');

                    return;
                }

                if(utility.user.get('role') == 'model')
                {
                    m_alert.show("不要和模特聊天。",'error');

                    return;
                }

                self.is_from_date = false;

                self.model.judge_can_date({ model_user_id : self.model.get('user_id')});


            },
            'tap [data-role=page-back]' : function (ev)
            {
                var self = this;

                page_control.back();
            },
            'tap [data-role="model-car-available_notice"]' :function()
            {
                var self = this;

                //self.$('[data-role="model-car-available_notice"]').addClass("fn-hide");
            },
            'tap [data-role="model-car-available_notice_text"]' : function()
            {
                var self = this;

                //self.$('[data-role="model-car-available_notice_text"]').addClass("fn-hide");

            },
            

            /**
             * 关注按钮
             */
            'tap [data-role="follow"]' : function()
            {
                var self = this;

                if(utility.is_other_browser_or_not_weixin_yueyue())
                {
                    return;
                }

                if(!utility.auth.is_login())
                {
                    page_control.navigate_to_page('account/login');

                    return;
                }

                var data =
                {
                    type : 'follow',
                    be_follow_user_id : self.model.get("user_id")
                };

                m_alert.show('关注中...',{delay : 1000});

                self.model.follow_request(data);
            },
            /**
             * 取消关注按钮
             */
            'tap [data-role="unfollow"]' : function()
            {
                var self = this;

                if(!utility.auth.is_login())
                {
                    page_control.navigate_to_page('account/login');

                    return;
                }

                var data =
                {
                    type : 'no_follow',
                    be_follow_user_id : self.model.get("user_id")
                };

                m_alert.show('取消关注中...',{delay : 1000});

                self.model.unfollow_request(data);
            },
            'tap [data-role="cam_icon"]':function(ev)
            {
                var self = this;

                if(utility.is_other_browser_or_not_weixin_yueyue())
                {
                    return;
                }

                var $cur_btn = $(ev.currentTarget);

                var cam_id = $cur_btn.attr('data-cam_id')

                page_control.navigate_to_page("zone/"+ cam_id + "/cameraman")
            },
            'tap [data-role="level"]' : function()
            {
                var self = this;

                if(utility.is_other_browser_or_not_weixin_yueyue())
                {
                    return;
                }

                page_control.navigate_to_page('mine/explain/lev');
            },
            'tap [data-role="jifen"]' : function()
            {
                var self = this;

                if(utility.is_other_browser_or_not_weixin_yueyue())
                {
                    return;
                }

                page_control.navigate_to_page('mine/explain/charm');
            },
            'tap [data-role="go_fans"]' : function()
            {
                var self = this;

                if(utility.is_other_browser_or_not_weixin_yueyue())
                {
                    return;
                }

                page_control.navigate_to_page('account/fans_follows/'+self.model.get("user_id")+'/fans');
            },
            /**
             * 点击查看轮播图
             * @param ev
             */
            'tap [data-role=grid-pic-container]' : function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                // 当前图片
                var cur_alumn_img = $cur_btn.attr('data-alumn-img');

                // 所有图片
                var $total_alumn_img = self.$('[data-role=grid-pic-container]');

                var total_alumn_img_arr = [];

                var total_weixin_alumn_arr = [];

                $total_alumn_img.each(function(i,obj)
                {
                    total_alumn_img_arr.push
                    ({
                        url : $(obj).attr('data-alumn-img'),
                        text : ''
                    });

                    total_weixin_alumn_arr.push($(obj).attr('data-alumn-img'));
                });				

                if(WeixinApi.isWexXin())
                {
					//WeixinApi.enableDebugMode();

                    WeixinApi.imagePreview(cur_alumn_img,total_weixin_alumn_arr);

                    return;
                }

                // 当前图片索引
                var index = $total_alumn_img.index($cur_btn);

                // 轮播图数据
                var data =
                {
                    img_arr : total_alumn_img_arr,
                    index : index
                };

                console.log(data);

                if(!App.isPaiApp)
                {
                    console.log('no App');

                    return;
                }

                App.show_alumn_imgs(data);
            },
            'tap [data-role="nav-to-comment"]' : function(ev)
            {
                var self = this;

                if(utility.is_other_browser_or_not_weixin_yueyue())
                {
                    return;
                }

                var $cur_btn = $(ev.currentTarget);

                var user_id = self.model.get('user_id');

                page_control.navigate_to_page('comment/list/model/'+user_id+'/0');
            },
            'tap [data-role="menu"]' : function()
            {
                // 弹出层设置

                var self = this;

                if(utility.is_other_browser_or_not_weixin_yueyue())
                {
                    return;
                }

                if(!self.mine_Popup){
                    self.mine_Popup = new mine_popup({
                        uid:utility.user.get('user_id'),
                        report_model_id : self.model.get('user_id'),
                        items : {
                            edit:false,
                            report:true
                        }
                    }).show();
                }else{
                    self.mine_Popup.show();
                }
            },
            'tap [data-role="pop-max-dabenxiang"]' : function(ev)
            {
                var self = this;
                $(ev.currentTarget).addClass('fn-hide');
                utility.storage.set("pop-max-dabenxiang",1)
            },
            'tap [data-role="focus"]' :function(ev)
            {
                var self = this;

                if(utility.is_other_browser_or_not_weixin_yueyue())
                {
                    return;
                }

                if(!utility.auth.is_login())
                {
                    page_control.navigate_to_page('account/login');

                    return;
                }

                var $cur_btn = $(ev.currentTarget);

                var type = $cur_btn.attr('data-focus-type')

                var data;

                switch(type)
                {
                    case 'to_focuse': data = {type : 'follow',be_follow_user_id : self.model.get("user_id")};
                        m_alert.show('关注中...','right',{delay : 1000});
                        self.model.follow_request(data);
                        break;
                    case 'focused': data = {type : 'no_follow',be_follow_user_id : self.model.get("user_id")};
                        m_alert.show('取消关注中...','right',{delay : 1000});
                        self.model.unfollow_request(data);
                        break;
                    case 'each': data = {type : 'no_follow',be_follow_user_id : self.model.get("user_id")};
                        m_alert.show('取消关注中...','right',{delay : 1000});
                        self.model.unfollow_request(data);
                        break;
                }
            },
            'tap  [data-role="to_more_style"]' : function()
            {
                var self = this;

				

                if(utility.is_other_browser_or_not_weixin_yueyue())
                {
                    return;
                }

                if(!utility.auth.is_login())
                {
                    page_control.navigate_to_page('account/login');

                    return;
                }

                //用于预览跳过来
                if(self.preview_data && self.preview_data.is_preview){
                    self.model.is_preview = true;
                    page_control.navigate_to_page("model_style/" + self.model.user_id,{data:self.preview_data});
                    return;
                }

                /*if(utility.login_id == self.model.get('user_id'))
                 {
                 m_alert.show("不要刷单啊，自己不能约自己",'error');
                 return;
                 }*/

                // 模特点击全部风格，把当前模特对象传递过去，模拟成预览
                var preview_data = null;

                if(utility.login_id && utility.user.get('role') == 'model' )
                {
                    preview_data = {data:self.model.toJSON(),is_preview:true};
                }

                // 直接跳过去选择风格
                page_control.navigate_to_page("model_style/" + self.model.get('user_id'),preview_data);
            },
            'tap [data-role="other_browser_tips"]' : function()
            {
                var self = this;

                utility.judge_go_to_app
                ({
                    url : 'yueyue://goto?type=inner_app&pid=1220025&user_id=' + self.model.get('user_id'),
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

            }
            
        },
        _setup_events : function()
        {
            var self = this;

            
            self.model
                .on('success:fetch', function(response,options)
                {
                    self._insert_data();

                    //安装轮播
                    self._setup_slide();

                    //console.log('slide');

                    if(self.model.toJSON().is_follow)
                    {
                        self.$('[data-role="unfollow"]').removeClass('fn-hide');
                    }
                    else
                    {
                        self.$('[data-role="follow"]').removeClass('fn-hide');
                    }


                    self.$('[data-role="msg"]').removeClass('fn-hide');

                    // 显示模特卡名字
                    self.$('[data-role="moteka-title"]').html(self.model.get('nickname'));

                    if(!self.view_scroll_obj)
                    {
                        self._setup_scroll();

                        self.view_scroll_obj.reset_top();

                        self._drop_reset();

                        return;
                    }

                    self._drop_reset();
                    self.view_scroll_obj.refresh();
                    self.view_scroll_obj.reset_top();


                })
                .on('complete:fetch', function (response,options)
                {
                    //m_alert.hide();
                    self.view_scroll_obj.refresh();
                })
                .on('success:fetch_follow',function(response,options)
                {
                    self._change_focus_type(response);

                    m_alert.show(response.result_data.msg,'right',{delay : 1000});
                })
                .on('error:fetch_follow',function(response,options)
                {
                    m_alert.show('关注失败！','error',{delay : 1000});
                })
                .on('complete:fetch_follow',function(response,options)
                {
                    //m_alert.hide();
                })
                .on('success:fetch_unfollow',function(response,options)
                {
                    self._change_focus_type(response);

                    m_alert.show(response.result_data.msg,'right',{delay : 1000});
                })
                .on('error:fetch_unfollow',function(response,options)
                {
                    m_alert.show('取消失败！','error',{delay : 1000});
                })
                .on('complete:fetch_unfollow',function(response,options)
                {
                    //m_alert.hide();
                })
                // 判断是否约拍
                .on('before:judge_can_date',function()
                {
                    if(self.is_from_date)
                    {
                        m_alert.show('约拍正在准备中。。。','loading',{delay:-1});
                    }
                    else
                    {
                        m_alert.show('私聊正在准备中。。。','loading',{delay:-1});
                    }

                })
                .on('success:judge_can_date',function(response,options)
                {
                    var response = response.result_data;

                    if(response.code !=1)
                    {
                        m_alert.show(response.msg,'error');

                        var url = encodeURIComponent(window.location.href);

                        switch (utility.int(response.model_level_require))
                        {
                            case 2:
                                page_control.navigate_to_page('mine/authentication/v2/from_date');
                                break;
                            case 3:
                                page_control.navigate_to_page('mine/authentication/v3/from_date_'+url);
                                break;
                        }

                        return;
                    }

                    m_alert.hide();

                    if(self.is_from_date)
                    {
                        page_control.navigate_to_page("model_style/" + self.model.get('user_id'));
                    }
                    else
                    {


                        var data =
                        {
                            senderid : utility.login_id,
                            receiverid : utility.int(self.model.get('user_id')),
                            sendername : utility.user.get('nickname'),
                            receivername : self.model.get('user_name'),
                            sendericon : utility.user.get('user_icon'),
                            receivericon : self.model.get('user_icon')
                        };

                        if(!App.isPaiApp)
                        {
                            console.warn('no App');

                            return;
                        }

                        App.chat(data);
                    }


                })
                .on('error:judge_can_date',function()
                {
                    m_alert.show('网络异常','error');
                })
                .on('success:wx_sign',function(response)
                {
                    console.log(response.result_data)
                    //设置wx.config 传入 appId，timestamp，nonceStr，signature


                });


            self.on('render',function()
            {
                self.get_model_info();

                self.$('[data-role="pop-max-dabenxiang"]').addClass('fn-hide');

                if(utility.storage.get('pop-max-dabenxiang') || self.preview_data)
                {
                    //self.$('[data-role="pop-max-dabenxiang"]').addClass('fn-hide');
                }
            });
        },
        _set_WX_share : function(data)
        {
            var self = this;

            console.log(data);

            if(WeiXinSDK.isWeiXin() && global_config.WeiXin_Version >= "6.0.2")//(global_config.WeiXin_Version.indexOf('6.0.2') != -1|| global_config.WeiXin_Version.indexOf('6.1') != -1)
            {

                var loc = window.location;

                self.WeiXin_data =
                {
                    title: data.share_text.title,//"模特邀约第一移动平台", // 分享标题
                    desc: data.share_text.content,//"我是约女神【" + data.user_name + "】，等你约拍哦~", // 分享描述
                    link: data.share_text.url+'?_from_source=normal', // 分享链接
                    imgUrl:data.share_text.img,//data.user_icon, // 分享图标
                    type: 'link', // 分享类型,music、video或link，不填默认为link
                    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                    success: function () {
                        // 用户确认分享后执行的回调函数
                        //alert("分分分成功")
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
                    link: data.share_text.url+'?_from_source=timeline', // 分享链接
                    imgUrl:data.share_text.img,//data.user_icon, // 分享图标
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
                }

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
                        var loc = window.location;

                        var wxData = {
                            "appId": "wx25fbf6e62a52d11e",
                            "imgUrl" : data.share_text.img,//data.user_icon,
                            "link" : data.share_text.url + '?_from_source=normal',
                            "desc" : data.share_text.content,//"我是约女神【" + data.user_name + "】，等你约拍哦~",
                            "title" : data.share_text.title//"模特邀约第一移动平台"
                        };

                        var wx_Data_Timeline  = {
                            "appId": "wx25fbf6e62a52d11e",
                            "imgUrl" : data.share_text.img,//data.user_icon,
							"link" : data.share_text.url + '?_from_source=timeline',       
                            //"link" : loc.origin + loc.pathname + loc.search + "&for_share=timeline" + loc.hash,
                            "desc" : data.share_text.title,//"我是约女神【" + data.user_name + "】，等你约拍哦~",
                            "title" :  data.share_text.content//"模特邀约第一移动平台"
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




            /*
            var WeiXin_data =
            {
                title: "2模特邀约第一移动平台", // 分享标题
                desc: "2我是约女神【" + data.user_name + "】，等你约拍哦~", // 分享描述
                link: loc.origin + loc.pathname + loc.search + "&for_share=timeline" + loc.hash, // 分享链接
                imgUrl:data.user_icon, // 分享图标
                type: 'link', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                    // 用户确认分享后执行的回调函数

                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                    alert("分分分失败")
                }
            }
            */



        },
        _change_focus_type : function(response)
        {
            var self = this;

            console.log(response)
            self.$('[data-role="focus"]').addClass('fn-hide');

            if(response.result_data.is_follow == 2)
            {
                self.$('[data-focus-type="each"]').removeClass('fn-hide');
            }
            else if(response.result_data.is_follow == 1)
            {
                self.$('[data-focus-type="focused"]').removeClass('fn-hide');
            }
            else
            {
                self.$('[data-focus-type="to_focuse"]').removeClass('fn-hide');
            }
        },
        _insert_data : function(data)
        {
            var self = this;

            var model_data = self.model.toJSON();

            //用于预览 data参数存在时是预览
            if(data){
                data.is_preview = true;
                model_data = data;
            }
            console.log(model_data)
            switch (model_data.level_require)
            {
                case '1': model_data = $.extend(true,{},model_data,{ level_r_1: true});break;
                case '2': model_data = $.extend(true,{},model_data,{ level_r_2: true});break;
                case '3': model_data = $.extend(true,{},model_data,{ level_r_3: true});break;
            }


            // 提供模特卡页面给第三方浏览器和微信非约yue公众号的用户观看
            if(utility.is_other_browser_or_not_weixin_yueyue())
            {
                /*if(!utility.is_dev_mode())
                {
                    self.$('[data-role="date-footer"]').addClass('fn-hide');
                    self.$('[data-role="other_browser_tips"]').removeClass("fn-hide");
                }
                else
                {
                    self.$('[data-role="other_browser_tips"]').addClass("fn-hide");
                    self.$('[data-role="date-footer"]').removeClass('fn-hide');
                }*/
                //self.$('[data-role="date-footer"]').addClass('fn-hide');
                //self.$('[data-role="other_browser_tips"]').removeClass("fn-hide");

				self.$('[data-role="other_browser_tips"]').addClass("fn-hide");
                self.$('[data-role="date-footer"]').removeClass('fn-hide');
            }
            // 微信约yue公众号打开
            else
            {
                self.$('[data-role="other_browser_tips"]').addClass("fn-hide");
                self.$('[data-role="date-footer"]').removeClass('fn-hide');
            }

            /*if(ua.is_weixin)
            {

            }
            else
            {
                //不是微信 且 不是预览编辑模特卡 显示tips
                if(!self.is_dev_mode())
                {
                    self.$('[data-role="other_browser_tips"]').removeClass("fn-hide");
                }
                else
                {
                    self.$('[data-role="other_browser_tips"]').addClass("fn-hide");
                }

            }*/


            //self.model.wx_sign(loc.origin + loc.pathname + loc.search);
             if(WeixinApi.isWexXin() || WeiXinSDK.isWeiXin())
             {
                 self._set_WX_share(model_data);//旧接口
             }

            if(model_data.new_model_style_arr)
            {
                model_data.model_style_combo = model_data.new_model_style_arr;
            }

            if(model_data.is_follow == 2)
            {
                model_data = $.extend(true,{},model_data,{is_follow_each_other: true});
            }
            else if(model_data.is_follow == 1)
            {
                model_data = $.extend(true,{},model_data,{is_follow_single: true});
            }

            var html_str = items_tpl(model_data);

            self.$items_container.html(html_str);

            self.can_pai = true;

            var html_str = starts_tpl
            ({
                data : model_data.comment_stars_list
            });

            self.$('[data-role="starts"]').html(html_str);

            //self.view_scroll_obj.refresh();

            //self.view_scroll_obj.scrollTo(0,0);

            //头像数等于2时调整布局
            if(model_data.date_log && model_data.date_log.length == 2)
            {
                self.$('[data-role="ph_contain"]').css('-webkit-box-pack','start');

                self.$('[data-role="cam_icon"]').css('margin-right','10px')
            }

            if(utility.login_id == self.model.get('user_id'))
            {
                self.$focus_contain = self.$('[data-role="focus-contain"]');

                self.$focus_contain.addClass("fn-hide");
            }



            // 显示模特卡名字 hudw 2014.11.21
            self.$('[data-role="moteka-title"]').html(utility.user.get('nickname'));

        },
        _setup_scroll : function()
        {
            var self = this;

            var view_scroll_obj = Scroll(self.$container,
            {
                lazyLoad : true,
                prevent_tag : 'slider'

            });


            self.view_scroll_obj = view_scroll_obj;

            self.view_scroll_obj.on('dropload',function(e)
            {
                self.get_model_info();

            });
        },
        setup : function()
        {
            var self = this;


            var container_height = utility.get_grid_size('double').height;

            // 配置交互对象
            self.$container = self.$('[data-role=items-container]');

            self.$items_container = self.$('[data-role="items-container"]');

            self.$container_img_list = self.$('[data-role="container-img-list"]');


            self.$container.height(window.innerHeight -45);

            self.$container_img_list.height(container_height);

            self.can_pai = false;

            self._setup_events();

            self.setup_dialog_down_app();

            self.setup_dialog_login();

            self.setup_dialog_tips();

            self.setup_dialog_down_app_v2();

            if(utility.is_other_browser_or_not_weixin_yueyue())
            {
                if(ua.is_qq)
                {
                    self.$container.height(window.innerHeight);
                }

                self.$('[data-role="container"]').append(download_tips());
                self.$('[data-role="page-back"]').addClass('fn-invisible');
                self.$('[data-role="menu"]').addClass('fn-invisible');
            }


            //self.get_model_info();


        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },

         setup_dialog_down_app : function()
        {
            var self = this;

            // 已注册过的提示层安装 hudw 2014.11.21
            self.dialog_down_app = new dialog({
                content: '<p class="wx_card_notice">需要和模特私聊，可拨打客服电话<br>4000-82-9003或直接下载APP</p><button class="ui-button ui-button-primary ui-button-size-middle ui-button-block wx_card_login_btn"  data-role="button" data-name="wx_card_download_btn"><span class="ui-button-content">下载APP</span></button>'
            });

            self.dialog_down_app
                .on('tap:button:wx_card_download_btn',function()
                {
                    this.hide();

                    window.location.href = 'http://app.yueus.com/';
                })
                .on('tap:button:wx_card_go_date_btn', function()
                {
                    this.hide();

                    if(!utility.auth.is_login())
                    {
                        console.log("no_login");

                        self.dialog_login.show();

                        return;

                    }


                    //用于预览跳过来
                    if(self.preview_data && self.preview_data.is_preview){
                        self.model.is_preview = true;
                        page_control.navigate_to_page("model_style/" + self.model.user_id,{data:self.preview_data});
                        return;
                    }

                    if(utility.login_id == self.model.get('user_id'))
                    {
                        m_alert.show("不要刷单啊，自己不能约自己",'error');
                        return;
                    }

                    if(utility.login_id && utility.user.get('role') == 'model' )
                    {
                        m_alert.show("我们暂时不支持模特约拍模特功能。",'error');

                        return;
                    }

                    // 直接跳过去选择风格
                    page_control.navigate_to_page("model_style/" + self.model.get('user_id'));
                    /*
                    if(!utility.auth.is_login())
                    {
                        self.dialog_login.show();

                        return;
                    }
                    else
                    {

                        console.log("去授权");
                        //page_control.navigate_to_page("model_style/" + self.get("model").get("user_id"));
                    }
                    */

                    //page_control.navigate_to_page('account/login/has_reg_user_id_'+self.enter_phone.val());
                })
            //self.dialog_down_app.show();
        },
        setup_dialog_login : function()
        {
            var self = this;

            self.dialog_login = new dialog({
                content: '<p class="wx_card_notice">您还没有注册</p><button class="ui-button ui-button-primary ui-button-size-middle ui-button-block wx_card_login_btn"  data-role="button" data-name="wx_card_login_btn"><span class="ui-button-content">注册</span></button><p style="margin-top: 16px;color:#666666;font-size: 12px;height: 18px;line-height: 18px;">注册太麻烦，想要直接约？</p><p style="color:#666666;font-size: 12px;height: 18px;line-height: 18px">拨打4000-82-9003，我们帮你！</p>'
            })
                .on('tap:button:wx_card_login_btn',function()
                {
                    this.hide();

                    // modify by hudw 2014.12.23
                    page_control.navigate_to_page('account/register/reg');
                });


            //var self = this;

            //self.dialog_login.hide();

            //page_control.navigate_to_page('account/login');
        },
        setup_dialog_tips : function()
        {
            var self = this;

            var str = '需要和模特私聊，可拨打客服电话<br>4000-82-9003或直接下载APP';

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
        setup_dialog_down_app_v2 : function()
        {
            var self = this;

            var str = '网页版暂不支持该功能，请使用约yue APP获得更佳体验';

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
        _drop_reset : function()
        {
            var self = this;

            self.view_scroll_obj && self.view_scroll_obj.resetload();
        },

        set_preview_data : function(data){
            var self = this;
            self.preview_data = data || null;
            return self;
        },


        /**
         * 安装轮播滚动
         * @private
         */

        _setup_slide : function(data){

            var self = this;
            var contents = [];

            if(data){
                var group_render_queue = data;
            }else{
                var group_render_queue = self.model.toJSON().new_model_pic;
            }

            if(!group_render_queue){
                return;
            }
            var len = group_render_queue.length;



            // 头部组图
            for(var i = 0;i<len;i++)
            {
                if(i>=1)
                {
                    for(var n=0;n<group_render_queue[i].length;n++)
                    {
                        if((n+2)%3==0)
                        {
                            group_render_queue[i][n].type = 'middle';
                        }
                        else
                        {
                            group_render_queue[i][n].type = 'one';
                        }


                    }
                }

                var img_render_queue = group_render_queue[i];

                console.log(img_render_queue)

                var grid_list_view = new grid
                ({
                    templateModel :
                    {
                        tpl_data : img_render_queue,
                        tpl_type : '1double_others_one'
                    }
                }).render();

                var new_grid_container = document.createElement('li');

                $(new_grid_container).html(grid_list_view.list());

                contents.push({
                    content : $(new_grid_container).html()
                })
            }


            self.slide_view_pic_len = contents.length;
            //当只有一张图时不显示小圆点
            var no_single;
            (contents.length > 1) ? ( no_single = true ) : ( no_single = false );
            self.$slide_out = self.$('[data-role=container-img-list]');

            //设置选中第一个
            // modify by hudw 先判断对象是否存在
            if(contents[0])
            {
                contents[0].class = "current";
            }


            /**
             * modify by hudw
             * 2014.12.25
             * 使用iscroll 实现轮播
             */
            self.$slide_out.html(slide_tpl
            ({
                contents : contents,
                no_single : no_single
            }));

            var l = self.$slide_out.find('.swiper-slide').length;
            var w = utility.get_view_port_width();
            var h = grid_list_view && grid_list_view._get_size_by_type('double').height;

            self.$slide_out.find('.swiper-slide').width(w).height(h);
            self.$slide_out.height(h);

            self.slide_view = new IScroll(self.$slide_out,
                {
                    bounce: false,
                    snap: true,
                    snapThreshold : utility.get_view_port_width()/5,
                    momentum: false,
                    hScroll : true,
                    vScroll : false,
                    hScrollbar: false,
                    vScrollbar: false,
                    checkDOMChanges: true
                });

            self.slide_view.current_page = 1 ;

            $(self.slide_view.scroller).width(utility.float(l*w)).height(h);

            if(contents.length>1)
            {
                self.$slide_out.height(h+20);
                self.$slide_out.find('[data-role="ad-pics-num"] span').eq(0).addClass('swiper-visible-switch swiper-active-switch');
            }

            self.slide_view.on('scrollEnd',function()
            {
                var _self = this;

                var idx = _self.currPageX;

                self.$slide_out.find('[data-role="ad-pics-num"] span').removeClass('swiper-visible-switch swiper-active-switch');

                self.$slide_out.find('[data-role="ad-pics-num"] span').eq(idx).addClass('swiper-visible-switch swiper-active-switch');
            });

            self.slide_view.refresh();
            /***END***/

            setTimeout(function(){
                self.view_scroll_obj.refresh();

                //self.view_scroll_obj.scrollTo(0,0);
            },50)

        },


        get_model_info : function()
        {
            var self = this;

            //用于预览
            if(self.preview_data){
                console.log(self.preview_data)
                self._insert_data(self.preview_data);

                //安装轮播
                self._setup_slide(self.preview_data.new_model_pic);

                if(!self.view_scroll_obj)
                {
                    self._setup_scroll();

                    self.view_scroll_obj.reset_top();


                }
                //zy 2014.12.17
            }else{
                self.model.get_info();
            }

        },
        reset_viewport_height : function()
        {
            return utility.get_view_port_height()-45;
        }

    });

    module.exports = model_card_view;
});