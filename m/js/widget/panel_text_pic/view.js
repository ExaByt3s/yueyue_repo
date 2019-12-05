/**
 * Created by nolest on 2014/9/3.
 *
 *
 *
 *
 *
 *  介绍页'添加模特'模块
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var utility = require('../../common/utility');
    var tpl = require('./tpl/main.handlebars');

    module.exports = View.extend
    ({
        attrs :
        {
            template : tpl,
            inset_node : '',
            upload_pic_view:''
        },
        events :
        {


        },/*
        _parseElement : function()
        {
            var self = this;

            var template_model = self.get('templateModel');

            self.set('templateModel', template_model);

            View.prototype._parseElement.apply(self);

        },*/
        _setup_events : function()
        {

        },
        setup : function()
        {
            var self = this;

            //设置被插入的html节点
            self.inset_node = self.$('[data-role=picture-list]')

            // 安装事件
            self._setup_events();

        },
        show : function()
        {
            var self = this;

            self.$el.removeClass('fn-hide');
        },
        hide : function()
        {
            var self = this;

            self.$el.addClass('fn-hide');
        },

        set_upload_pic_view : function(){
            var self = this;
            self.upload_pic_view =''

            return self;
        }
    });
});
