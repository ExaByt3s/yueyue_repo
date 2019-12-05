/**
 * ֧��ҳ��
   hudw 2015.4.15
**/
"use strict";

var back_btn = require('../common/widget/back_btn/main');
var utility = require('../common/utility/index');
var $ = require('zepto');
var fastclick = require('fastclick');
var yue_ui = require('../yue_ui/frozen');
var App =  require('../common/I_APP/I_APP');
var coupon = require('../coupon/list');
var yueyue_header = require('../common/widget/yueyue_header/main');

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
         * ��ȡ������Ϣ������ר����app
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

	var tpl = __inline('./pay_tt_info.tmpl');

	var _self = {};

	// Ĭ�ϲ���
	var defaults=
	{
		title : '����'
	};
	
	// ��ȡ����		
	_self.quotes_id = $('#quotes_id').val();

	// ��ʼ��dom
	_self.$page_container = $('[data-role="page-container"]');		

	// ��ʼ��ͳ�Ƶ�
	if(App.isPaiApp)
	{
		App.analysis('moduletongji',{pid:'1220089',mid:'122OD04006'});
	}
				
	// ���캯��
	var pay_tt_class = function()
	{
		var self = this;

		self.init();
						
	};
   
	// ��ӷ���������
	pay_tt_class.prototype = 
	{
		refresh : function()
		{
			var self = this;

			var $loading=$.loading
			({
				content:'������...'
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

                    // ��ʼ���Ż�ȯ
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


					// ��װ�¼�
					self.setup_event();

					$loading.loading("hide");
				},
				error : function()
				{
					$loading.loading("hide");

					$.tips
					({
						content : '�����쳣',
						stayTime:3000,
				        type:"warn"
					});
				}
			});
		},
		page_back : function()
		{

		},
		// ��ʼ��					
		init : function()
		{
			var self = this;
			var $back_btn_html = back_btn.render();
			$header.prepend($back_btn_html);
			
			// ��װ�����¼�
			self.setup_back();
		},
		hide : function()
		{

		},
		// ��װ���˰�ť
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
		// ��װ�¼�		
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

            // ��װ�Ż�ȯͷ��
            yueyue_header.render($('[data-role="nav-header"]')[0],
                {
                    left_text : '����',
                    title:'�����Ż�ȯ',
                    right_text : 'ȷ��'
                });

            _self.$left_btn = $('[data-role="left-btn"]');
            _self.$right_btn = $('[data-role="right-btn"]');
			
			// Ĭ������ѡ��֧����
			_self.selected_pay_action_type ='alipay_purse';

            // Ĭ������ѡ�����
            _self.can_use_balance = true;

            // ѡ�����֧��
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

                // ��ʼ����
                self.count_money(pay_items_model);
            });
			
			// ѡ��֧����ʽѡ��
			_self.$pay_li.on('click',function(ev)
			{
				var $cur_btn = $(ev.currentTarget);
				var $yes_tag = $cur_btn.find('[data-role="yes-tag"]');

				var tag = $yes_tag.hasClass('icon-select-active');
				
				// �������ѡ�еģ������Ժ���չ
				_self.$pay_li.find('[data-role="yes-tag"]').removeClass('icon-select-active').addClass('icon-select-no');

                $yes_tag.addClass('icon-select-active').removeClass('icon-select-no');

                var pay_type =  $cur_btn.attr('data-pay-type');
				
				// �����Ƿ�ѡ��֧����ʽ
				_self.selected_pay_action_type =pay_type;

                _self.$select_ab_btn.find('[data-role="yes-tag"]').removeClass('icon-select-active');

                if(_self.can_use_balance)
                {
                    _self.$select_ab_btn.find('[data-role="yes-tag"]').addClass('icon-select-active');
                }

			});

            // �ر��Ż�ȯ��
            _self.$left_btn.on('click',function()
            {
                _self.$page_container.removeClass('fn-hide');
                _self.$coupon_list_wrap.addClass('fn-hide');
            });

            // �ر��Ż�ȯ��
            _self.$right_btn.on('click',function()
            {
                _self.$page_container.removeClass('fn-hide');
                _self.$coupon_list_wrap.addClass('fn-hide');

                //Ҫʵ��ѡ���Ż�ȯʱ��Ĵ���
                var selected_coupon = _self.coupon_obj.selected_coupon;

                var pay_items_model =
                {
                    can_use_balance : _self.can_use_balance,
                    available_balance : _self.ab,
                    total_price : _self.total_price,
                    set_pay_type : false,
                    coupon : selected_coupon && selected_coupon.face_value
                };

                // ��ʼ����
                self.count_money(pay_items_model);

                // ��ʾ�Ż���Ϣ
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

            // �����Ż�ȯ�б�
            _self.$coupon_money.on('click',function(ev)
            {
                _self.$page_container.addClass('fn-hide');
                _self.$coupon_list_wrap.removeClass('fn-hide');
            });
			
			// ȷ����ť
			_self.$pay_btn.on('click',function()
			{
				App.analysis('eventtongji',{id:'1220090'});

				if(!_self.selected_pay_action_type)
				{
					alert('��ѡ��֧����ʽ');
					return;
				}
				
				if(confirm('ȷ��֧��?'))
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
									// ֧����֧��������App�ӿ�
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
									// ΢��֧��
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
									content:'���֧���ɹ�',
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

            // ��ʼ������
            var pay_items_model =
            {
                can_use_balance : _self.ab>0?true:false,
                available_balance : _self.ab,
                total_price : _self.total_price
            };

            // ��ʼ����
            self.count_money(pay_items_model);
		},
        count_money : function(params)
        {
            var self = this;

            var total_price = utility.float(params.total_price);

            var available_balance = utility.float(params.available_balance);

            var set_pay_type = params.set_pay_type == null ? true : false;

            var coupon = params.coupon || 0;

            // Ǯ��Ҫ�۵�Ǯ
            var less_money = 0;

            // ʹ���Ż݄���ʱ��
            if(coupon)
            {
                total_price = total_price - utility.float(coupon);
            }

            // ��Ҫ����Ǯ
            var must_pay_money = available_balance - total_price;

            // ʹ�����֧��
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

                // �����ȫ��Ǯ֧���˶���
                if(must_pay_money>=0)
                {
                    self.clear_select();

                    self.control_other_pay_item({show:false});

                    console.log('�����ȫ��Ǯ֧���˶���');
                }
                // ����Ǯ֧��������ʱ����Ҫ���֧��
                else
                {

                    self.control_other_pay_item({show:true});

                    // ����Ĭ��֧����ʽ�������Ż݄�������Ĭ��֧����ʽ
                    if(set_pay_type)
                    {
                        //self.pay_item_obj._select_pay_type('alipay_purse');
                        $('[data-pay-type="'+_self.selected_pay_action_type+'"]').find('[data-role="yes-tag"]')
                            .removeClass('icon-select-no').addClass('icon-select-active');
                    }
                }
            }
            // ��ȫʹ�õ�����֧��
            else
            {
                // ��Ҫ����Ǯ�����ܼ� ��ȥ �Ż݄�
                must_pay_money = total_price;

                self.clear_select(true);

                self.control_other_pay_item({show:true});

                // ����Ĭ��֧����ʽ�������Ż݄�������Ĭ��֧����ʽ
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
            _self.$less_money.html('-��'+less_money);

            if(coupon)
            {
                _self.$coupon_money_text.html('-��'+coupon);
            }
            else
            {
                _self.$coupon_money_text.html('');
            }



        },
		// tt ֧������
		pay_action : function(params,callback)
		{
			var $loading=$.loading
			({
				content:'������...'
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
						
						// ֧���ɹ�
						callback.success.call(this,res);
						
						var type = 'success';
					}
					else
					{
						// ֧��ʧ��

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
						content:'�����쳣',
						stayTime:3000,
						type:'warn'
					});
				}
			});
		},
        /**
         * ���ָ��ѡ����
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
            // ������ָ����ǣ��������ѡ��
            else
            {
                $yes_tag.removeClass('icon-select-active').addClass('icon-select-no');
            }
        },
        /**
         * ���Ƶ�����֧����ʾ����
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
                    str = '֧���ɹ�';
                    break;
                case 0:
                    str = '��������';
                    break;
                case -3:
                    str = '֧��ʧ��';
                    break;
                case -4:
                    str = '֧��ȡ��';
                    break;
            }

            return str;
        }
	};

	// ʵ����tt֧����			
	var pt_obj = new pay_tt_class();		
	
	pt_obj.refresh();



})($,window);



