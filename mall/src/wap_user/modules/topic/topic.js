
var $ = require('zepto');
var LZ = require('../common/lazyload/lazyload');
var utility = require('../common/utility/index');
var App =  require('../common/I_APP/I_APP.js');
var menu = require('../menu/index');
var WeiXinSDK =  require('../common/I_WX_SDK/I_WX_SDK.js');

var demo_tpl = __inline("../topic/tpl_demo.tmpl");
var mySwiper = require('../common/widget/swiper/1.0.0/swiper3.07.min.js');


$(function()
    {
        // 加载轮播图
        var mySwiper = new Swiper ('.swiper-container', {
            direction: 'horizontal',
            loop: false,
            autoplay : 3000,
            speed:300,
            autoplayDisableOnInteraction : false,
            // 如果需要分页器
            pagination: '.swiper-pagination'
        });

        // ===== 加载自定义模板 =====
        var html_str = demo_tpl
        ({
            demo : tpl_json
        });
        $('.tpl-demo').html(html_str);
        // ===== 加载自定义模板 =====

        var $topic_container = $('.topic-info-container').eq(0);        

        $topic_container.find('img').each(function(i,obj)
        {
            var ori_img_url = $(obj).attr('src');
            $(obj).attr({'src':'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkJDQzA1MTVGNkE2MjExRTRBRjEzODVCM0Q0NEVFMjFBIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkJDQzA1MTYwNkE2MjExRTRBRjEzODVCM0Q0NEVFMjFBIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6QkNDMDUxNUQ2QTYyMTFFNEFGMTM4NUIzRDQ0RUUyMUEiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6QkNDMDUxNUU2QTYyMTFFNEFGMTM4NUIzRDQ0RUUyMUEiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz6p+a6fAAAAD0lEQVR42mJ89/Y1QIABAAWXAsgVS/hWAAAAAElFTkSuQmCC','height':200,'data-lazyload-url':ori_img_url,'data-ori-src':ori_img_url});
        });

        $topic_container.removeClass('fn-hide');

        //new 对象 新建内置对象
        var lazyloading = new LZ($('body'),{

        });

        App.isPaiApp && App.showtopmenu(true);

        /**** 调用微信分享 ****/
        if(WeiXinSDK.isWeiXin())
        {
            var share = share_text.result_data;

            // 朋友圈
            var WeiXin_data_Timeline =
                {
                    title: share.title, // 分享标题
                    link: share.url, // 分享链接
                    imgUrl: share.img, // 分享图标
                    success: function ()
                    {
                        // 用户确认分享后执行的回调函数
                        new Image().src = 'http://yp.yueus.com/action/wx_share_callback.php?platform=timeline&url='+encodeURIComponent(window.location.href);
                    },
                    cancel: function ()
                    {
                        // 用户取消分享后执行的回调函数
                    }
                };

            // 好友、QQ
            var WeiXin_data =
                {
                    title: share.title, // 分享标题
                    desc: share.content, // 分享描述
                    link: share.url, // 分享链接
                    imgUrl: share.img, // 分享图标
                    type: '', // 分享类型,music、video或link，不填默认为link
                    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                    success: function ()
                    {
                        // 用户确认分享后执行的回调函数
                        new Image().src = 'http://yp.yueus.com/action/wx_share_callback.php?platform=friends&url='+encodeURIComponent(window.location.href);
                    },
                    cancel: function ()
                    {
                        // 用户取消分享后执行的回调函数
                    }
                };

            WeiXinSDK.ready(function()
            {
                WeiXinSDK.ready(function()
                {
                    WeiXinSDK.ShareToFriend(WeiXin_data);

                    WeiXinSDK.ShareTimeLine(WeiXin_data_Timeline);

                    WeiXinSDK.ShareQQ(WeiXin_data);
                });

            });
        }
        /**** 调用微信分享 ****/
        

        $('.topic-info-container').find('img').on('click',function(ev){
            var self = this;

            var $cur_btn = $(ev.currentTarget);

            var $total_alumn_img = $('.topic-info-container').find('img');

            if($cur_btn.parents('a').length > 0 )
            {
                var cur_a_link = $cur_btn.parents('a').attr('href');

                window.location.href = cur_a_link;

                return false;
            }

            var total_alumn_img_arr = [];

            // 当前图片索引
            var index = $total_alumn_img.index($cur_btn);

            var data =
                {
                    img_arr : total_alumn_img_arr,
                    index : index
                };
            ev.preventDefault();
            var cur_img = $(this).attr('data-ori-src');

            if(App.isPaiApp)
            {
                $total_alumn_img.each(function(i,obj)
                {
                    total_alumn_img_arr.push
                    ({
                        url : $(obj).attr('data-ori-src'),
                        text : ''
                    });
                });
                App.show_alumn_imgs(data);
            }
            else if(WeiXinSDK.isWeiXin())
            {
                // 微信显示大图操作
                $total_alumn_img.each(function(index, item)
                {
                    total_alumn_img_arr.push($(item).attr('data-ori-src'))
                });
                //todo 以后微信或wap版本调用
                WeiXinSDK.imagePreview(cur_img,total_alumn_img_arr);
            }
        });

        //右上角菜单弹出层
        var menu_data =
                    [
                        {
                            index:0,
                            content:'分享',
                            click_event:function()
                            {
                                var self = this;
                                App.share_card(share_text.result_data,
                                        function(data)
                                        {

                                        }
                                )
                            }
                        },
                        {
                            index:1,
                            content:'首页',
                            click_event:function()
                            {
                                App.isPaiApp && App.switchtopage({page:'hot'});
                            }
                        },
                        {
                            index:2,
                            content:'刷新',
                            click_event:function()
                            {
                                window.location.href = window.location.href;
                            }
                        }

                    ];
        menu.render($('body'),menu_data);
        var __showTopBarMenuCount = 0;

        utility.$_AppCallJSObj.on('__showTopBarMenu',function(event,data)
        {

            __showTopBarMenuCount++;

            if(__showTopBarMenuCount%2!=0)
            {
                menu.show()
            }
            else
            {
                menu.hide()
            }
        });
    });