/**
 * Created by zy on 2014/9/26.
 *
 *滑动图片组件
 *
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var view = require('../../common/view');
    var utility = require('../../common/utility');
    var iscroll = require('../../common/new_iscroll');
    var slide_tpl = require('./tpl/slide.handlebars');

    var iscroll_slide = view.extend({
        attrs: {
            template: slide_tpl
        },

        events: {

        },

        /**
         * 构建element
         * @private
         */
        _parseElement : function() {
            var self = this;
            view.prototype._parseElement.apply(self);

        },

        _setup_iscroll : function(){
            var self = this;

            self.screen_width = utility.get_view_port_width()

            self.$ad_pic_point = self.$('[data-role=ad-pics-num]')//底部小点容器
            self.point_len = self.$ad_pic_point.children().length;

            self.options = $.extend({},self.options,{
                bounce: self.get('bounce')||  false,
                snap:  self.get('snap')|| true,
                snapThreshold :  self.get('snap_threshold')|| utility.get_view_port_width()/5,
                momentum: self.get('momentum')|| false,
                hScroll : self.get('h_scroll')||  true,
                vScroll : self.get('v_scroll')||  false,
                hScrollbar: self.get('h_scrollbar')||  false,
                vScrollbar: self.get('v_scrollbar')||  false,
                checkDOMChanges: self.get('check_dom_changes')||  true
            });

            self.$('[data-role="ad-pic"]').width(self.screen_width * self.point_len);
            self.$el.find('.swiper-slide').width(self.screen_width).height(self.get('height'));

            self._swiper = new iscroll(self.$el,self.options);


            self._swiper.on('scrollEnd',function()
            {
                var _self = this;

                if(!self.is_auto_slide){
                    self.slide_view_x = _self.x;
                    self.set_point_current(Math.abs(_self.x/self.screen_width));
                }

                self.is_auto_slide = false;

            });

            //是否自动播放
            (self.get('auto_play') > 0) && self.setup_auto_slider();


        },

        set_point_current : function(idx){
            var self = this;

            self.is_auto_slide = true;

            var $span = self.$ad_pic_point.find('span');

            $span.removeClass('swiper-visible-switch swiper-active-switch');

            $span.eq(idx).addClass('swiper-visible-switch swiper-active-switch');

            self.slide_view_pic_idx = idx;
            console.log(idx);
        },

        _setup_events:function(){
            var self = this;

            self.on('render',function(){
                //安装滚动(这个必需在渲染后安装，否则获取不到宽度)
                self._setup_iscroll();
            });


        },

        setup_auto_slider : function(){
            var self = this;
            self.slide_view_x = 0;//当前滚动图片X位置
            self.slide_view_pic_idx = 0;//当前滚动图片索引
            self.interval_running = false;

            self.slider_interval = setInterval(function(){
                self.auto_slider();
            },self.get('auto_play') || 3000);

        },


        auto_slider : function(){
            var self = this;

            if(self.slide_view_x == -(utility.get_view_port_width() * (self.point_len - 1))){
                self.slide_view_x = 0;
                self.slide_view_pic_idx = 0;
            }else{
                self.slide_view_x -=utility.get_view_port_width();
                self.slide_view_pic_idx++
            }


            self._swiper.scrollTo(self.slide_view_x,0,300);
            self.set_point_current(self.slide_view_pic_idx);


        },

        setup : function()
        {
            var self = this;

            self.$ad_pic = self.$('[data-role="ad-pic"]');
            self.is_auto_slide = false;
            // 安装事件
            self._setup_events();
        },

        render : function(pic_list)
        {
            var self = this;

            view.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },

        /**
         * 设置参数
         * @param options
         * @return {*}
         */
        set_options : function(options){
            var self = this;

            self.options = options;

            return self;

        },

        /**
         * 获得滚动图片组的长度
         * @returns {*}
         */
        get_length : function(){
            var self = this;
            return self.point_len;
        },


        clear_interval : function(){
            var self = this;
            self.interval_running = false;
            self.slider_interval && clearInterval(self.slider_interval);//删除自动播放
        },


        /**
         * 销毁
         * @returns {slide}
         */
        destroy : function() {
            var self = this;


            view.prototype.remove.call(self);
            view.prototype.destroy.call(self);

            return self;
        }
    });
    module.exports = iscroll_slide;
});
