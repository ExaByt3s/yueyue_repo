
<script type="text/javascript">
    <?php
if ($_obj['page']['scene'] == "app"){
?>
        <?php
if ($_obj['page']['mode'] == "dev"){
?>
         window._page_mode = 'appdev';
		 window._action_mode = 'action';			
        <?php
} elseif ($_obj['page']['mode'] == "beta"){
?>
         window._page_mode = 'appbeta'; 
		 window._action_mode = 'action_beta';	
        <?php
} else {
?>
		 window._page_mode = 'app';    
		 window._action_mode = 'action';	
    <?php
}
?>
<?php
} else {
?>
    <?php
if ($_obj['page']['mode'] == "dev"){
?>
         window._page_mode = 'webdev';
		 window._action_mode = 'action_beta';	
    <?php
} elseif ($_obj['page']['mode'] == "beta"){
?>
         window._page_mode = 'webbeta';
		 window._action_mode = 'action_beta';
	<?php
} elseif ($_obj['page']['mode'] == "test"){
?>
         window._page_mode = 'webbeta';
		 window._action_mode = 'action_beta';
    <?php
} else {
?>
         window._page_mode = 'index.html';
		 window._action_mode = 'action';	
    <?php
}
?>
<?php
}
?>
</script>
<?php
if ($_obj['page']['scene'] == "app"){
?>
    <?php
if ($_obj['page']['mode'] == "dev"){
?>
	<script charset="utf-8" src="/mobile/js/libs/sea-package.js"></script>
	<script charset="utf-8" src="/mobile/js/libs/utility-package.js"></script>
    <?php
} elseif ($_obj['page']['mode'] == "beta"){
?>
	<!--<script src='http://192.168.18.60:8080//target/target-script-min.js#pai'></script>-->
	<script charset="utf-8" src="/mobile/package/resource/<?php
echo $_obj['version']['app'];
?>
/sea-package.js"></script>
	<script charset="utf-8" src="/mobile/package/resource/<?php
echo $_obj['version']['app'];
?>
/utility-package.js"></script>

    <?php
} else {
?>
<script charset="utf-8" src="/mobile/package/resource/<?php
echo $_obj['version']['app'];
?>
/sea-package.js"></script>
<script charset="utf-8" src="/mobile/package/resource/<?php
echo $_obj['version']['app'];
?>
/utility-package.js"></script>
    <?php
}
?>
<?php
} else {
?>
    <?php
if ($_obj['page']['mode'] == "dev"){
?>
<script charset="utf-8" src="/mobile/js/libs/cb-seajs-package.js"></script>
	<?php
} elseif ($_obj['page']['mode'] == "test"){
?>
	<script charset="utf-8" src="/mobile/package/resource/0.0.1-beta/sea-package.js"></script>
	<script charset="utf-8" src="/mobile/package/resource/0.0.1-beta/utility-package.js"></script>
    <?php
} else {
?>
<script charset="utf-8" src="/mobile/package/resource/<?php
echo $_obj['version']['app'];
?>
/sea-package.js"></script>
<script charset="utf-8" src="/mobile/package/resource/<?php
echo $_obj['version']['app'];
?>
/utility-package.js"></script>
    <?php
}
?>
<?php
}
?>


