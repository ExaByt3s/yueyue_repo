/**
 * @desc:   用Jquery的方法，无刷新页面，编辑表格
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/19
 * @Time:   14:49
 * version: 1.0
 */
$(function() {
//给页面中有bigclassname类的标签上加上click函数
    $(".bigclassname").click(function() {

        var objTD = $(this);

//先将老的类别名称保存起来,并用trim方法去掉左右多余的空格
        var oldText = $.trim(objTD.text());

//构造一个input的标签对象（作用是为了让这个input失效，不然点击多次后，文字会消失）
        var input = $("<input type='text' value='" + oldText + "' />");

//当前td的内容变为文本框，并且把老类别名放进去
        objTD.html(input);

//设置文本框的点击事件失效
        input.click(function() {
            return false;
        });

//设置文本框样式，让界面显示的人性化点
        input.css("font-size", "16px");
        input.css("text-align", "center");
        input.css("background-color", "#ffffff");
        input.width("120px");

//自动选中文本框中的文字
        input.select();

//文本框失去焦点时重新变为文本
        input.blur(function() {

//获得新输入的类别名
            var newText = $(this).val();

//用新的类别名文字替换之前变过来的输入框状态
            objTD.html(newText);

//获取该类别名所对应的ID(bigclassid)
            var bigclassid = objTD.attr("data-id");

//将新的类别名进行转码，不然界面以及数据库显示的都是"???"这样的乱码
            //newText = escape(newText);
            newText = encodeURIComponent(newText);

//获取要传到"一般处理文件"(update_bigclassname_2.php)中的URL
            var url = "index.php?id=" + bigclassid + "&apply_for_name=" +newText+"&act=update";

//AJAX异步更改数据库,data为成功后的回调返回值，用于显示提示信息
            $.post(url, function(data) {});

        });
    });
    });
