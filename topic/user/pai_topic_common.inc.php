<?php
//输出警告+跳转
function print_message_jump_url($msg,$url){
    if(!empty($msg) && !empty($url))
    {
		echo "<script language=\"Javascript\"> \n";
		echo "alert(\"$msg\");";
        echo "top.location=\"$url\";";
		echo "</script>";        
    }
}

function print_message_return($msg)
{
    if(!empty($msg))
    {
		echo "<script language=\"Javascript\"> \n";
		echo "alert(\"$msg\");";
        echo "history.go(-1);";
		echo "</script>";        
    }    
}

function print_message($msg)
{
    if(!empty($msg))
    {
		echo "<script language=\"Javascript\"> \n";
		echo "alert(\"$msg\");";
		echo "</script>";        
    }    
}


function check_user($phone, $pwd)
{
    $phone  = (int)$phone;
    $pwd    = md5($pwd);
    $sql_str = 'SELECT user_id FROM pai_db.pai_user_tbl WHERE cellphone=:x_phone AND pwd_hash=:x_pwd';
    sqlSetParam($sql_str, 'x_phone', $phone);
    sqlSetParam($sql_str, 'x_pwd', $pwd);
    $result = db_simple_getdata($sql_str, TRUE, 101);
    
//    if($phone != 13751783133)
//    {
//        if($result['role'] != 'model') 
//        {
//           print_message_jump_url('摄影师不能登陆', 'index.php');
//           exit(); 
//        }         
//    }
    

    return $result['user_id'];
    
}

function fileupload($tmp_name,$original,$type,$size,$maxsize)	{
	if (!empty($tmp_name))	{
		//if ($maxsize >= $size)	
        {
			echo "111";
			$filename = md5(uniqid(microtime(),1));	
			
			/** 所在文件夹 **/
			$folder = date("Ymd",time());
			$store_dir = "../images/icon/".$folder."/";

			if (! file_exists($store_dir)) {
				mkdir($store_dir);
			}
			
			switch ($type)	{
				case "image/jpeg":$imagetype = ".jpg";break;
                case "image/pjpeg":$imagetype = ".jpg";break;
				case "image/gif":$imagetype = ".gif";break;
				case "image/x-png":$imagetype = ".png";break;
				default:$imagetype = "error";break;
			}
			//图片路径
			$image_dir = $store_dir.$filename.$imagetype;
			$image_path = $folder."/".$filename.$imagetype;	//相对路径
            $image_url = 'http://yp.yueus.com/topic/images/icon/' . $image_path;
            
            //echo $image_dir;
			//如果图片有重复，报错！
			if (file_exists($image_dir))	{}
					
			if ($type!="error" AND copy($tmp_name, $image_dir)) {
          		$array_img['image_dir']     = $image_dir;
                $array_img['image_path']    = $image_path;
                $array_img['image_url']     = $image_url;
          		return $array_img;
        	}
        	else return 0;		
		}
        //else {
        //	return 0;
        //}
	}
	else return 0;
}
?>