<?
include_once("include/FastTemplate.php");
include_once("include/pagebar.php");
$tpl = new FastTemplate("templates");
$tpl->define(
	array(
		MAIN => "bbscheck.htm"
	)
);
$tpl->define_block(MAIN,BBS_LIST);
$pagesize=20;
$text=$_REQUEST["text_msg"];
if($_GET["pagecount"]=="")
{
	$offset = 0;
}
else
{
	$offset = $_GET["pagecount"];
}
if($text!="")
{
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
	$res = $client ->searchFun("actions.PocoindexFunction.searchBbsTest",$querys);
	$client ->close();

	/*
	include_once("include/fulltext_search_helper_core_class.inc.php");
	$index = new fulltext_search_helper_core_class("121.9.249.32",9970);
	$search_args_arr[] = array(
	"indexname" => "101",				//索引名
	"fields"	=> "*",				//默认是*全部字段，要单独字段写上该字段
	"where"		=> "text:".$text,			//查询多个条件text=giggs,credit_point>0
	"order"		=> "",			//不用排序可以填空 credit_point desc
	"limit"		=> $offset.",".$pagesize, 			    //要返回全部数据填空 0,100
	"source"	=> "999",
	"keyword"	=> $text,
	"act_type_id"	=> "",
	"remark"	=> ""
	);
	$res=$index->Select($search_args_arr);
	*/
	//print_r($res);
	//$total=$res["result_total"];
	//$totalsum=$res["total"];
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
		$tpl->assign(
			array(
				GOTOURL		=> "http://my.poco.cn/id-".$resultRow[$i]["user_id"].".shtml",
				URL_LINK	=> 'http://bbs.poco.cn/topic-htx-fid-'.$resultRow[$i]["forum_id"].'-tid-'.$resultRow[$i]["tid"].'-ump-1-m-0-p-0.shtml'
			)
		);
		$tpl->parse_block(BBS_LIST);
	}
}

$tpl->assign("DOACTION","bbscheck");
$tpl->assign("TOP_BAR",$top_bar);
$tpl->assign("UP_BAR",$up_bar);
$tpl->assign("NUM_BAR",$num_bar);
$tpl->assign("NEXT_BAR",$next_bar);
$tpl->assign("END_BAR",$end_bar);

$tpl->assign("TEXT",$text);
$tpl->assign("PAGE",$page);
$tpl->assign("TOTAL_SUM",$totalsum);
$tpl->assign("USERID",$_SESSION["userid"]);
$tpl->parse(tpl_main,"MAIN");
$tpl->FastPrint();
?>