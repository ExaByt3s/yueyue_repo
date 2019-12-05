<?php
/*
Uploadify 后台处理 Demo
Author:wind
Date:2013-1-4
uploadify 后台处理！
*/

//设置上传目录
$path = "../upload/images";	

if (!empty($_FILES)) {
	
	//得到上传的临时文件流
	$tempFile = $_FILES['Filedata']['tmp_name'];
	
    //允许的文件大小
//    $fileSize = $_FILES['Filedata']['size'];
//    if($size>=30000){
//        exit('您上传的文件大小超过限定');
//    }
    
    
    
	//允许的文件后缀
	//$fileTypes = array('jpg','jpeg','gif','png'); 
    //$fileTypes = array('exe');
	$fileType = $_FILES['Filedata']['type'];
//    echo $fileType;
//    exit;
//    switch($fileType)
//    {
//        case 'image/pjpeg' : 
//            $nameback='.jpg';  //jpeg
//            break;
//        case 'image/jpeg' : 
//            $nameback='.jpg';  //jpg
//            break;
//        case 'image/gif' : 
//            $nameback='.gif';  //gif
//            break;
//        case 'image/png' : 
//            $nameback='.png'; //png
//            break;
//        case 'image/bmp' : 
//            $nameback='.bmp'; //bmp
//            break;
//            
//        default:
//            exit('上传失败！');
//    }
    
    
	//得到文件原名
	//$fileName = iconv("UTF-8","GB2312",$_FILES["Filedata"]["name"]);
    
    $file_array = explode('.', $_FILES["Filedata"]["name"]);
    $num = count($file_array);
    if($num)
    {
        $num = $num-1;
    }
    
    
    $fileName = time() . rand(0, 10000) . "." . $file_array[$num]; 
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	
	//接受动态传值
	$files=$_POST['typeCode'];
	
	//最后保存服务器地址
	if(!is_dir($path))
	   mkdir($path);
	if (move_uploaded_file($tempFile, $path.$fileName)){
		echo $fileName;
	}else{
		echo "上传失败！";
	}
}
?>