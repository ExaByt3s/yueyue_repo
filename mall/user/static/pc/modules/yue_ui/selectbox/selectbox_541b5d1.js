define("yue_ui/selectbox/selectbox",function(e){function t(e,t){e=this.select=s(e),s.extend(this,t||{});var o=this,i=!("minWidth"in e[0].style);if(!e.is("[multiple]")){e.data("selectbox")&&e.data("selectbox").remove();var n=this._tpl(this.selectboxHtml,s.extend({textContent:o._getOption().html()||""},e.data()));this._selectbox=s(n),this._value=this._selectbox.find("[data-value]"),this.isShowDropdown&&!e.attr("disabled")&&(this._globalKeydown=s.proxy(this._globalKeydown,this),this._selectbox.on(this._clickType+" focus blur",function(e){o[o._clickType===e.type?"click":e.type]()})),this._selectbox.css({width:e.outerWidth()+"px"}),e.on("focus blur",function(e){o[e.type](),e.preventDefault()}).on("change",function(){var e=o._getOption().html();o._value.html(e)}),e.css({opacity:0,position:"absolute",left:i?"-9999px":"auto",right:"auto",top:"auto",bottom:"auto",zIndex:this.isShowDropdown?-1:1}).data("selectbox",this),e.after(this._selectbox)}}var s=e("components/jquery/jquery.js"),o=e("yue_ui/selectbox/popup"),i=function(){};return i.prototype=o.prototype,t.prototype=new i,s.extend(t.prototype,{selectboxHtml:'<div class="ui-selectbox" hidefocus="true" style="user-select:none" onselectstart="return false" tabindex="-1" aria-hidden><div class="ui-selectbox-inner" data-value="">{{textContent}}</div><i class="ui-selectbox-icon"></i></div>',dropdownHtml:'<dl class="ui-selectbox-dropdown">{{options}}</dl>',optgroupHtml:'<dt class="ui-selectbox-optgroup">{{label}}</dt>',optionHtml:'<dd class="ui-selectbox-option {{className}}" data-option="{{index}}" tabindex="-1">{{textContent}}</dd>',selectedClass:"ui-selectbox-selected",disabledClass:"ui-selectbox-disabled",focusClass:"ui-selectbox-focus",openClass:"ui-selectbox-open",isShowDropdown:!("createTouch"in document),selectedIndex:0,value:"",close:function(){this._popup&&(this._popup.close().remove(),this.change())},show:function(){var e=this,t=this.select,i=e._selectbox;if(!t[0].length)return!1;var n=20,l=t.outerHeight(),c=t.offset().top-s(document).scrollTop(),d=s(window).height()-c-l,a=Math.max(c,d)-n,h=this._popup=new o;h.node.innerHTML=this._dropdownHtml(),this._dropdown=s(h.node),s(h.backdrop).css("opacity",0).on(this._clickType,s.proxy(this.close,this));var u=e._dropdown.children(),p=!("minWidth"in u[0].style);u.css({minWidth:i.innerWidth(),maxHeight:a,overflowY:"auto",overflowX:"hidden"}),this._dropdown.on(this._clickType,"[data-option]",function(t){var o=s(this).data("option");e.selected(o),e.close(),t.preventDefault()}),h.onshow=function(){s(document).on("keydown",e._globalKeydown),i.addClass(e.openClass),e.selectedIndex=t[0].selectedIndex,e.selected(e.selectedIndex)},h.onremove=function(){s(document).off("keydown",e._globalKeydown),i.removeClass(e.openClass)},this._oldValue=this.select.val(),h.showModal(i[0]),p&&(u.css({width:Math.max(i.innerWidth(),u.outerWidth()),height:Math.min(a,u.outerHeight())}),h.reset())},selected:function(e){if(this._getOption(e).attr("disabled"))return!1;var t=this._dropdown,s=this._dropdown.find("[data-option="+e+"]"),o=this.select[0].options[e].value,i=this.select[0].selectedIndex,n=this.selectedClass;return t.find("[data-option="+i+"]").removeClass(n),s.addClass(n),s.focus(),this._value.html(this._getOption(e).html()),this.value=o,this.selectedIndex=e,this.select[0].selectedIndex=this.selectedIndex,this.select[0].value=this.value,!0},change:function(){this._oldValue!==this.value&&this.select.triggerHandler("change")},click:function(){this.select.focus(),this._popup&&this._popup.open?this.close():this.show()},focus:function(){this._selectbox.addClass(this.focusClass)},blur:function(){this._selectbox.removeClass(this.focusClass)},remove:function(){this.close(),this._selectbox.remove()},_clickType:"onmousedown"in document?"mousedown":"touchstart",_getOption:function(e){return e=void 0===e?this.select[0].selectedIndex:e,this.select.find("option").eq(e)},_tpl:function(e,t){return e.replace(/{{(.*?)}}/g,function(e,s){return t[s]})},_dropdownHtml:function(){var e="",t=this,o=this.select,i=o.data(),n=0,l=function(o){o.each(function(){var o=s(this),l="";l=this.selected?t.selectedClass:this.disabled?t.disabledClass:"",e+=t._tpl(t.optionHtml,s.extend({value:o.val(),textContent:o.html(),index:n,className:l},o.data(),i)),n++})};return o.find("optgroup").length?o.find("optgroup").each(function(o){e+=t._tpl(t.optgroupHtml,s.extend({index:o,label:this.label},s(this).data(),i)),l(s(this).find("option"))}):l(o.find("option")),this._tpl(this.dropdownHtml,{options:e})},_move:function(e){var t=0,s=this.select[0].length-1,o=this.select[0].selectedIndex+e;o>=t&&s>=o&&(this.selected(o)||this._move(e+e))},_globalKeydown:function(e){var t;switch(e.keyCode){case 8:t=!0;break;case 9:case 27:case 13:this.close(),t=!0;break;case 38:this._move(-1),t=!0;break;case 40:this._move(1),t=!0}t&&e.preventDefault()}}),function(e,o){"select"===e.type?new t(e,o):s(e).each(function(){new t(this,o)})}});