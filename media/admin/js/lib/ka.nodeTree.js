ka.NodeTree = new Class({
    Extends: ka.ObjectTree,

    types: {
        '0': 'page_green.png',
        '1': 'page_green.png',
        '2': 'folder.png',
        '3': 'page_white_text.png',
        '-1': 'world.png'
    },

    initialize: function (pContainer, pDomain, pOptions, pRefs) {
        this.domain_rsn = pDomain;
        this.container = pContainer;

        this.setOptions(pOptions);

        if (Cookie.read('krynPageTree_' + pDomain)) {
            var opens = Cookie.read('krynPageTree_' + pDomain);
            opens = opens.split('.');
            Array.each(opens, function (open) {
                this.opens[ open ] = true;
            }.bind(this));
        }

        if (pRefs) {
            this.options.pageObj = pRefs.pageObj;
            this.options.win = pRefs.win;
        }

        this.main = new Element('div', {
            'class': 'ka-pageTree'
        }).inject(this.container);

        this.topDummy = new Element('div', {
            'class': 'ka-pageTree-top-dummy'
        }).inject(this.main);

        this.panePagesTable = new Element('table', {
            style: 'width: 100%',
            cellpadding: 0,
            cellspacing: 0
        }).inject(this.main);

        this.container.addEvent('scroll', this.setDomainPosition.bind(this));

        if (this.options.win)
            this.options.win.addEvent('resize', this.setDomainPosition.bind(this));

        this.panePagesTBody = new Element('tbody').inject(this.panePagesTable);
        this.panePagesTr = new Element('tr').inject(this.panePagesTBody);
        this.panePagesTd = new Element('td').inject(this.panePagesTr);

        this.panePages = new Element('div', {
            'class': 'ka-pageTree-pages'
        }).inject(this.panePagesTd);

        this.paneDomain = new Element('div', {
            'class': 'ka-pageTree-domain'
        }).inject(this.main);

        this.panePages.setStyle('display', '');

        this.paneDomain.set('morph', {duration: 200});

        if (this.options.selectPage) {
            this.startupWithPageInfo(this.options.selectPage);
        } else {
            this.loadFirstLevel();
        }

        if (pContainer && pContainer.getParent('.kwindow-border')) {
            pContainer.getParent('.kwindow-border').retrieve('win').addEvent('close', this.clean.bind(this));
        }

        window.addEvent('mouseup', this.destroyContext.bind(this));

        this.main.addEvent('mouseup', this.onClick.bind(this));
        this.main.addEvent('mousedown', this.onMousedown.bind(this));
    },


    startupWithPageInfo: function (pRsn, pCallback) {

        new Request.JSON({url: _path + 'admin/pages/getPageInfo', noCache: 1, onComplete: function (res) {

            if (res.domain_rsn == this.domain_rsn) {
                this.load_page_childs = [];
                res._parents.each(function (page) {
                    this.load_page_childs.include(page.rsn);
                }.bind(this));
            }
            if (pCallback) {
                pCallback(res);
            } else {
                this.loadFirstLevel();
            }

        }.bind(this)}).get({rsn: pRsn});

    },

    clean: function () {

        this.destroyContext();

    },

    setDomainPosition: function () {

        var nLeft = this.container.scrollLeft;
        var nTop = 0;

        var panePos = this.panePagesTable.getPosition(this.container).y;
        if (panePos - 20 < 0) {
            nTop = (panePos - 20) * -1;
            var maxTop = this.panePages.getSize().y - 20;
            if (nTop > maxTop) nTop = maxTop;
        }

        this.paneDomain.morph({
            //'width': nWidth,
            'left': nLeft,
            'top': nTop
        });

    },

    loadFirstLevel: function () {

        if (this.lastFirstLevelRq) {
            this.lastFirstLevelRq.cancel();
        }

        this.lastFirstLevelRq = new Request.JSON({url: _path + 'admin/pages/getTreeDomain', noCache: 1, onComplete: this.renderFirstLevel.bind(this)}).get({
            domain_rsn: this.domain_rsn
        });

    },

    renderFirstLevel: function (pDomain) {

        this.loadingDone = false;

        if (!pDomain && this.lastDomain) {
            pDomain = this.lastDomain;
        }

        this.lastDomain = pDomain;

        if (pDomain.error) {
            this.main.destroy();
            return;
        }

        this.paneDomain.empty();
        this.panePages.empty();

        this.domainA = this.addItem(pDomain, this.paneDomain);

        if (this.options.withPageAdd) {
            if (ka.checkDomainAccess(pDomain.rsn, 'addPages')) {
                new Element('img', {
                    src: _path + PATH_MEDIA + '/admin/images/icons/add.png',
                    title: _('Add page'),
                    'class': 'ka-pageTree-add'
                }).addEvent('click', function (e) {
                    this.fireEvent('pageAdd', pDomain.rsn);
                }.bind(this)).inject(this.items[0]);
            }
        }
        this.fireEvent('childsLoaded', [pDomain, this.domainA]);

    },

    onMousedown: function (e) {
        e.preventDefault();
    },

    onClick: function (e) {

        if (this.inDragMode) return;

        var target = e.target;
        if (!target) return;
        var a = null;

        if (target.hasClass('ka-pageTree-item-toggler')) return;

        if (target.hasClass('ka-pageTree-item')) {
            a = target;
        }

        if (!a && target.getParent('.ka-pageTree-item')) {
            a = target.getParent('.ka-pageTree-item');
        }

        if (!a) return;

        var item = a.retrieve('item');

        if (e.rightClick) {
            this.openContext(e, a, item);
            return;
        }

        if (item.domain) {

            if (this.options.no_domain_select != true) {

                this.fireEvent('selection', [item, a])
                this.fireEvent('domainClick', [item, a])

                this.unselect();
                if (this.options.noActive != true) {
                    a.addClass('ka-pageTree-item-selected');
                }

                this.lastSelectedItem = a;
                this.lastSelectedPage = item;
            }

        } else {

            this.fireEvent('selection', [item, a])
            this.fireEvent('click', [item, a]);

            this.unselect();

            if (this.options.noActive != true) {
                a.addClass('ka-pageTree-item-selected');
            }

            this.lastSelectedItem = a;
            this.lastSelectedPage = item;
        }

    },

    reloadParentOfActive: function () {

        if (!this.lastSelectedItem) return;

        if (this.lastSelectedPage.domain || this.lastSelectedPage.prsn == 0) {
            this.reload();
            return;
        }

        var parent = this.lastSelectedItem.getParent().getPrevious();
        if (parent && parent.hasClass('ka-pageTree-item')) {
            this.lastScrollPos = this.container.getScroll();
            this.loadChilds(parent);
        }
    },

    addItem: function (pItem, pParent) {

        var a = new Element('div', {
            'class': 'ka-pageTree-item',
            title: 'ID=' + pItem.rsn
        });

        if (pItem.domain) {
            this.domainA = a;
        }

        var container = pParent;
        if (pParent.childContainer) {
            container = pParent.childContainer;
            a.parent = pParent;
        }

        a.inject(container);

        a.pageTreeObj = this;

        a.span = new Element('span', {
            'class': 'ka-pageTree-item-title',
            text: (pItem.title) ? pItem.title : pItem.domain
        }).inject(a);

        if (this.lastSelectedPage && (
            (this.lastSelectedPage.domain && pItem.domain && this.lastSelectedPage.rsn == pItem.rsn) || (!pItem.domain && !this.lastSelectedPage.domain && this.lastSelectedPage.rsn == pItem.rsn)
            )) {

            if (this.options.noActive != true) {
                a.addClass('ka-pageTree-item-selected');
            }

            this.lastSelectedItem = a;
            this.lastSelectedPage = pItem;
        }

        if (pItem.domain) {
            this.items[0] = a;
        } else {
            this.items[ pItem.rsn ] = a;
            //Drag'n'Drop
            if (!this.options.noDrag) {
                a.addEvent('mousedown', function (e) {

                    if (!ka.checkPageAccess(pItem.rsn, 'movePages')) {
                        return;
                    }

                    a.store('mousedown', true);
                    if (this.options.move != false) {
                        (function () {
                            if (a.retrieve('mousedown')) {
                                this.createDrag(a, e);
                            }
                        }).delay(200, this)
                    }
                }.bind(this))
            }
        }

        a.addEvent('mouseout', function () {
            this.store('mousedown', false);
        });
        a.addEvent('mouseup', function () {
            this.store('mousedown', false);
        });

        if (!pItem.domain && a.parent) {
            a.setStyle('padding-left', a.parent.getStyle('padding-left').toInt() + 15);
        }

        a.store('item', pItem);

        /* masks */
        a.masks = new Element('span', {
            'class': 'ka-pageTree-item-masks'
        }).inject(a, 'top');

        new Element('img', {
            'class': 'ka-pageTree-item-type',
            src: _path + PATH_MEDIA + '/admin/images/icons/' + this.types[pItem.type]
        }).inject(a.masks);


        if ((pItem.type == 0 || pItem.type == 1) && pItem.visible == 0) {
            new Element('img', {
                src: _path + PATH_MEDIA + '/admin/images/icons/pageMasks/invisible.png'
            }).inject(a.masks);
        }

        if (pItem.type == 1) {
            new Element('img', {
                src: _path + PATH_MEDIA + '/admin/images/icons/pageMasks/link.png'
            }).inject(a.masks);
        }

        if ((pItem.type == 0 || pItem.type == 3) && pItem.draft_exist == 1) {
            new Element('img', {
                src: _path + PATH_MEDIA + '/admin/images/icons/pageMasks/draft_exist.png'
            }).inject(a.masks);
        }

        if (pItem.access_denied == 1) {
            new Element('img', {
                src: _path + PATH_MEDIA + '/admin/images/icons/pageMasks/access_denied.png'
            }).inject(a.masks);
        }

        if (pItem.type == 0 && pItem.access_from_groups != "" && typeOf(pItem.access_from_groups) == 'string') {
            new Element('img', {
                src: _path + PATH_MEDIA + '/admin/images/icons/pageMasks/access_group_limited.png'
            }).inject(a.masks);
        }

        /* toggler */
        a.toggler = new Element('img', {
            'class': 'ka-pageTree-item-toggler',
            title: _('Open/Close subitems'),
            src: _path + PATH_MEDIA + '/admin/images/icons/tree_plus.png'
        }).inject(a, 'top');

        if (!pItem.hasChilds && (!pItem.childs || pItem.childs.length == 0 )) {
            a.toggler.setStyle('visibility', 'hidden');
        } else {
            a.toggler.addEvent('click', function (e) {
                e.stopPropagation();
                window.fireEvent('click');
                this.toggleChilds(a);
            }.bind(this));
        }

        /* childs */
        if (pItem.domain) {
            a.childContainer = this.panePages;
        } else {
            a.childContainer = new Element('div', {
                'class': 'ka-pageTree-item-childs'
            }).inject(container);
        }

        a.childsLoaded = (pItem.childs) ? true : false;

        var openId = ( pItem.domain ) ? 'p' + pItem.rsn : pItem.rsn;

        if ((!this.firstLoadDone || this.need2SelectAPage)) {
            if ((this.options.selectDomain && pItem.domain ) || (this.options.selectPage && !pItem.domain && pItem.rsn == this.options.selectPage)) {
                if (this.options.noActive != true) {
                    a.addClass('ka-pageTree-item-selected');
                }
                this.lastSelectedItem = a;
                this.lastSelectedPage = pItem;
                this.need2SelectAPage = false;
            }
        }

        if (this.opens[openId]) {
            this.openChilds(a);
        }

        if ((!this.firstLoadDone || this.need2SelectAPage) && this.load_page_childs !== false) {
            if (!pItem.domain && this.load_page_childs.contains(pItem.rsn)) {
                this.openChilds(a);
            } else if (pItem.domain) {
                this.openChilds(a);
            }
        } else if ((!this.firstLoadDone || this.need2SelectAPage) && this.options.openFirstLevel && pItem.domain && !this.opens[openId]) {
            this.openChilds(a);
        }

        if (pItem.childs) {
            var canChangeItemsGeneration = this.inItemsGeneration == true ? false : true;

            if (canChangeItemsGeneration) {
                this.inItemsGeneration = true;
            }

            Array.each(pItem.childs, function (item) {
                this.addItem(item, a);
            }.bind(this));

            if (canChangeItemsGeneration) {
                this.inItemsGeneration = false;
            }
        }

        this.checkDoneState();

        return a;
    },

    checkDoneState: function () {

        var loadingDone = true;
        if (this.inItemsGeneration == false) {
            Object.each(this.loadChildsRequests, function (request) {
                if (request == true) {
                    loadingDone = false;
                }
            }.bind(this));
        } else {
            loadingDone = false;
        }

        if (loadingDone == true) {

            this.loadChildsRequests = {};
            if (this.firstLoadDone == false) {
                this.firstLoadDone = true;

                this.fireEvent('ready');
            }

            if (this.lastScrollPos) {
                this.container.scrollTo(this.lastScrollPos.x, this.lastScrollPos.y);
            }
            this.setDomainPosition();
        }

        this.loadingDone = loadingDone;

    },

    saveOpens: function () {

        var opens = '';
        Object.each(this.opens, function (bool, key) {
            if (bool == true) {
                opens += key + '.';
            }
        });
        Cookie.write('krynPageTree_' + this.domain_rsn, opens);

    },

    toggleChilds: function (pA) {

        if (pA.childContainer.getStyle('display') != 'block') {
            this.openChilds(pA);
        } else {
            this.closeChilds(pA);
        }
    },

    closeChilds: function (pA) {
        var item = pA.retrieve('item');
        var id = item.domain ? 'p' + item.rsn : item.rsn;

        pA.childContainer.setStyle('display', '');
        pA.toggler.set('src', _path + PATH_MEDIA + '/admin/images/icons/tree_plus.png');
        this.opens[ id ] = false;
        this.setDomainPosition();

        this.saveOpens();
    },

    openChilds: function (pA) {

        var item = pA.retrieve('item');
        var id = item.domain ? 'p' + item.rsn : item.rsn;

        pA.toggler.set('src', _path + PATH_MEDIA + '/admin/images/icons/tree_minus.png');
        if (pA.childsLoaded == true) {
            pA.childContainer.setStyle('display', 'block');
            this.opens[ id ] = true;
            this.saveOpens();
        } else {
            this.loadChilds(pA, true);
        }
        this.setDomainPosition();

    },

    reloadChilds: function (pA) {
        this.loadChilds(pA, false);
    },

    loadChilds: function (pA, pAndOpen) {

        var item = pA.retrieve('item');

        if (item.domain) {

            this.loadFirstLevel();

        } else {

            var loader = new Element('img', {
                src: _path + PATH_MEDIA + '/admin/images/loading.gif'
            }).inject(pA.masks)

            var id = ( item.domain ) ? 'p' + item.rsn : item.rsn;

            this.loadChildsRequests[ item.rsn ] = true;
            new Request.JSON({url: _path + 'admin/pages/getTree', noCache: 1, onComplete: function (pItems) {

                pA.childContainer.empty();

                loader.destroy();

                if (pAndOpen) {
                    pA.toggler.set('src', _path + PATH_MEDIA + '/admin/images/icons/tree_minus.png');
                    pA.childContainer.setStyle('display', 'block');
                    this.opens[ id ] = true;
                    this.saveOpens();
                }

                pA.childsLoaded = true;

                if (pItems.length == 0) {
                    pA.toggler.setStyle('visibility', 'hidden');
                    return;
                }

                this.inItemsGeneration = true;
                Array.each(pItems, function (childitem) {
                    this.addItem(childitem, pA);
                }.bind(this));
                this.inItemsGeneration = false;

                this.loadChildsRequests[ item.rsn ] = false;
                this.checkDoneState();

                this.fireEvent('childsLoaded', [item, pA]);
                this.setDomainPosition();

            }.bind(this)}).get({ page_rsn: item.rsn });

        }
    },

    unselect: function () {

        if (this.lastSelectedItem) {
            this.lastSelectedItem.removeClass('ka-pageTree-item-selected');
        }

        this.lastSelectedItem = false;
        this.lastSelectedPage = false;
    },

    createDrag: function (pA, pEvent) {

        this.currentPageToDrag = pA;

        var canMovePage = true;
        var page = pA.retrieve('item');
        if (page.domain) {
            if (!ka.checkPageAccess(page.rsn, 'movePages', 'd')) {
                canMovePage = false;
            }
        } else {
            if (!ka.checkPageAccess(page.rsn, 'movePages')) {
                canMovePage = false;
            }
        }

        var kwin = pA.getParent('.kwindow-border');

        if (this.lastClone) {
            this.lastClone.destroy();
        }

        this.lastClone = new Element('div', {
            'class': 'ka-pageTree-drag-box'
        }).inject(kwin);

        new Element('span', {
            text: pA.get('text')
        }).inject(this.lastClone);

        pA.masks.clone().inject(this.lastClone, 'top');

        var drag = this.lastClone.makeDraggable({
            snap: 0,
            onDrag: function (pDrag, pEvent) {
                if (!pEvent.target) return;
                var element = pEvent.target;

                if (!element.hasClass('ka-pageTree-item')) {
                    element = element.getParent('.ka-pageTree-item');
                }

                if (element) {

                    var pos = pEvent.target.getPosition(document.body);
                    var size = pEvent.target.getSize();
                    var mrposy = pEvent.client.y - pos.y;

                    if (mrposy < size.y / 3) {
                        this.createDropElement(element, 'before');
                    } else if (mrposy > ((size.y / 3) * 2)) {
                        this.createDropElement(element, 'after');
                    } else {
                        //middle
                        this.createDropElement(element, 'inside');
                    }

                }
            }.bind(this),
            onDrop: this.cancelDragNDrop.bind(this),
            onCancel: function () {
                this.cancelDragNDrop(true);
            }.bind(this)
        });

        this.inDragMode = true;
        this.inDragModeA = pA;

        var pos = kwin.getPosition(document.body);

        this.lastClone.setStyles({
            'left': pEvent.client.x + 5 - pos.x,
            'top': pEvent.client.y + 5 - pos.y
        });

        document.addEvent('mouseup', this.cancelDragNDrop.bind(this, true));

        drag.start(pEvent);
    },

    createDropElement: function (pTarget, pPos) {

        if (this.loadChildsDelay) clearTimeout(this.loadChildsDelay);

        if (this.dropElement) {
            this.dropElement.destroy();
            delete this.dropElement;
        }

        if (this.currentPageToDrag == pTarget) return;

        this.dragNDropElement = pTarget;
        this.dragNDropPos = pPos;

        if (this.dropLastItem) {
            this.dropLastItem.removeClass('ka-pageTree-item-dragOver');
            this.dropLastItem.setStyle('padding-bottom', 1);
            this.dropLastItem.setStyle('padding-top', 1);
        }

        var item = pTarget.retrieve('item');


        pTarget.setStyle('padding-bottom', 1);
        pTarget.setStyle('padding-top', 1);

        if (!item.domain) {
            if (pPos == 'after' || pPos == 'before') {
                this.dropElement = new Element('div', {
                    'class': 'ka-pageTree-dropElement',
                    styles: {
                        'margin-left': pTarget.getStyle('padding-left').toInt() + 16
                    }
                });
            } else {
                if (this.lastDropElement == pTarget) {
                    return;
                }
            }
        }


        var canMoveInto = true;
        if (item.domain) {
            if (!ka.checkPageAccess(item.rsn, 'addPages', 'd')) {
                canMoveInto = false;
            }
        } else {
            if (!ka.checkPageAccess(item.rsn, 'addPages')) {
                canMoveInto = false;
            }
        }

        var canMoveAround = true;
        if (pTarget.parent) {
            var parentPage = pTarget.parent.retrieve('item');
            if (parentPage.domain) {
                if (!ka.checkPageAccess(parentPage.rsn, 'addPages', 'd')) {
                    canMoveAround = false;
                }
            } else {
                if (!ka.checkPageAccess(parentPage.rsn, 'addPages')) {
                    canMoveAround = false;
                }
            }
        }

        if (!item.domain && pPos == 'after') {
            if (canMoveAround) {
                this.dropElement.inject(pTarget.getNext(), 'after');
                pTarget.setStyle('padding-bottom', 0);
            }

        } else if (!item.domain && pPos == 'before') {
            if (canMoveAround) {
                this.dropElement.inject(pTarget, 'before');
                pTarget.setStyle('padding-top', 0);
            }

        } else if (pPos == 'inside') {
            if (canMoveInto) {
                pTarget.addClass('ka-pageTree-item-dragOver');
            }
            this.loadChildsDelay = function () {
                this.openChilds(pTarget);
            }.delay(1000, this);
        }


        this.dropLastItem = pTarget;
    },

    cancelDragNDrop: function (pWithoutMoving) {

        if (this.lastClone) {
            this.lastClone.destroy();
            delete this.lastClone;
        }
        if (this.dropElement) {
            this.dropElement.destroy();
            delete this.dropElement;
        }
        if (this.dropLastItem) {
            this.dropLastItem.removeClass('ka-pageTree-item-dragOver');
            this.dropLastItem.setStyle('padding-bottom', 1);
            this.dropLastItem.setStyle('padding-top', 1);
            delete this.dropLastItem;
        }
        this.inDragMode = false;
        delete this.inDragModeA;


        if (pWithoutMoving != true) {

            var pos = {
                'before': 'up',
                'after': 'down',
                'inside': 'into'
            };

            var to = this.dragNDropElement.retrieve('item');
            var where = this.currentPageToDrag.retrieve('item');

            var whereRsn = where.rsn;
            var toRsn = to.rsn;
            var code = pos[this.dragNDropPos];

            var toDomain = to.domain ? true : false;
            this.movePage(whereRsn, toRsn, code, toDomain);
        }
        document.removeEvent('mouseup', this.cancelDragNDrop.bind(this));
    },


    reloadParent: function (pA) {
        if (pA.parent) {
            pA.pageTreeObj.reloadChilds(pA.parent);
        } else {
            pA.pageTreeObj.reload();
        }
    },

    movePage: function (pWhereRsn, pToRsn, pCode, pToDomain) {
        var _this = this;
        var req = {
            rsn: pWhereRsn,
            torsn: pToRsn,
            mode: pCode,
            toDomain: pToDomain ? 1 : 0
        };

        new Request.JSON({url: _path + 'admin/pages/move', onComplete: function (res) {

            //target item this.dragNDropElement
            if (this.dragNDropElement.parent) {
                this.dragNDropElement.pageTreeObj.reloadChilds(this.dragNDropElement.parent);
            } else {
                this.dragNDropElement.pageTreeObj.reload();
            }

            //origin item this.currentPageToDrag
            if (this.currentPageToDrag.parent && (!this.dragNDropElement.parent || this.dragNDropElement.parent != this.currentPageToDrag.parent)) {
                this.currentPageToDrag.pageTreeObj.reloadChilds(this.currentPageToDrag.parent);
            } else if (!this.dragNDropElement.parent || this.dragNDropElement.pageTreeObj != this.currentPageToDrag.pageTreeObj) {
                this.currentPageToDrag.pageTreeObj.reload();
            }

            ka.loadSettings(['r2d']);

        }.bind(this)}).post(req);
    },

    reload: function () {
        this.lastScrollPos = this.container.getScroll();
        this.loadFirstLevel();
    },


    isReady: function () {
        return this.firstLoadDone;
    },

    hasChilds: function (pPage) {
        if (this._pagesParent.get(pPage.rsn)) {
            return true;
        }
        return false;
    },

    getSelected: function () {
        if (this.lastSelectedItem) {
            return this.lastSelectedItem;
        }
        return false;
    },

    select: function (pRsn) {

        this.unselect();

        if (this.items[ pRsn ]) {
            //has been already loaded
            this.items[ pRsn ].addClass('ka-pageTree-item-selected');

            this.lastSelectedItem = this.items[ pRsn ];
            this.lastSelectedPage = this.items[ pRsn ].retrieve('item');
            return;
        }

        this.need2SelectAPage = true;

        this.startupWithPageInfo(pRsn, function (res) {

            this.options.selectPage = pRsn;
            this.renderFirstLevel();

            Array.each(this.load_page_childs, function (item) {
                if (this.items[item]) {
                    this.openChilds(this.items[item]);
                }
            }.bind(this));
        }.bind(this));

    },

    destroyContext: function () {
        if (this.oldContext) {
            this.lastContextA.removeClass('ka-pageTree-item-hover');
            this.oldContext.destroy();
            delete this.oldContext;
        }
    },

    openContext: function (pEvent, pA, pPage) {

        if (this.options.withContext != true) return;

        if (!pEvent.rightClick) return;

        window.fireEvent('mouseup');
        pEvent.stopPropagation();

        pA.addClass('ka-pageTree-item-hover');
        this.lastContextA = pA;

        this.oldContext = new Element('div', {
            'class': 'ka-pagesTree-context'
        }).inject(document.body);

        var wsize = window.getSize();

        var left = pEvent.page.x - (this.container.getPosition(document.body).x);
        var mtop = pEvent.page.y - (this.container.getPosition(document.body).y);

        var left = pEvent.page.x;
        var mtop = pEvent.page.y;
        if (mtop < 0) {
            mtop = 1;
        }

        this.oldContext.setStyles({
            left: left,
            'top': mtop
        });

        if (pPage.type == 0 || pPage.type == 1) {
            new Element('a', {
                html: _('Preview')
            }).addEvent('click', function () {
                if (this.options.pageObj) {
                    this.options.pageObj.toPage(pPage);
                }
            }.bind(this)).inject(this.oldContext);

            new Element('a', {
                'class': 'delimiter'
            }).inject(this.oldContext);
        }

        var canDelete = canAdd = canHide = false;

        if (pPage.domain) {
            canDelete = ka.checkPageAccess(pPage.rsn, 'deletePages', 'd');
            canAdd = ka.checkPageAccess(pPage.rsn, 'addPages', 'd');
        } else {
            canDelete = ka.checkPageAccess(pPage.rsn, 'deletePages');
            canAdd = ka.checkPageAccess(pPage.rsn, 'addPages');
            canHide = ka.checkPageAccess(pPage.rsn, 'visible');
        }

        if (canAdd) {
            new Element('a', {
                html: _('New')
            }).addEvent('click', function () {

                var param = {};
                if (pPage.domain) {
                    param.selectDomain = pPage.rsn;
                    param.domain_rsn = pPage.rsn;
                } else {
                    param.selectPage = pPage.rsn;
                    param.domain_rsn = pPage.domain_rsn;
                }

                param.onComplete = function (pDomain) {
                    this.options.pageObj.domainTrees[pDomain].reload();
                }.bind(this)

                ka.wm.open('admin/pages/addDialog', param);

            }.bind(this)).inject(this.oldContext);
        }

        if (canDelete) {

            new Element('a', {
                html: _('Delete')
            }).addEvent('click', function () {
                if (this.options.pageObj) {
                    if (pPage.domain) {
                        this.options.pageObj.deleteDomain(pPage);
                    } else {
                        this.options.pageObj.deletePage(pPage);
                    }
                }
            }.bind(this)).inject(this.oldContext);

        }

        if (canHide) {
            new Element('a', {
                html: _('Hide/Unhide')
            }).addEvent('click', function () {
                this.toggleHide(pA);
            }.bind(this)).inject(this.oldContext);
        }

        if (canAdd || canDelete || canHide) {
            new Element('a', {
                'class': 'delimiter'
            }).inject(this.oldContext);

        }

        new Element('a', {
            html: _('Export')
        }).addEvent('click', function () {
            var param = {};
            if (pPage.domain) {
                param.domain = pPage.rsn;
            } else {
                param.page = pPage.rsn;
            }
            param.type = 'export';
            ka.wm.open('admin/system/backup', param);
        }.bind(this)).inject(this.oldContext);

        new Element('a', {
            html: _('Import')
        }).addEvent('click', function () {
            var param = {};
            if (pPage.domain) {
                param.domain = pPage.rsn;
            } else {
                param.page = pPage.rsn;
            }
            param.type = 'import';
            ka.wm.open('admin/system/backup', param);
        }.bind(this)).inject(this.oldContext);

        new Element('a', {
            'class': 'delimiter'
        }).inject(this.oldContext);

        if (!pPage.domain) {
            new Element('a', {
                html: _('Copy')
            }).addEvent('click', function () {
                ka.setClipboard(' \'' + pPage.title + '\' ' + _('page copied'), 'pageCopy', pPage);
            }.bind(this)).inject(this.oldContext);

            new Element('a', {
                html: _('Copy with subpages')
            }).addEvent('click', function () {
                ka.setClipboard(' \'' + pPage.title + '\' ' + _('page with subpages copied'), 'pageCopyWithSubpages', pPage);
            }.bind(this)).inject(this.oldContext);

            new Element('a', {
                'class': 'delimiter'
            }).inject(this.oldContext);
        }

        var canPasteInto = true;
        if (pPage.domain) {
            if (!ka.checkPageAccess(pPage.rsn, 'addPages', 'd')) {
                canPasteInto = false;
            }
        } else {
            if (!ka.checkPageAccess(pPage.rsn, 'addPages')) {
                canPasteInto = false;
            }
        }

        var canPasteAround = true;
        if (pA.parent) {
            var parentPage = pA.parent.retrieve('item');
            if (parentPage.domain) {
                if (!ka.checkPageAccess(parentPage.rsn, 'addPages', 'd')) {
                    canPasteAround = false;
                }
            } else {
                if (!ka.checkPageAccess(parentPage.rsn, 'addPages')) {
                    canPasteAround = false;
                }
            }
        }

        if (canPasteAround || canPasteInto) {

            new Element('a', {
                'class': 'noaction',
                html: _('Paste')
            }).inject(this.oldContext);


            if (canPasteAround && !pPage.domain) {
                new Element('a', {
                    'class': 'indented',
                    html: _('Before')
                }).addEvent('click', function () {
                    this.paste('up', pPage);
                }.bind(this)).inject(this.oldContext);
            }

            if (canPasteInto) {
                new Element('a', {
                    'class': 'indented',
                    html: _('Into')
                }).addEvent('click', function () {
                    this.paste('into', pPage);
                }.bind(this)).inject(this.oldContext);
            }
            if (canPasteAround && !pPage.domain) {
                new Element('a', {
                    'class': 'indented',
                    html: _('After')
                }).addEvent('click', function () {
                    this.paste('down', pPage);
                }.bind(this)).inject(this.oldContext);
            }
        }
        var csize = this.oldContext.getSize();

        if (mtop + csize.y > wsize.y) {
            mtop = mtop - csize.y;
            this.oldContext.setStyle('top', mtop + 1);
        }
    },

    toggleHide: function (pA) {

        var item = pA.retrieve('item');

        var req = {rsn: item.rsn};
        req.visible = item.visible == 1 ? 0 : 1;

        new Request.JSON({url: _path + 'admin/pages/setHide', noCache: 1, async: false, onComplete: function () {
            this.reloadParent(pA);
        }.bind(this)}).post(req);
    },

    paste: function (pPos, pPage) {
        var clipboard = ka.getClipboard();
        if (!(clipboard.type == 'pageCopyWithSubpages' || clipboard.type == 'pageCopy')) {
            return;
        }

        var req = {};
        req.page = clipboard.value.rsn;

        req.to = pPage.rsn;
        req.to_domain = pPage.domain ? 1 : 0;
        req.pos = pPos;
        req.type = clipboard.type;

        new Request.JSON({url: _path + 'admin/pages/paste', noCache: 1, async: false, onComplete: function () {
            this.reload();
            ka.loadSettings(['r2d']);
        }.bind(this)}).post(req);
    }
});
