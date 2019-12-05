/**
* js倒计时组件
* @authors 汤圆
* @date    2015-05-30 08:49:05
*  options 传入参数
*  _time_limit      要倒计时多少秒 
*  _ele             要倒计时的节点
*  _stop_class      停止时候的样式
*  _again_txt       重新获取的显示的文字
*  _ajax            ajax请求参数
*  counting         控制器
*/

var $ = require('jquery');

// 构造函数对象
function countdown(options) 
{
    var options = options || {} ;
    this._time_limit = options._time_limit || 60 ;
    this._ele = options._ele  ;
    this._stop_class = options._stop_class ;
    this._again_txt =  options._again_txt;
    this._phone_number_ele =  options._phone_number_ele;
    this._ajax = options._ajax;
    // 控制器
    this.counting = true;
    this._init();

}

// 添加原型链方法
countdown.prototype = 
{
    // 初始化
    _init : function () 
    {
        var _self = this ;
        _self._ele.on('click', function() {
            if (_self.counting) 
            {

                var phone_number_val = _self._phone_number_ele.val().trim();
   
                if (!phone_number_val) 
                {
                    alert('请输入手机号码');
                    return ;
                }

                // 手机号码匹配
                var phone_reg = new RegExp(/^[0-9]{11}$/);
                var new_phone_test = phone_reg.test(phone_number_val); 

                if (!new_phone_test) 
                {
                    alert('手机号码错误，请重新输入');
                    return ;
                }

                _self.obj_phone_number_val = phone_number_val;


                // 计时开始
                _self._count_down(_self._time_limit);

                // 发送请求
                _self._ajax_fn(_self._ajax);
            }
            
        })
    },

    // 计时函数
    _count_down : function (sec) 
    {
        var _self = this ;
        _self.counting = false;
        _self._ele.html(sec);
        _self._ele.addClass(_self._stop_class);
        _self.count_Interval = setInterval(function()
        {
            sec--;
            if (sec == 0)
            {
                _self._stop_count_down();
            }
            else
            {
                _self._ele.html(sec);
            }
        }, 1000)
    },


    // 清除计时器
    _stop_count_down : function() 
    {
        var _self = this;
        clearInterval(_self.count_Interval);
        _self._ele.html(_self._again_txt);
        _self.counting = true;
        _self._ele.removeClass(_self._stop_class);
    },


    // 发送请求
    _ajax_fn : function (options) 
    {
        var _self = this ;
        $.ajax({
            url: options.url || {} ,
            data: 
            {
                phone_num : _self.obj_phone_number_val
            } ,
            dataType: options.dataType || {} ,
            type: 'POST',
            cache: false,
            beforeSend : function()
            {
                options.beforeSend && options.beforeSend.call(this);
            },
            success : function(ret)
            {
                options.callback && options.callback.call(this,ret);
            },
            error : function()
            {
                options.error && options.error.call(this);
            }
        });
    }


}

return countdown ;


