<?php
/**
 * �ϴ��������ϴ���Ƶ
 * User: Jinqn
 * Date: 14-04-09
 * Time: ����10:17
 */
include "Uploader.class.php";

/* �ϴ����� */
$base64 = "upload";
switch (htmlspecialchars($_GET['action'])) {
    case 'uploadimage':
        $config = array(
            "pathFormat" => $CONFIG['imagePathFormat'],
            "maxSize" => $CONFIG['imageMaxSize'],
            "allowFiles" => $CONFIG['imageAllowFiles']
        );
        $fieldName = $CONFIG['imageFieldName'];
        break;
    case 'uploadscrawl':
        $config = array(
            "pathFormat" => $CONFIG['scrawlPathFormat'],
            "maxSize" => $CONFIG['scrawlMaxSize'],
            "allowFiles" => $CONFIG['scrawlAllowFiles'],
            "oriName" => "scrawl.png"
        );
        $fieldName = $CONFIG['scrawlFieldName'];
        $base64 = "base64";
        break;
    case 'uploadvideo':
        $config = array(
            "pathFormat" => $CONFIG['videoPathFormat'],
            "maxSize" => $CONFIG['videoMaxSize'],
            "allowFiles" => $CONFIG['videoAllowFiles']
        );
        $fieldName = $CONFIG['videoFieldName'];
        break;
    case 'uploadfile':
    default:
        $config = array(
            "pathFormat" => $CONFIG['filePathFormat'],
            "maxSize" => $CONFIG['fileMaxSize'],
            "allowFiles" => $CONFIG['fileAllowFiles']
        );
        $fieldName = $CONFIG['fileFieldName'];
        break;
}

/* �����ϴ�ʵ����������ϴ� */
$up = new Uploader($fieldName, $config, $base64);

/**
 * �õ��ϴ��ļ�����Ӧ�ĸ�������,����ṹ
 * array(
 *     "state" => "",          //�ϴ�״̬���ϴ��ɹ�ʱ���뷵��"SUCCESS"
 *     "url" => "",            //���صĵ�ַ
 *     "title" => "",          //���ļ���
 *     "original" => "",       //ԭʼ�ļ���
 *     "type" => ""            //�ļ�����
 *     "size" => "",           //�ļ���С
 * )
 */

/* �������� */
return json_encode($up->getFileInfo());
