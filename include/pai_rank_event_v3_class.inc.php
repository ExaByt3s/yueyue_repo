<?php
/**
 * @desc:   榜单首页和内容页配置v3版
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/6
 * @Time:   13:32
 * version: 3.0
 */

class pai_rank_event_v3_class extends POCO_TDG
{
	
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
    {
        include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/new_rank/pai_cms_rank_class.inc.php");
        $this->cms_rank_obj = new pai_cms_rank_class();//榜单类
    }

    /**
     * 通过版本名获取数据【为app准备】
     * @param string $versions_name
     * @return int
     */
    public function get_versions_id_by_name($versions_name)
    {
        $versions_list = include("/disk/data/htdocs232/poco/pai/yue_admin_v2/new_rank/versions_config.inc.php");
        $versions_name = trim($versions_name);
        if(strlen($versions_name) <1) return 0;

        if($versions_name == '3.1.10') return 2;
        foreach($versions_list as $val)
        {
            if($val['name'] == $versions_name) return $val['versions_id'];
        }
        return 0;
    }

    /**
     * 获取列表数据
     * @param int $main_id
     * @param string $limit
     * @return array|bool
     */
    public function get_cms_rank_info_list($main_id, $limit = '0,15')
    {
        $main_id = intval($main_id);
        if($main_id <0)return false;
        $arr = array();

        $ret = $this->cms_rank_obj->get_main_info_by_id($main_id);
        $total_count = $this->cms_rank_obj->get_rank_info_list(true,$main_id,'','on',0,'');
        $list = $this->cms_rank_obj->get_rank_info_list(false,$main_id,'','on',0,'','`order` DESC,id DESC', $limit);
        if(!is_array($ret)) $ret = array();
        foreach($list as &$v)
        {
            $v['curl'] = $this->get_url_by_type($v['rank_type'],$v['title'],$v['pid'],$v['link_url'],$v['rank_id'],$v['cms_type']);
            if(strlen($v['img_url'])>0) $v['img_url'] = yueyue_resize_act_img_url($v['img_url']);
        }
        $arr = array(
            'title'=> trim($ret['title']),
            'list' => $list,
            'total_count' => $total_count
        );
        return $arr;
    }
    /**
     * 获取榜单连接
     * @param int $type 类型
     * @param $title 大标题
     * @param int $pid      PID
     * @param string $url 自动模式连接
     * @param int $rank_id 榜单模式,榜单连接
     * @param string $cms_type 榜单模式,有商品和商家模式(good,mall)
     * @return string
     */
    public function get_url_by_type($type =0,$title,$pid,$url='',$rank_id =0,$cms_type = '')
    {
        $type = intval($type);
        $title = trim($title);
        $pid = intval($pid);
        $url = trim($url);
        $rank_id = intval($rank_id);
        $cms_type = trim($cms_type);

        $curl = '';
        if($type ==1)//榜单方式
        {
            $cms_str = urlencode('yueyue_static_cms_id=' .$rank_id.'&cms_type='.$cms_type);
            $curl = "yueyue://goto?type=inner_app&pid={$pid}&return_query={$cms_str}&title=".urlencode(iconv('gbk', 'utf8', $title));
        }
        elseif($type == 0)
        {
            $url_arr = parse_url($url);
            $httts = trim($url_arr['scheme']);
            if($httts == 'http' || $httts == 'https')//是否为http||https
            {
                $wifi_url = str_replace('yp.yueus.com','yp-wifi.yueus.com',$url);
                $curl = "yueyue://goto?type=inner_web&url=".urlencode(iconv('gbk', 'utf8', $url))."&wifi_url=".urlencode(iconv('gbk', 'utf8', $wifi_url))."&title=".urlencode(iconv('gbk', 'utf8', $title))."&showtitle=2";
            }
            elseif($httts == 'yueyue' || $httts == 'yueseller')
            {
                $curl = $url;
            }
            else
            {
                $curl = "yueyue://goto?type=inner_app&pid={$pid}&return_query=".urlencode(iconv('gbk', 'utf8', $url))."&title=" . urlencode(iconv('gbk', 'utf8', $title));
            }

        }
        return $curl;
    }

