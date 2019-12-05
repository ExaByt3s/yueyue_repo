define("pay_tt",function(a){"use strict";var e=a("common/widget/back_btn/main"),t=a("common/utility/index"),n=a("components/zepto/zepto.js"),o=a("components/fastclick/fastclick.js"),s=(a("yue_ui/frozen"),a("common/I_APP/I_APP")),l=a("coupon/list"),i=a("common/widget/yueyue_header/main");"addEventListener"in document&&document.addEventListener("DOMContentLoaded",function(){o.attach(document.body)},!1),function(a,n){s.isPaiApp&&s.check_login(function(a){return t.int(a.pocoid)?void 0:void s.openloginpage(function(a){"0000"==a.code&&t.refresh_page()})});var o=a("#nav-header"),c=Handlebars.template(function(a,e,t,n,o){this.compilerInfo=[4,">= 1.0.0"],t=this.merge(t,a.helpers),o=o||{};var s,l,i="",c="function",r=this.escapeExpression;return i+='<ul class="ui-list ui-list-text ui-border-b">\r\n	<li>\r\n		<div class="ui-avatar">\r\n		   <span style="background-image:url(\'',(l=t.user_icon)?s=l.call(e,{hash:{},data:o}):(l=e&&e.user_icon,s=typeof l===c?l.call(e,{hash:{},data:o}):l),i+=r(s)+'\')"></span>\r\n		</div>\r\n		<div class="ui-list-info ui-border-t">\r\n			<h4>',(l=t.user_nickname)?s=l.call(e,{hash:{},data:o}):(l=e&&e.user_nickname,s=typeof l===c?l.call(e,{hash:{},data:o}):l),i+=r(s)+"</h4>\r\n			<p>服务类型 ",(l=t.service_name)?s=l.call(e,{hash:{},data:o}):(l=e&&e.service_name,s=typeof l===c?l.call(e,{hash:{},data:o}):l),i+=r(s)+'</p>\r\n		</div>\r\n	</li>				\r\n</ul>\r\n<ul class="ui-list ui-list-text">\r\n	<li class="ui-border-t">\r\n		<div class="ui-txt-default ">支付类型：<span>',(l=t.pay_type)?s=l.call(e,{hash:{},data:o}):(l=e&&e.pay_type,s=typeof l===c?l.call(e,{hash:{},data:o}):l),i+=r(s)+'</span></div>\r\n	</li>	\r\n	<li class="ui-border-t">\r\n		<div class="ui-txt-default ">报价金额：<span>￥',(l=t.total_amount)?s=l.call(e,{hash:{},data:o}):(l=e&&e.total_amount,s=typeof l===c?l.call(e,{hash:{},data:o}):l),i+=r(s)+'</span></div>\r\n	</li>\r\n	<li class="ui-border-t">\r\n		<div class="ui-txt-default ">服务金：<span>￥<label data-role="pay-amount">',(l=t.pay_amount)?s=l.call(e,{hash:{},data:o}):(l=e&&e.pay_amount,s=typeof l===c?l.call(e,{hash:{},data:o}):l),i+=r(s)+'</label></span></div>\r\n	</li>\r\n</ul>\r\n\r\n<!--支付模块-->\r\n<ul class="ui-list ui-list-text mt20 fn-hide">\r\n	<li >\r\n		<div class="ui-txt-default ">钱包<span></span></div>\r\n	</li>\r\n    <li data-role="coupon-money">\r\n        <div class="ui-txt-default ">使用优惠券：<span><div class="ui-nowrap" style="width: 200px;" data-role="coupon-money-tag"></div></span></div>\r\n        <div class="ui-edge-right">\r\n            <span class="count-money-color" data-role="coupon-money-text"></span>\r\n            <i class="icon icon-select-no" data-role="yes-tag"></i>\r\n        </div>\r\n    </li>\r\n    <li data-role="select-available-balance">\r\n		<div class="ui-txt-default ">账户余额：<span>￥<label data-role="available_balance">',(l=t.available_balance)?s=l.call(e,{hash:{},data:o}):(l=e&&e.available_balance,s=typeof l===c?l.call(e,{hash:{},data:o}):l),i+=r(s)+'</label></span></div>\r\n		<div class="ui-edge-right">\r\n			<span class="count-money-color" data-role="less-money"></span>\r\n			<i class="icon icon-select-no" data-role="yes-tag"></i>\r\n		</div>\r\n	</li>					\r\n</ul>\r\n<ul class="ui-list ui-list-text ui-border-b mt20" data-role="must-pay-container">\r\n	<li class="ui-border-t">\r\n		<div class="ui-txt-default ">还需支付：<span class="count-money-color">￥<label data-role="need-price">',(l=t.pay_amount)?s=l.call(e,{hash:{},data:o}):(l=e&&e.pay_amount,s=typeof l===c?l.call(e,{hash:{},data:o}):l),i+=r(s)+'</label></span></div>\r\n	</li>	\r\n</ul>\r\n<ul class="ui-list ui-list-text last-container" data-role="other-pay-container">\r\n	<li class="ui-border-t" data-pay-type="alipay_purse" data-role="pay-li">\r\n		<div class="ui-txt-default ">\r\n			<div class="pay-type">\r\n				<i class="icon icon-zhifubao"></i>\r\n				<div class="ui-list-info ">\r\n					<h4 class="ui-nowrap" >支付宝支付</h4>\r\n					<p class="ui-nowrap">推荐有支付宝账号的用户使用</p>\r\n				</div>\r\n			</div>\r\n			<div class="ui-edge-right">\r\n				<i class="icon icon-select-no" data-role="yes-tag"></i>\r\n			</div>\r\n		</div>\r\n	</li>	\r\n	<li class="ui-border-t" data-pay-type="tenpay_wxapp" data-role="pay-li">\r\n		<div class="ui-txt-default ">\r\n			<div class="pay-type">\r\n				<i class="icon icon-wx-pay"></i>\r\n				<div class="ui-list-info ">\r\n					<h4 class="ui-nowrap" >微信支付</h4>\r\n					<p class="ui-nowrap">安装微信5.0及以上版本的使用</p>\r\n				</div>\r\n			</div>\r\n			<div class="ui-edge-right">\r\n				<i class="icon icon-select-no" data-role="yes-tag"></i>\r\n			</div>\r\n		</div>\r\n	</li>\r\n</ul>\r\n<!--支付模块-->\r\n<div class="buttom-btn-wrap">\r\n	<button class="ui-tt-pay-btn" id="pay-btn">\r\n		确认支付\r\n	</button>\r\n</div>'}),r={};r.quotes_id=a("#quotes_id").val(),r.$page_container=a('[data-role="page-container"]'),s.isPaiApp&&s.analysis("moduletongji",{pid:"1220089",mid:"122OD04006"});var d=function(){var a=this;a.init()};d.prototype={refresh:function(){var e=this,o=a.loading({content:"加载中..."});t.ajax_request({url:n.$__config.ajax_url.get_tt_pay_info,data:{quotes_id:r.quotes_id},success:function(t){var n=t.result_data;r.$page_container.html(""),r.$page_container.html(c(n)),n.request_id&&(r.request_id=n.request_id),n.total_amount&&(r.order_total_amount=n.total_amount),n.pay_amount&&(r.order_pay_amount=n.pay_amount),r.coupon_obj=l.init({container:a('[data-role="coupon-list-container"]'),module_type:"task_request",request_id:r.request_id,quotes_id:r.quotes_id,order_total_amount:r.order_total_amount,order_pay_amount:r.order_pay_amount,page:1}),e.setup_event(),o.loading("hide")},error:function(){o.loading("hide"),a.tips({content:"网络异常",stayTime:3e3,type:"warn"})}})},page_back:function(){},init:function(){var a=this,t=e.render();o.prepend(t),a.setup_back()},hide:function(){},setup_back:function(){a('[data-role="page-back"]').on("tap",function(){s.isPaiApp&&s.app_back()})},setup_event:function(){var e=this;r.$pay_li=a('[data-role="pay-li"]'),r.$pay_btn=a("#pay-btn"),r.$select_ab_btn=a('[data-role="select-available-balance"]'),r.$less_money=a('[data-role="less-money"]'),r.$available_balance=a('[data-role="available_balance"]'),r.$need_price=a('[data-role="need-price"]'),r.ab=a('[data-role="available_balance"]').html(),r.total_price=a('[data-role="pay-amount"]').html(),r.$coupon_list_wrap=a('[data-role="coupon-list-wrap"]'),r.$coupon_money=a('[data-role="coupon-money"]'),r.$coupon_money_tag=a('[data-role="coupon-money-tag"]'),r.$coupon_money_text=a('[data-role="coupon-money-text"]'),i.render(a('[data-role="nav-header"]')[0],{left_text:"返回",title:"可用优惠券",right_text:"确定"}),r.$left_btn=a('[data-role="left-btn"]'),r.$right_btn=a('[data-role="right-btn"]'),r.selected_pay_action_type="alipay_purse",r.can_use_balance=!0,r.$select_ab_btn.on("click",function(t){var n=a(t.currentTarget),o=n.find('[data-role="yes-tag"]'),s=o.hasClass("icon-select-active");s?o.addClass("icon-select-no").removeClass("icon-select-active"):o.removeClass("icon-select-no").addClass("icon-select-active"),r.can_use_balance=o.hasClass("icon-select-active");var l={can_use_balance:r.can_use_balance,available_balance:r.ab,total_price:r.total_price,coupon:r.coupon_obj.selected_coupon&&r.coupon_obj.selected_coupon.face_value};e.count_money(l)}),r.$pay_li.on("click",function(e){{var t=a(e.currentTarget),n=t.find('[data-role="yes-tag"]');n.hasClass("icon-select-active")}r.$pay_li.find('[data-role="yes-tag"]').removeClass("icon-select-active").addClass("icon-select-no"),n.addClass("icon-select-active").removeClass("icon-select-no");var o=t.attr("data-pay-type");r.selected_pay_action_type=o,r.$select_ab_btn.find('[data-role="yes-tag"]').removeClass("icon-select-active"),r.can_use_balance&&r.$select_ab_btn.find('[data-role="yes-tag"]').addClass("icon-select-active")}),r.$left_btn.on("click",function(){r.$page_container.removeClass("fn-hide"),r.$coupon_list_wrap.addClass("fn-hide")}),r.$right_btn.on("click",function(){r.$page_container.removeClass("fn-hide"),r.$coupon_list_wrap.addClass("fn-hide");var a=r.coupon_obj.selected_coupon,t={can_use_balance:r.can_use_balance,available_balance:r.ab,total_price:r.total_price,set_pay_type:!1,coupon:a&&a.face_value};e.count_money(t),r.$coupon_money_tag.html(a.batch_name),a?r.$coupon_money.find('[data-role="yes-tag"]').addClass("icon-select-active").removeClass("icon-select-no"):r.$coupon_money.find('[data-role="yes-tag"]').removeClass("icon-select-active").addClass("icon-select-no")}),r.$coupon_money.on("click",function(){r.$page_container.addClass("fn-hide"),r.$coupon_list_wrap.removeClass("fn-hide")}),r.$pay_btn.on("click",function(){if(s.analysis("eventtongji",{id:"1220090"}),!r.selected_pay_action_type)return void alert("请选择支付方式");if(confirm("确认支付?")){var o=n.location.href.replace("pay.php","pay_success.php"),l={third_code:r.selected_pay_action_type,quotes_id:r.quotes_id,redirect_url:o,coupon_sn:r.coupon_obj.selected_coupon&&r.coupon_obj.selected_coupon.coupon_sn||0,available_balance:r.ab,is_available_balance:r.$select_ab_btn.find('[data-role="yes-tag"]').hasClass("icon-select-active")};e.pay_action(l,{success:function(o){var l=o.result_data&&o.result_data.third_code,i="pay_success.php?"+o.result_data.payment_no;switch(l){case"alipay_purse":s.alipay({alipayparams:o.result_data.request_data,payment_no:o.result_data.payment_no},function(o){var l=t.int(o.result),c=e.after_pay_text(l);a.tips({content:c,stayTime:3e3,type:"success"}),(1==l||-1==l||-2==l)&&(s.openttpayfinish(),n.location.href=i)});break;case"tenpay_wxapp":s.wxpay(JSON.parse(o.result_data.request_data),function(o){var l=t.int(o.result),c=e.after_pay_text(l);a.tips({content:c,stayTime:3e3,type:"success"}),(1==l||-1==l||-2==l)&&(s.openttpayfinish(),n.location.href=i)})}},error:function(){}})}});var o={can_use_balance:r.ab>0?!0:!1,available_balance:r.ab,total_price:r.total_price};e.count_money(o)},count_money:function(e){var n=this,o=t.float(e.total_price),s=t.float(e.available_balance),l=null==e.set_pay_type?!0:!1,i=e.coupon||0,c=0;i&&(o-=t.float(i));var d=s-o;e.can_use_balance?(c=d,0>=c?c=s:(c=o,d=0),r.$select_ab_btn.find('[data-role="yes-tag"]').removeClass("icon-select-no").addClass("icon-select-active"),d>=0?(n.clear_select(),n.control_other_pay_item({show:!1}),console.log("余额完全够钱支付了订单")):(n.control_other_pay_item({show:!0}),l&&a('[data-pay-type="'+r.selected_pay_action_type+'"]').find('[data-role="yes-tag"]').removeClass("icon-select-no").addClass("icon-select-active"))):(d=o,n.clear_select(!0),n.control_other_pay_item({show:!0}),l&&a('[data-pay-type="'+r.selected_pay_action_type+'"]').find('[data-role="yes-tag"]').removeClass("icon-select-no").addClass("icon-select-active")),n.must_pay_money=t.format_float(d,2),n.must_pay_money<0&&(n.must_pay_money=-1*n.must_pay_money),r.$need_price.html(n.must_pay_money),r.$less_money.html("-￥"+c),r.$coupon_money_text.html(i?"-￥"+i:"")},pay_action:function(e,o){var s=a.loading({content:"发送中..."});t.ajax_request({url:n.$__config.ajax_url.pay_tt_action,data:e,type:"POST",success:function(e){if(s.loading("hide"),e.result_data&&e.result_data.result>0){o.success.call(this,e);var t="success"}else{var t="warn";a.tips({content:e.result_data.message,stayTime:3e3,type:t})}},error:function(){o.error.call(this),s.loading("hide"),a.tips({content:"网络异常",stayTime:3e3,type:"warn"})}})},clear_select:function(a){var e=r.$pay_li.find('[data-role="yes-tag"]');a?r.$select_ab_btn.find('[data-role="yes-tag"]').removeClass("icon-select-active").addClass("icon-select-no"):e.removeClass("icon-select-active").addClass("icon-select-no")},control_other_pay_item:function(e){e.show?(a('[data-role="other-pay-container"]').removeClass("fn-hide"),a('[data-role="must-pay-container"]').removeClass("last-sec-container")):(a('[data-role="other-pay-container"]').addClass("fn-hide"),a('[data-role="must-pay-container"]').addClass("last-sec-container"))},after_pay_text:function(a){var e="";switch(t.int(a)){case 1:case-2:case-1:e="支付成功";break;case 0:e="其它错误";break;case-3:e="支付失败";break;case-4:e="支付取消"}return e}};var _=new d;_.refresh()}(n,window)});