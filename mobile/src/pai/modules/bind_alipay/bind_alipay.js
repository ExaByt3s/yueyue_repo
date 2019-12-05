/**
 * Created by hudingwen on 15/6/4.
 * 绑定支付宝页面
 */

"use strict";


var utility = require('../common/utility/index');
var $ = require('zepto');
var fastclick = require('fastclick');
var yue_ui = require('../yue_ui/frozen');
var App =  require('../common/I_APP/I_APP');
var abnormal = require('../common/widget/abnormal/index');
var menu = require('../menu/index');
var uploader = require('../common/widget/uploader/index');

if ('addEventListener' in document)
{
    document.addEventListener('DOMContentLoaded', function ()
    {
        fastclick.attach(document.body);
    }, false);
}

(function($,window)
{
    var _self = $({});

    if(App.isPaiApp)
    {
        App.check_login(function(data)
        {
            /**
             * 获取个人信息函数，专用于app
             */

            var params = window.__YUE_APP_USER_INFO__;

            var local_user_id = utility.login_id;
            var client_user_id = window.__YUE_APP_USER_INFO__.user_id || 0;

            var async = (local_user_id == client_user_id);

            console.log("=====local_user_id,client_user_id=====");
            console.log(local_user_id,client_user_id);

            utility.ajax_request
            ({
                url: window.$__config.ajax_url.auth_get_user_info,
                data : params,
                cache: false,
                async : async
            });

            if(!utility.int(data.pocoid))
            {
                App.openloginpage(function(data)
                {
                    if(data.code == '0000')
                    {
                        utility.refresh_page();
                    }
                });

                return;
            }

        });

    }
    else
    {
        if(!utility.auth.is_login())
        {
            window.location.href = '../../account/login.php?redirect_url='+encodeURIComponent(window.location.href);
        }
    }

    /*********** 右上角菜单栏 ************/
    /*
     传入对象
     {index:索引值,根据索引值从小到大排列选项顺序}
     {content:文本内容}
     {click_event:点击事件}
     */
    var menu_data =
        [
            {
                index:0,
                content:'首页',
                click_event:function()
                {
                    App.isPaiApp && App.switchtopage({page:'hot'});
                }
            },
            {
                index:1,
                content:'刷新',
                click_event:function()
                {
                    window.location.href = window.location.href;
                }
            }
        ];

    /*
     render() 方法传入(父容器，对象)
     show()   下拉菜单
     hide()   上拉菜单
     */
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
    /*********** 右上角菜单栏 ************/

    var bind_alipay_class = function()
    {
        var self = this;

        self.init();
    };

    bind_alipay_class.prototype =
    {
        init : function()
        {
            var self = this;

            _self.$uploader = $('[data-role="upload-img-container"]');
            _self.$bind_alipay = $('[data-role="bind-alipay"]');
            _self.$photo_container = $('[data-role="photo-container"]');
            _self.$upload_confirm = $('[data-role="upload-confirm"]');
            _self.$photo_confirm = $('[data-role="photo-confirm"]');
            _self.$id_pic = $('[data-role="ID-pic"]');
            _self.$name = $('[data-role="account-name"]');
            _self.$alipay = $('[data-role="account-alipay"]');
            _self.$confirm = $('[data-role="confrim"]');

            if(_self.$uploader[0])
            {
                // 初始化上传组件
                _self.uploader_obj = uploader.render(_self.$uploader[0],{});

                _self.shili_img = 'http://yp.yueus.com/mobile/images/pai/id_demo.png';

                // 安装事件
                self._setup_event();
            }


        },
        _setup_event : function()
        {
            var self = this;

            // 切换上传图片的界面
            _self.uploader_obj.$el.on('click:upload_flag',function(ev)
            {

                if(!_self.uploader_obj.get().length)
                {
                    // 重新清除已经上传的图片
                    self.show_photo_page();
                }


            });

			if(App.isPaiApp)
			{
				// 调用传图
				_self.$upload_confirm.on('click',function()
				{
					_self.uploader_obj.upload_action
					({
						callback : function(pic_list)
						{
							if(pic_list.length>0)
							{
								setTimeout(function()
								{
									var img = pic_list[0];

									img = utility.matching_img_size(img,640);

									_self.upload_success_img = img;

									_self.$id_pic.css('background-image','url('+img+')');

									_self.$photo_confirm.removeClass('disabled');
								},1000);
							}

						}
					});
				});
			}
			else
			{
				_self.uploader_obj.$el.on('upload:success',function(args,pic_list)
				{
					if(pic_list)
					{
						setTimeout(function()
						{
							var img = pic_list;

							img = utility.matching_img_size(img,640);

							_self.upload_success_img = img;

							_self.$id_pic.css('background-image','url('+img+')');

							_self.$photo_confirm.removeClass('disabled');
						},1000);
					}
				});
			}

            

            // 确认图片
            _self.$photo_confirm.on('click',function()
            {
                if(_self.$photo_confirm.hasClass('disabled'))
                {
                    return;
                }

                _self.uploader_obj = uploader.render(_self.$uploader[0],
                    {
                        upload : true,
                        img_url : _self.upload_success_img,
                        width : '90px',
                        height : '90px'
                    });

                self.hide_photo_page();
            });

            //提交绑定信息
            _self.$confirm.on('click',function()
            {
                var third_account = _self.$alipay.val();
                var real_name = _self.$name.val();
                var pic = _self.uploader_obj.get()[0];
                var error_msg = '';

                if(!real_name)
                {
                    error_msg = '请填写你的姓名';
                }
                else if(!third_account)
                {
                    error_msg = '请填写支付宝账号';
                }
                else if(!pic)
                {
                    error_msg = '请上传图片';
                }

                if(error_msg)
                {
                    $.tips
                    ({
                        content:error_msg,
                        stayTime:3000,
                        type:'warn'
                    });

                    return;
                }


                self.submit
                ({
                    third_account : third_account,
                    real_name : real_name,
                    pic : pic,
                    type : 'bind_act'
                });
            });

            //提交请求成功
            _self.on('submit:success',function(e,res)
            {
                if(res.code>0)
                {
                    window.location.href = window.location.href;
                }
                else
                {
                    $.tips
                    ({
                        content:res.msg,
                        stayTime:3000,
                        type:'warn'
                    });
                }
            });

            _self.on('submit:error',function(e,res)
            {
                $.tips
                ({
                    content:'网络异常',
                    stayTime:3000,
                    type:'warn'
                });
            });
        },
        refresh : function()
        {
            window.location.href = window.location.href;
        },
        show_photo_page : function()
        {
            var self = this;

            _self.$bind_alipay.addClass('fn-hide');
            _self.$photo_container.removeClass('fn-hide');
            _self.$photo_confirm.addClass('disabled');
            _self.$id_pic.css('background-image','url('+_self.shili_img+')');

        },
        hide_photo_page : function()
        {
            var self = this;

            _self.$bind_alipay.removeClass('fn-hide');
            _self.$photo_container.addClass('fn-hide');
        },
        submit : function(params)
        {
            var self = this;

            self._sending = false;

            if(self._sending)
            {
                return;
            }

            utility.ajax_request
            ({
                url : window.$__config.ajax_url.bind_alipay,
                data : params,
                beforeSend : function()
                {
                    _self.$loading = $.loading
                    ({
                        content:'发送中...'
                    });
                },
                success : function(response)
                {
                    self._sending = false;

                    _self.$loading.loading("hide");

                    var res = response.result_data;

                    _self.trigger('submit:success',res);
                },
                error : function()
                {

                    self._sending = false;

                    _self.$loading.loading("hide");

                    _self.trigger('submit:error',res);

                    $.tips
                    ({
                        content:'网络异常',
                        stayTime:3000,
                        type:'warn'
                    });
                }
            });
        }
    };


    var bind_alipay_obj = new bind_alipay_class();


})($,window);