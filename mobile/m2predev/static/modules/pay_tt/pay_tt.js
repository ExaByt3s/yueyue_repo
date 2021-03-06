define('pay_tt', function(require, exports, module){ /**
 * 支付页面
   hudw 2015.4.15
**/
"use strict";

var back_btn = require('common/widget/back_btn/main');
var utility = require('common/utility/index');
var $ = require('components/zepto/zepto.js');
var fastclick = require('components/fastclick/fastclick.js');
var yue_ui = require('yue_ui/frozen');
var App =  require('common/I_APP/I_APP');
var coupon = require('coupon/list');
var yueyue_header = require('common/widget/yueyue_header/main');

if ('addEventListener' in document) {
    document.addEventListener('DOMContentLoaded', function() {
        fastclick.attach(document.body);
    }, false);
}

(function($,window)
{

	if(App.isPaiApp)
	{
        /**
         * 获取个人信息函数，专用于app
         */

        var params = window.__YUE_APP_USER_INFO__;

        var local_user_id = utility.login_id;
        var client_user_id = window.__YUE_APP_USER_INFO__.user_id || 0;

        var async = (local_user_id == client_user_id);

        console.log("=====local_user_id,client_user_id=====");
        console.log(local_user_id,client_user_id);

        utility.ajax_request
        ({
            url: window.$__config.ajax_url.auth_get_user_info,
            data : params,
            cache: false,
            async : async
        });

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

	var $header = $('#nav-header');	

	var tpl = Handlebars.template(function (Handlebars,depth0,helpers,partials,data) {
  this.compilerInfo = [4,'>= 1.0.0'];
helpers = this.merge(helpers, Handlebars.helpers); data = data || {};
  var buffer = "", stack1, helper, functionType="function", escapeExpression=this.escapeExpression;


  buffer += "<ul class=\"ui-list ui-list-text ui-border-b\">\r\n	<li>\r\n		<div class=\"ui-avatar\">\r\n		   <span style=\"background-image:url('";
  if (helper = helpers.user_icon) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.user_icon); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "')\"></span>\r\n		</div>\r\n		<div class=\"ui-list-info ui-border-t\">\r\n			<h4>";
  if (helper = helpers.user_nickname) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.user_nickname); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</h4>\r\n			<p>服务类型 ";
  if (helper = helpers.service_name) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.service_name); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</p>\r\n		</div>\r\n	</li>				\r\n</ul>\r\n<ul class=\"ui-list ui-list-text\">\r\n	<li class=\"ui-border-t\">\r\n		<div class=\"ui-txt-default \">支付类型：<span>";
  if (helper = helpers.pay_type) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.pay_type); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</span></div>\r\n	</li>	\r\n	<li class=\"ui-border-t\">\r\n		<div class=\"ui-txt-default \">报价金额：<span>￥";
  if (helper = helpers.total_amount) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.total_amount); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</span></div>\r\n	</li>\r\n	<li class=\"ui-border-t\">\r\n		<div class=\"ui-txt-default \">服务金：<span>￥<label data-role=\"pay-amount\">";
  if (helper = helpers.pay_amount) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.pay_amount); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</label></span></div>\r\n	</li>\r\n</ul>\r\n\r\n<!--支付模块-->\r\n<ul class=\"ui-list ui-list-text mt20 \">  \r\n	<li >\r\n		<div class=\"ui-txt-default \">钱包<span></span></div>\r\n	</li>\r\n    <li data-role=\"coupon-money\">\r\n        <div class=\"ui-txt-default \">使用优惠券：<span><div class=\"ui-nowrap\" style=\"width: 200px;\" data-role=\"coupon-money-tag\"></div></span></div>\r\n        <div class=\"ui-edge-right\">\r\n            <span class=\"count-money-color\" data-role=\"coupon-money-text\"></span>\r\n            <i class=\"icon icon-select-no\" data-role=\"yes-tag\"></i>\r\n        </div>\r\n    </li>\r\n    <li data-role=\"select-available-balance\">\r\n		<div class=\"ui-txt-default \">账户余额：<span>￥<label data-role=\"available_balance\">";
  if (helper = helpers.available_balance) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.available_balance); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</label></span></div>\r\n		<div class=\"ui-edge-right\">\r\n			<span class=\"count-money-color\" data-role=\"less-money\"></span>\r\n			<i class=\"icon icon-select-no\" data-role=\"yes-tag\"></i>\r\n		</div>\r\n	</li>					\r\n</ul>\r\n<ul class=\"ui-list ui-list-text ui-border-b mt20\" style=\"margin-bottom: 0;\"  data-role=\"must-pay-container\">\r\n	<li class=\"ui-border-t\">\r\n		<div class=\"ui-txt-default \">还需支付：<span class=\"count-money-color\">￥<label data-role=\"need-price\">";
  if (helper = helpers.pay_amount) { stack1 = helper.call(depth0, {hash:{},data:data}); }
  else { helper = (depth0 && depth0.pay_amount); stack1 = typeof helper === functionType ? helper.call(depth0, {hash:{},data:data}) : helper; }
  buffer += escapeExpression(stack1)
    + "</label></span></div>\r\n	</li>	\r\n</ul>\r\n<ul class=\"ui-list ui-list-text \" data-role=\"other-pay-container\">\r\n	<li class=\"ui-border-t\" data-pay-type=\"alipay_purse\" data-role=\"pay-li\">\r\n		<div class=\"ui-txt-default \">\r\n			<div class=\"pay-type\">\r\n				<i class=\"icon icon-zhifubao\"></i>\r\n				<div class=\"ui-list-info \">\r\n					<h4 class=\"ui-nowrap\" >支付宝支付</h4>\r\n					<p class=\"ui-nowrap\">推荐有支付宝账号的用户使用</p>\r\n				</div>\r\n			</div>\r\n			<div class=\"ui-edge-right\">\r\n				<i class=\"icon icon-select-no\" data-role=\"yes-tag\"></i>\r\n			</div>\r\n		</div>\r\n	</li>	\r\n	<li class=\"ui-border-t\" data-pay-type=\"tenpay_wxapp\" data-role=\"pay-li\">\r\n		<div class=\"ui-txt-default \">\r\n			<div class=\"pay-type\">\r\n				<i class=\"icon icon-wx-pay\"></i>\r\n				<div class=\"ui-list-info \">\r\n					<h4 class=\"ui-nowrap\" >微信支付</h4>\r\n					<p class=\"ui-nowrap\">安装微信5.0及以上版本的使用</p>\r\n				</div>\r\n			</div>\r\n			<div class=\"ui-edge-right\">\r\n				<i class=\"icon icon-select-no\" data-role=\"yes-tag\"></i>\r\n			</div>\r\n		</div>\r\n	</li>\r\n</ul>\r\n\r\n<div class=\"last-container\"></div>\r\n<!--支付模块-->\r\n<div class=\"buttom-btn-wrap\">\r\n	<button class=\"ui-tt-pay-btn\" id=\"pay-btn\">\r\n		确认支付\r\n	</button>\r\n</div>";
  return buffer;
  });

	var _self = {};

	// 默认参数
	var defaults=
	{
		title : '付款'
	};
	
	// 获取参数		
	_self.quotes_id = $('#quotes_id').val();

	// 初始化dom
	_self.$page_container = $('[data-role="page-container"]');		

	// 初始化统计点
	if(App.isPaiApp)
	{
		App.analysis('moduletongji',{pid:'1220089',mid:'122OD04006'});
	}
				
	// 构造函数
	var pay_tt_class = function()
	{
		var self = this;

		self.init();
						
	};
   
	// 添加方法与属性
	pay_tt_class.prototype = 
	{
		refresh : function()
		{
			var self = this;

			var $loading=$.loading
			({
				content:'加载中...'
			});

			utility.ajax_request
			({
				url : window.$__config.ajax_url.get_tt_pay_info,
				data : {
					quotes_id : _self.quotes_id
				},						
				success : function(res)
				{
					var content = res.result_data;	

					_self.$page_container.html('');
					
					_self.$page_container.html(tpl(content));

                    if(content.request_id){_self.request_id = content.request_id;}
                    if(content.total_amount){_self.order_total_amount = content.total_amount;}
                    if(content.pay_amount){_self.order_pay_amount = content.pay_amount;}

                    // 初始化优惠券
                    _self.coupon_obj = coupon.init
                    ({
                        container : $('[data-role="coupon-list-container"]'),
                        module_type : 'task_request',
                        request_id : _self.request_id,
                        quotes_id : _self.quotes_id,
                        order_total_amount :_self.order_total_amount,
                        order_pay_amount : _self.order_pay_amount,
                        page : 1

                    });


					// 安装事件
					self.setup_event();

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
		init : function()
		{
			var self = this;
			var $back_btn_html = back_btn.render();
			$header.prepend($back_btn_html);
			
			// 安装后退事件
			self.setup_back();
		},
		hide : function()
		{

		},
		// 安装后退按钮
		setup_back : function()
		{
			$('[data-role="page-back"]').on('tap',function()
			{
				if(App.isPaiApp)
				{
					App.app_back();
				}
			});
		},
		// 安装事件		
		setup_event : function()
		{
			var self = this;

			_self.$pay_li = $('[data-role="pay-li"]');			
			_self.$pay_btn = $('#pay-btn');
            _self.$select_ab_btn = $('[data-role="select-available-balance"]');
            _self.$less_money = $('[data-role="less-money"]');
            _self.$available_balance = $('[data-role="available_balance"]');
            _self.$need_price = $('[data-role="need-price"]');
            _self.ab = $('[data-role="available_balance"]').html();
            _self.total_price = $('[data-role="pay-amount"]').html();
            _self.$coupon_list_wrap = $('[data-role="coupon-list-wrap"]');
            _self.$coupon_money = $('[data-role="coupon-money"]');
            _self.$coupon_money_tag = $('[data-role="coupon-money-tag"]');
            _self.$coupon_money_text = $('[data-role="coupon-money-text"]');

            // 安装优惠券头部
            yueyue_header.render($('[data-role="nav-header"]')[0],
                {
                    left_text : '返回',
                    title:'可用优惠券',
                    right_text : '确定'
                });

            _self.$left_btn = $('[data-role="left-btn"]');
            _self.$right_btn = $('[data-role="right-btn"]');
			
			// 默认设置选中支付宝
			_self.selected_pay_action_type ='alipay_purse';

            // 默认设置选中余额
            _self.can_use_balance = true;

            // 选择余额支付
            _self.$select_ab_btn.on('click',function(ev)
            {
                var $cur_btn = $(ev.currentTarget);
                var $yes_tag = $cur_btn.find('[data-role="yes-tag"]');
                var tag = $yes_tag.hasClass('icon-select-active');

                if(tag)
                {
                    $yes_tag.addClass('icon-select-no').removeClass('icon-select-active');
                }
                else
                {
                    $yes_tag.removeClass('icon-select-no').addClass('icon-select-active');
                }

                _self.can_use_balance = $yes_tag.hasClass('icon-select-active');

                var pay_items_model =
                {
                    can_use_balance : _self.can_use_balance,
                    available_balance : _self.ab,
                    total_price : _self.total_price,
                    coupon : _self.coupon_obj.selected_coupon && _self.coupon_obj.selected_coupon.face_value
                };

                // 初始化表单
                self.count_money(pay_items_model);
            });
			
			// 选择支付方式选中
			_self.$pay_li.on('click',function(ev)
			{
				var $cur_btn = $(ev.currentTarget);
				var $yes_tag = $cur_btn.find('[data-role="yes-tag"]');

				var tag = $yes_tag.hasClass('icon-select-active');
				
				// 清空所有选中的，用于以后扩展
				_self.$pay_li.find('[data-role="yes-tag"]').removeClass('icon-select-active').addClass('icon-select-no');

                $yes_tag.addClass('icon-select-active').removeClass('icon-select-no');

                var pay_type =  $cur_btn.attr('data-pay-type');
				
				// 设置是否选中支付方式
				_self.selected_pay_action_type =pay_type;

                _self.$select_ab_btn.find('[data-role="yes-tag"]').removeClass('icon-select-active');

                if(_self.can_use_balance)
                {
                    _self.$select_ab_btn.find('[data-role="yes-tag"]').addClass('icon-select-active');
                }

			});

            // 关闭优惠券层
            _self.$left_btn.on('click',function()
            {
                _self.$page_container.removeClass('fn-hide');
                _self.$coupon_list_wrap.addClass('fn-hide');
            });

            // 关闭优惠券层
            _self.$right_btn.on('click',function()
            {
                _self.$page_container.removeClass('fn-hide');
                _self.$coupon_list_wrap.addClass('fn-hide');

                //要实现选中优惠券时候的处理
                var selected_coupon = _self.coupon_obj.selected_coupon;

                var pay_items_model =
                {
                    can_use_balance : _self.can_use_balance,
                    available_balance : _self.ab,
                    total_price : _self.total_price,
                    set_pay_type : false,
                    coupon : selected_coupon && selected_coupon.face_value
                };

                // 初始化表单
                self.count_money(pay_items_model);

                // 显示优惠信息
                _self.$coupon_money_tag.html(selected_coupon.batch_name);

                if(selected_coupon)
                {
                    _self.$coupon_money.find('[data-role="yes-tag"]').addClass('icon-select-active').removeClass('icon-select-no');
                }
                else
                {
                    _self.$coupon_money.find('[data-role="yes-tag"]').removeClass('icon-select-active').addClass('icon-select-no');
                }
            });

            // 进入优惠券列表
            _self.$coupon_money.on('click',function(ev)
            {
                _self.$page_container.addClass('fn-hide');
                _self.$coupon_list_wrap.removeClass('fn-hide');
            });
			
			// 确定按钮
			_self.$pay_btn.on('click',function()
			{
				App.analysis('eventtongji',{id:'1220090'});

				if(!_self.selected_pay_action_type)
				{
					alert('请选择支付方式');
					return;
				}
				
				if(confirm('确认支付?'))
				{
					var redirect_url = window.location.href.replace('pay.php','pay_success.php');

					var params = 
					{
						third_code : _self.selected_pay_action_type,
						quotes_id  : _self.quotes_id,
						redirect_url : redirect_url,
                        coupon_sn : (_self.coupon_obj.selected_coupon && _self.coupon_obj.selected_coupon.coupon_sn) || '',
                        available_balance : _self.ab,
                        is_available_balance : _self.$select_ab_btn.find('[data-role="yes-tag"]').hasClass('icon-select-active') ? 1 : 0

					};

					self.pay_action(params,
					{
						success :function(res)
						{
							var third_code = res.result_data && res.result_data.third_code;
							var channel_return = 'pay_success.php?'+res.result_data.payment_no;
							var result = res.result_data && res.result_data.result;
							
							if(result == 1)
							{
								switch(third_code)
								{
									// 支付宝支付，调用App接口
									case 'alipay_purse':
										App.alipay
										({
											alipayparams : res.result_data.request_data,
											payment_no : res.result_data.payment_no
										},function(data)
										{
											var result = utility.int(data.result);

											var text = self.after_pay_text(result);

											$.tips
											({
												content:text,
												stayTime:3000,
												type:'success'
											});

											if(result == 1 || result ==-1 || result == -2)
											{
												window.location.href = channel_return;
											}
										});
										break;
									// 微信支付
									case 'tenpay_wxapp':									
										App.wxpay(JSON.parse(res.result_data.request_data),function(data)
										{
											var result = utility.int(data.result);

											var text = self.after_pay_text(result);

											$.tips
											({
												content:text,
												stayTime:3000,
												type:'success'
											});

											if(result == 1 || result ==-1 || result == -2)
											{
												window.location.href = channel_return;
											}
										});
										break;
								}
							}
							else
							{
								$.tips
								({
									content:'余额支付成功',
									stayTime:3000,
									type:'success'
								});

								window.location.href = channel_return;
							}
							
							

							

							
						},
						error : function()
						{
							
						}
					});
				}
			});

            // 初始化计算
            var pay_items_model =
            {
                can_use_balance : _self.ab>0?true:false,
                available_balance : _self.ab,
                total_price : _self.total_price
            };

            // 初始化表单
            self.count_money(pay_items_model);
		},
        count_money : function(params)
        {
            var self = this;

            var total_price = utility.float(params.total_price);

            var available_balance = utility.float(params.available_balance);

            var set_pay_type = params.set_pay_type == null ? true : false;

            var coupon = params.coupon || 0;

            // 钱包要扣的钱
            var less_money = 0;

            // 使用优惠劵的时候
            if(coupon)
            {
                total_price = total_price - utility.float(coupon);
            }

            // 需要给的钱
            var must_pay_money = available_balance - total_price;

            // 使用余额支付
            if(params.can_use_balance)
            {

                less_money = must_pay_money;

                if(less_money <= 0)
                {
                    less_money = available_balance;
                }
                else
                {
                    less_money = total_price;

                    must_pay_money = 0;
                }

                _self.$select_ab_btn.find('[data-role="yes-tag"]').removeClass('icon-select-no').addClass('icon-select-active');

                // 余额完全够钱支付了订单
                if(must_pay_money>=0)
                {
                    self.clear_select();

                    self.control_other_pay_item({show:false});

                    console.log('余额完全够钱支付了订单');
                }
                // 余额不够钱支付订单的时候，需要混合支付
                else
                {

                    self.control_other_pay_item({show:true});

                    // 设置默认支付方式，计算优惠劵后不设置默认支付方式
                    if(set_pay_type)
                    {
                        //self.pay_item_obj._select_pay_type('alipay_purse');
                        $('[data-pay-type="'+_self.selected_pay_action_type+'"]').find('[data-role="yes-tag"]')
                            .removeClass('icon-select-no').addClass('icon-select-active');
                    }
                }
            }
            // 完全使用第三方支付
            else
            {
                // 需要给的钱就是总价 减去 优惠劵
                must_pay_money = total_price;

                self.clear_select(true);

                self.control_other_pay_item({show:true});

                // 设置默认支付方式，计算优惠劵后不设置默认支付方式
                if(set_pay_type)
                {
                    $('[data-pay-type="'+_self.selected_pay_action_type+'"]').find('[data-role="yes-tag"]')
                        .removeClass('icon-select-no').addClass('icon-select-active');
                }
            }

            self.must_pay_money = utility.format_float(must_pay_money,2);

            if(self.must_pay_money<0)
            {
                self.must_pay_money = self.must_pay_money * -1;
            }

            _self.$need_price.html(self.must_pay_money);
            _self.$less_money.html('-￥'+less_money);

            if(coupon)
            {
                _self.$coupon_money_text.html('-￥'+coupon);
            }
            else
            {
                _self.$coupon_money_text.html('');
            }



        },
		// tt 支付请求
		pay_action : function(params,callback)
		{
			var $loading=$.loading
			({
				content:'发送中...'
			});

			utility.ajax_request
			({
				url : window.$__config.ajax_url.pay_tt_action,
				data : params,			
				type : 'POST',	
				success : function(res)
				{
					$loading.loading("hide");
										
					if(res.result_data && res.result_data.result>0)
					{
						
						// 支付成功
						callback.success.call(this,res);
						
						var type = 'success';
					}
					else
					{
						// 支付失败

						var type = 'warn';

						$.tips
						({
							content:res.result_data.message,
							stayTime:3000,
							type:type
						});
					}

					 
   
				},
				error : function()
				{
					callback.error.call(this);

					$loading.loading("hide");

					$.tips
					({
						content:'网络异常',
						stayTime:3000,
						type:'warn'
					});
				}
			});
		},
        /**
         * 清除指定选择项
         * @param tag
         */
        clear_select : function(tag)
        {
            var self = this;

            var $yes_tag =_self.$pay_li.find('[data-role="yes-tag"]');

            if(tag)
            {
                _self.$select_ab_btn.find('[data-role="yes-tag"]')
                    .removeClass('icon-select-active').addClass('icon-select-no');
            }
            // 不传递指定标记，清除所有选择
            else
            {
                $yes_tag.removeClass('icon-select-active').addClass('icon-select-no');
            }
        },
        /**
         * 控制第三方支付显示隐藏
         * @param options
         */
        control_other_pay_item : function(options)
        {
            var self = this;

            if(options.show)
            {
                $('[data-role="other-pay-container"]').removeClass('fn-hide');
                $('[data-role="must-pay-container"]').removeClass('last-sec-container');
            }
            else
            {
                $('[data-role="other-pay-container"]').addClass('fn-hide');
                $('[data-role="must-pay-container"]').addClass('last-sec-container');
            }

            

        },
		after_pay_text : function(code) {
            var str = '';

            switch (utility.int(code)) {
                case 1:
                case -2:
                case -1:
                    str = '支付成功';
                    break;
                case 0:
                    str = '其它错误';
                    break;
                case -3:
                    str = '支付失败';
                    break;
                case -4:
                    str = '支付取消';
                    break;
            }

            return str;
        }
	};

	// 实例化tt支付类			
	var pt_obj = new pay_tt_class();		
	
	pt_obj.refresh();



})($,window);



 
});