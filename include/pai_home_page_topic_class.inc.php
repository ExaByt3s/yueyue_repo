<?php
/*
 * ������
 */
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');

class pai_home_page_topic_class extends POCO_TDG
{
	var $cms_obj;
	
	function __construct()
	{
		$this->cms_obj = new cms_system_class ();
	}
	
	/*
	 * ��ȡ�����
	 */
	function get_big_category($location_id = 0)
	{
		switch ($location_id)
		{
			//����
			case 101029001 :
				$issue_id = 9595;
				break;
			
			/*//�人
			case 101019001 :
				$issue_id = 54;
				break;
			
			//����
			case 101001001 :
				$issue_id = 55;
				break;
			
			//�Ϻ�
			case 101003001 :
				$issue_id = 56;
				break;
			
			//�ɶ�
			case 101022001 :
				$issue_id = 58;
				break;
			
			//����
			case 101004001 :
				$issue_id = 57;
				break;*/
			
			default :
				$issue_id = 9595;
				break;
		}
		
		$info = $this->cms_obj->get_record_list_by_issue_id(false, $issue_id, "0,10", "place_number ASC", $freeze=null, $where_str="");
		foreach($info as $k=>$val)
		{
            $remark_arr = explode("|",$val['remark']);
			$info[$k]['pc_img'] = $val['img_url'];
			$info[$k]['b_id'] = $remark_arr[0];
            $info[$k]['type_id'] = $remark_arr[1];
		}
		return $info;
	}
	
	/*
	 * ��ȡС����
	 */
	function get_small_category($issue_id=0)
	{
		$info = $this->cms_obj->get_record_list_by_issue_id(false, $issue_id, "0,10", "place_number ASC", $freeze=null, $where_str="");
		foreach($info as $k=>$val)
		{
			$content_arr = explode("|",$val['content']);
			$info[$k]['pc_img'] = $content_arr[0];
			$info[$k]['wap_img'] = $content_arr[1];
			$info[$k]['app_img'] = $content_arr[2];
			$info[$k]['s_id'] = $val['remark'];
		}
		return $info;
	}
	
	/*
	 * ��ȡС����3����Ʒ
	 */
	function get_small_category_3_goods($issue_id=0)
	{
		$info = $this->cms_obj->get_record_list_by_issue_id(false, $issue_id, "0,1", "place_number ASC", $freeze=null, $where_str="");
		$s_id = (int)$info[0]['remark'];
		
		return $this->get_goods_list($b_select_conut=false, $s_id, $limit="0,3", $order_by="place_number ASC", $freeze=null, $where_str="");
	}
	
	/*
	 * ��ȡ��Ʒ�б�
	 */
	function get_goods_list($b_select_conut=false, $issue_id, $limit="0,10", $order_by="place_number ASC", $freeze=null, $where_str="")
	{
		$info = $this->cms_obj->get_record_list_by_issue_id($b_select_conut, $issue_id, $limit, $order_by, $freeze, $where_str);
		foreach($info as $k=>$val)
		{
			$content_arr = explode("|",$val['content']);
			$remark_arr = explode("|",$val['remark']);
			$info[$k]['pc_img'] = $content_arr[0];
			$info[$k]['wap_img'] = $content_arr[1];
			$info[$k]['app_img'] = $content_arr[2];
			
			$info[$k]['star'] = (int)$remark_arr[0];
			$info[$k]['auth'] = $remark_arr[1];
		}
		return $info;
	}
	
	/*
	 * ��ȡ��ҳר���б�
	 */
	function get_topic_list()
	{
		$info = $this->cms_obj->get_record_list_by_issue_id($b_select_conut=false, 10128, $limit="0,100", $order_by="place_number ASC", $freeze=null, $where_str="");
		return $info;
	}

