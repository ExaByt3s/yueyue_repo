<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=GBK">
<script type="text/javascript" src="./js/jquery.min.js"></script>
<title>ǰ̨�������ܴ���</title>
<link rel="stylesheet" type="text/css" href="css/style.css">
<script src="js/shadowbox/shadowbox.js" type="text/javascript"></script>
<link href="js/shadowbox/shadowbox.css" rel="stylesheet" type="text/css" />
<style>
    #search { margin: 20px;}
</style>
</head>
<body>
    <div id="search">
        <form action="goods_search_for_front.php?type_id=<?php echo $type_id;?>" method="post" >
            <?php if( ! empty($rs) ):?>
                <?php foreach($rs as $k => $v):?>
                    <span><?php echo $v['text'];?>:</span>
                    <select name="<?php echo $v['name'];?>" onchange="show_child(this);">
                        <?php foreach($v['data'] as $dk => $dv):?>
                        <option value="<?php echo $dv['key'];?>" <?php if( ! empty($v['function_param'])):?> <?php if($select_input['detail'][$v['function_param']['parent_id']] == $dv['key']):?> selected <?php endif;?> <?php else:?> <?php if($select_input[$v['name']] == $dv['key']):?> selected <?php endif;?> <?php endif;?>><?php echo $dv['val'];?></option>
                                <?php if( ! empty($dv['child_data']) ):?>
                                        <?php foreach($dv['child_data']['data'] as $ck => $cv):?>
                                            <option style="display: none;" class="<?php echo $dv['key'];?>" is_selected="<?php if($select_input['third'][$dv['key']]== $cv['key']):?> selected <?php else:?> '' <?php endif;?>" child_name="<?php echo $dv['child_data']['name'];?>" value="<?php echo $cv['key'];?>">
                                                <?php echo $cv['val'];?>
                                            </option>
                                         <?php endforeach;?>
                                <?php endif;?>
                        <?php endforeach;?>
                    </select>
                <?php endforeach;?>
            <?php endif;?>
            <input type="hidden" name="type_id" value="<?php echo $type_id;?>"/>
            <input type="submit" value="search" style="margin-left:12px;" />
            <a href="goods_search_for_front.php?type_id=<?php echo $type_id;?>" style="margin-left:100px;">�����������</a>
        </form>
    </div>
    <div id="tabs">
        <div class="tabbox">
          <div class="table-list">
            <table width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th width="8%">�û�D</th>
                  <th>��Ʒ����</th>
                  <th width="8%">����</th>
                  <th width="8%">�۸�</th>
                  <th width="8%">����</th>
                  <th width="15%">ʱ��</th>
                  <th width="5%">���¼�</th>
                  <th>����</th>			  
                  <?php if($type_id == 3):?>
                    <th>��ױ����</th>
                  <?php elseif($type_id == 31):?>
                    <th>����Ա</th>
                    <th>���</th>
                    <th>�ֱ�</th>
                 <?php elseif($type_id == 40):?>
                    <th>��������-������</th>
                 <?php elseif($type_id == 12):?>
                    <th>����</th>
                    <th>���</th>
                  <?php elseif($type_id == 5):?>
                    <th>��ʦ����</th>
                    <th>��ѵ����</th>
                    <th>�ڿη�ʽ</th>
                    <th>�γ�����</th>
                  <?php endif;?>
                  <th width="8%">����</th>
                </tr>
              </thead>
              <tbody>
                <?php if( ! empty($list)):?>
                <?php foreach($list as $k => $v):?>  
                <tr>

                  <td width="8%" align="center"><?php echo $v['user_name'];?></td>
                  <td align="center">
                  <a rel="shadowbox[goods_modbile_<?php echo $v['goods_id'];?>]" title="yueyue://goto?type=inner_app&pid=1220102&goods_id=<?php echo $v['goods_id'];?>" href="http://www.yueus.com/mall/user/goods/service_detail.php?goods_id=<?php echo $v['goods_id'];?>" >
                  <img src='<?php echo $v['images'];?>' width='100px'/>
                  </a><br />
                    ��Ʒ����:[ <?php echo $v['goods_id'];?> ]<br />
                    <?php if($v['is_black']== '1'):?>
                    <font color="#FF0000">[ ������ʾ ]</font><br>
                    <?php endif;?>
                    <?php echo $v['titles'];?> </td>
                  <td width="8%" align="center"><?php echo $v['location_name'];?></td>
                  <td width="8%" align="right"><?php echo $v['prices'];?></td>
                  <td width="8%" align="center"><?php echo $v['type_name'];?></td>
                  <td width="15%" align="left">
                  ��:<?php echo $v['add_time'];?><br>
                  ��:<?php echo $v['audit_time'];?><br>
                  ��:<?php echo $v['onsale_time'];?>
                  </td>
                  <td width="5%" align="center">
                  <?php echo $v['status_name'];?><br>
                    <?php if($v['is_show'] == 1):?>
                    <a rel="shadowbox[goods_show_<?php echo $v['goods_id'];?>];height=240;width=420" href="goods.php?action=chshow&id=<?php echo $v['goods_id'];?>&status=2&note=show">
                    <?php else:?>
                    <a href="javascript:change_show(<?php echo $v['goods_id'];?>,1)">
                    <?php endif;?>
                    <?php echo $v['show_name'];?>
                    </a>
                  </td>
                  <td width="5%" align="center"><?php echo $v['score'];?></td>

                  <?php if($type_id == 3):?>
                    <td align="center"><?php echo $v['goods_att']['152'];?></td>
                  <?php elseif($type_id == 31):?>
                    <td width="5%" align="center"><?php echo $v['belong_user'];?></td>
                    <td width="5%" align="center"><?php echo $v['profile_data']['0']['att_data_format']['m_height']['value'];?>CM</td>
                    <td width="5%" align="center"><?php echo $v['profile_data']['0']['att_data_format']['m_cups']['value'];?> <?php echo $v['profile_data']['0']['att_data_format']['m_cup']['value'];?></td>
                  <?php elseif($type_id == 40):?>
                    <td align="center"><?php echo $v['goods_att']['90'];?></td>
                  <?php elseif($type_id == 12):?>
                    <td width="5%" align="center"><?php echo $v['goods_att']['20'];?></td>
                    <td width="5%" align="center"><?php echo $v['goods_att']['19'];?>ƽ��</td>
                  <?php elseif($type_id == 5):?>
                    <td width="5%" align="center"><?php echo $v['profile_data']['0']['att_data_format']['t_teacher']['value'];?></td>
                    <td width="5%" align="center"><?php echo $v['profile_data']['0']['att_data_format']['t_experience']['value'];?></td>
                    <td width="5%" align="center"><?php echo $v['goods_att']['62'];?></td>
                    <td width="5%" align="center"><?php echo $v['goods_att']['133'];?></td>
                  <?php endif;?>


                  <td width="8%" align="center">
                  <?php if($v['seller_status'] == 1):?>
                      <?php if($v['goods_status'] == 0):?>
                        <a href="javascript:change_status(<?php echo $v['goods_id'];?>,1)">ͨ��</a><br>
                        <a rel="shadowbox[goods_change_<?php echo $v['goods_id'];?>];height=240;width=420" href="goods.php?action=chstatus&id=<?php echo $v['goods_id'];?>&status=2&note=show">��ͨ��</a><br>
                        <!--<a href="javascript:change_status(<?php echo $v['goods_id'];?>,3)">ɾ��</a><br>-->
                        <?php elseif($v['goods_status'] == 1):?>
                        <a rel="shadowbox[goods_change_<?php echo $v['goods_id'];?>];height=240;width=420" href="goods.php?action=chstatus&id=<?php echo $v['goods_id'];?>&status=2&note=show">��ͨ��</a><br>
                        <!--
                        <a href="javascript:change_status(<?php echo $v['goods_id'];?>,3)">ɾ��</a><br>
                        -->
                        <?php elseif($v['goods_status'] == 2):?>
                        <a href="javascript:change_status(<?php echo $v['goods_id'];?>,1)">ͨ��</a><br>
                        <!--
                        <a href="javascript:change_status(<?php echo $v['goods_id'];?>,3)">ɾ��</a><br>
                        -->
                      <?php endif;?>
                  <?php endif;?>
                    <a rel="shadowbox[goods_edit_<?php echo $v['goods_id'];?>]" href="goods.php?action=edit&id=<?php echo $v['goods_id'];?>">����</a></td>
                </tr>
                <?php endforeach;?>  
                <?php endif;?>
              </tbody>
            </table>
          </div>
        </div>
        <div id="pages" class="page"> <?php echo $page;?> </div>  
    </div>
    
