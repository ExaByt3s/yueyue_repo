<?php
/**
 * 接口 公用处理方法
 *
 * @author willike
 * @since 2015/10/14
 */

/**
 * 品类分类列表
 *
 * @param int $type_id
 * @return mixed
 */
function interface_type_list($type_id = null) {
    // 品类图标
    $type_list_ = array(
        31 => array(
            'type_id' => 31,
            'title' => '模特服务',
            'image' => 'http://image19-d.yueus.com/yueyue/20151126/20151126172542_929730_10002_63814.png?75x75_130', // 模特约拍
        ),
        12 => array(
            'type_id' => 12,
            'title' => '场地租赁',
            'image' => 'http://image19-d.yueus.com/yueyue/20151126/20151126172625_364612_10002_63816.png?75x75_130', // 影棚租赁
        ),
        5 => array(
            'type_id' => 5,
            'title' => '约培训',
            'image' => 'http://image19-d.yueus.com/yueyue/20151126/20151126172659_742803_10002_63818.png?75x75_130', // 约培训
        ),
        40 => array(
            'type_id' => 40,
            'title' => '约摄影师',
            'image' => 'http://image19-d.yueus.com/yueyue/20151126/20151126172643_116157_10002_63817.png?75x75_130', // 摄影服务
        ),
        3 => array(
            'type_id' => 3,
            'title' => '化妆服务',
            'image' => 'http://image19-d.yueus.com/yueyue/20151126/20151126172600_335864_10002_63815.png?75x75_130', // 化妆服务
        ),
        41 => array(
            'type_id' => 41,
            'title' => '约美食',
            'image' => 'http://image19-d.yueus.com/yueyue/20151126/20151126172726_506437_10002_63819.png?75x75_130', // 约美食
        ),
        42 => array(
            'type_id' => 42,
            'title' => '约活动',
            'image' => 'http://image19-d.yueus.com/yueyue/20151126/20151126172513_400676_10002_63813.png?75x75_130', // 约活动
        ),
        43 => array(
            'type_id' => 43,
            'title' => '约有趣',
            'image' => 'http://image19-d.yueus.com/yueyue/20151126/20151126172744_527972_10002_63820.png?75x75_130', // 约有趣
        ),
    );
    if (is_null($type_id)) {
        return $type_list_;
    }
    return isset($type_list_[$type_id]) ? $type_list_[$type_id] : array();
}

/**
 * 服务编辑 图片上传配置
 *
 * @param string $cover 封面图
 * @param string $contents 详情
 * @param int $type_id 分类ID
 * @param int $pic_num 上传图片数
 * @param array $addon 附加规则
 * @return array
 */
function interface_services_upload_config($cover, $contents = null, $type_id = 0, $pic_num = 20, $addon = array()) {
    $notice_arr = array(
        43 => '建议上传最能代表所提供服务的图片作为商品封面',
        5 => '建议上传突出服务标题的图片作为商品封面',
        31 => '建议上传符合所选风格的作品作为商品封面',
        12 => '建议上传场地环境图作为商品封面',
        3 => '建议上传此风格的清晰妆容正面照作为商品封面',
    );
    $config = array(
        'cover' => empty($cover) ? '' : $cover, // 封面图
        // 封面图(上传)
        'post_cover' => 'http://sendmedia-w.yueus.com:8079/upload.cgi',
        'post_cover_wifi' => 'http://sendmedia-w.yueus.com:8079/upload.cgi',
        'cover_size' => 640,
        'cover_tips' => '请上传封面图',
        // 图文详情(上传)
        'post_pic' => 'http://sendmedia-w.yueus.com:8079/upload.cgi',
        'post_pic_wifi' => 'http://sendmedia-w.yueus.com:8079/upload.cgi',
        'pic_size' => 640,
        'pic_num' => $pic_num,
    );
    if (isset($notice_arr[$type_id])) {
        $config['cover_notice'] = $notice_arr[$type_id];
    }
    if (!is_null($contents)) {
        $config['contents'] = $contents;
        $config['contents_tips'] = '请编辑图文详情';
    }
    if (!empty($addon)) {
        $config = array_merge($config, $addon);
    }
    return $config;
}

/**
 * 获取 商家 属性
 *
 * @param array $att_data
 * @return array
 */
function interface_get_seller_property($att_data) {
    if (empty($att_data)) {
        return array();
    }
    $type_list_ = interface_type_list();  // 品类列表
    // 品类图标 ( 3 化妆,31 模特,40 摄影师,12 影棚,5 培训,41 美食,43 其他服务 )
    $style_keymap_ = array(
        'm' => 31,
        'yp' => 12,
        't' => 5,
        'p' => 40,
        'hz' => 3,
        'ms' => 41,
        'ot' => 43,
        'ev' => 42,
    );
    $attr_arr = array(
        'm_level' => array(
            1 => 'V1手机认证',
            2 => 'V2实名认证',
            3 => 'V3达人认证',
        ),
        'm_height' => 'CM',
        'm_weight' => 'KG',
    );
    $attr_list = $bwh_arr = array();
    foreach ($att_data as $val) {
        $value = $val['value'];
        if ($value == '' || $value == NULL) {
            // 去除 一些空值
            continue;
        }
        $name = $val['name'];
        $vkey = $val['key'];
        if (in_array($vkey, array('p_order_income', 'p_team', 'm_level', 'yp_place', 'yp_background', 't_way',
            'yp_can_photo', 'yp_lighter', 'yp_other_equitment', 'ms_forwarding', 'hz_order_way', 'hz_place'))) {
            // 不显示 订单收入,团队构成
            continue;
        }
        if (in_array($vkey, array('m_height', 'm_weight', 'm_cups', 'm_cup', 'm_bwh'))) {
            $unit = isset($attr_arr[$vkey]) ? $attr_arr[$vkey] : '';
            $bwh_arr[$vkey] = $value . $unit;
            continue;
        }
        $type[] = array();
        list($key, $one) = explode('_', $vkey);
        $attr_list[$key][] = array(
            'type' => 'label',
            'id' => $vkey,
            'title' => $name,
            'value' => isset($attr_arr[$vkey]) ? $attr_arr[$vkey][$value] : $value,
        );
    }
    $property = array();
    foreach ($attr_list as $key => $value) {
        $type_id = $style_keymap_[$key];  // 匹配对应ID
        $style_info = isset($type_list_[$type_id]) ? $type_list_[$type_id] : array();
        if ($key === 'm') {
            $bwh_arr['m_cups'] = $bwh_arr['m_cups'] . $bwh_arr['m_cup'];
            unset($bwh_arr['m_cup']);
            $style_info['bwh'] = array(
                'type' => 'round',
                'title' => '身材',
                'value' => $bwh_arr,
            );
        }
        $style_info['description'] = $value;
        $property[] = $style_info;
    }
    return $property;
}

/**
 * 截取 内容 部分
 *
 * @param string $contents
 * @param int $size 截取大小 ( 2个字符 )
 * @return string
 */
function interface_content_strip($contents, $size = 300) {
    $contents = trim($contents);
    $size = intval($size);
    if (empty($contents) || $size < 1) {
        return $contents;
    }
    $contents = html_entity_decode($contents, ENT_QUOTES, 'GB2312');  // 转编译
    $contents = mb_strimwidth(strip_tags($contents), 0, $size, '...');  // 截取前150
    $contents = trim(interface_html_decode($contents));   // html 残留标签 转编译
    return $contents;
}

/**
 * 内容 转UBB
 *
 * @param string $contents
 * @return string
 */
