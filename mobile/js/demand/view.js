/**
 * 基础页面框架
 * 汤圆
 * @param  {[type]} require
 * @param  {[type]} exports
 * @param  {[type]} module
 * @return {[type]}
 */
define(function(require, exports, module)
{
    //基础框架
    var $ = require('$');
    var page_control = require('../frame/page_control');
    var View = require('../common/view');
    var utility = require('../common/utility');
    var templateHelpers = require('../common/template-helpers');
    var Scroll = require('../common/scroll');
    var footer = require('../widget/footer/index');
    var m_alert = require('../ui/m_alert/view');
    var App = require('../common/I_APP');
    var m_select = require('../ui/m_select/view');
    var abnormal = require('../widget/abnormal/view');

    //当前页引用
    var current_main_template = require('./tpl/main.handlebars');
    var page_item_tpl = require('./tpl/page-item.handlebars');

    var is_error_fetch = false;

    var current_view = View.extend(
    {
        attrs:
        {
            template: current_main_template
        },
        events:
        {

            'tap [data-role=web-page-back]': function(ev)
            {
                var self = this;

                if(is_error_fetch)
                {
                    if(App.isPaiApp)
                    {
                        App.app_back();
                    }
                    else
                    {
                        page_control.back();
                    }

                    return;
                }


                // 第一页的返回
                if(self.cur_page_index == 0)
                {
                    if(App.isPaiApp)
                    {
                        window.AppCanPageBack = true;

                        App.app_back();
                    }
                    else
                    {
                        page_control.back();
                    }

                }
                // 其他页面的返回
                else
                {



                    self.back_page();
                }
            },
            /**
             * 时间控件
             * @param ev
             */
            'tap [data-role=btn-time]': function(ev)
            {
                var self = this;
                var $ev = $(ev.currentTarget);
                // 安装时间控件
                // 如果要点击事件才安装控件，则要判断该控件是否存在，避免重复安装
                if(!self['begin_time'])
                {
                    self.setup_select();
                }
                else
                {
                    self['begin_time'].show();
                }


                // 时间确认
                self['begin_time'].on('confirm:success',function(arr)
                {

                    $ev.addClass('cur');

                    var val =  arr[0].value+" "+arr[1].text;

                    self.$('[data-role=btn-time]').html(val);

                    self.all_data['p'+self.cur_page_index] =
                    {
                        title :  self.$('[data-index="'+self.cur_page_index+'"]').find('[data-role="title"]').html(),
                        value :  arr[0].value+" "+arr[1].value

                    };
                    console.log(self.all_data);

                });

                // self.btn_time_config = false ;
            },
            /**
             * 二级菜单的选项
             * @param ev
             */
            'tap [data-role="sec-menu"]': function(ev)
            {
                var self = this;
                var $ev = $(ev.currentTarget);

                var $sec_list = $ev.find('[data-role="list"]');

                $ev.parent().find('[data-role="list"]').addClass('fn-hide');
                $sec_list.removeClass('fn-hide');
            },




            /**
             * 底部按钮
             * @param ev
             */
            'tap [data-next-step]': function(ev)
            {
                var self = this;

                if(!utility.auth.is_login())
                {
                    if(App.isPaiApp)
                    {
                        App.openloginpage(function(data)
                        {
                            if(data.code == '0000')
                            {
                                utility.refresh_page();
                            }
                        });
                    }
                    else
                    {
                        page_control.navigate_to_page('account/login');
                    }

                    return;
                }

                var $ev = $(ev.currentTarget);

                var next = $ev.attr('data-next-step');

                if(next == -1)
                {
                    if(App.isPaiApp)
                    {
                        App.switchtopage({page:'hot'});
                    }
                    else
                    {
                        page_control.navigate_to_page('hot');
                    }

                    return;
                }

                if (!self.all_data.p0)
                {
                    m_alert.show('请完善好当前步骤内容', 'error',{delay:1000});
                }

                // if (!self.all_data['p'+self.cur_page_index])
                // {
                //      m_alert.show('请完善好当前步骤内容', 'error',{delay:1000});

                // }

                
                else
                {
                    var type_index = next-1;
                    var type_val = $ev.parent().parent().find('[data-role=textarea]').val() ;



                    // 如果是输入框，要输入文字才能跳转
                    if (self.data_model.total_data[type_index].content.type == 'textarea' || self.data_model.total_data[type_index].content.type == 'input')
                    {
                        if(self.data_model.total_data[type_index].content.type == 'input')
                        {
                            var type_val = $ev.parent().parent().find('[data-role=input]').val() ;
                        }

                        // 必填的才提示
                        if (self.data_model.total_data[type_index].content.must_be_txt) 
                        {
                            if ( type_val == '') 
                            {
                                m_alert.show('请完善好当前步骤内容', 'error',{delay:1200});
                                return ;
                            }
                        }
                        


                        self.all_data['p'+self.cur_page_index] =
                        {
                            title :  self.$('[data-index="'+self.cur_page_index+'"]').find('[data-role="title"]').html(),
                            value :  type_val
                        };

                    }

                    if ($ev.attr('data-role') == 'submit') 
                    {

                        var location_id = utility.storage.get('location_id');

                        self.all_data = $.extend(true,{},self.all_data,{id : self.id,location_id : location_id});

                        self.model.send_data(self.all_data);

                        return ;
                    }

                    self.go_page(next);

                }

            },
            /**
             * 每一页跳转的按钮
             * @param ev
             */
            'tap [data-go-to]' : function(ev)
            {
                var self = this;
                var $ev = $(ev.currentTarget);
                var next = $ev.attr('data-go-to');

                // 当前页面是第一页，所以要使用下一步按钮才能跳页
                if(next == 2)
                {
                    return;
                }

                if ($(ev.target).attr('data-txt'))
                {
                    //self.all_data = $.extend(true,{},self.all_data,{ys_price : $(ev.target).attr('data-txt')});

                    self.all_data['p'+self.cur_page_index] =
                    {
                        title :  self.$('[data-index="'+self.cur_page_index+'"]').find('[data-role="title"]').html(),
                        value :  $(ev.target).attr('data-txt')

                    };

                    console.log(self.all_data);
                    // 跳转页面。跳转的目的地根据数据字段next决定，-1为最后一页
                    self.go_page(next);

                }

            }




        },
        /**
         * 安装事件
         * @private
         */
        setup_events: function()
        {

            var self = this;

            //传入参数 
            self.id = self.get('id');
            console.log(self.id);

            self.model.set('id',self.id);
        
            self.btn_time_config = true ;

            // 初始化翻页为1
            self.init_page = 1 ;

            // 收集数据对象
            self.all_data = {}


            self.model
                .on('before:fetch', function(response, xhr)
                {
                    //m_alert.show('加载中...', 'loading');
                })
                .on('success:fetch', function(response, xhr)
                {
                    m_alert.hide();

                    self.rend_html(response, xhr);


                })
                .on('error:fetch', function(response, xhr)
                {

                    is_error_fetch = true;

                    window.AppCanPageBack = true;

                    //无内容时显示背景
                    self.abnormal_view = new abnormal({
                        templateModel :
                        {
                            content_height : utility.get_view_port_height('all') - 75
                        },
                        parentNode:self.$container
                    }).render();

                    m_alert.show('网络异常', 'error');

                })
                .on('complete:fetch', function(xhr, status) {

                });



                self.model
                    .on('before:send_data:fetch', function(response, xhr)
                    {
                        m_alert.show('发送中...', 'loading',{delay:-1});
                    })
                    .on('success:send_data:fetch', function(response, xhr)
                    {

                        m_alert.hide();

                        if (response.result_data.code == 1) 
                        {
                            self.go_page(self.data_model.total_page);
                        }

                    })
                    .on('error:send_data:fetch', function(response, xhr)
                    {
                        m_alert.show('网络异常', 'error');
                    })
                    .on('complete:send_data:fetch', function(xhr, status) {

                    });



            //当前view 操作
            self.on('update_list', function(response, xhr)
                {
                    // 区分当前对象
                    if (!self.view_scroll_obj)
                    {
                        self._setup_scroll();
                    }
                    self.view_scroll_obj.refresh();

                    // 重置渲染队列
                    self._render_queue = [];

                })
                .once('render', self.render_after, self);
                

        },

        /**
         * 视图初始化入口
         */
        setup: function()
        {
            var self = this;

            // 渲染队列
            self._render_queue = [];

            // 配置交互对象
            self.$container = self.$('[data-role=container]'); // 滚动容器

            self.$main_ele = self.$('[data-role=main-ele]');

            if(utility.auth.is_login())
            {

                if(utility.user.get('role') == 'model')
                {
                    m_alert.show('该功能模块尚未支持模特使用','right',{delay:3000});

                    setTimeout(function()
                    {
                        if(App.isPaiApp)
                        {
                            App.switchtopage({page:'hot'});
                        }
                        else
                        {
                            page_control.navigate_to_page('hot');
                        }

                    },3000);

                    return;

                }
            }



            // 安装事件
            self.setup_events();

            // 安装滚动条
            self._setup_scroll();

            // self.rend_html();


            

        },

        // 渲染模板
        rend_html :function(response, xhr)
        {
            var self = this;

            // self.total_page = 8;

            // 配置模板数据
            // self.data_model =
            // {
            //     total_page : 8 ,
            //     total_data :
            //     [
            //         {
            //             page : 1,
            //             next : 2,
            //             title : "你预想的拍摄时间？",
            //             content :
            //             {
            //                 type : "time",
            //                 data :
            //                 [
            //                     {text: '选择一个时间'}
                               
            //                 ]
            //             },
            //             btn : "下一步"
            //         },

            //         {
            //             page : 2,
            //             next : 3,
            //             title : "你想拍什么风格的模特？",
            //             content :
            //             {
            //                type : "list-select",
            //                data :
            //                 [
            //                     {
            //                         text : '性感',
            //                         son_txt :
            //                         [
            //                             {text: '真空'},
            //                             {text: '全裸'}
            //                         ]
            //                     },
            //                     {
            //                         text : '清新',
            //                         son_txt :
            //                         [
            //                             {text: '真空'},
            //                             {text: '全裸'},
            //                             {text: '内衣/比基尼'}
            //                         ]
            //                     },
            //                     {
            //                         text : '街拍',
            //                         son_txt :
            //                         [
            //                             {text: '真空'},
            //                             {text: '全裸'},
            //                             {text: '内衣/比基尼'}
            //                         ]
            //                     },
            //                     {
            //                         text : '复古',
            //                         son_txt :
            //                         [
            //                             {text: '真空'},
            //                             {text: '全裸'},
            //                             {text: '内衣/比基尼'}
            //                         ]
            //                     },
            //                     {
            //                         text : '商业',
            //                         son_txt :
            //                         [
            //                             {text: '真空'},
            //                             {text: '全裸'},
            //                             {text: '内衣/比基尼'},
            //                             {text: '测试'}
            //                         ]
            //                     }
                
            //                 ]
            //             },
            //            btn : ""
            //         },

            //         {
            //             page : 3,
            //             next : 4,
            //             title : "你的预算是多少",
            //             content :
            //             {
            //                 type : "list-radio",
            //                 data :
            //                 [
            //                     {text: '100以下'},
            //                     {text: '101-300'},
            //                     {text: '301-500'},
            //                     {text: '501-800'},
            //                     {text: '801-1000'},
            //                     {text: '1000以上'},
            //                     {text: '视情况而定'}
            //                 ]
            //             },
            //             btn : ""
            //         },
            //         {
            //             page : 4,
            //             next : 5,
            //             branch :true,
            //             title : "你有定下拍摄场地么？ <br> 如果有，请输入拍摄场地方便模特选择。",
            //             content :
            //             {
            //                 type : "list-radio",
            //                 data :
            //                 [
            //                     {text: '有',flag:true,next:'4-1'},
            //                     {text: '没有',flag:false,next:5}
            //                 ]
            //             },
            //             btn : ""
            //         },
            //         // 此处为分支路线
            //         {
            //             page : '4-1',
            //             next : 5,
            //             title : '你有定下拍摄场地么？ <br> 如果有，请输入拍摄场地方便模特选择。',
            //             content :
            //             {
            //                 type : "textarea",
            //                 placeholder : '请输入详细地址',
            //                 data : []
            //             },
            //             btn : "下一步"
            //         },
            //         {
            //             page : 5,
            //             next : 6,
            //             title : "你需要约约为你提供配套服务么？",
            //             content :
            //             {
            //                 type : "list-radio",
            //                 data :
            //                 [
            //                     {text: '场地服务'},
            //                     {text: '化妆服务'},
            //                     {text: '服装服务'},
            //                     {text: '不需要了'}
            //                 ]
            //             },
            //             btn : ""
            //         },

            //         {
            //             page : 6,
            //             next : 7,
            //             title : "你希望多久内找到合适的模特？",
            //             content :
            //             {
            //                 type : "list-radio",
            //                 data :
            //                 [
            //                     {text: '没所谓，我不急'},
            //                     {text: '两天内'},
            //                     {text: '一天内'},
            //                     {text: '现在，马上就想约拍'}
            //                 ]
            //             },
            //             btn : ""
            //         },

            //         {
            //             page : 7,
            //             next : 8,
            //             title : "选填项；比如身材、样貌等其他要求",
            //             content :
            //             {
            //                 type : "textarea",
            //                 placeholder : '请输入详细地址',
            //                 data : []
            //             },
            //             submit : true,
            //             btn : "发布我的需求"
            //         },

            //         {
            //             page : 8,
            //             next : -1,
            //             title : "<div><i class='icon icon-success-max'></i></div><p class='p1'>发布成功</p><p class='p2'>稍后我们会通过消息给你推送模特，<br>请你留意系统消息。</p>",
            //             content :
            //             {
            //                 type : false,
            //                 data :
            //                 [
                                
            //                 ]
            //             },
            //             jump_page : true,// 是否跳转
            //             jump_info :
            //             {
            //                 url : '',
            //                 type : ''
            //             },//跳转内容
            //             btn : "确定"
            //         }

            //     ]
            // }
            //  显示总页数字
            // var all_page = self.data_model.length - 1  ;
            // 
            // 原型
            self.data_model = 
            {
                total_page :'' ,
                total_data :''
            }

            self.data_model.total_page = response.result_data.data.data_model.total_page;
            self.data_model.total_data = response.result_data.data.data_model.total_data;


            self.$('[data-role=all-page]').html(self.data_model.total_page);
            self.$('[data-role=main-title]').html(response.result_data.data.data_model.title);

            

            self.page_item_html = page_item_tpl(
            {
                data: self.data_model.total_data
            },
            {
                helpers : {if_equal:templateHelpers.if_equal}
            });

            self.$main_ele.html(self.page_item_html);

            self.history_page = [];

            // 放第几页出来，显示
            self.go_page(1);


        },

        // 跳到某一页
        go_page : function(num)
        {
            var self = this;

            var pre_page = num;

            self.history_page.push(pre_page);

            self.$('[data-page]').addClass('fn-hide');
            self.$('[data-page=page-'+num+']').removeClass('fn-hide');
            self.$('[data-role=cur-page]').html(num);

            var cur_page = self.$('[data-page]').not('.fn-hide').attr('data-index') || 0;

            self.cur_page_index = cur_page;



            // console.log(self.cur_page_index)

        },
        /**
         * 返回函数
         */
        back_page : function()
        {
            var self = this;

            var last_page =1;

            if(self.history_page.length<=1)
            {
                var last_page = 1;
            }
            else
            {
                self.history_page.pop();

                var last_page = self.history_page[self.history_page.length-1];
            }

            var num = last_page;

            self.all_data['p'+self.cur_page_index] = {};

            self.$('[data-page]').addClass('fn-hide');
            self.$('[data-page=page-'+num+']').removeClass('fn-hide');
            self.$('[data-role=cur-page]').html(num);

            var cur_page = self.$('[data-page]').not('.fn-hide').attr('data-index') || 0;

            self.cur_page_index = cur_page;

            console.log(self.cur_page_index)

        },

        // 跳下一页
        next_page : function() 
        {
            var self = this;
            self.init_page++ ;
            self.go_page(self.init_page)
        },

        // 跳上一页
        pre_page : function() 
        {
            var self = this;
            self.init_page-- ;
            if (self.init_page == 0) 
            {
                page_control.back();
            }
            if (self.init_page < 1) 
            {
                return ;
            }

            self.go_page(self.init_page)
        },

        // 清空对象数据
        clear_obj_data : function(obj) 
        {
            var self = this;
            for (var i in obj) {
                obj[i] = '';
            };
        },

        // 选择时间
        setup_select : function()
        {
            var self = this;

            var date_arr = utility.select_time.mix_date().date_arr;
            var area_time_arr = utility.select_time.area_time_arr;
            //var hour_arr = utility.select_time.mix_date().hour_arr;
            //var min_arr = utility.select_time.mix_date().min_arr;

            self['begin_time'] = new m_select
            ({
                templateModel :
                {
                    options : [date_arr,area_time_arr]

                },
                parentNode: self.parentNode
            }).render();
            self['begin_time'].show();
        },

        /**
         * 渲染模板
         * @param response
         * @param options
         * @private
         */
        render_html: function(response, xhr)
        {
            var self = this;


            console.log(response);
            self.trigger('update_list', response, xhr);
        },
        
        render_after: function()
        {
            var self = this;
            self.model.get_list();
        },

        /**
         * 安装滚动条
         * @private
         */
        _setup_scroll: function()
        {
            var self = this;
            var view_scroll_obj = Scroll(self.$container,
            {
                is_hide_dropdown: true
            });
            self.view_scroll_obj = view_scroll_obj;
        },
        /**
         * 安装底部导航
         * @private
         */
        _setup_footer: function()
        {
            var self = this;
            var footer_obj = new footer(
            {
                // 元素插入位置
                parentNode: self.$el,
                // 模板参数对象
                templateModel:
                {
                    // 高亮设置参数
                    is_model_pai: true
                }
            }).render();
        },
        render: function()
        {
            var self = this;
            // 调用渲染函数
            View.prototype.render.apply(self);
            self.trigger('render');
            self.trigger('update_list');
            return self;
        }
    });
    module.exports = current_view;
});