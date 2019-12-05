<?php
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

include_once ('/disk/data/htdocs232/poco/pai/yue_admin/yue_access_control.inc.php');

$list = array ('operate', 'expand', 'financial' );

$auth = yueyue_admin_check ( 'oa', $list, 1 );

if (! $auth)
{
	header("Location: http://www.yueus.com/yue_admin/login_e.php?referer_url=http%3A%2F%2Fwww.yueus.com%2Fyue_admin%2Foa%2F");
}
else
{
	$oa_role = $auth [0];
}

include_once ('top.php');

//����״̬
if (! function_exists ( 'yue_oa_order_status' ))
{
	function yue_oa_order_status($name)
	{
		if ($name == 'wait')
		{
			$ret = "�ȴ��µ�";
		}
		elseif ($name == 'confirm_order')
		{
			$ret = "���µ�";
		}
		elseif ($name == 'complete_recommend')
		{
			$ret = "���Ƽ�";
		}
		elseif ($name == 'shoot_confirm')
		{
			$ret = "��ȷ���տ�";
		}
		elseif ($name == 'pay_confirm')
		{
			$ret = "���տ�";
		}
		elseif ($name == 'wait_shoot')
		{
			$ret = "������";
		}
		elseif ($name == 'wait_close')
		{
			$ret = "���ᵥ";
		}
		elseif ($name == 'close')
		{
			$ret = "�ѽᵥ";
		}
		elseif ($name == 'refund')
		{
			$ret = "���˿�";
		}
		elseif ($name == 'cancel')
		{
			$ret = "��ȡ��";
		}
		
		return $ret;
	}
}

/*
 * �б�״̬����ͬ��ɫ��ʾ��ͬ״̬��
 */
if (! function_exists ( 'yue_oa_list_status' ))
{
    /**
     * @param $oa_role
     * @param $list_status
     * @return string
     */
    function yue_oa_list_status($oa_role, $list_status)
	{
		if ($oa_role == 'operate')
		{
			if ($list_status == 'wait')
			{
				$status = "audit_status='wait' AND order_status='wait'";
			}
			elseif ($list_status == 'doing')
			{
				$status = "audit_status='pass' AND order_status IN ('confirm_order','complete_recommend','shoot_confirm','pay_confirm','wait_shoot','wait_close')";
			}
			elseif ($list_status == 'done')
			{
				$status = "audit_status='pass' AND order_status='close'";
			}
			elseif ($list_status == 'cancel')
			{
				$status = "audit_status IN ('pass','not_pass') AND order_status IN ('refund','cancel')";
			}
		}
		elseif ($oa_role == 'expand')
		{

            //OA TTģʽ����Ȩ��
            $category_arr = array ('0','1', '2', '3' ,'7');
            $category_auth = yueyue_admin_check ( 'expand_category', $category_arr, 1 );
            if($category_auth)
            {
                $category_ids = implode(",",$category_auth);
                $category_where = " service_id IN ({$category_ids})";
            }

            //OA �̳�ģʽ����Ȩ��
            $mall_category_arr = array ('31','3','12','5','40');
            $mall_category_auth = yueyue_admin_check ( 'mall_expand_category', $mall_category_arr, 1 );
            if($mall_category_auth)
            {
                foreach($mall_category_auth as $type_id)
                {
                    $mall_category_where_arr[] = "type_id_str LIKE '%{$type_id}%'";
                }

                $mall_category_where_str = implode(" OR ", $mall_category_where_arr);

                $mall_category_where = "  ({$mall_category_where_str})";
            }

			if ($list_status == 'wait')
			{
				$status = "audit_status='pass' AND order_status='confirm_order'";
			}
			elseif ($list_status == 'doing')
			{
				$status = "audit_status='pass' AND order_status IN ('complete_recommend')";
			}
			elseif ($list_status == 'done')
			{
				$status = "audit_status='pass' AND order_status='shoot_confirm'";
			}
			elseif ($list_status == 'cancel')
			{
				$status = "audit_status IN ('pass','not_pass') AND order_status IN ('refund','cancel')";
			}

            if($mall_category_where && $category_where)
            {
                $status .= " AND (".$category_where." OR ".$mall_category_where.")";
            }
            else
            {
                $status .= " AND ".$category_where.$mall_category_where;
            }

		}
		elseif ($oa_role == 'financial')
		{
			if ($list_status == 'wait')
			{
				$status = "audit_status='pass' AND order_status='shoot_confirm'";
			}
			elseif ($list_status == 'doing')
			{
				$status = "audit_status='pass' AND order_status IN ('pay_confirm','wait_shoot','wait_close')";
			}
			elseif ($list_status == 'done')
			{
				$status = "audit_status='pass' AND order_status='close'";
			}
			elseif ($list_status == 'cancel')
			{
				$status = "audit_status IN ('pass','not_pass') AND order_status IN ('refund','cancel')";
			}
		}elseif($oa_role == 'admin')
		{
			$status = "1";
		}
		
		return $status;
	}

}



/*
 * ����ɸѡ������TT���̳�
 */
function type_where($type_id)
{
    $type_id = (int)$type_id;
    switch($type_id)
    {
        //ģ��Լ��
        case "31":
            $where = "type_id_str LIKE '%31%' OR service_id IN (0,7)";
            break;

        //��Ӱ����
        case "40":
            $where = "type_id_str LIKE '%40%'";
            break;

        //��ױ
        case "3":
            $where = "type_id_str LIKE '%3%' OR service_id=3";
            break;

        //��Ӱ��ѵ
        case "5":
            $where = "type_id_str LIKE '%5%' OR service_id=2";
            break;

        //Ӱ������
        case "12":
            $where = "type_id_str LIKE '%12%' OR service_id=1";
            break;
    }

    return "(".$where.")";
}

?>