/**
 * Created by nolest on 2014/9/3.
 *
 * 活动安排view
 *
 *
 */
define(function(require, exports, module)
{
    var $ = require('$');
    var page_control = require('../../../frame/page_control');
    var View = require('../../../common/view');
    var tpl = require('./tpl/main.handlebars');
    var utility = require('../../../common/utility');
    var Scroll = require('../../../common/scroll');
    var select_view = require('../../../widget/select/view');
    var panel_pick = require('../../../widget/panel_pick/view');
    var panel_leader = require('../../../widget/panel_leader/view');
    var location_data = require('../../../common/location_data');
    var m_alert = require('../../../ui/m_alert/view');

    // 对Date的扩展，将 Date 转化为指定格式的String
    // 月(M)、日(d)、小时(h)、分(m)、秒(s)、季度(q) 可以用 1-2 个占位符，
    // 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字)
    // 例子：
    // (new Date()).Format("yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423
    // (new Date()).Format("yyyy-M-d h:m:s.S")      ==> 2006-7-2 8:9:4.18
    Date.prototype.Format = function(fmt)
    { //author: meizz
        var o = {
            "M+" : this.getMonth()+1,                 //月份
            "h+" : this.getHours(),
            "d+" : this.getDate(),                    //日
            "m+" : this.getMinutes(),                 //分
            "s+" : this.getSeconds(),                 //秒
            "q+" : Math.floor((this.getMonth()+3)/3), //季度
            "S"  : this.getMilliseconds()             //毫秒
        };
        if(/(y+)/.test(fmt))
        {
            fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));
        }


        for(var k in o)
            if(new RegExp("("+ k +")").test(fmt))
                fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));

        return fmt;
    };


    var pub_info_view = View.extend
    ({
        attrs:
        {
            template: tpl
        },
        forms :
        {
            select : {}
        },
        events :
        {
            'swiperight' : function (){
                page_control.back();
            },
            'tap [data-role="page-back"]':function()
            {
                page_control.back();
            },
            'tap [data-role="add_panel"]' : function()
            {
                var self = this;

                self._set_one_pick_panel();

                self.view_scroll_obj.refresh();

            },
            'tap [data-role="next-step"]' : function()
            {
                var self = this;

                var data = self._mix_selected_value(self.selected_obj);
                console.log(self._mix_selected_value(self.selected_obj))

                if(self.arrange_panel_arr.length == 0)
                {
                    m_alert.show('请添加场次','error')

                    return
                }

                if(self.leader_panel_arr.length == 0)
                {
                    m_alert.show('请添加领队','error')

                    return
                }

                console.log("can?",self.can_next)
                if(self.can_next)
                {
                    page_control.navigate_to_page("act/detail/0",data)
                }
            },
            'tap [data-role="add_leader_panel"]' : function()
            {
                var self = this;

                self._set_one_leader_panel();

                self.view_scroll_obj.refresh();
            },
            'tap [data-role="pick-close-panel"]' : function(ev)
            {
                var self = this;

                $(ev.currentTarget).parent().parent().remove();

                self.view_scroll_obj.refresh();

                var arrange_count = $(ev.currentTarget).attr("arrange_panel_count")

                self._remove_pick_panel(arrange_count);
                //重置显示数
                self._reset_arrange_index();

            },
            'tap [data-role="leader-close-panel"]' : function(ev)
            {
                var self = this;

                $(ev.currentTarget).parent().parent().remove();

                self.view_scroll_obj.refresh();

                var leader_count = $(ev.currentTarget).attr("leader_panel_count")

                self._remove_leader_panel(leader_count);

                self._reset_leader_index();

            },
            'tap [data-role="page-back"]' :function()
            {
                page_control.back()
            }
        },
        /**
         * 整合选择参数
         * @param obj
         * @returns {*}
         * @private
         */
        _reset_arrange_index : function()
        {
            var self = this;

            var panels = self.$('[data-role="panel-pick-con"]');

            var indexs = self.$('[data-role="panel_index"]');

            $.each(panels,function(i,obj)
            {
                $(indexs[i]).html(i+1)
            })
        },
        _reset_leader_index : function()
        {
            var self = this;

            var panels = self.$('[data-role="component-panel-leader"]');

            var indexs = self.$('[data-role="leader_panel_index"]');

            $.each(panels,function(i,obj)
            {
                $(indexs[i]).html(i+1)
            })

        },
        _mix_selected_value : function(obj)
        {
            var self = this;

            var remark = self.$remark.val();

            var all_pick_panel = self.$('[data-role="panel-pick-con"]');

            var all_leader_panel = self.$('[data-role="component-panel-leader"]')

            var arrange_arr = [];

            var leader_arr = [];

            var num = 0;

            self.can_leader_next = false;

            self.can_tel_next = false;

            self.can_next = false;

            self.can_num_next = false;

            self.can_time_next = false;

            $.each(self.leader_panel_arr,function(i,obj)
            {
                console.log(obj.get_selected_obj(),obj.get_selected_obj().name)

                var phone_reg = new RegExp(/^[0-9]{11}$/);

                if(obj.get_selected_obj().mobile.trim() == "" || !phone_reg.test(obj.get_selected_obj().mobile.trim()))
                {
                    m_alert.show('请正确填写手机号码','error');

                    self.can_tel_next = false;

                }
                else
                {
                    self.can_tel_next = true;
                }
                if(obj.get_selected_obj().name.trim() == "")
                {
                    m_alert.show('请填写联系人昵称','error');

                    self.can_leader_next = false;

                }
                else
                {
                    self.can_leader_next = true;
                }


                leader_arr.push(obj.get_selected_obj());

            });

            $.each(self.arrange_panel_arr,function(i,obj)
            {
                num = utility.int(num) + utility.int(obj.get_selected_obj().num);

                console.log(obj.get_selected_obj())
                if(obj.get_selected_obj().begin_time && obj.get_selected_obj().end_time)
                {
                    var text = (new Date(obj.get_selected_obj().begin_time.replace(/-/g, '/'))).Format("MM.dd hh:mm") +
                        '-'
                        +(new Date(obj.get_selected_obj().end_time.replace(/-/g, '/'))).Format("MM.dd hh:mm") ;

                    self.can_time_next = true;
                }
                else
                {
                    var text = '';

                    m_alert.show('请选择时间','error');

                    self.can_time_next = false;

                    return
                }

                if(num < 0 || num == 0 || isNaN(num))
                {
                    console.log(typeof (num));

                    m_alert.show('请正确填写名额','error');

                    self.can_num_next = false;

                    return
                }
                else
                {
                    self.can_num_next = true;
                }


                arrange_arr.push($.extend(true,{},obj.get_selected_obj(),{ session : i+1,text:text}));


            });

            self.can_next = self.can_leader_next && self.can_tel_next && self.can_num_next && self.can_time_next;


            var pick_value =
            {
                remark : remark,
                table_info : arrange_arr
            };

            var leader_value =
            {
                leader_info_detail : leader_arr
            };

            if(arrange_arr.length > 1)
            {
                var event_time = arrange_arr[0].begin_time + "至" + arrange_arr[arrange_arr.length-1].end_time;
            }
            else if (arrange_arr.length ==  1)
            {
                var event_time = arrange_arr[0].begin_time + "至" + arrange_arr[0].end_time;
            }

            var info = $.extend(true,{},self.selected_obj,pick_value,leader_value,{event_time : event_time},{num:num});

            return info

        },
        /**
         * 添加领队信息
         * @private
         */
        _set_one_leader_panel : function()
        {
            var self = this;

            ++self.leader_panel_count;

            var leader_panel = new panel_leader
            ({
                leader_panel_count : self.leader_panel_count,
                parentNode:self.$leader_con,
                templateModel :
                {
                    leader_panel_count : ++self.$('[data-role="component-panel-leader"]').length
                }
            }).render();

            self.leader_panel_arr.push(leader_panel);

        },
        _remove_leader_panel : function(id)
        {
            var self = this;

            $.each(self.leader_panel_arr,function(i,obj)
            {
                if(obj.get("leader_panel_count") == id)
                {
                    self.leader_panel_arr.splice(i,1)
                }
            })

        },
        /**
         * 添加场次
         * @private
         */
        _set_one_pick_panel : function()
        {
            var self = this;

            // 重置场次选择模块数
            if(self.$('[arrange_panel_count]').length == 0)
            {
                self.arrange_panel_count = 0;
            }

            //场次上限
            if(self.$('[arrange_panel_count]').length < 5)
            {




                // 处理前一场和下一场的时间，确保不会有交集
                /*if(self.arrange_panel_count >1)
                {
                    var before_pick = self.arrange_panel_arr[self.arrange_panel_count-2].get_selected_obj();
                    var after_pick = self.arrange_panel_arr[self.arrange_panel_count-1].get_selected_obj();


                    if(before_pick.begin_time && before_pick.end_time && utility.is_bigger_time(after_pick.begin_time,before_pick.end_time))
                    {
                        alert('第'+(self.arrange_panel_count)+'场次的开始时间必须在第'+(self.arrange_panel_count-1)+'场次的结束时间之后');

                        return;
                    }
                }*/

                if(self.arrange_panel_count > 0)
                {
                    var before_pick = self.arrange_panel_arr[self.arrange_panel_count-1].get_selected_obj();


                    if(!before_pick.begin_time && !before_pick.end_time)
                    {
                        alert('请选择时间');

                        return;
                    }

                }
                if(self.arrange_panel_count >1)
                {


                    var before_pick = self.arrange_panel_arr[self.arrange_panel_count-2].get_selected_obj();
                    var after_pick = self.arrange_panel_arr[self.arrange_panel_count-1].get_selected_obj();

                    console.log(utility.is_bigger_time(before_pick.end_time,after_pick.begin_time))

                    if(utility.is_bigger_time(before_pick.end_time,after_pick.begin_time))
                    {
                        alert('第'+(self.arrange_panel_count)+'场次的开始时间必须在第'+(self.arrange_panel_count-1)+'场次的结束时间之后');

                        return;
                    }
                }




                ++self.arrange_panel_count;


                var arrange_panel = new panel_pick
                ({
                    arrange_panel_count : self.arrange_panel_count,
                    parentNode:self.$arrange_con,
                    templateModel :
                    {
                        arrange_panel_count : ++self.$('[arrange_panel_count]').length
                    }
                }).render();

                self.arrange_panel_arr.push(arrange_panel);


            }
            else
            {
                m_alert.show("场次已满",'right');
            }

        },
        _remove_pick_panel : function(id)
        {
            var self = this;

            $.each(self.arrange_panel_arr,function(i,obj)
            {

                if(obj.get("arrange_panel_count") == id)
                {
                    self.arrange_panel_arr.splice(i,1);

                    self.arrange_panel_count--;
                }
            });

        },
        _setup_events : function()
        {
            var self = this;

            self.model
                .on('success:fetch',function(response,options)
                {

                    self._setup_club_select(response);
                })
        },
        _setup_club_select : function(response)
        {

        },
        _setup_select : function()
        {

        },
        _setup_scroll : function()
        {
            var self = this;
            var view_scroll_obj = Scroll(self.$container,
                {
                    is_hide_dropdown : true
                });
            self.view_scroll_obj = view_scroll_obj;
        },
        reset_viewport_height : function()
        {
            return utility.get_view_port_height('nav') - 50;
        },
        setup : function()
        {
            var self = this;

            self.$container = self.$('[data-role="action-pub-content"]'); // 滚动容器

            self.$arrange_con = self.$('[data-role="parts-con"]');

            self.$leader_con = self.$('[data-role="lead-con"]');

            self.selected_obj = self.get("selected_obj");

            self.arrange_panel_arr = [];

            self.leader_panel_arr = [];

            self.arrange_panel_count = 0;

            self.leader_panel_count = 0;

            self.$remark = self.$('[data-role="text"]');

            self._setup_events();

            self._setup_scroll();

            self._setup_select();

            self.can_next = false;

            self.view_scroll_obj.refresh();

            self._set_one_leader_panel();

            self._set_one_pick_panel();

        },
        render : function()
        {
            var self = this;

            var view_port_height = self.reset_viewport_height();

            self.$container.height(view_port_height);

            View.prototype.render.apply(self);

            self.trigger('render');

            return self;
        },
        reset_scroll_height : function()
        {
            var self = this;

            var view_port_height = self.reset_viewport_height();

            self.$container.height(view_port_height);

            self.view_scroll_obj.refresh();
        }

    });

    module.exports = pub_info_view;
});