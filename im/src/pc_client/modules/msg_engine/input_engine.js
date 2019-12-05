/**
 * Created by nolest on 15/10/9.
**/

// ========= 模块引入 =========
var $ = require('jquery');



var input_engine =
{
    dialog : [],
    init: function()
    {
        console.log("engine!!");
    },
    put_msg : function(str,obj)
    {
        var self = this;

        var obj =
        {
            id : str,
            storage : obj
        }

        self.dialog.push(obj)
    },
    get_msg : function(str)
    {
        var self = this;

        var res = [];

        $.each(self.dialog,function(i,obj)
        {
            if(obj.id == str)
            {
                res.push(obj);
            }
        })

        return res
    }
}

return input_engine;