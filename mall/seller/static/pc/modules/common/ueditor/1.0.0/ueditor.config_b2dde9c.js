define("common/ueditor/1.0.0/ueditor.config",function(){!function(){function e(e,i){return r(e||self.document.URL||self.location.href,i||t())}function t(){var e=document.getElementsByTagName("script");return e[e.length-1].src}function r(e,t){var r=t;return/^(\/|\\\\)/.test(t)?r=/^.+?\w(\/|\\\\)/.exec(e)[0]+t.replace(/^(\/|\\\\)/,""):/^[a-z]+:/i.test(t)||(e=e.split("#")[0].split("?")[0].replace(/[^\\\/]+$/,""),r=e+""+t),i(r)}function i(e){var t=/^[a-z]+:\/\//.exec(e)[0],r=null,i=[];for(e=e.replace(t,"").split("?")[0].split("#")[0],e=e.replace(/\\/g,"/").split(/\//),e[e.length-1]="";e.length;)".."===(r=e.shift())?i.pop():"."!==r&&i.push(r);return t+i.join("/")}var o=window.UEDITOR_HOME_URL||e();if(/test/.test(window.location.href))var o="http://static.yueus.com/mall/seller/test/static/pc/modules/common/ueditor/1.0.0/ueditor.js".split("/");else var o="http://static-c.yueus.com/mall/seller/static/pc/modules/common/ueditor/1.0.0/ueditor.js".split("/");o.pop(),o=o.join("/")+"/",window.UEDITOR_CONFIG={UEDITOR_HOME_URL:o,serverUrl:o+"php/controller.php",_imageUrl:"http://sendmedia.yueus.com:8079/upload.cgi",_imageFieldName:"opus",_is_yueyue_upload:!0,toolbars:[["fullscreen","source","|","undo","redo","|","bold","italic","underline","fontborder","strikethrough","superscript","subscript","removeformat","formatmatch","autotypeset","blockquote","pasteplain","|","forecolor","backcolor","insertorderedlist","insertunorderedlist","selectall","cleardoc","|","rowspacingtop","rowspacingbottom","lineheight","|","customstyle","paragraph","fontfamily","fontsize","|","directionalityltr","directionalityrtl","indent","|","justifyleft","justifycenter","justifyright","justifyjustify","|","touppercase","tolowercase","|","link","unlink","anchor","|","imagenone","imageleft","imageright","imagecenter","|","simpleupload","insertimage","emotion","scrawl","insertvideo","music","attachment","map","gmap","insertframe","insertcode","webapp","pagebreak","template","background","|","horizontal","date","time","spechars","snapscreen","wordimage","|","inserttable","deletetable","insertparagraphbeforetable","insertrow","deleterow","insertcol","deletecol","mergecells","mergeright","mergedown","splittocells","splittorows","splittocols","charts","|","print","preview","searchreplace","help","drafts"]]},window.UE={getUEBasePath:e}}()});