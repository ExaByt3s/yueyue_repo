/**
 * 充值成功页
 * Created by hudw on 2015/5/6.
 */
"use strict";


var utility = require('../common/utility/index');
var $ = require('zepto');
var fastclick = require('fastclick');
var yue_ui = require('../yue_ui/frozen');
var App =  require('../common/I_APP/I_APP');



if ('addEventListener' in document)
{
    document.addEventListener('DOMContentLoaded', function ()
    {
        fastclick.attach(document.body);
    }, false);
}



(function($,window)
{


    $('[data-role="confirm-btn"]').on('click',function(ev)
    {
        if(App.isPaiApp)
        {
            App.switchtopage({page : 'mine'});
        }
        else
        {
            window.location.href = 'http://mp.weixin.qq.com/s?__biz=MzAxMDA5NzcxNg==&mid=208946949&idx=1&sn=51858dd131f4f72f7cdd3d3fa660e507#rd'
        }
    });
})($,window);