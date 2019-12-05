<?php


function get_view($tpl_full_path) 
{
    $tpl_full_path = (string)$tpl_full_path;

    return $GLOBALS['pai_app']->getView(G_PAI_APP_PATH.$tpl_full_path, true);
}

/**
 * 获得图片的缩略图size
 *
 * @author 引用于摄影
 *
 * @param $real_arr  array 实际图片大小 $real_arr['width'] $real_arr['height']
 * @param $limit_arr  array 缩略限制 $limit_arr['width'] $limit_arr['height']
 * @return array 缩略后的size
 */
function photo_img_scaling_size($real_arr, $limit_arr) 
{
    if ( ! isset($real_arr['width']) || ! isset($real_arr['height']) )
    {
        return false;
    }

    $limit_arr['width'] = isset($limit_arr['width'])?$limit_arr['width']:10000;
    $limit_arr['height'] = isset($limit_arr['height'])?$limit_arr['height']:10000;

    // 如果都小于限制的大小，直接输出
    if ( $real_arr['width'] < $limit_arr['width'] && $real_arr['height'] < $limit_arr['height'] )
    {
        return $real_arr;
    }

    $realScal = $real_arr['width']/$real_arr['height'];

    // 对比比例，看按照长度还是宽度进行缩图
    if ( $realScal > $limit_arr['width']/$limit_arr['height'] )
    {
        // 用宽度做计算
        $ret_arr['width'] = $limit_arr['width'];
        $tmp_height = ( float )($ret_arr['width']/$realScal);
        $c_scale = ( int ) ($limit_arr['height'] / 2) * 0.12;
        $c_scale = ((($limit_arr['height'] / 2 + $c_scale)) >= ($tmp_height / 2)) ? 0 : $c_scale;
        $ret_arr['height'] = (int)($tmp_height - $c_scale);
    }
    else
    {
        $ret_arr['height'] = $limit_arr['height'];
        $ret_arr['width'] = (int)($ret_arr['height']*$realScal);
    }

    return $ret_arr;
}



?>