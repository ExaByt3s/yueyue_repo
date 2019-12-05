<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
 <HEAD>
  <TITLE> 专题跳转录入 </TITLE>
  <meta http-equiv="Content-Type" content="text/html; charset=gbk2312" />
  <META NAME="Generator" CONTENT="EditPlus">
  <META NAME="Author" CONTENT="">
  <META NAME="Keywords" CONTENT="">
  <META NAME="Description" CONTENT="">
     <style>
         body
         {
             margin: 0;
             padding: 0;
             font-family: 'Microsoft YaHei', 'Verdana', 'SimSun';
         }
         #nav
         {
             width: 100%;
             background: #ffffff;
         }
         #title
         {
             margin: 0 auto;
             margin-top: 60px;
             width: 590px;
             height: 45px;
             line-height: 45px;
             text-align: left;
             font-size: 30px;
             font-weight: 700;
             color: #ff4978;

         }
         #container
         {
             margin: 0 auto;
             width: 990px;
         }
         #left
         {
             float: left;
             width: 550px;

         }
         #right
         {
             float:right;
             width: 440px;
         }
         input
         {
             display: inline-block;
             margin-top: 15px;
         }
         .notice
         {
             display: inline-block;
             width: 200px;
             text-align: right;
         }
         #output
         {
             word-break:break-all;
         }
         .btn_ok
         {
             padding: 0px;
             font-size: 20px;
             color: #ffffff;
             width: 100px;
             line-height: 40px;
             height: 40px;
             background:#ff4978;
             border: 1px solid #ffffff;
             font-family: 'Microsoft YaHei', 'Verdana', 'SimSun';
             margin-left: 100px;
             margin-right: 50px;

         }
         .btn_empty
         {
             padding: 0px;
             font-size: 20px;
             color: #ff4978;
             width: 100px;
             line-height: 40px;
             height: 40px;
             background:#ffffff;
             border: 1px solid #ff4978;
             font-family: 'Microsoft YaHei', 'Verdana', 'SimSun';
         }
         .btn_copy
         {
             padding: 0px;
             font-size: 20px;
             color: #ffffff;
             width: 100px;
             line-height: 40px;
             height: 40px;
             background:#ff4978;
             border: 1px solid #ffffff;
             font-family: 'Microsoft YaHei', 'Verdana', 'SimSun';
             margin-right: 50px;
         }
         #timer
         {
             margin-bottom: 10px;
         }
     </style>
 </HEAD>

 <BODY>
 <div id="nav">
     <div id="title">
         专题跳转录入
     </div>
 </div>
 <div id="container">
     <div id="left">
         <div class="notice"></div>
         <label class="choose-item" for="url-style_inside">
             <input type="radio" id="url-style_inside" value="inside" checked="true" name="url-style">
             内页
         </label>
         <label class="choose-item" for="url-style_outside">
             <input type="radio" id="url-style_outside" value="outside" name="url-style">
             外页
         </label>
         <label class="choose-item" for="url-style_other">
             <input type="radio" id="url-style_other" value="other" name="url-style">
             其他
         </label><BR>
         <div class="notice"></div><INPUT TYPE="text" NAME="url" id="url" value=""><BR>
         <div class="notice"></div>
         <label class="choose-item" for="no-jump_yes">
             <input type="radio" id="no-jump_yes" value="true" name="no-jump">
             跳转
         </label>
         <label class="choose-item" for="no-jump_no">
             <input type="radio" id="no-jump_no" checked="true" value="false" name="no-jump">
             不跳转
         </label><BR>

         <INPUT class="btn_ok" TYPE="button" VALUE="生成" ONCLICK="btnclick()">
         <INPUT class="btn_empty" TYPE="button" VALUE="清空" ONCLICK="btnclean()">
     </div>
     <div id="right">
         <div id="timer"></div>
         <div id="output"></div>
     </div>

<SCRIPT LANGUAGE="JavaScript">


function $(id)
{
  return document.getElementById(id);
}

function btnclean()
{
    $('url').value = '';
    $('output').innerHTML = '';
    $('timer').innerHTML = '';
}

function get_radio_value(name)
{
    var tags = document.getElementsByName(name);

    if(tags.length>0)
    {
        for(var i=0;i<tags.length;i++)
        {

            if(tags[i].checked)
            {
                return tags[i].value;
            }
        }
    }

    return '';
}

function btnclick()
{
  var cache  =
      {
          url: $('url').value,
          disable_no_jump : get_radio_value('no-jump'),
          disable_url_style : get_radio_value('url-style')
      };

if(cache.disable_url_style == 'inside')
{
	cache.url = cache.url.substring(cache.url.indexOf('#')+1);
}
    console.log(cache);

    document.getElementById('output').innerHTML = "data-role=nav-page" + " data-url-style=" + cache.disable_url_style + " data-url=" + cache.url + " data-no-jump=" + cache.disable_no_jump;

    var date = new Date();
    document.getElementById('timer').innerHTML = '' + date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();

}

//-->
</SCRIPT>

 </div>

 </BODY>
</HTML>
