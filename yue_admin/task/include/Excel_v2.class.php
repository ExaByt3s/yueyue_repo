<?php
/**
 * @desc:   导出excel数据类 格式为xls
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/23
 * @Time:   15:26
 * version: 1.0
 */
/*$head = array(
     array('title'=> '列1','width'=>200),
     array('title'=> '列2','width'=>200),
     array('title'=> '列3','width'=>200),
     array('title'=> '列4','width'=>200)
);
$data = array(
    array('2014-04-06 12:12:15','2014-04-06 12:12:15','2014-04-06 12:12:15','2014-04-06 12:12:15'),
    array('2014-04-06 12:12:15','2014-04-06 12:12:15','2014-04-06 12:12:15','2014-04-06 12:12:15'),
    array('2014-04-06 12:12:15','2014-04-06 12:12:15','2014-04-06 12:12:15','2014-04-06 12:12:15'),
    array('2014-04-06 12:12:15','2014-04-06 12:12:15','2014-04-06 12:12:15','2014-04-06 12:12:15')
);
Excel_v3::start($head,$data,'手机','s3');*/

class Excel_v2{
    /**
     * excel文档头(返回的行)
     *
     * 依照excel xml规范。
     * @access private
     * @var string
     */
    private  static $header = "<?xml version=\"1.0\" encoding=\"GBK\"?\>
	<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\"
	xmlns:x=\"urn:schemas-microsoft-com:office:excel\"
	xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\"
	xmlns:html=\"http://www.w3.org/TR/REC-html40\">";
    /**
     * excel页脚
     * 依照excel xml规范。
     *
     * @access private
     * @var string
     */
    private static $footer = "</Workbook>";

    /**
     * 文档行(行数组中)
     *
     * @access private
     * @var array
     */
    private static $lines = array ();

    private static $column = "";

    /**
     * 工作表(数组)
     *
     * @access private
     * @var array
     */
    private static $worksheets = array ();
    /**
     * 单元格样式
     * @access private
     * @var string
     */
    private static $cellstyle = array(
        's1'=>'<Style ss:ID="s1"><Alignment ss:Horizontal="Center" ss:WrapText="1"/><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/><Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/><Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/></Borders><Font ss:FontName="Tahoma" x:CharSet="134" ss:Size="11"/><Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/></Style>',
        's2'=>'<Style ss:ID="s2"><Alignment ss:Horizontal="Center" ss:WrapText="1"/><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/><Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/><Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/></Borders><Font ss:FontName="宋体" x:CharSet="134" ss:Size="11"/><Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/></Style>',
        's3'=>'<Style ss:ID="s3"><Alignment ss:Horizontal="Center" ss:WrapText="1"/><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/><Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/><Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/></Borders><Font ss:FontName="宋体" x:CharSet="134" ss:Size="11" ss:Color="#FF0000"/><Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/></Style>'
    );

    /**
     * 默认单元格数据格式
     * @access private
     * @var string
     */
    private static $default_cellformat = "String";

    private static  $default_headStyle ="s1";
    private static  $default_bodyStyle ="s2";
    /**
     * @var int
     */
    private static $cnt = 0; // 计数器
    /**
     * @var int
     */
    private static $limit = 100000;// 每隔$limit行，刷新一下输出buffer，不要太大，也不要太小

    public static function start($head,$body,$filename='',$headStyle='',$bodyStyle='')
    {
        if(!$filename)
            $filename = date('YmdHis',time());
        $encoded_filename = urlencode($filename);
        $encoded_filename = str_replace("+", "%20", $encoded_filename);
        //头
        $ua = $_SERVER["HTTP_USER_AGENT"];
        header("Content-Type: application/vnd.ms-excel");
        if(preg_match("/MSIE/", $ua)){
            header('Content-Disposition: attachment; filename="'.$encoded_filename.'.xls"');
        }else if(preg_match("/Firefox/", $ua)){
            header('Content-Disposition: attachment; filename*="GBK\'\''.$filename.'.xls"');
        }else{
            header('Content-Disposition: attachment; filename="'.$filename.'.xls"');
        }
        header('Cache-Control: max-age=0');
        if(strlen($headStyle) >1) self::$default_headStyle = $headStyle;
        if(strlen($bodyStyle) >1) self::$default_bodyStyle = $bodyStyle;
        self::addHeader($head,$headStyle);
        self::addBody($body,$bodyStyle);
        self::addWorksheet($filename);
        echo stripslashes (self::$header);
        //样式
        echo "\n<Styles>";
        foreach((array)self::$cellstyle as $k=>$v){
            echo "\n".$v;
        }
        echo "\n</Styles>";
        echo implode ("\n", self::$worksheets);
        echo self::$footer;
    }