function interface_content_to_ubb($contents) {
    if (empty($contents)) {
        return '';
    }
    $contents = stripos($contents, '&lt;') < 10 ? html_entity_decode($contents, ENT_COMPAT | ENT_HTML401, 'GB2312') : $contents;
    $contents = '[color=#333333]' . interface_ubb_encode($contents) . '[/color]';
    return $contents;
}

/**
 * 获取 商品属性
 *
 * @param array $goods_system_data
 * @param int $goods_location_id ( for 美食)
 * @return array
 */
function interface_get_goods_property($goods_system_data, $goods_location_id = 101029001) {
    $hide_item_ = array(
        'c3e878e27f52e2a57ace4d9a76fd9acf', // 拍摄套系样图 不出现 2015-9-16
//        '00ec53c4682d36f5c4359f4ae7bd7ba1', // 活动地区 不出现 2015-11-19
    );
    $service = $guide = $guide_img = $menu = $package = array();  // 服务内容
    $recommend = ''; // 推荐原因 ( for 美食 )
    $tmp_property = array();  // 临时存放,用于属性处理
    $property_unit_conf_ = pai_mall_load_config('property_unit');  // 获取属性对应单位
    foreach ($goods_system_data as $property_info) {
        $property_value = trim($property_info['value']);
        if ($property_value == '' || $property_value == null || $property_value == 'null') {
            continue;
        }
        $property_key = $property_info['key']; // 属性key
        if (in_array($property_key, $hide_item_)) {
            continue;
        }
        $property_id = $property_info['id']; // 属性ID
        $property_name = $property_info['name'];  // 标题
        // 单位
        $property_unit = empty($property_info['mess']) ? (isset($property_unit_conf_[$property_id]) ? $property_unit_conf_[$property_id] : '') : $property_info['mess'];
        if ($property_id == 249) {
            // 约美食 ( 菜式 )
            $menu = array(
                'id' => $property_id,
                'title' => $property_name,
                'value' => explode('|', $property_value),
            );
            continue;
        }
        if (in_array($property_id, array(266, 276, 277))) {
            // 约美食 ( 达人推荐 )
            $recommend .= $property_value;
            continue;
        }
        if (in_array($property_id, array(258, 259, 260, 261))) {
            // 约美食 ( 导航 )
            $addr = '';
            if (in_array($property_id, array(259, 261))) {
                // 259餐厅地址, 261导航方式 添加省市
                $addr = get_poco_location_name_by_location_id($goods_location_id) . ' ';
            }
            $guide[] = array(
                'id' => $property_id,
                'title' => $property_name . '：', // 添加冒号
                'value' => $addr . $property_value,
            );
            continue;
        }
        if ($property_id == 265) {
            // 约美食 ( 导航图片 )
            $guide_images_ = explode(',', $property_value);
            foreach ($guide_images_ as $img) {
                $guide_imgs[] = array(
                    'thumb' => yueyue_resize_act_img_url($img, '640'), // 缩略图
                    'original' => yueyue_resize_act_img_url($img), // 原图
                );
            }
            continue;
        }
        if ($property_id == 320 && !empty($goods_location_id)) {  // 影棚详细地址
            // 添加 地区
            $property_value = get_poco_location_name_by_location_id($goods_location_id) . ' ' . $property_value;
        }
        if (in_array($property_id, array(360, 361))) {  // 培训时间处理
            if (!isset($tmp_property[360])) {
                if (!isset($tmp_property[361])) {
                    $tmp_property[$property_id] = $property_value;
                    continue;
                }
                $train_value = $property_value . '-' . $tmp_property[361];
            } else {
                $train_value = $tmp_property[360] . '-' . $property_value;
            }
            $service[] = array(
                'id' => '360,361',
                'title' => '培训时间：',
                'value' => $train_value,
            );
            continue;
        }
        if (in_array($property_id, array(272, 381))) {  // 活动地址,地区处理
            if (!isset($tmp_property[272])) {
                if (!isset($tmp_property[381])) {
                    $tmp_property[$property_id] = $property_value;
                    continue;
                }
                $tmp_property[381] = get_poco_location_name_by_location_id($tmp_property[381]);
                $event_addr_value = $tmp_property[381] . ' ' . $property_value;
            } else {
                $property_value = get_poco_location_name_by_location_id($property_value);  // 活动地区 id => city
                $event_addr_value = $property_value . ' ' . $tmp_property[272];
            }
            // 活动(活动地址) 放第一个
            $property_ = array(
                'id' => '272,381',
                'title' => '活动地址：',
                'value' => $event_addr_value,
            );
            $service = array_merge(array($property_), $service);
            continue;
        }
        if (in_array($property_id, array(382, 452))) {  // 处理培训标签
            if ($property_id == 382) {
                $tmp_property[$property_id] = $property_value;
            } elseif ($property_id == 452) {
                if (strpos($tmp_property[382], '其他') === false) {
                    // 培训类型 没有选择其他, 则其他标签不出现
                    continue;
                }
            }
        }
        if (in_array($property_id, array(314, 315, 316, 444, 445, 446))) {
            if (empty($property_value)) {
                continue;
            }
            if (in_array($property_id, array(314, 315, 316))) {
                // 套餐名称
                $service[] = array(
                    'id' => $property_id,
                    'title' => '--' . $property_name . '--',
                    'value' => ''
                );
            }
            // 摄影套餐/约有趣属性 ( 序列化 ) 2015-9-9
            $property_package_ = unserialize($property_value);
            foreach ($property_package_ as $package_) {
                $package_value = trim($package_['value']);
                if ($package_value == '' || $package_value == null || $package_value == 'null') {
                    continue;
                }
                $package_key = $package_['key'];
                $package_unit = empty($package_['mess']) ? '' : $package_['mess'];
                $package_value = str_replace(array('<br rel=auto>', '&lt;br rel=auto&gt;'), "\r\n", $package_value);
                $service[] = array(
                    'id' => $package_key,
                    'title' => $package_['name'] . '：',
                    'value' => $package_value . $package_unit,
                );
                if ($package_key == 'name') {
                    $package[$property_id] = $package_value;
                }
            }
            continue;
        }
        if (!empty($property_info['child_data'])) {
            // 支持 新版风格 2015-9-8
            $property_value_arr_ = explode(',', $property_value);
            $property_value = $sub_property_value = '';
            foreach ($property_info['child_data'] as $child) {
                if (in_array($child['key'], $property_value_arr_)) {
                    $property_value .= $child['name'] . ',';
                }
                if (!empty($child['child_data'])) {
                    $child_property_value_arr_ = explode(',', $child['value']);
                    foreach ($child['child_data'] as $sub_child) {
                        if (in_array($sub_child['key'], $child_property_value_arr_) || in_array($sub_child['key'], $property_value_arr_)) {
                            $sub_property_value .= $sub_child['name'] . ',';
                        }
                    }
                }
            }
            if (in_array($property_id, array(450))) {  // 培训标签
                $property_value = trim($sub_property_value, ',');
            } else {
                $property_value = trim($property_value . $sub_property_value, ',');
            }
        } else {
            $property_value = str_replace(array('<br rel=auto>', '&lt;br rel=auto&gt;'), "\r\n", $property_value);
        }
        if (empty($property_value)) {
            continue;
        }
        if ($property_id == 88 && !is_numeric($property_value)) {
            // fix 课程周期 单位问题  2015-10-13
            $property_unit = '';
        }
        $property_value = str_replace(array('<br>', '<br/>', '<br />'), "\n", $property_value); // 换行
        $property_ = array(
            'id' => $property_id,
            'title' => $property_name . '：',
            'value' => $property_value . $property_unit,
        );
        if (in_array($property_id, array(267, 272))) {
            // 美食(预约要求)/ 活动(活动地址) 放在第一位
            $service = array_merge(array($property_), $service);
            continue;
        }
        $service[] = $property_;
    }
    return array(
        'service' => $service,
        'guide' => $guide,
        'guide_img' => $guide_imgs,
        'menu' => $menu,
        'recommend' => $recommend,
        'package' => $package,
    );
}

