ka.field = new Class({
    
    Implements: [Options,Events],
    
    options: {
    
    },
    
    field: {},
    
    initialize: function( pField, pContainer, pRefs ){
        this.field = pField;
        
        this.setOptions(pField);
        if( typeOf(pRefs) == 'object' ){
            Object.each(pRefs, function(item,key){
                this.field[key] = item;
            }.bind(this));
        }
               
        if(! this.field.value ) this.field.value = '';

        
        if( this.field.tableitem ){
            this.tr = new Element('tr', {
                'class': 'ka-field-main'
            }).inject( pContainer||document.hidden );

            this.title = new Element('td', {
                'class': 'ka-field-tdtitle',
                width: (this.field.tableitem_title_width)?this.field.tableitem_title_width:180
            }).inject( this.tr );
            
            this.main = new Element('td', {
            }).inject( this.tr );
            
        } else {
            this.main = new Element( 'div', {
                'class': 'ka-field-main'
            }).inject( pContainer||document.hidden );
            
            if( this.field.panel_width )
                this.main.setStyle('width', this.field.panel_width);    
            else
                this.main.setStyle('width', 330);
            
            if( pField.type == 'headline' ){
                new Element('div', {
                    style: 'clear: both;'
                }).inject( this.main );
                new Element('h2', {
                    'class': 'ka-field-headline',
                    html: _(pField.label)
                }).inject( this.main );
                return;
            }
    
            if( pField.small ){
                this.main.set('class', 'ka-field-main ka-field-main-small');
            }
            this.title = new Element('div', {
                'class': 'ka-field-title'
            }).inject( this.main );
            
        }
        
        if( this.field.invisible == 1)
            this.main.setStyle('display', 'none');
        
        if( this.field.win )
            this.win = this.field.win;

        if( pField.label ){
            this.titleText = new Element('div', {
                'class': 'title',
                html: pField.label
            }).inject( this.title );
        }

        if( pField.help && this.titleText ){
            new Element('img', {
                src: _path+'inc/template/admin/images/icons/help_gray.png',
                width: 14,
                style: 'float: right; cursor: pointer; position: relative; top: -1px;',
                title: _('View help to this field'),
                styles: {
                    opacity: 0.7
                }
            })
            .addEvent('mouseover', function(){
                this.setStyle('opacity', 1);
            })
            .addEvent('mouseout', function(){
                this.setStyle('opacity', 0.7);
            })
            .addEvent('click', function(){
                ka.wm.open('admin/help', {id: pField.help});
            })
            .inject( this.titleText );
        }
        
        if( this.field.desc ){
            new Element('div', {
                'class': 'desc',
                html: this.field.desc
            }).inject( this.title );
        }

        this.fieldPanel = new Element('div', {
            'class': 'ka-field-field'
        }).inject( this.main );

        this.addEvent('change', function(){
            this.fireEvent('check-depends');
        }.bind(this));

        this.renderField();
        
        if( this.field['default'] && this.field['default'] != "" && this.field.type != 'datetime' ){
            this.setValue( this.field['default'] );
        }
        
        if(! this.field.startempty && this.field.value ){
            this.setValue( this.field.value, true );
        }
        
        if( this.input ){
            if( this.input.get('tag') == 'input' && this.input.get('class') == 'text' ){
                this.input.store( 'oldBg', this.input.getStyle('background-color') );
                this.input.addEvent('focus', function(){
                    this.setStyle('border', '1px solid black');
                    this.setStyle('background-color', '#fff770');
                });
                this.input.addEvent('blur', function(){
                    this.setStyle('border', '1px solid silver');
                    this.setStyle('background-color', this.retrieve('oldBg'));
                });
                this.input.addEvent('keyup', function(){
                    this.fireEvent('change', this.getValue());
                }.bind(this));
            }
        
            if( !this.field.disabled ){
                this.input.addEvent('change', function(){
                    this.fireEvent('change', this.getValue());
                }.bind(this));
                this.input.addEvent('keyup', function(){
                    this.fireEvent('change', this.getValue());
                }.bind(this));
            } else {
                this.input.set('disabled',true);
            }
        }
    },

    renderField: function(){
        if( this.field.type )
            this.field.type = this.field.type.toLowerCase();
        
        switch( this.field.type ){
        case 'password':
            this.renderPassword();
            break;
        //case 'file':
        //    this.renderFile();
        //    break;
        case 'select':
            this.renderSelect();
            break;
        case 'textlist':
            this.renderTextlist();
            break;
        case 'textarea':
           this.renderTextarea(); 
           break;
        case 'array': 
            this.renderArray();
            break;
        case 'wysiwyg':
            this.renderWysiwyg();
            break;
        case 'date':
            this.renderDate();
            break;
        case 'datetime':
            this.renderDate({time:true});
            break;
        case 'checkbox':
            this.renderCheckbox();
            break;
        case 'file':
        case 'filechooser':
            this.renderChooser({pagefiles: 1, upload: 1, files:1});
            break;
        case 'folder':
            this.renderChooser({pagefiles: 1, upload: 1, files:1, onlyDir: 1});
            break;
        case 'pagechooser':
        case 'page':
            this.renderChooser({pages: 1});
            break;
        case 'chooser':
            this.renderChooser({pages: 1, files: 1, upload: 1, pagefiles: 1});
            break;
        case 'filelist': 
            this.renderFileList();
            break;
        case 'multiupload':
            this.initMultiUpload();
            break;
        case 'layoutelement':
            this.initLayoutElement();
            break;
        case 'headline':
            this.renderHeadline();
            break;
        case 'info':
            this.renderInfo();
            break;
        case 'imagegroup':
            this.renderImageGroup();
            break;
        case 'custom':
            this.renderCustom();
            break;
        case 'number':
            this.renderNumber();
            break;
        case 'window_list':
            this.renderWindowList();
            break;
        case 'text':
        default: 
            this.renderText();
            break;
        }
        if(this.input){

            if( this.field.length+0 > 0 ){
                //alert(this.field.length);
                this.input.setStyle('width', (this.field.length.toInt()*9));
            }
            
            this.input.store('oldClass', this.input.get('class') );
        }
    },
    
    renderTextlist: function(){
        var _this = this;
        var _searchValue;
    
        var box, timer, boxHead, boxBody, lastRq, curSelection;
        
        var div = new Element('div', {
            'class': 'ka-field-textlist'
        }).inject( this.fieldPanel );
        
        if( this.field.width )
            div.setStyle('width', this.field.width);
        
        var input = new Element('input', {
            autocomplete: false,
            tabindex: 0,
            style: 'width: 7px;'
        }).inject( div );
        
        var clear = new Element('div', {
            'class': 'ka-field-textlist-clear'
        })
        .addEvent('click', function(e){
            if( _this.field.store ){
                input.setStyle('left', '');  
                input.setStyle('position', '');
                input.focus();
                active = input;
                input.value = '';
                div.getElements('.ka-field-textlist-item-active').removeClass('ka-field-textlist-item-active');
                searchValue();
                e.stop();
            }
        }).inject( div );
        
        new Element('img', {
            src: _path+'inc/template/admin/images/icons/tree_minus.png'
        }).inject( clear );
        
        var active = input;
        
        var addTextlistItem = function( pLabel, pValue ){
        
            if( !pValue )
            pValue = pLabel;
        
            if( !_this.field.doubles || _this.field.doubles != true || _this.field.doubles != 1 ){
                //check for doubles
                
                var found = false;
                div.getElements('.ka-field-textlist-item').each(function(item){
                    if( found == true ) return;
                    if( item.retrieve('value') == pValue ){
                        found = true;
                    }
                
                });
                if( found ) return;
            }

            var item = new Element('div', {
                'class': 'ka-field-textlist-item'
            }).inject( input, 'before' );
            
            var title = new Element('span',{
                text: pLabel?pLabel:'...'
            }).inject( item );
            
            if( !pLabel ){
                
                new Request.JSON({url: _path+'admin/'+_this.field.store, onComplete:function(res){
                    title.set('text', res.label )
                }}).get({cmd: 'item', id: pValue});
            }
            
            item.addEvent('mousedown', function(e){
            
                e.stop();
                input.setStyle('left', -5000);              
                input.setStyle('position', 'absolute');
                active.removeClass('ka-field-textlist-item-active');
                active = this;
                active.addClass('ka-field-textlist-item-active');
                input.focus();
                input.value = '';
            
            });
            
            item.store( 'value', pValue );
            
            new Element('a', {
                text: 'x'
            })
            .addEvent('mousedown', function(e){
                e.stop();
                if( active == this.getParent() ){
                    var next = this.getParent().getNext();
                    if( !next.hasClass('ka-field-textlist-item') && next.get('tag') != 'input' ){
                        next = this.getParent().getPrevious();
                    }
                    if( !next.hasClass('ka-field-textlist-item') && next.get('tag') != 'input' ){
                        next = input;
                    }
                    if( next.get('tag') == 'input' ){
                        active = input;
                        input.setStyle('left', '');  
                        input.setStyle('position', '');
                        input.focus();
                    } else {
                        active = next;                        
                        active.addClass('ka-field-textlist-item-active');
                    }
                }
                this.getParent().destroy();
            })
            .inject( item );
        
        };
        
        var checkAndCreateItem = function(){
            
            if( boxBody.getElement('.active') ){
                var item = boxBody.getElement('.active');
                addTextlistItem( item.get('text'), item.retrieve('value') );
            }
        
        }
        
        var updatePosition = function(){
            if( box && box.getParent ){
            
                box.position({
                    relativeTo: div,
                    position: 'bottomLeft',
                    edge: 'upperLeft'
                });

                var pos = box.getPosition();
                var size = box.getSize();
                
                var bsize = window.getSize( $('desktop') );
                
                var height;
        
                if( size.y+pos.y > bsize.y ){
                    height = bsize.y-pos.y-10;
                }

                if( height ) {
                
                    if( height < 100 ){
                    
                        box.position({
                            relativeTo: div,
                            position: 'upperLeft',
                            edge: 'bottomLeft'
                        });
                        
                    } else {
                        box.setStyle('height', height);
                    }
                    
                }

                timer = updatePosition.delay(500);
            }
        }
        
        _searchValue = function( pValue ){
            if( lastRq )
                lastRq.cancel();

            var lastRq = new Request.JSON({url: _path+'admin/'+_this.field.store, noCache: 1, onComplete:function(res){
                
                boxBody.empty();
                if( typeOf(res) != 'object' ){
                    boxBody.set('html', _('No results.'));
                } else {
                    Object.each(res, function(label,value){
                        var a = new Element('a', {
                            text: label
                        }).inject( boxBody );
                        
                        a.addEvent('mousedown', function(e){
                            boxBody.getElements('a').removeClass('active');
                            this.addClass('active');
                            active = input;
                            checkAndCreateItem();
                            this.removeClass('active');
                            input.focus();
                            input.value = '';
                            e.stop();
                        });
                        a.store('value', value);
                    });
                    if( boxBody.getElement('a') && pValue )
                        boxBody.getElement('a').addClass( 'active' );
                }
                
            }}).post({search: pValue});
        }

        var searchValue = function( pValue ){

            if( !box ){
                var target = document.body;
                if( _this.fieldPanel.getParent('.kwindow-border') ){
                    target = _this.fieldPanel.getParent('.kwindow-border');
                }
                box = new Element('div', {
                    'class': 'ka-field-textlist-searchbox'
                }).inject( target );
                
                if( timer )
                    clearTimeout( timer );

                updatePosition();
            
                /*boxHead = new Element('div', {
                    'class': 'ka-field-textlist-searchbox-head'
                }).inject( box );
                boxHeadC = new Element('div', {
                    'class': 'ka-field-textlist-searchbox-head-c'
                }).inject( boxHead );
                
                new Element('input', {
                    'class': 'ka-field-textlist-searchbox-head'
                }).inject( boxHeadC );
                */

                boxBody = new Element('div', {
                    'class': 'ka-field-textlist-searchbox-body'
                }).inject( box );
            }
            _searchValue( pValue );
        }

        var hideSearchBox = function(){
            if( box ){
                boxBody.empty();
                box.destroy();
                box = null;
            }
        }
    
        if( this.field.store ){
            input.addEvent('blur', hideSearchBox);
            window.addEvent('click', hideSearchBox);
            input.addEvent('focus', function(){
                if( this.value.length > 0 ){
                    searchValue( this.value );
                }
            });
        }
        

        input.addEvent('keydown', function(e){
            if( e.key == 'enter' && this.value.length > 0 ){
                if( _this.field.store ){
                    checkAndCreateItem();
                } else {
                    addTextlistItem( this.value );
                }
                this.value = '';
            }
            
            if( e.key == 'top' || e.key == 'bottom' ){
                e.stop();
            }
            
            if( e.key == 'backspace' ){
                if( active.get('tag') == 'div' ){
                    this.inject( active, 'after' );
                    active.destroy();
                    active = this;
                    this.setStyle('left', '');
                    this.setStyle('position', '');
                    this.focus();
                }
            }
            
            var oldActive = active;
            if( (e.key == 'left' || e.key == 'backspace' ) && this.value.length == 0 ){
                if( active.getPrevious() && active.get('tag') == 'input' ){
                    if( active.get('tag') == 'input' ){                
                        this.setStyle('left', -5000);              
                        this.setStyle('position', 'absolute');
                        active.removeClass('ka-field-textlist-item-active');
                        active = this.getPrevious();
                        active.addClass('ka-field-textlist-item-active');
                    }
                }
                if( oldActive.get('tag') != 'input' ) {
                    this.inject( active, 'before' );
                    active.removeClass('ka-field-textlist-item-active');
                    active = this;
                    this.setStyle('left', '');  
                    this.setStyle('position', '');
                    this.focus();
                }
            }
            
            if( e.key == 'right' && this.value.length == 0 ){
                if( active.getNext() && active.getNext().hasClass('ka-field-textlist-item') && active.get('tag') == 'input' ){
                    if( active.get('tag') == 'input' ){
                        this.setStyle('left', -5000);              
                        this.setStyle('position', 'absolute');
                        active.removeClass('ka-field-textlist-item-active');
                        active = this.getNext();
                        active.addClass('ka-field-textlist-item-active');
                    }
                }
                if( oldActive.get('tag') != 'input' ) {                  
                    this.inject( active, 'after' );
                    active.removeClass('ka-field-textlist-item-active');
                    active = this;
                    this.setStyle('left', '');  
                    this.setStyle('position', '');
                    this.focus();
                }
            }
            
            if( _this.field.store ){
                if( e.key == 'down' ){
                    var oldActive = boxBody.getElement('.active');
                    if( oldActive && oldActive.getNext() ){
                        oldActive.getNext().addClass( 'active' );
                        oldActive.removeClass('active');
                    }
                }
                
                if( e.key == 'up' ){
                    var oldActive = boxBody.getElement('.active');
                    if( oldActive && oldActive.getPrevious() ){
                        oldActive.getPrevious().addClass( 'active' );
                        oldActive.removeClass('active');
                    }
                }
            }
        });
        
        var lastSearch = false;
        input.addEvent('keyup', function(e){
            if( _this.field.store ){
                if( this.value.length > 0 ){
                    if( lastSearch == this.value ){
                        return;
                    }
                    lastSearch = this.value;
                    searchValue( this.value );
                } else {
                    hideSearchBox();
                    lastSearch = '';
                }
            }
            this.setStyle('width', 6.5*(this.value.length+1));
        });
        
        div.addEvent('click', function(e){
            if( e.target && !e.target.hasClass('ka-field-textlist') ) return;
            input.inject( clear, 'before' );
            input.setStyle('position', '');
            input.setStyle('left', '');
            if( active )
                active.removeClass('ka-field-textlist-item-active');
            input.focus();
            active = input;
        });
        
        this._setValue = function( pValue ){
        
            if( typeOf(pValue) == 'string' ) pValue = JSON.decode( pValue );
        
            if( _this.field.store ){
                Array.each( pValue, function(item){
                    addTextlistItem( false, item );
                });
            } else {
                Array.each( pValue, function(item){
                    addTextlistItem( item );
                });
            }
        }
        
        this.getValue = function(){
            var res = [];            
            div.getElements('.ka-field-textlist-item').each(function(item){
                res.include( item.retrieve('value') );
            });
            return res;
        }
    
    },
    
    renderCustom: function(){
        var _this = this;
        
        if( window[this.field['class']] ){
            
            this.customObj = new window[this.field['class']]( this.field, this.fieldPanel );
            
            this.customObj.addEvent('change', function(){
                this.fireEvent('change', this.getValue());
            }.bind(this));
            
            this._setValue = this.customObj.setValue.bind(this.customObj);
            this.getValue = this.customObj.getValue.bind(this.customObj);
            this.isEmpty = this.customObj.isEmpty.bind(this.customObj);
            this.highlight = this.customObj.highlight.bind(this.customObj);
        } else {
            alert(_('Custom field: '+this.field['class']+'. Can not find this javascript class.'));
        }
    },
    
    renderArray: function(){
    
        var table = new Element('table', {
            cellpadding: 1,
            cellspacing: 0,
            width: '100%',
            'class': 'ka-field-array'
        }).inject( this.fieldPanel );
        
        this.fieldPanel.setStyle('margin-left', 11);
        
        if( this.field.width )
            this.fieldPanel.setStyle('width', this.field.width);
        
        var thead = new Element('thead').inject( table );
        var tbody = new Element('tbody').inject( table );
    
        var actions = new Element('div', {
            
        }).inject( this.fieldPanel );
        
        
        var tr = new Element('tr').inject( thead );
        Array.each(this.field.columns, function(col, colid){
            var td = new Element('th', {
                text: _(col.label)
            }).inject( tr );
            if( col.width ){
                td.set('width', col.width);
            }
        });
        var td = new Element('th', {
            style: 'width: 30px'
        }).inject( tr );
        
        var addRow = function( pValue ){
        
            var tr = new Element('tr').inject( tbody );
            tr.fields = {};

            Object.each(this.field.fields, function(field, field_key){
                
                if( !field.panel_width ) field.panel_width = '100%';
                
                var nField = new ka.field( field );
                var td = new Element('td').inject( tr );
                nField.inject( td );
                
                if( pValue && pValue[field_key] )
                    nField.setValue( pValue[field_key] );

                tr.fields[field_key] = nField;
            
            }.bind(this));
            
            var td = new Element('td').inject( tr );
            
            new Element('img', {
                src: _path+'inc/template/admin/images/icons/delete.png',
                style: 'cursor: pointer;',
                title: _('Remove')
            })
            .addEvent('click', function(){
                tr.destroy();
            })
            .inject( td );
            
        
        }.bind(this);
        
        new ka.Button(this.field.addText?this.field.addText:_('Add'))
        .addEvent('click', addRow)
        .inject( actions );
        
        this.getValue = function(){
            var res = [];
            var ok = true;
        
            tbody.getChildren('tr').each(function(tr){
                if( ok == false ) return;
                                
                var row = {};
                Object.each( tr. fields, function(field, field_key){
                    if( ok == false ) return;
                    
                    if( !field.isOk() ){
                        ok = false;
                    } else {
                        row[field_key] = field.getValue();
                    }
                
                });
                
                res.include( row );
            
            });
            
            if( ok == false ) return;
            return res;
        }
        
        this._setValue = function( pValue ){
            tbody.empty();
            
            if( typeOf(pValue) == 'string' ){
                pValue = JSON.decode( pValue );
            }
            
            if( typeOf(pValue) != 'array' ) return;
            
            Array.each( pValue, function(item){
                addRow( item );
            });
        }
        
        if( this.field.startWith && this.field.startWith > 0 ){
            for( var i=0; i<this.field.startWith; i++ ){
                addRow();
            }
        }
    
    },
    
    renderWindowList: function(){
    
        var div = new Element('div', {
            styles: {
                height: this.field.height
            }
        }).inject(this.fieldPanel);
        
        if(! this.field.panel_width )
            this.main.setStyle('width', '');
        
        var titleGroups = new Element('div', {
            'class': 'kwindow-win-title kwindow-win-titleGroups',
            style: 'display: none; top: 0px;padding: 3px; height: 25px; min-height: 25px;'
        }).inject( div );
        
        var content = new Element('div', {
            'class': 'kwindow-win-content',
            style: 'top: 36px;'
        }).inject( div );
    
        var pos = this.field['window'].indexOf('/');
        var module = this.field['window'].substr(0,pos);
        var code = this.field['window'].substr( pos+1 );
        
        
        var win = {};
        Object.append(win, this.win);
        
        Object.append(win, {
            content: content,
            extendHead: function(){
                titleGroups.setStyle('display', 'block');
            },
            addButtonGroup: function(){
                titleGroups.setStyle('display', 'block');
                return new ka.buttonGroup( titleGroups );
            },
            module: module,
            code: code,
            _confirm: this.win._confirm,
            params: {},
            id: this.win.id
        });
        
        this.getValue = function(){};

        this._setValue = function( pValue ){
            
            if( !this.list ){
                this.list = new ka.list( win, {
                    relation_table: pValue.table,
                    relation_params: pValue.params
                });
            } else {
                this.list.options.relation_params = pValue.params;
                
                if( this.list.classLoaded == true ){
                    this.list.loadPage( 1, true );    
                } else {
                    this.list.addEvent('render', function(){
                        this.list.loadPage( 1, true );
                    }.bind(this));
                }
                
            }
        }.bind(this);
    },
    
    renderImageGroup: function(){
        
        this.input = new Element('div', {
            style: 'padding: 5px;'
        }).inject( this.fieldPanel );
        
        this.imageGroup = new ka.imageGroup( this.input );
        
        this.imageGroupImages = {};
        
        $H(this.field.items).each(function(image, value){
            
            this.imageGroupImages[ value ] = this.imageGroup.addButton( image.label, image.src );
            
        }.bind(this));
        
        this.imageGroup.addEvent('change', function(){
            
            this.fireEvent('change', this.getValue());
            
        }.bind(this));
        
        this.getValue = function(){
            
            var value = false;
            $H(this.imageGroupImages).each(function(button,tvalue){
                if( button.get('class').test('buttonHover') )
                    value = tvalue;
            });
            
            return value;
        }
        
        this._setValue = function( pValue ){
            
            $H(this.imageGroupImages).each(function(button,tvalue){
                button.removeClass('buttonHover');
                if( pValue == tvalue )
                    button.addClass('buttonHover');
            });
        }
        
    },

    renderHeadline: function(){
        
        this.input = new Element('h2',{
            html: this.field.label
        }).inject( this.fieldPanel );
        
    },
 
    renderInfo: function(){
        
        return;
        
    },
    
    renderFileList: function( pOpts ){
        var relHeight = (this.field.height) ? this.field.height : 150;
        var main = new Element('div', {
            styles: {
                position: 'relative',
                'height' : relHeight,
                'width': (this.field.width)?this.field.width:null
            }
        }).inject( this.fieldPanel );
        
        var wrapper = new Element('div', {
            style: 'position: absolute; left: 0px; top: 0px; bottom: 0px; right: 18px;'
        }).inject( main );

        this.input = new Element('select', {
            size: (this.field.size)?this.field.size:5,
            style: 'width: 100%',
            styles: {
                'height': (this.field.height)?this.field.height:null
            }
        }).inject( wrapper );
        var input = this.input;


        var addFile = function( pPath ){
            new Element('option', {
                value: pPath,
                text: pPath
            }).inject( input );
        }

        this.addImgBtn = new Element('img', {
            src: _path+'inc/template/admin/images/icons/add.png',
            style: 'position: absolute; top: 0px; right: 0px; cursor: pointer;'
        })
        .addEvent('click', function(){

            ka.wm.openWindow( 'admin', 'pages/chooser', null, -1, {onChoose: function( pValue ){
                addFile( pValue );
                this.win.close();//close paes/chooser windows -> onChoose.bind(this) in chooser-event handler
            },
            opts: {files: 1, upload: 1}
            });

        }.bind(this))
        .inject( main );


        this.addImgBtn = new Element('img', {
            src: _path+'inc/template/admin/images/icons/delete.png',
            style: 'position: absolute; top: 19px; right: 0px; cursor: pointer;'
        })
        .addEvent('click', function(){
            input.getElements('option').each(function(option){
                if(option.selected) option.destroy();
            });
        }.bind(this))
        .inject( main );


        var _this = this;
        this.getValue = function(){
            var res = [];
            _this.input.getElements('option').each(function(option){
                res.include( option.value );
            });
            return res;
        }

        this._setValue = function( pValues ){
            input.empty();
            if( $type(pValues) == 'string') pValues = JSON.decode(pValues);
            if( $type(pValues) != 'array' ) return;
            pValues.each(function(item){
                new Element('option', {
                    text: item,
                    value: item
                }).inject( input );
            });
            this.fireEvent('change', this.getValue());
        }

    },

    setInputActive: function( pSet ){
        var bg = 'white';
        if( pSet ){
            //yes
            this.input.setStyle('cursor', 'auto');
        } else {
            bg = '#ddd';
            this.input.setStyle('cursor', 'default');
        }
        this.input.setStyle('background-color', bg);
        this.input.store( 'oldBg', bg);
    },

    renderFileChooser: function(){
        this.input = new Element('input', {
            'class': 'text',
            type: 'text'
        })
        .inject( this.fieldPanel );
    },

    renderChooser: function( pOpts ){
        this.input = new Element('input', {
            'class': 'text',
            type: 'text',
            style: ((this.field.small == 1) ? 'width: 100px' : 'width: 210px'),
            disabled: this.field.onlyIntern
        })
        .addEvent('focus', function(){
            this.setInputActive( true );
        }.bind(this))
        .addEvent('blur', function(){
            if( this.input.value != this._automaticUrl ){//wurde verändert
                this._value = false;
                this.setInputActive( true );
            } else {
                this.setInputActive( false );
            }
        }.bind(this))
        .addEvent('keyup', function(){
            this.fireEvent('blur');
        })
        .inject( this.fieldPanel );

        var div = new Element('span').inject( this.fieldPanel );
        var button = new ka.Button(_('Choose'))
            .addEvent('click', function(){
                var _this = this;
                ka.wm.openWindow( 'admin', 'backend/chooser', null, -1, {onChoose: function( pValue ){
                    _this.setValue( pValue, true );
                    this.win.close();//close paes/chooser windows -> onChoose.bind(this) in chooser-event handler
                },
                value: this._value,
                cookie: this.field.cookie,
                domain: this.field.domain,
                display: this.field.display,
                opts: pOpts});
        }.bind(this))
        .setStyle('position', 'relative')
        .setStyle('top', '-1px')
        .inject( div, 'after' );

        this.pageChooserPanel = new Element('span', {style: 'color: gray;'}).inject( button, 'after' );

        this._setValue = function( pVal, pIntern ){
            this._value = pVal;
            this.pageChooserPanel.empty();
            if( pVal+0 > 0 ){
                this.setInputActive( false );
                this.pageChooserGetUrl();
            } else {
                this.setInputActive( true );
                this.input.value = pVal;
            }
            this.input.title = this.input.value;
            if( pIntern )
                this.fireEvent('change', this.getValue());
        }

        this.getValue = function(){
            return (this._value)?this._value:this.input.value;
        }
    },

    pageChooserGetUrl: function(){
        if( this.lastPageChooserGetUrlRequest )
            this.lastPageChooserGetUrlRequest.cancel();
        
        this.lastPageChooserGetUrlRequest = new Request.JSON({url: _path+'admin/pages/getUrl', noCache: 1, onComplete: function(res){
            this._automaticUrl = res;
            this.input.value = res;
            this.input.fireEvent('blur');
            this.fireEvent('change', this.getValue());
        }.bind(this)}).post({rsn: this._value });

    },

    renderSelect: function(){
        var _this = this;
        var multiple = ( this.field.multi || this.field.multiple );
        var sortable = this.field.sortable;


        var selWidth = 133;
        if( this.field.tinyselect )
            selWidth = 75;
        if( sortable )
            selWidth -= 8;

        if( !this.field.tableItems && this.field.table_items ){
            this.field.tableItems = this.field.table_items;
        }

        if( multiple && (!this.field.size || this.field.size+0 < 4 ) )
            this.field.size = 4;
        
        if( multiple ){
            this.input = new Element('select', {
                size: this.field.size
            })
            .addEvent('change', function(){
                this.fireEvent('change', this.getValue());
            }.bind(this))
            .inject( this.fieldPanel );
        }
            
        if( !this.field.tableItems && this.field.items )
            this.field.tableItems = this.field.items;

        
        var label = _this.field.table_label;
        var key = _this.field.table_key ? _this.field.table_key : _this.field.table_id;

        if( _this.field.relation == 'n-n' ){
            var label = _this.field['n-n'].right_label;
            var key = _this.field['n-n'].right_key;
        }

        if( multiple ){
    
            if( $type(this.field.tableItems) == 'array' ){
                this.field.tableItems.each(function(item){
                    if(!item) return;
                    
                    if( _this.field.lang && item.lang != _this.field.lang && item.lang ) return;
                    
                    var text = '';
                    if( _this.field.table_view ){
                        $H(_this.field.table_view).each(function(val, mykey){
                            var _val = '';
                            switch( val ){
                            case 'time':
                                _val = new Date(item[mykey]*1000).format('db');
                                break;
                            default:
                                _val = item[mykey];
                            }
                            text = text + ', ' + _val;
                        });
                        text = text.substr(2, text.length);
                    } else if( item && item[label] ){
                        text = item[label];
                    }
    
                    var t = new Element('option', {
                        text: text,
                        value: item[key]
                    })
                    if(t && _this.input )
                        t.inject( _this.input );
                });
            } else if ( $type(this.field.tableItems) == 'object' ){
    
                $H(this.field.tableItems).each(function(item, key){
                    var t = new Element('option', {
                        text: item,
                        value: key
                    })
                    if(t && _this.input )
                        t.inject( _this.input );
                });
            }


            this.main.setStyle('width', 355);
            //if( this.field.small )
            //    this.main.setStyle('height', 80);
            //else
            //    this.main.setStyle('height', 115);
            
            var table = new Element('table').inject(this.input.getParent());
            var tbody = new Element('tbody').inject( table );
            
            var tr = new Element('tr').inject( tbody );
            var td = new Element('td').inject(tr);
            var td2 = new Element('td', {width: 32, style: 'vertical-align: middle;'}).inject(tr);
            var td3 = new Element('td').inject(tr);
            
            this.input.setStyle('width', selWidth);
            
            
            this.input.inject( td );

            var toRight = new ka.Button('»')
            .addEvent('click', function(){
                if( this.input.getSelected() ){
                    this.input.getSelected().each(function(obj){
                        var clone = obj.clone();
                        clone.inject( this.inputVals );
                        obj.set('disabled', true);
                        obj.set('selected', false);
                    }.bind(this));
                }
            }.bind(this))
            .setStyle('left', -2)
            .inject( td2 );
            
            new Element('span', {html: "<br /><br />"}).inject( td2 );
                
            var toLeft = new ka.Button('«')
            .addEvent('click', function(){
                if( this.inputVals.getSelected() ){
                    this.input.getElement('option[value='+this.inputVals.value+']').set('disabled', false);
                    this.inputVals.getSelected().destroy();
                }
            }.bind(this))
            .setStyle('left', -2)
            .inject( td2 );
            

            this.input.addEvent('dblclick', function(){
                toRight.fireEvent('click');
            }.bind(this))
            
            
            this.inputVals = new Element('select', {
                size: this.field.size,
                style: 'width: '+selWidth+'px'
            })
            .addEvent('dblclick', function(){
                toLeft.fireEvent('click');
            }.bind(this))
            .inject(td3);
            

            if( this.field.tinyselect )
                this.inputVals.setStyle('width', 75);
            
        } else {
            ///not mutiple
            this.select = new ka.Select();
            
            this.select.addEvent('change', function(){
                this.fireEvent('change', this.getValue());
            }.bind(this));

            this.select.inject( this.fieldPanel );
                        
            if( typeOf(this.field.tableItems) == 'array' ){

                Array.each(this.field.tableItems, function(item, key){
                    this.select.add( item[key], item[label] );
                }.bind(this));

            } else if ( typeOf(this.field.tableItems) == 'object' ){
    
                Object.each(this.field.tableItems, function(item, key){
                    this.select.add( key, item );
                }.bind(this));

            }
        }
        
        if( sortable ){
            var td4 = new Element('td').inject(tr);
            var elUp = new Element('img', {
                  src: _path+'inc/template/admin/images/icons/arrow_up.png',
                  style: 'display: block; cursor: pointer;'
            }).addEvent('click', function() {
              if(!this.inputVals.getElements('option') || this.inputVals.getElements('option').length < 2 || !this.inputVals.getSelected())
                 return;
              
              var selOption = this.inputVals.getSelected();
              //check if el is top                
              if(!selOption.getPrevious('option') || !$defined(selOption.getPrevious('option')[0]))
                 return;
              var selOptionClone = selOption.clone(true).inject(selOption.getPrevious('option')[0], 'before');
              selOption.destroy();

           }.bind(this)).inject(td4); 
            
            new Element('div', {html: "<br /><br />"}).inject( td4 );
            // var elDown = new ka.Button('Dw').addEvent('click',
            var elDown = new Element('img', {
                src: _path+'inc/template/admin/images/icons/arrow_down.png',
                style: 'display: block; cursor: pointer;'
            }).addEvent('click', function(){
                
                if(!this.inputVals.getElements('option') || this.inputVals.getElements('option').length < 2 || !this.inputVals.getSelected())
                    return;
                
                var selOption = this.inputVals.getSelected();
                
                //check if el is top                
                if(!selOption.getNext('option') || !$defined(selOption.getNext('option')[0]))
                    return;
                    
                var selOptionClone = selOption.clone(true).inject(selOption.getNext('option')[0], 'after');
                selOption.destroy();

            }.bind(this)).inject(td4);         
        }

        if( this.field.directory ){
            this.input.set('title', _('This list is based on files on this directory:')+' '+this.field.directory);
            new Element('div', {
                text:  _('Based on:')+' '+this.field.directory,
                style: 'font-size: 11px; color: silver'
            }).inject( this.select||this.input, 'after' );
        }

        this._setValue = function( pValue, pIntern ){
         
            if( multiple ){
                this.inputVals.empty();
                this.input.getElements('option').set('disabled', false);
            
         
                this.input.getElements('option').each(function(option){
                    option.selected = false;
                });
            }

            if( _this.field['relation'] == 'n-n' || multiple ){
                if( typeOf(pValue) == 'string' ) pValue = JSON.decode( pValue );
                if( typeOf(pValue) != 'array' ) pValue = []; 
            }
            
            if( _this.field['relation'] == 'n-n' ){

                pValue.each(function( _item ){
                   _this.input.getElements('option').each(function(option){
                        if( option.value == _item[_this.field['n-n'].middle_keyright] )
                           if( multiple ){
                              option.clone().inject( this.inputVals );
                              option.set('disabled', true);
                              option.set('selected', false);
                           } else {
                              option.selected = true;
                           }
                    }.bind(this));
                }.bind(this));
                
            } else if( multiple && !sortable){
                
                this.input.getElements('option').each(function(option){
                    if( pValue.contains( option.value ) ){
                     option.clone().inject( this.inputVals );
                     option.set('disabled', true);
                     option.set('selected', false);
                    }
                }.bind(this));

            } else if( multiple ){
                 pValue.each(function(pItem) {
                   iSelOption = this.input.getElement('option[value="'+pItem+'"]');
                         if($defined(iSelOption) && $type(iSelOption) != 'null'){
                            
                       iSelOption.clone().inject( this.inputVals );
                       iSelOption.set('disabled', true);
                       iSelOption.set('selected', false);
                         }
                 }.bind(this));  
               
                
            } else {
                this.select.setValue( pValue );
            }
            
            if( pIntern )
               this.fireEvent('change', this.getValue());
        };

        this.getValue = function(){
            var res = [];
            if( multiple ){
                _this.inputVals.getElements('option').each(function(option){
                    res.include( option.value );
                });
            } else {
                res = _this.select.getValue();
            }
            return res;
        }

    },

    renderWysiwyg: function(){
        this.lastId = 'WindowField'+this.fieldId+((new Date()).getTime())+''+$random(123,5643)+''+$random(13284134,1238845294);
        //this.lastId = 'field'+(new Date()).getTime();
        
        this.input = new Element('textarea', {
            id: this.lastId,
            name: this.lastId,
            value: this.field.value,
            styles: {
                'height': (this.field.height)?this.field.height:80,
                'width': (this.field.width)?this.field.width:''
            }
        }).inject( this.fieldPanel );

        //(function(){
       //     tinyMCE.execCommand('mceAddControl', false, this.lastId );
        //    initTiny( this.lastId );
        //}).bind(this).delay(100);
        if(! this.field.withOutTinyInit ){
            try {
            //initResizeTiny( this.lastId, _path+'inc/template/css/kryn_tinyMceContent.css' );
/*            tinyMCE.init({
                mode: 'exact',
                element: this.lastId,
                content_css: _path+'inc/template/css/kryn_tinyMceContent.css',
                document_base_url : _path
            });
*/
            //initTiny( this.lastId, _path+'inc/template/css/kryn_tinyMceContent.css' );
            } catch(e){
            }
        }

        this.initTiny = function(){
            ka._wysiwygId2Win.include( this.lastId, this.field.win );
            initResizeTiny( this.lastId, _path+'inc/template/css/kryn_tinyMceContent.css' );
        }.bind(this);

        this._setValue = function( pValue, pIntern ){
            var tiny = tinyMCE.get( this.lastId );
            if( tiny )
                tiny.setContent( pValue );
            else
                this.input.value = pValue;
            
            if( pIntern )
                this.fireEvent('change', this.getValue());
        }

        this.getValue = function(){
            if(! tinyMCE.get( this.lastId ) )
                return false;
            return tinyMCE.get( this.lastId ).getContent();
        }
    },

    renderDate: function( pOptions ){
        this.input = new Element('input', {
            'class': 'text ka-field-dateTime',
            type: 'text',
            style: 'width: 110px'
        })
        .inject( this.fieldPanel );
        var datePicker = new ka.datePicker( this.input, pOptions );

        if( this.field.win ){
            this.field.win.addEvent('resize', datePicker.updatePos.bind(datePicker));
            this.field.win.addEvent('move', datePicker.updatePos.bind(datePicker));
            
        }
        
        this.getValue = function(){
            return datePicker.getTime();
        };
        this._setValue = function( pVal, pIntern ){
            datePicker.setTime( (pVal != 0)?pVal:false );
            
            if( pIntern )
                this.fireEvent('change', this.getValue());
        }.bind(this);
        
        if( this.field['default'] && this.field['default'] != "" ){
            var time = new Date(this.field['default']).getTime();
            if( this.field['default'] ){
                var time = new Date().getTime();
            }
            this.setValue( time, true );
        }
    },

    renderCheckbox: function(){
        var _this = this;
        
        var div = new Element('div', {
            'class': 'ka-field-checkbox ka-field-checkbox-off'
        }).inject( this.fieldPanel );
        
        new Element('div', {
            text: 'l',
            style: 'font-weight: bold; color: white; position: absolute; left: 18px; font-size: 15px; top: 2px;'
        }).inject( div );
        
        
        new Element('div', {
            text: 'O',
            style: 'font-weight: bold; color: #f4f4f4; position: absolute; right: 15px; font-size: 15px; top: 2px;'
        }).inject( div );
        
        var knob = new Element('div', {
            'class': 'ka-field-checkbox-knob'
        }).inject( div );
        
        this.value = false;
        
        knob.addEvent('click', function(){
            this.setValue( this.value==false?1:0 );
        }.bind(this));

        this.getValue = function(){
            return this.value;
        }.bind(this);

        this._setValue = function( p ){
            if( p == 0 ) p = false;
            if( p == 1 ) p = true;
            this.value = p;
            if( this.value ){
                div.addClass('ka-field-checkbox-on');
                div.removeClass('ka-field-checkbox-off');
            } else {
                div.addClass('ka-field-checkbox-off');
                div.removeClass('ka-field-checkbox-on');
            }
        }.bind(this);

    },

    renderNumber: function(){
        
        this.renderText();
        
        this.input.addEvent('keyup', function(){
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        
    },
    
    renderText: function(){
        var _this = this;
        this.input = new Element('input', {
            'class': 'text',
            type: 'text'
        })
        .addEvent('blur', function(){
            _this.isEmpty();
        })
        .inject( this.fieldPanel );
        var _this = this;
        
        if( this.field.width ){
            this.input.setStyle('width', this.field.width);
        }

        if( this.field.check == 'kurl' ){
            
            this.input.addEvent('keyup', function(e){
                
                var old = this.getSelectedRange();
                var o = ['ä', 'Ä', 'ö', 'Ö', 'ü', 'Ü', 'ß'];
                
                o.each(function(char){
                    if( this.value.contains(char) ){
                        old.start++;
                        old.end++;
                    }
                }.bind(this));
                
                this.value = _this.checkKurl( this.value);
                
                /*if( this.value.substr(0, 1) == '-' )
                    this.value = this.value.substr( 1, this.value.length );
                */
                
                this.selectRange( old.start, old.end );
                 
            });
        }
    },

    checkKurl: function( pValue ){
        if( this.field.check == 'kurl' )
            return pValue
            .replace(/Ä/g, 'AE')
            .replace(/ä/g, 'ae')
            .replace(/Ö/g, 'OE')
            .replace(/ö/g, 'oe')
            .replace(/Ü/g, 'UE')
            .replace(/ü/g, 'ue')
            .replace(/ß/g, 'ss')
            .replace(/\W/g, '-').toLowerCase();
        else
            return pValue;
    },

    renderTextarea: function(){
        var _this = this;
        this.input = new Element('textarea', {
            styles: {
                'height': (this.field.height)?this.field.height:80,
                'width': (this.field.width)?this.field.width:''
            }
        })
        .addEvent('blur', function(){
            _this.isEmpty();
        })
        .inject( this.fieldPanel );
    },
    
    renderFile: function(){
        this.input = new Element('input', {
            'class': 'text',
            type: 'file',
            name: this.fieldId
        }).inject( this.fieldPanel );
        var _this = this;
        this._setValue = function(){};
    },

    renderPassword: function(){
        var _this = this;
        this.input = new Element('input', {
            'class': 'text',
            type: 'password'
        })
        .addEvent('blur', function(){
            _this.isEmpty();
        })
        .inject( this.fieldPanel );
    },

    empty: function(){
        if( this.emptyIcon ) this.emptyIcon.destroy();
        if( !this.input ) return;

        this.emptyIcon = new Element('img',{
            src: _path+'inc/template/admin/images/icons/exclamation.png',
            'class': 'emptyIcon'
        }).inject( this.input.getParent()  );
        //this.emptyIcon.setStyles({
        //    left: this.input.getPosition(this.input.getParent()).x + this.input.getSize().x
        //});
        this.input.set('class', this.input.get('class')+' empty' );
    },

    highlight: function(){
        if( !this.input ) return;
        this.input.highlight();
    },

    isEmpty: function(){
        if( this.field.empty == false){
            var val = this.getValue();
            if( val == '' ){
                this.empty();
                return true;
            }
        }
        if( this.emptyIcon ) this.emptyIcon.destroy();

        if( this.input )
            this.input.set('class', this.input.retrieve('oldClass') );

        return false;
    },

    isOk: function(){
        if( this.field.empty === false )
            return !this.isEmpty();
        return true;
    },

    getValue: function(){
        if( !this.input ) return;
        return this.input.value;
    },

    toString: function(){
        return this.getValue();
    },

    setValue: function( pValue, pIntern ){
        
        if( pValue == null && this.field.default ){
            pValue = this.field.default;
        }
        
        if( this.input )
            this.input.value = pValue;

        if( this._setValue ){
            this._setValue( pValue, pIntern );
        }

        if( pIntern )
            this.fireEvent('change', this.getValue()); //fires check-depends too
        else
            this.fireEvent('check-depends');
    },
    
    _setValue: function( pValue, pIntern ){
        //Override this function to define a own setter
    },

    //should not be used anymore
    //use instead: this.fireEvent('change', this.getValue());
    onChange: function(){
        this.fireEvent('change', this.getValue());
    },

    findWin: function(){
    
        if( this.win ) return;
        
        var win = this.toElement().getParent('.kwindow-border');
        if( !win ) return;
        
        this.win = win.retrieve('win');
    },
    
    toElement: function(){
        return ( this.field.tableitem )? this.tr : this.main;
    },

    inject: function( pTo, pP ){
    
        if( this.field.onlycontent ){
            this.fieldPanel.inject( pTo, pP );
            return this;
        }
            
        
        if( this.main.getDocument() != pTo.getDocument() ){
            pTo.getDocument().adoptNode( this.tr || this.main );
        }
        
        if( this.tr )
            this.tr.inject( pTo, pP );
        else
            this.main.inject( pTo, pP );
        
        if( this.customObj )
            this.customObj.inject( this.fieldPanel );
        
        this.findWin();
        
        return this;
    },
    
    destroy: function(){
        this.main.destroy();
    },

    hide: function(){
        if( this.tr )
            this.tr.setStyle( 'display', 'none' );
        else
            this.main.setStyle( 'display', 'none' );
    },
    
    
    /**
     * Is hidden because a depends issue.
     */
    isHidden: function(){
        if( this.tr ){
            if( this.tr.getStyle('display') == 'none' ){
                return true;
            }
        } else if( this.main.getStyle('display') == 'none' )
            return true;
        return false;
    },

    show: function(){
        if( this.tr )
            this.tr.setStyle( 'display', 'table-row' );
        else
            this.main.setStyle( 'display', 'block' );
    },    
    
    
    initLayoutElement: function(){
        
        _win = this.field.win;    
        this.obj = new ka.field_layoutElement(this);
        
        this._setValue = this.obj.setValue.bind(this.obj);
        this.getValue = this.obj.getValue.bind(this.obj);
    },
    
    setArrayValue: function( pValues, pKey ){
    
        if( pValues == null ){
            this.setValue(null,true);
            return;
        }
    
        var values = pValues;
        var keys = pKey.split('[');
        var notFound = false;
        Array.each(keys, function(key){

            if( notFound ) return;
            if( values[ key.replace(']','')] )
                values = values[ key.replace(']','')];
            else
                notFound = true;

        });
        
        if( !notFound )
            this.setValue( values );
    },
    
    initMultiUpload: function() {    
        //need to pass the win instance seperatly otherwise the setOptions method will thrown an error 
        _win = this.field.win;    
        this.field.win = false;
        
        
        _this = this;
        //init ext js class
        if(this.field.extClass){
            try {
                this.obj = new window[ this.field.extClass ]( this.field, _win, _this );                
            }catch(e) {
                
                this.obj = new ka.field_multiUpload(this.field, _win, _this);
            }
        }else{
            this.obj = new ka.field_multiUpload(this.field, _win, _this);
        }
        
        this.isEmpty = this.obj.isEmpty.bind(this.obj);
    }
});
