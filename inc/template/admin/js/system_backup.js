var admin_system_backup = new Class({

    

    initialize: function( pWin ){
    
        this.win = pWin;
        
        this.renderLayout();
    
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
        this.btnGrp.addButton(_('Delete'), _path+'inc/template/admin/images/icons/delete.png', this.import.bind(this));
        this.btnImport = this.btnGrp.addButton(
            _('Import'),
            _path+'inc/template/admin/images/icons/database_import.png',
            this.import.bind(this)
        );
        
        
        this.addGrp = this.win.addButtonGroup();
        this.addGrp.setStyle('margin-left', 100);
        this.addSaveBtn = this.addGrp.addButton(_('Save'), _path+'inc/template/admin/images/button-save.png', this.save.bind(this));
        
        this.addGenerateBtn = this.addGrp.addButton(    
            _('Generate now'),
            _path+'inc/template/admin/images/button-save-and-publish.png', 
            this.generate.bind(this)
        );


        this.addGrp.hide();
    },

    save: function(){
    
    },
    
    generate: function(){
        
    },
    
    setAddButtons: function(){
    
        this.addSaveBtn.hide();
        this.addGenerateBtn.hide();


        if( !this.fields || this.fields.getValue('method') == 'download' ){        
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
        this.setAddButtons();
        this.addGrp.show();
        this.btnNewBackup.setPressed(true);
        this.btnImport.setPressed(false);
        
        var fields = {
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
                    start: {
                        label: _('Starts at'),
                        needValue: 'cronjob',
                        type: 'select',
                        items: {
                            infinite: _('Immediately'),
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
                        items: {
                            hour: _('Hour'),
                            '3hour': _('3 Hours'),
                            '6hour': _('6 Hours'),
                            '12hour': _('12 Hours'),
                            day: _('Day'),
                            week: _('Week'),
                            month: _('Month'),
                            quarter: _('Quarter'),
                            specifiy: _('Specifiy'),
                            
                        },
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
                    choose: _('Choose node')
                },
                depends: {
                    pages_allversions: {
                        needValue: ['all', 'choose'],
                        label: _('With all versions'),
                        desc: _('Please note: This can generate a really huge backup file if you generated many versions.'),
                        type: 'checkbox'
                    },
                    pages_choose: {
                        label: _('Nodes'),
                        needValue: 'choose',
                        desc: _('Please select a page node or a domain.'),
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
                    extensions_data_choose: {
                        needValue: 'choose',
                        type: 'textlist',
                        store: 'admin/backend/stores/extensions'
                        
                    },
                }
            }
        
        }
        
        this.fields = new ka.parse( this.main, fields );
        
    },
    
    deselect: function(){
    
    },
    
    loadBackups: function(){
    
    
    }


});