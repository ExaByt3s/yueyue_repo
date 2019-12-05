<?
session_start();

# Function Name: ImgRandom
# Purpose: 生成随机码
# Description: 返回值，图片随机码

function ImageRandom($str) 
{                           // 字串
	$font = 15;                                      // 字号
	$width = 100;                                    // 图片大小宽
	$height = 22;                                    // 图片大小长
	$left = rand(0,8);                               // 坐标x
	$top = rand(0,6);                                // 坐标y
	$back = rand(0, 255);                            // 干扰色
	$fill_red = rand(0,127);                         // 填充色R
	$fill_green = rand(0,127);                       // 填充色G
	$fill_blue = rand(0,127);                        // 填充色B
	$font_red = rand(128,255);                       // 字体颜色R
	$font_green = rand(128,255);                     // 字体颜色G
	$font_blue = rand(128,255);                      // 字体颜色B
	
	$im = ImageCreate($width,$height);
	$bgcolor = ImageColorAllocate($im, 255,255,255);
	$ftcolor = ImageColorAllocate($im,0,0,0);
	ImageInterlace($im,1);
	ImageFilledRectangle($im,0,0,$width,$height,$bgcolor);
	
	
	//位置交错
	for ($i = 0; $i < strlen($str); $i++) 
	{
	if ( $i % 2 == 0 )   
	$top = 1;
	else 
	$top = 6;
	imagestring($im, 14, 10*$i+15, $top, substr($str,$i,1), 255); 
	}
	
	for ($i = 0; $i < 150;$i++)   //加入干扰象素 
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