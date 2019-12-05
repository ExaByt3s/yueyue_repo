<?php
/*
 * //商家个人中心编辑模块
 *
 * author 星星
 *
 * 2015-9-6
 *
 */

include_once '../common.inc.php';
include_once './ajax_module_function_com.php';//数据结构的处理函数

//培训模块编辑
$type_id = (int)$_INPUT['type_id'];//类型ID
$user_id = $yue_login_id;

//校验
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

/**公共查阅处理**/
$mall_obj = POCO::singleton('pai_mall_seller_class');
$seller_info=$mall_obj->get_seller_info($user_id,2);
$seller_name=$seller_info['seller_data']['name'];
if(empty($seller_name))
{
    //没有进行商家认证的
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
    //没有进行商家认证的
    $arr['msg'] = 'seller_profile_id empty';
    $arr['status'] = -1;
    echo json_encode($arr);
    exit;
}

/**对应处理**/
$ret = $mall_obj->get_seller_profile($seller_profile_id);
//构造培训商家老师类型数组
$att_data['t_teacher'] = $ret[0]['att_data_format']['t_teacher'];
$t_teacher_array = construct_radio_list($att_data,'t_teacher');


//构造培训商家培训经验数组
$att_data['t_experience'] = $ret[0]['att_data_format']['t_experience'];
$t_experience_array = construct_select_list($att_data,'t_experience');


$t_teacher_html = "";
foreach($t_teacher_array as $k => $v)
{
    $t_teacher_html .= '<label><input data-role="change_check" type="radio" name="'.$v["name"].'" value="'.$v["t_teacher_value"].'" '.$v["sel"].' class="J_t_teacher"/>'.$v["t_teacher_value"].'</label>';
}
//年限
$t_experience_html = "";
foreach($t_experience_array as $k => $v)
{
    $t_experience_html .=  '<option value="'.$v["t_experience_value"].'" '.$v["sel"].' >'.$v["t_experience_value"].'</option>';

}



//统一根据type_id控制页面名称(2015-10-27)
$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$mod_title = $pai_mall_goods_obj->get_goods_typename_for_type_id($type_id);

$coustruct_html =  '<div class="mode-sc J_type_div" id="service_match_module_'.$type_id.'" >
                        <div class="packages-List">
                            <div class="item clearfix">
                                <div class="listItem">
                                    <span>'.$mod_title.'商家</span>
                                </div>
                                <div class="item editing-from-wrap">
                                    <div class="mod-form-title">
                                        <span>老师类型：</span>
                                    </div>
                                    <div class="from-con">
                                        <div class="mod-radio-item clearfix">
                                            '.$t_teacher_html.'
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="item clearfix">
                                <h3 class="title" style="width: 100px">培训经验：</h3>
                                <div class="from-con" style="width: 680px">
                                    <div class="mod-select-item photo-select-item fl mr10">
                                        <select data-role="change_check" name="'.$att_data["t_experience"]["key"].'" class="font_wryh" value="'.$att_data["t_experience"]["value"].'" valid-rule="**1-10" valid-index="15" id="t_experience" valid_group="service_match_module_'.$type_id.'">
                                            <option value="">请选择</option>
                                            '.$t_experience_html.'
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';

echo $coustruct_html;
?>