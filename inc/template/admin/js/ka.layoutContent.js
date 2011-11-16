ka.layoutContent = new Class({

    Implements: Events,
	
	noAccess: false,
    isRemoved: false,
    content: {},
    
    selected: false,
	
    initialize: function( pContent, pContainer, pLayoutBox ){
        
        this.content = Object.clone(pContent);
        this.container = pContainer;

        if( this.content.type == 'picture' ){
            var options = this.content.content.split('::');
            this.content.content = '<img src="'+options[1]+'" ';
            var properties = typeOf(options[0]) == 'string' ? JSON.decode(options[0]) : {};
            
            if( properties.alt )
                this.content.content += ' alt="'+properties.alt+'" ';
            if( properties.title )
                this.content.content += ' title="'+properties.title+'" ';
                
            this.content.content += ' />';
            
            
            if( properties.link )
                this.content.content = '<a href="'+properties.link+'">'+this.content.content+'</a>';
                
            if( properties.align )
                this.content.content = '<div style="text-align: '+properties.align+'">'+this.content.content+'</div>';
            
            this.content.type = 'text';
        }

        this.w = this.container.getWindow();
        this.editMode = 0;
        this.layoutBox = pLayoutBox;

        this.langs = {
            text: _('Text and Picture'),
            template: _('Template'),
            layoutelement: _('Layout Element'),
            navigation: _('Navigation'),
            pointer: _('Pointer'),
            html: _('HTML'),
            php: _('PHP'),
            plugin: _('Plugin')
        };
        
        if( this.layoutBox.pageInst.page && !ka.checkPageAccess( this.layoutBox.pageInst.page.rsn, 'content-'+pContent.type ) ){
        	this.noAccess = true;
        }

        this.renderToolbar();

        this.renderBox();
        
        window.document.body.addEvent('deselect-content-elements', this.deselect.bind(this));
        
        if( this.win )
            this.win.border.addEvent('deselect-content-elements', this.deselect.bind(this));
    },
    
    renderToolbar: function(){
    
    
        var target = this.w.document.body;
        if( this.container.getParent('.kwindow-border') ){
            target = this.container.getParent('.kwindow-border');
        }
    
        this.toolbar = new Element('div', {
            'class': ' ka-normalize ka-layoutContent-toolbar SilkTheme'
        })
        .addEvent('mousedown', function(e){
            if( e.target && ( e.target.get('tag') != 'input' && e.target.get('tag') != 'select') )
                e.preventDefault();
        })
        .addEvent('click', function(e){
            e.stopPropagation();
        }).inject( target )
        
        this.toolbarArrow = new Element('img', {
            src: _path+'inc/template/admin/images/ka-tooltip-corner-top.png',
            'class': 'ka-layoutContent-toolbar-arrow'
        }).inject( this.toolbar );
    
        this.toolbarWysiwygContainer = new Element('div', {
            'class': 'ka-layoutContent-toolbar-wysiwyg'
        }).inject( this.toolbar );
        
        this.toolbarTitleContainer = new Element('div', {
            'class': 'ka-layoutContent-toolbar-title'
        }).inject( this.toolbar );
        
        this.toolbarTitleContainerSelects = new Element('div', {
            'class': 'ka-layoutContent-toolbar-title-selects'
        }).inject( this.toolbarTitleContainer );
        
        this.w.addEvent('scroll', this.positionToolbar.bind(this));
        this.w.addEvent('resize', this.positionToolbar.bind(this));
        
        this.iTitle = new Element('input', {
            'class': 'ka-normalize ka-layoutContent-title'
        })
        .addEvent('blur', function(){
            if( this.iTitle.value == '' ){
                this.iTitle.store('empty', true);
            } else {
                this.iTitle.store('empty', false);
            }
            this.iTitleSetBlankText();
        }.bind(this))
        .addEvent('focus', function(){
            if( this.retrieve('empty') == true ){
                this.value = '';
                this.setStyle('color', '');
            }
        })
        .addEvent('keyup', function(){
            this.fireTemplateRefresh();
        }.bind(this))
        .inject( this.toolbarTitleContainer );
        
        this.iTitle.store('empty', true);
        this.iTitleSetBlankText();
        
        this.imgAccess = new Element('img', {
            src: _path+'inc/template/admin/images/admin-pages-viewType-versioning.png',
            'class': 'ka-layoutContent-toolbar-access',
            title: _('Access settings')
        })
        .addEvent('click', this.openAccessDialog.bind(this))
        .inject( this.toolbarTitleContainer );
        
        this.sType = new ka.Select()
        .addEvent('change', this.changeType.bind(this))
        .inject( this.toolbarTitleContainerSelects );
        
        document.id(this.sType).setStyle('width', 65);
        
        Object.each( this.langs, function(item, id){
            this.sType.add( id, item );
        }.bind(this));
        
        this.sTemplate = new ka.Select()
        .addEvent('change', function(){
            this.content.template = this.sTemplate.getValue();
            this.setDivContent();
        }.bind(this))
        .inject( this.toolbarTitleContainerSelects );
        document.id(this.sTemplate).setStyle('width', 65);
        
        this.sTemplate.add( '-', _('-- no layout --') );

        Object.each( ka.settings.contents, function(la, key){
            
            this.sTemplate.addSplit( key );
             
            Object.each( la, function( layoutFile,layoutTitle ){
                this.sTemplate.add( layoutFile, _(layoutTitle) );
            }.bind(this))

        }.bind(this));
        
        this.optionsImg = new Element('div', {
            'class': 'ka-layoutContent-toolbar-images'
        }).inject( this.toolbarTitleContainer );

        this.renderTitleActions();
    
    },
    
    fireTemplateRefresh: function(){
    
        this.content.title = this.iTitle.value;
        
        if( this.templateRefreshTimeout )
            clearTimeout( this.templateRefreshTimeout );

        this.templateRefreshTimeout = this.setDivContent.delay(200, this);
      
    },

    openAccessDialog: function(){
    
        var win = this.window.win;
        if( this.container.getParent('.kwindow-border') ){
            win = this.container.getParent('.kwindow-border').retrieve('win');
        }
    
        var dialog = win.newDialog();
        
        dialog.setStyle('height', 250);
        dialog.setStyle('width', 360);
        dialog.center();
       
       
        var fields = {
        
            "unsearchable": {
                label: _('Unsearchable'),
                desc: _('Hides this element in the search index'),
                type: "checkbox"
            },
            
            "access_from": {
                label: _('Release at'),
                type: 'datetime'
            },
            "access_to": {
                label: _('Hide at'),
                type: 'datetime'
            },
            
            "access_from_groups": {
                label: _('Limit access to groups'),
                type: 'textlist',
                store: 'admin/backend/stores/groups'
            }
        
        };
       
        this.accessFields = new ka.parse( dialog, fields );
        
        var groups = this.content.access_from_groups;
        if( typeOf(groups) == 'string' )
             groups = groups.split(',');

        this.accessFields.setValue({
            unsearchable: this.content.unsearchable,
            access_from: this.content.access_from,
            access_to: this.content.access_to,
            access_from_groups: groups
        });

        var bottom = new Element('div', {
            'class': 'ka-kwindow-prompt-bottom'
        }).inject( dialog );
        
        new ka.Button(_('Cancel'))
        .addEvent('click', dialog.close.bind(dialog))
        .inject( bottom );
        
        new ka.Button(_('Ok'))
        .addEvent('click', function(){
            
            var value = this.accessFields.getValue();
            
            this.content.unsearchable = value.unsearchable;
            this.content.access_from = value.access_from;
            this.content.access_to = value.access_to;
            this.content.access_from_groups = value.access_from_groups;
            
            delete this.accessFields;

            dialog.close();

        }.bind(this))
        .inject( bottom );
       
        /* 
        dialog.bottom.destroy();
        dialog.content.destroy();
        */

    
    },
    
    showToolbar: function(){

        if( this.toolbar.getParent() ) return;

        var target = this.w.document.body;
        if( this.container.getParent('.kwindow-border') ){
            target = this.container.getParent('.kwindow-border');
        }
        this.toolbar.inject( target );
        this.positionToolbar();
    },
    
    positionToolbar: function(){

        if( !this.toolbar.getParent() ) return;

        var pos = this.main.getPosition( this.toolbar.getParent() );
        var size = this.main.getSize();
        
        var size = this.toolbar.getSize();
        var wsize = this.toolbar.getParent().getSize();
        var scroll = this.toolbar.getParent().getScroll();
        
        var npos = {
            'left': pos.x-3,
            'top': pos.y+4
        };
        
        npos['top'] -= this.toolbar.getSize().y+7;
        
        //if not in viewport
        if( npos['top']+size.y > wsize.y+scroll.y ){
            npos['top'] = wsize.y+scroll.y-size.y;
        }
        if( npos['top'] < scroll.y ){
            npos['top'] = scroll.y;
        }
        
        if( npos['left']+size.x > wsize.x+scroll.x ){
            npos['left'] = wsize.x+scroll.x-size.x;
        }
        
        this.toolbar.setStyles(npos);
        
        var diff = pos.x-npos['left'];
        this.toolbarArrow.setStyle('left', diff + size.x/5-10);
        
    },
    
    hideToolbar: function(){
        this.toolbar.dispose();
    },

    renderBox: function(){
        
        var pos = null;
        if( this.content['new'] ||  this.content['top'] ){
            pos = 'top';
        }

        var toElement = this.container;
        if( this.content['afterElement'] ){
            toElement = this.content['afterElement'];
            pos = 'after';
        }

        this.main = new Element('div', {
            'class': 'ka-layoutContent-main'
        }).inject( toElement, pos );
        
        this.window = this.main.getWindow();
        
        this.main.addEvent('click', function(e){
            
        	window.document.body.fireEvent('deselect-content-elements');
            
            if( this.win )
                this.win.border.fireEvent('deselect-content-elements');
            
            this.select();
            e.stopPropagation();

        }.bind(this));

        this.main.store( 'layoutContent', this );
        this.main.layoutContent = this;
        
        this.div = new Element('div', {
            'class': 'ka-layoutContent-div'
        }).inject( this.main );
        
        this.body = new Element('div').inject( this.div );

        this.dataToView();
        
        if( this.content['new'] ||this.content.toEdit ){
            this.select();
        }
        
        this.hideToolbar();

    },
    
    dataToView: function(){
    	
    	this.sType.setValue( this.content.type );
    	this.sTemplate.setValue( this.content.template );
    	
    	this.iTitle.value = this.content.title?this.content.title:"";
    	this.iTitle.store('empty', this.content.title==""||!this.content.title?true:false);
        this.iTitleSetBlankText();

        this.changeType();
    
    },
    
    iTitleSetBlankText: function(){
    
        if( this.iTitle.retrieve('empty') == true ){
            this.iTitle.value = _('Element title');
            document.id(this.iTitle).setStyle('color', 'gray');
        } else {
            document.id(this.iTitle).setStyle('color', '');
        }
    
    },

    hideBubbleBox: function( pNow ){
        if( pNow ){
            this.bubbleBox.setStyle('opacity', 0);
            this.bubbleBox.setStyle('display', 'none'); 
        } else {
            this.bubbleBox.set('tween', {onComplete: function(){
                this.bubbleBox.setStyle('display', 'none'); 
            }.bind(this)});
            this.bubbleBox.tween('opacity', 0);
        }
    },

    setBubbleContent: function(){

        var title = this.content.title;
        if( title == ""){
            title = _('[No title]');
        }

        this.bubbleBoxContent.set('html', '<div style="font-weight: bold;">'+title+'</div>'+
                _('Template file')+': '+ this.getTemplateTitle(this.content.template)+'<br />'+
                '');

    },

    renderTitleActions: function(){
        var p = _path+'inc/template/admin/images/icons/';

        if( !this.content.noActions ){
	        
	        this.hideImg = new Element('img', {
	            src: p+'lightbulb.png',
	            title: _('Hide/Unhide')
	        })
	        .inject( this.optionsImg );
	
	        new Element('img', {
	            src: p+'page_copy.png',
	            title: _('Copy')
	        })
	        .addEvent('click', this.copy.bindWithEvent(this))
	        .inject( this.optionsImg );
	
	        new Element('img', {
	            src: p+'page_paste.png',
	            title: _('Paste')
	        })
	        .addEvent('click', this.pasteAfter.bindWithEvent(this))
	        .inject( this.optionsImg );
	        
	        new Element('img', {
	            src: p+'arrow_up.png',
	            title: _('Move up')
	        })
	        .addEvent('click', this.toUp.bindWithEvent(this))
	        .inject( this.optionsImg );
	
	        new Element('img', {
	            src: p+'arrow_down.png',
	            title: _('Move down')
	        })
	        .addEvent('click', this.toDown.bindWithEvent(this))
	        .inject( this.optionsImg );
	
	
	        new Element('img', {
	            src: p+'arrow_out.png',
	            'class': 'ka-layoutContent-mover',
	            style: 'cursor: move',
	            title: _('Drag and drop')
	        })
	        .addEvent('click', function(e){
	            if( e ) e.stop();
	        })
	        .inject( this.optionsImg );
	
	        
	        if( !this.noAccess ){
		        new Element('img', {
		            src: p+'delete.png',
		            title: _('Delete')
		        })
		        .addEvent('click', this.remove.bindWithEvent(this))
		        .inject( this.optionsImg );
	        }
	        
	        if( !this.noAccess ){
	        	this.hideImg.addEvent('click', this.toggleHide.bindWithEvent(this))
	        }
        }
    },

    /*
    * ACTIONS
    */

    remove: function(e){

    	this.deselect();

    	this.isRemoved = true;
        this.fireEvent('remove');

        this.main.destroy();
        this.content = null;

        if( e ){
            e.stop();
            e.stopPropagation();
        }
    },

    toUp: function(e){
        if( e ) e.stop();
        var previous = this.main.getPrevious();
        if( previous )
            this.main.inject( previous, 'before' );
        this.positionToolbar();
    },

    toDown: function(e){
        if( e ) e.stop();
        var next = this.main.getNext();
        if( next )
            this.main.inject( next, 'after' );
        this.positionToolbar();
    },

    copy: function( e ){
       if( e ) e.stop();
       var title = (this.content.title=='')?_('[No title]'):this.content.title;
       this.content['new'] = false;
       this.content['top'] = false;
       ka.setClipboard( 'Seiteninhalt \''+title+'\' kopiert.', 'pageItem', this.content );
    },

   toggleHide: function( e ){
        if( e ) e.stop();
        if( this.content.hide == 1 ){
            this.content.hide = 0;
        } else {
            this.content.hide = 1;
        }
        this.setHide( this.content.hide );
    },

    pasteAfter: function( e ){
        if( e ) e.stop();
        var clip = ka.getClipboard();
        content = new Hash(clip.value);
        if( clip.type == 'pageItem' ){
            content.rsn = null;
            content['new'] = false;
            var n = new ka.layoutContent( content, this.container, this.layoutBox );
            n.main.inject( this.main, 'after' );
            n.main.highlight();
            this.container.retrieve('layoutBox').contents.include( n );
       }
        if( clip.type == 'pageItems' ){
            var arr = $A(clip.value);
            for( var i = arr.length-1; i >= 0; i-- ){
                var content = arr[i];
                content.rsn = null;
                content['new'] = false;
                var n = new ka.layoutContent( content, this.contentContainer, this.layoutBox );
                n.main.inject( this.main, 'after' );
                n.main.highlight();
                this.container.retrieve('layoutBox').contents.include( n );
            };
        }
        var layoutBox = this.container.retrieve('layoutBox');
        layoutBox.initSort();
    },

    toData: function( pForce ){
    	
    	if( this.noAccess ) return;
        
        if( !this.content ) this.content = {};
        
        if( !pForce && !this.isSelected() ) return;

        if( this.content.type == 'layoutelement' ){
        	this.saveLayoutElement();
        }

        
    	if( this.iTitle.retrieve('empty') !== true ){
            this.content.title = this.iTitle.value;
    	} else {
            this.content.title = "";
    	}

        this.content.type = this.sType.getValue();
        this.content.template = this.sTemplate.getValue();

        
        switch( this.content.type ){
        case 'plugin':
            if( this.pluginChooser ){
                this.content.content = this.pluginChooser.getValue();
            }
            break;

        case 'picture':
        case 'text':
        case 'html':
        case 'php':
            if( this.textarea ){
                this.content.content = this.textarea.value;
            }
            break;
        case 'pointer':
            break;
        case 'navigation':
            this.content.content.template = this.navigationTemplate.getValue();
            break;
        case 'template':
            this.content.content = this.templateFileField.getValue();
            break;
        }
    },

    getTemplateTitle: function( pFile ){
        if( pFile == "" ) return _('No layout');
        var res = 'not-found';
        $H(ka.settings.contents).each(function(la, key){
            $H(la).each(function(layoutFile,layoutTitle){
                if( pFile == layoutFile )
                    res = layoutTitle;
            });
        });
        return res;
    },


    //toEditMode
    changeType: function(){

    	this.oldType = this.content.type;
        this.content.type = this.sType.getValue();
        this.setDivContent( true );

    },
    
    saveLayoutElement: function(){
    	
    	if( !this.layoutElement ) return;
    	
    	var layout = this.layoutElementSelect.getValue();
    	var contents = this.layoutElement.getValue();
    	
    	this.content.content = JSON.encode({
    		layout: layout,
    		contents: contents
    	});
    	
    },
    
    toLayoutElement: function(){

        //this.layoutBox.pageInst.elementPropertyFields.eLayoutSelect.removeEvents();
        
        this.toolbarWysiwygContainer.empty();
        
        new Element('span', {
            text: _('Layout')
        }).inject( this.toolbarWysiwygContainer );

        this.layoutElementSelect = new ka.Select();
        
        
		Object.each(ka.settings.configs, function(config, key){

			if( config['themes'] ){

				Object.each(config['themes'], function(options, themeTitle){
					
					if( options['layoutElement'] ){
						
						this.layoutElementSelect.addSplit( themeTitle );
			    		
						Object.each(options['layoutElement'], function(templatefile, label){
                            this.layoutElementSelect.add( templatefile, label );
						}.bind(this));
				
					}

				}.bind(this));
			}
		}.bind(this));  
        
        this.layoutElementSelect.inject( this.toolbarWysiwygContainer );
        
		this._loadLayoutElement( true );
    },
    
    _loadLayoutElement: function( pInit ){
    	
    	var content = false;
    	if( this.content.content ){
    		try {
    			content = JSON.decode(this.content.content);
    		} catch( e ){
    			content = false;
    		}
    	}
    	
    	
    	if( this.oldType == this.content.type && this.layoutElement ){
    		//change layout possible
    		this.layoutElement.loadTemplate( newLayout );
    		return;
    	}
    	
    	var contents = false;
    	if( content )
    		contents = content.contents;

    	this.body.set('html', _('Loading ...'));
    	
    	if( !content ){
    		content = {
				layout: this.layoutElementSelect.getValue(),
				contents: {}
    		}
    	}
    	
    	this.layoutElement = new ka.layoutElement( this.body, content.layout, this.win );

		if( contents ){
	    	this.layoutElementSelect.setValue(content.layout);
			this.layoutElement.setValue( contents );
		}
		
    },

    type2HTML: function(){
        this.body.empty();
        
        var p = new Element('div', {
            style: 'margin-right: 4px;'
        }).inject( this.body );
        
        this.textarea = new Element( 'textarea', {
            style: 'width: 100%; overflow: hidden; font-family: "Verdana, sans"; outline: 0; margin: 0px; padding: 1px; height: 40px; line-height: 14px; font-size: 13px; border: 1px solid silver;',
            'class': 'text', 
            text: this.content.content
        }).inject( p );
        
        this.dummy = new Element('div', {style: 'white-space: pre;font-family: "Verdana, sans"; word-wrap: break-word; border: 1px solid silver; background-color: white; line-height: 14px; font-size: 13px; position: absolute; left: 0px; top: 0px;'})
        .inject( document.hidden );

        this.dummy.setStyle('width', this.body.getSize().x-3);
        
        var adjustHeight = function(){
        
            this.dummy.set('text', this.textarea.value);
            var height = this.dummy.getSize().y;

            if( height > 25 )
                this.textarea.setStyle('height', height+35);
            
        }.bind(this);
        
        this.textarea.addEvent('keyup', adjustHeight.bind(this));
        this.textarea.addEvent('keydown', adjustHeight.bind(this));
        this.textarea.addEvent('keypress', adjustHeight.bind(this));
        this.textarea.addEvent('change', adjustHeight.bind(this));
        
        this.textarea.fireEvent('keyup');
    },

    type2Navi: function(){    	
        var _this = this;
        
        this.body.empty();
        
        
        try {
            if( $type(_this.content.content) == 'string' )
                _this.content.content = JSON.decode(_this.content.content);
            if( $type(_this.content.content) != 'object' )
                _this.content.content = {};
        } catch(e){
            _this.content.content = {};
        }

        var templateNavi = new ka.field(
            {label: _('Navigation template'), type: 'select', small: 1}
        ).inject( this.body );
        
        templateNavi.select.addEvent('click', function(e){
            e.stopPropagation();
        });
        this.navigationTemplate = templateNavi;

        templateNavi.addEvent('change', function( pValue ){
            _this.content.content.template = templateNavi.input.value;
        });

        Object.each(ka.settings.navigations, function(la, key){
            
            templateNavi.select.addSplit( key );
            

            Object.each(la, function(layoutFile,layoutTitle){
                if( limitLayouts && limitLayouts.length > 0 && !limitLayouts.contains( layoutFile ) ) return;
                
                templateNavi.select.add( layoutFile, layoutTitle );
                
            });
    
        }.bind(this));

        templateNavi.setValue( this.content.content.template );

        var field = new ka.field(
            {label: _('Entry point'), type: 'page', empty: false, small: 1, onlyIntern: true}
        ).inject( this.body );

        if( this.content.content.entryPoint )
            field.setValue( this.content.content.entryPoint );

        field.addEvent('change', function( pValue ){
            this.content.content.entryPoint = pValue;
        }.bind(this));
        
        new Element('div', {style: 'clear: both'}).inject( this.body );
    },

    type2Pointer: function(){

        var field = new ka.field(
            {label: _('Choose deposit'), type: 'pageChooser', empty: false, small: true}
        ).inject( this.body );

        field.setValue(this.content.content);
        
        var _this = this;

        field.addEvent('change', function( pPage ){
            _this.content.content = pPage;
            _this.setDivPointer();
        });
    },

    renderNavigationChoosenPage: function(){
        this.navigationChoosenPageDiv.set('html', this.navigationChoosenPage.title+': '+this.navigationChoosenPage.realUrl);
    },

    type2Plugin: function(){
    
        var win = this.window.win;
        if( this.container.getParent('.kwindow-border') ){
            win = this.container.getParent('.kwindow-border').retrieve('win');
        }
    
        var dialog = win.newDialog();
        
        this.pluginChooser = new ka.pluginChooser( this.content.content, dialog );
        
        this.pluginChooser.addEvent('ok', function(){
            this.content.content = this.pluginChooser.getValue();
            this.setDivPlugin();
            this.deselect();
            dialog.close();
        }.bind(this));
        
        this.pluginChooser.addEvent('cancel', function(){
            dialog.close();
        }.bind(this));
        
        this.pluginChooser.addEvent('loadOptions', function(){
            dialog.position();
        }.bind(this));
        
        dialog.setStyle('height', 300);
        dialog.setStyle('width', 600);
        dialog.center();
        
        dialog.bottom.destroy();
        dialog.content.destroy();
        
    },

    type2Template: function(){

        this.body.empty();
        
        var small = 0;
        var width = this.body.getSize().x;
        
        if( width < 200 )
            small = 1;

        this.templateFileField = new ka.field({
            label: _('Template file'), type: 'file', small: small
        })
        .inject( this.body );
        
        if( small == 1 ){
            new Element('div', {
                'style': 'clear: both;'
            }).inject( this.body );
            
            document.id( this.templateFileField ).setStyle('width', width-20);
            var title = document.id( this.templateFileField ).getElement('.ka-field-title').getElement('.title');
            title.setStyle('width', width-30);
        }
        
        this.templateFileField.setValue( this.content.content );

        return;
    },

    type2Text: function(){

        this.body.empty();

        this.textarea = new Element('textarea', {
            value: this.content.content,
            style: 'width: 100%'
        }).inject( this.body );

        this.lastTextarea = this.textarea;

        this.layoutBox.alloptions.toolbarContainer = this.toolbarWysiwygContainer;

        if( this.mooeditable ){
            this.mooeditable.textarea = this.textarea;
            this.mooeditable.attach();
        } else {
            this.mooeditable = initWysiwyg( this.textarea, this.layoutBox.alloptions );
        }

    },

    openInBigEditor: function(){
        var tiny = this.w.tinyMCE.get(this.lastId);
        
        if( tiny )
            this.content.content = tiny.getContent();

        ka.wm.openWindow( 'admin', 'pages/bigEditor', null, this.w.win.id, {content: this.content.content, onSave: function( pContent ){
            this.content.content = pContent;
            if( this.editMode == 1 ){
                var tiny = this.w.tinyMCE.get(this.lastId);
                tiny.setContent( pContent );
            }
        }.bind(this)});
    },

    setHide: function( pHide ){
    	if( !this.hideImg ) return;
    	
        if( this.content.hide == 0 ){
            this.hideImg.set('src', _path+'inc/template/admin/images/icons/lightbulb.png');
        } else {
            this.hideImg.set('src', _path+'inc/template/admin/images/icons/lightbulb_off.png');
        }
    },

    setDivContent: function( pRenderBody ){
        
        if( this.body && this.lastContent &&
            this.lastContent.template == this.content.template &&
            this.lastContent.type == this.content.type &&
            this.lastContent.title == this.content.title
        ) return this._setDivContent();
        
        if( this.lastCR )
            this.lastCR.cancel();
        
        if( this.content.template == '-' || this.content.template == '' ){

            this.body.dispose();
            this.div.empty();
            this.body.inject( this.div );

            if( pRenderBody )
                this._setDivContent();

            this.lastContent = Object.clone(this.content);

            return;
        }
        
        this.lastCR = new Request.JSON({url: _path+'admin/backend/getContentTemplate', noCache: 1, onComplete: function( pTpl ){
            
            this.body.dispose();
            this.div.set('html', pTpl);

            this.nbody = this.div.getElement('.ka-layoutelement-content-content');
            this.title = this.div.getElement('.ka-layoutelement-content-title');
            
            if( this.nbody )
                this.body.replaces( this.nbody );
            else
                this.body.inject( this.div );
                
            if( !this.body.hasClass('ka-layoutelement-content-content') )
                this.body.addClass('ka-layoutelement-content-content');

            if( this.title ){
                this.title.addEvent('click', function(){
                    this.showToolbar();
                    this.iTitle.highlight();
                    this.iTitle.focus();
                }.bind(this));
            }
            
            if( pRenderBody )
                this._setDivContent();

            this.lastContent = Object.clone(this.content);
            
        }.bind(this)}).get({title: this.content.title, type: this.content.type, template: this.content.template});
        
    },
    
    _setDivContent: function(){
        //here we need a valid this.body ref
        
        if( !this.content ) return;

        if( this.content.type != 'text' &&  this.content.type != 'picture' ){
            this.lastId = false;
        }
        
        this.toolbar.removeClass('ka-layoutContent-toolbar-withwysiwyg');

        if( !['text', 'picture'].contains(this.content.type) ){
            this.main.addClass('ka-layoutContent-body-notext');
        } else {

            this.main.removeClass('ka-layoutContent-body-notext');
            this.toolbar.addClass('ka-layoutContent-toolbar-withwysiwyg');
        }

        if( this.title )
            this.title.set('html', this.content.title);
            
        if( this.content.type != 'text' && this.content.type != 'picture' )
            this.lastTextarea = false;
            
        if( (this.content.type != 'text' && this.content.type != 'picture' ) && this.mooeditable ){
            this.mooeditable.detach();
            this.mooeditable = null;
        }
        
        if( this.lastContent && this.lastContent.type != this.content.type ){
            this.toolbarWysiwygContainer.empty();
        }

        if( this.oldType != this.content.type ){
            this.content.content = '';
        }

        switch( this.content.type ){
        case 'text':
            this.type2Text();
            break;
        case 'plugin':
            this.setDivPlugin();
            break;
        case 'navigation':
            this.type2Navi();
            break;
        case 'template':
            //this.setDivTemplate();
            this.type2Template();
            break;
        case 'pointer':
            this.type2Pointer();
            break;
        case 'html':
        case 'php':
            this.type2HTML();
            break;
        case 'layoutelement':
        	this.toLayoutElement();
        	break;
        }
        
        this.setHide( this.content.hide );

        var title = this.content.title;
        if( title == ""){
            title = _('[No title]');
        }
        
        this.lastContent = Object.clone(this.content);

        this.positionToolbar();
        
        this.positionToolbar.delay(100, this);
        this.positionToolbar.delay(500, this);

    },

    setDivHTML: function(){
        /*this.body.empty();
        new Element('div', {
            text: this.content.content
        }).inject( this.body );
        */
    },

    setDivTemplate: function(){
        this.body.empty();
        new Element('div', {
            html: _('File: %s').replace('%s', this.content.content)
        }).inject( this.body );
    },

    setDivPic: function(){
        if( !this.picDivContentImg )
            this.body.empty();
        
        this.type2PicSrc = '';
        
        if( this.content.content ){
            var t = this.content.content.split('::');
            this.type2PicSrc = t[1];
            var temp  = t[0];
            if( temp != 'none' )
                this.opts = JSON.decode( temp );
        }
        
        if( this.type2PicSrc == '' && $type(this.type2PicSrc) != 'string' ) return;

        if( this.body.getElements('img.ka-type-picture').length == 0 ){
        
            this.picDivContentDiv = new Element('div', {
                styles: {
                    'overflow-x': 'hidden'
                }
            }).inject( this.body );
            
            this.picDivContentImg = new Element('img', {
                src: this.type2PicSrc,
                'class': 'ka-type-picture',
                height: 40,
                title: this.type2PicSrc
            }).inject( this.picDivContentDiv );
        
        }
        
        if( this.opts && this.opts.align ){
            this.picDivContentDiv.setStyle('text-align', this.opts.align);
        }
        
        if( $type(this.type2PicSrc) == 'string' )
            this.picDivContentImg.set('src', this.type2PicSrc);
        
        if( this.opts ){
            if( this.opts.width && this.opts.height ){
                this.picDivContentImg.set('width', this.opts.width);
                this.picDivContentImg.set('height', this.opts.height);
            }
        }
            
    },

    setDivPlugin: function( pContainer ){
        var mybody = (pContainer)?pContainer:this.body;
        
        if( this.bodyPluginRequest )
            this.bodyPluginRequest.cancel();

        var t = this.content.content.split('::');
        var info = this.content.content;
        if( typeOf(info) != 'string' ) return;
        
        var pos = info.indexOf('::');
        var extension = info.substr(0,pos);
        var info = info.substr(pos+2);
        
        var pos = info.indexOf('::');
        var plugin = info.substr(0,pos);
        var info = info.substr(pos+2);

        var title = _('Please choose'), pluginTitle;
        
        if( ka.settings.configs[extension] ){
            title = ka.settings.configs[extension].title['en'];
            if( ka.settings.configs[extension].title[window._session.lang] )
                title = ka.settings.configs[extension].title[window._session.lang];
        }
        
        if( ka.settings.configs[extension] )
            pluginTitle = _(ka.settings.configs[extension].plugins[plugin][0]);
        
        mybody.empty();
        new Element('div', {
            'style': 'font-weight: bold',
            html: title
        }).inject( mybody );
        
        new Element('div', {
            html: pluginTitle
        }).inject( mybody );
        
        new ka.Button(_('Edit properties'))
        .addEvent('click', function(){
            this.type2Plugin();
        }.bind(this))
        .addEvent('click', function(e){
            e.stopPropagation();
        })
        .inject( mybody );
        
        return;
        
        /* old
        this.bodyPluginRequest = new Request.JSON({url: _path+'admin/backend/plugins/preview/', noCache: 1, onComplete: function(html){
            this.body.empty();
            var div = new Element('div', {
                html: html
            }).inject( mybody );
        }.bind(this)}).post({ content: this.content.content });
        */
        
    },

    setDivNavigation: function( pContainer ){
        var mybody = (pContainer)?pContainer:this.body;
        if( this.bodyNavigationRequest )
            this.bodyNavigationRequest.cancel();

        try {
            if( $type(this.content.content) != 'object')
                this.content.content = JSON.decode( this.content.content );
        } catch(e) {
        }
        if( $type(this.content.content) != 'object' ) return;

        this.bodyNavigationRequest = new Request.JSON({url: _path+'admin/backend/navigationPreview/', noCache: 1, onComplete: function(html){
            this.body.empty();
            var div = new Element('div', {
                html: html
            }).inject( mybody );
            new Element('img', {
                src: _path+'inc/template/admin/images/icons/bullet_go.png',
                title: _('Open target')
            })
            .addEvent('click', function(){
                this.w.kpage.loadPage( this.content.content, true );
            }.bind(this))
            .inject( div, 'top' ); 

        }.bind(this)}).post({content: this.content.content.entryPoint});
    },

    setDivPointer: function( pContainer ){
        var mybody = (pContainer)?pContainer:this.body;
        if( this.bodyPointerRequest )
            this.bodyPointerRequest.cancel();

        this.bodyPointerRequest = new Request.JSON({url: _path+'admin/backend/pointerPreview/', noCache: 1, onComplete: function(html){
            this.body.empty();
            var div = new Element('div', {
                html: html
            }).inject( mybody );
            new Element('img', {
                src: _path+'inc/template/admin/images/icons/bullet_go.png',
                title: _('Open target')
            })
            .addListener('click', function(){
                this.w.kpage.loadPage( this.content.content, true );
            }.bind(this))
            .inject( div, 'top' ); 
        }.bind(this)}).post({content: this.content.content});
    },

    select: function(){
    	if( this.noAccess ) return;
        if( this.isSelected() ) return;
        
        this.layoutBox.deselectAll( this );
        
        this.showToolbar();
        
        this.main.addClass('ka-layoutContent-main-selected');
    },
    
    deselectChilds: function(){

        if( this.layoutElement )
        	this.layoutElement.deselectAll();
    	
    },
    
    isSelected: function(){
    
        return this.main.hasClass('ka-layoutContent-main-selected');
    
    },

    deselect: function(){
    
    	if( this.noAccess ) return;
        if( !this.isSelected() ) return;

        this.toData( true );

        this.hideToolbar();

        this.main.removeClass('ka-layoutContent-main-selected');
            
        if( (this.content.type == 'text' || this.content.type == 'picture') && this.hideTinyMceToolbar ){
            this.hideTinyMceToolbar();
        }

    },

    prepareData: function(){
    
        switch( this.content.type ){
        case 'navigation':
            if( $type(this.content.content) == 'object' )
                this.content.content = JSON.encode( this.content.content );
            break;
        }
    },

    getValue: function( pAndClose ){

        this.toData();

        this.prepareData();
        /*
        if( pAndClose == true && this.editMode == 1 ) {
            this.toggleEdit();
        } else if( this.editMode == 1 ) {
            this.toData();
        }*/
        
        return this.content;
    }

});