/**
 * 获取 场次列表属性
 *
 * @param array $goods_prices_list_data
 * @param int $activity_id
 * @param boolean $is_seller 是否是商家
 * @return array
 */
function interface_get_goods_showing($goods_prices_list_data, $activity_id = 0, $is_seller = false) {
    if (empty($goods_prices_list_data) || !is_array($goods_prices_list_data)) {
        return array();
    }
    $showing_exhibit = $all_exhibit = array();
    $all_is_finish = 1;
    $tmp_i = 0;
    $min_attend_prices = $max_attend_prices = 0;  // 所有场次 最低最高价
    $all_stock_num_total = $all_stock_num = $all_attend_num = 0;  // 所有场次 库存信息
    $min_attend_unit = $max_attend_unit = '';  // 活动所有场次 最低/高价的规格单位
    $mall_order_obj = POCO::singleton('pai_mall_order_class');  // for seller
    foreach ($goods_prices_list_data as $value) {
        $show_stock_num_total = intval($value['stock_num_total']);  // 总库存
        $all_stock_num_total += $show_stock_num_total;
        $show_stock_num = intval($value['stock_num']); // 当前库存
        $all_stock_num += $show_stock_num;
        $show_attend = $show_stock_num_total - $show_stock_num;  // 参与人数
        $show_name = $value['name'];
        $start_time = $value['time_s'];  // 开始时间
        $show_start = date('Y-m-d H:i', $start_time);
        $end_time = intval($value['time_e']);  // 结束时间
        $show_end = date('Y-m-d H:i', $end_time);
        $show_status = intval($value['status']);  //  1进行中 2结束
        $show_prices_list_data = $value['prices_list_data'];
        $show_prices = $max_prices = 0;
        $unit = $max_unit = '';
        foreach ($show_prices_list_data as $show_prices_list) {
            $tmp_price_ = $show_prices_list['prices'];
            if ($tmp_price_ <= 0) {
                continue;
            }
            $tmp_unit_ = $show_prices_list['name']; // 规格
            if ($show_prices <= 0 || bccomp($tmp_price_, $show_prices, 2) < 0) {
                $show_prices = $tmp_price_;
                $unit = $tmp_unit_;
            }
            if ($max_prices <= 0 || bccomp($tmp_price_, $max_prices, 2) > 0) {
                $max_prices = $tmp_price_;
                $max_unit = $tmp_unit_;
            }
            if ($min_attend_prices <= 0 || bccomp($tmp_price_, $min_attend_prices, 2) < 0) {
                $min_attend_prices = $tmp_price_;  // 所有场次最低价
                $min_attend_unit = $tmp_unit_;
            }
            if ($max_attend_prices <= 0 || bccomp($tmp_price_, $max_attend_prices, 2) > 0) {
                $max_attend_prices = $tmp_price_;  // 所有场次最高价
                $max_attend_unit = $tmp_unit_;
            }
        }
        if (bccomp($show_prices, $max_prices, 2) != 0) {
            $seller_prices = '￥' . $show_prices . '/' . $unit . '-￥' . $max_prices . '/' . $max_unit;
        } else {
            $seller_prices = '￥' . $show_prices . '/' . $unit;
        }
        $unit = empty($unit) ? '' : '/' . $unit;  // 规格单位
        $is_finish = interface_activity_is_finish($end_time, $show_status);  // 活动是否结束判断
        $stage_id = $value['type_id'];  // 场次ID
        if ($is_seller == true && !empty($activity_id)) {
            $trade_total = $mall_order_obj->get_order_list_of_paid_by_stage($activity_id, $stage_id, true);
            $show_attend = intval($trade_total);  // 付款人数
        }
        $all_attend_num += $show_attend;  // 总(参与/付款)人数
        $exhibit = array(
            'stage_id' => $stage_id,  // 场次ID
            'status' => $show_status,
            'title' => $show_name . ' ' . $show_start . '至' . $show_end,
            'name' => $show_name,
            'period' => $show_start . '至' . $show_end,
            'prices' => '￥' . sprintf('%.2f', $show_prices),
            'unit' => (count($show_prices_list_data) > 1) ? $unit . ' 起' : $unit,
            'seller_prices' => $seller_prices,
            'attend_str' => $is_seller == true ? '已付款人数 ' : '已报名人数 ',
            'attend_num' => $show_attend < 0 ? 0 : $show_attend,
            'total_num' => $show_stock_num_total,
            'stock_num' => $show_stock_num,
            'is_finish' => $is_finish ? 1 : 0,
        );
        $key = $start_time . ($tmp_i % 10);  // 排序key
        $tmp_i++;
        $all_exhibit[$key] = $exhibit;
        if ($is_finish == true) {
            // 已结束的 场次 不显示
            continue;
        }
        if ($show_stock_num > 0) { // 有库存(非报满)
            $all_is_finish = 0;
        }
        // 处理标题
//        $exhibit['title'] = mb_strimwidth(strip_tags($show_name), 0, 9, '...') . ' ' . $show_start . '至' . $show_end;
        $showing_exhibit[$key] = $exhibit;
    }
    // 场次排序
    if (!empty($all_exhibit)) {
        ksort($all_exhibit);
        $all_exhibit = array_values($all_exhibit);
    }
    if (!empty($showing_exhibit)) {
        ksort($showing_exhibit);
        $showing_exhibit = array_values($showing_exhibit);
    }
    if (bccomp($min_attend_prices, $max_attend_prices, 2) != 0) {
        $seller_prices = '￥' . $min_attend_prices . '/' . $min_attend_unit . '-￥' . $max_attend_prices . '/' . $max_attend_unit;
    } else {
        $seller_prices = '￥' . $min_attend_prices . '/' . $min_attend_unit;
    }
    $attend = array(
        'seller_prices' => $seller_prices,
        'prices' => '￥' . sprintf('%.2f', $min_attend_prices),
        'unit' => '/' . $min_attend_unit . ' 起',
        'attend_str' => $is_seller == true ? '已付款人数 ' : '已报名人数 ',
        'attend_num' => $all_attend_num,
        'total_num' => $all_stock_num_total,
        'stock_num' => $all_stock_num,
        'is_finish' => $all_is_finish,
    );
    return array(
        'attend' => $attend,
        'all_exhibit' => $all_exhibit,
        'showing_exhibit' => $showing_exhibit,
        'is_finish' => $all_is_finish,
    );
}

/**
 * 获取 搜索的 筛选条件
 *
 * @param int $type_id 分类ID
 * @return array
 */
