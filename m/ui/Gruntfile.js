module.exports = function(grunt) {
    var _ = grunt.util._;

    var rootPath = __dirname + '/';

    grunt.loadNpmTasks('grunt-contrib-compass');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-cssmin');

    var buildCacheDir = '~build-cache';

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        compass: {
            options: {
                //config: 'config.rb',
                outputStyle: 'compressed',
                noLineComments: true,
                //cleanup: false,
                //force: true,
                sassDir: 'src'
            },
            build: {
                options: {
                    httpPath: 'http://www.poco.cn/css_common/pocoui/assets',
                    httpImagesPath: 'http://www.poco.cn/css_common/pocoui/assets/images/',
                    httpGeneratedImagesPath: 'http://www.poco.cn/css_common/pocoui/assets/images/',

                    imagesDir: '../images/',
                    httpFontsDir: 'fonts',
                    fontsDir: '../fonts', // css中字体路径

                    generatedImagesDir: '../images/',

                    cssDir: buildCacheDir
                }
            },
            dev: {
                options: {
                    noLineComments: false,
                    outputStyle: 'expanded',

                    httpPath: '../',

                    imagesDir: '../images', // css中图片路径
                    generatedImagesDir: '../images', // 生成图片的存档位置
                    fontsDir: '../fonts', // css中字体路径
                    cssDir: '../css'
                }
            }
        },

        copy: {
            build: {
                filter: 'isFile'
            }
        },

        watch: {
            specify: {
                options: {
                    nospawn: true
                },
                files: ['src/**/*.scss'],
                filter: 'isFile'
            }
        },
		//css压缩代码
		cssmin: {
		  minify: {
			expand: true,
			cwd: '../css/pai/',//要压缩的css目录
			src: ['*.css'],	
			dest: '../css/pai/',//生成css的目录
			ext:  '.min.css'
		  }
		},

        clean: {
            build: [buildCacheDir]
        }
    });


    // 监听处理
    grunt.event.on('watch', function(action, filePath, target) {
        if (/scss$/.test(filePath) == false) {
            return false;
        }

        grunt.log.writeln("\n========== watch trigger ==========\n");

        grunt.log.ok('"' + filePath + '" 内容发生变化，开始寻找主编译文件.');

        var targetPath = getCompileFile(filePath);
        if (targetPath) {

            grunt.log.ok('主编译文件: "' + targetPath + '" 准备编译.');
            grunt.config.set('compass.dev.options.specify', [targetPath]);
            grunt.task.run('compass:dev');
        } else {
            grunt.log.error('"' + filePath + '" 找不到主编译文件.');
        }

        return false;
    });

    // 对单独文件打包
    // sudo grunt build --target=module
    grunt.registerTask('build', '', function() {
        var target = grunt.option('target') || '';

        if (!target) {
            grunt.log.error('target error');
            return false;
        }

        var srcRootPath = 'src/' + target;

        var package = grunt.file.readJSON(srcRootPath + '/package.json');

        if (package.build && package.build.output) {
            var targetSCSSPaths = [];
            _.each(package.build.output, function(value, key) {
                var targetSCSSPath = srcRootPath + '/' + value;

                grunt.log.ok('targetScssPath：' + targetSCSSPath);

                targetSCSSPaths.push(targetSCSSPath);
            });

            grunt.log.ok('buildCacheDir：' + buildCacheDir);

            // 把scss编译到缓存文件夹中
            grunt.config.set('compass.build.options.specify', targetSCSSPaths);
            grunt.config.set('compass.build.options.cssDir', buildCacheDir);

            // 缓存文件夹中转移到目标目录
            var distPath =  'dist/' + target + '/' + package.version + '/';
            grunt.log.ok('copy src：' + buildCacheDir + '/' + targetCssName);
            grunt.log.ok('distPath：' + distPath);
            grunt.config.set('copy.build', {
                files: [{
                    expand: true,
                    cwd: buildCacheDir + '/' + target + '/',
                    src: ['**'],
                    dest: distPath
                }]
            });
        } else {
            var targetScssPath = srcRootPath + '.scss';
            var targetCssName = package.name + '.css';
            var distPath =  'dist/' + target + '/' + package.version + '/';

            grunt.log.ok('targetScssPath：' + targetScssPath);
            grunt.log.ok('buildCacheDir：' + buildCacheDir);

            // 把scss编译到缓存文件夹中
            grunt.config.set('compass.build.options.specify', [targetScssPath]);
            grunt.config.set('compass.build.options.cssDir', [buildCacheDir]);


            // 缓存文件夹中转移到目标目录
            grunt.log.ok('copy src：' + buildCacheDir + '/' + targetCssName);
            grunt.log.ok('distPath：' + distPath);
            grunt.config.set('copy.build', {
                src: buildCacheDir + '/' + target + '.css',
                dest: distPath + '/' + targetCssName
            });
        }

        // run
        grunt.task.run('compass:build');
        grunt.task.run('copy:build');
        //grunt.task.run('clean:build');
    });

    //grunt.registerTask('default', ['watch:specify']);
    grunt.registerTask('default', '', function() {
        var target = grunt.option('target') || '';

        if (target) {
            grunt.config.set('watch.specify.files', ['src/' + target + '/**/*.scss']);
        }

        grunt.task.run('watch:specify');
    });
    grunt.registerTask('dev', ['compass:dev']);

	grunt.registerTask('css_min', function()
	{
		grunt.task.run('cssmin:minify');
	});

    // Help
    // ----------------------------------
    function isWinPath(filePath) {
        return /\\/.test(filePath);
    }

    function path2Arr(filePath) {
        var delimiter = isWinPath(filePath) ? '\\' : '/';
        return filePath.split(delimiter);
    }

    /**
     * 寻找主编译文件
     * src/m/home/_index.scss
     * src/m/home/_feed.scss
     * src/m/home/_sidebar.scss
     * src/m/home/sidebar/_person.mod.scss
     *  =>
     * src/m/home/home.scss
     *
     * @param filePath
     * @returns {*}
     */
    function getCompileFile(filePath) {
        var pathArr = path2Arr(filePath);

        var hasUnderline = /_.+\.scss$/.test(filePath);

        if (hasUnderline) {
            var pathNode, parentPath;
            while (pathNode = pathArr.pop()) {
                if (pathArr.length < 2) {
                    return false;
                }

                parentPath = pathArr.join('/') + '.scss';
                if (grunt.file.exists(parentPath)) {
                    return parentPath;
                }
            }

            return false;
        }

        return filePath;
    }
};