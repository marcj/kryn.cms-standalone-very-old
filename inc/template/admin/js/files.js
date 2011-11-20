var admin_files = new Class({
    
    historyIndex: 0,
    history: {},
    
    current: '',
    
    _modules: [],
    
    
    __images: ['jpg','jpeg','gif','png','bmp'],
    __ext: ['css', 'tpl', 'js', 'html'],
    _krynFolders: ['kryn/', 'css/', 'images/', 'js/', 'admin/'],
    firstLoaded: 0,

    uploadTrs: {},
    uploadFilesCount: 0,
    uploadFileNames: {},
    fileUploadSpeedLastCheck: 0,
    fileUploadedSpeedLastLoadedBytes: 0,
    fileUploadedLoadedBytes: 0,
    fileUploadSpeedLastByteSpeed: 0,
    fileUploadSpeedInterval: false,
    
    html5UploadXhr: {},
    
    initialize: function( pWindow ){
        this.win = pWindow;

        this.win.fileObj = this;

        this.options = {};
        this.options.onlyUserDefined = (Cookie.read('adminFiles_OnlyUserFiles')==0)?false:true;

        this.win.content.setStyle('overflow', 'hidden');

        this._createLayout();
        this.loadModules();
        
        this.win.border.addEvent('click', function(){
            if( this.context )
                this.context.destroy(); 
        }.bind(this));

        this.title = this.win.getTitle();
        this.initHotkeys();
        
        this.win.addEvent('close', function(){
            this.cancelUploads();
        }.bind(this));
    },

    initHotkeys: function(){

        this.win.addHotkey('x', true, false, this.cut.bind(this));
        this.win.addHotkey('c', true, false, this.copy.bind(this));
        this.win.addHotkey('v', true, false, this.paste.bind(this));
        this.win.addHotkey('delete', false, false, this.remove.bind(this));
        this.win.addHotkey('space', false, false, this.preview.bind(this));

    },

    setTitle: function(){
    
        var folder = this.current;
        if( folder.substr(0,1) != '/' )
            folder = '/'+folder;
    
        this.win.setTitle( folder );
    },
    
    recoverSWFUpload: function(){
        this.buttonId = this.win.id+'_'+Math.ceil(Math.random()*100);
        this.uploadBtn.set('html', '<span id="'+this.buttonId+'"></span>');
        this.initSWFUpload();
    },
        
    bytesToSize: function( bytes ){
        var sizes = ['Bytes', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];
        if (bytes == 0) return 'n/a';
        var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        if (i == 0) { return (bytes / Math.pow(1024, i)) + ' ' + sizes[i]; }
        return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
    },
    
    minimizeUpload: function(){
    
    
    },
    
    newFileUpload: function( pFile ){
    
        if( !this.fileUploadDialog ){
            this.fileUploadDialog = this.win.newDialog( '', true );
            
            this.fileUploadDialog.setStyles({
                height: '60%',
                width: '80%'
            });
            this.fileUploadDialog.center();
            
            this.fileUploadCancelBtn = new ka.Button(_('Cancel'))
            .addEvent('click', this.cancelUploads.bind(this))
            .inject(this.fileUploadDialog.bottom);
            
            
            this.fileUploadMinimizeBtn = new ka.Button(_('Minimize'))
            .addEvent('click', this.minimizeUpload.bind(this))
            .inject( this.fileUploadDialog.bottom );
            
            var table = new Element('table', {style: 'width: 100%;', 'class': 'admin-file-uploadtable'}).inject( this.fileUploadDialog.content );
            this.fileUploadTBody = new Element('tbody').inject( table );
            
            
            this.fileUploadDialogProgress = new ka.Progress();
            document.id(this.fileUploadDialogProgress).inject( this.fileUploadDialog.bottom );
            document.id(this.fileUploadDialogProgress).setStyle( 'width', 132 );
            document.id(this.fileUploadDialogProgress).setStyle( 'position', 'absolute' );
            document.id(this.fileUploadDialogProgress).setStyle( 'top', 4 );
            document.id(this.fileUploadDialogProgress).setStyle( 'left', 9 );
        
            this.fileUploadDialogAll = new Element('div', {
                style: 'position: absolute; left: 155px; top: 6px; color: gray;'
            }).inject( this.fileUploadDialog.bottom );

            this.fileUploadDialogAllText = new Element('span').inject( this.fileUploadDialogAll );
            this.fileUploadDialogAllSpeed = new Element('span').inject( this.fileUploadDialogAll );

        }
        
        this.fileUploadMinimizeBtn.show();
        this.fileUploadCancelBtn.setText( _('Cancel') );
        

        this.uploadFilesCount++;

        var tr = new Element('tr').inject( this.fileUploadTBody );

        var td = new Element('td', {
            width: 20,
            text: '#'+this.uploadFilesCount
        }).inject( tr );

        tr.name = new Element('td', {
            text: pFile.name
        }).inject( tr );

        var td = new Element('td', {
            width: 60,
            style: 'text-align: center; color: gray;',
            text: this.bytesToSize(pFile.size)
        }).inject( tr );

        tr.status = new Element('td', {
            text: _('Pending ...'),
            width: 150,
            style: 'text-align: center;'
        }).inject( tr );

        var td = new Element('td', {
            width: 150
        }).inject( tr );

        tr.progress = new ka.Progress();
        document.id(tr.progress).inject( td );
        document.id(tr.progress).setStyle( 'width', 132);
        
        tr.deleteTd = new Element('td', {
            width: 20
        }).inject( tr );
        
        new Element('img', {
            src: _path+'inc/template/admin/images/icons/delete.png',
            style: 'cursor: pointer;',
            title: _('Cancel upload')
        })
        .addEvent('click', function(){
            tr.canceled = true;
            ka.uploads[this.win.id].cancelUpload( pFile.id );
        }.bind(this))
        .inject( tr.deleteTd );
        
        this.uploadTrs[ pFile.id ] = tr;
        this.uploadTrs[ pFile.id ].file = pFile;
        
        if( pFile.html5 ){

            if( !pFile.post ) pFile.post = {};

            if( !pFile.post.path ){
                pFile.post.path = this.current;
            }

        } else {
            ka.uploads[this.win.id].addFileParam( pFile.id, 'path', this.current );
        }
        
        if( ka.settings.upload_max_filesize && ka.settings.upload_max_filesize < pFile.size ){
        
            this.uploadError( pFile );
            
            
        } else {
        
            if( pFile.html5 )
                this.fileUploadCheck( this.html5FileUploads[ pFile.id ] );
            else
                this.fileUploadCheck( ka.uploads[this.win.id].getFile( pFile.id ) );
        }
        
        this.uploadAllProgress();
    
    },
    
    fileUploadCheck: function( pFile ){

        var name = pFile.name;
            
        this.uploadTrs[ pFile.id ].status.set('html', ('Pending ...'));

        if( this.uploadTrs[ pFile.id ].rename ){
            this.uploadTrs[ pFile.id ].rename.destroy();
            delete this.uploadTrs[ pFile.id ].rename;
        }
        
        if( pFile.post && pFile.post.name && name != pFile.post.name ){
            
            name = pFile.post.name;
            
            this.uploadTrs[ pFile.id ].rename = new Element('div', {
                style: 'color: gray; padding-top: 4px;',
                text: '-> '+name
            }).inject( this.uploadTrs[ pFile.id ].name );
        }

        var overwrite = (pFile.post.overwrite == 1)?1:0;

        new Request.JSON({url: _path+'admin/files/prepareUpload', noCache: 1, onComplete: function( res ){

            if( res.renamed ){
                if( this.uploadTrs[ pFile.id ].rename ){
                    this.uploadTrs[ pFile.id ].rename.destroy();
                }
                this.uploadTrs[ pFile.id ].rename = new Element('div', {
                    style: 'color: gray; padding-top: 4px;',
                    text: '-> '+res.name
                }).inject( this.uploadTrs[ pFile.id ].name );
                
                if( pFile.html5 ){
                    this.html5FileUploads[ pFile.id ].post.name = res.name;
                } else {
                    ka.uploads[this.win.id].addFileParam( pFile.id, 'name', res );
                }
            }

            if( res.exist ){
                this.uploadTrs[ pFile.id ].status.set('html', '<div style="color: red">'+_('Filename already exists')+'</div>');
                
                this.uploadTrs[ pFile.id ].needAction = true;
                
                new ka.Button(_('Rename'))
                .addEvent('click', function(){

                    this.win._prompt(_('New filename'), name, function(res){

                        if( res ){
                            
                            this.uploadTrs[ pFile.id ].needAction = false;
                            
                            if( pFile.html5 ){
                                this.html5FileUploads[ pFile.id ].post.name = res;
                                this.fileUploadCheck( this.html5FileUploads[ pFile.id ] );
                            } else {
                                ka.uploads[this.win.id].addFileParam( pFile.id, 'name', res );
                                this.fileUploadCheck( ka.uploads[this.win.id].getFile( pFile.id ) );
                            }
                        }

                    }.bind(this));
                
                }.bind(this))
                .inject( this.uploadTrs[ pFile.id ].status );
                
                
                new ka.Button(_('Overwrite'))
                .addEvent('click', function(){
                    
                    this.uploadTrs[ pFile.id ].needAction = false;
                    
                    if( pFile.html5 ){
                        this.html5FileUploads[ pFile.id ].post.overwrite = 1;
                        this.fileUploadCheck( this.html5FileUploads[ pFile.id ] );
                    } else {
                        ka.uploads[this.win.id].addFileParam( pFile.id, 'overwrite', '1' );
                        this.fileUploadCheck( ka.uploads[this.win.id].getFile( pFile.id ) );
                    }

                }.bind(this))

                .inject( this.uploadTrs[ pFile.id ].status );
                
                this.uploadCheckOverwriteAll();

            } else {
                
                if( pFile.html5 ){
                    this.startHtml5Upload( pFile.id );
                } else {
                    ka.uploads[this.win.id].startUpload( pFile.id );
                }
            
            }
        
        }.bind(this)}).get({path: pFile.post.path, name: name, overwrite: overwrite });
    
    },
    
    uploadCheckOverwriteAll: function(){
    
        var needButton = false;
        var countWhichNeedsAction = 0;
        Object.each(this.uploadTrs, function(tr, id){
            if( tr.file && tr.needAction == true ){
                countWhichNeedsAction++;
            }
        }.bind(this));

        if( countWhichNeedsAction > 1 ){
            if( !this.uploadOverwriteAllButton ){
                this.uploadOverwriteAllButton = new ka.Button(_('Overwrite all')).
                addEvent('click', function(){
                
                    Object.each(this.uploadTrs, function(tr, id){
                        
                        tr.needAction = false;
                        
                        if( tr.file.html5 ){
                            this.html5FileUploads[ tr.file.id ].post.overwrite = 1;
                            this.fileUploadCheck( this.html5FileUploads[  tr.file.id ] );
                        } else {
                            ka.uploads[this.win.id].addFileParam( tr.file.id, 'overwrite', '1' );
                            this.fileUploadCheck( ka.uploads[this.win.id].getFile( tr.file.id ) );
                        }
                        
                    }.bind(this));
                    
                    document.id(this.uploadOverwriteAllButton).destroy();
                    delete this.uploadOverwriteAllButton;
                    
                }.bind(this))
                .inject( this.fileUploadDialog.bottom, 'top' );
            }
        }
    
    },
    
    uploadNext: function(){
    
        var found = false;
        Object.each(this.uploadTrs, function(file, id){
            if( !found && file && !file.needAction && !file.complete && !file.error ){
                found = file;
            }
        }.bind(this));

        if( found ){

            if( found.file.html5 ){
                this.startHtml5Upload( found.file.id );
            } else {
                ka.uploads[this.win.id].startUpload( found.file.id );
            }

        }
    
    },
    
    uploadAllProgress: function(){
    
        var count = 0;
        var loaded = 0;
        var all = 0;
        var done = 0;
        var failed = 0;

        Object.each( this.uploadTrs, function(tr,id){
            
            if( !tr.canceled )
                count++;
            
            if( tr.loaded && !tr.canceled )
                loaded += tr.loaded;
            
            if( !tr.error && !tr.canceled )
                all += tr.file.size;
            
            if( tr.complete == true )
                done++;
                
            if( tr.error == true ){
                failed++;
            }
            
        });
            
        this.fileUploadDialogAllText.set('text', _('%s done').replace('%s', done+'/'+count)+'.');
        
        this.fileUploadedTotalBytes = all;
        this.fileUploadedLoadedBytes = loaded;
        this.fileUploadCalcSpeed();
        
        var percent = Math.ceil((loaded / all) * 100);
        if( done == count ){
            percent = 100;
        }
        this.fileUploadDialogProgress.setValue( percent );
        
        if( failed == 0 && all == loaded ){
            if( !this.fileUploadCloseInfo ){
                this.fileUploadCloseInfo = new Element('span', {
                    text: _('This dialog closes in few seconds'),
                    style: 'padding-right: 15px; color: gray;'
                }).inject( document.id(this.fileUploadCancelBtn), 'before' );
                (function(){
                    this.cancelUploads();
                }.bind(this)).delay(4000);
            }
        } else if( all == loaded ){
            this.fileUploadCancelBtn.setText(_('Close'));
        }

    },
    
    fileUploadCalcSpeed: function( pForce ){
    
        if( this.fileUploadSpeedInterval && !pForce ) return;
        
        var speed = ' -- KB/s, '+_('%s minutes left').replace('%s', '--:--');
        var again = false;

        if( this.fileUploadSpeedLastCheck == 0 ){
            this.fileUploadSpeedLastCheck = (new Date()).getTime()-1000;
        }

        var timeDiff = (new Date()).getTime() - this.fileUploadSpeedLastCheck;
        var bytesDiff = this.fileUploadedLoadedBytes - this.fileUploadedSpeedLastLoadedBytes;

        var d = timeDiff/1000;

        var byteSpeed = bytesDiff / d;
        
        if( byteSpeed > 0 )
            this.fileUploadSpeedLastByteSpeed = byteSpeed;
        
        var residualBytes = this.fileUploadedTotalBytes - this.fileUploadedLoadedBytes;
        var time = '<span style="color: green;">'+_('Done')+'</span>';
        if( residualBytes > 0 ){
            
            var timeLeftSeconds = residualBytes/byteSpeed;
            var timeLeft = (timeLeftSeconds/60).toFixed(2);
        
            time = _('%s minutes left').replace('%s', timeLeft);
        } else {
            //done
            clearInterval(this.fileUploadSpeedInterval);
            this.fileUploadMinimizeBtn.hide();
        }
        
        if( this.fileUploadSpeedLastByteSpeed == 0 ){
            speed = ' -- KB/s';
        } else {
            speed = ' '+this.bytesToSize(this.fileUploadSpeedLastByteSpeed)+' KB/s, '+time;
        }
        
        this.fileUploadDialogAllSpeed.set('html', speed);
    
        this.fileUploadSpeedLastCheck = (new Date()).getTime();
        
        this.fileUploadedSpeedLastLoadedBytes = this.fileUploadedLoadedBytes;
        
        if( !this.fileUploadSpeedInterval ){
            this.fileUploadSpeedInterval = this.fileUploadCalcSpeed.periodical(500, this, true);
        }
    },
    
    uploadProgress: function( pFile, pBytesCompleted, pBytesTotal ){

        var percent = Math.ceil((pBytesCompleted / pBytesTotal) * 100);
        this.uploadTrs[ pFile.id ].progress.setValue( percent );
        this.uploadTrs[ pFile.id ].loaded = pBytesCompleted;
        
        this.uploadAllProgress();
    },
    
    uploadStart: function( pFile ){

        this.uploadTrs[ pFile.id ].status.set('html', _('Uploading ...'));

    },
    
    uploadComplete: function( pFile ){
    
        if( !this.uploadTrs[ pFile.id ] ) return;
        
        this.uploadTrs[ pFile.id ].status.set('html', '<span style="color: green">'+_('Complete')+'</span>');
        this.uploadTrs[ pFile.id ].progress.setValue( 100 );

        this.uploadTrs[ pFile.id ].complete = true;
        this.uploadTrs[ pFile.id ].loaded = pFile.size;
        
        this.uploadTrs[ pFile.id ].deleteTd.destroy();
        
        this.uploadAllProgress();
        
        if( this && this.reload )
            this.reload();

        this.uploadNext();
        
    },
    
    uploadError: function( pFile ){

        if( !pFile ) return;

        if( !this.uploadTrs[ pFile.id ] ) return;
        
        this.uploadTrs[ pFile.id ].deleteTd.destroy();
                
        if( ka.settings.upload_max_filesize && ka.settings.upload_max_filesize < pFile.size ){
            this.uploadTrs[ pFile.id ].status.set('html', '<span style="color: red">'+_('File size limit exceeded')+'</span>');
            new Element('img', {
                style: 'position: relative; top: 2px; left: 2px;',
                src: _path+'inc/template/admin/images/icons/error.png',
                title: _('The file size exceeds the limit allows by upload_max_filesize or post_max_size on your server. Please contact the administrator.')
            }).inject( this.uploadTrs[ pFile.id ].status );
        } else {
            if( this.uploadTrs[ pFile.id ].canceled ){
                this.uploadTrs[ pFile.id ].status.set('html', '<span style="color: red">'+_('Canceled')+'</span>');
            } else {
                this.uploadTrs[ pFile.id ].status.set('html', '<span style="color: red">'+_('Unknown error')+'</span>');
            }
        }
        
        this.uploadTrs[ pFile.id ].error = true;

        this.uploadAllProgress();

        this.uploadNext();
                
    },
    
    clearUploadVars: function(){
        
        this.uploadFilesCount = 0;
        delete this.uploadTrs;
        
        this.uploadTrs = {};
        this.uploadFileNames = {};
        
        
        this.fileUploadedTotalBytes = 0;
        this.fileUploadedLoadedBytes = 0;
        this.fileUploadSpeedLastCheck = 0;
        this.fileUploadedSpeedLastLoadedBytes = 0;
        
        this.fileUploadedLoadedBytes = 0;
        this.fileUploadSpeedLastByteSpeed = 0;

        delete this.fileUploadSpeedInterval;
        
        delete this.fileUploadDialog;
        
        if( this.uploadOverwriteAllButton )
            this.uploadOverwriteAllButton.destroy();

        delete this.uploadOverwriteAllButton;
    },
    
    cancelUploads: function(){
        
        try {
            //flash calls are sometimes a bit buggy.
        
            Object.each(this.uploadTrs, function(tr, id){
                if( !tr.complete && tr.file ){
                    if( tr.file.html5 ){
                        if( this.html5UploadXhr[ tr.file.id ] )
                            this.html5UploadXhr[ tr.file.id ].abort();
                    } else {
                        ka.uploads[this.win.id].cancelUpload( id );
                    }
                }
            }.bind(this))
        } catch( e ){
            logger(e);
        }
        
        if( this.fileUploadDialog )
            this.fileUploadDialog.close();
        
        if( this.fileUploadSpeedInterval )
            clearInterval(this.fileUploadSpeedInterval);
        
        this.clearUploadVars();
        
    },
    
    initSWFUpload: function(){

        ka.uploads[this.win.id] = new SWFUpload({
            upload_url: _path+"admin/files/upload/?"+window._session.tokenid+"="+window._session.sessionid,
            file_post_name: "file",
            flash_url : _path+"inc/template/admin/swfupload.swf",
            file_upload_limit : "500",
            file_queue_limit : "0",

            file_queued_handler: this.newFileUpload.bind(this),
            upload_progress_handler: this.uploadProgress.bind(this),
            upload_start_handler: this.uploadStart.bind(this),
            upload_success_handler: this.uploadComplete.bind(this),
            upload_error_handler: this.uploadError.bind(this),

            button_placeholder_id : this.buttonId,
            button_width: 26,
            button_height: 20,
            button_text : '<span class="button"></span>',
            button_text_top_padding: 0,
            button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
            button_cursor: SWFUpload.CURSOR.HAND
        });
    },
    
    loadModules: function(){
        Object.each(ka.settings.configs, function(config, ext){
            this._modules.include(ext+'/');
        }.bind(this));
        this.loadPath('/');
    },

    newUploadBtn: function(){
    
        this.uploadBtn = this.boxAction.addButton( _('Upload file'), _path+'inc/template/admin/images/admin-files-uploadFile.png');
            
        if( !window.FormData ){    
            this.uploadBtn.addEvent('mousedown', function(e){
                e.stopPropagation();
            });
            this.buttonId = this.win.id+'_'+Math.ceil(Math.random()*100);
            this.uploadBtn.set('html', '<span id="'+this.buttonId+'"></span>');
            this.initSWFUpload();
        } else {
            
            this.uploadFileChoser = new Element('input', {
                type: 'file',
                multiple: true
            }).inject( this.win.content );
            
            this.uploadBtn.addEventListener("click", function (e) {
                this.uploadFileChoser.click();
                e.preventDefault();
            }.bind(this), false);
            
            this.uploadFileChoser.addEventListener("change", function (e) {
                this.checkFileDrop();
            }.bind(this), false);
            
        }
    },
    
    _createLayout: function(){

        var boxNavi = this.win.addButtonGroup();

        var toLeft = new Element('img', {
            src: _path+'inc/template/admin/images/admin-files-toLeft.png'
        });
        boxNavi.addButton( _('Back'), _path+'inc/template/admin/images/admin-files-toLeft.png', function(){
            this.goHistory('left');
        }.bind(this));

        boxNavi.addButton( _('Forward'), _path+'inc/template/admin/images/admin-files-toRight.png', function(){
            this.goHistory('right');
        }.bind(this));

        this.upBtn = boxNavi.addButton( _('Up'), _path+'inc/template/admin/images/admin-files-toUp.png', this.up.bind(this) );
        this.upBtn.fileObj = this;
        
        boxNavi.addButton( _('Refresh'), _path+'inc/template/admin/images/admin-files-refresh.png', this.reload.bind(this) );
        
        var boxAction = this.win.addButtonGroup();
        this.boxAction = boxAction;
        boxAction.addButton( _('New file'), _path+'inc/template/admin/images/admin-files-newFile.png', this.newFile.bind(this) );
        boxAction.addButton( _('New directory'), _path+'inc/template/admin/images/admin-files-newDir.png', this.newFolder.bind(this) );

        this.newUploadBtn();

        this.upBtn.addClass('admin-files-droppables');
        
        //view types
        var boxTypes = this.win.addButtonGroup();
        this.typeButtons = new Hash();
        
        this.typeButtons['icon'] = boxTypes.addButton( _('Icon view'), _path+'inc/template/admin/images/admin-files-list-icons.png',
            this.setListType.bind(this, 'icon'));
            
        this.typeButtons['miniatur'] = boxTypes.addButton( _('Image view'), _path+'inc/template/admin/images/admin-files-list-miniatur.png', this.setListType.bind(this, ['miniatur', null, 70]));

//      this.typeButtons['image']  = boxTypes.addButton( 'Bilderansicht',
//          _path+'inc/template/admin/images/admin-files-list-images.png', this.setListType.bind(this, 'image'));

        this.typeButtons['detail'] = boxTypes.addButton( _('Detail view'), _path+'inc/template/admin/images/admin-files-list-detail.png', this.setListType.bind(this, 'detail'));

        this.typeButtons.each(function(btn){
            btn.store('oriClass', btn.get('class'));
        });

        var userGrp = this.win.addButtonGroup();
        this.userFilesBtn = userGrp.addButton(_('Hide system files'), _path+'inc/template/admin/images/icons/folder_brick.png', this.toggleUserMode.bind(this));
        this.userFilesBtn.setPressed( this.options.onlyUserDefined );
        this.userFilesBtn.addEvent('click', function(){
            this.renderInfos();
        }.bind(this));

        //address
        var addressPos = new Element('div', {
            'class': 'admin-files-actionBar-addressPos'
        }).inject( this.win.titleGroups );
        this.address = new Element('input', {
            'class': 'admin-files-actionBar-address',
            value: '/'
        })
        .addEvent('mousedown', function(e){
            e.stopPropagation();
        })
        .addEvent('keyup', function(e){
            if(e.key == 'enter' )
                this.loadPath(this.value);
        }.bind(this)).inject( addressPos );
        
        var searchPos = new Element('div', {
            'class': 'admin-files-actionBar-searchPos'
        }).inject( this.win.titleGroups );

        this.searchInput = new Element('input', {
            'class': 'admin-files-actionBar-search'
        })
        .addEvent('keyup', function(e){
            this.startSearch();
        }.bind(this))
        .addEvent('mousedown', function(e){
            e.stopPropagation();
        })
        .inject( searchPos );
        
        this.fileContainer = new Element('div', {
            'class': 'admin-files-droppables admin-files-fileContainer'
        })
        .addEvent('mousedown', function( pEvent ){
            this.checkMouseDown( pEvent );
        }.bind(this))
        .addEvent('mouseup', function( pEvent ){
            this.drag = false;
            
            if( this.lastDragTimer )
                clearTimeout( this.lastDragTimer );
                
            this.checkMouseClick( pEvent );
            this.closeSearch();

        }.bind(this))
        .addEvent('dblclick', function( pEvent ){
            this.checkMouseDblClick( pEvent );
            this.closeSearch();
        }.bind(this))
        .addEvent('mousemove', function(pEvent){
            this.checkMouseMove(pEvent);
        }.bind(this))
        .inject( this.win.content );

        this.win.content.addEventListener('dragover', this.checkFileDragOver.bind(this));
        this.win.content.addEventListener('dragleave', this.checkFileDragLeave.bind(this));
        this.win.content.addEventListener('drop', this.checkFileDrop.bind(this));

        this.fileContainer.fileObj = this;
        

        this.loader = new ka.loader().inject( this.win.content );
        this.loader.setStyle('left', 141);
        
        this.infos = new Element('div', {
            'class': 'admin-files-infos'
        }).inject( this.win.content );

        this.setListType('icon', true); //TODO retrieve cookie
    },

    toggleUserMode: function(){
        if( this.options.onlyUserDefined )
            this.options.onlyUserDefined = false;
        else 
            this.options.onlyUserDefined = true;
        this.userFilesBtn.setPressed( this.options.onlyUserDefined );
        Cookie.write( 'adminFiles_OnlyUserFiles', (this.options.onlyUserDefined)?1:0 );
        this.reRender();
        //this.renderFiles();
        //this.saveCookie();
    },


    setListType: function( pType, noReload, pSetIconZoom ){
    
        this.typeButtons.each(function(btn){
            btn.set('class', btn.retrieve('oriClass'));
        });
        var b = this.typeButtons[pType];
        b.set('class', b.get('class') + ' buttonHover');
        
        this.listType = pType;
        
        if( this.listType == 'detail' ){
            this.fileContainer.addClass('admin-files-fileContainer-details');
        } else {
            this.fileContainer.removeClass('admin-files-fileContainer-details');
        }
        
        if( !noReload ){
            this.reRender();
        }
        
        if( pSetIconZoom ){
            this.setIconZoom( pSetIconZoom );
        } else {
            this.setIconZoom();
        }
    },
    
    
    newFile: function(){
        this.win._prompt(_('File name'), '', function(name){
            if( !name) return;
            new Request.JSON({url: _path+'admin/files/newFile/', onComplete: function(res){
                this.reload();
            }.bind(this)}).post({path: this.current, name: name});
        }.bind(this));
    },
    
    newFolder: function(){
        this.win._prompt(_('Folder name'), '', function(name){
            if( !name) return;
            new Request.JSON({url: _path+'admin/files/newFolder/', onComplete: function(res){
                this.reload();
            }.bind(this)}).post({path: this.current, name: name});
        }.bind(this));
    },
    
    rename: function( pFile ){
        var name = this.win._prompt(_('Rename')+': ', pFile.name, function(name){
            if( !name) return;
            new Request.JSON({url: _path+'admin/files/renameFile/', onComplete: function(res){
                this.reload();
            }.bind(this)}).post({path: this.current, name: pFile.name, newname: name});
        }.bind(this));
    },
    
    remove: function(){
    
        var selectedFiles = this.getSelectedFiles();

        if(! Object.getLength(selectedFiles) > 0 ) return;
        
        this.win._confirm(_('Really remove selected file/s?'), function(res){
            if(!res) return;
            Object.each(selectedFiles, function(item){
                

                if( item.path.substr(0, 6) == 'trash/' ) {
                    item.name = item.path.replace( /.*\//, '' );
                }
            
                new Request.JSON({url: _path+'admin/files/deleteFile/', onComplete: function(res){
                    this.reload();
                }.bind(this)}).post({path: this.current, name: item.name});

            }.bind(this));

        }.bind(this));
    },
    
    paste: function(){
    
        if(! ka.getClipboard().type == 'filemanager' && !ka.getClipboard().type == 'filemanagerCut') return;
        
        var files = [];
        
        var clipboard = ka.getClipboard('filemanager');
        var move = 0;
        
        if( ka.getClipboard().type == 'filemanagerCut' ){
            clipboard = ka.getClipboard('filemanagerCut');
            move = 1;
        }
        
        if( clipboard ){
            Object.each(clipboard.value,function(file){
                files.include(file.path);
            });
        }

        if( move == 1 )
            this.moveFiles( files, this.current );
        else
            this.copyFiles( files, this.current );
    },

    moveFiles: function( pFilePaths, pTargetDirectory, pOverwrite, pCallback ){
    
        new Request.JSON({url: _path+'admin/files/paste', noCache: 1, onComplete: function(res){
            if(res.exist){
                this.win._confirm(_('One or more files already exist. Overwrite ?'), function(p){
                    
                    if(!p)return;
                    this.moveFiles( pFilePaths, pTargetDirectory, true );
                    
                }.bind(this));
            } else {
                this.reload();
                if( pCallback )
                    pCallback();
            }
        }.bind(this)}).post({from: pFilePaths, to: pTargetDirectory, overwrite: pOverwrite, move: 1});

    },
    
    copyFiles: function( pFilePaths, pTargetDirectory, pOverwrite ){
    
        new Request.JSON({url: _path+'admin/files/paste', noCache: 1, onComplete: function(res){
            if( res.exist ){
                this.win._confirm(_('One or more files already exist. Overwrite ?'), function(p){
                    if( !p )return;
                    this.copyFiles( pFilePaths, pTargetDirectory, true );
                }.bind(this));
            } else {
                this.reload();
            }
        }.bind(this)}).post({from: pFilePaths, to: pTargetDirectory, overwrite: pOverwrite});

    },
    
    loadPath: function( pPath, pCallback ){

        if( pPath.substr(0,6) == 'trash/' && pPath.length >= 7 ){
            this.win._alert(_('You cannot open a file in the trash folder. To view this file, press right click and choose recover.'));
            return;
        }

        if( this.history[ this.historyIndex ] != pPath ){
            this.historyIndex++;
            this.history[ this.historyIndex ] = pPath;
            this.load( pPath, pCallback );
        }
    },
    
    getUpPath: function(){
        var pos = this.current.substr( 0, this.current.length-1).lastIndexOf( '/' );
        return this.current.substr( 0, pos+1 );  
    },
    
    up: function(){
        if( this.current.substr( this.current.length-1, 1) == '/' && this.current.length > 1 ){ 
            this.loadPath( this.getUpPath() );
        }
    },
    
    goHistory: function( pWay ){
        if( pWay == 'left' ){
            this.historyIndex--;
            if(! this.history[ this.historyIndex ] )
                this.historyIndex++;
        } else {
            this.historyIndex++;
            if(! this.history[ this.historyIndex ] )
                this.historyIndex--;
        }

        var path = this.history[ this.historyIndex ];
        this.load( path );
    },
    
    reload: function(){
        this.load( this.current );
    },
    
    renderInfos: function( pFiles ){
        
        if( pFiles )
            this.renderFiles = pFiles;
        else
            pFiles = this.renderFiles;
        
        this.infos.empty();

        if( !this.options.onlyUserDefined ){
        
            new Element('div', {
                text: _('Kryn')
            }).inject( this.infos );
    
            Object.each(pFiles, function(file){
                if( this._krynFolders.indexOf(file.path) >= 0){
                    this.newInfoItem(file);
                }
            }.bind(this));
            
            new Element('div', {
                text: _('Extensions')
            }).inject( this.infos );

            Object.each(pFiles, function(file){
                if( this._modules.indexOf(file.path) >= 0){
                    this.newInfoItem(file);
                }
            }.bind(this));
        
        }
        
        new Element('div', {
            text: _('User defined')
        }).inject( this.infos );

        Object.each(pFiles, function(file){
            if( this._modules.indexOf(file.path) == -1 && this._krynFolders.indexOf(file.path) == -1){
                this.newInfoItem(file);
            }
        }.bind(this));
    },
    
    newInfoItem: function( pFile ){
    
        if( pFile.type != 'dir' ) return;

        var item = new Element('a', {
            text: pFile.name,
            'class': 'admin-files-droppables'+((pFile.path=='trash/')?' admin-files-item-dir_bin':'')
        })
        .addEvent('mousedown', function(e){ e.stop() })
        .addEvent('click', this.loadPath.bind(this, pFile.path ))
        .inject( this.infos ); 
        
        item.store('file', pFile);
        item.fileObj = this;
    },
    
    load: function( pPath, pCallback ){
        
        if( this.curRequest )
            this.curRequest.cancel();

        this.loader.show();
            
        this.curRequest = new Request.JSON({url: _path+'admin/files/loadFolder/', noCache: 1, onComplete: function(res){
    
            if(! res ){
                this.loader.hide();
                alert( _('%s: file not found').replace('%s', pPath ) );
                return;
            }
            if( res.type == 'file' ){
                this.history[ this.historyIndex ] = null;
                this.historyIndex--;
                ka.wm.openWindow( this.win.module, this.win.code+'/edit', null, null, {file: res});
                this.loader.hide();
                return;
            }
            
            if( res.type == 'dir' && res.folderFile.path == 'trash/' ){
                this.boxAction.hide();
            } else {
                this.boxAction.show();
            }
            
            this.current = pPath;

            if( res.type == 'dir' ){
            
                this.setTitle();
                this.currentFolderFile = res.folderFile;
                this.fileContainer.store('file', res.folderFile);
                
                if( this.currentFolderFile.writeaccess == true ){
                    this.boxAction.show();
                }
                
                if( this.current.substr( this.current.length-1, 1) != '/' )
                    this.current += '/';
                if( this.current.substr( 0, 1) != '/' )
                    this.current = '/'+this.current;
                
                this.address.value = this.current;

                this.render( res.items );

                if( this.current == '/' ){
                    this.renderInfos( res.items );
                }
            }

            this.loader.hide();
            
            this.upBtn.store('file', {isDir: true, path: this.getUpPath()});
            
            if( pCallback ){
                pCallback();
            }
        
        }.bind(this)}).post({ path: pPath });
    },

    reRender: function(){
        this.render( this.files );
    },

    render: function( pItems ){

        this.files = pItems;
        this.fileContainer.empty();
        
        var nfiles = [];
        //first folders, then files
        Object.each(this.files, function(f){
            if( f.type == 'dir' ){
                if( this.options.onlyUserDefined == true && (this._krynFolders.indexOf( f.path ) >= 0 ||  this._modules.indexOf(f.path) >= 0 ) ) {
                    return;
                }
                nfiles.include( f );
            }
        }.bind(this));

        Object.each(this.files, function(f){
            if( f.type != 'dir' ){
                nfiles.include( f );
            }
        });
        this.files2View = nfiles;
        var items = [];

        if( this.listType == 'icon' || this.listType == 'miniatur' ){
            items = this.renderIcons( this.files2View );
        }

        if( this.listType == 'image' ){
            this.renderImage();
        }

        if( this.listType == 'detail' ){
            this.renderDetail();
        }
        
    },
    
    checkFileDragOver: function( pEvent ){
        var file;

        pEvent.stopPropagation();
        pEvent.preventDefault();
        
        if( !window.FormData ){
            return;
        }
        
        var item = pEvent.target;
        
        if( !item.hasClass('admin-files-item') ){
            item = item.getParent('.admin-files-item');
        }

        if( !item && pEvent.target.hasClass('admin-files-droppables') ){
            item = pEvent.target;
        }

        if( item )
            file = item.retrieve('file');
        
        if( file && file.isDir == true && file.path != 'trash/' && file.path != '/' && !item.hasClass('admin-files-fileContainer') ){
            item.addClass('admin-files-item-selected');
        } else {
            this.fileContainer.addClass('admin-files-fileContainer-selected');
        }

    },
    
    checkFileDragLeave: function( pEvent ){

        pEvent.stopPropagation();  
        pEvent.preventDefault();
    
        var item = pEvent.target;
        
        if( !item.hasClass('admin-files-item') ){
            item = item.getParent('.admin-files-item');
        }
        if( !item && pEvent.target.hasClass('admin-files-droppables') ){
            item = pEvent.target;
        }
        
        if( item ){
            item.removeClass('admin-files-item-selected');
        }
    
        this.fileContainer.removeClass('admin-files-fileContainer-selected');
    
    },
    
    checkFileDrop: function( pEvent ){
        var file;

        if( pEvent ){
            pEvent.stopPropagation();  
            pEvent.preventDefault();
        }

        if( !window.FormData ){
            return;
        }

        this.fileContainer.removeClass('admin-files-fileContainer-selected');
        
        var files = (pEvent)?pEvent.dataTransfer.files:this.uploadFileChoser.files;
        
        if( pEvent ){
            var item = pEvent.target;
            
            if( !item.hasClass('admin-files-item') ){
                item = item.getParent('.admin-files-item');
            }
    
            if( !item && pEvent.target.hasClass('admin-files-droppables') ){
                item = pEvent.target;
            }
        }
        
        if( !this.html5FileUploads )
            this.html5FileUploads = {};

        if( item ){
            file = item.retrieve('file');
            item.removeClass('admin-files-item-selected');
        }
            
        if( file && (file.isDir != true || file.path == 'trash/') ){
            return;
        }
        
        if( !file && this.current == 'trash/' ) return;

        Array.each(files, function( chosenFile ){

            if( file ){
                chosenFile.post = {path: file.path};
            }
            
            chosenFile.html5 = true;
            chosenFile.id = 'HTML5_'+Object.getLength(this.html5FileUploads);
            
            this.html5FileUploads[ chosenFile.id ] = chosenFile;

            this.newFileUpload( this.html5FileUploads[ chosenFile.id ] );

        }.bind(this));

    },
    
    startHtml5Upload: function( pFileId ){

        var file = this.html5FileUploads[ pFileId ];
        
        var xhr = new XMLHttpRequest();

        this.html5UploadXhr[ pFileId ] = xhr;
        
        if( xhr.upload ){
        
            xhr.upload.addEventListener("progress", function(pEvent) {
                this.uploadProgress( file, pEvent.loaded, pEvent.total );
            }.bind(this), false);

            xhr.onreadystatechange = function(e) {
                if( xhr.readyState == 4 ){
                    if( xhr.status == 200 ){
                        this.uploadComplete( file );
                    } else {
                        this.uploadError( file );
                    }
                }
            }.bind(this);

            if( !file.post ) file.post = {};
            
            this.uploadStart( file );
            
            file.post[window._session.tokenid] = window._session.sessionid;

            xhr.open("POST", _path+"admin/files/upload/?"+Object.toQueryString(file.post), true);
                            
            var formData = new FormData();
            formData.append('file', file);
            xhr.send( formData );

        }
    
    },
    
    startSelector: function( pEvent ){
    
        var offset = this.win.content.getPosition( document.body );
        
        if( this.selectorDiv ){
            this.selectorDiv.destroy();
            delete this.selectorDiv;       
        }
    
        this.selectorDiv = new Element('div', {
            'class': 'admin-files-selector',
            styles: {
                'top': pEvent.page.y-offset.y+1,
                'left': pEvent.page.x-offset.x+1,
                width: 2,
                height: 2
            }
        })
        .setStyle('opacity', 0.5)
        .inject( this.win.content );
        
        this.selectorStartMousePos = {
            x: pEvent.page.x,
            y: pEvent.page.y
        };
        
        this.selectorStartPos = {
            x: pEvent.page.x-offset.x,
            y: pEvent.page.y-offset.y
        };
        
        var diffY, diffX, curPos, file;
        
        var items = this.fileContainer.getElements('.admin-files-item');

        Array.each(items, function(item){
            item.pos = item.getPosition( this.win.content );
            item.size = item.getSize();
        }.bind(this));
        
        this.selectorDrag = new Drag(this.selectorDiv, {
            style: false,
            onDrag: function( pElement, pEvent ){
            
                diffY = pEvent.page.y-this.selectorStartMousePos.y;
                diffX = pEvent.page.x-this.selectorStartMousePos.x;
                
                if( diffX < 0 ){
                    diffX *= -1;
                    this.selectorDiv.setStyle('left', this.selectorStartPos.x-diffX);
                }
                if( diffY < 0 ){
                    diffY *= -1;
                    this.selectorDiv.setStyle('top', this.selectorStartPos.y-diffY);
                }
                
                this.selectorDiv.setStyle('width', diffX);
                this.selectorDiv.setStyle('height', diffY);
                
                curPos = {
                    left: this.selectorDiv.getStyle('left').toInt(),
                    top: this.selectorDiv.getStyle('top').toInt(),
                    width: this.selectorDiv.getStyle('width').toInt(),
                    height: this.selectorDiv.getStyle('height').toInt()
                }
                
                Array.each(items, function(item){

                    if( (item.pos.x+item.size.x) > curPos.left &&
                        item.pos.x < (curPos.left+curPos.width) &&
                        item.pos.y < (curPos.top+curPos.height) &&
                        (item.pos.y+item.size.y) > curPos.top 
                     ){
                        file = item.retrieve('file');
                        if( file && file.path != 'trash/' ){
                            item.addClass('admin-files-item-selected');
                        }
                    } else {
                        item.removeClass('admin-files-item-selected');
                    }
                    
                });
            
            }.bind(this),
            
            onComplete: function(){
                this.selectorDiv.destroy();
                delete this.selectorDiv;
            }.bind(this),
            
            onCancel: function(){
                this.selectorDiv.destroy();
                delete this.selectorDiv;
            }.bind(this),
        
        });
        
        this.selectorDrag.start( pEvent );
    
    },
    
    checkMouseDown: function( pEvent ){
    
        var item = pEvent.target;
        
        selection = window.getSelection() ;
        selection.removeAllRanges();
            
        (function(){
            selection = window.getSelection() ;
            selection.removeAllRanges();
        }).delay(40);
        
        if( !item.hasClass('admin-files-item') ){
            item = item.getParent('.admin-files-item');
        }

        if( !item ){
            item = pEvent.target.getParent('tr');
        }

        pEvent.preventDefault();
        
        if( item ){
            var file = item.retrieve('file');
    
            if( file && file.path != 'trash/' )
                this.startDrag( pEvent, item );
                
        } else if( !pEvent.rightClick ){

            if( pEvent.target.hasClass('admin-files-fileContainer') ){
                this.deselectAll();
                this.startSelector(pEvent);
            }

        }
    },

    checkMouseDblClick: function( pEvent ){
    
        var item = pEvent.target;

        if( !item.hasClass('admin-files-item') ){
            item = item.getParent('.admin-files-item');
        }
        if( !item ){
            item = pEvent.target.getParent('tr');
        }

        if( !item ) return;
        
        var file = item.retrieve('file');
        
        if( file )
            this.loadPath( file.path );
        

    },
    
    checkMouseMove: function( pEvent ){
    
        if( !ka.inFileDragMode ) return;
        
        if( !this.win.isInFront() ){
            this.win.toFront();
        }
    
    },
    
    checkMouseClick: function( pEvent ){

        if( !pEvent ) return;

        if( (!pEvent.control && !pEvent.meta && !pEvent.shift ) && !pEvent.rightClick )
            this.deselectAll();
        
        if( !pEvent.target ) return;
        
        var item = pEvent.target;
        
        if( !item.hasClass('admin-files-item') ){
            item = item.getParent('.admin-files-item');
        }
        
        if( !item ){
            item = pEvent.target.getParent('tr');
        }
        
        if( !item ){

            this.deselectAll();

            if( pEvent.rightClick )
                this.openContext( this.currentFolderFile, pEvent );
            
            return;
        }
        
        if( pEvent.shift ){

            var allSelected = this.fileContainer.getElements('.admin-files-item-selected');
            var all = this.fileContainer.getElements('.admin-files-item');

            var firstPos = all.indexOf(allSelected[0]);
            //var lastPos = all.indexOf(allSelected[ allSelected.length-1 ]);
            
            var thisPos = all.indexOf( item );
            var tfile, i;
            
            
            if( thisPos > firstPos ){
                for( i = firstPos; i<thisPos; i++){
                    file = all[i].retrieve('file');
                    if( file.path != 'trash/' )
                        all[i].addClass('admin-files-item-selected');
                }
            } else {
                for( i = thisPos; i<firstPos; i++){
                    file = all[i].retrieve('file');
                    if( file.path != 'trash/' )
                        all[i].addClass('admin-files-item-selected');
                    all[i].addClass('admin-files-item-selected');
                }
            }
            
        }

        var file = item.retrieve('file');

        if( !item.hasClass('admin-files-item-selected') ){

            if( file && file.path != 'trash/' )
                item.addClass('admin-files-item-selected');

        } else if( pEvent.control || pEvent.meta ) {
            item.removeClass('admin-files-item-selected');
        }
        
        if( pEvent.rightClick && file ){
            this.openContext( file, pEvent );
        }

    },
    
    getSelectedFiles: function(){
        var res = {};

        this.fileContainer.getElements('.admin-files-item-selected').each(function(item){
            var file = item.retrieve('file');
            res[ file.path ] = file;
        });
    
        return res;
    },
    
    getSelectedItems: function(){
        var res = {};

        this.fileContainer.getElements('.admin-files-item-selected').each(function(item){
            var file = item.retrieve('file');
            res[ file.path ] = item;
        });
    
        return res;
    },
    
    preview: function( pEvent ){
    
        if( pEvent.target && pEvent.target.get('tag') == 'input' && !pEvent.target.hasClass('admin-files-preview-input') ){
            return;
        }

        var selectedItems = this.getSelectedItems();
        
        if( this.previewDiv ){
            this.previewDiv.destroy();
            delete this.previewDiv;
            return;
        }
        
        if( !this.previewInput ){
        
            this.previewInput = new Element('input', {
                'class': 'admin-files-preview-input'
            }).inject( document.hidden );
            this.previewInput.addEvent('blur', function(){
                if( this.previewDiv ){
                    this.previewDiv.destroy();
                    delete this.previewDiv;
                }
            }.bind(this));
            
            this.previewInput.addEvent('keydown', function(e){
                if(  e.key == 'space' && this.previewDiv ){
                    this.previewDiv.destroy();
                    delete this.previewDiv;
                    e.stop();
                }
            }.bind(this));
        
        }

        if( Object.getLength(selectedItems) == 1 ){
            
            var item, file, image;
            
            Object.each( selectedItems, function(citem){
                item = citem;
            });
            
            this.previewInput.focus();
            
            file = item.retrieve('file');
            
            pEvent.preventDefault();
        
            this.previewDiv = new Element('div', {
                'class': 'admin-files-preview'
            }).inject( document.id('desktop') );
            
            this.previewDiv.makeDraggable();
            
            this.previewDiv.addEvent('mouseup', function(){
                this.previewInput.focus();
            }.bind(this));
            
            this.previewDiv.addEvent('mousedown', function(){
                this.previewInput.focus();
            }.bind(this));
            
            new Element('img', {
                src: _path+'inc/template/admin/images/loading.gif',
                style: 'margin-top: 270px;'
            }).inject( this.previewDiv );

            this.previewDiv.position();
            
            var image;
            
            if( this.__images.contains(file.ext.toLowerCase()) ){
                image = _path+'inc/template/'+file.path;
                Asset.image(image, {
                    onLoad: function(){
                        
                        if( this.lastPreviewPath != image ) return;
                        
                        this.previewDiv.empty();
                        var img = new Element('img', {
                            src: image,
                            style: 'position: relative;'
                        }).inject( this.previewDiv );
                        
                        (function(){ img.position();}).delay(10);
                        (function(){ img.position();}).delay(50);
                        (function(){ img.position();}).delay(250);
                    
                    }.bind(this)
                });
            } else {
                this.previewDiv.empty();
                item.getElement('img').clone().inject( this.previewDiv );
            }
            
            this.lastPreviewPath = image;

        }
        
        
    
    },
    
    startDrag: function( pEvent, pItem ){

        this.drag = true;

        this.lastDragTimer = (function(){
            if( this.drag == true )
                this._startDrag( pEvent, pItem );

        }).delay(300, this);
        
    },
    
    _startDrag: function( pEvent, pItem ){
    
        selection = window.getSelection() ;
        selection.removeAllRanges();
        
        if( !pItem.hasClass('admin-files-item-selected') ){
            pItem.addClass('admin-files-item-selected');
        }
        
        var desktop = document.id('desktop');
    
        var selectedItems = this.getSelectedItems();
        
        
        var selectedItems = this.getSelectedItems();
        var container;
        
        var draggedItems = [];
        var moveFiles = [];

        draggedItems.include( pItem );
        
        if( Object.getLength(selectedItems) == 1 ){
            
            var item;
            Object.each(selectedItems, function(selectedItem){
                item = selectedItem;
            });

            moveFiles.include( item.retrieve('file').path );
            
            container = item.clone();
            var pos = item.getPosition(desktop);
            
            container.setStyles({
                opacity: 0.7,
                left: pEvent.page.x-34,
                'top': pEvent.page.y-75,
                zIndex: 15000,
                position: 'absolute'
            }).inject( desktop );
            
        } else if( Object.getLength(selectedItems) > 1 ){
        
            container = new Element('div').setStyles({
                opacity: 0.7,
                zIndex: 15000,
                width: 50,
                height: 55,
                left: pEvent.page.x-20,
                'top': pEvent.page.y-70,
                cursor: 'default',
                position: 'absolute'
            }).inject( desktop );
            
            Object.each(selectedItems, function(item){
                
                draggedItems.include( item );
                moveFiles.include( item.retrieve('file').path );

                var clone = item.clone().setStyles({
                    position: 'absolute',
                    width: 50,
                    height: 50,
                    cursor: 'default',
                    'background-color': 'transparent',
                    margin: 0
                }).inject( container );
                
                if( clone.getElement('div') )
                    clone.getElement('div').destroy();
                    
                if( item.get('tag') == 'tr' ){
                    var imgClone = clone.getElement('img').clone();
                    clone.empty();
                    imgClone.inject( clone );
                }

                clone.getElement('img').setStyles({
                    width: 30,
                    height: 30
                });
                
                var i = Math.random();
                var r = (60*i)-30;
                
                if( this.lastRotateValue && this.lastRotateValue < 0 && r < 0)
                    r = r*-1;

                this.lastRotateValue = r;
                                
                clone.setStyle('-webkit-transform', 'rotate('+r+'deg)');
                clone.setStyle('-moz-transform', 'rotate('+r+'deg)');
        
            }.bind(this));
            
            new Element('div', {
                style: 'position: absolute; bottom: 0px; left: 0px; width: 100%; text-align: center;',
                text: _('%d files').replace('%d', Object.getLength(selectedItems))
            }).inject( container );
        
            
        } else if( Object.getLength(selectedItems) == 0 ){
            return;
        }
        
        var fromDir = this.current;
        if( fromDir != '/' )
            fromDir = fromDir.substr(1);

        this.newDragMove( pEvent, container, draggedItems, moveFiles, fromDir );
    },
    
    newDragMove: function( pEvent, pContainer, pDraggedItems, pFilePaths, pFromDir ){
        
        this.dragMove = new Drag.Move(pContainer, {

            droppables: '.admin-files-droppables',

            onDrop: function(element, droppable){
                
                ka.inFileDragMode = false;
                element.destroy();
                
                if( droppable.get('tag') == 'td' )
                    droppable = droppable.getParent();
                
                if( !droppable ) return;
            
                if( droppable.fileObj && droppable.fileObj.activeAutoDirOpenerTimeout )
                    clearTimeout(droppable.fileObj.activeAutoDirOpenerTimeout);
                    
                if( this.activeAutoDirOpenerTimeout ){
                    clearTimeout( this.activeAutoDirOpenerTimeout );
                }
            
                var file = droppable.retrieve('file');
                if( !file || file.path == pFromDir || file.path == 'trash/' || file.isDir != true ) return;
                    
                if( !pDraggedItems.contains( droppable ) ){
                    droppable.removeClass('admin-files-item-selected');
                    this.fileContainer.removeClass('admin-files-fileContainer-selected');  

                    droppable.fileObj.moveFiles( pFilePaths, file.path, false, function(){
                    
                        if( droppable.fileObj != this )
                            this.reload();
                    
                    }.bind(this));

                }
            }.bind(this),
            
            onEnter: function(element, droppable){

                if( droppable != pContainer ){
                
                    if( droppable.get('tag') == 'td' )
                        droppable = droppable.getParent();

                    var file = droppable.retrieve('file');
                                        
                    if( file && file.path != 'trash/' && file.isDir == true && !droppable.hasClass('admin-files-fileContainer') ){
                        droppable.addClass('admin-files-item-selected');
                        droppable.fileObj.startAutoDirOpener( file, this.updateDragMoveDroppables.bind(this) );
                    }
                    
                    if( !file || file.path == pFromDir || this.current == '/'+file.path ) return;

                    if( droppable.hasClass('admin-files-fileContainer') )
                        droppable.addClass('admin-files-fileContainer-selected');

                }
            }.bind(this),
            
            onLeave: function(element, droppable){
            
                if( droppable.get('tag') == 'td' )
                    droppable = droppable.getParent();
            
                if( droppable.fileObj && droppable.fileObj.activeAutoDirOpenerTimeout )
                    clearTimeout(droppable.fileObj.activeAutoDirOpenerTimeout);
            
                droppable.removeClass('admin-files-fileContainer-selected');
                if( !pDraggedItems.contains( droppable ) )
                    droppable.removeClass('admin-files-item-selected');
            },

            onCancel: function(dragging){
                dragging.destroy();
                ka.inFileDragMode = false;
            }

        });

        this.dragMove.start( pEvent );

        ka.inFileDragMode = true;
    },
    
    updateDragMoveDroppables: function(){
        if( this.dragMove )
            this.dragMove.droppables = $$('.admin-files-droppables');
    },
    
    startAutoDirOpener: function( pFile, pCallback ){
        
        this.activeAutoDirOpenerTimeout = (function(){
        
            this.loadPath( pFile.path, pCallback );
        
        }).delay(1000, this);
    
    },

    renderImage: function(){

    },

    renderDetail: function(){
        
        var pAdmin = _path+'inc/template/admin/';

        this.detailTable = new ka.Table([
            ['', 20],
            [_('Name')],
            [_('Size'), 100],
            [_('Last modified'), 155]
        ]).inject( this.fileContainer );

        var rows = [];
        this.files2View.each(function(file){
            

            var bg = '';
            if( file.type != 'dir' && this.__images.contains(file.ext.toLowerCase()) ){ //is image
                bg = 'image'
            } else if( file.type == 'dir' ) {
                bg = 'dir'
            } else if( this.__ext.contains(file.ext) ){
                bg = file.ext;
            } else {
                bg = 'tpl';
            }
            
            if( file.path == 'trash/' ){
                bg = 'dir_bin';
            }
              
            var image = new Element('img', {
                src: _path+'inc/template/admin/images/ext/'+bg+'-mini.png'
            });

            var size = file.size;
            
            if( file.type == 'dir' )
                size = _('Directory');

            rows.include([
                image,
                file.name,
                size,
                new Date(file.mtime*1000).format('db')
            ]);

        }.bind(this));

        this.detailTable.setValues( rows );

        this.detailTable.tableBody.getElements('tr').each(function(tr, id){
            tr.store('file', this.files2View[id]);
            tr.fileObj = this;
            tr.getElements('td').addClass('admin-files-droppables');
        }.bind(this));
    },
    
    setIconZoom: function( pZoom ){
    
        if( this.iconZoom ){
            this.fileContainer.removeClass('admin-files-item-size-'+this.iconZoom);
        }
    
        if( pZoom ){
            this.iconZoom = pZoom;
            this.fileContainer.addClass('admin-files-item-size-'+pZoom);
        } else {
            this.iconZoom = false;
        }
    
    },
    
    renderIcons: function( pItems ){
        var html = "";

        var knownExts = ["tpl", "html", "jpg"];
        var krynFiles = [];
        var moduleFiles = [];

        var files = [];
        
        if( pItems ){
            pItems.each(function(item){
                var titem = null;
                if( item.type == 'dir' ){
                    titem = this.__buildItem( item );
                }
                if( !titem ) return;
                
                if( this.current == '/' && titem ){
                    if( this._krynFolders.indexOf( item.path ) >= 0 ){
                        krynFiles.include( titem );
                    } else if( this._modules.indexOf(item.path) >= 0  ) {
                        moduleFiles.include( titem );
                    } else {
                        files.include( titem );
                    }
                } else {
                    files.include( titem );
                }
            }.bind(this));

            pItems.each(function(item){
                if( item.type != 'dir' ){
                    if( this.current == '/' )
                        files.include( this.__buildItem( item ) );
                    else
                        files.include( this.__buildItem( item ) );
                }
            }.bind(this));
        }

        if( this.current == '/' ){
            
            if( krynFiles.length > 0 ){
                new Element('div', {
                    'class': 'admin-files-seperator',
                    text: 'Kryn'
                }).inject( this.fileContainer );
                krynFiles.each(function(item){ item.inject( this.fileContainer ); }.bind(this));
            }
            
            if( moduleFiles.length > 0 ){
                new Element('div', {
                    'class': 'admin-files-seperator',
                    html: _('Extensions')
                }).inject( this.fileContainer );
                moduleFiles.each(function(item){ item.inject( this.fileContainer ); }.bind(this));
            }
            
            new Element('div', {
                'class': 'admin-files-seperator',
                html: _('User defined')
            }).inject( this.fileContainer );

            files.each(function(item){ if(item) item.inject( this.fileContainer ); }.bind(this));

        } else {
            files.each(function(item){ if(item) item.inject( this.fileContainer ); }.bind(this));
        }

        return files;
    },
    
    __buildItem: function( pFile ){
        
        var fileIcon;

        var base = new Element('div', {
            'class': (pFile.path=='trash/'?'':'admin-files-droppables ')+'admin-files-item',
            title: pFile.name
        });

        if( this.__images.contains( pFile.ext.toLowerCase() )  ){
        
            fileIcon = 'admin/backend/imageThumb/?'+Object.toQueryString({file: pFile.path, mtime: pFile.mtime});
            base.addClass('admin-files-item-image');

        } else {
            
            fileIcon = 'inc/template/admin/images/';

            if( pFile.type == 'dir' ){
                if( pFile.path == 'trash/' )
                    fileIcon += 'file-icon-bin.png';
                else
                    fileIcon += 'file-icon-folder.png';
            } else {
                fileIcon += 'file-icon-text.png';
            }

        }
        base.fileObj = this;
        
        new Element('img', {
            src: _path+fileIcon
        }).inject( base );
        
        new Element('div', {
            'text': (pFile.path == 'trash/')?_('Trash'):this.escTitle(pFile.name, base.getSize().x),
        }).inject( base );

        base.store('file', pFile);
        return base;
    },
    
    escTitle: function( pTitle, pSize ){
    
        //TODO, depend on the size
    
        var maxLine = 13;
        var maxAll = 24;
        if( this.listType == 'miniatur' ) {
            maxLine = 21;
            maxAll = 39;
        }
        pTitle = pTitle.substr(0,maxLine)+"\n"+pTitle.substr(maxLine, maxAll);
        if( pTitle.length > maxAll )
            pTitle =  pTitle.substr(0,maxAll)+'..';
            
        return pTitle;
    },
    
    recover: function( pFile ){
    
        this.win._confirm(_('This file will be moved to: %s')
                .replace('%s', '<br/><br/>'+pFile['original_path'].replace('inc/template','')+'<br/><br/>')+_('Are you really sure?'), function(res){
            if( res ){
            
                new Request.JSON({url: _path+'admin/files/recover', noCache: 1, onComplete: function(){
                    this.reload();
                }.bind(this)}).post({rsn: pFile.original_rsn});
            
            }
        }.bind(this));
    
    },
    
    openContext: function( pFile, pEvent ){

        if( this.context )
            this.context.destroy();

        if( pFile.path == 'trash/' ){
            return;
        }

        this.context = new Element('div', {
            'class': 'admin-files-context'
        }).inject( this.win.border );

        
        if( pFile.path.substr(0, 6) == 'trash/' ){
            //pressed on a item in the trash folder

            var recover = new Element('a', {
                html: _('Recover')
            })
            .addEvent('click', function(){
                this.recover( pFile );
            }.bind(this))
            .inject( this.context )
            
            var remove = new Element('a', {
                'class': 'delimiter',
                html: _('Remove')
            })
            .addEvent('click', this.remove.bind(this, pFile) )
            .inject( this.context );
            
        } else {
            

            if( this.currentFolderFile.path != pFile.path ){
                var open = new Element('a', {
                    html: _('Open')
                })
                .addEvent('click', function(){
                    this.loadPath( pFile.path );
                }.bind(this))
                .inject( this.context )
            }
            
            var externalPath = _path+pFile.path;
            if( pFile.path.substr(0,1) == '/' )
                externalPath = _path+pFile.path.substr(1,pFile.path.length);
            
            var openExternal = new Element('a', {
                html: _('Open external'),
                target: '_blank',
                href: externalPath
            })
            .inject( this.context )
            
        
            if( this.currentFolderFile.path == pFile.path ){
                //clicked on the background
    
                var paste = new Element('a', {
                    html: _('Paste (strg+v)')
                })
                .addEvent('click', this.paste.bind(this) )
                .inject( this.context );
                
            } else {
                
                var cut = new Element('a', {
                    'class': 'delimiter',
                    html: _('Cut (strg+x)')
                })
                .addEvent('click', this.cut.bind(this) )
                .inject( this.context );
                
                var copy = new Element('a', {
                    html: _('Copy (strg+c)')
                })
                .addEvent('click', this.copy.bind(this) )
                .inject( this.context );
    
                var duplicate = new Element('a', {
                    html: _('Duplicate')
                })
                .addEvent('click', this.duplicate.bind(this, pFile))
                .inject( this.context );
                
                var newversion = new Element('a', {
                    html: _('New version')
                })
                .addEvent('click', this.newversion.bind(this, pFile))
                .inject( this.context );
                
                var remove = new Element('a', {
                    'class': 'delimiter',
                    html: _('Remove')
                })
                .addEvent('click', this.remove.bind(this, pFile) )
                .inject( this.context );
                
                var rename = new Element('a', {
                    html: _('Rename')
                })
                .addEvent('click', this.rename.bind(this, pFile) )
                .inject( this.context );
            }
            
            var settings = new Element('a', {
                'class': 'delimiter',
                html: _('Properties')
            })
            .addEvent('click', function(){
                ka.wm.open('admin/files/properties', pFile);
            })
            .inject( this.context );
            
        }
        
        var deactivate = function ( item ){
            if( !item ) return;
            item.addClass('notactive')
            item.removeEvents('click');
        }
        
        var selectedFiles = this.getSelectedFiles();
        
        if( Object.getLength(selectedFiles) > 1 || pFile.type == 'dir' ){
            if( duplicate ) duplicate.destroy();
            if( newversion ) newversion.destroy();
        }
        
        if( Object.getLength(selectedFiles) > 1 ){
            deactivate(open);
            deactivate(openExternal);
            deactivate(settings);
            deactivate(rename);
        }

        if( ka.getClipboard().type != 'filemanager' &&  ka.getClipboard().type != 'filemanagerCut' ){
            deactivate(paste);
        }

        Object.each(selectedFiles, function( myfile ){
            
            if( myfile.writeaccess != true || this._krynFolders.indexOf( myfile.path ) >= 0 || this._modules.indexOf(myfile.path) >= 0){
                //no writeaccess
                deactivate(cut);
                deactivate(remove);
                deactivate(rename);
                deactivate(newversion);
            }
            
        }.bind(this));
        
        if( this.currentFolderFile.writeaccess != true ){
            deactivate(paste);
        }
        
        var pos = this.win.border.getPosition( document.body );

        this.context.setStyles({
            left: (parseInt(pEvent.client.x)+4-pos.x)+'px',
            top: (parseInt(pEvent.client.y)+4-pos.y)+'px'
        });

    },
    
    duplicate: function( pFile ){

        var newName = pFile.name;
        var t = newName.split('.');
        if( t[1] ){
            newName = t[0]+'-'+_('duplication')+'.'+t[1];
        }
        
        this.win._prompt(_('New name')+': ', newName, function(name){
            if( !name) return;
            new Request.JSON({url: _path+'admin/files/duplicateFile/', onComplete: function(res){
                this.reload();
            }.bind(this)}).post({path: pFile.path, newname: name});
        }.bind(this));
        
    },
    
    newversion: function( pFile ){
        
        new Request.JSON({url: _path+'admin/files/addVersion/', onComplete: function(res){
            ka._helpsystem.newBubble(_('New version created'), pFile.path, 3000 );  
        }.bind(this)}).post({path: pFile.path});

    },
    
    copy: function(){
        var title = '';
        
        var selectedFiles = this.getSelectedFiles();
        
        if( Object.getLength(selectedFiles) > 1 ){
            title = _('%d files copied').replace('%d', Object.getLength(selectedFiles) );
        } else {
            Object.each(selectedFiles, function(item){
                title = _('%s files copied').replace('%s', item.name.substr(0,25)+((item.name.length>25)?'...':'') );
            });
        }
        ka.setClipboard( title, 'filemanager', selectedFiles );
    },

    cut: function(){
    
        var selectedFiles = this.getSelectedFiles();

        if( Object.getLength(selectedFiles) > 1 ){
            title = _('%d files cut').replace('%d', Object.getLength(selectedFiles));
        } else {
            Object.each(selectedFiles, function(item){
                title = _('%s file cut').replace('%s', item.name.substr(0,25)+((item.name.length>25)?'...':'') );
            });
        }
        ka.setClipboard( title, 'filemanagerCut', selectedFiles );

    },

    deselectAll: function(){
    
        this.fileContainer.getElements('.admin-files-item-selected').removeClass('admin-files-item-selected');

    },
    
    startSearch: function(){
        if( this._searchTimer )
            $clear( this._searchTimer );
    
        if( this.searchInput.value == "" ){
            this.closeSearch();
        } else {
            this._searchTimer = this._search.delay(300, this, this.searchInput.value);
        }
    
    },
    
    _search: function( pQ ){
        
        if( !this.searchPane ){
            this.searchPane = new Element('div', {
                style: 'position: absolute; padding: 5px; top: 0px; right: 0px; bottom: 0px; width: 250px; border-left: 2px solid silver; background-color: #ddd;',
                styles: {
                    opacity: 0.95
                }
            }).inject( this.win.content );
            
            this.searchPaneTitle = new Element('div', {
                style: 'position: absolute; top: 0px; left: 0px; right: 0px; height: 25px; line-height: 25px; font-weight: bold; padding-left: 5px; color: gray;border-bottom: 1px solid silver; background-color: #e4e4e4;'
            }).inject( this.searchPane );
            
            searchPaneCloser = new Element('div', {
                style: 'position: absolute; top: 3px; right: 3px; font-weight: bold;',
                'class': 'kwindow-win-titleBarIcon kwindow-win-titleBarIcon-close'
            })
            .addEvent('click', function(){
                this.closeSearch();
            }.bind(this))
            .inject( this.searchPane );
            
            this.searchPaneContent = new Element('div',{
                style: 'position: absolute; overflow: auto; top: 0px; left: 0px; right: 0px; top: 26px; bottom: 0px;'
            }).inject( this.searchPane );
        }
            
        this.searchPaneContent.set('html', '<div style="text-align: center; padding-top: 25px;">'+
        '<img src="'+_path+'inc/template/admin/images/ka-tooltip-loading.gif" /><br />'+
        _('Searching ...')+
        '</div>');

        if( this.lastqrq )
            this.lastqrq.cancel();
        
        this.searchPaneTitle.set('html', _('Searching ...'));
        this.lastqrq = new Request.JSON({url: _path+'admin/files/search', noCache: 1, onComplete: function(res){
            
            this.showSearchEntries( res );
            
        }.bind(this)}).post({q: pQ, path: this.current}); 
        
    },
    
    showSearchEntries: function( pResult ){
    
        this.searchPaneContent.empty();
        this.searchPaneTitle.set('html', _('Results'));
        
        if( $type( pResult ) == 'array' && pResult.length > 0 ){
            pResult.each(function(item){
            
                var a = new Element('a', {
                    text: item.name,
                    href: 'javascript: ;',
                    style: 'display: block; text-decoration: none; font-weight: bold; padding: 2px; cursor: pointer;'
                }).inject( this.searchPaneContent );
                
                a.addEvent('click', function(){
                    this.loadPath( item.path );
                }.bind(this));
                
                
                new Element('div', {
                    text: item.path.replace(/inc\/template\//g, ''),
                    style: 'padding-left: 5px; color: #aaa; font-weight: normal;'
                }).inject( a );
            
            
            }.bind( this ));
        } else {
            this.searchPaneContent.set('html', _('No files found.'));
        }
    
    },

    
    closeSearch: function(){
        if( this.lastqrq )
            this.lastqrq.cancel();
        
        if( this.searchPane ){
            this.searchPane.destroy();
            this.searchPane = null;
        }
    }
});
