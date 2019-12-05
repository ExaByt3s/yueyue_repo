
function PCB_TRACE(message)
{
	if(!window.PCB){
		console.log(message);
	}
	else{
		PCB.TRACE(message);
	}
}


function PCB_GET_ELEMENT(xpath)										
{																		
	try{																
		var obj = document.evaluate(xpath, document, null, XPathResult.ANY_TYPE, null);
		return obj.iterateNext();										
	}																	
	catch (e) {															
		PCB_TRACE(e.message);												
	}
}
																		
function PCB_GET_POSITION(xpath)										
{																		
	try{																
		var e = PCB_GET_ELEMENT(xpath);										
		if (e){																
			var pos = e.getBoundingClientRect();							
			var sjson = JSON.stringify({ left:pos.left, top : pos.top, width : pos.width, height : pos.height });
			PCB.DispatchJson(sjson);		
			return pos;									
		}																	
	}																	
	catch (e) {															
		PCB_TRACE(e.message);												
	}
}

function PCB_SIM_CLICK(xpath,random,x,y)
{
	try{																
		var element = document;
		if(xpath) 
			element = PCB_GET_ELEMENT(xpath);																
		if(!element){
			PCB_TRACE("ELEMENT NOT FOUND  XPATH= " + xpath);
			return;	
		}
		PCB_TRACE("click begin ->" + element);
		if(random == 1){
			var pos = element.getBoundingClientRect();
			x =  pos.left + Math.random() % (pos.width);
			y = pos.top + Math.random() % (pos.height);
		}
		var evt = document.createEvent('MouseEvents');					
		evt.initMouseEvent('click', true, true, window,					
			1, x, y, x, y, false, false, false, false, 0, null);		
		element.dispatchEvent(evt);										
		PCB_TRACE("click end ->" + element);
	}																	
	catch (e) {															
		PCB_TRACE(e.message);												
	}
}

function PCB_SIM_TOUCH(element, type, identifier, x, y)				
{																		
	try{																
		var evt, touch, touches;											
		if (!document.createTouch || !document.createTouchList)
		{					
			PCB_TRACE("document.createTouch OR document.createTouchList NOT FOUND!!!");
			return;
		}	
		evt = document.createEvent('Event');								
		evt.initEvent(type, true, true);								
		touch = document.createTouch(window, element, identifier, x, y, x, y);
		if (type == 'touchend') {										
			touches = document.createTouchList();						
		}																
		else {															
			touches = document.createTouchList(touch);					
		}																
		evt.touches = touches;	
		element.dispatchEvent(evt);	
	}																	
	catch (e) {															
		PCB_TRACE(e.message);												
	}
}

function PCB_SIM_TOUCH_CLICK(xpath,random,x,y)
{
	try{																
		var element = document;
		if(xpath) 
			element = PCB_GET_ELEMENT(xpath);																
		if(!element){
			PCB_TRACE("ELEMENT NOT FOUND  XPATH= " + xpath);
			return;	
		}
		PCB_TRACE("touch click begin ->" + element.localName + "." + element.className);												
		
		if(random == 1){
			var pos = element.getBoundingClientRect();
			x =  pos.left + Math.random() % (pos.width);
			y = pos.top + Math.random() % (pos.height);
		}
		var identifier = new Date().getTime();
		
		PCB_SIM_TOUCH(element, 'touchstart', identifier, x, y);	
		PCB_SIM_TOUCH(element, 'touchend', identifier, x, y);	
		PCB_TRACE("touch click end ->" + element);
		PCB_SIM_CLICK(xpath,0,x,y);
	}																	
	catch (e) {															
		PCB_TRACE(e.message);												
	}
}

function PCB_SIM_SLIDE(xpath,start_x,start_y,dx,dy)
{			
	try{																
		var element = document;
		if(xpath) 
			element = PCB_GET_ELEMENT(xpath);																
		if(!element){
			PCB_TRACE("ELEMENT NOT FOUND  XPATH= " + xpath);
			return;	
		}
		PCB_TRACE("slide begin ->" + element.localName + "." + element.className);												
		var identifier = new Date().getTime();
		var step_x, step_y;												
		var step = 50;													
		step_x = (dx) / step;	
		step_y = (dy) / step;	
		var loop;
		PCB_SIM_TOUCH(element, 'touchstart', identifier, start_x, start_y);	
		for(loop = 0 ; loop < step ; loop++){
			PCB_SIM_TOUCH(element, 'touchmove', identifier, start_x + loop * step_x , start_y + loop * step_y);	
		}	
		PCB_SIM_TOUCH(element, 'touchend', identifier, start_x + loop * step_x , start_y + loop * step_y);	
		PCB_TRACE("slide end ->" + element);												
	}																	
	catch (e) {															
		PCB_TRACE(e.message);												
	}
}	

function PCB_SET_VAL(id,value)										
{																		
	try{																
		var element = document.getElementById(id);
		if(element){
			element.value = value;
		}
	}																	
	catch (e) {															
		PCB_TRACE(e.message);												
	}
}

function PCB_SET_VAL_BYPATH(xpath,value)										
{																		
	try{																
		var element = PCB_GET_ELEMENT(xpath);
		if(element){
			element.value = value;
		}
	}
	catch (e) {															
		PCB_TRACE(e.message);												
	}
}

function PCB_SET_CHECKBOX(id, is_check)
{
	try{																
		var element = document.getElementById(id);
		if(element){
			if(is_check == 1)
			{
				element.setAttribute('checked', 'checked');
			}else{
				element.removeAttribute('checked');
			}
			
		}
	}																	
	catch (e) {															
		PCB_TRACE(e.message);												
	}
}


function PCB_RUN_JS(callback)
{
	PCB_TRACE('Ö´ÐÐJS:' + callback);
	callback.call(this);
}

function PCB_RUN_UNI(mid)
{
	var page = document.getElementById('p2');
	var timestamp=new Date().getTime();
	var uni_id = mid + '_' + timestamp;
	if(page.style.display == "block")
	{
		var img1 = new Image();
		img1.src = 'http://my.poco.cn/site_stat/uni.php?type=add&uni_id=' + uni_id + '&is_bingo=1';
		
	}else{
		var img1 = new Image();
		img1.src = 'http://my.poco.cn/site_stat/uni.php?type=add&uni_id=' + uni_id + '&is_bingo=0';
		
	}
}


PCB_TRACE("event sim ready");																
