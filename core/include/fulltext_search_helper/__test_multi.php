<?
include_once("fulltext_search_helper_core_class.inc.php");

$index_obj = new fulltext_search_helper_core_class();

	$wheres = "���ν��";	//��ѯ�������text=giggs,credit_point>0


$search_args_arr[] = array(
	"indexname" => 100,				//������
	"fields"	=> "timer,peers,link,size,name",				//Ĭ����*ȫ���ֶΣ�Ҫ�����ֶ�д�ϸ��ֶ�
	"where"		=> "text={$wheres}",//��ѯ�������text=giggs,credit_point>0
	"order"		=> "peers#DESC",			//�������������� credit_point desc
	"limit"		=> "0,2" 			//Ҫ����ȫ��������� 0,100
	);

	$search_args_arr[] = array(
	"indexname" => 113,				//������
	"fields"	=> "fname,type,link,size,itime,resnum",				//Ĭ����*ȫ���ֶΣ�Ҫ�����ֶ�д�ϸ��ֶ�
	"where"		=> "text={$wheres}",//��ѯ�������text=giggs,credit_point>0
	"order"		=> "resnum#DESC",			//�������������� credit_point desc
	"limit"		=> "0,2" 			//Ҫ����ȫ��������� 0,100
	);


	$index_obj = new fulltext_search_helper_core_class();
	$res_arr = $index_obj->MultiSelect($search_args_arr);


	var_dump($res_arr);
?>