/**
 * @desc:   ��Jquery�ķ�������ˢ��ҳ�棬�༭���
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/19
 * @Time:   14:49
 * version: 1.0
 */
$(function() {
//��ҳ������bigclassname��ı�ǩ�ϼ���click����
    $(".bigclassname").click(function() {

        var objTD = $(this);

//�Ƚ��ϵ�������Ʊ�������,����trim����ȥ�����Ҷ���Ŀո�
        var oldText = $.trim(objTD.text());

//����һ��input�ı�ǩ����������Ϊ�������inputʧЧ����Ȼ�����κ����ֻ���ʧ��
        var input = $("<input type='text' value='" + oldText + "' />");

//��ǰtd�����ݱ�Ϊ�ı��򣬲��Ұ���������Ž�ȥ
        objTD.html(input);

//�����ı���ĵ���¼�ʧЧ
        input.click(function() {
            return false;
        });

//�����ı�����ʽ���ý�����ʾ�����Ի���
        input.css("font-size", "16px");
        input.css("text-align", "center");
        input.css("background-color", "#ffffff");
        input.width("120px");

//�Զ�ѡ���ı����е�����
        input.select();

//�ı���ʧȥ����ʱ���±�Ϊ�ı�
        input.blur(function() {

//���������������
            var newText = $(this).val();

//���µ�����������滻֮ǰ������������״̬
            objTD.html(newText);

//��ȡ�����������Ӧ��ID(bigclassid)
            var bigclassid = objTD.attr("data-id");

//���µ����������ת�룬��Ȼ�����Լ����ݿ���ʾ�Ķ���"???"����������
            //newText = escape(newText);
            newText = encodeURIComponent(newText);

//��ȡҪ����"һ�㴦���ļ�"(update_bigclassname_2.php)�е�URL
            var url = "index.php?id=" + bigclassid + "&apply_for_name=" +newText+"&act=update";

//AJAX�첽�������ݿ�,dataΪ�ɹ���Ļص�����ֵ��������ʾ��ʾ��Ϣ
            $.post(url, function(data) {});

        });
    });
    });
