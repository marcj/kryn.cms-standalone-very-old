ka.windowCombine = new Class({

    Extends: ka.list,
    lastSortValue: false,
    itemsLoadedCount: 0,
    combine: true,
    
    searchPaneHeight: 110,
    
    renderLayout: function(){
        
        this.main = new Element('div',{
            'class': 'ka-list-main',
            style: 'bottom: 0px; top: 0px; overflow: hidden;'
        }).inject( this.win.content );
        
        
        this.mainLeft = new Element('div', {
            style: 'position: absolute; left: 0px; top: 0px; bottom: 0px; width: 265px; border-right: 1px solid silver;'
        }).inject( this.main );
        
        this.mainLeftTop = new Element('div', {
            style: 'position: absolute; left: 0px; padding: 5px 6px; top: 0px; height: 20px; right: 0px; border-bottom: 1px solid gray;',
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
            style: 'position: absolute; left: 0px; top: 31px; bottom: 0px; right: 0px; overflow: auto;'
        })
        .addEvent('scroll', this.checkScrollPosition.bind(this, true))
        .inject( this.mainLeft );
        
        this.mainLeftDeleter = new Element('div', {
            'class': 'ka-list-bottom',
            style: 'position: absolute; left:0px; height: 0px; bottom: 0px; right: 0px; overflow: hidden'
        }).inject( this.mainLeft );
        
        new ka.Button(_('Select all'))
        .addEvent('click', function(){
            
            if(!this.checkboxes) return;
            if( this.checkedAll ){
                $$(this.checkboxes).set('checked', false);
                this.checkedAll = false;
            } else{
               $$(this.checkboxes).set('checked', true);
               this.checkedAll = true;
            }
            
        }.bind(this))
        .inject( this.mainLeftDeleter ); 
        
        new ka.Button(_('Remove selected'))
        .addEvent('click', this.removeSelected.bind(this))
        .inject( this.mainLeftDeleter ); 
        
        //window.addEvent('resize', this.checkScrollPosition.bind(this));
        
        this.mainLeftItemsScroll = new Fx.Scroll( this.mainLeftItems, {
            transition: Fx.Transitions.linear,
            duration: 300
        });
        
        this.win.addEvent('resize', this.checkScrollPosition.bind(this,true));
        
        this.mainRight = new Element('div', {
            'class': 'ka-list-combine-right'
        }).inject( this.main );
        
        
        document.addEvent('keydown', this.leftItemsDown.bindWithEvent(this));
    },
    
    leftItemsDown: function( pE ){
    
        if( !this.win.inFront ) return;
            
        pE = new Event(pE);
        
        if( pE.key == 'down' || pE.key == 'up'){
            pE.stop();
        }
        
        var active = this.mainLeftItems.getElement('.active');
        
        var newTarget;
            
        if( pE.key == 'down' ){

            if( active )
                newTarget = active.getNext('.ka-list-combine-item');
            
            if( !newTarget )
                this.mainLeftItems.scrollTo(0,this.mainLeftItems.getScrollSize().y+50);
                
            
            /*if( !newTarget )
                newTarget = this.mainLeftItems.getElement('.ka-list-combine-item');
            */    
        } else if( pE.key == 'up' ){
            
            if( active )
                newTarget = active.getPrevious('.ka-list-combine-item');
                
            if( !newTarget )
                this.mainLeftItems.scrollTo(0,0);
                
            /*
            if( !newTarget )
                newTarget = this.mainLeftItems.getLast('.ka-list-combine-item');
            */
        }
        
        if( !newTarget ) return;
    
        var pos = newTarget.getPosition( this.mainLeftItems );
        var size = newTarget.getSize();
        
        var spos = this.mainLeftItems.getScroll();
        var ssize = this.mainLeftItems.getSize();
        
        var bottomline = spos.y+ssize.y;
        
        if( pos.y < 0 ){
            this.mainLeftItems.scrollTo( 0, spos.y+pos.y );
        } else if( pos.y+size.y > ssize.y ){
            //scroll down
            //this.mainLeftItems.scrollTo(0, (pos.y-bottomline));
            this.mainLeftItems.scrollTo(0, (pos.y+size.y)+spos.y-ssize.y);
            //logger(ssize.y+' => ');
        }
        
        this.loadItem( newTarget.retrieve('item') );
        
        this.checkScrollPosition( false, true );
        
    },
    
    renderActionbar: function(){
        var _this = this;
        
        this.renderSearchPane();
    
        if( this.values.multiLanguage )
        	this.win.extendHead();
        
        if( this.values.add || this.values.remove || this.values.custom){
            this.actionsNavi = this.win.addButtonGroup();
            this.actionsNavi.setStyle('margin-right', 159+15);
        }

        if( this.values.remove ){
            this.toggleRemoveBtn = this.actionsNavi.addButton(_('Remove'),
                _path+'inc/template/admin/images/icons/'+this.values.iconDelete, function(){
               this.toggleRemove();
            }.bind(this));
        }

        if( this.values.add ){
            this.addBtn = this.actionsNavi.addButton(
                _('Add'),
                _path+'inc/template/admin/images/icons/'+this.values.iconAdd,
                this.add.bind(this)
            );
            /*function(){
                ka.wm.openWindow( _this.win.module, _this.win.code+'/add', null, null, {
                	lang: (_this.languageSelect)?_this.languageSelect.value:false
                });
            });*/
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
        
        new Element('div', {style: 'color: gray; padding-left: 4px; padding-top:3px;', html: _('Use * as wildcard')}).inject( this.mainLeftSearch );

        var table = new Element('table').inject( this.mainLeftSearch );
        
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
                
                var fieldObj = new ka.field(field, this.searchPane)
                .addEvent('change', this.doSearch.bind(this));                
                this.searchFields.set(mkey, fieldObj );
                
                if( field.type == 'select' ){
                    if( field.multiple ){
                    	new Element('option',{
                    		value: '',
                    		text: _('-- Please choose --')
                    	}).inject(fieldObj.input, 'top');
                    	
                    	fieldObj.setValue("");
                	} else {
                	   fieldObj.select.add('', _('-- Please choose --'));
                	}
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
        
            this.from = 0;
            this.max = 0;
            this._lastItems = null;
        
            this.reload();
        }.bind(this);
        this.lastTimer = mySearch.delay(200);
    },
    
    renderLoader: function(){
        
    },
    
    checkScrollPosition: function( pRecheck, pAndScrollToSelect ){
    
        if( this.loadingNewItems ) return;
    
        if( this.mainLeftItems.getScroll().y - (this.mainLeftItems.getScrollSize().y-this.mainLeftItems.getSize().y) == 0 ){
            this.loadMore(pAndScrollToSelect);
        } else if( this.maxItems > 0 && (this.mainLeftItems.getScrollSize().y-this.mainLeftItems.getSize().y) == 0 ){
            this.loadMore(pAndScrollToSelect);
        
        /*
        } else if( this.mainLeftItems.getLast('.ka-list-combine-item') == this.mainLeftItems.getElement('.active')  ){
            this.loadMore();
        } else if( this.mainLeftItems.getFirst('.ka-list-combine-item') == this.mainLeftItems.getElement('.active')  ){
            this.loadPrevious();
        */
        
        }
        if( this.mainLeftItems.getScroll().y == 0 ){
            this.loadPrevious(pAndScrollToSelect);
        }
        
        if( pRecheck == true )
            this.checkScrollPosition.delay(50, this);
        
    },
    
    loadMore: function( pAndScrollToSelect ){
        if( this.max < this.maxItems ){
            this.loadItems( this.max, (this.values.itemsPerPage)?this.values.itemsPerPage:5, pAndScrollToSelect );
        }
    },
    
    loadPrevious: function( pAndScrollToSelect ){
        //logger('loadPrevious');    
        if( this.from > 0 ){
            
            var items = (this.values.itemsPerPage)?this.values.itemsPerPage:5;
            var newFrom = this.from - items;
            var maxItems = items;
            
            if( newFrom < 0 ) {
                maxItems += newFrom;
                newFrom = 0;
            }
            //logger(this.mainLeftItems.getScroll().y);
            //logger(this.from+': '+newFrom+'->'+maxItems);
            this.loadItems( newFrom, maxItems, pAndScrollToSelect );
        }
    },
    
    changeLanguage: function(){    
        this.reload();
    },
    
    clear: function(){
        
        this._lastItems = null;
        this.clearItemList();
        this.from = 0;
        this.max = 0; //(this.values.itemsPerPage)?this.values.itemsPerPage:5;
        
    },
    
    reload: function(){
        this.clear();
    	this.loadItems( this.from, this.max );
    },
    
    loadItems: function( pFrom, pMax, pAndScrollToSelect ){
        var _this = this;

        //logger(pFrom+' => '+pMax);
        
        if( this._lastItems ){
            if( pFrom > this._lastItems.maxItems )
                return;
        }
        
        pMax = (pMax>0)?pMax:5;

        if( this.lastRequest )
            this.lastRequest.cancel();

        if( this.from == null || pFrom >= this.from )
            this.itemLoaderStart();
        else
            this.prevItemLoaderStart();

        if( this.loader )
            this.loader.show();
        
        this.lastRequest = new Request.JSON({url: _path+'admin/'+this.win.module+'/'+this.win.code+'?cmd=getItems', noCache: true, onComplete:function( res ){
            
            if( !res.items && (this.from == 0 || !this.from) ){
                this.itemLoaderNoItems();
            }
            
            if( !res.items ) return;
            
            this.renderItems(res, pFrom);
            
            //logger(this.from+' > '+pFrom);
            
            if( this.from == null || pFrom < this.from ){
                this.from = pFrom;
            } else if( pFrom == null ){
                this.from = 0;
            }
            
            var nMax = Object.getLength(res.items);
                
            if( !this.max || this.max < pFrom+nMax )
                this.max = pFrom+nMax;      
            
            if( res.maxItems > 0 ){
                if( this.max == res.maxItems ){
                    this.itemLoaderEnd();
                } else {
                    this.itemLoaderStop();
                }
            } else {
                this.itemLoaderNoItems();
            }
            
            if( this.from > 0 ){
                this.prevItemLoaderStop();
            } else {
                this.prevItemLoaderNoItems();
            }
        
            
            //logger('loadItems done: from='+this.from+', max='+this.max);
    
            this.itemsFrom.set('html', this.from+1);
            this.itemsLoaded.set('html', this.max);
            this.itemsMax.set('html', res.maxItems);
            
            if( pAndScrollToSelect ){
                var target = this.mainLeftItems.getElement('.active');
                if( target ){
                    var pos = target.getPosition( this.mainLeftItems );
                    
                    this.mainLeftItems.scrollTo(0, pos.y-(this.mainLeftItems.getSize().y/2));
                    
                }
            } else {
                if( this.from > 0 ){
                    if( this.mainLeftItems.getScroll().y < 5 )
                        this.mainLeftItems.scrollTo(0,5);
                }
            }
            
            if( this.from > 0 && this.mainLeftItems.getScroll().y == 0 )
                this.loadPrevious(true);
            else if( res.maxItems > 0 && (this.mainLeftItems.getScrollSize().y-this.mainLeftItems.getSize().y) == 0 )
                this.loadMore(true);
            
        }.bind(this)}).post({ 
            module: this.win.module,
            code: this.win.code, 
            from: pFrom,
            max: pMax,
            orderBy: this.sortField,
            filter: this.searchEnable,
            language: (this.languageSelect)?this.languageSelect.value:false,
            filterVals: (this.searchEnable)?this.getSearchVals():'',
            orderByDirection: this.sortDirection,
            params: JSON.encode(this.win.params)
        });
    },
    
    clearItemList: function(){    
        this.lastSortValue = false;
        this.itemsLoadedCount = 0;
        
        this.from = null;
        this.max = 0;
        
        this.checkboxes = [];
        
        this._lastItems = null;
            
        this.mainLeftItems.empty();
        this.createItemLoader();
    },
    
    createItemLoader: function(){
    
        this.itemLoader = new Element('div', {
            'class': 'ka-list-combine-itemloader'
        }).inject( this.mainLeftItems );
        
        this.prevItemLoader = new Element('div', {
            'class': 'ka-list-combine-itemloader',
            'style': 'display: none;'
        }).inject( this.mainLeftItems, 'top' );
        
        this.itemLoaderStop();
    
    },
    
    itemLoaderStop: function(){
        this.loadingNewItems = false;
        if( !this.itemLoader ) return;
        this.itemLoader.set('html', _('Scroll to the bottom to load more entries.'));
    },
    
    itemLoaderEnd: function(){
        this.loadingNewItems = false;
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
        logger('hopp');
    },
    
    prevItemLoaderStart: function(){
        this.loadingNewItems = true;
        if( !this.prevItemLoader ) return;
        this.prevItemLoader.set('html', '<img src="'+_path+'inc/template/admin/images/loading.gif" />'+'<br />'+_('Loading entries ...'));
    },
    
    prevItemLoaderStop: function(){
        this.prevLoadingNewItems = false;
        if( !this.prevItemLoader ) return;
        this.prevItemLoader.setStyle('display', 'block');
        this.prevItemLoader.set('html', _('Scroll to the top to load previous entries.'));
    },
    
    prevItemLoaderNoItems: function(){
        this.loadingNewItems = false;
        this.prevItemLoader.setStyle('display', 'none');
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
            this.mainLeftItems.tween('top', 31+this.searchPaneHeight+1);
            this.searchOpened = true;
            this.doSearch();
        } else {
            
            this.searchEnable = 0;
            this.searchIcon.removeClass('ka-list-combine-searchicon-active');
    
            new Fx.Tween(this.mainLeftSearch).start('height', 0).chain(function(){
                this.mainLeftSearch.setStyle('border-bottom', '0px');
                this.checkScrollPosition();
            }.bind(this));
            
            this.mainLeftItems.tween('top', 31);
            this.searchOpened = false;
            this.reload();
        }
    
    },
    
    findSplit: function( pSplitTitle ){
        var res = false;
        
        var splits = this.mainLeftItems.getElements('.ka-list-combine-splititem');
        splits.each(function(item, id){
        
            if( item.get('html') == pSplitTitle ){
                res = item;
            }
        
        }.bind(this));
    
        return res;
    },
    
    renderItems: function( pItems, pFrom ){
        var _this = this;

        this._lastItems = pItems;
        
        this.currentPage = pItems.page;
        this.maxPages = pItems.maxPages;
        this.maxItems = pItems.maxItems;

        //this.ctrlMax.set('text', '/ '+pItems.maxPages);

        _this.tempcount = 0;
        
        var lastSplitTitleForThisRound = false;
        
        if( pItems.items ){
            
            var position = pFrom+0;
        
            Object.each(pItems.items, function(item){
            
                this.itemsLoadedCount++;
                position++;
            
                var splitTitle = this.getSplitTitle( item );

                var res = this.addItem( item );
                res.store('position', position+0);
                
                if( this.from == null || pFrom > this.from ){
                
                    /*if( this.lastSortValue != splitTitle ){
                    
                        this.lastSortValue = splitTitle;
                        
                        var split = this.addSplitTitle( splitTitle );
                        split.inject( this.itemLoader, 'before' );   
                    }*/
                    
                    res.inject( this.itemLoader, 'before' );
                    
                    var split = res.getPrevious('.ka-list-combine-splititem');
                    
                    if( split ){
                        if( split.get('html') != splitTitle ){
                            var split = this.addSplitTitle( splitTitle );
                            split.inject( res, 'before' );
                        }
                    } else {
                        var split = this.addSplitTitle( splitTitle );
                        split.inject( res, 'before' );
                    }
                    
                } else {
                    
                    /*var oldSameSplit = this.findSplit( splitTitle );
                    if( oldSameSplit && lastSplitTitleForThisRound == false ){
                        //logger(oldSameSplit);
                        oldSameSplit.destroy();
                    }*/
                    
                    res.inject( this.prevItemLoader, 'before' );
                    
                    /*if( lastSplitTitleForThisRound != splitTitle ){
                        var split = this.addSplitTitle( splitTitle );
                        lastSplitTitleForThisRound = splitTitle;
                        split.inject( res, 'before' );
                    }*/
                    
                    var split = res.getNext('.ka-list-combine-splititem');
                    
                    var found = true;
                    
                    if( split ){
                        if( split.get('html') != splitTitle ){
                            found = false;
                        } else {
                            res.inject( split, 'after' );
                        }
                    } else {
                        found = false;
                    }
                    
                    
                    if( !found ){    
                        var split = res.getPrevious('.ka-list-combine-splititem');
                        if( split ){
                            if( split.get('html') != splitTitle ){
                                var split = this.addSplitTitle( splitTitle );
                                split.inject( res, 'before' );
                            }
                        } else {
                            var split = this.addSplitTitle( splitTitle );
                            split.inject( res, 'before' );
                        }
                    }
                    
                }
                
                if( res.hasClass('active') )
                    this.lastItemPosition = position+0;
                
                _this.tempcount++;
            }.bind(this));
        }
        
        this.prevItemLoader.inject( this.mainLeftItems, 'top' );
        
    },
    
    getSplitTitle: function( pItem ){    
        
        var value = this.getItemTitle( pItem, this.sortField );
        if( value == '' ) return _('-- No value --');
                
        if( !this.values.columns[this.sortField]['type'] || this.values.columns[this.sortField].type == "text" ){
            
            return '<b>'+value.substr(0,1).toUpperCase()+'</b>';
            
        } else {
        
            if( ["datetime", "date"].contains(this.values.columns[this.sortField]['type']) ){
                
                if( pItem['values'][this.sortField] > 0 ){
                
                    var time = new Date(pItem['values'][this.sortField]*1000);
                    value = time.timeDiffInWords();
                    
                } else {
                    value = _('No value');
                }
                
            }
            return value;
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
        
    
        return value?value:'';
    },
    
    prepareLoadPage: function(){
        
        //this.mainLeftItems.empty();
        this.itemLoaderStart();
        
    },
    
    add: function(){
        
        this.addBtn.setPressed(true);
        
        this.win.setTitle(_('Add'));
        
        this.lastItemPosition = null;
        this.currentItem = null;
        
        var active = this.mainLeftItems.getElement('.active');
        if( active )
            active.removeClass('active');
        
        if( this.currentEdit ){
            this.currentEdit.destroy();
            this.currentEdit = null;
        }
        if( this.currentAdd ){
            this.currentAdd.destroy();
            this.currentAdd = null;
        }
    
        this.currentAdd = new ka.windowAdd({
            extendHead: this.win.extendHead.bind(this.win),
            addSmallTabGroup: this.win.addSmallTabGroup.bind(this.win),
            addButtonGroup: this.win.addButtonGroup.bind(this.win),
            addEvent: this.win.addEvent.bind(this.win),
            border: this.win.border,
            module: this.win.module,
            code: this.win.code+'/add',
            content: this.mainRight,
            inlineContainer: this.win.inlineContainer,
            id: this.win.id
        }, this.mainRight);
            
        this.currentAdd.addEvent('save', this.addSaved.bind(this));
    
    },
    
    addSaved: function( pValues, pAnswer ){
    
    
        /*logger(pValues);
        logger(pAnswer);
        logger( this.currentAdd.values.primary );*/
    
        if( this.currentAdd.values.primary.length > 1 ) return;

        this.lastLoadedItem = null;
        this._lastItems = null;
        
        delete pValues.module;
        delete pValues.code;
        
        this.needSelection = true;
        var primaries = {};
        
        this.currentAdd.values.primary.each(function(primary){
            primaries[primary] = pAnswer.last_id;
        }.bind(this));
        
        if( !this.win.params )
            this.win.params = {};
        
        this.win.params.selected = primaries;
        
        this.loadAround( this.win.params.selected );
    
    },
    
    toggleRemove: function(){
        if( !this.inRemoveMode ){
            this.mainLeftItems.addClass('remove-activated');
            this.inRemoveMode = true;
            this.mainLeftDeleter.tween('height', 29);
            this.mainLeftItems.tween('bottom', 30);
            this.toggleRemoveBtn.setPressed(true);
        } else {
            this.mainLeftItems.removeClass('remove-activated');
            this.inRemoveMode = false;
            this.mainLeftDeleter.tween('height', 0);
            this.mainLeftItems.tween('bottom', 0);
            this.toggleRemoveBtn.setPressed(false);
        }
    },
    
    loadItem: function( pItem ){
        var _this = this;
        
        this.addBtn.setPressed(false);
        
        if( this.currentAdd ){
            this.currentAdd.destroy();
            this.currentAdd = null;
        }
                
        this.mainLeftItems.getChildren().each(function(item,i){
            
            item.removeClass('active');
            if( item.retrieve('item') == pItem ){
                item.addClass('active');
                found = true;
            }
        });
        
        this.currentItem = pItem;
        
        if( !this.currentEdit ){
        
            var cloned = {};
            Object.append(cloned, this.win);

            this.currentEdit = new ka.windowEdit(Object.append( cloned, {
                code: this.win.code+'/edit',
                params: pItem,
            }), this.mainRight);
            
            this.currentEdit.addEvent('save', this.saved.bind(this));
            this.currentEdit.addEvent('load', this.itemLoaded.bind(this));
            
            /*this.currentEdit.addEvent('render', function(){
            
                _this.topTabGroup = this.topTabGroup;
                _this.renderTopTabGroup();
            
            });*/
        } else {

            this.currentEdit.win.params = pItem;
            this.currentEdit.loadItem();
        
        }
    
    },
    
    itemLoaded: function( pItem ){
        this.lastLoadedItem = pItem.values;
        this.setWinParams();
    },
    
    renderFinished: function(){
    
        if( this.win.params && this.win.params.list.language && this.languageSelect ){
            this.languageSelect.setValue( this.win.params.list.language );
        }
        
        if( this.win.params && this.win.params.list ){
            this.sortField = this.win.params.list.orderBy;
            this.sortDirection = this.win.params.list.orderByDirection;
        }
        
        if( this.win.params && this.win.params.selected ){
            this.needSelection = true;
            this.loadAround( this.win.params.selected );
        } else {
            this.loadItems(0, (this.values.itemsPerPage)?this.values.itemsPerPage:5 );
        }
    
    },
    
    setWinParams: function(){
    
        var type = null;
        var selected = null;
        if( this.currentEdit && this.currentEdit.values ){
            type = 'edit';
            
            var primaries = {};
            
            this.currentEdit.values.primary.each(function(primary){
                primaries[primary] = this.currentItem.values[primary];
            }.bind(this));
            
            selected = primaries;
            
        } else if( this.currentAdd ){
            type = 'add';
        }
        
        this.win.params = {
            module: this.win.module,
            code: this.win.code,
            type: type,
            selected: selected,
            list: {
                orderBy: this.sortField,
                filter: this.searchEnable,
                language: (this.languageSelect)?this.languageSelect.value:false,
                filterVals: (this.searchEnable)?this.getSearchVals():'',
                orderByDirection: this.sortDirection                
            }
        };
    
        this.setTitle();
    },
    
    setTitle: function(){
    
        if( this.currentEdit && this.currentEdit.item ){
        
            var item = this.currentEdit.item;

            var title = item.values.title;
            if( !title )
                title = item.values.name;
            if( !title )
                title = item.values.name;
                
            if( this.currentEdit.values.editTitleField )
                title = item.values[ this.currentEdit.values.editTitleField ];
            else if( this.currentEdit.values.titleField ){
                title = item.values[ this.currentEdit.values.titleField ];
            } else if( !title ){
                Object.each( item.values, function(item){
                    if( !title && item != '' && typeOf(item) == 'string' ){
                        title = item;
                    }
                })
            }
                
            this.win.setTitle(title);
        }
    },
    
    reloadAll: function(){
    	this.loadItems( this.from, this.max );
    },
    
    loadAround: function( pPrimaries ){
    
        if( this.lastRequest )
            this.lastRequest.cancel();
        
        //this.itemLoaderStart();

        //if( this.loader )
        //    this.loader.show();

        //logger( pPrimaries );
        this.lastRequest = new Request.JSON({url: _path+'admin/'+this.win.module+'/'+this.win.code+'?cmd=getItems', noCache: true, onComplete:function( res ){
            
            //logger( res );
            if( res > 0 ){
                var range = (this.values.itemsPerPage)?this.values.itemsPerPage:5;
                
                var from = res;
                if( res < range ){
                    from = 0;
                } else {
                    from = res-Math.floor(range/2);
                }

                this.clearItemList();
                this.loadItems( from, range );
            }
            
        }.bind(this)}).post({ 
            module: this.win.module,
            code: this.win.code,
            getPosition: pPrimaries,
            orderBy: this.sortField,
            filter: this.searchEnable,
            language: (this.languageSelect)?this.languageSelect.value:false,
            filterVals: (this.searchEnable)?this.getSearchVals():'',
            orderByDirection: this.sortDirection
        });
    
    },
    
    saved: function( pItem, pRes, pPublished ){
    
        if( pPublished ) {
            /*this.lastLoadedItem && (pItem[this.sortField] && this.lastLoadedItem[this.sortField] &&
            this.lastLoadedItem[this.sortField] != pItem[this.sortField]) ){*/
            
            this.lastLoadedItem = pItem;
            this._lastItems = null;
            
            this.loadAround( this.win.params.selected );
        }
        
        return;
        
        /*
        var primaries = {};
        
        this.currentEdit.values.primary.each(function(primary){
            primaries[primary] = this.currentItem.values[primary];
        }.bind(this));
                
        this.loadAround( primaries );
        
        return;
        */
        
        //logger(pItem);
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
            
            if( target != false ){
                
                if( this.lastSavedUpdateRq )
                    this.lastSavedUpdateRq.cancel();
                
                var req = { 
                    module: this.win.module,
                    code: this.win.code,
                    primary: {}
                };
                
                this.currentEdit.values.primary.each(function(primary){
                    req['primary'][primary] = this.currentItem.values[primary];
                }.bind(this));
                
                if( this.currentEdit.values.multiLanguage )
                    req['language'] = this.currentItem.values['lang'];
                
                this.lastSavedUpdateRq = new Request.JSON({url: _path+'admin/'+this.win.module+'/'+this.win.code+'?cmd=getItems',
                noCache: true, onComplete:function( res ){
                                        
                    var newItem = this.addItem( res.items[0] );
                    newItem.inject( target, 'before' );
                    target.destroy();
                    
                    var splitTitle = this.getSplitTitle( this.currentItem );
                    var splitTitleNew = this.getSplitTitle( res.item[0] );
                    
                    if( splitTitle != splitTitleNew ){
                        
                        //TODO delete all items and reload items around this one
                        //this.reloadAround( req );
                        
                    }
                                        
                    this.currentItem = res.items[0];
                    
                }.bind(this)}).post(req);
            
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
        return new Element('div', {
            'class': 'ka-list-combine-splititem',
            html: pItem
        });
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
        .addEvent('click', this.loadItem.bind(this, pItem));
        
        
        if( this.values.remove == true ){
            
            if( pItem['remove'] ){
            
                var removeBox = new Element('div', {
                    'class': 'ka-list-combine-item-remove'
                }).inject( item );
                
                //new ka.Button(_('Remove')).inject( removeBox );
                
                var removeCheckBox = new Element('div', {
                    'class': 'ka-list-combine-item-removecheck'
                })
                .inject( item );
        
                var mykey = {};
                this.values.primary.each(function(primary){
                    mykey[primary] = pItem.values[primary];
                });
                //if( this.values.edit ){
                    this.checkboxes.include( new Element('input',{
                        value: JSON.encode(mykey),
                        type: 'checkbox'
                    })
                    .addEvent('click', function(e){
                        e.stopPropagation();
                    })
                    .inject( removeCheckBox ) );
                //}
            }
        }
        
        
        
        if( this.currentEdit && this.currentEdit.values ){
        
            var oneIsFalse = false;
            
	        this.currentEdit.values.primary.each(function(prim){
	           if( this.currentItem['values'][prim] != pItem.values[prim] )
	               oneIsFalse = true;
	        }.bind(this))
	        
	        if( oneIsFalse == false )
	           item.addClass('active');
        }
        
        
        if( this.needSelection ){
        
            var oneIsFalse = false;
            
	        Object.each(this.win.params.selected, function(value, prim){
	           if( value != pItem.values[prim] )
	               oneIsFalse = true;
	        }.bind(this))
	        
	        if( oneIsFalse == false ){
                item.fireEvent('click', pItem);
                item.addClass('active');
                this.needSelection = false;
            }
        }
        
        //parse
        this.values.columns.each(function(column,columnId){
        
            if( item.getElement('*[id='+columnId+']') ){
                
                var value = this.getItemTitle( pItem, columnId );
                
                item.getElement('*[id='+columnId+']').set('html', value);
            
            }

        }.bind(this));
        
        return item;
        
        
        
        //TODO
        
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
