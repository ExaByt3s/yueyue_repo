/**
 * 首页 - 城市选择
 * 汤圆 2014.18.
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../frame/page_control');
    var View = require('../common/view');
    var comment = require('../comment/tpl/main.handlebars');
    var utility = require('../common/utility');
    var templateHelpers = require('../common/template-helpers');
    var Scroll = require('../common/new_iscroll');
    var utility = require('../common/utility');
    var cameraman_part = require('../comment/tpl/cameraman.handlebars');
    var event_part = require('../comment/tpl/event.handlebars');
    var model_part = require('../comment/tpl/model.handlebars');

    var global_config = require('../common/global_config');
    var m_alert = require('../ui/m_alert/view');
    var App = require('../common/I_APP');


    var comment_view = View.extend
    ({

        attrs:
        {
            template: comment
        },
        events :
        {
            'swiperight' : function (){
                page_control.back();
            },
            'tap [data-role=date-city]' : function (ev)
            {
                var $current_target = $(ev.currentTarget);

                page_control.back();
            },
            'tap [data-role=page-back]' : function (ev)
            {
                page_control.back();
            },
            'tap [data-role=publish]' : function (ev)
            {
                var self = this;


                    switch (self.role)
                    {
                        case 'cameraman':
                            self.send_data_cameraman();
                            break;

                        case 'model':
                            self.send_data_model();
                            break;

                        case 'event':
                            self.send_data_event();
                            break;
                    }

            },
            'tap [data-role=btn-total]' : function (ev)
            {
                var self = this;
                self.total = self.get_start(ev,self.$('[data-role=total]'));
                //console.log(self.total);
            },
            'tap [data-role=rp]' : function (ev)
            {
                var self = this;
                self.rp = self.get_start(ev,self.$('[data-role=rp]'));
                //console.log(self.total);
            },
            'tap [data-role=time_right]' : function (ev)
            {
                var self = this;
                var $cur_btn = $(ev.currentTarget);

                self.$('[data-role=time_right]').removeClass('current');

                if( $cur_btn.hasClass('current'))
                {
                    $cur_btn.removeClass('current')
                    
                }else{
                    $cur_btn.addClass('current')
                }
                self.time_right = $cur_btn.attr('data');
            },
            'tap [data-role=data-name]' : function (ev)
            {
                var self = this;
                if(self.config){
                    $(ev.currentTarget).removeClass('ui-switch-off');
                    $(ev.currentTarget).addClass('ui-switch-on');
                    self.config = false ;
                }else{
                    $(ev.currentTarget).addClass('ui-switch-off');
                    $(ev.currentTarget).removeClass('ui-switch-on');
                    self.config = true ;
                }
            },

            'tap [data-role=bxl]' : function (ev)
            {
                var self = this;
                self.bxl = self.get_start(ev,self.$('[data-role=bxl]'));
                //console.log(self.total);
            },
            'tap [data-role=zsx]' : function (ev)
            {
                var self = this;
                var $cur_btn = $(ev.currentTarget);

                self.$('[data-role=zsx]').removeClass('current');

                if( $cur_btn.hasClass('current'))
                {
                    $cur_btn.removeClass('current')
                    
                }else{
                    $cur_btn.addClass('current')
                }
                self.zsx = $cur_btn.attr('data');
            },

            'tap [data-role=event_zjnl]' : function (ev)
            {
                var self = this;
                self.event_zjnl = self.get_start(ev,self.$('[data-role=event_zjnl]'));
                //console.log(self.total);
            },

            'tap [data-role=event_mtsp]' : function (ev)
            {
                var self = this;
                self.event_mtsp = self.get_start(ev,self.$('[data-role=event_mtsp]'));
                //console.log(self.total);
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
            self.role = self.attrs.role ;
            self.date_id = self.attrs.date_id ;
            self.role_id = self.attrs.role_id ;
            self.table_id = self.attrs.table_id ;
            


            // 渲染队列

            // 配置交互对象
            self.$container = self.$('[data-role=container]'); // 滚动容器
            self.$hot_city_container = self.$('[data-role=hot-city-container]'); 
            self.$role_html = self.$('[data-role=role-html]'); 
            self.$title = self.$('[data-role=title]'); 
            self.$publish = self.$('[data-role=publish]'); 
            self.$data_txt_val = self.$('[data-role=data-txt]'); 
            self.$data_name = self.$('[data-role=data-name]'); 
            self.config = true ;

            // 安装事件
            self._setup_events();
            self._role_html();
        },

        _role_html : function()
        {
            var self = this;
            switch (self.role)
            {
                case 'cameraman':
                    self.$role_html.html(cameraman_part);
                    self.$title.html('摄影师评价');
                break;

                case 'model':
                    self.$role_html.html(model_part);
                    self.$title.html('模特评价');
                break;

                case 'event':
                    self.$role_html.html(event_part);
                    self.$title.html('活动评价');
                break;

            }
        },

        send_data_cameraman : function()
        {
            var self = this;

            self.total = self.total ? self.total : 0 ; //总体评价
            self.rp_cameraman = self.rp ? self.rp : 0 ; //rp
            self.data_txt_val =  $.trim( self.$data_txt_val.val() ) ; //评价内容 

            if( self.total == 0)
            {
                m_alert.show('总体评价还没评分哦！','error',{delay:1000});
                return ;
            }

            if( self.rp_cameraman == 0)
            {
                m_alert.show('RP还没评分哦！','error',{delay:1000});
                return ;
            }

            if( !self.time_right)
            {
                m_alert.show('还没选择时间观念哦！','error',{delay:1000});
                return ;
            }

            if( self.data_txt_val == '')
            {
                m_alert.show('请输入内容哦！','error',{delay:1000});
                return ;
            }

            if( self.config )
            {
                self.data_name = 0;
            }else{
                self.data_name = 1;
            }

            var data = {
                date_id: self.date_id,
                model_user_id : utility.login_id ,
                cameraman_user_id : self.role_id ,
                overall_score : self.total ,
                rp_score : self.rp_cameraman,
                time_sense : self.time_right,
                comment : self.data_txt_val ,
                is_anonymous : self.data_name
            }
            console.log(data);
             utility.ajax_request
                ({
                    url: global_config.ajax_url.add_cameraman_comment,
                    type : 'POST',
                    data : data ,
                    cache: false,
                    beforeSend: function (xhr, options)
                    {
                        m_alert.show('请求发送中...','loading',{delay:1000});
                    },
                    success: function (data)
                    {

                        if ( data.result_data.code == 1 )
                        {   console.log(444);

                            m_alert.show(data.result_data.msg,'right',{delay:1000});

                            if(self.from_app)
                            {
                                App.app_back();

                            }
                            else
                            {
                                page_control.navigate_to_page('comment/list/'+self.role+'/'+self.role_id+"/1");

                            }
                        }

                        if ( data.result_data.code == 0 )
                        {
                            m_alert.show(data.result_data.msg,'error',{delay:1000});

                        }

                    },
                    error: function ( xhr, options)
                    {          
                         m_alert.show('请求发送失败，请重试','error',{delay:1000});
                        //self.trigger('error:login:fetch',  xhr, options);
                    },
                    complete: function (xhr, status)
                    {
                        //self.trigger('complete:login:fetch', xhr, status);
                    }
                });
        },

        send_data_model : function()
        {

            var self = this;
            self.total = self.total ? self.total : 0 ; //总体评价
            self.bxl_model = self.bxl ? self.bxl : 0 ; //表现力
            self.data_txt_val =  $.trim( self.$data_txt_val.val() ) ; //评价内容 

            if( self.total == 0)
            {
                m_alert.show('总体评价还没评分哦！','error',{delay:1000});
                return ;
            }

            if( self.bxl_model == 0)
            {
                m_alert.show('表现力还没评分哦！','error',{delay:1000});
                return ;
            }

            if( !self.zsx)
            {
                m_alert.show('还没选择真实性哦！','error',{delay:1000});
                return ;
            }

            if(!self.time_right)
            {
                m_alert.show('还没选择时间观念哦！','error',{delay:1000});
                return ;
            }

            if( self.data_txt_val == '')
            {
                m_alert.show('请输入内容哦！','error',{delay:1000});
                return ;
            }

            if( self.config )
            {
                self.data_name = 0;
            }else{
                self.data_name = 1;
            }

            var data = {
                date_id: self.date_id,
                cameraman_user_id : utility.login_id ,
                model_user_id : self.role_id ,
                overall_score : self.total ,
                expressive_score : self.bxl_model,
                truth : self.zsx,
                time_sense : self.time_right,
                comment : self.data_txt_val ,
                is_anonymous : self.data_name
            }

            utility.ajax_request
                ({
                    url: global_config.ajax_url.add_model_comment,
                    type : 'POST',
                    data : data ,
                    cache: false,
                    beforeSend: function (xhr, options)
                    {
                        m_alert.show('请求发送中...','loading',{delay:1000});
                    },
                    success: function (data)
                    {
                        console.log(data);

                        if ( data.result_data.code == 1 )
                        {
                            m_alert.show(data.result_data.msg,'right',{delay:1000});
                            page_control.navigate_to_page('comment/list/'+self.role+'/'+self.role_id+"/1");
                        }

                        if ( data.result_data.code == 0 )
                        {
                            m_alert.show(data.result_data.msg,'error',{delay:1000});
                        }

                    },
                    error: function ( xhr, options)
                    {          
                         m_alert.show('请求发送失败，请重试','error',{delay:1000});
                        //self.trigger('error:login:fetch',  xhr, options);
                    },
                    complete: function (xhr, status)
                    {
                        //self.trigger('complete:login:fetch', xhr, status);
                    }
                });

        },

        send_data_event : function()
        {

            var self = this;
            self.total = self.total ? self.total : 0 ; //总体评价
            self.event_zjnl = self.event_zjnl ? self.event_zjnl : 0 ; //组织能力
            self.event_mtsp = self.event_mtsp ? self.event_mtsp : 0 ; //模特水平


            self.data_txt_val =  $.trim( self.$data_txt_val.val() ) ; //评价内容 

            if( self.total == 0)
            {
                m_alert.show('总体评价还没评分哦！','error',{delay:1000});
                return ;
            }

            if( self.event_zjnl == 0 )
            {
                m_alert.show('组织能力还没评分哦','error',{delay:1000});
                return ;
            }

            if( self.event_mtsp == 0 )
            {
                m_alert.show('模特水平还没评分哦','error',{delay:1000});
                return ;
            }


            if( self.data_txt_val == '')
            {
                m_alert.show('请输入内容哦！','error',{delay:1000});
                return ;
            }

            if( self.config )
            {
                self.data_name = 0;
            }else{
                self.data_name = 1;
            }

            var data = {
                event_id: self.date_id,
                user_id : utility.login_id,
                overall_score : self.total ,
                organize_score : self.event_zjnl ,
                model_score : self.event_mtsp,
                comment : self.data_txt_val ,
                is_anonymous : self.data_name ,
                table_id : self.table_id
                
            }

  
            utility.ajax_request
                ({
                    url: global_config.ajax_url.add_event_comment,
                    type : 'POST',
                    data : data ,
                    cache: false,
                    beforeSend: function (xhr, options)
                    {
                        m_alert.show('请求发送中...','loading',{delay:1000});
                    },
                    success: function (data)
                    {
                        console.log(data);

                        if ( data.result_data.code == 1 )
                        {
                            m_alert.show(data.result_data.msg,'right',{delay:1000});
                            page_control.navigate_to_page('comment/list/'+self.role+'/'+self.date_id+"/"+self.role_id);
                        }

                        if ( data.result_data.code == 0 )
                        {
                            m_alert.show(data.result_data.msg,'error',{delay:1000});
                        }

                    },
                    error: function ( xhr, options)
                    {          
                         m_alert.show('请求发送失败，请重试','error',{delay:1000});
                        //self.trigger('error:login:fetch',  xhr, options);
                    },
                    complete: function (xhr, status)
                    {
                        //self.trigger('complete:login:fetch', xhr, status);
                    }
                });

        },


        get_start : function(ev,id)
        {
            var self = this;
            var target = $(ev.target);
            for(var i = 0, len = id.length; i < len; i++) {
                if( i <= target.index() ) {
                    id.eq(i).removeClass('icon-start-l-g');
                    id.eq(i).addClass('icon-start-l-y');

                } else {
                    id.eq(i).removeClass('icon-start-l-y');
                    id.eq(i).addClass('icon-start-l-g');
                }
            }
            return target.index()+1 ; 
        },

        
        render : function()
        {
            var self = this;

            // 调用渲染函数
            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        reset_scroll_height : function()
        {
            var self = this;

            // 换上了原生的滚动条则不需要计算页面高度，所以这里可以删掉设置高度代码
            // modify by hudw
            self.view_scroll_obj.refresh();
        }

    });

    module.exports = comment_view;
});