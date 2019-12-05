module.exports = function(grunt) {
    var fs = require('fs');
    var _ = grunt.util._;

    var buildDirPath = '~build-cache';

    grunt.loadNpmTasks('grunt-cmd-transport');
    grunt.loadNpmTasks('grunt-cmd-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-clean');

    var cmdTransport = require('grunt-cmd-transport');
    var script = cmdTransport.script.init(grunt);
    var style = cmdTransport.style.init(grunt);
    var text = cmdTransport.text.init(grunt);
    var template = cmdTransport.template.init(grunt);
    //var json = cmdTransport.json.init(grunt);

    var CandidateFiles = [];

    grunt.initConfig({
        transport: {
            options: {
                //paths: [],

                //alias: '<%= pkg.build.alias %>',
                //idleading: '<%= pkg.organization %>/' + buildConfig.id + '/' + version + '/'

                parsers: {
                    '.js': [script.jsParser],
                    '.css': [style.css2jsParser],
                    '.html': [text.html2jsParser],
                    //'.json': [json.jsonParser],
                    '.tpl': [template.tplParser],
                    '.handlebars': [template.handlebarsParser]
                },

                handlebars: {
                    id: 'utility/handlebars/1.3.0/runtime',
                    knownHelpers: [],
                    knownHelpersOnly: false
                }
            },
            build: {
                files: [{
                    expand: true,
                    cwd: 'js',
                    src: ['**'],
                    dest: buildDirPath,
                    filter: getFile
                }]
            }
        },

        concat: {
            build: {
                options: {
                    //paths: [],
                    include: 'relative'
                },

                files: [
                    // min file
                    {
                        expand: true,
                        cwd: buildDirPath,
                        src: ['**'],
                        dest: 'dist',
                        filter: function(filePath) {
                            if (getFile(filePath) &&
                                filePath.indexOf('debug') < 0) {
                                return true;
                            }

                            return false;
                        }
                    },
                    // debug file
                    {
                        expand: true,
                        cwd: buildDirPath,
                        src: ['**'],
                        dest: 'dist',
                        filter: function(filePath) {
                            if (getFile(filePath) &&
                                filePath.indexOf('debug') > -1) {
                                return true;
                            }

                            return false;
                        }
                    }
                ]
            }
        },

        uglify: {
            options: {
                beautify: {
                    ascii_only: true
                }

            },
            build: {
                files: [{
                    expand: true,
                    src: ['*.js', '!*-debug.js']
                }]
            }
        },

        clean: {
            build: [buildDirPath]
        }
    });

    // sudo grunt build --f
    grunt.registerTask('build', '', function() {

        var targetRootPath = 'js/';

        var buildConfig = grunt.file.readJSON('package.json');

        var buildOutPut = buildConfig.build.output || '';
        if (!buildOutPut) {
            grunt.log.error('output error');
            return false;
        }

        // transport
        // -------------------
        // 设置id
        var idleading = [
            buildConfig.name, buildConfig.version, ''
        ].join('/');
        grunt.config.set('transport.options.idleading', idleading);

        // 设置alias
        grunt.config.set('transport.options.alias', buildConfig.build.alias);

        // 设置files
        //grunt.config.set('transport.build.files.0.cwd', targetRootPath);
        //grunt.config.set('transport.build.files.0.src', '*');
        grunt.task.run('transport:build');


        // concat
        // -------------------
        // 设置include
        var concatInclude = buildConfig.build.include || '';
        if (concatInclude === 'self' ||
            concatInclude === 'relative' ||
            concatInclude === 'all') {

            grunt.config.set('concat.build.options.include', concatInclude);
        }

        // 设置files
        var distPath = [
            'dist', buildConfig.version, ''
        ].join('/');
        //distPath = targetRootPath + distPath;

        if (_.isString(buildOutPut)) {
            tmpName = buildOutPut;
            buildOutPut = {};
            buildOutPut[tmpName] = ['**'];
        }

        var concatFilesConfig = [];
        _.each(buildOutPut, function(value, key) {
            var filesSrc = filesObjectFormat(value);

            // min file
            var minFile = distPath + key;
            concatFilesConfig.push({
                src: filesSrc.min,
                dest: minFile, filter: 'isFile'
            });

            // debug file
            var debugFile = minFile.replace(/\.js$/, '-debug.js');
            concatFilesConfig.push({
                src: filesSrc.debug,
                dest: debugFile, filter: 'isFile'
            });

            grunt.log.ok('Concat set min file path: ' + filesSrc.min);
            grunt.log.ok('Concat set debug file path: ' + filesSrc.debug);
        });
        grunt.config.set('concat.build.files', concatFilesConfig);

        // uglify
        // -------------------
        grunt.log.writeln('Uglify set file cwd: ' + distPath);
        grunt.log.writeln('Uglify set file dest: ' + distPath);

        grunt.config.set('uglify.build.files.0.cwd', distPath);
        grunt.config.set('uglify.build.files.0.dest', distPath);

        // 开始任务
        //grunt.task.run('transport:build');
        grunt.task.run('concat:build');
        grunt.task.run('uglify:build');
        grunt.task.run('clean:build');
    });

    // helps
    function getFile(filePath) {
		
		console.log("filePath:"+filePath)

        if (grunt.file.isFile(filePath) === false ||
            /\.(?:js|tpl|handlebars|css)$/.test(filePath) === false) {
            return false;
        }

        if (!/~/.test(filePath)) {
            grunt.log.ok('Selected ' + filePath);
            return true;
        } else {
            grunt.log.error('Skip ' + filePath);
            return false;
        }
    }

    function extend() {
        var target = arguments[0] || {},
            i = 1, length = arguments.length,
            options, name, src, copy, copyIsArray, clone;

        // Handle case when target is a string or something (possible in deep copy)
        if (typeof target !== 'object' && !_.isFunction(target)) {
            target = {};
        }

        for (;i < length; i++) {
            if ((options = arguments[i]) != null) {
                // Extend the base object
                for (name in options) {
                    src = target[name];
                    copy = options[name];

                    // Prevent never-ending loop
                    if (target === copy) {
                        continue;
                    }

                    // Recurse if we're merging plain objects or arrays
                    if (copy && (isPlainObject(copy) || (copyIsArray = _.isArray(copy)))) {
                        if (copyIsArray) {
                            copyIsArray = false;
                            clone = src && _.isArray(src) ? src : [];
                        } else {
                            clone = src && isPlainObject(src) ? src : {};
                        }

                        // Never move original objects, clone them
                        target[name] = extend(clone, copy);

                        // Don't bring in undefined values
                    } else if (copy !== undefined) {
                        target[name] = copy;
                    }
                }
            }
        }

        // Return the modified object
        return target;
    }

    function getFileExt(file) {
        var fileArr = file.split('.');
        return (fileArr.length > 1) ? fileArr.pop() : '';
    }

    function filesObjectFormat(files) {
        if (!_.isArray(files) && _.isString(files)) {
            files = [files];
        }

        var minFilesSrc = [];
        var debugFilesSrc = [];
        _.each(files, function(value, key) {
            var fileExt = getFileExt(value);

            var tmpSrcPath = buildDirPath + '/' + value;

            switch (fileExt) {
                case 'js':
                    minFilesSrc.push(tmpSrcPath);
                    minFilesSrc.push('!' + tmpSrcPath.replace(/\.js$/, '-debug.js'));

                    debugFilesSrc.push(tmpSrcPath.replace(/\.js$/, '-debug.js'));
                    break;
                case 'tpl':
                    tmpSrcPath += '.js';
                    minFilesSrc.push(tmpSrcPath);
                    minFilesSrc.push('!' + tmpSrcPath.replace(/\.tpl\.js$/, '-debug.tpl.js'));

                    debugFilesSrc.push(tmpSrcPath.replace(/\.tpl\.js$/, '-debug.tpl.js'));
                    break;
                case 'handlebars':
                    tmpSrcPath += '.js';
                    minFilesSrc.push(tmpSrcPath);
                    minFilesSrc.push('!' + tmpSrcPath.replace(/\.tpl\.js$/, '-debug.handlebars.js'));

                    debugFilesSrc.push(tmpSrcPath.replace(/\.tpl\.js$/, '-debug.handlebars.js'));
                    break;
            }
        });

        return {
            min: minFilesSrc,
            debug: debugFilesSrc
        };
    }

    function isPlainObject(obj) {
        // Must be an Object.
        // Because of IE, we also have to check the presence of the constructor property.
        // Make sure that DOM nodes and window objects don't pass through, as well
        if (!obj || !_.isObject(obj) || obj.nodeType || isWindow(obj)) {
            return false;
        }

        try {
            // Not own constructor property must be Object
            if (obj.constructor &&
                !has(obj, 'constructor') &&
                !has(obj.constructor.prototype, 'isPrototypeOf')) {
                return false;
            }
        } catch (e) {
            // IE8,9 Will throw exceptions on certain host objects #9897
            return false;
        }

        // Own properties are enumerated firstly, so to speed up,
        // if last one is own, then all properties are own.

        var key;
        for (key in obj) {}

        return key === undefined || has(obj, key);
    }

    function isWindow(obj) {
        return obj != null && obj == obj.window;
    }
    function has(obj, key) {
        return Object.prototype.hasOwnProperty.call(obj, key);
    }
};