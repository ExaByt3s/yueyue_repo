Handlebars.registerHelper('compare', function(left, operator, right, options) {
    if (arguments.length < 3) {
        throw new Error('Handlerbars Helper "compare" needs 2 parameters');
    }
    var operators = {
        '==':     function(l, r) {return l == r; },
        '===':    function(l, r) {return l === r; },
        '!=':     function(l, r) {return l != r; },
        '!==':    function(l, r) {return l !== r; },
        '<':      function(l, r) {return l < r; },
        '>':      function(l, r) {return l > r; },
        '<=':     function(l, r) {return l <= r; },
        '>=':     function(l, r) {return l >= r; },
        'typeof': function(l, r) {return typeof l == r; }
    };

    if (!operators[operator]) {
        throw new Error('Handlerbars Helper "compare" doesn\'t know the operator ' + operator);
    }

    var result = operators[operator](left, right);

    if (result) {
        return options.fn(this);
    } else {
        return options.inverse(this);
    }
});

/**
 * 判断两个指是否相等 (但准确度要扩展优化)
 * @param a
 * @param b
 * @param options
 * @returns {*}
 */
Handlebars.registerHelper('if_equal', function(a,b,options) {
    if(a == b)
    {
        return options.fn(this);
    }
    else
    {
        return options.inverse(this);
    }

});

Handlebars.registerHelper('percent', function(a,b,options) {
    if (arguments.length < 2) {
        throw new Error('Handlerbars Helper "to%" needs 2 parameters');
    }

    return (Math.round((a/b)*100) + '%')

});

/**
 * 转换指定图片size，用于约约的图片
 * 例子 {{change_img_size images "260" }} ==>转换260的图
 * @param  {[type]} img_url [description]
 * @param  {[type]} size)   {               if (!img_url) {        throw new Error('Handlerbars Helper change_img_size function has no "img_url" params');    }    if (!size) {        throw new Error('Handlerbars Helper change_img_size function has no "size" params');    }    size [description]
 * @return {[type]}         [description]
 */
Handlebars.registerHelper('change_img_size', function(img_url,size) {
    if (!img_url) {
        throw new Error('Handlerbars Helper change_img_size function has no "img_url" params');
    }

    if (!size) {
        throw new Error('Handlerbars Helper change_img_size function has no "size" params');
    }    

    return __handlebars_change_img_resize(img_url,size);

});

Handlebars.registerHelper('calculate', function(a,operator,b,options) {
    if (!operator) {
        throw new Error('Handlerbars Helper "calculate" needs at lest 2 parameters');
    }

    var operators = {
        '+':     function(l, r) {return parseInt(l) + parseInt(r); },
        '-':    function(l, r) {return parseInt(l) - parseInt(r); },
        '*':     function(l, r) {return l*r; },
        '/':    function(l, r) {return l !== r; }
    };

    if (!operators[operator]) {
        throw new Error('Handlerbars Helper "compare" doesn\'t know the operator ' + operator);
    }

    var result = operators[operator](a, b);

    return result

});

//注册索引+1的helper
Handlebars.registerHelper("add_one",function(index)
{
  //利用+1的时机，在父级循环对象中添加一个_index属性，用来保存父级每次循环的索引
  this._index = index;
  //返回+1之后的结果
  return this._index;
});

// 截取指定字数字符串
Handlebars.registerHelper("cutstr",function(str,len)
{  
  return cutstr(str,len);
});

/**
 * 切换图片size
 * @param img_url
 * @param size
 * @returns {*}
 */
function __handlebars_change_img_resize(img_url,size)
{
    var size_str = '';

    size = size || '';

    size = parseInt(size,10);

    if($.inArray(size, [120,320,165,640,600,145,440,230,260]) != -1)
    {
        size_str = '_' +size;
    }
    else
    {
        size_str = '';
    }
    // 解析img_url

    var url_reg = /^http:\/\/(img|image)\d*(-c|-wap|-d)?(.poco.cn.*|.yueus.com.*)\.jpg|gif|png|bmp/i;

    var reg = /_(32|64|86|100|145|165|260|320|440|468|640).(jpg|png|jpeg|gif|bmp)/i;

    if (url_reg.test(img_url))
    {
        if(reg.test(img_url))
        {
            img_url = img_url.replace(reg,size_str+'.$2');
            
        }
        else
        {
            img_url = img_url.replace('/(\.\d*).jpg|.jpg|.gif|.png|.bmp/i', size_str+".jpg");//两个.jpg为兼容软件的上传图片名

        }
    }


    
    return img_url;
}

/**
 * 截取指定字数字符串
 * @param  {[type]} str [description]
 * @param  {[type]} len [description]
 * @return {[type]}     [description]
 */
function cutstr(str,len)
{
   var str_length = 0;
   var str_len = 0;
      str_cut = new String();
      str_len = str.length;
      for(var i = 0;i<str_len;i++)
     {
        a = str.charAt(i);
        str_length++;
        if(escape(a).length > 4)
        {
         //中文字符的长度经编码之后大于4
         str_length++;
         }
         str_cut = str_cut.concat(a);
         if(str_length>=len)
         {
         str_cut = str_cut.concat("...");
         return str_cut;
         }
    }
    //如果给定字符串小于指定长度，则返回源字符串；
    if(str_length<len){
     return  str;
    }
}