	/*
	 * ��ҳ�����б�
	 */
	function get_comment_list()
	{
		$info = $this->cms_obj->get_record_list_by_issue_id($b_select_conut=false, 10152, $limit="0,3", $order_by="place_number ASC", $freeze=null, $where_str="");
		foreach($info as $k=>$val)
		{
			$info[$k]['user_icon'] = get_user_icon($val['user_id']);
		}
		return $info;
	}
	
	
	/*
	 * ��ȡ����banner
	 */
	function get_banner_list($b_id)
	{
		switch ($b_id)
		{
		//��ģ��
			case 9421 :
				$issue_id = 11793;
				break;
	    //�ҳ���
			case 9424 :
				$issue_id = 11796;
				break;
		//������
			case 9948 :
				$issue_id = 11794;
				break;
		//����ѵ		
			case 9423 :
				$issue_id = 10268;
				break;
		//�һ�ױ	
			case 9425 :
				$issue_id = 11795;
				break;
				
		//��Ӱ����
			case 27927 :
				$issue_id = 27928;
				break;
				 	
			default:
				$issue_id = 11793;
				break;

		}
		$info = $this->cms_obj->get_record_list_by_issue_id($b_select_conut=false, $issue_id, $limit="0,100", $order_by="place_number ASC", $freeze=null, $where_str="");
		foreach($info as $k=>$val)
		{
			$content_arr = explode("|",$val['content']);

			$info[$k]['pc_img'] = $val['img_url'];
			//$info[$k]['wap_img'] = $content_arr[1];
			$info[$k]['app_img'] = $val['img_url'];
			
		}
		return $info;
	}


    /*
     * ���ݵ���ID������ID��ȡBANNER
     */
    function get_banner_type_list($location_id,$type_id)
    {
        $location_id = (int)$location_id;
        $type_id = (int)$type_id;

        $location_id = $location_id ? $location_id : 101029001;
        $type_id = $type_id ? $type_id : 31;

        //����
        $banner_data['101029001']['3'] = 11795;//��ױ����
        $banner_data['101029001']['5'] = 10268;//��Ӱ��ѵ
        $banner_data['101029001']['31'] = 11793;//ģ�ط���
        $banner_data['101029001']['40'] = 27928;//��Ӱ����
        $banner_data['101029001']['12'] = 11796;//Ӱ������
        $banner_data['101029001']['42'] = 11794;//�
        $banner_data['101029001']['41'] = 36370;//Լ��ʳ
        $banner_data['101029001']['43'] = 36502;//Լ����

        //�人
        $banner_data['101019001']['3'] = 35058;//��ױ����
        $banner_data['101019001']['5'] = 35059;//��Ӱ��ѵ
        $banner_data['101019001']['31'] = 35060;//ģ�ط���
        $banner_data['101019001']['40'] = 35061;//��Ӱ����
        $banner_data['101019001']['12'] = 35062;//Ӱ������
        $banner_data['101019001']['42'] = 35063;//�
        $banner_data['101019001']['41'] = 36371;//Լ��ʳ
        $banner_data['101019001']['43'] = 36503;//Լ����

        //����
        $banner_data['101001001']['3'] = 35064;//��ױ����
        $banner_data['101001001']['5'] = 35065;//��Ӱ��ѵ
        $banner_data['101001001']['31'] = 35066;//ģ�ط���
        $banner_data['101001001']['40'] = 35067;//��Ӱ����
        $banner_data['101001001']['12'] = 35068;//Ӱ������
        $banner_data['101001001']['42'] = 35069;//�
        $banner_data['101001001']['41'] = 36372;//Լ��ʳ
        $banner_data['101001001']['43'] = 36504;//Լ����

        //�Ϻ�
        $banner_data['101003001']['3'] = 35070;//��ױ����
        $banner_data['101003001']['5'] = 35071;//��Ӱ��ѵ
        $banner_data['101003001']['31'] = 35072;//ģ�ط���
        $banner_data['101003001']['40'] = 35073;//��Ӱ����
        $banner_data['101003001']['12'] = 35074;//Ӱ������
        $banner_data['101003001']['42'] = 35075;//�
        $banner_data['101003001']['41'] = 36373;//Լ��ʳ
        $banner_data['101003001']['43'] = 36505;//Լ����

        //����
        $banner_data['101004001']['3'] = 35076;//��ױ����
        $banner_data['101004001']['5'] = 35077;//��Ӱ��ѵ
        $banner_data['101004001']['31'] = 35078;//ģ�ط���
        $banner_data['101004001']['40'] = 35079;//��Ӱ����
        $banner_data['101004001']['12'] = 35080;//Ӱ������
        $banner_data['101004001']['42'] = 35081;//�
        $banner_data['101004001']['41'] = 36374;//Լ��ʳ
        $banner_data['101004001']['43'] = 36506;//Լ����

        //�ɶ�
        $banner_data['101022001']['3'] = 35082;//��ױ����
        $banner_data['101022001']['5'] = 35083;//��Ӱ��ѵ
        $banner_data['101022001']['31'] = 35084;//ģ�ط���
        $banner_data['101022001']['40'] = 35085;//��Ӱ����
        $banner_data['101022001']['12'] = 35086;//Ӱ������
        $banner_data['101022001']['42'] = 35087;//�
        $banner_data['101022001']['41'] = 36375;//Լ��ʳ
        $banner_data['101022001']['43'] = 36507;//Լ����

        //����
        $banner_data['101015001']['3'] = 35093;//��ױ����
        $banner_data['101015001']['5'] = 35092;//��Ӱ��ѵ
        $banner_data['101015001']['31'] = 35091;//ģ�ط���
        $banner_data['101015001']['40'] = 35090;//��Ӱ����
        $banner_data['101015001']['12'] = 35089;//Ӱ������
        $banner_data['101015001']['42'] = 35088;//�
        $banner_data['101015001']['41'] = 36376;//Լ��ʳ
        $banner_data['101015001']['43'] = 36508;//Լ����


        //����
        $banner_data['101029002']['3'] = 37061;//��ױ����
        $banner_data['101029002']['5'] = 37058;//��Ӱ��ѵ
        $banner_data['101029002']['31'] = 37059;//ģ�ط���
        $banner_data['101029002']['40'] = 37063;//��Ӱ����
        $banner_data['101029002']['12'] = 37062;//Ӱ������
        $banner_data['101029002']['42'] = 37060;//�
        $banner_data['101029002']['41'] = 37065;//Լ��ʳ
        $banner_data['101029002']['43'] = 37066;//Լ����


        $issue_id = $banner_data[$location_id][$type_id];

        if(empty($issue_id))
        {
            return false;
        }

        $info = $this->cms_obj->get_record_list_by_issue_id($b_select_conut=false, $issue_id, $limit="0,100", $order_by="place_number ASC", $freeze=null, $where_str="");
        foreach($info as $k=>$val)
        {
            $content_arr = explode("|",$val['content']);

            $info[$k]['pc_img'] = $val['content'];
            $info[$k]['app_img'] = $val['img_url'];
            $info[$k]['app_img_v2'] = $val['remark'];
        }
        return $info;

    }
	
