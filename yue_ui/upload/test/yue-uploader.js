/**
 * Created by hudingwen on 15/6/9.
 */
(function($)
{
    var yueWebUploader = function(element,options)
    {

        options = options || {};
        // ��������
        options.upload_tips_txt = options.upload_tips_txt || '����Ƭ�ϵ������������ѡ20��';

        var html_tpl = '<div id="uploader">'+
                            '<div class="queueList">'+
                                '<div id="dndArea" class="placeholder">'+
                                    '<div id="filePicker"></div>'+
                                    '<p>'+options.upload_tips_txt+'</p>'+
                                '</div>'+
                            '</div>'+
                            '<div class="statusBar" style="display:none;">'+
                                '<div class="progress">'+
                                    '<span class="text">0%</span>'+
                                    '<span class="percentage"></span>'+
                                '</div><div class="info"></div>'+
                                '<div class="btns">'+
                                    '<div id="filePicker2"></div><div class="uploadBtn">��ʼ�ϴ�</div>'+
                                '</div>'+
                            '</div>'+
                        '</div>';

        $(element).html(html_tpl);

        var $wrap = $('#uploader');



        // ͼƬ����
        var $queue = $( '<ul class="filelist"></ul>' )
            .appendTo( $wrap.find( '.queueList' ) );
        // ״̬�����������ȺͿ��ư�ť
        var $statusBar = $wrap.find( '.statusBar' );
        // �ļ�����ѡ����Ϣ��
        var $info = $statusBar.find( '.info' );
        // �ϴ���ť
        var $upload = $wrap.find( '.uploadBtn' );
        // ûѡ���ļ�֮ǰ�����ݡ�
        var $placeHolder = $wrap.find( '.placeholder' );
        var $progress = $statusBar.find( '.progress' ).hide();
        // ���ӵ��ļ�����
        var fileCount = 0;
        // ���ӵ��ļ��ܴ�С
        var fileSize = 0;

        // �Ż�retina, ��retina�����ֵ��2
        var ratio = window.devicePixelRatio || 1;

        // ����ͼ��С
        var thumbnailWidth = 110 * ratio;
        var thumbnailHeight = 110 * ratio;

        // ������pedding, ready, uploading, confirm, done.
        var state = 'pedding';

        // �����ļ��Ľ�����Ϣ��keyΪfile id
        var percentages = {};
        // �ж�������Ƿ�֧��ͼƬ��base64
        var isSupportBase64 = ( function() {
            var data = new Image();
            var support = true;
            data.onload = data.onerror = function() {
                if( this.width != 1 || this.height != 1 ) {
                    support = false;
                }
            }
            data.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
            return support;
        } )();

        // ����Ƿ��Ѿ���װflash�����flash�İ汾
        var flashVersion = ( function() {
            var version;

            try {
                version = navigator.plugins[ 'Shockwave Flash' ];
                version = version.description;
            } catch ( ex ) {
                try {
                    version = new ActiveXObject('ShockwaveFlash.ShockwaveFlash')
                        .GetVariable('$version');
                } catch ( ex2 ) {
                    version = '0.0';
                }
            }
            version = version.match( /\d+/g );
            return parseFloat( version[ 0 ] + '.' + version[ 1 ], 10 );
        } )();

        supportTransition = (function(){
            var s = document.createElement('p').style,
                r = 'transition' in s ||
                    'WebkitTransition' in s ||
                    'MozTransition' in s ||
                    'msTransition' in s ||
                    'OTransition' in s;
            s = null;
            return r;
        })();

        // WebUploaderʵ��
        var uploader;

        if ( !WebUploader.Uploader.support('flash') && WebUploader.browser.ie )
        {

            // flash ��װ�˵��ǰ汾���͡�
            if (flashVersion) {
                (function(container) {
                    window['expressinstallcallback'] = function( state ) {
                        switch(state) {
                            case 'Download.Cancelled':
                                alert('��ȡ���˸��£�')
                                break;

                            case 'Download.Failed':
                                alert('��װʧ��')
                                break;

                            default:
                                alert('��װ�ѳɹ�����ˢ�£�');
                                break;
                        }
                        delete window['expressinstallcallback'];
                    };

                    var swf = './expressInstall.swf';
                    // insert flash object
                    var html = '<object type="application/' +
                        'x-shockwave-flash" data="' +  swf + '" ';

                    if (WebUploader.browser.ie) {
                        html += 'classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" ';
                    }

                    html += 'width="100%" height="100%" style="outline:0">'  +
                        '<param name="movie" value="' + swf + '" />' +
                        '<param name="wmode" value="transparent" />' +
                        '<param name="allowscriptaccess" value="always" />' +
                        '</object>';

                    container.html(html);

                })($wrap);

                // ѹ����û�а�ת��
            } else {
                $wrap.html('<a href="http://www.adobe.com/go/getflashplayer" target="_blank" border="0"><img alt="get flash player" src="http://www.adobe.com/macromedia/style_guide/images/160x41_Get_Flash_Player.jpg" /></a>');
            }

            return;
        } else if (!WebUploader.Uploader.support()) {
            alert( 'Web Uploader ��֧�������������');
            return;
        }

        var params = options.params || {};

        var default_params = {
            pick: {
                id: '#filePicker',
                label: '���ѡ��ͼƬ'
            },
            dnd: '#dndArea',
            paste: '#uploader',
            swf: 'Uploader.swf',
            chunked: false,
            chunkSize: 512 * 1024,
            server: 'http://sendmedia.yueus.com:8079/upload.cgi',

            runtimeOrder: 'flash',

            // accept: {
            //     title: 'Images',
            //     extensions: 'gif,jpg,jpeg,bmp,png',
            //     mimeTypes: 'image/*'
            // },

            // ����ȫ�ֵ���ק���ܡ������������ͼƬ�Ͻ�ҳ���ʱ�򣬰�ͼƬ�򿪡�
            fileVal : 'opus',
            disableGlobalDnd: true,
            fileNumLimit: 20,
            fileSizeLimit: 200 * 1024 * 1024,    // 200 M
            fileSingleSizeLimit: 50 * 1024 * 1024    // 50 M
        };

        params = $.extend(true,default_params,options);

        // ʵ����
        uploader = WebUploader.create(params);

        // ��קʱ������ js, txt �ļ���
        uploader.on( 'dndAccept', function( items ) {
            var denied = false,
                len = items.length,
                i = 0,
            // �޸�js����
                unAllowed = 'text/plain;application/javascript ';

            for ( ; i < len; i++ ) {
                // ������б�����
                if ( ~unAllowed.indexOf( items[ i ].type ) ) {
                    denied = true;
                    break;
                }
            }

            return !denied;
        });

        uploader.on('dialogOpen', function() {
            console.log('here');
        });


        // ���ӡ������ļ����İ�ť��
        uploader.addButton({
            id: '#filePicker2',
            label: '��������'
        });

        uploader.on('ready', function() {
            window.uploader = uploader;
        });

        // �����ļ����ӽ���ʱִ�У�����view�Ĵ���
        function addFile( file ) {
            var $li = $( '<li id="' + file.id + '">' +
                    '<p class="title">' + file.name + '</p>' +
                    '<p class="imgWrap"></p>'+
                    '<p class="progress"><span></span></p>' +
                    '</li>' ),

                $btns = $('<div class="file-panel">' +
                    '<span class="cancel">ɾ��</span>' +
                    '<span class="rotateRight">������ת</span>' +
                    '<span class="rotateLeft">������ת</span></div>').appendTo( $li ),
                $prgress = $li.find('p.progress span'),
                $wrap = $li.find( 'p.imgWrap' ),
                $info = $('<p class="error"></p>'),

                showError = function( code ) {
                    switch( code ) {
                        case 'exceed_size':
                            text = '�ļ���С����';
                            break;

                        case 'interrupt':
                            text = '�ϴ���ͣ';
                            break;

                        default:
                            text = '�ϴ�ʧ�ܣ�������';
                            break;
                    }

                    $info.text( text ).appendTo( $li );
                };

            if ( file.getStatus() === 'invalid' ) {
                showError( file.statusText );
            } else {
                // @todo lazyload
                $wrap.text( 'Ԥ����' );
                uploader.makeThumb( file, function( error, src ) {
                    var img;

                    if ( error ) {
                        $wrap.text( '����Ԥ��' );
                        return;
                    }

                    if( isSupportBase64 ) {
                        img = $('<img src="'+src+'">');
                        $wrap.empty().append( img );
                    } else {
                        $.ajax('../../server/preview.php', {
                            method: 'POST',
                            data: src,
                            dataType:'json'
                        }).done(function( response ) {
                            if (response.result) {
                                img = $('<img src="'+response.result+'">');
                                $wrap.empty().append( img );
                            } else {
                                $wrap.text("Ԥ������");
                            }
                        });
                    }
                }, thumbnailWidth, thumbnailHeight );

                percentages[ file.id ] = [ file.size, 0 ];
                file.rotation = 0;
            }

            file.on('statuschange', function( cur, prev ) {
                if ( prev === 'progress' ) {
                    $prgress.hide().width(0);
                } else if ( prev === 'queued' ) {
                    $li.off( 'mouseenter mouseleave' );
                    $btns.remove();
                }

                // �ɹ�
                if ( cur === 'error' || cur === 'invalid' ) {
                    console.log( file.statusText );
                    showError( file.statusText );
                    percentages[ file.id ][ 1 ] = 1;
                } else if ( cur === 'interrupt' ) {
                    showError( 'interrupt' );
                } else if ( cur === 'queued' ) {
                    percentages[ file.id ][ 1 ] = 0;
                } else if ( cur === 'progress' ) {
                    $info.remove();
                    $prgress.css('display', 'block');
                } else if ( cur === 'complete' ) {
                    $li.append( '<span class="success"></span>' );
                }

                $li.removeClass( 'state-' + prev ).addClass( 'state-' + cur );
            });

            $li.on( 'mouseenter', function() {
                $btns.stop().animate({height: 30});
            });

            $li.on( 'mouseleave', function() {
                $btns.stop().animate({height: 0});
            });

            $btns.on( 'click', 'span', function() {
                var index = $(this).index(),
                    deg;

                switch ( index ) {
                    case 0:
                        uploader.removeFile( file );
                        return;

                    case 1:
                        file.rotation += 90;
                        break;

                    case 2:
                        file.rotation -= 90;
                        break;
                }

                if ( supportTransition ) {
                    deg = 'rotate(' + file.rotation + 'deg)';
                    $wrap.css({
                        '-webkit-transform': deg,
                        '-mos-transform': deg,
                        '-o-transform': deg,
                        'transform': deg
                    });
                } else {
                    $wrap.css( 'filter', 'progid:DXImageTransform.Microsoft.BasicImage(rotation='+ (~~((file.rotation/90)%4 + 4)%4) +')');
                    // use jquery animate to rotation
                    // $({
                    //     rotation: rotation
                    // }).animate({
                    //     rotation: file.rotation
                    // }, {
                    //     easing: 'linear',
                    //     step: function( now ) {
                    //         now = now * Math.PI / 180;

                    //         var cos = Math.cos( now ),
                    //             sin = Math.sin( now );

                    //         $wrap.css( 'filter', "progid:DXImageTransform.Microsoft.Matrix(M11=" + cos + ",M12=" + (-sin) + ",M21=" + sin + ",M22=" + cos + ",SizingMethod='auto expand')");
                    //     }
                    // });
                }


            });

            $li.appendTo( $queue );
        }

        // ����view������
        function removeFile( file ) {
            var $li = $('#'+file.id);

            delete percentages[ file.id ];
            updateTotalProgress();
            $li.off().find('.file-panel').off().end().remove();
        }

        function updateTotalProgress() {
            var loaded = 0,
                total = 0,
                spans = $progress.children(),
                percent;

            $.each( percentages, function( k, v ) {
                total += v[ 0 ];
                loaded += v[ 0 ] * v[ 1 ];
            } );

            percent = total ? loaded / total : 0;


            spans.eq( 0 ).text( Math.round( percent * 100 ) + '%' );
            spans.eq( 1 ).css( 'width', Math.round( percent * 100 ) + '%' );
            updateStatus();
        }

        function updateStatus() {
            var text = '', stats;

            if ( state === 'ready' ) {
                text = 'ѡ��' + fileCount + '��ͼƬ����' +
                    WebUploader.formatSize( fileSize ) + '��';
            } else if ( state === 'confirm' ) {
                stats = uploader.getStats();
                if ( stats.uploadFailNum ) {
                    text = '�ѳɹ��ϴ�' + stats.successNum+ '����Ƭ��XX��ᣬ'+
                        stats.uploadFailNum + '����Ƭ�ϴ�ʧ�ܣ�<a class="retry" href="#">�����ϴ�</a>ʧ��ͼƬ��<a class="ignore" href="#">����</a>'
                }

            } else {
                stats = uploader.getStats();
                text = '��' + fileCount + '�ţ�' +
                    WebUploader.formatSize( fileSize )  +
                    '�������ϴ�' + stats.successNum + '��';

                if ( stats.uploadFailNum ) {
                    text += '��ʧ��' + stats.uploadFailNum + '��';
                }
            }

            $info.html( text );
        }

        function setState( val ) {
            var file, stats;

            if ( val === state ) {
                return;
            }

            $upload.removeClass( 'state-' + state );
            $upload.addClass( 'state-' + val );
            state = val;

            switch ( state ) {
                case 'pedding':
                    $placeHolder.removeClass( 'element-invisible' );
                    $queue.hide();
                    $statusBar.addClass( 'element-invisible' );
                    uploader.refresh();
                    break;

                case 'ready':
                    $placeHolder.addClass( 'element-invisible' );
                    $( '#filePicker2' ).removeClass( 'element-invisible');
                    $queue.show();
                    $statusBar.removeClass('element-invisible');
                    uploader.refresh();
                    break;

                case 'uploading':
                    $( '#filePicker2' ).addClass( 'element-invisible' );
                    $progress.show();
                    $upload.text( '��ͣ�ϴ�' );
                    break;

                case 'paused':
                    $progress.show();
                    $upload.text( '�����ϴ�' );
                    break;

                case 'confirm':
                    $progress.hide();
                    $( '#filePicker2' ).removeClass( 'element-invisible' );
                    $upload.text( '��ʼ�ϴ�' );

                    stats = uploader.getStats();
                    if ( stats.successNum && !stats.uploadFailNum ) {
                        setState( 'finish' );
                        return;
                    }
                    break;
                case 'finish':
                    stats = uploader.getStats();
                    if ( stats.successNum ) {
                        alert( '�ϴ��ɹ�' );
                    } else {
                        // û�гɹ���ͼƬ������
                        state = 'done';
                        location.reload();
                    }
                    break;
            }

            updateStatus();
        }

        uploader.onUploadProgress = function( file, percentage ) {
            var $li = $('#'+file.id),
                $percent = $li.find('.progress span');

            $percent.css( 'width', percentage * 100 + '%' );
            percentages[ file.id ][ 1 ] = percentage;
            updateTotalProgress();

            uploader.trigger('file_upload_progress',file,percentage);
        };

        uploader.onFileQueued = function( file ) {
            fileCount++;
            fileSize += file.size;

            if ( fileCount === 1 ) {
                $placeHolder.addClass( 'element-invisible' );
                $statusBar.show();
            }

            addFile( file );
            setState( 'ready' );
            updateTotalProgress();

            uploader.trigger('file_queued',file);
        };

        uploader.onFileDequeued = function( file ) {
            fileCount--;
            fileSize -= file.size;

            if ( !fileCount ) {
                setState( 'pedding' );
            }

            removeFile( file );
            updateTotalProgress();

            uploader.trigger('file_dequeued',file);

        };

        uploader.on( 'all', function( type ) {
            var stats;
            switch( type ) {
                case 'uploadFinished':
                    setState( 'confirm' );
                    break;

                case 'startUpload':
                    setState( 'uploading' );
                    break;

                case 'stopUpload':
                    setState( 'paused' );
                    break;

            }
        });

        uploader.onError = function( code ) {
            alert( 'Eroor: ' + code );

            uploader.trigger('error',code);
        };

        $upload.on('click', function() {
            if ( $(this).hasClass( 'disabled' ) ) {
                return false;
            }

            if ( state === 'ready' ) {
                uploader.upload();
            } else if ( state === 'paused' ) {
                uploader.upload();
            } else if ( state === 'uploading' ) {
                uploader.stop();
            }
        });

        $info.on( 'click', '.retry', function() {
            uploader.retry();
        } );

        $info.on( 'click', '.ignore', function() {
            alert( 'todo' );
        } );

        $upload.addClass( 'state-' + state );
        updateTotalProgress();



        return uploader;

    };

    window.yueWebUploader = yueWebUploader ;



})($ || window.Zepto);