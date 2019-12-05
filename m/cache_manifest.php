<?php
/**
 * 离线应用缓存
 */

header('Content-type: text/cache-manifest; charset=UTF-8');

$version = require_once('/disk/data/htdocs232/poco/pai/m/config/version_control.conf.php');
?>
CACHE MANIFEST

<?php
if (G_PAI_APP_PAGE_MODE === 'dev') {

?>

# rev <?php echo $version['dev']['cache_time']?>

<?php
} else if (G_PAI_APP_PAGE_MODE === 'wx'){
?>

# rev <?php echo $version['wx']['cache_time']?>



<?php
} else if  (G_PAI_APP_PAGE_MODE === 'beta'){
?>

# rev <?php echo $version['beta']['cache_time']?>




<?php
}	
?>


# rev <?php echo $version['wx']['cache_time']?>

#CACHE:
http://yp.yueus.com/m/js/libs/sea-package.js
http://yp.yueus.com/m/js/libs/utility-package.js
http://yp.yueus.com/m/js/libs/cb-seajs-package.js

http://cb.poco.cn/??utility/zepto/1.1.4/zepto-debug.js,utility/zepto/modules/data/1.1.4/data-debug.js,utility/backbone/1.1.2/backbone-debug.js,utility/hammer/plugins/jquery.hammer/1.1.3/jquery.hammer-debug.js,utility/underscore/1.6.0/underscore-debug.js,utility/handlebars/1.3.0/runtime-debug.js,utility/iscroll/4.2.5/iscroll-debug.js,matcha/cookie/1.0.0/cookie-debug.js

http://cb.poco.cn/utility/hammer/1.1.3/hammer-debug.js

http://yp.yueus.com/mobile/js/libs/??utility/zepto/1.1.4/zepto-debug.js,utility/zepto/modules/data/1.1.4/data-debug.js,utility/backbone/1.1.2/backbone-debug.js,utility/hammer/plugins/jquery.hammer/1.1.3/jquery.hammer-debug.js,utility/underscore/1.6.0/underscore-debug.js,utility/handlebars/1.3.0/runtime-debug.js,utility/iscroll/4.2.5/iscroll-debug.js,matcha/cookie/1.0.0/cookie-debug.js

http://yp.yueus.com/m/js/libs/utility/hammer/1.1.3/hammer-debug.js

http://yp.yueus.com/mobile/js/libs/??utility/zepto/1.1.4/zepto.js,utility/zepto/modules/data/1.1.4/data.js,utility/backbone/1.1.2/backbone.js,utility/hammer/plugins/jquery.hammer/1.1.3/jquery.hammer.js,utility/underscore/1.6.0/underscore.js,utility/handlebars/1.3.0/runtime.js,utility/iscroll/4.2.5/iscroll.js,matcha/cookie/1.0.0/cookie.js

http://yp.yueus.com/mobile/js/libs/utility/hammer/1.1.3/hammer.js

<?php
if (G_PAI_APP_PAGE_MODE === 'dev') {
?>

<?php
} else if (G_PAI_APP_PAGE_MODE === 'wx'){
?>

http://yp.yueus.com/m/dist/<?php echo $version['wx']['script']?>/app-debug.js?<?php echo $version['wx']['cache_time']?>

http://yp.yueus.com/m/dist/<?php echo $version['wx']['script']?>/app.js?<?php echo $version['wx']['cache_time']?>

 


<?php
} else if  (G_PAI_APP_PAGE_MODE === 'beta'){
?>

http://yp.yueus.com/m/dist/<?php echo $version['beta']['script']?>/app-debug.js?<?php echo $version['beta']['cache_time']?>

http://yp.yueus.com/m/dist/<?php echo $version['beta']['script']?>/app.js?<?php echo $version['beta']['cache_time']?>




<?php
}	
?>

NETWORK:
*

FALLBACK:
*