<?php
/*
 *
 * //��Ʒ�༭ģ��
 *
 *
 *
 *
 */

//��������ӷ���
$return_data = children_type_contruct($page_data,"39059724f73a9969845dfe4146c5660e",$action);
$activity_detail_data_arr = $return_data[0];//��Ӧ����ĵ�ѡ
$show_J_child_title = $return_data[1];

$tpl->assign("activity_detail_data_arr",$activity_detail_data_arr);


//�ٷ��˺�
$official_login_id_arr = $pai_mall_goods_obj->_is_official_user;
if(in_array($yue_login_id,$official_login_id_arr))//�ж��Ƿ�ٷ��˺ſ��Ʒ���ҳ��һЩ����1�����Ƴ�������
{
    $is_official_user = 1;
}
else
{
    $is_official_user = 0;
}
$tpl->assign("is_official_user",$is_official_user);



//�༭ʱ�����⴦�����
if($action=="edit")
{
    //�����������
    $page_data['default_data']['province']['value'] = substr($page_data['default_data']['location_id']['value'],0,6);
    if($page_data['system_data']['00ec53c4682d36f5c4359f4ae7bd7ba1']['value'])
    {
        //���ַ����
        $page_data['system_data']['province_2']['value'] = substr($page_data['system_data']['00ec53c4682d36f5c4359f4ae7bd7ba1']['value'],0,6);
    }
}


//���ƶ�����ɫbanner��ʾ
foreach($user_status_list as $k => $v)
{
    if($v['type_id']==$type_id)
    {
        if($v['status']==0)
        {
            $hide_system_msg = 0;
        }
    }
}




//�������DIY�ṹ
$tmp_contact_list = $page_data['contact_data'];
//print_r($tmp_contact_list);
$i=0;
foreach($tmp_contact_list as $key => $value)
{

    //��ʼ�����
    $contact_list[$i] = $value;
    $contact_list[$i]['data_mark'] = $i+1;
    $i++;
}

//�����νṹ
$tmp_price_list = $page_data['prices_data'];
$i=0;
foreach($tmp_price_list as $key => $value)
{

    //��ʼ�����
    $new_price_list[$i] = $value;
    $new_price_list[$i]["time_s_str"] = $value["time_s"];
    $new_price_list[$i]["time_e_str"] = $value["time_e"];
    $new_price_list[$i]["time_s"] = date("Y-m-d H:i",$value["time_s"]);
    $new_price_list[$i]["time_e"] = date("Y-m-d H:i",$value["time_e"]);
    //����״̬������
    if($new_price_list[$i]["status"]==1)
    {
        //�������ж�����
        if($new_price_list[$i]["stock_num_total"]!=$new_price_list[$i]["stock_num"])
        {
            $new_price_list[$i]["join_status"] = 1;//��ʾ���˱����ˣ�ֻ���޸�����
            $new_price_list[$i]["edit_attr"] = 'readonly="true"';//�ɱ༭״̬
            $new_price_list[$i]["edit_stock_num_attr"] = '';//�ɱ༭����״̬
            $new_price_list[$i]["time_edit_attr"] = 0;//ʱ�䲻���޸�
            $new_price_list[$i]["prices_add_del_btn_show"] = 0;//�۸����ɾ����ť��ʾ
            $new_price_list[$i]["section_del_btn_show"] = 0;//����ɾ����ť��ʾ
            $new_price_list[$i]["section_save_btn_show"] = 1;//���α��水ť��ʾ
            //����
            $new_price_list[$i]["section_text"] = ":�ó������˱����ˣ�ֻ���޸�����������ɾ��";
        }
        else
        {
            //û�˱���
            //�жϸó����Ƿ��Ѿ����˿�ʼʱ��
            if($value["time_s"]<time())//�Ѿ����˿�ʼʱ��
            {
                $new_price_list[$i]["join_status"] = 1;//��ʾ���˱����ˣ�ֻ���޸�����
                $new_price_list[$i]["edit_attr"] = 'readonly="true"';//�ɱ༭״̬
                $new_price_list[$i]["edit_stock_num_attr"] = '';//�ɱ༭����״̬
                $new_price_list[$i]["time_edit_attr"] = 0;//ʱ�䲻���޸�
                $new_price_list[$i]["prices_add_del_btn_show"] = 0;//�۸����ɾ����ť��ʾ
                $new_price_list[$i]["section_del_btn_show"] = 0;//����ɾ����ť��ʾ
                $new_price_list[$i]["section_save_btn_show"] = 1;//���α��水ť��ʾ
                //����
                $new_price_list[$i]["section_text"] = ":�ó���û�˱������Ѿ����˿�ʼʱ�䣬ֻ���޸�����������ɾ��";
            }
            else
            {
                $new_price_list[$i]["join_status"] = 0;//��ʾû���˱����ˣ�ȫ�����޸�
                $new_price_list[$i]["edit_attr"] = '';//�ɱ༭״̬
                $new_price_list[$i]["edit_stock_num_attr"] = '';//�ɱ༭����״̬
                $new_price_list[$i]["time_edit_attr"] = 1;//ʱ�������޸�
                $new_price_list[$i]["prices_add_del_btn_show"] = 1;//�۸����ɾ����ť��ʾ
                $new_price_list[$i]["section_del_btn_show"] = 1;//����ɾ����ť��ʾ
                $new_price_list[$i]["section_save_btn_show"] = 1;//���α��水ť��ʾ
                //����
                $new_price_list[$i]["section_text"] = ":�ó���û�˱�������û�й���ʼʱ�䣬���Զ��޸ģ�ɾ��";
            }


        }
    }
    else
    {
        $new_price_list[$i]["join_status"] = 2;//��ʾ�����ĳ��Σ����������ˣ�ȫ�������޸�
        $new_price_list[$i]["edit_attr"] = 'readonly="true"';//�ɱ༭״̬
        $new_price_list[$i]["edit_stock_num_attr"] = 'readonly="true"';//�ɱ༭����״̬
        $new_price_list[$i]["time_edit_attr"] = 0;//ʱ�䲻���޸�
        $new_price_list[$i]["prices_add_del_btn_show"] = 0;//�۸����ɾ����ť��ʾ
        $new_price_list[$i]["section_del_btn_show"] = 0;//����ɾ����ť��ʾ
        $new_price_list[$i]["section_save_btn_show"] = 0;//���α��水ť��ʾ
        //����
        $new_price_list[$i]["section_text"] = ":�ó����Ѿ������������޸ĸ�ɾ��";
    }

    //������ʾ��ǰ��������
    $new_price_list[$i]["has_joined_num"] = (int)$value["stock_num_total"]-(int)$value["stock_num"];

    $the_lowest_prices = "";
    $j=0;
    foreach($new_price_list[$i]["prices_list_data"] as $k => $v)
    {
        $new_price_list[$i]["prices_list_data"][$k]["data_mark"] = $j+1;
        //�����ϲ�༭�������
        $new_price_list[$i]["prices_list_data"][$k]["edit_attr"] = $new_price_list[$i]["edit_attr"];
        //��ť��ʾ
        $new_price_list[$i]["prices_list_data"][$k]["prices_add_del_btn_show"] = $new_price_list[$i]["prices_add_del_btn_show"];

        //ѭ����ȡ�ó�����ͼ�
        if(empty($the_lowest_prices))
        {
            $the_lowest_prices = $v["prices"];
        }
        else
        {
            if($v["prices"]<$the_lowest_prices)
            {
                $the_lowest_prices = $v["prices"];
            }
        }
        $j++;
    }
    //��ֵ��ͼ۵��ϲ�
    $new_price_list[$i]["the_lowest_prices"] = $the_lowest_prices;

    $section_diy_count = ((int)count($new_price_list[$i]["prices_list_data"]))+1;//��ȡ�۸��ļ���ֵ

    $new_price_list[$i]['section_diy_count'] = $section_diy_count;
    $new_price_list[$i]['data_mark'] = $i+1;
    $i++;

}
//ҳ���ʼ����Ҫ��ֵ
$section_count = count($new_price_list);
$section_count = (int)$section_count+1;


