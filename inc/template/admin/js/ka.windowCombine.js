ka.windowCombine = new Class({

    Extends: ka.list,
    lastSortValue: false,
    itemsLoadedCount: 0,
    
    searchPaneHeight: 110,
    
    renderLayout: function(){
        
        this.main = new Element('div',{
            'class': 'ka-list-main',
            style: 'bottom: 0px; top: 0px; overflow: hidden;'
        }).inject( this.win.content );
        
        
        this.mainLeft = new Element('div', {
            style: 'position: absolute; left: 0px; top: 0px; bottom: 0px; width: 250px; border-right: 1px solid silver;'
        }).inject( this.main );
        
        this.mainLeftTop = new Element('div', {
            style: 'position: absolute; left: 0px; padding: 5px 6px; top: 0px; height: 14px; right: 0px; border-bottom: 1px solid gray;',
            'class': 'ka-list-combine-left-top'
        }).inject( this.mainLeft );
        
        this.sortSpan = new Element('span', {
            style: 'margin-left: 30px; line-height: 17px;'
        }).inject( this.mainLeftTop );
        
        this.itemCount = new Element('div', {
            'class': 'ka-list-combine-left-itemcount'
        }).inject( this.mainLeftTop );
        
        this.itemsFrom = new Element('span', {text: '0'}).inject( this.itemCount );
        new Element('span', {text: '-'}).inject( this.itemCount );
        this.itemsLoaded = new Element('span', {text: '0'}).inject( this.itemCount );
        new Element('span', {text: '/'}).inject( this.itemCount );
        this.itemsMax = new Element('span', {text: '0'}).inject( this.itemCount );
        
        this.mainLeftSearch = new Element('div', {
            'class': 'ka-list-combine-searchpane'
        }).inject( this.mainLeft );
        
        this.mainLeftItems = new Element('div', {
            style: 'position: absolute; left: 0px; top: 25px; bottom: 0px; right: 0px; overflow: auto;'
        })
        .addEvent('scroll', this.checkScrollPosition.bind(this))
        .inject( this.mainLeft );
        
        
        this.win.addEvent('resize', this.checkScrollPosition.bind(this));
        
        this.mainRight = new Element('div', {
            'class': 'ka-list-combine-right'
        }).inject( this.main );
    },
    
    renderActionbar: function(){
        var _this = this;
        
        this.renderSearchPane();
    
        if( this.values.multiLanguage )
        	this.win.extendHead();
        
        if( this.values.add || this.values.remove || this.values.custom){
            this.actionsNavi = this.win.addButtonGroup();
            this.actionsNavi.setStyle('margin-right', 159);
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
        
        
        
        this.searchIcon = new Element('div', {
            'class': 'ka-list-combine-searchicon',
            html: '<img src="'+_path+'inc/template/admin/images/search-icon.png" />',
        })
        .addEvent('click', this.toggleSearch.bind(this))
        .inject( this.mainLeftTop );
        
        
        
     
                
        this.sortSelect = new ka.Select();
        this.sortSelect.inject(this.sortSpan);
        this.sortSelect.setStyle('width', 55);
        //this.sortSelect = new Element('select').inject( this.sortSpan );
        this.values.columns.each(function(column,id){
        
            this.sortSelect.add(id+'______asc', _(column.label)+
                ' <img style="position: relative; top: -1px" src="'+_path+'inc/template/admin/images/icons/bullet_arrow_up_s.png" />');
            this.sortSelect.add(id+'______desc', _(column.label)+
                ' <img style="position: relative; top: -1px" src="'+_path+'inc/template/admin/images/icons/bullet_arrow_down_s.png" />');
            /*
            new Element('option', {
                text: _(column.label),
                value: id
            }).inject( this.sortSelect );*/
        
        }.bind(this));
        
        this.sortSelect.addEvent('change', function(){
    
            var sortId = this.sortSelect.getValue();
            
            
            this.sortField = sortId.split('______')[0];
            
            /*if( this.values.columns[this.sortField] && (this.values.columns[this.sortField]['type'] == 'datetime' || 
                this.values.columns[this.sortField]['type'] == 'date') ){
                this.sortDirection = 'DESC';
            }*/
            
            this.sortDirection = sortId.split('______')[1];
            
            this.reload();
            
        
        }.bind(this));
        
        //this.sortSelect.value = this.sortField;
        
        
        this.sortSelect.setValue( this.sortField+'______'+((this.sortDirection=='DESC')?'desc':'asc') );
        
        this.createItemLoader();
        
        /*
        this.userFilter = new Element('div', {
            'class': 'ka-list-combine-userfilter'
        }).inject( this.mainLeftTop );
        
        new Element('a', {
            text: _('me')
        }).inject( this.userFilter );
        
        
        new Element('a', {
            text: _('all'),
            'class': 'active'
        }).inject( this.userFilter );
        
        new Element('a', {
            html: '<img src="'+_path+'inc/template/admin/images/icons/tree_minus.png" />',
            style: 'padding-left: 2px; padding-right: 2px;'
        }).inject( this.userFilter );
        */
        /*
        this.searchBar = this.win.addButtonGroup();
        
        new Element('input', {
            'class': 'ka-list-combine-search',
            value: 'Keyword ...'
        })
        .addEvent('click', function(e){
            e.stop();
        })
        .inject( this.searchBar.boxWrapper );
        
        
        new Element('a', {
            html: '<img src="'+_path+'inc/template/admin/images/icons/tree_minus.png" />',
            style: 'padding-left: 2px; padding-right: 2px;'
        }).inject( this.searchBar.boxWrapper );
        
        this.searchBar.setStyle('margin-right', 3);
        */
    
    },
    
    renderSearchPane: function(){
        
        var table = new Element('table').inject( this.mainLeftSearch );;
        
        this.searchPane = new Element('tbody', {
        }).inject( table );

        this.searchFields = new Hash();
        var doSearchNow = false;

        if( this.values.filter && this.values.filter.each ){
            this.values.filter.each(function(filter, key){

                
                var mkey = key;
                
                if( $type(key) == 'number' ){
                    mkey = filter;
                }
                
                var field = this.values.filterFields[ mkey ];
                
                
                var title = this.values.columns[mkey].label;
                field.label = _(title);
                field.small = true;
                field.tableitem = true;
                field.tableitem_title_width = 50;
                
                var fieldObj = new ka.field(field)
                .addEvent('change', this.doSearch.bind(this))
                .inject( this.searchPane );
                
                this.searchFields.set(mkey, fieldObj );
                
                if( field.type == 'select' ){
                	new Element('option',{
                		value: '',
                		text: _('-- Please choose --')
                	}).inject(fieldObj.input, 'top');
                	fieldObj.setValue("");
                }
                
                if( this.win.params && this.win.params.filter ){
                	$H(this.win.params.filter).each(function(item,key){
                		if( item == mkey ){
                			fieldObj.setValue( this.win.params.item.values[key] );
                        	doSearchNow = true;
                		}
                	}.bind(this));
                }

            }.bind(this));
        } else {
            this.filterButton.destroy();
        }
        
        if( doSearchNow ){
        	this.toggleSearch();
        	this.loadAlreadyTriggeredBySearch = true;
        	this.doSearch();
        }
    },
    
    
    doSearch: function(){
        if( this.lastTimer )
            $clear( this.lastTimer );

        var mySearch = function(){
            this.reload();
        }.bind(this);
        this.lastTimer = mySearch.delay(200);
    },
    
    renderLoader: function(){
        
    },
    
    checkScrollPosition: function(){
    
        if( this.loadingNewItems ) return;
    
        if( this.mainLeftItems.getScroll().y - (this.mainLeftItems.getScrollSize().y-this.mainLeftItems.getSize().y) == 0 ){
            this.loadMore();
        } else if( this.maxItems > 0 && (this.mainLeftItems.getScrollSize().y-this.mainLeftItems.getSize().y) == 0 )
            this.loadMore();
    
    },
    
    loadMore: function(){    
        if( this.currentPage < this.maxPages ){
            //load next page
            this.loadPage(parseInt(this.currentPage)+1);
        }
    },
    
    changeLanguage: function(){
    
        this.reload();
    },
    
    reload: function(){
        this.clearItemList();
    	this.loadPage(1);
    },
    
    clearItemList: function(){    
        this.lastSortValue = false;
        this.itemsLoadedCount = 0;
        
        this.mainLeftItems.empty();
        this.createItemLoader();
    },
    
    createItemLoader: function(){
    
        this.itemLoader = new Element('div', {
            'class': 'ka-list-combine-item ka-list-combine-itemloader'
        }).inject( this.mainLeftItems );
        
        this.itemLoaderStop();
    
    },
    
    itemLoaderStop: function(){
        this.loadingNewItems = false;
        if( !this.itemLoader ) return;
        this.itemLoader.set('html', _('Scroll to the bottom to load more entries.'));
    },
    
    itemLoaderEnd: function(){
        if( !this.itemLoader ) return;
        this.itemLoader.set('html', _('No entries left.'));
    },
    
    itemLoaderStart: function(){
        this.loadingNewItems = true;
        if( !this.itemLoader ) return;
        this.itemLoader.set('html', '<img src="'+_path+'inc/template/admin/images/loading.gif" />'+'<br />'+_('Loading entries ...'));
    },
    
    itemLoaderNoItems: function(){
        this.itemLoader.set('html', _('There are no entries.'));
    },
    
    renderMultilanguage: function(){
        //chooser
    
        
        this.languageSelect = new ka.Select();
        this.languageSelect.inject(this.sortSpan);
        this.languageSelect.setStyle('width', 55);
        
        
        this.languageSelect.addEvent('change', this.changeLanguage.bind(this));

        $H(ka.settings.langs).each(function(lang,id){
        
            this.languageSelect.add(id, lang.langtitle+' ('+lang.title+', '+id+')');
            
        }.bind(this));
    	
    	this.languageSelect.setValue( window._session.lang );
    
    },
    
    toggleSearch: function(){
    
        if( !this.searchOpened ){
            this.searchEnable = 1;
            this.searchIcon.addClass('ka-list-combine-searchicon-active');
            this.mainLeftSearch.tween('height', this.searchPaneHeight);
            this.mainLeftSearch.setStyle('border-bottom', '1px solid silver');
            this.mainLeftItems.tween('top', 25+this.searchPaneHeight+1);
            this.searchOpened = true;
        } else {
            
            this.searchEnable = 0;
            this.searchIcon.removeClass('ka-list-combine-searchicon-active');
    
            new Fx.Tween(this.mainLeftSearch).start('height', 0).chain(function(){
                this.mainLeftSearch.setStyle('border-bottom', '0px');
                this.checkScrollPosition();
            }.bind(this));
            
            this.mainLeftItems.tween('top', 25);
            this.searchOpened = false;
            this.reload();
        }
    
    },
    
    renderItems: function( pItems ){
        var _this = this;

        this.checkboxes = [];
        

        this._lastItems = pItems;
        
        this.currentPage = pItems.page;
        this.maxPages = pItems.maxPages;
        this.maxItems = pItems.maxItems;

        //this.ctrlMax.set('text', '/ '+pItems.maxPages);

        _this.tempcount = 0;
        
        
        if( pItems.items ){
            Object.each(pItems.items, function(item){
            
                this.itemsLoadedCount++;
            
                var value = this.getItemTitle( item, this.sortField );
                
                
                if( !this.values.columns[this.sortField]['type'] || this.values.columns[this.sortField].type == "text" ){
                    
                    var firstChar = value.substr(0,1).toUpperCase();
                    if( firstChar != this.lastSortValue ){
                        this.lastSortValue = firstChar;
                        this.addSplitTitle( '<b>'+firstChar+'</b>' );
                    }
                    
                } else {
                
                    if( ["datetime", "date"].contains(this.values.columns[this.sortField]['type']) ){
                        
                        if( item['values'][this.sortField] > 0 ){
                        
                            var time = new Date(item['values'][this.sortField]*1000);
                            value = time.timeDiffInWords();
                            
                        } else {
                            value = _('No value');
                        }
                        
                        //if( cur.format('%d') == cur.format('%d') ){
                        //    value = _("Today");
                        //} 
                        
                    }
                
                    if( value != this.lastSortValue ){
                        this.lastSortValue = value;
                        this.addSplitTitle( this.lastSortValue );
                    }
                }
                
            
                _this.addItem( item );
                _this.tempcount++;
            }.bind(this));
        }
        
        if( pItems.maxItems > 0 ){
            if( this.currentPage == pItems.maxPages ){
                this.itemLoaderEnd();
            } else {
                this.itemLoaderStop();
            }
        } else {
            this.itemLoaderNoItems();
        }
        
        

        if( pItems.maxItems > 0 ){
            this.itemsFrom.set('html', 1);
            this.itemsLoaded.set('html', this.itemsLoadedCount);
            this.itemsMax.set('html', pItems.maxItems);
        } else {
            this.itemsFrom.set('html', 0);
            this.itemsLoaded.set('html', this.itemsLoadedCount);
            this.itemsMax.set('html', pItems.maxItems);
        }
        
        if( pItems.maxItems > 0 && (this.mainLeftItems.getScrollSize().y-this.mainLeftItems.getSize().y) == 0 )
            this.loadMore();
            
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
    
    prepareLoadPage: function(){
        
        //this.mainLeftItems.empty();
        this.itemLoaderStart();
        
    },
    
    loadItem: function( pItem ){
        var _this = this;

        var found = false;
        while( !found ){
            //until we found it in the item list
        
            this.mainLeftItems.getChildren().each(function(item,i){
                
                item.removeClass('active');
                if( item.retrieve('item') == pItem ){
                    item.addClass('active');
                    found = true;
                }
            });
            
            if( found == false ){
                //load next page
            }
        }
        
        
        this.currentItem = pItem;
        
        if( !this.currentEdit ){
        
            this.currentEdit = new ka.windowEdit({
                extendHead: this.win.extendHead.bind(this.win),
                addSmallTabGroup: this.win.addSmallTabGroup.bind(this.win),
                addButtonGroup: this.win.addButtonGroup.bind(this.win),
                addEvent: this.win.addEvent.bind(this.win),
                border: this.win.border,
                module: this.win.module,
                code: this.win.code+'/edit',
                params: pItem
            }, this.mainRight);
            
            this.currentEdit.addEvent('save', this.saved.bind(this));
            
            /*this.currentEdit.addEvent('render', function(){
            
                _this.topTabGroup = this.topTabGroup;
                _this.renderTopTabGroup();
            
            });*/
        } else {
        
            this.currentEdit.win.params = pItem;
            this.currentEdit.loadItem();
        
        }
        
    
    },
    
    saved: function( pItem ){
        
        logger(pItem);
        var sortedColumnChanged = false;
        
        if( sortedColumnChanged ){
                this.reload();
        } else {
        
            var target = false;
            this.mainLeftItems.getChildren().each(function(item,i){
                    
                if( item.retrieve('item') == this.currentItem ){
                    target = item;
                }
                
            }.bind(this));
            
            logger("huhu. "+target);
            
            if( target != false ){
                var newItem = this.addItem( pItem );
                newItem.inject( target, 'before' );
                target.destroy();
            } else {
                this.reload();
            }
        }
    
    },
    
    /*
    renderTopTabGroup: function(){
        if( !this.topTabGroup ) return;
        this.topTabGroup.setStyle('left', 158);
    },*/
    
    addSplitTitle: function( pItem ){
        new Element('div', {
            'class': 'ka-list-combine-splititem',
            html: pItem
        }).inject( this.itemLoader, 'before' );
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
        .inject( this.itemLoader, 'before' );
        
        
        if( this.currentEdit ){
        
            var oneIsFalse = false;
            
	        this.currentEdit.values.primary.each(function(prim){
	           if( this.currentItem['values'][prim] != pItem.values[prim] )
	               oneIsFalse = true;
	        }.bind(this))
	        
	        if( oneIsFalse == false )
	           item.addClass('active');
        }
        
        //parse
        this.values.columns.each(function(column,columnId){
        
            if( item.getElement('[id='+columnId+']') ){
                
                var value = this.getItemTitle( pItem, columnId );
                
                item.getElement('[id='+columnId+']').set('html', value);
            
            }

        }.bind(this));
        
        return item;
        
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
