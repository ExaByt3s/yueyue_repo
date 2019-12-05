<?php
/**
 * ajax操作
 * @author Bowie
 */

define("G_YUE_CMS_CHECK_ADMIN",1);
/**
 * common
 */
include_once("cms_common.inc.php");

/**
 * sajax
 */
include_once("./include/sajax.inc.php");

/**
 *  操作函数
 */

/**
 * 添加一条记录
 *
 * @param int $issue_id
 * @param int $rank_id
 * @param int $user_id
 * @param string $title
 * @param int $place_number
 * @param string $link_url
 * @param string $img_url
 * @param string $content
 * @param string $remark
 * @return mixed
 */
function cms_add_record($issue_id, $rank_id, $channel_id, $user_id, $title, $place_number, $link_url, $img_url, $content, $remark, $link_type)
{
	global $yue_login_id;

	$issue_id 		= $issue_id*1;
	$rank_id		= $rank_id*1;
	$channel_id		= $channel_id*1;
	$user_id 		= $user_id*1;
	$title 			= trim(htmlspecialchars($title));
	$place_number 	= $place_number*1;
	$link_url 		= html_entity_decode(trim($link_url));
	$img_url 		= html_entity_decode(trim($img_url));
	
	$remark	 		= preg_replace('/(<br>)+$/','',$remark);
	$content	 	= preg_replace('/(<br>)+$/','',$content);
	$link_type		= $link_type;

	if ($issue_id < 1) return "err:issue_id={$issue_id}";

	$log_id = cms_system_class::add_record_by_issue_id($issue_id, $user_id, $title, $place_number, $link_url, $img_url, $content, $remark, $link_type);

	if($log_id*1 > 0 AND trim($msg) <> '')
	{
		$ret = "+ok:log_id={$log_id}&msg={$msg}{$issue_msg}";
	}
	else
	{
		$ret = $log_id;	// 出错的情况或是用户还未到限制的情况
	}


	return $ret;

}

/**
 * 修改记录信息
 *
 * @param int $log_id
 * @param int $issue_id
 * @param int $user_id
 * @param string $title
 * @param int $place_number
 * @param string $link_url
 * @param string $img_url
 * @param string $content
 * @param string $remark
 * @return mixed
 */
function cms_update_record($log_id, $issue_id, $user_id, $title, $place_number, $link_url, $img_url, $content, $remark, $freeze, $link_type)
{
	global $yue_login_id;

	$log_id 		= $log_id*1;
	$issue_id 		= $issue_id*1;
	$user_id 		= $user_id*1;
	$title 			= trim(htmlspecialchars($title));
	$place_number 	= $place_number*1;
	$link_url 		= html_entity_decode(trim($link_url));
	$img_url 		= html_entity_decode(trim($img_url));
	$remark	 		= preg_replace('/(<br>)+$/','',$remark);
	$content	 	= preg_replace('/(<br>)+$/','',$content);
	$link_type		= $link_type;
	
	$cms_db_obj = POCO::singleton ( 'cms_db_class' );

	$issue_info = $cms_db_obj->get_cms_info('issue_tbl', "issue_id='{$issue_id}'");
	$rank_id = $issue_info['rank_id'];
	
	$channel_id = cms_system_class::get_rank_info_by_rank_id($rank_id,'channel_id');
	

	$pai_user_obj = POCO::singleton ( 'pai_user_class' );
	$user_info = $pai_user_obj->get_user_info($user_id);

	if (strlen($title)==0) $title = $user_info["nickname"];
	if (strlen($link_url)==0) $link_url = "";
	if (strlen($img_url)==0)
	{
		$user_icon_obj = POCO::singleton ( 'pai_user_icon_class' );
		$img_url = $user_icon_obj->get_user_icon($user_id, 86);
	}

	$update_data = array(
	"place_number"	=> $place_number,
	"user_id"		=> $user_id,
	"user_name"		=> $user_info["nickname"],
	"sex"			=> $user_info["sex"],
	"location_id"	=> $user_info["location_id"],
	"title"			=> $title,
	"img_url"		=> $img_url,
	"link_url"		=> $link_url,
	"link_type"		=> $link_type,
	"content"		=> $content,
	"remark"		=> $remark,
	"admin_user_id"	=> $yue_login_id,
	"post_date"		=> time(),
	);

	return cms_system_class::update_issue_record_by_log_id($log_id, $issue_id, $update_data, $freeze);
}

