define("common/widget/selectlocal/selectlocal",function(e){function t(e,t,c){var n=this;n.firstSelect=e,n.secondSelect=t,n.thirdSelect=c,n.secondTips="",n.thirdTips="",n.firstSelect instanceof i||(n.firstSelect=i(n.firstSelect)),n.secondSelect instanceof i||(n.secondSelect=i(n.secondSelect)),n.thirdSelect&&(n.thirdSelect instanceof i||(n.thirdSelect=i(n.thirdSelect)),n.thirdTips=n.thirdSelect.html()),n.secondTips=n.secondSelect.html(),n.init()}var i=e("components/jquery/jquery.js");return i.extend(t.prototype,{init:function(){var e=this;e._bind()},_bind:function(){var e=this;e.firstSelect.change(function(){e._showNextLevel(e.firstSelect,e.secondSelect,e.secondTips),e.thirdSelect&&e.thirdSelect.html(e.thirdTips)}),e.thirdSelect&&e.secondSelect.change(function(){e._showNextLevel(e.secondSelect,e.thirdSelect,e.thirdTips)})},_showNextLevel:function(e,t,c){var n,l,s,o,d,r,h,f,S=i(e[0].options[e[0].selectedIndex]).attr("data-types"),p=c;if("string"==typeof S){if(n=S.split(","),""===n[0]&&n.shift(),n.length>1)for(d=0,r=n.length;r>d;d++){if(l=n[d].split("|"),s="",l.length>2)for(o=l[2].split("-"),""===o[0]&&o.shift(),h=0,f=o.length;f>h;h++)s+=","+o[h]+"|"+o[++h];p+='<option value="'+l[0]+'" data-types="'+s+'">'+l[1]+"</option>"}else p='<option value="">-��-</option>';t.html(p)}}}),t});