define(function(require, exports, module) {
    var $ = require('$');
    var IScroll = require('iscroll');

    var Utility = require('./utility');
    var ImgReady = require('./img-ready');
    var UA = require('../frame/ua');
    var Backbone = require('backbone');

    var lazyLoadMaps = {};
    var error_maps = {};

    module.exports = function($element, options)
    {
        options || (options = {});

        var myScroll = $.extend({}, Backbone.Events);

        myScroll.$element = $element;


        // 此处的参数是用于下拉刷新的
        var args = {
                loadClass : 'dropload-box',                                     // 下拉容器class
                refreshDOM : '<div class="dropload-refresh"><i class="icon icon-more-24x32"></i><span>下拉刷新</span></div>',    // 下拉DOM
                moreDOM : '<div class="dropload-refresh"><i class="icon icon-more-24x32"></i><span>加载更多</span></div>',    // 上拉DOM
                updateDOM : '<div class="dropload-update"><i class="icon icon-more-24x32"></i><span>释放更新</span></div>',      // 更新DOM
                afterMoreDom : '<div style="height:50px;line-height: 50px;text-align: center;width: 100%">释放更新</div>',
                loadDOM : '<div class="dropload-load"><i class="icon icon-loading ui-loading-animate"></i><span>加载中...</span></div>',         // 加载DOM
                direction : 'up',                                               // 加载内容方向
                down_direction : '',
                distance : 50,                                                   // 下拉距离
                down_direction_distance : 200,
                prevent_tag : ''
            },
            _startY = 0,
            _moveY = 0,
            _curY = 0,
            _offsetY = 0,
            _loadHeight = 0,
            _childrenHeight = 0,
            _scrollTop = 0,
            insertDOM = false,
            loading = false,
            loadName = '',
            is_hide_dropdown = false;

        options = $.extend({},args,options);



        var loadName = '.'+options.loadClass;

        // 绑定触摸
        myScroll.$element.on('touchstart',function(e){
            if(loading){
                return;
            }

            if($(e.target).parents('[data-role="ad-pic"]').attr('data-prevent-scroll') == options.prevent_tag)
            {
                return;
            }

            myScroll.fnTouches(e);
            myScroll.fnTouchstart(e);
        });
        myScroll.$element.on('touchmove',function(e){
            if(loading){
                return;
            }

            if($(e.target).parents('[data-role="ad-pic"]').attr('data-prevent-scroll') == options.prevent_tag)
            {
                return;
            }

            myScroll.fnTouches(e);
            myScroll.fnTouchmove(e);
        });
        myScroll.$element.on('touchend',function(e){
            if(loading){
                return;
            }

            if($(e.target).parents('[data-role="ad-pic"]').attr('data-prevent-scroll') == options.prevent_tag)
            {
                return;
            }

            myScroll.fnTouchend();
        });

        myScroll.destroy = function() {
            var self = this;
            self._scrollEndTimer && clearTimeout(self._scrollEndTimer);
            lazyLoadMaps[currentHash] && delete lazyLoadMaps[currentHash];

            $element.off('scroll');

            myScroll.trigger('destroy');
        };
        myScroll.scrollTo = function(x, y) {
            $element.scrollLeft(x);
            $element.scrollTop(y);
        };
        myScroll.refresh = function() {
            collectElement();
        };

        myScroll.resetLazyLoad = function()
        {
            // to do
        };

        myScroll.resetlazyLoad = function()
        {
            // to do
        };

        /**
         * 强制读取图片
         */
        myScroll.force_load_img = function()
        {
            // to do
            var self = this;

            var wrapperHeight = getElementHeight($element);

            var offset = wrapperHeight / 2;

            var scrollTop = $element.scrollTop();

            checkElement({
                thresholdMin: scrollTop - offset,
                thresholdMax: wrapperHeight + scrollTop + offset,
                y: scrollTop
            });

        };

        /**
         * 注意，从这里添加的事件都是用于下拉刷新的
         * modify by hudw 2014.11.24
         */

        // touches
        myScroll.fnTouches = function(e)
        {
            if(options.is_hide_dropdown)
            {
                return;

            }

            if(!e.touches){
                e.touches = e.originalEvent.touches;
            }
        };

        // touchstart
        myScroll.fnTouchstart = function(e)
        {
            if(options.is_hide_dropdown)
            {
                return;

            }

            var me = this;
            _startY = e.touches[0].pageY;
            _loadHeight = me.$element.height();
            _childrenHeight = me.$element.children().height();
            _scrollTop = me.$element.scrollTop();
        };

        // touchmove
        myScroll.fnTouchmove = function(e)
        {
            if(options.is_hide_dropdown)
            {
                return;

            }

            _curY = e.touches[0].pageY;
            _moveY = _curY - _startY;
            var me = this,
                _absMoveY = Math.abs(_moveY);



            // 加载上放
            if(options.direction == 'up' && _scrollTop <= 0 && _moveY > 0)
            {
                me.touch_direction = options.direction;

                e.preventDefault();
                if(!insertDOM){
                    me.$element.prepend('<div class="'+options.loadClass+'"></div>');
                    insertDOM = true;
                }
                fnTransition($(loadName),0);

                // 下拉
                if(_absMoveY <= options.distance){
                    _offsetY = _absMoveY;
                    $(loadName).html('').append(options.refreshDOM);

                    // 指定距离 < 下拉距离 < 指定距离*2
                }else if(_absMoveY > options.distance && _absMoveY <= options.distance*2){
                    _offsetY = options.distance+(_absMoveY-options.distance)*0.5;
                    $(loadName).html('').append(options.updateDOM);

                    // 下拉距离 > 指定距离*2
                }else{
                    _offsetY = options.distance+options.distance*0.5+(_absMoveY-options.distance*2)*0.2;
                }

                if(options.is_hide_dropdown)
                {
                    $(loadName).addClass('fn-invisible');
                }

                $(loadName).css({'height': _offsetY});

            }

            //console.log(_childrenHeight,_loadHeight,_scrollTop,_moveY)

            // 加载下方
            if(options.down_direction == 'down' && _childrenHeight <= (_loadHeight+_scrollTop) && _moveY < 0){
                e.preventDefault();

                me.touch_direction = options.down_direction;

                if(!insertDOM){
                    me.$element.append('<div class="'+options.loadClass+'"></div>');
                    insertDOM = true;
                }
                fnTransition($(loadName),0);
                // 下拉
                if(_absMoveY <= options.down_direction_distance){
                    _offsetY = _absMoveY;
                    $(loadName).html('').append(options.moreDOM);
                    // 指定距离 < 下拉距离 < 指定距离*2
                }else if(_absMoveY > options.down_direction_distance && _absMoveY <= options.down_direction_distance*2){
                    _offsetY = options.down_direction_distance+(_absMoveY-options.down_direction_distance)*0.5;
                    $(loadName).html('').append(options.afterMoreDom);
                    // 下拉距离 > 指定距离*2
                }else{
                    _offsetY = options.down_direction_distance+options.down_direction_distance*0.5+(_absMoveY-options.down_direction_distance*2)*0.2;
                }
                $(loadName).css({'height': _offsetY});
                me.$element.scrollTop(_offsetY+_scrollTop);


            }
        };

        // touchend
        myScroll.fnTouchend = function()
        {
            var me = this,
                _absMoveY = Math.abs(_moveY);

            if(options.is_hide_dropdown)
            {
                $(loadName).addClass('fn-invisible');

            }

            if(insertDOM){
                fnTransition($(loadName),300);
                if(me.touch_direction == 'down')
                {
                    if(_absMoveY > options.down_direction_distance)
                    {
                        $(loadName).css({'height':$(loadName).children().height()});
                        $(loadName).html('').append(options.loadDOM);
                        me.fnCallback('down');

                    }
                    else
                    {
                        $(loadName).css({'height':'0'}).on('webkitTransitionEnd',function()
                        {
                            insertDOM = false;
                            $(this).remove();
                        });
                    }


                }
                else
                {
                    if(_absMoveY > options.distance)
                    {
                        $(loadName).css({'height':$(loadName).children().height()});
                        $(loadName).html('').append(options.loadDOM);
                        me.fnCallback();

                    }
                    else
                    {
                        $(loadName).css({'height':'0'}).on('webkitTransitionEnd',function()
                        {
                            insertDOM = false;
                            $(this).remove();
                        });
                    }
                }

                _moveY = 0;
            }

        };

        // 回调
        myScroll.fnCallback = function(direction)
        {
            var me = this;
            loading = true;

            var direction = direction || 'up'

            if(direction == 'up')
            {
                myScroll.trigger('dropload',me);
            }
            else
            {
                myScroll.trigger('pullload',me);
            }


        };

        // 重置
        myScroll.resetload = function()
        {
            var me = this;



            if($(loadName).length>0)
            {
                $(loadName).css({'height':'0'}).on('transitionEnd webkitTransitionEnd',function()
                {
                    loading = false;
                    insertDOM = false;
                    $(this).remove();
                });
            }
            else
            {
                loading = false;
                insertDOM = false;
                $(this).remove();
            }
        };

        // 快速置顶
        // 重现置顶，顶替 scrollTo(0,0) 但不是最好的解决办法
        // hudw 2014.11.24
        myScroll.reset_top = function()
        {
            var self = this;

            myScroll.scrollTo(0,1);
            myScroll.scrollTo(0,0);
        };

        /**
         * 移动滚动条位置，主要用于后加载刷新
         */
        myScroll.change_scroll_position = function()
        {
            var self = this;

            myScroll.scrollTo(0,myScroll.$element.scrollTop()+1);
        };




        $element.on('scroll', function(event) {
            myScroll.trigger('scrollEnd', event);

            myScroll._scrollEndTimer && clearTimeout(myScroll._scrollEndTimer);
            myScroll._scrollEndTimer = setTimeout(function() {
                myScroll.trigger('scrollEndAfter', event);

            }, 300);
        }).addClass('native_scroll');

        if (options.lazyLoad)
        {
            var currentHash = Utility.getHash();

            myScroll.on('scrollEndAfter', function(event) {
                var self = this;

                var wrapperHeight = getElementHeight(self.$element);
                var offset = wrapperHeight / 2;
                var scrollTop = self.$element.scrollTop();

                checkElement({
                    thresholdMin: scrollTop - offset,
                    thresholdMax: wrapperHeight + scrollTop + offset,
                    y: scrollTop
                });
            });

            myScroll.initLazyLoad = function() {
                var self = this;
                self._scrollEndTimer && clearTimeout(self._scrollEndTimer);

                var currentHash = Utility.getHash();

                // 重置后加载项目
                lazyLoadMaps[currentHash] = {
                    instance: myScroll,
                    $element: $element,
                    queue: []
                };

                collectElement();

                var wrapperHeight = getElementHeight($element);
                var offset = wrapperHeight / 2;
                var scrollTop = $element.scrollTop();

                checkElement({
                    thresholdMin: scrollTop - offset,
                    thresholdMax: wrapperHeight + scrollTop + offset,
                    y: scrollTop
                });
            };

            myScroll.initLazyLoad();
        }

        return myScroll;
    };

    // Helps
    // ----------

    /**
     * 筛选符合条件的后加载元素
     * @param options
     */
    function checkElement(options) {
        var hash = Utility.getHash();
        var info = lazyLoadMaps[hash];

        if (!info) {
            return;
        }

        var imgQueues = info.queue;

        if (!imgQueues.length) {
            return;
        }

        var reserveItem = [], $img, imgOffset, imgOffsetTop, queueInfo;
        while (queueInfo = imgQueues.shift()) {
            $img = queueInfo.$element;
            imgOffset = $img.offset();

            imgOffsetTop = imgOffset.top + options.y;



            if (options.thresholdMin <= imgOffsetTop &&
                options.thresholdMax >= imgOffsetTop) {

                loadImg(queueInfo, info);
            } else {
                reserveItem.push(queueInfo);
            }
        }

        lazyLoadMaps[hash].queue = reserveItem;
    }

    /**
     * 收集后加载元素
     */
    function collectElement() {
        var hash = Utility.getHash();
        var info = lazyLoadMaps[hash];

        if (!lazyLoadMaps[hash]) {
            return;
        }



        var $images = info.$element.find('i[data-lazyload]');
        var i = 0, len = $images.length;



        var imgUrl, $img;
        for (; i < len; i++) {
            $img = $images.eq(i);
            imgUrl = $img.attr('data-lazyload');

            if (!imgUrl) {
                continue;
            }



            info.queue.push({
                uri: imgUrl,
                $element: $img
            });

            error_maps[imgUrl] = {};

            error_maps[imgUrl]['reload_idx'] = 0;



            $img.removeAttr('data-lazyload');
        }
    }

    /**
     * 加载图片
     * @param queueInfo 队列信息
     * @param MapInfo
     * @param force 是否强制
     */
    function loadImg(queueInfo, MapInfo, force) {
        var requestUri = queueInfo.uri;



        //requestUri = requestUri.replace('.poco.cn', '.poco.com');
        requestUri && ImgReady(requestUri, {
            load: function() {

                if(error_maps[queueInfo.uri])
                {
                    error_maps[queueInfo.uri].reload_idx = 0;
                }



                var imgNode = this;

                var needRefresh = false;

                var $img = queueInfo.$element;

                var size = Utility.int($img.attr('data-size') || 0);

                var css = {
                    backgroundImage: 'url(' + queueInfo.uri + ')'
                };
                if (size) {
                    needRefresh = true;

                    var oldWidth = imgNode.width;
                    var oldHeight = imgNode.height;

                    var newWidth = size;
                    var newHeight = (newWidth * oldHeight) / oldWidth;

                    css.width = Utility.int(newWidth);
                    css.height = Utility.int(newHeight);

                }

                if (force && imgNode.width > imgNode.height) {
                    css.backgroundSize = 'auto 100%';
                }

                queueInfo.retry && $img.removeClass('refresh reload');
                $img.css(css).addClass('loaded');

                if (needRefresh) {
                    MapInfo.instance.refresh();
                }
            },
            error: function() {
                if (queueInfo.retry)
                {
                    queueInfo.$element.removeClass('refresh reload')
                        .addClass('error');
                    return;
                }

                var size = '';
                // 145、230和440图片格式错误时，提交修复请求
                if (requestUri.indexOf('_145.jpg') !== -1) {
                    size = 145;
                } else if (requestUri.indexOf('_230.jpg') !== -1) {
                    size = 230;
                } else if (requestUri.indexOf('_440.jpg') !== -1) {
                    size = 440;
                }

                //重新读取

                reload(queueInfo, MapInfo);

                // 修复
                //fixImg(requestUri, size);

                /*queueInfo.$element.one('tap', function(event) {
                    event.stopPropagation();
                    event.preventDefault();
                    queueInfo.retry = 1;

                    loadImg(queueInfo, MapInfo);
                }).addClass('refresh');*/
            }
        });
    }

    function reload(queueInfo, MapInfo)
    {

        /**
         * 重复加载烂图，如果加载3次都失败就取消加载
         */
        if(error_maps[queueInfo.uri] && error_maps[queueInfo.uri].reload_idx == 3)
        {
            return;
        }

        if(!error_maps[queueInfo.uri])
        {
            error_maps[queueInfo.uri] = {};

            error_maps[queueInfo.uri]['reload_idx'] = 0;
        }

        error_maps[queueInfo.uri].reload_idx++;

        loadImg(queueInfo, MapInfo);

        console.log("%cImg now go to reload()",'color:#f90');
    }



    function getElementHeight($element) {
        return $element.height() - 44;
    }

    // css过渡
    function fnTransition(dom,num){
        dom.css({
            '-webkit-transition':'all '+num+'ms',
            'transition':'all '+num+'ms'
        });
    }
});