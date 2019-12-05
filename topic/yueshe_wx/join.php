<?php

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$id = (int)$_INPUT['id'];

$tpl = $my_app_pai->getView('join.tpl.htm');

$header_html = $my_app_pai->webControl('yueshe_wx_topic_header', array(), true);
$tpl->assign("header_html",$header_html);

$footer_html = $my_app_pai->webControl('yueshe_wx_topic_footer', array("id"=>$id), true);
$tpl->assign("footer_html",$footer_html);

$tpl->assign("yue_login_id",$yue_login_id);
$tpl->assign("id",$id);

$__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
$__is_yueyue = stripos($_SERVER['HTTP_USER_AGENT'], 'yue') ? true : false;

if($__is_yueyue)
{
	$tpl->assign("is_yueyue",1);
}

if($_POST)
{
	$nickname = trim($_INPUT['nickname']);
	$phone = trim($_INPUT['phone']);
	$weixin = trim($_INPUT['weixin']);
	$remark = trim($_INPUT['remark']);
	$link = trim($_INPUT['link']);
	$what_type = trim($_INPUT['what_type']);
	
	if(empty($nickname))
	{
		//js_pop_msg("�ǳƲ���Ϊ��");
		echo "<script>alert('�ǳƲ���Ϊ��');</script>";
		exit;
	}
	
	if(empty($phone))
	{
		//js_pop_msg("�ֻ����벻��Ϊ��");
		echo "<script>alert('�ֻ����벻��Ϊ��');</script>";
		exit;
	}
	
	if( !preg_match('/^1\d{10}$/isU',$phone) )
	{
		//js_pop_msg("�ֻ������ʽ����");
		echo "<script>alert('�ֻ������ʽ����');</script>";
		exit;
	}
	
	if(check_enroll($phone))
	{
		//js_pop_msg("���Ѿ���������");
		echo "<script>alert('���Ѿ���������');</script>";
		exit;
	}
	
	if(empty($weixin))
	{
		//js_pop_msg("΢�źŲ���Ϊ��");
		echo "<script>alert('΢�źŲ���Ϊ��');</script>";
		exit;
	}
	
	if(empty($_INPUT['location']))
	{
		//js_pop_msg("ʡ�в���Ϊ��");
		echo "<script>alert('ʡ�в���Ϊ��');</script>";
		exit;
	}
	
	if(empty($_INPUT['how_long']))
	{
		//js_pop_msg("�������޲���Ϊ��");
		echo "<script>alert('�������޲���Ϊ��');</script>";
		exit;
	}
	
	if(empty($what_type))
	{
		//js_pop_msg("�ó��������Ͳ���Ϊ��");
		echo "<script>alert('�ó��������Ͳ���Ϊ��');</script>";
		exit;
	}
	
	if(empty($_INPUT['team']))
	{
		//js_pop_msg("�Ŷӹ��ɲ���Ϊ��");
		echo "<script>alert('�Ŷӹ��ɲ���Ϊ��');</script>";
		exit;
	}
	
	if(empty($_INPUT['income']))
	{
		//js_pop_msg("�¾���Ӱ�������벻��Ϊ��");
		echo "<script>alert('�¾���Ӱ�������벻��Ϊ��');</script>";
		exit;
	}
	
	if(empty($link))
	{
		//js_pop_msg("������Ӱ��Ʒ����ҳ�����Ӳ���Ϊ��");
		echo "<script>alert('������Ӱ��Ʒ����ҳ�����Ӳ���Ϊ��');</script>";
		exit;
	}
	
	if(empty($remark))
	{
		//js_pop_msg("��������Ϊ��");
		echo "<script>alert('��������Ϊ��');</script>";
		exit;
	}

	$insert_data['nickname'] = $nickname;
	$insert_data['phone'] = $phone;
	$insert_data['weixin'] = $weixin;
	$insert_data['location'] = $_INPUT['location'];
	$insert_data['how_long'] = $_INPUT['how_long'];
	$insert_data['what_type'] = $_INPUT['what_type'];
	$insert_data['team'] = $_INPUT['team'];
	$insert_data['income'] = $_INPUT['income'];
	$insert_data['link'] = $_INPUT['link'];
	$insert_data['remark'] = $remark;
	$insert_data['add_time'] = time();
	$insert_data['refer'] = $_INPUT['refer'];
	
	$insert_str = db_arr_to_update_str($insert_data);
	
	$sql = "insert into pai_db.pai_yueshe_weixin_topic_tbl set ".$insert_str;
	db_simple_getdata($sql,false,101);
	
	//js_pop_msg("����ɹ�",false,"share.php?id={$id}&phone={$phone}");
	if($__is_weixin)
	{
		echo "<script>alert('����ɹ�');top.location.href='share.php?id={$id}&phone={$phone}';</script>";
		
	}
	else
	{
		echo "<script>window.parent.show_success()</script>";
	}
	exit;
}

$tpl->output();


function check_enroll($phone)
{
	$sql = "select * from pai_db.pai_yueshe_weixin_topic_tbl where phone='{$phone}'";
	$ret = db_simple_getdata($sql,false,101);
	if($ret)
	{
		return true;
	}
	else
	{
		return false;
	}
}
 ?>