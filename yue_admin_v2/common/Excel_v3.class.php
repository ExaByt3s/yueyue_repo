<?php
/**
 * @desc:   ����excel������ ��ʽΪxls
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/23
 * @Time:   15:26
 * version: 1.0
 */
class Excel_v3{
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
     * @var int
     */
    public static $cnt = 0; // ������
    /**
     * @var int
     */
    public static $limit = 100000;// ÿ��$limit�У�ˢ��һ�����buffer����Ҫ̫��Ҳ��Ҫ̫С

    /**
     * @param $head
     * @param $body
     * @param string $filename
     */
    public static function start($head,$body,$filename='')
    {
        if(!$filename)
            $filename = date('YmdHis',time());
        $filename = $filename.'.xls';
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=$filename");
        header("Content-Type:charset=GBK");
        self::addHeader($head);
        self::addBody($body);
        if(self::$head)
            echo self::$head;
        echo self::$body;
    }

    /**
     * @param $arr
     */
    public static function addHeader($arr){
        foreach ($arr as $headVal) {
            if(self::$charset == 'utf-8') $headVal = self::charset($headVal);
            self::$head .= "{$headVal}\t ";
        }
        self::$head .= "\n";
    }

    /**
     * @param $arr
     */
    public static function addBody($arr){
        foreach($arr as $arrBody){
            foreach($arrBody as $bodyVal){
                self::$cnt ++;
                if (self::$limit == self::$cnt) { //ˢ��һ�����buffer����ֹ�������ݹ����������
                    ob_flush();
                    flush();
                    self::$cnt = 0;
                }
                if(self::$charset == 'utf-8') $bodyVal = self::charset($bodyVal);
                self::$body .= "{$bodyVal}\t ";
            }
            self::$body .= "\n";
        }
    }


    /**
     * ����ת��
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


