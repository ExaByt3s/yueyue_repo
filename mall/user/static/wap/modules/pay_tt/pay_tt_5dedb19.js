define("pay_tt",function(a){"use strict";var e=a("common/widget/back_btn/main"),t=a("common/utility/index"),n=a("common/ua/index"),o=a("components/zepto/zepto.js"),s=a("components/fastclick/fastclick.js"),i=(a("yue_ui/frozen"),a("common/I_APP/I_APP")),l=a("coupon/list"),r=a("common/widget/yueyue_header/main"),c=a("common/widget/header/main");"addEventListener"in document&&document.addEventListener("DOMContentLoaded",function(){s.attach(document.body)},!1),function(a,o){var s=a("#nav-header"),d=Handlebars.template(function(a,e,t,n,o){function s(a,e){var n,o,s="";return s+='\r\n<ul class="info ui-list ui-list-text pt15 pr15 pb5">\r\n	<li>\r\n		<p>��Ʒ���ƣ�',(o=t.goods_name)?n=o.call(a,{hash:{},data:e}):(o=a&&a.goods_name,n=typeof o===u?o.call(a,{hash:{},data:e}):o),s+=y(n)+"</p>\r\n	</li>\r\n    <li>\r\n        <div>���",n=t["if"].call(a,a&&a.prices_spec,{hash:{},inverse:v.program(4,l,e),fn:v.program(2,i,e),data:e}),(n||0===n)&&(s+=n),s+="</div>\r\n    </li>\r\n    <li>\r\n        <div>ȫ�",(o=t.amount)?n=o.call(a,{hash:{},data:e}):(o=a&&a.amount,n=typeof o===u?o.call(a,{hash:{},data:e}):o),s+=y(n)+"</div>\r\n    </li>\r\n    <li>\r\n        <div>���ڣ�",(o=t.service_time_str)?n=o.call(a,{hash:{},data:e}):(o=a&&a.service_time_str,n=typeof o===u?o.call(a,{hash:{},data:e}):o),s+=y(n)+"</div>\r\n    </li>\r\n</ul>\r\n"}function i(a,e){var n,o;return(o=t.prices_spec)?n=o.call(a,{hash:{},data:e}):(o=a&&a.prices_spec,n=typeof o===u?o.call(a,{hash:{},data:e}):o),y(n)}function l(){return"��"}function r(){return'\r\n        <li class="ui-border-t" data-pay-type="alipay_purse" data-role="pay-li">\r\n            <div class="ui-txt-default ">\r\n                <div class="pay-type">\r\n                    <i class="icon icon-zhifubao"></i>\r\n                    <div class="ui-list-info ">\r\n                        <h4 class="ui-nowrap" >֧����֧��</h4>\r\n                        <p class="ui-nowrap">�Ƽ���֧�����˺ŵ��û�ʹ��</p>\r\n                    </div>\r\n                </div>\r\n                <div class="ui-edge-right">\r\n                    <i class="icon icon-select-no" data-role="yes-tag"></i>\r\n                </div>\r\n            </div>\r\n        </li>\r\n        <li class="ui-border-t" data-pay-type="tenpay_wxapp" data-role="pay-li">\r\n            <div class="ui-txt-default ">\r\n                <div class="pay-type">\r\n                    <i class="icon icon-wx-pay"></i>\r\n                    <div class="ui-list-info ">\r\n                        <h4 class="ui-nowrap" >΢��֧��</h4>\r\n                        <p class="ui-nowrap">��װ΢��5.0�����ϰ汾��ʹ��</p>\r\n                    </div>\r\n                </div>\r\n                <div class="ui-edge-right">\r\n                    <i class="icon icon-select-no" data-role="yes-tag"></i>\r\n                </div>\r\n            </div>\r\n        </li>\r\n        '}function c(){return'\r\n            <li class="ui-border-t" data-pay-type="tenpay_wxpub" data-role="pay-li">\r\n                <div class="ui-txt-default ">\r\n                    <div class="pay-type">\r\n                        <i class="icon icon-wx-pay"></i>\r\n                        <div class="ui-list-info ">\r\n                            <h4 class="ui-nowrap" >΢��֧��</h4>\r\n                            <p class="ui-nowrap">��װ΢��5.0�����ϰ汾��ʹ��</p>\r\n                        </div>\r\n                    </div>\r\n                    <div class="ui-edge-right">\r\n                        <i class="icon icon-select-no" data-role="yes-tag"></i>\r\n                    </div>\r\n                </div>\r\n            </li>\r\n        '}function d(){return'\r\n        <li class="ui-border-t" data-pay-type="alipay_wap" data-role="pay-li">\r\n                <div class="ui-txt-default ">\r\n                    <div class="pay-type">\r\n                        <i class="icon icon-zhifubao"></i>\r\n                        <div class="ui-list-info ">\r\n                            <h4 class="ui-nowrap" >֧����֧��</h4>\r\n                            <p class="ui-nowrap">�Ƽ���֧�����˺ŵ��û�ʹ��</p>\r\n                        </div>\r\n                    </div>\r\n                    <div class="ui-edge-right">\r\n                        <i class="icon icon-select-no" data-role="yes-tag"></i>\r\n                    </div>\r\n                </div>\r\n            </li>\r\n        '}this.compilerInfo=[4,">= 1.0.0"],t=this.merge(t,a.helpers),o=o||{};var p,_="",u="function",y=this.escapeExpression,v=this;return _+='<div id="global-header"></div>\r\n\r\n',p=t.each.call(e,(p=e&&e.data,null==p||p===!1?p:p.detail_list),{hash:{},inverse:v.noop,fn:v.program(1,s,o),data:o}),(p||0===p)&&(_+=p),_+='\r\n\r\n\r\n\r\n<div class="mt30 pl15 mb10">Ӧ�����</div>\r\n\r\n<!--֧��ģ��-->\r\n<ul class="ui-list ui-list-text " class="ui-border-t">\r\n    <li class="ui-border-t">\r\n        <div class="ui-txt-default ">֧���ܼۣ�<span >��<label data-role="pay-amount">'+y((p=e&&e.data,p=null==p||p===!1?p:p.total_amount,typeof p===u?p.apply(e):p))+'</label></span></div>\r\n    </li>\r\n    <li data-role="coupon-money" class="ui-border-t">\r\n        <div class="ui-txt-default ">ʹ��/�һ��Ż�ȯ��<span><div class="ui-nowrap" style="width: 200px;" data-role="coupon-money-tag"></div></span></div>\r\n        <div class="ui-edge-right">\r\n            <span class="count-money-color" data-role="coupon-money-text"></span>\r\n            <i class="icon icon-select-no" data-role="yes-tag"></i>\r\n        </div>\r\n    </li>\r\n    <li data-role="select-available-balance" class="ui-border-t">\r\n		<div class="ui-txt-default ">Ǯ��<span>����<label data-role="available_balance">'+y((p=e&&e.data,p=null==p||p===!1?p:p.available_balance,typeof p===u?p.apply(e):p))+'</label></span>����<span class="count-money-color" data-role="less-money"></span></div>\r\n		<div class="ui-edge-right">\r\n\r\n			<i class="icon icon-select-no" data-role="yes-tag"></i>\r\n		</div>\r\n	</li>\r\n    <li class="ui-border-t">\r\n        <div class="ui-txt-default ">����֧����<span class="count-money-color_v2 fb">��<label data-role="need-price">'+y((p=e&&e.data,p=null==p||p===!1?p:p.pay_amount,typeof p===u?p.apply(e):p))+'</label></span></div>\r\n    </li>\r\n</ul>\r\n<ul class="ui-list ui-list-text ui-border-b mt20 fn-hide" style="margin-bottom: 0;"  data-role="must-pay-container"></ul>\r\n\r\n<div data-role="other-pay-container">\r\n    <div class="mt30 pl15 mb10">֧����ʽ</div>\r\n    <ul class="ui-list ui-list-text " >\r\n        ',p=t["if"].call(e,(p=e&&e.data,null==p||p===!1?p:p.is_yueyue_app),{hash:{},inverse:v.noop,fn:v.program(6,r,o),data:o}),(p||0===p)&&(_+=p),_+="\r\n        ",p=t["if"].call(e,(p=e&&e.data,null==p||p===!1?p:p.is_weixin),{hash:{},inverse:v.noop,fn:v.program(8,c,o),data:o}),(p||0===p)&&(_+=p),_+="\r\n        ",p=t["if"].call(e,(p=e&&e.data,null==p||p===!1?p:p.is_zfb_wap),{hash:{},inverse:v.noop,fn:v.program(10,d,o),data:o}),(p||0===p)&&(_+=p),_+='\r\n    </ul>\r\n\r\n    <div style="height:100px;" class="bg"></div>\r\n\r\n</div>\r\n\r\n\r\n\r\n\r\n<div class="last-container fn-hide"></div>\r\n<!--֧��ģ��-->\r\n<div class="buttom-btn-wrap ui-border-t">\r\n    <div class="pl10 text-info">\r\n        ����֧����<span class="count-money-color_v2 red-font" style="font-size: 18px;">��<label data-role="need-price">'+y((p=e&&e.data,p=null==p||p===!1?p:p.pay_amount,typeof p===u?p.apply(e):p))+'</label></span>\r\n    </div>\r\n    <div class="right">\r\n        <button class="ui-tt-pay-btn " id="pay-btn">\r\n            <span class="ui-button-content" ><i class="icon icon-btn-icon-fk "></i></span>\r\n            <span class="txt">ȥ֧��</span>\r\n        </button>\r\n    </div>\r\n\r\n</div>'}),p={};p.order_sn=a("#order_sn").val(),p.$page_container=a('[data-role="page-container"]'),i.isPaiApp;var _=function(){var a=this;a.init()};_.prototype={refresh:function(){var e=this,n=a.loading({content:"������..."});t.ajax_request({url:o.$__config.ajax_url.get_tt_pay_info,data:{order_sn:p.order_sn},success:function(t){var o=t.result_data;p.$page_container.html(""),p.$page_container.html(d(o)),o.request_id&&(p.request_id=o.request_id),o.total_amount&&(p.order_total_amount=o.total_amount),o.pay_amount&&(p.order_pay_amount=o.pay_amount),p.coupon_obj=l.init({container:a('[data-role="coupon-list-container"]'),order_sn:p.order_sn,order_total_amount:p.order_total_amount,order_pay_amount:p.order_pay_amount,page:1,extend_params:{module_type:"mall_order"},success:function(){var a=p.coupon_obj.selected_coupon,t={can_use_balance:p.can_use_balance,available_balance:p.ab,total_price:p.total_price,set_pay_type:!1,coupon:a&&a.face_value};e.count_money(t),p.$coupon_money_tag.html(a?a.batch_name:""),a?p.$coupon_money.find('[data-role="yes-tag"]').addClass("icon-select-active").removeClass("icon-select-no"):p.$coupon_money.find('[data-role="yes-tag"]').removeClass("icon-select-active").addClass("icon-select-no")}}),c.init({ele:a("#global-header"),title:"֧��",header_show:!0,right_icon_show:!1,share_icon:{show:!1,content:""},omit_icon:{show:!1,content:""},show_txt:{show:!1,content:"�༭"}}),e.setup_event(),n.loading("hide")},error:function(){n.loading("hide"),a.tips({content:"�����쳣",stayTime:3e3,type:"warn"})}})},page_back:function(){},init:function(){var a=this,t=e.render();s.prepend(t),a.setup_back()},hide:function(){},setup_back:function(){a('[data-role="page-back"]').on("tap",function(){i.isPaiApp&&i.app_back()})},setup_event:function(){var e=this;p.$pay_li=a('[data-role="pay-li"]'),p.$pay_btn=a("#pay-btn"),p.$select_ab_btn=a('[data-role="select-available-balance"]'),p.$less_money=a('[data-role="less-money"]'),p.$available_balance=a('[data-role="available_balance"]'),p.$need_price=a('[data-role="need-price"]'),p.ab=a('[data-role="available_balance"]').html(),p.total_price=a('[data-role="pay-amount"]').html(),p.$coupon_list_wrap=a('[data-role="coupon-list-wrap"]'),p.$coupon_money=a('[data-role="coupon-money"]'),p.$coupon_money_tag=a('[data-role="coupon-money-tag"]'),p.$coupon_money_text=a('[data-role="coupon-money-text"]');var s=o.location;s.origin||(s.origin=s.protocol+"//"+s.hostname+(s.port?":"+s.port:"")),r.render(a('[data-role="nav-header"]')[0],{left_text:"����",title:"�����Ż�ȯ",right_text:"ȷ��"}),p.$left_btn=a('[data-role="left-btn"]'),p.$right_btn=a('[data-role="right-btn"]'),p.selected_pay_action_type=i.isPaiApp?"alipay_purse":n.is_weixin?"tenpay_wxpub":"alipay_wap",p.can_use_balance=!0,p.$select_ab_btn.on("click",function(t){var n=a(t.currentTarget),o=n.find('[data-role="yes-tag"]'),s=o.hasClass("icon-select-active");s?o.addClass("icon-select-no").removeClass("icon-select-active"):o.removeClass("icon-select-no").addClass("icon-select-active"),p.can_use_balance=o.hasClass("icon-select-active");var i={can_use_balance:p.can_use_balance,available_balance:p.ab,total_price:p.total_price,coupon:p.coupon_obj.selected_coupon&&p.coupon_obj.selected_coupon.face_value};e.count_money(i)}),p.$pay_li.on("click",function(e){{var t=a(e.currentTarget),n=t.find('[data-role="yes-tag"]');n.hasClass("icon-select-active")}p.$pay_li.find('[data-role="yes-tag"]').removeClass("icon-select-active").addClass("icon-select-no"),n.addClass("icon-select-active").removeClass("icon-select-no");var o=t.attr("data-pay-type");p.selected_pay_action_type=o,p.$select_ab_btn.find('[data-role="yes-tag"]').removeClass("icon-select-active"),p.can_use_balance&&p.$select_ab_btn.find('[data-role="yes-tag"]').addClass("icon-select-active")}),p.$left_btn.on("click",function(){p.$page_container.removeClass("fn-hide"),p.$coupon_list_wrap.addClass("fn-hide"),n.is_yue_app||a("main").css("padding-top","45px")}),p.$right_btn.on("click",function(){p.$page_container.removeClass("fn-hide"),p.$coupon_list_wrap.addClass("fn-hide");var t=p.coupon_obj.selected_coupon,o={can_use_balance:p.can_use_balance,available_balance:p.ab,total_price:p.total_price,set_pay_type:!1,coupon:t&&t.face_value};e.count_money(o),p.$coupon_money_tag.html(t?t.batch_name:""),t?p.$coupon_money.find('[data-role="yes-tag"]').addClass("icon-select-active").removeClass("icon-select-no"):p.$coupon_money.find('[data-role="yes-tag"]').removeClass("icon-select-active").addClass("icon-select-no"),n.is_yue_app||a("main").css("padding-top","45px")}),p.$coupon_money.on("click",function(){p.$page_container.addClass("fn-hide"),p.$coupon_list_wrap.removeClass("fn-hide"),n.is_yue_app||a("main").css("padding-top",0)}),p.$pay_btn.on("click",function(){if(!p.selected_pay_action_type)return void alert("��ѡ��֧����ʽ");if(confirm("ȷ��֧��?")){var n=o.location.href.replace("pay.php","success.php"),l={third_code:p.selected_pay_action_type,order_sn:p.order_sn,redirect_url:n,coupon_sn:p.coupon_obj.selected_coupon&&p.coupon_obj.selected_coupon.coupon_sn||"",available_balance:p.ab,is_available_balance:p.$select_ab_btn.find('[data-role="yes-tag"]').hasClass("icon-select-active")?1:0};if(a("#submit_token").val())return void(o.location.href="../notice/index.php?type=pay");if("tenpay_wxpub"==p.selected_pay_action_type){var r=l;return delete r.redirect_url,r.redirect_url=s.origin+s.pathname+"success.php?order_sn="+p.order_sn,r=a.extend(r,{type:"mall_order"}),r=a.param(r),void(o.location.href="http://yp.yueus.com/m/pay_jump_v2.php?"+r)}"alipay_wap"==p.selected_pay_action_type&&(l.redirect_url=s.origin+s.pathname+"success.php?order_sn="+p.order_sn),e.pay_action(l,{success:function(n){var s=n.result_data&&n.result_data.third_code,l="success.php?order_sn="+n.result_data.order_sn,r=n.result_data&&n.result_data.result;if(1==r)switch(s){case"alipay_purse":i.alipay({alipayparams:n.result_data.request_data,payment_no:n.result_data.payment_no},function(n){var s=t.int(n.result),i=e.after_pay_text(s);a.tips({content:i,stayTime:3e3,type:"success"}),(1==s||-1==s||-2==s)&&(a("#submit_token").val((new Date).getTime()),o.location.href=l)});break;case"tenpay_wxapp":i.wxpay(JSON.parse(n.result_data.request_data),function(n){var s=t.int(n.result),i=e.after_pay_text(s);a.tips({content:i,stayTime:3e3,type:"success"}),(1==s||-1==s||-2==s)&&(a("#submit_token").val((new Date).getTime()),o.location.href=l)});break;case"alipay_wap":if(1==r)o.location.href=n.result_data.request_data;else{var c=e.after_pay_text(r);a.tips({content:c,stayTime:3e3,type:"warn"})}}else a.tips({content:"���֧���ɹ�",stayTime:3e3,type:"success"}),a("#submit_token").val((new Date).getTime()),o.location.href=l},error:function(){}})}});var l={can_use_balance:p.ab>0?!0:!1,available_balance:p.ab,total_price:p.total_price};e.count_money(l)},count_money:function(e){var n=this,o=t.float(e.total_price),s=t.float(e.available_balance),i=null==e.set_pay_type?!0:!1,l=e.coupon||0,r=0;l&&(o-=t.float(l));var c=s-o;e.can_use_balance?(r=c,0>=r?r=s:(r=o,c=0),p.$select_ab_btn.find('[data-role="yes-tag"]').removeClass("icon-select-no").addClass("icon-select-active"),c>=0?(n.clear_select(),n.control_other_pay_item({show:!1}),console.log("�����ȫ��Ǯ֧���˶���")):(n.control_other_pay_item({show:!0}),i&&a('[data-pay-type="'+p.selected_pay_action_type+'"]').find('[data-role="yes-tag"]').removeClass("icon-select-no").addClass("icon-select-active"))):(c=o,n.clear_select(!0),n.control_other_pay_item({show:!0}),i&&a('[data-pay-type="'+p.selected_pay_action_type+'"]').find('[data-role="yes-tag"]').removeClass("icon-select-no").addClass("icon-select-active")),n.must_pay_money=t.format_float(c,2),n.must_pay_money<0&&(n.must_pay_money=-1*n.must_pay_money),p.$need_price.html(n.must_pay_money),p.$less_money.html("-��"+r),p.$coupon_money_text.html(l?"-��"+l:"")},pay_action:function(e,n){var s=a.loading({content:"������..."});t.ajax_request({url:o.$__config.ajax_url.pay_tt_action,data:e,type:"POST",success:function(e){if(s.loading("hide"),e.result_data&&e.result_data.result>0){n.success.call(this,e);var t="success"}else{var t="warn";a.tips({content:e.result_data.message,stayTime:3e3,type:t})}},error:function(){n.error.call(this),s.loading("hide"),a.tips({content:"�����쳣",stayTime:3e3,type:"warn"})}})},clear_select:function(a){var e=p.$pay_li.find('[data-role="yes-tag"]');a?p.$select_ab_btn.find('[data-role="yes-tag"]').removeClass("icon-select-active").addClass("icon-select-no"):e.removeClass("icon-select-active").addClass("icon-select-no")},control_other_pay_item:function(e){e.show?(a('[data-role="other-pay-container"]').removeClass("fn-hide"),a('[data-role="must-pay-container"]').removeClass("last-sec-container")):(a('[data-role="other-pay-container"]').addClass("fn-hide"),a('[data-role="must-pay-container"]').addClass("last-sec-container"))},after_pay_text:function(a){var e="";switch(t.int(a)){case 1:case-2:case-1:e="֧���ɹ�";break;case 0:e="��������";break;case-3:e="֧��ʧ��";break;case-4:e="֧��ȡ��"}return e}};var u=new _;u.refresh()}(o,window)});