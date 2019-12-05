define('common/lazyload/lazyload', function(require, exports, module){ /**
*  lazyload 后加载  与 图片展示居中处理
* @authors 汤圆
*/

var $ = require('components/jquery/jquery.js');
var utility = require('common/utility/index');
/**
 * 图片后加载
 * @param {[jquery object]} contain [后加载图片容器的父容器]  如$('#xx')
 * 
 * @param {[object]} options {size : 后加载容器宽度 如320} 可不传，如果传，目的计算出容器高，占位，解决div刚开始没有，一下子加载图片弹出高度，引起视觉差异体验问题。
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
        throw '尚未初始化化lazyload的父容器';
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

        //传入父对象
        if(contain){
            this.container = contain;
            //首次触发
            self.freshELE(this.container);
            self.engine();
            //绑定事件
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

        //外部刷新 传入父元素 找到具有[data-lazyload-url]的元素
        //con ? self.currentELE = $(con).find('[data-lazyload-url]') : this.currentELE = $(this.container).find('[data-lazyload-url]');
    },


    engine : function(){

        var self = this;

        //[data-preurl]元素遍历加载图片
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

        // 如果图片被缓存，则直接返回缓存数据
        if (img.complete) {
            self.isFunction(options.load) && options.load.call(img);
            return;
        }

        // 加载错误后的事件
        img.onerror = function () {
            self.isFunction(options.error) && options.error.call(img);
            img = img.onload = img.onerror = null;

            //delete img;
        };

        // 完全加载完毕的事件
        img.onload = function () {
            self.isFunction(options.load) && options.load.call(img);

            // IE gif动画会循环执行onload，置空onload即可
            img = img.onload = img.onerror = null;

            //delete img;
        };
    },


    // 开始load图片
    loadImage : function (el,options) 
    {
        var self = this;
        //加载图片 区分标签 IMG 和 其他[background-image]
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
                console.log('图片加载完成');
                var img = this;

                if($(el).hasClass('error'))
                {
                    $(el).removeClass('error refresh');
                }

                var size = el.getAttribute('data-size') || options.size;
                var oldWidth = img.width;
                var oldHeight = img.height;


                // 根据是否开启 图片居中功能，处理分支
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
                console.log(src+'图片加载失败');
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


    // 设置图片规格，宽，高
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


    //判断元素是否处于浏览器可见范围中
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

    // 根据图片地址获取图片大小
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