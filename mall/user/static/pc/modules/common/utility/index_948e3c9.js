define("common/utility/index",function(p){function e(p,e){var i="";e=e||"",i=-1!=n.inArray(e,[120,320,165,640,600,145,440,230,260])?"_"+e:"";var g=/^http:\/\/(img|image)\d*(-c|-wap|-d)?(.poco.cn.*|.yueus.com.*)\.jpg|gif|png|bmp/i,r=/_165.jpg|_320.jpg|_640.jpg|_120.jpg|_600.jpg|_145.jpg|_260.jpg|_440.jpg/i;return g.test(p)&&(p=r.test(p)?p.replace(r,i+".jpg"):p.replace("/(.d*).jpg|.jpg|.gif|.png|.bmp/i",i+".jpg")),p}var n=p("components/jquery/jquery.js"),i=(n(window),p("common/placeholder/index"),{fix_placeholder:function(){n("input").placeholder()},matching_img_size:function(p,n){var i=n;return e(p,i)}});return i});