<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
			<title>����</title>
			<style>
			td{ font-size:12px;}
			label{width: 100px;float: left;}
			</style>
			<script type="text/javascript">
				function close_order(order_sn){
					document.getElementById("step_"+order_sn).value = "close";
					submit_order(order_sn);
				}
				function pay_order(order_sn)
				{
					document.getElementById("step_"+order_sn).value = "pay";
					submit_order(order_sn);
				}
				function submit_order(order_sn)
				{
					document.getElementById("form_"+order_sn).submit();
				}
			</script>
		</head>
		<body> 
		
		<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
		  <tr>
		    <td height="30" align="center"><a href="buy_goods_list.php" target="_self">�����Ʒ�б�</a></td>
		    <td height="30" align="center"><a href="buy_bill_list.php" target="_self">���붩���б�</a></td>
	      </tr>
	    </table>
		<table width="90%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#999999">
			<tr>
				<td width="8%" height="30" align="center" bgcolor="#FFFFFF">����ID</td>
				<td width="8%" height="30" align="center" bgcolor="#FFFFFF">��������</td>
				<td width="8%" height="30" align="center" bgcolor="#FFFFFF">����ID</td>
				<td width="20%" height="30" align="center" bgcolor="#FFFFFF">״̬</td>
				<td width="10%" height="30" align="center" bgcolor="#FFFFFF">ǩ����</td>
				<td width="10%" height="30" align="center" bgcolor="#FFFFFF">�����ܼ�</td>
				<td height="30" align="center" bgcolor="#FFFFFF">����</td>
			</tr>
