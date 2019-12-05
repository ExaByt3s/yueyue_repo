/**
 * 我的zone 视图
 * hudw 2014.9.8
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var tpl = require('./tpl/model_main.handlebars');
    var utility = require('../../common/utility');
    var Scroll = require('../../common/scroll');
    var App = require('../../common/I_APP');
    var grid = require('../../widget/model_pic/view');
    var info_tpl = require('./tpl/model_info.handlebars');


    var mine_view = View.extend
    ({
        attrs:
        {
            template: tpl
        },
        events :
        {

            'tap [data-role="bar-swt"]' : function(ev)
            {
                var self = this;

                if(self.$style_list.hasClass('fn-hide'))
                {
                    self.$style_list.removeClass('fn-hide');
                    self.$bottom_info.addClass('fn-hide');
                    self.$bar_sty.addClass('bar-hover');
                }
                else
                {
                    self.$bottom_info.removeClass('fn-hide');
                    self.$style_list.addClass('fn-hide');
                    self.$bar_sty.removeClass('bar-hover');
                }

                self.view_scroll_obj.refresh();

            },

            'tap [data-role="page-back"]' : function()
            {
                var self = this;

                page_control.back();
            },
            'tap [data-role="edit"]' : function()
            {
                var self =this;
                page_control.navigate_to_page('model_date/model_card/edit_all');
            },
            'swiperight' : function()
            {
                page_control.back();
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
            'tap [data-role="pop-dabenxiang-model"]' : function(ev)
            {
                var self = this;
                $(ev.currentTarget).addClass('fn-hide');
                utility.storage.set("pop-dabenxiang-model",1)
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
            if(utility.storage.get('pop-dabenxiang-model'))
            {
                self.$('[data-role="pop-dabenxiang-model"]').addClass('fn-hide');
            }

            self._render_info();

            self.model.on('change', function() {
                    self._render_info();//监听编辑页修改
            });

            self.on('update_info',function(response,xhr)
            {

                // 区分当前对象
                var _self = this;

                if(!self.view_scroll_obj)
                {
                    //主要滚动条
                    self._setup_scroll();

                    self.view_scroll_obj.scrollTo(0,0);

                    self.view_scroll_obj.resetLazyLoad();

                    return;
                }

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
        },
        /**
         * 渲染内容
         * @param response
         * @private
         */
        _render_info : function(response)
        {
            var self = this;
            console.log(self.model.toJSON());
            var html_str = info_tpl(self.model.toJSON());

            self.$info_container.html(html_str);


            var art_imgs = [];
            var pic_arr = utility.user.get('pic_arr');

            for(var i=0;i < pic_arr.length; i++)
            {
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
                    'user_icon' : pic_arr[i].img,
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
                        tpl_type : '1l1m1r'
                    }
                }).render();

                self.$info_container.find('[data-role=grid-container]').html(grid_list_view.list());//塞头图片
            }

            self.trigger('update_info');

            self.$style_list = self.$('[data-role="style"]');
            self.$bottom_info = self.$('[data-role="bottom-info"]');
            self.$bar_sty = self.$('[data-role="bar-swt"]');

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



            // 安装事件
            self._setup_events();

            // 刷新
            self.refresh();

        },
        refresh : function()
        {
            var self = this;

            self.model.get_zone_info('model',utility.int(self.get('user_id')));

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