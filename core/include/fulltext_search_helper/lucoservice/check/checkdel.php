<?
include_once("include/FastTemplate.php");
include_once("include/pagebar.php");
$tpl = new FastTemplate("templates");
$tpl->define(
	array(
		MAIN => "checkdel.htm"
	)
);
$tpl->define_block(MAIN,MSG_LIST);
$text=$_REQUEST["text_msg"];
$pagesize=20;
if($_GET["pagecount"]=="")
{
	$offset = 0;
}
else
{
	$offset = $_GET["pagecount"];
}
/*
$totalsum=0;
$text=$_REQUEST["text_msg"];
if($_REQUEST["text_page"]=="")
{
	$page=0;
	$text_page=1;
}
else
{
	$text_page=$_REQUEST["text_page"];
	$page=$text_page-1;
}
$offset=$page*50;
*/
//echo phpinfo();
//print_r($text);
if($text!="")
{
	//echo __FILE__ ;
	//include_once("real_food_search_client.class.php");
	include_once("/disk/data/htdocs233/mypoco/fulltext_search_helper/lucoservice/real_search_client.class.php");
	$client = new LucoClient("113.107.204.207",8810);

	$querys["text"] = $text;
	if($offset == 0)
	{
		$querys["limit"] = "0,20";
	}
	else
	{
		$querys["limit"] = $offset * 20 .",20";
	}
	//print_r($querys);
	$res = $client ->searchFun("actions.PocoindexFunction.searchPocoTest",$querys);
	$client ->close();
	
/*
	include_once("include/fulltext_search_helper_core_class.inc.php");
	$index = new fulltext_search_helper_core_class("121.9.249.32",9970);
	$search_args_arr[] = array(
	"indexname" => "98",				//索引名
	"fields"	=> "sort_order,act_id,item_id,act_type_id,user_id,item_title,item_summary,item_img,add_time,hit_count,cmt_count,last_cmt_time,vote_count,is_vouch,item_tags,user_credit_point,keywords,item_img_count,is_img_item",				//默认是*全部字段，要单独字段写上该字段
	"where"		=> "text:".$text,			//查询多个条件text=giggs,credit_point>0
	"order"		=> "",			//不用排序可以填空 credit_point desc
	"limit"		=> $offset.",".$pagesize, 			    //要返回全部数据填空 0,100
	"source"	=> "999",
	"keyword"	=> $text,
	"act_type_id"	=> "",
	"remark"	=> ""
	);
	*/
	//print_r($search_args_arr);
	//print_r($res);
	//$res=$index->Select($search_args_arr);
	//print_r($res);
	$total=$res->total;
	$totalsum=$res->total;
	$page=ceil($totalsum/$pagesize);

	$pagebar = new PageBar($totalsum,$pagesize,"text_msg");
	$offset=$pagebar->offset();

	$top_bar = $pagebar->pre_group();
	$up_bar = $pagebar->pre_page();
	$num_bar = $pagebar->num_bar();
	$next_bar = $pagebar->next_page();
	$end_bar = $pagebar->next_group();

	
	$resultRow = $res -> resultRow;
	for($i=0;$i<count($resultRow);$i++)
	//for($i=0;$i<$total;$i++)
	{
		$act_type_id=$resultRow[$i]["act_type_id"];
		if($act_type_id==1)
		{
			$source="博客日记";
			$url='http://blog1.poco.cn/myBlogDetail-htx-id-'.$resultRow[$i]["item_id"].'-userid-'.$resultRow[$i]["user_id"].'-pri-0-n-0.xhtml';
		}
		else if($act_type_id==2)
		{
			$source="我的网摘";
			$url='http://my.poco.cn/urldetail.htx&fav_id='.$resultRow[$i]["item_id"].'&ispri=2';
		}
		else if($act_type_id==3)
		{
			$source="摄影作品";
			$url='http://my.poco.cn/lastphoto_v2-htx-id-'.$resultRow[$i]["item_id"].'-user_id-'.$resultRow[$i]["user_id"].'-p-0.xhtml';
		}
		else if($act_type_id==4)
		{
			$source="RSS订阅";
			$url='http://my.poco.cn/rssr/rssr_main.php?user_id='.$resultRow[$i]["user_id"].'#read_channel('.$resultRow[$i]["item_id"].')';
		}
		else if($act_type_id==5)
		{
			$source="相册";
			$url='http://my.poco.cn/album/album_show_details.htx&user_id='.$resultRow[$i]["user_id"].'&item_id='.$resultRow[$i]["item_id"];
		}
		else if($act_type_id==6)
		{
			$source="美食DIY";
			$url='http://my.poco.cn/myBlogDetail-htx-id-'.$resultRow[$i]["item_id"].'-userid-'.$resultRow[$i]["user_id"].'-pri-0-n-0.xhtml';
		}
		else if($act_type_id==7)
		{
			$source="美食作品";
			$url='http://my.poco.cn/commenddetail_v2-htx-id-'.$resultRow[$i]["item_id"].'.shtml';
		}
		else if($act_type_id==9)
		{
			$source="视频";
			$url='http://my.poco.cn/plive/plive_opus_details.htx&opus_id='.$resultRow[$i]["item_id"];
		}
		else if($act_type_id==11)
		{
			$source="电影评论";
			$url='http://movie.poco.cn/commend_detail.htx&id='.$resultRow[$i]["item_id"];
		}
		else if($act_type_id==12)
		{
			$source="最新电影";
			$url='http://movie.poco.cn/newMovieDetail.htx&id='.$resultRow[$i]["item_id"];
		}
		else if($act_type_id==13)
		{
			$source="杂志";
			$url='http://my.poco.cn/z_maker/z_maker_opus_details.htx&opus_id='.$resultRow[$i]["item_id"].'&user_id='.$resultRow[$i]["user_id"];
		}
		else if($act_type_id==14)
		{
			$source="活动";
			$url='http://my.poco.cn/v2/party_jump_page.htx&id='.$resultRow[$i]["item_id"];
		}
		else if($act_type_id==21)
		{
			$source="items_图片";
			$url='http://my.poco.cn/items/item_details.htx&item_id='.$resultRow[$i]["item_id"].'&user_id='.$resultRow[$i]["user_id"];
		}
		else if($act_type_id==35)
		{
			$source="资源分享";
			$url='http://my.poco.cn/res_share/res_share_details.htx&res_group_hash='.$resultRow[$i]["item_id"].'&user_id='.$resultRow[$i]["user_id"];
		}
		else if($act_type_id==36)
		{
			$source="美食bbs";
			$url='http://gz.food.poco.cn/club_bbs/club_bbs_topic.htx&tid='.$resultRow[$i]["item_id"];
		}
		else if($act_type_id==41)
		{
			$source="图片中心";
			$url='http://my.poco.cn/pic/pic_details.htx&pic_group_hash='.$resultRow[$i]["item_id"];
		}
		else if($act_type_id==42)
		{
			$source="相片特效";
			$url='http://my.poco.cn/flash_album/flash_album_details.htx&flash_album_id='.$resultRow[$i]["item_id"].'&user_id='.$resultRow[$i]["user_id"];
		}
		else if($act_type_id==43)
		{
			$source="宠物社区bbs帖";
			$url='http://pet86.poco.cn/bbs/thread-'.$resultRow[$i]["item_id"].'-1-1.html';
		}
		else if($act_type_id==45)
		{
			$source="动感相册";
			$url='http://my.poco.cn/pzm/pzm_opus_details.htx&opus_id='.$resultRow[$i]["item_id"].'&user_id='.$resultRow[$i]["user_id"];
		}
		$tpl->assign(
			array(
				IDS				=> ($offset+$i),
				TITLENAME		=> $resultRow[$i]["item_title"],
				ITEM_SUMMARY	=> substr($resultRow[$i]["item_summary"],0,288),
				SOURCE			=> $source,
				LINKURL			=> urlencode($url),
				CHA_URL			=> $url,
				URL_LINK		=> "http://my.poco.cn/act/admin/act_admin_fulltextsearch_del_notifier.php?url_arr=urlencoude(".$url.")",
				GOTOURL			=> "<a href='http://my.poco.cn/id-".$resultRow[$i]["user_id"].".shtml' target='_bank'><font color='blue'>他的空间</font></a>"
			)
		);
		$tpl->parse_block(MSG_LIST);
	}
}

$tpl->assign("DOACTION","checkdel");
$tpl->assign("GOACTION","dodel");

$tpl->assign("TOP_BAR",$top_bar);
$tpl->assign("UP_BAR",$up_bar);
$tpl->assign("NUM_BAR",$num_bar);
$tpl->assign("NEXT_BAR",$next_bar);
$tpl->assign("END_BAR",$end_bar);

$tpl->assign("TEXT",$text);
$tpl->assign("PAGE",$page);
//$tpl->assign("TEXT_PAGE",$text_page);
$tpl->assign("TOTAL_SUM",$totalsum);
$tpl->assign("USERID",$_SESSION["userid"]);
$tpl->parse(tpl_main,"MAIN");
$tpl->FastPrint();
?>