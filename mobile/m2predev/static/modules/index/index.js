define('index', function(require, exports, module){ "use strict";

var header = require('common/widget/header/main');

var $ = require('components/zepto/zepto.js');

header.render(document.getElementById('header'));

console.log('Hi my name is index');
 
});