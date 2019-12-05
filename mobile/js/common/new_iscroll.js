/**
 * Created by hdw on 2014/11/23.
 */
define(function(require, exports, module) {
    var $ = require('$');
    var IScroll = require('iscroll');

    var Utility = require('./utility');
    var ImgReady = require('./img-ready');
    var UA = require('../base/ua');

    var lazyLoadMaps = {};

    // 扩展后加载方法
    if (!IScroll.prototype.on) {
        IScroll.prototype.on = function (type, fn) {
            if ( !this._events[type] ) {
                this._events[type] = [];
            }

            this._events[type].push(fn);

            return this;
        };

        IScroll.prototype.off = function (type, fn) {
            if ( !this._events[type] ) {
                return;
            }

            var index = this._events[type].indexOf(fn);

            if ( index > -1 ) {
                this._events[type].splice(index, 1);
            }

            return this;
        };

        IScroll.prototype._execEvent = function (type) {
            if (!this._events) {
                this._events = {};
            }

            if ( !this._events[type] ) {
                return;
            }

            var i = 0,
                l = this._events[type].length;

            if ( !l ) {
                return;
            }

            for ( ; i < l; i++ ) {
                this._events[type][i].apply(this, [].slice.call(arguments, 1));
            }

            return this;
        };
    }
    if (!IScroll.prototype.resetLazyLoad) {
        /**
         * 重置后加载项目
         * @returns {IScroll}
         */
        IScroll.prototype.resetLazyLoad = function() {
            var self = this;

            if (!self.options.lazyLoad) {
                return self;
            }

            self._scrollEndTimer && clearTimeout(self._scrollEndTimer);

            var currentHash = Utility.getHash();

            lazyLoadMaps[currentHash] = {
                instance: self,
                $element: $(self.scroller),
                queue: []
            };

            collectElement();

            var wrapperHeight = self.wrapperH;
            var offset = wrapperHeight / 2;
            checkElement({
                thresholdMin: -offset,
                thresholdMax: wrapperHeight + offset,
                y: self.y
            });

            return self;
        };
    }

    // 滚动速度
    var raw = IScroll.prototype._momentum;
    IScroll.prototype._momentum = function(dist, time, maxDistUpper, maxDistLower, size) {
        return raw.apply(this, [
            dist, time * 2, // 倍数越少越快
            maxDistUpper,
            maxDistLower,
            size
        ]);
    };

    module.exports = function(element, options) {
        options || (options = {});

        // 初始化实例
        if (!!element[0] || element instanceof $) {
            element = element[0];
        }

        options.checkDOMChanges = false;
        options.bounce = (!options.bounce)? true : false;
        options.useTransform = true;
        options.useTransition = UA.isIDevice;
        options.zoom = false;
        options.handleClick = false;
        options.hideScrollbar = false;
        options.scrollbarClass ="scroll_bar_class";
        options.fadeScrollbar = true;
        options.hideScrollbar = true;

        options.onRefresh = function() {
            return this._execEvent('refresh');
        };

        options.onTouchEnd = function() {
            return this._execEvent('touchEnd');
        };

        options.onScrollStart = function(e) {
            return this._execEvent('scrollStart', e);
        };
        options.onScrollMove = function() {
            return this._execEvent('scrollMove');
        };
        options.onScrollEnd = function() {
            return this._execEvent('scrollEnd');
        };
        options.onDestroy = function() {
            return this._execEvent('destroy');
        };

        options.onBeforeScrollStart = function(e) {
            return this._execEvent('scrollBeforeStart', e);
        };

        $(element).css('position','relative');


        var myScroll = new IScroll(element, options);


        // 防止阻塞，提高性能
        myScroll.refresh = function() {
            var self = this;

            setTimeout(function() {
                IScroll.prototype.refresh.apply(self, arguments);
            }, 0);

            return self;
        };

        myScroll.on('scrollBeforeStart',function(e)
        {
            var target = e.target;
            while (target.nodeType != 1) target = target.parentNode;


            if (target.tagName != 'SELECT' && target.tagName != 'INPUT' && target.tagName != 'TEXTAREA')
            {
                e.preventDefault();
            }

        });

        myScroll.on('scrollEnd', function() {
            var self = this;

            self._scrollEndTimer && clearTimeout(self._scrollEndTimer);
            self._scrollEndTimer = setTimeout(function() {
                self._execEvent('scrollEndAfter');
            }, 500);
        });

        myScroll.on('scrollMove', function()
        {
            var self = this;

            self._execEvent('scrollMoveAfter');
        });

        if (!options.lazyLoad) {
            return myScroll;
        }

        var currentHash = Utility.getHash();

        // 内容长度发生变化，刷新滚动条
        myScroll.on('refresh', function() {
            refreshElementPosition();
            collectElement();
        });
        myScroll.on('destroy', function() {
            var self = this;

            self._scrollEndTimer && clearTimeout(self._scrollEndTimer);
            lazyLoadMaps[currentHash] && delete lazyLoadMaps[currentHash];
        });
        myScroll.on('scrollEndAfter', function() {
            var self = this;

            var wrapperHeight = self.wrapperH;
            var offset = wrapperHeight / 2;

            checkElement({
                thresholdMin: -offset,
                thresholdMax: wrapperHeight + offset,
                y: self.y
            });
        });





        // 重置后加载项目
        myScroll.resetLazyLoad();

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
        var reserveItem = [], $img, imgOffset, imgOffsetTop, queueInfo;
        while (queueInfo = imgQueues.shift()) {
            $img = queueInfo.$element;
            //console.log($img.parent().position())
            imgOffset = queueInfo.position || $img.position();

            imgOffsetTop = imgOffset.top + options.y;


            if (options.thresholdMin <= imgOffsetTop &&
                options.thresholdMax >= imgOffsetTop) {
                //console.log(queueInfo)
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
                position: get_pos($img[0]),
                $element: $img
            });

            $img.removeAttr('data-lazyload');
        }
    }

    function refreshElementPosition() {
        var hash = Utility.getHash();
        var info = lazyLoadMaps[hash];

        if (!info) {
            return;
        }

        var imgQueues = info.queue;
        var i = 0, len = imgQueues.length, $img, queueInfo;
        if (!len) {
            return;
        }
        for (; i < len; ++i) {
            queueInfo = lazyLoadMaps[hash]['queue'][i];
            $img = queueInfo.$element;
            queueInfo.position = get_pos($img[0]);
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
                if (queueInfo.retry) {
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

                // 修复
                fixImg(requestUri, size);

                queueInfo.$element.one('tap', function(event) {
                    event.stopPropagation();
                    event.preventDefault();
                    queueInfo.retry = 1;

                    loadImg(queueInfo, MapInfo);
                }).addClass('refresh');
            }
        });
    }

    function fixImg(requestUri, size) {

        return;
        var reUri = new RegExp('_' + size + '.jpg');
        var newRequestUri = requestUri.replace(reUri, '_640.jpg');
        var url = '/module_common/item/fix_item_145img.php?item_url=' + encodeURIComponent(newRequestUri) + '&size=' + size;

        if (size == 440) {
            url += '&resize_rules=only_set_width';
        }

        new Image().src = url;
    }

    function get_pos(obj) {
        var curleft = curtop = 0;
        if (obj.offsetParent) {
            do {
                curleft += obj.offsetLeft;
                curtop += obj.offsetTop;
            } while (obj = obj.offsetParent);
        }
        return {
            top : curtop,
            left : curleft
        };
    }
});