function interface_get_search_filter($type_id) {
    if (empty($type_id)) {
        return array();
    }
    include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
    $attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
//    if ($type_id == 5) {
//        $result = $attribute_obj->property_for_search_get_data($type_id, true);
//    } else {
//        $result = $attribute_obj->property_for_search_get_data($type_id);
//    }
    $result = $attribute_obj->property_for_search_get_data($type_id);
    foreach ($result as $attr) {
        $key = $attr['name'];
        $title = $attr['text'];  // 显示名称
        $choice_type = strval($attr['select_type']);  // 1单选,2多选
        $options = array();
        $sub_attr = $attr['data'];
        if (empty($sub_attr)) {
            $filter[] = array('key' => $key, 'title' => $title, 'options' => $options,);
            continue;
        }
        foreach ($sub_attr as $val) {
            $sub_title = $val['val'];
            $sub_value = $val['key'];
            if ($type_id == 42) {
                if ($key == 'front_time' && $sub_value == 'self') {
                    // 活动[自定义], 不需要
                    continue;
                }
            }
            $sub_options = array();
            $sub_val = $val['child_data'];
            if (isset($sub_val['data']) && !empty($sub_val['data'])) {
                foreach ($sub_val['data'] as $v) {
                    $sub_options[] = array(
                        'key' => $sub_val['name'], 'title' => $v['val'], 'value' => $v['key'],);
                }
            }
            $options[] = array(
                'key' => $key,
                'title' => $sub_title,
                'choice_type' => $choice_type,
                'value' => $sub_value,
                'options' => $sub_options,
            );
        }
        if (in_array($key, array('detail[400]', 'detail[402]'))) {
            $tmp_filter[$key] = array('key' => $key, 'title' => $title, 'choice_type' => $choice_type, 'options' => $options,);
            continue;
        }
        $filter[] = array('key' => $key, 'title' => $title, 'choice_type' => $choice_type, 'options' => $options,);
    }
    // 处理 培训标签
    $tag_keymap = array(
        'detail[382]383' => 'detail[400]',  // 摄影培训
        'detail[382]385' => 'detail[402]',  // 美体培训
    );
    foreach ($filter as $key => $type) {
        foreach ($type['options'] as $k => $item) {
            $map_key = $item['key'] . $item['value'];
            if (isset($tag_keymap[$map_key])) {
                $tags_key = $tag_keymap[$map_key];
                $filter[$key]['options'][$k]['options'][] = $tmp_filter[$tags_key];
            }
        }
    }
    return $filter;
}

/**
 * 获取 筛选/排序 参数
 *
 * @param int $type_id 大分类ID
 * @param boolean $b_get_filter
 * @param array $select_data 选中项目
 * @return array
 */
function interface_get_search_screening($type_id, $b_get_filter = true, $select_data = array()) {
    // 排序
    $orderby = array(
        'title' => '排序',
        'goods' => array(
            array('key' => 'orderby', 'title' => '默认排序', 'value' => '-1',),
            array('key' => 'orderby', 'title' => '销量高到低', 'value' => '1',),
//            array('key' => 'orderby', 'title' => '销量低到高', 'value' => '2',),
            array('key' => 'orderby', 'title' => '价格高到低', 'value' => '3',),
            array('key' => 'orderby', 'title' => '价格低到高', 'value' => '4',),
            array('key' => 'orderby', 'title' => '人气高到低', 'value' => '5',),
//            array('key' => 'orderby', 'title' => '人气低到高', 'value' => '6',),
            array('key' => 'orderby', 'title' => '评分高到低', 'value' => '7',),
//            array('key' => 'orderby', 'title' => '评分低到高', 'value' => '8',),
        ),
        'seller' => array(
            array('key' => 'orderby', 'title' => '默认排序', 'value' => '-1',),
            array('key' => 'orderby', 'title' => '销量高到低', 'value' => '1',),
            array('key' => 'orderby', 'title' => '销量低到高', 'value' => '2',),
            array('key' => 'orderby', 'title' => '评分高到低', 'value' => '3',),
            array('key' => 'orderby', 'title' => '评分低到高', 'value' => '4',),
        ),
    );
    // 3 化妆,31 模特,40 摄影师,12 影棚,5 培训,41 美食,43 其他服务
    $type_name_ = array(3 => '化妆', 31 => '模特', 40 => '摄影师', 12 => '影棚', 5 => '培训', 41 => '美食', 43 => '约有趣', 42 => '活动');
    $filter = ($b_get_filter === false) ? array() : interface_get_search_filter($type_id);
    // 不要默认选中 2015-10-28
    $new_select_data = array(
//        'orderby' => -1,
//        'detail[46]' => '',
//        'm_height' => '',
//        'm_cup' => '',
//        'm_sex' => '',
//        'prices_list' => '',
    );
    foreach ($select_data as $key => $value) {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $new_select_data[$key . '[' . $k . ']'] = $v;
            }
            continue;
        }
        $new_select_data[$key] = $value;
    }
    $filter = interface_select_search_screening($filter, $new_select_data, 'value');
    $orderby = interface_select_search_screening($orderby, $new_select_data, 'value');
    return array(
        'filter' => array(
            'choice_type' => 2, // 第一层  1单选2多选
            'title' => $type_name_[$type_id] . '筛选',
            'options' => empty($filter) ? array() : $filter,
        ),
        'orderby' => empty($orderby) ? array() : $orderby,
    );
}

/*
 * 筛选条件 选中处理
 *
 * @param array $data
 * @param array $select_data 选中数据
 * @param string $check_field 对比的 值字段名称
 * @return array
 */
function interface_select_search_screening($data, $select_data, $check_field = 'value') {
    if (empty($data) || empty($select_data) || empty($check_field)
        || !is_array($data) || !is_array($select_data)
    ) {
        return $data;
    }
    $select_key_ = array_keys($select_data);
    if (!isset($data['key']) || !isset($data[$check_field])) {
        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                continue;
            }
            if (isset($value['key'])) {
                $value['is_select'] = 0;
                $data[$key] = $value;
            }
            if (!isset($value['key']) || !isset($value[$check_field])) {
                $value = interface_select_search_screening($value, $select_data, $check_field);
                $data[$key] = $value;
                continue;
            }
            $d_key = $value['key'];
            $value['is_select'] = 0;
            if (in_array($d_key, $select_key_)) {
                if ($value[$check_field] == $select_data[$d_key]) {
                    $value['is_select'] = 1;
                }
            }
            $data[$key] = $value;
        }
    } else if (isset($data['key']) && isset($data[$check_field])) {
        $data['is_select'] = 0;
        $k = $data['key'];
        if (in_array($k, $select_key_)) {
            if ($data[$check_field] == $select_data[$k]) {
                $data['is_select'] = 1;
            }
        }
    }
    return $data;
}


/**
 * 初始化 商品属性
 *
 * @param array $system_data 系统数据
 * @param array $addon_data 附加数据( for 摄影 )
 * @return array
 */
