<?
include_once("include/FastTemplate.php");
include_once("include/pagebar.php");
$tpl = new FastTemplate("templates");
$tpl->define(
	array(
		MAIN => "gongancheck.htm"
	)
);
$tpl->define_block(MAIN,MSG_LIST);
$pagesize=50;

$conn = mysql_connect("121.9.211.159","yy-poco","poco,.site-2004");
mysql_select_db("java_black_leach_db",$conn);
$searchconn = mysql_connect("121.9.249.25","search_engine","SearCh100401");
mysql_select_db("search_engine_db",$searchconn);
$bbsconn = mysql_connect("121.9.249.25","java_dev_group","344#!poco");
mysql_select_db("bbs_db",$bbsconn);

//print_r($_REQUEST);
//print_r($_POST);
//删除和阅读相关数据
if( ($_GET["dotype"]=="del" && $_GET["delid"]!="") || ($_POST["Senduser"]=="delallmsg" && $_POST["Submit"]=="全部删除") )
{
	//echo count($_POST["url_arr"]);
	if(count($_POST["url_arr"])>0)
	{
		for($aa=0;$aa<count($_POST["url_arr"]);$aa++)
		{
			$allids = $allids.$_POST["url_arr"][$aa].","; 
		}
		//echo "select id,index_name,user_id,act_type_id,item_id,tid,black_words from leach_item_tbl where id in ('".substr($allids,0,-1)."')";
		$checksql = mysql_query("select id,index_name,user_id,act_type_id,item_id,tid,black_words from leach_item_tbl where id in (".substr($allids,0,-1).")",$conn);
	}
	else
	{
		$checksql = mysql_query("select id,index_name,user_id,act_type_id,item_id,tid,black_words from leach_item_tbl where id ='".$_GET["delid"]."'",$conn);
	}
	//echo count(mysql_fetch_row($checksql));
	while(list($uid,$index_name,$user_id,$act_type_id,$item_id,$tid,$black_words)=mysql_fetch_row($checksql))
	{
		//echo $uid;
		if($index_name=="98")
		{
			$msgres = mysql_query("select item_title,item_summary from act_push_to_search_engine_pool_tbl where act_id='".$tid."'",$searchconn);
			list($item_title,$item_summary)=mysql_fetch_row($msgres);
		}
		else if($index_name=="101")
		{
			if($item_id<10)
			{
				$bbsres = mysql_query("select title,description from bbs_topics_tbl_0".$item_id." where tid='".$tid."'",$bbsconn);
			}
			else
			{
				$bbsres = mysql_query("select title,description from bbs_topics_tbl_".$item_id." where tid='".$tid."'",$bbsconn);
			}
			list($item_title,$item_summary)=mysql_fetch_row($bbsres);
		}
		if($act_type_id==1)
		{
			$source="博客日记";
			$inurl='http://blog1.poco.cn/myBlogDetail-htx-id-'.$item_id.'-userid-'.$user_id.'-pri-0-n-0.xhtml';
		}
		else if($act_type_id==2)
		{
			$source="我的网摘";
			$inurl='http://my.poco.cn/urldetail.htx&fav_id='.$item_id.'&ispri=2';
		}
		else if($act_type_id==3)
		{
			$source="摄影作品";
			$inurl='http://my.poco.cn/lastphoto_v2-htx-id-'.$item_id.'-user_id-'.$user_id.'-p-0.xhtml';
		}
		else if($act_type_id==4)
		{
			$source="RSS订阅";
			$inurl='http://my.poco.cn/rssr/rssr_main.php?user_id='.$user_id.'#read_channel('.$item_id.')';
		}
		else if($act_type_id==5)
		{
			$source="相册";
			$inurl='http://my.poco.cn/album/album_show_details.htx&user_id='.$user_id.'&item_id='.$item_id;
		}
		else if($act_type_id==6)
		{
			$source="美食DIY";
			$inurl='http://my.poco.cn/myBlogDetail-htx-id-'.$item_id.'-userid-'.$user_id.'-pri-0-n-0.xhtml';
		}
		else if($act_type_id==7)
		{
			$source="美食作品";
			$inurl='http://my.poco.cn/commenddetail_v2-htx-id-'.$item_id.'.shtml';
		}
		else if($act_type_id==9)
		{
			$source="视频";
			$inurl='http://my.poco.cn/plive/plive_opus_details.htx&opus_id='.$item_id;
		}
		else if($act_type_id==11)
		{
			$source="电影评论";
			$inurl='http://movie.poco.cn/commend_detail.htx&id='.$item_id;
		}
		else if($act_type_id==12)
		{
			$source="最新电影";
			$inurl='http://movie.poco.cn/newMovieDetail.htx&id='.$item_id;
		}
		else if($act_type_id==13)
		{
			$source="杂志";
			$inurl='http://my.poco.cn/z_maker/z_maker_opus_details.htx&opus_id='.$item_id.'&user_id='.$user_id;
		}
		else if($act_type_id==14)
		{
			$source="活动";
			$inurl='http://my.poco.cn/v2/party_jump_page.htx&id='.$item_id;
		}
		else if($act_type_id==21)
		{
			$source="items_图片";
			$inurl='http://my.poco.cn/items/item_details.htx&item_id='.$item_id.'&user_id='.$user_id;
		}
		else if($act_type_id==35)
		{
			$source="资源分享";
			$inurl='http://my.poco.cn/res_share/res_share_details.htx&res_group_hash='.$item_id.'&user_id='.$user_id;
		}
		else if($act_type_id==36)
		{
			$source="美食bbs";
			$inurl='http://gz.food.poco.cn/club_bbs/club_bbs_topic.htx&tid='.$item_id;
		}
		else if($act_type_id==41)
		{
			$source="图片中心";
			$inurl='http://my.poco.cn/pic/pic_details.htx&pic_group_hash='.$item_id;
		}
		else if($act_type_id==42)
		{
			$source="相片特效";
			$inurl='http://my.poco.cn/flash_album/flash_album_details.htx&flash_album_id='.$item_id.'&user_id='.$user_id;
		}
		else if($act_type_id==43)
		{
			$source="宠物社区bbs帖";
			$inurl='http://pet86.poco.cn/bbs/thread-'.$item_id.'-1-1.html';
		}
		else if($act_type_id==45)
		{
			$source="动感相册";
			$inurl='http://my.poco.cn/pzm/pzm_opus_details.htx&opus_id='.$item_id.'&user_id='.$user_id;
		}
		else
		{
			$source="论坛BBS";
			$inurl='http://bbs.poco.cn/topic-htx-fid-'.$item_id.'-tid-'.$tid.'-ump-1-m-0-p-0.shtml';
		}
		//echo "DELETE FROM leach_item_tbl where id='".$uid."'";
		//echo "INSERT INTO delmsg (source,inurl) VALUES ('".$source."','".$inurl."')";
		mysql_query("INSERT INTO delmsg (source,inurl) VALUES ('".$source."','".$inurl."')",$conn);
		mysql_query("DELETE FROM leach_item_tbl where id='".$uid."'",$conn);
	}
	echo "<script>alert('删除成功！');window.location.href='main.php?action=gongancheck&type=".$_REQUEST["type"]."';</script>";
	exit;
}
if(($_GET["dotype"]=="up" && $_GET["delid"]!="") || ($_POST["Senduser"]=="upallmsg" && count($_POST["url_arr"])>0) )
{
	
	if(count($_POST["url_arr"])>0)
	{
		for($bb=0;$bb<count($_POST["url_arr"]);$bb++)
		{
			$upids = $upids.$_POST["url_arr"][$bb].","; 
		}
		mysql_query("DELETE FROM leach_item_tbl where id in (".substr($upids,0,-1).")",$conn);
		//echo "DELETE FROM leach_item_tbl where id in (".substr($upids,0,-1).")";
	}
	else
	{
		//echo "DELETE FROM leach_item_tbl where id='".$_GET["delid"]."'";
		mysql_query("DELETE FROM leach_item_tbl where id='".$_GET["delid"]."'",$conn);
	}
	echo "<script>alert('阅读成功！');window.location.href='main.php?action=gongancheck&type=".$_REQUEST["type"]."';</script>";
	exit;
}


