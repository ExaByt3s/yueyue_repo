/**
 * 工具集
 * hdw  2014.8.13
 */

/**
 * @param {String}  errorMessage   错误信息
 * @param {String}  scriptURI      出错的文件
 * @param {Long}    lineNumber     出错代码的行号
 * @param {Long}    columnNumber   出错代码的列号
 */
window.onerror = function (errorMessage, scriptURI, lineNumber, columnNumber)
{

    // 有callback的情况下，将错误信息传递到options.callback中
    if (typeof callback === 'function') {
        callback({
            message: errorMessage,
            script: scriptURI,
            line: lineNumber,
            column: columnNumber
        });
    } else {
        // 其他情况，都以alert方式直接提示错误信息
        var err_log_src = 'http://www.yueus.com/mobile_app/log/save_log.php?from_str=yue_m2&err_level=2&url='+encodeURIComponent(window.location.href);

        var img = new Image();

        var msgs = [];

        msgs.push("代码有错。。。");
        msgs.push("\n错误信息：", errorMessage);
        msgs.push("\n出错文件：", scriptURI);
        msgs.push("\n出错位置：", lineNumber + '行，' + columnNumber + '列');


        if(window.location.href.indexOf('webdev')!=-1)
        { 

            console.log(msgs.join(''));
        }
        else
        {
            img.src = err_log_src+'&err_str='+encodeURIComponent(msgs.join(''));
        }


    }
};


var $ = require('jquery');
var $win = $(window);
var placeholder = require('../placeholder/index');

var utility = 
{
	fix_placeholder : function()
	{
		$('input').placeholder();
		
	}
};

return utility; 