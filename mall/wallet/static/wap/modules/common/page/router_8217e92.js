define("common/page/router",function(t,r,e){!function(){function t(t,r){t.addEventListener?t.addEventListener("hashchange",r,!1):t.attachEvent&&t.attachEvent("hashchange",r)}function r(t,r){t.removeEventListener?t.removeEventListener("hashchange",r,!1):t.detachEvent&&t.detachEvent("hashchange",r)}function o(){for(var t=1;t<arguments.length;t++)for(var r in arguments[t])arguments[t].hasOwnProperty(r)&&(arguments[0][r]=arguments[t][r]);return arguments[0]}Function.prototype.bind||(Function.prototype.bind=function(t){var r=this,e=Array.prototype.slice.call(arguments);return t=e.shift(),function(){return r.apply(t,e.concat(Array.prototype.slice.call(arguments)))}});var n="([^/\\?]+)",i=/:([\w\d]+)/g,s=/\/\*(?!\*)/,a="/([^/\\?]+)",h=/\*{2}/,u="(.*?)\\??",c=/\/*$/,p=function(t){this.href=t,this.params,this.query,this.splat,this.hasNext=!1};p.prototype.get=function(t,r){return this.params&&void 0!==this.params[t]?this.params[t]:this.query&&void 0!==this.query[t]?this.query[t]:void 0!==r?r:void 0};var l=function(r){this._options=o({ignorecase:!0},r),this._routes=[],this._befores=[],this._errors={_:function(t,r,e){console&&console.warn&&console.warn("Router.js : "+e)},_404:function(t,r){console&&console.warn&&console.warn("404! Unmatched route for url "+r)},_500:function(t,r){if(!console||!console.error)throw new Error("500");console.error("500! Internal error route for url "+r)}},this._paused=!1,this._hasChangeHandler=this._onHashChange.bind(this),t(window,this._hasChangeHandler)};l.prototype._onHashChange=function(){return this._paused||this._route(this._extractFragment(window.location.href)),!0},l.prototype._extractFragment=function(t){var r=t.indexOf("#");return r>=0?t.substring(r):"#/"},l.prototype._throwsRouteError=function(t,r,e){return this._errors["_"+t]instanceof Function?this._errors["_"+t](r,e,t):this._errors._(r,e,t),!1},l.prototype._buildRequestObject=function(t,r,e,o){if(!t)throw new Error("Unable to compile request object");var n=new p(t);r&&(n.params=r);var i=t.split("?");if(2==i.length){var s=null,a=i[1].split("&");n.query={};for(var h=0,u=a.length;u>h;h++)s=a[h].split("="),n.query[decodeURI(s[0])]=decodeURI(s[1].replace(/\+/g,"%20"));n.query}return e&&e.length>0&&(n.splats=e),o===!0&&(n.hasNext=!0),n},l.prototype._followRoute=function(t,r,e){var o,n=e.splice(0,1),i=this._routes[n],s=r.match(i.path),a={},h=[];if(!i)return this._throwsRouteError(500,new Error("Internal error"),t);for(var u=0,c=i.paramNames.length;c>u;u++)a[i.paramNames[u]]=s[u+1];if(u+=1,s&&u<s.length)for(var p=u;p<s.length;p++)h.push(s[p]);var l=0!==e.length,f=function(r,e,o,n){return function(n,i,s){return n||i?i?this._throwsRouteError(s||500,i,t):void this._followRoute(r,e,o):this._throwsRouteError(500,'Cannot call "next" without an error if request.hasNext is false',t)}.bind(this,n)}.bind(this)(t,r,e,l);o=this._buildRequestObject(t,a,h,l),i.routeAction(o,f)},l.prototype._routeBefores=function(t,r,e,o,n){var i;if(t.length>0){var s=t.splice(0,1);s=s[0],i=function(r,i){return r?this._throwsRouteError(i||500,r,e):void this._routeBefores(t,s,e,o,n)}.bind(this)}else i=function(t,r){return t?this._throwsRouteError(r||500,t,e):void this._followRoute(e,o,n)}.bind(this);r(this._buildRequestObject(e,null,null,!0),i)},l.prototype._route=function(t){var r,e="",o=this._befores.slice(),n=[],i=t;if(0===i.length)return!0;i=i.replace(c,""),r=i.split("?")[0].replace(c,"");for(var s in this._routes)this._routes.hasOwnProperty(s)&&(e=this._routes[s],e.path.test(r)&&n.push(s));if(!(n.length>0))return this._throwsRouteError(404,null,t);if(o.length>0){var a=o.splice(0,1);a=a[0],this._routeBefores(o,a,t,i,n)}else this._followRoute(t,i,n)},l.prototype.back=function(){window.location.hash=""},l.prototype.pause=function(){return this._paused=!0,this},l.prototype.play=function(t){return t="undefined"==typeof t?!1:t,this._paused=!1,t&&this._route(this._extractFragment(window.location.href)),this},l.prototype.setLocation=function(t){return window.history.pushState(null,"",t),this},l.prototype.redirect=function(t){return this.setLocation(t),this._paused||this._route(this._extractFragment(t)),this},l.prototype.addRoute=l.prototype.add=l.prototype.route=l.prototype.get=function(t,r){var e,o=this._options.ignorecase?"i":"",p=[];if("string"==typeof t){for(t=t.replace(c,"");null!==(e=i.exec(t));)p.push(e[1]);t=new RegExp(t.replace(i,n).replace(s,a).replace(h,u)+"(?:\\?.+)?$",o)}return this._routes.push({path:t,paramNames:p,routeAction:r}),this},l.prototype.before=function(t){return this._befores.push(t),this},l.prototype.errors=function(t,r){if(isNaN(t))throw new Error("Invalid code for routes error handling");if(!(r instanceof Function))throw new Error("Invalid callback for routes error handling");return t="_"+t,this._errors[t]=r,this},l.prototype.run=function(t){return t||(t=this._extractFragment(window.location.href)),t=0===t.indexOf("#")?t:"#"+t,this.redirect(t),this},l.prototype.destroy=function(){return r(window,this._hasChangeHandler),this},e.exports=l}()});