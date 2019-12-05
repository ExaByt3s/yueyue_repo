<!--<?php
header("Content-Type: text/html; charset=utf-8");
?>-->
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;" charset="utf-8" />
<title>POCO在线编辑器</title>
	<script type="text/javascript" src="js/sea.js"></script>
	<script src="js/editor/lib/codemirror.js"></script>	
	<script src="js/editor/mode/javascript/javascript.js"></script>
	<script src="js/editor/mode/php/php.js"></script>
	<script src="js/editor/addon/edit/matchbrackets.js"></script>
	<script src="js/editor/mode/htmlmixed/htmlmixed.js"></script>
	<script src="js/editor/mode/xml/xml.js"></script>
	<script src="js/editor/mode/clike/clike.js"></script>	
	<script src="js/editor/addon/hint/show-hint.js"></script>	
	<script src="js/editor/addon/hint/php-hint.js"></script>
	<script src="js/editor/addon/comment/comment.js"></script>

	<link rel="stylesheet" href="js/editor/lib/codemirror.css">
	<link rel="stylesheet" href="css/reset.css">
	<link rel="stylesheet" href="js/editor/addon/hint/show-hint.css">


	<link rel="stylesheet" href="js/editor/theme/lesser-dark.css">		
	

	<style type="text/css">
		body{backface-visibility: hidden;transform: translate3d(0,0,0);/* Turning off margin fixes this issue*//* margin: 0;*/}
		.squareMenu{height: 45px;min-width: 1000px;width: 100%;background-color: #343B3D;color: white;font-size: 18px;-webkit-box-shadow: 1px 1px 5px rgba(0, 0, 0, .2);box-shadow: 1px 1px 5px rgba(0, 0, 0, .2);line-height: 45px;}
		.squareMenu li{background-color: #3C4345;}
		.squareMenu li{float: left;line-height: 40px;padding: 0 20px;cursor: pointer;}
		.squareMenu a{color: #3DC2FF;}
		.squareMenu .focus a, .squareMenu a:hover,.squareMenu a:active{color: #fff;}		

		#sub{background:#097696;font-family:'微软雅黑';color: #fff;}
		#sub:active{background:#3DC2FF;}
		#sub:hover{background:#3DC2FF;}

		/*loading anmatie*/.spinner{margin: 100px auto;width: 60px;height: 30px;text-align: center;font-size: 10px;}
		.spinner > div{background-color: #fff;height: 100%;width: 6px;display: inline-block;-webkit-animation: stretchdelay 1.2s infinite ease-in-out;animation: stretchdelay 1.2s infinite ease-in-out;-webkit-box-shadow: 0 1px 1px rgba(0,0,0,.2);box-shadow: 0 1px 1px rgba(0,0,0,.2);}
		.spinner .rect2{-webkit-animation-delay: -1.1s;animation-delay: -1.1s;}
		.spinner .rect3{-webkit-animation-delay: -1.0s;animation-delay: -1.0s;}
		.spinner .rect4{-webkit-animation-delay: -0.9s;animation-delay: -0.9s;}
		.spinner .rect5{-webkit-animation-delay: -0.8s;animation-delay: -0.8s;}
		@-webkit-keyframes stretchdelay{0%, 40%, 100%{-webkit-transform: scaleY(0.4)}
		20%{-webkit-transform: scaleY(1.0)}}@keyframes stretchdelay{0%, 40%, 100%{transform: scaleY(0.4);-webkit-transform: scaleY(0.4);}
		20%{transform: scaleY(1.0);-webkit-transform: scaleY(1.0);}}
		.loading{display: none;position: absolute;width: 300px;height: 100px;left: 50%;top:50%;margin: -50px 0 0 -150px}
		pre{color: #ff9e59;font-family: 微软雅黑;}

		.CodeMirror-wrap{}

		.result{overflow: auto;color: #fff;}

		.border_line {border-top: 1px solid #ccc;position:absolute;top:0;height: 7px;width: 100%;left: 0;}
		
	</style>
</head>
<body>
<div class="wrapper" style="position: relative;font-family:'微软雅黑';">
	<div class="top_bar">
		<div class="squareMenu clearfix">
	      <ul>
	        <li>
	        	<div data-hide-tag style="display:none" >
					<input id="sub" type="button" value="执行程序" style="height:30px; width:150px;margin-left:10px; ">
					<label style="margin-left:10px;">调试：<input data-debug type="checkbox" name="_debug" value="1"></label>
					<label style="margin-left:10px;display: none">清缓存：<input data-no-cache type="checkbox" name="_no_cach" value="1"></label>
					<label style="margin-left:10px;">E_ALL：<input data-e-all type="checkbox" name="_e_all" value="1"></label>
					<label style="margin-left:10px;">实时：<input data-relatime type="checkbox" name="realtime" value="1" checked=""></label>
				</div>
	        </li>
	      	<li >
	      		<a href="javascript:void(0)">开发语言(Php)</a>	      		
	      	</li>
	        <li style="display:none">
	        	<a href="javascript:void(0)" >选择主题</a>
	        	<select id="select">
	        		<option value="lesser-dark">lesser-dark</option>
	        		<option value="solarized dark">solarized dark</option>
				    <option value="monokai">monokai</option>
				    <option value="default">default</option>
				    <!--
				    <option value="3024-day">3024-day</option>
				    <option value="-night">-night</option>
				    <option value="ambiance">ambiance</option>
				    <option value="base16-dark">base16-dark</option>
				    <option value="base16-light">base16-light</option>
				    <option value="blackboard">blackboard</option>
				    <option value="cobalt">cobalt</option>
				    <option value="eclipse">eclipse</option>
				    <option value="elegant">elegant</option>
				    <option value="erlang-dark">erlang-dark</option>				    
				    <option value="mbo">mbo</option>
				    <option value="mdn-like">mdn-like</option>
				    <option value="midnight">midnight</option>
				    <option value="neat">neat</option>
				    <option value="night">night</option>
				    <option value="paraiso-dark">paraiso-dark</option>
				    <option value="paraiso-light">paraiso-light</option>
				    <option value="pastel-on-dark">pastel-on-dark</option>
				    <option value="rubyblue">rubyblue</option>				    
				    <option value="solarized light">solarized light</option>
				    <option value="the-matrix">the-matrix</option>
				    <option value="tomorrow-night-eighties">tomorrow-night-eighties</option>
				    <option value="twilight">twilight</option>
				    <option value="vibrant-ink">vibrant-ink</option>
				    <option value="xq-dark">xq-dark</option>
				    <option value="xq-light">xq-light</option>	
				    -->			    
				</select>
	        </li>
	        <li>
	        	<a class="clear_btn" href="javascript:void(0)" >清空输出</a>
	        </li>
	        <li>
	        	<a  href="javascript:void(0)" >Ver <span data-verson>0</span> 暂支持php</a>
	        </li>
	      </ul>		  
	     </div>
	</div>
	<div class="main_container" data-hide-tag style="display:none">
		<table cellspacing="0" cellpadding="0" border="0" width="100%" >
			<tr>
				<td width="100%" valign="top"  >					
				<textarea id="txtarea" class="txtarea" name="code" wrap="off" onkeydown="return catchTab(this,event)">define('runcode', 1);
</textarea>
			</td >			
			</tr>
			<tr>
				<td width="100%" valign="top" >				
					<div data-result_code style="border-top: 1px solid #ccc">			
						<div class="result" style="position:relative;" >
							
							<div style="margin:10px 0 10px 37px">
								<h3 style="color:#f90">Ver <span data-verson>0</span></h3>
								<h3 style="color:#f90">新增功能</h3>
								<ol style="list-style-type: decimal;">
									<li>修复中文编码问题</li>
									<li>新增语法提示功能，使用<span style="color:#f90">Ctr+←</span>即可。PS：很多热键组合都冲突，所以只想到这个。</li>
									<li>复制黏贴的Bug。</li>
									<li>新增注释功能，添加和取消注释都是使用 <span style="color:#f90">Ctr+/</span>即可</li>
									<li>添加执行程序热键，使用<span style="color:#f90">Ctr+Enter</span>即可</li>
									<li>支持调试、E_ALL功能。<br></li>									
								</ol>
								<h3 style="color:#f90">Bug</h3>
								<ol style="list-style-type: decimal;">
									<li>当前版本目前只支持php，迟下就有javascript版本的了，等等啊。。。</span>即可。PS：很多热键组合都冲突，所以只想到这个。</li>
									<li>加载速度比较慢，代码都还没怎么去优化 →_→ 有空再说了</li>
									<li>语法提示还有完善交互啊。。</li>
									<li>虽然现在基本够用了，但还有很多bug。。。啊T_T</li>									
								</ol>
								
							</div>				
						</div>
						
				   </div>
				</td>
			</tr>
		</table>		
	</div>
	
	<div data-loaing class="loading">
		<div class="spinner">
		  <div class="rect1"></div>
		  <div class="rect2"></div>
		  <div class="rect3"></div>
		  <div class="rect4"></div>
		  <div class="rect5"></div>
		</div>
	</div>
</div>


</body>

</html>
<script type="text/javascript">
<!--
var noCachePrefix = 'seajs-ts='
//noCacheTimeStamp = noCachePrefix + 'iii';
var noCacheTimeStamp = noCachePrefix + new Date().getTime()

var verson = '0.1.2'

seajs.config
({
	map: [
		[/^.*$/, function(url) {
		  if (url.indexOf(noCachePrefix) === -1) {
			url += (url.indexOf('?') === -1 ? '?' : '&') + noCacheTimeStamp
		  }
		  return url
		}]
	  ],
	alias: 
	{		
		'jquery' : 'base/jquery.min',
		'zepto' : 'base/zepto.min',		
		'ua' : 'base/ua',

    },
	preload : ['base/plugin-text']
});

	seajs.use('zepto',function(a)
	{		
		var $ = a;

		var show_result_tag = false		
		
	 	var myTextArea = $('#txtarea');
	 	
	 	//var select_theme = window.localStorage.getItem('theme')?window.localStorage.getItem('theme'):$('#select').find('option').first().val()
	 	
	 	var select_theme = 'lesser-dark'

	 	$('[data-verson]').html(verson)
	 	
		
		$("#select option[value='"+select_theme+"']").attr("selected",true);
		
		$('[data-hide-tag]').show()	 		 			 	

	    window.enabled = true;
	  	function enableEditor() {
		  window.editor = CodeMirror.fromTextArea(myTextArea[0], {
		  	lineNumbers: true,
			mode: 'application/x-httpd-php-open',
			theme : select_theme, 			
			indentWithTabs: true,
			enterMode: "keep",
			smartIndent: true,
			fixedGutter : true,			
			styleActiveLine: true,
    		matchBrackets: true,  
    		autoMatchParens: true,
    		extraKeys: 
    		{
    			"Ctrl-Left": "autocomplete",
    			'Cmd-/' : 'toggleComment',
    			'Ctrl-/' : 'toggleComment'
    		},
    		lineWrapping: true, //是否自动换行
			onCursorActivity: function() {
				editor.setLineClass(hlLine, null);
				if (!editor.somethingSelected()) hlLine = editor.setLineClass(editor.getCursor().line, "activeline");
			}
		  });
		  CodeMirror.defineMIME("application/x-httpd-php-open", {name: "php", phpOpen: true}); 


		  
		}
	
		enableEditor();					


		$('.result').css({'background-color':$('.CodeMirror').css('background-color')})

	 	set_size()
		
		window.onresize = function()
		{
			set_size()			
				    
		}		

		
	 	$('#select').change(function()
	 	{
	 		var theme = $(this).val()

	 		window.editor.setOption("theme", theme);

	 		window.result_editor.setOption("theme", theme);

	 		window.localStorage.setItem('theme',theme)
	 		$('.result').css({'background-color':$('.CodeMirror').css('background-color'),'border-left':'1px solid #494949'})
	 	})

	 	$('#sub').click(function()
	 	{	 		
	 		
	 		run_code_action()
	 		
	 	})
	 	
	 	$('.clear_btn').click(function()
	 	{
	 		$('.result').html('');
	 	})
	 	

	 	/*
	 	$('.border_line')[0].ondragenter = function(e)
	 	{  		 

	 		//相当于onmouseover  
  
	        var margin_val = 45	 

	        var x = e.x
	        var y = e.y

	        console.log(y)

	 		var c_height = parseInt((window.innerHeight-margin_val))
	
	 		$('.CodeMirror').height(parseInt(c_height*0.4))
			$('.CodeMirror-gutters').height(parseInt(c_height*0.4))
			$('.result').height(parseInt(c_height*0.6))  
	          
	    };
	    */  

	 	var loading_tag = false;

	 	$(document).keydown(function(event)
	 	{
			if(event.keyCode==13 && event.ctrlKey && !event.shiftKey && !event.altKey) 
			{								

		    	run_code_action()
		  	}
		});

	 	function run_code_action(finish_run,error)
	 	{	 		
	 		if(loading_tag)	
			{
				return 
			}

			loading_tag = true;

	 		$('[data-loaing]').show();

	 		$('#sub').attr('disabled',true);
	 		$('#sub').val('运行中...')

	 		var realtime = 0
	 		var _debug = 0
	 		var _e_all = 0;

	 		if($('[data-realtime]').attr("checked"))
	 		{
	 			realtime = $('[data-realtime]').val()
	 		}

	 		if($('[data-debug]').attr("checked"))
	 		{
	 			_debug = $('[data-debug]').val()
	 		}
	 		
	 		if($('[data-e-all]').attr("checked"))
	 		{
	 			_e_all = $('[data-e-all]').val()
	 		}

	 		send_request
		 	({
		 		url : 'http://www.yueus.com/yue_ui/coder/action/tools_action.php',
		 		type : 'POST',
		 		data : 
		 		{
		 			text : encodeURIComponent(window.editor.getValue()),
		 			realtime : realtime,
		 			_debug : _debug,
		 			_e_all : _e_all,
		 		},
		 		callback : function(data)
		 		{
		 			if(typeof finish_run == 'function')
		 			{

		 				finish_run.call(this,data)		 			
		 				
		 			}		 			

		 			$('.result').html(data);	 			
		 			
		 			$('[data-loaing]').hide();

		 			$('#sub').removeAttr('disabled');
	 				$('#sub').val('执行程序')

	 				loading_tag = false
		 		},
		 		error : function(data)
		 		{
		 			show_result_tag = false;

		 			if(typeof error == 'function')
		 			{
		 				error.call(this,data)
		 					 				
		 			}

		 			$('.result').html(data);

	 				$('[data-loaing]').hide();

	 				$('#sub').removeAttr('disabled');
	 				$('#sub').val('执行程序')

	 				loading_tag = false
		 		}
		 	})
	 	}	 		 

	 	function set_size()
	 	{
	 		var margin_val = 45	 

	 		var c_height = parseInt((window.innerHeight-margin_val))
	
	 		$('.CodeMirror').height(parseInt(c_height*0.35))
			$('.CodeMirror-gutters').height(parseInt(c_height*0.35))
			$('.result').height(parseInt(c_height*0.65))

			$('.CodeMirror').width(window.innerWidth)
			$('.result').width(window.innerWidth)
			
	 	}



	 	function send_request(options)
		{
			var options = options || {}
			var url = options.url || ""
			var type = options.type || "GET"
			var data = options.data || {}
			var callback = options.callback || ""
			var error = options.error || ""
			
			var merge_data = $.extend(data , {t : parseInt(new Date().getTime())})

			$.ajax
			({
				type: type,
				url : url,
				data: merge_data,
				dataType : "text",
				success : function(data)
				{                    
					if(typeof(callback)=="function")
					{
						callback.call(this,data)
					}
				},
				error : function()
				{
					

					if(typeof(error)=="function")
					{
						error.call(this)
					}
				}
			})
		}
	})

//-->
</script>
