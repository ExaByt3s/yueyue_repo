<?php

define('UC_CONNECT', 'mysql');				// ���� UCenter �ķ�ʽ: mysql/NULL, Ĭ��Ϊ��ʱΪ fscoketopen()
							// mysql ��ֱ�����ӵ����ݿ�, Ϊ��Ч��, ������� mysql

//���ݿ���� (mysql ����ʱ, ����û������ UC_DBLINK ʱ, ��Ҫ�������±���)
define('UC_DBHOST', '113.107.204.252');				// UCenter ���ݿ�����
define('UC_DBUSER', 'bbs');							// UCenter ���ݿ��û���
define('UC_DBPW', 'bbs123*()');						// UCenter ���ݿ�����
define('UC_DBNAME', 'discuz_bbs');					// UCenter ���ݿ�����
define('UC_DBCHARSET', 'gbk');						// UCenter ���ݿ��ַ���
define('UC_DBTABLEPRE', 'discuz_bbs.pre_ucenter_');			// UCenter ���ݿ��ǰ׺

//ͨ�����
define('UC_KEY', 'yueyue_key_smicxns!%#@Ksdfas');				// �� UCenter ��ͨ����Կ, Ҫ�� UCenter ����һ��
define('UC_API', 'http://pai.poco.cn/discuz/uc_server');					// UCenter �� URL ��ַ, �ڵ���ͷ��ʱ�����˳���
define('UC_CHARSET', 'gbk');									// UCenter ���ַ���
define('UC_IP', '');											// UCenter �� IP, �� UC_CONNECT Ϊ�� mysql ��ʽʱ, ���ҵ�ǰӦ�÷�������������������ʱ, �����ô�ֵ
define('UC_APPID', 3);											// ��ǰӦ�õ� ID

//ucexample_2.php �õ���Ӧ�ó������ݿ����Ӳ���
$dbhost = '113.107.204.252';				// ���ݿ������
$dbuser = 'heyh';							// ���ݿ��û���
$dbpw = 'HeyH(d!3';							// ���ݿ�����
$dbname = 'pai_db';							// ���ݿ���
$pconnect = 0;								// ���ݿ�־����� 0=�ر�, 1=��
$tablepre = '';   							// ����ǰ׺, ͬһ���ݿⰲװ�����̳���޸Ĵ˴�
$dbcharset = 'gbk';							// MySQL �ַ���, ��ѡ 'gbk', 'big5', 'utf8', 'latin1', ����Ϊ������̳�ַ����趨

//ͬ����¼ Cookie ����
$cookiedomain = ''; 			// cookie ������
$cookiepath = '/';			// cookie ����·��