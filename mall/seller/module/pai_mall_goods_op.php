<?php

/**
 * 服务认证操作页
 *
 * 2015-6-16
 *
 * author  星星
 *
 */
define('G_SIMPLE_INPUT_CLEAN_VALUE',1);
include_once '../common.inc.php';
if($yue_login_id==134207)
{
    //此用户问题处理好后删除
    //临时日志  http://yp.yueus.com/logs/201509/23_goods_op.txt
    pai_log_class::add_log(array(), 'goods_op', 'goods_op');
}


$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$pai_mall_goods_type_attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');


/*if($yue_login_id==100004)
{
    var_dump($_POST);
    $default_data = $_INPUT['default_data']['content'];
    $editorValue = $_INPUT['editorValue'];
    echo "<script>top.alert('".$default_data."');top.alert('".$editorValue."');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit("测试");
}*/

/*******美食达人特殊处理2015-10-20********/
$update_detail = (int)$_INPUT['update_detail'];
/*******美食达人特殊处理2015-10-20********/
$type_id = (int)$_INPUT['type_id'];//模板要传入
//$store_id = (int)$_INPUT['store_id'];//模板要传入
$goods_id = (int)$_INPUT['goods_id'];//编辑使用
$type_id_array = array(3,5,12,31,40,41,43);