    /**
     * 获取两张表数据
     * @param bool bool $b_select_count
     * @param int $versions_id
     * @param string $page_type
     * @param string $switch on|off
     * @param int $location_id
     * @param string $where_str
     * @param string $order_by
     * @param string $limit
     * @param string $field
     * @return array
     */
    private function get_cms_rank_list($b_select_count = false,$versions_id,$page_type,$switch,$location_id,$where_str ='',$order_by = 'id DESC',$limit = '0,30',$field='*')
    {
        $arr = array('index','category_index','list');
        $versions_id = (int)$versions_id;
        $page_type = trim($page_type);
        $location_id = (int)$location_id;
        if(strlen($page_type) <1 || !in_array($page_type,$arr))
        {
            return false;
        }
        if($location_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .="location_id={$location_id}";
        }
        $ret = $this->cms_rank_obj->get_main_rank_list(false,$page_type,0,$versions_id,$switch,0,$where_str,$order_by, "0,99999999",$field);
        if(!is_array($ret)) $ret = array();
        foreach($ret as $key=>$val)
        {
            if(strlen($val['img_url'])>0) $ret[$key]['img_url'] = yueyue_resize_act_img_url($val['img_url']);//图片
            if(strlen($val['link'])>0) $ret[$key]['curl'] = $this->get_url_by_type(0,$val['title'],0,$val['link']);//有约更多
            $id = intval($val['id']);
            $rank_info = $this->cms_rank_obj->get_rank_info_list(false,$id,'','on',0,'','`order` DESC,id DESC', $limit); //获取下级数据
            if(!is_array($rank_info)) $rank_info = array();

            $general_info = array();
            $banner_info = array();
            $i = 0;
            $j = 0;
            foreach($rank_info as &$v)
            {
                $v['curl'] = $this->get_url_by_type($v['rank_type'],$v['title'],$v['pid'],$v['link_url'],$v['rank_id'],$v['cms_type']);
                if(strlen($v['img_url'])>0) $v['img_url'] = yueyue_resize_act_img_url($v['img_url']); //图片转换
                if($v['type'] == 'general')
                {
                    $general_info[$i] = $v;
                    $i++;
                }
                elseif($v['type'] == 'banner')
                {
                    $banner_info[$j] = $v;
                    $j++;
                }

            }
            $ret[$key]['general_info'] = $general_info;
            $ret[$key]['banner_info'] = $banner_info;
            $ret[$key]['rank_info'] = $rank_info;
            unset($general_info);
            unset($banner_info);
            unset($rank_info);
        }
        return $ret;
    }
    /**
     * 获取列表数据 APP接口,勿动
     * @param $page_type  位置  index(首页),list(列表页),category_index(首页分类)
     * @param int $type_id      分类ID  (首页可以为空,默认选择为-1),例如模特邀约为31
     * @param int $location_id  地区ID  (默认选择为101029001广州)例如广州的location_id '101029001'
     * @param $versions_name     版本名(可以为空) 例如 '3.0.0'
     * @param string $limit     循环条数,示例:"0,15"
     * @param string $switch       状态-1全部，off下架，on为上架
     * @return array|int        返回值
     */
    public function get_cms_rank_by_location_id($page_type,$type_id = -1,$location_id = 101029001,$versions_name,$limit="0,15",$switch='on')
    {
        $arr = array('index','category_index','list');
        $page_type = trim($page_type);
        $type_id = (int)$type_id;
        $location_id = (int)$location_id;
        $versions_name = trim($versions_name);
        $switch = trim($switch);
        $versions_id = 0;
        $where_str = '';
        if(strlen($page_type) <1 || !in_array($page_type,$arr))
        {
            return false;
        }
        if($page_type == 'list' || $page_type== 'category_index')
        {
            if($type_id >=0)
            {
                if(strlen($where_str) >0) $where_str .= ' AND ';
                $where_str .= "type_id ={$type_id}";
            }
        }
        if($location_id <1)
        {
            $location_id = 101029001;
        }
        if(strlen($versions_name) >0)
        {
            $versions_id = $this->get_versions_id_by_name($versions_name);
        }
        else
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "versions_id !=2";
        }
        $list = $this->get_cms_rank_list(false,$versions_id,$page_type,$switch,$location_id,$where_str,'`order` DESC,id DESC',$limit,'id,location_id,page_type,module_type,type_id,versions_id,link,`order`,title,img_url');
        return $list;
    }
}
