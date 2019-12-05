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

//ģ��ģ��༭
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
$att_data['m_height'] = $ret[0]['att_data_format']['m_height'];
$att_data['m_weight'] = $ret[0]['att_data_format']['m_weight'];
$att_data['m_bwh']    = $ret[0]['att_data_format']['m_bwh'];
$att_data['m_cups']   = $ret[0]['att_data_format']['m_cups'];
$att_data['m_cup']    = $ret[0]['att_data_format']['m_cup'];
$att_data['m_level']  = $ret[0]['att_data_format']['m_level'];

$att_data['m_experience'] = $ret[0]['att_data_format']['m_experience'];
//����ģ�ؾ�������
$m_experience_array = construct_select_list($att_data,'m_experience');


//print_r($att_data);
//print_r($m_experience_array);
//exit();
//����ģ��CUP����
$cup_num_array = array(
    array("cup"=>"30"),
    array("cup"=>"32"),
    array("cup"=>"34"),
    array("cup"=>"36"),
    array("cup"=>"38")
);//cup��������ʽ����
$cup_english_array = array(
    array("cup"=>"A"),
    array("cup"=>"B"),
    array("cup"=>"C"),
    array("cup"=>"D"),
    array("cup"=>"E+")
);//cup��Ӣ����ʽ����

//��ѡ���cup
foreach($cup_num_array as $keyN=>$vCupn)
{
    $sel ='';
    if($vCupn['cup'] == $att_data['m_cups']['value']) $sel ="selected=true";
    $cup_num_array[$keyN]['sel'] = $sel;
}
foreach($cup_english_array as $keyE=>$vCupe)
{
    $sel ='';

    if($vCupe['cup'] == $att_data['m_cup']['value']) $sel ="selected=true";
    $cup_english_array[$keyE]['sel'] = $sel;
}

//��ΧĬ��ֵ
$bwh_data = array(
    'b' => '',
    'w' => '',
    'h' => ''
);

list($bwh_data['b'],$bwh_data['w'],$bwh_data['h']) = explode('-',$att_data['m_bwh']['value']);



//ѭ����ģ�鹹��
//ģ�ؾ���
$m_experience_html = "";
foreach($m_experience_array as $k => $v)
{
    $m_experience_html .=  '<option value="'.$v["m_experience_value"].'" '.$v["sel"].' >'.$v["m_experience_value"].'</option>';

}
//ģ���ֱ�
$cup_num_html = "";
foreach($cup_num_array as $k => $v)
{
    $cup_num_html .=  '<option value="'.$v["cup"].'" '.$v["sel"].' >'.$v["cup"].'</option>';

}
//ģ���ֱ���ӦӢ������
$cup_english_html = "";
foreach($cup_english_array as $k => $v)
{
    $cup_english_html .=  '<option value="'.$v["cup"].'" '.$v["sel"].' >'.$v["cup"].'</option>';

}

//��ʾ�߼�����
$choose_style_1 = "";
$choose_style_2 = "";
$choose_style_3 = "";
if($att_data['m_level']['value']==1)
{
    $choose_style_1 = 'class="cur"';
}
else if($att_data['m_level']['value']==2)
{
    $choose_style_2 = 'class="cur"';
}
else if($att_data['m_level']['value']==3)
{
    $choose_style_3 = 'class="cur"';
}



//����Html����
//ͳһ����type_id����ҳ������(2015-10-27)
$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$mod_title = $pai_mall_goods_obj->get_goods_typename_for_type_id($type_id);

