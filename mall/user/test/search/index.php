<?php
include_once 'config.php';


// ========================= ��ʼ���ӿ� start =======================

$type_id = trim($_INPUT['type_id']);
$query = trim($_INPUT['query']);
$keywords = trim($_INPUT['keywords']);
$search_type = trim($_INPUT['search_type']);
$page = intval($_INPUT['p']);
$a_key = $_INPUT['a_key'];
$b_key = trim($_INPUT['b_key']);
$orderby = intval($_INPUT['orderby']);
if (empty($main_title )) 
{
    $main_title = '����';
}

//��ҳ�������⴦�� ��Ϊ����
$detail_data = array();
$third_data = array();
if( ! empty($_GET['detail']) && ! is_array($_GET['detail']) && $_GET['for_page'] == 1 )
{
    
    $_GET['detail'] = urldecode($_GET['detail']);
    
    $detail_ll = explode('_',$_GET['detail']);
    
    if( ! empty($detail_ll) )
    {
        
        foreach($detail_ll as $k => $v)
        {
            
            $detail_parent_child_ll = explode(',',$v);
            if( ! empty($detail_parent_child_ll) )
            {
                $detail_data[$detail_parent_child_ll['0']] = $detail_parent_child_ll['1'];
            }
        } 
    } 

   $_GET['detail'] = $detail_data;
   
}

if( ! empty($_GET['third']) && ! is_array($_GET['third']) &&  $_GET['for_page'] == 1 )
{
    $_GET['third'] = urldecode($_GET['third']);
    $third_ll = explode('_',$_GET['third']);
    if( ! empty($third_ll) )
    {
        foreach($third_ll as $k => $v)
        {
            $third_parent_child_ll = explode(',',$v);
            if( ! empty($third_parent_child_ll) )
            {
                $third_data[$third_parent_child_ll['0']] = $third_parent_child_ll['1'];
            }
        } 
    } 
    
   $_GET['third'] = $third_data;
}


/*************************** ͳһ����PC��WAP��ɸѡ�������ݸ�ʽ ***************************/

// �ռ�����
if(empty($page))
{
  $page = 1;
}

if(empty($orderby))
{
  $orderby = '-1';
}

if(!empty($_INPUT['keyword']))
{
  $keywords = $_INPUT['keyword'];
}

// ֻ��ȡ40����
$keywords = mall_search_str_cut($keywords,80,'');
$filter = $_GET;
$filter['keywords'] = $keywords;
$filter['location_id'] = $_COOKIE['yue_location_id'] ? $_COOKIE['yue_location_id'] : 101029001;
$is_seller = ($search_type=='seller');

// ɸѡ ��������
$type_obj = POCO::singleton('pai_mall_goods_type_attribute_class');

if($_GET['m_cup'][0] == 'E')
{
    $_GET['m_cup'] = 'E+';
}

$is_use_filter = false;
// ��������ɸѡ����
if(!$is_seller)
{
  // �������ͻ�ȡɸѡ���� //true������Ի���
  $filter_data = $type_obj->property_for_search_get_data($type_id,true);
  //�����Ӱ��ѵ��������ֵ�Ͳ���ʾ
  if( ! $_GET['third']['383'] || $_GET['detail']['382']!='383' )
  {
      foreach($filter_data as $k=> $v)
      {
          if($v['name'] == 'detail[400]')
          {
              unset($filter_data[$k]);
          }  
      }  
  }
  //�������ģ�ص�������ֵ�Ͳ���ʾ
  if( ! $_GET['third']['385'] || $_GET['detail']['382'] !='385' )
  {
      foreach($filter_data as $k => $v)
      {
          if($v['name'] == 'detail[402]')
          {
              unset($filter_data[$k]);
          }  
      }   
  }
  
  
  
   
  // ��һ��ɸѡ�������
  $filter_data_length = count($filter_data);


  // ѭ����һ��ɸѡ����
  foreach($filter_data as $key => $val)
  {
    $_GET['p'] = 1;
    $url_array = $_GET;
    
    $detial_name = $val['name'];

    if(!empty($val['data']))
    {
      $child_filter_a = $val['data'];
      
      // ѭ���ڶ���ɸѡ����
      
      $i_temp_val = '';
      foreach($child_filter_a as $i_key => $i_val)
      {
        
        $url_array[$detial_name] = $i_val['key'];                                       
        // ������ת����       
        $detial_name_tmp = explode('[',$detial_name);
        if($detial_name_tmp[0] and $detial_name_tmp[1])
        {
            $detial_name_tmp[1] = str_replace(']','',$detial_name_tmp[1]);          
            $temp_detail_val = $url_array[$detial_name_tmp[0]][$detial_name_tmp[1]];                          
            !$i_temp_val?$i_temp_val = $url_array[$detial_name_tmp[0]][$detial_name_tmp[1]]:"";

            unset($url_array[$detial_name_tmp[0]][$detial_name_tmp[1]]);
            //unset($url_array['third']);

            // ���ø���
            if( $i_val['key'] == $i_temp_val)
            {
                $filter_data[$key]['selected_key'] = $i_val['key'];
                $filter_data[$key]['selected_name'] = $detial_name;
                $filter_data[$key]['data'][$i_key]['selected'] = true;
                $filter_data[$key]['selected_content'] = $i_val['val'];


            }


        }
        else
        {
            $temp_detail_val = $_GET[$detial_name];

            // ���ø���
            if( $url_array[$detial_name] == $temp_detail_val || $url_array[$detial_name] == 'E' && $temp_detail_val == 'E+')
            {

                $filter_data[$key]['data'][$i_key]['selected'] = true;
                $filter_data[$key]['selected_key'] = urlencode($i_val['key']);
                $filter_data[$key]['selected_name'] = $detial_name;
                $filter_data[$key]['selected_content'] = $i_val['val'];

            }

        }

        // ת����һ��ɸѡ����                  
        $filter_data[$key]['data'][$i_key]['link'] = './index.php?'. urldecode(http_build_query($url_array));
        $filter_data[$key]['data'][$i_key]['rel_key'] = urlencode($i_val['key']);

        // ==============================

        if(!empty($i_val['child_data']['data']))
        {
          $child_filter_b = $i_val['child_data']['data']; 
          $detail_name_de = $i_val['child_data']['name'];     

          foreach($child_filter_b as $j_key => $j_val)
          {
            
            //$url_array['b_key'] = $j_val['key'];
            $url_array[$detail_name_de] = $j_val['key'];
            /////////////////////////////
            $detial_name_tmp_de = explode('[',$detail_name_de);
            if($detial_name_tmp_de[0] and $detial_name_tmp_de[1])
            {
              $detial_name_tmp_de[1] = str_replace(']','',$detial_name_tmp_de[1]);

              $j_temp_val = $_GET[$detial_name_tmp_de[0]][$detial_name_tmp_de[1]];


              unset($url_array[$detial_name_tmp_de[0]][$detial_name_tmp_de[1]]);
            }
            
            
            // ������ת����
            $filter_data[$key]['data'][$i_key]['child_data']['data'][$j_key]['link'] = './index.php?'. urldecode(http_build_query($url_array));
            // ���ø���
            if($j_val['key'] == $j_temp_val)
            {
                $filter_data[$key]['data'][$i_key]['child_data']['data'][$j_key]['selected'] = true;
                if(!empty($j_val['key']))
                {
                    $filter_data[$key]['selected_third_key'] = urlencode($j_val['key']);
                    $filter_data[$key]['selected_third_name'] = $detail_name_de;
                    $filter_data[$key]['selected_content'] = $i_val['val'].'/'.$j_val['val'];
                    $filter_data[$key]['data'][$i_key]['child_data']['is_show_child_data'] = true;


                }



            }
            
            unset($url_array[$detail_name_de]);             
          }    
           

          // ������ڵ�һ��ɸѡ���ͽ��ڶ�����ɸѡ���ݸ�ʽ���ڵ�һ������
          if($i_temp_val == $i_val['key'])
          {
            $child_filter_b_temp = $filter_data[$key]['data'][$i_key]['child_data'];
            $child_filter_b_temp['text'] = $i_val['val'];
          }
        }
        
        
      }
    }
  }

  $temp_filter_data = $filter_data;
  if(!empty($temp_filter_data))
  {

      foreach($temp_filter_data as $key => $val)
      {
          if(!empty($val['selected_key']))
          {
              $is_use_filter = true;
              break;
          }

      }
  }


  
}