function interface_init_goods_property($system_data, $addon_data = array()) {
    if (empty($system_data)) {
        return array();
    }
    // 价格(摄影专用)
    $prices_keymap = array(
        'ad13a2a07ca4b7642959dc0c4c740ab6' => '311', // 标配服务套餐内容
        '758874998f5bd0c393da094e1967a72b' => '310', // 创意裸价套餐内容
        '3fe94a002317b5f9259f82690aeea4cd' => '312', // 尊享服务套餐内容
    );
    $property_unit_config = pai_mall_load_config('property_unit');  // 获取属性对应单位
    // 文字类型
    $words_style_ = array(
        // 培训
        '093f65e080a295f8076b1c5722a46aa2', '072b030ba126b2f4b2374f342be9ed44', '7f39f8317fbdb1988ef4c628eba02591',
        'fc490ca45c00b1249bbe3554a4fdf6fb', '3295c76acbf4caaed33c36b1b5fc2cb1', '2a38a4a9316c49e5a833517c45d31070',
        '5737c6ec2e0716f3d8a7a5c4e0de0d9a', 'c058f544c737782deacefa532d9add4c', 'e7b24b112a44fdd9ee93bdf998c6ca0e',
        '52720e003547c70561bf5e03b95aa99f', '735b90b4568125ed6c3f678819b6e058',
        // 化妆
        '7cbbc409ec990f19c78c75bd1e06f215',
        // 影棚
        '320722549d1751cf3f247855f937b982', '6f4922f45568161a8cdf4ad2299f6d23',
        // 其他
        'd709f38ef758b5066ef31b18039b8ce5', '0e01938fc48a2cfb5f2217fbfb00722d',
        // 摄影
        '98dce83da57b0395e163467c9dae521b', 'f4b9ec30ad9f68f89b29639786cb62ef',
        // 美食
        '077e29b11be80ab57e1a2ecabb7da330', '502e4a16930e414107ee22b6198c578f', 'cfa0860e83a4c3a763a7e62d825349f7',
        'b1a59b315fc9a3002ce38bbe070ec3f5', 'eda80a3d5b344bc40f3bc04f65b7a357', '8f121ce07d74717e0b1f21d122e04521',
        'f7664060cc52bc6f3d620bcedc94a4b6',
        // 其他
        'booking_time', 're_material', 'date_time', 'eed5af6add95a9a6f1252739b1ad8c24',
        'goods_size', 'goods_material', 'custom_time',
    );
    // 多选
    $double_select_ = array(
        '839ab46820b524afda05122893c2fe8e', '9fc3d7152ba9336a670e36d0ed79bc43',
        // 培训标签
        'f5f8590cd58a54e94377e6ae2eded4d9',
    );
    // 必须有二级目录
    $sub_options_ = array(
        // 摄影,培训
        '8613985ec49eb8f757ae6439e879bb2a', '9fc3d7152ba9336a670e36d0ed79bc43',
    );
    // 最大输入 限制
    $length_limit_ = array(
        '502e4a16930e414107ee22b6198c578f' => 30,  // 餐厅名称
        'eda80a3d5b344bc40f3bc04f65b7a357' => 200,  // 预约要求
        '8f121ce07d74717e0b1f21d122e04521' => 200,  // 温馨提示
        'b1a59b315fc9a3002ce38bbe070ec3f5' => 30, // 导航方式
    );
    // 提示语
    $hint_data = array(
        'c058f544c737782deacefa532d9add4c' => '请输入备注',
        '502e4a16930e414107ee22b6198c578f' => '输入餐厅名称',
        'a4f23670e1833f3fdb077ca70bbd5d66' => '输入联系电话',
        'b1a59b315fc9a3002ce38bbe070ec3f5' => '乘车路线、自驾路线、路线图',
        'eda80a3d5b344bc40f3bc04f65b7a357' => '输入预约要求',
        '8f121ce07d74717e0b1f21d122e04521' => '温馨提示',
        '2a38a4a9316c49e5a833517c45d31070' => '示例：8月18号起，每周六',
        'date_time' => '示例：每周五晚上8:00',
        'booking_time' => '示例：用户需提前一天预约',
    );
    // 错误提示提示语
    $err_tips_ = array(
        'a3f390d88e4c41f2747bfa2f1b5f87db' => '请选择妆面类型',
        'e2c420d928d4bf8ce0ff2ec19b371514' => '请输入耗时时间',
        '16a5cdae362b8d27a1d8f8c7b78b4330' => '请输入起拍件数',
        'd9d4f495e875a2e075a1a4a6e1b9770f' => '请选择风格类型',
        '07cdfd23373b17c6b337251c22b7ea57' => '请选择服务关键词',
        'fb7b9ffa5462084c5f4e7e85a093e6d7' => '请输入服务关键词',
        'd709f38ef758b5066ef31b18039b8ce5' => '请输入详细地址',
        '0e01938fc48a2cfb5f2217fbfb00722d' => '请输入注意事项',

        '98dce83da57b0395e163467c9dae521b' => '请编辑拍摄地址',
        'f4b9ec30ad9f68f89b29639786cb62ef' => '请编辑主题描述',
        '839ab46820b524afda05122893c2fe8e' => '请选择标签',
        '8613985ec49eb8f757ae6439e879bb2a' => '请选择拍摄类型',

        '70efdf2ec9b086079795c442636b55fb' => '请选择可拍摄类型',
        '1f0e3dad99908345f7439f8ffabdffc4' => '请输入使用面积',
        '98f13708210194c475687be6106a3b84' => '请选择背景',
        '6f4922f45568161a8cdf4ad2299f6d23' => '请输入灯光/器材配套',
        '320722549d1751cf3f247855f937b982' => '请输入影棚地址',

        '9fc3d7152ba9336a670e36d0ed79bc43' => '请选择培训类型',
        '47d1e990583c9c67424d369f3414728e' => '请选择课程类别',
        '093f65e080a295f8076b1c5722a46aa2' => '请编辑课程目标',
        '5737c6ec2e0716f3d8a7a5c4e0de0d9a' => '请编辑课程亮点',
        '735b90b4568125ed6c3f678819b6e058' => '请编辑课程大纲',
        'fc490ca45c00b1249bbe3554a4fdf6fb' => '请输入上课地址',
        '072b030ba126b2f4b2374f342be9ed44' => '请选择开课日期',
        '52720e003547c70561bf5e03b95aa99f' => '请选择培训时间',
        '2a38a4a9316c49e5a833517c45d31070' => '请选择课程周期',
        '7647966b7343c29048673252e490f736' => '请输入课程数量',
        'caf1a3dfb505ffed0d024130f58c5cfa' => '请输入课程时长',
        '44f683a84163b3523afe57c2e008bc8c' => '请选择授课方式',
        '5b8add2a5d98b1a652ea7fd72d942dac' => '请选择课程形式',

        '4f6ffe13a5d75b2d6a3923922b3922e5' => '请选择培训类型',
        'f5f8590cd58a54e94377e6ae2eded4d9' => '请选择培训标签',
        '9431c87f273e507e6040fcb07dcb4509' => '请输入您的培训类型',

        'ad13a2a07ca4b7642959dc0c4c740ab6.name' => '请输入套餐名称',
        '3fe94a002317b5f9259f82690aeea4cd.name' => '请输入套餐名称',
    );
    // 提示信息
    $notice_data_ = array(
        'ad13a2a07ca4b7642959dc0c4c740ab6' => '请填写套餐名称、价格及服务标准',  // 标准套餐
        '758874998f5bd0c393da094e1967a72b' => '此服务只含拍摄+修片价格，请按需填写',  // 创意裸价
        '3fe94a002317b5f9259f82690aeea4cd' => '此服务选填，请按需填写套餐内容',  // 尊享服务
    );
    // 自定义输入类型 ( 控件类型，1、跳页选择； 2、弹框选择；3、弹页输入；4、当前输入；5、标签选择 )
    $control_type_ = array(
        'd709f38ef758b5066ef31b18039b8ce5' => 3,
        'eed5af6add95a9a6f1252739b1ad8c24' => 3,
        're_material' => 3,  // (其他服务)材料
        'photo_content' => 3, // (约摄影师)相册
    );
    // 选填项
    $optional_fields_ = array(
        '9431c87f273e507e6040fcb07dcb4509',
    );
    // 标题
    $title_data_ = array(
        'custom_time' => '定制用时',
    );
    $init_list = $package = $first_options = $second_options = array();
    foreach ($system_data as $sys_value) {
        $sys_key = $sys_value['key'];  // 入库键值
        if (empty($sys_key)) {  // 没有key
            continue;
        }
        $input_type = 2;  // 数字
        if (in_array($sys_key, $words_style_)) {
            $input_type = 1;  // 文字
        }
        $control_type = '4'; //控件类型，1、跳页选择； 2、弹框选择；3、弹页输入；4、当前输入；5、标签选择
        $sys_id = $sys_value['id']; // 标识(获取单位)
        $hint = '编辑';  // 提示语
        $unit = isset($property_unit_config[$sys_id]) ? $property_unit_config[$sys_id] : '';  // 单位
        $show_value = empty($sys_value['value']) ? '' : $sys_value['value'];  // 显示的值
        $option_value = '';  // 初始化 入库值
        $choice_type = $choice_num = 0;  //选项 (最大)选取个数
        $format_data = $sys_value['format_data'];  // 格式化数据(摄影:套餐信息,其他:属性信息)
        if (!empty($format_data)) {
            $package = array();
            $p_prices = '';  // 套餐价格
            foreach ($format_data as $format_info) {
                $format_key = $format_info['key'];  // 入库键值
                // (套餐)价格在拍摄时长之上
                $p_show_value = empty($format_info['value']) ? '' : $format_info['value'];
                if ($format_key == '95') {
                    $prices_id = $prices_keymap[$sys_key]; // 价格
                    if (isset($addon_data[$prices_id])) {
                        $package[] = $addon_data[$prices_id];
                        $p_prices = $addon_data[$prices_id]['value'];
                    }
                }
                $item_split = '0'; // 界面是否分割 (0不分割)
                if (in_array($format_key, array('98'))) {
                    $item_split = '1';  // 分割
                }
                if ($sys_key == '758874998f5bd0c393da094e1967a72b') { // 创意裸价
                    if (in_array($format_key, array('name', '98', '99', '106', 'photo_content'))) {
                        continue; // 当是 创意裸价 时, 不显示 名称,相册等
                    }
                    if (in_array($format_key, array('97'))) {
                        $item_split = '1'; // 精选张数 分割界面
                    }
                }
                $p_hint = '编辑';
                $p_input_type = in_array($format_key, $words_style_) ? 1 : 2; // 1文字,2数字
                $p_control_type = 4; // 控件类型
                $p_max_length = 0; // 最大输入个数
                if (in_array($format_key, array('name', 'photo_content'))) {
                    $p_input_type = 1; // 文字
                }
                if ($format_key == 'name') {
                    $p_max_length = 8;  // 品类下的套餐名字长度：限定为8个字
                }
                $p_options = array();
                $p_option_value = ''; // 入库值
                $p_child_data = $format_info['child_data'];  // 选项
                if (!empty($p_child_data)) {
                    foreach ($p_child_data as $p_child) {
                        $p_options[] = array(
                            'value' => $p_child['name'],
                            'option_value' => $p_child['key'],
                        );
                    }
                    $p_hint = '请选择';
                    $p_control_type = 2;
                    $p_input_type = 0;
                    $p_option_value = $p_show_value;
                }
                $p_hint = isset($hint_data[$format_key]) ? $hint_data[$format_key] : $p_hint;  // 提示语
                $p_title = isset($title_data_[$format_key]) ? $title_data_[$format_key] : $format_info['name'];
                // 是否有自定义 控件类型
                $p_control_type = isset($control_type_[$format_key]) ? $control_type_[$format_key] : $p_control_type;
                $p_tips = isset($err_tips_[$sys_key . '.' . $format_key]) ? $err_tips_[$sys_key . '.' . $format_key] : '';
                $package[] = array(
                    'type' => strval($p_control_type), //控件类型
                    'id' => $format_key,
                    'title' => $p_title,  // 显示的标题
                    'value' => $p_show_value,  // 显示的值
                    'option_value' => $p_option_value,
                    'input_type' => $p_input_type,  // 文字1,数字2
                    'input_required' => in_array($format_key, $optional_fields_) ? 0 : 1,  // 0选填,1必填
                    'max_length' => $p_max_length,  // 最大输入个数
                    'item_split' => $item_split,  // 1 分割, 0 不分割
                    'options' => $p_options,
                    'hint' => $p_hint,
                    'unit' => $format_info['mess'],
                    'tips' => $p_tips,
                );
            }
            $init_list[$sys_key] = array(
                'type' => '3', //控件类型
                'id' => $sys_key,
                'title' => str_replace('内容', '', $sys_value['name']),
                'value' => empty($p_prices) ? '' : '￥' . $p_prices, // 价格
                'option_value' => $p_prices,
                'input_type' => 1,
                'hint' => '编辑',
                'unit' => '',
                'tips' => '请输入套餐规格',
                'notice' => isset($notice_data_[$sys_key]) ? $notice_data_[$sys_key] : '',
                'options' => $package,
            );
            continue;
        }
        $child_data = $sys_value['child_data']; // 属性选项
        if (!empty($child_data)) {
            $option_value = $show_value; // 选项处理 (入库值=显示值)
            $c_show_value = '';
            foreach ($child_data as $child) {
                $c_key = $child['key'];
                $c_name = $child['name'];
                if (in_array($c_key, explode(',', $option_value))) {
                    $c_show_value .= $c_name . '-';
                }
                $first_options[$sys_key][] = array(
                    'value' => $c_name,
                    'option_value' => $c_key,
                );
                $c_value = $child['value'];  // 二级选中值
                if (!empty($child['child_data'])) {
                    $items = array();
                    $sub_c_show_value = '';  // 一级选项
                    $sub_option_value = '';  // 二级入库值
                    foreach ($child['child_data'] as $sub_child) {
                        $sub_c_key = $sub_child['key'];
                        $sub_c_name = $sub_child['name'];
                        if (in_array($sub_c_key, explode(',', $option_value)) || in_array($sub_c_key, explode(',', $c_value))) {
                            $sub_c_show_value .= $sub_c_name . ',';
                            $sub_option_value .= $sub_c_key . ','; // 拼接二级key
//                            $option_value = $sub_c_key; // 赋予二级key
                        }
                        $items[] = array(
                            'value' => $sub_c_name,
                            'option_value' => $sub_c_key,
                        );
                    }
                    if (!empty($sub_option_value)) {
                        if (in_array($c_key, array('18d8042386b79e2c279fd162df0205c8', '816b112c6105b3ebd537828a39af4818', '69cb3ea317a32c4e6143e665fdb20b14'))) {
                            // 培训标签
                            $option_value = $c_key . '-' . trim($sub_option_value, ',');
                        } else {
                            // key 一级拼接二级
                            $option_value = $option_value . '-' . trim($sub_option_value, ',');
                        }
                    }
                    if (!empty($sub_c_show_value)) {
                        $c_show_value = $c_show_value . trim($sub_c_show_value, ',') . '-';
                    }
                    $second_options[] = array(
                        'id' => $c_key,
                        'items' => $items,
                    );
                } else if (in_array($sys_key, $sub_options_)) {
                    // 必须有二级目录 (拍摄类型,速成培训 ...)
                    $items = array(
                        'value' => $c_name,
                        'option_value' => $c_key,
                    );
                    $second_options[] = array(
                        'id' => $c_key,
                        'items' => array($items),
                    );
                }
            }
            $control_type = '2'; //控件类型，1、跳页选择； 2、弹框选择；3、弹页输入；4、当前输入；5、标签选择
            $input_type = 0;
            $choice_num = 1;
            $choice_type = 1;  // 单选
            $hint = '请选择';
            $show_value = empty($c_show_value) ? '' : trim($c_show_value, '-');
        }
        $hint = isset($hint_data[$sys_key]) ? $hint_data[$sys_key] : $hint;  // 提示语
        // 处理多选
        if (in_array($sys_key, $double_select_)) {
            if (in_array($sys_key, array('839ab46820b524afda05122893c2fe8e'))) {
                // 处理标签标签
                $control_type = 5;
            }
            $choice_type = 2;
            $choice_num = 2;  // 最多可选两个
        }
        // 自定义控件类型
        $control_type = isset($control_type_[$sys_key]) ? $control_type_[$sys_key] : $control_type;
        $title = $sys_value['name'];
        if (in_array($sys_key, array('c0e190d8267e36708f955d7ab048990d', 'ec8ce6abb3e952a85b8551ba726a1227'))) {
            // 菜系, 环境
            $title = str_replace('标签', '', $title);
        }
        $init_list[$sys_key] = array(
            'type' => $control_type, //控件类型
            'id' => $sys_key, // 入库键值
            'title' => $title, // 显示标题
            'value' => $show_value, // 显示值
            'option_value' => $option_value, // 入库值
            'input_type' => $input_type, // 输入类型 1 文字 2 数字 3 小数
            'input_required' => in_array($sys_key, $optional_fields_) ? 0 : 1,  // 0选填,1必填
            'max_length' => isset($length_limit_[$sys_key]) ? $length_limit_[$sys_key] : 0,  // 最大输入个数
            'choice_type' => $choice_type, // 1 单选, 2 多选
            'choice_num' => $choice_num, // 最大选中个数
            'hint' => $hint,
            'unit' => $unit,
            'tips' => isset($err_tips_[$sys_key]) ? $err_tips_[$sys_key] : '',
        );
    }
    return array(
        'init_list' => $init_list,
        'package' => $package,
        'first_options' => $first_options,
        'second_options' => $second_options
    );
}

