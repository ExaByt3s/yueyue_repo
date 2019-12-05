define('async_data/user_async', function(require, exports, module){ /**
 * Created by nolest on 15/11/2.
 *
 **/

// =========   =========
var $ = require('components/jquery/jquery.js');



var user_async =
{
    info : [],
    send_data : [],//发送值
    async_base : [],
    async_user_base : [],
    async_seller_user_base : [],
    async_buyer_user_base : [],
    init: function(options)
    {
        var self = this;

        self.ajax_success = options.ajax_success || function(){};
    },
    get_info : function(id_arr)
    {
        var self = this;

        if(id_arr)
        {
            self.send_data = id_arr;

            self._send_ajax(self.send_data);
        }
    },
    _send_ajax : function(data)
    {
        var self = this;


        $.ajax
        ({
            url: window.$__ajax_domain + 'get_user_info_by_id.php',
            data : {id_arr : data},
            dataType: 'json',
            type: 'POST',
            cache: false,
            beforeSend: function()
            {

            },
            success: function(data)
            {
                //储存用户信息
                $.each(data.result_data,function(i,obj)
                {
                    if(!self.async_base[obj.id])
                    {
                        self.async_base[obj.id] =
                        ({
                            id : obj.id,
                            name : obj.name,
                            icon : obj.icon
                        })
                    }
                })

                if(typeof self.ajax_success == 'function')self.ajax_success.call(this,data)
            },
            error: function(data)
            {
                //self._send_ajax(self.send_data)
            },
            complete: function()
            {

            }
        });
    },
    _send_info_ajax : function(arr)
    {
        var self = this;

        console.log("发送的东西");
        console.log(arr);
        $.ajax
        ({
            url: window.$__ajax_domain + 'get_user_info_by_id_v2.php',
            data : {arr : arr},
            dataType: 'json',
            type: 'POST',
            cache: false,
            beforeSend: function()
            {

            },
            success: function(data)
            {
                self.set_in_base(data.result_data,'ajax');
                if(typeof self.ajax_success == 'function')self.ajax_success.call(this,data)
            },
            error: function(data,xhr)
            {
                //异步获取失败的处理
                console.log(data,xhr)
            },
            complete: function()
            {

            }
        });
    },
    set_in_base : function(data,type) //'init_buyer_list','init_seller_list','ajax'
    {
        console.log("set_in_base")
        console.log(data);
        var self = this;

        for(var i in data)
        {
            switch (type)
            {
                case 'init_buyer_list':
                    var list = data[i].buyer_list
                    for(var k in list)
                    {
                        //买家列表渲染时储存买家用户数据
                        var s_obj =
                        {
                            id : list[k].custom_id,
                            icon : list[k].custom_icon,
                            name : list[k].custom_name,
                            role : list[k].send_user_role
                        };
                        if(!self.if_in_list(list[k].custom_id,self.async_user_base))
                        {
                            self.async_user_base.push(s_obj);
                        }
                        if(!self.if_in_list(list[k].custom_id,self.async_buyer_user_base))
                        {
                            self.async_buyer_user_base.push(s_obj);
                        }
                    }
                    break;
                case 'init_seller_list':
                    var list = data[i];
                    var s_obj =
                    {
                        id : list.seller_user_id,
                        icon : list.icons,
                        name : list.nickname,
                        role : 'yueseller'
                    };
                    if(!self.if_in_list(list.seller_user_id,self.async_user_base))
                    {
                        self.async_user_base.push(s_obj);
                    }
                    if(!self.if_in_list(list.seller_user_id,self.async_seller_user_base))
                    {
                        self.async_seller_user_base.push(s_obj);
                    }
                    break;
                case 'ajax':
                    var list = data;
                    for(var k in list)
                    {
                        //买家列表渲染时储存买家用户数据
                        var s_obj =
                        {
                            id : list[k].user_id,
                            icon : list[k].icon,
                            name : list[k].name,
                            role : list[k].role
                        };
                        if(!self.if_in_list(list[k].user_id,self.async_user_base))
                        {
                            self.async_user_base.push(s_obj);
                        }
                        if(!self.if_in_list(list[k].user_id,self.async_buyer_user_base))
                        {
                            self.async_buyer_user_base.push(s_obj);
                        }
                    }
                    break;
            }
        }
        console.log(self.async_buyer_user_base);
        console.log(self.async_seller_user_base);
        console.log(self.async_user_base);
    },
    if_in_list : function(id,list)
    {
        var self = this;

        var is_in = false;

        for(y in list)
        {
            if(list[y].id == id)
            {
                is_in = true
            }
        }

        return is_in
    },
    set_base_by_async : function(obj_arr)
    {
        var self = this;

        self._send_info_ajax(obj_arr);
    },
    get_user_base : function(id)
    {
        var self = this;

        var id = id;

        var res =
        {
            user_arr : [],
            seller_arr : [],
            buyer_arr : []
        };

        for(var i in self.async_user_base)
        {
            if(self.async_user_base[i].id == id)
            {
                res.user_arr.push(self.async_user_base[i]);
            }
        }

        for(var u in self.async_seller_user_base)
        {
            if(self.async_seller_user_base[u].id == id)
            {
                res.seller_arr.push(self.async_seller_user_base[u]);
            }
        }

        for(var o in self.async_buyer_user_base)
        {
            if(self.async_buyer_user_base[o].id == id)
            {
                res.buyer_arr.push(self.async_buyer_user_base[o]);
            }
        }

        return res;

    }
}

return user_async; 
});