    public static function addWorksheet($sheettitle)
    {
        //剔除特殊字符
        $sheettitle = preg_replace ("/[\\\|:|\/|\?|\*|\[|\]]/", "", $sheettitle);
        //现在,将其减少到允许的长度
        $sheettitle = substr ($sheettitle, 0, 50);
        self::$worksheets[] = "\n<Worksheet ss:Name=\"$sheettitle\">\n<Table ss:DefaultRowHeight=\"20\">\n".
            self::$column."\n".
            implode ("\n", self::$lines).
            "</Table>\n</Worksheet>\n";
    }
    public static function addHeader($array){
        $cells = '';
        $column = '';
        $i = 1;
        $style_str = 'ss:StyleID="'.self::$default_headStyle.'"';
        foreach ($array as $k => $v){
            $width = 200;
            $title = '';
            if(is_array($v)){//是数组的时候
                $title = trim($v['title']);
                $width = intval($v['width']) == 0 ? 200:intval($v['width']);
            }else{
                $title = trim($v);
            }
            $format_str = self::$default_cellformat;
            $cells .= "<Cell {$style_str}><Data ss:Type=\"{$format_str}\">{$title}</Data></Cell>\n";
            $column .= "<Column ss:Index=\"{$i}\" ss:AutoFitWidth=\"0\" ss:Width=\"{$width}\"/>";
            $i++;
        }
        //添加表头进数据中
        self::$lines[] = "<Row>\n" . $cells . "</Row>\n";
        self::$column = $column;
    }

    /**
     * 添加单行数据
     *
     * @access private
     * @param array 1维数组
     * @todo 行创建
     */
    private static function addRow ($array)
    {
        //初始化单元格
        $cells = "";
        //构建单元格
        $style_str = 'ss:StyleID="'.self::$default_bodyStyle.'"';
        $format_str = self::$default_cellformat;
        foreach ($array as $k => $v){
            $v= htmlspecialchars($v,ENT_COMPAT);
            $cells .= "<Cell {$style_str}><Data ss:Type=\"{$format_str}\">{$v}</Data></Cell>\n";
        }
        //构建行数据
        self::$lines[] = "<Row>\n" . $cells . "</Row>\n";
    }

    public static function addBody($arr){
        foreach($arr as $arrBody){
            self::$cnt ++;
            if (self::$limit == self::$cnt) { //刷新一下输出buffer，防止由于数据过多造成问题
                ob_flush();
                flush();
                self::$cnt = 0;
            }
            self::addRow($arrBody);
        }
    }

    /**
     * 转码函数
     *
     * @param mixed $content
     * @param string $from
     * @param string $to
     * @return mixed
     */
    public function charset($content, $from='gbk', $to='utf-8') {
        $from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
        $to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
        if (strtoupper($from) === strtoupper($to) || empty($content)) {
            //如果编码相同则不转换
            return $content;
        }
        if (function_exists('mb_convert_encoding')) {
            if (is_array($content)){
                $content = var_export($content, true);
                $content = mb_convert_encoding($content, $to, $from);
                eval("\$content = $content;");return $content;
            }else {
                return mb_convert_encoding($content, $to, $from);
            }
        } elseif (function_exists('iconv')) {
            if (is_array($content)){
                $content = var_export($content, true);
                $content = iconv($from, $to, $content);
                eval("\$content = $content;");return $content;
            }else {
                return iconv($from,$to,$content);
            }
        } else {
            return $content;
        }
    }
}