/**
 * 初始化服务属性 (补丁)
 *
 * @param mixed $patch_data 初始化数据
 * @param string $pro_type 属性类型
 * @param int $type_id 大分类
 * @return array
 */
function interface_init_goods_property_patch($patch_data, $pro_type, $type_id = 0) {
    switch ($pro_type) {
        case 'titles':
            return array(
                'type' => '4', //控件类型
                'id' => 'titles', // 入库键值
                'title' => '服务名称', // 显示标题
                'value' => empty($patch_data) ? '' : $patch_data, // 显示值
                'option_value' => '', // 入库值
                'input_type' => '1', // 输入类型 1 文字 2 数字 3 小数
                'input_required' => 1, // 必填
                'max_length' => 30,  // 最大输入个数
                'hint' => '请输入服务名称',
                'unit' => '',
                'tips' => '请输入服务名称',
            );
            break;
        case 'location_id':
            $city = '';
            if (!empty($patch_data)) {
                $location_id_arr = explode(',', $patch_data);
                foreach ($location_id_arr as $location_id) {
                    if (empty($location_id)) {
                        continue;
                    }
                    $location = get_poco_location_name_by_location_id($location_id);  // 城市;
                    if ($type_id == 5) {
                        list($province, $l_city) = explode(' ', $location);
                        $location = str_replace('市', '', $l_city);
                    }
                    $city .= '/' . $location;
                }
                $city = trim($city, '/');
            }
            return array(
                'type' => '2', //控件类型
                'id' => 'location_id', // 入库键值
                'title' => $type_id == 5 ? '招生范围' : '所在地区',
                'value' => $city, // 显示值
                'option_value' => empty($patch_data) ? '' : $patch_data, // 入库值
                'input_type' => '2', // 输入类型 1 文字 2 数字 3 小数
                'input_required' => 1, // 必填
                'hint' => '',
                'unit' => '',
                'tips' => '请选择地区',
            );
            break;
        case 'standerd':
            $prices_list = array();
            foreach ($patch_data as $value) {
                $id = $value['id'];
                $prices_list[] = array(
                    'type' => '4',
                    'id' => $id,
                    'title' => empty($value['name_val']) ? $value['name'] : $value['name_val'],
                    'value' => empty($value['value']) ? '' : $value['value'],
                    'input_type' => '3',
                    'hint' => '价格',
                    'unit' => '元',
                    'tips' => '请输入价格',
                );
            }
            return $prices_list;
            break;
        default:
            return array();
    }
}

