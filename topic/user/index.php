<?php
header("Location:http://www.yueus.com/model/");
exit();
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('pai_topic_common.inc.php');

if(!$yue_login_id) header("Location:login.php");

$area_array = array("广州", "北京", "上海", "成都", "南京", "江苏", "湖南", "湖北", "广东", "福建", "辽宁", "黑龙江", "山西", "海南", "天津", "重庆", "内蒙古", "河北", "吉林", "安徽", "山东", "浙江", "江西", "广西", "贵州", "四川", "云南", "陕西", "甘肃", "宁夏", "新疆", "西藏");

$act = $_REQUEST['act'];

if($act == 'logout')
{
    $pai_user_obj 		= POCO::singleton('pai_user_class');
    $pai_user_obj->logout();
    print_message_jump_url('退出成功！', 'index.php');
}

if($act == 'edit')
{
    $nickname       = $_POST['nickname'];
    $price          = $_POST['price'];
    $discount       = $_POST['discount'];
    $area           = $_POST['area'];
    $height         = $_POST['height'];
    $weight         = $_POST['weight'];
    $cup            = $_POST['cup'];
    $cup_num        = $_POST['cup_num'];
    $b_size         = $_POST['b_size'];
    $w_size         = $_POST['w_size'];
    $h_size         = $_POST['h_size'];
    $information    = $_POST['information'];
    $shot_type      = $_POST['shot_type'];
    $signature      = $_POST['signature'];

    //print_r($_FILES);
    $my_img        = fileupload($_FILES['icon']['tmp_name'],$_FILES['icon']['name'],$_FILES['icon']['type'],$_FILES['icon']['size'],1024*10000000);
    if($my_img['image_url'])
    {
        $icon_url = $my_img['image_url'];
    }

    if($_POST['img'])
    {
        $img_array      = serialize($_POST['img']);
    }


    $bwh = $b_size . '-' . $w_size . '-' . $h_size;

    $sql_str = "UPDATE pai_topic_db.pai_topic_user_info_tbl SET
                nickname=:x_nickname,
                price=:x_price,
                area=:x_area,
                height=:x_height,
                weight=:x_weight,
                cup=:x_cup,
                cup_num=:x_c_u_num, 
                bwh=:x_bwh,
                shot_type=:x_shot_type,
                signature=:x_signature,
                information=:x_information,";
    if($img_array) $sql_str .= " img=:x_img, ";
    if($icon_url)  $sql_str .= " icon=:x_icon, ";
    $sql_str .= "b_size=:x_b_size,
                w_size=:x_w_size,
                h_size=:x_h_size
                WHERE yue_user_id=$yue_login_id";

    sqlSetParam($sql_str, 'x_nickname', $nickname);
    sqlSetParam($sql_str, 'x_price', $price);
    sqlSetParam($sql_str, 'x_area', $area);
    sqlSetParam($sql_str, 'x_height', $height);
    sqlSetParam($sql_str, 'x_weight', $weight);
    sqlSetParam($sql_str, 'x_cup', $cup);
    sqlSetParam($sql_str, 'x_c_u_num', $cup_num);
    sqlSetParam($sql_str, 'x_bwh', $bwh);
    sqlSetParam($sql_str, 'x_shot_type', $shot_type);
    sqlSetParam($sql_str, 'x_signature', $signature);
    sqlSetParam($sql_str, 'x_information', $information);
    sqlSetParam($sql_str, 'x_icon', $icon_url);
    sqlSetParam($sql_str, 'x_img', $img_array);
    sqlSetParam($sql_str, 'x_b_size', $b_size);
    sqlSetParam($sql_str, 'x_w_size', $w_size);
    sqlSetParam($sql_str, 'x_h_size', $h_size);
    db_simple_getdata($sql_str, TRUE, 101);
    if(db_simple_get_affected_rows() < 1)
    {
        $sql_str = "INSERT IGNORE INTO pai_topic_db.pai_topic_user_info_tbl(yue_user_id, nickname, price,  information, `area`, height, weight, cup_num, cup, bwh, shot_type, signature, icon,  img, b_size, w_size, h_size)
                    VALUES ($yue_login_id, :x_nickname, :x_price, :x_information, :x_area, :x_height, :x_weight, :x_c_u_num, :x_cup, :x_bwh, :x_shot_type, :x_signature, :x_icon, :x_img, :x_b_size, :x_w_size, :x_h_size)";

        sqlSetParam($sql_str, 'x_nickname', $nickname);
        sqlSetParam($sql_str, 'x_price', $price);
        sqlSetParam($sql_str, 'x_area', $area);
        sqlSetParam($sql_str, 'x_height', $height);
        sqlSetParam($sql_str, 'x_weight', $weight);
        sqlSetParam($sql_str, 'x_cup', $cup);
        sqlSetParam($sql_str, 'x_c_u_num', $cup_num);
        sqlSetParam($sql_str, 'x_bwh', $bwh);
        sqlSetParam($sql_str, 'x_shot_type', $shot_type);
        sqlSetParam($sql_str, 'x_signature', $signature);
        sqlSetParam($sql_str, 'x_information', $information);
        sqlSetParam($sql_str, 'x_img', $img_array);
        sqlSetParam($sql_str, 'x_b_size', $b_size);
        sqlSetParam($sql_str, 'x_w_size', $w_size);
        sqlSetParam($sql_str, 'x_h_size', $h_size);
        sqlSetParam($sql_str, 'x_icon', $icon_url);

        db_simple_getdata($sql_str, TRUE, 101);
    }

    print_message('数据更新成功！');

}

$sql_str = "SELECT * FROM pai_topic_db.pai_topic_user_info_tbl WHERE yue_user_id=$yue_login_id";
$result = db_simple_getdata($sql_str, TRUE, 101);
$array_img = unserialize($result['img']);

$area_str = "";
foreach($area_array AS $val)
{
    if($val == $result['area'])
    {
        $area_str .= '<option value="' . $val . '" selected >' . $val . '</option>';
    }else{
        $area_str .= '<option value="' . $val . '" >' . $val . '</option>'; 
    }
}
//$result['img_content'] = '<input name="icon" type="hidden" value="' . $result['icon']. '" />"';
$num=0;
foreach($array_img AS $key=>$val)
{
    $num = (int)$key+1;
    if($val == $result['icon'])
    {
       $result['img_content'] .= '<input name="img[]" type="hidden" value="' . $val. '" id="img_input_'.$num.'"/><img  id="img_div_'.$num.'" src="' . $val . '" width="200px" border="2px" /><span class="delete_span" id="span_div_'.$num.'" onclick="delete_img('. $num .');" >删除</span>';
    }else{
       $result['img_content'] .= '<input name="img[]" type="hidden" value="' . $val. '" id="img_input_'.$num.'"/><img  id="img_div_'.$num.'" src="' . $val . '" width="200px" /><span class="delete_span" id="span_div_'.$num.'" onclick="delete_img('. $num .');" >删除</span>';
    }

    //if($num%4 == 0 ) $result['img_content'] .= "<BR>";
}
$tpl = new SmartTemplate("index.tpl.html");

$cup_n = 'cup_num_' . $result['cup_num'];
$tpl->assign($cup_n, 'selected');

$cup_d = 'cup_' . $result['cup'];
$tpl->assign($cup_d, 'selected');

//控制图片的下标
$tpl->assign("area_str", $area_str);
$tpl->assign("num",$num);
$tpl->assign($result);
$tpl->output();
?>
