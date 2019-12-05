/**
 * Created by nolest on 2014/9/2.
 *
 *
 *
 * 活动介绍view
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var View = require('../../../common/view');
    var tpl = require('./tpl/main.handlebars');
    var utility = require('../../../common/utility');
    var Scroll = require('../../../common/scroll');
    var location_data = require('../../../common/location_data');
    var upload_pic = require('../../../widget/upload_pic/view');
    var App = require('../../../common/I_APP');
    var panel = require('../../../widget/panel_text_pic/view');
    var m_alert = require('../../../ui/m_alert/view');


    var pub_info_view = View.extend
    ({

        attrs:
        {
            template: tpl
        },
        forms :
        {
            select : {},
            model_pics : [],
            title_pic : []
        },
        events :
        {
            'swiperight' : function (){
                page_control.back();
            },
            'tap [data-role="page-back"]':function()
            {
                page_control.back();
            },
            'tap [data-role="add-title-pic"]' : function(ev)
            {
                var self = this;

                $(ev.currentTarget).addClass("fn-hide");

                self.$('[data-role="title-pic"]').removeClass("fn-hide");

                self.view_scroll_obj.refresh();

            },
            'tap [data-role="title-pic"]' : function(ev)
            {
                var self = this;

                $(ev.currentTarget).addClass("fn-hide");

                self.$('[data-role="add-title-pic"]').removeClass("fn-hide");

                self.view_scroll_obj.refresh();;
            },
            'tap [data-role="add_panel"]' : function()
            {
                var self = this;

                self._set_one_panel(self.$module_con);

                self.view_scroll_obj.refresh();

            },
            'tap [data-role="close-panel"]' : function(ev)
            {
                var self = this;

                $(ev.currentTarget).parent().parent().remove();

                self.view_scroll_obj.refresh();
            },
            'tap [data-role="next-step"]' : function()
            {
                var self = this;

                var info = self._mix_selected_value(self.selected_obj)

                console.log(info);

                //info.cover_image = 'a'
                if(!info.cover_image || info.cover_image == "")
                {
                    m_alert.show('封面图不能为空','error');

                    return
                }
                if(info.content.trim() == "")
                {
                    m_alert.show('请填写主题','error');

                    return
                }



                if(self.can_next)
                {
                    page_control.navigate_to_page("act/pub_arrange",info);
                }


            },
            'tap [data-role="page-back"]' :function()
            {
                page_control.back()
            }
        },
        _setup_events : function()
        {
            var self = this;

            self._setup_upload_pic(self.$title_pic_con,'one');

            self.model
                .on('success:fetch',function(response,options)
                {


                })
        },
        _mix_selected_value : function(obj)
        {
            var self = this;

            var theme = self.$theme.val();

            var all_panel = self.$('[data-role="component-panel-text-pic"]');

            var obj_arr = [];

            // 场地模特
            if(all_panel.length == 0)
            {
                m_alert.show('请增加场地/模特描述','error');

                return;
            }


            $.each(all_panel,function(i,obj)
            {

                var text = $(obj).children('[data-role="module-input"]').val().trim();

                var img_arr = self.forms['model_pics'][i];

                var img_obj_arr = [];

                var img_len = img_arr.get_amount();

                if(text == "")
                {
                    m_alert.show('描述介绍不能为空','error');

                    self.can_next = false;
                }
                else
                {
                    self.can_next = true;
                }

                for(var n =0;n<img_len;n++)
                {
                    img_obj_arr[n] =
                    {
                        img_s : img_arr.get_value()[n]
                    }

                }

                var pre_obj =
                {
                    text : text,
                    img : img_obj_arr
                };

                obj_arr.push(pre_obj)

            });



            // 封面图
            var title_pic = self.forms['title_pic'].get_value()[0];

            var value =
            {
                cover_image : title_pic,
                content : theme,
                other_info_detail : obj_arr
            };

            var info = $.extend(true,{},self.selected_obj,value);



            return info

        },
        _setup_scroll : function()
        {
            var self = this;
            var view_scroll_obj = Scroll(self.$container,
                {
                    lazyLoad : true
                });
            self.view_scroll_obj = view_scroll_obj;
        },
        reset_viewport_height : function()
        {
            return utility.get_view_port_height('nav') - 50;
        },
        _set_one_panel : function(parentNode)
        {
            var self = this;

            self.module_panel = new panel
            ({
                parentNode:parentNode
            }).render();

            self.module_panel.set({
                'upload_pic_view' :  self._setup_upload_pic(self.module_panel.$('[data-role=picture-list]'),'more')
            })

            //self._setup_upload_pic(self.module_panel.$('[data-role=picture-list]'));
        },


        /**
         * 安装上传图片组件
         * @param parent_node
         * @param type
         * @private
         */
        _setup_upload_pic : function(parent_node,type){

            var self = this;
            var pic_w_h = Math.ceil(((utility.get_view_port_width() - 72) / 3),10);
            var is_cover = 0;

                if(type == 'one'){
                    is_cover = 1;
                }


            var upload_pic_view = new upload_pic({
                templateModel :
                {
                    max_size : pic_w_h//设置方图的宽高（这里为设置添加按钮宽高）
                },
                parentNode : parent_node,
                is_cover : is_cover,
                max_pic : 5,
                cover_max_size : 150
            }).set_w_h(pic_w_h).render();



            upload_pic_view.on('tap:upload_pic',function(){

               /* var pic_list = [
                    'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_145.jpg'
                ]

                upload_pic_view.add_pic(pic_list);
                self.view_scroll_obj.refresh();
                return;*/
                var max_selection = 5 - upload_pic_view.get_amount();
                if(is_cover){
                    max_selection = 1
                }


                if(App.isPaiApp)
                {
                    App.upload_img
                    ('multi_img',{
                        is_async_upload : 0,
                        is_multiple_upload : is_cover,
                        //max_selection : 5 - upload_pic_view.get_amount(),
                        max_selection : max_selection,
                        is_zip : 1

                    },function(data)
                    {
                        var pic_list=[];

                        if(data.imgs.length>0)
                        {
                            for(var i = 0;i<data.imgs.length;i++)
                            {
                                pic_list.push(data.imgs[i].url);
                            }

                            upload_pic_view.add_pic(pic_list);

                            self.view_scroll_obj.refresh();
                        }
                    });
                }
                else
                {
                    upload_pic_view.add_pic(['http://image226-c.poco.cn/mypoco/myphoto/20140421/15/5524685720140421151001080_640.jpg?1024x684_120']);

                    self.view_scroll_obj.refresh();

                    self.view_scroll_obj.change_scroll_position();
                }



            });

            if(is_cover)
            {
                self.forms['title_pic'] = upload_pic_view;
            }
            else
            {
                self.forms['model_pics'].push(upload_pic_view);
            }


        },
        setup : function()
        {
            var self = this;

            self.$container = self.$('[data-role="action-intro-content"]'); // 滚动容器

            self.$title_pic_con = self.$('[data-role="title-pic-con"]');

            self.$module_con = self.$('[data-role="module-con"]');

            self.$theme = self.$('[data-role="theme"]');

            self._setup_events();

            self._setup_scroll();

            self.can_next = false;

            self.view_scroll_obj.refresh();

            self.selected_obj = self.get("selected_obj");

            self._set_one_panel(self.$module_con);

        },
        render : function()
        {
            var self = this;

            var view_port_height = self.reset_viewport_height();

            self.$container.height(view_port_height);

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        reset_scroll_height : function()
        {
            var self = this;

            var view_port_height = self.reset_viewport_height();

            self.$container.height(view_port_height);

            self.view_scroll_obj.refresh();
        }

    });

    module.exports = pub_info_view;
});