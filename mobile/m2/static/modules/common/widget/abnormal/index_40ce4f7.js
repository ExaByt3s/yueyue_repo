define("common/widget/abnormal/index",function(n,r,e){var o=n("components/zepto/zepto.js");e.exports={render:function(n,r){r=r||{};var e=Handlebars.template(function(n,r,e,o,t){function a(){return'data-role="refresh-page"'}function i(){return"stream-network-error"}function s(){return"stream-empty"}function c(){return"icon-stream-network-error"}function f(){return"icon-stream-empty"}function p(){return"��ǰ���粻����"}function l(){return"��������"}function m(){return"<p>����������ᴥ��Ļ���¼���</p>"}this.compilerInfo=[4,">= 1.0.0"],e=this.merge(e,n.helpers),t=t||{};var d,u="",h=this;return u+='<div style="padding-top: 50%;">\n    <div ',d=e["if"].call(r,r&&r.broken_network,{hash:{},inverse:h.noop,fn:h.program(1,a,t),data:t}),(d||0===d)&&(u+=d),u+=' class="stream-abnormal ',d=e["if"].call(r,r&&r.broken_network,{hash:{},inverse:h.program(5,s,t),fn:h.program(3,i,t),data:t}),(d||0===d)&&(u+=d),u+='" data-role="tap-screen" >\n        <i class="icon ',d=e["if"].call(r,r&&r.broken_network,{hash:{},inverse:h.program(9,f,t),fn:h.program(7,c,t),data:t}),(d||0===d)&&(u+=d),u+='"></i>\n        <h4 >',d=e["if"].call(r,r&&r.broken_network,{hash:{},inverse:h.program(13,l,t),fn:h.program(11,p,t),data:t}),(d||0===d)&&(u+=d),u+="</h4>\n        ",d=e["if"].call(r,r&&r.broken_network,{hash:{},inverse:h.noop,fn:h.program(15,m,t),data:t}),(d||0===d)&&(u+=d),u+="\n    </div>\n</div>"});n.innerHTML=e(r),o(n).find('[data-role="refresh-page"]').on("click",function(){window.location.href=window.location.href})}}});