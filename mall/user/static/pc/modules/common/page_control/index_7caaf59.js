define("common/page_control/index",function(h,n,o){o.exports={hash_list:[],reg_hash:function(h){for(var n=this,o=0;o<h.length;o++){if("function"!=typeof h[o].func)throw"params[1] is not a function";n.hash_list.push({hash:h[o].hash,func:h[o].func})}n.hash_event()},hash_event:function(){var h=this;window.onhashchange=function(){console.log(window.location.href);for(var n=0;n<h.hash_list.length;n++)window.location.hash==h.hash_list[n].hash&&h.hash_list[n].func()}}}});