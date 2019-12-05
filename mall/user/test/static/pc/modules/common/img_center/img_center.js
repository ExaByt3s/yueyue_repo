define('common/img_center/img_center', function(require, exports, module){ /**
*  lazyload �����  �� ͼƬչʾ���д���
* @authors ��Բ
*/

var $ = require('components/jquery/jquery.js');

/**
 * ͼƬ�����
 * @param {[jquery object]} contain [�����ͼƬ�����ĸ�����]  ��$('#xx')
 * 
 * @param {[object]} options {size : ������������ ��320} �ɲ������������Ŀ�ļ���������ߣ�ռλ�����div�տ�ʼû�У�һ���Ӽ���ͼƬ�����߶ȣ������Ӿ������������⡣
 */

function img_cen(options)
{
    var self = this;
    var options = options || {};
    self.img_center = options.img_center || {};
    self.img_center_width = options.img_center.width || 0;
    self.img_center_height = options.img_center.height || 0;


    var contain = options.contain ;

    if(!contain)
    {
        throw '��δ��ʼ����lazyload�ĸ�����';
    }
    self.init(contain,options);
    return self;
}

img_cen.prototype =
{

    init : function(contain,options)
    {
        var self = this;

        self.options = options;

        self.size = self.options.size;

        self.currentELE = {};

        //���븸����
        if(contain){
            this.container = contain;
            //�״δ���
            self.freshELE(this.container);
            self.engine();
            //���¼�
            $(window).scroll(function()
            {
                self.engine();
            });
        }
        else{
            this.container = null;
        }

    },


    freshELE : function(con){

        var self = this

        con = con || self.container;

        var $ele_arr = $(con).find('[data-lazyload-url]');

        var map = {};

        $ele_arr.each(function(i,obj)
        {
            var url = $(obj).attr('data-lazyload-url');
            map[url] =
            {
                url : url,
                obj : obj
            };
        });

        self.currentELE = map;



        //�ⲿˢ�� ���븸Ԫ�� �ҵ�����[data-lazyload-url]��Ԫ��
        //con ? self.currentELE = $(con).find('[data-lazyload-url]') : this.currentELE = $(this.container).find('[data-lazyload-url]');
    },


    engine : function(){

        var self = this;

        //[data-preurl]Ԫ�ر�������ͼƬ
        for (var key in self.currentELE){

            if ( self.elementInViewport(self.currentELE[key].obj) ){
                self.loadImage(self.currentELE[key].obj,
                    {
                        size : self.size,
                        callback : function(img)
                        {
                            if(img.src == self.currentELE[img.src] && self.currentELE[img.src].url)
                            {
                                delete self.currentELE[img.src];
                            }
                        }
                    });
            }
        }
    },


    refresh : function()
    {
        var self = this;
        self.freshELE();
        self.engine();
    },


    img_ready : function(imgUrl, options)
    {
        var self = this;
        var img = new Image();

        img.src = imgUrl;

        // ���ͼƬ�����棬��ֱ�ӷ��ػ�������
        if (img.complete) {
            self.isFunction(options.load) && options.load.call(img);
            return;
        }

        // ���ش������¼�
        img.onerror = function () {
            self.isFunction(options.error) && options.error.call(img);
            img = img.onload = img.onerror = null;

            //delete img;
        };

        // ��ȫ������ϵ��¼�
        img.onload = function () {
            self.isFunction(options.load) && options.load.call(img);

            // IE gif������ѭ��ִ��onload���ÿ�onload����
            img = img.onload = img.onerror = null;

            //delete img;
        };
    },


    // ��ʼloadͼƬ
    loadImage : function (el,options) 
    {
        var self = this;
        //����ͼƬ ���ֱ�ǩ IMG �� ����[background-image]
        var img = new Image();
        var src = el.getAttribute('data-lazyload-url');
            // self = this;

        options = options || {};

        if(!src)
        {
            return;
        }

        self.img_ready(src,
        {
            load : function()
            {
                console.log('ͼƬ�������');
                var img = this;

                if($(el).hasClass('error'))
                {
                    $(el).removeClass('error refresh');
                }

                var size = el.getAttribute('data-size') || options.size;
                var oldWidth = img.width;
                var oldHeight = img.height;


                // �����Ƿ��� ͼƬ���й��ܣ������֧
                if (self.img_center.is_open) 
                {
                    self.set_img_center(el,self.img_center_width,self.img_center_height);
                }
                else
                {
                    if(size)
                    {
                        var newHeight = self.setImageSize(size,oldWidth,oldHeight);
                        el.style.height = newHeight+'px';
                    }
                }

                self._st = setTimeout(function()
                {
                    el.tagName == 'IMG' ? el.src = src : el.style.backgroundImage = 'url('+ src + ')';

                    $(el).addClass('loaded').removeAttr('data-lazyload-url');
                },500);


                typeof options.callback == 'function' && options.callback.call(self,img);
            },
            error : function()
            {
                console.log(src+'ͼƬ����ʧ��');
                var img = this;

                $(el).addClass('error');

                $(el).one('click', function(event)
                {
                    event.stopPropagation();
                    event.preventDefault();

                    img.retry = 1;

                    self.loadImage(img);
                }).addClass('refresh');
            }
        });
    },


    // ����ͼƬ��񣬿���
    setImageSize : function(size,oldWidth,oldHeight)
    {
        var self = this;
        var newWidth = parseInt(size);
        var oldWidth = parseInt(oldWidth);
        var oldHeight = parseInt(oldHeight);
        var newHeight = (newWidth * oldHeight) / oldWidth;
        newHeight = parseInt(newHeight);
        return newHeight;
    },


    //�ж�Ԫ���Ƿ���������ɼ���Χ��
    elementInViewport : function(el) {
        var self = this;
        var rect = el.getBoundingClientRect()
        return (
            rect.top >= 0 && rect.left >= 0 && rect.top <= (window.innerHeight || document.documentElement.clientHeight)
            )
    },


    isFunction : function(o) {
        var self = this;
        return typeof o === 'function';
    },


    set_img_center : function (imgObjImg,setObjWidth,setObjHeigth) 
    {
        var self = this;
        var imgSrc = imgObjImg.getAttribute('data-lazyload-url');
        var imgSrcObj = self.get_yue_img_size_from_url(imgSrc);
        // imgObjImg.style.visibility = "hidden" ;

        setObjWidth  = parseInt(setObjWidth);
        setObjHeigth = parseInt(setObjHeigth);
        // var imgWidth = imgObjImg.width;
        // var imgHeight = imgObjImg.height;    
        var imgWidth = imgSrcObj.width;
        var imgHeight = imgSrcObj.height;  
          
        if( (imgWidth/setObjWidth) > (imgHeight/setObjHeigth) )
        {
            var retWidth = setObjHeigth*imgWidth / imgHeight;
            imgObjImg.style.width = retWidth;
            imgObjImg.style.height = setObjHeigth + 'px';
            imgObjImg.style.marginLeft = -(retWidth-setObjWidth)/2 + "px";
        }
        else
        {
            var retHeight = setObjWidth*imgHeight / imgWidth;
            imgObjImg.style.height = retHeight;
            imgObjImg.style.width  = setObjWidth + 'px';
            imgObjImg.style.marginTop = -(retHeight-setObjHeigth)/2 + "px";
        }   
        // imgObjImg.style.visibility = "visible" ;
    },

    // ����ͼƬ��ַ��ȡͼƬ��С
    get_yue_img_size_from_url : function (url)
    {
        var m  = url.match(/\?(.*)/);

        var width  = '';
        var height = '';

        if (m[1])
        {

            var result = m[1];

            var img_size_arr = result.match(/(\d+)x(\d+)_(\d+)/);

            if(img_size_arr[1])
            {
                width = img_size_arr[1];
            }
            if(img_size_arr[2])
            {
                height = img_size_arr[2];
            }

            return {
                width : width,
                height : height
            };

        }

        return {
            width : width,
            height : height
        };
    }


};

module.exports = img_cen;
 
});