<?php 
function yue_topic_enroll_button($topic_id=0)
{
	global $yue_login_id;
	
	$topic_enroll_obj    = POCO::singleton('pai_topic_enroll_class');
	
	$check = $topic_enroll_obj->check_topic_enroll($topic_id,$yue_login_id);
	
	if($check)
	{
		$button = '<button class="ui-button ui-button-primary ui-button-size-large ui-button-block ui-button-dark" style="width:120px;height:30px;line-height:30px;text-align:center;" data-role="nav-direct-confirm" data-url-style="inside" data-url="87" data-no-jump="false"> <span class="ui-button-content">已报名</span> </button>';
	}
	else
	{
		$button = '	<button class="ui-button ui-button-primary ui-button-size-large ui-button-block" style="width:120px;height:30px;line-height:30px;text-align:center;" data-role="nav-direct-confirm" data-url-style="inside" data-url="'.$topic_id.'" data-no-jump="false"> <span class="ui-button-content">我要报名</span> </button>';
	}
	
	
	$str = <<<EOT
		{$button}	
<script type="text/javascript">
    // 对应专题执行脚本
    (function(window,$)
    {
        $('[data-role="nav-direct-confirm"]').on('tap',function(ev)
        {
            var \$cur_btn = $(ev.currentTarget);
            var params = \$cur_btn.attr('data-url');
            var obj = eval("(" + params + ")");
            var topic_id= \$cur_btn.attr('data-url');
            var btn_txt = \$cur_btn.find('span').html();
            $.ajax
            ({
                url : './tool/topic_enroll.php',
                type: 'GET',
                dataType : 'json',
                cache : false,
                data :
                {
                    topic_id: topic_id
                },
                beforeSend : function()
                {
                    \$cur_btn.find('span').html('提交中...');
                },
                success : function(data)
                {
                    console.log(window);
                    if(data)
                    {
                        if(data.code)
                        {
                           \$cur_btn.addClass('ui-button-dark').find('span').html(data.msg);
                        }
                        else
                        {
                            \$cur_btn.addClass('ui-button-dark').find('span').html(data.msg);
                        }
                    }
                },
                error : function()
                {
                    \$cur_btn.find('span').html(btn_txt);
                }
            });
        });
    })(window,$);
</script>
EOT;
	return $str;
}


function mall_topic_enroll_button($topic_id)
{

    $str = <<<EOT
    <iframe name='frame' id='frame' style='display:none'></iframe>
	<a class="db" onclick="if(!confirm('确定要报名吗？')) { return false; }" href="http://www.yueus.com/action/topic_enroll.php?topic_id=$topic_id" target='frame'><div><button class="ui-button  ui-button-block ui-button-100per ui-button-size-x ui-button-bg-ff6" style="width:100px;margin:auto"><span class="ui-button-content">点击报名</span></button></div></a>

EOT;
    return $str;
}

?>