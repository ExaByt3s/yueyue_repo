define("common/ueditor/1.0.0/third-party/webuploader/webuploader.flashonly",function(e,t,n){!function(e,t){var i,r={},o=function(e,t){var n,i,r;if("string"==typeof e)return a(e);for(n=[],i=e.length,r=0;i>r;r++)n.push(a(e[r]));return t.apply(null,n)},s=function(e,t,n){2===arguments.length&&(n=t,t=null),o(t||[],function(){u(e,n,arguments)})},u=function(e,t,n){var i,s={exports:t};"function"==typeof t&&(n.length||(n=[o,s.exports,s]),i=t.apply(null,n),void 0!==i&&(s.exports=i)),r[e]=s.exports},a=function(t){var n=r[t]||e[t];if(!n)throw new Error("`"+t+"` is undefined");return n},c=function(e){var t,n,i,o,s,u;u=function(e){return e&&e.charAt(0).toUpperCase()+e.substr(1)};for(t in r)if(n=e,r.hasOwnProperty(t)){for(i=t.split("/"),s=u(i.pop());o=u(i.shift());)n[o]=n[o]||{},n=n[o];n[s]=r[t]}},l=t(e,s,o);c(l),"object"==typeof n&&"object"==typeof n.exports?n.exports=l:"function"==typeof define&&define.amd?define([],l):(i=e.WebUploader,e.WebUploader=l,e.WebUploader.noConflict=function(){e.WebUploader=i})}(this,function(e,t,n){return t("dollar-third",[],function(){return e.jQuery||e.Zepto}),t("dollar",["dollar-third"],function(e){return e}),t("promise-third",["dollar"],function(e){return{Deferred:e.Deferred,when:e.when,isPromise:function(e){return e&&"function"==typeof e.then}}}),t("promise",["promise-third"],function(e){return e}),t("base",["dollar","promise"],function(t,n){function i(e){return function(){return u.apply(e,arguments)}}function r(e,t){return function(){return e.apply(t,arguments)}}function o(e){var t;return Object.create?Object.create(e):(t=function(){},t.prototype=e,new t)}var s=function(){},u=Function.call;return{version:"0.1.2",$:t,Deferred:n.Deferred,isPromise:n.isPromise,when:n.when,browser:function(e){var t={},n=e.match(/WebKit\/([\d.]+)/),i=e.match(/Chrome\/([\d.]+)/)||e.match(/CriOS\/([\d.]+)/),r=e.match(/MSIE\s([\d\.]+)/)||e.match(/(?:trident)(?:.*rv:([\w.]+))?/i),o=e.match(/Firefox\/([\d.]+)/),s=e.match(/Safari\/([\d.]+)/),u=e.match(/OPR\/([\d.]+)/);return n&&(t.webkit=parseFloat(n[1])),i&&(t.chrome=parseFloat(i[1])),r&&(t.ie=parseFloat(r[1])),o&&(t.firefox=parseFloat(o[1])),s&&(t.safari=parseFloat(s[1])),u&&(t.opera=parseFloat(u[1])),t}(navigator.userAgent),os:function(e){var t={},n=e.match(/(?:Android);?[\s\/]+([\d.]+)?/),i=e.match(/(?:iPad|iPod|iPhone).*OS\s([\d_]+)/);return n&&(t.android=parseFloat(n[1])),i&&(t.ios=parseFloat(i[1].replace(/_/g,"."))),t}(navigator.userAgent),inherits:function(e,n,i){var r;return"function"==typeof n?(r=n,n=null):r=n&&n.hasOwnProperty("constructor")?n.constructor:function(){return e.apply(this,arguments)},t.extend(!0,r,e,i||{}),r.__super__=e.prototype,r.prototype=o(e.prototype),n&&t.extend(!0,r.prototype,n),r},noop:s,bindFn:r,log:function(){return e.console?r(console.log,console):s}(),nextTick:function(){return function(e){setTimeout(e,1)}}(),slice:i([].slice),guid:function(){var e=0;return function(t){for(var n=(+new Date).toString(32),i=0;5>i;i++)n+=Math.floor(65535*Math.random()).toString(32);return(t||"wu_")+n+(e++).toString(32)}}(),formatSize:function(e,t,n){var i;for(n=n||["B","K","M","G","TB"];(i=n.shift())&&e>1024;)e/=1024;return("B"===i?e:e.toFixed(t||2))+i}}}),t("mediator",["base"],function(e){function t(e,t,n,i){return o.grep(e,function(e){return!(!e||t&&e.e!==t||n&&e.cb!==n&&e.cb._cb!==n||i&&e.ctx!==i)})}function n(e,t,n){o.each((e||"").split(u),function(e,i){n(i,t)})}function i(e,t){for(var n,i=!1,r=-1,o=e.length;++r<o;)if(n=e[r],n.cb.apply(n.ctx2,t)===!1){i=!0;break}return!i}var r,o=e.$,s=[].slice,u=/\s+/;return r={on:function(e,t,i){var r,o=this;return t?(r=this._events||(this._events=[]),n(e,t,function(e,t){var n={e:e};n.cb=t,n.ctx=i,n.ctx2=i||o,n.id=r.length,r.push(n)}),this):this},once:function(e,t,i){var r=this;return t?(n(e,t,function(e,t){var n=function(){return r.off(e,n),t.apply(i||r,arguments)};n._cb=t,r.on(e,n,i)}),r):r},off:function(e,i,r){var s=this._events;return s?e||i||r?(n(e,i,function(e,n){o.each(t(s,e,n,r),function(){delete s[this.id]})}),this):(this._events=[],this):this},trigger:function(e){var n,r,o;return this._events&&e?(n=s.call(arguments,1),r=t(this._events,e),o=t(this._events,"all"),i(r,n)&&i(o,arguments)):this}},o.extend({installTo:function(e){return o.extend(e,r)}},r)}),t("uploader",["base","mediator"],function(e,t){function n(e){this.options=i.extend(!0,{},n.options,e),this._init(this.options)}var i=e.$;return n.options={},t.installTo(n.prototype),i.each({upload:"start-upload",stop:"stop-upload",getFile:"get-file",getFiles:"get-files",addFile:"add-file",addFiles:"add-file",sort:"sort-files",removeFile:"remove-file",skipFile:"skip-file",retry:"retry",isInProgress:"is-in-progress",makeThumb:"make-thumb",getDimension:"get-dimension",addButton:"add-btn",getRuntimeType:"get-runtime-type",refresh:"refresh",disable:"disable",enable:"enable",reset:"reset"},function(e,t){n.prototype[e]=function(){return this.request(t,arguments)}}),i.extend(n.prototype,{state:"pending",_init:function(e){var t=this;t.request("init",e,function(){t.state="ready",t.trigger("ready")})},option:function(e,t){var n=this.options;return arguments.length>1?void(i.isPlainObject(t)&&i.isPlainObject(n[e])?i.extend(n[e],t):n[e]=t):e?n[e]:n},getStats:function(){var e=this.request("get-stats");return{successNum:e.numOfSuccess,cancelNum:e.numOfCancel,invalidNum:e.numOfInvalid,uploadFailNum:e.numOfUploadFailed,queueNum:e.numOfQueue}},trigger:function(e){var n=[].slice.call(arguments,1),r=this.options,o="on"+e.substring(0,1).toUpperCase()+e.substring(1);return t.trigger.apply(this,arguments)===!1||i.isFunction(r[o])&&r[o].apply(this,n)===!1||i.isFunction(this[o])&&this[o].apply(this,n)===!1||t.trigger.apply(t,[this,e].concat(n))===!1?!1:!0},request:e.noop}),e.create=n.create=function(e){return new n(e)},e.Uploader=n,n}),t("runtime/runtime",["base","mediator"],function(e,t){function n(t){this.options=i.extend({container:document.body},t),this.uid=e.guid("rt_")}var i=e.$,r={},o=function(e){for(var t in e)if(e.hasOwnProperty(t))return t;return null};return i.extend(n.prototype,{getContainer:function(){var e,t,n=this.options;return this._container?this._container:(e=i(n.container||document.body),t=i(document.createElement("div")),t.attr("id","rt_"+this.uid),t.css({position:"absolute",top:"0px",left:"0px",width:"1px",height:"1px",overflow:"hidden"}),e.append(t),e.addClass("webuploader-container"),this._container=t,t)},init:e.noop,exec:e.noop,destroy:function(){this._container&&this._container.parentNode.removeChild(this.__container),this.off()}}),n.orders="html5,flash",n.addRuntime=function(e,t){r[e]=t},n.hasRuntime=function(e){return!!(e?r[e]:o(r))},n.create=function(e,t){var s,u;if(t=t||n.orders,i.each(t.split(/\s*,\s*/g),function(){return r[this]?(s=this,!1):void 0}),s=s||o(r),!s)throw new Error("Runtime Error");return u=new r[s](e)},t.installTo(n.prototype),n}),t("runtime/client",["base","mediator","runtime/runtime"],function(e,t,n){function i(t,i){var o,s=e.Deferred();this.uid=e.guid("client_"),this.runtimeReady=function(e){return s.done(e)},this.connectRuntime=function(t,u){if(o)throw new Error("already connected!");return s.done(u),"string"==typeof t&&r.get(t)&&(o=r.get(t)),o=o||r.get(null,i),o?(e.$.extend(o.options,t),o.__promise.then(s.resolve),o.__client++):(o=n.create(t,t.runtimeOrder),o.__promise=s.promise(),o.once("ready",s.resolve),o.init(),r.add(o),o.__client=1),i&&(o.__standalone=i),o},this.getRuntime=function(){return o},this.disconnectRuntime=function(){o&&(o.__client--,o.__client<=0&&(r.remove(o),delete o.__promise,o.destroy()),o=null)},this.exec=function(){if(o){var n=e.slice(arguments);return t&&n.unshift(t),o.exec.apply(this,n)}},this.getRuid=function(){return o&&o.uid},this.destroy=function(e){return function(){e&&e.apply(this,arguments),this.trigger("destroy"),this.off(),this.exec("destroy"),this.disconnectRuntime()}}(this.destroy)}var r;return r=function(){var e={};return{add:function(t){e[t.uid]=t},get:function(t,n){var i;if(t)return e[t];for(i in e)if(!n||!e[i].__standalone)return e[i];return null},remove:function(t){delete e[t.uid]}}}(),t.installTo(i.prototype),i}),t("lib/blob",["base","runtime/client"],function(e,t){function n(e,n){var i=this;i.source=n,i.ruid=e,t.call(i,"Blob"),this.uid=n.uid||this.uid,this.type=n.type||"",this.size=n.size||0,e&&i.connectRuntime(e)}return e.inherits(t,{constructor:n,slice:function(e,t){return this.exec("slice",e,t)},getSource:function(){return this.source}}),n}),t("lib/file",["base","lib/blob"],function(e,t){function n(e,n){var o;t.apply(this,arguments),this.name=n.name||"untitled"+i++,o=r.exec(n.name)?RegExp.$1.toLowerCase():"",!o&&this.type&&(o=/\/(jpg|jpeg|png|gif|bmp)$/i.exec(this.type)?RegExp.$1.toLowerCase():"",this.name+="."+o),!this.type&&~"jpg,jpeg,png,gif,bmp".indexOf(o)&&(this.type="image/"+("jpg"===o?"jpeg":o)),this.ext=o,this.lastModifiedDate=n.lastModifiedDate||(new Date).toLocaleString()}var i=1,r=/\.([^.]+)$/;return e.inherits(t,n)}),t("lib/filepicker",["base","runtime/client","lib/file"],function(t,n,i){function r(e){if(e=this.options=o.extend({},r.options,e),e.container=o(e.id),!e.container.length)throw new Error("��ťָ������");e.innerHTML=e.innerHTML||e.label||e.container.html()||"",e.button=o(e.button||document.createElement("div")),e.button.html(e.innerHTML),e.container.html(e.button),n.call(this,"FilePicker",!0)}var o=t.$;return r.options={button:null,container:null,label:null,innerHTML:null,multiple:!0,accept:null,name:"file"},t.inherits(n,{constructor:r,init:function(){var t=this,n=t.options,r=n.button;r.addClass("webuploader-pick"),t.on("all",function(e){var s;switch(e){case"mouseenter":r.addClass("webuploader-pick-hover");break;case"mouseleave":r.removeClass("webuploader-pick-hover");break;case"change":s=t.exec("getFiles"),t.trigger("select",o.map(s,function(e){return e=new i(t.getRuid(),e),e._refer=n.container,e}),n.container)}}),t.connectRuntime(n,function(){t.refresh(),t.exec("init",n),t.trigger("ready")}),o(e).on("resize",function(){t.refresh()})},refresh:function(){var e=this.getRuntime().getContainer(),t=this.options.button,n=t.outerWidth?t.outerWidth():t.width(),i=t.outerHeight?t.outerHeight():t.height(),r=t.offset();n&&i&&e.css({bottom:"auto",right:"auto",width:n+"px",height:i+"px"}).offset(r)},enable:function(){var e=this.options.button;e.removeClass("webuploader-pick-disable"),this.refresh()},disable:function(){var e=this.options.button;this.getRuntime().getContainer().css({top:"-99999px"}),e.addClass("webuploader-pick-disable")},destroy:function(){this.runtime&&(this.exec("destroy"),this.disconnectRuntime())}}),r}),t("widgets/widget",["base","uploader"],function(e,t){function n(e){if(!e)return!1;var t=e.length,n=r.type(e);return 1===e.nodeType&&t?!0:"array"===n||"function"!==n&&"string"!==n&&(0===t||"number"==typeof t&&t>0&&t-1 in e)}function i(e){this.owner=e,this.options=e.options}var r=e.$,o=t.prototype._init,s={},u=[];return r.extend(i.prototype,{init:e.noop,invoke:function(e,t){var n=this.responseMap;return n&&e in n&&n[e]in this&&r.isFunction(this[n[e]])?this[n[e]].apply(this,t):s},request:function(){return this.owner.request.apply(this.owner,arguments)}}),r.extend(t.prototype,{_init:function(){var e=this,t=e._widgets=[];return r.each(u,function(n,i){t.push(new i(e))}),o.apply(e,arguments)},request:function(t,i,r){var o,u,a,c,l=0,f=this._widgets,h=f.length,p=[],d=[];for(i=n(i)?i:[i];h>l;l++)o=f[l],u=o.invoke(t,i),u!==s&&(e.isPromise(u)?d.push(u):p.push(u));return r||d.length?(a=e.when.apply(e,d),c=a.pipe?"pipe":"then",a[c](function(){var t=e.Deferred(),n=arguments;return setTimeout(function(){t.resolve.apply(t,n)},1),t.promise()})[c](r||e.noop)):p[0]}}),t.register=i.register=function(t,n){var o,s={init:"init"};return 1===arguments.length?(n=t,n.responseMap=s):n.responseMap=r.extend(s,t),o=e.inherits(i,n),u.push(o),o},i}),t("widgets/filepicker",["base","uploader","lib/filepicker","widgets/widget"],function(e,t,n){var i=e.$;return i.extend(t.options,{pick:null,accept:null}),t.register({"add-btn":"addButton",refresh:"refresh",disable:"disable",enable:"enable"},{init:function(e){return this.pickers=[],e.pick&&this.addButton(e.pick)},refresh:function(){i.each(this.pickers,function(){this.refresh()})},addButton:function(t){var r,o,s,u=this,a=u.options,c=a.accept;if(t)return s=e.Deferred(),i.isPlainObject(t)||(t={id:t}),r=i.extend({},t,{accept:i.isPlainObject(c)?[c]:c,swf:a.swf,runtimeOrder:a.runtimeOrder}),o=new n(r),o.once("ready",s.resolve),o.on("select",function(e){u.owner.request("add-file",[e])}),o.init(),this.pickers.push(o),s.promise()},disable:function(){i.each(this.pickers,function(){this.disable()})},enable:function(){i.each(this.pickers,function(){this.enable()})}})}),t("lib/image",["base","runtime/client","lib/blob"],function(e,t,n){function i(e){this.options=r.extend({},i.options,e),t.call(this,"Image"),this.on("load",function(){this._info=this.exec("info"),this._meta=this.exec("meta")})}var r=e.$;return i.options={quality:90,crop:!1,preserveHeaders:!0,allowMagnify:!0},e.inherits(t,{constructor:i,info:function(e){return e?(this._info=e,this):this._info},meta:function(e){return e?(this._meta=e,this):this._meta},loadFromBlob:function(e){var t=this,n=e.getRuid();this.connectRuntime(n,function(){t.exec("init",t.options),t.exec("loadFromBlob",e)})},resize:function(){var t=e.slice(arguments);return this.exec.apply(this,["resize"].concat(t))},getAsDataUrl:function(e){return this.exec("getAsDataUrl",e)},getAsBlob:function(e){var t=this.exec("getAsBlob",e);return new n(this.getRuid(),t)}}),i}),t("widgets/image",["base","uploader","lib/image","widgets/widget"],function(e,t,n){var i,r=e.$;return i=function(e){var t=0,n=[],i=function(){for(var i;n.length&&e>t;)i=n.shift(),t+=i[0],i[1]()};return function(e,r,o){n.push([r,o]),e.once("destroy",function(){t-=r,setTimeout(i,1)}),setTimeout(i,1)}}(5242880),r.extend(t.options,{thumb:{width:110,height:110,quality:70,allowMagnify:!0,crop:!0,preserveHeaders:!1,type:"image/jpeg"},compress:{width:1600,height:1600,quality:90,allowMagnify:!1,crop:!1,preserveHeaders:!0}}),t.register({"make-thumb":"makeThumb","before-send-file":"compressImage"},{makeThumb:function(e,t,o,s){var u,a;return e=this.request("get-file",e),e.type.match(/^image/)?(u=r.extend({},this.options.thumb),r.isPlainObject(o)&&(u=r.extend(u,o),o=null),o=o||u.width,s=s||u.height,a=new n(u),a.once("load",function(){e._info=e._info||a.info(),e._meta=e._meta||a.meta(),a.resize(o,s)}),a.once("complete",function(){t(!1,a.getAsDataUrl(u.type)),a.destroy()}),a.once("error",function(){t(!0),a.destroy()}),void i(a,e.source.size,function(){e._info&&a.info(e._info),e._meta&&a.meta(e._meta),a.loadFromBlob(e.source)})):void t(!0)},compressImage:function(t){var i,o,s=this.options.compress||this.options.resize,u=s&&s.compressSize||307200;return t=this.request("get-file",t),!s||!~"image/jpeg,image/jpg".indexOf(t.type)||t.size<u||t._compressed?void 0:(s=r.extend({},s),o=e.Deferred(),i=new n(s),o.always(function(){i.destroy(),i=null}),i.once("error",o.reject),i.once("load",function(){t._info=t._info||i.info(),t._meta=t._meta||i.meta(),i.resize(s.width,s.height)}),i.once("complete",function(){var e,n;try{e=i.getAsBlob(s.type),n=t.size,e.size<n&&(t.source=e,t.size=e.size,t.trigger("resize",e.size,n)),t._compressed=!0,o.resolve()}catch(r){o.resolve()}}),t._info&&i.info(t._info),t._meta&&i.meta(t._meta),i.loadFromBlob(t.source),o.promise())}})}),t("file",["base","mediator"],function(e,t){function n(){return o+s++}function i(e){this.name=e.name||"Untitled",this.size=e.size||0,this.type=e.type||"application",this.lastModifiedDate=e.lastModifiedDate||1*new Date,this.id=n(),this.ext=u.exec(this.name)?RegExp.$1:"",this.statusText="",a[this.id]=i.Status.INITED,this.source=e,this.loaded=0,this.on("error",function(e){this.setStatus(i.Status.ERROR,e)})}var r=e.$,o="WU_FILE_",s=0,u=/\.([^.]+)$/,a={};return r.extend(i.prototype,{setStatus:function(e,t){var n=a[this.id];"undefined"!=typeof t&&(this.statusText=t),e!==n&&(a[this.id]=e,this.trigger("statuschange",e,n))},getStatus:function(){return a[this.id]},getSource:function(){return this.source},destory:function(){delete a[this.id]}}),t.installTo(i.prototype),i.Status={INITED:"inited",QUEUED:"queued",PROGRESS:"progress",ERROR:"error",COMPLETE:"complete",CANCELLED:"cancelled",INTERRUPT:"interrupt",INVALID:"invalid"},i}),t("queue",["base","mediator","file"],function(e,t,n){function i(){this.stats={numOfQueue:0,numOfSuccess:0,numOfCancel:0,numOfProgress:0,numOfUploadFailed:0,numOfInvalid:0},this._queue=[],this._map={}}var r=e.$,o=n.Status;return r.extend(i.prototype,{append:function(e){return this._queue.push(e),this._fileAdded(e),this},prepend:function(e){return this._queue.unshift(e),this._fileAdded(e),this},getFile:function(e){return"string"!=typeof e?e:this._map[e]},fetch:function(e){var t,n,i=this._queue.length;for(e=e||o.QUEUED,t=0;i>t;t++)if(n=this._queue[t],e===n.getStatus())return n;return null},sort:function(e){"function"==typeof e&&this._queue.sort(e)},getFiles:function(){for(var e,t=[].slice.call(arguments,0),n=[],i=0,o=this._queue.length;o>i;i++)e=this._queue[i],(!t.length||~r.inArray(e.getStatus(),t))&&n.push(e);return n},_fileAdded:function(e){var t=this,n=this._map[e.id];n||(this._map[e.id]=e,e.on("statuschange",function(e,n){t._onFileStatusChange(e,n)})),e.setStatus(o.QUEUED)},_onFileStatusChange:function(e,t){var n=this.stats;switch(t){case o.PROGRESS:n.numOfProgress--;break;case o.QUEUED:n.numOfQueue--;break;case o.ERROR:n.numOfUploadFailed--;break;case o.INVALID:n.numOfInvalid--}switch(e){case o.QUEUED:n.numOfQueue++;break;case o.PROGRESS:n.numOfProgress++;break;case o.ERROR:n.numOfUploadFailed++;break;case o.COMPLETE:n.numOfSuccess++;break;case o.CANCELLED:n.numOfCancel++;break;case o.INVALID:n.numOfInvalid++}}}),t.installTo(i.prototype),i}),t("widgets/queue",["base","uploader","queue","file","lib/file","runtime/client","widgets/widget"],function(e,t,n,i,r,o){var s=e.$,u=/\.\w+$/,a=i.Status;return t.register({"sort-files":"sortFiles","add-file":"addFiles","get-file":"getFile","fetch-file":"fetchFile","get-stats":"getStats","get-files":"getFiles","remove-file":"removeFile",retry:"retry",reset:"reset","accept-file":"acceptFile"},{init:function(t){var i,r,u,a,c,l,f,h=this;if(s.isPlainObject(t.accept)&&(t.accept=[t.accept]),t.accept){for(c=[],u=0,r=t.accept.length;r>u;u++)a=t.accept[u].extensions,a&&c.push(a);c.length&&(l="\\."+c.join(",").replace(/,/g,"$|\\.").replace(/\*/g,".*")+"$"),h.accept=new RegExp(l,"i")}return h.queue=new n,h.stats=h.queue.stats,"html5"===this.request("predict-runtime-type")?(i=e.Deferred(),f=new o("Placeholder"),f.connectRuntime({runtimeOrder:"html5"},function(){h._ruid=f.getRuid(),i.resolve()}),i.promise()):void 0},_wrapFile:function(e){if(!(e instanceof i)){if(!(e instanceof r)){if(!this._ruid)throw new Error("Can't add external files.");e=new r(this._ruid,e)}e=new i(e)}return e},acceptFile:function(e){var t=!e||e.size<6||this.accept&&u.exec(e.name)&&!this.accept.test(e.name);return!t},_addFile:function(e){var t=this;return e=t._wrapFile(e),t.owner.trigger("beforeFileQueued",e)?t.acceptFile(e)?(t.queue.append(e),t.owner.trigger("fileQueued",e),e):void t.owner.trigger("error","Q_TYPE_DENIED",e):void 0},getFile:function(e){return this.queue.getFile(e)},addFiles:function(e){var t=this;e.length||(e=[e]),e=s.map(e,function(e){return t._addFile(e)}),t.owner.trigger("filesQueued",e),t.options.auto&&t.request("start-upload")},getStats:function(){return this.stats},removeFile:function(e){var t=this;e=e.id?e:t.queue.getFile(e),e.setStatus(a.CANCELLED),t.owner.trigger("fileDequeued",e)},getFiles:function(){return this.queue.getFiles.apply(this.queue,arguments)},fetchFile:function(){return this.queue.fetch.apply(this.queue,arguments)},retry:function(e,t){var n,i,r,o=this;if(e)return e=e.id?e:o.queue.getFile(e),e.setStatus(a.QUEUED),void(t||o.request("start-upload"));for(n=o.queue.getFiles(a.ERROR),i=0,r=n.length;r>i;i++)e=n[i],e.setStatus(a.QUEUED);o.request("start-upload")},sortFiles:function(){return this.queue.sort.apply(this.queue,arguments)},reset:function(){this.queue=new n,this.stats=this.queue.stats}})}),t("widgets/runtime",["uploader","runtime/runtime","widgets/widget"],function(e,t){return e.support=function(){return t.hasRuntime.apply(t,arguments)},e.register({"predict-runtime-type":"predictRuntmeType"},{init:function(){if(!this.predictRuntmeType())throw Error("Runtime Error")},predictRuntmeType:function(){var e,n,i=this.options.runtimeOrder||t.orders,r=this.type;if(!r)for(i=i.split(/\s*,\s*/g),e=0,n=i.length;n>e;e++)if(t.hasRuntime(i[e])){this.type=r=i[e];break}return r}})}),t("lib/transport",["base","runtime/client","mediator"],function(e,t,n){function i(e){var n=this;e=n.options=r.extend(!0,{},i.options,e||{}),t.call(this,"Transport"),this._blob=null,this._formData=e.formData||{},this._headers=e.headers||{},this.on("progress",this._timeout),this.on("load error",function(){n.trigger("progress",1),clearTimeout(n._timer)})}var r=e.$;return i.options={server:"",method:"POST",withCredentials:!1,fileVal:"file",timeout:12e4,formData:{},headers:{},sendAsBinary:!1},r.extend(i.prototype,{appendBlob:function(e,t,n){var i=this,r=i.options;i.getRuid()&&i.disconnectRuntime(),i.connectRuntime(t.ruid,function(){i.exec("init")}),i._blob=t,r.fileVal=e||r.fileVal,r.filename=n||r.filename},append:function(e,t){"object"==typeof e?r.extend(this._formData,e):this._formData[e]=t},setRequestHeader:function(e,t){"object"==typeof e?r.extend(this._headers,e):this._headers[e]=t},send:function(e){this.exec("send",e),this._timeout()},abort:function(){return clearTimeout(this._timer),this.exec("abort")},destroy:function(){this.trigger("destroy"),this.off(),this.exec("destroy"),this.disconnectRuntime()},getResponse:function(){return this.exec("getResponse")},getResponseAsJson:function(){return this.exec("getResponseAsJson")},getStatus:function(){return this.exec("getStatus")},_timeout:function(){var e=this,t=e.options.timeout;t&&(clearTimeout(e._timer),e._timer=setTimeout(function(){e.abort(),e.trigger("error","timeout")},t))}}),n.installTo(i.prototype),i}),t("widgets/upload",["base","uploader","file","lib/transport","widgets/widget"],function(e,t,n,i){function r(e,t){for(var n,i=[],r=e.source,o=r.size,s=t?Math.ceil(o/t):1,u=0,a=0;s>a;)n=Math.min(t,o-u),i.push({file:e,start:u,end:t?u+n:o,total:o,chunks:s,chunk:a++}),u+=n;return e.blocks=i.concat(),e.remaning=i.length,{file:e,has:function(){return!!i.length},fetch:function(){return i.shift()}}}var o=e.$,s=e.isPromise,u=n.Status;o.extend(t.options,{prepareNextFile:!1,chunked:!1,chunkSize:5242880,chunkRetry:2,threads:3,formData:null}),t.register({"start-upload":"start","stop-upload":"stop","skip-file":"skipFile","is-in-progress":"isInProgress"},{init:function(){var t=this.owner;this.runing=!1,this.pool=[],this.pending=[],this.remaning=0,this.__tick=e.bindFn(this._tick,this),t.on("uploadComplete",function(e){e.blocks&&o.each(e.blocks,function(e,t){t.transport&&(t.transport.abort(),t.transport.destroy()),delete t.transport}),delete e.blocks,delete e.remaning})},start:function(){var t=this;o.each(t.request("get-files",u.INVALID),function(){t.request("remove-file",this)}),t.runing||(t.runing=!0,o.each(t.pool,function(e,n){var i=n.file;i.getStatus()===u.INTERRUPT&&(i.setStatus(u.PROGRESS),t._trigged=!1,n.transport&&n.transport.send())}),t._trigged=!1,t.owner.trigger("startUpload"),e.nextTick(t.__tick))},stop:function(e){var t=this;t.runing!==!1&&(t.runing=!1,e&&o.each(t.pool,function(e,t){t.transport&&t.transport.abort(),t.file.setStatus(u.INTERRUPT)}),t.owner.trigger("stopUpload"))},isInProgress:function(){return!!this.runing},getStats:function(){return this.request("get-stats")},skipFile:function(e,t){e=this.request("get-file",e),e.setStatus(t||u.COMPLETE),e.skipped=!0,e.blocks&&o.each(e.blocks,function(e,t){var n=t.transport;n&&(n.abort(),n.destroy(),delete t.transport)}),this.owner.trigger("uploadSkip",e)},_tick:function(){var t,n,i=this,r=i.options;return i._promise?i._promise.always(i.__tick):void(i.pool.length<r.threads&&(n=i._nextBlock())?(i._trigged=!1,t=function(t){i._promise=null,t&&t.file&&i._startSend(t),e.nextTick(i.__tick)},i._promise=s(n)?n.always(t):t(n)):i.remaning||i.getStats().numOfQueue||(i.runing=!1,i._trigged||e.nextTick(function(){i.owner.trigger("uploadFinished")}),i._trigged=!0))},_nextBlock:function(){var e,t,n=this,i=n._act,o=n.options;return i&&i.has()&&i.file.getStatus()===u.PROGRESS?(o.prepareNextFile&&!n.pending.length&&n._prepareNextFile(),i.fetch()):n.runing?(!n.pending.length&&n.getStats().numOfQueue&&n._prepareNextFile(),e=n.pending.shift(),t=function(e){return e?(i=r(e,o.chunked?o.chunkSize:0),n._act=i,i.fetch()):null},s(e)?e[e.pipe?"pipe":"then"](t):t(e)):void 0},_prepareNextFile:function(){var e,t=this,n=t.request("fetch-file"),i=t.pending;n&&(e=t.request("before-send-file",n,function(){return n.getStatus()===u.QUEUED?(t.owner.trigger("uploadStart",n),n.setStatus(u.PROGRESS),n):t._finishFile(n)}),e.done(function(){var t=o.inArray(e,i);~t&&i.splice(t,1,n)}),e.fail(function(e){n.setStatus(u.ERROR,e),t.owner.trigger("uploadError",n,e),t.owner.trigger("uploadComplete",n)}),i.push(e))},_popBlock:function(e){var t=o.inArray(e,this.pool);this.pool.splice(t,1),e.file.remaning--,this.remaning--},_startSend:function(t){var n,i=this,r=t.file;i.pool.push(t),i.remaning++,t.blob=1===t.chunks?r.source:r.source.slice(t.start,t.end),n=i.request("before-send",t,function(){r.getStatus()===u.PROGRESS?i._doSend(t):(i._popBlock(t),e.nextTick(i.__tick))}),n.fail(function(){1===r.remaning?i._finishFile(r).always(function(){t.percentage=1,i._popBlock(t),i.owner.trigger("uploadComplete",r),e.nextTick(i.__tick)}):(t.percentage=1,i._popBlock(t),e.nextTick(i.__tick))})},_doSend:function(t){var n,r,s=this,a=s.owner,c=s.options,l=t.file,f=new i(c),h=o.extend({},c.formData),p=o.extend({},c.headers);t.transport=f,f.on("destroy",function(){delete t.transport,s._popBlock(t),e.nextTick(s.__tick)}),f.on("progress",function(e){var n=0,i=0;n=t.percentage=e,t.chunks>1&&(o.each(l.blocks,function(e,t){i+=(t.percentage||0)*(t.end-t.start)}),n=i/l.size),a.trigger("uploadProgress",l,n||0)}),n=function(e){var n;return r=f.getResponseAsJson()||{},r._raw=f.getResponse(),n=function(t){e=t},a.trigger("uploadAccept",t,r,n)||(e=e||"server"),e},f.on("error",function(e,i){t.retried=t.retried||0,t.chunks>1&&~"http,abort".indexOf(e)&&t.retried<c.chunkRetry?(t.retried++,f.send()):(i||"server"!==e||(e=n(e)),l.setStatus(u.ERROR,e),a.trigger("uploadError",l,e),a.trigger("uploadComplete",l))}),f.on("load",function(){var e;return(e=n())?void f.trigger("error",e,!0):void(1===l.remaning?s._finishFile(l,r):f.destroy())}),h=o.extend(h,{id:l.id,name:l.name,type:l.type,lastModifiedDate:l.lastModifiedDate,size:l.size}),t.chunks>1&&o.extend(h,{chunks:t.chunks,chunk:t.chunk}),a.trigger("uploadBeforeSend",t,h,p),f.appendBlob(c.fileVal,t.blob,l.name),f.append(h),f.setRequestHeader(p),f.send()},_finishFile:function(e,t,n){var i=this.owner;return i.request("after-send-file",arguments,function(){e.setStatus(u.COMPLETE),i.trigger("uploadSuccess",e,t,n)}).fail(function(t){e.getStatus()===u.PROGRESS&&e.setStatus(u.ERROR,t),i.trigger("uploadError",e,t)}).always(function(){i.trigger("uploadComplete",e)})}})}),t("widgets/validator",["base","uploader","file","widgets/widget"],function(e,t,n){var i,r=e.$,o={};return i={addValidator:function(e,t){o[e]=t},removeValidator:function(e){delete o[e]}},t.register({init:function(){var e=this;r.each(o,function(){this.call(e.owner)})}}),i.addValidator("fileNumLimit",function(){var e=this,t=e.options,n=0,i=t.fileNumLimit>>0,r=!0;i&&(e.on("beforeFileQueued",function(e){return n>=i&&r&&(r=!1,this.trigger("error","Q_EXCEED_NUM_LIMIT",i,e),setTimeout(function(){r=!0},1)),n>=i?!1:!0}),e.on("fileQueued",function(){n++}),e.on("fileDequeued",function(){n--}),e.on("uploadFinished",function(){n=0}))}),i.addValidator("fileSizeLimit",function(){var e=this,t=e.options,n=0,i=t.fileSizeLimit>>0,r=!0;i&&(e.on("beforeFileQueued",function(e){var t=n+e.size>i;return t&&r&&(r=!1,this.trigger("error","Q_EXCEED_SIZE_LIMIT",i,e),setTimeout(function(){r=!0},1)),t?!1:!0}),e.on("fileQueued",function(e){n+=e.size}),e.on("fileDequeued",function(e){n-=e.size}),e.on("uploadFinished",function(){n=0}))}),i.addValidator("fileSingleSizeLimit",function(){var e=this,t=e.options,i=t.fileSingleSizeLimit;i&&e.on("beforeFileQueued",function(e){return e.size>i?(e.setStatus(n.Status.INVALID,"exceed_size"),this.trigger("error","F_EXCEED_SIZE",e),!1):void 0})}),i.addValidator("duplicate",function(){function e(e){for(var t,n=0,i=0,r=e.length;r>i;i++)t=e.charCodeAt(i),n=t+(n<<6)+(n<<16)-n;return n}var t=this,n=t.options,i={};n.duplicate||(t.on("beforeFileQueued",function(t){var n=t.__hash||(t.__hash=e(t.name+t.size+t.lastModifiedDate));return i[n]?(this.trigger("error","F_DUPLICATE",t),!1):void 0}),t.on("fileQueued",function(e){var t=e.__hash;t&&(i[t]=!0)}),t.on("fileDequeued",function(e){var t=e.__hash;t&&delete i[t]}))}),i}),t("runtime/compbase",[],function(){function e(e,t){this.owner=e,this.options=e.options,this.getRuntime=function(){return t},this.getRuid=function(){return t.uid},this.trigger=function(){return e.trigger.apply(e,arguments)}}return e}),t("runtime/flash/runtime",["base","runtime/runtime","runtime/compbase"],function(t,n,i){function r(){var e;try{e=navigator.plugins["Shockwave Flash"],e=e.description}catch(t){try{e=new ActiveXObject("ShockwaveFlash.ShockwaveFlash").GetVariable("$version")}catch(n){e="0.0"}}return e=e.match(/\d+/g),parseFloat(e[0]+"."+e[1],10)}function o(){function i(e,t){var n,i,r=e.type||e;n=r.split("::"),i=n[0],r=n[1],"Ready"===r&&i===c.uid?c.trigger("ready"):o[i]&&o[i].trigger(r.toLowerCase(),e,t)}var r={},o={},s=this.destory,c=this,l=t.guid("webuploader_");n.apply(c,arguments),c.type=u,c.exec=function(e,n){var i,s=this,u=s.uid,l=t.slice(arguments,2);return o[u]=s,a[e]&&(r[u]||(r[u]=new a[e](s,c)),i=r[u],i[n])?i[n].apply(i,l):c.flashExec.apply(s,arguments)},e[l]=function(){var e=arguments;setTimeout(function(){i.apply(null,e)},1)},this.jsreciver=l,this.destory=function(){return s&&s.apply(this,arguments)},this.flashExec=function(e,n){var i=c.getFlash(),r=t.slice(arguments,2);return i.exec(this.uid,e,n,r)}}var s=t.$,u="flash",a={};return t.inherits(n,{constructor:o,init:function(){var e,n=this.getContainer(),i=this.options;n.css({position:"absolute",top:"-8px",left:"-8px",width:"9px",height:"9px",overflow:"hidden"}),e='<object id="'+this.uid+'" type="application/x-shockwave-flash" data="'+i.swf+'" ',t.browser.ie&&(e+='classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" '),e+='width="100%" height="100%" style="outline:0"><param name="movie" value="'+i.swf+'" /><param name="flashvars" value="uid='+this.uid+"&jsreciver="+this.jsreciver+'" /><param name="wmode" value="transparent" /><param name="allowscriptaccess" value="always" /></object>',n.html(e)},getFlash:function(){return this._flash?this._flash:(this._flash=s("#"+this.uid).get(0),this._flash)}}),o.register=function(e,n){return n=a[e]=t.inherits(i,s.extend({flashExec:function(){var e=this.owner,t=this.getRuntime();return t.flashExec.apply(e,arguments)}},n))},r()>=11.4&&n.addRuntime(u,o),o}),t("runtime/flash/filepicker",["base","runtime/flash/runtime"],function(e,t){var n=e.$;return t.register("FilePicker",{init:function(e){var t,i,r=n.extend({},e);for(t=r.accept&&r.accept.length,i=0;t>i;i++)r.accept[i].title||(r.accept[i].title="Files");delete r.button,delete r.container,this.flashExec("FilePicker","init",r)},destroy:function(){}})}),t("runtime/flash/image",["runtime/flash/runtime"],function(e){return e.register("Image",{loadFromBlob:function(e){var t=this.owner;t.info()&&this.flashExec("Image","info",t.info()),t.meta()&&this.flashExec("Image","meta",t.meta()),this.flashExec("Image","loadFromBlob",e.uid)}})}),t("runtime/flash/transport",["base","runtime/flash/runtime","runtime/client"],function(e,t,n){var i=e.$;return t.register("Transport",{init:function(){this._status=0,this._response=null,this._responseJson=null},send:function(){var e,t=this.owner,n=this.options,r=this._initAjax(),o=t._blob,s=n.server;
r.connectRuntime(o.ruid),n.sendAsBinary?(s+=(/\?/.test(s)?"&":"?")+i.param(t._formData),e=o.uid):(i.each(t._formData,function(e,t){r.exec("append",e,t)}),r.exec("appendBlob",n.fileVal,o.uid,n.filename||t._formData.name||"")),this._setRequestHeader(r,n.headers),r.exec("send",{method:n.method,url:s},e)},getStatus:function(){return this._status},getResponse:function(){return this._response},getResponseAsJson:function(){return this._responseJson},abort:function(){var e=this._xhr;e&&(e.exec("abort"),e.destroy(),this._xhr=e=null)},destroy:function(){this.abort()},_initAjax:function(){var e=this,t=new n("XMLHttpRequest");return t.on("uploadprogress progress",function(t){return e.trigger("progress",t.loaded/t.total)}),t.on("load",function(){var n=t.exec("getStatus"),i="";return t.off(),e._xhr=null,n>=200&&300>n?(e._response=t.exec("getResponse"),e._responseJson=t.exec("getResponseAsJson")):n>=500&&600>n?(e._response=t.exec("getResponse"),e._responseJson=t.exec("getResponseAsJson"),i="server"):i="http",t.destroy(),t=null,i?e.trigger("error",i):e.trigger("load")}),t.on("error",function(){t.off(),e._xhr=null,e.trigger("error","http")}),e._xhr=t,t},_setRequestHeader:function(e,t){i.each(t,function(t,n){e.exec("setRequestHeader",t,n)})}})}),t("preset/flashonly",["base","widgets/filepicker","widgets/image","widgets/queue","widgets/runtime","widgets/upload","widgets/validator","runtime/flash/filepicker","runtime/flash/image","runtime/flash/transport"],function(e){return e}),t("webuploader",["preset/flashonly"],function(e){return e}),n("webuploader")})});