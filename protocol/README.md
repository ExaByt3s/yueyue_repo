#ԼԼͨ��Э�� 1.0 Beta

----

## Э��˵��

    ��Э������ԼԼ�ͻ������������֮���ͨ�š�
    Э�鸺�𽫶����ݽ���ǩ��У�飬HTMLע����ˣ��ַ�����ת��ȴ���

## ����ģ��

 * ���ݽ��գ�����ͻ��˵�������������У�飬ǩ��У��ȣ�
 * �û���Ȩ�������û�access_tokenע�ᣬ��Ȩ��֤��ˢ����֤�ȣ� 
 * ������Ӧ����������������װ�����봦�����ܴ������׳����ݵȣ�

## ����·��

    /protocol/yue_protocol.inc.php

## ����ʵ�� (��ʵ����)

    new yue_protocol_system();

## ���÷���

> $cp = new yue\_protocol\_system();

 * ��ȡ�ͻ�������(http)��`$cp->get_input(array $options)`;
 * ��ȡ��������(include)��`$cp->get_input_process($input, $token_check, $b_response)`;
 * �׳�(�򷵻�)��������ݣ�`$cp->output($options, $is_conv)`;
 * ��ȡTOKEN���ݣ�`$cp->get_access_info($user_id, $app_name, $b_auto_create, $b_use_cache)`;
 * ˢ��TOKEN���ݣ�`$cp->refresh_access_info($user_id, $app_name, $refresh_token, $access_info)`;
 * ��֤TOKEN���ݣ�`$cp->is_access_expire($user_id, $app_name, $access_token, $access_info)`;
 * ��ȡapp name��`$cp->get_app_name()`;

## ���ӹ���
    
    /protocol/master/index.php
    Э������̨
