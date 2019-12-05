define("common/ueditor/1.0.0/third-party/highcharts/modules/map.src",function(){!function(t){function e(t,e,o){for(var a=4,i=[];a--;)i[a]=Math.round(e.rgba[a]+(t.rgba[a]-e.rgba[a])*(1-o));return"rgba("+i.join(",")+")"}var o,a=t.Axis,i=t.Chart,n=t.Point,r=t.Pointer,l=t.each,s=t.extend,h=t.merge,p=t.pick,m=t.numberFormat,c=t.getOptions(),d=t.seriesTypes,u=c.plotOptions,x=t.wrap,g=t.Color,f=function(){};c.mapNavigation={buttonOptions:{align:"right",verticalAlign:"bottom",x:0,width:18,height:18,style:{fontSize:"15px",fontWeight:"bold",textAlign:"center"}},buttons:{zoomIn:{onclick:function(){this.mapZoom(.5)},text:"+",y:-32},zoomOut:{onclick:function(){this.mapZoom(2)},text:"-",y:0}}},t.splitPath=function(t){var e;for(t=t.replace(/([A-Za-z])/g," $1 "),t=t.replace(/^\s*/,"").replace(/\s*$/,""),t=t.split(/[ ,]+/),e=0;e<t.length;e++)/[a-zA-Z]/.test(t[e])||(t[e]=parseFloat(t[e]));return t},t.maps={},x(a.prototype,"getSeriesExtremes",function(t){var e,o,a=this.isXAxis,i=[];l(this.series,function(t,e){t.useMapGeometry&&(i[e]=t.xData,t.xData=[])}),t.call(this),e=p(this.dataMin,Number.MAX_VALUE),o=p(this.dataMax,Number.MIN_VALUE),l(this.series,function(t,n){t.useMapGeometry&&(e=Math.min(e,t[a?"minX":"minY"]),o=Math.max(o,t[a?"maxX":"maxY"]),t.xData=i[n])}),this.dataMin=e,this.dataMax=o}),x(a.prototype,"setAxisTranslation",function(t){var e,a,i,n=this.chart,r=n.plotWidth/n.plotHeight,l=this.isXAxis,s=n.xAxis[0];t.call(this),"map"!==n.options.chart.type||l||s.transA===o||(this.transA=s.transA=Math.min(this.transA,s.transA),e=(s.max-s.min)/(this.max-this.min),i=e>r?this:s,a=(i.max-i.min)*i.transA,i.minPixelPadding=(i.len-a)/2)}),x(i.prototype,"render",function(e){var o=this,a=o.options.mapNavigation;e.call(o),o.renderMapNavigation(),a.zoomOnDoubleClick&&t.addEvent(o.container,"dblclick",function(t){o.pointer.onContainerDblClick(t)}),a.zoomOnMouseWheel&&t.addEvent(o.container,void 0===document.onmousewheel?"DOMMouseScroll":"mousewheel",function(t){o.pointer.onContainerMouseWheel(t)})}),s(r.prototype,{onContainerDblClick:function(t){var e=this.chart;t=this.normalize(t),e.isInsidePlot(t.chartX-e.plotLeft,t.chartY-e.plotTop)&&e.mapZoom(.5,e.xAxis[0].toValue(t.chartX),e.yAxis[0].toValue(t.chartY))},onContainerMouseWheel:function(t){var e,o=this.chart;t=this.normalize(t),e=t.detail||-(t.wheelDelta/120),o.isInsidePlot(t.chartX-o.plotLeft,t.chartY-o.plotTop)&&o.mapZoom(e>0?2:.5,o.xAxis[0].toValue(t.chartX),o.yAxis[0].toValue(t.chartY))}}),x(r.prototype,"init",function(t,e,o){t.call(this,e,o),o.mapNavigation.enableTouchZoom&&(this.pinchX=this.pinchHor=this.pinchY=this.pinchVert=!0)}),s(i.prototype,{renderMapNavigation:function(){var t,e,o,a=this,i=this.options.mapNavigation,n=i.buttons,r=function(){this.handler.call(a)};if(i.enableButtons)for(t in n)n.hasOwnProperty(t)&&(o=h(i.buttonOptions,n[t]),e=a.renderer.button(o.text,0,0,r).attr({width:o.width,height:o.height}).css(o.style).add(),e.handler=o.onclick,e.align(s(o,{width:e.width,height:e.height}),null,"spacingBox"))},fitToBox:function(t,e){return l([["x","width"],["y","height"]],function(o){var a=o[0],i=o[1];t[a]+t[i]>e[a]+e[i]&&(t[i]>e[i]?(t[i]=e[i],t[a]=e[a]):t[a]=e[a]+e[i]-t[i]),t[i]>e[i]&&(t[i]=e[i]),t[a]<e[a]&&(t[a]=e[a])}),t},mapZoom:function(t,e,o){if(!this.isMapZooming){var a,i=this,n=i.xAxis[0],r=n.max-n.min,l=p(e,n.min+r/2),s=r*t,h=i.yAxis[0],m=h.max-h.min,c=p(o,h.min+m/2),d=m*t,u=l-s/2,x=c-d/2,g=p(i.options.chart.animation,!0),f=i.fitToBox({x:u,y:x,width:s,height:d},{x:n.dataMin,y:h.dataMin,width:n.dataMax-n.dataMin,height:h.dataMax-h.dataMin});n.setExtremes(f.x,f.x+f.width,!1),h.setExtremes(f.y,f.y+f.height,!1),a=g?g.duration||500:0,a&&(i.isMapZooming=!0,setTimeout(function(){i.isMapZooming=!1},a)),i.redraw()}}}),u.map=h(u.scatter,{animation:!1,nullColor:"#F8F8F8",borderColor:"silver",borderWidth:1,marker:null,stickyTracking:!1,dataLabels:{verticalAlign:"middle"},turboThreshold:0,tooltip:{followPointer:!0,pointFormat:"{point.name}: {point.y}<br/>"},states:{normal:{animation:!0}}});var y=t.extendClass(n,{applyOptions:function(e,o){var a=n.prototype.applyOptions.call(this,e,o);return a.path&&"string"==typeof a.path&&(a.path=a.options.path=t.splitPath(a.path)),a},onMouseOver:function(){clearTimeout(this.colorInterval),n.prototype.onMouseOver.call(this)},onMouseOut:function(){var t=this,o=+new Date,a=g(t.options.color),i=g(t.pointAttr.hover.fill),r=t.series.options.states.normal.animation,l=r&&(r.duration||500);l&&4===a.rgba.length&&4===i.rgba.length&&(delete t.pointAttr[""].fill,clearTimeout(t.colorInterval),t.colorInterval=setInterval(function(){var n=(new Date-o)/l,r=t.graphic;n>1&&(n=1),r&&r.attr("fill",e(i,a,n)),n>=1&&clearTimeout(t.colorInterval)},13)),n.prototype.onMouseOut.call(t)}});d.map=t.extendClass(d.scatter,{type:"map",pointAttrToOptions:{stroke:"borderColor","stroke-width":"borderWidth",fill:"color"},colorKey:"y",pointClass:y,trackerGroups:["group","markerGroup","dataLabelsGroup"],getSymbol:f,supportsDrilldown:!0,getExtremesFromAll:!0,useMapGeometry:!0,init:function(e){var a,i,n,r,s,h,p,c,u,x,g=this,f=e.options.legend.valueDecimals,y=[],b="horizontal"===e.options.legend.layout;t.Series.prototype.init.apply(this,arguments),h=g.options.colorRange,p=g.options.valueRanges,p?(l(p,function(e){i=e.from,n=e.to,a="",i===o?a="< ":n===o&&(a="> "),i!==o&&(a+=m(i,f)),i!==o&&n!==o&&(a+=" - "),n!==o&&(a+=m(n,f)),y.push(t.extend({chart:g.chart,name:a,options:{},drawLegendSymbol:d.area.prototype.drawLegendSymbol,visible:!0,setState:function(){},setVisible:function(){}},e))}),g.legendItems=y):h&&(i=h.from,n=h.to,r=h.fromLabel,s=h.toLabel,u=b?[0,0,1,0]:[0,1,0,0],b||(x=r,r=s,s=x),c={linearGradient:{x1:u[0],y1:u[1],x2:u[2],y2:u[3]},stops:[[0,i],[1,n]]},y=[{chart:g.chart,options:{},fromLabel:r,toLabel:s,color:c,drawLegendSymbol:this.drawLegendSymbolGradient,visible:!0,setState:function(){},setVisible:function(){}}],g.legendItems=y)},drawLegendSymbol:d.area.prototype.drawLegendSymbol,drawLegendSymbolGradient:function(t,e){var o,a,i,n,r,l=t.options.symbolPadding,s=p(t.options.padding,8),h=this.chart.renderer.fontMetrics(t.options.itemStyle.fontSize).h,m="horizontal"===t.options.layout,c=p(t.options.rectangleLength,200);m?(o=-(l/2),a=0):(o=-c+t.baseline-l/2,a=s+h),e.fromText=this.chart.renderer.text(e.fromLabel,a,o).attr({zIndex:2}).add(e.legendGroup),i=e.fromText.getBBox(),e.legendSymbol=this.chart.renderer.rect(m?i.x+i.width+l:i.x-h-l,i.y,m?c:h,m?h:c,2).attr({zIndex:1}).add(e.legendGroup),n=e.legendSymbol.getBBox(),e.toText=this.chart.renderer.text(e.toLabel,n.x+n.width+l,m?o:n.y+n.height-l).attr({zIndex:2}).add(e.legendGroup),r=e.toText.getBBox(),m?(t.offsetWidth=i.width+n.width+r.width+2*l+s,t.itemY=h+s):(t.offsetWidth=Math.max(i.width,r.width)+l+n.width+s,t.itemY=n.height+s,t.itemX=l)},getBox:function(t){var e=Number.MIN_VALUE,o=Number.MAX_VALUE,a=Number.MIN_VALUE,i=Number.MAX_VALUE;l(t||this.options.data,function(t){for(var n=t.path,r=n.length,l=!1,s=Number.MIN_VALUE,h=Number.MAX_VALUE,p=Number.MIN_VALUE,m=Number.MAX_VALUE;r--;)"number"!=typeof n[r]||isNaN(n[r])||(l?(s=Math.max(s,n[r]),h=Math.min(h,n[r])):(p=Math.max(p,n[r]),m=Math.min(m,n[r])),l=!l);t._maxX=s,t._minX=h,t._maxY=p,t._minY=m,e=Math.max(e,s),o=Math.min(o,h),a=Math.max(a,p),i=Math.min(i,m)}),this.minY=i,this.maxY=a,this.minX=o,this.maxX=e},translatePath:function(t){var e,o=this,a=!1,i=o.xAxis,n=o.yAxis;for(t=[].concat(t),e=t.length;e--;)"number"==typeof t[e]&&(t[e]=Math.round(a?i.translate(t[e]):n.len-n.translate(t[e])),a=!a);return t},setData:function(){t.Series.prototype.setData.apply(this,arguments),this.getBox()},translate:function(){var t=this,e=Number.MAX_VALUE,o=Number.MIN_VALUE;t.generatePoints(),l(t.data,function(a){a.shapeType="path",a.shapeArgs={d:t.translatePath(a.path)},"number"==typeof a.y&&(a.y>o?o=a.y:a.y<e&&(e=a.y))}),t.translateColors(e,o)},translateColors:function(t,a){var i,n,r=this.options,s=r.valueRanges,h=r.colorRange,p=this.colorKey;h&&(i=g(h.from),n=g(h.to)),l(this.data,function(l){var m,c,d,u,x=l[p];if(s){for(d=s.length;d--;)if(m=s[d],i=m.from,n=m.to,(i===o||x>=i)&&(n===o||n>=x)){c=m.color;break}}else h&&void 0!==x&&(u=1-(a-x)/(a-t),c=null===x?r.nullColor:e(i,n,u));c&&(l.color=null,l.options.color=c)})},drawGraph:f,drawDataLabels:f,drawPoints:function(){var e=this,o=e.xAxis,a=e.yAxis,i=e.colorKey;l(e.data,function(t){t.plotY=1,null===t[i]&&(t[i]=0,t.isNull=!0)}),d.column.prototype.drawPoints.apply(e),l(e.data,function(t){var e=t.dataLabels,n=o.toPixels(t._minX,!0),r=o.toPixels(t._maxX,!0),l=a.toPixels(t._minY,!0),s=a.toPixels(t._maxY,!0);t.plotX=Math.round(n+(r-n)*p(e&&e.anchorX,.5)),t.plotY=Math.round(l+(s-l)*p(e&&e.anchorY,.5)),t.isNull&&(t[i]=null)}),t.Series.prototype.drawDataLabels.call(e)},animateDrilldown:function(t){var e,o=this.chart.plotBox,a=this.chart.drilldownLevels[this.chart.drilldownLevels.length-1],i=a.bBox,n=this.chart.options.drilldown.animation;t||(e=Math.min(i.width/o.width,i.height/o.height),a.shapeArgs={scaleX:e,scaleY:e,translateX:i.x,translateY:i.y},l(this.points,function(t){t.graphic.attr(a.shapeArgs).animate({scaleX:1,scaleY:1,translateX:0,translateY:0},n)}),delete this.animate)},animateDrillupFrom:function(t){d.column.prototype.animateDrillupFrom.call(this,t)},animateDrillupTo:function(t){d.column.prototype.animateDrillupTo.call(this,t)}}),u.mapline=h(u.map,{lineWidth:1,backgroundColor:"none"}),d.mapline=t.extendClass(d.map,{type:"mapline",pointAttrToOptions:{stroke:"color","stroke-width":"lineWidth",fill:"backgroundColor"},drawLegendSymbol:d.line.prototype.drawLegendSymbol}),u.mappoint=h(u.scatter,{dataLabels:{enabled:!0,format:"{point.name}",color:"black",style:{textShadow:"0 0 5px white"}}}),d.mappoint=t.extendClass(d.scatter,{type:"mappoint"}),t.Map=function(e,o){var a,i={endOnTick:!1,gridLineWidth:0,labels:{enabled:!1},lineWidth:0,minPadding:0,maxPadding:0,startOnTick:!1,tickWidth:0,title:null};return a=e.series,e.series=null,e=h({chart:{type:"map",panning:"xy"},xAxis:i,yAxis:h(i,{reversed:!0})},e,{chart:{inverted:!1}}),e.series=a,new t.Chart(e,o)}}(Highcharts)});