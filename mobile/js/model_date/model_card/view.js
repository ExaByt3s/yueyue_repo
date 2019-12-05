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
    var slide = require('../../widget/slide/view');
    var slide_v2 = require('../../widget/slide_v2/view');
    var grid = require('../../widget/model_pic/view');
    var App = require('../../common/I_APP');
    var global_config = require('../../common/global_config');
    var m_alert = require('../../ui/m_alert/view');
    var mine_popup = require('../../ui/mine-popup/index');
    var abnormal = require('../../widget/abnormal/view');

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

                // app 统计事件
                App.isPaiApp && App.analysis('eventtongji',global_config.analysis_event['model_date']);

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
            'tap  [data-role="to_more_style"]' : function()
            {
                var self = this;

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

                // app 统计事件
                App.isPaiApp && App.analysis('eventtongji',global_config.analysis_event['model_chat']);

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


                self.is_from_date = false;

                self.model.judge_can_date({ model_user_id : self.model.get('user_id') , btn_type:'chat'});


            },
            'tap [data-role=page-back]' : function (ev)
            {
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
            'tap [data-role="focus"]' :function(ev)
            {
                var self = this;

                // app 统计事件
                App.isPaiApp && App.analysis('eventtongji',global_config.analysis_event['model_follow']);

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
            'tap [data-role="go_fans"]' : function()
            {
                var self = this;

                page_control.navigate_to_page('account/fans_follows/'+self.model.get("user_id")+'/fans');
            },
            'tap [data-role="level"]' : function()
            {
                var self = this;

                page_control.navigate_to_page('mine/explain/lev');
            },
            'tap [data-role="jifen"]' : function()
            {
                var self = this;

                page_control.navigate_to_page('mine/explain/charm');
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

                $total_alumn_img.each(function(i,obj)
                {
                    total_alumn_img_arr.push
                    ({
                        url : $(obj).attr('data-alumn-img'),
                        text : ''
                    });
                });

                // 当前图片索引
                var index = $total_alumn_img.index($cur_btn);

                // 轮播图数据
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

                App.show_alumn_imgs(data);
            },
            'tap [data-role="nav-to-comment"]' : function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var user_id = self.model.get('user_id');

                page_control.navigate_to_page('comment/list/model/'+user_id+'/0');
            },
            'tap [data-role="menu"]' : function()
            {
                // 弹出层设置

                var self = this;

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
            'tap [data-role="cam_icon"]':function(ev)
            {
                var self = this;

                var $cur_btn = $(ev.currentTarget);

                var cam_id = $cur_btn.attr('data-cam_id')

                page_control.navigate_to_page("zone/"+ cam_id + "/cameraman")
            },
            'tap [data-role="share"]' : function()
            {
                var self = this;

                console.log(self.share_data)

                if(App.isPaiApp)
                {
                    App.share_card(self.share_data,
                        function(data)
                        {
                            console.log(data);

                        }
                    )
                }
            }
            
        },
        _setup_events : function()
        {
            var self = this;

            
            self.model
                .on('before:fetch', function(response,options)
                {
                    m_alert.show('加载中...','loading',{delay:-1});
                })
                .on('success:fetch', function(response,options)
                {
                    m_alert.hide();

                    self.$container.removeClass('fn-hide');

                    self.abnormal_view && self.abnormal_view.hide();

                    self._insert_data();
                    //安装轮播
                    self._setup_slide();

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
                .on('error:fetch', function (response,options)
                {
                    m_alert.hide();

                    App.get_netstatus(function(data)
                    {
                        if(data.type && data.type == 'off')
                        {
                            self.$container.addClass('fn-hide');

                            if(!self.abnormal_view)
                            {
                                self.abnormal_view = new abnormal({
                                    templateModel :
                                    {
                                        broken_network : 1,
                                        content_height : utility.get_view_port_height('all') - 75
                                    },
                                    parentNode: self.$big_container
                                }).render();

                                self.abnormal_view.on('tap:broken_network',function()
                                {
                                    self.get_model_info();
                                });
                            }
                            else
                            {
                                self.abnormal_view.show();
                            }
                        }


                    });

                })
                .on('success:fetch_follow',function(response,options)
                {
                    self._change_focus_type(response);

                    var fans_obj = self.$('[data-role="be_follow_count"]');

                    if(response.result_data.code != 0)
                    {
                        //粉丝数加减隐藏 2015-1-6 nolest
                        //fans_obj.html(utility.int(fans_obj.html()) + 1);
                    }

                    m_alert.show(response.result_data.msg,'right',{delay : 1000});
                })
                .on('error:fetch_follow',function(response,options)
                {
                    m_alert.show('关注失败！','error',{delay : 1000});
                })
                .on('complete:fetch_follow',function(response,options)
                {

                })
                .on('success:fetch_unfollow',function(response,options)
                {
                    self._change_focus_type(response);

                    var fans_obj = self.$('[data-role="be_follow_count"]');

                    //粉丝数加减隐藏 2015-1-6 nolest
                    //fans_obj.html(utility.int(fans_obj.html()) - 1);

                    m_alert.show(response.result_data.msg,'right',{delay : 1000});
                })
                .on('error:fetch_unfollow',function(response,options)
                {
                    m_alert.show('取消失败！','error',{delay : 1000});
                })
                .on('complete:fetch_unfollow',function(response,options)
                {

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

                        if(utility.user.get('role') == 'cameraman')
                        {
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

                        console.log('go to chat');

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
                });


            self.on('render',function()
            {
                self.get_model_info();

                if(utility.storage.get('pop-max-dabenxiang') || self.preview_data)
                {
                    self.$('[data-role="pop-max-dabenxiang"]').addClass('fn-hide');
                }
            });
        },
        _change_focus_type : function(response)
        {
            var self = this;

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

            //用于预览
            if(data){
                data.is_preview = true;
                model_data = data;
            }


            switch (model_data.level_require)
            {
                case '1': model_data = $.extend(true,{},model_data,{ level_r_1: true});break;
                case '2': model_data = $.extend(true,{},model_data,{ level_r_2: true});break;
                case '3': model_data = $.extend(true,{},model_data,{ level_r_3: true});break;
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

            //头像数等于2时调整布局
            if(model_data.date_log && model_data.date_log.length == 2)
            {
                self.$('[data-role="ph_contain"]').css('-webkit-box-pack','start');

                self.$('[data-role="cam_icon"]').css('margin-right','10px')
            }

            var html_str = starts_tpl
            ({
                data : model_data.comment_stars_list
            });

            self.$('[data-role="starts"]').html(html_str);

            if(utility.login_id == self.model.get('user_id'))
            {
                self.$focus_contain = self.$('[data-role="focus-contain"]');

                self.$focus_contain.addClass("fn-hide");
            }

            self.$items_container.find('[data-role="model-id"]').html(self.get('user_id'));
            // 显示模特卡名字 hudw 2014.11.21
            self.$('[data-role="moteka-title"]').html(utility.user.get('nickname'));

            self.$('[data-role="share"]').removeClass("fn-hide");

            if(model_data.share_text)
            {
                var share = model_data.share_text;

                self.share_data =
                {
                    url : share.url,
                    pic : share.img,
                    userid : model_data.user_id,
                    title: share.title,
                    content: share.content,
                    sinacontent : share.sina_content,
                    qrcodeurl: share.qrcodeurl

                };
            }




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

            self.$big_container = self.$('[data-role=container]');

            self.$items_container = self.$('[data-role="items-container"]');

            self.$container_img_list = self.$('[data-role="container-img-list"]');


            self.$container.height(self.reset_viewport_height());

            self.$container_img_list.height(container_height);

            self.can_pai = false;

            self._setup_events();

            if(self.get('templateModel').is_preview)
            {
                self.$('[data-role="share"]').addClass('fn-invisible');
            }

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

//            self.slide_view = new slide({
//                templateModel :
//                {
//                    contents : contents,
//                    no_single : no_single
//                },
//                parentNode : self.$slide_out
//            }).set_options({
//                    disableScroll : true, // 停止滚动冒泡
//                    continuous : true, // 无限循环的图片切换效果
//                    //auto : 0,
//                    startSlide : 0 //起始图片切换的索引位置
//                }).render();

            self.slide_view = new slide_v2({
                templateModel :
                {
                    contents : contents,
                    no_single : no_single,
                    height:grid_list_view && grid_list_view._get_size_by_type('double').height //因为插件是拿到整个的高度设置，所以要减去下面小点的div高度
                },
                parentNode:self.$slide_out
            }).set_options({
                    //loop : true,
                    grab_cursor : true,
                    pagination_clickable : true
                    //autoplay : 1000
                }).render();

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