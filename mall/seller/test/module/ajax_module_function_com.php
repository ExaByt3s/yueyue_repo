<?php
/******2015-9-4构造符合当前数据结构使用的公共方法*********/
/*
 *
 * @parma array $sourece_data 源数据
 * @parma string key_name 要处理对应key值
 * return array 返回符合结构的数组
 *
 */

//构造下拉列表公共方法
function construct_select_list($source_data,$key_name)
{

    //配置下拉列表数组
    $tmp_array = array();
    foreach($source_data[$key_name]['child_data'] as $key => $value)
    {
        $tmp_array[$key][$key_name.'_value'] = $value;
    }

    //下拉被选中
    foreach($tmp_array as $key => $value)
    {
        $sel ='';

        if($value[$key_name.'_value'] == $source_data[$key_name]['value']) $sel ="selected=true";
        $tmp_array[$key]['sel'] = $sel;
    }
    return $tmp_array;

}
//构造单选公共方法
function construct_radio_list($source_data,$key_name)
{
    //配置单选数组
    $tmp_array = array();
    foreach($source_data[$key_name]['child_data'] as $key => $value)
    {
        $tmp_array[$key][$key_name.'_value'] = $value;
        $tmp_array[$key]['name'] = $source_data[$key_name]['key'];

    }
    //单选被选中
    foreach($tmp_array as $key => $value)
    {
        $sel ='';

        if($value[$key_name.'_value'] == $source_data[$key_name]['value']) $sel ="checked=checked";
        $tmp_array[$key]['sel'] = $sel;
    }
    //默认第一个选中处理
    if($sel=="")
    {
        $tmp_array[0]['sel'] = "checked=checked";
    }
    return $tmp_array;
}

//构造多选公共方法
function construct_checkbox_list($source_data,$key_name)
{
    //配置单选数组
    $tmp_array = array();
    foreach($source_data[$key_name]['child_data'] as $key => $value)
    {
        $tmp_array[$key][$key_name.'_value'] = $value;
        $tmp_array[$key]['name'] = $source_data[$key_name]['key'];

    }
    //拆分数组
    $explode_array = explode(",",$source_data[$key_name]['value']);
    //多选被选中
    $sel_status = false;//有选中
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
    //默认第一个选中处理
    if(!$sel_status)
    {
        $tmp_array[0]['sel'] = "checked=checked";
    }
    return $tmp_array;
}




?>