<?php
/**
 * @desc:   �̳Ƕ���log
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/24
 * @Time:   14:13
 * version: 1.0
 */
class pai_mall_order_log_class extends POCO_TDG
{
    /**
     * ���캯��
     */
    public function __construct()
    {
        $this->setServerId( 101 );
        $this->setDBName( 'pai_log_db' );
    }

    /**
     * ѡ�����ݱ�,��������ᴴ��
     * @return bool
     * @throws App_Exception
     */
    public function set_mall_log_tbl()
    {
        $table_num = date('Ym',time());
        $table_name = "yueus_mall_order_log_{$table_num}";
        $res = $this->query("SHOW TABLES FROM {$this->_db_name} LIKE '{$table_name}'");
        if (empty($res) || !is_array($res)) //�����ڴ���
        {
            $sql_str = "CREATE TABLE IF NOT EXISTS {$this->_db_name}.{$table_name} (
                      `id` int(10) unsigned NOT NULL auto_increment COMMENT '����ID',
                      `action` varchar(100) NOT NULL COMMENT '��������',
                      `order_id` int(10) unsigned NOT NULL COMMENT '����ID',
                      `buyer_user_id` int(10) unsigned NOT NULL default '0' COMMENT '������ID',
                      `type_id` int(10) unsigned NOT NULL default '0',
                      `seller_user_id` int(10) unsigned NOT NULL default '0' COMMENT '�̼�ID',
                      `order_info_ser` text NOT NULL COMMENT '����ϵ�л�����',
                      `goods_info_ser` text NOT NULL COMMENT '��Ʒϵ�л�����',
                      `comment_info_ser` text NOT NULL COMMENT '����ϵ�л�����',
                      `item` varchar(100) NOT NULL COMMENT '��Ŀ��',
                      `add_time` int(10) unsigned NOT NULL default '0' COMMENT '���ʱ��',
                      PRIMARY KEY  (`id`),
                      KEY `order_id` (`order_id`),
                      KEY `buyer_user_id` (`buyer_user_id`),
                      KEY `seller_user_id` (`seller_user_id`),
                      KEY `type_id` (`type_id`)
                    ) ENGINE=MyISAM;";
            $this->query($sql_str);
        }
        $this->setTableName($table_name);
        return true;
    }

    /**
     * @param $insert_data
     * @return int
     */
    private function add_info($insert_data)
    {
        if( !is_array($insert_data) || empty($insert_data) )
        {
            return 0;
        }
        $this->set_mall_log_tbl();
        return $this->insert($insert_data, 'IGNORE');
    }


    /**
     *��Ӷ���LOG
     * @param string $action ����
     * @param int $order_id  ����ID
     * @param int $buyer_user_id ������ID
     * @param int $seller_user_id �̼�ID
     * @param array $order_info   ������Ϣ
     * @param array $comment_info  ������Ϣ
     * @param string $item   ��Ŀ����
     * @return int ����ֵ
     */
    public function  add_order_log($action,$order_id,$buyer_user_id,$seller_user_id,$order_info = array(),$comment_info = array(),$item = 'yueyue')
    {
        global $_INPUT;
        //��־
        pai_log_class::add_log($_INPUT, 'add_order started', 'add_order');
        $action = trim($action);
        $order_id = intval($order_id);
        $buyer_user_id = intval($buyer_user_id);
        $seller_user_id = intval($seller_user_id);
        $order_info = (array)$order_info;
        //$goods_info = (array)$goods_info;
        $comment_info = (array)$comment_info;
        $item = trim($item);
        if(strlen($action) <1 || $buyer_user_id <1 || $seller_user_id <1) return 0;
        $data = array();
        $data['action'] = $action;
        $data['order_id'] = $order_id;
        $data['buyer_user_id'] =$buyer_user_id;
        $data['seller_user_id'] = $seller_user_id;
        $data['type_id'] = intval($order_info['type_id']);
        $data['order_info_ser'] = serialize($order_info);
        //$data['goods_info_ser'] = serialize($goods_info);
        $data['comment_info_ser'] = serialize($comment_info);
        $data['item'] = $item;
        $data['add_time'] = time();
        $this->add_info($data);
        return true;
    }
}