<?php
/**
 * 商品上下架异步处理
 *
 * 2015-6-18
 *
 * author  星星
 *
 */


include_once '../common.inc.php';

$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');

$ajax_status = 1;
if(empty($yue_login_id))
{
    $ajax_status = 0;
    $res = "login error";
}
else
{
    $user_id = $yue_login_id;
    $goods_id_str = trim($_INPUT['goods_id_str']);
    $action = trim($_INPUT['action']);//上架还是下架
    $action_array = array("show","show_all","hidden","hidden_all","del");//操作数组

    $goods_id_array = explode(",",$goods_id_str);


    if(!empty($goods_id_array) && in_array($action,$action_array))
    {
        $show_hidden_array = array("show","show_all","hidden","hidden_all");
        if(in_array($action,$show_hidden_array))
        {
            //更新情况
            if($action=="show" || $action=="show_all")
            {
                $update_data['is_show'] = 1;
            }
            else
            {
                $update_data['is_show'] = 2;
            }



            //操作
            $goods_id_array_count = count($goods_id_array);
            if($goods_id_array_count>1)
            {
                foreach($goods_id_array as $key => $value)
                {
                    $goods_id_new[] = (int)$value;
                }



                foreach($goods_id_new as $k => $v)
                {
                    $ret = $pai_mall_goods_obj->user_change_goods_show_status($v,$update_data['is_show'],$user_id);
                    if($ret['result']!=1)
                    {
                        //更新出现错误
                        $res = iconv('gbk//IGNORE','utf-8', $ret["message"]);
                        $ajax_status = 0;
                        break;
                    }
                }


            }
            else
            {
                $goods_id = (int)$goods_id_array[0];
                $ret = $pai_mall_goods_obj->user_change_goods_show_status($goods_id,$update_data['is_show'],$user_id);
                if($ret['result']!=1)
                {
                    //更新出现错误
                    $ajax_status = 0;
                }
                $res = iconv('gbk//IGNORE','utf-8', $ret["message"]);
            }
        }
        else if($action=="del")
        {
            //删除数据
            $goods_id_array_count = count($goods_id_array);
            if($goods_id_array_count>0)
            {
                foreach($goods_id_array as $key => $value)
                {
                    $res = $pai_mall_goods_obj->user_del_goods($value,$user_id);
                    if($res['result']!=1)
                    {
                        $res = iconv('gbk//IGNORE','utf-8', $ret["message"]);
                        $ajax_status = 0;
                    }
                }
            }
            else
            {
                $goods_id = (int)$goods_id_array[0];
                $res = $pai_mall_goods_obj->user_del_goods($goods_id,$user_id);
                if($res['result']!=1)
                {
                    $ajax_status = 0;
                }
                $res = iconv('gbk//IGNORE','utf-8', $ret["message"]);
            }


        }

    }
    else
    {
        $ajax_status = 0;
        $res = $res = iconv('gbk//IGNORE','utf-8', "参数错误");
    }
}




$res_arr = array(
    "ajax_status"=>$ajax_status,
    "res"=>$res

);

echo json_encode($res_arr);


?>