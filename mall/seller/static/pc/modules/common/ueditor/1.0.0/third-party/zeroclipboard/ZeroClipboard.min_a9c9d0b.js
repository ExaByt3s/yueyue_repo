define("common/ueditor/1.0.0/third-party/zeroclipboard/ZeroClipboard.min",function(e,t,n){!function(e){"use strict";var t,a={bridge:null,version:"0.0.0",pluginType:"unknown",disabled:null,outdated:null,unavailable:null,deactivated:null,overdue:null,ready:null},r={},i=null,o=0,l={},s=0,c={},u=function(){var e,t,n,a,r="ZeroClipboard.swf";if(!document.currentScript||!(a=document.currentScript.src)){var i=document.getElementsByTagName("script");if("readyState"in i[0])for(e=i.length;e--&&("interactive"!==i[e].readyState||!(a=i[e].src)););else if("loading"===document.readyState)a=i[i.length-1].src;else{for(e=i.length;e--;){if(n=i[e].src,!n){t=null;break}if(n=n.split("#")[0].split("?")[0],n=n.slice(0,n.lastIndexOf("/")+1),null==t)t=n;else if(t!==n){t=null;break}}null!==t&&(a=t)}}return a&&(a=a.split("#")[0].split("?")[0],r=a.slice(0,a.lastIndexOf("/")+1)+r),r}(),d=function(){var e=/\-([a-z])/g,t=function(e,t){return t.toUpperCase()};return function(n){return n.replace(e,t)}}(),f=function(t,n){var a,r,i;return e.getComputedStyle?a=e.getComputedStyle(t,null).getPropertyValue(n):(r=d(n),a=t.currentStyle?t.currentStyle[r]:t.style[r]),"cursor"!==n||a&&"auto"!==a||(i=t.tagName.toLowerCase(),"a"!==i)?a:"pointer"},p=function(t){t||(t=e.event);var n;this!==e?n=this:t.target?n=t.target:t.srcElement&&(n=t.srcElement),H.activate(n)},h=function(e,t,n){e&&1===e.nodeType&&(e.addEventListener?e.addEventListener(t,n,!1):e.attachEvent&&e.attachEvent("on"+t,n))},v=function(e,t,n){e&&1===e.nodeType&&(e.removeEventListener?e.removeEventListener(t,n,!1):e.detachEvent&&e.detachEvent("on"+t,n))},y=function(e,t){if(!e||1!==e.nodeType)return e;if(e.classList)return e.classList.contains(t)||e.classList.add(t),e;if(t&&"string"==typeof t){var n=(t||"").split(/\s+/);if(1===e.nodeType)if(e.className){for(var a=" "+e.className+" ",r=e.className,i=0,o=n.length;o>i;i++)a.indexOf(" "+n[i]+" ")<0&&(r+=" "+n[i]);e.className=r.replace(/^\s+|\s+$/g,"")}else e.className=t}return e},g=function(e,t){if(!e||1!==e.nodeType)return e;if(e.classList)return e.classList.contains(t)&&e.classList.remove(t),e;if(t&&"string"==typeof t||void 0===t){var n=(t||"").split(/\s+/);if(1===e.nodeType&&e.className)if(t){for(var a=(" "+e.className+" ").replace(/[\n\t]/g," "),r=0,i=n.length;i>r;r++)a=a.replace(" "+n[r]+" "," ");e.className=a.replace(/^\s+|\s+$/g,"")}else e.className=""}return e},m=function(){var e,t,n,a=1;return"function"==typeof document.body.getBoundingClientRect&&(e=document.body.getBoundingClientRect(),t=e.right-e.left,n=document.body.offsetWidth,a=Math.round(t/n*100)/100),a},b=function(t,n){var a={left:0,top:0,width:0,height:0,zIndex:D(n)-1};if(t.getBoundingClientRect){var r,i,o,l=t.getBoundingClientRect();"pageXOffset"in e&&"pageYOffset"in e?(r=e.pageXOffset,i=e.pageYOffset):(o=m(),r=Math.round(document.documentElement.scrollLeft/o),i=Math.round(document.documentElement.scrollTop/o));var s=document.documentElement.clientLeft||0,c=document.documentElement.clientTop||0;a.left=l.left+r-s,a.top=l.top+i-c,a.width="width"in l?l.width:l.right-l.left,a.height="height"in l?l.height:l.bottom-l.top}return a},w=function(e,t){var n=null==t||t&&t.cacheBust===!0;return n?(-1===e.indexOf("?")?"?":"&")+"noCache="+(new Date).getTime():""},x=function(t){var n,a,r,i,o="",l=[];if(t.trustedDomains&&("string"==typeof t.trustedDomains?i=[t.trustedDomains]:"object"==typeof t.trustedDomains&&"length"in t.trustedDomains&&(i=t.trustedDomains)),i&&i.length)for(n=0,a=i.length;a>n;n++)if(i.hasOwnProperty(n)&&i[n]&&"string"==typeof i[n]){if(r=k(i[n]),!r)continue;if("*"===r){l=[r];break}l.push.apply(l,[r,"//"+r,e.location.protocol+"//"+r])}return l.length&&(o+="trustedOrigins="+encodeURIComponent(l.join(","))),t.forceEnhancedClipboard===!0&&(o+=(o?"&":"")+"forceEnhancedClipboard=true"),o},C=function(e,t,n){if("function"==typeof t.indexOf)return t.indexOf(e,n);var a,r=t.length;for("undefined"==typeof n?n=0:0>n&&(n=r+n),a=n;r>a;a++)if(t.hasOwnProperty(a)&&t[a]===e)return a;return-1},O=function(e){if("string"==typeof e)throw new TypeError("ZeroClipboard doesn't accept query strings.");return"number"!=typeof e.length?[e]:e},T=function(t,n,a,r){r?e.setTimeout(function(){t.apply(n,a)},0):t.apply(n,a)},D=function(e){var t,n;return e&&("number"==typeof e&&e>0?t=e:"string"==typeof e&&(n=parseInt(e,10))&&!isNaN(n)&&n>0&&(t=n)),t||("number"==typeof R.zIndex&&R.zIndex>0?t=R.zIndex:"string"==typeof R.zIndex&&(n=parseInt(R.zIndex,10))&&!isNaN(n)&&n>0&&(t=n)),t||0},z=function(){var e,t,n,a,r,i,o=arguments[0]||{};for(e=1,t=arguments.length;t>e;e++)if(null!=(n=arguments[e]))for(a in n)if(n.hasOwnProperty(a)){if(r=o[a],i=n[a],o===i)continue;void 0!==i&&(o[a]=i)}return o},k=function(e){if(null==e||""===e)return null;if(e=e.replace(/^\s+|\s+$/g,""),""===e)return null;var t=e.indexOf("//");e=-1===t?e:e.slice(t+2);var n=e.indexOf("/");return e=-1===n?e:-1===t||0===n?null:e.slice(0,n),e&&".swf"===e.slice(-4).toLowerCase()?null:e||null},N=function(){var e=function(e,t){var n,a,r;if(null!=e&&"*"!==t[0]&&("string"==typeof e&&(e=[e]),"object"==typeof e&&"number"==typeof e.length))for(n=0,a=e.length;a>n;n++)if(e.hasOwnProperty(n)&&(r=k(e[n]))){if("*"===r){t.length=0,t.push("*");break}-1===C(r,t)&&t.push(r)}};return function(t,n){var a=k(n.swfPath);null===a&&(a=t);var r=[];e(n.trustedOrigins,r),e(n.trustedDomains,r);var i=r.length;if(i>0){if(1===i&&"*"===r[0])return"always";if(-1!==C(t,r))return 1===i&&t===a?"sameDomain":"always"}return"never"}}(),E=function(e){if(null==e)return[];if(Object.keys)return Object.keys(e);var t=[];for(var n in e)e.hasOwnProperty(n)&&t.push(n);return t},I=function(e){if(e)for(var t in e)e.hasOwnProperty(t)&&delete e[t];return e},P=function(){try{return document.activeElement}catch(e){}return null},j=function(e,t){for(var n={},a=0,r=t.length;r>a;a++)t[a]in e&&(n[t[a]]=e[t[a]]);return n},L=function(e,t){var n={};for(var a in e)-1===C(a,t)&&(n[a]=e[a]);return n},S=function(e){var t={},n={};if("object"==typeof e&&e){for(var a in e)if(a&&e.hasOwnProperty(a)&&"string"==typeof e[a]&&e[a])switch(a.toLowerCase()){case"text/plain":case"text":case"air:text":case"flash:text":t.text=e[a],n.text=a;break;case"text/html":case"html":case"air:html":case"flash:html":t.html=e[a],n.html=a;break;case"application/rtf":case"text/rtf":case"rtf":case"richtext":case"air:rtf":case"flash:rtf":t.rtf=e[a],n.rtf=a}return{data:t,formatMap:n}}},A=function(e,t){if("object"!=typeof e||!e||"object"!=typeof t||!t)return e;var n={};for(var a in e)if(e.hasOwnProperty(a)){if("success"!==a&&"data"!==a){n[a]=e[a];continue}n[a]={};var r=e[a];for(var i in r)i&&r.hasOwnProperty(i)&&t.hasOwnProperty(i)&&(n[a][t[i]]=r[i])}return n},F=function(e){return function(t){return e.call(t,0)}}(e.Array.prototype.slice),B=function(){function e(e){var t=e.match(/[\d]+/g);return t.length=3,t.join(".")}function t(e){return!!e&&(e=e.toLowerCase())&&(/^(pepflashplayer\.dll|libpepflashplayer\.so|pepperflashplayer\.plugin)$/.test(e)||"chrome.plugin"===e.slice(-13))}function n(n){n&&(l=!0,n.version&&(u=e(n.version)),!u&&n.description&&(u=e(n.description)),n.filename&&(c=t(n.filename)))}var r,i,o,l=!1,s=!1,c=!1,u="";if(navigator.plugins&&navigator.plugins.length)r=navigator.plugins["Shockwave Flash"],n(r),navigator.plugins["Shockwave Flash 2.0"]&&(l=!0,u="2.0.0.11");else if(navigator.mimeTypes&&navigator.mimeTypes.length)o=navigator.mimeTypes["application/x-shockwave-flash"],r=o&&o.enabledPlugin,n(r);else if("undefined"!=typeof ActiveXObject){s=!0;try{i=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7"),l=!0,u=e(i.GetVariable("$version"))}catch(d){try{i=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6"),l=!0,u="6.0.21"}catch(f){try{i=new ActiveXObject("ShockwaveFlash.ShockwaveFlash"),l=!0,u=e(i.GetVariable("$version"))}catch(p){s=!1}}}}a.disabled=l!==!0,a.outdated=u&&parseFloat(u)<11,a.version=u||"0.0.0",a.pluginType=c?"pepper":s?"activex":l?"netscape":"unknown"};B();var H=function(e){if(!(this instanceof H))return new H(e);if(this.id=""+o++,l[this.id]={instance:this,elements:[],handlers:{}},e&&this.clip(e),"boolean"!=typeof a.ready&&(a.ready=!1),!H.isFlashUnusable()&&null===a.bridge){var t=this,n=R.flashLoadTimeout;"number"==typeof n&&n>=0&&setTimeout(function(){"boolean"!=typeof a.deactivated&&(a.deactivated=!0),a.deactivated===!0&&H.emit({type:"error",name:"flash-deactivated",client:t})},n),a.overdue=!1,X()}};H.prototype.setText=function(e){return H.setData("text/plain",e),this},H.prototype.setHtml=function(e){return H.setData("text/html",e),this},H.prototype.setRichText=function(e){return H.setData("application/rtf",e),this},H.prototype.setData=function(){return H.setData.apply(H,F(arguments)),this},H.prototype.clearData=function(){return H.clearData.apply(H,F(arguments)),this},H.prototype.setSize=function(e,t){return G(e,t),this};var M=function(e){a.ready===!0&&a.bridge&&"function"==typeof a.bridge.setHandCursor?a.bridge.setHandCursor(e):a.ready=!1};H.prototype.destroy=function(){this.unclip(),this.off(),delete l[this.id]};var $=function(){var e,t,n,a=[],r=E(l);for(e=0,t=r.length;t>e;e++)n=l[r[e]].instance,n&&n instanceof H&&a.push(n);return a};H.version="2.0.0-beta.5";var R={swfPath:u,trustedDomains:e.location.host?[e.location.host]:[],cacheBust:!0,forceHandCursor:!1,forceEnhancedClipboard:!1,zIndex:999999999,debug:!1,title:null,autoActivate:!0,flashLoadTimeout:3e4};H.isFlashUnusable=function(){return!!(a.disabled||a.outdated||a.unavailable||a.deactivated)},H.config=function(e){if("object"==typeof e&&null!==e&&z(R,e),"string"!=typeof e||!e){var t={};for(var n in R)R.hasOwnProperty(n)&&(t[n]="object"==typeof R[n]&&null!==R[n]?"length"in R[n]?R[n].slice(0):z({},R[n]):R[n]);return t}return R.hasOwnProperty(e)?R[e]:void 0},H.destroy=function(){H.deactivate();for(var e in l)if(l.hasOwnProperty(e)&&l[e]){var t=l[e].instance;t&&"function"==typeof t.destroy&&t.destroy()}var n=a.bridge;if(n){var r=U(n);r&&("activex"===a.pluginType&&"readyState"in n?(n.style.display="none",function i(){if(4===n.readyState){for(var e in n)"function"==typeof n[e]&&(n[e]=null);n.parentNode.removeChild(n),r.parentNode&&r.parentNode.removeChild(r)}else setTimeout(i,10)}()):(n.parentNode.removeChild(n),r.parentNode&&r.parentNode.removeChild(r))),a.ready=null,a.bridge=null,a.deactivated=null}H.clearData()},H.activate=function(e){t&&(g(t,R.hoverClass),g(t,R.activeClass)),t=e,y(e,R.hoverClass),V();var n=R.title||e.getAttribute("title");if(n){var r=U(a.bridge);r&&r.setAttribute("title",n)}var i=R.forceHandCursor===!0||"pointer"===f(e,"cursor");M(i)},H.deactivate=function(){var e=U(a.bridge);e&&(e.removeAttribute("title"),e.style.left="0px",e.style.top="-9999px",G(1,1)),t&&(g(t,R.hoverClass),g(t,R.activeClass),t=null)},H.state=function(){return{browser:j(e.navigator,["userAgent","platform","appName"]),flash:L(a,["bridge"]),zeroclipboard:{version:H.version,config:H.config()}}},H.setData=function(e,t){var n;if("object"==typeof e&&e&&"undefined"==typeof t)n=e,H.clearData();else{if("string"!=typeof e||!e)return;n={},n[e]=t}for(var a in n)a&&n.hasOwnProperty(a)&&"string"==typeof n[a]&&n[a]&&(r[a]=n[a])},H.clearData=function(e){"undefined"==typeof e?(I(r),i=null):"string"==typeof e&&r.hasOwnProperty(e)&&delete r[e]};var X=function(){var t,n,r=document.getElementById("global-zeroclipboard-html-bridge");if(!r){var i=N(e.location.host,R),o="never"===i?"none":"all",l=x(R),s=R.swfPath+w(R.swfPath,R);r=Z();var c=document.createElement("div");r.appendChild(c),document.body.appendChild(r);var u=document.createElement("div"),d="activex"===a.pluginType;u.innerHTML='<object id="global-zeroclipboard-flash-bridge" name="global-zeroclipboard-flash-bridge" width="100%" height="100%" '+(d?'classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"':'type="application/x-shockwave-flash" data="'+s+'"')+">"+(d?'<param name="movie" value="'+s+'"/>':"")+'<param name="allowScriptAccess" value="'+i+'"/><param name="allowNetworking" value="'+o+'"/><param name="menu" value="false"/><param name="wmode" value="transparent"/><param name="flashvars" value="'+l+'"/></object>',t=u.firstChild,u=null,t.ZeroClipboard=H,r.replaceChild(t,c)}t||(t=document["global-zeroclipboard-flash-bridge"],t&&(n=t.length)&&(t=t[n-1]),t||(t=r.firstChild)),a.bridge=t||null},Z=function(){var e=document.createElement("div");return e.id="global-zeroclipboard-html-bridge",e.className="global-zeroclipboard-container",e.style.position="absolute",e.style.left="0px",e.style.top="-9999px",e.style.width="1px",e.style.height="1px",e.style.zIndex=""+D(R.zIndex),e},U=function(e){for(var t=e&&e.parentNode;t&&"OBJECT"===t.nodeName&&t.parentNode;)t=t.parentNode;return t||null},V=function(){if(t){var e=b(t,R.zIndex),n=U(a.bridge);n&&(n.style.top=e.top+"px",n.style.left=e.left+"px",n.style.width=e.width+"px",n.style.height=e.height+"px",n.style.zIndex=e.zIndex+1),G(e.width,e.height)}},G=function(e,t){var n=U(a.bridge);n&&(n.style.width=e+"px",n.style.height=t+"px")};H.emit=function(t){var n,o,l,s,c,u,d,f,p;if("string"==typeof t&&t&&(n=t),"object"==typeof t&&t&&"string"==typeof t.type&&t.type&&(n=t.type,o=t),n){if(t=q(n,o),_(t),"ready"===t.type&&a.overdue===!0)return H.emit({type:"error",name:"flash-overdue"});if(l=!/^(before)?copy$/.test(t.type),t.client)J.call(t.client,t,l);else for(s=t.target&&t.target!==e&&R.autoActivate===!0?K(t.target):$(),c=0,u=s.length;u>c;c++)d=z({},t,{client:s[c]}),J.call(s[c],d,l);return"copy"===t.type&&(p=S(r),f=p.data,i=p.formatMap),f}};var J=function(t,n){var a=l[this.id]&&l[this.id].handlers[t.type];if(a&&a.length){var r,i,o,s,c=this;for(r=0,i=a.length;i>r;r++)o=a[r],s=c,"string"==typeof o&&"function"==typeof e[o]&&(o=e[o]),"object"==typeof o&&o&&"function"==typeof o.handleEvent&&(s=o,o=o.handleEvent),"function"==typeof o&&T(o,s,[t],n)}return this},Y={ready:"Flash communication is established",error:{"flash-disabled":"Flash is disabled or not installed","flash-outdated":"Flash is too outdated to support ZeroClipboard","flash-unavailable":"Flash is unable to communicate bidirectionally with JavaScript","flash-deactivated":"Flash is too outdated for your browser and/or is configured as click-to-activate","flash-overdue":"Flash communication was established but NOT within the acceptable time limit"}},q=function(e,n){if(e||n&&n.type){n=n||{},e=(e||n.type).toLowerCase(),z(n,{type:e,target:n.target||t||null,relatedTarget:n.relatedTarget||null,currentTarget:a&&a.bridge||null});var r=Y[n.type];return"error"===n.type&&n.name&&r&&(r=r[n.name]),r&&(n.message=r),"ready"===n.type&&z(n,{target:null,version:a.version}),"error"===n.type&&(n.target=null,/^flash-(outdated|unavailable|deactivated|overdue)$/.test(n.name)&&z(n,{version:a.version,minimumVersion:"11.0.0"})),"copy"===n.type&&(n.clipboardData={setData:H.setData,clearData:H.clearData}),"aftercopy"===n.type&&(n=A(n,i)),n.target&&!n.relatedTarget&&(n.relatedTarget=W(n.target)),n}},W=function(e){var t=e&&e.getAttribute&&e.getAttribute("data-clipboard-target");return t?document.getElementById(t):null},_=function(e){var n=e.target||t;switch(e.type){case"error":C(e.name,["flash-disabled","flash-outdated","flash-deactivated","flash-overdue"])&&z(a,{disabled:"flash-disabled"===e.name,outdated:"flash-outdated"===e.name,unavailable:"flash-unavailable"===e.name,deactivated:"flash-deactivated"===e.name,overdue:"flash-overdue"===e.name,ready:!1});break;case"ready":var i=a.deactivated===!0;z(a,{disabled:!1,outdated:!1,unavailable:!1,deactivated:!1,overdue:i,ready:!i});break;case"copy":var o,l,s=e.relatedTarget;!r["text/html"]&&!r["text/plain"]&&s&&(l=s.value||s.outerHTML||s.innerHTML)&&(o=s.value||s.textContent||s.innerText)?(e.clipboardData.clearData(),e.clipboardData.setData("text/plain",o),l!==o&&e.clipboardData.setData("text/html",l)):!r["text/plain"]&&e.target&&(o=e.target.getAttribute("data-clipboard-text"))&&(e.clipboardData.clearData(),e.clipboardData.setData("text/plain",o));break;case"aftercopy":H.clearData(),n&&n!==P()&&n.focus&&n.focus();break;case"mouseover":y(n,R.hoverClass);break;case"mouseout":R.autoActivate===!0&&H.deactivate();break;case"mousedown":y(n,R.activeClass);break;case"mouseup":g(n,R.activeClass)}};H.prototype.on=function(e,t){var n,r,i,o={},s=l[this.id]&&l[this.id].handlers;if("string"==typeof e&&e)i=e.toLowerCase().split(/\s+/);else if("object"==typeof e&&e&&"undefined"==typeof t)for(n in e)e.hasOwnProperty(n)&&"string"==typeof n&&n&&"function"==typeof e[n]&&this.on(n,e[n]);if(i&&i.length){for(n=0,r=i.length;r>n;n++)e=i[n].replace(/^on/,""),o[e]=!0,s[e]||(s[e]=[]),s[e].push(t);if(o.ready&&a.ready&&H.emit({type:"ready",client:this}),o.error){var c=["disabled","outdated","unavailable","deactivated","overdue"];for(n=0,r=c.length;r>n;n++)if(a[c[n]]){H.emit({type:"error",name:"flash-"+c[n],client:this});break}}}return this},H.prototype.off=function(e,t){var n,a,r,i,o,s=l[this.id]&&l[this.id].handlers;if(0===arguments.length)i=E(s);else if("string"==typeof e&&e)i=e.split(/\s+/);else if("object"==typeof e&&e&&"undefined"==typeof t)for(n in e)e.hasOwnProperty(n)&&"string"==typeof n&&n&&"function"==typeof e[n]&&this.off(n,e[n]);if(i&&i.length)for(n=0,a=i.length;a>n;n++)if(e=i[n].toLowerCase().replace(/^on/,""),o=s[e],o&&o.length)if(t)for(r=C(t,o);-1!==r;)o.splice(r,1),r=C(t,o,r);else s[e].length=0;return this},H.prototype.handlers=function(e){var t,n=null,a=l[this.id]&&l[this.id].handlers;if(a){if("string"==typeof e&&e)return a[e]?a[e].slice(0):null;n={};for(t in a)a.hasOwnProperty(t)&&a[t]&&(n[t]=a[t].slice(0))}return n},H.prototype.clip=function(e){e=O(e);for(var t=0;t<e.length;t++)if(e.hasOwnProperty(t)&&e[t]&&1===e[t].nodeType){e[t].zcClippingId?-1===C(this.id,c[e[t].zcClippingId])&&c[e[t].zcClippingId].push(this.id):(e[t].zcClippingId="zcClippingId_"+s++,c[e[t].zcClippingId]=[this.id],R.autoActivate===!0&&h(e[t],"mouseover",p));var n=l[this.id].elements;-1===C(e[t],n)&&n.push(e[t])}return this},H.prototype.unclip=function(e){var t=l[this.id];if(!t)return this;var n,a=t.elements;e="undefined"==typeof e?a.slice(0):O(e);for(var r=e.length;r--;)if(e.hasOwnProperty(r)&&e[r]&&1===e[r].nodeType){for(n=0;-1!==(n=C(e[r],a,n));)a.splice(n,1);var i=c[e[r].zcClippingId];if(i){for(n=0;-1!==(n=C(this.id,i,n));)i.splice(n,1);0===i.length&&(R.autoActivate===!0&&v(e[r],"mouseover",p),delete e[r].zcClippingId)}}return this},H.prototype.elements=function(){var e=l[this.id];return e&&e.elements?e.elements.slice(0):[]};var K=function(e){var t,n,a,r,i,o=[];if(e&&1===e.nodeType&&(t=e.zcClippingId)&&c.hasOwnProperty(t)&&(n=c[t],n&&n.length))for(a=0,r=n.length;r>a;a++)i=l[n[a]].instance,i&&i instanceof H&&o.push(i);return o};R.hoverClass="zeroclipboard-is-hover",R.activeClass="zeroclipboard-is-active","function"==typeof define&&define.amd?define(function(){return H}):"object"==typeof n&&n&&"object"==typeof n.exports&&n.exports?n.exports=H:e.ZeroClipboard=H}(function(){return this}())});