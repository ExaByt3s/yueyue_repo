<?php
/*
 * //�̼Ҹ������ı༭ģ��
 *
 * author ����
 *
 * 2015-9-6
 *
 */

include_once '../common.inc.php';
include_once './ajax_module_function_com.php';//���ݽṹ�Ĵ�����

//��ѵģ��༭
$type_id = (int)$_INPUT['type_id'];//����ID
$user_id = $yue_login_id;

//У��
if($type_id <1)
{
    $arr['msg'] = 'type_id empty';
    $arr['status'] = -1;
    echo json_encode($arr);
    exit;
}

if($user_id <1)
{
    $arr['msg'] = 'user_id empty';
    $arr['status'] = -1;
    echo json_encode($arr);
    exit;
}

/**�������Ĵ���**/
$mall_obj = POCO::singleton('pai_mall_seller_class');
$seller_info=$mall_obj->get_seller_info($user_id,2);
$seller_name=$seller_info['seller_data']['name'];
if(empty($seller_name))
{
    //û�н����̼���֤��
    $arr['msg'] = 'seller_name empty';
    $arr['status'] = -1;
    echo json_encode($arr);
    exit;
}

if(!is_array($seller_info)) $seller_info = array();
$seller_profile_id = intval($seller_info['seller_data']['profile'][0]['seller_profile_id']);
//print_r($seller_info['seller_data']['profile'][0]);

if($seller_profile_id <1)
{
    //û�н����̼���֤��
    $arr['msg'] = 'seller_profile_id empty';
    $arr['status'] = -1;
    echo json_encode($arr);
    exit;
}

/**��Ӧ����**/
$ret = $mall_obj->get_seller_profile($seller_profile_id);
//��������������ݱ�ǩ
$att_data['ot_label'] = $ret[0]['att_data_format']['ot_label'];
$ot_label_array = construct_checkbox_list($att_data,'ot_label');

$ot_otherlabel = $ret[0]['att_data_format']['ot_otherlabel']['value'];




$ot_label_html = "";
foreach($ot_label_array as $k => $v)
{
    if($v["ot_label_value"]=="����")
    {
        $ot_label_html .= '<br/><label style="line-height:30px;padding-right:10px;"><input data-role="change_check" type="checkbox" name="'.$v["name"].'[]" value="'.$v["ot_label_value"].'" '.$v["sel"].' class="J_ot_label"/>'.$v["ot_label_value"].'</label>';
    }
    else
    {
        $ot_label_html .= '<label style="line-height:30px;"><input data-role="change_check" type="checkbox" name="'.$v["name"].'[]" value="'.$v["ot_label_value"].'" '.$v["sel"].' class="J_ot_label"/>'.$v["ot_label_value"].'</label>';
    }
}





//ͳһ����type_id����ҳ������(2015-10-27)
$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$mod_title = $pai_mall_goods_obj->get_goods_typename_for_type_id($type_id);

$coustruct_html =  '<div class="mode-sc J_type_div" id="service_match_module_'.$type_id.'" >
                        <div class="packages-List">
                            <div class="item clearfix">
                                <div class="listItem">
                                    <span>'.$mod_title.'�̼�</span>
                                </div>
                                <div class="item editing-from-wrap">
                                    <div class="mod-form-title fl">
                                        <span>ѡ�������ݱ�ǩ����������������</span>
                                    </div>
                                    <div class="from-con" style="width:460px;">
                                        <div class="mod-checkbox-item clearfix">
                                            '.$ot_label_html.'
                                            <label>
                                                <input data-role="change_check" value="'.$ot_otherlabel.'" type="text" name="ot_otherlabel" class="checkbox-input font_wryh" placeholder="����"  valid-rule="**0-100" valid-index="101" tabindex="1" valid_group="service_match_module_'.$type_id.'"/>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';


echo $coustruct_html;
?>