<?php
class pai_cms_parse_class extends POCO_TDG
{
	
	private $cms_obj;
    private $pai_user_obj;
    private $model_style_obj;
    private $pai_pic_obj;
    private $model_card_obj;
    private $cameraman_card_obj;
    private $pic_score_obj;
    
    private $wifi_mobile_card_url;
    private $mobile_card_url;
    private $wifi_cameraman_card_url;
    private $cameraman_card_url; 
    
    private $return_data_list;
    private $tmp_data_list;
	private $come_from;
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_search_bad_list_tbl' );
        
        include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
        $this->cms_obj                  = new cms_system_class();        
        $this->pai_user_obj             = POCO::singleton ( 'pai_user_class' );
        $this->model_style_obj          = POCO::singleton ( 'pai_model_style_v2_class' );
        $this->pai_pic_obj              = POCO::singleton ( 'pai_pic_class' );
        $this->model_card_obj           = POCO::singleton ( 'pai_model_card_class' );
        $this->cameraman_card_obj       = POCO::singleton ( 'pai_cameraman_card_class' );
        $this->pic_score_obj            = POCO::singleton ( 'pai_score_rank_class');
        
        $this->wifi_mobile_card_url     = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#model_card/[YUE_LOGIN_ID]';
        $this->mobile_card_url          = 'http://yp.yueus.com/mobile/app?from_app=1#model_card/[YUE_LOGIN_ID]';
        
        $this->wifi_cameraman_card_url  = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#zone/[YUE_LOGIN_ID]/cameraman';
        $this->cameraman_card_url       = 'http://yp.yueus.com/mobile/app?from_app=1#zone/[YUE_LOGIN_ID]/cameraman';
	
