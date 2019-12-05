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
     * ��ҳ��
     *
     * @var integer
     */
		var $tpage;
   /**
     * �м���ʾҳ���ĵ�����
     *
     * @var integer
     */
		var $middle_ouput;
   /**
     * �м���ʾҳ����������
     *
     * @var integer
     */
		var $middle_page_num;
   /**
     * ֻ����ʾ��ҳ����ʾ��¼��
     *
     * @var integer
     */
		var $p_output;

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

			$this->curr  = $current;
			$this->psize = $pagesize;

			if (!$this->file)
			{
				$url="http://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"];
				if ($_SERVER["QUERY_STRING"]) 
				{
					$url1=explode("&",$_SERVER["QUERY_STRING"]);
					$url2="?".$url1[0];
				}
				$this->file=$url.$url2;
				//$this->file = preg_replace("/\.htx&.*/",".php",$this->file);
			}


			$this->output_total = $total;

			if ($this->tpage > 1) {

				if ($current>5) {
					$this->output_pre10 = '<a href='.$this->file.'&'.$this->pvar.'='.($current-5).($this->varstr).' title="ǰ��ҳ">&lt;&lt;&lt;</a>';
				}
				if ($current>1) {
					$this->output_pre = '<span class="link"><a href='.$this->file.'&'.$this->pvar.'='.($current-1).($this->varstr).' >��һҳ</a></span>';
				}

				$start	= floor($current/5)*5;
				$end	= $start+4;

				if ($start<1)			{$start=1;}
				if ($end>$this->tpage)	{$end=$this->tpage;}

				for ($i=$start; $i<=$end; $i++) {
					if ($current==$i) {
						$this->output_page.='<span class="cuer">'.$i.'</span>';    //�����ǰҳ��

					} else {
						$this->output_page.='<a href="'.$this->file.'&'.$this->pvar.'='.$i.$this->varstr.'" >'.$i.'</a>';    //���ҳ��

					}
				}
                $current_num = 5; 
				$option_tpage = (($current+$current_num)<$this->tpage) ? ($current+$current_num):$this->tpage;

				for ($i=($current-$current_num>0) ? ($current-$current_num) : 1; $i<=$option_tpage ; $i++) {
					if ($current==$i) {
						$this->_output_select_tbl_options.='<option value="'.$i.'" selected>��'.$i.'ҳ</option>';
						$this->middle_page_num.='<font color="red">'.$i.'</font>&nbsp;';
					} else {
						$this->_output_select_tbl_options.='<option value="'.$i.'">��'.$i.'ҳ</option>';
						$this->middle_page_num.='<a href="'.$this->file.'&'.$this->pvar.'='.$i.'">'.$i.'</a>&nbsp;';

					}
				}

				if ($current<$this->tpage) {
					$this->output_back = '<span class="link"><a href='.$this->file.'&'.$this->pvar.'='.($current+1).($this->varstr).' >��һҳ</a></span>';
				}
				if ($this->tpage>5 && ($this->tpage-$current)>=5 ) {
					$this->output_back10 = '<a href='.$this->file.'&'.$this->pvar.'='.($current+5).($this->varstr).' title="����ҳ">&gt;&gt;&gt;</a>';
				}
			}

			$this->output = "<span style=font-size:9pt;>";
			$this->output.= "��¼��:".$this->output_total."&nbsp;&nbsp;";
			$this->output.= "�� ".$this->curr."/".$this->tpage."&nbsp;ҳ&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			$this->output.= $this->output_pre10;
			$this->output.= $this->output_pre;
			$this->output.= $this->output_page;
			$this->output.= $this->output_back;
			$this->output.= $this->output_back10;
			$this->output.= "</span>";

			/*	�м��ҳ */
			
			$this->middle_ouput = "<span style=font-size:9pt;>";
			$this->middle_ouput.= "��¼��:".$this->output_total."&nbsp;&nbsp;";
			$this->middle_ouput.= "�� ".$this->curr."/".$this->tpage."&nbsp;ҳ&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			$this->middle_ouput.= $this->output_pre10;
			$this->middle_ouput.= $this->output_pre;
			$this->middle_ouput.= $this->middle_page_num;
			$this->middle_ouput.= $this->output_back;
			$this->middle_ouput.= $this->output_back10;
			$this->middle_ouput.= "</span>";
			/*	ֻ��ʾ��ҳ */
			

			$this->p_output= $this->output_pre10;
			$this->p_output.= $this->output_pre;
			$this->p_output.= $this->output_page;
			$this->p_output.= $this->output_back;
			$this->p_output.= $this->output_back10;

			
			if ($this->_output_select_tbl_options)
			{
				$this->output_select_tbl="<select onchange=\"javascript:window.location='".
				$this->file."&".$this->pvar."='+this.value+'".$this->varstr."'\">".
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
			foreach ($data as $k=>$v) {
				$this->varstr.='&amp;'.$k.'='.urlencode($v);
			}
		}

		/**
     * ��ҳ������
     *
     * @access public
     * @param bool $return Ϊ��ʱ����һ���ַ���������ֱ�������Ĭ��ֱ�����
     * @return string
     */
		function output($return = false) {
			if ($return) {
				return $this->output;
			} else {
				echo $this->output;
			}
		}
	
		/**
     * �м��ҳ������
     *
     * @access public
     * @param bool $return Ϊ��ʱ����һ���ַ���������ֱ�������Ĭ��ֱ�����
     * @return string
     */
		function middle_output($return = false) {
			if ($return) {
				return $this->middle_ouput;
			} else {
				echo $this->middle_ouput;
			}
		}
			/**
     * ֻ��ʾ��ҳ����ʾ��¼��
     *
     * @access public
     * @param bool $return Ϊ��ʱ����һ���ַ���������ֱ�������Ĭ��ֱ�����
     * @return string
     */
		function p_output($return = false) {
			if ($return) {
				return $this->p_output;
			} else {
				echo $this->p_output;
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
		
		/**
     * ȡ����ǰҳ
     *
     * @access public
     * @return string
     */
		function p_in() {
			return $this->curr;
		}
 
	} //End Class
?>