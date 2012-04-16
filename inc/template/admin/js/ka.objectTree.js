ka.objectTree = new Class({

    Implements: [Options, Events],
    ready: false,

    options: {
        withObjectAdd: false, //adds a plus sign and opens <withObjectAdd> entry point
        viewAllObjects: false, //loads initily all objects
        rootObject: false
    },

    items: {},
    loadChildsRequests: {},

    loadingDone: false,
    firstLoadDone: false,

    load_object_childs: false,
    need2SelectAObject: false,
    domainA: false,
    inItemsGeneration: false,

    //contains the open state of the objects
    opens: {},

    objectKey: '',
    objectDefinition: {},

    initialize: function (pContainer, pObjectKey, pOptions, pRefs) {

        this.objectKey = pObjectKey;
        this.container = pContainer;

        this.setOptions(pOptions);

        if (Cookie.read('krynObjectTree_' + pObjectKey)) {
            var opens = Cookie.read('krynObjectTree_' + pObjectKey);
            opens = opens.split('.');
            Array.each(opens, function (open) {
                this.opens[ open ] = true;
            }.bind(this));
        }

        if (pRefs) {
            this.options.objectObj = pRefs.objectObj;
            this.options.win = pRefs.win;
        }

        this.main = new Element('div', {
            'class': 'ka-objectTree'
        }).inject(this.container);

        this.topDummy = new Element('div', {
            'class': 'ka-objectTree-top-dummy'
        }).inject(this.main);

        this.paneObjectsTable = new Element('table', {
            style: 'width: 100%',
            cellpadding: 0,
            cellspacing: 0
        }).inject(this.main);

        this.container.addEvent('scroll', this.setRootPosition.bind(this));

        if (this.options.win)
            this.options.win.addEvent('resize', this.setRootPosition.bind(this));

        this.paneObjectsTBody = new Element('tbody').inject(this.paneObjectsTable);
        this.paneObjectsTr = new Element('tr').inject(this.paneObjectsTBody);
        this.paneObjectsTd = new Element('td').inject(this.paneObjectsTr);

        this.paneObjects = new Element('div', {
            'class': 'ka-objectTree-objects'
        }).inject(this.paneObjectsTd);

        this.paneObjects.setStyle('display', '');


        if (this.options.rootObject){
            this.paneRoot = new Element('div', {
                'class': 'ka-objectTree-domain'
            }).inject(this.main);
            this.paneRoot.set('morph', {duration: 200});
        }

        if (this.options.selectObject) {
            this.startupWithObjectInfo(this.options.selectObject);
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


    startupWithObjectInfo: function (pRsn, pCallback) {

        new Request.JSON({url: _path + 'admin/objects/getObjectInfo', noCache: 1, onComplete: function (res) {

            if (res.domain_rsn == this.domain_rsn) {
                this.load_object_childs = [];
                res._parents.each(function (object) {
                    this.load_object_childs.include(object.rsn);
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

    setRootPosition: function () {

        if (!this.options.rootObject) return;

        var nLeft = this.container.scrollLeft;
        var nTop = 0;

        var panePos = this.paneObjectsTable.getPosition(this.container).y;
        if (panePos - 20 < 0) {
            nTop = (panePos - 20) * -1;
            var maxTop = this.paneObjects.getSize().y - 20;
            if (nTop > maxTop) nTop = maxTop;
        }

        this.paneRoot.morph({
            //'width': nWidth,
            'left': nLeft,
            'top': nTop
        });

    },

    loadFirstLevel: function () {

        if (this.lastFirstLevelRq) {
            this.lastFirstLevelRq.cancel();
        }

        var viewAllObjects = this.options.viewAllObjects ? 1 : 0;

        this.lastFirstLevelRq = new Request.JSON({url: _path + 'admin/backend/objects/getTreeDomain', noCache: 1, onComplete: this.renderFirstLevel.bind(this)}).get({
            domain_rsn: this.domain_rsn,
            viewAllObjects: viewAllObjects
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

        this.paneRoot.empty();
        this.paneObjects.empty();

        this.domainA = this.addItem(pDomain, this.paneRoot);

        if (this.options.withObjectAdd) {
            if (ka.checkDomainAccess(pDomain.rsn, 'addObjects')) {
                new Element('img', {
                    src: _path + 'inc/template/admin/images/icons/add.png',
                    title: _('Add object'),
                    'class': 'ka-objectTree-add'
                }).addEvent('click', function (e) {
                    this.fireEvent('objectAdd', pDomain.rsn);
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

        if (target.hasClass('ka-objectTree-item-toggler')) return;

        if (target.hasClass('ka-objectTree-item')) {
            a = target;
        }

        if (!a && target.getParent('.ka-objectTree-item')) {
            a = target.getParent('.ka-objectTree-item');
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
                    a.addClass('ka-objectTree-item-selected');
                }

                this.lastSelectedItem = a;
                this.lastSelectedObject = item;
            }

        } else {

            this.fireEvent('selection', [item, a])
            this.fireEvent('click', [item, a]);

            this.unselect();

            if (this.options.noActive != true) {
                a.addClass('ka-objectTree-item-selected');
            }

            this.lastSelectedItem = a;
            this.lastSelectedObject = item;
        }

    },

    reloadParentOfActive: function () {

        if (!this.lastSelectedItem) return;

        if (this.lastSelectedObject.domain || this.lastSelectedObject.prsn == 0) {
            this.reload();
            return;
        }

        var parent = this.lastSelectedItem.getParent().getPrevious();
        if (parent && parent.hasClass('ka-objectTree-item')) {
            this.lastScrollPos = this.container.getScroll();
            this.loadChilds(parent);
        }
    },

    addItem: function (pItem, pParent) {

        var a = new Element('div', {
            'class': 'ka-objectTree-item',
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

        a.objectTreeObj = this;

        a.span = new Element('span', {
            'class': 'ka-objectTree-item-title',
            text: (pItem.title) ? pItem.title : pItem.domain
        }).inject(a);

        if (this.lastSelectedObject && (
            (this.lastSelectedObject.domain && pItem.domain && this.lastSelectedObject.rsn == pItem.rsn) || (!pItem.domain && !this.lastSelectedObject.domain && this.lastSelectedObject.rsn == pItem.rsn)
            )) {

            if (this.options.noActive != true) {
                a.addClass('ka-objectTree-item-selected');
            }

            this.lastSelectedItem = a;
            this.lastSelectedObject = pItem;
        }

        if (pItem.domain) {
            this.items[0] = a;
        } else {
            this.items[ pItem.rsn ] = a;
            //Drag'n'Drop
            if (!this.options.noDrag) {
                a.addEvent('mousedown', function (e) {

                    if (!ka.checkObjectAccess(pItem.rsn, 'moveObjects')) {
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
            'class': 'ka-objectTree-item-masks'
        }).inject(a, 'top');

        new Element('img', {
            'class': 'ka-objectTree-item-type',
            src: _path + 'inc/template/admin/images/icons/' + this.types[pItem.type]
        }).inject(a.masks);


        if ((pItem.type == 0 || pItem.type == 1) && pItem.visible == 0) {
            new Element('img', {
                src: _path + 'inc/template/admin/images/icons/pageMasks/invisible.png'
            }).inject(a.masks);
        }

        if (pItem.type == 1) {
            new Element('img', {
                src: _path + 'inc/template/admin/images/icons/pageMasks/link.png'
            }).inject(a.masks);
        }

        if ((pItem.type == 0 || pItem.type == 3) && pItem.draft_exist == 1) {
            new Element('img', {
                src: _path + 'inc/template/admin/images/icons/pageMasks/draft_exist.png'
            }).inject(a.masks);
        }

        if (pItem.access_denied == 1) {
            new Element('img', {
                src: _path + 'inc/template/admin/images/icons/pageMasks/access_denied.png'
            }).inject(a.masks);
        }

        if (pItem.type == 0 && pItem.access_from_groups != "" && typeOf(pItem.access_from_groups) == 'string') {
            new Element('img', {
                src: _path + 'inc/template/admin/images/icons/pageMasks/access_group_limited.png'
            }).inject(a.masks);
        }

        /* toggler */
        a.toggler = new Element('img', {
            'class': 'ka-objectTree-item-toggler',
            title: _('Open/Close subitems'),
            src: _path + 'inc/template/admin/images/icons/tree_plus.png'
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
            a.childContainer = this.paneObjects;
        } else {
            a.childContainer = new Element('div', {
                'class': 'ka-objectTree-item-childs'
            }).inject(container);
        }

        a.childsLoaded = (pItem.childs) ? true : false;

        var openId = ( pItem.domain ) ? 'p' + pItem.rsn : pItem.rsn;

        if ((!this.firstLoadDone || this.need2SelectAObject)) {
            if ((this.options.selectDomain && pItem.domain ) || (this.options.selectObject && !pItem.domain && pItem.rsn == this.options.selectObject)) {
                if (this.options.noActive != true) {
                    a.addClass('ka-objectTree-item-selected');
                }
                this.lastSelectedItem = a;
                this.lastSelectedObject = pItem;
                this.need2SelectAObject = false;
            }
        }

        if (this.opens[openId]) {
            this.openChilds(a);
        }

        if ((!this.firstLoadDone || this.need2SelectAObject) && this.load_object_childs !== false) {
            if (!pItem.domain && this.load_object_childs.contains(pItem.rsn)) {
                this.openChilds(a);
            } else if (pItem.domain) {
                this.openChilds(a);
            }
        } else if ((!this.firstLoadDone || this.need2SelectAObject) && this.options.openFirstLevel && pItem.domain && !this.opens[openId]) {
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
            this.setRootPosition();
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
        Cookie.write('krynObjectTree_' + this.domain_rsn, opens);

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
        pA.toggler.set('src', _path + 'inc/template/admin/images/icons/tree_plus.png');
        this.opens[ id ] = false;
        this.setRootPosition();

        this.saveOpens();
    },

    openChilds: function (pA) {

        var item = pA.retrieve('item');
        var id = item.domain ? 'p' + item.rsn : item.rsn;

        pA.toggler.set('src', _path + 'inc/template/admin/images/icons/tree_minus.png');
        if (pA.childsLoaded == true) {
            pA.childContainer.setStyle('display', 'block');
            this.opens[ id ] = true;
            this.saveOpens();
        } else {
            this.loadChilds(pA, true);
        }
        this.setRootPosition();

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
                src: _path + 'inc/template/admin/images/loading.gif'
            }).inject(pA.masks)

            var id = ( item.domain ) ? 'p' + item.rsn : item.rsn;

            var viewAllObjects = this.options.viewAllObjects ? 1 : 0;

            this.loadChildsRequests[ item.rsn ] = true;
            new Request.JSON({url: _path + 'admin/objects/getTree', noCache: 1, onComplete: function (pItems) {

                pA.childContainer.empty();

                loader.destroy();

                if (pAndOpen) {
                    pA.toggler.set('src', _path + 'inc/template/admin/images/icons/tree_minus.png');
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
                this.setRootPosition();

            }.bind(this)}).get({ object_rsn: item.rsn, viewAllObjects: viewAllObjects });

        }
    },

    unselect: function () {

        if (this.lastSelectedItem) {
            this.lastSelectedItem.removeClass('ka-objectTree-item-selected');
        }

        this.lastSelectedItem = false;
        this.lastSelectedObject = false;
    },

    createDrag: function (pA, pEvent) {

        this.currentObjectToDrag = pA;

        var canMoveObject = true;
        var object = pA.retrieve('item');
        if (object.domain) {
            if (!ka.checkObjectAccess(object.rsn, 'moveObjects', 'd')) {
                canMoveObject = false;
            }
        } else {
            if (!ka.checkObjectAccess(object.rsn, 'moveObjects')) {
                canMoveObject = false;
            }
        }

        var kwin = pA.getParent('.kwindow-border');

        if (this.lastClone) {
            this.lastClone.destroy();
        }

        this.lastClone = new Element('div', {
            'class': 'ka-objectTree-drag-box'
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

                if (!element.hasClass('ka-objectTree-item')) {
                    element = element.getParent('.ka-objectTree-item');
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

        if (this.currentObjectToDrag == pTarget) return;

        this.dragNDropElement = pTarget;
        this.dragNDropPos = pPos;

        if (this.dropLastItem) {
            this.dropLastItem.removeClass('ka-objectTree-item-dragOver');
            this.dropLastItem.setStyle('padding-bottom', 1);
            this.dropLastItem.setStyle('padding-top', 1);
        }

        var item = pTarget.retrieve('item');


        pTarget.setStyle('padding-bottom', 1);
        pTarget.setStyle('padding-top', 1);

        if (!item.domain) {
            if (pPos == 'after' || pPos == 'before') {
                this.dropElement = new Element('div', {
                    'class': 'ka-objectTree-dropElement',
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
            if (!ka.checkObjectAccess(item.rsn, 'addObjects', 'd')) {
                canMoveInto = false;
            }
        } else {
            if (!ka.checkObjectAccess(item.rsn, 'addObjects')) {
                canMoveInto = false;
            }
        }

        var canMoveAround = true;
        if (pTarget.parent) {
            var parentObject = pTarget.parent.retrieve('item');
            if (parentObject.domain) {
                if (!ka.checkObjectAccess(parentObject.rsn, 'addObjects', 'd')) {
                    canMoveAround = false;
                }
            } else {
                if (!ka.checkObjectAccess(parentObject.rsn, 'addObjects')) {
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
                pTarget.addClass('ka-objectTree-item-dragOver');
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
            this.dropLastItem.removeClass('ka-objectTree-item-dragOver');
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
            var where = this.currentObjectToDrag.retrieve('item');

            var whereRsn = where.rsn;
            var toRsn = to.rsn;
            var code = pos[this.dragNDropPos];

            var toDomain = to.domain ? true : false;
            this.moveObject(whereRsn, toRsn, code, toDomain);
        }
        document.removeEvent('mouseup', this.cancelDragNDrop.bind(this));
    },


    reloadParent: function (pA) {
        if (pA.parent) {
            pA.objectTreeObj.reloadChilds(pA.parent);
        } else {
            pA.objectTreeObj.reload();
        }
    },

    moveObject: function (pWhereRsn, pToRsn, pCode, pToDomain) {
        var _this = this;
        var req = {
            rsn: pWhereRsn,
            torsn: pToRsn,
            mode: pCode,
            toDomain: pToDomain ? 1 : 0
        };

        new Request.JSON({url: _path + 'admin/objects/move', onComplete: function (res) {

            //target item this.dragNDropElement
            if (this.dragNDropElement.parent) {
                this.dragNDropElement.objectTreeObj.reloadChilds(this.dragNDropElement.parent);
            } else {
                this.dragNDropElement.objectTreeObj.reload();
            }

            //origin item this.currentObjectToDrag
            if (this.currentObjectToDrag.parent && (!this.dragNDropElement.parent || this.dragNDropElement.parent != this.currentObjectToDrag.parent)) {
                this.currentObjectToDrag.objectTreeObj.reloadChilds(this.currentObjectToDrag.parent);
            } else if (!this.dragNDropElement.parent || this.dragNDropElement.objectTreeObj != this.currentObjectToDrag.objectTreeObj) {
                this.currentObjectToDrag.objectTreeObj.reload();
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

    hasChilds: function (pObject) {
        if (this._objectsParent.get(pObject.rsn)) {
            return true;
        }
        return false;
    },

    createMoveContextMenu: function (pWhere, pTo) {

        var pos = this.currentDropper.getPosition(this.container);
        var st = this.container.scrollTop.toInt();

        var _this = this;
        var t = pWhere.retrieve('object');
        pWhereRsn = t.rsn;

        var t = pTo.retrieve('object');
        var domain_rsn = 0;
        var actions = [
            {code: 'up', label: _('Above')},
            {code: 'into', label: _('Into')},
            {code: 'down', label: _('Below')}
        ];


        pToRsn = t.rsn;
        if (t.rsn == 0) {
            //t.rsn = 'domain';
            pToRsn = 'domain';
            domain_rsn = t.domain_rsn;
            var actions = [
                {code: 'into', label: _('Into')}
            ];
        }

        _this.createMoveContextMenuOver = true;

        var mtop = pos.y - 15;
        if (mtop < 0) {
            mtop = 1;
        }

        var mleft = 6;
        if (this.currentDropper.getStyle('padding-left')) {
            mleft = this.currentDropper.getStyle('padding-left').toInt();
        }

        var context = new Element('div', {
            'class': 'objectsTree-context-move'
        }).setStyles({
            left: mleft,
            top: mtop,
            opacity: 0
        }).addEvent('mouseout',
            function () {
                _this.createMoveContextMenuOver = false;
                var __this = this;
                (function () {
                    if (!_this.createMoveContextMenuOver) {
                        __this.destroy();
                    }
                }).delay(500);
            }).inject(this.container);

        actions.each(function (item) {
            new Element('a', {
                html: item.label,
                'class': item.code
            }).addEvent('click',
                function () {
                    _this.moveObject(pWhereRsn, pToRsn, item.code, domain_rsn);
                    context.destroy();
                }).addEvent('mouseover',
                function (e) {
                    _this.createMoveContextMenuOver = true;
                }).inject(context);
        });

        context.set('tween', {duration: 200});
        context.tween('opacity', 1);

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
            this.items[ pRsn ].addClass('ka-objectTree-item-selected');

            this.lastSelectedItem = this.items[ pRsn ];
            this.lastSelectedObject = this.items[ pRsn ].retrieve('item');
            return;
        }

        this.need2SelectAObject = true;

        this.startupWithObjectInfo(pRsn, function (res) {

            this.options.selectObject = pRsn;
            this.renderFirstLevel();

            Array.each(this.load_object_childs, function (item) {
                if (this.items[item]) {
                    this.openChilds(this.items[item]);
                }
            }.bind(this));
        }.bind(this));

    },

    destroyContext: function () {
        if (this.oldContext) {
            this.lastContextA.removeClass('ka-objectTree-item-hover');
            this.oldContext.destroy();
            delete this.oldContext;
        }
    },

    openContext: function (pEvent, pA, pObject) {

    }

});
