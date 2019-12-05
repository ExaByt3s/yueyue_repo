<?php
/**
 * 一个用于Mysql数据库的分页类
 *
 * @author      Avenger <avenger@php.net>
 * @version     1.0
 * @lastupdate  2005-12-11 11:11:33
 *
 *
 * 使用实例:
 * $p = new show_page;		//建立新对像
 * $p->file="ttt.php";		//设置文件名，默认为当前页
 * $p->pvar="pagecount";	//设置页面传递的参数，默认为p
 * $p->setvar(array("a" => '1', "b" => '2'));	//设置要传递的参数,要注意的是此函数必须要在 set 前使用，否则变量传不过去
 * $p->set(20,2000,1);		//设置相关参数，共三个，分别为'页面大小'、'总记录数'、'当前页(如果为空则自动读取GET变量)'
 * $p->output(0);			//输出,为0时直接输出,否则返回一个字符串
 * echo $p->limit();		//输出Limit子句。在sql语句中用法为 "SELECT * FROM TABLE LIMIT {$p->limit()}";
 *
 */

if (!class_exists("show_page"))
{
	class show_page {

		/**
     * 页面输出结果
     *
     * @var string
     */
		var $output;

		/**
     * 使用该类的文件,默认为 PHP_SELF
     *
     * @var string
     */
		var $file;

		/**
     * 页数传递变量，默认为 'p'
     *
     * @var string
     */
		var $pvar = "p";

		/**
     * 页面大小
     *
     * @var integer
     */
		var $psize;

		/**
     * 当前页面
     *
     * @var ingeger
     */
		var $curr;

		/**
     * 要传递的变量数组
     *
     * @var array
     */
		var $varstr;

		/**
     * 总页数
     *
     * @var integer
     */
		var $tpage;

		/**
     * 输出页数字符分离变量
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
     * 分页设置
     *
     * @access public
     * @param int $pagesize 页面大小
     * @param int $total    总记录数
     * @param int $current  当前页数，默认会自动读取
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
				$this->file = $HTTP_SERVER_VARS['PHP_SELF'];
				$this->file = preg_replace("/\.htx&.*/",".php",$this->file);
				$this->file =$this->file."?";
			}


			$this->output_total = $total;

			if ($this->tpage > 1) {

				if ($current>10) {
					$this->output_pre10 = '<a href='.$this->file.$this->pvar.'='.($current-10).($this->varstr).' title="前十页">&lt;&lt;&lt;</a>&nbsp;';
				}
				if ($current>1) {
					$this->output_pre = '<a href='.$this->file.$this->pvar.'='.($current-1).($this->varstr).' title="前一页">上一页</a>&nbsp;';
				}

				$start	= floor($current/10)*10;
				$end	= $start+9;

				if ($start<1)			{$start=1;}
				if ($end>$this->tpage)	{$end=$this->tpage;}

				for ($i=$start; $i<=$end; $i++) {
					if ($current==$i) {
						$this->output_page.='&nbsp;<span><font color="red">'.$i.'</font></span>';    //输出当前页数

					} else {
						$this->output_page.='&nbsp;<span><a href="'.$this->file.$this->pvar.'='.$i.$this->varstr.'">'.$i.'</a></span>';    //输出页数

					}
				}

				$option_tpage = (($current+5)<$this->tpage) ? ($current+5):$this->tpage;

				for ($i=($current-5>0) ? ($current-5) : 1; $i<=$option_tpage ; $i++) {
					if ($current==$i) {
						$this->_output_select_tbl_options.='<option value="'.$i.'" selected>第'.$i.'页</option>';
					} else {
						$this->_output_select_tbl_options.='<option value="'.$i.'">第'.$i.'页</option>';

					}
				}

				if ($current<$this->tpage) {
					$this->output_back = '&nbsp;<a href='.$this->file.$this->pvar.'='.($current+1).($this->varstr).' title="下一页">下一页</a>&nbsp;';
				}
				if ($this->tpage>10 && ($this->tpage-$current)>=10 ) {
					$this->output_back10 = '&nbsp;<a href='.$this->file.$this->pvar.'='.($current+10).($this->varstr).' title="下十页">&gt;&gt;&gt;</a>';
				}
			}

			$this->output = "<font style=font-size:9pt;>";
			$this->output.= "记录数:".$this->output_total."&nbsp;&nbsp;";
			$this->output.= "第 ".$this->curr."/".$this->tpage."&nbsp;页&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
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
     * 要传递的变量设置
     *
     * @access public
     * @param array $data   要传递的变量，用数组来表示，参见上面的例子
     * @return void
     */	
		function setvar($data) {
			foreach ($data as $k=>$v) {
				$this->varstr.='&amp;'.$k.'='.urlencode($v);
			}
		}

		/**
     * 分页结果输出
     *
     * @access public
     * @param bool $return 为真时返回一个字符串，否则直接输出，默认直接输出
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
     * 生成Limit语句
     *
     * @access public
     * @return string
     */
		function limit() {
			return (($this->curr-1)*$this->psize).','.$this->psize;
		}

	} //End Class
}
?>