	/*
	 * ��ȡ�����İ�
	 */
	function get_category_text($b_id)
	{
		switch ($b_id)
		{
		//��ģ��
			case 9421 :
				$text['top1'] = "��ģ��";
				$text['top2'] = "Ů��ݵ�����ʱԼԼ";
				$text['top3'] = "�����������������������";
				$text['top4'] = "Ů���û���Ķ������ж� ";
				break;
	    //�ҳ���
			case 9424 :
				$text['top1'] = "�ҳ���";
				$text['top2'] = "����ԤԼӰ��";
				$text['top3'] = "Ӱ�������50ƽ�׵���ǧƽ�ײ��ȣ���ͬ���εĵƹⲼ����ԼԼ������ĸ��ִ�����������";
				$text['top4'] = "��𱬵�Ӱ�Ｏ���";
				break;
		//������
			case 9948 :
				$text['top1'] = "������";
				$text['top2'] = "��õ����Ļƽ̨";
				$text['top3'] = "���¡���ࡢ��õ���Ӱ���һ��ƽ̨����ת�������Ļ";
				$text['top4'] = "��õĻ��������";
				break;
		//����ѵ		
			case 9423 :
				$text['top1'] = "����ѵ	";
				$text['top2'] = "ȫ�����ȵ���Ӱ��ѵƽ̨";
				$text['top3'] = "��ʦ�󿧣������γ̣�����ѡ��";
				$text['top4'] = "���ྫ�ʿγ̾���ԼԼ����";
				break;
		//�һ�ױ	
			case 9425 :
				$text['top1'] = "�һ�ױ";
				$text['top2'] = "���ר����ױʦ";
				$text['top3'] = "��ԼԼ��һ˲�������������ô�򵥡�";
				$text['top4'] = "�����漣 �Ӵ˿�ʼ";
				break;
				
		//��Ӱ����
			case 27927 :
				$text['top1'] = "����Ӱ";
				$text['top2'] = "����Լ��Ӱ";
				$text['top3'] = "������Ӱ��ʦ����������ʱ���Ϊ���ṩרҵ��Ӱ����";
				$text['top4'] = "";
				break;
				
			default:
				$text['top1'] = "��ģ��";
				$text['top2'] = "Ů��ݵ�����ʱԼԼ";
				$text['top3'] = "�����������������������";
				$text['top4'] = "Ů���û���Ķ������ж� ";
				break;
		}
		
		return $text;
	}


