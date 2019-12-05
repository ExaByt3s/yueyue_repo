<?php
/**
 * @desc:   ����excel������ ��ʽΪcsv
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/23
 * @Time:   15:26
 * version: 1.0
 */


/**
 * ������excel�ļ�(һ�㵼�����ĵĶ������룬��Ҫ���б���ת����
 * ʹ�÷�������
 * $head = array('��1', '��2', '��3', '��4');
 * $data =  array(
 *   array('����1','����2','����3','����4'),
 *   array('����1','����2','����3','����4'),
 *   array('����1','����2','����3','����4'),
 *   array('����1','����2','����3','����4')
 *   )
 *  );
 * $filename = '������';
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
    public static $cnt = 0; // ������
    /**
     * @var int
     */
    public static $limit = 100000;// ÿ��$limit�У�ˢ��һ�����buffer����Ҫ̫��Ҳ��Ҫ̫С

    /**
     * ��ʼ����
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
     *  ��ӱ�ͷ
     */
    public function addHeader(){
        if(self::$charset == 'utf-8'){
            self::$head = self::charset(self::$head);
        }
        fputcsv(self::$fp, self::$head);  // ������ͨ��fputcsvд���ļ����
    }

    /**
     *�������
     */
    public function addBody(){
        // ����ȡ�����ݣ����˷��ڴ�
        foreach (self::$body as $row) {

            self::$cnt ++;
            if (self::$limit == self::$cnt) { //ˢ��һ�����buffer����ֹ�������ݹ����������
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