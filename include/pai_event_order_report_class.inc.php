<?php

/**
 * @desc ��������
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015��5��18��
 * @version 1
 */
 class pai_event_order_report_class extends POCO_TDG
 {
 	/**
 	 * ���캯��
 	 *
 	 */
 	public function __construct()
 	{
 		$this->setServerId ( 22 );
 		$this->setDBName ( 'yueyue_stat_db' );
 		$this->setTableName ( 'yueyue_event_order_tbl' );
 	}


     /**
      * @param bool $b_select_count �Ƿ��ѯ����
      * @param string $where_str    ����
      * @param string $order_by     ����
      * @param string $limit        ѭ������
      * @param string $fields       ��ѯ�ֶ�
      * @return array|int           ����ֵ
      */
     public function get_list($b_select_count = false,$where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
 	{
 		
 		if ($b_select_count == true)
 		{
 			$ret = $this->findCount ( $where_str );
 		}
 		else
 		{
 			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
 		}
 		return $ret;
 	}


     /**
      * �������ڻ�ȡ�������� ���а���Լ�ĺ�����
      *
      * @param int   $intime               //���� ʱ���
      * @param string $where_str           //���� $where_str = "id = 1";
      * @return array array('count_yuepai'  => 0,'amount_yuepai' => 0,'count_waipai'  => 0,'amount_waipai' => 0);
      *
      */
     public function event_order_ret_by_date($intime,$where_str = '')
    {
        $result = array(
            'count_yuepai'  => 0, //Լ�Ķ�������
            'amount_yuepai' => 0, //Լ�Ķ����ܶ�
            'count_coupon_yuepai'  => 0, //��ʹ���Ż�ȯԼ�Ķ�������
            'amount_coupon_yuepai' => 0, //��ʹ���Ż�ȯԼ�Ķ������
            'count_waipai'  => 0, //���Ķ�������
            'amount_waipai' => 0, //���Ķ����ܶ�
            'count_coupon_waipai'  => 0, //��ʹ���Ż�ȯ���Ķ�������
            'amount_coupon_waipai' => 0  //��ʹ���Ż�ȯ���Ķ������
        );
        $intime = intval($intime)-24*3600;
        if($intime <1) return $result;
        $date = date('Y-m-d', $intime);

        $yuepai_order  = $this->get_yuepai_order($date,$where_str);
        $waipai_order  = $this->get_waipai_order($date,$where_str);
        $yuepai_coupon = $this->get_yuepai_coupon($date,$where_str);
        //print_r($waipai_order);
        $waipai_coupon = $this->get_waipai_coupon($date,$where_str);

        //��������
        $count_yuepai         = intval($yuepai_order['yuepai']['yuepai_num']);
        $amount_yuepai        = sprintf('%.2f',$yuepai_order['yuepai']['yuepai_price']);
        $count_coupon_yuepai  = intval($yuepai_coupon['yuepai_coupon']['yuepai_coupon_num']);
        $amount_coupon_yuepai = sprintf('%.2f',$yuepai_coupon['yuepai_coupon']['yuepai_coupon_price']);
        $count_waipai         = intval($waipai_order['waipai']['waipai_num']);
        $amount_waipai        = sprintf('%.2f',$waipai_order['waipai']['waipai_price']);
        $count_coupon_waipai  = intval($waipai_coupon['waipai_coupon']['waipai_coupon_num']);
        $amount_coupon_waipai = sprintf('%.2f',$waipai_coupon['waipai_coupon']['waipai_coupon_price']);

        $result['count_yuepai']         = $count_yuepai;
        $result['amount_yuepai']        = $amount_yuepai;
        $result['count_coupon_yuepai']  = $count_coupon_yuepai;
        $result['amount_coupon_yuepai'] = $amount_coupon_yuepai;
        $result['count_waipai']         = $count_waipai;
        $result['amount_waipai']        = $amount_waipai;
        $result['count_coupon_waipai']  = $count_coupon_waipai;
        $result['amount_coupon_waipai'] = $amount_coupon_waipai;
        return $result;
    }

     /**
      * ��ȡԼ�Ķ������Ͷ����۸�
      * @param date   $date  //���� ��ʽΪ 'xxxx-xx-xx' ����: '2015-05-31'
      * @param string //���� $where_str = "id = 1";
      * @return array array('result' => 0,'message' => '', 'yuepai' => array());
      */
     public function get_yuepai_order($date,$where_str = '')
     {
         $result = array('result' => 0,'message' => '', 'yuepai' => array());

         $date = trim($date);

         if(strlen($date) <1)
         {
             $result['result']  = '-1';
             $result['message'] = '���ڲ���Ϊ��';
             return $result;
         }

         //��������
         $date = date('Y-m-d', strtotime($date));
         if(strlen($where_str)>0) $where_str .= ' AND ';
         $where_str .= "FROM_UNIXTIME(complete_time,'%Y-%m-%d')=:x_date";
         sqlSetParam($where_str,'x_date',$date);
         if(strlen($where_str)>0) $where_str .= ' AND ';
         $where_str .= "(event_status='2' AND date_id>0 OR (event_status='3' AND type='have_part_refunded')) AND pay_status=1";

         $ret = $this->find($where_str,'',"count(id) as yuepai_num,sum(budget) as yuepai_price");
         if(!is_array($ret)) $ret = array();
         $result['yuepai'] = $ret;
         return $result;
     }

     /**
      * ��ȡ���Ķ����ͼ۸�
      *
      * @param date   $date        //���� ��ʽΪ 'xxxx-xx-xx' ����: '2015-05-31'
      * @param string $where_str   //���� $where_str = "id = 1";
      * @return array array('result' => 0,'message' => '', 'waipai' => array());
      */
     public function get_waipai_order($date, $where_str = '')
     {
         $result = array('result' => 0,'message' => '', 'waipai' => array());

         $date = trim($date);

         if(strlen($date) <1)
         {
             $result['result']  = '-1';
             $result['message'] = '���ڲ���Ϊ��';
             return $result;
         }
         //��������
         $date = date('Y-m-d', strtotime($date));
         if(strlen($where_str)>0) $where_str .= ' AND ';
         $where_str .= "FROM_UNIXTIME(complete_time,'%Y-%m-%d')=:x_date";
         sqlSetParam($where_str,'x_date',$date);
         if(strlen($where_str)>0) $where_str .= ' AND ';
         $where_str .= "(event_status='2' AND date_id=0) AND pay_status=1";

         $ret = $this->find($where_str,'',"count(id) as waipai_num,sum(budget*enroll_num) as waipai_price");
         if(!is_array($ret)) $ret = array();
         $result['waipai'] = $ret;
         return $result;
     }

     /**
      * ��ȡԼ��ʹ���Ż�ȯʹ�����
      *
      * @param date   $date       //���� ��ʽΪ 'xxxx-xx-xx' ����: '2015-05-31'
      * @param string $where_str  //���� $where_str = "id = 1";
      * @return array array('result' => 0,'message' => '', 'yuepai_coupon' => array());
      */
     public function get_yuepai_coupon($date,$where_str = '')
     {
         $result = array('result' => 0,'message' => '', 'yuepai_coupon' => array());

         $date = trim($date);

         if(strlen($date) <1)
         {
             $result['result']  = '-1';
             $result['message'] = '���ڲ���Ϊ��';
             return $result;
         }
         //��������
         $date = date('Y-m-d', strtotime($date));
         if(strlen($where_str)>0) $where_str .= ' AND ';
         $where_str .= "FROM_UNIXTIME(complete_time,'%Y-%m-%d')=:x_date";
         sqlSetParam($where_str,'x_date',$date);
         if(strlen($where_str)>0) $where_str .= ' AND ';
         $where_str .= "(event_status='2' AND date_id>0 OR (event_status='3' AND type='have_part_refunded'))
                       AND pay_status=1 AND is_use_coupon=1";
         $ret = $this->find($where_str,'',"count(id) as yuepai_coupon_num,sum(discount_price) as yuepai_coupon_price");
         if(!is_array($ret)) $ret = array();
         $result['yuepai_coupon'] = $ret;
         return $result;
     }

     /**
      * ��ȡ����ʹ���Ż�ȯʹ�����
      *
      * @param date   $date      //���� ��ʽΪ 'xxxx-xx-xx' ����: '2015-05-31'
      * @param string $where_str //$where_str = "id = 1";
      * @return array array('result' => 0,'message' => '', 'waipai_coupon' => array());
      */
     public function get_waipai_coupon($date, $where_str = '')
     {
         $result = array('result' => 0,'message' => '', 'waipai_coupon' => array());
         $date = trim($date);
         if(strlen($date) <1)
         {
             $result['result']  = '-1';
             $result['message'] = '���ڲ���Ϊ��';
             return $result;
         }
         //��������
         $date = date('Y-m-d', strtotime($date));
         if(strlen($where_str)>0) $where_str .= ' AND ';
         $where_str .= "FROM_UNIXTIME(complete_time,'%Y-%m-%d')=:x_date";
         sqlSetParam($where_str,'x_date',$date);
         if(strlen($where_str)>0) $where_str .= ' AND ';
         $where_str .= "(event_status='2' AND date_id=0) AND pay_status=1 AND is_use_coupon=1";

         $ret = $this->find($where_str,'',"count(id) as waipai_coupon_num,sum(discount_price) as waipai_coupon_price");
         if(!is_array($ret)) $ret = array();
         $result['waipai_coupon'] = $ret;
         return $result;
     }
 }