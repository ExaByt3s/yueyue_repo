<?
/** unescape，解js的escape的
 * @author  POCO TEAM
 * 函数原型：function unescape($str,$b_use_iconv=false) return $string;
 */


//数据文件(*.data)位置
define("G_UNESCAPE_DATA_FILE_PATH", realpath(dirname(__FILE__))."/");


function _un_to_utf8($str)
{
	// First, Conv %uXXXX to unicode(dec), in a array
	$pattern = "/%u[0-9A-F]{4}|[\x01-\x7F]/";
	preg_match_all($pattern, $str, $array);
	foreach($array[0] as $uni_str)
	{
		if (substr($uni_str, 0, 2) == "%u")
		{
			$unicode = hexdec(substr($uni_str, 2));
		}
		else
		{
			$unicode = ord($uni_str);
		}
		$uni[] = $unicode;
	}
	// Conv unicode array to UTF-8 string
	return _unicode_to_utf8($uni);
}

function _unicode_to_utf8( $str )
{
	$utf8 = '';
	if($str=='')
	return $utf8;
	foreach( $str as $unicode )
	{
		if ( $unicode < 128 )
		{

			$utf8.= chr( $unicode );
		}
		elseif ( $unicode < 2048 )
		{
			$utf8.= chr( 192 +  ( ( $unicode - ( $unicode % 64 ) ) / 64 ) );
			$utf8.= chr( 128 + ( $unicode % 64 ) );
		}
		else
		{
			$utf8.= chr( 224 + ( ( $unicode - ( $unicode % 4096 ) ) / 4096 ) );
			$utf8.= chr( 128 + ( ( ( $unicode % 4096 ) - ( $unicode % 64 ) ) / 64 ) );
			$utf8.= chr( 128 + ( $unicode % 64 ) );
		}
	}
	return $utf8;
}


class __Encoding
{
	var $GetEncoding="1";
	var $ToEncoding="3";
	var $UnicodeString="";
	var $Encodings=array("1"=>"GBK","2"=>"UTF-8","3"=>"UTF-16BE","4"=>"UTF-16LE","5"=>"BIG5");


