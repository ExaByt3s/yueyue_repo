<?php

$is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android') ? true : false;
$is_iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') ? true : false;  

if($is_android || $is_iphone)
{
	$url = "http://app.yueus.com/";
		if($_SERVER["QUERY_STRING"]) $url .= '?' . $_SERVER["QUERY_STRING"];
	header("Location:{$url}");
	exit;
}

$jump_off = stripos($_SERVER['HTTP_HOST'], 'selltime.cn') ? true : false;
if($jump_off)
{
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: http://www.yueus.com/');
		exit();
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
		<meta property="qc:admins" content="34114622376155536375" /> 
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
		<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
		<title>ԼԼ--���Ч��ʱ�����ƽ̨</title>
		<meta name="keywords" content="ԼԼ��ģ�أ���Ӱʦ��Լ�ģ�˽�ģ����ģ���Ӱ������" />
		<meta name="Description" content="ԼԼ��������O2O��Ʒ�������ÿ����Ů�������Լ�������ʱ�䣬�����������֡����û����ý��׸����ģ�˽�Ļ���ȷ����Ӱʦ��ģ����ʵ��ͨ����ά����������Ӱʦ���Լ����ĵ�ģ�أ�֧��һ��Լ�����ĴӴ˴������ٹµ���" />
		<link href="css/zt-3.css?32" type="text/css" rel="stylesheet" />
		<script src="js/jquery-1.8.3.min.js"></script>
		<script src="js/jquery.fullPage.min.js"></script>
		<script src="js/jquery.easing.min.js"></script>
		<script type="text/javascript" src="js/jquery.carouFredSel-5.5.0-auto.js"></script>
		
		<script>
		$(function(){
				$('#dowebok').fullpage({
						sectionsColor: ['#3d3634', '#404135', '#f2f2f2', '#333333'],
						anchors: ['section1', 'section2', 'section3', 'section4'],
						menu: '#page'
				});
		});
		</script>

</head>



<body>

	<div class="yue-new-page font_wryh">
		<div class="nav-bar">
		    <div class="container w1000 clearfix">
		      <h2 class="logo"><a href="http://www.yueus.com/" target="_blank"></a></h2>
		      <div class="contact">
			        <span><a href="http://s.yueus.com/">�����̼�</a></span>
			        |
			        <span>�ͷ��绰��4000-82-9003</span>
			        |
			        <span class="code-nav-item"><a href="#this">΢�Ź��ں�</a>
			               <!--΢�ŵ�����-->
				               <div class="pop-weixin"> <i class="top-icon"></i>
					                 <div class="pop-con font_wryh circle-5px clearfix">
						                   <div class="s-item item">
							                     <div class="img"><img src="images/s-code-img-150x150.png?v2"></div>
							                     <p>��עԼԼ���ں�</p>
							                   </div>

						             <!--       <div class="m-item item">
						                     	<div class="img"><img src="images/m-code-img-150x150.png"></div>
						                    	 <p>����ģ�أ���Ҫ����!</p>
						                   </div> -->

					                 </div>
				               </div>
			               <!--΢�ŵ����� end-->
			        </span>
		       <!--  <span><a href="#">����ʦ��̳</a></span>|
		        <span><a href="#">΢�Ź��ں�</a> 
		        </span> -->
		      </div>
		    </div>
		  </div>
		<div class="yue-new-wrapper" id="dowebok">
			<div class="section section1" >
				<div class="container">
					<div class="content">
						<!--����-->
						<div class="path1 clearfix">
							<div class="iphone-item"></div>
							<div class="text-item">
								<div class="listItem"></div>
								<div class="download-item">
									<h3 class="title">ɨ���ά�룬��������</h3>
									<div class="code-item clearfix">
										<div class="code-img circle-5px"><img src="images/download_V2/code-img-116x116.jpg" /></div>
										<div class="btn-item">
											<a href="http://app.yueus.com/download_iphone.php"  class="ui-btn ui-btn-bf iphone-btn">
												<i class="icon"></i><em>iPhone������</em>
											</a>
											<a href="http://app.yueus.com/download_android.php" class="ui-btn ui-btn-bf android-btn">
												<i class="icon"></i><em>Android������</em>
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!--���� end-->


					</div>
					<div class="next_btn">
							<span class=""  style="display:none" id="ie_allow"><img src="images/allow.gif" /></span>
							<span class="allow "  style="display:none"  id="gg_allow" style="display:none"><img src="images/next-btn-42x22.png" /></span>
					</div>
				</div>
			</div>
			<div class="section section2" >
				<div class="container">
					<div class="content">
						<!--����-->
						<div class="path2 clearfix">
							<div class="scrollBox">
								<ul class="img_list clearfix" id="foo2">
									  <li class="banner1">
									  	<div class="text-wrap clearfix">
									  		<div class="phone-item"></div>
									  		<div class="txt-item">
									  			<div class="title-con clearfix">
									  				<i class="icon model-icon"></i>
									  				<div class="txt-con">
									  					<h3 class="title">Լģ��</h3>
									  					<p>ԼԼ���֣���������</p>
									  				</div>
									  			</div>
									  			<div class="text-con">
									  				<p class="txt">�ر��<i class="line-icon"> </i><span>100000+ģ������Լ</span>
									  				</p>
									  				<p class="txt">�ر���<i class="line-icon"> </i><span>98% �û����޵�Լ�Ĺ���</span></p>
									  				<p class="txt">�ر���<i class="line-icon"> </i><span>�״�ģ��˽��ģʽ</span></p>
									  				<p class="txt">�ر�ֵ<i class="line-icon"> </i><span>Լ�Ա�ģ�ؿɻ�ø��ಹ��</span></p>
									  			</div>
									  		</div>
									  	</div>	
									  </li>
									  <li class="banner2">
									  	<div class="text-wrap clearfix">
									  		<div class="phone-item"></div>
									  		<div class="txt-item">
									  			<div class="title-con clearfix">
									  				<i class="icon shoot-icon"></i>
									  				<div class="txt-con">
									  					<h3 class="title">��ҵ����</h3>
									  					<p>ԼԼ���֣����ز���</p>
									  				</div>
									  			</div>
									  			<div class="text-con">
									  				<p class="txt"> ����ȫ<i class="line-icon"> </i><span>��ʮ����ǧƽ��Ӱ��Ӧ�о���</span></p>
									  				<p class="txt">������<i class="line-icon"> </i><span>������Ӱ���������㴴����������</span></p>
									  				<p class="txt">�ظ�Ч<i class="line-icon"> </i><span>��ʱ�η�����ԤԼʡȥ��ͨ�鷳</span></p>
									  				<p class="txt">������<i class="line-icon"> </i><span>һվʽ������������ѡ��</span></p>
									  			</div>
									  		</div>
									  	</div>	
									  </li>
									 <li class="banner3">
									  	<div class="text-wrap clearfix">
									  		<div class="phone-item"></div>
									  		<div class="txt-item">
									  			<div class="title-con clearfix">
									  				<i class="icon makeup-icon"></i>
									  				<div class="txt-con">
									  					<h3 class="title">Լ��ױ</h3>
									  					<p>ԼԼ���֣���������</p>
									  				</div>
									  			</div>
									  			<div class="text-con">
									  				<p class="txt"> ��רҵ<i class="line-icon"> </i><span>ҵ�����ױ����ʦ������</span></p>
									  				<p class="txt">�طḻ<i class="line-icon"> </i><span>����ױ�ݣ�������ױʦ��ʱԤԼ</span></p>
									  				<p class="txt">������<i class="line-icon"> </i><span>��ǧ�ֻ�ױƷƷ������ָ��</span></p>
									  				<p class="txt">�ط���<i class="line-icon"> </i><span>��פ��ױʦ�������ϸ��������</span></p>
									  			</div>
									  		</div>
									  	</div>	
									  </li>
									  <li class="banner4">
									   	<div class="text-wrap clearfix">
									   		<div class="phone-item"></div>
									   		<div class="txt-item">
									   			<div class="title-con clearfix">
									   				<i class="icon active-icon"></i>
									   				<div class="txt-con">
									   					<h3 class="title">Լ�</h3>
									   					<p>ԼԼ���֣�˵�߾���</p>
									   				</div>
									   			</div>
									   			<div class="text-con">
									   				<p class="txt">�ػ�<i class="line-icon"> </i><span>һ��ƽ̨����ת�������Ļ</span></p>
									   				<p class="txt">�ر��<i class="line-icon"> </i><span>һ��֧�����ɿ���һ��</span></p>
									   				<p class="txt">������<i class="line-icon"> </i><span>��ʦͬ���㴴�������㲻��µ�</span></p>
									   				<p class="txt">��ʡ��<i class="line-icon"> </i><span>������ĵ���������������</span></p>
									   			</div>
									   		</div>
									   	</div>	
									   </li>
									   <li class="banner5">
									    	<div class="text-wrap clearfix">
									    		<div class="phone-item"></div>
									    		<div class="txt-item">
									    			<div class="title-con clearfix">
									    				<i class="icon train-icon"></i>
									    				<div class="txt-con">
									    					<h3 class="title">Լ��ѵ</h3>
									    					<p>ԼԼ���֣�ѧϰ����</p>
									    				</div>
									    			</div>
									    			<div class="text-con">
									    				<p class="txt">�ؿ���<i class="line-icon"> </i><span>��ʦ����פ�������û�����</span></p>
									    				<p class="txt">�ص߸�<i class="line-icon"> </i><span>���˵�ʦ����֤���ɴ�ҵ�ڵ�</span></p>
									    				<p class="txt">����Ȥ<i class="line-icon"> </i><span>�����������⣬ѧϰ��Ӱ����Ȥ</span></p>
									    				<p class="txt">�ط��<i class="line-icon"> </i><span>��ѧ�ҽ�����������ͣ������</span></p>
									    			</div>
									    		</div>
									    	</div>	
									    </li>
									    <li class="banner6">
									     	<div class="text-wrap clearfix">
									     		<div class="phone-item"></div>
									     		<div class="txt-item">
									     			<div class="title-con clearfix">
									     				<i class="icon shoot-icon"></i>
									     				<div class="txt-con">
									     					<h3 class="title">Լ��Ӱ</h3>
									     					<p>ԼԼ���֣�ר������</p>
									     				</div>
									     			</div>
									     			<div class="text-con">
									     				<p class="txt">�ش���<i class="line-icon"> </i><span>ȫ��������Ӱ������Լ</span></p>
									     				<p class="txt">�ض���<i class="line-icon"> </i><span>���������񣬼�¼��һ������</span></p>
									     				<p class="txt">����Ȥ<i class="line-icon"> </i><span>������ɫ����ר��������</span></p>
									     				<p class="txt">�ظ�Ч<i class="line-icon"> </i><span>��ʱ��˽����Ӱʦ��ͨ���㷽��</span></p>
									     			</div>
									     		</div>
									     	</div>	
									     </li>
									     <li class="banner7">
									      	<div class="text-wrap clearfix">
									      		<div class="phone-item"></div>
									      		<div class="txt-item">
									      			<div class="title-con clearfix">
									      				<i class="icon interest-icon"></i>
									      				<div class="txt-con">
									      					<h3 class="title">Լ��Ȥ</h3>
									      					<p>ԼԼ���֣���������</p>
									      				</div>
									      			</div>
									      			<div class="text-con">
									      				<p class="txt">��רҵ<i class="line-icon"> </i><span>ȫ����TOP�ĳ�����˴�����</span></p>
									      				<p class="txt">�طḻ<i class="line-icon"> </i><span>��������Ļ�����㲻������</span></p>
									      				<p class="txt">������<i class="line-icon"> </i><span>û��Լ������ֻ���벻��</span></p>
									      				<p class="txt">����Ȥ<i class="line-icon"> </i><span>�������в�֪����get���¼���</span></p>
									      			</div>
									      		</div>
									      	</div>	
									      </li>
									      <li class="banner8">
									       	<div class="text-wrap clearfix">
									       		<div class="phone-item"></div>
									       		<div class="txt-item">
									       			<div class="title-con clearfix">
									       				<i class="icon foot-icon"></i>
									       				<div class="txt-con">
									       					<h3 class="title">Լ��ʳ</h3>
									       					<p>ԼԼ���֣���ζֿ��</p>
									       				</div>
									       			</div>
									       			<div class="text-con">
									       				<p class="txt">����ζ<i class="line-icon"> </i><span>���޺óԲ�����������</span></p>
									       				<p class="txt">�ؿɿ�<i class="line-icon"> </i><span>��ʳ�����ҿʹ��������Ƽ�</span></p>
									       				<p class="txt">�ط�ʢ<i class="line-icon"> </i><span>�������ؼ����ξ�ѡ��</span></p>
									       				<p class="txt">�ر���<i class="line-icon"> </i><span>��ָһ�㣬�����µ�Ԥ��</span></p>
									       			</div>
									       		</div>
									       	</div>	
									       </li>
								</ul>
								<div class="page-wrap clearfix"> 
									<a href="#" class="pre fl" id="prev2"></a> 
									<a href="#" class="next fl" id="next2"></a> 
								</div>	
							  </div>

						</div>
						<!--���� end-->
					</div>
				</div>
			</div>
			<div class="section section3">
				<div class="container">
					<div class="content">
						<!--����-->
						<div class="path3 clearfix">
						<div class="img-item">
						<div class="img-list">
									<ul id="shenfen_wrap_img">
										<li style="display:block" class="li-bg-1"><img src="images/download_V2/approve-img-494x309.jpg?2" /></li>
										<li style="display:none" class="li-bg-2"><img src="images/download_V2/phone-1-250x424.jpg" /></li>
										<li style="display:none" class="li-bg-2"><img src="images/download_V2/phone-2-250x424.jpg?5" /></li>
										<li style="display:none" class="li-bg-2"><img src="images/download_V2/phone-3-250x424.jpg?2" /></li>
									</ul>
								</div>
							</div>
							<div class="list-item">
							<div class="listItem"></div>
							<div class="list clearfix circle-5px">
								<ul id="shenfen_wrap">
									<li class="cur">
										<div class="item item-1"><a href="#this"><i class="icon common-3-bg"></i><em>�̼�˫����֤</em></a></div>
									</li>
									<li>
										<div class="item item-2"><a href="#this"><i class="icon common-3-bg"></i><em>�״���ά��ǩ��</em></a></div>
									</li>
									<li>
										<div class="item item-3"><a href="#this"><i class="icon common-3-bg"></i><em>֧������ϵͳ</em></a></div>
									</li>
									<li>
										<div class="item item-4"><a href="#this"><i class="icon common-3-bg"></i><em>˫�����ۻ���</em></a></div>
									</li>
								</ul>
							</div>
						</div>
						</div>

						<!--���� end-->
					</div>
				</div>
			</div>
			<div class="section section4" >
				<div class="container">
					<div class="content">
						<!--����-->
						<div class="path4">
							<div class="listItem"></div>
							<div class="download-item">
								<div class="code-item clearfix">
									<div class="code-img circle-5px"><img src="images/code-img-145x145.png" /></div>
									<div class="btn-item"><a href="http://app.yueus.com/download_iphone.php" class="ui-btn ui-btn-fb iphone-btn"><i class="icon"></i><em>iPhone������</em></a><a href="http://app.yueus.com/download_android.php" class="ui-btn ui-btn-fb android-btn"><i class="icon"></i><em>Android������</em></a></div>
								</div>
							</div>
						</div>
						<!--���� end-->
					</div>
					<div class="footer"><a href="about.php">��˾����</a>|<a href="about.php#lxwm">��ϵ����</a>|<!-- <a href="#">����ʦ��̳</a>| --><span>Copyright&copy; 2003-2015 YUEUS.COM </span></div>
				</div>
			</div>
		</div>
		<!--��ҳ-->
		<ol class="page" id="page">
			<li data-menuanchor="section1" class="active"><a href="#section1"><span class="dot">1</span></a></li>
			<li data-menuanchor="section2" class=""><a href="#section2"><span class="dot">2</span></a></li>
			<li data-menuanchor="section3" class=""><a href="#section3"><span class="dot">3</span></a></li>
			<li data-menuanchor="section4" class=""><a href="#section4"><span class="dot">4</span></a></li>
		</ol>
		<!--��ҳ end-->
	</div>

<script type="text/javascript">

$(document).ready(function(b) {


		//�ж�ua  ��ʾ�·��ļ�ͷ��ť
		var Sys = {};
		var ua = navigator.userAgent.toLowerCase();
		var s;
		(s = ua.match(/rv:([\d.]+)\) like gecko/)) ? Sys.ie = s[1] :
		(s = ua.match(/msie ([\d.]+)/)) ? Sys.ie = s[1] :
		(s = ua.match(/firefox\/([\d.]+)/)) ? Sys.firefox = s[1] :
		(s = ua.match(/chrome\/([\d.]+)/)) ? Sys.chrome = s[1] :
		(s = ua.match(/opera.([\d.]+)/)) ? Sys.opera = s[1] :
		(s = ua.match(/version\/([\d.]+).*safari/)) ? Sys.safari = s[1] : 0;
		
		if(Sys.ie)
		{
				$('#ie_allow').show();
				$('.next_btn').css({
						bottom: '5px'
				});
		}
		else
		{
				$('#gg_allow').show();
		}


		// var $hl_li = $('#hl_mt li');
		// $hl_li.click(function() {
		//     $hl_li.removeClass('cur');
		//     $(this).addClass('cur');
		//     var index = $hl_li.index($(this));
		//     $('#hl_mt_img li').hide();
		//     $('#hl_mt_img li').eq(index).show();
		// });


		//��2���Զ������л�
		var $ele = $('#hl_mt');
		var $hl_li = $('#hl_mt li');
		var init =  0 ;

		//�Զ�ִ��
		function autoExt()
		{
				$hl_li.removeClass('cur');
				$('#hl_mt_img li').hide();
				if ( init >= $hl_li.length) 
				{
						init = 0 ;
				} 
				init++ ; 
				$hl_li.eq(init-1).addClass('cur');
				$('#hl_mt_img li').eq(init-1).show();
				autoTime = setTimeout(autoExt, 3000);
		} 
		autoExt();

		$ele.on('mouseover',function(){
				clearTimeout(autoTime);
		});

		$ele.on('mouseout',function(){
				clearTimeout(autoTime);
				autoTime = setTimeout(autoExt, 3000);
		});

		$hl_li.click(function() {
				var index = $hl_li.index($(this));
				$hl_li.removeClass('cur');
				$(this).addClass('cur');
				$('#hl_mt_img li').hide();
				$('#hl_mt_img li').eq(index).show();
				init = index ;
		});


		//��3���Զ������л�
		var $shenfen_wrap_li = $('#shenfen_wrap li');
		$shenfen_wrap_li.click(function() {
				$shenfen_wrap_li.removeClass('cur');
				$(this).addClass('cur');
				var index = $shenfen_wrap_li.index($(this));
				$('#shenfen_wrap_img li').hide();
				$('#shenfen_wrap_img li').eq(index).show();
		});

		var $ele2 = $('#shenfen_wrap');
		var $hl_li2 = $('#shenfen_wrap li');
		var init2 =  0 ;

		//�Զ�ִ��
		function autoExt2()
		{
				$hl_li2.removeClass('cur');
				$('#shenfen_wrap_img li').hide();
				if ( init2 >= $hl_li2.length) 
				{
						init2 = 0 ;
				} 
				init2++ ; 
				$hl_li2.eq(init2-1).addClass('cur');
				$('#shenfen_wrap_img li').eq(init2-1).show();

				autoTime2 = setTimeout(autoExt2, 3000);
		} 
		autoExt2();

		$ele2.on('mouseover',function(){
				clearTimeout(autoTime2);
		});

		$ele2.on('mouseout',function(){
				clearTimeout(autoTime2);
				autoTime2 = setTimeout(autoExt2, 3000);
		});

		$hl_li2.click(function() {
				var index = $hl_li2.index($(this));
				$hl_li2.removeClass('cur');
				$(this).addClass('cur');
				$('#shenfen_wrap_img li').hide();
				$('#shenfen_wrap_img li').eq(index).show();
				init2 = index ;
		});

		//��������
		$('#logo').click(function() {
				b.fn.fullpage.moveTo(1);
		});

		$('.next_btn').click(function() {
				b.fn.fullpage.moveTo(2);
		});

		//΢�Ź��ںŵ����� 
		$('.code-nav-item').hover(function()
		{
				$(this).addClass('code-nav-item-cur');
		},function()
		{
				$(this).removeClass('code-nav-item-cur');
		});



		$('.bigshow-nav-item').hover(function()
		{
				$(this).addClass('bigshow-nav-item-cur');
		},function()
		{
				$(this).removeClass('bigshow-nav-item-cur');
		});


		//  ����li�߶�
		var foo2_li = $('#foo2 li');

		foo2_li.css({
			height: $(window).height()
		});

		//	Scrolled by user interaction
		$('#foo2').carouFredSel({
			prev: '#prev2',
			next: '#next2',
			pagination: "#pager2",
			auto: false
		});

		

});

</script>

<script>
		var _hmt = _hmt || [];
		(function() {
				var hm = document.createElement("script");
				hm.src = "//hm.baidu.com/hm.js?9fa08589aee02a0d763d50fe4dca33cd";
				var s = document.getElementsByTagName("script")[0];
				s.parentNode.insertBefore(hm, s);
		})();
</script>



</body>
</html>   