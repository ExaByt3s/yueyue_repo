define("seller/service_list",function(a){function e(a){{var e=n.extend(c.result_data,{page:a});o.ajax_request_app({data:e,path:"customer/sell_services_list",beforeSend:function(){s=!1,window.$loading=n.loading({content:"发送中..."})},success:function(e){s=!0,n("#btn_more").removeClass("fn-hide");var t=e.data.list;if(console.log(t),$loading.loading("hide"),1==a&&!t.length)return void i.render(n('[data-role="list_container"]')[0],{});if(1==a&&t.length<5&&n("#btn_more").addClass("fn-hide"),!t||!t.length)return n("#btn_more").addClass("fn-hide"),void n.tips({content:"已经到尽头啦",stayTime:3e3,type:"warn"});var o=Handlebars.template(function(a,e,n,t,o){function i(a,e){var t,o,i="";return i+=' \r\n<a href="',(o=n.link)?t=o.call(a,{hash:{},data:e}):(o=a&&a.link,t=typeof o===l?o.call(a,{hash:{},data:e}):o),i+=s(t)+'">\r\n    <div class="item">\r\n        <div class="lbox ">\r\n            <i style="background-image:url(',(o=n.images)?t=o.call(a,{hash:{},data:e}):(o=a&&a.images,t=typeof o===l?o.call(a,{hash:{},data:e}):o),i+=s(t)+')"></i>\r\n        </div>\r\n\r\n        <div class="rbox ">\r\n            <h3 class="title color-000 f14">',(o=n.titles)?t=o.call(a,{hash:{},data:e}):(o=a&&a.titles,t=typeof o===l?o.call(a,{hash:{},data:e}):o),i+=s(t)+'</h3>\r\n            <p class="price color-ff6">',(o=n.prices)?t=o.call(a,{hash:{},data:e}):(o=a&&a.prices,t=typeof o===l?o.call(a,{hash:{},data:e}):o),i+=s(t)+'</p>\r\n            <p class="num color-999 f12">',(o=n.buy_num)?t=o.call(a,{hash:{},data:e}):(o=a&&a.buy_num,t=typeof o===l?o.call(a,{hash:{},data:e}):o),i+=s(t)+"</p>\r\n        </div>\r\n    </div>\r\n</a>\r\n"}this.compilerInfo=[4,">= 1.0.0"],n=this.merge(n,a.helpers),o=o||{};var r,l="function",s=this.escapeExpression,d=this;return r=n.each.call(e,e&&e.data,{hash:{},inverse:d.noop,fn:d.program(1,i,o),data:o}),r||0===r?r:""}),r=o({data:t});d.append(r)},error:function(){s=!0,$loading.loading("hide"),alert("请求失败，请重试")}})}}var n=a("components/zepto/zepto.js"),t=a("common/widget/header/main"),o=a("common/utility/index"),i=(a("yue_ui/frozen"),a("common/widget/abnormal/index")),r=n({});r.header_obj=t.init({ele:n("#global-header"),title:"服务列表",header_show:!0,right_icon_show:!1,share_icon:{show:!1,content:""},omit_icon:{show:!1,content:""},show_txt:{show:!0,content:"编辑"}});var l=1,s=!1,d=n("#render_ele"),c=window.__page_params;e(l),n("#btn_more").on("click",function(){s&&e(++l)})});