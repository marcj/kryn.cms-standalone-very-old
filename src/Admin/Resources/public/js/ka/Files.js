ka.Files = new Class({

    Binds: ['updateStatusBar'],
    Implements: [Options, Events],

    historyIndex: 0,
    history: {},

    current: '',

    _modules: [],
    isFirstLoad: true,

    __images: ['.jpg', '.jpeg', '.gif', '.png', '.bmp'],
    __ext: ['.css', '.tpl', '.js', '.html', '.htm'],
    _krynFolders: ['/kryn/', '/css/', '/images/', '/js/', '/admin/'],

    uploadTrs: {},
    uploadFilesCount: 0,
    uploadFileNames: {},
    fileUploadSpeedLastCheck: 0,
    fileUploadedSpeedLastLoadedBytes: 0,
    fileUploadedLoadedBytes: 0,
    fileUploadSpeedLastByteSpeed: 0,
    fileUploadSpeedInterval: false,

    html5UploadXhr: {},


    options: {

        useWindowHeader: false, //uses the kwindow instance to add smallTabButtons etc

        search: true,
        path: '/',
        withSidebar: false,

        onlyLocal: false, //only local files are selectable. So exludes all magic folders
        returnPath: false, //return the path instead of the object_id (like in version <= 0.9)

        selection: true,
        /* if selection is false, all options below will be ignored */
        selectionValue: false, //not useful, use setValue() instead
        selectionOnlyFiles: false,
        selectionOnlyFolders: false,
        multi: false
    },

    rootFile: {},
    path2File: {},

    sidebarFiles: {},

    container: false,
    win: false,

    initialize: function (pContainer, pOptions, pWindowApi, pObjectKey) {
        this.win = pWindowApi;
        this.container = pContainer;

        this.setOptions(pOptions);

        this._createLayout();
        this.loadModules();

        this.win.border.addEvent('click', function () {
            if (this.context) {
                this.context.destroy();
            }
        }.bind(this));

        this.title = this.win.getTitle();
        this.initHotkeys();

        this.win.addEvent('close', function () {

            if (this.previewDiv) {
                this.previewDiv.destroy();
            }

            this.cancelUploads();
        }.bind(this));

        this.loadRoot();

        this.addEvent('select', this.updateStatusBar);
        this.addEvent('deselect', this.updateStatusBar);
    },

    loadRoot: function(){

        new Request.JSON({url: _pathAdmin + 'admin/file/single', noCache: 1, onComplete: function (pResponse) {

            if (!pResponse || !pResponse.data || pResponse.error){
                this.win._alert(t('Access denied.'), function(){
                    this.win.close();
                }.bind(this))
            } else {
                this.rootFile = pResponse.data;
                this.path2File['/'] = pResponse.data;
                if (this.options.selectionValue) {
                    this.loadPath(this.options.selectionValue);
                } else {
                    this.loadPath(this.options.path);
                }
            }
        }.bind(this)}).get({path: '/'});
    },

    initHotkeys: function () {

        this.win.addHotkey('x', true, false, this.cut.bind(this));
        this.win.addHotkey('c', true, false, this.copy.bind(this));
        this.win.addHotkey('v', true, false, this.paste.bind(this));
        this.win.addHotkey('delete', false, false, this.remove.bind(this));
        this.win.addHotkey('space', false, false, this.preview.bind(this));

    },

    setTitle: function () {

        var folder = this.current;
        if (folder.substr(0, 1) != '/') {
            folder = '/' + folder;
        }

        this.win.setTitle(folder);
    },

    recoverSWFUpload: function () {
        this.buttonId = this.win.id + '_' + Math.ceil(Math.random() * 100);
        this.uploadBtn.set('html', '<span id="' + this.buttonId + '"></span>');
        this.initSWFUpload();
    },

    minimizeUpload: function () {


    },

    newFileUpload: function (pFile) {

        if (!this.fileUploadDialog) {

            this.fileUploadDialog = this.win.newDialog('', true);

            this.fileUploadDialog.setStyles({
                height: '60%',
                width: '80%'
            });
            this.fileUploadDialog.center();

            this.fileUploadCancelBtn = new ka.Button(t('Cancel')).addEvent('click', this.cancelUploads.bind(this)).inject(this.fileUploadDialog.bottom);

            this.fileUploadMinimizeBtn = new ka.Button(t('Minimize')).addEvent('click', this.minimizeUpload.bind(this)).inject(this.fileUploadDialog.bottom);

            var table = new Element('table', {style: 'width: 100%;', 'class': 'admin-file-uploadtable'}).inject(this.fileUploadDialog.content);
            this.fileUploadTBody = new Element('tbody').inject(table);


            this.fileUploadDialogProgress = new ka.Progress();
            document.id(this.fileUploadDialogProgress).inject(this.fileUploadDialog.bottom);
            document.id(this.fileUploadDialogProgress).setStyle('width', 132);
            document.id(this.fileUploadDialogProgress).setStyle('position', 'absolute');
            document.id(this.fileUploadDialogProgress).setStyle('top', 4);
            document.id(this.fileUploadDialogProgress).setStyle('left', 9);

            this.fileUploadDialogAll = new Element('div', {
                style: 'position: absolute; left: 168px; top: 9px; color: gray;'
            }).inject(this.fileUploadDialog.bottom);

            this.fileUploadDialogAllText = new Element('span').inject(this.fileUploadDialogAll);
            this.fileUploadDialogAllSpeed = new Element('span').inject(this.fileUploadDialogAll);

        }

        this.fileUploadMinimizeBtn.show();
        this.fileUploadCancelBtn.setText(t('Cancel'));

        this.uploadFilesCount++;

        var tr = new Element('tr').inject(this.fileUploadTBody);

        var td = new Element('td', {
            width: 20,
            style: 'color: gray',
            text: '#' + this.uploadFilesCount
        }).inject(tr);

        tr.name = new Element('td', {
            text: pFile.name
        }).inject(tr);

        var td = new Element('td', {
            width: 60,
            style: 'text-align: center; color: gray;',
            text: ka.bytesToSize(pFile.size)
        }).inject(tr);

        tr.status = new Element('td', {
            text: t('Pending ...'),
            width: 150,
            style: 'text-align: center;'
        }).inject(tr);

        var td = new Element('td', {
            width: 150
        }).inject(tr);

        tr.progress = new ka.Progress();
        document.id(tr.progress).inject(td);
        document.id(tr.progress).setStyle('width', 132);

        tr.deleteTd = new Element('td', {
            width: 20
        }).inject(tr);

        new Element('img', {
            src: _path + 'bundles/admin/images/icons/delete.png',
            style: 'cursor: pointer;',
            title: _('Cancel upload')
        }).addEvent('click', function () {
            tr.canceled = true;
            ka.uploads[this.win.id].cancelUpload(pFile.id);
        }.bind(this)).inject(tr.deleteTd);

        this.uploadTrs[ pFile.id ] = tr;
        this.uploadTrs[ pFile.id ].file = pFile;

        if (pFile.html5) {

            if (!pFile.post) pFile.post = {};

            if (!pFile.post.path) {
                pFile.post.path = this.current;
            }

        } else {
            ka.uploads[this.win.id].addFileParam(pFile.id, 'path', this.current);
        }

        if (ka.settings.upload_max_filesize && ka.settings.upload_max_filesize < pFile.size) {

            this.uploadError(pFile);

        } else {

            if (pFile.html5) {
                this.fileUploadCheck(this.html5FileUploads[ pFile.id ]);
            } else {
                this.fileUploadCheck(ka.uploads[this.win.id].getFile(pFile.id));
            }
        }

        this.uploadAllProgress();

    },

    fileUploadCheck: function (pFile) {

        var name = pFile.name;

        this.uploadTrs[ pFile.id ].status.set('html', ('Pending ...'));

        if (this.uploadTrs[ pFile.id ].rename) {
            this.uploadTrs[ pFile.id ].rename.destroy();
            delete this.uploadTrs[ pFile.id ].rename;
        }

        if (pFile.post && pFile.post.name && name != pFile.post.name) {

            name = pFile.post.name;

            this.uploadTrs[ pFile.id ].rename = new Element('div', {
                style: 'color: gray; padding-top: 4px;',
                text: '-> ' + name
            }).inject(this.uploadTrs[ pFile.id ].name);
        }

        var overwrite = (pFile.post.overwrite == 1) ? 1 : 0;

        new Request.JSON({url: _pathAdmin + 'admin/file/upload/prepare', noCache: 1, onComplete: function (pResponse) {

            var res = pResponse.data;

            if (res.renamed) {
                if (this.uploadTrs[ pFile.id ].rename) {
                    this.uploadTrs[ pFile.id ].rename.destroy();
                }
                this.uploadTrs[ pFile.id ].rename = new Element('div', {
                    style: 'color: gray; padding-top: 4px;',
                    text: '-> ' + res.name
                }).inject(this.uploadTrs[ pFile.id ].name);

                if (pFile.html5) {
                    this.html5FileUploads[ pFile.id ].post.name = res.name;
                } else {
                    ka.uploads[this.win.id].addFileParam(pFile.id, 'name', res);
                }
            }

            if (res.exist) {
                this.uploadTrs[ pFile.id ].status.set('html', '<div style="color: red">' + _('Filename already exists') + '</div>');

                this.uploadTrs[ pFile.id ].needAction = true;

                new ka.Button(t('Rename')).addEvent('click', function () {

                    this.win._prompt(t('New filename'), name, function (res) {

                        if (res) {

                            this.uploadTrs[ pFile.id ].needAction = false;

                            if (pFile.html5) {
                                this.html5FileUploads[ pFile.id ].post.name = res;
                                this.fileUploadCheck(this.html5FileUploads[ pFile.id ]);
                            } else {
                                ka.uploads[this.win.id].addFileParam(pFile.id, 'name', res);
                                this.fileUploadCheck(ka.uploads[this.win.id].getFile(pFile.id));
                            }
                        }

                    }.bind(this));

                }.bind(this)).inject(this.uploadTrs[ pFile.id ].status);


                new ka.Button(_('Overwrite')).addEvent('click', function () {

                    this.uploadTrs[ pFile.id ].needAction = false;

                    if (pFile.html5) {
                        this.html5FileUploads[ pFile.id ].post.overwrite = 1;
                        this.fileUploadCheck(this.html5FileUploads[ pFile.id ]);
                    } else {
                        ka.uploads[this.win.id].addFileParam(pFile.id, 'overwrite', '1');
                        this.fileUploadCheck(ka.uploads[this.win.id].getFile(pFile.id));
                    }

                }.bind(this))

                    .inject(this.uploadTrs[ pFile.id ].status);

                this.uploadCheckOverwriteAll();

            } else {

                if (pFile.html5) {
                    this.startHtml5Upload(pFile.id);
                } else {
                    ka.uploads[this.win.id].startUpload(pFile.id);
                }

            }

        }.bind(this)}).post({path: pFile.post.path, name: name, overwrite: overwrite });

    },

    uploadCheckOverwriteAll: function () {

        var needButton = false;
        var countWhichNeedsAction = 0;
        Object.each(this.uploadTrs, function (tr, id) {
            if (tr.file && tr.needAction == true) {
                countWhichNeedsAction++;
            }
        }.bind(this));

        if (countWhichNeedsAction > 1) {
            if (!this.uploadOverwriteAllButton) {
                this.uploadOverwriteAllButton = new ka.Button(_('Overwrite all')).addEvent('click', function () {

                    Object.each(this.uploadTrs, function (tr, id) {

                        tr.needAction = false;

                        if (tr.file.html5) {
                            this.html5FileUploads[ tr.file.id ].post.overwrite = 1;
                            this.fileUploadCheck(this.html5FileUploads[  tr.file.id ]);
                        } else {
                            ka.uploads[this.win.id].addFileParam(tr.file.id, 'overwrite', '1');
                            this.fileUploadCheck(ka.uploads[this.win.id].getFile(tr.file.id));
                        }

                    }.bind(this));

                    document.id(this.uploadOverwriteAllButton).destroy();
                    delete this.uploadOverwriteAllButton;

                }.bind(this)).inject(this.fileUploadDialog.bottom, 'top');
            }
        }

    },

    uploadNext: function () {

        var found = false;
        Object.each(this.uploadTrs, function (file, id) {
            if (!found && file && !file.needAction && !file.complete && !file.error) {
                found = file;
            }
        }.bind(this));

        if (found) {

            if (found.file.html5) {
                this.startHtml5Upload(found.file.id);
            } else {
                ka.uploads[this.win.id].startUpload(found.file.id);
            }

        }

    },

    uploadAllProgress: function () {

        var count = 0;
        var loaded = 0;
        var all = 0;
        var done = 0;
        var failed = 0;

        Object.each(this.uploadTrs, function (tr, id) {

            if (!tr.canceled) {
                count++;
            }

            if (tr.loaded && !tr.canceled) {
                loaded += tr.loaded;
            }

            if (!tr.error && !tr.canceled) {
                all += tr.file.size;
            }

            if (tr.complete == true) {
                done++;
            }

            if (tr.error == true) {
                failed++;
            }

        });

        this.fileUploadDialogAllText.set('text', _('%s done').replace('%s', done + '/' + count) + '.');

        this.fileUploadedTotalBytes = all;
        this.fileUploadedLoadedBytes = loaded;
        this.fileUploadCalcSpeed();

        var percent = Math.ceil((loaded / all) * 100);
        if (done == count) {
            percent = 100;
        }
        this.fileUploadDialogProgress.setValue(percent);

        if (failed == 0 && all == loaded) {
            if (!this.fileUploadCloseInfo) {
                this.fileUploadCloseInfo = new Element('span', {
                    text: t('This dialog closes in few seconds'),
                    style: 'padding-right: 15px; color: gray;'
                }).inject(document.id(this.fileUploadCancelBtn), 'before');
                (function () {
                    this.cancelUploads();
                }.bind(this)).delay(500);
            }
        } else if (all == loaded) {
            this.fileUploadCancelBtn.setText(t('Close'));
        }

    },

    fileUploadCalcSpeed: function (pForce) {

        if (this.fileUploadSpeedInterval && !pForce) return;

        var speed = ' -- KB/s, ' + _('%s minutes left').replace('%s', '--:--');
        var again = false;

        if (this.fileUploadSpeedLastCheck == 0) {
            this.fileUploadSpeedLastCheck = (new Date()).getTime() - 1000;
        }

        var timeDiff = (new Date()).getTime() - this.fileUploadSpeedLastCheck;
        var bytesDiff = this.fileUploadedLoadedBytes - this.fileUploadedSpeedLastLoadedBytes;

        var d = timeDiff / 1000;

        var byteSpeed = bytesDiff / d;

        if (byteSpeed > 0) {
            this.fileUploadSpeedLastByteSpeed = byteSpeed;
        }

        var residualBytes = this.fileUploadedTotalBytes - this.fileUploadedLoadedBytes;
        var time = '<span style="color: green;">' + _('Done') + '</span>';
        if (residualBytes > 0) {

            var timeLeftSeconds = residualBytes / byteSpeed;
            var timeLeft = (timeLeftSeconds / 60).toFixed(2);

            time = _('%s minutes left').replace('%s', timeLeft);
        } else {
            //done
            clearInterval(this.fileUploadSpeedInterval);
            this.fileUploadMinimizeBtn.hide();
        }

        if (this.fileUploadSpeedLastByteSpeed == 0) {
            speed = ' -- KB/s';
        } else {
            speed = ' ' + ka.bytesToSize(this.fileUploadSpeedLastByteSpeed) + '/s, ' + time;
        }

        this.fileUploadDialogAllSpeed.set('html', speed);

        this.fileUploadSpeedLastCheck = (new Date()).getTime();

        this.fileUploadedSpeedLastLoadedBytes = this.fileUploadedLoadedBytes;

        if (!this.fileUploadSpeedInterval) {
            this.fileUploadSpeedInterval = this.fileUploadCalcSpeed.periodical(500, this, true);
        }
    },

    uploadProgress: function (pFile, pBytesCompleted, pBytesTotal) {

        var percent = Math.ceil((pBytesCompleted / pBytesTotal) * 100);
        this.uploadTrs[ pFile.id ].progress.setValue(percent);
        this.uploadTrs[ pFile.id ].loaded = pBytesCompleted;

        this.uploadAllProgress();
    },

    uploadStart: function (pFile) {

        this.uploadTrs[ pFile.id ].status.set('html', t('Uploading ...'));

    },

    uploadComplete: function (pFile) {

        if (!this.uploadTrs[ pFile.id ]) return;

        this.uploadTrs[ pFile.id ].status.set('html', '<span style="color: green">' + _('Complete') + '</span>');
        this.uploadTrs[ pFile.id ].progress.setValue(100);

        this.uploadTrs[ pFile.id ].complete = true;
        this.uploadTrs[ pFile.id ].loaded = pFile.size;

        this.uploadTrs[ pFile.id ].deleteTd.destroy();

        this.uploadAllProgress();

        if (this && this.reload) {
            this.reload();
        }

        this.uploadNext();

    },

    uploadError: function (pFile) {

        if (!pFile) return;

        if (!this.uploadTrs[ pFile.id ]) return;

        var xhr = this.html5UploadXhr[ pFile.id ];

        this.uploadTrs[ pFile.id ].deleteTd.destroy();
        if (xhr) xhr.abort();

        if (ka.settings.upload_max_filesize && ka.settings.upload_max_filesize < pFile.size) {
            this.uploadTrs[ pFile.id ].status.set('html', '<span style="color: red">' + t('File size limit exceeded') + '</span>');
            new Element('img', {
                style: 'position: relative; top: 2px; left: 2px;',
                src: _path + 'bundles/admin/images/icons/error.png',
                title: t('The file size exceeds the limit allows by upload_max_filesize or post_max_size on your server. Please contact the administrator.')
            }).inject(this.uploadTrs[ pFile.id ].status);
        } else {
            if (this.uploadTrs[ pFile.id ].canceled) {
                this.uploadTrs[ pFile.id ].status.set('html', '<span style="color: red">' + t('Canceled') + '</span>');
            } else {
                var text =  t('Unknown error');
                if (xhr){
                    try {
                        switch (xhr.status){
                            case 413:
                                text = t('413: Request Entity Too Large');
                                break;
                        }
                    } catch(e){

                    }
                }
                this.uploadTrs[ pFile.id ].status.set('html', '<span style="color: red">' + text + '</span>');

            }
        }

        this.uploadTrs[ pFile.id ].error = true;

        this.uploadAllProgress();

        this.uploadNext();

    },

    clearUploadVars: function () {

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

        if (this.uploadOverwriteAllButton) {
            this.uploadOverwriteAllButton.destroy();
        }

        delete this.uploadOverwriteAllButton;
    },

    cancelUploads: function () {

        try {
            //flash calls are sometimes a bit buggy.

            Object.each(this.uploadTrs, function (tr, id) {
                if (!tr.complete && tr.file) {
                    if (tr.file.html5) {
                        if (this.html5UploadXhr[ tr.file.id ]) {
                            this.html5UploadXhr[ tr.file.id ].abort();
                        }
                    } else {
                        ka.uploads[this.win.id].cancelUpload(id);
                    }
                }
            }.bind(this))
        } catch (e) {
            logger(e);
        }

        if (this.fileUploadDialog) {
            this.fileUploadDialog.close();
        }

        if (this.fileUploadSpeedInterval) {
            clearInterval(this.fileUploadSpeedInterval);
        }

        this.clearUploadVars();

    },

    initSWFUpload: function () {

        ka.uploads[this.win.id] = new SWFUpload({
            upload_url: _path + "admin/file/upload/?" + window._session.tokenid + "=" + window._session.sessionid,
            file_post_name: "file",
            flash_url: _path + "admin/swfupload.swf",
            file_upload_limit: "500",
            file_queue_limit: "0",

            file_queued_handler: this.newFileUpload.bind(this),
            upload_progress_handler: this.uploadProgress.bind(this),
            upload_start_handler: this.uploadStart.bind(this),
            upload_success_handler: this.uploadComplete.bind(this),
            upload_error_handler: this.uploadError.bind(this),

            button_placeholder_id: this.buttonId,
            button_width: 26,
            button_height: 20,
            button_text: '<span class="button"></span>',
            button_text_top_padding: 0,
            button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
            button_cursor: SWFUpload.CURSOR.HAND
        });
    },

    loadModules: function () {
        Object.each(ka.settings.configs, function (config, ext) {
            this._modules.include('/'+ext+'/');
        }.bind(this));
    },

    newUploadBtn: function () {

        this.uploadBtn = this.boxAction.addButton(t('Upload'), '#icon-upload-7');

        if (!window.FormData) {
            this.uploadBtn.addEvent('mousedown', function (e) {
                e.stopPropagation();
            });
            this.buttonId = this.win.id + '_' + Math.ceil(Math.random() * 100);
            this.uploadBtn.set('html', '<span id="' + this.buttonId + '"></span>');
            this.initSWFUpload();
        } else {

            this.uploadFileChooser = new Element('input', {
                type: 'file',
                multiple: true,
                style: 'position: absolute; left: -3000px; top: -9999px'
            }).inject(this.container);

            this.uploadBtn.addEventListener("click", function (e) {
                this.uploadFileChooser.click();
                e.preventDefault();
            }.bind(this), false);

            this.uploadFileChooser.addEventListener("change", function (e) {
                this.checkFileDrop();
            }.bind(this), false);

        }
    },

    _createLayout: function() {

        if (!this.options.useWindowHeader){

            this.header = new Element('div', {
                style: 'position: absolute; top: 6px; left: 0px; height: 50px; right: 0px;'
            }).inject(this.container);

        } else {
            this.header = this.win.titleGroups;
            this.win.extendHead();
            this.win.border.addClass('ka-window-extend-head-files');
        }

        this.wrapper = new Element('div', {
            'class': 'ka-Files-wrapper'
        }).inject(this.container);

        this.headerLayout = new ka.Layout(this.wrapper, {
            fixed: false,
            layout: [{
                columns: [50, null, 150]
            }]
        });

        document.id(this.headerLayout).addClass('ka-Files-header');

        var actionsContainer = this.headerLayout.getCell(1, 1);

        actionsContainer.setStyle('white-space',  'nowrap');

        var boxNavi = new ka.ButtonGroup(actionsContainer, {onlyIcons: true});

        var toLeft = new Element('img', {
            src: _path + 'bundles/admin/images/admin-files-toLeft.png'
        });
        boxNavi.addButton(t('Back'),'#icon-arrow-left-15', function () {
            this.goHistory('left');
        }.bind(this));

        boxNavi.addButton(t('Forward'), '#icon-arrow-right-15', function () {
            this.goHistory('right');
        }.bind(this));

        this.upBtn = boxNavi.addButton(t('Up'), '#icon-arrow-up-14', this.up.bind(this));
        this.upBtn.fileObj = this;

        boxNavi.addButton(t('Refresh'), '#icon-reload-CW', this.reload.bind(this));

        var boxAction = new ka.ButtonGroup(actionsContainer);
        this.boxAction = boxAction;
        boxAction.addButton(t('File'), '#icon-file-add', this.newFile.bind(this));
        boxAction.addButton(t('Folder'), '#icon-folder-4', this.newFolder.bind(this));

        this.newUploadBtn();

        this.upBtn.addClass('admin-files-droppables');

        //view types
        var boxTypes = new ka.ButtonGroup(actionsContainer, {onlyIcons: true});
        this.typeButtons = new Hash();

        this.typeButtons['icon'] = boxTypes.addButton(t('Icon view'), '#icon-grid-2', this.setListType.bind(this, 'icon', null, null));

        this.typeButtons['miniatur'] = boxTypes.addButton(t('Image view'), '#icon-images', this.setListType.bind(this, 'miniatur', null, 70));

        this.typeButtons['detail'] = boxTypes.addButton(t('Detail view'), '#icon-list-4', this.setListType.bind(this, 'detail', null, null));

        this.typeButtons.each(function   (btn) {
            btn.store('oriClass', btn.get('class'));
        });

        //address
        var addressContainer = this.headerLayout.getCell(1, 2);

        this.address = new ka.Field({
            type: 'text',
            noWrapper: true
        }, addressContainer);

        this.address.getFieldObject().input.addEvent('mousedown', function(e){
            if (e.key == 'enter') {
                this.loadPath(this.address.getValue());
            }
        }.bind(this));

        var searchContainer = this.headerLayout.getCell(1, 3);

        this.search = new ka.Field({
            type: 'text',
            noWrapper: true
        }, searchContainer);
        document.id(this.search).setStyle('margin-left', '15px');

        this.search.getFieldObject().input.addEvent('mousedown', function(e){
            if (e.key == 'enter') {
                this.startSearch();
            }
        }.bind(this));

        new Element('img', {
            src: _path+ 'bundles/admin/images/icon-search-loupe.png',
            style: 'position: absolute; right: 12px; top: 8px;'
        }).inject(this.search, 'after');

        this.mainLayout = new ka.Layout(this.wrapper, {
            fixed: false,
            splitter: this.options.withSidebar ? [
                [1, 1, 'right']
            ] : [],
            layout: [{
                columns: this.options.withSidebar ? [250, null] : [null]
            }]
        });

        document.id(this.mainLayout).addClass('ka-Files-body');

        var fileContainerCell = this.mainLayout.getCell(1, 2);
        var sideBarCell = this.mainLayout.getCell(1, 1);
        if (!this.options.withSidebar) {
            fileContainerCell = this.mainLayout.getCell(1, 1);
        }

        document.id(fileContainerCell).addClass('admin-files-fileContainer-bg');

        this.fileContainer = new Element('div', {
            'class': 'admin-files-droppables admin-files-fileContainer ka-scrolling'
        }).addEvent('mousedown', function (pEvent) {
            this.checkMouseDown(pEvent);
        }.bind(this)).addEvent('mouseup', function (pEvent) {
            this.drag = false;

            if (this.lastDragTimer) {
                clearTimeout(this.lastDragTimer);
            }

            this.checkMouseClick(pEvent);
            this.closeSearch();

        }.bind(this)).addEvent('dblclick', function (pEvent) {
            this.checkMouseDblClick(pEvent);
            this.closeSearch();
        }.bind(this)).addEvent('mousemove', function (pEvent) {
            this.checkMouseMove(pEvent);
        }.bind(this))
        .inject(fileContainerCell);

        if (ka.mobile){
            this.fileContainer.addEvent('click', function (pEvent) {
                this.checkMouseDblClick(pEvent);
                this.closeSearch();
            }.bind(this))
        }

        this.fileContainer.addEvent('scroll', this.loadImagesInViewPort.bind(this));

        if (!this.options.useWindowHeader){
            this.fileContainer.setStyle('top', 35);
        }

        if (this.container.addEventListener) {
            this.container.addEventListener('dragover', this.checkFileDragOver.bind(this));
            this.container.addEventListener('dragleave', this.checkFileDragLeave.bind(this));
            this.container.addEventListener('drop', this.checkFileDrop.bind(this));
        }

        this.fileContainer.fileObj = this;

        if (this.options.withSidebar) {
            this.infos = new Element('div', {
                'class': 'admin-files-infos'
            }).inject(sideBarCell, 'top');

            this.mainLayout.getTable().setStyle('table-layout', 'fixed')
        }

        this.statusBar = new Element('div', {
            'class': 'admin-files-status-bar'
        }).inject(this.wrapper);

        this.loaderContainer = new Element('div', {
            'class': 'admin-files-status-bar-loader'
        }).inject(this.statusBar);

        this.loader = new ka.Loader(this.loaderContainer, {
        });

        this.statusBarSelected = new Element('span').inject(this.statusBar);

        this.setListType('icon', true); //TODO retrieve cookie
    },

    showLoader: function(pVisible){
        pVisible ? this.loader.show() : this.loader.hide();
    },

    updateStatusBar: function(){

        var selected = this.getSelectedFilesAsArray();
        var items = this.fileContainer.getElements('.admin-files-item');

        var text = '';

        if (selected.length > 0){
            text = tf('%d of %d selected.', selected.length, items.length);
        } else {
            text = tf('%d items.', items.length);
        }

        this.statusBarSelected.set('text', text);

    },


    setListType: function (pType, noReload, pSetIconZoom) {

        this.typeButtons.each(function(item){
            item.setPressed(false);
        });
        var b = this.typeButtons[pType];
        b.setPressed(true);

        this.listType = pType;

        if (this.listType == 'detail') {
            this.fileContainer.addClass('admin-files-fileContainer-details');
        } else {
            this.fileContainer.removeClass('admin-files-fileContainer-details');
        }

        if (!noReload) {
            this.reRender();
        }

        if (pSetIconZoom) {
            this.setIconZoom(pSetIconZoom);
        } else {
            this.setIconZoom();
        }
    },


    newFile: function () {

        if (this.currentFile.writeaccess == false) {
            this.win._alert(_('Access denied'));
            return;
        }

        this.win._prompt(_('File name'), '', function (name) {
            if (!name) return;
            new Request.JSON({url: _pathAdmin + 'admin/file', onComplete: function (res) {
                this.reload();
            }.bind(this)}).post({path: this.current+'/'+name});
        }.bind(this));
    },

    newFolder: function () {

        if (this.currentFile.writeAccess == false) {
            this.win.alert(t('Access denied'));
            return;
        }

        this.win._prompt(t('Folder name'), '', function(name){
            if (!name) return;
            new Request.JSON({url: _pathAdmin + 'admin/file/folder', onComplete: function (res) {
                this.reload();
            }.bind(this)}).post({path: this.current+'/'+name});
        }.bind(this));
    },

    rename: function (pFile) {
        this.win._prompt(_('Rename') + ': ', pFile.name, function(name){
            if (!name) return;
            this.move(this.current+pFile.name, this.current+name);
        }.bind(this));
    },

    move: function( pPath, pNewPath, pOverwrite ){

        new Request.JSON({url: _pathAdmin + 'admin/file/move', onComplete: function(res){
            if(res.file_exists == 1){
                this.win._confirm(_('The new filename already exists. Overwrite?'), function(answer){
                    if(answer) this.move(pPath, pNewPath, true);
                }.bind(this));
            } else {
                this.reload();
            }
        }.bind(this)}).post({path: pPath, newPath: pNewPath, overwrite: pOverwrite?1:0});
    },

    remove: function () {

        var selectedFiles = this.getSelectedFiles();

        if (!Object.getLength(selectedFiles) > 0) return;

        this.win._confirm(_('Really remove selected file/s?'), function (res) {
            if (!res) return;
            Object.each(selectedFiles, function (item) {

                new Request.JSON({url: _pathAdmin + 'admin/file', noCache: 1, onComplete: function (res) {
                    this.reload();
                }.bind(this)}).delete({path: item.path});

            }.bind(this));

        }.bind(this));
    },

    paste: function () {

        if (!ka.getClipboard().type == 'filemanager' && !ka.getClipboard().type == 'filemanagerCut') return;

        var files = [];

        var clipboard = ka.getClipboard('filemanager');
        var move = 0;

        if (ka.getClipboard().type == 'filemanagerCut') {
            clipboard = ka.getClipboard('filemanagerCut');
            move = 1;
        }

        if (clipboard) {
            Object.each(clipboard.value, function (file) {
                files.include(file.path);
            });
        }

        if (move == 1) {
            this.moveFiles(files, this.current);
        } else {
            this.copyFiles(files, this.current);
        }
    },

    moveFiles: function (pFilePaths, pTargetDirectory, pOverwrite, pCallback) {

        new Request.JSON({url: _pathAdmin + 'admin/file/paste', noCache: 1, onComplete: function (res) {
            if (res.exist) {
                this.win._confirm(_('One or more files already exist. Overwrite ?'), function (p) {

                    if (!p)return;
                    this.moveFiles(pFilePaths, pTargetDirectory, true);

                }.bind(this));
            } else {
                this.reload();
                if (pCallback) {
                    pCallback();
                }
            }
        }.bind(this)}).post({files: pFilePaths, path: pTargetDirectory, overwrite: pOverwrite, move: 1});

    },

    copyFiles: function (pFilePaths, pTargetDirectory, pOverwrite) {

        new Request.JSON({url: _pathAdmin + 'admin/file/paste', noCache: 1, onComplete: function (res) {
            if (res.exist) {
                this.win._confirm(_('One or more files already exist. Overwrite ?'), function (p) {
                    if (!p)return;
                    this.copyFiles(pFilePaths, pTargetDirectory, true);
                }.bind(this));
            } else {
                this.reload();
            }
        }.bind(this)}).post({files: pFilePaths, path: pTargetDirectory, overwrite: pOverwrite});

    },

    loadPath: function (pPath, pCallback) {

        if (pPath.substr(0, 7) == '/trash/' && pPath.length >= 7) {
            this.win._alert(t('You cannot open a file in the trash folder. To view this file, press right click and choose recover.'));
            return;
        }

        if (this.options.selection && (pPath.substr(0, 7) == '/trash/' || pPath == '/trash')){
            return false;
        }

        if (this.history[ this.historyIndex ] != pPath) {
            this.load(pPath, pCallback);
        }
    },

    getUpPath: function () {
        if (this.current != '/' && this.current.substr(this.current.length-1) == '/')
            this.current = this.current.substr(0, this.current.length-1);
        var pos = this.current.substr(0, this.current.length - 1).lastIndexOf('/');
        return this.current.substr(0, pos + 1);
    },

    up: function () {
        if (this.current.length > 1) {
            this.loadPath(this.getUpPath());
        }
    },

    goHistory: function (pWay) {
        if (pWay == 'left') {
            this.historyIndex--;
            if (!this.history[ this.historyIndex ]) {
                this.historyIndex++;
            }
        } else {
            this.historyIndex++;
            if (!this.history[ this.historyIndex ]) {
                this.historyIndex--;
            }
        }

        var path = this.history[ this.historyIndex ];
        this.load(path);
    },

    reload: function () {
        this.load(this.current);
    },

    renderTree: function () {
        if (!this.sideTree) {
            this.sideTree = new ka.Field({
                label: t('Nodes'),
                type: 'tree',
                noWrapper: true,
                objectKey: 'Core\\File'
            }, this.infos);

            this.sideTree.addEvent('select', function(item) {
                this.loadPath(item.path);
            }.bind(this));
        }

        if (this.sideTree.select) {
            console.log('select: ', this.currentFile);
            this.sideTree.select(this.currentFile.id);
        }

        return;
    },

    newInfoItem: function (pFile) {

        if (pFile.type != 'dir') return;

        var icon = this.getIcon(pFile);

        var item = new Element('a', {
            text: pFile.name,
            'class': 'admin-files-droppables '+icon
        }).addEvent('mousedown', function (e) {
            e.stop()
        }).addEvent('click', this.loadPath.bind(this, pFile.path))
        .inject(this.infos);

        item.fileItem = pFile;
        item.fileObj = this;

        this.sidebarFiles[pFile.path] = item;
    },

    load: function (pPath, pCallback) {

        if (this.curRequest) {
            this.curRequest.cancel();
        }

        if (pPath != '/' && pPath.substr(pPath.length-1) == '/')
            pPath = pPath.substr(0, pPath.length-1);

        if (pPath.substr(0, 1) != '/')
            pPath = '/'+pPath;


        this.showLoader(true);
        this.currentFile = this.path2File[pPath];
        if (!this.currentFile) {

            //we entered a own path
            //check first what it is, and the continue;
            this.curRequest = new Request.JSON({url: _pathAdmin + 'admin/file/single', noCache: 1, onComplete: function (pResponse){

                this.showLoader(false);

                if (pResponse.error == 'AccessDeniedException'){
                    //todo, show access denied in a more beauty way.
                    this.win._alert(_('%s: Access denied').replace('%s', pPath));
                    return;
                }

                if (pResponse.error == 'FileNotFoundException'){
                    //todo, show access denied in a more beauty way.
                    this.win._alert(_('%s: file not found').replace('%s', pPath));
                    return;
                }

                this.currentFile = pResponse.data;
                this.path2File[pResponse.data.path] = this.currentFile;

                if (this.options.selection && (this.options.selectionValue == pPath || this.options.selectionValue == pPath.substr(1))) {
                    if (this.currentFile.path != '/'){
                        this.load(this.currentFile.path.substr(0, this.currentFile.path.lastIndexOf('/')));
                    }
                } else {
                    if (this.currentFile.type == 'dir'){
                        this.load(pPath);
                    } else if (this.currentFile.type == 'file') {
                        ka.wm.openWindow('admin/file/edit', null, null, {file: {path: pPath}});
                    }
                }

            }.bind(this)}).get({path: pPath});
            return;
        }

        this.curRequest = new Request.JSON({url: _pathAdmin + 'admin/file', noCache: 1, onComplete: function (pResponse) {

            this.showLoader(false);

            if (pResponse.error == 'AccessDeniedException'){
                //todo, show access denied in a more beauty way.
                this.win._alert(_('%s: Access denied').replace('%s', pPath));
                return;
            }

            if (pResponse.error == 'FileNotFoundException'){
                //todo, show access denied in a more beauty way.
                this.win._alert(_('%s: file not found').replace('%s', pPath));
                return;
            }

            if (pPath == '/trash' || pPath.substr(0,7) == '/trash/') {
                this.boxAction.hide();
            } else {
                if (this.currentFile.writeAccess == true) {
                    this.boxAction.show();
                } else {
                    this.boxAction.hide();
                }
            }

            this.historyIndex++;
            this.history[ this.historyIndex ] = pPath;

            this.current = pPath;

            this.isFirstLoad = false;

            this.setTitle();

            this.address.setValue(this.current);

            this.render(pResponse.data);

            if (this.options.withSidebar) {
                this.renderTree();
            }

            this.updateStatusBar();

            this.infos.getChildren().removeClass('admin-files-item-selected');
            this.fireDeselect();

            if (this.sidebarFiles[this.current]){
                this.sidebarFiles[this.current].addClass('admin-files-item-selected');
                this.fireSelect();
            }

            this.showLoader(false);

            this.upBtn.fileItem = {type: 'dir', path: this.getUpPath()};

            if (typeOf(pCallback) == 'function') {
                pCallback();
            }

            if (this.dragMove){
                this.dragMove.droppables = $$(this.dragMove.options.droppables);
                this.dragMove.positions = this.dragMove.droppables.map(function(el){
                    return el.getCoordinates();
                });
            }

        }.bind(this)}).get({ path: pPath });
    },

    reRender: function () {
        this.render(this.files);
    },

    render: function (pItems) {

        this.files = pItems;
        this.fileContainer.empty();

        var nfiles = [];
        //first folders, then files
        Object.each(this.files, function (f) {

            this.path2File[f.path] = f;

            if (f.type == 'dir') {
                nfiles.include(f);
            }
        }.bind(this));

        Object.each(this.files, function (f) {
            if (f.type != 'dir') {
                nfiles.include(f);
            }
        });
        this.files2View = nfiles;

        this.fileContainer.removeClass('admin-files-listtype-icon');
        this.fileContainer.removeClass('admin-files-listtype-miniatur');
        this.fileContainer.removeClass('admin-files-listtype-detail');

        this.fileContainer.addClass('admin-files-listtype-'+this.listType);

        if (this.listType == 'icon' || this.listType == 'miniatur') {
            this.renderIcons(this.files2View);
        }

        if (this.listType == 'image') {
            this.renderImage();
        }

        if (this.listType == 'detail') {
            this.renderDetail();
        }

        this.loadImagesInViewPort();

    },

    loadImagesInViewPort: function(){

        if (this.lastLoadImagesInViewPortTimer)
            clearTimeout(this.lastLoadImagesInViewPortTimer);

        this.lastLoadImagesInViewPortTimer = this._loadImagesInViewPort.delay(100, this);

    },

    _loadImagesInViewPort: function(){

        //var currentTop = this.fileContainer.getScroll();

        var children = this.fileContainer.getChildren('.admin-files-item');
        containerHeight = this.fileContainer.getSize().y;

        var position;

        Array.each(children, function(file){
            if (!file.readyToLoadImage) return;
            if (file.imageLoaded) return;

            position = file.getPosition(this.fileContainer);

            if (position.y > 0 && position.y < containerHeight){

                var image = _pathAdmin + 'admin/file/preview?' + Object.toQueryString({
                    path: file.readyToLoadImage.path,
                    mtime: file.readyToLoadImage.mtime,
                    width: file.imageContainer.getSize().x-20,
                    height: file.imageContainer.getSize().y-20
                });
                file.image.set('src', image);

                file.imageLoaded = true;

            }

        }.bind(this));

    },

    checkFileDragOver: function (pEvent) {
        var file;

        pEvent.stopPropagation();
        pEvent.preventDefault();

        if (!window.FormData) {
            return;
        }

        var item = pEvent.target;

        if (!item.hasClass('admin-files-item')) {
            item = item.getParent('.admin-files-item');
        }

        if (!item && pEvent.target.hasClass('admin-files-droppables')) {
            item = pEvent.target;
        }

        if (item) {
            file = item.fileItem;
        }

        if (file && file.type == 'dir' && file.path != '/trash' && file.path != '/' && !item.hasClass('admin-files-fileContainer') && file.writeaccess) {
            item.addClass('admin-files-item-selected');
            this.fireSelect();
        } else if (this.currentFile.writeaccess) {
            this.fileContainer.addClass('admin-files-fileContainer-selected');
        }
    },

    checkFileDragLeave: function (pEvent) {

        pEvent.stopPropagation();
        pEvent.preventDefault();

        var item = pEvent.target;

        if (!item.hasClass('admin-files-item')) {
            item = item.getParent('.admin-files-item');
        }
        if (!item && pEvent.target.hasClass('admin-files-droppables')) {
            item = pEvent.target;
        }

        if (item) {
            item.removeClass('admin-files-item-selected');
            this.fireDeselect();
        }

        this.fileContainer.removeClass('admin-files-fileContainer-selected');

    },

    checkFileDrop: function (pEvent) {
        var file;

        if (pEvent) {
            pEvent.stopPropagation();
            pEvent.preventDefault();
        }

        if (!window.FormData) {
            return;
        }

        this.fileContainer.removeClass('admin-files-fileContainer-selected');

        var files = (pEvent) ? pEvent.dataTransfer.files : this.uploadFileChooser.files;

        if (pEvent) {
            var item = pEvent.target;

            if (!item.hasClass('admin-files-item')) {
                item = item.getParent('.admin-files-item');
            }

            if (!item && pEvent.target.hasClass('admin-files-droppables')) {
                item = pEvent.target;
            }
        }

        if (!this.html5FileUploads) {
            this.html5FileUploads = {};
        }

        if (item) {
            file = item.fileItem;
            item.removeClass('admin-files-item-selected');
            this.fireDeselect();
        }

        if (file && (file.type != 'dir' || file.path == '/trash')) {
            return;
        }

        if (!file && this.current == '/trash') return;

        Array.each(files, function (chosenFile) {

            if (file) {
                chosenFile.post = {path: file.path};
            }

            chosenFile.html5 = true;
            chosenFile.id = 'HTML5_' + Object.getLength(this.html5FileUploads);

            this.html5FileUploads[ chosenFile.id ] = chosenFile;

            this.newFileUpload(this.html5FileUploads[ chosenFile.id ]);

        }.bind(this));

    },

    startHtml5Upload: function (pFileId) {

        var file = this.html5FileUploads[ pFileId ];

        var xhr = new XMLHttpRequest();

        this.html5UploadXhr[ pFileId ] = xhr;

        if (xhr.upload) {

            xhr.upload.addEventListener("progress", function (pEvent) {
                this.uploadProgress(file, pEvent.loaded, pEvent.total);
            }.bind(this), false);

            xhr.onreadystatechange = function (e) {
                if (xhr.readyState == 4) {
                    if (xhr.status == 200) {
                        this.uploadComplete(file);
                    } else if (xhr.status > 0){
                        this.uploadError(file);
                    }
                }
            }.bind(this);

            if (!file.post) file.post = {};

            this.uploadStart(file);

            file.post[window._session.tokenid] = window._session.sessionid;

            xhr.open("POST", _pathAdmin + "admin/file/upload?" + Object.toQueryString(file.post), true);

            var formData = new FormData();
            formData.append('file', file);
            xhr.send(formData);

        }

    },

    checkAutoScroll: function (pEvent) {

        if (this.fileContainer.getSize().y != this.fileContainer.getScrollSize().y) {
            var curPos = pEvent.page.y - this.fileContainer.getPosition(document.body).y;

            if (curPos < 20) {
                this.fileContainer.scrollTo(this.fileContainer.getScroll().x, this.fileContainer.getScroll().y - 10);
            } else {
                var sizeY = this.fileContainer.getSize().y;
                if (curPos > 0 && sizeY - curPos < 20) {
                    this.fileContainer.scrollTo(this.fileContainer.getScroll().x, this.fileContainer.getScroll().y + 10);
                }
            }
        }

    },

    startSelector: function (pEvent) {

        var offset = this.fileContainer.getPosition(document.body);
        var scroll = this.fileContainer.getScroll();
        this.selectorMaxSizePos = this.fileContainer.getScrollSize();

        if (document.activeElement && 'blur' in document.activeElement){
            document.activeElement.blur();
        }

        if (this.selectorDiv) {
            this.selectorDiv.destroy();
            delete this.selectorDiv;
        }

        this.selectorDiv = new Element('div', {
            'class': 'admin-files-selector',
            styles: {
                'top': pEvent.page.y - offset.y + scroll.y + 1,
                'left': pEvent.page.x - offset.x + 1,
                width: 1,
                height: 1
            }
        }).setStyle('opacity', 0.5).inject(this.fileContainer);

        this.selectorStartMousePos = {
            x: pEvent.page.x,
            y: pEvent.page.y
        };

        this.selectorStartPos = {
            x: pEvent.page.x - offset.x + 1,
            y: pEvent.page.y - offset.y + scroll.y + 1
        };

        var diffY, diffX, curPos, file;

        var items = this.fileContainer.getElements('.admin-files-item');

        Array.each(items, function (item) {
            item.pos = item.getPosition(this.fileContainer);
            item.pos.y += scroll.y;
            item.size = item.getSize();
        }.bind(this));

        this.nextMouseClickIsInvalid = true;

        this.selectorDrag = new Drag(this.selectorDiv, {
            style: false,
            preventDefault: false,
            stopPropagation: false,
            onDrag: function (pElement, pEvent) {

                scroll = this.fileContainer.getScroll();

                diffY = (pEvent.page.y - offset.y + scroll.y + 1) - this.selectorStartPos.y;
                diffX = pEvent.page.x - this.selectorStartMousePos.x;

                this.checkAutoScroll(pEvent);

                if (diffX < 0) {
                    diffX *= -1;
                    this.selectorDiv.setStyle('left', this.selectorStartPos.x - diffX);
                }
                if (pEvent.page.x > this.selectorStartMousePos.x) {
                    this.selectorDiv.setStyle('left', this.selectorStartPos.x);
                }
                if (diffY < 0) {
                    diffY *= -1;
                    this.selectorDiv.setStyle('top', this.selectorStartPos.y - diffY);
                }
                if (pEvent.page.y > this.selectorStartMousePos.y) {
                    this.selectorDiv.setStyle('top', this.selectorStartPos.y);
                }

                curPos = {
                    left: this.selectorDiv.getStyle('left').toInt(),
                    top: this.selectorDiv.getStyle('top').toInt()
                };

                if (diffX + curPos.left + 2 < this.selectorMaxSizePos.x) {
                    this.selectorDiv.setStyle('width', diffX);
                }

                if (diffY + curPos.top + 2 < this.selectorMaxSizePos.y) {
                    this.selectorDiv.setStyle('height', diffY);
                }

                curPos['width'] = this.selectorDiv.getStyle('width').toInt();
                curPos['height'] = this.selectorDiv.getStyle('height').toInt();

                Array.each(items, function (item) {

                    if ((item.pos.x + item.size.x) > curPos.left && item.pos.x < (curPos.left + curPos.width) && item.pos.y < (curPos.top + curPos.height) && (item.pos.y + item.size.y) > curPos.top) {

                        this.selectItem(item);

                    } else {
                        item.removeClass('admin-files-item-selected');
                        this.fireDeselect();
                    }

                }.bind(this));

                this.updateStatusBar();
            }.bind(this),

            onComplete: function () {
                this.nextMouseClickIsInvalid = false;

                if (this.selectorDiv) {
                    this.selectorDiv.destroy();
                    delete this.selectorDiv;
                }
            }.bind(this),

            onCancel: function () {

                this.nextMouseClickIsInvalid = false;

                if (this.selectorDiv) {
                    this.selectorDiv.destroy();
                }

                delete this.selectorDiv;
            }.bind(this)

        });

        this.selectorDrag.start(pEvent);

    },

    checkMouseDown: function (pEvent) {

        var item = pEvent.target, file;

        selection = window.getSelection();
        selection.removeAllRanges();

        (function () {
            selection = window.getSelection();
            selection.removeAllRanges();
        }).delay(40);


        if (!item.hasClass('admin-files-item')) {
            item = item.getParent('.admin-files-item');
        }

        if (!item) {
            item = pEvent.target.getParent('tr');
        }

        if (item) {
            file = item.fileItem;
            this.lastClickedItem = item;
        } else {
            delete this.lastClickedItem;
        }

        this.updatePreview();

        if (item) {

            if (file && !file.magic && file.path != '/trash' && file.path.substr(0,7) != '/trash/') {
                if (this._modules.indexOf(file.path+'/') >= 0) return;
                this.startDrag(pEvent, item);
            }

        } else if (!pEvent.rightClick) {

            this.lastClickedItem = pEvent.target;
            if (pEvent.target.hasClass('admin-files-fileContainer')) {

                if (pEvent.target.getSize().y < pEvent.target.getScrollSize().y && (pEvent.target.getPosition(document.body).x + pEvent.target.getSize().x) - pEvent.page.x < 20) {
                    //if we click on the scrollbar, ignore it
                    return;
                }
                this.deselect();
                this.startSelector(pEvent);
            }
        }

    },

    checkMouseDblClick: function (pEvent) {

        var item = pEvent.target;

        if (!item.hasClass('admin-files-item')) {
            item = item.getParent('.admin-files-item');
        }
        if (!item) {
            item = pEvent.target.getParent('tr');
        }

        if (!item) return;

        var file = item.fileItem;

        if (file) {
            if (file.type == 'file' && this.options.selection && !this.options.selectionOnlyFolders) {
                this.fireEvent('select', [file, item]);
                this.fireEvent('dblclick', [file, item]);
                this.fireEvent('instantSelect', [file, item]);
            } else {
                if (file.type == 'file'){
                    if (file.path.substr(0, 7) == '/trash/') {
                        this.win._alert(_('You cannot open a file in the trash folder. To view this file, press right click and choose recover.'));
                        return;
                    }
                    ka.wm.openWindow('admin/file/edit', null, null, {file: file});
                } else
                    this.loadPath(file.path);
            }
        }

    },

    checkMouseMove: function (pEvent) {

        if (!ka.inFileDragMode) return;

        if (!this.win.isInFront()) {
            this.win.toFront();
        }

    },

    checkMouseClick: function (pEvent) {

        if (this.nextMouseClickIsInvalid == true) {
            this.nextMouseClickIsInvalid = false;
            return;
        }

        if (!pEvent) return;

        if ((!pEvent.control && !pEvent.meta && !pEvent.shift ) && !pEvent.rightClick) {
            this.deselect();
        }

        if (!pEvent.target) return;

        var item = pEvent.target;

        if (!item.hasClass('admin-files-item')) {
            item = item.getParent('.admin-files-item');
        }

        if (!item) {
            item = pEvent.target.getParent('tr');
        }

        if (!item) {

            this.deselect();

            if (pEvent.rightClick) {
                this.openContext(this.currentFile, pEvent);
            }

            return;
        }

        if (pEvent.shift) {

            var allSelected = this.fileContainer.getElements('.admin-files-item-selected');
            var all = this.fileContainer.getElements('.admin-files-item');

            var firstPos = all.indexOf(allSelected[0]);
            //var lastPos = all.indexOf(allSelected[ allSelected.length-1 ]);

            var thisPos = all.indexOf(item);
            var tfile, i;


            if (thisPos > firstPos) {
                for (i = firstPos; i < thisPos; i++) {
                    if (all[i]) {
                        this.selectItem(all[i]);
                    }
                }
            } else {
                for (i = thisPos; i < firstPos; i++) {
                    if (all[i]) {
                        this.selectItem(all[i]);
                    }
                }
            }

        }

        var file = item.fileItem;

        if (!item.hasClass('admin-files-item-selected')) {

            this.selectItem(item);
        } else if (pEvent.control || pEvent.meta) {
            item.removeClass('admin-files-item-selected');
            this.fireDeselect();
        }

        if (pEvent.rightClick && file) {
            this.openContext(file, pEvent);
        }

    },

    selectItem: function (pItem) {

        var file = pItem.fileItem;
        if (file && file.path != '/trash') {

            if (this.options.onlyLocal == 1 && file.magic) return false;

            if (this.options.selection) {
                if (this.options.selectionOnlyFiles && file.type == 'dir') return;
                if (this.options.selectionOnlyFolders && file.type == 'file') return;

                if (!this.options.multi && this.getSelectedCount() == 1) return;
            }

            pItem.addClass('admin-files-item-selected');
            this.fireSelect();
        }

    },

    getSelectedCount: function () {
        return this.fileContainer.getElements('.admin-files-item-selected').length;
    },

    getSelectedFiles: function () {
        var res = {};

        this.fileContainer.getElements('.admin-files-item-selected').each(function (item) {
            var file = item.fileItem;
            res[ file.path ] = file;
        });

        return res;
    },

    getSelectedFilesAsArray: function () {
        var res = [];

        this.fileContainer.getElements('.admin-files-item-selected').each(function (item) {
            var file = item.fileItem;
            res.include(file);
        });

        return res;
    },

    getSelectedItemsAsArray: function () {

        return this.fileContainer.getElements('.admin-files-item-selected');
    },

    getSelectedItems: function () {
        var res = {};

        this.fileContainer.getElements('.admin-files-item-selected').each(function (item) {
            var file = item.fileItem;
            res[ file.path ] = item;
        });

        return res;
    },

    closePreview: function(){

        if (!this.lastPreviewedItem) return;

        var img      = this.lastPreviewedItem.getElement('img') || this.lastPreviewedItem.getElement('div');
        var position = img.getPosition(this.container);
        var size     = img.getSize();

        var onComplete = function(){
            this.previewDiv.destroy();
            delete this.previewDiv;
            this.previewMorph.removeEvents('complete');
        }.bind(this);

        this.previewMorph.addEvent('complete', onComplete);

        this.previewMorph.start({
            opacity: 0,
            width: size.x,
            height: size.y,
            top: position.y,
            left: position.x
        });

        delete this.lastPreviewedItem;
    },

    updatePreview: function(){

        if (!this.lastClickedItem && this.lastPreviewedItem) {
            this.closePreview();
            return;
        }
        if (!this.previewDiv) return;

        this.lastPreviewedItem = this.lastClickedItem;

        var file = this.lastClickedItem.fileItem, image;
        var img;

        var size = this.previewDivSize;
        image = _pathAdmin + 'admin/file/preview?' + Object.toQueryString({
            path: file.path,
            width: size.x,
            height: size.y,
            mtime:file.mtime
        });

        if (this.previewLoader){
            this.previewLoader.destroy();
            delete this.previewLoader;
        }

        if (this.lastPreviewImage){
            this.lastPreviewImage.destroy();
            delete this.lastPreviewImage;
        }

        this.previewLoader = new ka.Loader(this.previewDiv, {
            big: true
        });

        logger('newLoader');

        Asset.image(image, {
            onLoad: function () {

                if (!this.previewDiv) return;
                if (this.lastPreviewPath != image) return;

                if (this.previewDiv.getElement('img'))
                    this.previewDiv.getElement('img').destroy();

                this.previewLoader.destroy();
                delete this.previewLoader;

                this.lastPreviewImage = new Element('img', {
                    src: image,
                    style: 'position: relative;'
                }).inject(this.previewDiv, 'top');

                [20, 50, 75, 100, 125, 150, 175, 200, 250].each(function(delayMs){
                    (function(){
                        if (!this.previewDiv) return;
                        this.lastPreviewImage.setStyle('top', this.previewDiv.getSize().y/2-this.lastPreviewImage.getSize().y/2);
                    }).delay(delayMs, this);
                }.bind(this));

            }.bind(this)
        });

        this.lastPreviewPath = image;

    },

    preview: function (pEvent) {

        if (pEvent && 'stop' in pEvent){
            pEvent.stop();
        }

        if (pEvent.target && pEvent.target.get('tag') == 'input' && !pEvent.target.hasClass('admin-files-preview-input')) {
            return;
        }

        var selectedItems = this.getSelectedItems();

        if (this.previewDiv) {
            this.closePreview();
            return;
        }

        if (Object.getLength(selectedItems) > 0) {

            var item, file, image;

            pEvent.preventDefault();

            this.lastPreviewedItem = this.lastClickedItem;
            file = this.lastClickedItem.fileItem;

            if (!this.__images.contains(file.path.substr(file.path.lastIndexOf('.')).toLowerCase())) {
                return;
            }

            this.previewDiv = new Element('div', {
                'class': 'admin-files-preview',
                style: 'display: none;'
            }).inject(this.container);

            this.previewDivResizer = new Element('div', {
                style: 'position: absolute;right: -1px;bottom: -1px;width: 9px;'+
                       'height: 9px; opacity: 0.7; background-image: url(' + _path + 'bundles/admin/images/win-bottom-resize.png);'+
                       'cursor: se-resize; background-position: 0px 11px;'
            }).inject(this.previewDiv);

            var img = this.lastPreviewedItem.getElement('img') || this.lastPreviewedItem.getElement('div');
            var position = img.getPosition(this.container);
            var size     = img.getSize();

            this.previewMorph = new Fx.Morph(this.previewDiv, {
                duration: 200,
                transition: Fx.Transitions.Quint.easeOut
            });

            this.previewDiv.setStyles({
                width: size.x,
                height: size.y,
                top: position.y,
                left: position.x,
                opacity: 0,
                display: 'block'
            });

            var containerSize = this.container.getSize();
            size.x = containerSize.x - 200;
            size.y = containerSize.y - 100;

            this.previewDivSize = size;

            this.previewMorph.start({
                width: size.x,
                height: size.y,
                top: (containerSize.y/2)-(size.y/2),
                left: (containerSize.x/2)-(size.x/2),
                opacity: 1
            });

            this.previewDivMover = new Element('div', {
                'class': 'admin-files-preview-inner'
            }).inject(this.previewDiv);

            this.previewDiv.makeDraggable({
                handle: this.previewDivMover
            });

            this.updatePreview();

        }
    },

    fireSelect: function () {
        if (this.options.selection) {
            var selectedItems = this.getSelectedItemsAsArray();
            var selectedFiles = this.getSelectedFilesAsArray();

            if (selectedFiles.length == 1) {
                this.options.selectionValue = selectedFiles[0];
                this.fireEvent('select', [selectedFiles[0], selectedItems[0]]);
            } else if (selectedFiles.length > 1) {
                this.options.selectionValue = selectedFiles;
                this.fireEvent('select', [selectedFiles, selectedItems]);
            }
        } else {
            this.fireEvent('select');
        }
    },

    getValue: function(){

        var selectedFiles = this.getSelectedFilesAsArray();

        if (selectedFiles.length == 1) {
            this.options.selectionValue = selectedFiles[0];
            if (this.options.returnPath)
                return this.options.selectionValue.path;
            else
                return this.options.selectionValue.id;

        } else if (selectedFiles.length > 1) {
            this.options.selectionValue = selectedFiles;
            var items = [];

            selectedFiles.each(function(file){
                if (this.options.returnPath)
                    items.include(file.path);
                else
                    items.include(file.object_id);
            }.bind(this))

            return items;

        }

        return false;
    },

    startDrag: function (pEvent, pItem) {

        this.drag = true;

        this.lastDragTimer = (function () {
            if (this.drag == true) {
                this._startDrag(pEvent, pItem);
            }

        }).delay(300, this);

    },

    _startDrag: function (pEvent, pItem) {

        selection = window.getSelection();
        selection.removeAllRanges();

        if (!pItem.hasClass('admin-files-item-selected')) {
            this.selectItem(pItem);
        }

        var desktop = this.container;

        var selectedItems = this.getSelectedItems();
        var container;

        var draggedItems = [];
        var moveFiles = [];

        draggedItems.include(pItem);

        if (Object.getLength(selectedItems) == 1) {

            var item;
            Object.each(selectedItems, function (selectedItem) {
                item = selectedItem;
            });

            moveFiles.include(item.fileItem.path);

            container = item.clone();
            container.removeClass('admin-files-item-selected')
            container.removeClass('admin-files-droppables');
            var pos = item.getPosition(desktop);

            container.setStyles({
                opacity: 0.7,
                left: pos.x,
                'top': pos.y,
                zIndex: 15000,
                position: 'absolute'
            }).inject(desktop);

        } else if (Object.getLength(selectedItems) > 1) {

            var pos = this.container.getPosition(document.body);

            container = new Element('div').setStyles({
                opacity: 0.7,
                zIndex: 15000,
                width: 50,
                height: 55,
                left: pEvent.page.x - 20,
                'top': pEvent.page.y - 70,
                cursor: 'default',
                position: 'absolute'
            }).inject(desktop);

            Object.each(selectedItems, function (item) {

                draggedItems.include(item);
                moveFiles.include(item.fileItem.path);

                var clone = item.clone().setStyles({
                    position: 'absolute',
                    width: 50,
                    height: 50,
                    cursor: 'default',
                    'background-color': 'transparent',
                    margin: 0
                }).inject(container);

                clone.removeClass('admin-files-item-selected');
                clone.removeClass('admin-files-droppables');

                clone.getElement('.admin-files-item-title').destroy();

                if (item.get('tag') == 'tr') {
                    var imgClone = clone.getElement('img').clone();
                    clone.empty();
                    imgClone.inject(clone);
                }

                if (clone.getElement('img')){
                    clone.getElement('img').setStyles({
                        width: 30,
                        height: 30
                    });
                }

                var i = Math.random();
                var r = (60 * i) - 30;

                if (this.lastRotateValue && this.lastRotateValue < 0 && r < 0) {
                    r = r * -1;
                }

                this.lastRotateValue = r;

                clone.setStyle('-webkit-transform', 'rotate(' + r + 'deg)');
                clone.setStyle('-moz-transform', 'rotate(' + r + 'deg)');

            }.bind(this));

            new Element('div', {
                style: 'position: absolute; bottom: 0px; left: 0px; width: 100%; text-align: center;',
                text: _('%d files').replace('%d', Object.getLength(selectedItems))
            }).inject(container);

        } else if (Object.getLength(selectedItems) == 0) {
            return;
        }

        var fromDir = this.current;

        this.newDragMove(pEvent, container, draggedItems, moveFiles, fromDir);
    },

    newDragMove: function (pEvent, pContainer, pDraggedItems, pFilePaths, pFromDir) {

        this.dragMove = new Drag.Move(pContainer, {

            droppables: '.admin-files-droppables',
            precalculate: true,

            onDrop: function (element, droppable) {

                ka.inFileDragMode = false;
                element.destroy();

                if (droppable.get('tag') == 'td') {
                    droppable = droppable.getParent();
                }

                if (!droppable) return;

                if (droppable.fileObj && droppable.fileObj.activeAutoDirOpenerTimeout) {
                    clearTimeout(droppable.fileObj.activeAutoDirOpenerTimeout);
                }

                if (this.activeAutoDirOpenerTimeout) {
                    clearTimeout(this.activeAutoDirOpenerTimeout);
                }

                var file = droppable.fileItem;
                if (!file || pFromDir == file.path || file.path == '/trash') return;
                if (file.type == 'file' || pFilePaths.contains(file.path)) return;

                if (file.writeaccess == false) return;

                if (!pDraggedItems.contains(droppable)) {
                    droppable.removeClass('admin-files-item-selected');
                    this.fileContainer.removeClass('admin-files-fileContainer-selected');

                    droppable.fileObj.moveFiles(pFilePaths, file.path, false, function () {

                        if (droppable.fileObj != this) {
                            this.reload();
                        }

                    }.bind(this));

                }
            }.bind(this),

            onEnter: function (element, droppable) {

                if (droppable != pContainer) {

                    if (droppable.get('tag') == 'td') {
                        droppable = droppable.getParent();
                    }

                    var file = droppable.fileItem;
                    if (!file){
                        return;
                    }

                    if (file.writeaccess == false) return;
                    if (!file || file.path == '/trash') return;
                    if (file.type == 'file' || pFilePaths.contains(file.path)) return;

                    if (!droppable.hasClass('admin-files-fileContainer')) {
                        droppable.addClass('admin-files-item-selected');
                        droppable.fileObj.startAutoDirOpener(file, this.updateDragMoveDroppables.bind(this));
                    }

                    if (droppable.hasClass('admin-files-fileContainer')) {
                        if (file.path == pFromDir) return;
                        droppable.addClass('admin-files-fileContainer-selected');
                    }

                }
            }.bind(this),

            onLeave: function (element, droppable) {

                if (droppable.get('tag') == 'td') {
                    droppable = droppable.getParent();
                }

                if (droppable.fileObj && droppable.fileObj.activeAutoDirOpenerTimeout) {
                    clearTimeout(droppable.fileObj.activeAutoDirOpenerTimeout);
                }

                droppable.removeClass('admin-files-fileContainer-selected');
                if (!pDraggedItems.contains(droppable)) {
                    droppable.removeClass('admin-files-item-selected');
                }
            },

            onCancel: function (dragging) {
                dragging.destroy();
                ka.inFileDragMode = false;
            }

        });

        this.dragMove.start(pEvent);

        ka.inFileDragMode = true;
    },

    updateDragMoveDroppables: function () {
        if (this.dragMove) {
            this.dragMove.droppables = $$('.admin-files-droppables');
        }
    },

    startAutoDirOpener: function (pFile, pCallback) {

        this.activeAutoDirOpenerTimeout = (function () {

            this.loadPath(pFile.path, pCallback);

        }).delay(1000, this);

    },

    renderImage: function () {

    },

    renderDetail: function () {

        var pAdmin = _pathAdmin + 'admin/';

        this.detailTable = new ka.Table([
            ['', 20],
            [_('Name')],
            [_('Size'), 100],
            [_('Last modified'), 155]
        ]).inject(this.fileContainer);

        var rows = [];
        this.files2View.each(function (file) {

            var bg = '';
            if (file.type != 'dir' && this.__images.contains(file.path.substr(file.path.lastIndexOf('.')).toLowerCase())) { //is image
                bg = 'image'
            } else if (file.type == 'dir') {
                bg = 'dir'
            } else if (this.__ext.contains(file.path.substr(file.path.lastIndexOf('.')))) {
                bg = file.path.substr(file.path.lastIndexOf('.')+1);
            } else {
                bg = 'tpl';
            }

            if (file.path == '/trash') {
                bg = 'dir_bin';
            }

            var image = new Element('img', {
                src: _path + 'bundles/admin/images/ext/' + bg + '-mini.png'
            });

            var size = ka.bytesToSize(file.size);

            if (file.type == 'dir') {
                size = _('Directory');
            }

            rows.include([
                image, file.name, size, new Date(file.mtime * 1000).format('db')
            ]);

        }.bind(this));

        this.detailTable.setValues(rows);

        this.detailTable.tableBody.getElements('tr').each(function (tr, id) {
            tr.fileItem = this.files2View[id];
            tr.fileObj = this;
            tr.getElements('td').addClass('admin-files-droppables');
        }.bind(this));
    },

    setIconZoom: function (pZoom) {

        if (this.iconZoom) {
            this.fileContainer.removeClass('admin-files-item-size-' + this.iconZoom);
        }

        if (pZoom) {
            this.iconZoom = pZoom;
            this.fileContainer.addClass('admin-files-item-size-' + pZoom);
        } else {
            this.iconZoom = false;
        }

    },

    renderIcons: function (pItems) {
        var html = "";

        var knownExts = ["tpl", "html", "jpg"];
        var krynFiles = [];
        var moduleFiles = [];

        var files = [];

        if (pItems) {
            pItems.each(function (item) {
                var titem =  this.__buildItem(item);
                if (!titem) return;
                titem.inject(this.fileContainer);
            }.bind(this));
        }

        return files;
    },

    getIcon: function(pFile){
        var fileIcon = 'icon-folder-4';

        if (pFile.type == 'dir') {
            if (pFile.path == '/trash') {
                fileIcon = 'icon-trashcan-6';
            } else {
                if (pFile.magic)
                    fileIcon = 'icon-network';
            }
        } else {
            fileIcon = 'icon-paper-2';
        }
        return fileIcon;
    },

    __buildItem: function (pFile) {

        var fileIcon;

        var base = new Element('div', {
            'class': (pFile.path == '/trash' ? '' : 'admin-files-droppables ') + 'admin-files-item',
            title: pFile.object_id+'='+pFile.name
        });

        var fileIconClass = null;
        var fileIcon = null;

        if (pFile.path.lastIndexOf('.') && this.__images.contains(pFile.path.substr(pFile.path.lastIndexOf('.')).toLowerCase())) {

            fileIcon = pFile;

        } else {

            fileIconClass = this.getIcon(pFile);

        }
        base.fileObj = this;

        base.imageContainer = new Element('div', {
            'class': 'admin-files-item-icon '+(fileIconClass?fileIconClass:'')
        }).inject(base);

        if (fileIcon) {
            base.image = new Element('img').inject(base.imageContainer);
        }

        if (fileIcon){
            base.readyToLoadImage = fileIcon;
        }

        var title = new Element('div', {
            'class': 'admin-files-item-title',
            'text': (pFile.path == '/trash') ? t('Trash') : this.escTitle(pFile.name, base.getSize().x)
        }).inject(base);


        if (fileIcon && pFile.dimensions) {
            new Element('div', {
                'class': 'admin-files-item-title-dimensions',
                text: pFile.dimensions.width+' x '+pFile.dimensions.height
            }).inject(title, 'top');
        }

        if (this.options.selectionValue) {
            if (typeOf(this.options.selectionValue) == 'string' &&
                (this.options.selectionValue == pFile.path || this.options.selectionValue == pFile.path.substr(1))) {
                base.addClass('admin-files-item-selected');
            } else if (typeOf(this.options.selectionValue) == 'array' && this.options.selectionValue.contains(pFile.path)) {
                base.addClass('admin-files-item-selected');
            }
            this.fireSelect();
        }

        base.fileItem = pFile;
        return base;
    },

    escTitle: function (pTitle, pSize) {

        //TODO, depend on the size

        var maxLine = 13;
        var maxAll = 32;
        if (this.listType == 'miniatur') {
            maxLine = 21;
            maxAll = 42;
        }

        return this.smartTrim(pTitle, maxAll);

        if (pTitle.length > maxAll) {
            pTitle = pTitle.substr(0, maxAll) + '..';
        }

        return pTitle;
    },

    smartTrim: function(string, maxLength){
        if (!string) return string;
        if (maxLength < 1) return string;
        if (string.length <= maxLength) return string;
        if (maxLength == 1) return string.substring(0,1) + '...';

        var midPoint = Math.ceil(string.length / 2);
        var toRemove = string.length - maxLength;
        var lStrip = Math.ceil(toRemove/2);
        var rStrip = toRemove - lStrip;
        return string.substring(0, midPoint-lStrip) + '...' + string.substring(midPoint+rStrip);

    },

    recover: function (pFile) {

        this.win._confirm(_('This file will be moved to: %s').replace('%s', '<br/><br/>' + pFile['original_path'] + '<br/><br/>') + _('Are you really sure?'), function (res) {
            if (res) {

                new Request.JSON({url: _pathAdmin + 'admin/file/recover', noCache: 1, onComplete: function () {
                    this.reload();
                }.bind(this)}).post({id: pFile.original_id});

            }
        }.bind(this));

    },

    destroyContext: function () {
        if (this.context) {
            this.context.destroy();
        }
        delete this.context;
    },

    openContext: function (pFile, pEvent) {

        if (this.context) {
            this.context.destroy();
        }

        if (pFile.path == '/trash') {
            return;
        }

        this.context = new Element('div', {
            'class': 'admin-files-context'
        }).inject(this.win.border);

        if (pFile.path.substr(0, 6) == '/trash') {
            //pressed on a item in the trash folder

            var recover = new Element('a', {
                html: _('Recover')
            }).addEvent('click', function () {
                this.recover(pFile);
            }.bind(this)).inject(this.context)

            var remove = new Element('a', {
                'class': 'delimiter',
                html: _('Remove')
            }).addEvent('click', this.remove.bind(this, pFile)).inject(this.context);

        } else {


            if (this.currentFile.path != pFile.path) {
                var open = new Element('a', {
                    html: _('Open')
                }).addEvent('click', function () {
                    this.loadPath(pFile.path);
                }.bind(this)).inject(this.context)
            }

            var openExternal = new Element('a', {
                html: _('Open external'),
                target: '_blank',
                href: _pathAdmin + 'admin/file/redirect?' +
                    Object.toQueryString({path: pFile.path, noCache: (new Date()).getTime()})
            }).inject(this.context)


            if (this.currentFile.path == pFile.path) {
                //clicked on the background

                var paste = new Element('a', {
                    html: _('Paste (strg+v)')
                }).addEvent('click', this.paste.bind(this)).inject(this.context);

            } else {

                var cut = new Element('a', {
                    'class': 'delimiter',
                    html: _('Cut (strg+x)')
                }).addEvent('click', this.cut.bind(this)).inject(this.context);

                var copy = new Element('a', {
                    html: _('Copy (strg+c)')
                }).addEvent('click', this.copy.bind(this)).inject(this.context);

                var duplicate = new Element('a', {
                    html: _('Duplicate')
                }).addEvent('click', this.duplicate.bind(this, pFile)).inject(this.context);

                var newversion = new Element('a', {
                    html: _('New version')
                }).addEvent('click', this.newversion.bind(this, pFile)).inject(this.context);

                var remove = new Element('a', {
                    'class': 'delimiter',
                    html: _('Remove')
                }).addEvent('click', this.remove.bind(this, pFile)).inject(this.context);

                var rename = new Element('a', {
                    html: _('Rename')
                }).addEvent('click', this.rename.bind(this, pFile)).inject(this.context);
            }

            var settings = new Element('a', {
                'class': 'delimiter',
                html: _('Properties')
            }).addEvent('click',
                function () {
                    ka.wm.open('admin/file/properties', pFile);
                }).inject(this.context);

        }

        var deactivate = function (item) {
            if (!item) return;
            item.addClass('notactive')
            item.removeEvents('click');
        }

        var selectedFiles = this.getSelectedFiles();

        if (Object.getLength(selectedFiles) > 1 || pFile.type == 'dir') {
            if (duplicate) duplicate.destroy();
            if (newversion) newversion.destroy();
        }

        if (Object.getLength(selectedFiles) > 1) {
            deactivate(open);
            deactivate(openExternal);
            deactivate(settings);
            deactivate(rename);
        }


        if (ka.getClipboard().type != 'filemanager' && ka.getClipboard().type != 'filemanagerCut') {
            deactivate(paste);
        }

        Object.each(selectedFiles, function (myfile) {

            if (myfile.magic || myfile.writeaccess != true || this._krynFolders.indexOf(myfile.path+'/') >= 0 || this._modules.indexOf(myfile.path+'/') >= 0) {
                //no writeaccess
                deactivate(cut);
                deactivate(remove);
                deactivate(rename);
                deactivate(newversion);
            }

            if (myfile.magic){
                deactivate(settings);
                deactivate(openExternal);
            }

        }.bind(this));

        if (this.currentFile.writeaccess != true) {
            deactivate(paste);
        }

        var pos = this.win.border.getPosition(document.body);

        this.context.setStyles({
            left: (parseInt(pEvent.client.x) + 4 - pos.x) + 'px',
            top: (parseInt(pEvent.client.y) + 4 - pos.y) + 'px'
        });

    },

    duplicate: function (pFile) {

        var newName = pFile.name;
        var t = newName.split('.');
        if (t[1]) {
            newName = t[0] + '-' + _('duplication') + '.' + t[1];
        }

        this.win._prompt(_('New name') + ': ', newName, function (name) {
            if (!name) return;
            this._duplicate(pFile, name);
        }.bind(this));

    },

    _duplicate: function(pFile, pName) {

        new Request.JSON({url: _pathAdmin + 'admin/file/duplicate', onComplete: function (res) {
            if(res.file_exists){
                this.win._confirm(_('The new filename already exists. Overwrite?'), function(answer){
                    if(answer) this._duplicate(pPath, pName, 1);
                }.bind(this));
            } else {
                this.reload();
            }
        }.bind(this)}).get({path: pFile.path, newName: pName});

    },

    newversion: function (pFile) {

        new Request.JSON({url: _pathAdmin + 'admin/file/version', onComplete: function (res) {
            ka.helpsystem.newBubble(_('New version created'), pFile.path, 3000);
        }.bind(this)}).post({path: pFile.path});

    },

    copy: function () {
        var title = '';

        var selectedFiles = this.getSelectedFiles();

        if (Object.getLength(selectedFiles) > 1) {
            title = _('%d file copied', Object.getLength(selectedFiles)).replace('%d', Object.getLength(selectedFiles));
        } else {
            Object.each(selectedFiles, function (item) {
                title = _('%s file copied').replace('%s', item.name.substr(0, 25) + ((item.name.length > 25) ? '...' : ''));
            });
        }
        ka.setClipboard(title, 'filemanager', selectedFiles);
    },

    cut: function () {

        var selectedFiles = this.getSelectedFiles();

        if (Object.getLength(selectedFiles) > 1) {
            title = _('%d files cut').replace('%d', Object.getLength(selectedFiles));
        } else {
            Object.each(selectedFiles, function (item) {
                title = _('%s file cut').replace('%s', item.name.substr(0, 25) + ((item.name.length > 25) ? '...' : ''));
            });
        }
        ka.setClipboard(title, 'filemanagerCut', selectedFiles);

    },

    deselect: function () {

        this.fileContainer.getElements('.admin-files-item-selected').removeClass('admin-files-item-selected');

        this.fireDeselect();
    },

    fireDeselect: function(){
        this.fireEvent('deselect');
    },

    startSearch: function () {
        if (this._searchTimer) {
            clearTimeout(this._searchTimer);
        }

        if (this.searchInput.getValue() == "") {
            this.closeSearch();
        } else {
            this._searchTimer = this._search.delay(300, this, this.searchInput.getValue());
        }

    },

    _search: function (pQ) {

        if (!this.searchPane) {
            this.searchPane = new Element('div', {
                style: 'position: absolute; padding: 5px; top: 0px; right: 0px; bottom: 0px; width: 250px; border-left: 2px solid silver; background-color: #ddd;',
                styles: {
                    opacity: 0.95
                }
            }).inject(this.container);

            this.searchPaneTitle = new Element('div', {
                style: 'position: absolute; top: 0px; left: 0px; right: 0px; height: 25px; line-height: 25px; font-weight: bold; padding-left: 5px; color: gray;border-bottom: 1px solid silver; background-color: #e4e4e4;'
            }).inject(this.searchPane);

            searchPaneCloser = new Element('div', {
                style: 'position: absolute; top: 3px; right: 3px; font-weight: bold;',
                'class': 'kwindow-win-titleBarIcon kwindow-win-titleBarIcon-close'
            }).addEvent('click', function () {
                this.closeSearch();
            }.bind(this)).inject(this.searchPane);

            this.searchPaneContent = new Element('div', {
                style: 'position: absolute; overflow: auto; top: 0px; left: 0px; right: 0px; top: 26px; bottom: 0px;'
            }).inject(this.searchPane);
        }

        this.searchPaneContent.set('html', '<div style="text-align: center; padding-top: 25px;">' + '<img src="' + _path + 'bundles/admin/images/ka-tooltip-loading.gif" /><br />' + _('Searching ...') + '</div>');

        if (this.lastqrq) {
            this.lastqrq.cancel();
        }

        this.searchPaneTitle.set('html', _('Searching ...'));
        this.lastqrq = new Request.JSON({url: _pathAdmin + 'admin/file/search', noCache: 1, onComplete: function (res) {

            this.showSearchEntries(res);

        }.bind(this)}).get({q: pQ, path: this.current});

    },

    showSearchEntries: function (pResult) {

        this.searchPaneContent.empty();
        this.searchPaneTitle.set('html', _('Results'));

        var table = new Element('table', {
            'class': 'ka-files-search-table',
            width: '100%',
            cellspacing: 0
        }).inject(this.searchPaneContent);
        var tbody = new Element('tbody').inject(table);
        var bg, div;

        if (typeOf(pResult) == 'array' && pResult.length > 0) {
            pResult.each(function (file) {

                var tr = new Element('tr').inject(tbody);
                var td = new Element('td', {width: 20}).inject(tr);

                bg = '';
                if (file.type != 'dir' && this.__images.contains(file.path.substr(file.path.lastIndexOf('.')).toLowerCase())) { //is image
                    bg = 'image'
                } else if (file.type == 'dir') {
                    bg = 'dir'
                } else if (this.__ext.contains(file.path.substr(file.path.lastIndexOf('.')))) {
                    bg = file.path.substr(file.path.lastIndexOf('.')+1);
                } else {
                    bg = 'tpl';
                }

                if (file.path == '/trash') {
                    bg = 'dir_bin';
                }

                var image = new Element('img', {
                    src: _path + 'bundles/admin/images/ext/' + bg + '-mini.png'
                }).inject( td );

                var td = new Element('td').inject(tr);

                div = new Element('div', {
                    text: file.path,
                    style: 'padding-left: 5px; color: #aaa; font-weight: normal;'
                }).inject(td);

                var a = new Element('a', {
                    text: file.name,
                    href: 'javascript: ;',
                    style: 'display: block; text-decoration: none; font-weight: bold; padding: 2px; cursor: pointer;'
                }).inject(div, 'top');

                a.addEvent('click', function () {
                    if (file.type == 'dir')
                        this.loadPath(file.path);
                    else
                        ka.wm.openWindow('admin/file/edit', null, null, {file: file});
                }.bind(this));


            }.bind(this));
        } else {
            this.searchPaneContent.set('html', t('No files found.'));
        }

    },

    closeSearch: function () {
        if (this.lastqrq) {
            this.lastqrq.cancel();
        }

        if (this.searchPane) {
            this.searchPane.destroy();
            this.searchPane = null;
        }
    }
});
