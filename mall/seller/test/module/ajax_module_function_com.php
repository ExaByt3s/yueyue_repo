<?php
/******2015-9-4������ϵ�ǰ���ݽṹʹ�õĹ�������*********/
/*
 *
 * @parma array $sourece_data Դ����
 * @parma string key_name Ҫ�����Ӧkeyֵ
 * return array ���ط��Ͻṹ������
 *
 */

//���������б�������
function construct_select_list($source_data,$key_name)
{

    //���������б�����
    $tmp_array = array();
    foreach($source_data[$key_name]['child_data'] as $key => $value)
    {
        $tmp_array[$key][$key_name.'_value'] = $value;
    }

    //������ѡ��
    foreach($tmp_array as $key => $value)
    {
        $sel ='';

        if($value[$key_name.'_value'] == $source_data[$key_name]['value']) $sel ="selected=true";
        $tmp_array[$key]['sel'] = $sel;
    }
    return $tmp_array;

}
//���쵥ѡ��������
function construct_radio_list($source_data,$key_name)
{
    //���õ�ѡ����
    $tmp_array = array();
    foreach($source_data[$key_name]['child_data'] as $key => $value)
    {
        $tmp_array[$key][$key_name.'_value'] = $value;
        $tmp_array[$key]['name'] = $source_data[$key_name]['key'];

    }
    //��ѡ��ѡ��
    foreach($tmp_array as $key => $value)
    {
        $sel ='';

        if($value[$key_name.'_value'] == $source_data[$key_name]['value']) $sel ="checked=checked";
        $tmp_array[$key]['sel'] = $sel;
    }
    //Ĭ�ϵ�һ��ѡ�д���
    if($sel=="")
    {
        $tmp_array[0]['sel'] = "checked=checked";
    }
    return $tmp_array;
}

//�����ѡ��������
function construct_checkbox_list($source_data,$key_name)
{
    //���õ�ѡ����
    $tmp_array = array();
    foreach($source_data[$key_name]['child_data'] as $key => $value)
    {
        $tmp_array[$key][$key_name.'_value'] = $value;
        $tmp_array[$key]['name'] = $source_data[$key_name]['key'];

    }
    //�������
    $explode_array = explode(",",$source_data[$key_name]['value']);
    //��ѡ��ѡ��
    $sel_status = false;//��ѡ��
    foreach($tmp_array as $key => $value)
    {
        $sel ='';

        if(in_array($value[$key_name.'_value'],$explode_array)) $sel ="checked=checked";
        $tmp_array[$key]['sel'] = $sel;
        if(!empty($sel))
        {
            $sel_status = true;
        }
    }
    //Ĭ�ϵ�һ��ѡ�д���
    if(!$sel_status)
    {
        $tmp_array[0]['sel'] = "checked=checked";
    }
    return $tmp_array;
}




?>