<?php
/**
 * һ������Mysql���ݿ�ķ�ҳ��
 *
 * @author      Avenger <avenger@php.net>
 * @version     1.0
 * @lastupdate  2005-12-11 11:11:33
 *
 *
 * ʹ��ʵ��:
 * $p = new show_page;		//�����¶���
 * $p->file="ttt.php";		//�����ļ�����Ĭ��Ϊ��ǰҳ
 * $p->pvar="pagecount";	//����ҳ�洫�ݵĲ�����Ĭ��Ϊp
 * $p->setvar(array("a" => '1', "b" => '2'));	//����Ҫ���ݵĲ���,Ҫע����Ǵ˺�������Ҫ�� set ǰʹ�ã��������������ȥ
 * $p->set(20,2000,1);		//������ز��������������ֱ�Ϊ'ҳ���С'��'�ܼ�¼��'��'��ǰҳ(���Ϊ�����Զ���ȡGET����)'
 * $p->output(0);			//���,Ϊ0ʱֱ�����,���򷵻�һ���ַ���
 * echo $p->limit();		//���Limit�Ӿ䡣��sql������÷�Ϊ "SELECT * FROM TABLE LIMIT {$p->limit()}";
 *
 */
if (!class_exists("show_page"))
{
	class show_page {

		/**
     * ҳ��������
     *
     * @var string
     */
		var $output;

		/**
     * ʹ�ø�����ļ�,Ĭ��Ϊ PHP_SELF
     *
     * @var string
     */
		var $file;

		/**
     * ҳ�����ݱ�����Ĭ��Ϊ 'p'
     *
     * @var string
     */
		var $pvar = "p";

		/**
     * ҳ���С
     *
     * @var integer
     */
		var $psize;

		/**
     * ��ǰҳ��
     *
     * @var ingeger
     */
		var $curr;

		/**
     * Ҫ���ݵı�������
     *
     * @var array
     */
		var $varstr;

    /**
     * �Ƿ���ʾ���һҳ����
     * 
     * @var $show_last
     */
     
     var $show_last;

		/**
     * ��ҳ��
     *
     * @var integer
     */
		var $tpage;

		/**
     * ���ê��
     *
     * @var string
     */
		var $hashstr;
		
	/**
	 * onclick�¼���
	 *
	 * @var string
	 */
		var $onclick;

		/**
     * ���ҳ���ַ��������
     *
     * @var integer
     */
		var $output_total;
		var $output_pre10;
		var $output_pre;
		var $output_page;
		var $output_back;
		var $output_back10;
		var $output_select_tbl;
		var $_output_select_tbl_options;
		var $_output_select_tbl_js;
		
		var $_parse_url_dot_html = false;
		var $_parse_url_to_dir = false;

		/**
     * ��ҳ����
     *
     * @access public
     * @param int $pagesize ҳ���С
     * @param int $total    �ܼ�¼��
     * @param int $current  ��ǰҳ����Ĭ�ϻ��Զ���ȡ
     * @return void
     */
		function set($pagesize=20,$total,$current=false) {
			global $HTTP_SERVER_VARS,$HTTP_GET_VARS;

			$this->tpage = ceil($total/$pagesize);
			if (!$current) {$current = $HTTP_GET_VARS[$this->pvar];}
			if ($current>$this->tpage) {$current = $this->tpage;}
			if ($current<1) {$current = 1;}
			if ($this->hashstr) { $this->varstr = $this->varstr."#".$this->hashstr;}

			$this->curr  = $current;
			$this->psize = $pagesize;
			
			

			if (!$this->file)
			{
				if (!$this->_parse_url_dot_html) 
				{
					$this->file = $HTTP_SERVER_VARS['PHP_SELF'];
				}
				else 
				{
					$this->file = basename($HTTP_SERVER_VARS['SCRIPT_FILENAME']);
				}
				
				$this->file = preg_replace("/\.htx&.*/",".php",$this->file);
				$this->file =$this->file."?";

				if (!defined("G_DB_GET_REALTIME_DATA"))
				{
					//$this->file = preg_replace("/\.php\?.*/",".htx&",$this->file);
				}
			}


			$this->output_total = $total;

			if ($this->tpage > 1) {

				if ($current>10) {
					$this->output_pre10 = '<a href='.$this->pares_url_to_dot_html($this->file.$this->pvar.'='.($current-10).($this->varstr)).' '.$this->_get_onclick(($current-10)).'  title="ǰʮҳ">&lt;&lt;&lt;</a>';
				}
				if ($current>1) {
					$this->output_pre = '<a href='.$this->pares_url_to_dot_html($this->file.$this->pvar.'='.($current-1).($this->varstr)).' '.$this->_get_onclick(($current-1)).' title="ǰһҳ">��һҳ</a>';
				}

				$start	= floor($current/10)*10;
				$end	= $start+9;

				if ($start<1)			{$start=1;}
				if ($end>$this->tpage)	{$end=$this->tpage;}

				for ($i=$start; $i<=$end; $i++) {
					if ($current==$i) {
						$this->output_page.='<a class="click">'.$i.'</a>';    //�����ǰҳ��

					} else {
						$this->output_page.='<a href="'.$this->pares_url_to_dot_html($this->file.$this->pvar.'='.$i.$this->varstr).'" '.$this->_get_onclick($i).'>'.$i.'</a>';    //���ҳ��

					}
				}

                if( ($this->tpage > $end) && ($this->show_last == 1) )
				{
                    $this->output_page .= '<span class="dian-more color2">������</span><a href="'.$this->pares_url_to_dot_html($this->file.$this->pvar.'='.$this->tpage.$this->varstr).'">'.$this->tpage.'</a>';
				}

				$option_tpage = (($current+5)<$this->tpage) ? ($current+5):$this->tpage;

				for ($i=($current-5>0) ? ($current-5) : 1; $i<=$option_tpage ; $i++) {
					if ($current==$i) {
						$this->_output_select_tbl_options.='<option value="'.$i.'" selected>��'.$i.'ҳ</option>';
					} else {
						$this->_output_select_tbl_options.='<option value="'.$i.'">��'.$i.'ҳ</option>';

					}
				}

				if ($current<$this->tpage) {
					$this->output_back = '<a href='.$this->pares_url_to_dot_html($this->file.$this->pvar.'='.($current+1).($this->varstr)).' '.$this->_get_onclick(($current+1)).' title="��һҳ">��һҳ</a>';
				}
				if ($this->tpage>10 && ($this->tpage-$current)>=10 ) {
					$this->output_back10 = '<a href='.$this->pares_url_to_dot_html($this->file.$this->pvar.'='.($current+10).($this->varstr)).' '.$this->_get_onclick(($current+10)).' title="��ʮҳ">&gt;&gt;&gt;</a>';
				}
			}

			$this->output = "<font style=font-size:9pt;>";
			$this->output.= "��¼��:".$this->output_total."&nbsp;&nbsp;";
			$this->output.= "�� ".$this->curr."/".$this->tpage."&nbsp;ҳ&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			$this->output.= $this->output_pre10;
			$this->output.= $this->output_pre;
			$this->output.= $this->output_page;
			$this->output.= $this->output_back;
			$this->output.= $this->output_back10;
			$this->output.= "</font>";

			if ($this->_output_select_tbl_options)
			{
				$this->output_select_tbl="<select onchange=\"javascript:window.location='".
				$this->file.$this->pvar."='+this.value+'".$this->varstr."'\">".
				$this->_output_select_tbl_options."</select>";
			}
		}

		/**
     * Ҫ���ݵı�������
     *
     * @access public
     * @param array $data   Ҫ���ݵı���������������ʾ���μ����������
     * @return void
     */	
		function setvar($data) {
			
			$and = ($this->_parse_url_dot_html || $this->_parse_url_to_dir) ? '&' : '&amp;' ;
			
			foreach ($data as $k=>$v) {
				$this->varstr.=$and.$k.'='.urlencode($v);
			}
		}

		/**
     * Ҫ���ݵ�ê������
     *
     * @access public
     * @param array $data   Ҫ����ê������
     * @return void
     */	
		function sethash($str) {
				$this->hashstr = $str;
		}
		
		function setonclick($str)
		{
			$this->onclick = $str;
		}
		
		function _get_onclick($page)
		{
			return ($this->onclick) ? "onclick=\"".$this->onclick."('{$page}', '{$this->varstr}')\"" : '';
		}
		

		/**
     * ��ҳ������
     *
     * @access public
     * @param bool $return Ϊ��ʱ����һ���ַ���������ֱ�������Ĭ��ֱ�����
     * @return string
     */
		function output($return = false, $no_pagetotal = false) {
			
			$out = $this->output;

			if($no_pagetotal)
			{
				$out = preg_replace("/��¼��:[\d]*&nbsp;&nbsp;/", '', $out);
			}

			if ($return) {
				return $out;
			} else {
				echo $out;
			}
		}

		/**
     * ����Limit���
     *
     * @access public
     * @return string
     */
		function limit() {
			return (($this->curr-1)*$this->psize).','.$this->psize;
		}
		
		function set_pares_url_to_dot_html($bool)
		{
			$this->_parse_url_dot_html = $bool;
		}

		function set_pares_url_to_dir($file_path)
		{
			$this->_parse_url_to_dir = true;
			$this->file = $file_path.'?';
		}

		/**
		 * �Ż�һ��ȫվ��ͨ�þ�̬����
		 *
		 * @param string $url
		 * @return unknown
		 */
		function pares_url_to_dot_html($url)
		{
			if ($this->_parse_url_dot_html) 
			{
				$query_str = '';
				$ret = '';
				$url = str_replace('htx&','php?',$url);
				$url_info = parse_url($url);
				
				extract($url_info);

				if(empty($query)) return $url;
				$path = str_replace('.php','-upi-',$path);
				parse_str($query, $query_arr);
				
				foreach ($query_arr as $param=>$value)
				{
					$query_str_arr[]= "{$param}-".urlencode($value);
				}
				
				$query_str = implode('-', $query_str_arr);

				return "{$path}{$query_str}.html".$fragment;
			}
			elseif($this->_parse_url_to_dir)
			{
				$query_str = '';
				$ret = '';
				$url = str_replace('htx&','php?',$url);
				$url_info = parse_url($url);

				extract($url_info);

				if(empty($query)) return $url;
				$path = str_replace('.php','/',$path);
				parse_str($query, $query_arr);

				foreach ($query_arr as $param=>$value)
				{
					$query_str_arr[]= "{$param}/".urlencode($value);
				}
				
				$query_str = implode('/', $query_str_arr);

				return "{$path}{$query_str}".$fragment;
			}
			else
			{
				return $url;
			}
		}

	} //End Class
}
?>