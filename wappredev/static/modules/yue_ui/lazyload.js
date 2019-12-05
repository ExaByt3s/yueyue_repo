define('yue_ui/lazyload', function(require, exports, module){ function LazyLoading(){

    LazyLoading.prototype.init = function(contain)
    {
        //传入父对象
        if(contain){
            this.container = contain;
            LazyLoading.prototype.freshELE(this.container);
            LazyLoading.prototype.engine();

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
        con ? this.currentELE = $(con).find('[data-preurl]') : this.currentELE = $(this.container).find('[data-preurl]');
    }

    LazyLoading.prototype.engine = function()
    {
        for (var i = 0; i < this.currentELE.length; i++){
            if (elementInViewport(this.currentELE[i])){
                loadImage(this.currentELE[i], function (){
                    this.currentELE.splice(i, i);
                });
            }
        }
    }

    function loadImage (el) {
        var img = new Image(),
            src = el.getAttribute('data-preurl');
        img.onload = function(){
            el.tagName == 'IMG' ? el.src = src : el.style.backgroundImage = 'url('+ src + ')';
        }
        img.src = src;
    }

    function elementInViewport(el) {
        var rect = el.getBoundingClientRect()
        return (
            rect.top >= 0 && rect.left >= 0 && rect.top <= (window.innerHeight || document.documentElement.clientHeight)
            )
    }
} 
});