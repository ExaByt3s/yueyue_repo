define("utility/hammer/plugins/jquery.hammer/2.0.1/jquery.hammer",["$","utility/hammer/2.0.1/hammer"],function(e){var t=e("$"),r=e("utility/hammer/2.0.1/hammer");!function(e,t,r){function n(n,a){var i=e(n);i.data(r)||i.data(r,new t(i[0],a))}e.fn.hammer=function(e){return this.each(function(){n(this,e)})},t.Manager.prototype.emit=function(t){return function(r,n){t.call(this,r,n),e(this.element).triggerHandler({type:r,gesture:n})}}(t.Manager.prototype.emit)}(t,r,"hammer")});