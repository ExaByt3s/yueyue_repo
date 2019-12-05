var $ = require('zepto');

var App = require('../common/I_APP/I_APP');

var utility = require('../common/utility/index');

var abnormal = require('../common/widget/abnormal/index');

var fastclick = require('fastclick');

var list_tmp = __inline('./list-item.tmpl');

var qrcode_widget = require('../qrcode/qrcode');

var list_item_class = require('../list/list.js'); 

var swiper = require('../common/widget/swiper/1.0.0/swiper3.07.min'); //3.07.min

var header = require('../common/widget/header/main');        

if ('addEventListener' in document) {
    document.addEventListener('DOMContentLoaded', function() {
        fastclick.attach(document.body);
    }, false);
}

// 做兼容
var APP = App;


// 订单列表类
var order_list_class = function(options)
{
    var self = this;

    options = options || {};

    self.$el = options.$el || $('body');

    var type_id = options.type_id;
    var status = options.status;

    self.type_id = type_id;
    self.status = status;
    self.type = 'daifukuan';

    self.name = options.name;
    self.user_icon = options.user_icon;
    self.user_id = options.user_id;
    self.pay_url = options.pay_url;

    self.init(options);



};

order_list_class.prototype = 
{
    init : function(options)
    {
        var self = this;

        App.isPaiApp && App.showtopmenu(false,{show_bar:true});

        //头部返回按钮
        var $back = $('#back');
        $back.on('click',function()
        {
            if(App.isPaiApp)
            {
                App.app_back();
                return;
            }

            if(document.referrer)
            {

                window.history.back();
            }
            else
            {
                window.location.href = "http://yp.yueus.com/mall/user/"+window.__test__str+"index.php" ;
            }
        });
        

        // 列表队列
        self.list_queue = {};

        // 默认显示待付款
        self.refresh(self.type,
        {
            params : 
            {
                type_id : self.type_id,
                status : self.status,
                page : 1
            }
            
        });

        self.setup_top_bar();
        
        
    },
    create_list : function(type)
    {
        var self = this;

        var list_wrapper_html = '<div class="order_list_container" data-role="'+type+'-list-container" ></div>';

        self.$el.find('[data-role="order-list-wrapper"]').append(list_wrapper_html);

        return self.$el.find('[data-role="'+type+'-list-container"]');
    },
    refresh : function(type,options)
    {
        var self = this;

        options = options || {};

        var $list_container_by_type = self.$el.find('[data-role="'+type+'-list-container"]');

        self.$el.find('.order_list_container').addClass('fn-hide');
        $list_container_by_type.removeClass('fn-hide');

        // 已经存在的列表，直接调用刷新方法
        if(self.list_queue && typeof(self.list_queue[type]) != 'undefined')
        {
            if(self.list_queue[type].$el.rq_status == 'error')
            {
                self.list_queue[type].refresh();
            }

            return;
            //self.list_queue[type].refresh();
        }
        else
        {
            // 渲染列表
            var AJAX_URL = window.$__ajax_domain+'get_order_list.php';            
            // 不存在的列表，就创建
            var $list_container = self.create_list(type);

            // 列表组件
            var list_obj = new list_item_class(
            {
                //渲染目标
                ele : $list_container, 
                //请求地址 
                url : AJAX_URL,
                //传递参数
                params : options.params,
                //模板
                template : list_tmp, 
                //lz是否开启参数
                is_open_lz_opts : false  
            });

            self.list_queue[type] = list_obj;

            self.list_queue[type].$el.on('list_render:after',function(events,$list_container,data,$list)
            {
                
                self.setup_event();

                var $list_container_by_type = self.$el.find('[data-role="'+type+'-list-container"]');

                // 处理红点显示隐藏
                if(!$list_container_by_type.find('[data-role="abnormal"]').hasClass('fn-hide'))
                {
                    $('[data-list-type="'+type+'"]').find('.red_dot').addClass('fn-hide');
                }
                
               
            });


        }
        
    },
    setup_top_bar : function()
    {
        var self = this;

        //栏目切换
        self.$el.find('[data-role="tap"]').on('click',function()
        {
            $(this).addClass('cur').siblings().removeClass('cur');

            var type = $(this).attr('data-list-type');
            var status = $(this).attr('data-status');

            self.type = type;
            self.status = status

            
            self.refresh(type,
            {
                params : 
                {
                    type_id : self.type_id,
                    status : self.status,
                    page : 1
                }
                
            });

            //self.ajax_control();

        });
    },
    setup_event : function($list_obj)
    {
        var self = this;

        var ajax_control_type;//请求类型

        $list_obj = self.$el;

        // 取消订单
        $list_obj.find('[data-role="cancel"]').off('click');
        $list_obj.find('[data-role="cancel"]').on('click',function()
        {
            ajax_control_type = 'cancel';

            var $con = $(this);

            var dialog = utility.dialog({
                title : '',
                content : '确认取消？'
            });

            dialog.on('confirm',function(event,args)
            {
                var data = {order_sn: $con.parents('.child').attr('order_sn'),type:ajax_control_type}
                self.ajax_control(data);
            });
        });

        // 关闭订单
        $list_obj.find('[data-role="close"]').off('click');
        $list_obj.find('[data-role="close"]').on('click',function(){
            ajax_control_type = 'close';

            var $con = $(this);

            var dialog = utility.dialog({
                title : '',
                content : '确认关闭？'
            });

            dialog.on('confirm',function(event,args)
            {
                var data = {order_sn: $con.parents('.child').attr('order_sn'),type:ajax_control_type}
                self.ajax_control(data);
            });
        });

        // 支付
        $list_obj.find('[data-role="pay"]').off('click');
        $list_obj.find('[data-role="pay"]').on('click',function(){

            var $con = $(this);

            var dialog = utility.dialog({
                title : '',
                content : '去支付？'
            });

            dialog.on('confirm',function(event,args)
            {
                utility.ajax_request
                ({
                    url: window.$__ajax_domain+'order_list_pay_judge.php',
                    data : {order_sn: $con.parents('.child').attr('order_sn')},
                    dataType: 'json',
                    type: 'POST',
                    cache: false,
                    beforeSend: function()
                    {
                        window.$loading = $.loading
                        ({
                            content:'请求支付中...'
                        });
                    },
                    success: function(data)
                    {
                        $loading.loading("hide");

                        if(data.result_data.data.result == 1){
                            //成功 后刷页
                            if(data.result_data.data.message != '')
                            {
                                $.tips
                                ({
                                    content:data.result_data.data.message,
                                    stayTime:3000,
                                    type:'success'
                                });
                            }

                            window.location.href = self.pay_url + $con.parents('.child').attr('order_sn');
                        }
                        else{
                            $.tips
                            ({
                                content:data.result_data.data.message,
                                stayTime:3000,
                                type:'warn'
                            });
                        }
                    },
                    error: function(data)
                    {
                        $loading.loading("hide");

                        $.tips
                        ({
                            content:'网络异常',
                            stayTime:3000,
                            type:'warn'
                        });
                    }
                });
            });
        });
        
        // 申请退款
        $list_obj.find('[data-role="refund"]').off('click');
        $list_obj.find('[data-role="refund"]').on('click',function(){
            ajax_control_type = 'refund';

            var $con = $(this);

            var dialog = utility.dialog({
                title : '',
                content : '确认申请退款？'
            });

            dialog.on('confirm',function(event,args)
            {
                var data = {order_sn:$con.parents('.child').attr('order_sn'),type:ajax_control_type}
                self.ajax_control(data);
            });

        });

        // 出示二维码    
        $list_obj.find('[data-role="ewm"]').off('click');
        $list_obj.find('[data-role="ewm"]').on('click',function()
        {
            var this_parent = $(this).parents('.child');
            var qrcodes = [];
            $.each(this_parent.find('[data-role-code="contain"]'),function(i,obj)
            {
                var inner_obj =
                {
                    url : $(obj).find('[data-code-url]').attr('data-code-url'),
                    number : $(obj).find('[data-code-number]').attr('data-code-number'),
                    name : $(obj).find('[data-code-name]').attr('data-code-name'),
                    url_img : $(obj).find('[data-code-img-url]').attr('data-code-img-url')
                }

                qrcodes.push(inner_obj);
            })

            console.log(qrcodes);

            if(APP.isPaiApp)
            {
                APP.qrcodeshow(qrcodes,0,function(){});
            }
            else
            {
                // 处理二维码
                    
                var qrcode_obj = new qrcode_widget
                ({
                    ele : $('#render_qrcode'), //渲染的节点
                    data : 
                    {
                        name : self.name,
                        user_icon : self.user_icon,
                        user_id : self.user_id,
                        data_arr : qrcodes
                    }
                    
                })
                // 处理二维码 end
            }
        });
        

        // 删除
        $list_obj.find('[data-role="delete"]').off('click');
        $list_obj.find('[data-role="delete"]').on('click',function(){
            ajax_control_type = 'delete';

            var $con = $(this);

            var dialog = utility.dialog({
                title : '',
                content : '确认删除？'
            });

            dialog.on('confirm',function(event,args)
            {
                var data = {order_sn:$con.parents('.child').attr('order_sn'),type:ajax_control_type}

                self.ajax_control(data);

            });

        });

        $list_obj.find('[data-role="nav-to-seller"]').off('click');
        $list_obj.find('[data-role="nav-to-seller"]').on('click',function()
        {
            var seller_user_id = $(this).attr('data-seller-user-id');
            if(App.isPaiApp)
            {
                App.nav_to_app_page
                ({
                    page_type : 'seller_user',
                    seller_user_id : seller_user_id
                });
            }
            else
            {
                window.location.href = '../seller/?seller_user_id='+seller_user_id;
            }
        });

        
    },
    //ajax请求
    ajax_control : function (data,success,error,complete)
    {
        var self = this;

        utility.ajax_request
        ({
            url: window.$__ajax_domain+'order_list_control.php',
            data : data,
            cache: false,
            beforeSend: function()
            {

                window.$loading = $.loading
                ({
                    content:'发送中...'
                });
            },
            success: function(data)
            {
                $loading.loading("hide");


                if(data.result_data.data.result == 1){
                    //成功 后刷页
                    $.tips
                    ({
                        content:data.result_data.data.message,
                        stayTime:3000,
                        type:'success'
                    });

                    self.list_queue[self.type].refresh();
                    
                }
                else{
                    $.tips
                    ({
                        content:data.result_data.data.message,
                        stayTime:3000,
                        type:'warn'
                    });
                }
            },
            error: function(data)
            {
                $loading.loading("hide");

                $.tips
                ({
                    content:'网络异常',
                    stayTime:3000,
                    type:'warn'
                });
            }
        });
    }
};

exports.init = function(options)
{
    return new order_list_class(options);
}

