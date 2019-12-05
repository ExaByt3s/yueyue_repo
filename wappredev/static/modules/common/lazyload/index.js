define('common/lazyload/index', function(require, exports, module){ function LazyLoading(){
    LazyLoading.prototype.init = function(contain)
    {
        //���븸����
        if(contain){
            this.container = contain;
            //�״δ���
            LazyLoading.prototype.freshELE(this.container);
            LazyLoading.prototype.engine();
            //���¼�
            $(window).scroll(function()
            {
                LazyLoading.prototype.engine();
            });
        }
        else{
            this.container = null;
        }
    }


    LazyLoading.prototype.freshELE = function(con){
        //�ⲿˢ�� ���븸Ԫ�� �ҵ�����[data-preurl]��Ԫ��
        con ? this.currentELE = $(con).find('[data-preurl]') : this.currentELE = $(this.container).find('[data-preurl]');
    }

    LazyLoading.prototype.engine = function(){
        //[data-preurl]Ԫ�ر�������ͼƬ
        for (var i = 0; i < this.currentELE.length; i++){
            if (elementInViewport(this.currentELE[i])){
                loadImage(this.currentELE[i], function (){
                    this.currentELE.splice(i, i);
                });
            }
        }
    }


    function loadImage (el) {
        //����ͼƬ ���ֱ�ǩ IMG �� ����[background-image]
        var img = new Image(),
            src = el.getAttribute('data-preurl');
        img.onload = function(){
            el.tagName == 'IMG' ? el.src = src : el.style.backgroundImage = 'url('+ src + ')';
        }
        img.src = src;
    }

    function elementInViewport(el) {
        //�ж�Ԫ���Ƿ���������ɼ���Χ��
        var rect = el.getBoundingClientRect()
        return (
            rect.top >= 0 && rect.left >= 0 && rect.top <= (window.innerHeight || document.documentElement.clientHeight)
            )
    }
}
return LazyLoading; 
});