if($_REQUEST["type"]=="nowday")
{
	$aaaaa = "&type=nowday";
	$result = mysql_query("select count(*) from leach_item_tbl where input_time>='".date("Y-m-d")."'",$conn);
	list($totalsum)=mysql_fetch_row($result);
	//echo $totalsum;
	$pagebar = new PageBar($totalsum,$pagesize,"text_msg");
	$offset = $pagebar->offset();
	//echo "select index_name,user_id,act_type_id,item_id,tid from leach_item_trace_tbl limit ".$offset.",".$pagesize;
	$res = mysql_query("select id,index_name,user_id,act_type_id,item_id,tid,black_words from leach_item_tbl where input_time>='".date("Y-m-d")."' order by input_time desc limit ".$offset.",".$pagesize,$conn);
}
else
{
	$aaaaa = "";
	$result = mysql_query("select count(*) from leach_item_tbl",$conn);
	list($totalsum)=mysql_fetch_row($result);
	//echo $totalsum;
	$pagebar = new PageBar($totalsum,$pagesize,"text_msg");
	$offset = $pagebar->offset();
	//echo "select index_name,user_id,act_type_id,item_id,tid from leach_item_trace_tbl limit ".$offset.",".$pagesize;
	$res = mysql_query("select id,index_name,user_id,act_type_id,item_id,tid,black_words from leach_item_tbl order by input_time desc limit ".$offset.",".$pagesize,$conn);
}
$i=1;
while(list($id,$index_name,$user_id,$act_type_id,$item_id,$tid,$black_words)=mysql_fetch_row($res))
{
	$blacks = array();
	$array_black = array();
	$new_black_words = "";
	$blacks = split("---", $black_words);
	//print_r($blacks);
	$array_black = array_values(array_unique($blacks));
	for($kk=0;$kk<count($array_black);$kk++)
	{
		if($array_black[$kk])
      $new_black_words .= "[".$array_black[$kk]."]";
	}
	//echo $new_black_words."\r\n";
	if($index_name=="98")
	{
		$msgres = mysql_query("select item_title,item_summary from act_push_to_search_engine_pool_tbl where act_id='".$tid."'",$searchconn);
		list($item_title,$item_summary)=mysql_fetch_row($msgres);
	}
	else if($index_name=="101")
	{
		if($item_id<10)
		{
			$bbsres = mysql_query("select title,description from bbs_topics_tbl_0".$item_id." where tid='".$tid."'",$bbsconn);
		}
		else
		{
			$bbsres = mysql_query("select title,description from bbs_topics_tbl_".$item_id." where tid='".$tid."'",$bbsconn);
		}
		list($item_title,$item_summary)=mysql_fetch_row($bbsres);
	}
	if($act_type_id==1)
	{
		$source="博客日记";
		$url='http://blog1.poco.cn/myBlogDetail-htx-id-'.$item_id.'-userid-'.$user_id.'-pri-0-n-0.xhtml';
	}
	else if($act_type_id==2)
	{
		$source="我的网摘";
		$url='http://my.poco.cn/urldetail.htx&fav_id='.$item_id.'&ispri=2';
	}
	else if($act_type_id==3)
	{
		$source="摄影作品";
		$url='http://my.poco.cn/lastphoto_v2-htx-id-'.$item_id.'-user_id-'.$user_id.'-p-0.xhtml';
	}
	else if($act_type_id==4)
	{
		$source="RSS订阅";
		$url='http://my.poco.cn/rssr/rssr_main.php?user_id='.$user_id.'#read_channel('.$item_id.')';
	}
	else if($act_type_id==5)
	{
		$source="相册";
		$url='http://my.poco.cn/album/album_show_details.htx&user_id='.$user_id.'&item_id='.$item_id;
	}
	else if($act_type_id==6)
	{
		$source="美食DIY";
		$url='http://my.poco.cn/myBlogDetail-htx-id-'.$item_id.'-userid-'.$user_id.'-pri-0-n-0.xhtml';
	}
	else if($act_type_id==7)
	{
		$source="美食作品";
		$url='http://my.poco.cn/commenddetail_v2-htx-id-'.$item_id.'.shtml';
	}
	else if($act_type_id==9)
	{
		$source="视频";
		$url='http://my.poco.cn/plive/plive_opus_details.htx&opus_id='.$item_id;
	}
	else if($act_type_id==11)
	{
		$source="电影评论";
		$url='http://movie.poco.cn/commend_detail.htx&id='.$item_id;
	}
	else if($act_type_id==12)
	{
		$source="最新电影";
		$url='http://movie.poco.cn/newMovieDetail.htx&id='.$item_id;
	}
	else if($act_type_id==13)
	{
		$source="杂志";
		$url='http://my.poco.cn/z_maker/z_maker_opus_details.htx&opus_id='.$item_id.'&user_id='.$user_id;
	}
	else if($act_type_id==14)
	{
		$source="活动";
		$url='http://my.poco.cn/v2/party_jump_page.htx&id='.$item_id;
	}
	else if($act_type_id==21)
	{
		$source="items_图片";
		$url='http://my.poco.cn/items/item_details.htx&item_id='.$item_id.'&user_id='.$user_id;
	}
	else if($act_type_id==35)
	{
		$source="资源分享";
		$url='http://my.poco.cn/res_share/res_share_details.htx&res_group_hash='.$item_id.'&user_id='.$user_id;
	}
	else if($act_type_id==36)
	{
		$source="美食bbs";
		$url='http://gz.food.poco.cn/club_bbs/club_bbs_topic.htx&tid='.$item_id;
	}
	else if($act_type_id==41)
	{
		$source="图片中心";
		$url='http://my.poco.cn/pic/pic_details.htx&pic_group_hash='.$item_id;
	}
	else if($act_type_id==42)
	{
		$source="相片特效";
		$url='http://my.poco.cn/flash_album/flash_album_details.htx&flash_album_id='.$item_id.'&user_id='.$user_id;
	}
	else if($act_type_id==43)
	{
		$source="宠物社区bbs帖";
		$url='http://pet86.poco.cn/bbs/thread-'.$item_id.'-1-1.html';
	}
	else if($act_type_id==45)
	{
		$source="动感相册";
		$url='http://my.poco.cn/pzm/pzm_opus_details.htx&opus_id='.$item_id.'&user_id='.$user_id;
	}
	else if($act_type_id=="")
	{
		$source="论坛BBS";
		$url='http://bbs.poco.cn/topic-htx-fid-'.$item_id.'-tid-'.$tid.'-ump-1-m-0-p-0.shtml';
	}
	$tpl->assign(
			array(
				IDS				=> ($offset+$i),
				DELID			=> $id,
				TITLENAME		=> $item_title,
				ITEM_SUMMARY	=> substr($item_summary,0,288),
				SOURCE			=> $source,
				BLACK_WOEDS		=> $new_black_words,
				LINKURL			=> urlencode($url),
				CHA_URL			=> $url,
				URL_LINK		=> "http://my.poco.cn/act/admin/act_admin_fulltextsearch_del_notifier.php?url_arr=urlencoude(".$url.")",
				GOTOURL			=> "<a href='http://my.poco.cn/id-".$user_id.".shtml' target='_bank'><font color='blue'>他的空间</font></a>"
			)
		);
	$tpl->parse_block(MSG_LIST);
	$i++;
}

mysql_close($conn);
mysql_close($searchconn);
mysql_close($bbsconn);

$top_bar = $pagebar->pre_group();
$up_bar = $pagebar->pre_page();
$num_bar = $pagebar->num_bar();
$next_bar = $pagebar->next_page();
$end_bar = $pagebar->next_group();

$tpl->assign("DOACTION","gongancheck");
$tpl->assign("GOACTION","dodel");

$tpl->assign("TOP_BAR",$top_bar);
$tpl->assign("UP_BAR",$up_bar);
$tpl->assign("NUM_BAR",$num_bar);
$tpl->assign("NEXT_BAR",$next_bar);
$tpl->assign("END_BAR",$end_bar);

$tpl->assign("TEXT",$text);
$tpl->assign("PAGE",$page);
$tpl->assign("GOTYPEACTION",$aaaaa);
$tpl->assign("TOTAL_SUM",$totalsum);
$tpl->assign("USERID",$_SESSION["userid"]);
$tpl->parse(tpl_main,"MAIN");
$tpl->FastPrint();
?>
