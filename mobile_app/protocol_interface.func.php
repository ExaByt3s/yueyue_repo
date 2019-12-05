<?php
/**
 * �ӿ� ���ô�����
 *
 * @author willike
 * @since 2015/10/14
 */

/**
 * Ʒ������б�
 *
 * @param int $type_id
 * @return mixed
 */
function interface_type_list($type_id = null) {
    // Ʒ��ͼ��
    $type_list_ = array(
        31 => array(
            'type_id' => 31,
            'title' => 'ģ�ط���',
            'image' => 'http://image19-d.yueus.com/yueyue/20151126/20151126172542_929730_10002_63814.png?75x75_130', // ģ��Լ��
        ),
        12 => array(
            'type_id' => 12,
            'title' => '��������',
            'image' => 'http://image19-d.yueus.com/yueyue/20151126/20151126172625_364612_10002_63816.png?75x75_130', // Ӱ������
        ),
        5 => array(
            'type_id' => 5,
            'title' => 'Լ��ѵ',
            'image' => 'http://image19-d.yueus.com/yueyue/20151126/20151126172659_742803_10002_63818.png?75x75_130', // Լ��ѵ
        ),
        40 => array(
            'type_id' => 40,
            'title' => 'Լ��Ӱʦ',
            'image' => 'http://image19-d.yueus.com/yueyue/20151126/20151126172643_116157_10002_63817.png?75x75_130', // ��Ӱ����
        ),
        3 => array(
            'type_id' => 3,
            'title' => '��ױ����',
            'image' => 'http://image19-d.yueus.com/yueyue/20151126/20151126172600_335864_10002_63815.png?75x75_130', // ��ױ����
        ),
        41 => array(
            'type_id' => 41,
            'title' => 'Լ��ʳ',
            'image' => 'http://image19-d.yueus.com/yueyue/20151126/20151126172726_506437_10002_63819.png?75x75_130', // Լ��ʳ
        ),
        42 => array(
            'type_id' => 42,
            'title' => 'Լ�',
            'image' => 'http://image19-d.yueus.com/yueyue/20151126/20151126172513_400676_10002_63813.png?75x75_130', // Լ�
        ),
        43 => array(
            'type_id' => 43,
            'title' => 'Լ��Ȥ',
            'image' => 'http://image19-d.yueus.com/yueyue/20151126/20151126172744_527972_10002_63820.png?75x75_130', // Լ��Ȥ
        ),
    );
    if (is_null($type_id)) {
        return $type_list_;
    }
    return isset($type_list_[$type_id]) ? $type_list_[$type_id] : array();
}

/**
 * ����༭ ͼƬ�ϴ�����
 *
 * @param string $cover ����ͼ
 * @param string $contents ����
 * @param int $type_id ����ID
 * @param int $pic_num �ϴ�ͼƬ��
 * @param array $addon ���ӹ���
 * @return array
 */
function interface_services_upload_config($cover, $contents = null, $type_id = 0, $pic_num = 20, $addon = array()) {
    $notice_arr = array(
        43 => '�����ϴ����ܴ������ṩ�����ͼƬ��Ϊ��Ʒ����',
        5 => '�����ϴ�ͻ����������ͼƬ��Ϊ��Ʒ����',
        31 => '�����ϴ�������ѡ������Ʒ��Ϊ��Ʒ����',
        12 => '�����ϴ����ػ���ͼ��Ϊ��Ʒ����',
        3 => '�����ϴ��˷�������ױ����������Ϊ��Ʒ����',
    );
    $config = array(
        'cover' => empty($cover) ? '' : $cover, // ����ͼ
        // ����ͼ(�ϴ�)
        'post_cover' => 'http://sendmedia-w.yueus.com:8079/upload.cgi',
        'post_cover_wifi' => 'http://sendmedia-w.yueus.com:8079/upload.cgi',
        'cover_size' => 640,
        'cover_tips' => '���ϴ�����ͼ',
        // ͼ������(�ϴ�)
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
        $config['contents_tips'] = '��༭ͼ������';
    }
    if (!empty($addon)) {
        $config = array_merge($config, $addon);
    }
    return $config;
}

/**
 * ��ȡ �̼� ����
 *
 * @param array $att_data
 * @return array
 */
function interface_get_seller_property($att_data) {
    if (empty($att_data)) {
        return array();
    }
    $type_list_ = interface_type_list();  // Ʒ���б�
    // Ʒ��ͼ�� ( 3 ��ױ,31 ģ��,40 ��Ӱʦ,12 Ӱ��,5 ��ѵ,41 ��ʳ,43 �������� )
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
            1 => 'V1�ֻ���֤',
            2 => 'V2ʵ����֤',
            3 => 'V3������֤',
        ),
        'm_height' => 'CM',
        'm_weight' => 'KG',
    );
    $attr_list = $bwh_arr = array();
    foreach ($att_data as $val) {
        $value = $val['value'];
        if ($value == '' || $value == NULL) {
            // ȥ�� һЩ��ֵ
            continue;
        }
        $name = $val['name'];
        $vkey = $val['key'];
        if (in_array($vkey, array('p_order_income', 'p_team', 'm_level', 'yp_place', 'yp_background', 't_way',
            'yp_can_photo', 'yp_lighter', 'yp_other_equitment', 'ms_forwarding', 'hz_order_way', 'hz_place'))) {
            // ����ʾ ��������,�Ŷӹ���
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
        $type_id = $style_keymap_[$key];  // ƥ���ӦID
        $style_info = isset($type_list_[$type_id]) ? $type_list_[$type_id] : array();
        if ($key === 'm') {
            $bwh_arr['m_cups'] = $bwh_arr['m_cups'] . $bwh_arr['m_cup'];
            unset($bwh_arr['m_cup']);
            $style_info['bwh'] = array(
                'type' => 'round',
                'title' => '���',
                'value' => $bwh_arr,
            );
        }
        $style_info['description'] = $value;
        $property[] = $style_info;
    }
    return $property;
}

/**
 * ��ȡ ���� ����
 *
 * @param string $contents
 * @param int $size ��ȡ��С ( 2���ַ� )
 * @return string
 */