/**
 * 删除记录
 *
 * @param int $log_id
 * @param int $issue_id
 * @return mixed
 */
function cms_del_record($log_id, $issue_id, $freeze)
{
	return cms_system_class::del_issue_record_by_log_id($log_id, $issue_id, $freeze);
}

/**
 * 添加一期榜单
 *
 * @param int $rank_id
 * @param int $issue_number
 * @param string $issue_name
 * @param string $begin_date
 * @param string $end_date
 * @return mixed
 */
function cms_add_issue($rank_id, $issue_number, $issue_name, $begin_date, $end_date, $freeze)
{
	return cms_system_class::add_issue_by_rank_id($rank_id, $issue_number, $issue_name, $begin_date, $end_date, $freeze);
}

/**
 * 修改一期榜单
 *
 * @param int $rank_id
 * @param int $issue_id
 * @param int $issue_number
 * @param string $issue_name
 * @param string $begin_date
 * @param string $end_date
 * @return mixed
 */
function cms_update_issue($rank_id, $issue_id, $issue_number, $issue_name, $begin_date, $end_date)
{
	global $yue_login_id;

	$rank_id		= $rank_id*1;
	$issue_id		= $issue_id*1;
	$issue_number 	= $issue_number*1;
	$issue_name 	= trim($issue_name);
	$begin_date 	= strtotime($begin_date);
	$end_date 		= strtotime($end_date);

	$cms_db_obj = POCO::singleton ( 'cms_db_class' );

	if ($issue_id < 1) 		return "err:issue_id={$issue_id}";
	if ($issue_number < 1) 	return "err:issue_number={$issue_number}";
	if (strlen($issue_name)==0)	return "err:issue_name={$issue_name}";
	if ($begin_date < 1 || $end_date < $begin_date) return "err:time is error ".date("Ymd",$begin_date)."--".date("Ymd",$end_date);
	if ($cms_db_obj->get_cms_count("issue_tbl", "rank_id={$rank_id} AND issue_number={$issue_number} AND issue_id!={$issue_id}") > 0) return "err:issue_number={$issue_number} exists";

	$update_data = array(
	"issue_number"	=> $issue_number,
	"issue_name"	=> $issue_name,
	"begin_date"	=> $begin_date,
	"end_date"		=> $end_date,
	"issuer_user_id"=> $yue_login_id,
	);

	$affected_rows = $cms_db_obj->update_cms("issue_tbl", "issue_id={$issue_id} AND rank_id={$rank_id}", $update_data);

	if ($affected_rows)
	{
		$last_issue = $cms_db_obj->get_cms_info("issue_tbl", "rank_id={$rank_id} AND freeze=0", "MAX(issue_number) AS last_issue");
		$cms_db_obj->update_cms("rank_tbl", "rank_id={$rank_id}", "last_issue='{$last_issue["last_issue"]}'");//更新最后期数

		//同步最新榜单数据
		cms_system_class::record_data_last_issue_sync_by_rank_id($rank_id);
		return 1;
	}
	else
	{
		return 0;
	}
}

/**
 * 删除一期榜单
 *
 * @param int $rank_id
 * @param int $issue_id
 * @return mixed
 */
function cms_del_issue($rank_id, $issue_id)
{
	$issue_id 		= $issue_id*1;
	$rank_id		= $rank_id*1;

	$cms_db_obj = POCO::singleton ( 'cms_db_class' );

	if ($issue_id < 1) return "err:issue_id={$issue_id}";
	if ($rank_id < 1) return "err:rank_id={$rank_id}";

	$issue_info = $cms_db_obj->get_cms_info("issue_tbl", "issue_id={$issue_id} AND rank_id={$rank_id}", "freeze");
	$freeze = $issue_info["freeze"];

	$affected_rows = $cms_db_obj->delete_cms("issue_tbl", "issue_id={$issue_id} AND rank_id={$rank_id}");//删除本期榜单

	if ($affected_rows)
	{
		$recore_tbl_name = ($freeze) ? "record_tbl_freeze" : "record_tbl";

		$cms_db_obj->delete_cms($recore_tbl_name, "issue_id={$issue_id}");//删除榜单记录

		if (false==$freeze)
		{
			$issue_info = $cms_db_obj->get_cms_info("issue_tbl", "rank_id={$rank_id} AND freeze=0", "MAX(issue_number) AS last_issue");
			$last_issue = $issue_info["last_issue"]*1;
			$cms_db_obj->update_cms("rank_tbl", "rank_id={$rank_id}", "last_issue={$last_issue}");//更新最后期数

			//同步最新榜单数据
			cms_system_class::record_data_last_issue_sync_by_rank_id($rank_id);
		}
		return $rank_id;
	}
	else
	{
		return 0;
	}
}

