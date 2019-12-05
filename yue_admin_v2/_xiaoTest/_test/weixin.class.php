<?php
/**
 * Desc:      
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/15 0015
 * @Time:   20:48
 * @Version: 1.0
 */
class weixin
{
    /**
     * @var
     */
    private $_appid;
    /**
     * @var
     */
    private $_appsecret;
    /**
     * @var string
     */
    private $_asscess_token;

    /**
     * @param $_appid
     * @param $_appsecret
     * @param string $_asscess_token
     */
    public function __construct($_appid,$_appsecret,$_asscess_token = ''){
        $this->_appid = $_appid;
        $this->_appsecret = $_appsecret;
        $this->_asscess_token = $_asscess_token;
    }
    /**
     * 发出HTTP请求
     * @param string $url
     * @param string $method GET|POST
     * @param string|array $postfields 这个参数可以通过urlencoded后的字符串类似'para1=val1&para2=val2&...'或使用一个以字段名为键值，字段数据为值的数组。如果value是一个数组，Content-Type头将会被设置成multipart/form-data。
     * @param array $headers 例如 array('Content-type: text/plain', 'Content-length: 100')
     * @return string
     */
    private function http($url, $method, $postfields=null, $headers=null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        if( is_array($headers) )
        {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if($method == 'POST')
        {
            curl_setopt($ch, CURLOPT_POST, true);
            if( !empty($postfields) )
            {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
            }
        }

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    /**
     * 获取asscess_token
     * @return bool|string
     */
    public function _get_asscess_token()
    {
        /*$file_path = './assess_token.txt';
        if(file_exists($file_path)) { //从文件中获取assess_token
            $content = file_get_contents($file_path);
            $ret = json_decode($content,true);
            $expires_in = intval($ret['expires_in']);
            if(time()-$expires_in <= fileatime($file_path)) {
                return trim($ret['access_token']);
            }
        }*/
        $curl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->_appid."&secret=".$this->_appsecret;
        $content = $this->http($curl, "GET");
        $ret = json_decode($content,true);
        $asscess_token = trim($ret['access_token']);
        if(strlen($asscess_token) >1)
        {
            //file_put_contents($file_path,$content);
            return $asscess_token;
        }else
        {
            return false;
        }
    }

    /**
     * @param int $sceneid  获取二维码id
     * @param int $type 临时的
     * @param int $expire_seconds
     * @return bool|string
     */
    public function _request_ticket($sceneid,$type = 0,$expire_seconds = 604800){
        if($type == 0)//临时的
        {
            $json_data = '{"expire_seconds": '.$expire_seconds.', "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.$sceneid.'}}}';
        }
        else{
            $json_data = '{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "'.$sceneid.'"}}}';
        }
        $curl = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$this->_get_asscess_token();
        $content = $this->http($curl, "POST",$json_data);
        $ret = json_decode($content,true);
        $ticket = trim($ret['ticket']);
        if(strlen($ticket) >1) return $ticket;
        return false;
    }

    /**
     * @param int $sceneid 获取二维码id
     * @param int $type  临时的
     * @param int $expire_seconds
     */
    public function _get_ticket($sceneid,$type = 0,$expire_seconds = 604800)
    {
        $ticket = $this->_request_ticket($sceneid,$type,$expire_seconds);
        if($ticket)
        {
            $curl = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($ticket);
            $content = $this->http($curl, "GET");
            header('content-type:image/jpg;');
            echo $content;
        }

    }

    /**
     * @param json $json_data  $json_data json数组
     * @return bool
     */
    public function create_menu($json_data)
    {
         $curl = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->_get_asscess_token();
        $content = $this->http($curl,"POST",$this->gbk_to_utf8($json_data));
        $ret = json_decode($content,true);
        $errcode = intval($ret['errcode']);
        if($errcode ==0)
        {
            return true;
        }
        return false;
    }

    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;
        $keyword = trim($postObj->Content);
        $type = trim($postObj->MsgType);
        $event_key = trim($postObj->EventKey);
        $event = trim($postObj->Event);
        $time = time();
        $textTpl = "<xml>
                   <ToUserName><![CDATA[%s]]></ToUserName>
                   <FromUserName><![CDATA[%s]]></FromUserName>
                   <CreateTime>%s</CreateTime>
                   <MsgType><![CDATA[%s]]></MsgType>
                   <Content><![CDATA[%s]]></Content>
                   <FuncFlag>0</FuncFlag>
                   </xml>";

        if($event_key == "V1001_TODAY_MUSIC")
        {
            $msgType = "text";
            $contentStr = 'sss';
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
            echo $resultStr;
            exit;
        }
        if($event == "subscribe")
        {
            $msgType = "text";
            $contentStr = '777';
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
            echo $resultStr;
            exit;
        }
    }

    /**
     * GBK转成UTF-8
     * @param string|array $str
     * @return array|string
     */
    private function gbk_to_utf8($str)
    {
        if( is_string($str) )
        {
            $str = iconv('gbk', 'utf-8', $str);
        }
        elseif( is_array($str) )
        {
            foreach ($str as $key=>$val)
            {
                $str[$key] = $this->gbk_to_utf8($val);
            }
        }
        return $str;
    }
}