<script>
(function(window, seajs) {


    seajs.config({
        paths: {
<?php
if ($_obj['page']['scene'] == "app"){
?>
    <?php
if ($_obj['page']['mode'] == "dev"){
?>
            utility: '/mobile/js/libs/utility',
            matcha: '/mobile/js/libs/matcha'
			pai : '/mobile/dist' 
	<?php
} elseif ($_obj['page']['mode'] == "beta"){
?>
			utility: 'http://cb.poco.cn/utility',
            matcha: 'http://cb.poco.cn/matcha',
			pai : '/mobile/dist'
    <?php
} else {
?>
			pai : '/mobile/dist'
    <?php
}
?>
<?php
} else {
?>
            utility: '/mobile/js/libs/utility',
            matcha: '/mobile/js/libs/matcha',
            pai : '/mobile/dist' 
<?php
}
?>
        },

        alias: {
<?php
if ($_obj['page']['mode'] == "dev"){
?>
            '$': 'utility/zepto/1.1.4/zepto-debug',
            '$-debug': 'utility/zepto/1.1.4/zepto-debug',
            'data': 'utility/zepto/modules/data/1.1.4/data-debug',
            'data-debug': 'utility/zepto/modules/data/1.1.4/data-debug',

            'seajs-debug': 'http://cb-c.poco.cn/seajs/plugins/seajs-debug/1.1.1/seajs-debug',
            'seajs-log': 'http://cb-c.poco.cn/seajs/plugins/seajs-log/1.0.1/seajs-log',

            'backbone': 'utility/backbone/1.1.2/backbone-debug',
            'backbone-debug': 'utility/backbone/1.1.2/backbone-debug',
            'underscore': 'utility/underscore/1.6.0/underscore',
            'handlebars': 'utility/handlebars/1.3.0/handlebars',

            'hammer': 'utility/hammer/1.1.3/hammer',
            'hammer-debug': 'utility/hammer/1.1.3/hammer-debug',
            'jquery.hammer': 'utility/hammer/plugins/jquery.hammer/1.1.3/jquery.hammer',
            'jquery.hammer-debug': 'utility/hammer/plugins/jquery.hammer/1.1.3/jquery.hammer-debug',

            'iscroll': 'utility/iscroll/4.2.5/iscroll-debug',
            'iscroll-debug': 'utility/iscroll/4.2.5/iscroll-debug',

            'megapix-image': 'utility/megapix-image/megapix-image',
            'megapix-image-debug': 'utility/megapix-image/megapix-image-debug',

            'cookie': 'matcha/cookie/1.0.0/cookie',
            'cookie-debug': 'matcha/cookie/1.0.0/cookie-debug'
<?php
} else {
?>
            '$': 'utility/zepto/1.1.4/zepto',
            '$-debug': 'utility/zepto/1.1.4/zepto-debug'
<?php
}
?>
        },

<?php
if ($_obj['page']['mode'] == "dev"){
?>
        base : '/mobile/js/',
<?php
} else {
?>
       comboMaxLength: 5000,
<?php
}
?>

        charset: 'utf-8'
    });

<?php
if ($_obj['page']['mode'] == "dev"){
?>
    var TIME_STAMP = new Date().getTime();

    var _debug = JSON.stringify(window.localStorage.getItem('seajs-debug-config')).indexOf('debug:true');

    if(_debug != 2)
    {
       // cb�����Ӳ���Ҫ�������
        seajs.on('fetch', function(data) 
        {


            if(data.uri && !(/t=\d+/.test(data.uri)) && !/cb(?:-c|).poco.cn/.test(data.uri) && !/fastclick/.test(data.uri))
            {
                // use data.requestUri not data.uri to avoid combo & timestamp conflict
                // avoid too long url
                var uri = data.requestUri || data.uri
                data.requestUri = (uri + (uri.indexOf('?') === -1 ? '?t=' : '&t=') + TIME_STAMP).slice(0, 2000)
            }
        });

        seajs.on('define', function(data) 
        {
            if (data.uri) {
                // remove like ?t=12312 or ?
                data.uri = data.uri.replace(/[\?&]t*=*\d*$/g, '')
            }
        });     
    }

	

<?php
}
?>

<?php
if ($_obj['page']['scene'] == "app"){
?>
    <?php
if ($_obj['page']['mode'] == "dev"){
?>
        seajs.use(['app']);
    <?php
} elseif ($_obj['page']['mode'] == "beta"){
?>
		
        seajs.use(['pai/<?php
echo $_obj['version']['script'];
?>
/app-debug']);
    <?php
} else {
?>
        seajs.use(['pai/<?php
echo $_obj['version']['script'];
?>
/app']);
    <?php
}
?>
<?php
} else {
?>
    <?php
if ($_obj['page']['mode'] == "dev"){
?>
        seajs.use(['app']);
    <?php
} elseif ($_obj['page']['mode'] == "beta"){
?>
        
        var TIME_STAMP = new Date().getTime();

        seajs.on('fetch', function(data) 
        {

            if(/dist/.test(data.uri))    
            {
                console.log(data.uri);

                var uri = data.requestUri || data.uri;
                data.requestUri = (uri + (uri.indexOf('?') === -1 ? '?t=' : '&t=') + TIME_STAMP).slice(0, 2000);
            }

            
        });



        seajs.use(['pai/<?php
echo $_obj['version']['script'];
?>
/app-debug']);
    <?php
} else {
?>
        seajs.use(['pai/<?php
echo $_obj['version']['script'];
?>
/app']);
    <?php
}
?>
<?php
}
?>
})(window, seajs);


</script>


