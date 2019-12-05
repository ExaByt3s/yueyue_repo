function LazyLoading(){
    LazyLoading.prototype.init = function(contain)
    {
        //传入父对象
        if(contain){
            this.container = contain;
            //首次触发
            LazyLoading.prototype.freshELE(this.container);
            LazyLoading.prototype.engine();
            //绑定事件
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
        //外部刷新 传入父元素 找到具有[data-preurl]的元素
        con ? this.currentELE = $(con).find('[data-preurl]') : this.currentELE = $(this.container).find('[data-preurl]');
    }

    LazyLoading.prototype.engine = function(){
        //[data-preurl]元素遍历加载图片
        for (var i = 0; i < this.currentELE.length; i++){
            if (elementInViewport(this.currentELE[i])){
                loadImage(this.currentELE[i], function (){
                    this.currentELE.splice(i, i);
                });
            }
        }
    }


    function loadImage (el) {
        //加载图片 区分标签 IMG 和 其他[background-image]
        var img = new Image(),
            src = el.getAttribute('data-preurl');
        img.onload = function(){
            el.tagName == 'IMG' ? el.src = src : el.style.backgroundImage = 'url('+ src + ')';
        }
        img.src = src;
    }

    function elementInViewport(el) {
        //判断元素是否处于浏览器可见范围中
        var rect = el.getBoundingClientRect()
        return (
            rect.top >= 0 && rect.left >= 0 && rect.top <= (window.innerHeight || document.documentElement.clientHeight)
            )
    }
}
return LazyLoading;