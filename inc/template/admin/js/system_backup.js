var admin_system_backup = new Class({
    
    items: {},
    
    initialize: function( pWin ){
    
        this.eachItems = {
            hour: _('Hour'),
            '3hour': _('3 Hours'),
            '6hour': _('6 Hours'),
            '12hour': _('12 Hours'),
            day: _('Day'),
            week: _('Week'),
            month: _('Month'),
            quarter: _('Quarter'),
            specifiy: _('Specifiy')
        };
        
        this.win = pWin;
        
        this.win.addEvent('close', function(){
            this.closed = true;
        }.bind(this));
        
        this.renderLayout();
        
        //TODO check if ka.settings.cronjob_key is setup. if not, create alert with link to system->settings
        
        
        this.fieldDefs = {
            method: {
                label: _('Method'),
                type: 'select',
                desc: _("Please note: If you choose 'Generate later' you have to setup the cronjob script."),
                help: 'admin/backup-cronjob',
                items: {
                    download: _('Download now'),
                    cronjob: _('Generate later')
                },
                onChange: this.setAddButtons.bind(this),
                depends: {
                    savetarget: {
                        label: ('Save target'),
                        needValue: 'cronjob',
                        type: 'select',
                        items: {
                            local: _('Local'),
                            ftp: _('FTP'),
                            ssh: _('SSH')
                        },
                        depends: {
                        
                            savetarget_local: {
                                needValue: 'local',
                                lable: _('Target'),
                                desc: _('Relative paths starts at the root of the kryn installation.'),
                                type: 'folder'
                            }
                        }
                    },
                    start: {
                        label: _('Starts at'),
                        needValue: 'cronjob',
                        type: 'select',
                        items: {
                            immediately: _('Immediately'),
                            specifiy: _('Specifiy')
                        },
                        depends: {
                            start_date: {
                                needValue: 'specifiy',
                                type: 'datetime'
                            }
                        }
                    },
                    each: {
                        label: _('Each ...'),
                        needValue: 'cronjob',
                        type: 'select',
                        items: this.eachItems,
                        depends: {
                            each_minute: {
                                desc: _('Specify in minutes'),
                                needValue: 'specifiy',
                                type: 'datetime'
                            }
                        }
                    },
        
                    end: {
                        label: _('Ends at'),
                        needValue: 'cronjob',
                        type: 'select',
                        items: {
                            infinite: _('Infinite'),
                            specifiy: _('Specifiy')
                        },
                        depends: {
                            end_date: {
                                needValue: 'specifiy',
                                type: 'datetime'
                            }
                        }
                    },
                    
                    maxrounds: {
                        label: _('Rounds'),
                        needValue: 'cronjob',
                        type: 'select',
                        items: {
                            infinite: _('Infinite'),
                            once: _('Once'),
                            specifiy: _('Specifiy')
                        },
                        depends: {
                            maxrounds_count: {
                                needValue: 'specifiy',
                                type: 'number',
                                width: 50
                            }
                        }
                    },
                }
            
            },

            pages: {
                label: _('Websites'),
                type: 'select',
                items: {
                    nothing: _('Nothing'),
                    all: _('All websites'),
                    choose: _('Choose nodes')
                },
                depends: {
                    pages_allversions: {
                        needValue: ['all', 'choose'],
                        label: _('With all versions'),
                        desc: _('This includes additionally all versions. Please note: Can blow up the backup file.'),
                        type: 'checkbox'
                    },
                    pages_domains: {
                        label: _('Domains'),
                        needValue: 'choose',
                        desc: _('Please select the domain/s.'),
                        type: 'textlist',
                        store: 'backend/stores/domains'
                    },
                    pages_nodes: {
                        label: _('Nodes'),
                        needValue: 'choose',
                        desc: _('Please select a page node.'),
                        type: 'array',
                        startWith: 1, 
                        columns: [
                            {label: ''}
                        ],
                        fields: {
                            domain: {
                                type: 'page'
                            }
                        }
                    }
                }
            },
            
            files: {
                label: _('Files'),
                desc: _('All files means all files except extension files.'),
                type: 'select',
                items: {
                    nothing: _('Nothing'),
                    all: _('All files'),
                    choose: _('Choose directories')
                },
                depends: {
                    files_allversions: {
                        needValue: ['all', 'choose'],
                        label: _('With all versions'),
                        desc: _('This includes additionally all versions. Please note: Can blow up the backup file.'),
                        type: 'checkbox'
                    },
                    files_choose :{
                        needValue: 'choose',
                        label: _('Folders'),
                        type: 'array',
                        startWith: 1, 
                        columns: [
                            {label: ''}
                        ],
                        fields: {
                            folder: {
                                type: 'folder'
                            }
                        }
                    }
                }
            },
            
            extensions: {
                label: _('Extensions'),
                desc: _('Contains the whole package and also your translated languages and adjusted templates.'),
                type: 'select',
                items: {
                    nothing: _('Nothing'),
                    all: _('All extensions'),
                    choose: _('Choose extension')
                },
                depends: {
                    extensions_choose: {
                        needValue: 'choose',
                        type: 'textlist',
                        store: 'admin/backend/stores/extensions'
                        
                    },
                }
            },
            
            extensions_data: {
                label: _('Extension contents'),
                desc: _('Means the contents in the database.'),
                type: 'select',
                items: {
                    nothing: _('Nothing'),
                    all: _('All extension contents'),
                    choose: _('Choose extension')
                },
                depends: {
                    extensions_data_allversions: {
                        needValue: ['all', 'choose'],
                        label: _('With all versions'),
                        desc: _('This includes additionally all versions. Please note: Can blow up the backup file.'),
                        type: 'checkbox'
                    },
                    extensions_data_choose: {
                        needValue: 'choose',
                        type: 'textlist',
                        store: 'admin/backend/stores/extensions'
                        
                    },
                }
            }
        }
    
        this.loadItems();
    },
    
    renderLayout: function(){
    
        this.left = new Element('div', {
            style: 'position: absolute; left: 0px; width: 200px; top: 0px;  overflow: auto;'
                    +'bottom: 0px; border-right: 1px solid silver; background-color: #f7f7f7;'
        }).inject( this.win.content );
    
        this.main = new Element('div', {
            style: 'position: absolute; right: 0px; top: 0px;'
                    +'bottom: 0px; left: 201px; overflow: auto;'
        }).inject( this.win.content );
    
        this.btnGrp = this.win.addButtonGroup();
        this.btnNewBackup = this.btnGrp.addButton(_('New Backup'), _path+'inc/template/admin/images/icons/add.png', this.add.bind(this));
        this.btnImport = this.btnGrp.addButton(
            _('Import'),
            _path+'inc/template/admin/images/icons/database_import.png',
            this.import.bind(this)
        );
        
        
        this.addGrp = this.win.addButtonGroup();
        this.addGrp.setStyle('margin-left', 130);
        this.addSaveBtn = this.addGrp.addButton(_('Save'), _path+'inc/template/admin/images/button-save.png', this.save.bind(this));
        this.addDeleteBtn = this.addGrp.addButton(_('Delete'), _path+'inc/template/admin/images/icons/delete.png', this.remove.bind(this));

        this.addGenerateBtn = this.addGrp.addButton(    
            _('Generate now'),
            _path+'inc/template/admin/images/button-save-and-publish.png', 
            this.generate.bind(this)
        );


        this.addGrp.hide();
    },
    
    renderItems: function( pItems ){
    
        this.left.empty();
        this.items = {};
        
        Object.each( pItems, function(item, id ){
            this.addItem( id, item );
        }.bind(this));
    
    },
    
    loadItem: function( pId ){
        
        this.deselect();
        this.lastSelect = this.items[ pId ];
        
        this.btnNewBackup.setPressed(false);
        this.btnImport.setPressed(false);
        this.addDeleteBtn.show();

        this.addGrp.show();
        this.main.empty();

        this.fields = new ka.parse( this.main, this.fieldDefs );
        var values = this.items[ pId ].retrieve('item');

        this.fields.setValue( values );
        this.setAddButtons();
        
        this.items[ pId ].addClass('ka-backup-item-active');
        
    },
    
    addItem: function( pId, pItem ){
        var div = new Element('div', {
            'class': 'ka-backup-item'
        })
        .addEvent('click', this.loadItem.bind(this, pId))
        .inject( this.left );
        
        div.store('item', pItem);
        div.store('id', pId);
        this.items[ pId ] = div;
        
        new Element('h2', {
            text: '#'+pId
        }).inject( div );
        
        if( pItem.method != 'download' ){
            new Element('div', {
                text: _('Starts: ')+( pItem.start=='immediately'?_('Immediately'):new Date(pItem.start_date*1000).format('db'))
            }).inject( div );
            
            var times = _('One times');
            if( pItem.maxrounds == 'infinite' ){
                times = _('Infinite times');
            } else if( pItem.maxrounds == 'specifiy' ){
                times = _('%d times').replace('%d', pItem.maxrounds_count);
            }
    
            var each = this.eachItems[pItem.each];
            if( pItem.each == 'specify' ){
                each = _('%d Minutes').replace('%d', pItem.each_minute);
            }
    
            new Element('div', {
                text: _('Each: ')+each+', '+times
            }).inject( div );
            new Element('div', {
                text: _('Ends: ')+( pItem.end=='infinite'?_('Infinite'):new Date(pItem.end_date*1000).format('db'))
            }).inject( div );
        } else {
            new Element('div', {
                text: _('One-time backup.')
            }).inject( div );
        }
        
        new Element('div', {
            html: _('Generated %d backups.').replace('%d', '<b>'+(pItem.generated?pItem.generated:0)+'</b>')
        }).inject( div );
        
        if( pItem.working ){
            this.attachProgressBar( div );
        }
        
    },
    
    attachProgressBar: function( pDiv ){
    
        var progress = new ka.Progress( false, 'Loading ...' );
        document.id(progress).inject( pDiv );
        document.id(progress).setStyle( 'margin-top', 5 );
        
        var update;
        
        var id = pDiv.retrieve('id');
        
        var translate= {
            error: _('Error'),
            start: _('Started'),
            not_found: _('Backup deleted.'),
            done: '<b style="color: green">'+_('Done')+'</b>'
        };
        
        update = function(){
            new Request.JSON({url: _path+'admin/system/backup/state', onComplete: function(res){
                if( !this.closed ){
                    progress.setText( res+': '+translate[res] );
                    if( res != 'done' && res != 'error' && res != 'not_found' )
                        update.delay(1000, this);
                }
            }.bind(this)}).get({id: id});
        }.bind(this);

        update();
    
    },
    
    loadItems: function(){
        new Request.JSON({url: _path+'admin/system/backup/list', onComplete: this.renderItems.bind(this)}).get();
    },

    save: function(){

        var id = '';
        if( this.lastSelect )
            id = '?id='+this.lastSelect.retrieve('id');

        var req = this.fields.getValue();
        new Request.JSON({url: _path+'admin/system/backup/save'+id, onComplete: function(){
            this.loadItems();
        }.bind(this)}).post(req);

    },
    
    remove: function(){
        
        if( !this.lastSelect ) return;
        
        var id = '?id='+this.lastSelect.retrieve('id');

        var req = this.fields.getValue();
        this.main.empty();

        new Request.JSON({url: _path+'admin/system/backup/remove'+id, onComplete: function(){
            delete this.lastSelect;
            this.addGrp.hide();
            this.loadItems();
        }.bind(this)}).post(req);
    
    },
    
        
    generate: function(){
        var req = this.fields.getValue();
        new Request.JSON({url: _path+'admin/system/backup/save?start=1', onComplete: function(){
            this.loadItems();
        }.bind(this)}).post(req);
    },
    
    setAddButtons: function(){
    
        this.addSaveBtn.hide();
        this.addGenerateBtn.hide();
        this.addDeleteBtn.hide();
        
        if( this.lastSelect )
            this.addDeleteBtn.show();

        if( (!this.fields || this.fields.getValue('method') == 'download') && !this.lastSelect ){        
            this.addGenerateBtn.show();
        } else {
            this.addSaveBtn.show();
        }
    },
    
    import: function(){
    
        this.deselect();
        this.btnNewBackup.setPressed(false);
        this.btnImport.setPressed(true);
        this.addGrp.hide();
        this.main.empty();
        
    },

    add: function(){
        this.main.empty();
        this.deselect();

        this.addGrp.show();

        this.btnNewBackup.setPressed(true);
        this.btnImport.setPressed(false);

        this.fields = new ka.parse( this.main, this.fieldDefs );
        this.setAddButtons();
    },
    
    deselect: function(){
        if( this.lastSelect )
            this.lastSelect.removeClass('ka-backup-item-active');
        delete this.lastSelect;
    }


});