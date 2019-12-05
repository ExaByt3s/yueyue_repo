define('common/page_control/index', function(require, exports, module){ 


module.exports =
{
    hash_list : [],
    reg_hash : function(arr)
    {
        var self = this;

        for(var i = 0;i<arr.length;i++)
        {
            if(typeof arr[i].func != 'function')throw 'params[1] is not a function';

            self.hash_list.push({hash:arr[i].hash,func:arr[i].func});
        }
        self.hash_event();
    },
    hash_event : function()
    {
        var self = this;

        window.onhashchange = function()
        {
            console.log(window.location.href)
            for(var i = 0;i<self.hash_list.length;i++)
            {
                if(window.location.hash == self.hash_list[i].hash)
                {
                    self.hash_list[i].func();
                }
            }
        }
    }
} 
});