    /*
	 * ��ȡ�����İ�
	 */
    function get_category_text_by_type_id($type_id)
    {
        switch ($type_id)
        {
            //��ģ��
            case 31 :
                $text['top1'] = "��ģ��";
                $text['top2'] = "Ů��ݵ�����ʱԼԼ";
                $text['top3'] = "�����������������������";
                $text['top4'] = "Ů���û���Ķ������ж� ";
                break;
            //�ҳ���
            case 12 :
                $text['top1'] = "�ҳ���";
                $text['top2'] = "����ԤԼӰ��";
                $text['top3'] = "Ӱ�������50ƽ�׵���ǧƽ�ײ��ȣ���ͬ���εĵƹⲼ����ԼԼ������ĸ��ִ�����������";
                $text['top4'] = "��𱬵�Ӱ�Ｏ���";
                break;
            //������
            case 99 :
                $text['top1'] = "������";
                $text['top2'] = "��õ����Ļƽ̨";
                $text['top3'] = "���¡���ࡢ��õ���Ӱ���һ��ƽ̨����ת�������Ļ";
                $text['top4'] = "��õĻ��������";
                break;
            //����ѵ
            case 5 :
                $text['top1'] = "����ѵ	";
                $text['top2'] = "ȫ�����ȵ���Ӱ��ѵƽ̨";
                $text['top3'] = "��ʦ�󿧣������γ̣�����ѡ��";
                $text['top4'] = "���ྫ�ʿγ̾���ԼԼ����";
                break;
            //�һ�ױ
            case 3 :
                $text['top1'] = "�һ�ױ";
                $text['top2'] = "���ר����ױʦ";
                $text['top3'] = "��ԼԼ��һ˲�������������ô�򵥡�";
                $text['top4'] = "�����漣 �Ӵ˿�ʼ";
                break;

            //��Ӱ����
            case 40 :
                $text['top1'] = "����Ӱ";
                $text['top2'] = "����Լ��Ӱ";
                $text['top3'] = "������Ӱ��ʦ����������ʱ���Ϊ���ṩרҵ��Ӱ����";
                $text['top4'] = "";
                break;


            //Լ��ʳ
            case 41 :
                $text['top1'] = "Լ��ʳ";
                $text['top2'] = "��ʳʢ�磬Լ����";
                $text['top3'] = "Я�ִ��ˣ���������ɫ���ȣ�����ζ��֮�ã�";
                $text['top4'] = "";
                break;

        }

        return $text;
    }
	
	/*
	 * ��ȡ����������Ʒ
	 */
	function get_category_goods($b_id)
	{
		switch ($b_id)
		{
		//��ģ��
			case 9421 :
				$issue_id = 12132;
				break;
	    //�ҳ���
			case 9424 :
				$issue_id = 12107;
				break;
		//������
			case 9948 :
				$issue_id = 12131;
				break;
		//����ѵ		
			case 9423 :
				$issue_id = 12105;
				break;
		//�һ�ױ	
			case 9425 :
				$issue_id = 12106;
				break;
				
			default:
				$issue_id = 12132;
				break;

		}
		
		$info = $this->cms_obj->get_record_list_by_issue_id($b_select_conut=false, $issue_id, $limit="0,3", $order_by="place_number ASC", $freeze=null, $where_str="");
		foreach($info as $k=>$val)
		{
			$content_arr = explode("|",$val['content']);
			$remark_arr = explode("|",$val['remark']);
			$info[$k]['pc_img'] = $content_arr[0];
			$info[$k]['wap_img'] = $content_arr[1];
			$info[$k]['app_img'] = $content_arr[2];
		}
		return $info;
	}