	function SetGetEncoding($GetEncoding)
	{
		$order=0;
		if ($this->CheckEncoding($GetEncoding,$order)) {
			$this->GetEncoding=$order;
			return true;
		}
		echo "警告:您设置了程序不支持的编码类型,编码格式将不做变动";
		return false;
	}
	function SetToEncoding($ToEncoding)
	{
		$order=0;
		if ($this->CheckEncoding($ToEncoding,$order)) {
			$this->ToEncoding=$order;
			return true;
		}
		echo "警告:您设置了程序不支持的编码类型,编码格式将不做变动";
		return false;
	}
	function EncodeString($String)
	{
		if ($this->GetEncoding=="1") {
			$this->UnicodeString=$this->GBKToUnicode($String);
		}
		elseif ($this->GetEncoding=="2") {
			$this->UnicodeString=$this->UTFToUnicode($String);
		}
		elseif ($this->GetEncoding=="3") {
			$this->UnicodeString=$String;
		}
		elseif ($this->GetEncoding=="4") {
			$this->UnicodeString=$this->ChangeByte($String);
		}
		elseif ($this->GetEncoding=="5") {
			$this->UnicodeString=$this->BIGToUnicode($String);
		}
		if ($this->ToEncoding=="1") {
			return $this->UnicodeToGBK($this->UnicodeString);
		}
		elseif ($this->ToEncoding=="2") {
			return $this->UnicodeToUTF($this->UnicodeString);
		}
		elseif ($this->ToEncoding=="3") {
			return $this->UnicodeString;
		}
		elseif ($this->ToEncoding=="4") {
			return $this->ChangeByte($this->UnicodeString);
		}
		elseif ($this->ToEncoding=="5") {
			return $this->UnicodeToBIG($this->UnicodeString);
		}
	}
	function GBKToUnicode(&$String)
	{
		$UnicodeData=file(G_UNESCAPE_DATA_FILE_PATH."gbkunicode.data");
		$ReturnString="";
		$StringLength=strlen($String);
		$p="";
		$q="";
		for($i=0;$i<$StringLength;$i++){
			if(($p=ord(substr($String,$i,1)))>128){
				$q=ord(substr($String,++$i,1));
				if ($p>254) {
					$ReturnString.="003f";
				}
				elseif ($q<64||$q>254) {
					$ReturnString.="003f";
				}
				else {
					$q=($q-64)*4;
					$ReturnString.=substr($UnicodeData[$p-128],$q,4);
				}
			}
			else {
				if ($p==128) {
					$ReturnString.="20ac";
				}
				else {
					$ReturnString.="00";
					$ReturnString.=dechex($p);
				}
			}
		}
		return $this->hex2bin($ReturnString);
	}
	function BIGToUnicode(&$String)
	{
		$UnicodeData=file(G_UNESCAPE_DATA_FILE_PATH."bigunicode.data");
		$ReturnString="";
		$StringLength=strlen($String);
		$p="";
		$q="";
		for($i=0;$i<$StringLength;$i++){
			if(($p=ord(substr($String,$i,1)))>128){
				$q=ord(substr($String,++$i,1));
				if ($p>249) {
					$ReturnString.="003f";
				}
				elseif ($q<64||$q>254) {
					$ReturnString.="003f";
				}
				else {
					$q=($q-64)*4;
					$ReturnString.=substr($UnicodeData[$p-160],$q,4);
				}
			}
			else {
				$ReturnString.="00";
				$ReturnString.=dechex($p);
			}
		}
		return $this->hex2bin($ReturnString);
	}
	function UnicodeToGBK(&$String)
	{
		$GBKData=file(G_UNESCAPE_DATA_FILE_PATH."unicodegbk.data");
		$ReturnString="";
		$StringLength=strlen($String);
		$p="";
		$q="";
		$temp="";
		for($i=0;$i<$StringLength;$i++){
			$p=ord(substr($String,$i++,1));
			if ($i==$StringLength) {
				$temp=dechex($p);
				if (strlen($temp)<2) {
					$temp="0".$temp;
				}
				$ReturnString.=$temp;
				continue;
			}
			$q=ord(substr($String,$i,1));
			if ($p==0&&$q<127) {
				$temp=dechex($q);
				if (strlen($temp)<2) {
					$temp="0".$temp;
				}
				$ReturnString.=$temp;
				continue;
			}
			$p++;
			$begin=hexdec(substr($GBKData[$p],0,2));
			if (strlen($GBKData[$p])<3||$q<$begin||$q>hexdec(substr($GBKData[$p],2,2))) {
				$ReturnString.="3f";
				continue;
			}
			$q*=4;
			$q-=$begin*4;
			$temp=substr($GBKData[$p],$q+4,2);
			if ($temp=="00") {
				$ReturnString.=substr($GBKData[$p],$q+6,2);
			}
			else {
				$ReturnString.=$temp.substr($GBKData[$p],$q+6,2);
			}
		}
		return $this->hex2bin($ReturnString);
	}
	function UnicodeToBIG(&$String)
	{
		$BIGData=file(G_UNESCAPE_DATA_FILE_PATH."unicodebig.data");
		$ReturnString="";
		$StringLength=strlen($String);
		$p="";
		$q="";
		$temp="";
		for($i=0;$i<$StringLength;$i++){
			$p=ord(substr($String,$i++,1));
			if ($i==$StringLength) {
				$temp=dechex($p);
				if (strlen($temp)<2) {
					$temp="0".$temp;
				}
				$ReturnString.=$temp;
				continue;
			}
			$q=ord(substr($String,$i,1));
			if ($p==0&&$q<127) {
				$temp=dechex($q);
				if (strlen($temp)<2) {
					$temp="0".$temp;
				}
				$ReturnString.=$temp;
				continue;
			}
			$p++;
			$begin=hexdec(substr($BIGData[$p],0,2));
			if (strlen($BIGData[$p])<3||$q<$begin||$q>hexdec(substr($BIGData[$p],2,2))) {
				$ReturnString.="3f";
				continue;
			}
			$q*=4;
			$q-=$begin*4;
			$temp=substr($BIGData[$p],$q+4,2);
			if ($temp=="00") {
				$ReturnString.=substr($BIGData[$p],$q+6,2);
			}
			else {
				$ReturnString.=$temp.substr($BIGData[$p],$q+6,2);
			}
		}
		return $this->hex2bin($ReturnString);
	}
	function UnicodeToUTF(&$String)
	{
		$len=strlen($String);
		$ReturnString="";
		for ($x=0;$x<$len;$x++) {
			$Char=substr($String,$x++,1);
			if ($x==$len) {
				$ReturnString.=bin2hex($Char);
				continue;
			}
			$Char.=substr($String,$x,1);
			$hex=bin2hex($Char);
			$dec=hexdec($hex);
			$bin=decbin($dec);
			$temp="";
			if($dec>0x7f){
				$binlen=strlen($bin);
				for ($i=0;$i<16-$binlen;$i++) {
					$bin="0".$bin;
				}
				$temp.="1110".substr($bin,0,4);
				$temp.="10".substr($bin,4,6);
				$temp.="10".substr($bin,10,6);
				$temp=dechex(bindec($temp));
			}
			else {
				$temp=substr($hex,2,2);
			}
			$ReturnString.=$temp;
		}
		return $this->hex2bin($ReturnString);
	}
	function ChangeByte(&$String)
	{
		$len=strlen($String);
		$ReturnString="";
		for ($i=0;$i<$len;$i++) {
			if ($i+1!=$len) {
				$ReturnString.=substr($String,$i+1,1).substr($String,$i++,1);
			}
			else {
				$ReturnString.=substr($String,$i,1);
			}
		}
		return $ReturnString;
	}
	function UTFToUnicode(&$String)
	{
		$UTFlen=strlen($String);
		$x="";
		$y="";
		$z="";
		$ReturnString="";
		for ($i=0;$i<$UTFlen;$i++) {
			if(($x=ord(substr($String,$i,1)))>128){
				if ($i+1==$UTFlen) {
					$ReturnString.=dechex($x);
					continue;
				}
				$y=ord(substr($String,++$i,1));
				if ($i+1==$UTFlen) {
					$ReturnString.=dechex($x).dechex($y);
					continue;
				}
				$x=decbin($x);
				$y=decbin($y);
				$z=decbin(ord(substr($String,++$i,1)));
				$temp=dechex(bindec(substr($x,4,4).substr($y,2,4).substr($y,6,2).substr($z,2,6)));
				$len=strlen($temp);
				for ($j=0;$j<4-$len;$j++) {
					$temp="0".$temp;
				}
				$ReturnString.=$temp;
			}
			else {
				$ReturnString.="00";
				$ReturnString.=dechex($x);
			}
		}
		return $this->hex2bin($ReturnString);
	}
	function hex2bin(&$String)
	{
		$ReturnString="";
		$len=strlen($String);
		for($i=0;$i<$len;$i+=2) {
			$ReturnString.= pack("C",hexdec(substr($String,$i,2)));
		}
		return $ReturnString;
	}
	function CheckEncoding($Encoding,&$order)
	{
		$order=0;
		reset($this->Encodings);
		while (list($key,$value)=each($this->Encodings)) {
			if ($Encoding==$value) {
				$order=$key;
				return true;
			}
		}
		return false;
	}
}