$coustruct_html =  '<div class="mode-sc J_type_div" id="service_match_module_'.$type_id.'">
                    <div class="packages-List">
                        <div class="listItem">
                            <span>'.$mod_title.'�̼�</span>
                        </div>
                        <div class="item clearfix">
                            <h3 class="title" style="width: 100px">ģ�ؾ��飺</h3>
                            <div class="from-con" style="width: 680px">
                                <div class="mod-select-item photo-select-item fl mr10">
                                    <select data-role="change_check" name="'.$att_data["m_experience"]["key"].'" class="font_wryh" value="'.$att_data["m_experience"]["value"].'" valid-rule="**1-10" valid-index="14" id="m_experience" valid_group="service_match_module_'.$type_id.'">
                                        <option value="">��ѡ��</option>
                                        '.$m_experience_html.'
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="item clearfix">
                            <h3 class="title">��ߣ�</h3>
                            <div class="from-con">
                                <div class="mod-input-item fl">
                                    <input data-role="change_check" type="text" class="input-txt input-txt-3 font_wryh" placeholder="" value="'.$att_data["m_height"]["value"].'" name="'.$att_data["m_height"]["key"].'" valid-rule="!z1-3" valid-index="4" valid_group="service_match_module_'.$type_id.'"/>
                                </div>
                                <span class="unit fl">CM</span>
                            </div>
                        </div>
                        <div class="item clearfix">
                            <h3 class="title">���أ�</h3>
                            <div class="from-con">
                                <div class="mod-input-item fl">
                                    <input data-role="change_check" type="text" class="input-txt input-txt-3 font_wryh" placeholder="" name="'.$att_data["m_weight"]["key"].'" value="'.$att_data["m_weight"]["value"].'" valid-rule="!z1-3" valid-index="5" valid_group="service_match_module_'.$type_id.'"/>
                                </div>
                                <span class="unit fl">KG</span>
                            </div>
                        </div>
                        <div class="item clearfix">
                            <h3 class="title">��Χ��</h3>
                            <div class="from-con">
                                <div class="mod-input-item fl">
                                    <input data-role="change_check" type="text" class="input-txt input-txt-3 font_wryh" placeholder="��Χ" name="b" value="'.$bwh_data["b"].'" valid-rule="!z1-10" valid-index="6" valid_group="service_match_module_'.$type_id.'"/>
                                </div>
                                <span class="unit fl mr10">CM</span>
                                <div class="mod-input-item fl">
                                    <input data-role="change_check" type="text" class="input-txt input-txt-3 font_wryh" placeholder="��Χ" name="w" value="'.$bwh_data["w"].'" valid-rule="!z1-10" valid-index="7" valid_group="service_match_module_'.$type_id.'"/>
                                </div>
                                <span class="unit fl mr10">CM</span>
                                <div class="mod-input-item fl">
                                    <input data-role="change_check" type="text" class="input-txt input-txt-3 font_wryh" placeholder="��Χ" name="h" value="'.$bwh_data["w"].'" valid-rule="nb1-10" valid-index="8" valid_group="service_match_module_'.$type_id.'"/>
                                </div>
                                <span class="unit fl">CM</span>
                            </div>
                        </div>
                        <div class="item clearfix">
                            <h3 class="title">CUP��</h3>
                            <div class="from-con">
                                <div class="mod-select-item photo-select-item fl mr10">
                                    <select data-role="change_check" name="'.$att_data["m_cups"]["key"].'" class="font_wryh" value="'.$att_data["m_cups"]["value"].'" valid-rule="nb1-10" valid-index="9" id="m_cups" valid_group="service_match_module_'.$type_id.'">
                                        <option value="">��ѡ��</option>
                                        '.$cup_num_html.'
                                    </select>
                                </div>
                                <div class="mod-select-item photo-select-item fl">
                                    <select data-role="change_check" name="'.$att_data["m_cup"]["key"].'" class="font_wryh" value="'.$att_data["m_cup"]["value"].'" valid-rule="**1-10" valid-index="10" id="m_cup" valid_group="service_match_module_'.$type_id.'">
                                        <option value="">��ѡ��</option>
                                        '.$cup_english_html.'
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="item">
                        <div class="mod-form-title">
                            <span>���õȼ�Ҫ��</span> <em class="tips">(��ӰʦԼ�������֤Ҫ��)</em>
                        </div>
                        <div class="item-con">
                            <div class="credit-rating-List clearfix" id="credit-rating-List">
                                <ul>
                                    <li '.$choose_style_1.' >
                                         <div class="credit-item">
                                         <a href="javascript:void(0);" data-role="1"><span>V1�ֻ���֤</span></a>
                                         <i class="top-icon"><b></b></i>
                                     </div>
                                        <div class="tips-pop">
                                            <div class="pop-con">V1���Է��ѻ���ֻ���֤</div>
                                        </div>
                                    </li>
                                    <li '.$choose_style_2.' >
                                       <div class="credit-item">
                                       <a href="javascript:void(0);" data-role="2"><span>V2ʵ����֤</span></a>
                                       <i class="top-icon"><b></b></i>
                                       </div>
                                        <div class="tips-pop">
                                            <div class="pop-con">V2���Է��ѻ��ʵ����֤</div>
                                        </div>
                                    </li>
                                    <li '.$choose_style_3.' >
                                        <div class="credit-item">
                                        <a href="javascript:void(0);" data-role="3"><span>V3������֤</span></a>
                                        <i class="top-icon"><b></b></i>
                                    </div>
                                        <div class="tips-pop">
                                            <div class="pop-con">V3���Է��ѻ�ô�����֤</div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <!--�ȼ�����ֵ-->
                            <input type="hidden" value="'.$att_data["m_level"]["value"].'" name="'.$att_data['m_level']['key'].'" id="m_level" valid-rule="nb1-2" valid-index="11" valid_group="service_match_module_'.$type_id.'"/>
                        </div>
                    </div>
                </div>';



echo $coustruct_html;
?>