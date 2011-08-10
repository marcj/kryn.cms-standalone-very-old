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
        new Request.JSON({url: _path+'admin/backend/window/loadClass/', noCache: true, onComplete: function(res){
            this.render( res );
        }.bind(this)}).post({ module: this.win.module, 'code': this.win.code });
    },
    
    generateItemParams: function( pVersion ){
    	var req = $H({});
	   
	    req.include( 'module', this.win.module );
	    req.include( 'code', this.win.code );
	    
	    if( pVersion )
	    	req.version = pVersion;
	
	    if( this.win.params ){
	        this.values.primary.each(function(prim){
	            req.include( 'primary:'+prim, this.win.params.values[prim] );
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
        this.lastRq = new Request.JSON({url: _path+'admin/backend/window/loadClass/getItem', noCache: true, onComplete: function(res){
            
        	_this._loadItem( res );
            
        }}).post(req);
    },

    addField: function( pField, pFieldId, pContainer ){
    
        if( !pField ) return;
    
        if( pField.type == 'wysiwyg' && !this.windowAdd ){
            pField.withOutTinyInit = true;
        }
        pField.win = this.win;
        pField.label = _(pField.label);
        pField.desc = _(pField.desc);
        
        if( this.languageSelect && pField.multiLanguage )
        	pField.lang = this.languageSelect.getValue();
        
        var field = new ka.field(pField, pFieldId );
        field.inject( pContainer );

        if( pField.type == 'wysiwyg' && this.windowAdd ){
            //var contentCss = _path+"inc/template/css/kryn_tinyMceContentElement.css";
            //initResizeTiny( field.lastId, contentCss );
            ka._wysiwygId2Win.include( field.lastId, this.win );
            initResizeTiny( field.lastId, _path+'inc/template/css/kryn_tinyMceContent.css' );
        }

        this.fields.include( pFieldId, field );
        this._fields.include( pFieldId, pField );
        return field;
    },


    _loadItem: function( pItem ){
        this.item = pItem;
        logger(pItem);
        this.previewUrls = pItem.preview_urls;
        
        this.fields.each(function(field, fieldId){
            try {
            	
                if( $type(pItem.values[fieldId]) == false )
                    field.setValue( '' );
                else if( !this._fields[fieldId].startempty )
                    field.setValue( pItem.values[fieldId] );

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
        
        
        if( this.values.multiLanguage ){
        	this.languageSelect.setValue( this.item.values.lang );
        	this.changeLanguage();
        }

        this.renderVersionItems();
    
        this.loader.hide();
        this.fireEvent('load', pItem);
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
      
        Object.each(this.values.previewPlugins, function(item,pluginId){
        
            var title = _(ka.settings.configs[this.win.module].plugins[pluginId][0]);
            
            
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
           
            /*.addEvent('click', function(){
                this.setPreviewValue( pluginId, true);
            }.bind(this))
            */
            
            
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
        
        logger(this.previewUrls);
        this.lastPreviewWin = window.open(this.previewUrls[pPluginId][pPageRsn], '_blank');
        
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
        new Request.JSON({url: _path+'admin/backend/window/loadClass/getItem', noCache: true, onComplete: function(res){
            
        	if( res && res.versions ){
	        	this.item.versions = res.versions;
	        	this.renderVersionItems();
        	}
            
        }.bind(this)}).post(req);
    	
    },
    
    renderVersionItems: function(){
    	if( this.values.versioning != true ) return;
    	
        this.versioningSelect.empty();
    	
    	
    	this.versioningSelect.add('', _('-- LIVE --'));
        
        /*new Element('option', {
            text: _('-- LIVE --'),
            value: ''
        }).inject( this.versioningSelect );*/
        
        if( $type( this.item.versions) == 'array' ){
	        this.item.versions.each(function(version, id){
    	
                this.versioningSelect.add( version.version, version.title );
	            /*new Element('option', {
	                 text: version.title,
	                 value: version.version
	             }).inject( this.versioningSelect );
	            */
	        	
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

        this.fields = $H({});
        this._fields = $H({});
        
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
            
            var target = this.form;
            
            if( this.values.layout ){
            	this.form.set('html', this.values.layout);
            }
            
            $H(this.values.fields).each(function(field, fieldId){

            	if( field.target )
            		field.target = '#'+field.target;
            	
                if( this.values.layout ){
                	target = this.form.getElement( field.target || '#default' );
                	this.win._alert(_('Layout is defined but target is invalid for field %s'.replace('%s', fieldId)));
                }
            	
                this.addField( field, fieldId, target );
            }.bind(this));
            
        } else if( this.values.tabFields ){
            
            this.topTabGroup = this.win.addSmallTabGroup();
            
            this._panes = {};
            this._buttons = $H({});
            this.firstTab = '';
            
            $H(this.values.tabFields).each(function(fields,title){
                if( this.firstTab == '' ) this.firstTab = title;
                this._panes[ title ] = new Element('div', {
                    'class': 'ka-windowEdit-form',
                    style: 'display: none;'
                }).inject( this.container );
                
                if( this.values.tabLayouts && this.values.tabLayouts[title] )
                	this._panes[title].set('html', this.values.tabLayouts[title]);
                
                this._renderFields( fields, this._panes[ title ] );
                
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
            this.versioningSelect.setStyle('top', 29);
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
            this.languageSelect.setStyle('top', 29);
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

            $H(ka.settings.langs).each(function(lang,id){
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
    	this.loadItem( value );
    },

    changeLanguage: function(){
    	var newFields = {};
        this.fields.each(function(item, fieldId){

        	if( item.field.type == 'select' && item.field.multiLanguage ){
        		item.field.lang = this.languageSelect.getValue();
        		var value = item.getValue();
        		var field = new ka.field( item.field );
        		field.inject( item.main, 'after' );
        		item.destroy();
        		field.setValue( value );
        		newFields[fieldId] = field;
        	}
        }.bind(this));
        
        $H(newFields).each(function(item,fieldId){
        	this.fields.set(fieldId, item);
        }.bind(this));
    },
    
    _renderFields: function( pFields, pContainer, pParentField ){
        if(!pFields.each) pFields = $H(pFields);
        
        
        pFields.each(function(field,id){

        	if( field.target )
        		field.target = '#'+field.target;

        	var target = pContainer.getElement( field.target || '#default' );
            if( !target )
            	target = pContainer;
        	
            var fieldOnj = this.addField( field, id, target );

            if( pParentField && field.needValue ){
            	
            	fieldOnj.hide();
            	pParentField.addEvent('change', function( pValue ){
            		if( pValue == field.needValue ){
            			fieldOnj.show();
            		} else {
            			fieldOnj.hide();
            		}
            	});
            	pParentField.fireEvent('change', pParentField.getValue());
            }
            
            if( field.depends ){
                var depends = new Element('div', {
                	style: 'margin-left: 26px; padding: 3px; border-left: 1px dotted gray'
                }).inject( target );
                this._renderFields( field.depends, depends, fieldOnj );
            }
        }.bind(this));
    },

    changeTab: function( pTab ){
    	this.currentTab = pTab;
        this._buttons.each(function(button,id){
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
            
            
            this.previewBtn = this.actionsNavi.addButton(_('Preview'), _path+'inc/template/admin/images/icons/eye.png',
                this.preview.bindWithEvent(this)
            );
            
            if( this.values.versioning == true ){
                this.saveAndPublishBtn = this.actionsNavi.addButton(_('Save and publish'),
                        _path+'inc/template/admin/images/button-save-and-publish.png', function(){
                   _this._save( false, true );
                }.bind(this));
            }
            
            this.actionsNaviDel = this.win.addButtonGroup();
            this.actionsNaviDel.addButton(_('Delete'), _path+'inc/template/admin/images/remove.png', function(){
               
            }.bind(this));

            
        } else {
    
            this.actions = new Element('div', {
                'class': 'ka-windowEdit-actions'
            }).inject( this.container );
    
            this.exit = new ka.Button(_('Cancel'))
            .addEvent( 'click', function(){
                _this.win.close();
            })
            .inject( this.actions );
    
            this.saveNoClose = new ka.Button(_('Save'))
            .addEvent('click', function(){
                _this._save();
            })
            .inject( this.actions );
    
            this.save = new ka.Button(_('Save and close'))
            .addEvent('click', function(){
                _this._save( true );
            })
            .inject( this.actions );
        
        }
    },

    _save: function( pClose, pPublish ){
        var go = true;
        var _this = this;
        var req = $H();
        
        if( this.item )
            req = $H(this.item.values);
        
        req.include( 'module', this.win.module );
        req.include( 'code', this.win.code );
        
        
        this.fields.each(function(item, fieldId){
            if( !item.isHidden() && !item.isOk() ){
            	
            	if( this.currentTab && this.values.tabFields){
            		var currenTab2highlight = false;
            		$H(this.values.tabFields).each(function(fields,key){
            			$H(fields).each(function(field, fieldKey){
            				if( fieldKey == fieldId ){
            					currenTab2highlight = key;
            				}
            			})
            		});
            		
            		if( currenTab2highlight && this.currentTab != currenTab2highlight ){
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
                req.set( fieldId, JSON.encode(value) );
            else if( $type(value) == 'object' )
                req.set( fieldId, JSON.encode(value) );
            else
                req.set( fieldId, value );
        }.bind(this));
        
        if( this.values.multiLanguage ){
        	req.set('lang', this.languageSelect.value);
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
                ka.settings.get('user').set('adminLanguage', req.get('adminLanguage') );
            }
            
            if( this.win.params ){
    	        this.values.primary.each(function(prim){
    	            req.include( 'primary:'+prim, this.win.params.values[prim] );
    	        }.bind(this));
    	    }
            
            new Request.JSON({url: _path+'admin/backend/window/loadClass/saveItem', noCache: true, onComplete: function(res){
                
                if( !_this.inline )
                	ka.wm.softReloadWindows( this.win.module, this.win.code.substr(0, this.win.code.lastIndexOf('/')) );
            	
            	if( _this.inline ) {
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
                
            	if( this.values.loadSettingsAfterSave == true ) ka.loadSettings();
                
                this.previewUrls = res.preview_urls;
                // Before close, perform saveSuccess
                this.fireEvent('save', [req, res]);
                
                this._saveSuccess();
                
            	if( !pClose && this.values.versioning == true ) this.loadVersions();
                
                if( pClose )
                    this.win.close();
                    
            }.bind(this)}).post(req);
        }
    },
    
    _saveSuccess: function()
    { }

});