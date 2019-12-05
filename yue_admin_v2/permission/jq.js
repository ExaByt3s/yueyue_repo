function mcfrm_submit()
{
	$.jBox.tip("正在提交...", 'loading');
}

function change_sub_op_cb(op_id, checked)
{
	if( checked )
	{
		checked = true;
	}
	else
	{  
		checked = false;
	}
	
	var op_list_obj = $('#role_op_list');
	op_list_obj.find('input[type="checkbox"][popid="'+op_id+'"]').each(function(){
		var $cb = $(this);
		if( $cb.length==1 )
		{
			$cb.attr('checked', checked);
			change_sub_op_cb($cb.val(), checked);
		}
	});
}

function change_parent_op_cb(popid)
{
	if( popid=='0' || popid==0 )
	{
		return ;
	}
	
	var op_list_obj = $('#role_op_list');
	var cur_obj = op_list_obj.find('input[type="checkbox"][value="'+popid+'"]');
	
	var cb_count = 0;
	op_list_obj.find('input[type="checkbox"][popid="'+popid+'"]').each(function(){
		if( $(this).attr('checked') || $(this).attr('tid')=='2' )
		{
			cb_count++;
		}
	});
	
	if( cb_count>0 )
	{
		cur_obj.attr('checked', true);
	}
	else
	{
		cur_obj.attr('checked', false);
	}
	
	change_parent_op_cb(cur_obj.attr('popid'));
}

$(function(){
	var op_list_obj = $('#role_op_list');
	op_list_obj.find('input[type="checkbox"]').click(function(){
		var cur_obj = $(this);
		change_sub_op_cb(cur_obj.val(), cur_obj.attr('checked'));
		change_parent_op_cb(cur_obj.attr('popid'));
	});	
});
