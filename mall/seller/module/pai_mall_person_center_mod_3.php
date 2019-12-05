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

//��ױģ��༭
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
//��ױ����
$att_data['hz_experience'] = $ret[0]['att_data_format']['hz_experience'];
$hz_experience_array = construct_select_list($att_data,'hz_experience');

//���컯ױʦ�Ŷ�����
$att_data['hz_team'] = $ret[0]['att_data_format']['hz_team'];
$hz_team_array = construct_radio_list($att_data,'hz_team');
//��ױ�̼��ó�
$att_data['hz_goodat'] = $ret[0]['att_data_format']['hz_goodat'];
$hz_goodat_array = construct_checkbox_list($att_data,'hz_goodat');

$hz_othergoodat = $ret[0]['att_data_format']['hz_othergoodat']['value'];



//����
$hz_experience_html = "";
foreach($hz_experience_array as $k => $v)
{
    $hz_experience_html .=  '<option data-role="change_check" value="'.$v["hz_experience_value"].'" '.$v["sel"].' >'.$v['hz_experience_value'].'</option>';

}
$hz_team_html = "";
foreach($hz_team_array as $k => $v)
{
    $hz_team_html .= '<label><input data-role="change_check" type="radio" name="'.$v["name"].'" value="'.$v["hz_team_value"].'" '.$v["sel"].' class="J_hz_team"/>'.$v["hz_team_value"].'</label>';
}
//����
$hz_goodat_html = "";
foreach($hz_goodat_array as $k => $v)
{
    $hz_goodat_html .= '<label><input data-role="change_check" type="checkbox" name="'.$v["name"].'[]" value="'.$v["hz_goodat_value"].'" '.$v["sel"].' class="J_hz_goodat"/>'.$v["hz_goodat_value"].'</label>';
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
                                <div class="item clearfix">
                                    <h3 class="title" style="width: 100px">��ҵ���ޣ�</h3>
                                    <div class="from-con" style="width: 680px">
                                        <div class="mod-select-item photo-select-item fl mr10">
                                            <select data-role="change_check" name="'.$att_data["hz_experience"]["key"].'" class="font_wryh" value="'.$att_data["hz_experience"]["value"].'" valid-rule="**1-10" valid-index="17" id="hz_experience" valid_group="service_match_module_'.$type_id.'">
                                                <option value="">��ѡ��</option>
                                                '.$hz_experience_html.'
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="item editing-from-wrap">
                                    <div class="mod-form-title">
                                        <span>�Ŷӹ�ģ��</span>
                                    </div>
                                    <div class="from-con">
                                        <div class="mod-radio-item clearfix">
                                            '.$hz_team_html.'
                                        </div>
                                    </div>
                                </div>
                                <div class="item editing-from-wrap">
                                    <div class="mod-form-title">
                                        <span>�ó�ױ�ݣ�</span>
                                    </div>
                                    <div class="from-con">
                                        <div class="mod-checkbox-item clearfix">
                                            '.$hz_goodat_html.'
                                            <label>
                                                <input data-role="change_check" value="'.$hz_othergoodat.'" type="text" name="hz_othergoodat" class="checkbox-input font_wryh" placeholder="����"  valid-rule="**0-100" valid-index="100" tabindex="1" valid_group="service_match_module_'.$type_id.'"/>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';

echo $coustruct_html;
?>