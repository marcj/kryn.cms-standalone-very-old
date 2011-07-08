ka.windowCombine = new Class({

    Extends: ka.list,
    
    
    renderLayout: function(){
    
        
        this.main = new Element('div',{
            'class': 'ka-list-main',
            style: 'bottom: 0px; top: 0px; overflow: hidden;'
        }).inject( this.win.content );
        
        this.mainLeft = new Element('div', {
            style: 'position: absolute; left: 0px; top: 0px; bottom: 0px; width: 250px;'
        }).inject( this.main );
        
        this.mainLeftItems = new Element('div', {
            style: 'position: absolute; left: 0px; top: 0px; bottom: 0px; right: 0px; border-right: 1px solid silver; overflow: auto;'
        }).inject( this.mainLeft );
        
        this.bottom = new Element('div',{
            'class': 'ka-list-bottom'
        }).inject( this.win.content );
        
    },
    
    renderActionbar: function(){
        var _this = this;
    
        if( this.values.multiLanguage )
        	this.win.extendHead();
        
        if( this.values.add || this.values.remove || this.values.custom){
            this.actionsNavi = this.win.addButtonGroup();
        }

        if( this.values.remove ){
            this.actionsNavi.addButton(_('Remove selected'), _path+'inc/template/admin/images/icons/'+this.values.iconDelete, function(){
               this.removeSelected();
            }.bind(this));
        }

        if( this.values.add ){
            this.actionsNavi.addButton(_('Add'), _path+'inc/template/admin/images/icons/'+this.values.iconAdd, function(){
                ka.wm.openWindow( _this.win.module, _this.win.code+'/add', null, null, {
                	lang: (_this.languageSelect)?_this.languageSelect.value:false
                });
            });
        }
     
     
           
        this.sortSelect = new Element('select').inject( this.bottom );
        this.values.columns.each(function(column,id){
        
            new Element('option', {
                text: _(column.label),
                value: id
            }).inject( this.sortSelect );
        
        }.bind(this));
        
        this.sortSelect.addEvent('change', function(){
    
            this.sortField = this.sortSelect.value;    
            
            if( this.values.columns[this.sortField] && (this.values.columns[this.sortField]['type'] == 'datetime' || 
                this.values.columns[this.sortField]['type'] == 'date') ){
                this.sortDirection = 'DESC';
            }
                            
            this.reload()
        
        }.bind(this));
    
    },
    
    _loadItems: function( pItems ){
        var _this = this;

        this.checkboxes = [];
        this.loader.hide();

        this._lastItems = pItems;

        //this.ctrlMax.set('text', '/ '+pItems.maxPages);

        _this.tempcount = 0;
        
        var lastSortValue = false;
        
        if( pItems.items ){
            pItems.items.each(function(item){
            
                var value = this.getItemTitle( item, this.sortField );
                
                
                if( !this.values.columns[this.sortField]['type'] || this.values.columns[this.sortField].type == "text" ){
                    
                    var firstChar = value.substr(0,1);
                    if( firstChar != lastSortValue ){
                        lastSortValue = firstChar;
                        this.addSplitTitle( firstChar );
                    }
                    
                } else {
                
                    if( ["datetime", "date"].contains(this.values.columns[this.sortField]['type']) ){
                        
                        var time = new Date(item['values'][this.sortField]*1000);
                        
                        //var cur = new Date();
                        
                        value = time.timeDiffInWords();
                        
                        //if( cur.format('%d') == cur.format('%d') ){
                        //    value = _("Today");
                        //} 
                        
                    }
                
                    if( value != lastSortValue ){
                        lastSortValue = value;
                        this.addSplitTitle( lastSortValue );
                    }
                }
                
            
                _this.addItem( item );
                _this.tempcount++;
            }.bind(this));
        }
    },
    
    getItemTitle: function( pItem, pColumnId ){
        
        var value = pItem['values'][pColumnId];
        
        var column = this.values.columns[pColumnId];

        if( column.format == 'timestamp' ){
            value = new Date(value*1000).toLocaleString();
        }

        if( column.type == 'datetime' || column.type == 'date' ){
            if( value != 0 && value ){
                var format = ( !column.format ) ? '%d.%m.%Y %H:%M': column.format;
                value = new Date(value*1000).format( format );
            } else {
                value = '';
            }
        }
        

        if( column.type == 'select' ){
        	value = pItem['values'][pColumnId+'__label'];
        }

        if( column.imageMap ){
            value = '<img src="'+_path+column.imageMap[value]+'"/>';
        }
    
        return value;
    },
    
    clear: function(){
        this.mainLeftItems.empty();
    },
    
    loadItem: function( pItem ){
    
        this.mainLeftItems.getChildren().each(function(item,i){
        
            item.removeClass('active');
            if( item.retrieve('item') == pItem )
                item.addClass('active');
        
        });
    
    },
    
    addSplitTitle: function( pItem ){
        new Element('div', {
            'class': 'ka-list-combine-splititem',
            html: pItem
        }).inject( this.mainLeftItems );
    },
    
    addItem: function( pItem ){
        
        var layout = '';
        
        if( this.values.itemLayout ){
            layout = this.values.itemLayout;
        } else {
            
            if( this.values.columns.title ){
                layout += '<h2 id="title"></h2>';
            } else if( this.values.columns.name ){
                layout += '<h2 id="name"></h2>';
            }
            
            layout += '<div class="subline">';
            
            var c = 1;
            this.values.columns.each(function(bla,id){
            
                if( id == "title" ) return;
                if( id == "name" ) return;
                
                if( c > 2 ) return;
                
                if( c == 2 )
                    layout += ', '; 
                
                layout += "<span id="+id+"></span>";
                c++;
            
            }.bind(this));
            
            layout += "</div>";
        }
        
        var item = new Element('div', {
            html: layout,
            'class': 'ka-list-combine-item'
        })
        .store('item', pItem)
        .addEvent('click', this.loadItem.bind(this, pItem))
        .inject( this.mainLeftItems );
        
        //parse
        this.values.columns.each(function(column,columnId){
        
            if( item.getElement('[id='+columnId+']') ){
                
                var value = this.getItemTitle( pItem, columnId );
                
                item.getElement('[id='+columnId+']').set('html', value);
            
            }

        }.bind(this));
        
        return;
        
        var _this = this;
        var tr = new Element('tr',{
            'class': (_this.tempcount%2)?'one':'two'
        }).inject( this.tbody );
       
        if( this.values.remove == true ){
            var td = new Element('td',{
                style: 'width: 21px;'
            }).inject(tr);
            if( pItem['remove'] ){
                var mykey = {};
                this.values.primary.each(function(primary){
                    mykey[primary] = pItem.values[primary];
                });
                //if( this.values.edit ){
                        this.checkboxes.include( new Element('input',{
                        value: JSON.encode(mykey),
                        type: 'checkbox'
                    }).inject(td) );
                //}
                }
        }
        
        this.values.columns.each(function(column,columnId){
            var value = pItem['values'][columnId];

            if( column.format == 'timestamp' ){
                value = new Date(value*1000).toLocaleString();
            }

            if( column.type == 'datetime' || column.type == 'date' ){
                if( value != 0 && value ){
                    var format = ( !column.format ) ? '%d.%m.%Y %H:%M': column.format;
                    value = new Date(value*1000).format( format );
                } else {
                    value = '';
                }
            }
            

            if( column.type == 'select' ){
            	value = pItem['values'][columnId+'__label'];
            }
            

            if( column.imageMap ){
                value = '<img src="'+_path+column.imageMap[value]+'"/>';
            }

            var td = new Element('td', {
                html: value
            })
            .addEvent('click', function(e){
                _this.select(this);
            })
            .addEvent('mousedown', function(e){
                e.stop();
            })
            .addEvent('dblclick', function(e){
                if( _this.values.editCode ){
                    ka.wm.open( _this.values.editCode, pItem ); 
                } else if( pItem.edit ){
                    ka.wm.openWindow( _this.win.module, _this.win.code+'/edit', null, null, pItem );
                }
            })
            .inject( tr );
            
            if( column.type == 'html' )
                td.set('html', value );
            
            //open window if open definied
            //todo: may this section isn't in use ?
            if( pItem.open ){
                td.addEvent('dblclick', function(){
                    var params = ( pItem.open[2] ) ? pItem.open[2] : pItem;
                    ka.wm.openWindow( pItem.open[0], pItem.open[1], null, null, params );
                });
            }
            //todoend
            
            if( column.width > 0 ){
                td.setStyle( 'width', column.width+'px' );
            }
        });
       
        if( this.values.remove == true || this.values.edit == true || this.values.itemActions ){
            var icon = new Element('td',{
                width: 40,
                'class': 'edit'
            }).inject( tr );
           
            if( this.values.itemActions && this.values.itemActions.each ){
                this.values.itemActions.each(function(action){
                    new Element('img', {
                        src: _path+'inc/template/'+action[1],
                        title: action[0]
                    })
                    .addEvent('click', function(){
                        ka.wm.open( action[2], {item: pItem, filter: action[3]} );
                    })
                    .inject( icon );
                });
                icon.setStyle('width', 40+(20*this.values.itemActions.length));
                this.titleIconTd.setStyle('width', 40+(20*this.values.itemActions.length));
            }
            
            if( pItem.edit ){
                new Element('img', {
                    src: _path+'inc/template/admin/images/icons/'+this.values.iconEdit
                })
                .addEvent('click', function(){
                    if( _this.values.editCode ){
                        ka.wm.open( _this.values.editCode, pItem ); 
                    } else if( pItem.edit ){
                       ka.wm.openWindow( _this.win.module, _this.win.code+'/edit', null, null, pItem );
                    }
                })
                .inject( icon );
            }
            if( pItem['remove'] ){
                new Element('img', {
                    src: _path+'inc/template/admin/images/icons/'+this.values.iconDelete
                })
                .addEvent('click', function(){
                    _this.win._confirm(_('Really delete?'), function(res){
                        if(!res) return;
                        _this.deleteItem( pItem );
                    });
                })
                .inject( icon );
            }
        }
    }
    
});
