define("common/valid/index",function(require,exports,module){function ValueValid(e){this.containNode=e,this.validNodes=$(this.containNode).find("[valid-rule]"),this.checkList=[],this.checkListNull=[],this.checkListPass=[],this.txs=function(e,t){return ValueValid.prototype.valid(e,t)}}return ValueValid.prototype.freshNodes=function(){this.validNodes=$(this.containNode).find("[valid-rule]")},ValueValid.prototype.checkValid=function(){function e(e){var t=$(e[0].node);t.attr("valid-errorAlert")&&""!=$.trim(t.attr("valid-errorAlert"))?alert(t.attr("valid-errorAlert")):t.trigger("validError")}this.checkList=[],this.checkListNull=[],this.checkListPass=[];for(var t=0;t<this.validNodes.length;t++){var a=ValueValid.prototype.result(this.validNodes[t]);this.checkList.push({node:this.validNodes[t],status:a}),a?this.checkListPass.push({node:this.validNodes[t],status:a}):this.checkListNull.push({node:this.validNodes[t],status:a})}return this.checkListNull.length>0&&e(this.checkListNull),{checkList:this.checkList,checkListNull:this.checkListNull,checkListPass:this.checkListPass,isPass:0==this.checkListNull.length?!0:!1}},ValueValid.prototype.result=function(e){function t(){i.attr("valid-emptyAlert")&&""!=$.trim(i.attr("valid-emptyAlert"))?alert(i.attr("valid-emptyAlert")):i.trigger("onEmpty")}function a(){throw"no expression"}var i=$(e);return""==$.trim(i.val())&&t(),i.attr("valid-rule")||""!=$.trim(i.attr("valid-rule"))?ValueValid.prototype.valid($.trim(i.val()),$.trim(i.attr("valid-rule"))):void a()},ValueValid.prototype.pushfunctions=function(e){function t(e,t){t.ValidClick&&e.on("click",t.ValidClick),t.ValidBlur&&e.on("blur",t.ValidBlur),t.ValidEmpty&&e.on("onEmpty",t.ValidEmpty),t.ValidError&&e.on("validError",t.ValidError)}for(var a=0;a<this.validNodes.length;a++)for(var i=$(this.validNodes[a]),r=parseInt(i.attr("valid-index")),s=0;s<e.length;s++)r===parseInt(e[s].index)&&t(i,e[s])},ValueValid.prototype.valid=function(val,exp){var type=exp.slice(0,2),min=parseInt(exp.slice(2,exp.indexOf("-"))),max=parseInt(exp.slice(exp.indexOf("-")+1,exp.length));if(isNaN(min)||isNaN(max))throw"valid-rule error";var reg_str;switch(type){case"en":reg_str=eval("/^[a-z]{"+min+","+max+"}$/i");break;case"zh":reg_str=eval("/^[һ-��]{"+min+","+max+"}$/gm");break;case"nb":reg_str=eval("/^\\d{"+min+","+max+"}$/");break;case"!z":reg_str=eval("/^[1-9]\\d{"+(min-1)+","+(max-1)+"}$/");break;case"**":reg_str=eval("/^[\\s|\\S]{"+min+","+max+"}$/");break;case"pw":reg_str=eval("/^\\w{"+min+","+max+"}$/");break;case"ph":reg_str=eval("/^\\d{"+min+","+max+"}$/");break;case"ma":reg_str=eval("/\\w+([-+.]\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*/");break;case"ur":break;case"id":reg_str=eval("/^([0-9]{17}[0-9xX]{1})|([0-9]{15})$/");break;case"fl":reg_str=eval("/^[0-9]{"+min+","+max+"}[\\.]\\d{1,2}$/");break;case"pn":reg_str=/[.]{1}/,reg_str=eval(reg_str.exec(val)?"/^\\d{"+min+","+max+"}[\\.]\\d{1,2}$/":"/^\\d{"+min+","+max+"}$/");break;case"an":reg_str=/[.]{1}/,reg_str=eval(reg_str.exec(val)?"/^\\d{"+min+","+max+"}[\\.]\\d{1,2}$/":"/^[1-9]\\d{"+(min-1)+","+(max-1)+"}$/");break;case"!s":reg_str=eval("/^[^.~!@#$%\\^\\+\\*&\\/?\\|:\\.{}()';=\"]{"+min+","+max+"}$/")}return reg_str.exec(val)},ValueValid});