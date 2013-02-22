ka.WindowCombine = new Class({

    Extends: ka.WindowList,
    lastSortValue: false,
    itemsLoadedCount: 0,
    combine: true,

    maxItems: null,

    searchPaneHeight: 110,

    renderLayout: function () {

        this.mainLayout = new ka.Layout(this.win.content, {
            layout: [{
                columns: [260, null]
            }],
            splitter: [
                [1, 1, 'right']
            ],
            connections: [
                [[1, 1], this.headerLayout.getColumn(1)]
            ]
        });

        this.mainLeft = this.mainLayout.getCell(1,1);

        this.mainLeft.setStyle('background-color', '#fafafa');

        this.treeContainer = new Element('div', {
            'class': 'ka-windowCombine-treeContainer ka-objectTree-container'
        }).inject(this.mainLeft);

        this.mainRight = this.mainLayout.getCell(1,2);
        this.mainRight.set('class', 'ka-list-combine-right');

        this.inputTrigger = new Element('input').inject(document.hiddenElement);

        this.inputTrigger.addEvent('focus', function () {
            this.ready2ChangeThroughKeyboard = true;
        }.bind(this));
        this.inputTrigger.addEvent('blur', function () {
            this.ready2ChangeThroughKeyboard = false;
        }.bind(this));

        if (this.classProperties.asNested){

            //load ka.objectTree
            this.renderLayoutNested(this.treeContainer);

        } else {
            //classic list

            this.mainLeftTop = new Element('div', {
                style: 'position: absolute; left: 0px; padding: 5px 6px; top: 0px; height: 20px; right: 6px; border-bottom: 1px solid gray;',
                'class': 'ka-list-combine-left-top'
            }).inject(this.mainLeft);

            this.sortSpan = new Element('span', {
                style: 'margin-left: 30px; line-height: 17px;'
            }).inject(this.mainLeftTop);

            this.itemCount = new Element('div', {
                'class': 'ka-list-combine-left-itemcount'
            }).inject(this.mainLeftTop);

            this.itemsFrom = new Element('span', {text: '0'}).inject(this.itemCount);
            new Element('span', {text: '-'}).inject(this.itemCount);
            this.itemsLoaded = new Element('span', {text: '0'}).inject(this.itemCount);
            new Element('span', {text: '/'}).inject(this.itemCount);
            this.itemsMaxSpan = new Element('span', {text: '0'}).inject(this.itemCount);

            this.mainLeftSearch = new Element('div', {
                'class': 'ka-list-combine-searchpane'
            }).inject(this.mainLeft);

            this.mainLeftItems = new Element('div', {
                style: 'position: absolute; left: 0px; top: 31px; bottom: 0px; right: 6px; overflow: auto;'
            }).addEvent('scroll', this.checkScrollPosition.bind(this, true)).inject(this.mainLeft);

            this.mainLeftDeleter = new Element('div', {
                'class': 'kwindow-win-buttonBar ka-windowCombine-list-actions'
            }).inject(this.mainLeft);

            new ka.Button(t('Select all')).addEvent('click', function () {

                if (!this.checkboxes) return;
                if (this.checkedAll) {
                    $$(this.checkboxes).set('checked', false);
                    this.checkedAll = false;
                } else {
                    $$(this.checkboxes).set('checked', true);
                    this.checkedAll = true;
                }

            }.bind(this)).inject(this.mainLeftDeleter);

            new ka.Button(t('Remove selected')).addEvent('click', this.removeSelected.bind(this)).inject(this.mainLeftDeleter);

            //window.addEvent('resize', this.checkScrollPosition.bind(this));

            this.mainLeftItemsScroll = new Fx.Scroll(this.mainLeftItems, {
                transition: Fx.Transitions.linear,
                duration: 300
            });

            this.win.addEvent('resize', this.checkScrollPosition.bind(this, true));

            document.addEvent('keydown', function(e){this.leftItemsDown.call(this, e)}.bind(this));


            this.renderSearchPane();
            this.createItemLoader();
        }


    },

    leftItemsDown: function (pE) {

        if (!this.win.inFront) return;
        if (this.ready2ChangeThroughKeyboard == false) return;

        pE = new Event(pE);

        if (pE.key == 'down' || pE.key == 'up') {
            pE.stop();
        }

        var active = this.mainLeftItems.getElement('.active');

        var newTarget;

        if (pE.key == 'down') {

            if (active) {
                newTarget = active.getNext('.ka-list-combine-item');
            }

            if (!newTarget) {
                this.mainLeftItems.scrollTo(0, this.mainLeftItems.getScrollSize().y + 50);
            }


            /*if( !newTarget )
             newTarget = this.mainLeftItems.getElement('.ka-list-combine-item');
             */
        } else if (pE.key == 'up') {

            if (active) {
                newTarget = active.getPrevious('.ka-list-combine-item');
            }

            if (!newTarget) {
                this.mainLeftItems.scrollTo(0, 0);
            }

            /*
             if( !newTarget )
             newTarget = this.mainLeftItems.getLast('.ka-list-combine-item');
             */
        }

        if (!newTarget) return;

        var pos = newTarget.getPosition(this.mainLeftItems);
        var size = newTarget.getSize();

        var spos = this.mainLeftItems.getScroll();
        var ssize = this.mainLeftItems.getSize();

        var bottomline = spos.y + ssize.y;

        if (pos.y < 0) {
            this.mainLeftItems.scrollTo(0, spos.y + pos.y);
        } else if (pos.y + size.y > ssize.y) {
            //scroll down
            this.mainLeftItems.scrollTo(0, (pos.y + size.y) + spos.y - ssize.y);
        }

        this.loadItem(newTarget.retrieve('item'));

        this.checkScrollPosition(false, true);

    },

    checkClassProperties: function(){

        if (!this.classProperties.asNested){
            return this.parent();
        }

        return true;
    },

    deselect: function(){

        if (this.mainLeftItems){
            var active = this.mainLeftItems.getElement('.active');
            if (active) {
                active.removeClass('active');
            }
        }

        if (this.nestedField){
            //deselect current trees
            this.nestedField.getFieldObject().deselect();
        }


    },

    renderActionbar: function () {

        this.renderTopActionBar(this.headerLayoutLeft.getColumn(1));

    },

    openAddItem: function(){

        this.add();

    },

    renderSearchPane: function () {

        this.searchIcon = new Element('div', {
            'class': 'ka-list-combine-searchicon icon-search-8'
        }).addEvent('click', this.toggleSearch.bind(this)).inject(this.mainLeftTop);


        this.sortSelect = new ka.Select();
        this.sortSelect.inject(this.sortSpan);
        this.sortSelect.setStyle('width', 150);

        Object.each(this.classProperties.columns, function (column, id) {

            this.sortSelect.add(id + '______asc', [t(column.label), '#icon-arrow-16']);
            this.sortSelect.add(id + '______desc', [t(column.label), '#icon-arrow-15']);

        }.bind(this));

        this.sortSelect.addEvent('change', function () {

            var sortId = this.sortSelect.getValue();


            this.sortField = sortId.split('______')[0];

            /*if( this.classProperties.fields[this.sortField] && (this.classProperties.fields[this.sortField]['type'] == 'datetime' ||
             this.classProperties.fields[this.sortField]['type'] == 'date') ){
             this.sortDirection = 'DESC';
             }*/

            this.sortDirection = sortId.split('______')[1];

            this.reload();


        }.bind(this));


        this.sortSelect.setValue(this.sortField + '______' + ((this.sortDirection == 'DESC') ? 'desc' : 'asc'));


        new Element('div', {style: 'color: gray; padding-left: 4px; padding-top:3px;', html: _('Use * as wildcard')}).inject(this.mainLeftSearch);

        var table = new Element('table').inject(this.mainLeftSearch);

        this.searchPane = new Element('tbody', {
        }).inject(table);

        this.searchFields = new Hash();
        var doSearchNow = false;

        if (this.classProperties.filter && this.classProperties.filter.each) {
            this.classProperties.filter.each(function (filter, key) {

                var mkey = key;

                if (typeOf(key) == 'number') {
                    mkey = filter;
                }

                var field = Object.clone(this.classProperties.filterFields[ mkey ]);


                var title = field.label;
                field.label = t(title);
                field.small = true;
                field.tableitem = true;
                field.tableItemLabelWidth = 50;

                var fieldObj = new ka.Field(field, this.searchPane).addEvent('change', this.doSearch.bind(this));
                this.searchFields.set(mkey, fieldObj);

                if (field.type == 'select') {
                    if (field.multiple) {
                        new Element('option', {
                            value: '',
                            text: _('-- Please choose --')
                        }).inject(fieldObj.input, 'top');

                        fieldObj.setValue("");
                    } else {
                        fieldObj.select.add('', _('-- Please choose --'), 'top');
                        fieldObj.setValue('');
                    }
                }

                if (this.win.params && this.win.params.filter) {
                    Object.each(this.win.params.filter, function (item, key) {
                        if (item == mkey) {
                            fieldObj.setValue(this.win.params.item[key]);
                            doSearchNow = true;
                        }
                    }.bind(this));
                }

            }.bind(this));
        }

        if (doSearchNow) {
            this.toggleSearch();
            this.loadAlreadyTriggeredBySearch = true;
            this.doSearch();
        }
    },


    doSearch: function () {

        if (this.lastTimer) {
            clearTimeout(this.lastTimer);
        }

        var mySearch = function () {

            this.from = 0;
            this.loadedCount = 0;
            this._lastItems = null;

            this.reload();
        }.bind(this);
        this.lastTimer = mySearch.delay(200);
    },

    renderLoader: function () {

    },

    checkScrollPosition: function (pRecheck, pAndScrollToSelect) {

        if (this.loadingNewItems) return;

        if (this.mainLeftItems.getScroll().y - (this.mainLeftItems.getScrollSize().y - this.mainLeftItems.getSize().y) == 0) {
            this.loadMore(pAndScrollToSelect);
        } else if (this.maxItems > 0 && (this.mainLeftItems.getScrollSize().y - this.mainLeftItems.getSize().y) == 0) {
            this.loadMore(pAndScrollToSelect);

            /*
             } else if( this.mainLeftItems.getLast('.ka-list-combine-item') == this.mainLeftItems.getElement('.active')  ){
             this.loadMore();
             } else if( this.mainLeftItems.getFirst('.ka-list-combine-item') == this.mainLeftItems.getElement('.active')  ){
             this.loadPrevious();
             */

        }
        if (this.mainLeftItems.getScroll().y == 0) {
            this.loadPrevious(pAndScrollToSelect);
        }

        if (pRecheck == true) {
            this.checkScrollPosition.delay(50, this);
        }

    },

    loadMore: function (pAndScrollToSelect) {
        if (this.loadedCount < this.maxItems) {
            this.loadItems(this.loadedCount, (this.classProperties.itemsPerPage) ? this.classProperties.itemsPerPage : 5, pAndScrollToSelect);
        }
    },

    loadPrevious: function (pAndScrollToSelect) {
        if (this.from > 0) {

            var items = (this.classProperties.itemsPerPage) ? this.classProperties.itemsPerPage : 5;
            var newFrom = this.from - items;
            var items = items;

            if (newFrom < 0) {
                items += newFrom;
                newFrom = 0;
            }
            this.loadItems(newFrom, items, pAndScrollToSelect);
        }
    },

    changeLanguage: function () {
        this.reload();
    },

    clear: function () {

        if (this.classProperties.asNested){

            this.mainLeft.empty();

        } else {
            this._lastItems = null;
            this.clearItemList();
            this.from = 0;
            this.loadedCount = 0; //(this.classProperties.itemsPerPage)?this.classProperties.itemsPerPage:5;
        }

    },

    reload: function () {

        if (this.ignoreNextSoftLoad) {
            delete this.ignoreNextSoftLoad;
            return;
        }

        if (this.classProperties.asNested){
            return this.renderLayoutNested(this.treeContainer);
        } else {
            this.clear();
            this.maxItems = null;
            return this.loadItems(this.from, this.loadedCount);
        }
    },

    loadItems: function (pFrom, pMax, pAndScrollToSelect) {


        if (this.maxItems === null){
            return this.loadCount(function(count){
                if (count == 0){
                    this.itemLoaderNoItems();
                } else {
                    this.loadItems(pFrom, pMax, pAndScrollToSelect);
                }

            }.bind(this));
        }

        if (this._lastItems) {
            if (pFrom > this._lastItems.count) {
                return;
            }
        }

        pMax = (pMax > 0) ? pMax : 5;

        if (this.lastRequest) {
            this.lastRequest.cancel();
        }

        if (this.from == null || pFrom >= this.from) {
            this.itemLoaderStart();
        } else {
            this.prevItemLoaderStart();
        }

        if (this.loader) {
            this.loader.show();
        }

        this.order = {};
        this.order[this.sortField] = this.sortDirection;

        this.lastRequest = new Request.JSON({url: _path + 'admin/' + this.getEntryPoint(),
        noCache: true, onComplete: function (response) {

            if (response.error){ 
                this.itemLoader.set('html', t('Something went wrong :-('));
                return;
            }

            if (typeOf(response.data) != 'array') response.data = [];

            var count = response.data.length;

            if (!count && (this.from == 0 || !this.from)) {
                this.itemLoaderNoItems();
            }

            if (!Object.getLength(response.data)) return;

            this.renderItems(response.data, pFrom);

            if (this.from == null || pFrom < this.from) {
                this.from = pFrom;
            } else if (pFrom == null) {
                this.from = 0;
            }

            if (!this.loadedCount || this.loadedCount < pFrom + count) {
                this.loadedCount = pFrom + count;
            }

            if (count > 0) {
                if (this.loadedCount == this.maxItems) {
                    this.itemLoaderEnd();
                } else {
                    this.itemLoaderStop();
                }
            } else {
                this.itemLoaderNoItems();
            }

            if (this.from > 0) {
                this.prevItemLoaderStop();
            } else {
                this.prevItemLoaderNoItems();
            }

            this.itemsFrom.set('html', this.from + 1);
            this.itemsLoaded.set('html', this.loadedCount);

            if (pAndScrollToSelect) {
                var target = this.mainLeftItems.getElement('.active');
                if (target) {
                    var pos = target.getPosition(this.mainLeftItems);

                    this.mainLeftItems.scrollTo(0, pos.y - (this.mainLeftItems.getSize().y / 2));

                }
            } else {
                if (this.from > 0) {
                    if (this.mainLeftItems.getScroll().y < 5) {
                        this.mainLeftItems.scrollTo(0, 5);
                    }
                }
            }

            if (this.from > 0 && this.mainLeftItems.getScroll().y == 0) {
                this.loadPrevious(true);
            } else if (this.maxItems-this.loadedCount > 0 && (this.mainLeftItems.getScrollSize().y - this.mainLeftItems.getSize().y) == 0) {
                this.loadMore(true);
            }

        }.bind(this)}).get({
            offset: pFrom,
            limit: pMax,
            order: this.order,
            language: (this.languageSelect) ? this.languageSelect.getValue() : false
        });
    },

    loadCount: function(pCallback){

        if (this.lastCountRequest) this.lastCountRequest.cancel();

        this.lastCountRequest = new Request.JSON({url: _path + 'admin/' + this.getEntryPoint()+'/:count',
        onComplete: function(response){

            this.maxItems = response.data+0;
            if (this.itemsMaxSpan)
                this.itemsMaxSpan.set('html', this.maxItems);

            if (pCallback)
                pCallback(response.data);

        }.bind(this)}).get();

    },

    clearItemList: function () {
        this.lastSortValue = false;
        this.itemsLoadedCount = 0;

        this.from = null;
        this.loadedCount = 0;

        this.checkboxes = [];

        this._lastItems = null;

        this.mainLeftItems.empty();

        this.createItemLoader();
    },

    createItemLoader: function () {

        this.itemLoader = new Element('div', {
            'class': 'ka-list-combine-itemloader'
        }).inject(this.mainLeftItems);

        this.prevItemLoader = new Element('div', {
            'class': 'ka-list-combine-itemloader',
            'style': 'display: none;'
        }).inject(this.mainLeftItems, 'top');

        this.itemLoaderStop();

    },

    itemLoaderStop: function () {
        this.loadingNewItems = false;
        if (!this.itemLoader) return;
        this.itemLoader.set('html', _('Scroll to the bottom to load more entries.'));
    },

    itemLoaderEnd: function () {
        this.loadingNewItems = false;
        if (!this.itemLoader) return;
        this.itemLoader.set('html', _('No entries left.'));
    },

    itemLoaderStart: function () {
        this.loadingNewItems = true;
        if (!this.itemLoader) return;
        this.itemLoader.set('html', '<img src="' + _path + 'admin/images/loading.gif" />' + '<br />' + _('Loading entries ...'));
    },

    itemLoaderNoItems: function () {
        this.itemLoader.set('html', _('There are no entries.'));
    },

    prevItemLoaderStart: function () {
        this.loadingNewItems = true;
        if (!this.prevItemLoader) return;
        this.prevItemLoader.set('html', '<img src="' + _path + 'admin/images/loading.gif" />' + '<br />' + _('Loading entries ...'));
    },

    prevItemLoaderStop: function () {
        this.prevLoadingNewItems = false;
        if (!this.prevItemLoader) return;
        this.prevItemLoader.setStyle('display', 'block');
        this.prevItemLoader.set('html', _('Scroll to the top to load previous entries.'));
    },

    prevItemLoaderNoItems: function () {
        this.loadingNewItems = false;
        this.prevItemLoader.setStyle('display', 'none');
    },


    renderHeader: function(){

        this.headerLayout = new ka.LayoutHorizontal(this.win.getTitleGroupContainer(), {
            columns: [260, null],
            fixed: false
        });

        this.headerLayoutLeft = new ka.LayoutHorizontal(this.headerLayout.getColumn(1), {
            columns: [null, 147],
            fixed: false
        });

        this.renderMultilanguage();


    },

    renderMultilanguage: function () {

        if (this.classProperties.multiLanguage) {

            this.languageSelect = new ka.Select(this.headerLayoutLeft.getColumn(2));

            document.id(this.languageSelect).setStyle('width', 140);

            this.languageSelect.addEvent('change', this.changeLanguage.bind(this));

            var hasSessionLang = false;
            Object.each(ka.settings.langs, function (lang, id) {

                this.languageSelect.add(id, lang.langtitle + ' (' + lang.title + ', ' + id + ')');
                if (id == window._session.lang)
                    hasSessionLang = true;

            }.bind(this));

            if (hasSessionLang)
                this.languageSelect.setValue(window._session.lang);
        }

    },

    toggleSearch: function () {

        if (!this.searchOpened) {
            this.searchEnable = 1;
            this.searchIcon.addClass('ka-list-combine-searchicon-active');
            this.mainLeftSearch.tween('height', this.searchPaneHeight);
            this.mainLeftSearch.setStyle('border-bottom', '1px solid silver');
            this.mainLeftItems.tween('top', 31 + this.searchPaneHeight + 1);
            this.searchOpened = true;
            this.doSearch();
        } else {

            this.searchEnable = 0;
            this.searchIcon.removeClass('ka-list-combine-searchicon-active');

            new Fx.Tween(this.mainLeftSearch).start('height', 0).chain(function () {
                this.mainLeftSearch.setStyle('border-bottom', '0px');
                this.checkScrollPosition();
            }.bind(this));

            this.mainLeftItems.tween('top', 31);
            this.searchOpened = false;
            this.reload();
        }

    },

    findSplit: function (pSplitTitle) {
        var res = false;

        var splits = this.mainLeftItems.getElements('.ka-list-combine-splititem');
        splits.each(function (item, id) {

            if (item.get('html') == pSplitTitle) {
                res = item;
            }

        }.bind(this));

        return res;
    },

    nestedItemSelected: function(pItem, pDom){

        //pDom.objectKey
        //pDom.id

        if (pDom.objectKey == this.classProperties.object){
            this.loadItem(pItem);
        } else {

            this.loadRootItem(pItem);

        }

    },

    renderItems: function (pItems, pFrom) {

        this._lastItems = pItems;

        //this.ctrlMax.set('text', '/ '+pItems.pages);

        this.tempcount = 0;

        var lastSplitTitleForThisRound = false;

        if (pItems) {

            var position = pFrom + 0;

            Array.each(pItems, function (item) {

                this.itemsLoadedCount++;
                position++;

                var splitTitle = this.getSplitTitle(item);

                var res = this.addItem(item);
                res.store('position', position + 0);

                if (this.from == null || pFrom > this.from) {

                    /*if( this.lastSortValue != splitTitle ){

                     this.lastSortValue = splitTitle;

                     var split = this.addSplitTitle( splitTitle );
                     split.inject( this.itemLoader, 'before' );
                     }*/

                    res.inject(this.itemLoader, 'before');

                    var split = res.getPrevious('.ka-list-combine-splititem');

                    if (split) {
                        if (split.get('html') != splitTitle) {
                            var split = this.addSplitTitle(splitTitle);
                            split.inject(res, 'before');
                        }
                    } else {
                        var split = this.addSplitTitle(splitTitle);
                        split.inject(res, 'before');
                    }

                } else {

                    res.inject(this.prevItemLoader, 'before');

                    var split = res.getNext('.ka-list-combine-splititem');

                    var found = true;

                    if (split) {
                        if (split.get('html') != splitTitle) {
                            found = false;
                        } else {
                            res.inject(split, 'after');
                        }
                    } else {
                        found = false;
                    }


                    if (!found) {
                        var split = res.getPrevious('.ka-list-combine-splititem');
                        if (split) {
                            if (split.get('html') != splitTitle) {
                                var split = this.addSplitTitle(splitTitle);
                                split.inject(res, 'before');
                            }
                        } else {
                            var split = this.addSplitTitle(splitTitle);
                            split.inject(res, 'before');
                        }
                    }

                }

                if (res.hasClass('active')) {
                    this.lastItemPosition = position + 0;
                }

                this.tempcount++;
            }.bind(this));
        }

        this.prevItemLoader.inject(this.mainLeftItems, 'top');

    },

    getSplitTitle: function (pItem) {

        var value = ka.getObjectFieldLabel(
            pItem,
            this.classProperties.columns[this.sortField],
            this.sortField,
            this.classProperties['object']
        );
        if (value == '') return _('-- No value --');

        if (!this.classProperties.columns[this.sortField])
            return value;

        if (!this.classProperties.columns[this.sortField]['type'] || this.classProperties.columns[this.sortField].type == "text") {

            return '<b>' + ((typeOf(value) == 'string') ? value.substr(0, 1).toUpperCase() : value) + '</b>';

        } else {

            if (["datetime", "date"].contains(this.classProperties.columns[this.sortField]['type'])) {

                if (pItem[this.sortField] > 0) {

                    var time = new Date(pItem[this.sortField] * 1000);
                    value = time.timeDiffInWords();

                } else {
                    value = t('No value');
                }

            }
            return value;
        }

    },

    prepareLoadPage: function () {

        //this.mainLeftItems.empty();
        this.itemLoaderStart();

    },

    add: function(){

        if (this.addBtn)
            this.addBtn.setPressed(true);

        if (this.addRootBtn)
            this.addRootBtn.setPressed(false);

        this.win.setTitle(t('Add'));

        this.lastItemPosition = null;
        this.currentItem = null;

        this.deselect();

        if (this.currentEdit) {
            this.currentEdit.destroy();
            delete this.currentEdit;
        }

        if (this.currentAdd) {
            this.currentAdd.destroy();
            delete this.currentAdd;
        }
        if (this.currentRootAdd) {
            this.currentRootAdd.destroy();
            delete this.currentRootAdd;
        }
        if (this.currentRootEdit){
            this.currentRootEdit.destroy();
            delete this.currentRootEdit;
        }

        var win = {};
        for (var i in this.win){
            win[i] = this.win[i];
        }

        win.entryPoint = ka.entrypoint.getRelative(this.getEntryPoint(), this.classProperties.editEntrypoint);

        win.getTitleGroupContainer = function(){
            return this.headerLayout.getColumn(2);
        }.bind(this);

        this.currentAdd = new ka.WindowAdd(win, this.mainRight);
        this.currentAdd.addEvent('add', this.addSaved.bind(this));
        this.currentAdd.addEvent('addMultiple', this.addSaved.bind(this));

    },


    addNestedRoot: function(){

        if (this.addBtn)
            this.addBtn.setPressed(false);

        if (this.addRootBtn)
            this.addRootBtn.setPressed(true);

        this.win.setTitle(this.addRootBtn.get('title'));

        this.lastItemPosition = null;
        this.currentItem = null;

        this.deselect();

        if (this.currentEdit) {
            this.currentEdit.destroy();
            delete this.currentEdit;
        }

        if (this.currentAdd) {
            this.currentAdd.destroy();
            delete this.currentAdd;
        }

        if (this.currentRootEdit){
            this.currentRootEdit.destroy();
            delete this.currentRootEdit;
        }

        if (this.currentAdd) {
            this.currentAdd.destroy();
            delete this.currentAdd;
        }

        var win = {};
        for (var i in this.win){
            win[i] = this.win[i];
        }

        win.entryPoint = ka.entrypoint.getRelative(this.getEntryPoint(), this.classProperties.nestedRootAddEntrypoint);

        win.getTitleGroupContainer = function(){
            return this.headerLayout.getColumn(2);
        }.bind(this);

        this.currentRootAdd = new ka.WindowAdd(win, this.mainRight);
        this.currentRootAdd.addEvent('add', this.addRootSaved.bind(this));
        this.currentRootAdd.addEvent('addMultiple', this.addRootSaved.bind(this));

    },

    addRootSaved: function(){
        this.changeLanguage();
    },


    addSaved: function (pRequest, pResponse) {

        this.ignoreNextSoftLoad = true;

        if (this.currentAdd.classProperties.primary.length > 1) return;

        this.lastLoadedItem = null;
        this._lastItems = null;

        this.needSelection = true;

        if (!this.win.params)
            this.win.params = {};

        this.win.params.selected = pResponse.data;

        if (this.classProperties.asNested){

            if (pRequest._position == 'first'){
                this.nestedField.getFieldObject().reloadBranch(pRequest._pk, pRequest._targetObjectKey);
            } else {
                this.nestedField.getFieldObject().reloadParentBranch(pRequest._pk, pRequest._targetObjectKey);
            }

        } else {
            return this.loadCount(function(count){
                this.loadAround(this.win.params.selected);
            }.bind(this));
        }


    },

    toggleRemove: function () {
        if (!this.inRemoveMode) {
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

    getRefWin: function(){
        var res = {};
        Object.each([
            'addEvent', 'removeEvent', 'extendHead', 'addSmallTabGroup', 'addButtonGroup', 'border',
            'inlineContainer', 'titleGroups', 'id', 'close', 'setTitle', '_confirm',
            'interruptClose'
        ], function(id){
            res[id] = this.win[id];
            if (typeOf(this.win[id]) == 'function')
                res[id] = this.win[id].bind(this.win);
            else
                res[id] = this.win[id];
        }.bind(this));
        return res;
    },

    loadItem: function (pItem) {
        var _this = this;

        if (this.currentAdd) {

            //TODO check unsaved
            var hasUnsaved = this.currentAdd.hasUnsavedChanges();
            this.currentAdd.destroy();
            this.currentAdd = null;
        }

        if (this.currentRootEdit){
            this.currentRootEdit.destroy();
            this.currentRootEdit = null;
        }

        if (this.currentRootAdd){
            this.currentRootAdd.destroy();
            delete this.currentRootAdd;
        }

        if (!this.currentEdit) {

            this.setActiveItem(pItem);
            if (this.addBtn)
                this.addBtn.setPressed(false);
            if (this.addRootBtn)
                this.addRootBtn.setPressed(false);

            var win = {};

            for (var i in this.win)
                win[i] = this.win[i];

            win.entryPoint = ka.entrypoint.getRelative(this.win.entryPoint, _this.classProperties.editEntrypoint);
            win.params = {item: pItem};
            win.getTitleGroupContainer = function(){
                return this.headerLayout.getColumn(2);
            }.bind(this);

            this.currentEdit = new ka.WindowEdit(win, this.mainRight);

            this.currentEdit.addEvent('save', this.saved.bind(this));
            this.currentEdit.addEvent('load', this.itemLoaded.bind(this));

        } else {

            var hasUnsaved = this.currentEdit.hasUnsavedChanges();

            if (hasUnsaved) {
                this.win.interruptClose = true;
                this.win._confirm(t('There are unsaved data. Want to continue?'), function (pAccepted) {
                    if (pAccepted) {
                        this.currentEdit.winParams = {item: pItem};
                        this.currentEdit.loadItem();

                        if (this.addBtn)
                            this.addBtn.setPressed(false);

                        this.setActiveItem(pItem);
                    }
                }.bind(this));
                return;
            } else {
                this.currentEdit.winParams = {item: pItem};
                this.currentEdit.loadItem();

                if (this.addBtn)
                    this.addBtn.setPressed(false);

                this.setActiveItem(pItem);
            }

        }

        this.inputTrigger.focus();

    },

    loadRootItem: function (pItem) {
        var _this = this;

        if (this.currentAdd) {
            //TODO check unsaved
            var hasUnsaved = this.currentAdd.hasUnsavedChanges();
            this.currentAdd.destroy();
            this.currentAdd = null;
        }

        if (this.currentEdit){
            this.currentEdit.destroy();
            this.currentEdit = null;
        }

        if (!this.currentRootEdit) {

            this.setActiveItem(pItem);

            if (this.addBtn)
                this.addBtn.setPressed(false);

            if (this.addRootBtn)
                this.addRootBtn.setPressed(false);

            var win = {};

            for (var i in this.win)
                win[i] = this.win[i];

            win.entryPoint = ka.entrypoint.getRelative(this.win.entryPoint, _this.classProperties.nestedRootEditEntrypoint);
            win.params = {item: pItem};
            win.getTitleGroupContainer = function(){
                return this.headerLayout.getColumn(2);
            }.bind(this);

            this.currentRootEdit = new ka.WindowEdit(win, this.mainRight);

            this.currentRootEdit.addEvent('save', this.saved.bind(this));
            this.currentRootEdit.addEvent('load', this.itemLoaded.bind(this));

        } else {

            var hasUnsaved = this.currentRootEdit.hasUnsavedChanges();

            if (hasUnsaved) {
                this.win.interruptClose = true;
                this.win._confirm(t('There are unsaved data. Want to continue?'), function (pAccepted) {
                    if (pAccepted) {
                        this.currentRootEdit.winParams = {item: pItem};
                        this.currentRootEdit.loadItem();

                        if (this.addBtn)
                            this.addBtn.setPressed(false);

                        this.setActiveItem(pItem);
                    }
                }.bind(this));
                return;
            } else {
                this.currentRootEdit.winParams = {item: pItem};
                this.currentRootEdit.loadItem();

                if (this.addBtn)
                    this.addBtn.setPressed(false);

                this.setActiveItem(pItem);
            }

        }

        this.inputTrigger.focus();

    },

    setActiveItem: function (pItem) {

        this.currentItem = pItem;

        if (this.classProperties.asNested) return;

        this.mainLeftItems.getChildren().each(function (item, i) {
            item.removeClass('active');
            if (item.retrieve('item') == pItem) {
                item.addClass('active');
            }
        });


    },

    itemLoaded: function (pItem) {
        this.lastLoadedItem = pItem;
        this.setWinParams();
    },

    renderFinished: function () {

        if (this.win.params && this.win.params.list && this.win.params.list.language && this.languageSelect) {
            this.languageSelect.setValue(this.win.params.list.language);
        }

        if (this.win.params && this.win.params.list) {
            this.sortField = this.win.params.list.orderBy;
            this.sortDirection = this.win.params.list.orderByDirection;
        }

        if (this.classProperties.asNested){
            if (this.win.params && this.win.params.selected) {
                this.nestedField.getFieldObject().select(this.win.params.selected);
            }
        } else {
            if (this.win.params && this.win.params.selected) {
                this.needSelection = true;
                this.loadAround(this.win.params.selected);
            } else {
                this.loadItems(0, (this.classProperties.itemsPerPage) ? this.classProperties.itemsPerPage : 5);
            }
        }

    },

    setWinParams: function () {

        var type = null;
        var selected = null;
        if (this.currentEdit && this.currentEdit.classProperties) {
            type = 'edit';

            var primaries = {};

            this.currentEdit.classProperties.primary.each(function (primary) {
                primaries[primary] = this.currentItem[primary];
            }.bind(this));

            selected = primaries;

        } else if (this.currentAdd) {
            type = 'add';
        }

        this.win.params = {
            type: type,
            selected: selected,
            list: {
                orderBy: this.sortField,
                filter: this.searchEnable,
                language: (this.languageSelect) ? this.languageSelect.getValue(): false,
                filterVals: (this.searchEnable) ? this.getSearchVals() : '',
                orderByDirection: this.sortDirection
            }
        };

        this.setTitle();
    },

    setTitle: function () {

        if (this.currentEdit && this.currentEdit.item) {

            var item = this.currentEdit.item;

            var title = item.title;
            if (!title) {
                title = item.name;
            }
            if (!title) {
                title = item.name;
            }

            if (this.currentEdit.classProperties.editTitleField) {
                title = item[ this.currentEdit.classProperties.editTitleField ];
            } else if (this.currentEdit.classProperties.titleField) {
                title = item[ this.currentEdit.classProperties.titleField ];
            } else if (!title) {
                Object.each(item, function (item) {
                    if (!title && item != '' && typeOf(item) == 'string') {
                        title = item;
                    }
                })
            }

            this.win.setTitle(title);
        }
    },

    reloadAll: function () {
        this.loadItems(this.from, this.loadedCount);
    },

    getEntryPoint: function(){
        return this.win.getEntryPoint();
    },

    loadAround: function (pPrimaries) {

        if (this.lastRequest) {
            this.lastRequest.cancel();
        }

        this.order = {};
        this.order[this.sortField] = this.sortDirection;

        this.lastRequest = new Request.JSON({url: _path + 'admin/' + this.getEntryPoint(), noCache: true, onComplete: function (response) {

            var position = response.data;

            if (position > 0) {
                var range = (this.classProperties.itemsPerPage) ? this.classProperties.itemsPerPage : 5;

                var from = position;
                if (position < range) {
                    from = 0;
                } else {
                    from = position - Math.floor(range / 2);
                }

                this.clearItemList();
                this.loadItems(from, range);
            } else {
                this.loadItems(0, 1);
                this.win.alert(t('Ooops. There was an error in the response of %s').replace('%s', 'GET '+_path + 'admin/' + this.getEntryPoint()));
            }

        }.bind(this)}).get({
            getPosition: pPrimaries,
            order: this.order,
            filter: this.searchEnable,
            language: (this.languageSelect) ? this.languageSelect.getValue() : false,
            filterVals: (this.searchEnable) ? this.getSearchVals() : ''
        });

    },

    saved: function (pItem, pRes) {

        this.ignoreNextSoftLoad = true;

        if (this.classProperties.asNested){

            this.reloadTreeItem();

        } else {
            this.lastLoadedItem = pItem;
            this._lastItems = null;

            this.loadAround(this.win.params.selected);
        }

    },

    reloadTreeItem: function(){

        var selected = this.nestedField.getFieldObject().getSelectedTree();
        if (selected){
            selected.reloadParentOfActive();
        }

    },

    /*
     renderTopTabGroup: function(){
     if( !this.topTabGroup ) return;
     this.topTabGroup.setStyle('left', 158);
     },*/

    addSplitTitle: function (pItem) {
        return new Element('div', {
            'class': 'ka-list-combine-splititem',
            html: pItem
        });
    },

    addItem: function (pItem) {

        var layout = '';
        var titleAdded, nameAdded;

        if (this.classProperties.itemLayout) {
            layout = this.classProperties.itemLayout;
        } else {

            if (this.classProperties.columns.title) {
                layout += '<h2>{title}</h2>';
                titleAdded = true;
            } else if (this.classProperties.columns.name) {
                layout += '<h2>{name}</h2>';
                nameAdded = true;
            }

            layout += '<div class="subline">';

            var c = 1;
            Object.each(this.classProperties.columns, function (bla, id) {

                if (id == "title" && titleAdded) return;
                if (id == "name" && nameAdded) return;

                if (c > 2) return;

                if (c == 2) {
                    layout += ', ';
                }

                layout += "<span>{"+id+"}</span>";
                c++;

            }.bind(this));

            layout += "</div>";
        }

        var item = new Element('div', {
            html: layout,
            'class': 'ka-list-combine-item'
        }).store('item', pItem);

        if (this.classProperties.edit)
            item.addEvent('click', this.loadItem.bind(this, pItem));


        //parse template
        var data = ka.getObjectLabels(
            this.classProperties.columns,
            pItem,
            this.classProperties['object'],
            true
        );

        mowla.render(item, data);

        if (this.classProperties.remove == true) {

            if (pItem['remove']) {

                var removeBox = new Element('div', {
                    'class': 'ka-list-combine-item-remove'
                }).inject(item);

                //new ka.Button(_('Remove')).inject( removeBox );

                var removeCheckBox = new Element('div', {
                    'class': 'ka-list-combine-item-removecheck'
                }).inject(item);

                var mykey = {};
                this.classProperties.primary.each(function (primary) {
                    mykey[primary] = pItem[primary];
                });
                //if( this.classProperties.edit ){
                this.checkboxes.include(new Element('input', {
                    value: JSON.encode(mykey),
                    type: 'checkbox'
                }).addEvent('click',
                    function (e) {
                        e.stopPropagation();
                    }).inject(removeCheckBox));
                //}
            }
        }


        if (this.currentEdit && this.currentEdit.classProperties) {

            var oneIsFalse = false;

            this.currentEdit.classProperties.primary.each(function (prim) {
                if (this.currentItem[prim] != pItem[prim]) {
                    oneIsFalse = true;
                }
            }.bind(this))

            if (oneIsFalse == false) {
                item.addClass('active');
            }
        }


        if (this.needSelection) {

            var oneIsFalse = false;

            Object.each(this.win.params.selected, function (value, prim) {
                if (value != pItem[prim]) {
                    oneIsFalse = true;
                }
            }.bind(this))

            if (oneIsFalse == false) {
                item.fireEvent('click', pItem);
                item.addClass('active');
                this.needSelection = false;
            }
        }

        return item;
    }

});
