/**
 * 我的zone 视图
 * hudw 2014.9.8
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var tpl = require('./tpl/main.handlebars');
    var utility = require('../../common/utility');
    var Scroll = require('../../common/scroll');
    var info_tpl = require('./tpl/info.handlebars');
    var mine_model = require('../model');
    var mine_popup = require('../../ui/mine-popup/index');
    var grid = require('../../widget/model_pic/view');
    var App = require('../../common/I_APP');



    var mine_view = View.extend
    ({
        attrs:
        {
            template: tpl
        },
        events :
        {

            'tap [data-role="page-back"]' : function()
            {
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
            'swiperight' : function()
            {
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
            'tap [data-role="user-info"]' : function()
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

                self.model.on(['change:nickname','change:pic_arr','change:user_icon' ].join(' '), function()
                {
                    // 只有编辑自己时才触发
                    if(self.model.get('user_id') == utility.user.get('user_id'))
                    {
                        self._render_info(utility.user.toJSON());//监听编辑页修改
                        setTimeout(function(){

                            self.view_scroll_obj.reset_top();

                            self.view_scroll_obj.resetLazyLoad();

                            self.view_scroll_obj.refresh();

                        },500)
                    }

                })
                .on('success:get_zone_info:fetch', function(response)
                {
                    self._render_info(response.result_data.data);

                });

            self.on('update_info',function(response,xhr)
            {

                // 区分当前对象
                var _self = this;


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
        /**
         * 安装滚动条
         * @private
         */

        _setup_scroll : function()
        {
            var self = this;
            var view_scroll_obj = Scroll(self.$container,
                {
                    lazyLoad : true
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
            var html_str = info_tpl(data);

            self.$info_container.html(html_str);

            //var art_imgs = self.model.get('zone_info').model_pic;
            var art_imgs = [];
            var pic_arr = data && data.pic_arr;

            for(var i=0;i < pic_arr.length; i++){
                var type;
                if((i+2)%3==0)
                {
                    type = 'middle';
                }
                else
                {
                    type = 'one';
                }


                art_imgs.push({
                    'type' : type,
                    'user_icon' : pic_arr[i].user_icon,
                    'big_user_icon' : pic_arr[i].big_user_icon
                });
            }


            if(art_imgs.length>0)
            {

                var grid_list_view = new grid
                ({
                    // 模板参数对象
                    templateModel :
                    {
                        tpl_data : art_imgs,
                        tpl_type : 'one'
                    }
                    //no_use_lazyload : 1
                }).render();

                self.$info_container.find('[data-role=grid-container]').html(grid_list_view.list());//塞头图片
            }

            self.trigger('update_info');


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


            if(self.get('is_myself'))
            {
                self.$setup_btn.find('a').removeClass('fn-hide');

                self.model = utility.user;

            }
            else
            {
                self.model = new mine_model({user_id : self.get('user_id')});
            }

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
        }

    });

    module.exports = mine_view;
});