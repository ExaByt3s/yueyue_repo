<?php

/**
 * ��Ʒ�б�ҳ
 *
 * 2015-10-30
 *
 * author  ����
 *
 */
include_once 'common.inc.php';
$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$pai_mall_goods_api_obj = POCO::singleton('pai_mall_api_class');
$order_obj = POCO::singleton('pai_mall_order_class');
$page_obj =POCO::singleton('show_page');
$page_obj->show_last=1;
//$page_obj -> set_pares_url_to_dot_html(true);//��̬
//$page_obj -> sethash('#list');
$user_id = $yue_login_id;
$pc_wap = 'pc/';

//�ж��Ƿ�ͨ���˷�����֤
if(!$seller_info['seller_data']['profile'][0]['type_id'])
{
    header("location:./normal_certificate_basic.php");
}


$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'activity_list.tpl.htm');

//���ݲ���������ʾ����
$show = $_INPUT['show'];
if(!isset($show))
{
    $show = 1;
}
else
{
    $show_array = array(1,2,3,4);
    if(!in_array($show,$show_array))
    {
        $show = 1;
    }
}

//��ҳ����
$show_count  = 5;	//ÿҳ��ʾ��
$data["action_type"] = $show;


//�������ݷ�ҳ
$p = (int)$_INPUT['p'];
if($p<=0)
{
    $p = 1;
}
$limit = ($p-1)*$show_count;
$limit_str = "{$limit},{$show_count}";

$goods_list_count = $pai_mall_goods_obj->user_goods_list($user_id,$data,true, $order_by = 'goods_id DESC', $limit_str, $fields = '*');
$goods_list = $pai_mall_goods_obj->user_goods_list($user_id,$data,$b_select_count = false, $order_by = 'goods_id DESC', $limit_str, $fields = '*');


//����������ǰҳ��û�����ݣ��õ�һҳ����
if($p>1 && empty($goods_list))
{
    $p = 1;
    $limit = ($p-1)*$show_count;
    $limit_str = "{$limit},{$show_count}";
    $goods_list_count = $pai_mall_goods_obj->user_goods_list($user_id,$data,true, $order_by = 'goods_id DESC', $limit_str, $fields = '*');
    $goods_list = $pai_mall_goods_obj->user_goods_list($user_id,$data,$b_select_count = false, $order_by = 'goods_id DESC', $limit_str, $fields = '*');

}

/**********************************************��ҳ����**********************************************/


$page_obj->setvar(array('show'=>$show));
$page_obj->set($show_count, $goods_list_count);



//$limit_str  = $page_obj->limit();
//$page_select = str_replace('&nbsp;', '', $page_obj->output_pre10.$page_obj->output_pre.$page_obj->output_page.$page_obj->output_back.$page_obj->output_back10);
$page_select = str_replace('&nbsp;', '', $page_obj->output_pre.$page_obj->output_page.$page_obj->output_back);
$page_select = str_replace('>��һҳ<', '>&lt;<', $page_select);
$page_select = str_replace('>��һҳ<', '>&gt;<', $page_select);
$page_select = str_replace("<span class=\"dian-more color2\">������</span>","...",$page_select);



$tpl->assign("page_select",$page_select);	//��ҳ



/**********************************************��ҳ����**********************************************/


//��ȡ��б����
$type_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
$type_name_list = $type_obj -> get_type_attribute_cate(0);
foreach($type_name_list as $val)
{
    $type_name[$val['id']] = $val;
}
$task_goods_type_obj = POCO::singleton('pai_mall_goods_type_class');
$config_type_list = $task_goods_type_obj->get_type_cate();
foreach($config_type_list as $key => $value)
{
    $type_name_array[$value["id"]] = $value["name"];
}


