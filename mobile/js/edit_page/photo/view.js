define(function(require,exports,module){
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var view = require('../../common/view');
    var App = require('../../common/I_APP');
    var scroll = require('../../common/scroll');
    var utility = require('../../common/utility');
    var m_alert = require('../../ui/m_alert/view');
    var templateHelpers = require('../../common/template-helpers');


    var tpl = require('./main.handlebars');

    var edit_view = view.extend
    ({
        attrs:
        {
            template:tpl
        },
        templateHelpers :
        {
            if_equal : templateHelpers.if_equal
        },
        /**
         * 事件
         */
        events:
        {
            'tap [data-role=back]' : '_back',
            'tap [data-role="btn"]' : function()
            {
                var self = this;

                if(self.get('params_obj'))
                {
                    var upload_obj = self.get('params_obj').upload_obj;
                    var bind_view_scroll_obj = self.get('params_obj').bind_view_scroll_obj;


                    if(App.isPaiApp)
                    {

                        App.upload_img
                        ('multi_img',{
                            is_async_upload : 0,
                            max_selection : 1,
                            is_zip : 1

                        },function(data)
                        {
                            var pic_list=[];

                            if(data.imgs.length>0)
                            {
                                for(var i = 0;i<data.imgs.length;i++)
                                {
                                    console.log(data.imgs[i].url);

                                    var img = utility.matching_img_size(data.imgs[i].url,165);

                                    pic_list[0] = img;
                                }

                                var ori_img = utility.matching_img_size(data.imgs[0].url,640);

                                setTimeout(function()
                                {
                                    self.$id_pic.css('background-image','url('+ori_img+')');
                                },1000);

                                upload_obj.add_pic(pic_list,true);

                            }
                        });


                    }
                    else
                    {

                        var pic_list =
                            [
                                'http://image227-c.poco.cn/mypoco/myphoto/20140716/10/17371899920140716100604099_640.jpg'
                            ];

                        self.$id_pic.css('background-image','url('+pic_list[0]+')');

                        upload_obj.add_pic(pic_list,true);

                        //bind_view_scroll_obj.refresh();
                        //bind_view_scroll_obj.change_scroll_position();
                        //bind_view_scroll_obj.force_load_img();

                    }
                }
            }
        },


        _back: function()
        {
            page_control.back();
        },


        setup: function()
        {
            var self = this;

            self.$id_pic = self.$('[data-role="ID-pic"]');
        },

        _setup_events:function()
        {
            var self = this;



        },



        /**
         * 渲染模板
         * @returns {Editview}
         */
        render: function()
        {
            var self = this;

            view.prototype.render.apply(self);


            self.trigger('render');

            return self;
        }

    });

    module.exports = edit_view;
});