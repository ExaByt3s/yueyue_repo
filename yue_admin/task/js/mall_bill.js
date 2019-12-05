$(function () {
    $(".ui_timepicker").datetimepicker({
        showSecond: true,
        timeFormat: '',
        stepHour: 1,
        stepMinute: 1,
        stepSecond: 1
    });
    $(".pay_time_timepicker").datetimepicker({
        showSecond: true,
        timeFormat: '',
        stepHour: 1,
        stepMinute: 1,
        stepSecond: 1
    });
    $(".add_time_timepicker").datetimepicker({
        showSecond: true,
        timeFormat: '',
        stepHour: 1,
        stepMinute: 1,
        stepSecond: 1
    });
    $("#search_keyword_option").change(function () {
        var input_name = $(this).children('option:selected').attr("data-val");
        $("#search_keyword_input").attr("name", input_name);
        $("#search_selected").val(input_name);
    });
});
/**
 * ���¹���Ϊ�رն���
 * yue_admin/task/mall_bill.php
 * @param order_sn
 */
function close_order(order_sn) {
    var dialog_form = $("#dialog-form");
    var reason = $("#reason");
    dialog_form.dialog("open");
    dialog_form.dialog({
        autoOpen: false,
        height: 300,
        width: 350,
        modal: true,
        buttons: {
            "ȷ��ȡ��": function () {
                var bValid = true;
                var reason_srt = encodeURIComponent(reason.val());
                bValid = bValid && checkLength(reason, "reason", 3, 200);
                if (bValid) {
                    $.ajax({
                        url: 'mall_bill.php?action=close_order&order_sn=' + order_sn,
                        type: 'post',
                        cache: false,
                        dataType: 'json',
                        data: {reason: reason.val()},
                        success: function (data) {
                            if (data.result == 1) {
                                $(this).dialog("close");
                                location.reload();
                            }
                            else {
                                $(this).dialog("close");
                                console.log(data);
                                alert('ʧ����');
                            }
                        },
                        error: function () {
                            alert("�쳣��");
                        }
                    });
                }
            }
        },
        close: function () {
        }
    });
}
/**
 * ���¹���Ϊ ǩ��
 * yue_admin/task/mall_bill.php
 * @param order_sn
 */
function sign_order(order_sn) {
		$.get("mall_bill.php", { action: "sign_order", order_sn: order_sn },
  function(data){
    if (data) {
		alert(data);
	}
		else {
			alert("no")
		}
  });   
}

/**
 * ���¹���Ϊ ���ܶ���
 * yue_admin/task/mall_bill.php
 * @param order_sn
 */
function accept_order(order_sn) {
	$.get("mall_bill.php", { action: "accept_order", order_sn: order_sn },
  function(data){
    if (data) {
		alert(data);
	}
		else {
			alert("no")
		}
  });      
}

function checkLength(o, n, min, max) {
    if (o.val().length < 3) {
        o.addClass("ui-state-error");
        updateTips("ȡ��ԭ�����");
        return false;
    } else {
        return true;
    }
}

function updateTips(t) {
    var tips = $(".validateTips");
    tips.text(t).addClass("ui-state-highlight");
    setTimeout(function () {
        tips.removeClass("ui-state-highlight", 1500);
    }, 500);
}

function search_order() {
    this.form.target = '';
    $('#action').attr('value', 'list');
    this.form.submit();
}