    function get_pc_home_category($location_id)
    {
        include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
        $cms_obj = new cms_system_class();

        $yuepai_arr = array(
            101029001 => 312, //����
            101019001 => 313, //�人
            101001001 => 314, //����
            101003001 => 315, //�Ϻ�
            101004001 => 316, //����
            101022001 => 318, //�ɶ�
            101015001 => 320, //����
            101024001 => 321, //�½�
        );
        $cover_img_arr = array(
            md5('ģ����Լ')=>'http://image19-d.yueus.com/yueyue/cms/20150825/47052015082509265062824369.png',
            md5('��Ӱ����')=>'http://image19-d.yueus.com/yueyue/cms/20150825/4045201508250928369016023.png',
            md5('��Ӱ��ѵ')=>'http://image19-d.yueus.com/yueyue/cms/20150825/82842015082509285947314889.png',
            md5('��ױ����')=>'http://image19-d.yueus.com/yueyue/cms/20150825/14262015082509292126338879.png',
            md5('Ӱ������')=>'http://image19-d.yueus.com/yueyue/cms/20150825/81152015082509295525782751.png',
            md5('Լ��ʳ')=>'http://image19-d.yueus.com/yueyue/20150918/20150918180354_857294_10002_13381.png?320x240_130',

        );

        // ����� ��ť
        $ico_key = isset($yuepai_arr[$location_id]) ? $yuepai_arr[$location_id] : 312;
        $ico_result = $cms_obj->get_last_issue_record_list(false, '0,10', 'place_number ASC', $ico_key);


        $ico_list = array();
        foreach ($ico_result as $value) {
            list($dsmall, $dbig) = explode('|', $value['content']);   // ����ǰ
            list($hsmall, $hbig) = explode('|', $value['remark']);  // ������
            if($value['title']!='���Ļ') {
                $ico_list[] = array(
                    'str' => $value['title'],
                    'url' => $value['link_url'],
                    'img_url' => $cover_img_arr[md5($value['title'])],
                );
            }
        }

        return $ico_list;
    }

    /*
     * 3.1.0��PC��ҳ����
     */
    function get_pc_home_category_3_1_0($location_id)
    {
        include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
        include_once('/disk/data/htdocs232/poco/pai/mall/user/api_rest.php');
        $cms_obj = new cms_system_class();

        $yuepai_arr = array(
            '101029001' => 587, //����
            '101001001' => 703, //����
            '101003001' => 705, //�Ϻ�
            '101022001' => 708, //�ɶ�
            '101004001' => 707, //����
            '101015001' => 710, //����
            '101029002' => 875, //����
        );
        $cover_img_arr = array(
            md5('Լģ��')=>'http://image19-d.yueus.com/yueyue/20151027/20151027114649_160564_10002_24233.png?236x176_130',
            md5('Լ��Ӱ')=>'http://image19-d.yueus.com/yueyue/20151027/20151027114727_907890_10002_24235.png?236x176_130',
            md5('Լ��ѵ')=>'http://image19-d.yueus.com/yueyue/20151027/20151027114748_279059_10002_24238.png?236x176_130',
            md5('Լ��ױ')=>'http://image19-d.yueus.com/yueyue/20151027/20151027114805_19341_10002_24241.png?236x176_130',
            md5('Լ��Ȥ')=>'http://image19-d.yueus.com/yueyue/20151027/20151027114821_33090_10002_24244.jpg?236x176_120',
            md5('Լ��ʳ')=>'http://image19-d.yueus.com/yueyue/20151027/20151027114841_550033_10002_24246.png?236x176_130',
            md5('��ҵ����')=>'http://image19-d.yueus.com/yueyue/20151027/20151027114855_878951_10002_24248.png?236x176_130',
            md5('Լ�')=>'http://image19-d.yueus.com/yueyue/20151027/20151027114909_240924_10002_24249.png?236x176_130',

        );

        // ����� ��ť
        $ico_key = isset($yuepai_arr[$location_id]) ? $yuepai_arr[$location_id] : $yuepai_arr['101029001'];
        $ico_result = $cms_obj->get_last_issue_record_list(false, '0,8', 'place_number ASC', $ico_key);


        $ico_list = array();
        foreach ($ico_result as $value) {
            list($dsmall, $dbig) = explode('|', $value['content']);   // ����ǰ
            list($hsmall, $hbig) = explode('|', $value['remark']);  // ������

            $ico_list[] = array(
                'str' => $value['title'],
                'url' => mall_yueyue_app_to_http($value['link_url']),
                'img_url' => $cover_img_arr[md5($value['title'])],
            );

        }

        return $ico_list;
    }
	
}

?>

