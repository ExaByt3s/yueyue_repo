define("common/utility/index",function(e){window.onerror=function(e,o,n,l){if("function"==typeof callback)callback({message:e,script:o,line:n,column:l});else{var t="http://www.yueus.com/mobile_app/log/save_log.php?from_str=yue_m2&err_level=2&url="+encodeURIComponent(window.location.href),i=new Image,r=[];r.push("代码有错。。。"),r.push("\n错误信息：",e),r.push("\n出错文件：",o),r.push("\n出错位置：",n+"行，"+l+"列"),-1!=window.location.href.indexOf("webdev")?console.log(r.join("")):i.src=t+"&err_str="+encodeURIComponent(r.join(""))}};var o=e("components/jquery/jquery.js"),n=(o(window),{fix_placeholder:function(){o.support.placeholder="placeholder"in document.createElement("input"),o(function(){o.support.placeholder||(o("[placeholder]").focus(function(){o(this).val()==o(this).attr("placeholder")&&o(this).val("")}).blur(function(){""==o(this).val()&&o(this).val(o(this).attr("placeholder"))}).blur(),o("[placeholder]").parents("form").submit(function(){o(this).find("[placeholder]").each(function(){o(this).val()==o(this).attr("placeholder")&&o(this).val("")})}))})}});return n});