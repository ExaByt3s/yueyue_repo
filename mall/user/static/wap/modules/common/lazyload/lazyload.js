define('common/lazyload/lazyload', function(require, exports, module){ /**
 * Created by hudingwen on 15/7/19.
 */
var $ = require('components/zepto/zepto.js');
var utility = require('common/utility/index');


(function($,window)
{
    function LazyLoading(contain,options)
    {
        var self = this;

        if(!contain)
        {
            throw '��δ��ʼ����lazyload�ĸ�����';
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

                if (elementInViewport(self.currentELE[key].obj)){
                    loadImage(self.currentELE[key].obj,
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
        }
    };

    function loadImage (el,options) {
        //����ͼƬ ���ֱ�ǩ IMG �� ����[background-image]
        var img = new Image(),
            src = el.getAttribute('data-lazyload-url'),
            self = this;

        options = options || {};

        img.src = src;

        img.onload = function()
        {
            if($(el).hasClass('error'))
            {
                $(el).removeClass('error refresh');
            }

            var size = el.getAttribute('data-size') || options.size;
            var oldWidth = this.width;
            var oldHeight = this.height;


            if(size)
            {
                var newHeight = setImageSize(size,oldWidth,oldHeight);
                el.style.height = newHeight+'px';
            }

            self._st = setTimeout(function()
            {
                el.tagName == 'IMG' ? el.src = src : el.style.backgroundImage = 'url('+ src + ')';

                $(el).addClass('loaded');
            },500);



            typeof options.callback == 'function' && options.callback.call(self,img);
        };

        img.onerror = function()
        {
            $(el).addClass('error');

            $(el).one('click', function(event)
            {
                event.stopPropagation();
                event.preventDefault();

                img.retry = 1;

                loadImage(img);
            }).addClass('refresh');
        }

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
        //�ж�Ԫ���Ƿ���������ɼ���Χ��
        var rect = el.getBoundingClientRect()
        return (
            rect.top >= 0 && rect.left >= 0 && rect.top <= (window.innerHeight || document.documentElement.clientHeight)
            )
    }


    module.exports = LazyLoading;


})($,window);






 
});