ka.WindowCombine = new Class({

    Extends: ka.WindowList,
    lastSortValue: false,
    itemsLoadedCount: 0,
    combine: true,

    searchPaneHeight: 110,

    renderLayout: function () {

        this.main = new Element('div', {
            'class': 'ka-list-main',
            style: 'bottom: 0px; top: 0px; overflow: hidden;'
        }).inject(this.win.content);

        this.inputTrigger = new Element('input').inject(document.hidden);

        this.inputTrigger.addEvent('focus', function () {
            this.ready2ChangeThroughKeyboard = true;
        }.bind(this));
        this.inputTrigger.addEvent('blur', function () {
            this.ready2ChangeThroughKeyboard = false;
        }.bind(this));

        this.mainLeft = new Element('div', {
            style: 'position: absolute; left: 0px; top: 0px; bottom: 0px; width: 265px; border-right: 1px solid silver;'
        }).inject(this.main);

        this.mainLeftTop = new Element('div', {
            style: 'position: absolute; left: 0px; padding: 5px 6px; top: 0px; height: 20px; right: 0px; border-bottom: 1px solid gray;',
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
        this.itemsMax = new Element('span', {text: '0'}).inject(this.itemCount);

        this.mainLeftSearch = new Element('div', {
            'class': 'ka-list-combine-searchpane'
        }).inject(this.mainLeft);

        this.mainLeftItems = new Element('div', {
            style: 'position: absolute; left: 0px; top: 31px; bottom: 0px; right: 0px; overflow: auto;'
        }).addEvent('scroll', this.checkScrollPosition.bind(this, true)).inject(this.mainLeft);

        this.mainLeftDeleter = new Element('div', {
            'class': 'ka-list-bottom',
            style: 'position: absolute; left:0px; height: 0px; bottom: 0px; right: 0px; overflow: hidden'
        }).inject(this.mainLeft);

        new ka.Button(_('Select all')).addEvent('click', function () {

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

        this.mainRight = new Element('div', {
            'class': 'ka-list-combine-right'
        }).inject(this.main);


        document.addEvent('keydown', function(e){this.leftItemsDown.call(this, e)}.bind(this));
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

    renderActionbar: function () {
        var _this = this;

        this.renderSearchPane();

        if (this.classProperties.multiLanguage) {
            this.win.extendHead();
        }

        if (this.classProperties.add || this.classProperties.remove || this.classProperties.custom) {
            this.actionsNavi = this.win.addButtonGroup();
            this.actionsNavi.setStyle('margin-right', 159 + 17);
        }

        if (this.actionsNavi) {
            if (this.classProperties.remove) {

                var icon = this.classProperties.removeIcon?this.classProperties.removeIcon:'admin/images/icons/'+this.classProperties.iconDelete;

                this.toggleRemoveBtn = this.actionsNavi.addButton(t('Remove'), ka.mediaPath(icon), function () {
                    this.toggleRemove();
                }.bind(this));
            }

            if (this.classProperties.add) {

                var icon = this.classProperties.addIcon?this.classProperties.addIcon:'admin/images/icons/'+this.classProperties.iconAdd;

                this.addBtn = this.actionsNavi.addButton(t('Add'), ka.mediaPath(icon), this.add.bind(this));
            }
        }


        this.searchIcon = new Element('div', {
            'class': 'ka-list-combine-searchicon icon-search-2'
        }).addEvent('click', this.toggleSearch.bind(this)).inject(this.mainLeftTop);


        this.sortSelect = new ka.Select();
        this.sortSelect.inject(this.sortSpan);
        this.sortSelect.setStyle('width', 150);

        Object.each(this.classProperties.fields, function (column, id) {

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

        this.createItemLoader();

    },

    renderSearchPane: function () {

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


                var title = this.classProperties.fields[mkey].label;
                field.label = t(title);
                field.small = true;
                field.tableitem = true;
                field.tableitem_title_width = 50;

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
                            fieldObj.setValue(this.win.params.item.values[key]);
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
            this.max = 0;
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
        if (this.max < this.maxItems) {
            this.loadItems(this.max, (this.classProperties.itemsPerPage) ? this.classProperties.itemsPerPage : 5, pAndScrollToSelect);
        }
    },

    loadPrevious: function (pAndScrollToSelect) {
        if (this.from > 0) {

            var items = (this.classProperties.itemsPerPage) ? this.classProperties.itemsPerPage : 5;
            var newFrom = this.from - items;
            var maxItems = items;

            if (newFrom < 0) {
                maxItems += newFrom;
                newFrom = 0;
            }
            this.loadItems(newFrom, maxItems, pAndScrollToSelect);
        }
    },

    changeLanguage: function () {
        this.reload();
    },

    clear: function () {

        this._lastItems = null;
        this.clearItemList();
        this.from = 0;
        this.max = 0; //(this.classProperties.itemsPerPage)?this.classProperties.itemsPerPage:5;

    },

    reload: function () {
        this.clear();
        this.loadItems(this.from, this.max);
    },

    loadItems: function (pFrom, pMax, pAndScrollToSelect) {
        var _this = this;

        if (this._lastItems) {
            if (pFrom > this._lastItems.maxItems) {
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

        this.lastRequest = new Request.JSON({url: _path + 'admin/' + this.win.module + '/' + this.oriWinCode, noCache: true, onComplete: function (response) {

            var res = response.data;

            if (!res.items && (this.from == 0 || !this.from)) {
                this.itemLoaderNoItems();
            }

            if (!res.items) return;

            this.renderItems(res, pFrom);

            if (this.from == null || pFrom < this.from) {
                this.from = pFrom;
            } else if (pFrom == null) {
                this.from = 0;
            }

            var nMax = Object.getLength(res.items);

            if (!this.max || this.max < pFrom + nMax) {
                this.max = pFrom + nMax;
            }

            if (res.maxItems > 0) {
                if (this.max == res.maxItems) {
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
            this.itemsLoaded.set('html', this.max);
            this.itemsMax.set('html', res.maxItems);

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
            } else if (res.maxItems > 0 && (this.mainLeftItems.getScrollSize().y - this.mainLeftItems.getSize().y) == 0) {
                this.loadMore(true);
            }

        }.bind(this)}).get({
            from: pFrom,
            max: pMax,
            orderBy: this.sortField,
            filter: this.searchEnable,
            language: (this.languageSelect) ? this.languageSelect.value : false,
            filterVals: (this.searchEnable) ? this.getSearchVals() : '',
            orderByDirection: this.sortDirection,
            params: JSON.encode(this.win.params)
        });
    },

    clearItemList: function () {
        this.lastSortValue = false;
        this.itemsLoadedCount = 0;

        this.from = null;
        this.max = 0;

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
        this.itemLoader.set('html', '<img src="' + _path + PATH_MEDIA + '/admin/images/loading.gif" />' + '<br />' + _('Loading entries ...'));
    },

    itemLoaderNoItems: function () {
        this.itemLoader.set('html', _('There are no entries.'));
    },

    prevItemLoaderStart: function () {
        this.loadingNewItems = true;
        if (!this.prevItemLoader) return;
        this.prevItemLoader.set('html', '<img src="' + _path + PATH_MEDIA + '/admin/images/loading.gif" />' + '<br />' + _('Loading entries ...'));
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

    renderMultilanguage: function () {

        if (this.classProperties.multiLanguage) {

            this.languageSelect = new ka.Select();
            this.languageSelect.inject(this.win.titleGroups);
            document.id(this.languageSelect).setStyles({
                'width': 106,
                left: 80,
                'position': 'absolute',
                'top': 0
            });

            this.languageSelect.addEvent('change', this.changeLanguage.bind(this));

            Object.each(ka.settings.langs, function (lang, id) {

                this.languageSelect.add(id, lang.langtitle + ' (' + lang.title + ', ' + id + ')');

            }.bind(this));

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

    renderItems: function (pItems, pFrom) {
        var _this = this;

        this._lastItems = pItems;

        this.maxPages = pItems.maxPages;
        this.maxItems = pItems.maxItems;

        //this.ctrlMax.set('text', '/ '+pItems.maxPages);

        _this.tempcount = 0;

        var lastSplitTitleForThisRound = false;

        if (pItems.items) {

            var position = pFrom + 0;

            Object.each(pItems.items, function (item) {

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

                _this.tempcount++;
            }.bind(this));
        }

        this.prevItemLoader.inject(this.mainLeftItems, 'top');

    },

    getSplitTitle: function (pItem) {

        var value = this.getItemTitle(pItem, this.sortField);
        if (value == '') return _('-- No value --');

        if (!this.classProperties.fields[this.sortField])
            return value;

        if (!this.classProperties.fields[this.sortField]['type'] || this.classProperties.fields[this.sortField].type == "text") {

            return '<b>' + value.substr(0, 1).toUpperCase() + '</b>';

        } else {

            if (["datetime", "date"].contains(this.classProperties.fields[this.sortField]['type'])) {

                if (pItem['values'][this.sortField] > 0) {

                    var time = new Date(pItem['values'][this.sortField] * 1000);
                    value = time.timeDiffInWords();

                } else {
                    value = _('No value');
                }

            }
            return value;
        }

    },

    getItemTitle: function (pItem, pColumnId) {

        var value = pItem['values'][pColumnId]
        if (!this.classProperties.fields[pColumnId]) return value;

        var column = this.classProperties.fields[pColumnId];

        if (column.format == 'timestamp') {
            value = new Date(value * 1000).toLocaleString();
        }

        if (column.type == 'datetime' || column.type == 'date') {
            if (value != 0 && value) {
                var format = ( !column.format ) ? '%d.%m.%Y %H:%M' : column.format;
                value = new Date(value * 1000).format(format);
            } else {
                value = '';
            }
        }

        if (column.type == 'select') {
            value = pItem['values'][pColumnId +'_'+ column.table_label] || pItem['values'][pColumnId + '__label'];
        }


        if (column.imageMap) {
            value = '<img src="' + _path + column.imageMap[value] + '"/>';
        }


        return value ? value : '';
    },

    prepareLoadPage: function () {

        //this.mainLeftItems.empty();
        this.itemLoaderStart();

    },

    add: function () {

        this.addBtn.setPressed(true);

        this.win.setTitle(_('Add'));

        this.lastItemPosition = null;
        this.currentItem = null;

        var active = this.mainLeftItems.getElement('.active');
        if (active) {
            active.removeClass('active');
        }

        if (this.currentEdit) {
            this.currentEdit.destroy();
            delete this.currentEdit;
        }
        if (this.currentAdd) {
            this.currentAdd.destroy();
            delete this.currentAdd;
        }

        var win = {};
        for (var i in this.win){
            win[i] = this.win[i];
        }

        win.code = this.oriWinCode+'/add';

        this.currentAdd = new ka.WindowAdd(win, this.mainRight);
        this.currentAdd.addEvent('save', this.addSaved.bind(this));

    },

    addSaved: function (pValues, pAnswer) {

        if (this.currentAdd.classProperties.primary.length > 1) return;

        this.lastLoadedItem = null;
        this._lastItems = null;

        delete pValues.module;
        delete pValues.code;

        this.needSelection = true;
        var primaries = {};

        this.currentAdd.classProperties.primary.each(function (primary) {
            primaries[primary] = pAnswer.last_id;
        }.bind(this));

        if (!this.win.params) {
            this.win.params = {};
        }

        this.win.params.selected = primaries;

        this.loadAround(this.win.params.selected);

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
            'module', 'code', 'inlineContainer', 'titleGroups', 'id', 'close', 'setTitle', '_confirm',
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

        if (!this.currentEdit) {

            this.setActiveItem(pItem);
            this.addBtn.setPressed(false);
            var win = {};

            for (var i in this.win)
                win[i] = this.win[i];

            win.code = this.oriWinCode+'/edit';
            win.params = {item: pItem.values};

            this.currentEdit = new ka.WindowEdit(win, this.mainRight);

            this.currentEdit.addEvent('save', this.saved.bind(this));
            this.currentEdit.addEvent('load', this.itemLoaded.bind(this));

        } else {

            var hasUnsaved = this.currentEdit.hasUnsavedChanges();

            if (hasUnsaved) {
                this.win.interruptClose = true;
                this.win._confirm(_('There are unsaved data. Want to continue?'), function (pAccepted) {
                    if (pAccepted) {
                        this.currentEdit.winParams = {item: pItem.values};
                        this.currentEdit.loadItem();
                        this.addBtn.setPressed(false);
                        this.setActiveItem(pItem);
                    }
                }.bind(this));
                return;
            } else {
                this.currentEdit.winParams = {item: pItem.values};
                this.currentEdit.loadItem();
                this.addBtn.setPressed(false);
                this.setActiveItem(pItem);
            }

        }

        this.inputTrigger.focus();

    },

    setActiveItem: function (pItem) {

        this.mainLeftItems.getChildren().each(function (item, i) {
            item.removeClass('active');
            if (item.retrieve('item') == pItem) {
                item.addClass('active');
            }
        });

        this.currentItem = pItem;

    },

    itemLoaded: function (pItem) {
        this.lastLoadedItem = pItem.values;
        this.setWinParams();
    },

    renderFinished: function () {

        if (this.win.params && this.win.params.list.language && this.languageSelect) {
            this.languageSelect.setValue(this.win.params.list.language);
        }

        if (this.win.params && this.win.params.list) {
            this.sortField = this.win.params.list.orderBy;
            this.sortDirection = this.win.params.list.orderByDirection;
        }

        if (this.win.params && this.win.params.selected) {
            this.needSelection = true;
            this.loadAround(this.win.params.selected);
        } else {
            this.loadItems(0, (this.classProperties.itemsPerPage) ? this.classProperties.itemsPerPage : 5);
        }

    },

    setWinParams: function () {

        var type = null;
        var selected = null;
        if (this.currentEdit && this.currentEdit.classProperties) {
            type = 'edit';

            var primaries = {};

            this.currentEdit.classProperties.primary.each(function (primary) {
                primaries[primary] = this.currentItem.values[primary];
            }.bind(this));

            selected = primaries;

        } else if (this.currentAdd) {
            type = 'add';
        }

        this.win.params = {
            module: this.win.module,
            code: this.oriWinCode,
            type: type,
            selected: selected,
            list: {
                orderBy: this.sortField,
                filter: this.searchEnable,
                language: (this.languageSelect) ? this.languageSelect.value : false,
                filterVals: (this.searchEnable) ? this.getSearchVals() : '',
                orderByDirection: this.sortDirection
            }
        };

        this.setTitle();
    },

    setTitle: function () {

        if (this.currentEdit && this.currentEdit.item) {

            var item = this.currentEdit.item;

            var title = item.values.title;
            if (!title) {
                title = item.values.name;
            }
            if (!title) {
                title = item.values.name;
            }

            if (this.currentEdit.classProperties.editTitleField) {
                title = item.values[ this.currentEdit.classProperties.editTitleField ];
            } else if (this.currentEdit.classProperties.titleField) {
                title = item.values[ this.currentEdit.classProperties.titleField ];
            } else if (!title) {
                Object.each(item.values, function (item) {
                    if (!title && item != '' && typeOf(item) == 'string') {
                        title = item;
                    }
                })
            }

            this.win.setTitle(title);
        }
    },

    reloadAll: function () {
        this.loadItems(this.from, this.max);
    },

    loadAround: function (pPrimaries) {

        if (this.lastRequest) {
            this.lastRequest.cancel();
        }

        this.lastRequest = new Request.JSON({url: _path + 'admin/' + this.win.module + '/' + this.oriWinCode + '?cmd=getItems', noCache: true, onComplete: function (response) {

            var res = response.data;

            if (res > 0) {
                var range = (this.classProperties.itemsPerPage) ? this.classProperties.itemsPerPage : 5;

                var from = res;
                if (res < range) {
                    from = 0;
                } else {
                    from = res - Math.floor(range / 2);
                }

                this.clearItemList();
                this.loadItems(from, range);
            }

        }.bind(this)}).post({
            module: this.win.module,
            code: this.oriWinCode,
            getPosition: pPrimaries,
            orderBy: this.sortField,
            filter: this.searchEnable,
            language: (this.languageSelect) ? this.languageSelect.value : false,
            filterVals: (this.searchEnable) ? this.getSearchVals() : '',
            orderByDirection: this.sortDirection
        });

    },

    saved: function (pItem, pRes, pPublished) {

        if (pPublished) {
            /*this.lastLoadedItem && (pItem[this.sortField] && this.lastLoadedItem[this.sortField] &&
             this.lastLoadedItem[this.sortField] != pItem[this.sortField]) ){*/

            this.lastLoadedItem = pItem;
            this._lastItems = null;

            this.loadAround(this.win.params.selected);
        }

        return;

        /*
         var primaries = {};

         this.currentEdit.classProperties.primary.each(function(primary){
         primaries[primary] = this.currentItem.values[primary];
         }.bind(this));

         this.loadAround( primaries );

         return;
         */

        //logger(pItem);
        var sortedColumnChanged = false;

        if (sortedColumnChanged) {
            this.reload();
        } else {

            var target = false;
            this.mainLeftItems.getChildren().each(function (item, i) {

                if (item.retrieve('item') == this.currentItem) {
                    target = item;
                }

            }.bind(this));

            if (target != false) {

                if (this.lastSavedUpdateRq) {
                    this.lastSavedUpdateRq.cancel();
                }

                var req = {
                    module: this.win.module,
                    code: this.oriWinCode,
                    primary: {}
                };

                this.currentEdit.classProperties.primary.each(function (primary) {
                    req['primary'][primary] = this.currentItem.values[primary];
                }.bind(this));

                if (this.currentEdit.classProperties.multiLanguage) {
                    req['language'] = this.currentItem.values['lang'];
                }

                this.lastSavedUpdateRq = new Request.JSON({url: _path + 'admin/' + this.win.module + '/' + this.oriWinCode + '?cmd=getItems',
                    noCache: true, onComplete: function (response) {

                        var res = response.data;

                        var newItem = this.addItem(res.items[0]);
                        newItem.inject(target, 'before');
                        target.destroy();

                        var splitTitle = this.getSplitTitle(this.currentItem);
                        var splitTitleNew = this.getSplitTitle(res.item[0]);

                        if (splitTitle != splitTitleNew) {

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

            if (this.classProperties.fields.title) {
                layout += '<h2 id="title"></h2>';
                titleAdded = true;
            } else if (this.classProperties.fields.name) {
                layout += '<h2 id="name"></h2>';
                nameAdded = true;
            }

            layout += '<div class="subline">';

            var c = 1;
            Object.each(this.classProperties.fields, function (bla, id) {

                if (id == "title" && titleAdded) return;
                if (id == "name" && nameAdded) return;

                if (c > 2) return;

                if (c == 2) {
                    layout += ', ';
                }

                layout += "<span id=" + id + "></span>";
                c++;

            }.bind(this));

            layout += "</div>";
        }

        var item = new Element('div', {
            html: layout,
            'class': 'ka-list-combine-item'
        }).store('item', pItem).addEvent('click', this.loadItem.bind(this, pItem));


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
                    mykey[primary] = pItem.values[primary];
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
                if (this.currentItem['values'][prim] != pItem.values[prim]) {
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
                if (value != pItem.values[prim]) {
                    oneIsFalse = true;
                }
            }.bind(this))

            if (oneIsFalse == false) {
                item.fireEvent('click', pItem);
                item.addClass('active');
                this.needSelection = false;
            }
        }

        //parse
        Object.each(pItem['values'], function (column, columnId) {

            if (item.getElement('*[id=' + columnId + ']')) {

                var value = this.getItemTitle(pItem, columnId);

                item.getElement('*[id=' + columnId + ']').set('html', value);

            }

        }.bind(this));

        return item;


        //TODO

        var _this = this;
        var tr = new Element('tr', {
            'class': (_this.tempcount % 2) ? 'one' : 'two'
        }).inject(this.tbody);

        if (this.classProperties.remove == true) {
            var td = new Element('td', {
                style: 'width: 21px;'
            }).inject(tr);
            if (pItem['remove']) {
                var mykey = {};
                this.classProperties.primary.each(function (primary) {
                    mykey[primary] = pItem.values[primary];
                });
                //if( this.classProperties.edit ){
                this.checkboxes.include(new Element('input', {
                    value: JSON.encode(mykey),
                    type: 'checkbox'
                }).inject(td));
                //}
            }
        }

        Object.each(this.classProperties.fields, function (column, columnId) {
            var value = pItem['values'][columnId];

            if (column.format == 'timestamp') {
                value = new Date(value * 1000).toLocaleString();
            }

            if (column.type == 'datetime' || column.type == 'date') {
                if (value != 0 && value) {
                    var format = ( !column.format ) ? '%d.%m.%Y %H:%M' : column.format;
                    value = new Date(value * 1000).format(format);
                } else {
                    value = '';
                }
            }


            if (column.type == 'select') {
                value = pItem['values'][columnId + '__label'];
            }


            if (column.imageMap) {
                value = '<img src="' + _path + column.imageMap[value] + '"/>';
            }

            var td = new Element('td', {
                html: value
            }).addEvent('click',
                function (e) {
                    _this.select(this);
                }).addEvent('mousedown',
                function (e) {
                    e.stop();
                }).addEvent('dblclick',
                function (e) {
                    if (_this.classProperties.editCode) {
                        ka.wm.open(_this.classProperties.editCode, pItem);
                    } else if (pItem.edit) {
                        ka.wm.openWindow(_this.win.module, _this.oriWinCode + '/edit', null, null, pItem);
                    }
                }).inject(tr);

            if (column.type == 'html') {
                td.set('html', value);
            }

            //open window if open definied
            //todo: may this section isn't in use ?
            if (pItem.open) {
                td.addEvent('dblclick', function () {
                    var params = ( pItem.open[2] ) ? pItem.open[2] : pItem;
                    ka.wm.openWindow(pItem.open[0], pItem.open[1], null, null, params);
                });
            }
            //todoend

            if (column.width > 0) {
                td.setStyle('width', column.width + 'px');
            }
        });

        if (this.classProperties.remove == true || this.classProperties.edit == true || this.classProperties.itemActions) {
            var icon = new Element('td', {
                width: 40,
                'class': 'edit'
            }).inject(tr);

            if (this.classProperties.itemActions && this.classProperties.itemActions.each) {
                this.classProperties.itemActions.each(function (action) {
                    new Element('img', {
                        src: _path + PATH_MEDIA + action[1],
                        title: action[0]
                    }).addEvent('click',
                        function () {
                            ka.wm.open(action[2], {item: pItem, filter: action[3]});
                        }).inject(icon);
                });
                icon.setStyle('width', 40 + (20 * this.classProperties.itemActions.length));
                this.titleIconTd.setStyle('width', 40 + (20 * this.classProperties.itemActions.length));
            }

            if (pItem.edit) {
                new Element('img', {
                    src: _path + PATH_MEDIA + '/admin/images/icons/' + this.classProperties.iconEdit
                }).addEvent('click',
                    function () {
                        if (_this.classProperties.editCode) {
                            ka.wm.open(_this.classProperties.editCode, pItem);
                        } else if (pItem.edit) {
                            ka.wm.openWindow(_this.win.module, _this.oriWinCode + '/edit', null, null, pItem);
                        }
                    }).inject(icon);
            }
            if (pItem['remove']) {
                new Element('img', {
                    src: _path + PATH_MEDIA + '/admin/images/icons/' + this.classProperties.iconDelete
                }).addEvent('click',
                    function () {
                        _this.win._confirm(_('Really delete?'), function (res) {
                            if (!res) return;
                            _this.deleteItem(pItem);
                        });
                    }).inject(icon);
            }
        }
    }

});
