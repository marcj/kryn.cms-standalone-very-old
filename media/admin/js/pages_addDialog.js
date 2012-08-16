var admin_pages_addDialog = new Class({

    initialize: function (pWin) {

        this.win = pWin;
        this.input = [];

        this._renderLayout();
    },

    choosePlace: function (pDomObject, pPos) {
        if (this.lastContext) this.lastContext.destroy();

        if (this.lastChoosenTitle) {
            this.lastChoosenTitle.removeClass('ka-pageTree-item-selected');
        }

        if (this.lastLine) {
            this.lastLine.destroy();
        }

        var item = pDomObject.retrieve('item');

        this.lastChoosenTitle = pDomObject;
        if (pPos == 'into') {
            this.lastChoosenTitle.addClass('ka-pageTree-item-selected');
        } else {
            this.lastLine = new Element('div', {
                style: 'border-top: 1px solid gray; height: 1px;',
                styles: {
                    'margin-left': pDomObject.getStyle('padding-left').toInt() + 10
                }
            });

            if (pPos == 'up') {
                this.lastLine.inject(pDomObject, 'before');
            } else {
                var target = pDomObject;

                if (pDomObject.getNext() && pDomObject.getNext().hasClass('ka-pageTree-item-childs')) {
                    target = pDomObject.getNext();
                }

                this.lastLine.inject(target, 'after');
            }
        }

        this.choosenItem = item;
        this.choosenDomObject = pDomObject;
        this.choosenPos = pPos;
        this.renderChoosenPlace();
    },

    renderChoosenPlace: function () {
        var pos = t('Below');
        if (this.choosenPos == 'up') {
            pos = t('Above');
        }
        if (this.choosenPos == 'into') {
            pos = t('Into');
        }
        var title = this.choosenDomObject.label;
        this.choosenPlaceDiv.set('html', _('Position') + ': <b>' + pos + ' <u>' + title + '</u></b>');
    },

    _renderLayout: function () {

        this.win.border.setStyle('height', 450);
        this.win.border.setStyle('width', 750);
        this.win.updateInlinePosition();

        var c = new Element('div', {
            style: 'position: absolute; left: 0px; right: 0px; top: 0px; bottom: 31px; overflow: auto;'
        }).inject(this.win.content);

        var leftSide = new Element('div', {
            'style': 'position: absolute; left: 0px; top: 0px; bottom: 0px; width: 52%; overflow: auto;'
        }).inject(c);
        var leftSide = new Element('div', {
            'style': 'padding: 8px;'
        }).inject(leftSide);

        new Element('div', {
            style: 'padding: 3px; font-size: 13px; font-weight: bold; color: gray; padding-bottom: 25px;',
            html: _('Step 1: Define new items')
        }).inject(leftSide);

        this.type = new ka.Field({
            label: _('Type'), type: 'select',
            items: {
                "0": t('Page'),
                "1": t('Link'),
                "2": t('Folder'),
                "3": t('Deposit')
            }
        }).inject(leftSide);

        this.layout = new ka.Field({
            label: _('Layout'), type: 'select'
        }).inject(leftSide);

        this.layout.select.add('', _(' -- No layout --'));

        Object.each(ka.settings.layouts, function (la, key) {
            this.layout.select.addSplit(key);
            var count = 0;
            Object.each(la, function (layoutFile, layoutTitle) {

                this.layout.select.add(layoutFile, layoutTitle);
                count++;

            }.bind(this));

            if (count == 0) {
                group.destroy();
            }

        }.bind(this));

        this.visible = new ka.Field({
            label: _('Visible'), desc: 'Let the items be visible in frontend navigations after creating', type: 'checkbox'
        }).inject(leftSide);

        this.type.addEvent('change', function (pValue) {
            if (pValue < 2) { //page or link
                this.layout.show();
                this.visible.show();
            } else {
                this.layout.hide();
                this.visible.hide();
            }
        }.bind(this));
        /*
         this.type = new Element('select').inject( leftSide );
         new Element('option', {text: _('Page'), value: 0}).inject( this.type );
         new Element('option', {text: _('Link'), value: 1}).inject( this.type );
         new Element('option', {text: _('Folder'), value: 2}).inject( this.type );
         new Element('option', {text: 'Ablage', value: 3}).inject( this.type );
         */

        var d = new Element('div', {'class': 'ka-field-main'}).inject(leftSide);
        var de = new Element('div', {'class': 'ka-field-title'}).inject(d);
        new Element('div', {'class': 'title', html: _('Titles')}).inject(de);
        new Element('div', {'class': 'desc', html: _('Enter here the titles of the new items. Each item in one field.')}).inject(de);

        this.inputPane = new Element('ol', {
            style: 'padding-left: 25px;'
        }).inject(leftSide);
        this.addInput();
        this.addInput();
        this.addInput();

        var addImg = new Element('img', {
            src: _path + PATH_MEDIA + '/admin/images/icons/add.png',
            style: 'cursor: pointer; position: relative; top: 4px; margin-right: 3px;'
        }).addEvent('click', this.addInput.bind(this)).inject(this.inputPane, 'after');

        new Element('span', {
            text: _('More items'),
            style: 'cursor: pointer;'
        }).addEvent('click', this.addInput.bind(this)).inject(addImg, 'after');

        var rightSide = new Element('div', {
            'style': 'position: absolute; right: 0px; top: 0px; bottom: 0px; width: 48%; overflow: auto;border-left: 1px solid silver;'
        }).inject(c);

        var rightSide = new Element('div', {
            'style': 'padding: 8px; padding-left: 10px;'
        }).inject(rightSide);


        new Element('div', {
            style: 'padding: 3px; font-size: 13px; font-weight: bold; color: gray;',
            html: _('Step 2: Define the position')
        }).inject(rightSide);

        new Element('div', {
            style: 'padding: 3px; color: gray; padding-bottom: 15px;',
            html: _('To define the position, click on a target and choose a direction. You will see a information about the position in the left bottom area of this window.')
        }).inject(rightSide);

        this.lastContext = null;

        this.win.content.addEvent('mouseover', function () {
            if (this.lastContext) this.lastContext.destroy();
        }.bind(this));


        var selectDomain = (this.win.params) ? this.win.params.selectDomain : null;
        var selectPage = (this.win.params) ? this.win.params.selectPage : null;


        this.pageTree = new ka.ObjectTree(rightSide, 'node', {
            rootId: this.win.params.domain_id,
            move: false,
            noSelection: true,
            openFirstLevel: true,
            onReady: function () {

                var domainItem = this.pageTree.rootA.retrieve('item');
                this.win.setTitle(_('Add pages to %s').replace('%s', domainItem.domain));
                var selected = this.pageTree.getSelected();

                if (selected) {
                    this.choosePlace(selected, 'into');
                }

            }.bind(this),

            onSelection: function (pItem, pDomObject) {

                if (this.lastContext) this.lastContext.destroy();

                if (pDomObject.objectKey == 'system_domain') {
                    if (!ka.checkPageAccess(pPage.id, 'addPages', 'd')) {
                        return;
                    }
                }

                this.lastContext = new Element('div', {
                    'class': 'ka-objectTree-context-move'
                }).addEvent('mouseover',
                    function (e) {
                        e.stop();
                    }).inject(this.win.content);

                var parent = pDomObject.parent;
                if (parent){
                    parentItem = parent.retrieve('item');
                }

                if (pDomObject.objectKey == 'node') {
                    if (!parent || (
                        (parent.objectKey == 'node' && ka.checkPageAccess(parent.id, 'addPages') ) && (parent.objectKey == 'system_domain' && ka.checkPageAccess(parent.id, 'addPages', 'd') )
                        )) {
                        new Element('a', {
                            text: t('Above'),
                            'class': 'up'
                        }).addEvent('click', function () {
                            this.choosePlace(pDomObject, 'up')
                        }.bind(this)).inject(this.lastContext);

                    }
                }
                new Element('a', {
                    text: _('Into'),
                    'class': 'into'
                }).addEvent('click', function () {
                    this.choosePlace(pDomObject, 'into')
                }.bind(this)).inject(this.lastContext);

                if (parent && parent.objectKey == 'node') {
                    if (!parent || (
                        (parent.objectKey == 'node' && ka.checkPageAccess(parent.id, 'addPages') ) && (parent.objectKey == 'system_domain' && ka.checkPageAccess(parent.id, 'addPages', 'd') )
                        )) {
                        new Element('a', {
                            text: t('Below'),
                            'class': 'down'
                        }).addEvent('click', function () {
                            this.choosePlace(pDomObject, 'down')
                        }.bind(this)).inject(this.lastContext);
                    }
                }

                var pos = pDomObject.getPosition(this.win.content);

                var mleft = pos.x;
                if (pDomObject.getStyle('padding-left')) {
                    mleft = pos.x + pDomObject.getStyle('padding-left').toInt();
                }

                this.lastContext.setStyles({
                    left: mleft,
                    top: pos.y - (this.lastContext.getSize().y / 2) + 7 + rightSide.scrollTop
                });
            }.bind(this)


        }, {win: this.win});


        /*

        this.pageTree = new ka.pagesTree(rightSide, this.win.params.domain_id, {
            move: false,
            noSelection: true,
            openFirstLevel: true,
            onReady: function () {

                var domainItem = this.pageTree.domainA.retrieve('item');
                this.win.setTitle(_('Add pages to %s').replace('%s', domainItem.domain));
                var selected = this.pageTree.getSelected();

                if (selected) {
                    this.choosePlace(selected, 'into');
                }

            }.bind(this),
            selectDomain: selectDomain,
            selectPage: selectPage,
            onSelection: function (pPage, pTitle) {
                if (this.lastContext) this.lastContext.destroy();

                if (pPage.domain) {
                    if (!ka.checkPageAccess(pPage.id, 'addPages', 'd')) {
                        return;
                    }
                }

                this.lastContext = new Element('div', {
                    'class': 'pagesTree-context-move'
                }).addEvent('mouseover',
                    function (e) {
                        e.stop();
                    }).inject(this.win.content);

                var parent = pPage.parent;
                if (parent) parent = parent.retrieve('item');

                if (!pPage.domain) {
                    if (!parent || (
                        (!parent.domain && ka.checkPageAccess(parent.id, 'addPages') ) && (parent.domain && ka.checkPageAccess(parent.id, 'addPages', 'd') )
                        )) {
                        new Element('a', {
                            text: _('Above'),
                            'class': 'up'
                        }).addEvent('click', function () {
                            this.choosePlace(pTitle, 'up')
                        }.bind(this)).inject(this.lastContext);

                    }
                }
                new Element('a', {
                    text: _('Into'),
                    'class': 'into'
                }).addEvent('click', function () {
                    this.choosePlace(pTitle, 'into')
                }.bind(this)).inject(this.lastContext);

                if (!pPage.domain) {
                    if (!parent || (
                        (!parent.domain && ka.checkPageAccess(parent.id, 'addPages') ) && (parent.domain && ka.checkPageAccess(parent.id, 'addPages', 'd') )
                        )) {
                        new Element('a', {
                            text: _('Below'),
                            'class': 'down'
                        }).addEvent('click', function () {
                            this.choosePlace(pTitle, 'down')
                        }.bind(this)).inject(this.lastContext);
                    }
                }

                var pos = pTitle.getPosition(this.win.content);

                var mleft = pos.x;
                if (pTitle.getStyle('padding-left')) {
                    mleft = pos.x + pTitle.getStyle('padding-left').toInt();
                }

                this.lastContext.setStyles({
                    left: mleft,
                    top: pos.y - (this.lastContext.getSize().y / 2) + 7 + rightSide.scrollTop
                });
            }.bind(this)
        });
        */
        this.bottom = new Element('div', {
            'class': 'kwindow-win-buttonBar' }).inject(this.win.content);

        this.saveBtn = new ka.Button(_('Cancel')).addEvent('click', function () {
            this.win.close();
        }.bind(this)).inject(this.bottom);

        this.saveBtn = new ka.Button(_('Add')).addEvent('click', this.addPages.bind(this)).inject(this.bottom);

        this.choosenPlaceDiv = new Element('div', {
            style: 'position: absolute; top: 6px; left: 6px; font-weight: bold; color: white; font-size: 12px;',
            html: _('No position choosen.')
        }).inject(this.bottom);

    },

    addPages: function () {
        var req = {};
        var c = 1;
        this.input.each(function (myi) {
            req['field_' + c] = myi.value;
            c++;
        });
        req.pos = this.choosenPos;


        if (!this.choosenItem) {
            this.win._alert(t('Please choose a position.'));
            return;
        }
        req.parentId = this.choosenDomObject.id;
        req.parentObjectKey = this.choosenDomObject.objectKey;


        req.type = this.type.getValue();
        req.layout = this.layout.getValue();
        req.visible = this.visible.getValue();

        new Request.JSON({url: _path + 'admin/pages/add', noCache: 1, async: false, onComplete: function () {
            ka.loadSettings(['r2d']);
            if (this.win.params.onComplete) {
                this.win.params.onComplete(req.domain_id);
            }
            this.win.close();
        }.bind(this)}).post(req);

    },

    addInput: function () {
        var p = new Element('li', {'class': 'ka-field-field'}).inject(this.inputPane);
        var input = new Element('input', {'class': 'text', type: 'text'}).addEvent('keydown', function (pEv) {
            pEv = new Event(pEv);
            if (pEv.key == 'tab' && this.input.indexOf(input) == this.input.length - 1) {
                var newfield = this.addInput();
                (function () {
                    newfield.focus();
                }).delay(100);
            }
        }.bind(this)).inject(p)
        this.input.include(input);
        return input;
    }
});