/**
 * 下面的是用iconv的
 *
 * @param unknown_type $str
 * @return unknown
 */
function escape_iconv($str)
{
	preg_match_all("/[\x80-\xff].|[\x01-\x7f]+/",$str,$r);
	$ar = $r[0];
	foreach($ar as $k=>$v)
	{
		if(ord($v[0]) < 128)
		$ar[$k] = rawurlencode($v);
		else
		$ar[$k] = "%u".bin2hex(iconv("GB2312","UCS-2",$v));
	}
	return join("",$ar);
}

function unescape_iconv($str)
{
	$str = rawurldecode($str);
	preg_match_all("/%u.{4}|&#x.{4};|&#\d+;|.+/U",$str,$r);
	$ar = $r[0];
	foreach($ar as $k=>$v)
	{
		if(substr($v,0,2) == "%u")
		$ar[$k] = iconv("UCS-2","GBK",pack("H4",substr($v,-4)));
		else if(substr($v,0,3) == "&#x")
		$ar[$k] = iconv("UCS-2","GBK",pack("H4",substr($v,3,-1)));
		else if(substr($v,0,2) == "&#")
		{
			$ar[$k] = iconv("UCS-2","GBK",pack("n",substr($v,2,-1)));
		}
	}
	return join("",$ar);
}



/**
 * unescape
 *
 * @param string $str
 * @param boolean $b_use_iconv
 * @return string
 */
function unescape($str,$b_use_iconv=false)
{
	if ($b_use_iconv)
	{
		return unescape_iconv($str);
	}
	else
	{
		$CharEncoding=new __Encoding();
		$CharEncoding->SetGetEncoding("UTF-8")||die("编码名错误");
		$CharEncoding->SetToEncoding("GBK")||die("编码名错误");
		return $CharEncoding->EncodeString(_un_to_utf8($str));
	}
}




?>