        $this->return_data_list = array();
        $this->tmp_data_list    = array();
    }

	public function set_come_from($come_from)
	{
		$this->come_from = $come_from;
	}

    /**
     * @param $ranking_array
     * @return array|bool
     */
    public function cms_parse_by_array($ranking_array)
    {
        if(is_array($ranking_array))
        {
            foreach($ranking_array AS $key=>$val)
            {
                $rank_id    = $key;
                $name       = $val[0];
                $query      = $val[1];
                $unit       = $val[2];
                $about_url  = $val[3];
                $where_str  = $val[4]?$val[4]:'place_number ASC';
                $dmid       = $val[5];
                //$query      = $val[6];
                
                $data_info  = $this->cms_obj->get_last_issue_record_list(false, '0,4', $where_str, $rank_id);
                $info_count = $this->cms_obj->get_last_issue_record_list(TRUE, '0,4', 'place_number DESC', $key);
                //print_r($data_info);
                $this->tmp_data_list['name']  = $name;
                $this->tmp_data_list['query'] = $query;
                $this->tmp_data_list['about'] = $about_url;
                if($info_count > 4) $this->tmp_data_list['query_str'] ='更多';
                $this->tmp_data_list['mid']   = "122PT02001";
                $this->tmp_data_list['dmid']  = "$dmid";

                if($this->come_from == 'weixin')
				{
					$this->tmp_data_list['rank_id']  = $rank_id;
				}

                if(!empty($data_info))
                {
                    foreach($data_info AS $k=>$v)
                    {
                        //用户ID
                        $record['user_id']      = $v['user_id'];
                        //统计用 
                        $record['vid']          = $v['user_id'];
                        $record['jid']          = "001";
                        $record['dmid']         = "$dmid";
                        $record['user_icon']    = $v['img_url'];
                        $record['nickname']     = $v['title'];
                        $record['num']          = $v['place_number'];
                        $record['unit']         = $unit; 
						if($this->come_from == 'weixin')
						{
							$record['link_type'] = $v['link_type'];
						}
                        



                        if($v['user_id'] > 0)  $record['user_icon'] = get_user_icon($record['user_id'], 468);
                        
                        //头像修改
                        $pic_array = $this->pai_pic_obj->get_user_pic($record['user_id'], $limit = '0,5');
                        if($pic_array) {
                            foreach ($pic_array AS $a => $b) {

                                $num = explode('?', $b['img']);
                                $num = explode('x', $num[1]);
                                $num_v2 = explode('_', $num[1]);

                                $width = $num[0];
                                $height = $num_v2[0];

                                $record['user_icon'] = str_replace("_260.", "_440.", $b['img']);
                                if ($width < $height) break;
                            }
                        }
                        
                        $user_info              = $this->model_style_obj->get_model_style_combo($record['user_id']);
                        $style_array            = explode(' ', $user_info['main'][0]['style']);
                        $record['style']        = $style_array[0]?$style_array[0]:'清新';



                        if($unit == '魅力')
                        {
                            $result                 = $this->pic_score_obj->get_score_rank($record['user_id']);
                            $record['num']          = $result['score'];
                            $record['unit']         = $unit;
                        }elseif($unit == '分'){
                            $record['num']          = $v['place_number'] * 2;
                            $record['unit']         = $unit;
                        }elseif($unit == '小时'){
                            $result                  = $this->model_style_obj->get_model_style_combo($record['user_id']);
                            if($result['main'][0]['hour'])
                            {
                                $record['num']       = $result['main'][0]['price'] . "/" . $result['main'][0]['hour'];
                                $record['unit']      = $unit;
                            }
                        }elseif($unit == '备注'){
                            $record['num']            =  $v['remark'];
                            $record['unit']           =  '';
                            $record['style']          =  '';
                        }else{
                            $record['num']            =  '';
                            $record['unit']           =  '';
                            $record['style']          = '';
                        }

                        $record['role']         = $this->pai_user_obj->check_role($record['user_id']);                    
                        if($record['role'] == 'model')
                        {
                            $record['url']  = $v['link_url']?$v['link_url']:str_replace("[YUE_LOGIN_ID]", $record['user_id'], $this->mobile_card_url);
                            $record['wifi_url'] = $v['link_url']?str_replace("yp.yueus.com", "yp-wifi.yueus.com", $v['link_url']):str_replace("[YUE_LOGIN_ID]", $record['user_id'], $this->wifi_mobile_card_url);
                        
                            $model_card_info = $this->model_card_obj->get_model_card_info($record['user_id']);
                            if($model_card_info['cover_img']) $record['user_icon'] = $model_card_info['cover_img']; 
                        
                        }else{
                            $record['url']  = $v['link_url']?$v['link_url']:str_replace("[YUE_LOGIN_ID]", $record['user_id'], $this->cameraman_card_url);
                            $record['wifi_url'] = $v['link_url']?str_replace("yp.yueus.com", "yp-wifi.yueus.com", $v['link_url']):str_replace("[YUE_LOGIN_ID]", $record['user_id'], $this->wifi_cameraman_card_url);
                        
                            $cameraman_card_info = $this->cameraman_card_obj->get_cameraman_card_info($record['user_id']);
                            if($cameraman_card_info['cover_img']) $record['user_icon'] = $cameraman_card_info['cover_img'];
                        }

                        $record['user_icon'] = str_replace("_165.", "_440.", $record['user_icon']);
                                                
                        $this->tmp_data_list['user_list'][] = $record;
                    }
                    
                    $this->return_data_list[] = $this->tmp_data_list;
                    $this->tmp_data_list = array();
                                   
                }else{
                    return FALSE;
                }
                
                
            }
            
        }
         return   $this->return_data_list;  
    }


    public function cms_parse_by_array_v2($ranking_array)
    {
        if(is_array($ranking_array))
        {
            foreach($ranking_array AS $key=>$val)
            {
                $rank_id    = $key;
                $name       = $val[0];
                $query      = "yueyue://goto?type=inner_app&pid=1220046&query=" . $val[1];
                $unit       = $val[2];
                $about_url  = $val[3];
                $where_str  = $val[4]?$val[4]:'place_number ASC';
                $dmid       = $val[5];
                //$query      = $val[6];

                $data_info  = $this->cms_obj->get_last_issue_record_list(false, '0,4', $where_str, $rank_id);
                $info_count = $this->cms_obj->get_last_issue_record_list(TRUE, '0,4', 'place_number DESC', $key);
                //print_r($data_info);
                $this->tmp_data_list['name']  = $name;
                $this->tmp_data_list['query'] = $query;
                $this->tmp_data_list['about'] = $about_url;
                if($info_count > 4) $this->tmp_data_list['query_str'] ='更多';
                $this->tmp_data_list['mid']   = "122PT02001";
                $this->tmp_data_list['dmid']  = "$dmid";

                if($this->come_from == 'weixin')
                {
                    $this->tmp_data_list['rank_id']  = $rank_id;
                }

                if(!empty($data_info))
                {
                    foreach($data_info AS $k=>$v)
                    {
                        $record = '';
                        //用户ID
                        $record['user_id']      = $v['user_id'];
                        //统计用
                        $record['vid']          = $v['user_id'];
                        $record['jid']          = "001";
                        $record['dmid']         = "$dmid";
                        $record['user_icon']    = $v['img_url'];
                        $record['nickname']     = $v['title'];
                        $record['num']          = $v['place_number'];
                        $record['unit']         = $unit;

                        //打折布点
                        $tips_obj = POCO::singleton ( 'pai_alter_price_class',array(true) );
                        $tag = $tips_obj->get_topic_user_tag($v['user_id']);
                        if($tag) $record['tips'] = $tag;

                        //$record['tips'] = '特价';

                        if($this->come_from == 'weixin')
                        {
                            $record['link_type'] = $v['link_type'];
                        }




                        if($v['user_id'] > 0)  $record['user_icon'] = get_user_icon($record['user_id'], 468);

                        //头像修改
                        $pic_array = $this->pai_pic_obj->get_user_pic($record['user_id'], $limit = '0,5');
                        if($pic_array) {
                            foreach ($pic_array AS $a => $b) {

                                $num = explode('?', $b['img']);
                                $num = explode('x', $num[1]);
                                $num_v2 = explode('_', $num[1]);

                                $width = $num[0];
                                $height = $num_v2[0];

                                $record['user_icon'] = str_replace("_260.", "_320.", $b['img']);
                                if ($width < $height) break;
                            }
                        }

                        $user_info              = $this->model_style_obj->get_model_style_combo($record['user_id']);
                        $style_array            = explode(' ', $user_info['main'][0]['style']);
                        $record['style']        = $style_array[0]?$style_array[0]:'清新';



                        if($unit == '魅力')
                        {
                            $result                 = $this->pic_score_obj->get_score_rank($record['user_id']);
                            $record['num']          = $result['score'];
                            $record['unit']         = $unit;
                        }elseif($unit == '分'){
                            $record['num']          = $v['place_number'] * 2;
                            $record['unit']         = $unit;
                        }elseif($unit == '小时'){
                            $result                  = $this->model_style_obj->get_model_style_combo($record['user_id']);
                            if($result['main'][0]['hour'])
                            {
                                $record['num']       = $result['main'][0]['price'] . "/" . $result['main'][0]['hour'];
                                $record['unit']      = $unit;
                            }
                        }elseif($unit == '备注'){
                            $record['num']            =  $v['remark'];
                            $record['unit']           =  '';
                            $record['style']          =  '';
                        }else{
                            $record['num']            =  '';
                            $record['unit']           =  '';
                            $record['style']          = '';
                        }

                        $record['role']         = $this->pai_user_obj->check_role($record['user_id']);
                        if($record['role'] == 'model')
                        {
                            //$record['url']  = $v['link_url']?$v['link_url']:str_replace("[YUE_LOGIN_ID]", $record['user_id'], $this->mobile_card_url);
                            //$record['wifi_url'] = $v['link_url']?str_replace("yp.yueus.com", "yp-wifi.yueus.com", $v['link_url']):str_replace("[YUE_LOGIN_ID]", $record['user_id'], $this->wifi_mobile_card_url);
                            //yueyue://goto?type=inner_app&pid=1234&xxx=xxx
                            if($v['link_url'])
                            {
                                $record['url']       = "yueyue://goto?type=inner_web&url=" . urlencode($v['link_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $v['link_url']));
                                //$record['wifi_url'] =  $record['url'];
                            }else{
                                $record['url'] = "yueyue://goto?type=inner_app&pid=1220025&mid=122RO01001&user_id=" . $record['user_id'];
                                //$record['wifi_url'] = $record['url'];
                            }

                            $model_card_info = $this->model_card_obj->get_model_card_info($record['user_id']);
                            if($model_card_info['cover_img']) $record['user_icon'] = $model_card_info['cover_img'];

                        }else{
                            //$record['url']  = $v['link_url']?$v['link_url']:str_replace("[YUE_LOGIN_ID]", $record['user_id'], $this->cameraman_card_url);
                            //$record['wifi_url'] = $v['link_url']?str_replace("yp.yueus.com", "yp-wifi.yueus.com", $v['link_url']):str_replace("[YUE_LOGIN_ID]", $record['user_id'], $this->wifi_cameraman_card_url);
                            if($v['link_url'])
                            {
                                $record['url']       = "yueyue://goto?type=inner_web&url=" . urlencode($v['link_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $v['link_url']));
                                //$record['wifi_url'] =  $record['url'];
                            }else{
                                $record['url'] = "yueyue://goto?type=inner_app&pid=1220026&mid=122RO02001&user_id=" . $record['user_id'];
                                //$record['wifi_url'] = $record['url'];
                            }



                            $cameraman_card_info = $this->cameraman_card_obj->get_cameraman_card_info($record['user_id']);
                            if($cameraman_card_info['cover_img']) $record['user_icon'] = $cameraman_card_info['cover_img'];
                        }

                        $record['user_icon'] = str_replace("_165.", "_320.", $record['user_icon']);

                        $this->tmp_data_list['user_list'][] = $record;
                        unset($record);
                    }

                    $this->return_data_list[] = $this->tmp_data_list;
                    $this->tmp_data_list = array();

                }else{
                    return FALSE;
                }


            }

        }
        return   $this->return_data_list;
    }


    public function cms_parse_by_array_v3($ranking_array)
    {
        if(is_array($ranking_array))
        {
            print_r($ranking_array);
            foreach($ranking_array AS $key=>$val)
            {
                //echo "进入循环";
                $rank_id    = $key;
                $name       = $val[0];
                $query      = "yueyue://goto?type=inner_app&pid=1220046&query=" . $val[1];
                $unit       = $val[2];
                $about_url  = $val[3];
                $where_str  = $val[4]?$val[4]:'place_number ASC';
                $dmid       = $val[5];
                //$query      = $val[6];

                //echo $where_str;
                //echo $rank_id;
                $data_info  = $this->cms_obj->get_last_issue_record_list(false, '0,4', $where_str, $rank_id);
                $info_count = $this->cms_obj->get_last_issue_record_list(TRUE, '0,4', 'place_number DESC', $key);
                //var_dump($data_info);
                $this->tmp_data_list['name']  = $name;
                $this->tmp_data_list['query'] = $query;
                $this->tmp_data_list['about'] = $about_url;
                if($info_count > 4) $this->tmp_data_list['query_str'] ='更多';
                $this->tmp_data_list['mid']   = "122PT02001";
                $this->tmp_data_list['dmid']  = "$dmid";

                if($this->come_from == 'weixin')
                {
                    $this->tmp_data_list['rank_id']  = $rank_id;
                }

                if(!empty($data_info))
                {
                    foreach($data_info AS $k=>$v)
                    {
                        $record = '';
                        //用户ID
                        $record['user_id']      = $v['user_id'];
                        //统计用
                        $record['vid']          = $v['user_id'];
                        $record['jid']          = "001";
                        $record['dmid']         = "$dmid";
                        $record['user_icon']    = $v['img_url'];
                        $record['nickname']     = $v['title'];
                        $record['num']          = $v['place_number'];
                        $record['unit']         = $unit;

                        //打折布点
                        $tips_obj = POCO::singleton ( 'pai_alter_price_class',array(true) );
                        $tag = $tips_obj->get_topic_user_tag($v['user_id']);
                        if($tag) $record['tips'] = $tag;

                        //$record['tips'] = '特价';

                        if($this->come_from == 'weixin')
                        {
                            $record['link_type'] = $v['link_type'];
                        }




                        if($v['user_id'] > 0)  $record['user_icon'] = get_user_icon($record['user_id'], 468);

                        //头像修改
                        $pic_array = $this->pai_pic_obj->get_user_pic($record['user_id'], $limit = '0,5');
                        if($pic_array) {
                            foreach ($pic_array AS $a => $b) {

                                $num = explode('?', $b['img']);
                                $num = explode('x', $num[1]);
                                $num_v2 = explode('_', $num[1]);

                                $width = $num[0];
                                $height = $num_v2[0];

                                $record['user_icon'] = str_replace("_260.", "_320.", $b['img']);
                                if ($width < $height) break;
                            }
                        }

                        $user_info              = $this->model_style_obj->get_model_style_combo($record['user_id']);
                        $style_array            = explode(' ', $user_info['main'][0]['style']);
                        $record['style']        = $style_array[0]?$style_array[0]:'清新';



                        if($unit == '魅力')
                        {
                            $result                 = $this->pic_score_obj->get_score_rank($record['user_id']);
                            $record['num']          = $result['score'];
                            $record['unit']         = $unit;
                        }elseif($unit == '分'){
                            $record['num']          = $v['place_number'] * 2;
                            $record['unit']         = $unit;
                        }elseif($unit == '小时'){
                            $result                  = $this->model_style_obj->get_model_style_combo($record['user_id']);
                            if($result['main'][0]['hour'])
                            {
                                $record['num']       = $result['main'][0]['price'] . "/" . $result['main'][0]['hour'];
                                $record['unit']      = $unit;
                            }
                        }elseif($unit == '备注'){
                            $record['num']            =  $v['remark'];
                            $record['unit']           =  '';
                            $record['style']          =  '';
                        }else{
                            $record['num']            =  '';
                            $record['unit']           =  '';
                            $record['style']          = '';
                        }

                        $record['role']         = $this->pai_user_obj->check_role($record['user_id']);
                        if($record['role'] == 'model')
                        {
                            //$record['url']  = $v['link_url']?$v['link_url']:str_replace("[YUE_LOGIN_ID]", $record['user_id'], $this->mobile_card_url);
                            //$record['wifi_url'] = $v['link_url']?str_replace("yp.yueus.com", "yp-wifi.yueus.com", $v['link_url']):str_replace("[YUE_LOGIN_ID]", $record['user_id'], $this->wifi_mobile_card_url);
                            //yueyue://goto?type=inner_app&pid=1234&xxx=xxx
                            if($v['link_url'])
                            {
                                $record['url']       = "yueyue://goto?type=inner_web&url=" . urlencode($v['link_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $v['link_url']));
                                //$record['wifi_url'] =  $record['url'];
                            }else{
                                $record['url'] = "yueyue://goto?type=inner_app&pid=1220025&mid=122RO01001&user_id=" . $record['user_id'];
                                //$record['wifi_url'] = $record['url'];
                            }

                            $model_card_info = $this->model_card_obj->get_model_card_info($record['user_id']);
                            if($model_card_info['cover_img']) $record['user_icon'] = $model_card_info['cover_img'];

                        }else{
                            //$record['url']  = $v['link_url']?$v['link_url']:str_replace("[YUE_LOGIN_ID]", $record['user_id'], $this->cameraman_card_url);
                            //$record['wifi_url'] = $v['link_url']?str_replace("yp.yueus.com", "yp-wifi.yueus.com", $v['link_url']):str_replace("[YUE_LOGIN_ID]", $record['user_id'], $this->wifi_cameraman_card_url);
                            if($v['link_url'])
                            {
                                $record['url']       = "yueyue://goto?type=inner_web&url=" . urlencode($v['link_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $v['link_url']));
                                //$record['wifi_url'] =  $record['url'];
                            }else{
                                $record['url'] = "yueyue://goto?type=inner_app&pid=1220026&mid=122RO02001&user_id=" . $record['user_id'];
                                //$record['wifi_url'] = $record['url'];
                            }



                            $cameraman_card_info = $this->cameraman_card_obj->get_cameraman_card_info($record['user_id']);
                            if($cameraman_card_info['cover_img']) $record['user_icon'] = $cameraman_card_info['cover_img'];
                        }

                        $record['user_icon'] = str_replace("_165.", "_320.", $record['user_icon']);

                        $this->tmp_data_list['user_list'][] = $record;
                        unset($record);
                    }

                    $this->return_data_list[] = $this->tmp_data_list;
                    $this->tmp_data_list = array();

                }else{
                    return FALSE;
                }


            }

        }
        return   $this->return_data_list;
    }


    /**
     * 此方法小肖榜单后台使用
     * [cms_parse_by_array_v3 description]
     * @param  [array] $ranking_array [数组]
     * @return [array]                [返回值]
     */
    public function cms_parse_by_array_for_rank_list($ranking_array)
    {
        if(is_array($ranking_array))
        {
            foreach($ranking_array AS $key=>$val)
            {
                $rank_id    = $key;
                $name       = $val[0];
                $query      = "yueyue://goto?type=inner_app&pid=1220046&query=" . $val[1];
                $unit       = $val[2];
                $about_url  = $val[3];
                $where_str  = $val[4]?$val[4]:'place_number ASC';
                $dmid       = $val[5];
                //$query      = $val[6];
                $data_info  = $this->cms_obj->get_last_issue_record_list(false, '0,99999999', $where_str, $rank_id);
                $info_count = $this->cms_obj->get_last_issue_record_list(TRUE, '0,99999999', 'place_number DESC', $key);
                //print_r($data_info);
                $this->tmp_data_list['name']  = $name;
                $this->tmp_data_list['query'] = $query;
                $this->tmp_data_list['about'] = $about_url;
                if($info_count > 4) $this->tmp_data_list['query_str'] ='更多';
                $this->tmp_data_list['mid']   = "122PT02001";
                $this->tmp_data_list['dmid']  = "$dmid";

                if($this->come_from == 'weixin')
                {
                    $this->tmp_data_list['rank_id']  = $rank_id;
                }

                if(!empty($data_info))
                {
                    foreach($data_info AS $k=>$v)
                    {
                        $record = '';
                        //用户ID
                        $record['user_id']      = $v['user_id'];
                        //统计用
                        $record['vid']          = $v['user_id'];
                        $record['jid']          = "001";
                        $record['dmid']         = "$dmid";
                        $record['user_icon']    = $v['img_url'];
                        $record['nickname']     = $v['title'];
                        $record['num']          = $v['place_number'];
                        $record['unit']         = $unit;

                        //打折布点
                        $tips_obj = POCO::singleton ( 'pai_alter_price_class',array(true) );
                        $tag = $tips_obj->get_topic_user_tag($v['user_id']);
                        if($tag) $record['tips'] = $tag;

                        //$record['tips'] = '特价';

                        if($this->come_from == 'weixin')
                        {
                            $record['link_type'] = $v['link_type'];
                        }




                        if($v['user_id'] > 0)  $record['user_icon'] = get_user_icon($record['user_id'], 468);

                        //头像修改
                        $pic_array = $this->pai_pic_obj->get_user_pic($record['user_id'], $limit = '0,5');
                        if($pic_array) {
                            foreach ($pic_array AS $a => $b) {

                                $num = explode('?', $b['img']);
                                $num = explode('x', $num[1]);
                                $num_v2 = explode('_', $num[1]);

                                $width = $num[0];
                                $height = $num_v2[0];

                                $record['user_icon'] = str_replace("_260.", "_320.", $b['img']);
                                if ($width < $height) break;
                            }
                        }

                        $user_info              = $this->model_style_obj->get_model_style_combo($record['user_id']);
                        $style_array            = explode(' ', $user_info['main'][0]['style']);
                        $record['style']        = $style_array[0]?$style_array[0]:'清新';



                        if($unit == '魅力')
                        {
                            $result                 = $this->pic_score_obj->get_score_rank($record['user_id']);
                            $record['num']          = $result['score'];
                            $record['unit']         = $unit;
                        }elseif($unit == '分'){
                            $record['num']          = $v['place_number'] * 2;
                            $record['unit']         = $unit;
                        }elseif($unit == '小时'){
                            $result                  = $this->model_style_obj->get_model_style_combo($record['user_id']);
                            if($result['main'][0]['hour'])
                            {
                                $record['num']       = $result['main'][0]['price'] . "/" . $result['main'][0]['hour'];
                                $record['unit']      = $unit;
                            }
                        }elseif($unit == '备注'){
                            $record['num']            =  $v['remark'];
                            $record['unit']           =  '';
                            $record['style']          =  '';
                        }else{
                            $record['num']            =  '';
                            $record['unit']           =  '';
                            $record['style']          = '';
                        }

                        $record['role']         = $this->pai_user_obj->check_role($record['user_id']);
                        if($record['role'] == 'model')
                        {
                            //$record['url']  = $v['link_url']?$v['link_url']:str_replace("[YUE_LOGIN_ID]", $record['user_id'], $this->mobile_card_url);
                            //$record['wifi_url'] = $v['link_url']?str_replace("yp.yueus.com", "yp-wifi.yueus.com", $v['link_url']):str_replace("[YUE_LOGIN_ID]", $record['user_id'], $this->wifi_mobile_card_url);
                            //yueyue://goto?type=inner_app&pid=1234&xxx=xxx
                            if($v['link_url'])
                            {
                                $record['url']       = "yueyue://goto?type=inner_web&url=" . urlencode($v['link_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $v['link_url']));
                                //$record['wifi_url'] =  $record['url'];
                            }else{
                                $record['url'] = "yueyue://goto?type=inner_app&pid=1220025&mid=122RO01001&user_id=" . $record['user_id'];
                                //$record['wifi_url'] = $record['url'];
                            }

                            $model_card_info = $this->model_card_obj->get_model_card_info($record['user_id']);
                            if($model_card_info['cover_img']) $record['user_icon'] = $model_card_info['cover_img'];
                            if($model_card_info['intro']) $record['intro'] = $model_card_info['intro'];

                        }else{
                            //$record['url']  = $v['link_url']?$v['link_url']:str_replace("[YUE_LOGIN_ID]", $record['user_id'], $this->cameraman_card_url);
                            //$record['wifi_url'] = $v['link_url']?str_replace("yp.yueus.com", "yp-wifi.yueus.com", $v['link_url']):str_replace("[YUE_LOGIN_ID]", $record['user_id'], $this->wifi_cameraman_card_url);
                            if($v['link_url'])
                            {
                                $record['url']       = "yueyue://goto?type=inner_web&url=" . urlencode($v['link_url']) . "&wifi_url=" . urlencode(str_replace("yp.yueus.com", "yp-wifi.yueus.com", $v['link_url']));
                                //$record['wifi_url'] =  $record['url'];
                            }else{
                                $record['url'] = "yueyue://goto?type=inner_app&pid=1220026&mid=122RO02001&user_id=" . $record['user_id'];
                                //$record['wifi_url'] = $record['url'];
                            }



                            $cameraman_card_info = $this->cameraman_card_obj->get_cameraman_card_info($record['user_id']);
                            if($cameraman_card_info['cover_img']) $record['user_icon'] = $cameraman_card_info['cover_img'];
                            if($cameraman_card_info['intro']) $record['intro'] = $cameraman_card_info['intro'];
                        }

                        $record['user_icon'] = str_replace("_165.", "_320.", $record['user_icon']);

                        $this->tmp_data_list['user_list'][] = $record;
                        unset($record);
                    }

                    $this->return_data_list[] = $this->tmp_data_list;
                    $this->tmp_data_list = array();

                }else{
                    return FALSE;
                }


            }

        }
        return   $this->return_data_list;
    }

    function del_goods_id($goods_id)
    {
        $goods_id = (int)$goods_id;
        $add_time = time();
        $sql_str = "INSERT INTO pai_log_db.pai_cms_del_goods_id_tbl(goods_id, add_time) VALUES ($goods_id,$add_time)";
        db_simple_getdata($sql_str, TRUE, 101);

    }
}
?>