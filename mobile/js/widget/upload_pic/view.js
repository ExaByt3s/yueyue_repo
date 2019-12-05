/**
 * Created by zy on 2014/9/16.
 *
 *上传图片组件
 *
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var view = require('../../common/view');
    var utility = require('../../common/utility');
    var main_tpl = require('./tpl/main.handlebars');
    var item_tpl = require('./tpl/item.handlebars');

    var upload_pic = view.extend({
        attrs: {
            template : main_tpl,
            max_pic : 9,
            is_cover : 0,
            cover_max_size : '',
            cover_max_size_h : ''
        },

        events: {
            'tap [data-role=del-pic]': '_del_pic',
            'tap [data-role=add-pic]': '_add_pic'
        },

        /**
         * 构建element
         * @private
         */
        _parseElement: function() {
            var self = this;
            view.prototype._parseElement.apply(self);

            self.$add_btn = self.$('[data-role=add-pic]');

        },

        _del_pic : function(ev){
            var self = this;
            var $target = $(ev.currentTarget);
            var data_id = $target.attr('data-id');
            self.$('[data-item-id="' + data_id + '"]').remove();
            var amount = self.get_amount();
            if(amount < self.get('max_pic')){
                self.show_add_btn();
            }

            self.trigger('tap:del_upload_pic', $target, ev);

            return self;

        },

        _add_pic : function(ev){
            var self = this;
            var $target = $(ev.currentTarget);


            self.trigger('tap:upload_pic', $target, ev);
        },

        /**
         * /添加图片（供外部使用）
         * @param pic_list
         * @return {*}
         */
        add_pic : function(pic_list,is_reset){
            if(!pic_list){
                return;
            }

            var self = this;
            var _pic_list = [];

            //构造图片数组和总图片对象
            for(var i=0; i<pic_list.length; i++){
                var pic_index = self._get_random();
                _pic_list.push({
                    url : pic_list[i],
                    id : pic_index
                })
            }

            //封面图时使用用户设置的宽高

            if(self.get('is_cover')){
                self.w_h = self.get('cover_max_size');
            }


            var html_str = item_tpl({
                is_cover : self.get('is_cover'),
                pic_list:_pic_list,
                max_size:self.w_h,
                max_size_h : self.get('cover_max_size_h') || self.w_h
            })

            if(!is_reset)
            {
                self.$add_btn.before(html_str);
            }
            else
            {
                self.$('[data-role="pic"]').remove();
                self.$add_btn.before(html_str);
            }



            //封面图的情况
            if(self.get('is_cover')){
                self.hide_add_btn();
            }

            //超过最大上传数隐藏添加按钮
            if(self.get_amount() >= self.get('max_pic')){
                self.hide_add_btn();
            }

            self.trigger('rendered');

            return self;
        },

        show_add_btn : function(){
            var self = this;
            self.$add_btn.removeClass('fn-hide');
            return self;
        },

        hide_add_btn : function(){
            var self = this;
            self.$add_btn.addClass('fn-hide');
            return self;
        },

        /**
         * 获得图片总数
         * @return {*}
         */
        get_amount : function(){
            var self = this;

            var $pic_list = self.$('[data-role=pic]');

            return $pic_list.length;
        },

        /**
         * 获得图片数组
         * @return {Array}
         */
        get_value : function()
        {
            var self = this;

            var value_arr = [];


            var $pic_list = self.$('[data-role=pic]');

            $pic_list.each(function(i,obj)
            {
                value_arr.push
                (
                    $(obj).attr('data-url')
                )
            });

            return value_arr;
        },

        set_w_h : function(w_h){
            var self = this;
            self.w_h = w_h;
            return self;
        },

        /**
         * 获得一个随机数
         */
        _get_random : function () {
            return Math.random().toString().replace('.', '');
        },

        /**
         * 销毁
         * @returns {upload_pic}
         */
        destroy: function() {
            var self = this;


            view.prototype.remove.call(self);
            view.prototype.destroy.call(self);

            return self;
        }
    });

    module.exports = upload_pic;
});
