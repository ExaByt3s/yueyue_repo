<?php

header("Access-Control-Allow-Origin:*"); 
header ("Content-type:Application/x-javascript;");

?>

window.onerror = function(msg,url,line,col,error){
    //û��URL���ϱ����ϱ�Ҳ��֪������
    if (msg != "Script error." && !url){
        return true;
    }
    //�����첽�ķ�ʽ
    //����������window.onunload����ajax�Ķ����ϱ�
    //���ڿͻ���ǿ�ƹر�webview������ζ����ϱ���Network Error
    //�Ҳ²�����window.onerror��ִ�����ڹر�ǰ�Ǳ�Ȼִ�е�
    //���뿪����֮����ϱ�����ҵ����˵�ǿɶ�ʧ��
    //�����Ұ������ִ�����ŵ��첽�¼�ȥִ��
    //�ű����쳣��������10��
    setTimeout(function(){
        console.log(msg,url,line,col,error)
    },0);

    return true;
};


<?php 

?>