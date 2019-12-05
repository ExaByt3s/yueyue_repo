/**
 * @author hudw
   2015.4
   fis pure config 设置
**/

var pk = require('./package.json');

// 配置开发模式
var file = fis.file.wrap('public_tag.txt');
var mode = file.getContent();
var domain  = '';

switch(mode.trim())
{
	case 'is_dev':
		domain = pk.dev_domain;
		break;
	case 'is_predev':
		domain = pk.predev_domain;
		break;
	case 'is_pub':
		domain = pk.pub_domain;
		break;
	case 'is_pub_test':
		domain = pk.pub_test_domain;
		break;
	default:
		domain = pk.dev_domain;		 
}



//如果要兼容低版本ie显示透明png图片，请使用pngquant作为图片压缩器，
//否则png图片透明部分在ie下会显示灰色背景
//fis.config.set('settings.optimzier.png-compressor.type', 'pngquant');

//设置jshint插件要排除检查的文件，默认不检查lib、jquery、backbone、underscore等文件
//使用pure release命令时，添加--lint或-l参数即可生效
fis.config.set('settings.lint.jshint.ignored', [ 'lib/**', /jquery|backbone|underscore/i ]);

//设置项目编码格式
fis.config.set('project.charset', 'gbk');

//设置合并格式
fis.config.set('modules.spriter', 'csssprites');

/***** SASS配置 START *****/

//项目排除掉 指定文件_xxx.scss，不对其进行构建
fis.config.set('project.exclude', ['**/_*.scss','/**.cmd','tpl/**.tpl.html']);
//scss后缀的文件，用fis-parser-sass插件编译
fis.config.set('modules.parser.scss', 'sass');
//scss文件产出为css文件
fis.config.set('roadmap.ext.scss', 'css');
fis.config.set('settings.parser.sass', 
{
    sourceMap:true,
    // 加入文件查找目录
    include_paths: []
});

/***** SASS配置 END *****/

/***** fis 核心配置 参数 *****/
// 因为fis架构的原因，本来合并图片的功能要重新设置，所以用unshift 插入新的设置，确保覆盖了默认的设置
fis.config.get('roadmap.path').unshift({
    reg: '**.scss',
    //配置useSprite表示reg匹配到的css需要进行图片合并
    useSprite: true,
	release : '/static/$&'
});

fis.config.get('roadmap.path').unshift({
    reg: '**.png',
    release: '/static/$&'
});

fis.config.get('roadmap.path').unshift({
    reg: /^\/style\/*\/(.*\.png)$/i,
    release: '/static/image/task/$1'
});

fis.config.get('roadmap.path').unshift({
    reg : '/lib/DatePicker/My97DatePicker.htm',
    release : '/static/lib/DatePicker/My97DatePicker.htm'
});


// 进行config配置
fis.config.merge({
	modules : {
        parser : {
            //.tmpl后缀的文件使用fis-parser-handlebars插件编译
            tmpl : 'handlebars'
        }
    },
    settings : {
        parser : {
            handlebars : {
                useData: true
            }
        }
    },
    roadmap : 
	{
		//静态资源域名，使用pure release命令时，添加--domains或-D参数即可生效
        domain : domain,
		//fis允许在前端开发中使用sass 等非标准语言，可以设置这类语言文件作为引用文件，并且作为对应产出文件
		ext : 
		{
			'scss' : 'css'
		}
    },
	path : [
            {
                //所有的style目录下面的css文件
                reg : '/style/**.css',
                //发布到/static/css/xxx目录下
                release : '/static/style$&',
				//使用域名控制
				useDomain : true,				
				useSprite: true
            },
			{
                //cmd文件不编译
                reg : '**.cmd',
                //编译的时候不要产出了
                release : false
            },			
			{
                //tpl目录文件不编译
                reg : '/tpl/$',
                //编译的时候不要产出了
                release : false
            },
			{
				reg: '**.handlebars',
				isJsLike: true,
				release : false
			},
			{
				reg : '**.tmpl',
				release : true,
				useDomain : true
			}
    ]
});

if(mode.trim() =='is_pub' || mode.trim() =='is_pub_test')
{
	fis.config.set('pack', 
	{
		//打包输出为common.js ,数组里面的匹配为要指定打包的文件
		'js/common/common.js' : ['/modules/common/**.js','/modules/yue_ui/**.js'],
		//打包输出为common.css ,数组里面的匹配为要指定打包的文件
		'style/libs/common.css' : ['/style/frozen/**.scss','/style/base.scss','/style/widget/**.scss']
	});
}



//csssprite处理时图片之间的边距，默认是3px 注意！！ 使用图片合并只有执行合并命令才生效
fis.config.set('settings.spriter.csssprites.margin', 10);
// 设置了scale ，每一条css里面就不用设置background-size
//fis.config.set('settings.spriter.csssprites.scale', 0.5);



//设置为ture则为全部合并
//fis.config.set('settings.postpackager.simple.autoCombine', false);