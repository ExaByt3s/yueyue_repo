define('common/lazyload/lazyload', function(require, exports, module){ /**
 * Created by hudingwen on 15/7/19.
 */
var $ = require('components/zepto/zepto.js');
var utility = require('common/utility/index');


(function($,window)
{   
    /**
     * 图片后加载
     * @param {[jquery object]} contain [后加载图片容器的父容器]
     * @param {[object]} options {size : 后加载容器宽度 如320}
     */
    function LazyLoading(contain,options)
    {
        var self = this;

        if(!contain)
        {
            throw '尚未初始化化lazyload的父容器';
        }

        options = options || {};

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
            });

            self.currentELE = map;



            //外部刷新 传入父元素 找到具有[data-lazyload-url]的元素
            //con ? self.currentELE = $(con).find('[data-lazyload-url]') : this.currentELE = $(this.container).find('[data-lazyload-url]');
        },
        engine : function(){

            var self = this;

            //[data-preurl]元素遍历加载图片
            
            if(!self.currentELE.length)
            {
                return;
            }
            for (var i = 0; i < self.currentELE.length; i++){
                if ( elementInViewport(self.currentELE[i].obj) ){
                    loadImage(self.currentELE[i].obj,
                        {
                            size : self.size,
                            callback : function(img)
                            {
                                if(img.src == self.currentELE[i] && self.currentELE[i].url)
                                {
                                    self.currentELE.splice(i,1);
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
        }
    };

    function loadImage (el,options) {
        //加载图片 区分标签 IMG 和 其他[background-image]
        var img = new Image(),
            src = el.getAttribute('data-lazyload-url'),
            self = this;

        options = options || {};

        if(!src)
        {
            return;
        }

        img_ready(src,
        {
            load : function()
            {
                var img = this;

                if($(el).hasClass('error'))
                {
                    $(el).removeClass('error refresh');
                }

                var size = el.getAttribute('data-size') || options.size;
                var oldWidth = img.width;
                var oldHeight = img.height;


                if(size)
                {
                    var newHeight = setImageSize(size,oldWidth,oldHeight);
                    el.style.height = newHeight+'px';
                }

                self._st = setTimeout(function()
                {
                    el.tagName == 'IMG' ? el.src = src : el.style.backgroundImage = 'url('+ src + ')';

                    if(el.tagName == 'IMG' && el.hasAttribute('height'))
                    {
                        el.removeAttribute('height');                                     
                    }

                    $(el).addClass('loaded').removeAttr('data-lazyload-url');
                },250);



                typeof options.callback == 'function' && options.callback.call(self,img);
            },
            error : function()
            {
                var img = this;

                console.warn('图片加载失败:src='+img.src);

                $(el).addClass('error');

                $(el).one('click', function(event)
                {
                    event.stopPropagation();
                    event.preventDefault();

                    img.retry = 1;

                    loadImage(img);
                }).addClass('refresh');
            }
        });



    }

    function setImageSize (size,oldWidth,oldHeight)
    {

        var newWidth = utility.int(size);
        var oldWidth = utility.int(oldWidth);
        var oldHeight = utility.int(oldHeight);

        var newHeight = (newWidth * oldHeight) / oldWidth;

        newHeight = utility.int(newHeight);

        return newHeight;
    }

    function elementInViewport(el) {
        //判断元素是否处于浏览器可见范围中
        var rect = el.getBoundingClientRect();

        /*
        console.log($(el).offset().top,window.scrollY);
        if(key == 'http://image19-d.yueus.com/yueyue/20150907/20150907093143_799656_100008_64797.jpg?260x260_120')
        {
            //console.log(rect);
            console.info($(el).offset().top,window.scrollY);
        }
        */


        return (
            rect.top >= 0 && rect.left >= 0 && rect.top <= (window.innerHeight || document.documentElement.clientHeight)

            )
    }

    function isFunction(o) {
        return typeof o === 'function';
    }

    function img_ready(imgUrl, options)
    {
        var img = new Image();

        img.src = imgUrl;

        // 如果图片被缓存，则直接返回缓存数据
        if (img.complete) {
            isFunction(options.load) && options.load.call(img);
            return;
        }

        // 加载错误后的事件
        img.onerror = function () {
            isFunction(options.error) && options.error.call(img);
            img = img.onload = img.onerror = null;

            //delete img;
        };

        // 完全加载完毕的事件
        img.onload = function () {
            isFunction(options.load) && options.load.call(img);

            // IE gif动画会循环执行onload，置空onload即可
            img = img.onload = img.onerror = null;

            //delete img;
        };
    };


    module.exports = LazyLoading;


})($,window);






 
});