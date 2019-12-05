/**
 * Created by nolest on 2014/9/24.
 *
 * 我的 - 活动列表
 */

define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../frame/page_control');
    var View = require('../../common/view');
    var container_tpl = require('./tpl/container.handlebars');
    var utility = require('../../common/utility');
    var Scroll = require('../../common/scroll');
    var m_alert = require('../../ui/m_alert/view');
    var list_tpl = require('./tpl/list.handlebars');
    var abnormal = require('../../widget/abnormal/view');


    var contain_view = View.extend
    ({
        attrs:
        {
            template: container_tpl
        },
        events :
        {

        },
        _setup_events : function()
        {
            var self = this;

            self.model
                .on('before:fetch',function()
                {


                    m_alert.show('加载中','loading')
                })
                .on('success:fetch',function(response,options)
                {
                    m_alert.hide();
                    //区分获取数据的view
                    if(options.data.type == self.get("templateModel").type_tag)
                    {
                        //self.$pull_down_btn.html('加载更多');

                        /*if(response.result_data.has_next_page)
                        {
                            self.$pull_down_btn.removeClass('fn-hide');
                        }
                        else
                        {
                            self.$pull_down_btn.addClass('fn-hide');
                        }*/

                        self.data_cache = response.result_data;

                        self._render_invited_card(response.result_data,options.data.type);

                        self.fetching = false;
                    }
                })
                .on("error:fetch",function(response,options)
                {
                    //self.$pull_down_btn.html('加载更多');

                    m_alert.show('网络异常','error');
                })
                .on("complete:fetch",function(response,options)
                {
                    //m_alert.hide();

                })

        },
        search_data_by_id : function(id)
        {
            var self = this;

            var data;

            if(self.data_cache)
            {
                $.each(self.data_cache.list,function(i,obj)
                {
                    if(obj.enroll_id == id)
                    {
                        data =  obj
                    }
                })
            }

            return data;
        },
        setup : function()
        {
            var self = this;

            self.type_tag = self.get("templateModel").type_tag;

            self.$container = self.$('[data-role='+ self.type_tag + '-container]');

            self.$content_inside = self.$('[data-role=' + self.type_tag + '-inside]');

            //self.$pull_down_btn = self.$('[data-role=' + self.type_tag + '-pull-down]');

            var view_port_height = self.reset_viewport_height();

            self.$container.height(view_port_height);

            self.page_count = 1;

            self.fetching = false;

            self._setup_events();


        },
        render : function()
        {
            var self = this;

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        reset_viewport_height : function()
        {
            return utility.get_view_port_height('nav') -40;
        },
        /* 渲染列表
         *
         */
        _render_invited_card : function(data,type)
        {
            var self = this;

            self.has_next_page = data.has_next_page;

            self.page_count++;

            if(data.list)
            {
                $.each(data.list,function(i,obj)
                {
                    var hide_line = false;

                    switch (type)
                    {
                        case 'unpaid' :
                            hide_line = !(obj.event_detail.enroll_pay_button || obj.event_detail.enroll_cancel_button);
                            break;
                        case 'paid' :
                            hide_line = !(obj.event_detail.enroll_code_button || obj.event_detail.enroll_comment_button);
                            break;
                        case 'pub' :
                            hide_line = !(obj.event_detail.event_finish_button || obj.event_detail.event_scan_button);
                            break;
                    }
                    /*
                    //按钮类型
                    var btn_type = {}


                    switch (type)
                    {
                        case 'unpaid' : btn_type = { btn_type_unpaid : 1 };break;
                        case 'paid' : btn_type = { btn_type_paid : 1};break;
                        case 'pub' : btn_type = { btn_type_pub : 1};break;
                    }
                    //待付款、已付款、发布按钮类型整合
                    var data = $.extend(true,{},obj.event_detail,btn_type,{enroll_id:obj.enroll_id,show_btn : 1});


                    //发布具体按钮整合
                    if(type == 'pub')
                    {
                        switch (data.audit_status)
                        {
                            case '0':data = $.extend(true,data,{pic_show_reject:0,pic_show_no_reject:1,show_btn : 0});break;
                            case '1':data = $.extend(true,data,{pic_show_reject:0,pic_show_no_reject:0,show_btn : 1,_author:1});break;
                            case '2':data = $.extend(true,data,{pic_show_reject:1,pic_show_no_reject:0,show_btn : 0});break;
                        }

                        if(data._author == 1)
                        {
                            switch (data.event_status)
                            {
                                case 0: data = $.extend(true,data,{pub_btn_finish:1,pub_btn_cancel:1,pub_btn_notice:0,pub_btn_notice_text:'',pub_btn_scan:1,show_btn : 1});break;
                                case 1: data = $.extend(true,data,{pub_btn_finish:0,pub_btn_cancel:0,pub_btn_notice:0,pub_btn_notice_text:'',show_btn : 0});break;
                                case 2: data = $.extend(true,data,{pub_btn_finish:0,pub_btn_cancel:0,pub_btn_notice:1,pub_btn_notice_text:'活动已结束',show_btn : 0});break;
                                case 3: data = $.extend(true,data,{pub_btn_finish:0,pub_btn_cancel:0,pub_btn_notice:1,pub_btn_notice_text:'活动已取消',show_btn : 0});break;
                            }
                        }
                    }

                    if(type == 'paid')
                    {
                        switch(data.is_scan)
                        {
                            case 0:data = $.extend(true,{},data,{show_scan_btn : 1,show_comment_btn : 0,show_btn : 1});break;
                            case 1:data = $.extend(true,{},data,{show_scan_btn : 1,show_comment_btn : 1,show_btn : 1});break;
                            case 2:data = $.extend(true,{},data,{show_scan_btn : 0,show_comment_btn : 1,show_btn : 1});break;
                        }


                    }

                    if((type == 'paid' && data.is_end && data.is_comment) || (type == 'paid' && data.is_end && data.is_scan==0))
                    {
                        data = $.extend(true,{},data,{show_btn : 0})
                    }

                    if((type == 'paid' && !data.is_end && data.is_scan ==1) && (data.is_scan==0 || type == 'paid' && !data.show_scan_btn))
                    {
                        data = $.extend(true,{},data,{show_btn : 0})
                    }
                    */

                    //在每个view中加入type 和隐藏1px border-top 和enroll_id nolset 2015-02-06
                    obj.event_detail = $.extend(true,{},obj.event_detail,{view_type : type,hide_line:hide_line,enroll_id:obj.enroll_id});

                    //待审核、审核未通过的图
                    switch (obj.event_detail.audit_status)
                    {
                        case '0':obj.event_detail = $.extend(true,obj.event_detail,{pic_show_reject:0,pic_show_no_reject:1});break;
                        case '1':obj.event_detail = $.extend(true,obj.event_detail,{pic_show_reject:0,pic_show_no_reject:0});break;
                        case '2':obj.event_detail = $.extend(true,obj.event_detail,{pic_show_reject:1,pic_show_no_reject:0});break;
                    }

                    var view = list_tpl
                    ({
                        data : obj.event_detail
                    });

                    self.$content_inside.append(view);
                });

                if(!self.view_scroll_obj)
                {
                    self._setup_scroll();

                    self.view_scroll_obj.refresh();
                }
                else
                {
                    self.view_scroll_obj.refresh();

                    self.view_scroll_obj.change_scroll_position();
                }



                /*if(self.page_count <=1)
                {
                    self.view_scroll_obj.scrollTo(0,0);

                    //self.view_scroll_obj.resetLazyLoad();

                    self.view_scroll_obj.refresh();
                }
                else
                {

                    //self.view_scroll_obj.resetLazyLoad();

                    self.view_scroll_obj.refresh();
                }*/

                self._drop_reset();


            }
            //无内容时显示背景
            if(self.$content_inside.children().length == 0)
            {
                setTimeout(function()
                {
                    self.abnormal_view = new abnormal({
                        templateModel :
                        {
                            content_height : utility.get_view_port_height('all') - 75
                        },
                        parentNode:self.$content_inside
                    }).render();
                },500)
            }
        },
        /* 安装滚动条
         *
         */
        _setup_scroll : function()
        {
            var self = this;

            var view_scroll_obj = Scroll(self.$container,
                {
                    lazyLoad : true,
                    down_direction : 'down',
                    down_direction_distance :50
                });

            /*view_scroll_obj.on('scrollMoveAfter',function()
            {
                var _self = this;



                if(_self.maxScrollY - _self.y > 60)
                {
                    //有下一页时才加载
                    if(self.has_next_page && !self.fetching)
                    {

                        self.fetching = true;

                        self.get_list();
                    }
                }
                else if(_self.maxScrollY - _self.y > 30)
                {
                    self.$pull_down_btn.html('上拉释放');
                }
            });
*/
            self.view_scroll_obj = view_scroll_obj;

            // modify by hudw 2015.2.2
            self.view_scroll_obj.on('pullload',function(e)
            {
                if(self.has_next_page && !self.fetching)
                {
                    self.fetching = true;

                    self.get_list();
                }
                else
                {
                    self._drop_reset();
                }

            });

            self.view_scroll_obj.on('dropload',function(e)
            {
                self.refresh();

            });

           self.view_scroll_obj.refresh();
        },
        /* 加载数据
         *
         */
        get_list : function()
        {
            var self = this;

            console.log("get-list")
            var data =
            {
                type: self.get("templateModel").type_tag,
                page : self.page_count
            };

            self.model.get_more_list(data);
        },
        _drop_reset : function()
        {
            var self = this;

            self.view_scroll_obj && self.view_scroll_obj.resetload();
        },
        /*  刷新方法
         *
         */
        refresh : function()
        {
            var self = this;

            console.log("refresh")
            self.page_count = 1;

            self.$content_inside.html("");

            var refresh_data =
            {
                type: self.get("templateModel").type_tag,
                page : self.page_count
            };

            self.model.get_more_list(refresh_data);

            self.view_scroll_obj.refresh();
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
        }
    });

    module.exports = contain_view;
});