function cms_freeze_issue($issue_id, $freeze)
{
	if($issue_id*1<1) return "err:issue_id={$issue_id}";

	if('true'===$freeze)
	{
		return cms_system_class::freeze_issue_by_issue_id($issue_id);
	}
	else
	{
		return cms_system_class::unfreeze_issue_by_issue_id($issue_id);
	}
}

/**
 * ajax代码输出
 */
$sajax_no_cache = true;    //是否需要缓存
$sajax_request_type = "POST";

sajax_init();
sajax_export("cms_add_record","cms_del_record","cms_update_record","cms_add_issue","cms_del_issue","cms_update_issue","cms_freeze_issue");
sajax_handle_client_request();

sajax_show_javascript();
?>
function addIssue()
{
	var rank_id = $('rank_id').value;
	var issue_number = $('issue_number').value;
	var issue_name = $('issue_name').value;
	var begin_date = $('begin_date').value;
	var end_date = $('end_date').value;
	var freeze = ($('freeze').checked) ? 1 : 0;
	
	if(isNaN(issue_number)) 	return feedback($('issue_number'),"请输入正确的数字序号期数");
	if(!issue_name) 					return feedback($('issue_name'),"请输入本期名称");
	if(!begin_date) 					return feedback($('begin_date'),"请输入开始时间");
	if(!end_date) 						return feedback($('end_date'),"请输入结束时间");
	
	disabledInput(true);
	x_cms_add_issue(rank_id, issue_number, issue_name, begin_date, end_date, freeze, callBackAddIssue);
}

function callBackAddIssue(ret)
{
	disabledInput(false);
	if(isNaN(ret)) 
	{
		if(ret.indexOf('err:')==0)   alert(ret);
		else		   alert('系统繁忙，提交失败，请重试')
		return;
	}
	
	$('issue_id').value = ret;
	$('issue_submit_btn').value = ' 修 改 ';	
	$('issue_submit_btn').onclick = function () {updateIssue();};	
	$('record_info_div').style.display = 'block';
	$('issue_del_btn').style.display = '';
	$('add_issue_tips').innerHTML = '新一期榜单添加完成，输入本期上榜推荐用户，请点击“插入推荐用户”按钮';
	
	addOption('back_issue', $('issue_name').value, ret, true);
}

function updateIssue()
{
	var rank_id = $('rank_id').value;
	var issue_id = $('issue_id').value;
	var issue_number = $('issue_number').value;
	var issue_name = $('issue_name').value;
	var begin_date = $('begin_date').value;
	var end_date = $('end_date').value;
	
	if(isNaN(issue_number)) 	return feedback($('issue_number'),"请输入正确的数字序号期数");
	if(!issue_name) 					return feedback($('issue_name'),"请输入本期名称");
	if(!begin_date) 					return feedback($('begin_date'),"请输入开始时间");
	if(!end_date) 						return feedback($('end_date'),"请输入结束时间");

	disabledInput(true);
	x_cms_update_issue(rank_id, issue_id, issue_number, issue_name, begin_date, end_date, callBackUpdateIssue);
}

function callBackUpdateIssue(ret)
{
	disabledInput(false);
	if(isNaN(ret)) 
	{
		if(ret.indexOf('err:')==0)   alert(ret);
		else		   alert('系统繁忙，提交失败，请重试')
		return;
	}
	
	if(ret > 0) 
	{
		alert('修改成功');
		$('back_issue').value=$('issue_id').value
		$('back_issue').options.item($('back_issue').selectedIndex).text = $('issue_name').value
	}
	else	alert('请确定已对原内容进行修改')
}

function delIssue()
{
	var rank_id = $('rank_id').value;
	var issue_id = $('issue_id').value;
	
	disabledInput(true);
	x_cms_del_issue(rank_id, issue_id, callBackDelIssue);
}

function callBackDelIssue(ret)
{
	disabledInput(false);
	if(isNaN(ret)) 
	{
		if(ret.indexOf('err:')==0)   alert(ret);
		else		   alert('系统繁忙，提交失败，请重试')
		return;
	}
	
	var rank_id = ret;
	if(rank_id > 0) window.location='cms_issue_list.php?rank_id='+rank_id;
	else alert('删除失败，可能本记录已被删除')
}