/**
 * 获取商品的 促销数据
 *
 * @param int $user_id
 * @param int $goods_id
 * @param array|float $prices_data
 *     array( array(
 *         'prices_type_id' => 77, //必填
 *         'goods_prices' => 88, //必填
 *     ), )
 * @param object $promotion_obj
 * @param boolean $get_promotion_list 是否返回促销列表
 * @return array
 */
function interface_get_goods_promotion($user_id, $goods_id, $prices_data, $promotion_obj = null, $get_promotion_list = false) {
    if (empty($goods_id) || empty($prices_data)) {
        return array();
    }
    if (!is_array($prices_data)) {
        $prices_list = array(
            array(
                'prices_type_id' => 0, //必填
                'goods_prices' => floatval($prices_data), //必填
            ),
        );
    } else {
        $prices_list = $prices_data;
    }
    $type_target = 'goods';  // 促销类型为商品
    $show_param_info = array(
        'channel_module' => 'mall_order', //必填
        'org_user_id' => 0,
        'location_id' => 0,
        'seller_user_id' => 0,
        'mall_type_id' => 0,
        'channel_gid' => $goods_id, //必填
    );
    if (empty($promotion_obj)) {
        $promotion_obj = POCO::singleton('pai_promotion_class');
    }
    $promotion_res = $promotion_obj->get_promotion_info_for_show_multiple($user_id, $type_target, $show_param_info, $prices_list, false);
    $abate = array();
    if (empty($promotion_res)) {
        return $abate;
    }
    $most_saving_promotion = $promotion_res['most_saving_promotion'];
    if (empty($most_saving_promotion)) {
        return $abate;
    }
    $period = $most_saving_promotion['start_time_str'] . '-' . $most_saving_promotion['end_time_str'];  // 有效期
    $abate = array(
        'abate' => $most_saving_promotion['cal_save_amount'],
        'notice' => $most_saving_promotion['type_name'],
        'period' => $period, // 有效期
        'description' => $period . "\n" . $most_saving_promotion['promotion_desc'],
        'marked' => 'http://image19-d.yueus.com/yueyue/20151012/20151012151631_726693_10002_34689.png?54x54_130',
    );
    if ($get_promotion_list === true) {
        $promotion_list = array();
        foreach ((array)$promotion_res['promotion_list'] as $result) {
            $prices_type_id = $result['prices_type_id'];
//            $period = $result['start_time_str'] . '-' . $result['end_time_str'];  // 有效期
            $promotion_list[$prices_type_id] = array(
                'abate' => $result['cal_save_amount'],
//                'notice' => $result['type_name'],
//                'period' => $period, // 有效期
//                'description' => $period . "\n" . $result['promotion_desc'],
                'marked' => 'http://image19-d.yueus.com/yueyue/20151012/20151012151631_726693_10002_34689.png?54x54_130',
            );
        }
        $abate['promotion_list'] = $promotion_list;
    }
    return $abate;
}

/**
 * 生成 消费者 订单按钮
 *
 * @param string $status 交易状态
 * @param string $is_buyer_comment 买家是否评论
 * @return array
 */
