/**
 * 我的zone 视图
 * hudw 2014.9.8
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var utility = require('../../common/utility');
    var Scroll = require('../../common/scroll');
    var mine_popup = require('../../ui/mine-popup/index');
    var slide_v2 = require('../../widget/slide_v2/view');
    var m_alert = require('../../ui/m_alert/view');
    var mine_model = require('../model');
    var grid = require('../../widget/model_pic/view');
    var App = require('../../common/I_APP');
    var global_config = require('../../common/global_config');
    var follow = require('../../widget/follow/index');

    var main_tpl = require('./tpl/main_v2.handlebars');
    var info_tpl = require('./tpl/info_v2.handlebars');

    var mine_view = View.extend
    ({
        attrs:
        {
            template: main_tpl
        },
        events :
        {

            'tap [data-role="page-back"]' : function()
            {
                var self = this;
                self.model.off(['change:nickname','change:pic_arr','change:user_icon' ].join(' '));
                page_control.back();
            },
            'tap [data-role="show-pop"]' : function()
            {
                var self = this;

                if(!self.get('is_myself'))
                {
                    return;
                }

                if(!utility.auth.is_login())
                {
                    alert('尚未登录');

                    return;
                }

                if(!self.mine_Popup){
                    self.mine_Popup = new mine_popup({
                        uid:utility.user.get('user_id'),
                        items :  {
                            edit:true,
                            report:false
                        }
                    }).show();
                }else{
                    self.mine_Popup.show();
                }

            },
            'swiperight' : function(ev)
            {
                var self = this;

                if($(ev.target.offsetParent).attr('data-prevent-scroll') == 'slider')
                {
                    return;
                }

                self.model.off(['change:nickname','change:pic_arr','change:user_icon' ].join(' '));

                page_control.back();
            },
            'tap [data-role="follow"]' : function()
            {
                var self = this;
                page_control.navigate_to_page('account/fans_follows/'+self.get('user_id')+'/follow');
            },

            'tap [data-role="fans"]' : function()
            {
                var self = this;
                page_control.navigate_to_page('account/fans_follows/'+self.get('user_id')+'/fans');
            },
            'tap [data-role="profile"]' : function()
            {
                var self = this;

                // 只有点击自己的头像才是修改
                // hudw 2014.12.4
                if(self.get('user_id') == utility.user.get('user_id'))
                {
                    page_control.navigate_to_page('mine/profile');
                }


            },

            'tap [data-role="go-comment"]' : function()
            {
                var self = this;
                console.log(utility.user.get("role"));
                page_control.navigate_to_page('comment/list/cameraman/'+self.get('user_id')+'/0');

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

                console.log(data);

                if(!App.isPaiApp)
                {
                    console.log('no App');

                    return;
                }

                App.show_alumn_imgs(data);
            },
            //大笨象弹窗口
            'tap [data-role="pop-dabenxiang-cameraman"]' : function(ev)
            {
                var self = this;
                $(ev.currentTarget).addClass('fn-hide');
                utility.storage.set("pop-dabenxiang-cameraman",1)
            },
            /**
             * 约拍过的模特
             * @param ev
             */
            'tap [data-role="nav-to-model-card"]' : function(ev)
            {
                var user_id = $(ev.currentTarget).attr('data-user-id');

                if(App.isPaiApp)
                {
                    App.nav_to_app_page
                    ({
                        page_type : 'model_card',
                        user_id : user_id
                    });
                }
                else
                {
                    page_control.navigate_to_page('model_card/'+user_id);
                }

            },
            /**
             * 去评论列表
             */
            'tap [data-role="nav-to-comment-list"]' : function()
            {
                var self = this;

                page_control.navigate_to_page('comment/list/cameraman/'+self.get('user_id'))
            },
            /**
             * 关注按钮
             * @param ev
             */
            'tap [data-role="focus"]' :function(ev)
            {
                var self = this;

                // app 统计事件
                App.isPaiApp && App.analysis('eventtongji',global_config.analysis_event['cameraman_follow']);

                if(!utility.auth.is_login())
                {
                    page_control.navigate_to_page('account/login');

                    return;
                }

                if(self.get("user_id") == utility.user.get('user_id'))
                {
                    m_alert.show('自己不能关注自己哦','right');

                    return;
                }

                if(self.is_follow_ing)
                {
                    return;
                }


                var $cur_btn = $(ev.currentTarget);

                var type = $cur_btn.attr('data-focus-type')

                var data;

                switch(type)
                {
                    case 'to_focuse':
                        data = {type : 'follow',be_follow_user_id : self.get("user_id")};
                        m_alert.show('关注中...','right',{delay : 1000});
                        self.follow_request(data);
                        break;
                    case 'focused':
                        data = {type : 'no_follow',be_follow_user_id : self.get("user_id")};
                        m_alert.show('取消关注中...','right',{delay : 1000});
                        self.follow_request(data);
                        break;
                    case 'each':
                        data = {type : 'no_follow',be_follow_user_id : self.get("user_id")};
                        m_alert.show('取消关注中...','right',{delay : 1000});
                        self.follow_request(data);
                        break;
                }
            },
            'tap [data-role="msg"]' : function()
            {
                var self = this;

                // app 统计事件
                App.isPaiApp && App.analysis('eventtongji',global_config.analysis_event['cameraman_chat']);

                if(self.get("user_id") == utility.user.get('user_id'))
                {
                    m_alert.show('自己不能和自己聊天哦','right');

                    return;
                }

                var data =
                {
                    senderid : utility.login_id,
                    receiverid : utility.int(self.model.get('user_id')),
                    sendername : utility.user.get('nickname'),
                    receivername : self.model.get('nickname'),
                    sendericon : utility.user.get('user_icon'),
                    receivericon : self.model.get('user_icon')
                };

                console.log(data);

                if(!App.isPaiApp)
                {
                    console.warn('no App');

                    return;
                }

                App.chat(data);
            }


        },

        /**
         * 安装事件
         * @private
         */
        _setup_events : function()
        {

            var self = this;

            //大笨象
            /*if(utility.storage.get('pop-dabenxiang-cameraman'))
             {
             self.$('[data-role="pop-dabenxiang-cameraman"]').addClass('fn-hide');
             }*/

            self.model.on(['change:nickname','change:pic_arr','change:user_icon','change:cover_img','change:intro','change:location_id'].join(' '), function(){
                // 只有编辑自己时才触发
                if(self.model.get('user_id') == utility.user.get('user_id'))
                {
                    var change_data = utility.user.toJSON();

                    //构造轮播图数据
                    if(this.changed.pic_arr){
                        var pic_arr_len = change_data.pic_arr.length
                        for(var i=0;i<pic_arr_len;i++){
                            if(i == 0){
                                change_data.pic_arr[i].type = 'double';
                            }else{
                                change_data.pic_arr[i].type = 'one';
                            }
                        }
                        change_data.new_pic_arr = [];

                        if(pic_arr_len > 0)
                        {
                            // modify by hudw
                            // hudw 2015.3.10
                            change_data.new_pic_arr[0] = change_data.pic_arr.slice(0,3);

                            if(pic_arr_len>3)
                            {
                                change_data.new_pic_arr[1] = change_data.pic_arr.slice(3,9);
                            }

                            if(pic_arr_len>9)
                            {
                                change_data.new_pic_arr[2] = change_data.pic_arr.slice(9,15);
                            }

                        }


                    }

                    self._render_info(change_data);//监听编辑页修改

                    setTimeout(function(){

                        self.view_scroll_obj.reset_top();

                        self.view_scroll_obj.resetLazyLoad();

                        self.view_scroll_obj.refresh();

                    },500)
                }
            })
            .on('before:get_zone_info:fetch',self._before_get_zone_info,self)
            .on('success:get_zone_info:fetch',self._success_get_zone_info,self)
            .on('error:get_zone_info:fetch',self._error_get_zone_info,self);

            self.on('update_info',function(response)
            {

                // 区分当前对象
                var _self = this;

                self._setup_slide(response);


                if(!self.view_scroll_obj)
                {
                    //主要滚动条
                    self._setup_scroll();
                    self._drop_reset();
                    self.view_scroll_obj.reset_top();


                }


                self._drop_reset();
                self.view_scroll_obj.resetLazyLoad();
                self.view_scroll_obj.reset_top();
                self.view_scroll_obj.refresh();


            });



        },

        _before_get_zone_info : function(){
            m_alert.show('加载中', 'loading', {
                delay: -1
            });
        },

        _success_get_zone_info : function(response){
            var self = this;
            var cameraman_data = response.result_data.data;
            self._render_info(cameraman_data);

            //头像数等于2时调整布局
            if(cameraman_data.date_log && cameraman_data.date_log.length == 2)
            {
                self.$('[data-role="ph_contain"]').css('-webkit-box-pack','start');

                self.$('[data-role="nav-to-model-card"]').css('margin-right','10px')
            }
            m_alert.hide();
        },

        _error_get_zone_info : function(){

            m_alert.show('加载出错', 'loading', {
                delay: 800
            });
        },

        /**
         * 安装轮播滚动
         * @private
         */

        _setup_slide : function(data){

            var self = this;



            var contents = [];

           /* if(data){
                var group_render_queue = data.new_pic_arr;
            }else{
                var group_render_queue = self.model.toJSON().new_pic_arr;
            }*/
            var group_render_queue = data.new_pic_arr;

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

            self.slide_view = new slide_v2({
                templateModel :
                {
                    contents : contents,
                    no_single : no_single,
                    height:grid_list_view && grid_list_view._get_size_by_type('double').height
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
            },50);



        },
        /**
         * 安装滚动条
         * @private
         */

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
                self.refresh();

            });

        },
        /**
         * 渲染内容
         * @param response
         * @private
         */
        _render_info : function(data)
        {

            var self = this;

            var show_follow = true;

            if(self.get('user_id') == utility.user.get('user_id'))
            {
                show_follow = false;
            }

            data.show_follow = show_follow;

            data.is_myself = self.get('is_myself');

            // 设置标题
            self.$user_title.html(data.nickname || '');

            // 处理分数过大
            if(data.date_log && data.date_log.length>0)
            {
                var re = /([0-9]+.[0-9]{1})[0-9]*/;

                for(var i=0;i<data.date_log.length;i++)
                {
                    var model_obj = data.date_log[i];
                    var model_core = utility.int(model_obj.score);

                    if(model_core >=1000)
                    {
                        model_obj.score = Math.round(model_core)/1000 + "K";
                    }
                    else if( model_core >=10000)
                    {
                        model_obj.score = Math.round(model_core*10)/1000 + "W";
                    }

                    if(model_obj.score)
                    {
                        model_obj.score = model_obj.score.replace(re,"$1");
                    }



                }
            }

            var html_str = info_tpl(data);

            self.$info_container.html(html_str);

            if(self.get('user_id') != utility.user.get('user_id'))
            {
                // 插入关注按钮
                self.follow_obj = new follow
                ({
                    parentNode : self.$info_container.find('[data-role="follow-wrapper"]'),
                    follow_status : data.is_follow
                }).render();


            }



            // 安装关注事件
            self.follow_obj && self.follow_obj.on('success:fetch_follow',function(response)
            {
                var res = response.result_data;

                self.is_follow_ing = false;

                if(res.code>0)
                {
                    self.follow_obj._render_item({key : res.is_follow});
                }

            }).on('error:fetch_follow',function()
            {
                var self = this;

                self.is_follow_ing = false;

                m_alert.show('网络异常','error');
            });

            self.trigger('update_info',data);


        },
        /**
         * 视图初始化入口
         */
        setup : function()
        {

            var self = this;

            // 配置交互对象
            self.$container = self.$('[data-role=container]'); // 滚动容器
            self.$info_container = self.$('[data-role="info"]'); // 空间信息容器
            self.$setup_btn = self.$('[data-role="show-pop"]');
            self.$user_title = self.$('[data-role="user-title"]');


            if(self.get('is_myself'))
            {
                self.$('[data-role="show-pop"]').removeClass('fn-hide');

                self.model = utility.user;

            }
            else
            {
                self.$('[data-role="show-pop"]').addClass('fn-hide');

                self.model = new mine_model({user_id : self.get('user_id')});
            }

            self.model.off('success:get_zone_info:fetch');

            // 安装事件
            self._setup_events();

            // 刷新
            self.refresh();

        },
        _drop_reset : function()
        {
            var self = this;

            self.view_scroll_obj && self.view_scroll_obj.resetload();
        },
        refresh : function()
        {
            var self = this;

            self.model.get_zone_info('cameraman',self.get('user_id'));



        },
        render : function()
        {
            var self = this;

            // 调用渲染函数
            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        follow_request : function(data)
        {
            var self = this;

            self.follow_obj.follow_action(data);
        }

    });

    module.exports = mine_view;
});