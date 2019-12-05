<?php

/**
 * 2012/9/14
 * 字符转换拼音类
 * 
 */
if (!class_exists("pinyin", false))
{
	class pinyin
	{
		var $isMemoryCache = 1;
		//是否缓存拼音文件
			var $path = "/disk/data/htdocs233/mypoco/apps/poco_v2/include/py.qdb";
		//拼音文件地址
			var $MemoryCache;
		//拼音矩阵
			var $errorMsgBox;
		//错误信息
		var $n_t = array(

			"ā" => "a", "á" => "a", "ǎ" => "a", "à" => "a", "ɑ" => "a",
			"ō" => "o", "ó" => "o", "ǒ" => "o", "ò" => "o",
			"ē" => "e", "é" => "e", "ě" => "e", "è" => "e", "ê" => "e",
			"ī" => "i", "í" => "i", "ǐ" => "i", "ì" => "i",
			"ū" => "u", "ú" => "u", "ǔ" => "u", "ù" => "u",
			"ǖ" => "v", "ǘ" => "v", "ǚ" => "v", "ǜ" => "v", "ü" => "v"

		);

		/**
		* 转换主方法
		*
		* @param string/array $str    需要转换的中文
		* @param int $isFrist        是否只取第一个拼音
		* @param int $isToneMark     是否保留音标
		* @param string $suffix        尾缀,默认为空格
		* @return string/array
		*/
		function ChineseToPinyin($str,$isFrist=0,$isToneMark = 0,$suffix = "")
		{
			$strLength = strlen($str);
			if ($strLength == 0)return false;

			if (!file_exists($this->path))
			{
			   $this -> addOneErrorMsg(1, "拼音文件路径不存在");
			   return false;
			} 
			$result = "";
			if (is_array($str))
			{
				$result_arr = array();
				foreach($str as $key => $val)
				{
					$result_arr[$key] = $this -> ChineseToPinyin($val,$isToneMark, $suffix);
				}
				return $result_arr;
			} 
			if (!$this -> isMemoryCache || empty($this -> memoryCache) ){

				$this->memoryCache = file_get_contents($this -> path);

			}

			for($i = 0 ; $i < $strLength ; $i++)
			{
				$ord1 = ord(substr($str, $i, 1));
				if ($ord1 > 128)
				{
					$ord2 = ord(substr($str, ++$i, 1));
					if (!isset($__arr[$ord1][$ord2]))
					{
						$leng = ($ord1 - 129) * ((254 - 63) * 8 + 2) + ($ord2 - 64) * 8;
						$__arr[$ord1][$ord2] = trim(substr($this -> memoryCache, $leng, 8));
					}
					$strtrLen = $isFrist ? 1 : 8;
					$result  .= substr($__arr[$ord1][$ord2], 0, $strtrLen) . $suffix;
				
				}
				else
				{
					$result .= substr($str, $i, 1);
				}

			}
			if (!$isToneMark)$result = strtr($result,$this -> n_t);
			return $result;

		}
		function addOneErrorMsg($No, $reason)
		{
			$this -> errorMsgBox[] = "<b>Error:</b>" . $No . "," . $reason;
		} 

		function showErrorMsg()
		{
			foreach($this -> errorMsgBox as $val)
			{
				echo $val . "\r\n\r\n</br></br>";
			} 
		} 

		function __destruct()
		{
			if (is_array($this -> errorMsgBox))
			{
				$this -> showErrorMsg();
			} 
		} 
	} 
}

?>