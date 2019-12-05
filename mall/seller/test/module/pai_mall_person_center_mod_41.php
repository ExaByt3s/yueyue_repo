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

//��ʳģ��༭
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
//������ʳ������������
$att_data['ms_experience'] = $ret[0]['att_data_format']['ms_experience'];
$ms_experience_array = construct_radio_list($att_data,'ms_experience');
//�����������
$att_data['ms_certification'] = $ret[0]['att_data_format']['ms_certification'];
$ms_certification_array = construct_radio_list($att_data,'ms_certification');


$ms_experience_html = "";
foreach($ms_experience_array as $k => $v)
{
    $ms_experience_html .= '<label><input data-role="change_check" type="radio" name="'.$v["name"].'" value="'.$v["ms_experience_value"].'" '.$v["sel"].' class="J_ms_experience"/>'.$v["ms_experience_value"].'</label>';
}

$ms_certification_html = "";
foreach($ms_certification_array as $k => $v)
{
    $ms_certification_html .= '<label><input data-role="change_check" type="radio" name="'.$v["name"].'" value="'.$v["ms_certification_value"].'" '.$v["sel"].' class="J_ms_certification"/>'.$v["ms_certification_value"].'</label>';
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
                                    <div class="mod-form-title">
                                        <span>��Ĵ�ҵ�����ǣ�</span>
                                    </div>
                                    <div class="from-con">
                                        <div class="mod-radio-item clearfix">
                                            '.$ms_experience_html.'
                                        </div>
                                    </div>
                                </div>
                                <div class="item editing-from-wrap">
                                    <div class="mod-form-title">
                                        <span>��ϣ������֤������ǣ�</span>
                                    </div>
                                    <div class="from-con">
                                        <div class="mod-radio-item clearfix">
                                            '.$ms_certification_html.'
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>';


echo $coustruct_html;


?>