if(empty($yue_login_id))
{
    echo "<script>top.alert('没有登录');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
$user_id = $yue_login_id;

//获取用户信息
$mall_obj = POCO::singleton('pai_mall_seller_class');
$seller_info=$mall_obj->get_seller_info($user_id,2);
$seller_store_id=$seller_info['seller_data']['company'][0]['store'][0]['store_id'];
//echo "store_id {$seller_store_id}";




if(!in_array($type_id,$type_id_array))
{
    echo "<script>top.alert('type_id 错误');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}
$action = trim($_INPUT['action']);//操作
$action_array = array("add","edit","money_edit","preview");
if(empty($action) || !in_array($action,$action_array))
{
    $action = "add";
}
//处理相关数据

//exit();
//循环数据校验处理
$data_error = "";
if(!empty($_INPUT['default_data']))
{
    //2015-8-20，兼容浏览器处理
    if($_INPUT['editorValue']!="")
    {
        $_INPUT['default_data']['content'] = trim($_INPUT['editorValue']);
    }
    //2015-8-20，兼容浏览器处理
    foreach($_INPUT['default_data'] as $key => $value)
    {
        if(empty($value))
        {
            //摄影服务没有图文内容
            if($key=="content")
            {
                if($type_id==40)
                {
                    continue;
                }
            }
            //摄影服务没有图文内容

            $data_error = $pai_mall_goods_type_attribute_obj->get_name_by_key($key);
            $data_error_tips = "data_error";
            break;
        }
        else
        {
            $_INPUT['default_data'][$key] = trim($value);
            //对图文编辑器做的过滤处理2015-7-6
            if($key=="content")
            {
                /*****2015-8-24校验图文编辑器内容图片渲染情况********/
                $loadingclass_res = strpos($_INPUT['default_data'][$key],"loadingclass");
                if($loadingclass_res>0)
                {
                    $data_error = "服务详情有图片在加载，";
                    $data_error_tips = "data_error";
                }
                /*****2015-8-24校验图文编辑器内容图片渲染情况********/

                //echo $_INPUT['default_data'][$key];
                //src链接校验
                $check = mall_src_link_check($_INPUT['default_data'][$key]);

                if(!$check)
                {
                    $data_error = "服务详情图片，须为本站上传的图片，";
                    $data_error_tips = "data_error";
                }

                //src链接校验结束

                //转码处理
                //$tmp_content = html_entity_decode($_INPUT['default_data'][$key]);
                $tmp_content = str_replace(array('&lt;','&gt;','&quot;'),array('<','>','"'),$_INPUT['default_data'][$key]);
                //闭合标签处理
                $tmp_content = mall_closetags($tmp_content);
                //过滤处理
                $tmp_content = strip_tags($tmp_content,'<p><img><br><embed>');
                //对html字符串里进行属性过滤处理
                //$tmp_content                = preg_replace("/class=\"(.*)\"/isU","",$tmp_content);
                $tmp_content                = preg_replace("/style=\"(.*)\"/isU","",$tmp_content);
                $tmp_content                = preg_replace("/style=\'(.*)\'/isU","",$tmp_content);
                //$tmp_content                = preg_replace("/width=\"(\d+)\"/is","",$tmp_content);
                //$tmp_content                = preg_replace("/height=\"(\d+)\"/is","",$tmp_content);
                //$tmp_content                = preg_replace("/width=(\d+)/is","",$tmp_content);
                //$tmp_content                = preg_replace("/height=(\d+)/is","",$tmp_content);
                $tmp_content                = preg_replace("/align=center/is","",$tmp_content);

                $_INPUT['default_data'][$key] = $tmp_content;

            }
            else if($key=="prices")
            {
                //对价格进行不取整处理2015-9-15
                $_INPUT['default_data'][$key] = trim($_INPUT['default_data'][$key]);
            }
        }
    }
}


/******2015-9-25去除美食菜单拼接处理******/
//$many_data_system_data_array = array("077e29b11be80ab57e1a2ecabb7da330");//增量的数组,作链接字符串处理，例如美食达人的菜单
/******2015-9-25去除美食菜单拼接处理******/

$can_empty_system_data_array = array(
    "7cbbc409ec990f19c78c75bd1e06f215",
    "2723d092b63885e0d7c260cc007e8b9d",
    "5f93f983524def3dca464469d2cf9f3e",
    "ed3d2c21991e3bef5e069713af9fa6ca",
    "fb7b9ffa5462084c5f4e7e85a093e6d7",
    "16a5cdae362b8d27a1d8f8c7b78b4330",
    "5737c6ec2e0716f3d8a7a5c4e0de0d9a",
    "c058f544c737782deacefa532d9add4c",
    "072b030ba126b2f4b2374f342be9ed44",
    "e7b24b112a44fdd9ee93bdf998c6ca0e",
    "52720e003547c70561bf5e03b95aa99f",
    "2a38a4a9316c49e5a833517c45d31070",
    "7647966b7343c29048673252e490f736",
    "caf1a3dfb505ffed0d024130f58c5cfa",
    "fc490ca45c00b1249bbe3554a4fdf6fb",
    "8f121ce07d74717e0b1f21d122e04521"


);
if(!empty($_INPUT['system_data']))
{
    foreach($_INPUT['system_data'] as $key => $value)
    {
        if(empty($value))
        {
            //处理选填项
            if(in_array($key,$can_empty_system_data_array))
            {
                //其他服务，勾选其他选项，输入内容判断
                if($key=="fb7b9ffa5462084c5f4e7e85a093e6d7")
                {
                    if($_INPUT['system_data']['07cdfd23373b17c6b337251c22b7ea57']=="6c524f9d5d7027454a783c841250ba71")//表示选中其他
                    {
                        $data_error = "其他选项";
                        $data_error_tips = "data_error";
                        break;
                    }
                }

                continue;

            }
            else
            {
                $data_error = $pai_mall_goods_type_attribute_obj->get_name_by_md5_key($key);
                $data_error_tips = "data_error";
                break;
            }



        }
        else
        {
            /******2015-9-25去除美食菜单拼接处理******/
            /*if(is_array($value))
            {
                //美食达人的菜单拼接处理
                if(in_array($key,$many_data_system_data_array))
                {
                    $tmp_value = implode("|",$value);
                    $value = $tmp_value;
                }

                $_INPUT['system_data'][$key] = $value;
            }
            else
            {
                $_INPUT['system_data'][$key] = trim($value);
            }*/
            /******2015-9-25去除美食菜单拼接处理******/
            if(is_array($value))
            {
                $_INPUT['system_data'][$key] = $value;
            }
            else
            {
                $_INPUT['system_data'][$key] = trim($value);
            }

        }
    }
}

$prices_mark = 0;//标记价格符合条件 2015-7-14
if(!empty($_INPUT['prices_de']))
{
    foreach($_INPUT['prices_de'] as $key => $value)
    {
        $_INPUT['prices_de'][$key] = trim($value);
        if(!empty($value))
        {
            $prices_mark = 1;
        }

        //特殊处理约摄套餐价格-2015-10-13
        if($key==312)//尊享服务
        {
            if($value=="")
            {
                unset($_INPUT["system_data"]["3fe94a002317b5f9259f82690aeea4cd"]);
            }
        }
        //特殊处理约摄套餐价格-2015-10-13
    }
}
else
{
    $prices_mark = 1;
}

//规格价格至少有一项不能为空 2015-7-14
if($prices_mark<1)
{
    $data_error = "价格";
    $data_error_tips = "data_error";
}

//约美食套餐的拆分特殊处理2015-9-24
if($type_id==41)
{
    //使用|@|进行套餐名跟人数隔开
    $prices_diy_tmp = $_INPUT['prices_diy'];
    foreach($prices_diy_tmp as $key => $value)
    {
        $prices_diy_tmp[$key]["name"] = $value["name_v1"]."（".$value["name_v2"]."人）";//加约定规格
    }
    $_INPUT['prices_diy'] = $prices_diy_tmp;
}


//约美食套餐的拆分特殊处理2015-9-24




//操作的数据
unset($_INPUT['goods_id']);
$op_data = $_INPUT;
//配置store_id
$op_data['store_id'] = $seller_store_id;

//图片
if($type_id==41 || $type_id==40)
{
    //美食达人导航图片处理
    $guide_img = $_INPUT['yue_upload_group_2'];
    $guide_img_data = array();
    $patt = "/undefined/";
    foreach($guide_img as $val)
    {
        //判断是否图片有误
        preg_match($patt,$val,$matches);
        if(!empty($matches))
        {
            $data_error = "图片";
            $data_error_tips = "data_error";
            break;
        }
        //判断图片是否为空
        if($val=="")
        {
            $data_error = "图片";
            $data_error_tips = "data_error";
            break;
        }

        $guide_img_data[] = $val;
    }
    $guide_img_data_str = implode(",",$guide_img_data);
    if($type_id==40)//摄影拍摄套图
    {
        $op_data["system_data"]['c3e878e27f52e2a57ace4d9a76fd9acf'] = $guide_img_data_str;
        //特殊处理，摄影服务,套图内容存入图文编辑字段
        $img_content = "<p>";
        foreach($guide_img_data as $k => $v)
        {
            $v = yueyue_resize_act_img_url($v,'640');
            $img_content .= '<img src="'.$v.'" _src="'.$v.'">';
        }
        $img_content .="</p>";
        $op_data["default_data"]['content'] = $img_content;


    }
    else if($type_id==41)//美食达人导航图
    {
        $op_data["system_data"]['e56954b4f6347e897f954495eab16a88'] = $guide_img_data_str;
    }


}





$img = $_INPUT['yue_upload_group_1'];
$img_data = array();
$patt = "/undefined/";
foreach($img as $val)
{
    //判断是否图片有误
    preg_match($patt,$val,$matches);
    if(!empty($matches))
    {
        $data_error = "图片";
        $data_error_tips = "data_error";
        break;
    }
    //判断图片是否为空
    if($val=="")
    {
        $data_error = "图片";
        $data_error_tips = "data_error";
        break;
    }

    $img_data[] = array('img_url'=>$val);
}
$op_data['img'] = $img_data;



if(!empty($data_error_tips))
{
    if($data_error=="服务详情有图片在加载，" || $data_error=="服务详情图片，须为本站上传的图片，")
    {
        echo "<script>top.alert('".$data_error."请检查服务详情内容');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    }
    else
    {
        echo "<script>top.alert('".$data_error."数据有误，请正确填写相关资料');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    }
    exit();
}


//特殊处理开课时间段必须少于当前时间
if($op_data["system_data"]['072b030ba126b2f4b2374f342be9ed44'])
{
    $post_time = $op_data["system_data"]['072b030ba126b2f4b2374f342be9ed44'];
    $post_time = strtotime($post_time);
    $now_time = time()-86400;
    if($post_time<=$now_time)
    {
        echo "<script>top.alert('开课时间不能小于昨天');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
        exit();
    }
}

//特殊处理

//print_r($op_data);
//exit();

if($action=="add")
{
    //插入数据
    $res = $pai_mall_goods_obj->user_add_goods($op_data,$user_id);
    if($res['result'] < 1)
    {
        echo "<script>top.alert('".$res['message']."');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
        exit();
    }
    else
    {
        //echo "insert success";
        echo "<script>top.window.layer.closeAll();top.window.location.href='../goods_list.php?show=0'</script>";
        exit();
    }
}
else if($action=="edit")
{
    //更新数据
    /*******美食达人特殊处理2015-10-20********/
    if($type_id==41 && $update_detail==1)
    {

        //只更新规定好的字段
        $op_data = "";
        $op_data["system_data"]['f7664060cc52bc6f3d620bcedc94a4b6'] = trim($_INPUT["system_data"]['f7664060cc52bc6f3d620bcedc94a4b6']);
        $res = $pai_mall_goods_obj->user_update_goods_for_detail($goods_id,$op_data,$user_id);

    }
    else
    {
        $res = $pai_mall_goods_obj->user_update_goods($goods_id,$op_data,$user_id);
    }
    /*******美食达人特殊处理2015-10-20********/

    if($res['result'] < 1)
    {
        echo "<script>top.alert('".$res['message']."');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
        exit();
    }
    else
    {
        //echo "insert success";
        echo "<script>top.window.layer.closeAll();top.window.location.href='../goods_list.php?show=0'</script>";
        exit();
    }

}
else if($action=="money_edit")
{
    $res = $pai_mall_goods_obj->user_update_goods_prices($goods_id,$op_data,$user_id);
    if($res['result'] < 1)
    {
        echo "<script>top.alert('".$res['message']."');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
        exit();
    }
    else
    {
        //echo "insert success";
        //检查此商品原来状态确定跳转位置
        $goods_info = $pai_mall_goods_obj->user_get_goods_info($goods_id,$user_id);
        $status = $goods_info['data']['goods_data']['status'];
        $is_show = $goods_info['data']['goods_data']['is_show'];
        if($status==1)
        {
            if($is_show==1)
            {
                $show = 1;
            }
            else
            {
                $show = 2;
            }
        }
        else
        {
            $show = 0;
        }


        echo "<script>top.window.layer.closeAll();top.window.location.href='../goods_list.php?show=".$show."'</script>";
        exit();
    }
}
else if($action=="preview")
{

    $time_mark_value = date("Ymdhis",time());
    $op_data['cache_id'] = $user_id.$time_mark_value;
    $ret = $pai_mall_goods_obj->set_goods_data_for_temp($op_data);
    if($ret)
    {
        //生成二维码图片
        $text = TASK_PROJECT_ROOT."/preview_middle_jump.php?cache_id=".$op_data['cache_id']."&type=service";
        $img = pai_activity_code_class::get_qrcode_img($text);
        //页面呈现二维码
        echo "<script>top.document.getElementById('qr_code_url').value='".$text."';top.document.getElementById('qr_code_img').value='".$img."';top.__qr_code_preview_obj.set_qr_img('".$img."');top.__qr_code_preview_obj.change_hide();</script>";
        exit();

    }
    else
    {
        //处理原页面的逻辑
        echo "<script>top.alert('生成二维码失败');</script>";
        exit();
    }


}






?>