function interface_trade_buyer_action($status, $is_buyer_comment) {
    if ($is_buyer_comment == 1) {  // 买家已评论
        return array();
    }
    // 按钮文案
    $action_arr = array(
        0 => '付款.pay|取消订单.close',
//        1 => '拒绝.refuse|同意.accept',
        2 => '申请退款.close|出示二维码.sign',
        7 => '删除订单.delete',
        8 => '评价对方.appraise'
    );
    $btn = explode('|', $action_arr[$status]);
    $arr = array();
    foreach ($btn as $value) {
        if (empty($value)) {
            continue;
        }
        list($name, $request) = explode('.', $value);
        if (empty($name) || empty($request)) {
            continue;
        }
        $arr[] = array(
            'title' => $name,
            'request' => $request, // $user_id, $order_sn
        );
    }
    return $arr;
}

/**
 * 生成 商家 订单按钮
 *
 * @param string $status 交易状态
 * @param string $is_seller_comment 商家评论
 * @param string $version 版本号
 * @return array
 */
function interface_trade_seller_action($status, $is_seller_comment, $version = null) {
    if ($is_seller_comment == 1) {  // 商家已评价
        return array();
    }
    // 按钮文案
    $action_arr = array(
        0 => '关闭订单.close',
        1 => '拒绝订单.refuse|接受订单.accept',
        2 => '扫码签到.sign',
//        2 => '取消交易并退款.close|扫码签到.sign',
//        7 => '删除订单.delete',
        10 => '评价对方.appraise'
    );
    if (version_compare($version, '1.1') >= 0) {
        $action_arr[0] = '关闭订单.close|修改价格.pending';
    }
    $btn = explode('|', $action_arr[$status]);
    $arr = array();
    foreach ($btn as $value) {
        if (empty($value)) {
            continue;
        }
        list($name, $request) = explode('.', $value);
        if (empty($name) || empty($request)) {
            continue;
        }
        $arr[] = array(
            'title' => $name,
            'request' => $request, // $user_id, $order_sn
        );
    }
    return $arr;
}


/**
 * 获取榜单列表
 *
 * @param int $rank_id
 * @param string $limit_str
 * @param object $cms_obj
 * @return array
 */
function interface_get_seller_issue_record($rank_id, $limit_str = '0,6', $cms_obj = null) {
    if ($rank_id < 1) {
        return array();
    }
    if (empty($cms_obj)) {
        include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
        $cms_obj = new cms_system_class();
    }
    $record_result = $cms_obj->get_last_issue_record_list(false, $limit_str, 'place_number DESC', $rank_id);
    $list = array();
    foreach ($record_result as $val) {
        $url = filter_var($val['link_url'], FILTER_VALIDATE_URL) ? $val['link_url'] : '';
        if (!empty($url)) {
            if ($val['link_type'] == 'inner_web') {
                $url = "yueseller://goto?type=inner_web&url=" . urlencode($val['link_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $val['link_url']));
            } elseif ($val['link_type'] == 'inner_app') {
                $scheme = parse_url($url, PHP_URL_SCHEME);  // 获取协议头
                if ($scheme != 'yueseller') {
                    $url = "yueseller://goto?type=inner_app&pid=1250004&user_id=" . $val['user_id'];
                }
            }
        }
        $issue = array(
            'dmid' => 'ad-' . $val['rank_id'],
            'title' => $val['title'],
            'img' => $val['img_url'],
            'url' => $url,
        );
        if ($rank_id == 1011) {  // 头条,2张图
            $issue['img'] = $val['content'];
            $issue['img_big'] = empty($val['remark']) ? $val['content'] : $val['remark'];
        }
        $list[] = $issue;
    }
    return $list;
}


/**
 * 组装 订单详情 显示
 *
 * @param array $trade_info 原始订单详情
 * @param string $version 版本信息
 * @return array
 */
function interface_trade_property_data($trade_info, $version) {
    $type_id = $trade_info['type_id'];   // 服务(商品)类型
    $detail = $property = $goods = $showing = array();
    if ($type_id == 42) {  // 活动
        $showing = array(
            array('title' => '场次：', 'value' => $trade_info['stage_title']),
            array('title' => '规格：', 'value' => $trade_info['prices_spec']),
            array('title' => '人数：', 'value' => $trade_info['quantity'] . '人'),
            array('title' => '活动开始时间：', 'value' => date('Y-m-d H:i', $trade_info['service_start_time'])),
            array('title' => '活动结束时间：', 'value' => date('Y-m-d H:i', $trade_info['service_end_time'])),
            array('title' => '活动地点：', 'value' => $trade_info['service_address']),
        );
        $goods = array(
            'id' => $trade_info['activity_id'],
            'name' => $trade_info['activity_name'],
            'image' => $trade_info['activity_images'],
            'description' => '规格：' . $trade_info['prices_spec'],
            'price' => '￥' . $trade_info['prime_prices'],
        );
        return array('goods' => $goods, 'detail' => $detail, 'property' => $property, 'showing' => $showing);
    }
    $spec = explode(':', $trade_info['prices_spec']);
    $limit = array(
        'title' => count($spec) > 1 ? trim($spec[0]) . '：' : '时长：',
        'value' => count($spec) > 1 ? trim($spec[1]) : $trade_info['prices_spec']
    );
//    $account = array('title' => '到场拍摄人数：', 'value' => $trade_info['service_people'] . '人');
    $standard = array('title' => '规格：', 'value' => count($spec) > 1 ? trim($spec[1]) : $trade_info['prices_spec']);
    $time = array('title' => '服务时间：', 'value' => date('Y-m-d H:i', $trade_info['service_time']));
    $addr = array('title' => '地点：', 'value' => $trade_info['service_address']);
    $num = array('title' => '购买数量：', 'value' => $trade_info['quantity']);
    $goods = array(  // 服务信息
        'id' => $trade_info['goods_id'],
        'name' => $trade_info['goods_name'],
        'image' => $trade_info['goods_images'],
    );
    switch ($type_id) {
        case '31':  // 模特服务
            $detail = array($limit, $num);
            $property = array($time, $addr);
            break;
        case '5':  // 培训
            $detail = array();
            $property = array($num);
            break;
        case '12':  // 影棚
            $detail = array($standard,);
            $property = array($addr, $time, $num);
            break;
        case '3':  // 化妆
            $detail = array($time, $addr);
            $property = array($num);
            break;
        case '40':  // 摄影
            $detail = array($time, $addr);
            $property = array($num);
            break;
        case '41': // 美食
            $detail = array($time, $addr);
            $property = array($num);
            break;
        case '42': // 活动 (见上)
            break;
        case '43': // 约有趣
            $detail = array($time, $addr);
            $property = array($num);
            break;
        default:  // 未定义
            $detail = array();
            $property = array();
            break;
    }
    if (version_compare($version, '1.3', '>')) {
        $showing = array($standard, $num, $time, $addr);
        $detail = array();
        $property = array();
    }
    return array('goods' => $goods, 'detail' => $detail, 'property' => $property, 'showing' => $showing);
}

/**
 * 评价评分 计算
 *
 * @param float $overall_score 总评分
 * @param int $review_times 评价次数
 * @return float
 */
function interface_reckon_average_score($overall_score, $review_times) {
    if (empty($overall_score) || empty($review_times)) {
        return 5.0;
    }
    $average_score = ceil($overall_score / $review_times * 2) / 2;//商家综合评价
    return sprintf('%.1f', $average_score);
}

/**
 * 活动是否结束判断
 *
 * @param int $end_time 结束时间
 * @param int $status 活动状态
 * @param int $is_over 是否结束
 * @return boolean (-true 结束)
 */
function interface_activity_is_finish($end_time, $status = 0, $is_over = 0) {
    if ($is_over == 1) {
        return true;
    }
    if (time() > $end_time) {
        return true;
    }
    if ($status == 2) {
        return true;
    }
    return false;
}
