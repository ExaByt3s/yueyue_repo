<?php
/**
 * 离线应用缓存
 */

header('Content-type: text/cache-manifest; charset=UTF-8');

$version = require_once('/disk/data/htdocs232/poco/pai/mobile/config/version_control.conf.php');
?>
CACHE MANIFEST

<?php
if (G_PAI_APP_PAGE_MODE === 'dev') {
?>
# rev <?php echo time()?>
<?php
} else {
?>
# rev <?php echo $version['beta']['cache_ver']?>
<?php
}
?>



#CACHE:
<?php
if (G_PAI_APP_PAGE_MODE === 'dev') {
?>

<?php
} else {
?>

http://yp.yueus.com/mobile/package/resource/0.0.1-beta/sea-package.js
http://yp.yueus.com/mobile/package/resource/0.0.1-beta/utility-package.js
http://yp.yueus.com/mobile/dist/0.0.1-beta/app-debug.js?<?php echo $version['beta']['cache_ver']?> 

http://cb.poco.cn/??utility/zepto/1.1.4/zepto-debug.js,utility/zepto/modules/data/1.1.4/data-debug.js,utility/backbone/1.1.2/backbone-debug.js,utility/hammer/plugins/jquery.hammer/1.1.3/jquery.hammer-debug.js,utility/underscore/1.6.0/underscore-debug.js,utility/handlebars/1.3.0/runtime-debug.js,utility/iscroll/4.2.5/iscroll-debug.js,matcha/cookie/1.0.0/cookie-debug.js

http://cb.poco.cn/utility/hammer/1.1.3/hammer-debug.js

http://yp.yueus.com/mobile/js/libs/??utility/zepto/1.1.4/zepto-debug.js,utility/zepto/modules/data/1.1.4/data-debug.js,utility/backbone/1.1.2/backbone-debug.js,utility/hammer/plugins/jquery.hammer/1.1.3/jquery.hammer-debug.js,utility/underscore/1.6.0/underscore-debug.js,utility/handlebars/1.3.0/runtime-debug.js,utility/iscroll/4.2.5/iscroll-debug.js,matcha/cookie/1.0.0/cookie-debug.js

http://yp.yueus.com/mobile/js/libs/utility/hammer/1.1.3/hammer-debug.js


<?php
}
?>

NETWORK:
*

FALLBACK:
*