define("common/cookie/index",function(){function e(e){return encodeURIComponent(e)}function n(e){return decodeURIComponent(e.replace(/\+/g," "))}function t(e){return"string"==typeof e&&""!==e}var o={get:function(e,o){o=o||{};var i,c;return t(e)&&(c=String(r.cookie).match(new RegExp("(?:^| )"+e+"(?:(?:=([^;]*))|;|$)")))&&c[1]&&(i=o.decode?n(c[1]):c[1]),i},set:function(n,o,c){c=c||{};var u=String(c.encode?e(o):o),a=c.expires,d=c.domain,p=c.path,s=c.secure;return"number"==typeof a&&(a=new Date,a.setTime(a.getTime()+c.expires*i)),a instanceof Date&&(u+="; expires="+a.toUTCString()),t(d)&&(u+="; domain="+d),t(p)&&(u+="; path="+p),s&&(u+="; secure"),r.cookie=n+"="+u,u},del:function(e,n){return this.set(e,"",{expires:-1,domain:n.domain,path:n.path,secure:n.secure})}},r=window.document,i=864e5;return o});