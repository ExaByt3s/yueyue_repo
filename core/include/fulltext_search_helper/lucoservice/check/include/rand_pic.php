<?
session_start();

# Function Name: ImgRandom
# Purpose: ���������
# Description: ����ֵ��ͼƬ�����

function ImageRandom($str) 
{                           // �ִ�
	$font = 15;                                      // �ֺ�
	$width = 100;                                    // ͼƬ��С��
	$height = 22;                                    // ͼƬ��С��
	$left = rand(0,8);                               // ����x
	$top = rand(0,6);                                // ����y
	$back = rand(0, 255);                            // ����ɫ
	$fill_red = rand(0,127);                         // ���ɫR
	$fill_green = rand(0,127);                       // ���ɫG
	$fill_blue = rand(0,127);                        // ���ɫB
	$font_red = rand(128,255);                       // ������ɫR
	$font_green = rand(128,255);                     // ������ɫG
	$font_blue = rand(128,255);                      // ������ɫB
	
	$im = ImageCreate($width,$height);
	$bgcolor = ImageColorAllocate($im, 255,255,255);
	$ftcolor = ImageColorAllocate($im,0,0,0);
	ImageInterlace($im,1);
	ImageFilledRectangle($im,0,0,$width,$height,$bgcolor);
	
	
	//λ�ý���
	for ($i = 0; $i < strlen($str); $i++) 
	{
	if ( $i % 2 == 0 )   
	$top = 1;
	else 
	$top = 6;
	imagestring($im, 14, 10*$i+15, $top, substr($str,$i,1), 255); 
	}
	
	for ($i = 0; $i < 150;$i++)   //����������� 
	{ 
		imagesetpixel($im, rand()%80 , rand()%60 , 255); 
	}      
	
	ImagePNG($im);
	
	ImageDestroy($im);
}

$str = rand(100000,999999);
$_SESSION["verify_random"]=$str;
ImageRandom($str);
?>