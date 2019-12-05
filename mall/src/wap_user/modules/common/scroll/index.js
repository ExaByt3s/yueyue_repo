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

    // Ϊ��ʹ��zepto ��Event
    var myScroll = $({});

    var is_cheap_device = (ua.isAndroid && ua.android_version < 4);

    // ����scroll����
    myScroll.$element = $element;

    myScroll.$element.addClass(options.className||'native-scroll');


    // ����scroll����
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
                        '<p>�������������¡�</p>'+
                        '<i class=""></i>'+
                        '</div>';
                }
                else if (status == 'prepare')
                {
                    return '<div class="ui-loading-wrap">'+
                        '<p>�ͷ�ˢ��...</p>'+
                        '<i class=""></i>'+
                        '</div>';
                }
                else if (status == 'load')
                {
                    return '<div class="ui-loading-wrap">'+
                        '<p>������</p>'+
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
                        '<p>���������ظ����</p>'+
                        '<i class=""></i>'+
                        '</div>';
                }
                else if (status == 'prepare')
                {
                    return '<div class="ui-loading-wrap">'+
                        '<p>�ͷż���...</p>'+
                        '<i class=""></i>'+
                        '</div>';
                }
                else if (status == 'load')
                {
                    return '<div class="ui-loading-wrap">'+
                        '<p>������</p>'+
                        '<i class="ui-loading"></i>'+
                        '</div>';
                }
            }


        }
    });

    dragger.on('dragDownLoad', function()
    {
        // ���ۺ�ʱ��������ҵ������������reset()�ӿڣ��Ի�ԭ״̬
        // ������onDragDownLoad()�ص���ʹ��ajax��������ʱ����ajax�Ļص�������Ӧ������reset()����drag״̬
        // ��������ã�drag������ʧЧ
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