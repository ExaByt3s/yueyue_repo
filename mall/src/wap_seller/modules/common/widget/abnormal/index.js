/**
 * Created by hudingwen on 15/6/2.
 * 通用提示页
 */

/**
 * @require ./main.scss
 *
 */

var $ = require('zepto');

module.exports =
{
    render: function (dom,options) {
        // tpl后缀的文件也可以用于模板嵌入，相比handlebars
        // tpl文件不具有模板变量功能，嵌入后只是作为字符串使
        // 用，tpl文件嵌入之前可以被插件压缩，体积更小。
        // handlebars由于缺少相应的压缩插件因此暂时不能在预
        // 编译阶段做压缩。选择tpl还是handlebars可以自由选
        // 择

        //console.log(css);


        options = options || {};

        var template  = __inline('./main.tmpl');

        dom.innerHTML = template(options);

        $(dom).find('[data-role="refresh-page"]').on('click',function()
        {
            window.location.href = window.location.href;
        });


    }
};