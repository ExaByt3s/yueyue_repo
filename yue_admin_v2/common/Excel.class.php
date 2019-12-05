<?php
/**
 * @desc:   导出excel数据类 格式为csv
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/23
 * @Time:   15:26
 * version: 1.0
 */


/**
 * 导出到excel文件(一般导出中文的都会乱码，需要进行编码转换）
 * 使用方法如下
 * $head = array('列1', '列2', '列3', '列4');
 * $data =  array(
 *   array('数据1','数据2','数据3','数据4'),
 *   array('数据1','数据2','数据3','数据4'),
 *   array('数据1','数据2','数据3','数据4'),
 *   array('数据1','数据2','数据3','数据4')
 *   )
 *  );
 * $filename = '订单表';
 * Excel::start($head,$data,$filename);
 */

class Excel{
    /**
     * @var
     */
    public static  $head;
    /**
     * @var
     */
    public static  $body;
    /**
     * @var string
     */
    public static  $charset = 'GBK';
    /**
     * @var null
     */
    public static  $fp = null;
    /**
     * @var int
     */
    public static $cnt = 0; // 计数器
    /**
     * @var int
     */
    public static $limit = 100000;// 每隔$limit行，刷新一下输出buffer，不要太大，也不要太小

    /**
     * 开始下载
     * @param string $filename
     * @param array $head
     * @param array $body
     */
    public static function start($head,$body,$filename=''){
        if(!$filename)
            $filename = date('YmdHis',time());
        $filename = $filename.'.csv';
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=$filename");
        header('Cache-Control: max-age=0');
        $fp = fopen('php://output', 'a');
        self::$head = $head;
        self::$body = $body;
        self::$fp = $fp;
        self::addHeader();
        self::addBody();
    }

    /**
     *  添加表头
     */
    public function addHeader(){
        if(self::$charset == 'utf-8'){
            self::$head = self::charset(self::$head);
        }
        fputcsv(self::$fp, self::$head);  // 将数据通过fputcsv写到文件句柄
    }

    /**
     *添加内容
     */
    public function addBody(){
        // 逐行取出数据，不浪费内存
        foreach (self::$body as $row) {

            self::$cnt ++;
            if (self::$limit == self::$cnt) { //刷新一下输出buffer，防止由于数据过多造成问题
                ob_flush();
                flush();
                self::$cnt = 0;
            }
            if(self::$charset == 'utf-8'){
                self::$body = self::charset(self::$body);
            }
            fputcsv(self::$fp, $row);
        }
    }
    /**
     * 编码转换
     * @param string|array $string
     * @return string|array
     */
    public function charset($string){
        if( is_string($string) )
        {
            $string = iconv('gbk', 'utf-8', $string);
        }
        elseif( is_array($string) )
        {
            foreach ($string as $key=>$val)
            {
                $str[$key] = gbk_to_utf8($val);
            }
        }
        return $string;

    }
}