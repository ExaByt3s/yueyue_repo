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
    var swipe = require('../../common/swipe');
    var templateHelpers = require('../../common/template-helpers');
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

        _setup_swipe : function(){
            var self = this;

            self.$ad_pic_point = self.$('[data-role=ad-pic-point]')//底部小点容器
            self.point_len = self.$ad_pic_point.children().length;

            self._swipe = new swipe(self.$el[0], {
                disableScroll : self.options.disableScroll || true, // 停止滚动冒泡
                continuous : self.options.continuous || true ,// 无限循环的图片切换效果
                auto :  self.options.auto || 0,
                startSlide : self.options.startSlide || 0, //起始图片切换的索引位置
                callback : function(index, slideInfo) { // 回调函数，可以获取到滑动中图片的索引
                    var current_index = $(slideInfo).attr('data-index');
                    //用于修复swipe两张图片时的bug
                    if(self.point_len == 2 && index >= 2){
                        current_index = current_index -2;
                    }
                    self.$ad_pic_point.find('[data-index="' + current_index + '"]').addClass('current').siblings().removeClass('current');
                },
                transitionEnd: function(index, slideInfo) { // 在最后滑动转化是执行
                }
            });
        },

        _setup_events:function(){
            var self = this;

            self.on('render',function(){
                //安装滚动(这个必需在渲染后安装，否则获取不到宽度)
                self._setup_swipe();
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
