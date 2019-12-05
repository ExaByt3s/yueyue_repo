/**
 * Created by hudingwen on 15/6/1.
 */

/**
 * @require ./scroll.scss
 */

'use strict';

var $ = require('zepto');
var ua = require('../ua/index');
var drag_loader  = require('./dragloader');

module.exports = function($element,options)
{
    options || (options = {});

    // 为了使用zepto 的Event
    var myScroll = $({});

    var is_cheap_device = (ua.isAndroid && ua.android_version < 4);

    // 配置scroll对象
    myScroll.$element = $element;

    myScroll.$element.addClass(options.className||'native-scroll');


    // 配置scroll参数
    options.dragDownRegionCls = options.dragDownRegionCls || 'latest';
    options.dragUpRegionCls = options.dragUpRegionCls || 'more';
    options.isCustomUI = options.isCustomUI || false;

    var dragger = new DragLoader($element[0],
    {
        dragDownRegionCls: options.dragDownRegionCls,
        dragUpRegionCls: options.dragUpRegionCls,
        dragDownHelper: function(status)
        {
            if(options.isCustomUI)
            {
                // todo
            }
            else
            {
                if (status == 'default')
                {

                    return '<div class="ui-loading-wrap">'+
                        '<p>向下拉加载最新↓</p>'+
                        '<i class=""></i>'+
                        '</div>';
                }
                else if (status == 'prepare')
                {
                    return '<div class="ui-loading-wrap">'+
                        '<p>释放刷新...</p>'+
                        '<i class=""></i>'+
                        '</div>';
                }
                else if (status == 'load')
                {
                    return '<div class="ui-loading-wrap">'+
                        '<p>加载中</p>'+
                        '<i class="ui-loading"></i>'+
                        '</div>';
                }
            }



        },
        dragUpHelper: function(status)
        {
            if(options.isCustomUI)
            {
                // todo
            }
            else
            {

                if (status == 'default')
                {

                    return '<div class="ui-loading-wrap">'+
                        '<p>向上拉加载更多↑</p>'+
                        '<i class=""></i>'+
                        '</div>';
                }
                else if (status == 'prepare')
                {
                    return '<div class="ui-loading-wrap">'+
                        '<p>释放加载...</p>'+
                        '<i class=""></i>'+
                        '</div>';
                }
                else if (status == 'load')
                {
                    return '<div class="ui-loading-wrap">'+
                        '<p>加载中</p>'+
                        '<i class="ui-loading"></i>'+
                        '</div>';
                }
            }


        }
    });

    dragger.on('dragDownLoad', function()
    {
        // 无论何时，必须由业务功能主动调用reset()接口，以还原状态
        // 比如在onDragDownLoad()回调中使用ajax加载数据时，在ajax的回调函数中应当调用reset()重置drag状态
        // 如果不重置，drag操作将失效
        //dragger.reset();
        myScroll.trigger('success:drag_down_load',dragger);
    });
    dragger.on('dragUpLoad', function()
    {
        //dragger.reset();
        myScroll.trigger('success:drag_up_load',dragger);
    });

    myScroll.dragger = dragger;

    return myScroll;
};