if($yue_login_id==100004)
{
    //print_r($new_price_list);
}

//ҳ���ʼ������ѡ����Ҫ��ֵ
$big_checked_status = 0;//ȫ��ѡ��
$small_checked_status = 0;//����ѡ��
if(!empty($page_data["default_data"]["location_id"]["value"]))
{
    $small_checked_status = 1;
}
else
{
    $big_checked_status = 1;
}
//����ȫ������������
$locate_config_arr  = array(
    array("name"=>"ȫ��","locate_value"=>0,"is_select"=>$big_checked_status),
    array("name"=>"����ѡ��","locate_value"=>1,"is_select"=>$small_checked_status),

);

//��־�γ��
if(!empty($page_data["default_data"]["lat_lon"]["value"]))
{
    $lat_lon_arr = explode(",",$page_data["default_data"]["lat_lon"]["value"]);
    $lng = $lat_lon_arr[0];
    $lat = $lat_lon_arr[1];
    $tpl->assign("lng",$lng);
    $tpl->assign("lat",$lat);
}

//�����������ҳ���ʼ��ֵ������ʹ��
$diy_count = count($contact_list);
$diy_count = (int)$diy_count+1;
//����ѡ��ʱ����С���ڣ���ǰ��
$now_date = date("Y-m-d",time());


/*******����ʹ��**********/
//����ϵͳ��Ϣ��ʾ
$hide_system_msg = 0;
//����״̬��������ʾ
$cur_status = $page_data["goods_data"]["status"];//��Ʒ��ˣ�0��δ��ˣ�1��ͨ����2����ͨ��
$cur_edit_status = $page_data["goods_data"]["edit_status"];//��޸ĵ���ˣ��ר�У�0����һ����ˣ�����У�1�����޸Ĺ����ǵ�һ��������У�δͨ������ʾ�Ĳ��������ύ���ݣ�2��ͨ����˲����µ����£�3����˲�ͨ������ʾ�Ĳ�������

if($cur_edit_status==0)
{
    $system_msg = "test���û���ڵ�һ����ˣ�����У�edit_status��".$cur_edit_status;
}
else if($cur_edit_status==1)
{
    $system_msg = "test���û���޸Ĺ����ǵ�һ��������У���δͨ������ʾ�Ĳ��������ύ���ݣ�edit_status��".$cur_edit_status;
}
else if($cur_edit_status==2)
{
    $system_msg = "test���û����ͨ����˲����µ��������ݣ�edit_status��".$cur_edit_status;
}
else if($cur_edit_status==3)
{
    $system_msg = "test���û������˲�ͨ����֮ǰ�ύ����޸ĵ�������Ч����ʾ�Ĳ��������ύ���ݣ�edit_status��".$cur_edit_status;
}
/*******����ʹ��**********/




$tpl->assign("locate_config_arr",$locate_config_arr);
$tpl->assign("big_checked_status",$big_checked_status);
$tpl->assign("now_date",$now_date);
$tpl->assign("diy_time",time()."0");//����һλ������������IDֵһ��
$tpl->assign("diy_time_md5",md5(time()."0"));//����һλ������������IDֵһ��
$tpl->assign("diy_count",$diy_count);
$tpl->assign("contact_list",$contact_list);
$tpl->assign("section_count",$section_count);
$tpl->assign("system_msg",$system_msg);



?>