/**
 * 优惠券
 hudw 2015.5.5
 **/

/**
 * @require ./list-coupon.scss
 */

"use strict";

var utility = require('../common/utility/index');
var $ = require('zepto');
var fastclick = require('fastclick');
var yue_ui = require('../yue_ui/frozen');
var App =  require('../common/I_APP/I_APP');
var item_tpl = __inline('./list-item.tmpl');

if ('addEventListener' in document) {
    document.addEventListener('DOMContentLoaded', function() {
        fastclick.attach(document.body);
    }, false);
}

if(App.isPaiApp)
{
    App.check_login(function(data)
    {
        if(!utility.int(data.pocoid))
        {
            App.openloginpage(function(data)
            {
                if(data.code == '0000')
                {
                    utility.refresh_page();
                }
            });

            return;
        }


    });

}

var _self = {};
var _coupon_map = {};

module.exports =
{
    render: function ($dom,data)
    {
        // tpl后缀的文件也可以用于模板嵌入，相比handlebars
        // tpl文件不具有模板变量功能，嵌入后只是作为字符串使
        // 用，tpl文件嵌入之前可以被插件压缩，体积更小。
        // handlebars由于缺少相应的压缩插件因此暂时不能在预
        // 编译阶段做压缩。选择tpl还是handlebars可以自由选
        // 择


        var self = this;

        $.each(data,function(i,obj)
        {

            if(obj.tab == 'available')
            {
                data[i] = $.extend(true,{},obj,{_class_for_available:true})
            }
            else if (obj.tab == 'used'){
                data[i] = $.extend(true,{},obj,{_class_for_used:true});
            }
            else if (obj.tab == 'expired') {
                data[i] = $.extend(true,{},obj,{_class_for_expired:true});
            }

            _coupon_map[obj.coupon_sn] = obj;
        });

        $dom.html('');

        $dom.html(item_tpl({data:data}));

        // 安装事件
        self.setup_event();

        return this;
    },
    refresh : function()
    {
        var self = this;

        var $loading=$.loading
        ({
            content:'加载中...'
        });

        utility.ajax_request
        ({
            url : window.$__config.ajax_url.get_user_coupon_list_by_check,
            type : 'GET',
            data :
            {
                module_type : self.module_type,
                request_id :self.request_id,
                quotes_id :self.quotes_id,
                order_total_amount  : self.order_total_amount ,
                order_pay_amount : self.order_pay_amount ,
                page : self.page
            },
            success : function(res)
            {
                var content = res.result_data;

                self.render(self.$container,content.list);

                $loading.loading("hide");
            },
            error : function()
            {
                $loading.loading("hide");

                $.tips
                ({
                    content : '网络异常',
                    stayTime:3000,
                    type:"warn"
                });
            }
        });


    },
    page_back : function()
    {

    },
    // 初始化
    init : function(options)
    {
        var self = this;

        self.$container = options.container || {};
        self.module_type = options.module_type || '';
        self.request_id = options.request_id || '';
		self.quotes_id = options.quotes_id || '';		
        self.order_total_amount  = options.order_total_amount  || '';
        self.order_pay_amount = options.order_pay_amount || '';
        self.page = options.page || 1;

        self.refresh();

        return self;
    },
    hide : function()
    {

    },
    // 安装事件
    setup_event : function()
    {
        var self = this;

        // 选择优惠券事件
        $('[data-coupon_sn]').on('click',function(ev)
        {
            var $cur_btn = $(ev.currentTarget);

            var coupon_sn = $cur_btn.attr('data-coupon_sn');

            var $check_box = $cur_btn.find('[type="checkbox"]');

            // 选中状态
            if($check_box.hasClass('no-selected') )
            {
                //先清空所有选择状态
                $('[data-coupon_sn]').find('[type="checkbox"]').attr('checked',"").addClass('no-selected');

                $check_box.removeAttr('checked').removeClass('no-selected');

                self.selected_coupon = _coupon_map[coupon_sn];
            }
            else
            {
                $check_box.attr('checked',"").addClass('no-selected');

                self.selected_coupon = '';
            }



        });
    }
};





