<?php
/**
 * 活动相关作品操作类
 *
 */

class pai_mall_relate_opus_class extends POCO_TDG
{	
	/**
	 * 最后一次错误提示
	 * @var string
	 */
	protected $_last_err_msg = null;
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
        $this->setServerId('101');
        $this->setDBName('mall_db');
	}


    private function set_relate_opus_tbl()
    {
        $this->setTableName('mall_relate_opus_tbl');
    }


    /**
	 * 添加作品链接
	 *
	 * @param array $data
	 * @return bool
	 */
	public function add_opus($data)
	{
        $data['user_id'] = (int)$data['user_id'];
        $data['goods_id'] = (int)$data['goods_id'];
        $data['url'] = trim($data['url']);

		if (empty($data['user_id']) || empty($data['goods_id']))
		{
            $result['result'] = -1;
            $result['message'] = "参数错误";
			return $result;
		}

        $activity_info = POCO::singleton('pai_mall_api_class')->get_goods_id_activity_info($data['goods_id']);
        if($activity_info['is_have_end']!=1)
        {
            $result['result'] = -1;
            $result['message'] = "活动还没结束，请稍后再发布";
            return $result;
        }


        $count_join = POCO::singleton('pai_mall_order_class')->get_order_list_by_activity_id_for_buyer($data['user_id'], $data['goods_id'], 8, true);

        if($count_join==0)
        {
            $result['result'] = -1;
            $result['message'] = "你的活动订单状态还没完成，请稍后再发布";
            return $result;
        }

        $collect_ret = $this->collect_opus_data($data['source'],$data['url']);

        if(empty($collect_ret['title']) || empty($collect_ret['img_url']))
        {
            $result['result'] = -1;
            $result['message'] = "无法识别作品链接，请更换链接后再试";
            return $result;
        }


        $this->set_relate_opus_tbl();

        $insert_data['user_id'] = $data['user_id'];
        $insert_data['goods_id'] = $data['goods_id'];
        $insert_data['url'] = $data['url'];
        $insert_data['title'] = $collect_ret['title'];
        $insert_data['img_url'] = $collect_ret['img_url'];
        $insert_data['add_time'] = time();
        $insert_data['source'] = $data['source'];
        $insert_data['status'] = 1;

		$this->insert($insert_data);

        $result['result'] = 1;
        $result['message'] = "添加成功";
        return $result;
	}

    /*
     * 获取作品列表
     */
    public function get_opus_list($b_select_count=false,$where_str='',$limit='0,10',$order_by='add_time desc',$fields='*')
    {

        $this->set_relate_opus_tbl();

        if ($b_select_count == true)
        {
            $ret = $this->findCount ( $where_str );
        }
        else
        {
            $ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
            $ret = $this->fill_data_list($ret);
        }
        return $ret;
    }


    /*
     * 获取作品列表
     */
    public function get_opus_full_list($b_select_count=false,$goods_id,$limit='0,10',$order_by='add_time desc')
    {
        $goods_id = (int)$goods_id;
        $where = "status=1 and goods_id={$goods_id}";

        $ret = $this->get_opus_list($b_select_count,$where,$limit,$order_by,'*');

        return $ret;
    }


    private function fill_data_list($list)
    {
        if(!is_array($list))
        {
            return array();
        }

        foreach($list as $k=>$val)
        {
            $list[$k]['nickname'] = get_user_nickname_by_user_id($val['user_id']);
            $list[$k]['user_icon'] = get_user_icon($val['user_id'], 64);
            $list[$k]['img_url'] = yueyue_resize_act_img_url($val['img_url'], 440);
        }

        return $list;
    }


    /*
     * 删除作品
     * @param $opus_id
     * @param $user_id
     */
    public function delete_opus($opus_id,$user_id)
    {
        $opus_id = (int)$opus_id;
        $user_id = (int)$user_id;

        if(empty($opus_id) || empty($user_id))
        {
            $result['result']=-1;
            $result['message']="参数错误";
            return $result;
        }

        $this->set_relate_opus_tbl();

        $opus_info = $this->find("opus_id={$opus_id} and status=1");

        if($opus_info['user_id']!=$user_id)
        {
            $result['result']=-1;
            $result['message']="非法操作";
            return $result;
        }

        $this->delete("opus_id={$opus_id}");

        $result['result']=1;
        $result['message']="删除成功";
        return $result;

    }


	/*
	 * 抓取作品封面图标题
	 * @param $source
	 * @param $url
	 */
    public function collect_opus_data($source,$url)
    {
        switch($source)
        {
            case 'poco':
                $ret = curl_event_data("event_api_class", "get_poco_excute", array("act.get_act_info_by_parse_url",array($url)));
                $title = $ret['item_title'];
                $img_url = $ret['item_img'];
                break;
        }

        $collect_ret['title'] =$title;
        $collect_ret['img_url'] =$img_url;

        return $collect_ret;
    }

}

?>