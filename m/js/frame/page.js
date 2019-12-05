define(function(require, exports)
{
    var $ = require('$');
     require('data');
    var ua = require('./ua');


    require('jquery.hammer');


    $(document.body).hammer();




    exports.new_page = function(options)
    {
        var Backbone = require('backbone');

        options || (options = {});
        var page_view_class = Backbone.View.extend
        ({
            tagName :  "div",
            className : "apps_page",
            title : options.title,
            manual_title : options.manual_title,
            dom_not_cache : options.dom_not_cache,
            transition_type : options.transition_type,
            ignore_exist : options.ignore_exist || false,
            without_his : options.without_his || false,
            events : options.events,
            render : options.render,
            page_show : options.page_show,
            page_before_show : options.page_before_show,
            page_back_show : options.page_back_show,
            page_init : options.page_init,
            window_change : options.window_change,
            page_before_hide : options.page_before_hide,
            page_hide : options.page_hide,
            page_before_remove : options.page_before_remove,
            page_lock : false,
            initialize : function()
            {
                var self = this;

                self.$cover = $('<div class="tran_cover fn-hide"></div>');

                //转场防事件穿透遮罩层  add by manson 2014.3.5
                self.$el.append(self.$cover);

                if(typeof(options.initialize)=="function")
                {
                    options.initialize.call(self);
                }
            },
            open_cover : function()
            {
                this.$cover.removeClass('fn-hide');
            },
            close_cover : function(delay)
            {
                var that = this;

                if (delay === -1) {
                    that.$cover.addClass('fn-hide');
                } else {
                    setTimeout(function(){
                        that.$cover.addClass('fn-hide');
                    }, delay || 300);
                }
            }
        });


        return new page_view_class;
    };
});