/*************************** ͳһ����PC��WAP�����ݸ�ʽ END ***************************/

/**
 * ��ȡָ������
 * @param  [type] $string [description]
 * @param  [type] $length [description]
 * @param  string $dot    [description]
 * @return [type]         [description]
 */
function mall_search_str_cut($string, $length, $dot = '...') 
{
 $strlen = strlen($string);
 if($strlen <= $length) return $string;
 $string = str_replace(array(' ','&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array('��',' ', '&', '"', "'", '��', '��', '��', '<', '>', '��', '��'), $string);
 $strcut = '';
 if(strtolower(CHARSET) == 'utf-8') {
  $length = intval($length-strlen($dot)-$length/3);
  $n = $tn = $noc = 0;
  while($n < strlen($string)) {
   $t = ord($string[$n]);
   if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
    $tn = 1; $n++; $noc++;
   } elseif(194 <= $t && $t <= 223) {
    $tn = 2; $n += 2; $noc += 2;
   } elseif(224 <= $t && $t <= 239) {
    $tn = 3; $n += 3; $noc += 2;
   } elseif(240 <= $t && $t <= 247) {
    $tn = 4; $n += 4; $noc += 2;
   } elseif(248 <= $t && $t <= 251) {
    $tn = 5; $n += 5; $noc += 2;
   } elseif($t == 252 || $t == 253) {
    $tn = 6; $n += 6; $noc += 2;
   } else {
    $n++;
   }
   if($noc >= $length) {
    break;
   }
  }
  if($noc > $length) {
   $n -= $tn;
  }
  $strcut = substr($string, 0, $n);
  $strcut = str_replace(array('��', '&', '"', "'", '��', '��', '��', '<', '>', '��', '��'), array(' ', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), $strcut);
 } else {
  $dotlen = strlen($dot);
  $maxi = $length - $dotlen - 1;
  $current_str = '';
  $search_arr = array('&',' ', '"', "'", '��', '��', '��', '<', '>', '��', '��','��');
  $replace_arr = array('&amp;','&nbsp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;',' ');
  $search_flip = array_flip($search_arr);
  for ($i = 0; $i < $maxi; $i++) {
   $current_str = ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
   if (in_array($current_str, $search_arr)) {
    $key = $search_flip[$current_str];
    $current_str = str_replace($search_arr[$key], $replace_arr[$key], $current_str);
   }
   $strcut .= $current_str;
  }
 }
 return $strcut.$dot;
}
// ========================= ��ʼ���ӿ� end =======================


 
// ========================= ����pc��wapģ�������ݸ�ʽ���� start  =======================
if(MALL_UA_IS_PC == 1)
{

	
	include_once './index-pc.php';

    //****************** pc�� ******************
    


}
else
{
    //****************** wap�� ******************
    include_once './index-wap.php';
}


//****************** pc��ͷ��ͨ�� start ******************



// ========================= ����ģ�����  =======================
$tpl->output();
?>