function freezeIssue(bool)
{
	var issue_id = $('issue_id').value;

	disabledInput(true);
	x_cms_freeze_issue(issue_id, bool, callBackFreezeIssue);
}

function callBackFreezeIssue(ret)
{	
	disabledInput(false);
	if(isNaN(ret)) 
	{
		if(ret.indexOf('err:')==0)   alert(ret);
		else		   alert('系统繁忙，提交失败，请重试');
		//alert(ret);
		return;
	}
	var freeze = ($('freeze').checked) ? 1 : 0;
	if(ret)
	{	
		if(freeze) alert('隐藏操作成功');
		else	alert('显示操作成功');	
		window.location.reload();
	}
	else alert('操作失败，请稍候再试');
}

function issueAddRecord()
{
	var issue_id = $('issue_id').value;
	var rank_id = $('rank_id').value;
	var channel_id = $('channel_id').value;
	var user_id = $('user_id').value;
	var title = $('title').value;
	var place_number = $('place_number').value;
	var link_url = $('link_url').value;
	var link_type = $('link_type').value;
	var img_url = $('img_url').value;
	var content = $('content').value;
	var remark = $('remark').value;
	
	
	//if(isNaN(place_number)) 	return feedback($('place_number'),"请输入正确的数字序号名次");
	if($('b_no_user_id').checked==false && (isNaN(user_id) || user_id<1)) 		return feedback($('user_id'),"请输入数字用户ＩＤ");
	
	content = content.replace(/\r/ig,'');
	content = content.replace(/\n/ig,'<br>');
	remark = remark.replace(/\r/ig,'');
	remark = remark.replace(/\n/ig,'<br>');
	
	disabledInput(true);
	x_cms_add_record(issue_id, rank_id, channel_id, user_id, title, place_number, link_url, img_url, content, remark, link_type, callBackIssueAddRecord);
}

function callBackIssueAddRecord(ret)
{
	disabledInput(false);


	var log_id;
	log_id = ret;

	if(ret.indexOf('err:')==0)
	{
		alert(ret);
		return;
	}

	if(ret.indexOf('+ok:')==0)
	{
		log_id = queryUrlString(ret,'log_id');
		var msg = queryUrlString(ret,'msg');
		alert(msg);
	}
		
	oTbl = $('record_tbl');
	oTr = oTbl.insertRow();
	oTr.id = 'record_tr_'+log_id;
	oTr.record_log_id = log_id;


	oTd = oTr.insertCell();
	oInput = document.createElement("input");
	oInput.type = 'button';
	oInput.id = 'record_editbtn_'+log_id;
	oInput.record_log_id = log_id;
	oInput.value = '修改';
	oInput.className = 'record_list_btn';
	oInput.onclick = function () {issueEditRecord(this.record_log_id, this);};
	oTd.appendChild(oInput);

	oInput = document.createElement("input");
	oInput.type = 'button';
	oInput.id = 'record_delbtn_'+log_id;
	oInput.record_log_id = log_id;
	oInput.value = '删除';
	oInput.className = 'record_list_btn';
	oInput.onclick = function () {if(confirm('确定删除本记录吗？')){issueDelRecord(this.record_log_id);}};
	oTd.appendChild(oInput);

	arr = new Array();
	arr[0] = $('place_number');
	arr[1] = $('user_id');
	arr[2] = $('title');
	arr[3] = $('link_url');
	arr[4] = $('img_url');
	arr[5] = $('content');
	arr[6] = $('remark');	
	createTrByArray(oTr, arr, log_id);
	$('upload_img_frm').src='cms_upload_img_frm.php';
}

