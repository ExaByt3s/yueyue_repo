define('common/lazyload/lazyload', function(require, exports, module){ /**
*  lazyload �����  �� ͼƬչʾ���д���
* @authors ��Բ
*/

var $ = require('components/jquery/jquery.js');
var utility = require('common/utility/index');
/**
 * ͼƬ�����
 * @param {[jquery object]} contain [�����ͼƬ�����ĸ�����]  ��$('#xx')
 * 
 * @param {[object]} options {size : ������������� ��320} �ɲ������������Ŀ�ļ���������ߣ�ռλ�����div�տ�ʼû�У�һ���Ӽ���ͼƬ�����߶ȣ������Ӿ������������⡣
 */

function LazyLoading(options)
{
    var self = this;
    var options = options || {};
    self.img_center = options.img_center || {};
    self.img_center_width = options.img_center.width || 0;
    self.img_center_height = options.img_center.height || 0;
    self.pre_load_165 = options.pre_load_165 || false;

    var contain = options.contain ;

    if(!contain)
    {
        throw '��δ��ʼ����lazyload�ĸ�����';
    }
    self.init(contain,options);
    return self;
}

LazyLoading.prototype =
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

        var map = [];

        $ele_arr.each(function(i,obj)
        {
            var url = $(obj).attr('data-lazyload-url');

            map[i] =
            {
                url : url,
                obj : obj
            };

            if(self.pre_load_165)
            {
                map[i] = $.extend(true,{},map[i],{url_165:utility.matching_img_size(url,165)})
            }

        });

        self.currentELE = map;

        //console.log(map);

        //�ⲿˢ�� ���븸Ԫ�� �ҵ�����[data-lazyload-url]��Ԫ��
        //con ? self.currentELE = $(con).find('[data-lazyload-url]') : this.currentELE = $(this.container).find('[data-lazyload-url]');
    },


    engine : function(){

        var self = this;

        //[data-preurl]Ԫ�ر�������ͼƬ
        for (var i = 0; i < self.currentELE.length; i++){
            if ( self.elementInViewport(self.currentELE[i].obj) ){
                self.loadImage(self.currentELE[i].obj,
                    {
                        size : self.size,
                        callback : function(img)
                        {
                            if(img.src == self.currentELE[i] && self.currentELE[i].url)
                            {
                                self.currentELE.splice(i,1);
                            }
                        },
                        url_165 : self.currentELE[i].url_165
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


                // �����Ƿ��� ͼƬ���й��ܣ�������֧
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

                if(self.pre_load_165 && options.url_165)
                {
                    el.src = options.url_165
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


    // ����ͼƬ��񣬿�����
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

            if ( (retWidth-setObjWidth)/2 > 220  ) 
            {
                imgObjImg.style.marginLeft = 0 ;
            }
            else
            {
                imgObjImg.style.marginLeft = -(retWidth-setObjWidth)/2 + "px";
            }

            

        }
        else
        {
            var retHeight = setObjWidth*imgHeight / imgWidth;
            imgObjImg.style.height = retHeight;
            imgObjImg.style.width  = setObjWidth + 'px';




            if ( retHeight - setObjHeigth - 80 < 0  ) 
            {
                imgObjImg.style.marginTop = -(retHeight-setObjHeigth)/2  + "px";
            }
            else
            {
                imgObjImg.style.marginTop = (-(retHeight-setObjHeigth)/2 + 50) + "px";
            }


            
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

module.exports = LazyLoading;
 
});