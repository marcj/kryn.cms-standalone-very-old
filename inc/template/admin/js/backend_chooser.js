var admin_backend_chooser = new Class({

    Implements: [Events,Options],

    options: {
        cookie: 'kFieldChooser',
        value: false,

        objects: [], //
        objectOptions: {}

        /*
        <objectId>: {
            <objectChooserOptions>
        }
        files:

        multi: false
        */
    },

    objectChooserInstance: {},
    pane2ObjectId: [],

    initialize: function (pWin) {
        this.win = pWin;

        this.options = this.win.params;

        this.value = this.win.params.value;
        this.p = _path + 'inc/template/admin/images/';

        this.options.multi = (this.options.multi) ? true : false;

        this.cookie = (this.win.params.cookie) ? this.win.params.cookie : '';
        this.cookie = 'kFieldChooser_' + this.cookie + '_';

        this.bottomBar = this.win.addBottomBar();
        this.bottomBar.addButton(_('Close'), this.win.close.bind(this.win));
        this.bottomBar.addButton(_('Choose'), function(){this.choose();}.bind(this));

        this._createLayout();
    },

    saveCookie: function () {

        Cookie.write(this.cookie + 'lastTab', this.currentPane);
    },

    _createLayout: function () {

        this.tapPane = new ka.tabPane(this.win.content, true, this.win);

        /*
        if (this.options.pages) {
            this.createPages();
            if (this.win.params.domain) {
                this.renderDomain(this.win.params.domain);
            } else {
                if (this.options.only_language) {//only view pages from this langauge and doesnt appear the language-selectbox
                    this.language = this.options.only_language;
                    this.loadPages();
                } else {
                    this.createLanguageBox();
                }
            }
        }*/

        if (!this.options.objectOptions)
            this.options.objectOptions = {};

        if (true || !this.options.objects || this.options.objects.length == 0){

            Object.each(ka.settings.configs, function(config, extKey){

                if (config.objects){
                    Object.each(config.objects, function(object, objectKey){
                        if (object.selectable)
                            this.createObjectChooser(objectKey, object);
                    }.bind(this));
                }

            }.bind(this));

        } else {

        }

    },

    createObjectChooser: function(pObjectKey, pObject){

        var bundle = this.tapPane.addPane(pObject.label, pObject.chooser_icon);
        this.pane2ObjectId[bundle.id] = pObjectKey;

        if (pObject.chooserClass){

            var chooserClass = window[pObject.chooserClass];
            if (pObject.chooserClass.indexOf('.') !== false){
                var split = pObject.chooserClass.split('.');
                chooserClass = window;
                split.each(function(s){
                    chooserClass = chooserClass[s];
                })
            }

            if (!chooserClass){
                this.win._alert(t("Can't find chooser class '%class%' in object '%object%'.")
                    .replace('%class%', pObject.chooserClass)
                    .replace('%object%', pObjectKey)
                )
            } else {
                this.objectChooserInstance[pObjectKey] = new chooserClass(
                    bundle.pane,
                    this.options.objectOptions[pObjectKey],
                    this.win
                );
            }
        }


        if (this.objectChooserInstance[pObjectKey] && this.objectChooserInstance[pObjectKey].addEvent){
            this.objectChooserInstance[pObjectKey].addEvent('choose', function(){
                this.choose(pObjectKey);
            }.bind(this));

        }

    },

    choose: function(pObjectKey){

        if (!pObjectKey){
            pObjectKey = this.pane2ObjectId[this.tapPane.index];
        }

        if (pObjectKey && this.objectChooserInstance[pObjectKey] && this.objectChooserInstance[pObjectKey].getValue){
            var value = this.objectChooserInstance[pObjectKey].getValue();
            if (!value)
                return;

            logger(value);
            this.saveCookie();
            this.saveCookie();
            this.fireEvent('choose', value);
            this.win.close();
        }
    },

    bullshit: function(){

        return;

        if (this.options.files) {
            this.createFiles();
        }

        this.buttons.each(function (button) {
            button.store('oriClass', button.get('class'));
        });

        var lastTab = Cookie.read(this.cookie + 'lastTab');
        if (['pages', 'files', 'upload'].contains(lastTab)) {
            if (lastTab == 'pages' && this.options.pages) {
                this.toPane('pages')
            }
            if ((lastTab == 'files' || lastTab == 'upload' ) && this.options.files) {
                this.toPane(lastTab)
            }
        } else {
            if (this.options.pages) {
                this.toPane('pages');
            } else if (this.options.files) {
                this.toPane('files');
            }
        }
    },

    createLanguageBox: function () {
        this.languageSelect = new Element('select', {
            style: 'position: absolute; right: 5px; top: 25px; width: 180px; height: 22px'
        }).inject(this.win.border);

        this.languageSelect.addEvent('change', this.changeLanguage.bind(this));

        $H(ka.settings.langs).each(function (lang, id) {
            new Element('option', {
                text: lang.langtitle + ' (' + lang.title + ', ' + id + ')',
                value: id
            }).inject(this.languageSelect);
        }.bind(this));

        //retrieve last selected lang from cookie
        this.changeLanguage();

    },

    changeLanguage: function () {
        this.language = this.languageSelect.value;
        this.loadPages();
    },

    createPages: function () {
        this.buttons['pages'] = this.buttonGroup.addButton(_('Pages'), this.p + 'icons/page.png', this.toPane.bind(this, 'pages'));
        this.panes['pages'] = new Element('div', {
            'class': 'treeContainer',
            style: 'position: absolute; left: 0px; right: 0px; top: 0px; bottom: 0px;'
        }).inject(this.win.content);
    },
    /*
     createUpload: function(){
     this.buttons['upload'] = this.buttonGroup.addButton(_('Upload'), this.p+'admin-files-uploadFile.png', this.toPane.bind(this,'upload'));
     this.panes['upload'] = new Element('div', {
     html: '<h3>'+_('Please choose one or more files')+'</h3>',
     style: 'position: absolute; left: 0px; right: 0px; top: 0px; bottom: 0px;'
     }).inject( this.win.content );

     },
     */

    loadPages: function () {
        var _this = this;
        this.panes['pages'].empty();
        this._domains = new Hash();
        this.domainTrees = new Hash();
        new Request.JSON({url: _path + 'admin/pages/getDomains/', onComplete: function (res) {
            if (!res) return;
            res.each(function (domain) {
                _this._domains.include(domain.rsn, domain);
                _this.domainTrees.include(domain.rsn, new ka.pagesTree(_this.panes['pages'], domain.rsn, {

                    onClick: function (pPage) {
                        _this.domainTrees.each(function (_domain) {
                            _domain.unselect();
                        });
                        if (_this.options.files) {
                            _this.filesPane.unselect();
                        }
                        _this.value = pPage.rsn;
                    },
                    selectPage: _this.value,
                    no_domain_select: true

                }));
            });
        }}).post({language: this.language });

    },

    renderDomain: function (pDomainRsn) {
        var _this = this;
        this.panes['pages'].empty();
        this._domains = new Hash();
        this.domainTrees = new Hash();
        _this.domainTrees.include(pDomainRsn, new ka.pagesTree(_this.panes['pages'], pDomainRsn, {
            onClick: function (pPage) {
                _this.domainTrees.each(function (_domain) {
                    _domain.unselect();
                });
                if (_this.options.files) {
                    _this.filesPane.unselect();
                }
                _this.value = pPage.rsn;
            },
            selectPage: _this.value,
            no_domain_select: true
        }));
    },

    createFiles: function () {

        this.buttons['files'] = this.buttonGroup.addButton(_('Files'), this.p + 'icons/folder.png', this.toPane.bind(this, 'files'));
        this.panes['files'] = new Element('div', {
            style: 'position: absolute; left: 0px; right: 0px; top: 0px; bottom: 0px;'
        }).inject(this.win.content);

        var filesHeader = new Element('div', {
            'class': 'ka-header-light',
            style: 'position: absolute; left: 0px; top: 4px; right: 0px; height: 27px; border-bottom: 1px solid silver;'
        }).inject(this.panes['files']);

        var filesContent = new Element('div', {
            style: 'position: absolute; left: 0px; top: 32px; right: 0px; bottom: 0px; background-color: white;'
        }).inject(this.panes['files']);

        var winApi = {

            addTabGroup: function () {
                return new ka.tabGroup(filesHeader);
            },

            addSmallTabGroup: function () {
                return new ka.smallTabGroup(filesHeader);
            },

            addButtonGroup: function () {
                return new ka.buttonGroup(filesHeader);
            },

            titleGroups: filesHeader,
            getTitle: function () {
            },
            setTitle: function () {
            },

            border: this.win.border,
            addEvent: this.win.addEvent.bind(this.win),
            addHotkey: this.win.addHotkey.bind(this.win),
            isInFront: this.win.isInFront.bind(this.win),
            newDialog: this.win.newDialog.bind(this.win)
        }

        this.filesPane = new ka.files(winApi, filesContent, {

            selection: true,
            selectionOnlyFolders: this.options.onlyDir,
            selectionMultiple: this.options.multi,
            selectionValue: this.value,

            onDblClick: function () {
                this.choose();
            }.bind(this),
            onDeselectAll: function () {
                this.value = false;
            }.bind(this),
            onSelect: function (pFile) {
                if (typeOf(pFile) == 'array') {
                    this.value = [];
                    pFile.each(function (file) {
                        this.value.include(file.path);
                    }.bind(this));
                } else {
                    this.value = pFile.path;
                }
                if (this.options.pages) {
                    this.domainTrees.each(function (domain) {
                        domain.unselect();
                    });
                }
            }.bind(this)
        });

    },

    toPane: function (pPane) {
        this.currentPane = pPane;
        this.buttons.each(function (button) {
            button.set('class', button.retrieve('oriClass'));
        });
        this.panes.each(function (pane) {
            pane.setStyle('display', 'none');
        });

        this.buttons[pPane].set('class', this.buttons[pPane].retrieve('oriClass') + ' buttonHover');
        this.panes[pPane].setStyle('display', 'block');
    }

});