function interface_content_strip($contents, $size = 300) {
    $contents = trim($contents);
    $size = intval($size);
    if (empty($contents) || $size < 1) {
        return $contents;
    }
    $contents = html_entity_decode($contents, ENT_QUOTES, 'GB2312');  // ת����
    $contents = mb_strimwidth(strip_tags($contents), 0, $size, '...');  // ��ȡǰ150
    $contents = trim(interface_html_decode($contents));   // html ������ǩ ת����
    return $contents;
}

/**
 * ���� תUBB
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
 * ��ȡ ��Ʒ����
 *
 * @param array $goods_system_data
 * @param int $goods_location_id ( for ��ʳ)
 * @return array
 */
function interface_get_goods_property($goods_system_data, $goods_location_id = 101029001) {
    $hide_item_ = array(
        'c3e878e27f52e2a57ace4d9a76fd9acf', // ������ϵ��ͼ ������ 2015-9-16
//        '00ec53c4682d36f5c4359f4ae7bd7ba1', // ����� ������ 2015-11-19
    );
    $service = $guide = $guide_img = $menu = $package = array();  // ��������
    $recommend = ''; // �Ƽ�ԭ�� ( for ��ʳ )
    $tmp_property = array();  // ��ʱ���,�������Դ���
    $property_unit_conf_ = pai_mall_load_config('property_unit');  // ��ȡ���Զ�Ӧ��λ
    foreach ($goods_system_data as $property_info) {
        $property_value = trim($property_info['value']);
        if ($property_value == '' || $property_value == null || $property_value == 'null') {
            continue;
        }
        $property_key = $property_info['key']; // ����key
        if (in_array($property_key, $hide_item_)) {
            continue;
        }
        $property_id = $property_info['id']; // ����ID
        $property_name = $property_info['name'];  // ����
        // ��λ
        $property_unit = empty($property_info['mess']) ? (isset($property_unit_conf_[$property_id]) ? $property_unit_conf_[$property_id] : '') : $property_info['mess'];
        if ($property_id == 249) {
            // Լ��ʳ ( ��ʽ )
            $menu = array(
                'id' => $property_id,
                'title' => $property_name,
                'value' => explode('|', $property_value),
            );
            continue;
        }
        if (in_array($property_id, array(266, 276, 277))) {
            // Լ��ʳ ( �����Ƽ� )
            $recommend .= $property_value;
            continue;
        }
        if (in_array($property_id, array(258, 259, 260, 261))) {
            // Լ��ʳ ( ���� )
            $addr = '';
            if (in_array($property_id, array(259, 261))) {
                // 259������ַ, 261������ʽ ���ʡ��
                $addr = get_poco_location_name_by_location_id($goods_location_id) . ' ';
            }
            $guide[] = array(
                'id' => $property_id,
                'title' => $property_name . '��', // ���ð��
                'value' => $addr . $property_value,
            );
            continue;
        }
        if ($property_id == 265) {
            // Լ��ʳ ( ����ͼƬ )
            $guide_images_ = explode(',', $property_value);
            foreach ($guide_images_ as $img) {
                $guide_imgs[] = array(
                    'thumb' => yueyue_resize_act_img_url($img, '640'), // ����ͼ
                    'original' => yueyue_resize_act_img_url($img), // ԭͼ
                );
            }
            continue;
        }
        if ($property_id == 320 && !empty($goods_location_id)) {  // Ӱ����ϸ��ַ
            // ��� ����
            $property_value = get_poco_location_name_by_location_id($goods_location_id) . ' ' . $property_value;
        }
        if (in_array($property_id, array(360, 361))) {  // ��ѵʱ�䴦��
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
                'title' => '��ѵʱ�䣺',
                'value' => $train_value,
            );
            continue;
        }
        if (in_array($property_id, array(272, 381))) {  // ���ַ,��������
            if (!isset($tmp_property[272])) {
                if (!isset($tmp_property[381])) {
                    $tmp_property[$property_id] = $property_value;
                    continue;
                }
                $tmp_property[381] = get_poco_location_name_by_location_id($tmp_property[381]);
                $event_addr_value = $tmp_property[381] . ' ' . $property_value;
            } else {
                $property_value = get_poco_location_name_by_location_id($property_value);  // ����� id => city
                $event_addr_value = $property_value . ' ' . $tmp_property[272];
            }
            // �(���ַ) �ŵ�һ��
            $property_ = array(
                'id' => '272,381',
                'title' => '���ַ��',
                'value' => $event_addr_value,
            );
            $service = array_merge(array($property_), $service);
            continue;
        }
        if (in_array($property_id, array(382, 452))) {  // ������ѵ��ǩ
            if ($property_id == 382) {
                $tmp_property[$property_id] = $property_value;
            } elseif ($property_id == 452) {
                if (strpos($tmp_property[382], '����') === false) {
                    // ��ѵ���� û��ѡ������, ��������ǩ������
                    continue;
                }
            }
        }
        if (in_array($property_id, array(314, 315, 316, 444, 445, 446))) {
            if (empty($property_value)) {
                continue;
            }
            if (in_array($property_id, array(314, 315, 316))) {
                // �ײ�����
                $service[] = array(
                    'id' => $property_id,
                    'title' => '--' . $property_name . '--',
                    'value' => ''
                );
            }
            // ��Ӱ�ײ�/Լ��Ȥ���� ( ���л� ) 2015-9-9
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
                    'title' => $package_['name'] . '��',
                    'value' => $package_value . $package_unit,
                );
                if ($package_key == 'name') {
                    $package[$property_id] = $package_value;
                }
            }
            continue;
        }
        if (!empty($property_info['child_data'])) {
            // ֧�� �°��� 2015-9-8
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
            if (in_array($property_id, array(450))) {  // ��ѵ��ǩ
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
            // fix �γ����� ��λ����  2015-10-13
            $property_unit = '';
        }
        $property_value = str_replace(array('<br>', '<br/>', '<br />'), "\n", $property_value); // ����
        $property_ = array(
            'id' => $property_id,
            'title' => $property_name . '��',
            'value' => $property_value . $property_unit,
        );
        if (in_array($property_id, array(267, 272))) {
            // ��ʳ(ԤԼҪ��)/ �(���ַ) ���ڵ�һλ
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
 * ��ȡ �����б�����
 *
 * @param array $goods_prices_list_data
 * @param int $activity_id
 * @param boolean $is_seller �Ƿ����̼�
 * @return array
 */
function interface_get_goods_showing($goods_prices_list_data, $activity_id = 0, $is_seller = false) {
    if (empty($goods_prices_list_data) || !is_array($goods_prices_list_data)) {
        return array();
    }
    $showing_exhibit = $all_exhibit = array();
    $all_is_finish = 1;
    $tmp_i = 0;
    $min_attend_prices = $max_attend_prices = 0;  // ���г��� �����߼�
    $all_stock_num_total = $all_stock_num = $all_attend_num = 0;  // ���г��� �����Ϣ
    $min_attend_unit = $max_attend_unit = '';  // ����г��� ���/�߼۵Ĺ��λ
    $mall_order_obj = POCO::singleton('pai_mall_order_class');  // for seller
    foreach ($goods_prices_list_data as $value) {
        $show_stock_num_total = intval($value['stock_num_total']);  // �ܿ��
        $all_stock_num_total += $show_stock_num_total;
        $show_stock_num = intval($value['stock_num']); // ��ǰ���
        $all_stock_num += $show_stock_num;
        $show_attend = $show_stock_num_total - $show_stock_num;  // ��������
        $show_name = $value['name'];
        $start_time = $value['time_s'];  // ��ʼʱ��
        $show_start = date('Y-m-d H:i', $start_time);
        $end_time = intval($value['time_e']);  // ����ʱ��
        $show_end = date('Y-m-d H:i', $end_time);
        $show_status = intval($value['status']);  //  1������ 2����
        $show_prices_list_data = $value['prices_list_data'];
        $show_prices = $max_prices = 0;
        $unit = $max_unit = '';
        foreach ($show_prices_list_data as $show_prices_list) {
            $tmp_price_ = $show_prices_list['prices'];
            if ($tmp_price_ <= 0) {
                continue;
            }
            $tmp_unit_ = $show_prices_list['name']; // ���
            if ($show_prices <= 0 || bccomp($tmp_price_, $show_prices, 2) < 0) {
                $show_prices = $tmp_price_;
                $unit = $tmp_unit_;
            }
            if ($max_prices <= 0 || bccomp($tmp_price_, $max_prices, 2) > 0) {
                $max_prices = $tmp_price_;
                $max_unit = $tmp_unit_;
            }
            if ($min_attend_prices <= 0 || bccomp($tmp_price_, $min_attend_prices, 2) < 0) {
                $min_attend_prices = $tmp_price_;  // ���г�����ͼ�
                $min_attend_unit = $tmp_unit_;
            }
            if ($max_attend_prices <= 0 || bccomp($tmp_price_, $max_attend_prices, 2) > 0) {
                $max_attend_prices = $tmp_price_;  // ���г�����߼�
                $max_attend_unit = $tmp_unit_;
            }
        }
        if (bccomp($show_prices, $max_prices, 2) != 0) {
            $seller_prices = '��' . $show_prices . '/' . $unit . '-��' . $max_prices . '/' . $max_unit;
        } else {
            $seller_prices = '��' . $show_prices . '/' . $unit;
        }
        $unit = empty($unit) ? '' : '/' . $unit;  // ���λ
        $is_finish = interface_activity_is_finish($end_time, $show_status);  // ��Ƿ�����ж�
        $stage_id = $value['type_id'];  // ����ID
        if ($is_seller == true && !empty($activity_id)) {
            $trade_total = $mall_order_obj->get_order_list_of_paid_by_stage($activity_id, $stage_id, true);
            $show_attend = intval($trade_total);  // ��������
        }
        $all_attend_num += $show_attend;  // ��(����/����)����
        $exhibit = array(
            'stage_id' => $stage_id,  // ����ID
            'status' => $show_status,
            'title' => $show_name . ' ' . $show_start . '��' . $show_end,
            'name' => $show_name,
            'period' => $show_start . '��' . $show_end,
            'prices' => '��' . sprintf('%.2f', $show_prices),
            'unit' => (count($show_prices_list_data) > 1) ? $unit . ' ��' : $unit,
            'seller_prices' => $seller_prices,
            'attend_str' => $is_seller == true ? '�Ѹ������� ' : '�ѱ������� ',
            'attend_num' => $show_attend < 0 ? 0 : $show_attend,
            'total_num' => $show_stock_num_total,
            'stock_num' => $show_stock_num,
            'is_finish' => $is_finish ? 1 : 0,
        );
        $key = $start_time . ($tmp_i % 10);  // ����key
        $tmp_i++;
        $all_exhibit[$key] = $exhibit;
        if ($is_finish == true) {
            // �ѽ����� ���� ����ʾ
            continue;
        }
        if ($show_stock_num > 0) { // �п��(�Ǳ���)
            $all_is_finish = 0;
        }
        // �������
//        $exhibit['title'] = mb_strimwidth(strip_tags($show_name), 0, 9, '...') . ' ' . $show_start . '��' . $show_end;
        $showing_exhibit[$key] = $exhibit;
    }
    // ��������
    if (!empty($all_exhibit)) {
        ksort($all_exhibit);
        $all_exhibit = array_values($all_exhibit);
    }
    if (!empty($showing_exhibit)) {
        ksort($showing_exhibit);
        $showing_exhibit = array_values($showing_exhibit);
    }
    if (bccomp($min_attend_prices, $max_attend_prices, 2) != 0) {
        $seller_prices = '��' . $min_attend_prices . '/' . $min_attend_unit . '-��' . $max_attend_prices . '/' . $max_attend_unit;
    } else {
        $seller_prices = '��' . $min_attend_prices . '/' . $min_attend_unit;
    }
    $attend = array(
        'seller_prices' => $seller_prices,
        'prices' => '��' . sprintf('%.2f', $min_attend_prices),
        'unit' => '/' . $min_attend_unit . ' ��',
        'attend_str' => $is_seller == true ? '�Ѹ������� ' : '�ѱ������� ',
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
 * ��ȡ ������ ɸѡ����
 *
 * @param int $type_id ����ID
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
        $title = $attr['text'];  // ��ʾ����
        $choice_type = strval($attr['select_type']);  // 1��ѡ,2��ѡ
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
                    // �[�Զ���], ����Ҫ
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
    // ���� ��ѵ��ǩ
    $tag_keymap = array(
        'detail[382]383' => 'detail[400]',  // ��Ӱ��ѵ
        'detail[382]385' => 'detail[402]',  // ������ѵ
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
 * ��ȡ ɸѡ/���� ����
 *
 * @param int $type_id �����ID
 * @param boolean $b_get_filter
 * @param array $select_data ѡ����Ŀ
 * @return array
 */
function interface_get_search_screening($type_id, $b_get_filter = true, $select_data = array()) {
    // ����
    $orderby = array(
        'title' => '����',
        'goods' => array(
            array('key' => 'orderby', 'title' => 'Ĭ������', 'value' => '-1',),
            array('key' => 'orderby', 'title' => '�����ߵ���', 'value' => '1',),
//            array('key' => 'orderby', 'title' => '�����͵���', 'value' => '2',),
            array('key' => 'orderby', 'title' => '�۸�ߵ���', 'value' => '3',),
            array('key' => 'orderby', 'title' => '�۸�͵���', 'value' => '4',),
            array('key' => 'orderby', 'title' => '�����ߵ���', 'value' => '5',),
//            array('key' => 'orderby', 'title' => '�����͵���', 'value' => '6',),
            array('key' => 'orderby', 'title' => '���ָߵ���', 'value' => '7',),
//            array('key' => 'orderby', 'title' => '���ֵ͵���', 'value' => '8',),
        ),
        'seller' => array(
            array('key' => 'orderby', 'title' => 'Ĭ������', 'value' => '-1',),
            array('key' => 'orderby', 'title' => '�����ߵ���', 'value' => '1',),
            array('key' => 'orderby', 'title' => '�����͵���', 'value' => '2',),
            array('key' => 'orderby', 'title' => '���ָߵ���', 'value' => '3',),
            array('key' => 'orderby', 'title' => '���ֵ͵���', 'value' => '4',),
        ),
    );
    // 3 ��ױ,31 ģ��,40 ��Ӱʦ,12 Ӱ��,5 ��ѵ,41 ��ʳ,43 ��������
    $type_name_ = array(3 => '��ױ', 31 => 'ģ��', 40 => '��Ӱʦ', 12 => 'Ӱ��', 5 => '��ѵ', 41 => '��ʳ', 43 => 'Լ��Ȥ', 42 => '�');
    $filter = ($b_get_filter === false) ? array() : interface_get_search_filter($type_id);
    // ��ҪĬ��ѡ�� 2015-10-28
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
            'choice_type' => 2, // ��һ��  1��ѡ2��ѡ
            'title' => $type_name_[$type_id] . 'ɸѡ',
            'options' => empty($filter) ? array() : $filter,
        ),
        'orderby' => empty($orderby) ? array() : $orderby,
    );
}

/*
 * ɸѡ���� ѡ�д���
 *
 * @param array $data
 * @param array $select_data ѡ������
 * @param string $check_field �Աȵ� ֵ�ֶ�����
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
 * ��ʼ�� ��Ʒ����
 *
 * @param array $system_data ϵͳ����
 * @param array $addon_data ��������( for ��Ӱ )
 * @return array
 */
function interface_init_goods_property($system_data, $addon_data = array()) {
    if (empty($system_data)) {
        return array();
    }
    // �۸�(��Ӱר��)
    $prices_keymap = array(
        'ad13a2a07ca4b7642959dc0c4c740ab6' => '311', // ��������ײ�����
        '758874998f5bd0c393da094e1967a72b' => '310', // ��������ײ�����
        '3fe94a002317b5f9259f82690aeea4cd' => '312', // ��������ײ�����
    );
    $property_unit_config = pai_mall_load_config('property_unit');  // ��ȡ���Զ�Ӧ��λ
    // ��������
    $words_style_ = array(
        // ��ѵ
        '093f65e080a295f8076b1c5722a46aa2', '072b030ba126b2f4b2374f342be9ed44', '7f39f8317fbdb1988ef4c628eba02591',
        'fc490ca45c00b1249bbe3554a4fdf6fb', '3295c76acbf4caaed33c36b1b5fc2cb1', '2a38a4a9316c49e5a833517c45d31070',
        '5737c6ec2e0716f3d8a7a5c4e0de0d9a', 'c058f544c737782deacefa532d9add4c', 'e7b24b112a44fdd9ee93bdf998c6ca0e',
        '52720e003547c70561bf5e03b95aa99f', '735b90b4568125ed6c3f678819b6e058',
        // ��ױ
        '7cbbc409ec990f19c78c75bd1e06f215',
        // Ӱ��
        '320722549d1751cf3f247855f937b982', '6f4922f45568161a8cdf4ad2299f6d23',
        // ����
        'd709f38ef758b5066ef31b18039b8ce5', '0e01938fc48a2cfb5f2217fbfb00722d',
        // ��Ӱ
        '98dce83da57b0395e163467c9dae521b', 'f4b9ec30ad9f68f89b29639786cb62ef',
        // ��ʳ
        '077e29b11be80ab57e1a2ecabb7da330', '502e4a16930e414107ee22b6198c578f', 'cfa0860e83a4c3a763a7e62d825349f7',
        'b1a59b315fc9a3002ce38bbe070ec3f5', 'eda80a3d5b344bc40f3bc04f65b7a357', '8f121ce07d74717e0b1f21d122e04521',
        'f7664060cc52bc6f3d620bcedc94a4b6',
        // ����
        'booking_time', 're_material', 'date_time', 'eed5af6add95a9a6f1252739b1ad8c24',
        'goods_size', 'goods_material', 'custom_time',
    );
    // ��ѡ
    $double_select_ = array(
        '839ab46820b524afda05122893c2fe8e', '9fc3d7152ba9336a670e36d0ed79bc43',
        // ��ѵ��ǩ
        'f5f8590cd58a54e94377e6ae2eded4d9',
    );
    // �����ж���Ŀ¼
    $sub_options_ = array(
        // ��Ӱ,��ѵ
        '8613985ec49eb8f757ae6439e879bb2a', '9fc3d7152ba9336a670e36d0ed79bc43',
    );
    // ������� ����
    $length_limit_ = array(
        '502e4a16930e414107ee22b6198c578f' => 30,  // ��������
        'eda80a3d5b344bc40f3bc04f65b7a357' => 200,  // ԤԼҪ��
        '8f121ce07d74717e0b1f21d122e04521' => 200,  // ��ܰ��ʾ
        'b1a59b315fc9a3002ce38bbe070ec3f5' => 30, // ������ʽ
    );
    // ��ʾ��
    $hint_data = array(
        'c058f544c737782deacefa532d9add4c' => '�����뱸ע',
        '502e4a16930e414107ee22b6198c578f' => '�����������',
        'a4f23670e1833f3fdb077ca70bbd5d66' => '������ϵ�绰',
        'b1a59b315fc9a3002ce38bbe070ec3f5' => '�˳�·�ߡ��Լ�·�ߡ�·��ͼ',
        'eda80a3d5b344bc40f3bc04f65b7a357' => '����ԤԼҪ��',
        '8f121ce07d74717e0b1f21d122e04521' => '��ܰ��ʾ',
        '2a38a4a9316c49e5a833517c45d31070' => 'ʾ����8��18����ÿ����',
        'date_time' => 'ʾ����ÿ��������8:00',
        'booking_time' => 'ʾ�����û�����ǰһ��ԤԼ',
    );
    // ������ʾ��ʾ��
    $err_tips_ = array(
        'a3f390d88e4c41f2747bfa2f1b5f87db' => '��ѡ��ױ������',
        'e2c420d928d4bf8ce0ff2ec19b371514' => '�������ʱʱ��',
        '16a5cdae362b8d27a1d8f8c7b78b4330' => '���������ļ���',
        'd9d4f495e875a2e075a1a4a6e1b9770f' => '��ѡ��������',
        '07cdfd23373b17c6b337251c22b7ea57' => '��ѡ�����ؼ���',
        'fb7b9ffa5462084c5f4e7e85a093e6d7' => '���������ؼ���',
        'd709f38ef758b5066ef31b18039b8ce5' => '��������ϸ��ַ',
        '0e01938fc48a2cfb5f2217fbfb00722d' => '������ע������',

        '98dce83da57b0395e163467c9dae521b' => '��༭�����ַ',
        'f4b9ec30ad9f68f89b29639786cb62ef' => '��༭��������',
        '839ab46820b524afda05122893c2fe8e' => '��ѡ���ǩ',
        '8613985ec49eb8f757ae6439e879bb2a' => '��ѡ����������',

        '70efdf2ec9b086079795c442636b55fb' => '��ѡ�����������',
        '1f0e3dad99908345f7439f8ffabdffc4' => '������ʹ�����',
        '98f13708210194c475687be6106a3b84' => '��ѡ�񱳾�',
        '6f4922f45568161a8cdf4ad2299f6d23' => '������ƹ�/��������',
        '320722549d1751cf3f247855f937b982' => '������Ӱ���ַ',

        '9fc3d7152ba9336a670e36d0ed79bc43' => '��ѡ����ѵ����',
        '47d1e990583c9c67424d369f3414728e' => '��ѡ��γ����',
        '093f65e080a295f8076b1c5722a46aa2' => '��༭�γ�Ŀ��',
        '5737c6ec2e0716f3d8a7a5c4e0de0d9a' => '��༭�γ�����',
        '735b90b4568125ed6c3f678819b6e058' => '��༭�γ̴��',
        'fc490ca45c00b1249bbe3554a4fdf6fb' => '�������Ͽε�ַ',
        '072b030ba126b2f4b2374f342be9ed44' => '��ѡ�񿪿�����',
        '52720e003547c70561bf5e03b95aa99f' => '��ѡ����ѵʱ��',
        '2a38a4a9316c49e5a833517c45d31070' => '��ѡ��γ�����',
        '7647966b7343c29048673252e490f736' => '������γ�����',
        'caf1a3dfb505ffed0d024130f58c5cfa' => '������γ�ʱ��',
        '44f683a84163b3523afe57c2e008bc8c' => '��ѡ���ڿη�ʽ',
        '5b8add2a5d98b1a652ea7fd72d942dac' => '��ѡ��γ���ʽ',

        '4f6ffe13a5d75b2d6a3923922b3922e5' => '��ѡ����ѵ����',
        'f5f8590cd58a54e94377e6ae2eded4d9' => '��ѡ����ѵ��ǩ',
        '9431c87f273e507e6040fcb07dcb4509' => '������������ѵ����',

        'ad13a2a07ca4b7642959dc0c4c740ab6.name' => '�������ײ�����',
        '3fe94a002317b5f9259f82690aeea4cd.name' => '�������ײ�����',
    );
    // ��ʾ��Ϣ
    $notice_data_ = array(
        'ad13a2a07ca4b7642959dc0c4c740ab6' => '����д�ײ����ơ��۸񼰷����׼',  // ��׼�ײ�
        '758874998f5bd0c393da094e1967a72b' => '�˷���ֻ������+��Ƭ�۸��밴����д',  // �������
        '3fe94a002317b5f9259f82690aeea4cd' => '�˷���ѡ��밴����д�ײ�����',  // �������
    );
    // �Զ����������� ( �ؼ����ͣ�1����ҳѡ�� 2������ѡ��3����ҳ���룻4����ǰ���룻5����ǩѡ�� )
    $control_type_ = array(
        'd709f38ef758b5066ef31b18039b8ce5' => 3,
        'eed5af6add95a9a6f1252739b1ad8c24' => 3,
        're_material' => 3,  // (��������)����
        'photo_content' => 3, // (Լ��Ӱʦ)���
    );
    // ѡ����
    $optional_fields_ = array(
        '9431c87f273e507e6040fcb07dcb4509',
    );
    // ����
    $title_data_ = array(
        'custom_time' => '������ʱ',
    );
    $init_list = $package = $first_options = $second_options = array();
    foreach ($system_data as $sys_value) {
        $sys_key = $sys_value['key'];  // ����ֵ
        if (empty($sys_key)) {  // û��key
            continue;
        }
        $input_type = 2;  // ����
        if (in_array($sys_key, $words_style_)) {
            $input_type = 1;  // ����
        }
        $control_type = '4'; //�ؼ����ͣ�1����ҳѡ�� 2������ѡ��3����ҳ���룻4����ǰ���룻5����ǩѡ��
        $sys_id = $sys_value['id']; // ��ʶ(��ȡ��λ)
        $hint = '�༭';  // ��ʾ��
        $unit = isset($property_unit_config[$sys_id]) ? $property_unit_config[$sys_id] : '';  // ��λ
        $show_value = empty($sys_value['value']) ? '' : $sys_value['value'];  // ��ʾ��ֵ
        $option_value = '';  // ��ʼ�� ���ֵ
        $choice_type = $choice_num = 0;  //ѡ�� (���)ѡȡ����
        $format_data = $sys_value['format_data'];  // ��ʽ������(��Ӱ:�ײ���Ϣ,����:������Ϣ)
        if (!empty($format_data)) {
            $package = array();
            $p_prices = '';  // �ײͼ۸�
            foreach ($format_data as $format_info) {
                $format_key = $format_info['key'];  // ����ֵ
                // (�ײ�)�۸�������ʱ��֮��
                $p_show_value = empty($format_info['value']) ? '' : $format_info['value'];
                if ($format_key == '95') {
                    $prices_id = $prices_keymap[$sys_key]; // �۸�
                    if (isset($addon_data[$prices_id])) {
                        $package[] = $addon_data[$prices_id];
                        $p_prices = $addon_data[$prices_id]['value'];
                    }
                }
                $item_split = '0'; // �����Ƿ�ָ� (0���ָ�)
                if (in_array($format_key, array('98'))) {
                    $item_split = '1';  // �ָ�
                }
                if ($sys_key == '758874998f5bd0c393da094e1967a72b') { // �������
                    if (in_array($format_key, array('name', '98', '99', '106', 'photo_content'))) {
                        continue; // ���� ������� ʱ, ����ʾ ����,����
                    }
                    if (in_array($format_key, array('97'))) {
                        $item_split = '1'; // ��ѡ���� �ָ����
                    }
                }
                $p_hint = '�༭';
                $p_input_type = in_array($format_key, $words_style_) ? 1 : 2; // 1����,2����
                $p_control_type = 4; // �ؼ�����
                $p_max_length = 0; // ����������
                if (in_array($format_key, array('name', 'photo_content'))) {
                    $p_input_type = 1; // ����
                }
                if ($format_key == 'name') {
                    $p_max_length = 8;  // Ʒ���µ��ײ����ֳ��ȣ��޶�Ϊ8����
                }
                $p_options = array();
                $p_option_value = ''; // ���ֵ
                $p_child_data = $format_info['child_data'];  // ѡ��
                if (!empty($p_child_data)) {
                    foreach ($p_child_data as $p_child) {
                        $p_options[] = array(
                            'value' => $p_child['name'],
                            'option_value' => $p_child['key'],
                        );
                    }
                    $p_hint = '��ѡ��';
                    $p_control_type = 2;
                    $p_input_type = 0;
                    $p_option_value = $p_show_value;
                }
                $p_hint = isset($hint_data[$format_key]) ? $hint_data[$format_key] : $p_hint;  // ��ʾ��
                $p_title = isset($title_data_[$format_key]) ? $title_data_[$format_key] : $format_info['name'];
                // �Ƿ����Զ��� �ؼ�����
                $p_control_type = isset($control_type_[$format_key]) ? $control_type_[$format_key] : $p_control_type;
                $p_tips = isset($err_tips_[$sys_key . '.' . $format_key]) ? $err_tips_[$sys_key . '.' . $format_key] : '';
                $package[] = array(
                    'type' => strval($p_control_type), //�ؼ�����
                    'id' => $format_key,
                    'title' => $p_title,  // ��ʾ�ı���
                    'value' => $p_show_value,  // ��ʾ��ֵ
                    'option_value' => $p_option_value,
                    'input_type' => $p_input_type,  // ����1,����2
                    'input_required' => in_array($format_key, $optional_fields_) ? 0 : 1,  // 0ѡ��,1����
                    'max_length' => $p_max_length,  // ����������
                    'item_split' => $item_split,  // 1 �ָ�, 0 ���ָ�
                    'options' => $p_options,
                    'hint' => $p_hint,
                    'unit' => $format_info['mess'],
                    'tips' => $p_tips,
                );
            }
            $init_list[$sys_key] = array(
                'type' => '3', //�ؼ�����
                'id' => $sys_key,
                'title' => str_replace('����', '', $sys_value['name']),
                'value' => empty($p_prices) ? '' : '��' . $p_prices, // �۸�
                'option_value' => $p_prices,
                'input_type' => 1,
                'hint' => '�༭',
                'unit' => '',
                'tips' => '�������ײ͹��',
                'notice' => isset($notice_data_[$sys_key]) ? $notice_data_[$sys_key] : '',
                'options' => $package,
            );
            continue;
        }
        $child_data = $sys_value['child_data']; // ����ѡ��
        if (!empty($child_data)) {
            $option_value = $show_value; // ѡ��� (���ֵ=��ʾֵ)
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
                $c_value = $child['value'];  // ����ѡ��ֵ
                if (!empty($child['child_data'])) {
                    $items = array();
                    $sub_c_show_value = '';  // һ��ѡ��
                    $sub_option_value = '';  // �������ֵ
                    foreach ($child['child_data'] as $sub_child) {
                        $sub_c_key = $sub_child['key'];
                        $sub_c_name = $sub_child['name'];
                        if (in_array($sub_c_key, explode(',', $option_value)) || in_array($sub_c_key, explode(',', $c_value))) {
                            $sub_c_show_value .= $sub_c_name . ',';
                            $sub_option_value .= $sub_c_key . ','; // ƴ�Ӷ���key
//                            $option_value = $sub_c_key; // �������key
                        }
                        $items[] = array(
                            'value' => $sub_c_name,
                            'option_value' => $sub_c_key,
                        );
                    }
                    if (!empty($sub_option_value)) {
                        if (in_array($c_key, array('18d8042386b79e2c279fd162df0205c8', '816b112c6105b3ebd537828a39af4818', '69cb3ea317a32c4e6143e665fdb20b14'))) {
                            // ��ѵ��ǩ
                            $option_value = $c_key . '-' . trim($sub_option_value, ',');
                        } else {
                            // key һ��ƴ�Ӷ���
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
                    // �����ж���Ŀ¼ (��������,�ٳ���ѵ ...)
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
            $control_type = '2'; //�ؼ����ͣ�1����ҳѡ�� 2������ѡ��3����ҳ���룻4����ǰ���룻5����ǩѡ��
            $input_type = 0;
            $choice_num = 1;
            $choice_type = 1;  // ��ѡ
            $hint = '��ѡ��';
            $show_value = empty($c_show_value) ? '' : trim($c_show_value, '-');
        }
        $hint = isset($hint_data[$sys_key]) ? $hint_data[$sys_key] : $hint;  // ��ʾ��
        // �����ѡ
        if (in_array($sys_key, $double_select_)) {
            if (in_array($sys_key, array('839ab46820b524afda05122893c2fe8e'))) {
                // �����ǩ��ǩ
                $control_type = 5;
            }
            $choice_type = 2;
            $choice_num = 2;  // ����ѡ����
        }
        // �Զ���ؼ�����
        $control_type = isset($control_type_[$sys_key]) ? $control_type_[$sys_key] : $control_type;
        $title = $sys_value['name'];
        if (in_array($sys_key, array('c0e190d8267e36708f955d7ab048990d', 'ec8ce6abb3e952a85b8551ba726a1227'))) {
            // ��ϵ, ����
            $title = str_replace('��ǩ', '', $title);
        }
        $init_list[$sys_key] = array(
            'type' => $control_type, //�ؼ�����
            'id' => $sys_key, // ����ֵ
            'title' => $title, // ��ʾ����
            'value' => $show_value, // ��ʾֵ
            'option_value' => $option_value, // ���ֵ
            'input_type' => $input_type, // �������� 1 ���� 2 ���� 3 С��
            'input_required' => in_array($sys_key, $optional_fields_) ? 0 : 1,  // 0ѡ��,1����
            'max_length' => isset($length_limit_[$sys_key]) ? $length_limit_[$sys_key] : 0,  // ����������
            'choice_type' => $choice_type, // 1 ��ѡ, 2 ��ѡ
            'choice_num' => $choice_num, // ���ѡ�и���
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
 * ��ʼ���������� (����)
 *
 * @param mixed $patch_data ��ʼ������
 * @param string $pro_type ��������
 * @param int $type_id �����
 * @return array
 */
function interface_init_goods_property_patch($patch_data, $pro_type, $type_id = 0) {
    switch ($pro_type) {
        case 'titles':
            return array(
                'type' => '4', //�ؼ�����
                'id' => 'titles', // ����ֵ
                'title' => '��������', // ��ʾ����
                'value' => empty($patch_data) ? '' : $patch_data, // ��ʾֵ
                'option_value' => '', // ���ֵ
                'input_type' => '1', // �������� 1 ���� 2 ���� 3 С��
                'input_required' => 1, // ����
                'max_length' => 30,  // ����������
                'hint' => '�������������',
                'unit' => '',
                'tips' => '�������������',
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
                    $location = get_poco_location_name_by_location_id($location_id);  // ����;
                    if ($type_id == 5) {
                        list($province, $l_city) = explode(' ', $location);
                        $location = str_replace('��', '', $l_city);
                    }
                    $city .= '/' . $location;
                }
                $city = trim($city, '/');
            }
            return array(
                'type' => '2', //�ؼ�����
                'id' => 'location_id', // ����ֵ
                'title' => $type_id == 5 ? '������Χ' : '���ڵ���',
                'value' => $city, // ��ʾֵ
                'option_value' => empty($patch_data) ? '' : $patch_data, // ���ֵ
                'input_type' => '2', // �������� 1 ���� 2 ���� 3 С��
                'input_required' => 1, // ����
                'hint' => '',
                'unit' => '',
                'tips' => '��ѡ�����',
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
                    'hint' => '�۸�',
                    'unit' => 'Ԫ',
                    'tips' => '������۸�',
                );
            }
            return $prices_list;
            break;
        default:
            return array();
    }
}

/**
 * ��ȡ��Ʒ�� ��������
 *
 * @param int $user_id
 * @param int $goods_id
 * @param array|float $prices_data
 *     array( array(
 *         'prices_type_id' => 77, //����
 *         'goods_prices' => 88, //����
 *     ), )
 * @param object $promotion_obj
 * @param boolean $get_promotion_list �Ƿ񷵻ش����б�
 * @return array
 */
function interface_get_goods_promotion($user_id, $goods_id, $prices_data, $promotion_obj = null, $get_promotion_list = false) {
    if (empty($goods_id) || empty($prices_data)) {
        return array();
    }
    if (!is_array($prices_data)) {
        $prices_list = array(
            array(
                'prices_type_id' => 0, //����
                'goods_prices' => floatval($prices_data), //����
            ),
        );
    } else {
        $prices_list = $prices_data;
    }
    $type_target = 'goods';  // ��������Ϊ��Ʒ
    $show_param_info = array(
        'channel_module' => 'mall_order', //����
        'org_user_id' => 0,
        'location_id' => 0,
        'seller_user_id' => 0,
        'mall_type_id' => 0,
        'channel_gid' => $goods_id, //����
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
    $period = $most_saving_promotion['start_time_str'] . '-' . $most_saving_promotion['end_time_str'];  // ��Ч��
    $abate = array(
        'abate' => $most_saving_promotion['cal_save_amount'],
        'notice' => $most_saving_promotion['type_name'],
        'period' => $period, // ��Ч��
        'description' => $period . "\n" . $most_saving_promotion['promotion_desc'],
        'marked' => 'http://image19-d.yueus.com/yueyue/20151012/20151012151631_726693_10002_34689.png?54x54_130',
    );
    if ($get_promotion_list === true) {
        $promotion_list = array();
        foreach ((array)$promotion_res['promotion_list'] as $result) {
            $prices_type_id = $result['prices_type_id'];
//            $period = $result['start_time_str'] . '-' . $result['end_time_str'];  // ��Ч��
            $promotion_list[$prices_type_id] = array(
                'abate' => $result['cal_save_amount'],
//                'notice' => $result['type_name'],
//                'period' => $period, // ��Ч��
//                'description' => $period . "\n" . $result['promotion_desc'],
                'marked' => 'http://image19-d.yueus.com/yueyue/20151012/20151012151631_726693_10002_34689.png?54x54_130',
            );
        }
        $abate['promotion_list'] = $promotion_list;
    }
    return $abate;
}

/**
 * ���� ������ ������ť
 *
 * @param string $status ����״̬
 * @param string $is_buyer_comment ����Ƿ�����
 * @return array
 */
function interface_trade_buyer_action($status, $is_buyer_comment) {
    if ($is_buyer_comment == 1) {  // ���������
        return array();
    }
    // ��ť�İ�
    $action_arr = array(
        0 => '����.pay|ȡ������.close',
//        1 => '�ܾ�.refuse|ͬ��.accept',
        2 => '�����˿�.close|��ʾ��ά��.sign',
        7 => 'ɾ������.delete',
        8 => '���۶Է�.appraise'
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
 * ���� �̼� ������ť
 *
 * @param string $status ����״̬
 * @param string $is_seller_comment �̼�����
 * @param string $version �汾��
 * @return array
 */
function interface_trade_seller_action($status, $is_seller_comment, $version = null) {
    if ($is_seller_comment == 1) {  // �̼�������
        return array();
    }
    // ��ť�İ�
    $action_arr = array(
        0 => '�رն���.close',
        1 => '�ܾ�����.refuse|���ܶ���.accept',
        2 => 'ɨ��ǩ��.sign',
//        2 => 'ȡ�����ײ��˿�.close|ɨ��ǩ��.sign',
//        7 => 'ɾ������.delete',
        10 => '���۶Է�.appraise'
    );
    if (version_compare($version, '1.1') >= 0) {
        $action_arr[0] = '�رն���.close|�޸ļ۸�.pending';
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
 * ��ȡ���б�
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
                $scheme = parse_url($url, PHP_URL_SCHEME);  // ��ȡЭ��ͷ
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
        if ($rank_id == 1011) {  // ͷ��,2��ͼ
            $issue['img'] = $val['content'];
            $issue['img_big'] = empty($val['remark']) ? $val['content'] : $val['remark'];
        }
        $list[] = $issue;
    }
    return $list;
}


/**
 * ��װ �������� ��ʾ
 *
 * @param array $trade_info ԭʼ��������
 * @param string $version �汾��Ϣ
 * @return array
 */
function interface_trade_property_data($trade_info, $version) {
    $type_id = $trade_info['type_id'];   // ����(��Ʒ)����
    $detail = $property = $goods = $showing = array();
    if ($type_id == 42) {  // �
        $showing = array(
            array('title' => '���Σ�', 'value' => $trade_info['stage_title']),
            array('title' => '���', 'value' => $trade_info['prices_spec']),
            array('title' => '������', 'value' => $trade_info['quantity'] . '��'),
            array('title' => '���ʼʱ�䣺', 'value' => date('Y-m-d H:i', $trade_info['service_start_time'])),
            array('title' => '�����ʱ�䣺', 'value' => date('Y-m-d H:i', $trade_info['service_end_time'])),
            array('title' => '��ص㣺', 'value' => $trade_info['service_address']),
        );
        $goods = array(
            'id' => $trade_info['activity_id'],
            'name' => $trade_info['activity_name'],
            'image' => $trade_info['activity_images'],
            'description' => '���' . $trade_info['prices_spec'],
            'price' => '��' . $trade_info['prime_prices'],
        );
        return array('goods' => $goods, 'detail' => $detail, 'property' => $property, 'showing' => $showing);
    }
    $spec = explode(':', $trade_info['prices_spec']);
    $limit = array(
        'title' => count($spec) > 1 ? trim($spec[0]) . '��' : 'ʱ����',
        'value' => count($spec) > 1 ? trim($spec[1]) : $trade_info['prices_spec']
    );
//    $account = array('title' => '��������������', 'value' => $trade_info['service_people'] . '��');
    $standard = array('title' => '���', 'value' => count($spec) > 1 ? trim($spec[1]) : $trade_info['prices_spec']);
    $time = array('title' => '����ʱ�䣺', 'value' => date('Y-m-d H:i', $trade_info['service_time']));
    $addr = array('title' => '�ص㣺', 'value' => $trade_info['service_address']);
    $num = array('title' => '����������', 'value' => $trade_info['quantity']);
    $goods = array(  // ������Ϣ
        'id' => $trade_info['goods_id'],
        'name' => $trade_info['goods_name'],
        'image' => $trade_info['goods_images'],
    );
    switch ($type_id) {
        case '31':  // ģ�ط���
            $detail = array($limit, $num);
            $property = array($time, $addr);
            break;
        case '5':  // ��ѵ
            $detail = array();
            $property = array($num);
            break;
        case '12':  // Ӱ��
            $detail = array($standard,);
            $property = array($addr, $time, $num);
            break;
        case '3':  // ��ױ
            $detail = array($time, $addr);
            $property = array($num);
            break;
        case '40':  // ��Ӱ
            $detail = array($time, $addr);
            $property = array($num);
            break;
        case '41': // ��ʳ
            $detail = array($time, $addr);
            $property = array($num);
            break;
        case '42': // � (����)
            break;
        case '43': // Լ��Ȥ
            $detail = array($time, $addr);
            $property = array($num);
            break;
        default:  // δ����
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
 * �������� ����
 *
 * @param float $overall_score ������
 * @param int $review_times ���۴���
 * @return float
 */
function interface_reckon_average_score($overall_score, $review_times) {
    if (empty($overall_score) || empty($review_times)) {
        return 5.0;
    }
    $average_score = ceil($overall_score / $review_times * 2) / 2;//�̼��ۺ�����
    return sprintf('%.1f', $average_score);
}

/**
 * ��Ƿ�����ж�
 *
 * @param int $end_time ����ʱ��
 * @param int $status �״̬
 * @param int $is_over �Ƿ����
 * @return boolean (-true ����)
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
