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
    var islider = require('../../common/islider');
    var slide_tpl = require('./tpl/slide.handlebars');

    var slide = view.extend({
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

        _setup_islider : function(){
            var self = this;

//            self.$ad_pic_point = self.$('[data-role=ad-pic-point]')//底部小点容器
//            self.point_len = self.$ad_pic_point.children().length;


            //self._swiper = new swiper(self.$el[0],{
            self._islider = new islider({
                type: 'dom',
                data: self.options.contents,
                dom: self.$el[0],
                isVertical: false,
                isLooping: true,
                isDebug: true,
                isAutoplay: false,
                animateType: 'default'
            });
        },

        _setup_events:function(){
            var self = this;

            self.on('render',function(){
                //安装滚动(这个必需在渲染后安装，否则获取不到宽度)
                self._setup_islider();
            });
        },

        setup : function()
        {
            var self = this;

            self.$ad_pic = self.$('[data-role="ad-pic"]')
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
    module.exports = slide;
});
