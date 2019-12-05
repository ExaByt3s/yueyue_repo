define('common/utility/index', function(require, exports, module){ /**
 * ���߼�
 * hdw  2014.8.13
 */

/**
 * @param {String}  errorMessage   ������Ϣ
 * @param {String}  scriptURI      ������ļ�
 * @param {Long}    lineNumber     ���������к�
 * @param {Long}    columnNumber   ���������к�
 */
window.onerror = function (errorMessage, scriptURI, lineNumber, columnNumber)
{

    // ��callback������£���������Ϣ���ݵ�options.callback��
    if (typeof callback === 'function') {
        callback({
            message: errorMessage,
            script: scriptURI,
            line: lineNumber,
            column: columnNumber
        });
    } else {
        // �������������alert��ʽֱ����ʾ������Ϣ
        var err_log_src = 'http://www.yueus.com/mobile_app/log/save_log.php?from_str=yue_m2&err_level=2&url='+encodeURIComponent(window.location.href);

        var img = new Image();

        var msgs = [];

        msgs.push("�����д�����");
        msgs.push("\n������Ϣ��", errorMessage);
        msgs.push("\n�����ļ���", scriptURI);
        msgs.push("\n����λ�ã�", lineNumber + '�У�' + columnNumber + '��');


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


var $ = require('components/jquery/jquery.js');
var $win = $(window);
var placeholder = require('common/placeholder/index');

var utility = 
{
	fix_placeholder : function()
	{
		$('input').placeholder();
		
	}
};

return utility;  
});