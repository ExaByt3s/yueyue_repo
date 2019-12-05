define('loader_script/index', function(require, exports, module){ 

var $ = require('components/zepto/zepto.js');

module.exports =
{
    collect : function()
    {
        var a = $('script');

        for(var i =0;i < a.length ; i++)
        {
            console.log(a[i].src)
        }

        var xmlhttp = new XMLHttpRequest();

        xmlhttp.open("GET",a[0].src,true);

        xmlhttp.onreadystatechange = function()
        {
            if(xmlhttp.readyState==4)
                  {
                 if(xmlhttp.status==200)
                     {
                         console.log(xmlhttp.responseText);
                     }
               }
        }

        xmlhttp.send();
    }
}

 
});