function issueEditRecord(log_id, obj)
{
	$('user_info_div').style.display='block';
	$('user_info_div').style.top = getTop(obj)-160;
	//$('upload_img').style.display='none';
	//$('no_user_info').style.display='none';

	//$(issue_id).value = $('issue_id').value;
	$('user_id').value = $('user_id_'+log_id).value;
	$('title').value = $('title_'+log_id).value;
	$('place_number').value = $('place_number_'+log_id).value;
	$('link_url').value = $('link_url_'+log_id).value;
	$('img_url').value = $('img_url_'+log_id).value;
	$('content').value = $('content_'+log_id).value;
	$('remark').value = $('remark_'+log_id).value;
	$('link_type').value = $('link_type_'+log_id).value;


	$('next_field').onclick = function () {issueUpdateRecord(log_id);};
	$('next_field').value='确定';
}
function issueUpdateRecord(log_id)
{
	if(log_id < 1) 
	{
		alert('参数错误：log_id = 0');
		return;
	}
	
	var issue_id = $('issue_id').value;
	var user_id = $('user_id').value;
	var title = $('title').value;
	var place_number = $('place_number').value;
	var link_url = $('link_url').value;
	var link_type = $('link_type').value;
	var img_url = $('img_url').value;
	var content = $('content').value;
	var remark = $('remark').value;
	var freeze = ($('freeze').checked) ? 1 : 0;

	//if(isNaN(place_number)) 	return feedback($('place_number'),"请输入正确的数字序号名次");
	if(isNaN(user_id)) 		return feedback($('user_id'),"请输入数字用户ＩＤ");
	
	content = content.replace(/\r/ig,'');
	content = content.replace(/\n/ig,'<br>');
	remark = remark.replace(/\r/ig,'');
	remark = remark.replace(/\n/ig,'<br>');
		
	disabledInput(true);
	x_cms_update_record(log_id, issue_id, user_id, title, place_number, link_url, img_url, content, remark, freeze, link_type, callBackIssueUpdateRecord);
}
function callBackIssueUpdateRecord(ret)
{
	
	disabledInput(false);
	if(isNaN(ret)) 
	{
		if(ret.indexOf('err:')==0)   alert(ret);
		else		   alert('系统繁忙，提交失败，请重试')
		return;
	}
	
	log_id = ret;
	$('issue_id').value = $(issue_id).value;
	$('user_id_'+log_id).value = $(user_id).value;
	$('title_'+log_id).value = $(title).value;
	$('place_number_'+log_id).value = $(place_number).value;
	$('link_url_'+log_id).value = $(link_url).value;
	$('link_type_'+log_id).value = $(link_type).value;
	$('link_type_name_'+log_id).innerHTML = $(link_type).options[$(link_type).selectedIndex].text;
	$('img_url_'+log_id).value = $(img_url).value;
	$('content_'+log_id).value = $(content).value;
	$('remark_'+log_id).value = $(remark).value; 
	$('user_info_div').style.display='none';
}

function issueDelRecord(log_id)
{
	if(log_id < 1) 
	{
		alert('参数错误：log_id = 0');
		return;
	}
	
	var issue_id = $('issue_id').value;
	var freeze = ($('freeze').checked) ? 1 : 0;
	disabledInput(true);
	x_cms_del_record(log_id, issue_id, freeze, callBackIssueDelRecord);
}

function callBackIssueDelRecord(ret)
{
	disabledInput(false);
	if(isNaN(ret)) 
	{
		if(ret.indexOf('err:')==0)   alert(ret);
		else		   alert('系统繁忙，提交失败，请重试')
		return;
	}
	
	log_id = ret;
	
	if(log_id) 
	{
		rowIndex = $('record_tr_'+log_id).rowIndex;
		$('record_tbl').deleteRow(rowIndex);
	}
	else
	{
		alert('删除失败，可能本记录已被删除，请刷新');
	}
}

function disabledInput(bool)
{
	var all = document.getElementsByTagName('*');
	var l = all.length;
	
	for(var i=0; i<l; i++ ) 
	{
		if(all[i].type != 'button') continue;
		all[i].disabled = bool;
	}
}

function createTrByArray(oTr, arr, ext)
{
	if(arr.length==0) return;
	
	for(j=0; j < arr.length; j++)
	{
		oTd = oTr.insertCell();
		if(arr[j].id=='remark' || arr[j].id=='content')
		{
			oInput = document.createElement("textarea");
			oInput.rows = 1;
			oInput.cols = 8;
		}
		else
		{
			oInput = document.createElement("input");
			oInput.type = 'text';
		}
		
		oInput.value = arr[j].value;
		oInput.title = arr[j].value;
		oInput.name = arr[j].id+'_'+ext;
		oInput.id = arr[j].id+'_'+ext;
		oInput.readOnly = true;
		oInput.onmouseover = function () {this.title=this.value;};
		if(arr[j].size && arr[j].size<30) oInput.size = arr[j].size;
		oTd.appendChild(oInput);
		if(arr[j].id=='place_number') arr[j].value++;
		else arr[j].value='';
	}	
	return oTr;
}

function queryUrlString(str,item)
{
	var sValue=str.match(new RegExp("[\:\&]"+item+"=([^\&]*)(\&?)","i"));
	return sValue?sValue[1]:sValue;
}
