<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=GBK">
<script type="text/javascript" src="./js/jquery.min.js"></script>
<title>�������������ļ�����</title>

<style>
    #search { margin: 20px;}
</style>
</head>
<body>
    <form action="" method="post" >
        <table>
            <?php if( ! empty($property_for_search_config[$type_id])):?>
                <?php $j = 0;$j_len = count($property_for_search_config[$type_id]);?>
                <?php foreach($property_for_search_config[$type_id] as $k => $v):?>
                    <tr>
                        <td style="vertical-align: top;">��������:<input type="text" value="<?php echo $v['text'];?>"/></td>
                        <td style="vertical-align:top;">����name:<input type="text" value="<?php echo $v['name'];?>"/></td>
                        <td style="vertical-align:top;">��������:<input type="text" style="width:20px;" value="<?php echo $v['data_type'];?>" /><span style="color:green;">1Ϊϵͳ����2Ϊ��������</span></td>
                        <td style="vertical-align:top;">��������:<input type="text" style="width:20px;" value="<?php echo $v['select_type'];?>" /><span style="color:green;">1Ϊ��ѡ2Ϊ��ѡ</span></td>
                        <td>
                            <?php if( ! empty($v['function_param']) ):?>
                                <span>type_id:<?php echo $v['function_param']['type_id'];?></span><span>parents_id:<input type="text" style="width:40px;" value="<?php echo $v['function_param']['parent_id'];?>"/></span>
                            <?php else:?>
                                <?php $i= 0; $len = count($v['data']);?>
                                <?php foreach($v['data'] as $dk => $dv):?>
                                <span>key:<input type="text" value="<?php echo $dv['key'];?>"</span>
                                <span>value:<input type="text" value="<?php echo $dv['val'];?>"</span>
                                &nbsp;<button>X��</button>
                                <?php $i++;?>
                                <?php if($i==$len):?>
                                &nbsp;<button>��������</button>
                                <?php endif;?>
                                
                                </br>
                                <?php endforeach;?>
                            <?php endif;?>
                        </td>
                        <td style="vertical-align: top;"><button>X����</button></td>
                    </tr>
                    <?php $j++;?>
                    <?php if($j == $j_len):?>
                        <tr>
                            <td style="vertical-align: top;"><button>��������</button></td>
                        </tr>
                    <?php endif;?>
                <?php endforeach;?>
            <?php endif;?>
        </table>
        <input type="submit" value="submit"/>
    </form>
</body>
</html>
