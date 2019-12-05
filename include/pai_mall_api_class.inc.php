<?php
/**
 * API类，对前台接口
 * 
 * 
 */

class pai_mall_api_class extends POCO_TDG
{

    /**
     * 数据验证
     * @param type $input
     * @return type
     */
    public function api_goods_data_check($input)
    {
        $pai_mall_goods_type_attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
        //循环数据校验处理
        $data_error = "";
        if(!empty($input['default_data']))
        {
            foreach($input['default_data'] as $key => $value)
            {
                if(empty($value))
                {
                    $data_error = $pai_mall_goods_type_attribute_obj->get_name_by_key($key);
                    $data_error_tips = "data_error";
                    break;
                }
                else
                {
                    $input['default_data'][$key] = trim($value);
                    //对图文编辑器做的过滤处理2015-7-6
                    if($key=="content")
                    {

                        //转码处理
                        //$tmp_content = html_entity_decode($input['default_data'][$key]);
                        $tmp_content = str_replace(array('&lt;','&gt;','&quot;'),array('<','>','"'),$input['default_data'][$key]);
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

                        $input['default_data'][$key] = $tmp_content;

                    }
                    else if($key=="prices")
                    {
                        //对价格进行取整处理2015-7-7
                        $input['default_data'][$key] = (int)$input['default_data'][$key];
                    }
                }
            }
        }


        $can_empty_system_data_array = array("7cbbc409ec990f19c78c75bd1e06f215","2723d092b63885e0d7c260cc007e8b9d","5f93f983524def3dca464469d2cf9f3e");
        if(!empty($input['system_data']))
        {
            foreach($input['system_data'] as $key => $value)
            {
                if(empty($value))
                {
                    //处理选填项
                    if(in_array($key,$can_empty_system_data_array))
                    {
                        //摄影服务的相册内容特殊处理
                        if($key=="2723d092b63885e0d7c260cc007e8b9d")
                        {
                            if($input['system_data']['f0935e4cd5920aa6c7c996a5ee53a70f']=="a97da629b098b75c294dffdc3e463904")//表示相册选中
                            {
                                $data_error = "相册内容";
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
                    $input['system_data'][$key] = trim($value);
                }
            }
        }


        if(!empty($input['prices_de']))
        {
            foreach($input['prices_de'] as $key => $value)
            {
                if(empty($value))
                {
                    //处理价格可以为空的项

                        $data_error = "价格";
                        $data_error_tips = "data_error";
                        break;

                }
                else
                {
                    $input['prices_de'][$key] = (int)$value;
                }

            }
        }
        
        return array('data'=>$input,'data_error_tips'=>$data_error_tips);
    }
    /**
     * 添加服务申请
     * @param type $post
     * @return type
     */
    public function api_add_service_sq($post)
    {
        $obj = POCO::singleton('pai_mall_certificate_service_class');
        return $obj->add_service_sq($post);
    }
    /**
     * 获取服务状态
     * @param type $user_id
     * @return type
     */
    public function api_get_service_status_by_user_id($user_id)
    {
        $obj = POCO::singleton('pai_mall_certificate_service_class');
        return $obj->get_service_status_by_user_id($user_id);
    }
    /**
     * 获取基础认证个人状态
     * @param type $user_id
     * @return type
     */
    public function api_get_person_status_by_user_id($user_id)
    {
        $obj = POCO::singleton('pai_mall_certificate_basic_class');
        return $obj->get_person_status_by_user_id($user_id);
    }
    
    /**
     * 检查基础认证身份证
     * @param type $id_card
     * @return type
     */
    public function api_id_card_check($id_card)
    {
        $obj = POCO::singleton('pai_mall_certificate_basic_class');
        return $obj->id_card_check($id_card);
    }
    /**
     * 添加商家申请
     * @param type $post
     * @return type
     */
     public function api_add_seller_sq($post)
     {
         $obj = POCO::singleton('pai_mall_certificate_basic_class');
         return $obj->add_seller_sq($post);
     }
    
     /**
      * 检查基础与企业认证是否可添加
      * @param type $user_id
      * @return type
      */   
     public function api_check_can_add($user_id)
     {
         $obj = POCO::singleton('pai_mall_certificate_basic_class');
         return $obj->check_can_add($user_id);
     }
     /**
      * 获取名称md5值
      * @param type $md5_key
      * @return type
      */
     public function api_get_name_by_md5_key($md5_key)
     {
         $obj = POCO::singleton('pai_mall_goods_type_attribute_class');
         return $obj->get_name_by_md5_key($md5_key);
     }
     
     /**
      * 获取名称根据key
      * @param type $key
      * @return type
      */
     public function api_get_name_by_key($key)
     {
         $obj = POCO::singleton('pai_mall_goods_type_attribute_class');
         return $obj->get_name_by_key($key);
     }
     
     /**
      * 获取商家版用户商品列表
      * @param type $user_id
      * @param type $data
      * @param type $bool
      * @param type $order_by
      * @param type $limit_str
      * @param type $select_field
      * @return type
      */
     public function api_user_goods_list($user_id,$data,$bool,$order_by,$limit_str,$select_field)
     {
         $task_goods_obj = POCO::singleton('pai_mall_goods_class');
         return $task_goods_obj->user_goods_list($user_id, $data, $bool, $order_by, $limit_str, $select_field);
     }
     
     /**
     * 组装商家版产品列表
     * @param type $goods_list
     * @return type
     */
    public function api_packing_user_goods_list($user_id,$data,$bool,$order_by,$limit_str,$select_field)
    {
        //获取原生的数据
        $goods_list = $this->api_user_goods_list($user_id, $data, $bool, $order_by, $limit_str, $select_field);
        
        //如果为空直接返回
        if(empty($goods_list))
        {
            return false;
        }
        
        //组装数据
        $service_list = array();
        foreach ($goods_list as $goods) 
        {
            $goods_id = $goods['goods_id'];
            $type_id = $goods['type_id'];
            $prices_list = unserialize($goods['prices_list']);
            $price_str = empty($prices_list) ? $goods['prices'] : (min($prices_list) . '-' . max($prices_list));
            $is_show = $goods['is_show'];
            $service_info = array(
                'goods_id' => $goods_id, // 商品ID
                'titles' => $goods['titles'], // 服务名称
                'type_id' => $type_id, // 服务分类
                'is_show' => $is_show, // 服务状态
                'images' => $goods['images'], // 图片展示
                'prices' => '￥' . $price_str,
            );
            $service_list[] = $service_info;
        }
        
        return $service_list;
    }
    
    /**
     * html 转 ubb 格式
     * 
     * @param string $str
     * @return string
     */
    public function api_ubb_encode($str) {
        if (empty($str)) {
            return FALSE;
        }
        $reg = array(
            '/\<a[^>]+href="mailto:(\S+)"[^>]*\>(.*?)<\/a\>/i', // Email
            '/\<a[^>]+href=\"([^\"]+)\"[^>]*\>(.*?)<\/a\>/i',
            '/\<img[^>]+src=\"([^\"]+)\"[^>]*\>/i',
            '/\<div[^>]+align=\"([^\"]+)\"[^>]*\>(.*?)<\/div\>/i',
            '/\<([\/]?)u\>/i',
            '/\<([\/]?)em\>/i',
            '/\<([\/]?)strong\>/i',
            '/\<([\/]?)b[^(a|o|>|r)]*\>/i',
            '/\<([\/]?)i\>/i',
            '/&amp;/i',
            '/&lt;/i',
            '/&gt;/i',
            '/&nbsp;/i',
            '/\s+/',
            '/&#160;/', // 特殊符号
            '/\<p[^>]*\>/i',
            '/\<br[^>]*\>/i',
            '/\<[^>]*?\>/i',
            '/\&#\d+;/',   // 特殊符号
        );
        $rpl = array(
            '[email=$1]$2[/email]',
            '[url=$1]$2[/url]',
            '[img]$1[/img]',
            '[align=$1]$2[/align]',
            '[$1u]',
            '[$1I]',
            '[$1b]',
            '[$1b]',
            '[$1i]',
            '&',
            '<',
            '>',
            ' ',
            ' ',
            ' ',
            "\r\n",
            "\r\n",
            '',
            '',
        );
        $str = preg_replace($reg, $rpl, $str);
        return trim($str);
    }
    
    /**
      * 获取商家版产品详情
      * @param type $goods_id
      * @param type $user_id
      * @return type
      */
     public function api_user_get_goods_info($goods_id, $user_id)
     {
         $task_goods_obj = POCO::singleton('pai_mall_goods_class');
         return $task_goods_obj->user_get_goods_info($goods_id,$user_id);
     }
    
    /**
     * 组装商家版产品详情
     * @param type $goods_result
     * @return type
     */
    public function api_packing_user_get_goods_info($goods_id, $user_id)
    {
        //获取原生的数据
        $goods_result = $this->api_user_get_goods_info($goods_id, $user_id);
        
        if($goods_result['result'] != 1)
        {
            return false;
        }
        
        //开始组装数据
        $goods = $goods_result['data'];
        $service = array();  // 服务内容
        $property_unit_config = pai_mall_load_config('property_unit');  // 获取属性对应单位
        // 商品详情
        foreach ($goods['system_data'] as $value) {
            $unit = $property_unit_config[$value['id']];
            $pval = $value['value'];
            if (!empty($value['child_data'])) {
                foreach ($value['child_data'] as $val) {
                    if ($pval == $val['key']) {
                        $pval = $val['name'];
                    }
                }
            }
            $service[] = array('title' => $value['name'] . ' :', 'value' => $pval . $unit);
        }
        $preview = array(); // 图片
        foreach ($goods['goods_data']['img'] as $value) {
            $img_url = $value['img_url'];
            $preview[] = array(
                'thumb' => yueyue_resize_act_img_url($img_url, '440'), // 缩略图
                'original' => yueyue_resize_act_img_url($img_url), // 原图
            );
        }
        $goods_user_id = $goods['goods_data']['user_id'];
        //$user_name = get_user_nickname_by_user_id($goods_user_id);  // 用户名
        //$user_avatar = get_user_icon($goods_user_id);  // 头像
        $seller_obj = POCO::singleton('pai_mall_seller_class');
        $user_result = $seller_obj->get_seller_info($goods_user_id, 2);  // 获取用户信息
        $profile = $user_result['seller_data']['profile'][0];
        $type_id_arr = explode(',', $profile['type_id']);  // 用户认证  31 模特 40 摄影师
        $user_avatar = $profile['avatar'];  // 头像
        $user_name = $profile['name'];  // 名称
        $profile_info = array();  // 人物信息
        foreach ($profile['default_data'] as $value) {
            $profile_info[$value['key']] = $value['value'];
        }

        $standard = array();  // 规格
        foreach ($goods['prices_data_list'] as $value) {
            $prices = floatval($value['value']);
            $standard[] = array(
                'id' => $value['id'],
                'name' => $value['name'],
                'value' => sprintf('%.2f', $prices),
                'original' => '',
                'num' => $value['num']
            );
            $min = ($prices < $min) ? $prices : (empty($min) ? $prices : $min);  // 最小价格
            $max = ($prices > $max) ? $prices : (empty($max) ? $prices : $max);   // 最大价格
        }
        // 价格范围
        $range = empty($max) ? '' : ( $min == $max ? $max : sprintf('%.2f', $min) . '-' . sprintf('%.2f', $max) );

        $score = intval($goods['goods_data']['total_overall_score']);   // 评价得分
        $contents = trim($goods['default_data']['content']['value']);
        $contents = stripos($contents, '&lt;') < 10 ? html_entity_decode($contents, ENT_COMPAT | ENT_HTML401, 'GB2312') : $contents;

        $goods_info = array(
            'goods_id' => $goods['goods_data']['goods_id'],
            // 顶部 轮播图片
            'preview' => $preview,
            'zoom' => 'yueseller://goto?user_id=' . $user_id . '&pid=1250026&type=inner_app',
            'title' => $goods['default_data']['titles']['value'],
            'prices' => '￥' . (empty($range) ? $goods['default_data']['prices']['value'] : $range), // 价格区间
            'original_prices' => '', // 原始价格
            // 规格
            'standard' => $standard,
            'promise' => $service, // 服务内容
            'user' => array(
                'user_id' => $goods_user_id,
                'name' => $user_name,
                'avatar' => $user_avatar,
            ),
            'business' => array(
                'merit' => array('title' => '评价', 'value' => strval($score > 10 ? 10 : ( $score < 0 ? 0 : $score))), // 综合评价
            ),
            'value'=>$goods['goods_data']['statistical']['bill_finish_num'],
            'profile_type' => $goods['goods_data']['type_id'], // 模特服务/摄影培训
            // 商品属性
            'property' => array(
                // 模特服务
                'model' => array(),
                // 摄影服务
                'photography' => array(),
            ),
            // 图文详情
            'detail' => array(
                'title' => $goods['default_data']['content']['name'],
                'value' => '[color=#333333]' . $this->api_ubb_encode($contents) . '[/color]',
            ),
            
        );

        if (in_array('40', $type_id_arr)) {   // 摄影师属性
            $goods_info['property']['photography'] = array(
                array('type' => 'label', 'title' => '擅长类型', 'value' => $profile_info['good_at']),
                array('type' => 'label', 'title' => '从业年限', 'value' => $profile_info['work_age'] . '年')
            );
        }
        if (in_array('31', $type_id_arr)) {   // 模特属性
            $profile_attr = array();  // 属性
            foreach ($profile['att_data'] as $value) {
                if ($value['value'] == '' || $value['value'] == NULL) {
                    // 去除 一些空值
                    continue;
                }
                $profile_attr[$value['key']] = $value['value'];
            }
            // 组装 模特属性
            $level_arr = array(
                1 => 'V1手机认证',
                2 => 'V2实名认证',
                3 => 'V3达人认证',
            );
            $goods_info['property']['model'] = array(
                'bwh' => array('type' => 'round', 'title' => '身材', 'value' => array(
                        'm_height' => $profile_attr['m_height'] . 'CM',
                        'm_weight' => $profile_attr['m_weight'] . 'KG',
                        'm_cups' => $profile_attr['m_cups'] . $profile_attr['m_cup'],
                        'm_bwh' => $profile_attr['m_bwh']
                    )
                ),
                'require' => array('type' => 'squared', 'title' => '信用等级要求', 'value' => $level_arr[$profile_attr['m_level']]),
            );
        }
        
        return $goods_info;
    }
    
    
    /**
      * 获取商家评价列表
      * @param type $user_id
      * @param type $bool
      * @param type $where
      * @param type $order_by
      * @param type $limit_str
      * @param type $select_field
      * @return type
      */
     public function api_get_seller_comment_list($user_id,$bool,$where,$order_by,$limit_str,$select_field)
     {
         $mall_comment_obj = POCO::singleton('pai_mall_comment_class');
         return  $mall_comment_obj->get_seller_comment_list($user_id, $bool, $where, $order_by, $limit_str, $select_field);
     }
     
    /**
     * 组装评价列表
     * @param type $comment_result
     * @return type
     */
    public function api_packing_comment_list($user_id,$bool,$where,$order_by,$limit_str,$select_field)
    {
        //获取原生的数据
        $comment_result = $this->api_get_seller_comment_list($user_id,$bool,$where,$order_by,$limit_str,$select_field);
        
        if(empty($comment_result))
        {
            return false;
        }
        
        //组装数据
        //$mall_order_obj = POCO::singleton('pai_mall_order_class');
        $comment_list = array();
        //$order_info_arr = array();
        foreach ($comment_result as $value) {
            $from_user_id = $value['from_user_id'];
            $name = get_user_nickname_by_user_id($from_user_id);  // 用户名
            $goods_id = $value['goods_id'];
            $is_anonymous = $value['is_anonymous'];  // 是否匿名评价 0为否 1为是
            $name = $is_anonymous == 1 ? (mb_substr($name, 0, 1, 'UTF8') . '***' . mb_substr($name, -1, 1, 'UTF8')) : $name;
            $order_id = $value['order_id'];
            $api_obj = POCO::singleton('pai_mall_api_class');
            $goods_info = $api_obj->api_user_get_goods_info($goods_id, $user_id);
            $title = $goods_info['goods_data']['titles'];
            $score = intval($value['overall_score']);
            $comment_list[] = array(
                'from_user_id' => $from_user_id,
                'avatar' => get_user_icon($from_user_id), // 头像
                'customer' => empty($name) ? '' : $name,
                'service_title' => ' ' . (empty($title) ? '暂无内容' : $title),
                'rating' => strval($score > 10 ? 10 : ( $score < 0 ? 0 : $score)), // 评分
                'comment' => $value['comment'], // 评论内容
                'add_time' => date('Y-m-d', $value['add_time']), // 评论时间
            );
            unset($goods_info);
        }
        
        return $comment_list;
        
    }
     
     /**
      * 用户改变商品状态
      * @param type $goods_id
      * @param type $status
      * @param type $user_id
      * @return type
      */
     public function api_user_change_goods_show_status($goods_id, $status, $user_id)
     {
         $mall_goods_obj = POCO::singleton('pai_mall_goods_class');   // 实例化对象
         return $mall_goods_obj->user_change_goods_show_status($goods_id, $status, $user_id);
     }
     
     
     /**
      * 获取共享文本
      * @param type $user_id
      */
     public function api_get_share_text($user_id)
     {
         $model_card_obj = POCO::singleton('pai_model_card_class');
         return $model_card_obj->get_share_text($user_id);
     }
     
     
     /**
      * 获取商家详情
      * @param type $goods_user_id
      * @param type $num
      * @return type
      */
     public function api_get_seller_info($user_id,$num)
     {
         $seller_obj = POCO::singleton('pai_mall_seller_class');
         return $seller_obj->get_seller_info($user_id, $num);
     }
     
     
     /**
      * 组装用户详情
      * @param type $user_result
      */
     public function api_packing_user_result($user_id,$num)
     {
         //获取原生的数据
        $user_result = $this->api_get_seller_info($user_id,$num);
        
        if(empty($user_result))
        {
            return false;
        }
         
        //组装数据
        //$user = $user_result['seller_data'];
        $profile = $user_result['seller_data']['profile'][0];
        $type_id_arr = explode(',', $profile['type_id']);  // 用户认证  31 模特 40 摄影师
        $profile_info = array();  // 简介
        foreach ($profile['default_data'] as $value) {
            $profile_info[$value['key']] = $value['value'];
        }
        $introduce = $profile_info['introduce'];  // 个人介绍
        $introduce = html_entity_decode($introduce, ENT_COMPAT | ENT_HTML401, 'GB2312');  // 转编译
        $introduce = mb_strimwidth(strip_tags($introduce), 0, 300, '...');  // 截取前150
        $introduce = trim($this->api_html_decode($introduce));   // html 残留标签 转编译

        $score = intval($profile['total_overall_score']);  // 综合评价
        $user_info = array(
            'user_id' => $profile['user_id'], // 用户ID
            'cover' => $profile['cover'], // 背景图
            'avatar' => $profile['avatar'], // 头像
            'name' => $profile['name'],
        //    'sex' => $profile['sex'],
            'introduce' => $introduce,
            'detail' => 'yueseller://goto?user_id=' . $user_id . '&pid=1250005&type=inner_app', // 资料详情
            'location' => get_poco_location_name_by_location_id($profile_info['location_id']),
            // 属性
            'property' => array(
                // 模特服务
                'model' => array(),
                // 摄影服务
                'photography' => array(),
            ),
            // 交易信息
            'business' => array(
                'merit' => array('title' => '综合评价', 'value' => strval($score > 10 ? 10 : ( $score < 1 ? 1 : $score))), // 综合评价
            ),
            'value'=>$user_result['seller_data']['bill_finish_num'], 
            'showtitle' => '拍摄风格',
            // 作品展示
            'showcase' => array(),
            // 分享
            'share' => array(),
        );
        
        if (in_array('40', $type_id_arr)) {   // 摄影师属性
            $user_info['property']['photography'] = array(
                array('type' => 'label', 'title' => '擅长类型', 'value' => $profile_info['good_at']),
                array('type' => 'label', 'title' => '从业年限', 'value' => $profile_info['work_age'] . '年')
            );
        }
        if (in_array('31', $type_id_arr)) {   // 模特属性
            $profile_attr = array();  // 属性
            foreach ($profile['att_data'] as $value) {
                if ($value['value'] == '' || $value['value'] == NULL) {
                    // 去除 一些空值
                    continue;
                }
                $profile_attr[$value['key']] = $value['value'];
            }
            // 组装 模特属性
            $level_arr = array(
                1 => 'V1手机认证',
                2 => 'V2实名认证',
                3 => 'V3达人认证',
            );
            $user_info['property']['model'] = array(
                'bwh' => array('type' => 'round', 'title' => '身材', 'value' => array(
                        'm_height' => $profile_attr['m_height'] . 'CM',
                        'm_weight' => $profile_attr['m_weight'] . 'KG',
                        'm_cups' => $profile_attr['m_cups'] . $profile_attr['m_cup'],
                        'm_bwh' => $profile_attr['m_bwh']
                    )
                ),
                'require' => array('type' => 'squared', 'title' => '信用等级要求', 'value' => $level_arr[$profile_attr['m_level']]),
            );
        }
        // 商品列表

        // status状态 0未审核,1通过,2未通过,3删除;show上/下架 1上架,2下架;type_id商品类型,keyword搜索关键字
        $data = array(
            'status' => 1,
            'show' => 1,
            'type_id' => 0,
            'keyword' => '',
        );
        $goods_list = $this->api_user_goods_list($user_id, $data, false, 'goods_id DESC', '0,5', '*');
        // 获取 属性
        //$attribute_obj = POCO::singleton('pai_mall_goods_type_attribute_class');
        //$attribute_list = $attribute_obj->get_type_attribute_cate(0);
        //$attribute_arr = array();
        //foreach ($attribute_list as $value) {
        //    $attribute_arr[$value['id']] = $value['name'];
        //}
        foreach ($goods_list as $value) {
            $prices_list = $value['prices_list'];
            $user_info['showcase'][] = array(
                'goods_id' => $value['goods_id'],
                'title' => $value['titles'],
                'prices' => '￥' . $value['prices'] . $value['unit'], // '/' . (empty($prices_list) ? $value['unit'] : $attribute_arr[$value['goods_id']]),
                'pic' => $value['images'],
      //        'content' => $value['content'],
            );
        }
        
        return $user_info;
        
     }
     
    /**
    * html实例 转编译
    * 
    * @param string $str
    * @return string 
    */
    public function api_html_decode($str) {
        $reg = array(
            '&lt;',
            '&gt;',
            '&quot;',
            '&nbsp;',
        );
        $rpl = array(
            '<',
            '>',
            '"',
            ' ',
        );
        $str = str_replace($reg, $rpl, $str);
        return trim($str);
    }
    
    /**
     * 消费者版取商品详情
     * @param type $goods_id
     * @return type
     */
    public function api_get_goods_info_by_goods_id($goods_id)
    {
        $task_goods_obj = POCO::singleton('pai_mall_goods_class');
        return $task_goods_obj->get_goods_info_by_goods_id($goods_id);
    }
    
    /**
     * 组装消费者版商品详情
     * @param type $goods_result
     * @return 
     */
    public function api_packing_get_goods_info_by_goods_id($goods_id)
    {
        //获取原生的数据
        $goods_result = $this->api_get_goods_info_by_goods_id($goods_id);
        
        if($goods_result['result'] != 1)
        {
            return false;
        }
        
        //组装数据
        $goods = $goods_result['data'];
        $service = array();  // 服务内容
        $property_unit_config = pai_mall_load_config('property_unit');  // 获取属性对应单位
        // 商品详情
        foreach ($goods['system_data'] as $value) {
            $unit = $property_unit_config[$value['id']];
            $pval = $value['value'];
            if (!empty($value['child_data'])) {
                foreach ($value['child_data'] as $val) {
                    if ($pval == $val['key']) {
                        $pval = $val['name'];
                    }
                }
            }
            $service[] = array('title' => $value['name'] . ' :', 'value' => $pval . $unit);
        }
        $preview = array(); // 图片
        foreach ($goods['goods_data']['img'] as $value) {
            $img_url = $value['img_url'];
            $preview[] = array(
                'thumb' => yueyue_resize_act_img_url($img_url, '440'), // 缩略图
                'original' => yueyue_resize_act_img_url($img_url), // 原图
            );
        }
        $goods_user_id = $goods['goods_data']['user_id'];
        //$user_name = get_user_nickname_by_user_id($goods_user_id);  // 用户名
        //$user_avatar = get_user_icon($goods_user_id);  // 头像

        $seller_obj = POCO::singleton('pai_mall_seller_class');
        $user_result = $seller_obj->get_seller_info($goods_user_id, 2);  // 获取用户信息
        $profile = $user_result['seller_data']['profile'][0];
        $type_id_arr = explode(',', $profile['type_id']);  // 用户认证  31 模特 40 摄影师
        $user_avatar = $profile['avatar'];  // 头像
        $user_name = $profile['name'];  // 名称
        $profile_info = array();  // 人物信息
        foreach ($profile['default_data'] as $value) {
            $profile_info[$value['key']] = $value['value'];
        }

        $standard = array();  // 规格
        foreach ($goods['prices_data_list'] as $value) {
            $prices = floatval($value['value']);
            $standard[] = array(
                'id' => $value['id'],
                'name' => $value['name'],
                'value' => sprintf('%.2f', $prices),
                'original' => '',
                'num' => $value['num']
            );
            $min = ($prices < $min) ? $prices : (empty($min) ? $prices : $min);  // 最小价格
            $max = ($prices > $max) ? $prices : (empty($max) ? $prices : $max);   // 最大价格
        }
        // 价格范围
        $range = empty($max) ? '' : ( $min == $max ? $max : sprintf('%.2f', $min) . '-' . sprintf('%.2f', $max) );

        $score = intval($goods['goods_data']['total_overall_score']);   // 评价得分
        $contents = trim($goods['default_data']['content']['value']);
        $contents = stripos($contents, '&lt;') < 10 ? html_entity_decode($contents, ENT_COMPAT | ENT_HTML401, 'GB2312') : $contents;

        $order_url = ORDER_LINK_HEAD . '/order/confirm.php?goods_id=' . $goods_id . '&type_id=' . $goods['goods_data']['type_id'];

        $goods_info = array(
            'goods_id' => $goods['goods_data']['goods_id'],
            // 顶部 轮播图片
            'preview' => $preview,
            'zoom' => 'yueyue://goto?user_id=' . $user_id . '&pid=1250026&type=inner_app', // 点击放大
            'title' => $goods['default_data']['titles']['value'],
            'prices' => '￥' . (empty($range) ? $goods['default_data']['prices']['value'] : $range), // 价格区间
            'original_prices' => '￥1000-2000', // 原始价格
            // 规格
            'standard' => $standard,
            'promise' => $service, // 服务内容
            'user' => array(
                'user_id' => $goods_user_id,
                'name' => $user_name,
                'avatar' => $user_avatar,
            ),
            'business' => array(
                'merit' => array('title' => '评价', 'value' => strval($score > 10 ? 10 : ( $score < 0 ? 0 : $score))), // 综合评价
                
                'value' =>$goods['goods_data']['statistical']['bill_finish_num'],
                'goods_id' => $goods['goods_data']['goods_id'],
            ),
            'profile_type' => $goods['goods_data']['type_id'], // 模特服务/摄影培训
            // 商品属性
            'property' => array(
                // 模特服务
                'model' => array(),
                // 摄影服务
                'photography' => array(),
            ),
            // 图文详情
            'detail' => array(
                'title' => $goods['default_data']['content']['name'],
                'value' => '[color=#333333]' . $this->api_ubb_encode($contents) . '[/color]',
            ),
            
            'order_url'=>  urlencode($order_url),
            'user_name'=>$user_name,
        );

        if (in_array('40', $type_id_arr)) {   // 摄影师属性
            $goods_info['property']['photography'] = array(
                array('type' => 'label', 'title' => '擅长类型', 'value' => $profile_info['good_at']),
                array('type' => 'label', 'title' => '从业年限', 'value' => $profile_info['work_age'] . '年')
            );
        }
        if (in_array('31', $type_id_arr)) {   // 模特属性
            $profile_attr = array();  // 属性
            foreach ($profile['att_data'] as $value) {
                if ($value['value'] == '' || $value['value'] == NULL) {
                    // 去除 一些空值
                    continue;
                }
                $profile_attr[$value['key']] = $value['value'];
            }
            // 组装 模特属性
            $level_arr = array(
                1 => 'V1手机认证',
                2 => 'V2实名认证',
                3 => 'V3达人认证',
            );
            $goods_info['property']['model'] = array(
                'bwh' => array('type' => 'round', 'title' => '身材', 'value' => array(
                        'm_height' => $profile_attr['m_height'] . 'CM',
                        'm_weight' => $profile_attr['m_weight'] . 'KG',
                        'm_cups' => $profile_attr['m_cups'] . $profile_attr['m_cup'],
                        'm_bwh' => $profile_attr['m_bwh']
                    )
                ),
                'require' => array('type' => 'squared', 'title' => '信用等级要求', 'value' => $level_arr[$profile_attr['m_level']]),
            );
        }


        return $goods_info;

                
    }
    
    /**
     * 获取商品列表页
     * @param type $data
     * @param type $bool
     * @param type $order_by
     * @param type $limit_str
     * @return type
     */
    public function api_search_goods_list($data, $bool, $order_by, $limit_str)
    {
        $task_goods_obj = POCO::singleton('pai_mall_goods_class');
        return  $task_goods_obj->search_goods_list($data, $bool, $order_by, $limit_str);
    }
    
    /**
     * 商品列表页数据组装
     * @param type $good_result
     * @return string
     */
    public function api_packing_search_goods_list($data, $bool, $order_by, $limit_str,$user_id)
    {
        //获取原生的数据
        $goods_result = $this->api_search_goods_list($data, $bool, $order_by, $limit_str);
        
        if( empty($goods_result) )
        {
            return false;
        }
        
        //组装数据
        $goods = array();
        foreach ($goods_result as $value) {
            $goods_id = $value['goods_id'];
            $score = $value['total_overall_score'];  // 评价
            $goods[] = array(
                'goods_id' => $goods_id,
                'seller' => '测试测试', // get_user_nickname_by_user_id($value['seller_id']),
                'titles' => $value['titles'],
                'images' => $value['images'],
                // 综合评价
                'merit' => array(
                    'title' => '评价：',
                    'value' => strval($score > 10 ? 10 : ( $score < 0 ? 0 : $score))
                ),
                'certification' => array(
                    'color' => 'green',
                    'value' => '约约认证',
                ),
            );
        }

        return $goods;
    }
    
    /**
     * 
     * 获取买家的评论列表
     * @param type $user_id
     * @param type $bool
     * @param type $where_str
     * @param type $order_by
     * @param type $limit_str
     * @param type $select_field
     * @return type
     */
    public function api_get_buyer_comment_list($user_id,$bool,$where_str,$order_by,$limit_str,$select_field)
    {
        $mall_comment_obj = POCO::singleton('pai_mall_comment_class');
        return $mall_comment_obj->get_buyer_comment_list($user_id, $bool,$where_str,$order_by, $limit_str,$select_field);
    }
    
    /**
     * 组装买家的评论列表
     * @param type $user_id
     * @param type $bool
     * @param type $where_str
     * @param type $order_by
     * @param type $limit_str
     * @param type $select_field
     * @return boolean
     */
    public function api_packing_get_buyer_comment_list($user_id,$bool,$where_str,$order_by,$limit_str,$select_field)
    {
        $comment_result = $this->api_get_buyer_comment_list($user_id, $bool, $where_str, $order_by, $limit_str, $select_field);
        if( empty($comment_result) )
        {
            return false;
        }
        //$mall_order_obj = POCO::singleton('pai_mall_order_class');
        $comment_list = array();
        //$order_info_arr = array();
        foreach ($comment_result as $value) {
            $from_user_id = $value['from_user_id'];
            $name = get_user_nickname_by_user_id($from_user_id);  // 用户名
            $goods_id = $value['goods_id'];
            $is_anonymous = $value['is_anonymous'];  // 是否匿名评价 0为否 1为是
            $name = $is_anonymous == 1 ? (mb_substr($name, 0, 1, 'UTF8') . '***' . mb_substr($name, -1, 1, 'UTF8')) : $name;
            $order_id = $value['order_id'];
            $goods_info = $task_goods_obj->get_goods_info_by_goods_id($goods_id);
            $title = $goods_info['goods_data']['titles'];
            $score = intval($value['overall_score']);
            $comment_list[] = array(
                'from_user_id' => $from_user_id,
                'avatar' => get_user_icon($from_user_id), // 头像
                'customer' => empty($name) ? '' : $name,
                'service_title' => ' ' . (empty($title) ? '暂无内容' : $title),
                'rating' => strval($score > 10 ? 10 : ( $score < 0 ? 0 : $score)), // 评分
                'comment' => $value['comment'], // 评论内容
                'add_time' => date('Y-m-d', $value['add_time']), // 评论时间
            );
            unset($goods_info);
        }
        
        return $comment_list;

    }
    
    /**
     * 商家获取商品类型
     * @param type $user_id
     * @return type
     */
    public function api_user_goods_type_list($user_id)
    {
        $task_goods_obj = POCO::singleton('pai_mall_goods_class');
        return $task_goods_obj->user_goods_type_list($user_id);  // 商家获取商品类型列表
    }
    
    
    /**
     * 拿商家的订单列表
     * @param type $user_id
     * @param type $type_id
     * @param type $status
     * @param type $bool
     * @param type $order_by
     * @param type $limit_str
     * @param type $select_field
     * @return type
     */
    public function  api_get_order_list_for_seller($user_id, $type_id, $status, $bool, $order_by, $limit_str,$select_field)
    {
        $mall_order_obj = POCO::singleton('pai_mall_order_class');   // 实例化商家交易对象
        return  $mall_order_obj->get_order_list_for_seller($user_id, $type_id, $status, $bool, $order_by, $limit_str, $select_field);
    }
    
    /**
     * 组装商家订单列表
     * @param type $user_id
     * @param type $type_id
     * @param type $status
     * @param type $bool
     * @param type $order_by
     * @param type $limit_str
     * @param type $select_field
     * @return boolean|int
     */
    public function api_packing_get_order_list_for_seller($user_id, $type_id, $status, $bool, $order_by, $limit_str,$select_field)
    {
        $trade_data = $this->api_get_order_list_for_seller($user_id, $type_id, $status, $bool, $order_by, $limit_str, $select_field);
        if( empty($trade_data) )
        {
            return false;
        }
        
        //$task_goods_obj = POCO::singleton('pai_mall_goods_class');
        //$type_arr = $task_goods_obj->user_goods_type_list($user_id);  // 商家获取商品类型列表
        //$type_arr = array(12 => '影棚租赁', 2 => '生活服务', 3 => '化妆服务', 5 => '摄影培训', 31 => '模特服务', 40 => '摄影服务',);
        $trade_list = array();
        foreach ($trade_data as $value) {
            $order_sn = $value['order_sn'];
            $status = $value['status'];
            $is_seller_comment = $value['is_seller_comment'];  // 是否评价
            $status_str = $value['status_str'];
            if ($status == 8 && $is_seller_comment == 0) {  // 未评价
                $status = 10;
                $status_str = '待评价';
            }
            $buyer_user_id = $value['buyer_user_id'];
            $trade_info = array(
                'order_sn' => $order_sn,
                'type_id' => $value['type_id'], // 商品品类
        //        'type_name' => $type_arr[$value['type_id']]['name'],
                'title' => get_user_nickname_by_user_id($buyer_user_id), // 标题, 可能是消费者
        //        'title' => $value['detail_list'][0]['goods_name'], // 标题, 可能是消费者
                'desc' => $value['detail_list'][0]['goods_name'], // 描述, 可能是商品名
        //        'desc' => empty($value['description']) ? $value['detail_list'][0]['goods_name'] : $value['description'], // 描述, 消费详情
                'status' => $status, // 状态
                'status_str' => $status_str, // 状态名称
                'cost' => $value['total_amount'], // 金额
                'goods_id' => $value['detail_list'][0]['goods_id'], // 商品ID
                'thumb' => $value['detail_list'][0]['goods_images'], // 预览图
                'action' => $this->btn_action($value['status'], $value['is_seller_comment']),
            );
            $trade_list[] = $trade_info;
        }
        
        return $trade_list;
        
    }
    
    /**
     * 接受订单
     * @param type $order_sn
     * @param type $user_id
     * @return type
     */
    public function api_accept_order($order_sn, $user_id)
    {
        $mall_order_obj = POCO::singleton('pai_mall_order_class'); 
        return $mall_order_obj->accept_order($order_sn, $user_id);
    }
    
    /**
     * 取消订单
     * @param type $order_sn
     * @param type $user_id
     * @return type
     */
    public function api_refuse_order($order_sn, $user_id)
    {
        $mall_order_obj = POCO::singleton('pai_mall_order_class'); 
        return $mall_order_obj->refuse_order($order_sn, $user_id);
    }
    
    /**
     * 签到订单
     * @param type $code_sn
     * @param type $user_id
     * @return type
     */
    public function api_sign_order($code_sn, $user_id)
    {
        $mall_order_obj = POCO::singleton('pai_mall_order_class'); 
        return $mall_order_obj->sign_order($code_sn, $user_id);
    }
    
    /**
     * 取消订单
     * @param type $order_sn
     * @param type $user_id
     * @return type
     */
    public function api_close_order_for_seller($order_sn, $user_id)
    {
        $mall_order_obj = POCO::singleton('pai_mall_order_class'); 
        return $mall_order_obj->close_order_for_seller($order_sn, $user_id);
    }
    
    /**
     * 取消订单
     * @param type $order_sn
     * @param type $user_id
     * @return type
     */
    public function api_del_order_for_seller($order_sn, $user_id)
    {
        $mall_order_obj = POCO::singleton('pai_mall_order_class'); 
        return $mall_order_obj->del_order_for_seller($order_sn, $user_id);
    }
    
    /**
     * 获取商家订单number
     * @param type $user_id
     * @return type
     */
    public function api_get_order_number_for_seller($user_id)
    {
        $mall_order_obj = POCO::singleton('pai_mall_order_class'); 
        return $mall_order_obj->get_order_number_for_seller($user_id);
    }
    
    /**
     * 获取买家订单列表
     * @param type $user_id
     * @param type $type_id
     * @param type $status
     * @param type $bool
     * @param type $order_by
     * @param type $limit_str
     * @param type $select_field
     * @return type
     */
    public function api_get_order_list_for_buyer($user_id, $type_id, $status, $bool, $order_by, $limit_str, $select_field)
    {
        $mall_order_obj = POCO::singleton('pai_mall_order_class');   // 实例化商家交易对象
        return $mall_order_obj->get_order_list_for_buyer($user_id, $type_id, $status,$bool, $order_by, $limit_str, $select_field);
    }
    
    
    /**
     * 获取订单详情
     * @param type $order_sn
     * @return type
     */
    public function api_get_order_full_info($order_sn)
    {
        $mall_order_obj = POCO::singleton('pai_mall_order_class');   // 实例化商家交易对象
        return $mall_order_obj->get_order_full_info($order_sn);
    }
    
    /**
     * 交易详情组装 
     * @param type $order_sn
     * @return boolean
     */
    public function api_packing_get_order_full_info($order_sn)
    {
        $order_info = $this->api_get_order_full_info($order_sn);
        
        if( empty($order_info) )
        {
            return false;
        }
        
        $status = $order_info['status'];
        $is_seller_comment = $order_info['is_seller_comment'];  // 是否评价
        $status_str = $order_info['status_str'];
        if ($status == 8 && $is_seller_comment == 0) {  // 未评价
            $status = 10;
            $status_str = '待评价';
        }
        // 组装交易详情数据
        $property_data = $this->property_data($order_info['detail_list'][0]);
        $trade_data = array(
            'order_sn' => $order_info['order_sn'], // 订单编号
            'status' => $status, // 状态
            'status_str' => $status_str, // 状态
        //    'expire_time' => $order_info['expire_time'], // 订单过期时间(自行计算有效时间)
            'expire' => $order_info['expire_str'], // 订单过期时间
            // 买家
            'customer' => array(
                'user_id' => $order_info['buyer_user_id'],
                'name' => '买家：',
                'value' => get_user_nickname_by_user_id($order_info['buyer_user_id']),
                'buyer_user_id'=>$order_info['buyer_user_id'],
            ),
            // 金额
            'cost' => array(
                'name' => '总价：',
                'value' => '￥' . $order_info['total_amount'],
            ),
            'goods' => array(
                'id' => $order_info['detail_list'][0]['goods_id'],
                'name' => $order_info['detail_list'][0]['goods_name'],
                'image' => $order_info['detail_list'][0]['goods_images'],
                'request' => 'yueseller://goto?user_id=' . $user_id . '&goods_id=' . $order_info['detail_list'][0]['goods_id'] . '&pid=1250007&type=inner_app',
            ),
            'trade' => array(
                array('title' => '订单编号：', 'value' => $order_info['order_sn']),
                array('title' => '下单时间：', 'value' => date('Y-m-d H:i', $order_info['add_time'])),
                array('title' => '支付时间：', 'value' => $order_info['is_pay'] == '1' ? date('Y-m-d H:i', $order_info['pay_time']) : ''),
                array('title' => '客服电话：', 'value' => '4000-82-9003'),
            ),
            'detail' => $property_data['detail'],
            'property' => $property_data['property'],
            'action' => $this->btn_action($order_info['status'], $is_seller_comment),
        );
        
        return $trade_data;
    }
    
    /**
     * 属性数据
     * @param type $detail
     * @return type
     */
    public function property_data($detail)
    {
        $type_id = $detail['type_id'];   // 服务(商品)类型
        $spec = explode(':', $detail['prices_spec']);
        $limit = array(
            'title' => count($spec) > 1 ? trim($spec[0]) . '：' : '时长：',
            'value' => count($spec) > 1 ? trim($spec[1]) : $detail['prices_spec']
        );
        $account = array('title' => '到场拍摄人数：', 'value' => $detail['service_people'] . '人');
        $standard = array('title' => '规格：', 'value' => count($spec) > 1 ? trim($spec[1]) : $detail['prices_spec']);
        $time = array('title' => '服务时间：', 'value' => date('Y-m-d H:i', $detail['service_time']));
        $addr = array('title' => '地点：', 'value' => $detail['service_address']);
        $num = array('title' => '购买数量：', 'value' => $detail['quantity']);
        switch ($type_id) {
            case '31':  // 模特服务
                $detail = array($limit, $account);
                $property = array($time, $addr);
                break;
            case '5':  // 培训
                $detail = array();
                $property = array($num);
                break;
            case '12':  // 影棚
                $detail = array($standard,);
                $property = array($time, $num);
                break;
            case '3':  // 化妆
                $detail = array($time, $addr);
                $property = array($num);
                break;
            case '40':  // 摄影
                $detail = array($time, $addr);
                $property = array($num);
                break;
            default:  // 未定义
                $detail = array();
                $property = array();
                break;
        }
        return array('detail' => $detail, 'property' => $property);
    }
    
    /**
     * 按纽操作
     * @param type $status
     * @param type $is_seller_comment
     * @return type
     */
    public function btn_action($status, $is_seller_comment) {
        
        if ($is_seller_comment == 1) {  // 商家已评论
            return array();
        }
        // 按钮文案
        $action_arr = array(
            0 => '关闭订单.close',
            1 => '拒绝订单.refuse|接受订单.accept',
            2 => '扫码签到.sign',
    //        2 => '取消交易并退款.close|扫码签到.sign',
    //        7 => '删除订单.delete',
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
     * 添加买家评论
     * @param type $data
     * @return type
     */
    public function api_add_buyer_comment($data)
    {
        $mall_comment_obj = POCO::singleton('pai_mall_comment_class');
        return $mall_comment_obj->add_buyer_comment($data);  // 评价买家
    }
    
    /**
     * 添加商家评论
     * @param type $data
     * @return type
     */
    public function api_add_seller_comment($data)
    {
        $mall_comment_obj = POCO::singleton('pai_mall_comment_class');
        return $mall_comment_obj->add_seller_comment($data); 
    }
    
    /**
     * 提交订单 
     * @param type $user_id
     * @param type $detail_list
     * @param type $more_info
     * @return type
     */
    public function api_submit_order($user_id, $detail_list, $more_info)
    {
        $mall_order_obj = POCO::singleton('pai_mall_order_class');   // 实例化商家交易对象
        return $mall_order_obj->submit_order($user_id, $detail_list, $more_info);
    }
    
    /**
     * 获取买家订单number
     * @param type $user_id
     * @return type
     */
    public function api_get_order_number_for_buyer($user_id)
    {
        $mall_order_obj = POCO::singleton('pai_mall_order_class');
        return $mall_order_obj->get_order_number_for_buyer($user_id);
    }
    
    /**
     * 获取商品场次的大小价
     * @param type $goods_id
     * @param type $type_id
     * @return boolean
     */
    public function get_goods_id_screenings_price_max_and_min($goods_id,$type_id)
    {
        $goods_id = (int)$goods_id;
        $type_id = (int)$type_id;
        if( ! $goods_id || ! $type_id )
        {
            return false;
        }
        $goods_obj = POCO::singleton('pai_mall_goods_class');
        $goods_info = $goods_obj->get_goods_info($goods_id);
        $price_ary = array();
        $min_price = $max_price = 0;
        $has_join = 0;
        $total_num = 0;
        $activity_name = '';
        $screenings_name = '';
        $time_s = 0;
        $time_e = 0;
        if( ! empty($goods_info['goods_data']['prices_de']))
        {
            foreach($goods_info['goods_data']['prices_de'] as $k => $v)
            {
                if($v['type_id'] == $type_id)
                {
                    foreach($v['prices_list_data'] as $pk => $pv)
                    {
                        $price_ary[] = $pv['prices'];
                    }
                    $has_join= $v['stock_num_total'] - $v['stock_num'];
                    if($has_join < 0)
                    {
                        $has_join = 0;
                    }
                    $total_num = $v['stock_num_total'];
                    $activity_name = $goods_info['goods_data']['titles'];
                    $screenings_name = $v['name'];
                    $time_s = $v['time_s'];
                    $time_e = $v['time_e'];
                    
                    break;
                }
            }
        }
        
        $max_price = max(array_filter($price_ary));
        $min_price = min(array_filter($price_ary));
        //只有一个价的时候
        if(count($price_ary) == 1)
        {
            $min_price = 0;
        }
        
        return array(
            'max_price'=>$max_price,//最大价
            'min_price'=>$min_price,//最小价
            'has_join'=>$has_join,//已经参与人数
            'total_num'=>$total_num,//总库存
            'activity_name'=>$activity_name,//活动名称
            'screenings_name'=>$screenings_name,//场次名称
            'time_s'=>$time_s,
            'time_e'=>$time_e,
        );
    }
    
    /**
     * 获取活动情况
     * @param type $goods_id
     * @return boolean|int
     */
    public function get_goods_id_activity_info($goods_id)
    {
        $goods_id = (int)$goods_id;
        if( ! $goods_id )
        {
            return false;
        }
        $goods_obj = POCO::singleton('pai_mall_goods_class');
        $goods_info = $goods_obj->get_goods_info($goods_id);
        
        $new_data = array();
        $new_titles = '';
        if( ! empty($goods_info) )
        {
            $new_data = $goods_obj->get_mall_goods_check($goods_info['goods_data']['goods_id']);
            if( ! empty($new_data) )
            {
                $new_titles = $new_data['default_data']['titles'];
            }
        }
        
        //dump($goods_info);
        //总共多少场，在进行有几场，有多人参与，价钱低到高
        
        $total_show = $ing_show = $has_join_num = $min_price = $max_price = $ing_show_has_person = $is_have_end = $time_s_min = $time_e_max =  0;
        $bill_pay_num = 0;
        $prices_list = $price_arys = $time_e = $time_s = array();
        
        if( ! empty($goods_info['goods_data']['prices_de']))
        {
            $bill_pay_num = (int)$goods_info['goods_data']['statistical']['bill_pay_num'];
            $total_show = count($goods_info['goods_data']['prices_de']);
            
            foreach($goods_info['goods_data']['prices_de'] as $k => $v)
            {
                $time_s_ary[] = $v['time_s'];
                $time_e_ary[] = $v['time_e'];
                $now_time = time();
                if( $now_time < $v['time_e'] )
                {
                    $ing_show++;
                    $has_join_num = (int)$v['stock_num_total'] - (int)$v['stock_num'];
                    if($has_join_num > 0)
                    {
                        $ing_show_has_person = true;
                    }
                }
                if($now_time > $v['time_e'])
                {
                    $is_have_end = 1;
                }
                
                $prices_list = unserialize($v['prices_list']);
                
                if( ! empty($prices_list) )
                {
                    foreach($prices_list as $pk => $vk)
                    {
                        $price_arys[] = $vk['prices'];
                    }
                }
            }
        }
        if( ! empty($price_arys) )
        {
            $min_price = min(array_filter($price_arys));
            $max_price = max(array_filter($price_arys));
        }
        if( ! empty($goods_info['goods_data']) )
        {
            $has_join_num = (int)$goods_info['goods_data']['stock_num_total'] - (int)$goods_info['goods_data']['stock_num'];
            if($has_join_num < 0 )
            {
                $has_join_num = 0;
            }
        }
        
        if( ! empty($time_s_ary) )
        {
            $time_s_min = min(array_filter($time_s_ary));
        }
        if( ! empty($time_e_ary) )
        {
            $time_e_max = max(array_filter($time_e_ary));
        }
        
        $rs = array(
            'total_show'=>$total_show, //总场次
            'ing_show'=>$ing_show,//正在进行中
            'has_join_num'=>$has_join_num,//已经参加人数
            'min_price'=>$min_price,//最小价
            'max_price'=>$max_price,//最大价
            'ing_show_has_person'=>$ing_show_has_person, //进行中是否有人参与
            'is_have_end'=>$is_have_end,//是否有场次结束
            'f_time'=>$time_s_min,//场次开始时间最小
            'e_time'=>$time_e_max,//场次结束时间最大
            'bill_pay_num'=>$bill_pay_num,//已经参与人数
            'edit_status'=>(int)$goods_info['goods_data']['edit_status'],//活动的编辑状态
            'new_titles'=>$new_titles,//活动新标题
        );
        
        
        return $rs;
        
    }
    
    
    /**
     * 组装商品列表前台的数据
     * @param type $list
     * @return type
     */
    public function goods_data_for_front_packing($list)
    {
        $mall_seller_obj 	= POCO::singleton('pai_mall_seller_class');
		$task_goods_obj 	= POCO::singleton('pai_mall_goods_class');
        $default_cover 	= $mall_seller_obj->_seller_cover; 
        
        if( ! empty($list['data']) )
        {
            foreach($list['data'] as $k => &$v)
            {
				//print_r($v);
                $name = get_seller_nickname_by_user_id($v['user_id']);
                $cover = empty($v['images']) ? $default_cover : $v['images'];
                $price_str = sprintf('%.2f', $v['prices']);
                $prices_list = unserialize($v['prices_list']);
                if (!empty($prices_list))
                {
                    $min = 0;
                    foreach ($prices_list as $pv) {
                        $pv = intval($pv);
                        if ($pv <= 0) {
                            continue;
                        }
                        $min = ($min > 0 && $min < $pv) ? $min : $pv;
                    }
                    if ($min > 0) {
                        $price_str = sprintf('%.2f', $min) . '元 起';
                    }
                }
                
                if($v['review_times'])
                {
                    $score = sprintf('%.1f', ceil($v['total_overall_score'] / $v['review_times'] * 2) / 2);
                }
                else
                {
                    $score = "5.0";
                }
                $buy_num = $v['bill_pay_num'];
                
                $v['seller'] = $name ? $name : '商家';
				$v['titles'] = '[' . $task_goods_obj->get_goods_typename_for_type_id($v[type_id]) .  ']' . preg_replace('/&#\d+;/', '', $v['titles']);
				$v['images'] =  yueyue_resize_act_img_url($cover, '640');
				$v['link']  =  'yueyue://goto?goods_id=' . $v['goods_id'] . '&pid=1220102&type=inner_app';
				$v['prices'] = '￥' . $price_str;
				$v['buy_num'] = $buy_num > 0 ? '已有' . $buy_num . '人购买' : $name;
				$v['step'] = $score . ' 分';
				//$v['bill_finish_num'] = '已售:' . ($v['old_bill_finish_num'] + $v['bill_pay_num']);
                
                //活动时特殊处理
                if($v['type_id'] == 42)
                {
                    $activity_info = $this->get_goods_id_activity_info($v['goods_id']);
                    $v['bill_finish_num'] = '已参与:'.$activity_info['has_join_num'];
                }else
                {
                    $v['bill_finish_num'] = '已售:' . $buy_num;
                }
				
				$v['seller_img'] =  get_seller_user_icon($v['user_id']);
                

            }
        }
        
        return $list;
    }
    
    /**
     * 组装商家列表前台的数据
     * @param type $list
     * @return type
     */
    public function seller_data_for_front_packing($list)
    {
        $mall_seller_obj 	= POCO::singleton('pai_mall_seller_class');
		$task_goods_obj 	= POCO::singleton('pai_mall_goods_class');
        $default_cover 	= $mall_seller_obj->_seller_cover; 
        
        if( ! empty($list['data']) )
        {
            foreach($list['data'] as $k => &$v)
            {
                // 搜索商家
				$search_data = array(
//                'user_id' => $value['user_id'],
					'keywords' => $v['user_id'],
				);
				$result_list = $mall_seller_obj->user_search_seller_list($search_data, '0,1');

				$search_result = $result_list['data'][0];
				if (empty($search_result)) {
					continue;
				}
                $seller_id = $search_result['user_id'];
                $name = get_seller_nickname_by_user_id($seller_id);
                $buy_num = $v['bill_finish_num']+$v['old_bill_finish_num']; // 购买人数
                $cover = empty($search_result['cover']) ? $default_cover : $search_result['cover'];
                
                $v['goods_id'] =  0;
                $v['seller_user_id'] = $seller_id;
                $v['seller'] = empty($name) ? '商家' : $name; // 暂时作为商家名称
                $v['titles'] =  $buy_num > 0 ? '已售:' . $buy_num  : '已售:' . $buy_num; // preg_replace('/&#\d+;/', '', $search_result['seller_introduce']),
                $v['images'] = yueyue_resize_act_img_url($cover, '640');
                $v['avatar'] = get_seller_user_icon($seller_id, $size = 86, $force_reflesh=false);
                $v['link'] =  'yueyue://goto?seller_user_id=' . $seller_id . '&pid=1220103&type=inner_app'; // 商家首页
                $v['prices'] =  empty($name) ? '商家' : $name; // 暂时作为商家名称
                $v['buy_num']  =  '提供' . $search_result['onsale_num'] . '个服务';
                $v['step'] = '5分';
            }
        }
        return $list;	
                            
    }
    
    /**
     * 获取活动的商品id与场次id
     * @param type $time_s
     * @param type $time_e
     * @return boolean
     */
    public function get_goods_id_and_type_id_by_time($time_s,$time_e)
    {
        if(empty($time_s) || empty($time_e) )
        {
            return false;
        }
        $sql = "select p.goods_id,p.type_id,g.titles as goods_name,p.name as type_id_name,g.user_id "
                . "from mall_db.mall_goods_prices_tbl as p left join mall_db.mall_goods_tbl as g on g.goods_id=p.goods_id "
                . "where g.type_id='42' and p.time_e >='{$time_s}' and p.time_e <= '{$time_e}'";
        return db_simple_getdata ( $sql, false, 101 );
        
    }
     
     
     
     
     
     
     
     
     
     
}
