define("common/widget/footer/footer",function(e){"use strict";function n(e){var n=this;n.options=e||{},n.$render_ele=e.ele||{},n.content=e.content||{},n.init()}e("components/zepto/zepto.js");return n.prototype={init:function(){var e=this;e.render(),e.setup_event()},render:function(){var e=this,n=Handlebars.template(function(e,n,t,i,o){this.compilerInfo=[4,">= 1.0.0"],t=this.merge(t,e.helpers),o=o||{};var a,l,r="",s="function",c=this.escapeExpression;return r+='<footer class="footer-v2">\n    <ul class="list clearfix f14 ">\n        <a href="',(l=t.index_link)?a=l.call(n,{hash:{},data:o}):(l=n&&n.index_link,a=typeof l===s?l.call(n,{hash:{},data:o}):l),r+=c(a)+'"><li><i class="icon icon-index"></i>��ҳ</li></a>\n        <a href="',(l=t.my_link)?a=l.call(n,{hash:{},data:o}):(l=n&&n.my_link,a=typeof l===s?l.call(n,{hash:{},data:o}):l),r+=c(a)+'"><li><i class="icon icon-my"></i>�ҵ�</li></a>\n    </ul>        \n</footer>'});e.view=e.$render_ele.html(n(e.content))},setup_event:function(){}},n});