/**
 * 评价列表
 * 汤圆
 */
 /**
  * @require ./qrcode.scss
  **/

"use strict";


var utility = require('../common/utility/index');
var $ = require('zepto');
var fastclick = require('fastclick');
var yue_ui = require('../yue_ui/frozen');
// var App =  require('../common/I_APP/I_APP');
var scroll = require('../common/scroll/index');
var items_tpl = __inline('./items.tmpl');

var s = require('../common/widget/swiper/1.0.0/swiper3.07.min');





if ('addEventListener' in document)
{
    document.addEventListener('DOMContentLoaded', function ()
    {
        fastclick.attach(document.body);
    }, false);
}

    var _self = $({});

    function qrcode(options) 
    {
        var self = this;
        self.options = options || {} ;
        self.render_ele = options.ele;
        self.data = options.data ;
        self.init()
        // 默认播放第0张
        self.play = options.play ? options.play : 0 ;
    }

    qrcode.prototype =
    {
        refresh : function()
        {
            var self = this;
            var html_str = items_tpl(self.data);
            var view = self.render_ele.html(html_str);

            self.close_ele = view.find('[data-role="close"]');
            self.swiper_container_ele = view.find('.swiper-container');
            //self.swiper_container_ele.height(window.innerHeight - 200);

            self.qrcode_main();

            self.close_ele.on('click', function(event) {
                event.preventDefault();
                self.hide();
            });
        },

        init : function()
        {

            var self = this;
            self.refresh();

        },

        qrcode_main : function()
        {
            var self = this;
            self.mySwiper = new Swiper ('.swiper-container', {
                direction: 'horizontal',
                loop: false,
                initialSlide : self.play ,
                // 如果需要分页器
                pagination: '.swiper-pagination'
            })
        },
        hide : function() 
        {
            var self = this;
            self.render_ele.html('');
        }
    };


return  qrcode;



