function ValueValid(contain){
    //接收一个容器对象
    this.containNode = contain;
    this.validNodes = $(this.containNode).find('[valid-rule]');
    this.checkList = [];
    this.checkListNull = [];
    this.checkListPass = [];
    
    this.txs = function(val,exp){
    	return ValueValid.prototype.valid(val,exp)
    }

}

ValueValid.prototype.freshNodes = function(){
    this.validNodes = $(this.containNode).find('[valid-rule]');
}

ValueValid.prototype.checkValid = function(){
	this.checkList = [];
	this.checkListNull = [];
	this.checkListPass = [];
    for(var i = 0; i < this.validNodes.length; i++){
    	var status = ValueValid.prototype.result(this.validNodes[i]);
    	this.checkList.push({node:this.validNodes[i],status:status});
    	if(!status){
    		this.checkListNull.push({node:this.validNodes[i],status:status})
    	}
    	else{
    		this.checkListPass.push({node:this.validNodes[i],status:status})
    	}
    }
    afterCheck(this.checkListNull);
    
    function afterCheck(list){
    	var node = $(list[0].node)
    	if(node.attr('valid-errorAlert') && node.attr('valid-errorAlert').trim() != ""){
    		alert(node.attr('valid-errorAlert'))
    	}
    	else{
    		node.trigger('validError');
    	}
    }

    return {
    	checkList : this.checkList,
		checkListNull :this.checkListNull,
		checkListPass : this.checkListPass
    }
}

ValueValid.prototype.result = function(node){
	var _node = $(node);

	if(_node.val().trim() == ''){
		empty();
	}//无输入
	if(!_node.attr('valid-rule') && (_node.attr('valid-rule').trim() == '')){
		//无表达式
		noExpect();
	}else{
		//有表达式
		return ValueValid.prototype.valid(_node.val().trim(),_node.attr('valid-rule').trim());
	}

	function empty(){
		if(_node.attr('valid-emptyAlert') && _node.attr('valid-emptyAlert').trim() != ""){
			alert(_node.attr('valid-emptyAlert'))
		}
		else{
			_node.trigger('onEmpty');
		}	
	}

	function noExpect(){
		throw 'no expression';
	}
}

ValueValid.prototype.pushfunctions = function(Obj){
	//时间绑定 处理对象
	for(var i = 0; i < this.validNodes.length; i++){	
		var node = $(this.validNodes[i]);
		var index_f = parseInt(node.attr('valid-index'));
			for(var k = 0; k < Obj.length; k++){
				if(index_f === parseInt(Obj[k].index)){
					binded(node,Obj[k]);
				}
			}
	}

	function binded(_node,data){
		//事件绑定
		data.ValidClick && _node.on('click',data.ValidClick);
		data.ValidBlur && _node.on('blur',data.ValidBlur);
		data.ValidEmpty && _node.on('onEmpty',data.ValidEmpty);
		data.ValidError && _node.on('validError',data.ValidError);
	}
}

ValueValid.prototype.valid = function(val,exp){
	//校验
	var type = exp.slice(0,2);
	var min = parseInt(exp.slice(2,exp.indexOf('-')));
	var max = parseInt(exp.slice(exp.indexOf('-')+1,exp.length));
	
	if(isNaN(min) || isNaN(max))throw 'valid-rule error';

	var reg_str;
	
	switch (type)
	{
		case 'en' : reg_str = eval("/^[a-z]{"+ min + "," + max + "}$/i");break;
		case 'zh' : reg_str = eval("/[\u4e00-\u9fa5]{"+ min + "," + max + "}/gm");break;
		case 'nb' : reg_str = eval("/^\\d{"+ min + "," + max + "}$/");break;
		case '**' : reg_str = eval("/^[\\s|\\S]{"+ min + "," + max + "}$/");break;
		case 'pw' : reg_str = eval("/^\\w{"+ min + "," + max + "}$/");break;
		case 'ph' : reg_str = eval("/^\\d{"+ min + "," + max + "}$/");break;
		case 'ma' : reg_str = eval("/\\w+([-+.]\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*/");break;
		case 'ur' :break;
	}
	return reg_str.exec(val)//val.match(reg_str)
}


return ValueValid;