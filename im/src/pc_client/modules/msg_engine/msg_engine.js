/**
 * Created by nolest on 15/10/9.
**/

// ========= 模块引入 =========
var $ = require('jquery');



var msg_engine =
{
    dialog : [],
    init: function()
    {
        console.log("engine!!");
    },
    put_msg : function(obj)
    {
        var self = this;

        var packs = obj;

        self._storage(packs);
//        content: "88"
//        img: "http://static.yueus.com/im/client/test/static/pc/image/pai/d9.jpg"
//        media_type: "text"
//        notice_id: 2538272
//        send_time: 1446098123
//        send_user_id: "100029"
//        send_user_role: "yuebuyer"
//        to_user_id: "100001"
    },
    get_msg : function(type,str)
    {
        var self = this;

        var res = [];
        //type : who_to_who...
        switch (type)
        {
            case 'who_to_who' : res = self._get_storage('who',str);break;
        }

        return res
    },
    _get_storage : function(type,str)
    {
        var self = this;

        var res = [];

        switch (type)
        {
            case 'who' :
                $.each(self.dialog,function(i,obj)
                {
                    if(obj.who == str || obj.who_reverse == str)
                    {
                        res.push(obj);
                    }
                });break;
        }
        return res
    },
    _storage : function(obj)
    {
        var self = this;

        var packs = obj;

        //条件增加 统一seller+buyer+role 作为who值，发送也一样。
        var who = packs.send_user_id + packs.to_user_id// + packs.send_user_role

        var who_reverse = packs.to_user_id + packs.send_user_id
            self.dialog.push
        ({
            who : who,
            who_reverse : who_reverse,
            direct : packs.send_user_role,//发送方向 从谁发送
            pack : packs
        })
    }

}

return msg_engine;