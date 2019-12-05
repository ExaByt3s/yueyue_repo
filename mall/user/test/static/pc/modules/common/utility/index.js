define('common/utility/index', function(require, exports, module){ /**
 * ���߼�
 * hdw  2014.8.13
 */

/**
 * �л�ͼƬsize
 * @param img_url
 * @param size
 * @returns {*}
 */
function change_img_resize(img_url,size)
{
    var size_str = '';

    size = size || '';

    if($.inArray(size, [120,320,165,640,600,145,440,230,260]) != -1)
    {
        size_str = '_' +size;
    }
    else
    {
        size_str = '';
    }
    // ����img_url

    var url_reg = /^http:\/\/(img|image)\d*(-c|-wap|-d)?(.poco.cn.*|.yueus.com.*)\.jpg|gif|png|bmp/i;

    var reg = /_165.jpg|_320.jpg|_640.jpg|_120.jpg|_600.jpg|_145.jpg|_260.jpg|_440.jpg/i;

    if (url_reg.test(img_url))
    {
        if(reg.test(img_url))
        {
            img_url = img_url.replace(reg,size_str+'.jpg');
            
        }
        else
        {
            img_url = img_url.replace('/(\.\d*).jpg|.jpg|.gif|.png|.bmp/i', size_str+".jpg");//����.jpgΪ����������ϴ�ͼƬ��

        }
    }


    
    return img_url;
}


var $ = require('components/jquery/jquery.js');
var $win = $(window);
var placeholder = require('common/placeholder/index');

var utility = 
{
	fix_placeholder : function()
	{
		$('input').placeholder();
		
	},
    //ͼƬת��size
    matching_img_size : function(img_url,size)
    {

        var sort_size = size;

        return change_img_resize(img_url,sort_size);
    }
};

return utility;  
});