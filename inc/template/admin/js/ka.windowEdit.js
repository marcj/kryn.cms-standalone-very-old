ka.windowEdit = new Class({

    Implements: Events,

    inline: false,

    initialize: function( pWin, pContainer ){
        this.win = pWin;
        
        if( !pContainer ){
            this.container = this.win.content;
            this.container.setStyle('overflow', 'visible');
        } else {
            this.inline = true;
            this.container = pContainer;
        }
        
        this.win.addEvent('close', this.checkClose.bind(this));
        
        this.load();
    },
    
    destroy: function(){
    
        if( this.topTabGroup ){
            this.topTabGroup.destroy();
        }
        
        if( this.actionsNavi )
            this.actionsNavi.destroy();
        
        if( this.actionsNaviDel )
            this.actionsNaviDel.destroy();
        
        if( this.versioningSelect )
            this.versioningSelect.destroy();
            
        if( this.languageSelect )
            this.languageSelect.destroy();
            
        this.versioningSelect = null;
        this.languageSelect = null;
        
        this.container.empty();
    
    },

    load: function(){
        var _this = this;
        new Request.JSON({url: _path+'admin/'+this.win.module+'/'+this.win.code, noCache: true, onComplete: function(res){
            this.render( res );
        }.bind(this)}).post();
    },
    
    generateItemParams: function( pVersion ){
    	var req = {};
	    
	    if( pVersion )
	    	req.version = pVersion;
	
	    if( this.win.params ){
	        this.values.primary.each(function(prim){
	            req[ prim ] = this.win.params.values[prim];
	        }.bind(this));
	    }
	    return req;
	},

    loadItem: function( pVersion ){
        var _this = this;
        var req = this.generateItemParams( pVersion );

        if( this.lastRq )
            this.lastRq.cancel();

        this.loader.show();
        this.lastRq = new Request.JSON({url: _path+'admin/'+this.win.module+'/'+this.win.code+'?cmd=getItem',

        noCache: true, onComplete: function(res){
            this._loadItem( res );
        }.bind(this)}).post(req);
    },
    
    _loadItem: function( pItem ){
        this.item = pItem;
        
        this.previewUrls = pItem.preview_urls;
        
        var first = false;
        
        Object.each(this.fields, function(field, fieldId){

            if( first == false && typeOf(pItem.values[fieldId]) == 'string' ){
                this.win.setTitle(pItem.values[fieldId]);
                first = true;
            }
            try {
            	
            	if( this.windowAdd && this.win.params && this.win.params.relation_table &&
                    this.win.params.relation_params[fieldId]
            	){
            	   field.setValue( this.win.params.relation_params[fieldId] );
            	   
            	} else if( field.field.type == 'window_list' ){
                    field.setValue({table: this.values.table, params: pItem.values});
                    
                } else if( typeOf(pItem.values[fieldId]) == 'null' ){
                    field.setValue( '' );

                } else if( !field.field.startempty ){
                    field.setValue( pItem.values[fieldId] );
                }

                if( !this.windowAdd ){
                    var contentCss = _path+"inc/template/css/kryn_tinyMceContentElement.css";
                    initResizeTiny( field.lastId, contentCss );
                    ka._wysiwygId2Win.include( field.lastId, this.win );
                }
                
                if( field.field.depends ){
                	field.fireEvent('change', field.getValue());
                }
                
            } catch(e) {
                //logger( "Error with "+fieldId+": "+e);
            }
        }.bind(this));
        
        
        if( this.values.multiLanguage && this.languageSelect.getValue() != this.item.values.lang ){
        	this.languageSelect.setValue( this.item.values.lang );
        	this.changeLanguage();
        }

        this.renderVersionItems();
    
        this.loader.hide();
        this.fireEvent('load', pItem);
        
        this.ritem = this.retrieveData();
    },
    
    renderPreviews: function(){
    
        if( !this.values.previewPlugins ){
            return;
        }
        
        //this.previewBtn;
        
        this.previewBox = new Element('div', {
            'class': 'ka-Select-chooser'
        });
        
        this.previewBox.addEvent('click', function(e){
            e.stop();
        });
        
        var target = this.win.content.getParent('.kwindow-border');
        this.previewBox.inject( target );
        
        this.previewBox.setStyle('display', 'none');
        
        //this.values.previewPlugins
        
        document.body.addEvent('click', this.closePreviewBox.bind(this));
      
        if( !this.values.previewPluginPages ){
            return;
        }
        
        Object.each(this.values.previewPlugins, function(item,pluginId){
        
            var title = ka.settings.configs[this.win.module].plugins[pluginId][0];
            
            
            new Element('div', {
                html: title,
                href: 'javascript:;',
                style: 'font-weight:bold; padding: 3px; padding-left: 15px;'
            })
            .inject( this.previewBox );      
            
            var index = pluginId;
            if( pluginId.indexOf('/') === -1 )
                index = this.win.module+'/'+pluginId;
            
            Object.each(this.values.previewPluginPages[index], function(pages,domain_rsn){
            
                Object.each(pages, function(page, page_rsn){                
                   
                    var domain = ka.getDomain(domain_rsn);
                    if( domain ){
                        new Element('a', {
                            html: '<span style="color: gray">['+domain.lang+']</span> '+page.path,
                            style: 'padding-left: 21px',
                            href: 'javascript:;'
                        })
                        .addEvent('click', this.doPreview.bind(this, [page_rsn, index]))
                        .inject( this.previewBox );
                    }
                    
                
                }.bind(this));
            
            }.bind(this));
            
        }.bind(this));
        
    },
    
    preview: function(e){
        this.togglePreviewBox(e);
    },
    
    doPreview: function( pPageRsn, pPluginId ){
        this.closePreviewBox();
        
        if( this.lastPreviewWin ){
            this.lastPreviewWin.close();
        }
        
        var url = this.previewUrls[pPluginId][pPageRsn];
        
        if( this.versioningSelect.getValue() != '-' ){
            url += '?kryn_framework_version_id='+this.versioningSelect.getValue()+'&kryn_framework_code='+pPluginId;
        }
        
        this.lastPreviewWin = window.open(url, '_blank');
        
    },
    
    setPreviewValue: function(){
        this.closePreviewBox();
    },
    
    closePreviewBox: function(){
        this.previewBoxOpened = false;
        this.previewBox.setStyle('display', 'none');
    },
    
    togglePreviewBox: function( e ){
    
        if( this.previewBoxOpened == true )
            this.closePreviewBox();
        else {
            if( e && e.stop ){
                document.body.fireEvent('click');
                e.stop();
            }
            this.openPreviewBox();
        }
    },
    
    openPreviewBox: function(){
    
        this.previewBox.setStyle('display', 'block');
        
        this.previewBox.position({
            relativeTo: this.previewBtn,
            position: 'bottomRight',
            edge: 'upperRight'
        });
        
        var pos = this.previewBox.getPosition();
        var size = this.previewBox.getSize();
        
        var bsize = window.getSize( $('desktop') );
        
        if( size.y+pos.y > bsize.y )
            this.previewBox.setStyle('height', bsize.y-pos.y-10);

        this.previewBoxOpened = true;
    },
    
    loadVersions: function(){
    	
        var req = this.generateItemParams();
        new Request.JSON({url: _path+'admin/'+this.win.module+'/'+this.win.code+'?cmd=getItem', noCache: true, onComplete: function(res){
            
        	if( res && res.versions ){
	        	this.item.versions = res.versions;
	        	this.renderVersionItems();
        	}
            
        }.bind(this)}).post(req);
    	
    },
    
    renderVersionItems: function(){
    	if( this.values.versioning != true ) return;
    	
        this.versioningSelect.empty();
        this.versioningSelect.chooser.setStyle('width', 210);
    	this.versioningSelect.add('-', _('-- LIVE --'));
        
        /*new Element('option', {
            text: _('-- LIVE --'),
            value: ''
        }).inject( this.versioningSelect );*/
        
        if( $type( this.item.versions) == 'array' ){
	        this.item.versions.each(function(version, id){
                this.versioningSelect.add( version.version, version.title );
	        }.bind(this));
        }
        
        if( this.item.version ){
        	this.versioningSelect.setValue( this.item.version );
        }
    	
    },

    render: function( pValues ){
        this.values = pValues;

        this.loader = new ka.loader().inject( this.container );
        this.loader.show();

        this.fields = {};
        
        this.renderMultilanguage();
        
        this.renderVersions();
        
        this.renderPreviews();
        
        this.renderFields();
        
        this.renderSaveActionBar();
        
        this.fireEvent('render');

        this.loadItem();
    },
    
    renderFields: function(){
    
        if( this.values.fields && $type(this.values.fields) != 'array'  ){
            //backward compatible
            this.form = new Element('div', {
                'class': 'ka-windowEdit-form'
            })
            .inject( this.container );
            
            if( this.values.layout ){
            	this.form.set('html', this.values.layout);
            }
            
        	var parser = new ka.parse( this.form, this.values.fields, {}, {win: this.win} );
            this.fields = parser.getFields();
            
        } else if( this.values.tabFields ){
            

            this.topTabGroup = this.win.addSmallTabGroup();
            
            this._panes = {};
            this._buttons = {};
            this.firstTab = '';
            this.fields = {};
            
            Object.each(this.values.tabFields, function( fields, title ){

                if( this.firstTab == '' ) this.firstTab = title;
                
                this._panes[ title ] = new Element('div', {
                    'class': 'ka-windowEdit-form',
                    style: 'display: none;'
                }).inject( this.container );
                
                if( this.values.tabLayouts && this.values.tabLayouts[title] )
                	this._panes[title].set('html', this.values.tabLayouts[title]);
                
                //this._renderFields( fields, this._panes[ title ] );

            	var parser = new ka.parse( this._panes[ title ], fields, {}, {win: this.win} );
                var pfields = parser.getFields();
                Object.append( this.fields, pfields );
                
                this._buttons[ title ] = this.topTabGroup.addButton(_(title), this.changeTab.bind(this,title));
            }.bind(this));
            this.changeTab(this.firstTab);
        }
    
    },
    
    renderVersions: function(){
    
        if( this.values.versioning == true ){
        	
        	/*this.versioningSelect = new Element('select', {
                style: 'position: absolute; right: '+versioningSelectRight+'px; top: 27px; width: 160px;'
            }).inject( this.win.border );*/
        	
        	
            var versioningSelectRight = 5;
            if( this.values.multiLanguage ){
                versioningSelectRight = 150;
            }
        
            this.versioningSelect = new ka.Select();
            this.versioningSelect.inject( this.win.border );
            this.versioningSelect.setStyle('width', 120);
            this.versioningSelect.setStyle('top', 26);
            this.versioningSelect.setStyle('right', versioningSelectRight);
            this.versioningSelect.setStyle('position', 'absolute');
        	
        	this.versioningSelect.addEvent('change', this.changeVersion.bind(this));
            
        }
    
    },
    
    renderMultilanguage: function(){
    
        if( this.values.multiLanguage ){
        	this.win.extendHead();
        	
            this.languageSelect = new ka.Select();
            this.languageSelect.inject( this.win.border );
            this.languageSelect.setStyle('width', 120);
            this.languageSelect.setStyle('top', 26);
            this.languageSelect.setStyle('right', 5);
            this.languageSelect.setStyle('position', 'absolute');
        	
        	
        	/*this.languageSelect = new Element('select', {
                style: 'position: absolute; right: 5px; top: 27px; width: 160px;'
            }).inject( this.win.border );*/

            this.languageSelect.addEvent('change', this.changeLanguage.bind(this));
            
            this.languageSelect.add('', _('-- Please Select --'));
            
            /*new Element('option', {
                text: _('-- Please select --'),
                value: ''
            }).inject( this.languageSelect );*/

            Object.each(ka.settings.langs, function(lang,id){
                /*new Element('option', {
                    text: lang.langtitle+' ('+lang.title+', '+id+')',
                    value: id
                }).inject( this.languageSelect );*/
                
                this.languageSelect.add( id, lang.langtitle+' ('+lang.title+', '+id+')' );
                
            }.bind(this));
            
            if( this.win.params )
                this.languageSelect.setValue( this.win.params.lang );
            
        }
        
    },
    
    changeVersion: function(){
    	var value = this.versioningSelect.getValue();
    	if( value == '-' )
    	   value = null;
    
    	this.loadItem( value );
    },

    changeLanguage: function(){
        Object.each(this.fields, function(item, fieldId){

        	if( item.field.type == 'select' && item.field.multiLanguage ){
        		item.field.lang = this.languageSelect.getValue();
                item.renderItems();
        	}
        }.bind(this));
    },
    
    changeTab: function( pTab ){
    	this.currentTab = pTab;
        Object.each(this._buttons, function(button,id){
            button.setPressed(false);
            this._panes[ id ].setStyle('display', 'none');
        }.bind(this));
        this._panes[ pTab ].setStyle('display', 'block');
        this._buttons[ pTab ].setPressed(true);
        this._buttons[ pTab ].stopTip();
    },
    
    renderSaveActionBar: function(){
        var _this = this;
        
        if( this.inline ) {
        
            this.actionsNavi = this.win.addButtonGroup();
            
            this.saveBtn = this.actionsNavi.addButton(_('Save'), _path+'inc/template/admin/images/button-save.png', function(){
                this._save();
            }.bind(this));
            
            
            if( this.values.previewPlugins ){
                this.previewBtn = this.actionsNavi.addButton(_('Preview'), _path+'inc/template/admin/images/icons/eye.png',
                    this.preview.bindWithEvent(this)
                );
            }
            
            if( this.values.versioning == true ){
                this.saveAndPublishBtn = this.actionsNavi.addButton(_('Save and publish'),
                        _path+'inc/template/admin/images/button-save-and-publish.png', function(){
                   _this._save( false, true );
                }.bind(this));
            }

            
        } else {
    
            this.actions = new Element('div', {
                'class': 'ka-windowEdit-actions'
            }).inject( this.container );
    
            this.exit = new ka.Button(_('Close'))
            .addEvent( 'click', this.checkClose.bind(this))
            .inject( this.actions );
    
            this.saveNoClose = new ka.Button(_('Save'))
            .addEvent('click', function(){
                _this._save();
            })
            .inject( this.actions );
    
            if( this.values.versioning == true ){
                this.save = new ka.Button(_('Save and publish'))
                .addEvent('click', function(){
                    _this._save( false, true );
                })
                .inject( this.actions );
            }
        
        }
    },
    
    retrieveData: function( pWithoutEmptyCheck ){
        
        var go = true;
        var req = {};
        
        Object.each(this.fields, function(item, fieldId){
            
            if( ['window_list'].contains(item.type) ) return;
        
            if( !pWithoutEmptyCheck && !item.isHidden() && !item.isOk() ){
            	
            	if( this.currentTab && this.values.tabFields ){
            		var currenTab2highlight = false;
            		Object.each(this.values.tabFields, function(fields,key){
            			Object.each(fields, function(field, fieldKey){
            				if( fieldKey == fieldId ){
            					currenTab2highlight = key;
            				}
            			})
            		});
            		
            		if( !pWithoutTabHiglighting && currenTab2highlight && this.currentTab != currenTab2highlight ){
            			var button = this._buttons[ currenTab2highlight ];
            			this._buttons[ currenTab2highlight ].startTip(_('Please fill!'));
            			button.toolTip.loader.set('src', _path+'inc/template/admin/images/icons/error.png');
            			button.toolTip.loader.setStyle('position', 'relative');
            			button.toolTip.loader.setStyle('top', '-2px');
            		}
            	}

                item.highlight();

                go = false;
            }
            var value = item.getValue();
            
            if( item.field.relation == 'n-n' )
                req[ fieldId ] = JSON.encode(value);
            else if( $type(value) == 'object' )
                req[ fieldId ] = JSON.encode(value);
            else
                req[ fieldId ] = value;

        }.bind(this));
        
        if( this.values.multiLanguage ){
        	req['lang'] = this.languageSelect.value;
        }
        
        if( go == false ){
            return false;
        }
        return req;
    
    },
    
    hasUnsavedChanges: function(){
    
        if( !this.ritem ) return false;
    
        var currentData = this.retrieveData( true );
        if( !currentData ) return true;
        
        var hasUnsaved = false;
        
        var blacklist = [];
        
        Object.each(currentData, function(value,id){
            if( blacklist.contains(id) ) return;
    
            if( typeOf(this.ritem[id]) == 'null' ) 
                this.ritem[id] = '';
            if( typeOf(value) == 'null' ) 
                value = '';

            if( value+"" != this.ritem[id] ){
                //logger(id+ ': '+value+' != '+this.ritem[id]);
                hasUnsaved = true;
            }
        }.bind(this));
        
        return hasUnsaved;
    },
    
    checkClose: function(){
    
        var hasUnsaved = this.hasUnsavedChanges();
        
        if( hasUnsaved ){
            this.win.interruptClose = true;
            this.win._confirm(_('There are unsaved data. Want to continue?'), function( pAccepted ){
                if( pAccepted ){
                    this.win.close();
                }
            }.bind(this));
        }
    
    },

    _save: function( pClose, pPublish ){
        var go = true;
        var _this = this;
        var req;

        var data = this.retrieveData();
        
        if( !data ) return;
        
        this.ritem = data;
        
        if( this.item ){
            req = Object.merge(this.item, data);
        }

        req.publish = (pPublish==true)?1:0;
        
        if( go ){
                
            if( this.inline ) {
                if( pPublish ){
                    this.saveAndPublishBtn.startTip( _('Save ...') );
                } else {
                    this.saveBtn.startTip( _('Save ...') );
                }
            } else {
                this.loader.show();
                if( !pClose && this.saveNoClose ){
                    this.saveNoClose.startTip(_('Save ...'));
                }
            }
            
            if( _this.win.module == 'users' && (_this.win.code == 'users/edit/'
                || _this.win.code == 'users/edit'
                || _this.win.code == 'users/editMe'
                || _this.win.code == 'users/editMe/'
                ) ){
                if( !ka.settings['user'] ) ka.settings['user'] = {};
                ka.settings['user']['adminLanguage'] = req['adminLanguage'];
            }
            
            if( this.win.params ){
            
                if( !this.windowAdd ){
        	        this.values.primary.each(function(prim){
        	            req[ prim ] = this.win.params.values[prim];
        	        }.bind(this));
    	        }
    	        
    	        if( this.win.params.relation_params ){
        	        Object.each(this.win.params.relation_params, function(value,id){
        	           req[ id ] = value;
        	        });
        	        req['_kryn_relation_table'] = this.win.params.relation_table;
        	        req['_kryn_relation_params'] = this.win.params.relation_params;
    	        }
    	    }
            
            new Request.JSON({url: _path+'admin/'+this.win.module+'/'+this.win.code+'?cmd=saveItem', noCache: true, onComplete: function(res){

                window.fireEvent('softReload', this.win.module+'/'+this.win.code.substr(0, this.win.code.lastIndexOf('/')) );
            	
            	if( this.inline ) {
                    if( pPublish ){
                        this.saveAndPublishBtn.stopTip( _('Saved') );
                    } else {
                        this.saveBtn.stopTip( _('Saved') );
                    }
                } else {
                    this.loader.hide();
                }
                
                if( !pClose && this.saveNoClose ){
                    this.saveNoClose.stopTip(_('Done'));
                }
                
                if( res.version_rsn ){
                    this.item.version = res.version_rsn;
                }
                
            	if( this.values.loadSettingsAfterSave == true ) ka.loadSettings();
            	if( this.values.load_settings == true ) ka.loadSettings();
                
                this.previewUrls = res.preview_urls;
                
                this.fireEvent('save', [req, res, pPublish]);
                
                // Before close, perform saveSuccess
                this._saveSuccess();
                
            	if( (!pClose || this.inline ) && this.values.versioning == true ) this.loadVersions();
                
                if( pClose )
                    this.win.close();
                    
            }.bind(this)}).post(req);
        }
    },
    
    _saveSuccess: function()
    { }

});