//年月(单选)
function showyearbox2(inputname, parentname)
{
	if (parentname == null || parentname == '') 
	{
		parentname = "span";
	}
	$(inputname).click(function(){
		var input=$(this);
		//$(input).parent(parentname).parent().before("<div class=\"menu_bg_layer\"></div>");
		//$(".menu_bg_layer").height($(document).height());
		//$(".menu_bg_layer").css({ width: $(document).width(), position: "absolute", left: "0", top: "0" , "z-index": "0"});
		$(input).parent(parentname).css("position","relative");
		
		var myDate = new Date();
		var y=myDate.getFullYear();
		var ymin=y-65;
		htm="<div class=\"showyearbox yearlist\">";		
		htm+="<div class=\"tit\">选择年份>></div>";
		htm+="<ul>";
		for (i=y;i>=ymin;i--)
		{
		htm+="<li title=\""+i+"\">"+i+"年</li>";
		}
		htm+="<div class=\"clear\"></div>";
		htm+="</ul>";
		htm+="</div>";
		//
		/*htm+="<div class=\"showyearbox monthlist\">";
		htm+="<div class=\"tit\">选择月份>></div>";
		htm+="<ul>";
		for (i=1;i<=12;i++)
		{
		htm+="<li title=\""+i+"\">"+i+"月</li>";
		}
		htm+="<div class=\"clear\"></div>";
		htm+="</ul>";
		htm+="</div>";*/
		$(input).blur();
		if ($(input).parent(parentname).find(".showyearbox").html())
		{
			//$(input).parent(parentname).children(".showyearbox.yearlist").slideToggle("fast");
			$(input).parent(parentname).children(".showyearbox.yearlist").slideToggle("fast");
		}
		else
		{
			$(input).parent(parentname).append(htm);
			$(input).parent(parentname).children(".showyearbox.yearlist").slideToggle("fast");
		}
		//
		$(input).parent(parentname).children(".yearlist").find("li").unbind("click").click(function()
		{
			$(input).val($(this).attr("title"));
			$(input).parent(parentname).children(".yearlist").hide();
			$(input).parent(parentname).children(".monthlist").show();
			$(input).parent(parentname).children(".monthlist").find("li").unbind("click").click(function()
			{
				$(input).val($(input).val()+$(this).attr("title"));
				$(".menu_bg_layer").hide();
				$(input).parent(parentname).css("position","");
				$(input).parent(parentname).find(".showyearbox").hide();
			});	
		});	
		//
		$(".showyearbox>ul>li").hover(
		function()
		{
		$(this).css("background-color","#DAECF5");
		$(this).css("color","#ff0000");
		},
		function()
		{
		$(this).css("background-color","");
		$(this).css("color","");
		}
		);
		//
		$(".menu_bg_layer").click(function(){
					$(".menu_bg_layer").hide();
					$(input).parent(parentname).css("position","");
					$(input).parent(parentname).find(".showyearbox").hide();			
		});
	});
}