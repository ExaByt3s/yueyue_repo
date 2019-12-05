<?php

/**
 * Desc:   小肖测试
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/15 0015
 * @Time:   16:51
 * @Version: 1.0
 */
//define your token
define("TOKEN", "weixin");
require_once('weixin.class.php');
$weixin = new weixin('wx1062854dd7b2dbd6','bc623d22ecaa944ca4e7fdd5ef79f16b', TOKEN);
$json_data ='{
                 "button":[
                 {
                      "type":"click",
                      "name":"菜单",
                      "key":"V1001_TODAY_MUSIC"
                  },
                  {
                       "name":"菜单",
                       "sub_button":[
                       {
                           "type":"view",
                           "name":"搜索",
                           "url":"http://www.soso.com/"
                        },
                        {
                           "type":"view",
                           "name":"视频",
                           "url":"http://v.qq.com/"
                        },
                        {
                           "type":"click",
                           "name":"赞一下我们",
                           "key":"V1001_GOOD"
                        }]
                   }]
             }';
//$weixin->create_menu($json_data);
$weixin->responseMsg();


/*$wechatObj = new wechatCallbackapiTest();
$wechatObj->responseMsg();

class wechatCallbackapiTest
{
    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        //extract post data
        if (!empty($postStr)){

            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $type = $postObj->MsgType;
            $time = time();
            $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
            if($type == "text")
            {
                $msgType = $type;
                $contentStr = 'sss';
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
                exit;
            }
            if(strlen( $keyword ) >1)
            {
                $msgType = $type;
                $contentStr = '文字信息';
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
                exit;
            }
            else{
                echo "Input something...";
            }

        }else {
            echo "多选订阅";
            exit;
        }
    }
}*/