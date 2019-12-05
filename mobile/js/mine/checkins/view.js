/**
 * 首页 - 城市选择
 * 汤圆 2014.18.
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var checkins = require('../checkins/tpl/main.handlebars');
    var utility = require('../../common/utility');
    var templateHelpers = require('../../common/template-helpers');
    var Scroll = require('../../common/scroll');
    var utility = require('../../common/utility');
    var global_config = require('../../common/global_config');
    var m_alert = require('../../ui/m_alert/view');
   
    var checkins_view = View.extend
    ({

        attrs:
        {
            template: checkins
        },
        events :
        {
            'swiperight' : function (){
                page_control.back();
            },
            'tap [data-role=date-city]' : function (ev)
            {

                page_control.back();
            },
            'tap [data-role=page-back]' : function (ev)
            {
                page_control.back();
            }
            ,
            'tap [data-role=btn_confirm_2]' : function (ev)
            {
     
                var self = this;
                self.get_num = self.$('[data-role="get_num"]').val();
                if( $.trim(self.get_num) == '')
                {
                    m_alert.show('请输入活动券密码','right',{delay:1000});
                    return ;
                }
                self._send_code(self.get_num);
            }
        },

        /**
         * 安装事件
         * @private
         */
        _setup_events : function()
        {

            var self = this;
            
            self.on('update_list',function(response,xhr)
            {

                // 区分当前对象
                var _self = this;

                self._setup_scroll();
                self.view_scroll_obj.refresh();

                // 重置渲染队列
                self._render_queue = [];
            })
            .once('render',self._render_after,self);

            self._setup_scroll();
            self.view_scroll_obj.refresh();

        },
        _send_code : function (){
            var self = this;

            utility.ajax_request
                ({
                    url: global_config.ajax_url.verify_code,
                    type : 'POST',
                    data :
                    {
                        code : self.get_num
                    },
                    cache: false,
                    beforeSend: function (xhr, options)
                    {
                        m_alert.show('请求发送中...','loading',{delay:-1});
                        //self.trigger('before:login:fetch', xhr, options);
                    },
                    success: function (data)
                    {
                        console.log(data);
                        if ( data.result_data.code == 1) 
                        {
                            m_alert.show(data.result_data.msg,'right',{delay:3000});

                            page_control.navigate_to_page(data.result_data.type);

                        }
                        if ( data.result_data.code == 0) {
                            m_alert.show(data.result_data.msg,'error',{delay:3000});
                        }
                    },
                    error: function ( xhr, options)
                    {          
                         m_alert.show('请求发送失败，请重试','error',{delay:3000});
                        //self.trigger('error:login:fetch',  xhr, options);
                    },
                    complete: function (xhr, status)
                    {
                        //self.trigger('complete:login:fetch', xhr, status);
                    }
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
                is_hide_dropdown : true
            });
            self.view_scroll_obj = view_scroll_obj;
        },
        
        _reset : function()
        {
            var self = this;
            self.collection.length && self.collection.each(self._add_one,self);
        },
        _add_one : function(model)
        {
            var self = this;
          
            
        },
        
        /**
         * 渲染城市
         * @param response
         * @param options
         * @private
         */
         _render_city : function(response,xhr)
        {
            var self = this;

        },

        _render_after :function()
        {
            var self = this ;
           
        },
        /**
         * 视图初始化入口
         */
        setup : function()
        {

            var self = this;

            // 渲染队列
            self._render_queue = [];

            // 配置交互对象
            self.$container = self.$('[data-role=container]'); // 滚动容器
            self.$btn_confirm_2 = self.$('[data-role=btn_confirm_2]'); 
            

            // 安装事件
            self._setup_events();


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

    module.exports = checkins_view;
});