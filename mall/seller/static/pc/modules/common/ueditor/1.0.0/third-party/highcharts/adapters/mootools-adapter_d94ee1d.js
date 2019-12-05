define("common/ueditor/1.0.0/third-party/highcharts/adapters/mootools-adapter",function(){!function(){var t=window,e=document,n=t.MooTools.version.substring(0,3),r="1.2"===n||"1.1"===n,a=r||"1.3"===n,o=t.$extend||function(){return Object.append.apply(Object,arguments)};t.HighchartsAdapter={init:function(t){var e=Fx.prototype,n=e.start,r=Fx.Morph.prototype,a=r.compute;e.start=function(e){var r=this.element;return e.d&&(this.paths=t.init(r,r.d,this.toD)),n.apply(this,arguments),this},r.compute=function(e,n,r){var o=this.paths;return o?void this.element.attr("d",t.step(o[0],o[1],r,this.toD)):a.apply(this,arguments)}},adapterRun:function(t,e){return"width"===e||"height"===e?parseInt($(t).getStyle(e),10):void 0},getScript:function(t,n){var r=e.getElementsByTagName("head")[0],a=e.createElement("script");a.type="text/javascript",a.src=t,a.onload=n,r.appendChild(a)},animate:function(e,n,r){var a=e.attr,i=r&&r.complete;a&&!e.setStyle&&(e.getStyle=e.attr,e.setStyle=function(){var t=arguments;this.attr.call(this,t[0],t[1][0])},e.$family=function(){return!0}),t.HighchartsAdapter.stop(e),r=new Fx.Morph(a?e:$(e),o({transition:Fx.Transitions.Quad.easeInOut},r)),a&&(r.element=e),n.d&&(r.toD=n.d),i&&r.addEvent("complete",i),r.start(n),e.fx=r},each:function(t,e){return r?$each(t,e):Array.each(t,e)},map:function(t,e){return t.map(e)},grep:function(t,e){return t.filter(e)},inArray:function(t,e,n){return e?e.indexOf(t,n):-1},offset:function(t){return t=t.getPosition(),{left:t.x,top:t.y}},extendWithEvents:function(t){t.addEvent||(t.nodeName?$(t):o(t,new Events))},addEvent:function(e,n,r){"string"==typeof n&&("unload"===n&&(n="beforeunload"),t.HighchartsAdapter.extendWithEvents(e),e.addEvent(n,r))},removeEvent:function(t,e,n){"string"!=typeof t&&t.addEvent&&(e?("unload"===e&&(e="beforeunload"),n?t.removeEvent(e,n):t.removeEvents&&t.removeEvents(e)):t.removeEvents())},fireEvent:function(t,e,n,r){e={type:e,target:t},e=a?new Event(e):new DOMEvent(e),e=o(e,n),!e.target&&e.event&&(e.target=e.event.target),e.preventDefault=function(){r=null},t.fireEvent&&t.fireEvent(e.type,e),r&&r(e)},washMouseEvent:function(t){return t.page&&(t.pageX=t.page.x,t.pageY=t.page.y),t},stop:function(t){t.fx&&t.fx.cancel()}}}()});