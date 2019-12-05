
function $() {
  var elements = new Array();

  for (var i = 0; i < arguments.length; i++) {
    var element = arguments[i];
    if (typeof element == 'string')
      element = document.getElementById(element);

    if (arguments.length == 1)
      return element;

    elements.push(element);
  }

  return elements;
}

function feedback(element,msg)
{
	alert(msg);
	element.focus();
	return false;
}

function addOption(elementId, text, value, selected, attArr)
{
	oSelect = document.getElementById(elementId);
	oOption = document.createElement("OPTION");
	oSelect.options.add(oOption);
	oOption.text = text;
	oOption.value = value;
	if(selected) oOption.selected = true;

	if(typeof(attArr)=='object')
	{
		for (key in attArr)   
		{
			eval('oOption.'+key+' = \''+attArr[key]+'\'');
		}
	}
}

function showHideObj(elementId, display)
{
	if(display)
	{
		$(elementId).style.display=display;
	}
	else
	{
		if($(elementId).style.display=='none') $(elementId).style.display = '';
		else	$(elementId).style.display = 'none';
	}
}

function getLeft(obj)
{
    var left = obj.offsetLeft;
    var top  = obj.offsetTop;
    obj = obj.offsetParent;
    while(obj.tagName != "BODY")
    {
        left += obj.offsetLeft;
        top  += obj.offsetTop;
        obj   = obj.offsetParent;
    }
    return left;
}

function getTop(obj)
{
    var left = obj.offsetLeft;
    var top  = obj.offsetTop;
    obj = obj.offsetParent;
    while(obj.tagName != "BODY")
    {
        left += obj.offsetLeft;
        top  += obj.offsetTop;
        obj   = obj.offsetParent;
    }
    return top;
}