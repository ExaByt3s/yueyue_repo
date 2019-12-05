<?php

/**
 * 2012/9/14
 * �ַ�ת��ƴ����
 * 
 */
if (!class_exists("pinyin", false))
{
	class pinyin
	{
		var $isMemoryCache = 1;
		//�Ƿ񻺴�ƴ���ļ�
			var $path = "/disk/data/htdocs233/mypoco/apps/poco_v2/include/py.qdb";
		//ƴ���ļ���ַ
			var $MemoryCache;
		//ƴ������
			var $errorMsgBox;
		//������Ϣ
		var $n_t = array(

			"��" => "a", "��" => "a", "��" => "a", "��" => "a", "��" => "a",
			"��" => "o", "��" => "o", "��" => "o", "��" => "o",
			"��" => "e", "��" => "e", "��" => "e", "��" => "e", "��" => "e",
			"��" => "i", "��" => "i", "��" => "i", "��" => "i",
			"��" => "u", "��" => "u", "��" => "u", "��" => "u",
			"��" => "v", "��" => "v", "��" => "v", "��" => "v", "��" => "v"

		);

		/**
		* ת��������
		*
		* @param string/array $str    ��Ҫת��������
		* @param int $isFrist        �Ƿ�ֻȡ��һ��ƴ��
		* @param int $isToneMark     �Ƿ�������
		* @param string $suffix        β׺,Ĭ��Ϊ�ո�
		* @return string/array
		*/
		function ChineseToPinyin($str,$isFrist=0,$isToneMark = 0,$suffix = "")
		{
			$strLength = strlen($str);
			if ($strLength == 0)return false;

			if (!file_exists($this->path))
			{
			   $this -> addOneErrorMsg(1, "ƴ���ļ�·��������");
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