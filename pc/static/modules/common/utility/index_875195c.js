define("common/utility/index",function(e){function n(e,n){var r="";n=n||"",r=-1!=o.inArray(n,[120,165,640,600,145,440,230,260])?"_"+n:"";var t=/^http:\/\/(img|image)\d*(-c|-wap|-d)?\.poco\.cn.*\.jpg|gif|png|bmp/i,i=/_165.jpg|_640.jpg|_120.jpg|_600.jpg|_145.jpg|_260.jpg|_440.jpg/i;return t.test(e)&&(e=i.test(e)?e.replace(i,r+".jpg"):e.replace("/(.d*).jpg|.jpg|.gif|.png|.bmp/i",r+".jpg")),e}window.onerror=function(e,n,o,r){if("function"==typeof callback)callback({message:e,script:n,line:o,column:r});else{var t="http://www.yueus.com/mobile_app/log/save_log.php?from_str=yue_m2&err_level=2&url="+encodeURIComponent(window.location.href),i=new Image,a=[];a.push("�����д�������"),a.push("\n������Ϣ��",e),a.push("\n�����ļ���",n),a.push("\n����λ�ã�",o+"�У�"+r+"��"),-1!=window.location.href.indexOf("webdev")?console.log(a.join("")):i.src=t+"&err_str="+encodeURIComponent(a.join(""))}};var o=e("zepto"),r=o(window),t=window.localStorage,i=e("common/cookie/index"),a=(e("common/ua/index"),parseInt(i.get("yue_member_id"))),c=window.location;if(c.origin||(c.origin=c.protocol+"//"+c.hostname+(c.port?":"+c.port:"")),/wifi/.test(c.origin)){c.origin.replace("-wifi","")}else{var p=c.origin.split(".");p[0]+"-wifi."+p[1]+"."+p[2]}var s={get_view_port_height:function(e){var n=45,o=45,t=r.height();switch(e){case"nav":t-=o;break;case"tab":t-=n;break;case"all":t=t-o-n}return t},get_view_port_width:function(){return r.width()},"int":function(e){return parseInt(e,10)||0},"float":function(e){return parseFloat(e)},format_float:function(e,n){n=n||0;var o=Math.pow(10,n);return Math.round(e*o)/o||0},getHash:function(){return window.location.hash.substr(1)},get_zoom_height_by_zoom_width:function(e,n,o){return parseInt(n*o/e)},storage:{prefix:"poco-yuepai-app-",set:function(e,n){return"undefined"==typeof n?s.storage.remove(e):(t.setItem(s.storage.prefix+e,JSON.stringify(n)),n)},get:function(e){var n=t.getItem(s.storage.prefix+e);return n?JSON.parse(n):n},remove:function(e){return t.removeItem(s.storage.prefix+e)}},is_empty:function(e){var n=typeof e;switch(n){case"undefined":var o=!0;break;case"boolean":var o=!e;break;case"number":if(e>0)var o=!1;else var o=!0;break;case"string":if(""==e||"0">=e&&!isNaN(parseInt(e)))var o=!0;else var o=!1;break;case"object":if(_.isEmpty(e))var o=!0;else if(e instanceof Array)if(0==e.length)var o=!0;else var o=!1;else{var o=!0;for(var r in e)o=!1}break;default:var o=!1}return o},ajax_request:function(e){var e=e||{},n=e.url,r=e.data||{},t=e.cache||!1,i=e.beforeSend||function(){},a=e.success||function(){},c=e.error||function(){},p=e.complete||function(){},s=e.type||"GET",u=e.dataType||"json",l=null==e.async?!0:!1;console.log(l),o.ajax({url:n,type:s,data:r,cache:t,async:l,dataType:u,beforeSend:i,success:a,error:c,complete:p})},auth:{is_login:function(){return s.login_id>0}},matching_img_size:function(e,o){var r=o;return n(e,r)},get_url_params:function(e,n){var o=new RegExp("[?&]"+n+"=([^&]+)","i"),r=o.exec(e);return r&&r.length>1?r[1]:""},page_pv_stat_action:function(e){},err_log:function(e,n,o){var e=e||1,n=encodeURIComponent(n)||encodeURIComponent(window.location.href),o=encodeURIComponent(o)||"",r="http://www.yueus.com/mobile_app/log/save_log.php?from_str=app&err_level="+e+"&url="+n,t=new Image;t.src=r+"&err_str="+o},refresh_page:function(){window.location.reload()},login_id:a||0,location_id:"0"};return s});