<?php
    include_once 'common.inc.php';
    $order_sn = $_REQUEST['order_sn'];
    // var_dump($yue_login_id);
	if( $_POST['step'] == 'submit_order' )
	{
		submit_order();
	}
	elseif( $_POST['step'] == 'pay' )
	{
		pay();
	}
	elseif( $_GET['order_sn'] )
	{
		get_order_info(trim($_GET['order_sn']));
	}
	elseif( $_POST['step']== 'del' )
	{
		del();
	}
	elseif( $_POST['step'] == 'close' )
	{
		close();
	}
	elseif( $_POST['step'] == 'comment' )
	{
		comment();
	}
	elseif( $_POST['step'] == 'refund' )
	{
		$res = POCO::singleton('pai_mall_order_class')->refund_order_for_buyer(intval($_POST['order_sn']), intval($_POST['buyer_user_id']));
		var_dump($res);
		get_order_list($yue_login_id);
	}
	else
	{
		get_order_list($yue_login_id);
	}

	if( $_POST['step2'] == 'close' )
	{
		close();
	}
	function submit_order(){
	    $req_goods = $_POST;
	    $buyer_user_id = intval($req_goods['user_id']);
	    //��ȡ��Ʒ��Ϣ
	    $goods_id = intval($req_goods['goods_id']);
		if(empty($goods_id)){
			die("��Ʒ��������");
		}
		$goods_obj = POCO::singleton('pai_mall_goods_class');
		$goods_info = $goods_obj->get_goods_info($goods_id);
		//��ȡ��Ʒ��Ϣend
		$goods_data = $goods_info['goods_data'];
		
		// ��������Ϣ
		$more_info = array(
			'referer' => 'pc',//������Դ
			'description' => '����',
			);		
		// ������ϸ��Ϣ
		$detail_list[] = array(
		 	'type_id'=> $goods_data['type_id'],//Ʒ��id
		 	'goods_id'=> $goods_id,//��Ʒid
		 	'goods_name'=> $goods_data['titles'],//��Ʒ����
		 	'prices_type_id'=> $goods_info['goods_prices_list'][0]['type_id'][0],//��Ʒ�۸��������id
		 	'prices_spec'=> $req_goods['prices_spec'],//��Ʒ���
		 	'goods_version'=> $goods_data['version'],//��Ʒ�汾
		 	'service_time'=> strtotime($req_goods['service_time']),//����ʱ��
		 	'service_location_id'=> $goods_data['location_id'],
		  	'service_address' => $req_goods['service_address'],
		  	'service_people' => $req_goods['service_people'],
		  	//'prices' => 0,
		  	'quantity'=> $req_goods['num'],
		  	);
		$ret = POCO::singleton('pai_mall_order_class')->submit_order($buyer_user_id, $detail_list, $more_info);
		if($ret['result']!=1){
			var_dump($ret['message']);
		}
		get_order_list($buyer_user_id);
    }

    function get_order_info($order_sn){
		$order_info[] = POCO::singleton('pai_mall_order_class')->get_order_full_info($order_sn);

		$status_str_arr = array(
			0 => '��֧��',
			1 => '��ȷ��',
			2 => '��ǩ��',
			7 => '�ѹر�',
			8 => '�����',
		);
		foreach($order_info as $val)
		{
			if($val['code_list'][0][code_sn])
			{
				$hash = qrcode_hash($val['order_id'], $val['order_id'], $val['code_list'][0][code_sn]);
				$jump_url = "http://yp.yueus.com/mobile/action/check_qrcode.php?event_id={$val['order_id']}&enroll_id={$val['order_id']}&code={$val['code_list'][0][code_sn]}&hash={$hash}";
				$img_url = POCO::singleton('pai_activity_code_class')->get_qrcode_img($jump_url);
				$img = "<img src='{$img_url}' width='145' height='145' />";
			}
		  	echo '<tr><td width="8%" height="30" align="center" bgcolor="#FFFFFF">'.$val['order_id'].'</td>';
			echo '<td width="8%" height="30" align="center" bgcolor="#FFFFFF">'.$val[order_sn].'</td>';	    
			echo '<td width="8%" height="30" align="center" bgcolor="#FFFFFF">'.$val['seller_user_id'].'</td>';
			echo '<td width="10%" height="30" align="center" bgcolor="#FFFFFF">'.
			    	$status_str_arr[$val['status']].
			    '</td>';
			echo '<td width="5%" height="30" align="center" bgcolor="#FFF">'.$val['code_list'][0][code_sn].'<br>'.$img;
			echo '</td>';
			echo '<td width="10%" height="30" align="center" bgcolor="#FFFFFF">'.$val[total_amount].'</td>';
			echo '<td height="30" align="left" bgcolor="#FFFFFF">'.
			    	'<form action="buy_bill_list.php" id="form_'.$val[order_sn].'" method=post>'.
			    		'<input name="order_sn" type="hidden" value="'.$val[order_sn].'" />'.
			    		'<input name="total_amount" type="hidden" value="'.$val[total_amount].'" />'.
						'<input name="buyer_user_id" type="hidden" value="'.$val[buyer_user_id].'" />';
			if($val['status'] == 0)
			{
				echo '<input name="step" type="hidden" value="pay" />'.
				'<input type="button" onclick="submit_order('.$val[order_sn].')" value="֧��" />';
			}
			if($val['status'] == 1)
			{
				echo '<input name="step" type="hidden" value="close" />'.
				'<input type="button" onclick="submit_order('.$val[order_sn].')" value="ȡ��">';
			}
			if($val['status'] == 2)
			{
				$service_time = $val['$detail_list'][0]['service_time']*1;
				$cur_time = time();
				$service_time_tmp = $service_time - 12*3600;
				//�жϷ���ʱ���Ƿ���������˿�ʱ��
				//if( $cur_time<$service_time_tmp )
				{
					echo '<input name="step" type="hidden" value="refund" />'.
						'<input type="button" onclick="submit_order('.$val[order_sn].')" value="�����˿�">';
				}
			}
			echo '</form>'.
			    '</td>'.
		  	'</tr>';
		}
    }

	function pay()
	{
    	$submit_data = $_POST;
    	$user_id = intval($submit_data['buyer_user_id']);
    	$amount = trim($submit_data['total_amount']);
    	// var_dump($submit_data);
		
 		$order_sn = trim($submit_data['order_sn']);
 		// var_dump($ret);
    	$res = POCO::singleton('pai_mall_order_class')->submit_pay_order($order_sn, $user_id, 1, 1, '', '', '', '');
    	// var_dump($res);
    	get_order_list($user_id);
	}

	function close()
	{
		$res = POCO::singleton('pai_mall_order_class')->close_order_for_buyer(intval($_POST['order_sn']), intval($_POST['buyer_user_id']));
		get_order_list(intval($_POST['buyer_user_id']));
	}

	function del(){
		$res = POCO::singleton('pai_mall_order_class')->del_order_for_buyer(intval($_POST['order_sn']), intval($_POST['buyer_user_id']));
		get_order_list(intval($_POST['buyer_user_id']));
	}

	function comment(){
		$insert_data['from_user_id'] = $_POST['buyer_user_id'];  //�������û�ID
		$insert_data['to_user_id'] = $_POST['seller_id']; // ���������û�ID
		$insert_data['order_id'] = $_POST['order_id']; 
		$insert_data['goods_id'] = $_POST['goods_id'];
		$insert_data['overall_score'] = $_POST['overall_score']; // �������۷���
		$insert_data['match_score'] = $_POST['match_score'];// ��Ʒ���Ϸ���
		$insert_data['manner_score'] = $_POST['manner_score'];// ̬������
		$insert_data['quality_score'] = $_POST['quality_score'];//  ��������
		$insert_data['comment']= $_POST['comment']; //  ��������

		$res = POCO::singleton('pai_mall_comment_class')->add_seller_comment($insert_data);
		get_order_list(intval($_POST['buyer_user_id']));
		var_dump($res);
	}

	function get_comment($user_id, $b_select_count = false, $where_str = '', $order_by = 'comment_id DESC', $limit = '0,10', $fields = '*')
	{
		POCO::singleton('pai_mall_comment_class')->get_buyer_comment_list($user_id, $b_select_count = false, $where_str = '', $order_by = 'comment_id DESC', $limit = '0,10', $fields = '*');
	}

	function get_order_list($user_id){
		
		$res = POCO::singleton('pai_mall_order_class')->get_order_list_for_buyer($user_id, -1, -1, false, '', '0,1000', '*');
		$status_str_arr = array(
			0 => '��֧��',
			1 => '��ȷ��',
			2 => '��ǩ��',
			7 => '�ѹر�',
			8 => '�����',
		);

		foreach($res as $val)
		{	
			if($val['code_list'][0][code_sn])
			{
				$hash = qrcode_hash($val['order_id'], $val['order_id'], $val['code_list'][0][code_sn]);
				$jump_url = "http://yp.yueus.com/mobile/action/check_qrcode.php?event_id={$val['order_id']}&enroll_id={$val['order_id']}&code={$val['code_list'][0][code_sn]}&hash={$hash}";
				$img_url = POCO::singleton('pai_activity_code_class')->get_qrcode_img($jump_url);
				$img = "<img src='{$img_url}' width='145' height='145' />";
			}
		  	echo '<tr><td width="8%" height="30" align="center" bgcolor="#FFFFFF">'.$val['order_id'].'</td>';
			echo '<td width="8%" height="30" align="center" bgcolor="#FFFFFF">'.$val[order_sn].'</td>';	    
			echo '<td width="8%" height="30" align="center" bgcolor="#FFFFFF">'.$val['seller_user_id'].'</td>';
			echo '<td width="10%" height="30" align="center" bgcolor="#FFFFFF">'.
			    	$status_str_arr[$val['status']].
			    '</td>';
			echo '<td width="5%" height="30" align="center" bgcolor="#FFF">'.$val['code_list'][0][code_sn].'<br>'.$img;
			echo '</td>';
			echo '<td width="10%" height="30" align="center" bgcolor="#FFFFFF">'.$val[total_amount].'</td>';
			echo '<td height="30" align="left" bgcolor="#FFFFFF" style="padding:10px;">'.
			    	'<form action="buy_bill_list.php?step=pay" id="form_'.$val[order_sn].'" method=post>'.
			    		'<input name="order_sn" type="hidden" value="'.$val[order_sn].'" />'.
			    		'<input name="total_amount" type="hidden" value="'.$val[total_amount].'" />'.
						'<input name="buyer_user_id" type="hidden" value="'.$val[buyer_user_id].'" />';
			if($val['status'] == 0)
			{
				echo '<input name="step" id="step_'.$val[order_sn].'" type="hidden" value="pay" />'.
					'<input type="button" onclick="close_order('.$val[order_sn].')" value="�ر�" />'.
				'<input type="button" onclick="pay_order('.$val[order_sn].')" value="֧��" />';
			}
			if($val['status'] == 1){
				echo '<input name="step" type="hidden" value="close" />'.
				'<input type="button" onclick="submit_order('.$val[order_sn].')" value="ȡ��">';
			}
			if($val['status'] == 2)
			{
				$service_time = $val['detail_list'][0]['service_time']*1;
				$cur_time = time();
				$service_time_tmp = $service_time - 12*3600;
//				echo $service_time;
				//�жϷ���ʱ���Ƿ���������˿�ʱ��
				//if( $cur_time<$service_time_tmp )
				{
					echo '<input name="step" type="hidden" value="refund" />'.
						'<input type="button" onclick="submit_order('.$val[order_sn].')" value="�����˿�">';
				}
			}
			if($val['status'] == 7)
			{
				echo '<input name="step" type="hidden" value="del" />'.
				'<input type="button" onclick="submit_order('.$val[order_sn].')" value="ɾ��">';
			}
			if($val['status'] == 8 && $val['is_buyer_comment']==0)
			{
				echo '<input type="text" value="" name="comment" placeholder="��������" /><br/><br/>'.
				'<label>�������۷�����</label><select name="overall_score">
					<option value="1">1</option><option value="2">2</option>
					<option value="3">3</option><option value="4">4</option>
					<option value="5">5</option>
				</select><br/><br/>'.
				'<label>��Ʒ���Ϸ�����</label><select name="match_score">
					<option value="1">1</option><option value="2">2</option>
					<option value="3">3</option><option value="4">4</option>
					<option value="5">5</option>
				</select><br/><br/>'.
				'<label>̬�����ۣ�</label><select name="manner_score">
					<option value="1">1</option><option value="2">2</option>
					<option value="3">3</option><option value="4">4</option>
					<option value="5">5</option>
				</select><br/><br/>'.
				'<label>����������</label><select name="quality_score">
					<option value="1">1</option><option value="2">2</option>
					<option value="3">3</option><option value="4">4</option>
					<option value="5">5</option>
				</select><br/><br/>'.
				'<input type="hidden" value="'.$val['order_id'].'" name="order_id" />'.
				'<input type="hidden" value="'.$val['seller_user_id'].'" name="seller_id" />'.
				'<input name="step" type="hidden" value="comment" />'.
				'<input name="buyer_user_id" type="hidden" value="'.$val['buyer_user_id'].'" />'.
				'<input name="goods_id" type="hidden" value="'.$val['detail_list'][0]['goods_id'].'" />'.
				'<input type="button" onclick="submit_order('.$val[order_sn].')" value="�ύ">';
			}
			echo '</form>'.
			    '</td>'.
		  	'</tr>';
			unset($img);
		}
	}
    
?>
		</table>
	</body>
</html>