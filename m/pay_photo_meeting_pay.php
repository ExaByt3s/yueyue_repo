<?php
/**
 * ����΢��֧��ҳ��
 * 
 * @author ����
 * 
 * 
 * @copyright 2015-04-02
 */
 
include_once('/disk/data/htdocs232/poco/pai/topic/meeting/config/phone_meeting_config.php');//���÷���Ӧ���ε�ID����Ǯ
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
define("G_DB_GET_REALTIME_DATA",1);
$summit_meeting_obj   = POCO::singleton('pai_summit_meeting_class');
$pai_sms_obj = POCO::singleton('pai_sms_class');
//��ʾҳ��
// ע�� URL һ��Ҫ��̬��ȡ������ hardcode.
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

//΢����Ȩ
$openid = trim($_COOKIE['yueus_openid']);
$not_repeat = intval($_INPUT['not_repeat']);
if( strlen($openid)<1 && $not_repeat!=1 )
{
    if( strpos($url, '?')===false )
    {
        $url = $url . '?not_repeat=1';
    }
    else
    {
        $url = $url . '&not_repeat=1';
    }
    $weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');
    $authorize_url = $weixin_pub_obj->auth_get_authorize_url(array('url'=>$url), 'snsapi_base');
    header("Location: {$authorize_url}");
    exit();
}
//��ʾҳ��

//����ҳ��
$op = trim($_INPUT['op']);

if( $op=='pay' )
{
    
    $meeting_id = (int)$_INPUT['meeting_id'];
    $phone = (int)$_INPUT['phone'];
    $email = trim($_INPUT['email']);
    $enroll_num = (int)$_INPUT['enroll_num'];
    $active_code = (int)$_INPUT['active_code'];
    //У�鳡��ID
    if(!in_array($meeting_id,$meeting_array))
    {
        $output_arr = array();
        $output_arr['code'] = 0;
        $output_arr['message'] = "data error";
        mobile_output($output_arr, false);
        exit();
    }


    //У���ֻ�
    if(empty($phone))
    {
        
        $output_arr = array();
        $output_arr['code'] = 0;
        $output_arr['message'] = "phone error";
        mobile_output($output_arr, false);
        exit();

    }

    //У������
    $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
    if ( !preg_match( $pattern, $email ) )
    {
        
        $output_arr = array();
        $output_arr['code'] = 0;
        $output_arr['message'] = "email error";
        mobile_output($output_arr, false);
        exit();
    }

    //У������
    if($enroll_num<1)
    {
        $output_arr = array();
        $output_arr['code'] = 0;
        $output_arr['message'] = "enroll number error";
        mobile_output($output_arr, false);
        exit();
    }
    
    //У����֤��
    if($active_code<1)
    {
        $output_arr = array();
        $output_arr['code'] = 0;
        $output_arr['message'] = "vertify number not exist error";
        mobile_output($output_arr, false);
        exit();
    }
    else
    {
        //У����֤��
        $verify_code = $active_code;
        $group_key = 'G_PAI_TOPIC_MEETING_VERIFY';
        $active_code_ret = $pai_sms_obj->check_verify_code($phone, $group_key, $verify_code, $user_id=0, $b_del_verify_code=true);
        if(!$active_code_ret)
        {
            $output_arr = array();
            $output_arr['code'] = 0;
            $output_arr['message'] = "vertify number error";
            mobile_output($output_arr, false);
            exit();
        }
        
    }
    
    
    //��������
    $insert_data['meeting_id'] = $meeting_id;
    $insert_data['email'] = $email;
    $insert_data['phone'] = $phone;
    $insert_data['enroll_num'] = $enroll_num;
    $sum_price = ($meeting_price_array[$meeting_id])*$enroll_num;//�ܼ۸�
    $insert_data['sum_price'] = $sum_price;
    $summit_meeting_id = $summit_meeting_obj->add_summit_meeting($insert_data);
    if(empty($summit_meeting_id) || $summit_meeting_id<=0)
    {
        $output_arr = array();
        $output_arr['code'] = 0;
        $output_arr['message'] = "enroll_id error";
        mobile_output($output_arr, false);
        exit();
    }
    //��Ӧ��subject���֣�
    $subject = $meeting_name_array[$meeting_id];//ƥ�����õ���
    $amount = $sum_price; //֧�����
    
    
    $channel_rid = $summit_meeting_id; //ģ�����ID������ID��������
    $subject = $subject; //��Ʒ���ơ������
    $amount = $amount; //֧�����
    $channel_return = 'http://www.yueus.com/topic/meeting/photo_meeting_middle_jump.php'; //֧���ɹ���ҳ����ת����
    $channel_notify = 'http://www.yueus.com/topic/meeting/pay_notify.php'; //�������첽֪֧ͨ��״̬
    $openid = trim($_COOKIE['yueus_openid']);
    if( strlen($openid)<1 )
    {
        $output_arr = array();
        $output_arr['code'] = 0;
        $output_arr['message'] = 'need weixin authorize!';
        mobile_output($output_arr, false);
        exit();
    }
    
    $channel_module = 'meeting'; //ģ�����ͣ����
    $third_code = 'tenpay_wxpub'; //֧����ʽ��΢��֧��PUB
    $payment_info = array(
        'third_code' => $third_code,
        'subject' => 'ԼԼ-' . $subject,
        'amount' => $amount,
        'channel_return' => $channel_return,
        'channel_notify' => $channel_notify,
        'openid' => $openid,
    );
    $payment_obj = POCO::singleton('pai_payment_class');
    $payment_ret = $payment_obj->submit_payment($channel_module, $channel_rid, $payment_info);
    if( $payment_ret['error']===0 )
    {
        $output_arr = array();
        $output_arr['code'] = 1;
        $output_arr['message'] = '';
        $output_arr['payment_no'] = $payment_ret['payment_no'];
        $output_arr['data'] = $payment_ret['request_data'];
        $output_arr['channel_return'] = $channel_return;
    }
    else
    {
        $output_arr = array();
        $output_arr['code'] = 0;
        $output_arr['message'] = $payment_ret['message'];
    }
    mobile_output($output_arr, false);
    exit();
}
//����ҳ��


//��������ʾ
$app_id = 'wx25fbf6e62a52d11e'; //ԼԼ��ʽ��
$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
$wx_sign_package = $weixin_helper_obj->wx_get_js_api_sign_package_by_app_id($app_id, $url);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title></title>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
<script>
  /*
   * ע�⣺
   * 1. ���е�JS�ӿ�ֻ���ڹ��ںŰ󶨵������µ��ã����ںſ�������Ҫ�ȵ�¼΢�Ź���ƽ̨���롰���ں����á��ġ��������á�����д��JS�ӿڰ�ȫ��������
   * 2. ��������� Android ���ܷ����Զ������ݣ��뵽�����������µİ����ǰ�װ��Android �Զ������ӿ��������� 6.0.2.58 �汾�����ϡ�
   * 3. �������⼰���� JS-SDK �ĵ���ַ��http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html
   *
   * ������������������ĵ�����¼5-�������󼰽���취�����������δ�ܽ����ͨ����������������
   * �����ַ��weixin-open@qq.com
   * �ʼ����⣺��΢��JS-SDK��������������
   * �ʼ�����˵�����ü��������������������ڣ��������������������ĳ������ɸ��Ͻ���ͼƬ��΢���Ŷӻᾡ�촦����ķ�����
   */
  wx.config({
    debug: true,
    appId: '<?php echo $wx_sign_package["appId"];?>',
    timestamp: <?php echo $wx_sign_package["timestamp"];?>,
    nonceStr: '<?php echo $wx_sign_package["nonceStr"];?>',
    signature: '<?php echo $wx_sign_package["signature"];?>',
    jsApiList: [
        'chooseWXPay'
    ]
  });
  wx.ready(function () {
    // ��������� API
  });

  function callpay()
  {
  
    var phone = $("#phone").val();
    var email = $("#email").val();
    var enroll_num = $("#enroll_num").val();
    var meeting_id = $("#meeting_id").val();
    var ajax_url = "pay_photo_meeting_pay.php?op=pay"+"&phone="+phone+"&email="+email+"&enroll_num="+enroll_num+"&meeting_id="+meeting_id;//��Ҫ�����Ż�
    $.getJSON(ajax_url,function(output){
        if( output )
        {
            var result_data = output.result_data;
            var data = eval('('+result_data.data + ')');
            if( result_data && result_data.code==1 )
            {
                wx.chooseWXPay({
                    timestamp: data.timeStamp, // ֧��ǩ��ʱ�����ע��΢��jssdk�е�����ʹ��timestamp�ֶξ�ΪСд�������°��֧����̨����ǩ��ʹ�õ�timeStamp�ֶ������д���е�S�ַ�
                    nonceStr: data.nonceStr, // ֧��ǩ��������������� 32 λ
                    package: data.package, // ͳһ֧���ӿڷ��ص�prepay_id����ֵ���ύ��ʽ�磺prepay_id=***��
                    signType: data.signType, // ǩ����ʽ��Ĭ��Ϊ'SHA1'��ʹ���°�֧���贫��'MD5'
                    paySign: data.paySign, // ֧��ǩ��
                    success: function (res) {
                        //window.location.href=result_data.channel_return;
                        alert('chooseWXPay:' + res);//�ɹ�������ת������������
                    }
                });
            }
            else
            {
                alert("result_data error " + result_data.message);
            }
        }
        else
        {
            alert('output error');
        }
    });
  }
</script>
</head>
<body>
  <br /><br /><br /><br />
    <div align="center">
        <input type="text" value="" id="phone">�ֻ�����</input>
        <input type="text" value="" id="email">����</input>
        <input type="text" value="" id="meeting_id">����ID</input>
        <input type="text" value="" id="enroll_num">����</input>
        <button style="width:210px; height:30px; background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" >����һ��</button>
    </div>
</body>
</html>
