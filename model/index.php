<?
// modify hudw 2015.2.9
if($_COOKIE['yue_member_id'])
{
     header('location:edit_model_card.php');
}
else
{
     header('location:login.php');

}
?>