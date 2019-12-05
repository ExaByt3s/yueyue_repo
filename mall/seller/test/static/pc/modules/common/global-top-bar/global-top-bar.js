define('common/global-top-bar/global-top-bar', function(require, exports, module){ 'use strict';

var $ = require('components/jquery/jquery.js');
$(document).ready(function() {  
    // $('[menu-item-trigger]').hover(function() {
    //     $(this).addClass('dropdown-menu-hover');
    // }, function() {
    //     $(this).removeClass('dropdown-menu-hover');
    // });
    // 
    $('[menu-item-trigger]').hover(function() {
            $(this).addClass('dropdown-menu-hover'); 
            $(this).stop().find(".dropdown-menu").slideDown(100);
        }, function() {
            $(this).removeClass('dropdown-menu-hover');
            $(this).stop().find(".dropdown-menu").slideUp(100);
    });
  
}); 
});