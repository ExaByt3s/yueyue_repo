<?
//����û�������
function check($userid,$password,$conn=""){
	$query="select count(*) from users where userid='".$userid."' and pass='".md5($password)."'";
	if($conn){
		$res=mysql_query($query,$conn);
	}
	else{
		$res=mysql_query($query);
	}
	list($tmp)=mysql_fetch_row($res);
	if($tmp){
		return true;
	}
	return false;
}
//�޸��û�����
function changepassword($userid,$old_password,$new_password,$conn=""){
	if(check($userid,$old_password,$conn)){
		$query="update users set pass='".md5($new_password)."' where userid='{$userid}' and valid=1";
		if($conn){
			$res=mysql_query($query,$conn);
		}
		else{
			$res=mysql_query($query);
		}
		if(mysql_error()){
			return 2; //���ݿ����
		}
		return 0; //���³ɹ�
	}
	return 1; //�û������벻��
}
//����û�
function adduser($userid,$name,$pass,$group,$mode,$conn=""){
	$sql_adduser="insert into users(userid,name,pass,groupid,mode) values('{$userid}','{$name}','".md5($pass)."','{$group}','{$mode}')";
	if($conn){
		mysql_query($sql_adduser,$conn);
	}
	else {
		mysql_query($sql_adduser);
	}
	if(mysql_error()){
		return 2; //���ݿ����
	}
	return 0; //��ӳɹ�
}
//�Ƿ���Ȩ��
function checkmode($userid,$modeid,$conn=""){
	//�ҳ����ܷ���
	$selectsid="select fid,mode_name from modes where sid=0";
	$res=mysql_query($selectsid);
	$mode_sort=array();
	while (list($fid,$mode_name)=mysql_fetch_row($res)) {
		$mode_sort[$fid]=$mode_name;	
	}
	mysql_free_result($res);
	//����û���������Լ�Ȩ��
	$selectuser="select users.name,groups.group_name,users.mode,groups.mode from users,groups where users.groupid=groups.groupid and users.valid=1 and groups.valid=1 and users.userid='".$_SESSION["userid"]."'";
	$res_user=mysql_query($selectuser);
	if(!list($username,$groupname,$usermode,$groupmode)=mysql_fetch_row($res_user)){
		return 1;
	}
	mysql_free_result($res_user);
}
//�޸��û���Ϣ
function changeinfo($name,$mail,$phone,$qq,$msn,$conn){
	$update="update users set name='".$name."',mail='".$mail."',phone='".$phone."',qq='".$qq."',msn='".$msn."' where userid='".$_SESSION["userid"]."'";
	if($conn){
		mysql_query($update,$conn);
	}
	else {
		mysql_query($update);
	}
	if(mysql_error()){
		return 2;//���ݿ����
	}
	return 0;//���ĳɹ�
}
//�޸��û�Ȩ��
function UpdateUsers($uid,$userid,$username,$usergroup,$usermodes,$conn)
{
	$upsql="update users set userid='".$userid."',name='".$username."',groupid='".$usergroup."',mode='".$usermodes."' where id='".$uid."'";
	if($conn)
	{
		mysql_query($upsql,$conn);
	}
	else
	{
		return 2;//���ݿ�����ʧ��
	}
	if(mysql_error())
	{
		return 1;//���ݿ��������
	}
	return 0;
}
//ǿ���޸�����
function UPpass($uid,$pass,$conn)
{
	$upsql="update users set pass='".md5($pass)."' where id='".$uid."'";
	if($conn)
	{
		mysql_query($upsql,$conn);
	}
	else
	{
		return 2;//���ݿ�����ʧ��
	}
	if(mysql_error())
	{
		return 1;//���ݿ��������
	}
	return 0;
}
?>