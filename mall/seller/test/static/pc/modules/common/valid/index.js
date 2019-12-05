define('common/valid/index', function(require, exports, module){ function ValueValid(contain,default_group){
    //����һ����������
    this.containNode = contain;
    this.validNodes = $(this.containNode).find('[valid-rule]');
    this.checkList = [];
    this.checkListNull = [];
    this.checkListPass = [];
    this.valid_group = default_group || "default";
    
    this.txs = function(val,exp){
    	return ValueValid.prototype.valid(val,exp)
    }


}

ValueValid.prototype.freshNodes = function(con,default_group){

    this.validNodes = $(this.containNode).find('[valid-rule]');
    this.valid_group = default_group;
}

ValueValid.prototype.change_group = function(group)
{
    this.valid_group = group;
}

ValueValid.prototype.get_group = function()
{
    return this.valid_group
}

ValueValid.prototype.checkValid = function(){
	this.checkList = [];
	this.checkListNull = [];
	this.checkListPass = [];
    for(var i = 0; i < this.validNodes.length; i++){


        if($(this.validNodes[i]).attr('valid_group') == undefined || $(this.validNodes[i]).attr('valid_group') == this.valid_group)
        {
            //��valid_group���� �� groupֵ����
            var status = ValueValid.prototype.result(this.validNodes[i]);
            this.checkList.push({node:this.validNodes[i],status:status});
            if(!status){
                this.checkListNull.push({node:this.validNodes[i],status:status})
            }
            else{
                this.checkListPass.push({node:this.validNodes[i],status:status})
            }
        }
        else
        {
            //����groupֵ ��ֵ������
        }
    }
    if(this.checkListNull.length > 0)afterCheck(this.checkListNull);
    
    function afterCheck(list){
    	var node = $(list[0].node)
    	if(node.attr('valid-errorAlert') && $.trim(node.attr('valid-errorAlert')) != ""){
    		alert(node.attr('valid-errorAlert'))
    	}
    	else{
    		node.trigger('validError');
    	}
    }

    return {
    	checkList : this.checkList,
		checkListNull : this.checkListNull,
		checkListPass : this.checkListPass,
		isPass : this.checkListNull.length == 0 ? true : false
    }
}

ValueValid.prototype.result = function(node){
	var _node = $(node);

	if($.trim(_node.val()) == ''){
		empty();
	}//������
	if(!_node.attr('valid-rule') && ($.trim(_node.attr('valid-rule')) == '')){
		//�ޱ��ʽ
		noExpect();
	}else{
		//�б��ʽ
		return ValueValid.prototype.valid($.trim(_node.val()),$.trim(_node.attr('valid-rule')));
	}

	function empty(){
		if(_node.attr('valid-emptyAlert') && $.trim(_node.attr('valid-emptyAlert')) != ""){
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
	//ʱ��� �������
	for(var i = 0; i < this.validNodes.length; i++){	
		var node = $(this.validNodes[i]);
        unbinded(node);
		var index_f = parseInt(node.attr('valid-index'));
			for(var k = 0; k < Obj.length; k++){
				if(index_f === parseInt(Obj[k].index)){

					binded(node,Obj[k]);
				}
			}
	}

    function unbinded(_node)
    {   //����¼�
        _node.unbind('onEmpty');
        _node.unbind('validError');
    }

	function binded(_node,data){
		//�¼���
		data.ValidClick && _node.on('click',data.ValidClick);
		data.ValidBlur && _node.on('blur',data.ValidBlur);
		data.ValidEmpty && _node.on('onEmpty',data.ValidEmpty);
		data.ValidError && _node.on('validError',data.ValidError);
	}
}

ValueValid.prototype.valid = function(val,exp){
	//У��
	var type = exp.slice(0,2);
	var min = parseInt(exp.slice(2,exp.indexOf('-')));
	var max = parseInt(exp.slice(exp.indexOf('-')+1,exp.length));
	
	if(isNaN(min) || isNaN(max))throw 'valid-rule error';

	var reg_str;
	
	switch (type)
	{
		case 'en' : reg_str = eval("/^[a-z]{"+ min + "," + max + "}$/i");return reg_str.exec(val);break; //Ӣ��
		case 'zh' : reg_str = eval("/^[\u4e00-\u9fa5]{"+ min + "," + max + "}$/gm");return reg_str.exec(val);break; //����
		case 'nb' : reg_str = eval("/^\\d{"+ min + "," + max + "}$/");return reg_str.exec(val);break; //����
        case '!z' : reg_str = eval("/^[1-9]\\d{"+ (min-1) + "," + (max-1) + "}$/");return reg_str.exec(val);break; //��������
		case '**' : reg_str = eval("/^[\\s|\\S]{"+ min + "," + max + "}$/");return reg_str.exec(val);break; //ȫ��
		case 'pw' : reg_str = eval("/^\\w{"+ min + "," + max + "}$/");return reg_str.exec(val);break;
		case 'ph' : reg_str = eval("/^\\d{"+ min + "," + max + "}$/");return reg_str.exec(val);break;
        case 'ma' : reg_str = eval("/\\w+([-+.]\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*/");return reg_str.exec(val);break; //��������
		case 'ur' : break;
        case 'id' : reg_str = eval("/^([0-9]{17}[0-9xX]{1})|([0-9]{15})$/");return reg_str.exec(val);break; //���֤
        case 'fl' : reg_str = eval("/^[0-9]{"+ min + "," + max + "}[\\.]\\d{1,2}$/");return reg_str.exec(val);break; //���� Ĭ��С��������λ
        case 'pn' :
            //�����͸����� Ĭ�ϱ���С�������λ
            reg_str = /[.]{1}/;
            if(reg_str.exec(val)){
                //�� .
                reg_str = eval("/^\\d{"+ min + "," + max + "}[\\.]\\d{1,2}$/");
            }
            else{
                //�� .
                reg_str = eval("/^\\d{"+ min + "," + max + "}$/");
            }
            return reg_str.exec(val);
            break;
        case 'an' :
            //�������͸����� Ĭ�ϱ���С�������λ
            reg_str = /[.]{1}/;
            if(reg_str.exec(val)){
                //�� .
                reg_str = eval("/^\\d{"+ min + "," + max + "}[\\.]\\d{1,2}$/");
            }
            else{
                //�� .
                reg_str = eval("/^[1-9]\\d{"+ (min-1) + "," + (max-1) + "}$/");
            }
            return reg_str.exec(val);
            break;
        case '!s' : reg_str = eval("/^[^.~!@#$%\\^\\+\\*&\\\/?\\|:\\.{}()';=\"]{"+ min + "," + max + "}$/");
            return reg_str.exec(val);
            break;
        case '!t' : reg_str = eval("/^[^$'\"]{"+ min + "," + max + "}$/");
            return reg_str.exec(val);
            break;
        case 'zp' :
            //���ڵ���0���������͸�����
            if(val >= 0){
                reg_str = /[.]{1}/;
                if(reg_str.exec(val)){
                    //�� .
                    reg_str = eval("/^\\d{"+ min + "," + max + "}[\\.]\\d{1,2}$/");
                }
                else{
                    //�� .
                    reg_str = eval("/^\\d{"+ min + "," + max + "}$/");
                }
                return reg_str.exec(val);
            }
            else{
                return null
            }
            break;
        case 'ap' :
            //����0���������͸����� ����
            if(val > 0 || !val){
                reg_str = /[.]{1}/;
                if(reg_str.exec(val)){
                    //�� .
                    reg_str = eval("/^\\d{"+ min + "," + max + "}[\\.]\\d{1,2}$/");
                }
                else{
                    //�� .
                    reg_str = eval("/^\\d{"+ min + "," + max + "}$/");
                }
                return reg_str.exec(val);
            }
            else{
                return null
            }
            break;
        case 'ze' :
            //����0��������
            if(val > 0){
                reg_str = /[.]{1}/;
                if(reg_str.exec(val)){
                    return null
                }
                else{
                    //�� .
                    reg_str = eval("/^\\d{"+ min + "," + max + "}$/");
                }
                return reg_str.exec(val);
            }
            else{
                return null
            }
            break;
        case 'zi' :
            //���ڵ���0��������
            if(val >= 0){
                reg_str = /[.]{1}/;
                if(reg_str.exec(val)){
                    return null
                }
                else{
                    //�� .
                    reg_str = eval("/^\\d{"+ min + "," + max + "}$/");
                }
                return reg_str.exec(val);
            }
            else{
                return null
            }
            break;
	}
	//return reg_str.exec(val)//val.match(reg_str)
}


return ValueValid; 
});