//�����б����������Ϣ
foreach($goods_list as $key => $value)
{
    $goods_list[$key]['type_name'] = $type_name_array[$value['type_id']];
    $tmp_data_arr = $pai_mall_goods_api_obj->get_goods_id_activity_info($value["goods_id"]);//ʹ�÷��������б���ص�����
    $goods_list[$key]['total_show'] = $tmp_data_arr["total_show"];
    $goods_list[$key]['ing_show'] = $tmp_data_arr["ing_show"];


    /*if($yue_login_id==100004)
    {
        print_r($tmp_data_arr);
    }*/
    //������ʾ��������
    if($show==4)//���������б����⴦�������ʾ
    {
        if(!empty($tmp_data_arr["new_titles"]))
        {
            //�ж��¾ɱ����Ƿ����
            if($value["titles"]!=$tmp_data_arr["new_titles"])
            {
                $goods_list[$key]['show_title_status'] = 1;//�����չʾ�¾ɱ���
                $goods_list[$key]['new_titles'] = $tmp_data_arr["new_titles"];
                $goods_list[$key]['old_titles'] = $value["titles"];
            }
            else
            {
                $goods_list[$key]['show_title_status'] = 0;//��Ȳ�չʾ�¾ɱ���
                $goods_list[$key]['new_titles'] = $value["titles"];
            }
        }
        else
        {
            $goods_list[$key]['show_title_status'] = 0;//��Ȳ�չʾ�¾ɱ���
            $goods_list[$key]['new_titles'] = $value["titles"];
        }

    }
    else
    {
        $goods_list[$key]['show_title_status'] = 0;//��Ȳ�չʾ�¾ɱ���
        $goods_list[$key]['new_titles'] = $value["titles"];
    }

    //��Ѹ���������
    $section_info = $order_obj->sum_order_quantity_of_paid_by_activity($value["goods_id"]);
    $goods_list[$key]['paid_num'] = (int)$section_info["paid_num"];


    if($tmp_data_arr["min_price"]==$tmp_data_arr["max_price"])
    {
        $goods_list[$key]['price_construct'] = "��".$tmp_data_arr["min_price"];
    }
    else
    {
        $goods_list[$key]['price_construct'] = "��".$tmp_data_arr["min_price"]."-".$tmp_data_arr["max_price"];
    }


    //�������������ƹ��ܰ�ť��ʾ
        $cur_section_end_status = "";//0����ʾȫ��δ������1����һ������
        $cur_section_join_status = "";//0��δ���˱�����1�����˱���
        $tmp_time_list = "";
        //ѭ������
        $tmp_time_list = unserialize($value["time_list"]);
        //$goods_list[$key]['time_sel_list'] = $tmp_time_list;
        foreach($tmp_time_list as $k => $v)
        {
            if($v["time_e"]<time())
            {
                $cur_section_end_status = 1;
                break;
            }
            else
            {
                $cur_section_end_status = 0;
            }
        }


        //�жϱ������
        if($tmp_data_arr["ing_show_has_person"]==0)//���ڽ��еĻ���Σ��Ƿ����˱�����
        {
            $cur_section_join_status = 0;//û��
        }
        else
        {
            $cur_section_join_status = 1;//��
        }

        $goods_list[$key]['cur_section_end_status'] = $cur_section_end_status;
        $goods_list[$key]['cur_section_join_status'] = $cur_section_join_status;

    switch($show)
    {
        case 1:
            //������
            if($cur_section_end_status==0)//���г���δ����
            {
                if($cur_section_join_status==0)//û�˱���
                {
                    $goods_list[$key]['edit_show'] = 1;//�༭��ť��ʾ
                    $goods_list[$key]['put_on_show'] = 0;//�ϼܰ�ť��ʾ
                    $goods_list[$key]['put_off_show'] = 1;//�¼ܰ�ť��ʾ
                    $goods_list[$key]['review_show'] = 0;//�ع˰�ť��ʾ
                    $goods_list[$key]['join_list_show'] = 0;//����������ť��ʾ
                    $goods_list[$key]['test_text'] = "test:���г���δ���������ڽ��еĳ���--û�˱���";//������ʾ

                }
                else if($cur_section_join_status==1)//���˱���
                {
                    $goods_list[$key]['edit_show'] = 1;//�༭��ť��ʾ
                    $goods_list[$key]['put_on_show'] = 0;//�ϼܰ�ť��ʾ
                    $goods_list[$key]['put_off_show'] = 0;//�¼ܰ�ť��ʾ
                    $goods_list[$key]['review_show'] = 0;//�ع˰�ť��ʾ
                    $goods_list[$key]['join_list_show'] = 1;//����������ť��ʾ
                    $goods_list[$key]['test_text'] = "test:���г���δ���������ڽ��еĳ���--���˱���";//������ʾ
                }

            }
            else if($cur_section_end_status==1)//��һ��������
            {
                if($cur_section_join_status==0)//û�˱���
                {
                    $goods_list[$key]['edit_show'] = 1;//�༭��ť��ʾ
                    $goods_list[$key]['put_on_show'] = 0;//�ϼܰ�ť��ʾ
                    $goods_list[$key]['put_off_show'] = 1;//�¼ܰ�ť��ʾ
                    $goods_list[$key]['review_show'] = 1;//�ع˰�ť��ʾ
                    $goods_list[$key]['join_list_show'] = 1;//����������ť��ʾ
                    $goods_list[$key]['test_text'] = "test:��һ�����Ͻ����ˣ����ڽ��еĳ���--û�˱���";//������ʾ
                }
                else if($cur_section_join_status==1)//���˱���
                {
                    $goods_list[$key]['edit_show'] = 1;//�༭��ť��ʾ
                    $goods_list[$key]['put_on_show'] = 0;//�ϼܰ�ť��ʾ
                    $goods_list[$key]['put_off_show'] = 0;//�¼ܰ�ť��ʾ
                    $goods_list[$key]['review_show'] = 1;//�ع˰�ť��ʾ
                    $goods_list[$key]['join_list_show'] = 1;//����������ť��ʾ
                    $goods_list[$key]['test_text'] = "test:��һ�����Ͻ����ˣ����ڽ��еĳ���--���˱���";//������ʾ
                }
            }

            break;
        case 2:
                //�ѽ���
                $goods_list[$key]['edit_show'] = 1;//�༭��ť��ʾ
                $goods_list[$key]['put_on_show'] = 0;//�ϼܰ�ť��ʾ
                $goods_list[$key]['put_off_show'] = 1;//�¼ܰ�ť��ʾ
                $goods_list[$key]['review_show'] = 1;//�ع˰�ť��ʾ
                $goods_list[$key]['join_list_show'] = 1;//����������ť��ʾ
                $goods_list[$key]['test_text'] = "test:��������ж�������";//������ʾ

            break;
        case 3:
            //���¼�
            if($cur_section_end_status==0)//���г���δ����
            {

                $goods_list[$key]['edit_show'] = 1;//�༭��ť��ʾ
                $goods_list[$key]['put_on_show'] = 1;//�ϼܰ�ť��ʾ
                $goods_list[$key]['put_off_show'] = 0;//�¼ܰ�ť��ʾ
                $goods_list[$key]['review_show'] = 0;//�ع˰�ť��ʾ
                $goods_list[$key]['join_list_show'] = 0;//����������ť��ʾ
                $goods_list[$key]['test_text'] = "test:���г���δ����";//������ʾ


            }
            else if($cur_section_end_status==1)//��һ��������
            {

                $goods_list[$key]['edit_show'] = 1;//�༭��ť��ʾ
                $goods_list[$key]['put_on_show'] = 1;//�ϼܰ�ť��ʾ
                $goods_list[$key]['put_off_show'] = 0;//�¼ܰ�ť��ʾ
                $goods_list[$key]['review_show'] = 1;//�ع˰�ť��ʾ
                $goods_list[$key]['join_list_show'] = 1;//����������ť��ʾ
                $goods_list[$key]['test_text'] = "test:��һ�����Ͻ����ˣ����ڽ��еĳ���--û�˱���";//������ʾ

            }
            break;
        case 4:
            //�����
            if($value["edit_status"]==0)//�״��ύ���
            {
                if($cur_section_end_status==0)//���г���δ����
                {

                    $goods_list[$key]['edit_show'] = 1;//�༭��ť��ʾ
                    $goods_list[$key]['put_on_show'] = 0;//�ϼܰ�ť��ʾ
                    $goods_list[$key]['put_off_show'] = 0;//�¼ܰ�ť��ʾ
                    $goods_list[$key]['review_show'] = 0;//�ع˰�ť��ʾ
                    $goods_list[$key]['join_list_show'] = 0;//����������ť��ʾ
                    $goods_list[$key]['test_text'] = "test:�״��ύ���,���г���δ����";//������ʾ

                }
            }
            else
            {
                if($cur_section_end_status==0)//���г���δ����
                {
                    if($cur_section_join_status==0)//û�˱���
                    {
                        $goods_list[$key]['edit_show'] = 1;//�༭��ť��ʾ
                        $goods_list[$key]['put_on_show'] = 0;//�ϼܰ�ť��ʾ
                        $goods_list[$key]['put_off_show'] = 0;//�¼ܰ�ť��ʾ
                        $goods_list[$key]['review_show'] = 0;//�ع˰�ť��ʾ
                        $goods_list[$key]['join_list_show'] = 0;//����������ť��ʾ
                        $goods_list[$key]['test_text'] = "test:���г���δ���������ڽ��еĳ���--û�˱���";//������ʾ
                    }
                    else if($cur_section_join_status==1)//���˱���
                    {
                        $goods_list[$key]['edit_show'] = 1;//�༭��ť��ʾ
                        $goods_list[$key]['put_on_show'] = 0;//�ϼܰ�ť��ʾ
                        $goods_list[$key]['put_off_show'] = 0;//�¼ܰ�ť��ʾ
                        $goods_list[$key]['review_show'] = 0;//�ع˰�ť��ʾ
                        $goods_list[$key]['join_list_show'] = 1;//����������ť��ʾ
                        $goods_list[$key]['test_text'] = "test:���г���δ���������ڽ��еĳ���--���˱���";//������ʾ
                    }

                }
                else if($cur_section_end_status==1)//��һ��������
                {
                    if($cur_section_join_status==0)//û�˱���
                    {
                        $goods_list[$key]['edit_show'] = 1;//�༭��ť��ʾ
                        $goods_list[$key]['put_on_show'] = 0;//�ϼܰ�ť��ʾ
                        $goods_list[$key]['put_off_show'] = 0;//�¼ܰ�ť��ʾ
                        $goods_list[$key]['review_show'] = 1;//�ع˰�ť��ʾ
                        $goods_list[$key]['join_list_show'] = 1;//����������ť��ʾ
                        $goods_list[$key]['test_text'] = "test:��һ�����Ͻ����ˣ����ڽ��еĳ���--���˱���";//������ʾ
                    }
                    else if($cur_section_join_status==1)//���˱���
                    {
                        $goods_list[$key]['edit_show'] = 1;//�༭��ť��ʾ
                        $goods_list[$key]['put_on_show'] = 0;//�ϼܰ�ť��ʾ
                        $goods_list[$key]['put_off_show'] = 0;//�¼ܰ�ť��ʾ
                        $goods_list[$key]['review_show'] = 1;//�ع˰�ť��ʾ
                        $goods_list[$key]['join_list_show'] = 1;//����������ť��ʾ
                        $goods_list[$key]['test_text'] = "test:��һ�����Ͻ����ˣ����ڽ��еĳ���--���˱���";//������ʾ
                    }
                }
            }
            break;
        default:
            break;
    }
    //�������������ƹ��ܰ�ť��ʾ





}
if($yue_login_id==100004)
{
    //print_r($goods_list);
}

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');

// ������
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/global-top-bar.php');

// �ײ�
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
// ͷ��������ʽ��js����
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_global_top', $pc_global_top);


// ͷ��bar
$global_top_bar = _get_wbc_global_top_bar();
$tpl->assign('global_top_bar', $global_top_bar);


// �ײ�
$footer = _get_wbc_footer();
$tpl->assign('footer', $footer);


$page_title = "��б�ҳ";

$tpl->assign("show",$show);
$tpl->assign("page_title",$page_title);
$tpl->assign("goods_list",$goods_list);
$tpl->output();

?>