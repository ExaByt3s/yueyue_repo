define("utility/iscroll/5.1.1/iscroll-lite",[],function(a,b,c){!function(a,b,d){function g(a,c){this.wrapper="string"==typeof a?b.querySelector(a):a,this.scroller=this.wrapper.children[0],this.scrollerStyle=this.scroller.style,this.options={startX:0,startY:0,scrollY:!0,directionLockThreshold:5,momentum:!0,bounce:!0,bounceTime:600,bounceEasing:"",preventDefault:!0,preventDefaultException:{tagName:/^(INPUT|TEXTAREA|BUTTON|SELECT)$/},HWCompositing:!0,useTransition:!0,useTransform:!0};for(var d in c)this.options[d]=c[d];this.translateZ=this.options.HWCompositing&&f.hasPerspective?" translateZ(0)":"",this.options.useTransition=f.hasTransition&&this.options.useTransition,this.options.useTransform=f.hasTransform&&this.options.useTransform,this.options.eventPassthrough=this.options.eventPassthrough===!0?"vertical":this.options.eventPassthrough,this.options.preventDefault=!this.options.eventPassthrough&&this.options.preventDefault,this.options.scrollY="vertical"==this.options.eventPassthrough?!1:this.options.scrollY,this.options.scrollX="horizontal"==this.options.eventPassthrough?!1:this.options.scrollX,this.options.freeScroll=this.options.freeScroll&&!this.options.eventPassthrough,this.options.directionLockThreshold=this.options.eventPassthrough?0:this.options.directionLockThreshold,this.options.bounceEasing="string"==typeof this.options.bounceEasing?f.ease[this.options.bounceEasing]||f.ease.circular:this.options.bounceEasing,this.options.resizePolling=void 0===this.options.resizePolling?60:this.options.resizePolling,this.options.tap===!0&&(this.options.tap="tap"),this.x=0,this.y=0,this.directionX=0,this.directionY=0,this._events={},this._init(),this.refresh(),this.scrollTo(this.options.startX,this.options.startY),this.enable()}var e=a.requestAnimationFrame||a.webkitRequestAnimationFrame||a.mozRequestAnimationFrame||a.oRequestAnimationFrame||a.msRequestAnimationFrame||function(b){a.setTimeout(b,1e3/60)},f=function(){function g(a){return f===!1?!1:""===f?a:f+a.charAt(0).toUpperCase()+a.substr(1)}var c={},e=b.createElement("div").style,f=function(){for(var b,a=["t","webkitT","MozT","msT","OT"],c=0,d=a.length;d>c;c++)if(b=a[c]+"ransform",b in e)return a[c].substr(0,a[c].length-1);return!1}();c.getTime=Date.now||function(){return(new Date).getTime()},c.extend=function(a,b){for(var c in b)a[c]=b[c]},c.addEvent=function(a,b,c,d){a.addEventListener(b,c,!!d)},c.removeEvent=function(a,b,c,d){a.removeEventListener(b,c,!!d)},c.momentum=function(a,b,c,e,f,g){var j,k,h=a-b,i=d.abs(h)/c;return g=void 0===g?6e-4:g,j=a+i*i/(2*g)*(0>h?-1:1),k=i/g,e>j?(j=f?e-f/2.5*(i/8):e,h=d.abs(j-a),k=h/i):j>0&&(j=f?f/2.5*(i/8):0,h=d.abs(a)+j,k=h/i),{destination:d.round(j),duration:k}};var h=g("transform");return c.extend(c,{hasTransform:h!==!1,hasPerspective:g("perspective")in e,hasTouch:"ontouchstart"in a,hasPointer:navigator.msPointerEnabled,hasTransition:g("transition")in e}),c.isBadAndroid=/Android /.test(a.navigator.appVersion)&&!/Chrome\/\d/.test(a.navigator.appVersion),c.extend(c.style={},{transform:h,transitionTimingFunction:g("transitionTimingFunction"),transitionDuration:g("transitionDuration"),transitionDelay:g("transitionDelay"),transformOrigin:g("transformOrigin")}),c.hasClass=function(a,b){var c=new RegExp("(^|\\s)"+b+"(\\s|$)");return c.test(a.className)},c.addClass=function(a,b){if(!c.hasClass(a,b)){var d=a.className.split(" ");d.push(b),a.className=d.join(" ")}},c.removeClass=function(a,b){if(c.hasClass(a,b)){var d=new RegExp("(^|\\s)"+b+"(\\s|$)","g");a.className=a.className.replace(d," ")}},c.offset=function(a){for(var b=-a.offsetLeft,c=-a.offsetTop;a=a.offsetParent;)b-=a.offsetLeft,c-=a.offsetTop;return{left:b,top:c}},c.preventDefaultException=function(a,b){for(var c in b)if(b[c].test(a[c]))return!0;return!1},c.extend(c.eventType={},{touchstart:1,touchmove:1,touchend:1,mousedown:2,mousemove:2,mouseup:2,MSPointerDown:3,MSPointerMove:3,MSPointerUp:3}),c.extend(c.ease={},{quadratic:{style:"cubic-bezier(0.25, 0.46, 0.45, 0.94)",fn:function(a){return a*(2-a)}},circular:{style:"cubic-bezier(0.1, 0.57, 0.1, 1)",fn:function(a){return d.sqrt(1- --a*a)}},back:{style:"cubic-bezier(0.175, 0.885, 0.32, 1.275)",fn:function(a){var b=4;return(a-=1)*a*((b+1)*a+b)+1}},bounce:{style:"",fn:function(a){return(a/=1)<1/2.75?7.5625*a*a:2/2.75>a?7.5625*(a-=1.5/2.75)*a+.75:2.5/2.75>a?7.5625*(a-=2.25/2.75)*a+.9375:7.5625*(a-=2.625/2.75)*a+.984375}},elastic:{style:"",fn:function(a){var b=.22,c=.4;return 0===a?0:1==a?1:c*d.pow(2,-10*a)*d.sin((a-b/4)*2*d.PI/b)+1}}}),c.tap=function(a,c){var d=b.createEvent("Event");d.initEvent(c,!0,!0),d.pageX=a.pageX,d.pageY=a.pageY,a.target.dispatchEvent(d)},c.click=function(a){var d,c=a.target;/(SELECT|INPUT|TEXTAREA)/i.test(c.tagName)||(d=b.createEvent("MouseEvents"),d.initMouseEvent("click",!0,!0,a.view,1,c.screenX,c.screenY,c.clientX,c.clientY,a.ctrlKey,a.altKey,a.shiftKey,a.metaKey,0,null),d._constructed=!0,c.dispatchEvent(d))},c}();g.prototype={version:"5.1.1",_init:function(){this._initEvents()},destroy:function(){this._initEvents(!0),this._execEvent("destroy")},_transitionEnd:function(a){a.target==this.scroller&&this.isInTransition&&(this._transitionTime(),this.resetPosition(this.options.bounceTime)||(this.isInTransition=!1,this._execEvent("scrollEnd")))},_start:function(a){if(!(1!=f.eventType[a.type]&&0!==a.button||!this.enabled||this.initiated&&f.eventType[a.type]!==this.initiated)){!this.options.preventDefault||f.isBadAndroid||f.preventDefaultException(a.target,this.options.preventDefaultException)||a.preventDefault();var c,b=a.touches?a.touches[0]:a;this.initiated=f.eventType[a.type],this.moved=!1,this.distX=0,this.distY=0,this.directionX=0,this.directionY=0,this.directionLocked=0,this._transitionTime(),this.startTime=f.getTime(),this.options.useTransition&&this.isInTransition?(this.isInTransition=!1,c=this.getComputedPosition(),this._translate(d.round(c.x),d.round(c.y)),this._execEvent("scrollEnd")):!this.options.useTransition&&this.isAnimating&&(this.isAnimating=!1,this._execEvent("scrollEnd")),this.startX=this.x,this.startY=this.y,this.absStartX=this.x,this.absStartY=this.y,this.pointX=b.pageX,this.pointY=b.pageY,this._execEvent("beforeScrollStart")}},_move:function(a){if(this.enabled&&f.eventType[a.type]===this.initiated){this.options.preventDefault&&a.preventDefault();var h,i,j,k,b=a.touches?a.touches[0]:a,c=b.pageX-this.pointX,e=b.pageY-this.pointY,g=f.getTime();if(this.pointX=b.pageX,this.pointY=b.pageY,this.distX+=c,this.distY+=e,j=d.abs(this.distX),k=d.abs(this.distY),!(g-this.endTime>300&&10>j&&10>k)){if(this.directionLocked||this.options.freeScroll||(this.directionLocked=j>k+this.options.directionLockThreshold?"h":k>=j+this.options.directionLockThreshold?"v":"n"),"h"==this.directionLocked){if("vertical"==this.options.eventPassthrough)a.preventDefault();else if("horizontal"==this.options.eventPassthrough)return this.initiated=!1,void 0;e=0}else if("v"==this.directionLocked){if("horizontal"==this.options.eventPassthrough)a.preventDefault();else if("vertical"==this.options.eventPassthrough)return this.initiated=!1,void 0;c=0}c=this.hasHorizontalScroll?c:0,e=this.hasVerticalScroll?e:0,h=this.x+c,i=this.y+e,(h>0||h<this.maxScrollX)&&(h=this.options.bounce?this.x+c/3:h>0?0:this.maxScrollX),(i>0||i<this.maxScrollY)&&(i=this.options.bounce?this.y+e/3:i>0?0:this.maxScrollY),this.directionX=c>0?-1:0>c?1:0,this.directionY=e>0?-1:0>e?1:0,this.moved||this._execEvent("scrollStart"),this.moved=!0,this._translate(h,i),g-this.startTime>300&&(this.startTime=g,this.startX=this.x,this.startY=this.y)}}},_end:function(a){if(this.enabled&&f.eventType[a.type]===this.initiated){this.options.preventDefault&&!f.preventDefaultException(a.target,this.options.preventDefaultException)&&a.preventDefault();var c,e,g=(a.changedTouches?a.changedTouches[0]:a,f.getTime()-this.startTime),h=d.round(this.x),i=d.round(this.y),j=d.abs(h-this.startX),k=d.abs(i-this.startY),l=0,m="";if(this.isInTransition=0,this.initiated=0,this.endTime=f.getTime(),!this.resetPosition(this.options.bounceTime))return this.scrollTo(h,i),this.moved?this._events.flick&&200>g&&100>j&&100>k?(this._execEvent("flick"),void 0):(this.options.momentum&&300>g&&(c=this.hasHorizontalScroll?f.momentum(this.x,this.startX,g,this.maxScrollX,this.options.bounce?this.wrapperWidth:0,this.options.deceleration):{destination:h,duration:0},e=this.hasVerticalScroll?f.momentum(this.y,this.startY,g,this.maxScrollY,this.options.bounce?this.wrapperHeight:0,this.options.deceleration):{destination:i,duration:0},h=c.destination,i=e.destination,l=d.max(c.duration,e.duration),this.isInTransition=1),h!=this.x||i!=this.y?((h>0||h<this.maxScrollX||i>0||i<this.maxScrollY)&&(m=f.ease.quadratic),this.scrollTo(h,i,l,m),void 0):(this._execEvent("scrollEnd"),void 0)):(this.options.tap&&f.tap(a,this.options.tap),this.options.click&&f.click(a),this._execEvent("scrollCancel"),void 0)}},_resize:function(){var a=this;clearTimeout(this.resizeTimeout),this.resizeTimeout=setTimeout(function(){a.refresh()},this.options.resizePolling)},resetPosition:function(a){var b=this.x,c=this.y;return a=a||0,!this.hasHorizontalScroll||this.x>0?b=0:this.x<this.maxScrollX&&(b=this.maxScrollX),!this.hasVerticalScroll||this.y>0?c=0:this.y<this.maxScrollY&&(c=this.maxScrollY),b==this.x&&c==this.y?!1:(this.scrollTo(b,c,a,this.options.bounceEasing),!0)},disable:function(){this.enabled=!1},enable:function(){this.enabled=!0},refresh:function(){this.wrapper.offsetHeight,this.wrapperWidth=this.wrapper.clientWidth,this.wrapperHeight=this.wrapper.clientHeight,this.scrollerWidth=this.scroller.offsetWidth,this.scrollerHeight=this.scroller.offsetHeight,this.maxScrollX=this.wrapperWidth-this.scrollerWidth,this.maxScrollY=this.wrapperHeight-this.scrollerHeight,this.hasHorizontalScroll=this.options.scrollX&&this.maxScrollX<0,this.hasVerticalScroll=this.options.scrollY&&this.maxScrollY<0,this.hasHorizontalScroll||(this.maxScrollX=0,this.scrollerWidth=this.wrapperWidth),this.hasVerticalScroll||(this.maxScrollY=0,this.scrollerHeight=this.wrapperHeight),this.endTime=0,this.directionX=0,this.directionY=0,this.wrapperOffset=f.offset(this.wrapper),this._execEvent("refresh"),this.resetPosition()},on:function(a,b){this._events[a]||(this._events[a]=[]),this._events[a].push(b)},off:function(a,b){if(this._events[a]){var c=this._events[a].indexOf(b);c>-1&&this._events[a].splice(c,1)}},_execEvent:function(a){if(this._events[a]){var b=0,c=this._events[a].length;if(c)for(;c>b;b++)this._events[a][b].apply(this,[].slice.call(arguments,1))}},scrollBy:function(a,b,c,d){a=this.x+a,b=this.y+b,c=c||0,this.scrollTo(a,b,c,d)},scrollTo:function(a,b,c,d){d=d||f.ease.circular,this.isInTransition=this.options.useTransition&&c>0,!c||this.options.useTransition&&d.style?(this._transitionTimingFunction(d.style),this._transitionTime(c),this._translate(a,b)):this._animate(a,b,c,d.fn)},scrollToElement:function(a,b,c,e,g){if(a=a.nodeType?a:this.scroller.querySelector(a)){var h=f.offset(a);h.left-=this.wrapperOffset.left,h.top-=this.wrapperOffset.top,c===!0&&(c=d.round(a.offsetWidth/2-this.wrapper.offsetWidth/2)),e===!0&&(e=d.round(a.offsetHeight/2-this.wrapper.offsetHeight/2)),h.left-=c||0,h.top-=e||0,h.left=h.left>0?0:h.left<this.maxScrollX?this.maxScrollX:h.left,h.top=h.top>0?0:h.top<this.maxScrollY?this.maxScrollY:h.top,b=void 0===b||null===b||"auto"===b?d.max(d.abs(this.x-h.left),d.abs(this.y-h.top)):b,this.scrollTo(h.left,h.top,b,g)}},_transitionTime:function(a){a=a||0,this.scrollerStyle[f.style.transitionDuration]=a+"ms",!a&&f.isBadAndroid&&(this.scrollerStyle[f.style.transitionDuration]="0.001s")},_transitionTimingFunction:function(a){this.scrollerStyle[f.style.transitionTimingFunction]=a},_translate:function(a,b){this.options.useTransform?this.scrollerStyle[f.style.transform]="translate("+a+"px,"+b+"px)"+this.translateZ:(a=d.round(a),b=d.round(b),this.scrollerStyle.left=a+"px",this.scrollerStyle.top=b+"px"),this.x=a,this.y=b},_initEvents:function(b){var c=b?f.removeEvent:f.addEvent,d=this.options.bindToWrapper?this.wrapper:a;c(a,"orientationchange",this),c(a,"resize",this),this.options.click&&c(this.wrapper,"click",this,!0),this.options.disableMouse||(c(this.wrapper,"mousedown",this),c(d,"mousemove",this),c(d,"mousecancel",this),c(d,"mouseup",this)),f.hasPointer&&!this.options.disablePointer&&(c(this.wrapper,"MSPointerDown",this),c(d,"MSPointerMove",this),c(d,"MSPointerCancel",this),c(d,"MSPointerUp",this)),f.hasTouch&&!this.options.disableTouch&&(c(this.wrapper,"touchstart",this),c(d,"touchmove",this),c(d,"touchcancel",this),c(d,"touchend",this)),c(this.scroller,"transitionend",this),c(this.scroller,"webkitTransitionEnd",this),c(this.scroller,"oTransitionEnd",this),c(this.scroller,"MSTransitionEnd",this)},getComputedPosition:function(){var c,d,b=a.getComputedStyle(this.scroller,null);return this.options.useTransform?(b=b[f.style.transform].split(")")[0].split(", "),c=+(b[12]||b[4]),d=+(b[13]||b[5])):(c=+b.left.replace(/[^-\d.]/g,""),d=+b.top.replace(/[^-\d.]/g,"")),{x:c,y:d}},_animate:function(a,b,c,d){function l(){var n,o,p,m=f.getTime();return m>=k?(g.isAnimating=!1,g._translate(a,b),g.resetPosition(g.options.bounceTime)||g._execEvent("scrollEnd"),void 0):(m=(m-j)/c,p=d(m),n=(a-h)*p+h,o=(b-i)*p+i,g._translate(n,o),g.isAnimating&&e(l),void 0)}var g=this,h=this.x,i=this.y,j=f.getTime(),k=j+c;this.isAnimating=!0,l()},handleEvent:function(a){switch(a.type){case"touchstart":case"MSPointerDown":case"mousedown":this._start(a);break;case"touchmove":case"MSPointerMove":case"mousemove":this._move(a);break;case"touchend":case"MSPointerUp":case"mouseup":case"touchcancel":case"MSPointerCancel":case"mousecancel":this._end(a);break;case"orientationchange":case"resize":this._resize();break;case"transitionend":case"webkitTransitionEnd":case"oTransitionEnd":case"MSTransitionEnd":this._transitionEnd(a);break;case"wheel":case"DOMMouseScroll":case"mousewheel":this._wheel(a);break;case"keydown":this._key(a);break;case"click":a._constructed||(a.preventDefault(),a.stopPropagation())}}},g.utils=f,"undefined"!=typeof c&&c.exports?c.exports=g:a.IScroll=g}(window,document,Math)});