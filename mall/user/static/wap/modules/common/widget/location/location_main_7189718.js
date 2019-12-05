define("common/widget/location/location_main",function(i){function a(i){var i=i||{};this.ele_tpl=i.ele,this.hot_city=i.hot_city||{},this.city_history_num=i.city_history_num||12,this.is_search=i.is_search||!1,this.callback=i.callback,this.all_hot_city_base=n.data.concat(this.hot_city.data),this.all_hot_city=[{title:n.title,data:this.all_hot_city_base,id:n.id}],this.init()}var t=i("components/zepto/zepto.js"),n=i("common/widget/location/hot_city_base"),l=i("common/utility/index"),e=i("common/cookie/index");return a.prototype={init:function(){var i=this;i.setup_event(),i.is_search&&i.search_city(),i.render(i.ele_tpl),i.navigation()},setup_event:function(){Array.prototype.uniqueFn=function(){for(var i=[],a=0;a<this.length;a++)-1==i.indexOf(this[a])&&i.push(this[a]);return i}},render:function(i){var a=this,n=Handlebars.template(function(i,a,t,n,l){return this.compilerInfo=[4,">= 1.0.0"],t=this.merge(t,i.helpers),l=l||{},'<div class="location-mod" data-role="location">\n\n    <div data-role="local_storage_city" class="local-city city-style">\n        \n    </div>\n\n    <div data-role="hot_city" class="city-style">\n        \n    </div>\n\n    <div data-role="all_city" >\n        \n    </div>\n\n\n    <div class="flight-ctltsfixed">\n        <div class="flight-ctltsfixed-pd" >\n            <ul class="flight-ctlts" data-role="navigation">\n                <li nav-id="history">历史</li>\n                <li nav-id="hot">热门</li>\n                <li nav-id="A">A</li>\n                <li nav-id="B">B</li>\n                <li nav-id="C">C</li>\n                <li nav-id="D">D</li>\n                <li nav-id="E">E</li>\n                <li nav-id="F">F</li>\n                <li nav-id="G">G</li>\n                <li nav-id="H">H</li>\n                <li nav-id="I">I</li>\n                <li nav-id="J">J</li>\n                <li nav-id="K">K</li>\n                <li nav-id="L">L</li>\n                <li nav-id="M">M</li>\n                <li nav-id="N">N</li>\n                <li nav-id="O">O</li>\n                <li nav-id="P">P</li>\n                <li nav-id="Q">Q</li>\n                <li nav-id="R">R</li>\n                <li nav-id="S">S</li>\n                <li nav-id="T">T</li>\n                <li nav-id="U">U</li>\n                <li nav-id="V">V</li>\n                <li nav-id="W">W</li>\n                <li nav-id="X">X</li>\n                <li nav-id="Y">Y</li>\n                <li nav-id="Z">Z</li>\n            </ul>\n        </div>\n    </div>\n\n\n</div>'});a.current_view=i.html(n({})),a.all_city_ele=a.current_view.find("[data-role=all_city]"),a.get_all_city(),t(a).on("success:get_all_city",function(i,t){a.render_all_city(t),a.top_main_ele=a.current_view.find("[data-role=location]"),a.on_local_storage_city()}),a.navigation_ele=a.current_view.find('[data-role="navigation"]'),a.hot_city_ele=a.current_view.find("[data-role=hot_city]"),a.hot_city.is_show?a.render_hot_city():a.navigation_ele.find('[nav-id="hot"]').addClass("fn-hide"),a.local_storage_city_ele=a.current_view.find('[data-role="local_storage_city"]'),a.render_local_storage_city()},get_all_city:function(){var i=this,a=e.get("yue_fav_userid")?e.get("yue_fav_userid"):0;t.ajax({url:"http://yp.yueus.com/mobile/test/location.php?callback=?",data:{user_id:a,wap:"1"},dataType:"JSONP",type:"GET",cache:!0,beforeSend:function(){i.$loading=t.loading({content:"加载中..."})},success:function(a){i.$loading.loading("hide"),t(i).trigger("success:get_all_city",a.data)},error:function(){i.$loading.loading("hide")},complete:function(){i.$loading.loading("hide")}})},render_hot_city:function(){var i=this,a=Handlebars.template(function(i,a,t,n,l){function e(i,a){var n,l,e="";return e+=' \n    <div class="title" data-id="',(l=t.id)?n=l.call(i,{hash:{},data:a}):(l=i&&i.id,n=typeof l===s?l.call(i,{hash:{},data:a}):l),e+=r(n)+'">',(l=t.title)?n=l.call(i,{hash:{},data:a}):(l=i&&i.title,n=typeof l===s?l.call(i,{hash:{},data:a}):l),e+=r(n)+'</div>\n    <div class="city-wrap clearfix">\n        ',n=t.each.call(i,i&&i.data,{hash:{},inverse:h.noop,fn:h.program(2,o,a),data:a}),(n||0===n)&&(e+=n),e+="\n    </div>\n"}function o(i,a){var n,l,e="";return e+=' \n            <div class="item" data-role="item" location_id="',(l=t.location_id)?n=l.call(i,{hash:{},data:a}):(l=i&&i.location_id,n=typeof l===s?l.call(i,{hash:{},data:a}):l),e+=r(n)+'">',(l=t.city)?n=l.call(i,{hash:{},data:a}):(l=i&&i.city,n=typeof l===s?l.call(i,{hash:{},data:a}):l),e+=r(n)+"</div>\n        "}this.compilerInfo=[4,">= 1.0.0"],t=this.merge(t,i.helpers),l=l||{};var c,d="",s="function",r=this.escapeExpression,h=this;return d+="\n",c=t.each.call(a,a&&a.data_main,{hash:{},inverse:h.noop,fn:h.program(1,e,l),data:l}),(c||0===c)&&(d+=c),d+="\n\n    \n\n\n    \n"});i.hot_city_ele.html(a({data_main:i.all_hot_city}))},render_all_city:function(i){{var a=this,t=Handlebars.template(function(i,a,t,n,l){function e(i,a){var n,l,e="";return e+=' \n    <div class="title" data-id="',(l=t.id)?n=l.call(i,{hash:{},data:a}):(l=i&&i.id,n=typeof l===s?l.call(i,{hash:{},data:a}):l),e+=r(n)+'">',(l=t.title)?n=l.call(i,{hash:{},data:a}):(l=i&&i.title,n=typeof l===s?l.call(i,{hash:{},data:a}):l),e+=r(n)+'</div>\n    <div class="city-wrap clearfix">\n        ',n=t.each.call(i,i&&i.data,{hash:{},inverse:h.noop,fn:h.program(2,o,a),data:a}),(n||0===n)&&(e+=n),e+="\n    </div>\n"}function o(i,a){var n,l,e="";return e+=' \n            <div class="item" data-role="item" location_id="',(l=t.location_id)?n=l.call(i,{hash:{},data:a}):(l=i&&i.location_id,n=typeof l===s?l.call(i,{hash:{},data:a}):l),e+=r(n)+'">',(l=t.city)?n=l.call(i,{hash:{},data:a}):(l=i&&i.city,n=typeof l===s?l.call(i,{hash:{},data:a}):l),e+=r(n)+"</div>\n        "}this.compilerInfo=[4,">= 1.0.0"],t=this.merge(t,i.helpers),l=l||{};var c,d="",s="function",r=this.escapeExpression,h=this;return d+="\n",c=t.each.call(a,a&&a.data_main,{hash:{},inverse:h.noop,fn:h.program(1,e,l),data:l}),(c||0===c)&&(d+=c),d+="\n\n    \n\n\n    \n"});a.all_city_ele.html(t({data_main:i}))}},on_local_storage_city:function(){var i=this;i.top_main_ele.find('[data-role="item"]').on("click",function(){var a=t(this).attr("location_id"),n=t(this).html(),l={location_id:a,city:n};i.set_local_storage_city(l),i.render_local_storage_city(),i.navigation_ele.find("[nav-id=history]").removeClass("fn-hide")})},set_local_storage_city:function(i){var a=this,t=[];a.callback&&a.callback.call(this,i),t.push(i);var n=l.storage.get("location_map");n&&(t=t.concat(n));for(var e=[],o=[],c=[],d=0;d<t.length;d++)e.push(t[d].location_id),o[t[d].location_id]=t[d].city;for(var s=e.uniqueFn(),d=0;d<s.length;d++)c.push({location_id:s[d],city:o[s[d]]});l.storage.set("location_map",c)},render_local_storage_city:function(){var i=this,a=l.storage.get("location_map");if(a){for(var n=[],e=0;e<a.length;e++)e<i.city_history_num&&n.push(a[e]);var o=[{title:"历史",id:"history",data:n}],c=Handlebars.template(function(i,a,t,n,l){function e(i,a){var n,l,e="";return e+=' \n    <div class="title" data-id="',(l=t.id)?n=l.call(i,{hash:{},data:a}):(l=i&&i.id,n=typeof l===s?l.call(i,{hash:{},data:a}):l),e+=r(n)+'">',(l=t.title)?n=l.call(i,{hash:{},data:a}):(l=i&&i.title,n=typeof l===s?l.call(i,{hash:{},data:a}):l),e+=r(n)+'</div>\n    <div class="city-wrap clearfix">\n        ',n=t.each.call(i,i&&i.data,{hash:{},inverse:h.noop,fn:h.program(2,o,a),data:a}),(n||0===n)&&(e+=n),e+="\n    </div>\n"}function o(i,a){var n,l,e="";return e+=' \n            <div class="item" data-role="item" location_id="',(l=t.location_id)?n=l.call(i,{hash:{},data:a}):(l=i&&i.location_id,n=typeof l===s?l.call(i,{hash:{},data:a}):l),e+=r(n)+'">',(l=t.city)?n=l.call(i,{hash:{},data:a}):(l=i&&i.city,n=typeof l===s?l.call(i,{hash:{},data:a}):l),e+=r(n)+"</div>\n        "}this.compilerInfo=[4,">= 1.0.0"],t=this.merge(t,i.helpers),l=l||{};var c,d="",s="function",r=this.escapeExpression,h=this;return d+="\n",c=t.each.call(a,a&&a.data_main,{hash:{},inverse:h.noop,fn:h.program(1,e,l),data:l}),(c||0===c)&&(d+=c),d+="\n\n    \n\n\n    \n"}),d=i.local_storage_city_ele.html(c({data_main:o}));d.find('[data-role="item"]').on("click",function(){var a=t(this).attr("location_id"),n=t(this).html(),l={location_id:a,city:n};i.set_local_storage_city(l),i.render_local_storage_city()})}else i.navigation_ele.find("[nav-id=history]").addClass("fn-hide")},clear_storage_city:function(){l.storage.remove("location_map")},navigation:function(){var i=this,a=i.navigation_ele;i.navigation_ele.on("touchstart",function(i){function n(){t("[data-id="+e+"]").offset()&&window.scrollTo(0,t("[data-id="+e+"]").offset().top-45)}a.find(".flight-ctlts-selected").remove();var l=t(i.target),e=l.attr("nav-id");if(e){"history"==e?(n(),e="历史"):"hot"==e?(n(),e="热门"):n();var o='<div class="flight-ctlts-selected">'+e+"</div>";l.append(o)}}),i.navigation_ele.on("touchend",function(){a.find(".flight-ctlts-selected").remove()})},search_city:function(){console.log("搜索")}},a});