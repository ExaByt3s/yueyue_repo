define("option_service/pc/option_service",function(e){var i=e("components/jquery/jquery.js"),n=Handlebars.template(function(e,i,n,a,s){return this.compilerInfo=[4,">= 1.0.0"],n=this.merge(n,e.helpers),s=s||{},'<div class="options_service_panel fn-hide" data-role="options_service_panel">\r\n    <div class="ds-box pack-center service_part_empty">----未选择发送的消费者----</div>\r\n</div>'}),a=Handlebars.template(function(e,i,n,a,s){function t(e,i){var a,s,t="";return t+='\r\n    <div class="ds-box orient-h inner_service_item" data-role="part_service_item" data_goods_id="',(s=n.goods_id)?a=s.call(e,{hash:{},data:i}):(s=e&&e.goods_id,a=typeof s===r?s.call(e,{hash:{},data:i}):s),t+=c(a)+'">\r\n        <div class="ds-box info-main flex-1">\r\n            <div class="ds-box img_con align-center">\r\n                <img src="',(s=n.images)?a=s.call(e,{hash:{},data:i}):(s=e&&e.images,a=typeof s===r?s.call(e,{hash:{},data:i}):s),t+=c(a)+'" style="width: 40px;height: 40px;border-radius: 2px;"/>\r\n            </div>\r\n            <div class="ds-box orient-v info-con flex-1 pack-center">\r\n                <div class="title">',(s=n.titles)?a=s.call(e,{hash:{},data:i}):(s=e&&e.titles,a=typeof s===r?s.call(e,{hash:{},data:i}):s),t+=c(a)+'</div>\r\n                <div class="ds-box p-g-con">\r\n                    <div class="prices">￥',(s=n.prices)?a=s.call(e,{hash:{},data:i}):(s=e&&e.prices,a=typeof s===r?s.call(e,{hash:{},data:i}):s),t+=c(a)+'</div>\r\n                    <div class="goods_id">商品ID:',(s=n.goods_id)?a=s.call(e,{hash:{},data:i}):(s=e&&e.goods_id,a=typeof s===r?s.call(e,{hash:{},data:i}):s),t+=c(a)+'</div>\r\n                </div>\r\n            </div>\r\n        </div>\r\n        <div class="ds-box service_btn orient-v pack-center">\r\n            <div class="ds-box align-center ways flex-1 pack-center ways" data-role="service_send">发送</div>\r\n            <div class="ds-box align-center ways flex-1 pack-center ways" data-role="service_goto">查看</div>\r\n        </div>\r\n    </div>\r\n'}this.compilerInfo=[4,">= 1.0.0"],n=this.merge(n,e.helpers),s=s||{};var o,r="function",c=this.escapeExpression,d=this;return o=n.each.call(i,i&&i.data,{hash:{},inverse:d.noop,fn:d.program(1,t,s),data:s}),o||0===o?o:""}),s=Handlebars.template(function(e,i,n,a,s){return this.compilerInfo=[4,">= 1.0.0"],n=this.merge(n,e.helpers),s=s||{},'<div class="ds-box pack-center service_part_empty">----该商家没有服务----</div>'}),t={init:function(e){var i=this;i.contain=e.bar,i.render_lib=e.data.result_data,i.click_send=e.click_send||function(){},i.click_link=e.click_link||function(){},i.render()},render:function(){var e=this,a=n();e.contain.append(a),e.panel_obj=i('[data-role="options_service_panel"]')},load:function(e){var n=this;if(e){var t,o="";console.log(n.render_lib),i.each(n.render_lib,function(i,n){console.log(2),n.seller_user_id==e&&(t=n.good_list)}),console.log(3),o=t&&0==t.length?s():a({data:t}),n.panel_obj.html(o),n._bind_events(t)}},show:function(){var e=this;e.panel_obj.removeClass("fn-hide")},hide:function(){var e=this;e.panel_obj.addClass("fn-hide")},_bind_events:function(e){var n=this;n.panel_obj.find('[data-role="service_send"]').on("click",function(){var a,s=i(this);i.each(e,function(e,i){i.goods_id==s.parents("[data_goods_id]").attr("data_goods_id")&&(a=i)}),"function"==typeof n.click_send&&n.click_send.call(this,a)}),n.panel_obj.find('[data-role="service_goto"]').on("click",function(){"function"==typeof n.click_link&&n.click_link.call(this,this)})}};return t});