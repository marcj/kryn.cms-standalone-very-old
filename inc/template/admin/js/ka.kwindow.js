ka.kwindowZIndex = 1000;
ka.kwindow = new Class({
    Implements: Events,

    initialize: function (pModule, pWindowCode, pLink, pInstanceId, pParams, pInline) {
        this.params = pParams;
        this.id = pInstanceId;
        this.module = pModule;
        this.code = pWindowCode;
        this.inline = pInline;
        this.link = pLink;
        if (!pLink) {
            this.link = {module: pModule, code: pWindowCode };
        }

        this.active = true;
        this.isOpen = true;
        this.createWin();
        //this.checkAccess();
        this.loadContent();

        this.addHotkey('esc', false, false, function () {
            this.close(true);
        }.bind(this));
    },

    //drops a icon-link to desktop which links to this window
    dropLink: function () {

        var title = this.getFullTitle();

        if (title.length > 25) {
            title = title.substr();
        }

        var icon = {
            title: title,
            params: this.params,
            module: this.module,
            code: this.code
        }
        ka._desktop.addIcon(icon);
        ka._desktop.save();
    },

    onResizeComplete: function () {
        this.fireEvent('resize');
    },

    softReload: function () {
    },

    iframeOnLoad: function () {

        if (this.inline) {
            var opener = ka.wm.getOpener(this.id);
            //opener.inlineContainer.empty();
            //this.content.inject( opener.inlineContainer );

            this.content.setStyles({'top': 5, 'bottom': 5, left: 5, right: 5});
            var borderSize = opener.border.getSize();

            opener.inlineContainer.setStyle('width', 530);

            //            this.iframe.contentWindow.document.body.style.height = '1px';
            //            this.iframe.contentWindow.document.body.style.width = '1px';

            var inlineSize = {x: this.iframe.contentWindow.document.html.scrollWidth + 50,
                y: this.iframe.contentWindow.document.html.scrollHeight + 50};

            //            this.iframe.contentWindow.document.body.style.height = inlineSize.y+'px';
            //            this.iframe.contentWindow.document.body.style.width = inlineSize.x+'px';

            if (inlineSize.x > borderSize.x) {
                opener.border.setStyle('width', inlineSize.x);
            }

            if (inlineSize.y + 35 > borderSize.y) {
                opener.border.setStyle('height', inlineSize.y + 35);
            }

            if (inlineSize.y < 450) {
                inlineSize.y = 450;
            }


            opener.inlineContainer.setStyles({
                height: inlineSize.y - 25,
                width: inlineSize.x
            });

        }
    },

    toDependMode: function (pInline) {
        this.inDependMode = true;

        this.dependModeOverlay = this.createOverlay();

        if (pInline) {

            var inlineModeParent = this.win;
            if (this.inline) {
                inlineModeParent = this.content.getParent();
            }

            this.inlineContainer = new Element('div', {
                'class': 'kwindow-win-inline',
                html: '<center><img src="' + _path + 'inc/template/admin/images/loading.gif" /></center>'
            }).inject(inlineModeParent);

        }
    },

    removeDependMode: function () {

        this.inDependMode = false;

        if (this.overlayForced) {
            this.overlayForced.destroy();
        }

        if (this.inlineContainer) {
            this.inlineContainer.destroy();
        }

        if (this.dependModeOverlay) {
            this.dependModeOverlay.destroy();
        }

    },

    getOpener: function () {
        return ka.wm.getOpener(this.id);
    },

    toBlockMode: function (pOpts, pCallback) {
        if (!pOpts.rsn > 0) return;

        this.blockModeOverlay = new Element('div', {
            style: ''
        }).inject(this.blockModeContainer);
    },

    _alert: function (pText, pCallback) {
        return this._prompt(pText, null, pCallback, {
            'alert': 1
        });
    },

    _confirm: function (pText, pCallback) {
        return this._prompt(pText, null, pCallback, {
            'confirm': 1
        });
    },

    _passwordPrompt: function (pDesc, pDefaultValue, pCallback, pOpts) {
        if (!pOpts) pOpts = {};
        pOpts.pw = 1;
        return this._prompt(pDesc, pDefaultValue, pCallback, pOpts);
    },

    _prompt: function (pDesc, pDefaultValue, pCallback, pOpts) {

        var res = false;
        if (!pOpts) pOpts = {};
        if (pOpts['confirm'] == 1) {
            res = true;
        }

        var main = this.newDialog(pDesc);

        if (pOpts['alert'] != 1 && pOpts['confirm'] != 1) {
            var input = new Element('input', {
                'class': 'text',
                'type': (pOpts.pw == 1) ? 'password' : 'text',
                value: pDefaultValue
            }).inject(main.content);

            input.focus();
        }

        var ok = false;

        if (pOpts['alert'] != 1) {

            new ka.Button(_('Cancel')).addEvent('click', function () {
                main.close();
                if (pCallback) {
                    pCallback(false);
                }
            }.bind(this)).inject(main.bottom);

            ok = new ka.Button('OK').addEvent('keyup',
                function (e) {
                    e.stopPropagation();
                    e.stop();
                }).addEvent('click', function (e) {
                if (e) {
                    e.stop();
                }
                if (input && input.value != '') {
                    res = input.value;
                }
                main.close();
                if (pCallback) {
                    pCallback.delay(50, null, res);
                }
            }.bind(this)).inject(main.bottom);
        }

        if (pOpts && pOpts['alert'] == 1) {

            ok = new ka.Button('OK').addEvent('keyup',
                function (e) {
                    e.stopPropagation();
                    e.stop();
                }).addEvent('click', function (e) {
                if (e) {
                    e.stop();
                }
                main.close();
                if (pCallback) {
                    pCallback.delay(50);
                }
            }.bind(this)).inject(main.bottom);
        }

        if (pOpts['alert'] != 1 && pOpts['confirm'] != 1) {
            input.addEvent('keyup', function (e) {
                if (e.key == 'enter') {
                    e.stopPropagation();
                    e.stop();
                    ok.fireEvent('click');
                }
            });
        }

        if (ok && !input) {
            ok.focus();
        }

        main.center();

        return main;
    },


    newDialog: function (pText, pAbsoluteContent) {

        var main = new Element('div', {
            'class': 'ka-kwindow-prompt'
        }).addEvent('click', function (e) {
            e.stopPropagation();
        });

        main.content = new Element('div', {
            html: pText,
            'class': 'ka-kwindow-prompt-text'
        }).inject(main);

        if (pAbsoluteContent) {
            main.content.addClass('ka-kwindow-prompt-text-abs');
        }

        main.overlay = this.createOverlay();

        main.inject(this.border);

        main.center = function () {
            var size = this.border.getSize();
            var dsize = main.getSize();
            var left = (size.x.toInt() / 2 - dsize.x.toInt() / 2);
            var mtop = (size.y.toInt() / 2 - dsize.y.toInt() / 2);
            main.setStyle('left', left);
            main.setStyle('top', mtop);
        }.bind(this);
        this.addEvent('resize', main.center);

        main.close = function () {

            main.overlay.destroy();
            main.destroy();
            this.removeEvent('resize', main.center);

        }.bind(this);

        main.bottom = new Element('div', {
            'class': 'ka-kwindow-prompt-bottom'
        }).inject(main);

        this.lastDialog = main;

        main.center();

        return main;

    },


    /*
     checkAccess: function(){
     var _this = this;
     var req = {
     code: this.code,
     module: this.module
     };
     new Request.JSON({url: _path+'admin/backend/window/checkAccess', noCache: 1, onComplete: function(res){
     if( res == true ){
     } else {
     _this._alert( 'Zugriff verweigert', function(){
     _this.close( true );
     });
     }
     }}).post( req );
     },
     */

    parseTitle: function (pHtml) {

        pHtml = pHtml.replace('<img', ' » <img');
        return pHtml.stripTags();
    },

    getTitle: function () {
        if (this.titleAdditional) {
            return this.parseTitle(this.titleAdditional.get('html'));
        }
        return '';
    },

    getFullTitle: function () {
        if (this.titlePath) {
            return this.parseTitle(this.titlePath.get('html'));
        }
        return '';
    },

    setTitle: function (pTitle) {
        this.clearTitle();
        this.addTitle(pTitle);
    },

    toBack: function () {
        this.title.setStyle('opacity', 0.4);
        this.inFront = false;
    },


    clearTitle: function () {
        this.titleAdditional.empty();
        ka.wm.updateWindowBar();
    },

    addTitle: function (pText) {

        new Element('img', {
            src: _path + 'inc/template/admin/images/ka-kwindow-title-path.png'
        }).inject(this.titleAdditional);

        new Element('span', {
            text: pText
        }).inject(this.titleAdditional);
        ka.wm.updateWindowBar();

    },

    isInFront: function () {

        if (ka.wm.zIndex == this.border.getStyle('z-index')) {
            return true;
        }

        return false;
    },

    toFront: function () {

        if (this.active) {
            this.title.setStyle('opacity', 1);
            if (this.border.getStyle('display') != 'block') {
                this.border.setStyles({
                    'display': 'block',
                    'opacity': 0
                });
                this.border.set('tween', {duration: 300});
                this.border.tween('opacity', 1);
            }

            ka.wm.zIndex++;
            this.border.setStyle('z-index', ka.wm.zIndex);

            ka.wm.setFrontWindow(this.id);
            if (ka.wm.toFront(this.id) == false) {//abhängigkeit zu anderem fenster vorhanden
                var win = ka.wm.getDependOn(this.id);
                if (win) {
                    win.toFront();
                    win.highlight();
                }
                return false;
            }
            if (this.inDependMode) return;

            this.isOpen = true;
            this.inFront = true;
            this.deleteOverlay();
            ka.wm.updateWindowBar();

            return true;
        }
    },

    addHotkey: function (pKey, pControl, pAlt, pCallback) {

        document.addEvent('keydown', function (e) {
            if (this.inFront && (!this.inOverlayMode)) {
                if (pControl && !e.control) return;
                if (pAlt && !e.alt) return;
                if (e.key == pKey) {
                    try {
                        pCallback(e);
                    } catch (e) {
                        logger(e)
                    }
                    ;
                }

            }
        }.bind(this));

    },


    _highlight: function (_this) {
        [_this.title, _this.bottom].each(function (item) {
            item.set('tween', {duration: 50, onComplete: function () {
                item.tween('opacity', 1);
            }});
            item.tween('opacity', 0.3);
        });
    },

    highlight: function () {
        var _this = this;
        (function () {
            _this._highlight(_this)
        }).delay(1);
        (function () {
            _this._highlight(_this)
        }).delay(150);
        (function () {
            _this._highlight(_this)
        }).delay(300);
    },

    isActive: function () {
        var _this = this;
        if (this.active) {
            if (ka.wm.dependExist(this.id) == true) {//abhängigkeit zu anderem fenster vorhanden
                this.highlight();
                return false;
            }
            return true;
        }
        return false;
    },

    setBarButton: function (pButton) {
        this.barButton = pButton;
    },

    minimize: function () {

        this.isOpen = false;

        ka.wm.updateWindowBar();

        var cor = this.border.getCoordinates();
        var quad = new Element('div', {
            styles: {
                position: 'absolute',
                left: cor.left,
                top: cor.top,
                width: cor.width,
                height: cor.height,
                border: '3px solid gray'
            }
        }).inject(this.border.getParent());

        quad.set('morph', {duration: 300, transition: Fx.Transitions.Quart.easeOut, onComplete: function () {
            quad.destroy();
        }});

        var cor2 = this.barButton.getCoordinates(this.border.getParent());
        quad.morph({
            width: cor2.width,
            top: cor2.top,
            left: cor2.left,
            height: cor2.height
        });
        this.border.setStyle('display', 'none');
        this.onResizeComplete();
    },

    maximize: function (pDontRenew) {
        var _this = this;

        if (this.isActive() == false) return;

        if (this.maximized) {
            this.borderDragger.attach();

            this.border.setStyles(this.oldDimension);
            this.maximizer.set('src', _path + 'inc/template/admin/images/win-top-bar-maximize.png');
            this.maximized = false;
            this.resizeBottomRight.setStyle('display', 'block');
            this.bottom.set('class', 'kwindow-win-bottom');
        } else {
            this.borderDragger.detach();

            this.oldDimension = this.border.getCoordinates(this.border.getParent());
            this.border.setStyles({
                width: '100%',
                height: '100%',
                left: 0,
                top: 0
            });
            this.maximizer.set('src', _path + 'inc/template/admin/images/win-top-bar-maximize-1.png');
            this.maximized = true;
            this.resizeBottomRight.setStyle('display', 'none');
            this.bottom.set('class', 'kwindow-win-bottom-maximized');
        }
        this.onResizeComplete();
    },

    saveDimension: function () {
        var pos = this.border.getCoordinates(this.border.getParent());
        var windows = (ka.settings['user'] && ka.settings['user']['windows']) ? ka.settings['user']['windows'] : {};
        if (!windows.set) {
            windows = new Hash();
        }

        if (this.maximized && this.oldDimension) {
            pos = this.oldDimension;
            pos.maximized = true;
        }
        pos.width = pos.width - 2;
        pos.height = pos.height - 2;
        windows.set(this.module + '::' + this.code, pos);
        ka.settings['user']['windows'] = windows;
        ka.saveUserSettings();
    },

    loadDimensions: function () {

        if (this.inline) return;

        this.border.setStyle('top', 20);
        this.border.setStyle('left', 40);
        this.border.setStyle('width', 500);
        this.border.setStyle('height', 320);

        var windows = ka.settings['user']['windows'];

        if (!windows) {
            windows = {};
        }

        var pos = windows[this.module + '::' + this.code];

        if (pos && pos.width > 50) {

            this.border.setStyles(pos);
            if (pos.maximized) {
                this.maximize(true);
            }

        } else if (this.values) {
            if (this.values.defaultWidth > 0) {
                this.border.setStyle('width', this.values.defaultWidth);
            }
            if (this.values.defaultHeight > 0) {
                this.border.setStyle('height', this.values.defaultHeight);
            }
        }

        if (this.values.fixedWidth > 0 || this.values.fixedHeight > 0) {
            if (this.values.fixedWidth > 0) {
                this.border.setStyle('width', this.values.fixedWidth);
            }
            if (this.values.fixedHeight > 0) {
                this.border.setStyle('height', this.values.fixedHeight);
            }
            this.resizeBottomRight.destroy();
            this.bottom.setStyle('background-image', 'none');
        }

        //check dimensions if to big/small
        this.checkDimensions();
    },

    checkDimensions: function () {

        if (this.inline) return;
        if (this.maximized) return;

        var desktopSize = $('desktop').getSize();
        var borderSize = this.border.getSize();
        var borderPosition = {y: this.border.getStyle('top').toInt(), x: this.border.getStyle('left').toInt()};

        var newY = false;
        var newHeight = false;

        if (this.values.minWidth && borderSize.x < this.values.minWidth) {
            this.border.setStyle('width', this.values.minWidth);
        }
        if (this.values.minWidth && borderSize.y < this.values.minHeight) {
            this.border.setStyle('height', this.values.minHeight);
        }


        var newX = false;
        var newWidth = false;

        if (borderSize.y + borderPosition.y > desktopSize.y) {
            var diff = (borderSize.y + borderPosition.y) - desktopSize.y;
            if (diff < borderPosition.y) {
                newY = borderPosition.y - diff - 1;
            } else {
                newY = 5;
                newHeight = desktopSize.y - 10;
            }
        }

        if (borderSize.x + borderPosition.x > desktopSize.x) {
            var diff = (borderSize.x + borderPosition.x) - desktopSize.x;
            if (diff < borderPosition.x) {
                newX = borderPosition.x - diff - 1;
            } else {
                newX = 5;
                newWidth = desktopSize.x - 10;
            }
        }

        if (newY) this.border.setStyle('top', newY);
        if (newX) this.border.setStyle('left', newX);

        if (newHeight) this.border.setStyle('height', newHeight);
        if (newWidth) this.border.setStyle('width', newWidth);

        if (this.border.getSize().y < 150) {
            this.border.setStyle('height', 150);
        }


    },

    close: function (pInternal) {

        if (this.isActive() == false) return;

        if (pInternal) {
            this.interruptClose = false;
            this.fireEvent('close');
            if (this.interruptClose == true) return;
        }

        ka.wm.close(this);

        //save dimension
        if (this.border) {

            if (this.module == 'users' && this.code == 'users/edit/') {
                ka.loadSettings();
            } else {
                this.saveDimension();
            }

            this.border.getElements('a.kwindow-win-buttonWrapper').each(function (button) {
                if (button.toolTip && button.toolTip.main) {
                    button.toolTip.main.destroy();
                }
            });

            this.border.destroy();
        }

        this.inFront = false;

        if (this.onClose) {
            this.onClose();
        }
    },

    loadContent: function (pVals) {

        if (pVals) {
            this._loadContent(pVals);
        } else {
            var _this = this;

            var module = this.module + '/';
            if (this.module == 'admin') {
                module = '';
            }

            this._ = new Request.JSON({url: _path + 'admin/' + module + this.code + '?cmd=getInfo', onComplete: function (res) {

                if (res.error == 'access_denied') {
                    alert(_('Access denied'));
                    _this.close(true);
                    return;
                }
                if (!res || res.error == 'param_failed') {
                    alert(_('Admin-Path not found') + ': ' + _this.module + ' => ' + _this.code);
                    _this.close(true);
                    return;
                }
                this._loadContent(res, res._path);
            }.bind(this)}).post();
        }
    },

    _loadContent: function (pVals, pPath) {
        this.values = pVals;
        if (this.values.multi === false) {
            var win = ka.wm.checkOpen(this.module, this.code, this.id);
            if (win) {
                this.close(true);
                if (win.softOpen) win.softOpen(this.params);
                win.toFront();
                return;
            }
        }

        var title = ka.settings.configs[ this.module ]['title']['en'];

        if (ka.settings.configs[ this.module ]['title'][window._session.lang]) {
            title = ka.settings.configs[ this.module ]['title'][window._session.lang];
        }

        if (title != 'Kryn.cms') {
            new Element('span', {
                text: title
            }).inject(this.titleText, 'before');

            new Element('img', {
                src: _path + 'inc/template/admin/images/ka-kwindow-title-path.png'
            }).inject(this.titleText, 'before');
        }

        Array.each(pPath, function (label) {

            new Element('span', {
                text: _(label)
            }).inject(this.titleText, 'before');

            new Element('img', {
                src: _path + 'inc/template/admin/images/ka-kwindow-title-path.png'
            }).inject(this.titleText, 'before');


        }.bind(this));

        if (!this.inline) {
            this.createResizer();
        }

        this.titleText.set('text', _(pVals.title));

        var _this = this;

        if (pVals.type == 'iframe') {
            this.iframe = new IFrame('iframe_kwindow_' + this.id, {
                'class': 'kwindow-iframe',
                frameborder: 0
            }).addEvent('load',
                function () {
                    _this.iframe.contentWindow.win = _this;
                    _this.iframe.contentWindow.ka = ka;
                    _this.iframe.contentWindow.wm = ka.wm;
                    this.contentWindow.fireEvent('kload');
                }).inject(this.content);
            this.iframe.set('src', _path + pVals.src);
        } else if (pVals.type == 'custom') {
            this.renderCustom();
        } else if (pVals.type == 'combine') {
            this.renderCombine();
        } else if (pVals.type == 'list') {
            this.renderList();
        } else if (pVals.type == 'add') {
            this.renderAdd();
        } else if (pVals.type == 'edit') {
            this.renderEdit();
        }

        if (this.inline) {
            this.getOpener().inlineContainer.empty();
            this.border.addClass('kwindow-border-inline');
            this.border.inject(this.getOpener().inlineContainer);
            this.updateInlinePosition();

            this.getOpener().addEvent('resize', this.updateInlinePosition.bind(this));

        } else {
            this.border.inject($('desktop'));
        }

        ka.wm.updateWindowBar();

        if (this.values.noMaximize === true) {
            this.maximizer.destroy();
        }

        if (this.values.print === true) {
            this.printer = new Element('img', {
                'class': 'kwindow-win-printer',
                src: _path + 'inc/template/admin/images/icons/printer.png'
            }).inject(this.border);
            this.printer.addEvent('click', this.print.bind(this));
        }

        this.loadDimensions();
    },

    updateInlinePosition: function () {
        if (this.inline && this.getOpener() && this.getOpener().inlineContainer) {
            this.border.position({ relativeTo: this.getOpener().inlineContainer });
        }
    },

    print: function () {
        var size = this.border.getSize();
        var popup = window.open('', '', 'width=' + size.x + ',height=' + size.y + ',menubar=yes,resizeable=yes,status=yes,toolbar=yes');
        var clone = this.content.clone();
        popup.document.open();
        popup.document.write('<head><title>Drucken</title></head><body></body>');
        clone.inject(popup.document.body);
        popup.document.close();

        $A(document.styleSheets).each(function (s, index) {
            var w = new Element('link', {
                rel: 'stylesheet',
                type: 'text/css',
                href: s.href,
                media: 'screen'
            }).inject(popup.document.body);
        });
        popup.print();
    },

    renderEdit: function () {
        this.edit = new ka.windowEdit(this);
    },

    renderAdd: function () {
        this.add = new ka.windowAdd(this);
    },

    renderCombine: function () {
        this.combine = new ka.windowCombine(this);
    },

    renderList: function () {
        this.list = new ka.list(this);
    },

    renderCustom: function () {
        var id = 'text';
        var _this = this;

        if (this.code.substr(this.code.length - 1, 1) == '/') {
            this.code = this.code.substr(0, this.code.length - 1);
        }

        var javascript = this.code.replace(/\//g, '_');

        var mdate = this.values.cssmdate;

        if (this.module == 'admin' && mdate) {
            new Asset.css(_path + 'inc/template/admin/css/' + javascript + '.css?mdate=' + mdate);
        } else if (mdate) {
            new Asset.css(_path + 'inc/template/' + this.module + '/admin/css/' + javascript + '.css?mdate=' + mdate);
        }

        var id = parseInt(Math.random() * 100) + parseInt(Math.random() * 100);

        window['contentCantLoaded_' + id] = function (pFile) {
            _this._alert('custom javascript file not found: ' + pFile, function () {
                _this.close(true);
            });
        }
        window['contentLoaded_' + id] = function () {
            this.custom = new window[ this.module + '_' + javascript ](this);
            if (this.custom) {
                this.addEvent('close', function () {
                    delete this.custom;
                }.bind(this));
            }
        }.bind(this);

        new Asset.javascript(_path + 'admin/backend/loadCustomJs/module:' + this.module + '/code:' + javascript + '/onLoad:' + id);
    },

    createWin: function () {
        var _this = this;

        this.border = new Element('div', {
            'class': 'kwindow-border  mooeditable-dialog-container'
        }).addEvent('mousedown', function (e) {
            if (this.mouseOnShadow != true) {
                _this.toFront();
            }
        }.bind(this)).inject(document.hidden).store('win', this);


        if (ka.settings.user.css3Shadow && ka.settings.user.css3Shadow == 1) {

            this.border.addClass('kwindow-border-shadow');

        } else if (!this.inline) {
            new Element('div', {
                'class': 'kwindow-shadow-bottom'
            }).inject(this.border);
            new Element('div', {
                'class': 'kwindow-shadow-bottom-left'
            }).inject(this.border);
            new Element('div', {
                'class': 'kwindow-shadow-bottom-right'
            }).inject(this.border);
            new Element('div', {
                'class': 'kwindow-shadow-left'
            }).inject(this.border);
            new Element('div', {
                'class': 'kwindow-shadow-right'
            }).inject(this.border);
            new Element('div', {
                'class': 'kwindow-shadow-top-right'
            }).inject(this.border);
            new Element('div', {
                'class': 'kwindow-shadow-top-left'
            }).inject(this.border);
        }

        this.border.getElements('div').each(function (mydiv) {
            if (mydiv.get('class').search('-shadow-') > 0) {
                mydiv.addEvent('mouseover', function () {
                    this.mouseOnShadow = true;
                }.bind(this));
                mydiv.addEvent('mouseout', function () {
                    this.mouseOnShadow = false;
                }.bind(this));
            }
        }.bind(this));

        this.win = this.border;

        this.title = new Element('div', {
            'class': 'kwindow-win-title'
        }).addEvent('dblclick',
            function () {
                if (_this.values.noMaximize !== true) {
                    _this.maximize();
                }
            }).inject(this.win);


        this.titlePath = new Element('span', {'class': 'ka-kwindow-titlepath'}).inject(this.title);
        this.titleText = new Element('span').inject(this.titlePath);

        this.titleAdditional = new Element('span').inject(this.titlePath);


        this.titleGroups = new Element('div', {
            'class': 'kwindow-win-titleGroups'
        }).addEvent('mousedown',
            function (e) {
                //e.stopPropagation();
            }).inject(this.win);

        this.createTitleBar();

        this.bottom = new Element('div', {
            'class': 'kwindow-win-bottom'
        }).inject(this.win);


        this.borderDragger = this.border.makeDraggable({
            handle: [this.title, this.titleGroups],
            //presentDefault: true,
            //stopPropagation: true,
            container: $('desktop'),
            snap: 3,
            onDrag: function (el, ev) {
                var cor = el.getCoordinates();
                if (cor.top < 0) {
                    el.setStyle('top', 0);
                }
                if (cor.left < 0) {
                    el.setStyle('left', 0);
                }
            },
            onStart: function () {
                if (ka.performance) {
                    this.content.setStyle('display', 'none');
                    this.titleGroups.setStyle('display', 'none');
                }
                window.fireEvent('click');

                ka.wm.hideContents();

            }.bind(this),
            onComplete: function () {
                ka.wm.showContents();

                if (ka.performance) {
                    this.content.setStyle('display', 'block');
                    this.titleGroups.setStyle('display', 'block');
                }

                ka.wm.fireEvent('move');
                this.fireEvent('move');

                this.saveDimension();
            }.bind(this),
            onCancel: function () {
                ka.wm.showContents();

                if (ka.performance) {
                    this.content.setStyle('display', 'block');
                    this.titleGroups.setStyle('display', 'block');
                }
            }.bind(this)
        });
        this.title.addEvent('mousedown', this.border.fireEvent.bind(this.border, 'mousedown'));
        this.titleGroups.addEvent('mousedown', this.border.fireEvent.bind(this.border, 'mousedown'));

        if (this.inline) {
            this.title.setStyle('display', 'none');
            this.titleGroups.setStyle('display', 'none');
            this.titleBar.setStyle('display', 'none');
            this.linker.setStyle('display', 'none');
        }

        this.content = new Element('div', {
            'class': 'kwindow-win-content'
        }).inject(this.win);

        this.inFront = true;

        (function () {
            _this.toFront();
        }).delay(40);

        //        this.loadContent();

    },

    setStatusText: function (pVal) {
        this.bottom.set('html', pVal);
    },

    extendHead: function () {
        this.title.setStyle('height', 39 + 14);
        this.content.setStyle('top', 39 + 16);
    },

    addTabGroup: function () {
        this.extendHead();
        return new ka.tabGroup(this.titleGroups);
    },

    addSmallTabGroup: function () {
        this.extendHead();
        return new ka.smallTabGroup(this.titleGroups);
    },

    addButtonGroup: function () {
        this.extendHead();
        return new ka.buttonGroup(this.titleGroups);
    },

    addBottomBar: function () {
        this.bottomBar = new Element('div', {
            'class': 'ka-windowEdit-actions',
            style: 'bottom: 18px'
        }).inject(this.border);

        this.bottomBar.addButton = function (pTitle, pOnClick) {
            return new ka.Button(pTitle).addEvent('click', pOnClick).inject(this.bottomBar);
        }.bind(this);

        this.content.setStyle('bottom', 49);
        return this.bottomBar;
    },

    createTitleBar: function () {
        var _this = this;
        this.titleBar = new Element('div', {
            'class': 'kwindow-win-titleBar'
        }).inject(this.win);

        this.linker = new Element('img', {
            style: 'position: absolute; left: 3px; top: 8px; cursor: pointer',
            title: _('Create a shortcut to the desktop'),
            src: _path + 'inc/template/admin/images/win-top-bar-link.png'
        }).addEvent('click', this.dropLink.bind(this)).inject(this.win);

        this.minimizer = new Element('img', {
            'class': 'kwindow-win-titleBarIcon',
            src: _path + 'inc/template/admin/images/win-top-bar-minimize.png'
        }).addEvent('click',
            function () {
                _this.minimize();
            }).inject(this.titleBar)

        this.maximizer = new Element('img', {
            'class': 'kwindow-win-titleBarIcon',
            src: _path + 'inc/template/admin/images/win-top-bar-maximize.png'
        }).addEvent('click',
            function () {
                _this.maximize();
            }).inject(this.titleBar);

        this.closer = new Element('div', {
            'class': 'kwindow-win-titleBarIcon kwindow-win-titleBarIcon-close'
        }).addEvent('click',
            function () {
                _this.close(true);
            }).inject(this.titleBar);

        this.titleBar.getElements('img').addEvents({
            'mouseover': function () {
                this.setStyle('opacity', 0.5);
            },
            'mouseout': function () {
                this.setStyle('opacity', 1);
            }
        });


    },

    createOverlay: function (pForce) {

        var overlay = new Element('div', {
            'class': 'ka-kwindow-overlay',
            styles: {
                opacity: 0.5,
                position: 'absolute',
                'background-color': '#888',
                left: 0, right: 0, 'top': 0, bottom: 0
            }
        });

        //if( pForce || this.forceOverlay || (this.values && this.values.type == 'iframe') )
        overlay.inject(this.border);

        return overlay;
    },

    deleteOverlay: function () {

        if (ka.performance) {
            this.content.setStyle('display', 'block');
            this.titleGroups.setStyle('display', 'block');
        }
        if (this.inDependMode) return;

        this.inOverlayMode = false;
    },

    createResizer: function () {
        var _this = this;
        this.resizeBottomRight = new Element('div', {
            styles: {
                position: 'absolute',
                right: -1,
                bottom: -1,
                width: 9,
                height: 9,
                opacity: 0.7,
                'background-position': '0px 11px',
                'background-image': 'url(' + _path + 'inc/template/admin/images/win-bottom-resize.png)',
                cursor: 'se-resize'
            }
        }).inject(this.border);

        var minWidth = ( this.values.minWidth > 0 ) ? this.values.minWidth : 400;
        var minHeight = ( this.values.minHeight > 0 ) ? this.values.minHeight : 300;

        this.border.makeResizable({
            grid: 1,
            limit: {x: [minWidth, 2000], y: [minHeight, 2000]},
            handle: this.resizeBottomRight,
            onStart: function () {

                if (ka.performance) {
                    this.content.setStyle('display', 'none');
                }
                window.fireEvent('click');

                ka.wm.hideContents();

            }.bind(this),
            onComplete: function () {

                ka.wm.showContents();

                this.content.setStyle('display', 'block');

                this.saveDimension();
                this.onResizeComplete();
                this.fireEvent('resize');

            }.bind(this),
            onCancel: function () {
                ka.wm.showContents();
            }
        });

        return;
    }

});
