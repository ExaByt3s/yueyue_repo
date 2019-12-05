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
		<title>约约--最高效的时间电商平台</title>
		<meta name="keywords" content="约约，模特，摄影师，约拍，私拍，外拍，摄影，交易" />
		<meta name="Description" content="约约是最美的O2O产品，在这里，每个美女都利用自己的零碎时间，用美丽来变现。信用机制让交易更安心，私聊机制确保摄影师与模特真实沟通，多维度搜索帮摄影师到自己想拍的模特，支持一人约多人拍从此创作不再孤单。" />
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
			        <span><a href="http://s.yueus.com/">我是商家</a></span>
			        |
			        <span>客服电话：4000-82-9003</span>
			        |
			        <span class="code-nav-item"><a href="#this">微信公众号</a>
			               <!--微信弹出层-->
				               <div class="pop-weixin"> <i class="top-icon"></i>
					                 <div class="pop-con font_wryh circle-5px clearfix">
						                   <div class="s-item item">
							                     <div class="img"><img src="images/s-code-img-150x150.png?v2"></div>
							                     <p>关注约约公众号</p>
							                   </div>

						             <!--       <div class="m-item item">
						                     	<div class="img"><img src="images/m-code-img-150x150.png"></div>
						                    	 <p>我是模特，我要变现!</p>
						                   </div> -->

					                 </div>
				               </div>
			               <!--微信弹出层 end-->
			        </span>
		       <!--  <span><a href="#">体验师论坛</a></span>|
		        <span><a href="#">微信公众号</a> 
		        </span> -->
		      </div>
		    </div>
		  </div>
		<div class="yue-new-wrapper" id="dowebok">
			<div class="section section1" >
				<div class="container">
					<div class="content">
						<!--内容-->
						<div class="path1 clearfix">
							<div class="iphone-item"></div>
							<div class="text-item">
								<div class="listItem"></div>
								<div class="download-item">
									<h3 class="title">扫描二维码，立即体验</h3>
									<div class="code-item clearfix">
										<div class="code-img circle-5px"><img src="images/download_V2/code-img-116x116.jpg" /></div>
										<div class="btn-item">
											<a href="http://app.yueus.com/download_iphone.php"  class="ui-btn ui-btn-bf iphone-btn">
												<i class="icon"></i><em>iPhone版下载</em>
											</a>
											<a href="http://app.yueus.com/download_android.php" class="ui-btn ui-btn-bf android-btn">
												<i class="icon"></i><em>Android版下载</em>
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!--内容 end-->


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
						<!--内容-->
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
									  					<h3 class="title">约模特</h3>
									  					<p>约约在手，正妹我有</p>
									  				</div>
									  			</div>
									  			<div class="text-con">
									  				<p class="txt">特别多<i class="line-icon"> </i><span>100000+模特随心约</span>
									  				</p>
									  				<p class="txt">特别赞<i class="line-icon"> </i><span>98% 用户点赞的约拍工具</span></p>
									  				<p class="txt">特别真<i class="line-icon"> </i><span>首创模特私聊模式</span></p>
									  				<p class="txt">特别值<i class="line-icon"> </i><span>约淘宝模特可获得更多补贴</span></p>
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
									  					<h3 class="title">商业定制</h3>
									  					<p>约约在手，场地不愁</p>
									  				</div>
									  			</div>
									  			<div class="text-con">
									  				<p class="txt"> 特齐全<i class="line-icon"> </i><span>几十到几千平方影棚应有尽有</span></p>
									  				<p class="txt">特完善<i class="line-icon"> </i><span>上万种影棚配套满足创作拍摄需求</span></p>
									  				<p class="txt">特高效<i class="line-icon"> </i><span>按时段分区域预约省去沟通麻烦</span></p>
									  				<p class="txt">特贴心<i class="line-icon"> </i><span>一站式打包拍摄服务按需选择</span></p>
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
									  					<h3 class="title">约化妆</h3>
									  					<p>约约在手，美丽永久</p>
									  				</div>
									  			</div>
									  			<div class="text-con">
									  				<p class="txt"> 特专业<i class="line-icon"> </i><span>业界资深化妆造型师量身定制</span></p>
									  				<p class="txt">特丰富<i class="line-icon"> </i><span>海量妆容，万名化妆师随时预约</span></p>
									  				<p class="txt">特任性<i class="line-icon"> </i><span>上千种化妆品品牌任你指定</span></p>
									  				<p class="txt">特放心<i class="line-icon"> </i><span>入驻化妆师均经过严格资质审核</span></p>
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
									   					<h3 class="title">约活动</h3>
									   					<p>约约在手，说走就走</p>
									   				</div>
									   			</div>
									   			<div class="text-con">
									   				<p class="txt">特火爆<i class="line-icon"> </i><span>一个平台，玩转所有外拍活动</span></p>
									   				<p class="txt">特便捷<i class="line-icon"> </i><span>一键支付即可快人一步</span></p>
									   				<p class="txt">特用心<i class="line-icon"> </i><span>大师同行陪创作，拍摄不会孤单</span></p>
									   				<p class="txt">特省心<i class="line-icon"> </i><span>无需费心到场即可享受拍摄</span></p>
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
									    					<h3 class="title">约培训</h3>
									    					<p>约约在手，学习无忧</p>
									    				</div>
									    			</div>
									    			<div class="text-con">
									    				<p class="txt">特靠谱<i class="line-icon"> </i><span>名师大咖入驻，百万用户好评</span></p>
									    				<p class="txt">特颠覆<i class="line-icon"> </i><span>人人导师，认证即可传业授道</span></p>
									    				<p class="txt">特有趣<i class="line-icon"> </i><span>周周玩乐主题，学习摄影更有趣</span></p>
									    				<p class="txt">特疯狂<i class="line-icon"> </i><span>你学我奖，奖励根本停不下来</span></p>
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
									     					<h3 class="title">约摄影</h3>
									     					<p>约约在手，专属自由</p>
									     				</div>
									     			</div>
									     			<div class="text-con">
									     				<p class="txt">特大量<i class="line-icon"> </i><span>全国海量摄影大咖轻松约</span></p>
									     				<p class="txt">特多样<i class="line-icon"> </i><span>多种拍摄风格，记录不一样的你</span></p>
									     				<p class="txt">特有趣<i class="line-icon"> </i><span>大量特色拍摄专题随心玩</span></p>
									     				<p class="txt">特高效<i class="line-icon"> </i><span>随时可私聊摄影师沟通拍摄方案</span></p>
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
									      					<h3 class="title">约有趣</h3>
									      					<p>约约在手，达人我有</p>
									      				</div>
									      			</div>
									      			<div class="text-con">
									      				<p class="txt">特专业<i class="line-icon"> </i><span>全国最TOP的潮玩达人带飞你</span></p>
									      				<p class="txt">特丰富<i class="line-icon"> </i><span>包罗万象的活动，让你不再无聊</span></p>
									      				<p class="txt">特新奇<i class="line-icon"> </i><span>没有约不到，只有想不到</span></p>
									      				<p class="txt">特有趣<i class="line-icon"> </i><span>在玩乐中不知不觉get到新技能</span></p>
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
									       					<h3 class="title">约美食</h3>
									       					<p>约约在手，滋味挚友</p>
									       				</div>
									       			</div>
									       			<div class="text-con">
									       				<p class="txt">特美味<i class="line-icon"> </i><span>搜罗好吃餐厅精心烹制</span></p>
									       				<p class="txt">特可靠<i class="line-icon"> </i><span>美食达人饕客大咖齐上阵推荐</span></p>
									       				<p class="txt">特丰盛<i class="line-icon"> </i><span>各国各地佳肴任君选择</span></p>
									       				<p class="txt">特便利<i class="line-icon"> </i><span>手指一点，轻松下单预订</span></p>
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
						<!--内容 end-->
					</div>
				</div>
			</div>
			<div class="section section3">
				<div class="container">
					<div class="content">
						<!--内容-->
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
										<div class="item item-1"><a href="#this"><i class="icon common-3-bg"></i><em>商家双重认证</em></a></div>
									</li>
									<li>
										<div class="item item-2"><a href="#this"><i class="icon common-3-bg"></i><em>首创二维码签到</em></a></div>
									</li>
									<li>
										<div class="item item-3"><a href="#this"><i class="icon common-3-bg"></i><em>支付担保系统</em></a></div>
									</li>
									<li>
										<div class="item item-4"><a href="#this"><i class="icon common-3-bg"></i><em>双向评价机制</em></a></div>
									</li>
								</ul>
							</div>
						</div>
						</div>

						<!--内容 end-->
					</div>
				</div>
			</div>
			<div class="section section4" >
				<div class="container">
					<div class="content">
						<!--内容-->
						<div class="path4">
							<div class="listItem"></div>
							<div class="download-item">
								<div class="code-item clearfix">
									<div class="code-img circle-5px"><img src="images/code-img-145x145.png" /></div>
									<div class="btn-item"><a href="http://app.yueus.com/download_iphone.php" class="ui-btn ui-btn-fb iphone-btn"><i class="icon"></i><em>iPhone版下载</em></a><a href="http://app.yueus.com/download_android.php" class="ui-btn ui-btn-fb android-btn"><i class="icon"></i><em>Android版下载</em></a></div>
								</div>
							</div>
						</div>
						<!--内容 end-->
					</div>
					<div class="footer"><a href="about.php">公司介绍</a>|<a href="about.php#lxwm">联系我们</a>|<!-- <a href="#">体验师论坛</a>| --><span>Copyright&copy; 2003-2015 YUEUS.COM </span></div>
				</div>
			</div>
		</div>
		<!--翻页-->
		<ol class="page" id="page">
			<li data-menuanchor="section1" class="active"><a href="#section1"><span class="dot">1</span></a></li>
			<li data-menuanchor="section2" class=""><a href="#section2"><span class="dot">2</span></a></li>
			<li data-menuanchor="section3" class=""><a href="#section3"><span class="dot">3</span></a></li>
			<li data-menuanchor="section4" class=""><a href="#section4"><span class="dot">4</span></a></li>
		</ol>
		<!--翻页 end-->
	</div>

<script type="text/javascript">

$(document).ready(function(b) {


		//判断ua  显示下方的箭头按钮
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


		//第2屏自动播放切换
		var $ele = $('#hl_mt');
		var $hl_li = $('#hl_mt li');
		var init =  0 ;

		//自动执行
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


		//第3屏自动播放切换
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

		//自动执行
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

		//跳到顶部
		$('#logo').click(function() {
				b.fn.fullpage.moveTo(1);
		});

		$('.next_btn').click(function() {
				b.fn.fullpage.moveTo(2);
		});

		//微信公众号弹窗口 
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


		//  设置li高度
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