</body>
</html>
<script>
Shadowbox.init({ 
    handleOversize: "drag", 
	overlayColor: '#000',
    modal: true,
    displayNav: true,
	displayCounter: false
}); 
Shadowbox.setup();    
function show_child(obj)
{
    $(obj).next("select").remove();
    var id = $(obj).val();
    var len = $("."+id).length;
    if(len == 0)
    {
        return false;
    }
    var name = $("."+id).eq(0).attr("child_name");
    var html = "<select name='"+name+"'>";
    for(var i = 0; i<=len;i++)
    {
        var value = $("."+id).eq(i).attr("value");
        var text = $("."+id).eq(i).text();
        var is_selected = $("."+id).eq(i).attr("is_selected");
        html+= "<option value='"+value+"' "+is_selected+" >"+text+"</option>";
    }
    
    html += "</select>";
    
    var $obj_html = $(html);
    
    $obj_html.insertAfter($(obj));
    
    return false;
}

function change_status(id,status)
{
	if(!confirm('�Ƿ�ȷ���޸�״̬��'))
	{
	   return;
    }
	$.ajax({   
			url:'goods.php?action=chstatus&id='+id+'&status='+status,
			type:'post',    
			cache:false,    
			dataType:'json',    
			success:function(data){    
			    alert(data.message);
			    /*   
				if(data.result==1){   
					alert('�޸ĳɹ�');
					location.reload();
				}else{    
					alert(data.message);  
				}
				*/    
           },    
            error : function(){    
                 alert("�쳣��");
            }  
    });  
}
function change_show(id,status)
{
	var $con_var = '�Ƿ�ȷ���ϼ�?';
	if(status==2)
	{
		$con_var = '�Ƿ�ȷ���¼�?';
    }
	if(!confirm($con_var))
	{
	   return;
    }
	$.ajax({    
			url:'goods.php?action=chshow&id='+id+'&status='+status,
			type:'post',    
			cache:false,    
			dataType:'json',    
			success:function(data){   
			    alert(data.message);
			    /*   
				if(data.result==1){    
					alert('�޸ĳɹ�');
					location.reload();
				}else{    
					alert(data.message);  
				}
				*/    
             },    
			 error : function(){    
				  alert("�쳣��");
             }    
    });  
}

$(function(){
    <?php if($select_input['third']):?>
            var objs_length = $("select").length;
            for(var i = 0; i <=objs_length;i++)
            {
                var obj = $("select").eq(i);
                show_child(obj);
            }
    <?php endif;?>
})
</script>
