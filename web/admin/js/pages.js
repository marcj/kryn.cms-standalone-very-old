var admin_pages = new Class({

    versionsSetLiveBtns: [],
    versionsLoadBtns: [],
    trys: 0,

    domainTrees: {},

    rpage: {},
    rdomain: {},

    initialize: function (pWin) {

        Date.defineFormat('version', '%d.%m.%Y, %H:%M:%S');

        this.win = pWin;
        this.win.kwin = this;
        this.win.forceOverlay = true;

        Object.each(ka.settings['langs'], function (lang, key) {
            if (this.language) return;
            this.language = key;
        }.bind(this));

        if (Cookie.read('kryn_pages_language')) {
            this.language = Cookie.read('kryn_pages_language');
        }

        if (this.win.params && this.win.params.lang) {
            this.language = this.win.params.lang;
        }

        this.lastLoadedLayoutCssFiles = [];

        this.aclCanLivePublish = true; //todo read from acl

        this.viewButtons = new Hash();
        this.panes = new Hash();
        this._createLayout();

        this.domainTrees = {};
        this.alreadyOnLoadPageLoaded = false;

        if (this.win.params && this.win.params.id && !this.win.params.domain) {
            this.loadPage(this.win.params.id, true);
        } else if (this.win.params && this.win.params.domain) {
            this.loadTree({selectDomain: this.win.params.id});
            this.loadDomain(this.win.params.id, true);
        } else {
            this.loadTree();
        }
    },

    overlayStart: function () {
        this.overlayEnd();
        this.overlay = new Element('div', {
            styles: {
                left: 0, right: 0, top: 0, bottom: 0,
                position: 'absolute',
                'background-color': 'silver', opacity: 0.1
            }
        }).inject(this.win.content);
    },

    overlayEnd: function () {
        if (this.overlay) this.overlay.destroy();
    },

    /*
     * LOAD PAGE
     */

    loadPage: function (pRsn, pSelect) {

        this.inDomainModus = false;

        this.lastLoadedContentRsn = false;

        this.alreadyOnLoadPageLoaded = true;

        if (this.iframe) this.iframe.destroy();
        this.iframe = null;

        this.layoutBoxes = new Hash();
        this.layoutBoxesInitialized = false;

        if (this.oldLoadPageRequest) {
            this.oldLoadPageRequest.cancel();
        }

        this.id = pRsn;
        this.oldPage = this.page;
        this.win.setLoading(true, null, {left: this.main.getStyle('left')});

        this.oldLoadPageRequest = new Request.JSON({url: _path + 'admin/pages/getPage', noCache: 1, onComplete: function (res) {

            this.saveDomainGrp.hide();
            this.savePageGrp.show();

            this.viewTypeGrpDomain.hide();
            this.viewTypeGrp.show();
            this.deleteDomainGrp.hide();
            this.deletePageGrp.show();

            this.loadedVersion = '-';

            this.page = res;

            if (pSelect && this.page.id) {
                var domain = ka.getDomain(this.page.domain_id);

                if (this.language != domain.lang || Object.getLength(this.domainTrees) == 0) {
                    this.language = domain.lang;
                    this.loadTree({selectPage: pRsn});
                } else {

                    var tree = this.domainTrees[ this.page.domain_id ];
                    if (tree.isReady()) {
                        tree.select(this.page.id);
                    } else {
                        tree.addEvent('ready', function () {
                            tree.select(this.page.id);
                            tree.removeEvents('ready');
                        }.bind(this));
                    }
                }
            }

            this._loadPage();
            this.win.setLoading(false);

            this.rpage = this.retrieveData();

        }.bind(this)}).post({ id: pRsn });
    },

    _loadPage: function () {
        this.savedPage = this.page;

        if ((this.page.type != 0 && this.page.type != 3) || (this.currentViewType == 'content' || this.currentViewType == 'versioning' || (this.currentViewType == 'searchIndex' && this.page.type != 0))) {
            this.viewType('general');
        } else if (!this.currentViewType || this.currentViewType == 'empty' || this.currentViewType == 'domain' || this.currentViewType == 'domainSettings' || this.currentViewType == 'domainTheme') {
            this.viewType('general');
        }


        var myurl = '';
        if (this.page.realUrl) {
            myurl = this.page.realUrl.substring(0, this.page.realUrl.length - this.page.url.length);
        }
        this.generalFieldsUrlPath.set('html', '<b>http://' + ka.getDomain(this.page.domain_id).domain + '/' + myurl + '</b>');

        this.win.setTitle(this.page.title);
        this.generalFields['type'].setValue(this.page.type);
        this.generalFields['title'].setValue(this.page.title);
        this.generalFields['page_title'].setValue(this.page.page_title);
        this.generalFields['url'].setValue(this.page.url);
        //xxx        this.generalFields['template'].setValue( this.page.template );

        var d = ka.getDomain(this.page.domain_id);

        this.win.params = {id: this.page.id, lang: d.lang};

        limitLayouts = false;
        if (typeOf(d.layouts) == 'string') {
            limitLayouts = $A(JSON.decode(d.layouts));
        }

        this.layout.empty();

        this.layout.add('', _(' -- No layout --'));

        Object.each(ka.settings.layouts, function (la, key) {

            Object.each(la, function (layoutFile, layoutTitle) {
                if (limitLayouts && limitLayouts.length > 0 && !limitLayouts.contains(layoutFile)) return;
                this.layout.add(layoutFile, key+' » '+layoutTitle);
            }.bind(this));

        }.bind(this));


        //set page propertie to default
        Object.each(this._pagePropertiesFields, function (fields, extKey) {
            Object.each(fields,function (field) {
                field.setValue();
            })
        });
        this.page.propertiesObj = JSON.decode(this.page.properties);
        if (this.page.propertiesObj) {

            //set page values
            Object.each(this.page.propertiesObj, function (properties, extKey) {

                Object.each(properties, function (property, propertyKey) {

                    if (this._pagePropertiesFields[extKey] && this._pagePropertiesFields[extKey][propertyKey]) {
                        this._pagePropertiesFields[extKey][propertyKey].setValue(property);
                    }

                }.bind(this))


            }.bind(this))

        }

        this.generalFields['layout'].setValue(this.page.layout);
        this.generalFields['target'].setValue(this.page.target);
        this.generalFields['link'].setValue(this.page.link);
        this.generalFields['link'].field.domain = this.page.domain_id;

        this.generalFields['visible'].setValue(this.page.visible);
        this.generalFields['access_denied'].setValue(this.page.access_denied);
        this.generalFields['force_https'].setValue(this.page.force_https);
        this.generalFields['access_from'].setValue(this.page.access_from);
        this.generalFields['access_to'].setValue(this.page.access_to);

        var temp = '';
        if (this.page.access_from_groups) {
            temp = this.page.access_from_groups.split(',');
        }
        this.generalFields['access_from_groups'].setValue(temp);
        this.generalFields['access_nohidenavi'].setValue(this.page.access_nohidenavi);
        this.generalFields['access_need_via'].setValue(this.page.access_need_via);

        this.generalFields['access_redirectto'].setValue(this.page.access_redirectto);
        this.generalFields['unsearchable'].setValue(this.page.unsearchable);

        search_words = (this.page.search_words) ? this.page.search_words : '';
        this.generalFields['search_words'].setValue(search_words);


        this.generalFields['resourcesCss'].setValue(this.page.resourcesCss);
        this.generalFields['resourcesJs'].setValue(this.page.resourcesJs);

        var metas = JSON.decode(this.page.meta);
        this.clearMeta();

        this._metas = [];
        var keywords = '';
        var description = '';
        if (metas) {
            var nmetas = new Hash();
            metas.each(function (pMeta) {
                if (pMeta) {
                    nmetas.include(pMeta.name, pMeta.value);
                }
            });
            metas = nmetas;
            metas.each(function (value, key) {
                if (key != 'keywords' && key != 'description') {
                    this.addMeta({key: key, value: value});
                }
            }.bind(this));
            keywords = (metas.keywords) ? metas.keywords : '';
            description = (metas.description) ? metas.description : '';
        }
        this.generalFields['metaKeywords'].setValue(keywords);
        this.generalFields['metaDesc'].setValue(description);


        this.versions.empty();
        this.loadVersions();

        this.urlAliase.empty();

        if (this.page.alias) {

            this.renderAlias();

        }

        if (this.page.type == 0) {
            this.loadSearchIndexOverview();
        }

        this.changeType();

    },

    renderAlias: function () {
        this.urlAliase.empty();
        if (typeOf(this.page.alias) != 'array') return;
        if (this.page.alias.length == 0) return;

        new Element('div', {
            text: _('There are URL aliases for this page'),
            style: 'margin: 3px 0px; color: gray;'
        }).inject(this.urlAliase);

        var table = new Element('table', {
            cellpadding: 4,
            cellspacing: 0,
            style: 'width: 300px; border-top: 1px solid #ddd;'
        }).inject(this.urlAliase);
        var tbody = new Element('tbody').inject(table);

        this.page.alias.each(function (item) {
            var tr = new Element('tr').inject(tbody);

            new Element('td', {
                html: '&raquo; ' + item.url,
                style: 'border-bottom: 1px solid #ddd;'
            }).inject(tr);

            var td = new Element('td', {
                width: 20,
                align: 'center',
                valign: 'top',
                style: 'border-bottom: 1px solid #ddd;'
            }).inject(tr);

            new Element('img', {
                src: _path + 'admin/images/icons/delete.png',
                style: 'cursor: pointer;',
                title: _('Delete this alias')
            }).addEvent('click', function () {
                this.deleteAlias(item.id);
            }.bind(this)).inject(td);
        }.bind(this));
    },

    deleteAlias: function (pRsn) {

        this.win._confirm(_('Really delete this alias?'), function (p) {
            if (!p) return;

            new Request.JSON({url: _path + 'admin/pages/deleteAlias', noCache: 1, onComplete: function () {
                this.loadAliases();
            }.bind(this)}).post({id: pRsn});

        }.bind(this));


    },

    loadAliases: function () {

        new Request.JSON({url: _path + 'admin/pages/getAliases', noCache: 1, onComplete: function (pAliases) {

            this.page.alias = pAliases;
            this.renderAlias();

        }.bind(this)}).post({page_id: this.page.id});

    },

    loadVersions: function () {

        if (this.oldLoadVersionsRequest) {
            this.oldLoadVersionsRequest.cancel();
        }

        //this._versionsLive = {};
        this.oldLoadVersionsRequest = new Request.JSON({url: _path + 'admin/pages/getVersions', noCache: 1, onComplete: function (res) {

            this.versionNoVersions.empty();

            if (!res || res.length == 0) {
                this.versions.hide();
                this.versionNoVersions.set('text', _('No versions'));
            } else {
                this.versions.show();
                this.versions.empty();

                res.each(function (version) {
                    var text = (new Date(version.modified * 1000)).format('version');
                    text = '#' + version.id + ' by ' + version.username + ' (' + text + ')';

                    if (version.active == 1) {
                        text = '[LIVE] ' + text;
                    }

                    this.versions.add(version.id, text);

                    if (version.active == 1 && this.loadedVersion == '-') {
                        this.versions.setValue(version.id);
                    } else if (version.id == this.loadedVersion) {
                        this.versions.setValue(this.loadedVersion);
                    }

                    //this._versionsLive[ version.id ] = version.active;
                }.bind(this));

                /*if( !pSetToNewestVersion ){
                 this.versions.value = this.page._activeVersion;
                 }*/
            }

            /*
             if( this.page.versions ){
             this.page.versions.each(function(version){
             new Element('option', {
             value: version.version_id,
             text: (new Date(version.mdate*1000)).format('version')
             }).inject( this.versions );
             }.bind(this));
             }
             */

        }.bind(this)}).post({id: this.page.id });
    },

    /*
     * CREATE LAYOUT 
     */


    hideBarHover: function () {
        this.hideBarHoverOutActive = false;
        this.treeContainer.set('tween', {onComplete: function () {
        }.bind(this)});
        /*this.treeContainer.setStyles({
         'display': 'none',
         'opacity': 0
         });*/
        this.tree.setStyle('height', '100%');
        this.treeContainer.tween('opacity', 0.95);
        this.treeSizer.tween('opacity', 1);
    },

    hideBarHoverOut: function () {
        this.hideBarHoverOutActive = true;
        (function () {
            if (this.hideBarHoverOutActive) {
                this.treeContainer.set('tween', {onComplete: function () {
                    this.tree.setStyle('height', '25px');
                }.bind(this)});
                this.treeContainer.tween('opacity', 0);
                this.treeSizer.tween('opacity', 0);
            }
        }.bind(this)).delay(200);
    },

    toggleHiderMode: function () {
        if (this.inHideMode != true) {
            this.inHideMode = true;
            this.oldMainPosition = this.main.getStyle('left').toInt();
            this.main.setStyle('left', 0);
            this.treeBar.setStyle('opacity', 0.7);
            this.treeContainer.setStyle('opacity', 0);
            this.tree.setStyle('height', '25px');
            this.treeSizer.setStyle('opacity', 0);
            this.treeHider.set('src', _path + 'admin/images/pages-tree-bar-arrow-down.jpg');
            this.treeBar.addEvent('mouseover', this.hideBarHover.bind(this));
            this.treeBar.addEvent('mouseout', this.hideBarHoverOut.bind(this));
            this.treeSizer.addEvent('mouseover', this.hideBarHover.bind(this));
            this.treeSizer.addEvent('mouseout', this.hideBarHoverOut.bind(this));
            this.treeContainer.addEvent('mouseover', this.hideBarHover.bind(this));
            this.treeContainer.addEvent('mouseout', this.hideBarHoverOut.bind(this));

            //to win.content
            this.elementPropertyToolbar.inject(this.tree, 'before');
            this.treeContainer.setStyle('bottom', 3);
        } else {
            this.inHideMode = false;

            this.treeSizer.removeEvents('mouseover');
            this.treeSizer.removeEvents('mouseout');
            this.treeBar.removeEvents('mouseover');
            this.treeBar.removeEvents('mouseout');
            this.treeContainer.removeEvents('mouseout');
            this.treeContainer.removeEvents('mouseover');

            this.treeBar.setStyle('opacity', 1);
            this.main.setStyle('left', this.oldMainPosition);
            this.treeContainer.setStyle('opacity', 1);
            this.tree.setStyle('height', '100%');
            this.treeSizer.setStyle('opacity', 1);
            this.treeHider.set('src', _path + 'admin/images/pages-tree-bar-arrow.jpg');

            if (this.elementPropertyToolbar.getSize().y > 1) {
                this.treeContainer.tween('bottom', 221);
            }

            this.elementPropertyToolbar.inject(this.tree);
        }
    },

    _createLayout: function () {
        var p = _path + 'admin/images/';

        var btnGrp = this.win.addButtonGroup();
        btnGrp.addButton(_('New domain'), _path + 'admin/images/icons/world_add.png', this.addDomain.bind(this));


        this.main = new Element('div', {
            styles: {
                position: 'absolute',
                left: 211, right: 0, 'top': 0, bottom: 0,
                'background-color': '#eee',
                'border-left': '1px solid silver',
                overflow: 'auto'
            }
        }).inject(this.win.content);


        this.tree = new Element('div', {
            'class': 'ka-pages-tree',
            styles: {
                position: 'absolute',
                left: 0, 'top': 0, 'overflow': 'visible', width: 200, height: '100%'
            }
        }).inject(this.win.content);

        this.treeContainer = new Element('div', {
            'class': 'treeContainer ka-pages-treeContainer',
            styles: {
                position: 'absolute',
                'background-color': '#f3f3f3',
                left: 0, right: 0, 'top': 16, bottom: 3,
                overflow: 'auto'
            }
        }).inject(this.tree);
        this.treeContainer.set('tween', {duration: 300});

        /*this.treeContainerTable = new Element('table', {
         style: 'width: 100%',
         cellpadding: 0,
         cellspacing: 0
         }).inject(this.treeContainer);

         this.treeContainerTbody = new Element('tbody').inject(this.treeContainerTable);
         this.treeContainerTr = new Element('tr').inject(this.treeContainerTbody);
         this.treeContainerTd = new Element('td').inject(this.treeContainerTr);*/

        this.treeSizer = new Element('div', {
            style: 'position: absolute; right: -7px; top: 0px; width: 5px; height: 100%; cursor: e-resize; border-left: 1px solid silver; border-right: 1px solid silver;'
        }).inject(this.tree);


        /*this.pluginChooserPane = new Element('div', {
         'class': 'ka-pages-pluginchooserpane',
         style: 'position: absolute; left: 0px; bottom: 0px; height: 0px; width: 0px; background-color: #eee; overflow: auto;'
         }).inject( this.tree );
         this.pluginChooserPane.set('tween', {transition: Fx.Transitions.Cubic.easeOut});*/


        //        this.treeContainer.setStyle('bottom', 0);


        /*
         * searchbar and hider
         */

        this.treeBar = new Element('div', {
            styles: {
                position: 'absolute',
                left: 0, right: 0, 'top': 0, height: 16,
                cursor: 'pointer',
                'background-image': 'url(' + _path + 'admin/images/pages-tree-bar-bg.jpg)'
            }
        }).addEvent('click', this.toggleHiderMode.bind(this)).inject(this.tree, 'top');

        this.treeHider = new Element('img', {
            style: 'margin-left: 5px;',
            src: _path + 'admin/images/pages-tree-bar-arrow.jpg'
        }).inject(this.treeBar);


        var left = 220;//todo maybe set into a cookie
        if (left > 0) {
            this.tree.setStyle('width', left);
            this.main.setStyle('left', left + 6);
            this.win.titleGroups.setStyle('padding-left', left - 54);
        }

        var _this = this;
        this.tree.makeResizable({
            grid: 1,
            snap: 0,
            handle: this.treeSizer,
            onDrag: function (el, ev) {
                el.setStyle('height', '100%');
                //_this.main.setStyle('left', _this.treeSizer.getPosition(_this.tree).x+9);
                var left = _this.tree.getSize().x;
                if (_this.inHideMode) {
                    _this.oldMainPosition = left + 6;
                } else {
                    _this.main.setStyle('left', left + 6);
                }
                if (left - 56 >= 160) {
                    _this.win.titleGroups.setStyle('padding-left', left - 54);
                } else {
                    _this.win.titleGroups.setStyle('padding-left', 160);
                }
            },
            onStart: function () {
                _this.overlayStart();
            },
            onComplete: function (el) {
                _this.overlayEnd();
                _this.refreshPageTrees();
            },
            onCancel: function () {
                _this.overlayEnd();
            }
        });


        //this.viewTypeGrpDomain = this.win.addButtonGroup();
        this.viewTypeGrpDomain = this.win.addSmallTabGroup();
        /*
        this.viewButtons['domain'] = this.viewTypeGrpDomain.addButton(_('Domain'), p + 'icons/world.png', this.viewType.bind(this, 'domain'));
        this.viewButtons['domainSessions'] = this.viewTypeGrpDomain.addButton(_('Sessions'), p + 'icons/group.png', this.viewType.bind(this, 'domainSessions'));
        this.viewButtons['domainTheme'] = this.viewTypeGrpDomain.addButton(_('Theme'), p + 'icons/layout.png', this.viewType.bind(this, 'domainTheme'));
        this.viewButtons['domainProperties'] = this.viewTypeGrpDomain.addButton(_('Properties'), p + 'icons/layout.png', this.viewType.bind(this, 'domainProperties'));
        this.viewButtons['domainSettings'] = this.viewTypeGrpDomain.addButton(_('Settings'), p + 'admin-pages-viewType-general.png', this.viewType.bind(this, 'domainSettings'));
        */
        this.viewButtons['domain'] = this.viewTypeGrpDomain.addButton(_('Domain'), this.viewType.bind(this, 'domain'));
        this.viewButtons['domainSessions'] = this.viewTypeGrpDomain.addButton(_('Sessions'), this.viewType.bind(this, 'domainSessions'));
        this.viewButtons['domainTheme'] = this.viewTypeGrpDomain.addButton(_('Theme'), this.viewType.bind(this, 'domainTheme'));
        this.viewButtons['domainProperties'] = this.viewTypeGrpDomain.addButton(_('Properties'), this.viewType.bind(this, 'domainProperties'));
        this.viewButtons['domainSettings'] = this.viewTypeGrpDomain.addButton(_('Settings'), this.viewType.bind(this, 'domainSettings'));


        this.viewButtons['domain'].setPressed(true);
        this.viewTypeGrpDomain.hide();


        /*pages edit */
        //var viewTypeGrp = this.win.addButtonGroup();
        var viewTypeGrp = this.win.addSmallTabGroup();
        this.viewTypeGrp = viewTypeGrp;
        this.viewTypeGrp.hide();
/*
        this.viewButtons['general'] = viewTypeGrp.addButton(_('General'), p + 'admin-pages-viewType-general.png', this.viewType.bind(this, 'general'));
        this.viewButtons['rights'] = viewTypeGrp.addButton(_('Access'), p + 'admin-pages-viewType-rights.png', this.viewType.bind(this, 'rights'));

        this.viewButtons['contents'] = viewTypeGrp.addButton(_('Contents'), p + 'admin-pages-viewType-content.png', this.viewType.bind(this, 'contents'));

        this.viewButtons['resources'] = viewTypeGrp.addButton(_('Resources'), p + 'admin-pages-viewType-resources.png', this.viewType.bind(this, 'resources'));
        this.viewButtons['properties'] = viewTypeGrp.addButton(_('Properties'), p + 'icons/plugin_disabled.png', this.viewType.bind(this, 'properties'));

        this.viewButtons['searchIndex'] = viewTypeGrp.addButton(_('Search'), p + 'admin-pages-viewType-search.png', this.viewType.bind(this, 'searchIndex'));

        this.viewButtons['versioning'] = viewTypeGrp.addButton(_('Versions'), p + 'admin-pages-viewType-versioning.png', this.viewType.bind(this, 'versioning'));
*/
        this.viewButtons['general'] = viewTypeGrp.addButton(_('General'), this.viewType.bind(this, 'general'));
        this.viewButtons['rights'] = viewTypeGrp.addButton(_('Access'), this.viewType.bind(this, 'rights'));

        this.viewButtons['contents'] = viewTypeGrp.addButton(_('Contents'), this.viewType.bind(this, 'contents'));

        this.viewButtons['resources'] = viewTypeGrp.addButton(_('Resources'), this.viewType.bind(this, 'resources'));
        this.viewButtons['properties'] = viewTypeGrp.addButton(_('Properties'), this.viewType.bind(this, 'properties'));

        this.viewButtons['searchIndex'] = viewTypeGrp.addButton(_('Search'), this.viewType.bind(this, 'searchIndex'));

        this.viewButtons['versioning'] = viewTypeGrp.addButton(_('Versions'), this.viewType.bind(this, 'versioning'));


        // save group for page
        var saveGrp = this.win.addButtonGroup();
        this.savePageGrp = saveGrp;
        this.saveButton = saveGrp.addButton(_('Save') + ' (SHIFT+ALT+S)', p + 'button-save.png', this.save.bind(this));
        //this.saveButtonAndClose = saveGrp.addButton( _('Save and close'), p+'button-save-and-close.png', this.saveAndClose.bind(this));

        if (this.aclCanLivePublish) {
            this.saveButtonPublish = saveGrp.addButton(_('Save and publish'), p + 'button-save-and-publish.png', this.saveAndClose.bind(this, true));
        }

        //        this.prevBtn = saveGrp.addButton( 'Vorschau', p+'icons/layout_header.png', this.saveAs.bind(this));
        this.liveEditBtn = saveGrp.addButton('Zur Seite (SHIFT+ALT+V)', p + 'icons/eye.png', function () {
            this.toPage();
        }.bind(this));


        this.savePageGrp.hide();

        this.win.addHotkey('s', true, true, this.save.bind(this));
        this.win.addHotkey('v', true, true, this.toPage.bind(this));

        this.deletePageGrp = this.win.addButtonGroup();
        this.deletePageBtn = this.deletePageGrp.addButton(_('Delete'), p + 'remove.png', this.deletePage.bind(this));
        this.searchIndexButton = this.deletePageGrp.addButton(_('Crawl this page now'), p + 'button-index-page.png', function () {
            this.createSearchIndexForPage();
        }.bind(this));

        this.deletePageGrp.hide();

        //save group for domain
        var saveGrp = this.win.addButtonGroup();
        this.saveDomainBtn = saveGrp.addButton(_('Save'), p + 'button-save.png', this.saveDomain.bind(this));
        this.saveDomainGrp = saveGrp;
        this.saveDomainGrp.hide();

        this.deleteDomainGrp = this.win.addButtonGroup();
        this.deleteDomainGrp.addButton(_('Delete'), p + 'remove.png', this.deleteDomain.bind(this));
        this.deleteDomainGrp.hide();

        //right place
        /*
         this.workspaceSelect = new Element('select', {
         style: 'position: absolute; right: 28px; top: 25px; width: 180px; height: 22px'
         }).inject( this.win.border );

         ka.settings.workspaces.each(function(work){
         new Element('option', {
         text: work.name,
         value: work.id
         }).inject( this.workspaceSelect );
         }.bind(this));

         this.workspaceAddBtn = new Element('img', {
         src: p+'icons/add.png',
         style: 'position: absolute; right: 7px; top: 28px;'
         }).inject( this.win.border );
         */

        this.languageSelect = new ka.Select().inject(this.win.titleGroups);

        document.id(this.languageSelect).setStyles({
            'position': 'absolute',
            'left': 5,
            'top': 0,
            'width': 140
        });

        this.languageSelect.addEvent('change', this.changeLanguage.bind(this));

        Object.each(ka.settings.langs, function (lang, id) {
            this.languageSelect.add(id, lang.langtitle + ' (' + lang.title + ', ' + id + ')');
        }.bind(this));

        this.languageSelect.setValue(this.language);

        this._createDomain();

        this._createDomainProperties();

        this._createGeneral();
        this._createRights();
        this._createContents();
        this._createResources();
        this._createVersioning();
        this._createSearchIndexPane();
        this._createProperties();

    },

    refreshPageTrees: function () {

        Object.each(this.domainTrees, function (domainTree) {
            domainTree.setRootPosition();
        });

    },

    toPage: function (pPage) {
        if (!pPage || !pPage.id) pPage = this.page;

        if (this.lastPreviewWin) {
            this.lastPreviewWin.close();
        }

        var url = this.getBaseUrl(pPage);

        var url = url + pPage.realUrl + '/';

        if (this.loadedVersion != '-' && this.loadedVersion && this.page && pPage.id == this.page.id) {

            url = url + '/kVersionId:' + this.loadedVersion;
        }
        this.lastPreviewWin = window.open(url, "_blank");
    },


    createSearchIndexForPage: function (pPage) {
        if (!pPage || !(pPage.id > 0)) {
            pPage = this.page;
        }


        //try getting search index key for force
        pageDomainRsn = pPage.domain_id;
        dISKey = false;
        try {
            dISKey = ka.getDomain(pageDomainRsn).search_index_key;
        } catch (e) {
        }


        //var indexUrl = this.getBaseUrl( pPage );
        var indexUrl = _path;

        if (ka.getDomain(pageDomainRsn).master != 1) {
            indexUrl += ka.getDomain(pageDomainRsn).lang + '/';
        }


        indexUrl += pPage.realUrl;
        iReq = {
            jsonOut: true,
            enableSearchIndexMode: true,
            forceSearchIndex: dISKey,
            kryn_domain: ka.getDomain(pageDomainRsn).domain
        };


        this.searchIndexButton.startTip(_('Indexing ...'));
        this.overlayStart();

        new Request.JSON({url: indexUrl, noCache: 1,
            onFailure: function () {
                this.overlay.destroy();
                this.searchIndexButton.stopTip();
            }.bind(this),
            onComplete: function (res) {
                this.overlay.destroy();
                if (!res) {
                    res = {msg: _('Failed')};
                }
                this.searchIndexButton.stopTip(res.msg);
                this.loadSearchIndexOverview();
            }.bind(this)
        }).post(iReq);

    },


    toggleSearchIndexButton: function (pType) {
        if (pType == 0 && this.page.unsearchable != 1) {
            this.searchIndexButton.show();
        } else {
            this.searchIndexButton.hide();
        }
    },


    getBaseUrl: function (pPage) {
        if (!pPage || !(pPage.id > 0)) pPage = this.page;
        var d = ka.getDomain(pPage.domain_id);

        var prefix = ( typeof(d.path) == 'undefined' || d.path == '' || d.path == null ) ? '/' : d.path;

        if (prefix.substr(prefix.length - 1, 1) != '/') {
            prefix = prefix + '/';
        }

        if (window.location.port != 80) {
            prefix = ":" + window.location.port + prefix;
        }

        if (d.master != 1) {
            prefix = prefix + d.lang + '/';
        }

        var url = 'http://' + d.domain + prefix;
        return url;
    },

    changeLanguage: function () {
        this.language = this.languageSelect.getValue();

        Cookie.write('kryn_pages_language', this.language);

        this.treeContainer.empty();
        this.loadTree();
        this.viewType('empty');
        this.savePageGrp.hide();
        this.viewTypeGrp.hide();
        this.saveDomainGrp.hide();
        this.viewTypeGrpDomain.hide();
        this.deleteDomainGrp.hide();
        this.deletePageGrp.hide();
    },

    deleteDomain: function (pDomain) {

        if (!pDomain) {
            pDomain = this.currentDomain;
        }

        if (!pDomain || pDomain.id < 0) {
            return;
        }

        this.win._confirm(_('Really delete this domain?'), function (p) {
            if (!p) return;

            new Request.JSON({url: _path + 'admin/pages/domain/delete', async: false, noCache: 1, onComplete: function () {
                ka.loadSettings();
                this.changeLanguage();
            }.bind(this)}).post({ id: pDomain.id });
        }.bind(this));
    },

    deletePage: function (pPage) {
        if (!pPage || !(pPage.id > 0)) pPage = this.page;
        var _this = this;
        this.win._confirm(_('Really remove?'), function (res) {
            if (!res) return;
            new Request.JSON({url: _path + 'admin/pages/deletePage', async: false, noCache: 1, onComplete: function () {
                _this.domainTrees[ pPage.domain_id ].reload();
            }}).post({ id: pPage.id });
        });
    },

    changeType: function () {

        if (this.inDomainModus) {
            this.saveDomainGrp.show();
            this.savePageGrp.hide();

            this.viewTypeGrpDomain.show();
            this.viewTypeGrp.hide();

            if (!ka.checkDomainAccess(this.currentDomain.id, 'deleteDomain')) {
                this.deleteDomainGrp.hide();
            } else {
                this.deleteDomainGrp.show();
            }

            this.deletePageGrp.hide();

        } else {
            this.saveDomainGrp.hide();
            this.savePageGrp.show();

            this.viewTypeGrpDomain.hide();
            this.viewTypeGrp.show();

            this.deleteDomainGrp.hide();
            this.deletePageGrp.show();
        }


        var type = this.generalFields['type'].getValue();

        this.generalFields.each(function (field) {
            field.show();
            field.fireEvent('check-depends');
        })

        this.viewButtons.each(function (field) {
            field.show();
        })

        if (this.inDomainModus) {
            return;
        }


        this.generalFields['target'].hide();
        this.generalFields['visible'].hide();
        this.aliase.setStyle('display', 'none');
        this.metas.setStyle('display', 'none');

        //this.saveButtonAndClose.hide();
        this.saveButtonPublish.hide();
        this.toggleSearchIndexButton(type);

        this.viewButtons['searchIndex'].hide();

        this.generalFields['link'].hide();

        if (type == 1 || type == 2) {//link oder ordner
            this.viewButtons['contents'].hide();
            this.viewButtons['versioning'].hide();
            this.viewButtons['resources'].hide();
            this.generalFields['page_title'].hide();
            //xxx       this.generalFields['template'].hide();
            //this.prevBtn.hide();
        } else { //page and ablage
            this.viewButtons['contents'].show();
            this.viewButtons['versioning'].show();
            this.viewButtons['properties'].show();
            this.generalFields['page_title'].show();
            //xxx       this.generalFields['template'].show();
            //this.prevBtn.show();
        }

        this.layout.hide();
        if (type == 3) { //ablage
            //xxx            this.generalFields['template'].hide();
            this.generalFields['url'].hide();
            this.ablageModus = true;
            //this.prevBtn.hide();
            this.liveEditBtn.hide();
        }

        if (type == 2) { //folder
            this.generalFields['url'].hide();
        }

        if (type == 1) { //link
            this.generalFields['url'].field.empty = true;
            this.generalFields['target'].show();
            this.generalFields['link'].show();
            this.generalFields['visible'].show();
        }

        if (type == 0) {
            this.generalFields['url'].field.check = 'kurl';
            this.aliase.setStyle('display', 'block');
            this.metas.setStyle('display', 'block');
            this.ablageModus = false;
            //this.prevBtn.show();         

            this.liveEditBtn.show();
            this.viewButtons['searchIndex'].show('inline');

            this.layout.hide();
            if (ka.checkPageAccess(this.page.id, 'canChangeLayout')) {
                this.layout.show();
            }
        }

        if (type == 0 || type == 3) { //page or ablage
            this.viewButtons['resources'].show();
            this.generalFields['visible'].show();

            if (this.currentViewType == 'contents') {
                this._loadContent();
            }
            //            this.saveButtonAndClose.show();

            if (ka.checkPageAccess(this.page.id, 'canPublish')) {
                this.saveButtonPublish.show();
            }
        }


        //permission
        if (!ka.checkPageAccess(this.page.id, 'general')) {
            this.viewButtons['general'].hide();
            if (this.currentViewType == 'general') {
                this.toAlternativPane();
            }
        }

        if (!ka.checkPageAccess(this.page.id, 'access')) {
            this.viewButtons['rights'].hide();
            if (this.currentViewType == 'rights') {
                this.toAlternativPane();
            }
        }

        if (!ka.checkPageAccess(this.page.id, 'contents')) {
            this.viewButtons['contents'].hide();
            if (this.currentViewType == 'access') {
                this.toAlternativPane();
            }
        }

        if (!ka.checkPageAccess(this.page.id, 'resources')) {
            this.viewButtons['resources'].hide();
            if (this.currentViewType == 'resources') {
                this.toAlternativPane();
            }
        }

        if (!ka.checkPageAccess(this.page.id, 'properties')) {
            this.viewButtons['properties'].hide();
            if (this.currentViewType == 'properties') {
                this.toAlternativPane();
            }
        }

        if (!ka.checkPageAccess(this.page.id, 'search')) {
            this.viewButtons['searchIndex'].hide();
            if (this.currentViewType == 'searchIndex') {
                this.toAlternativPane();
            }
        }

        if (!ka.checkPageAccess(this.page.id, 'versions')) {
            this.viewButtons['versioning'].hide();
            if (this.currentViewType == 'versioning') {
                this.toAlternativPane();
            }
        }

        this.deletePageBtn.show();
        if (!ka.checkPageAccess(this.page.id, 'deletePages')) {
            this.deletePageBtn.hide();
        }

        this.deletePageBtn.show();
        if (!ka.checkPageAccess(this.page.id, 'deletePages')) {
            this.deletePageBtn.hide();
        }


        ['type', 'title', 'page_title', 'url', 'visible', 'access_denied', 'force_https'].each(function (acl) {
            if (!ka.checkPageAccess(this.page.id, acl)) {
                this.generalFields[acl].hide();
            }
        }.bind(this));


        if (!ka.checkPageAccess(this.page.id, 'releaseDates')) {
            this.generalFields['access_from'].hide();
            this.generalFields['access_to'].hide();
        }

        if (!ka.checkPageAccess(this.page.id, 'limitation')) {
            this.generalFields['access_from_groups'].hide();
            this.generalFields['access_nohidenavi'].hide();
            this.generalFields['access_redirectto'].hide();
            this.generalFields['access_need_via'].hide();
        }

        if (!ka.checkPageAccess(this.page.id, 'meta')) {
            this.metas.setStyle('display', 'none');
        }

        //search
        /*this.setToBlListBtn.setStyle('display', 'block');
         if(!ka.checkPageAccess( this.page.id, 'setBlacklist' ) )
         this.setToBlListBtn.setStyle('display', 'none');
         */

        this.generalFields['unsearchable'].show();
        if (!ka.checkPageAccess(this.page.id, 'exludeSearch')) {
            this.generalFields['unsearchable'].hide();
        }

        this.generalFields['search_words'].show();
        if (!ka.checkPageAccess(this.page.id, 'searchKeys')) {
            this.generalFields['search_words'].hide();
        }

        //resources
        this.generalFields['resourcesCss'].show();
        if (!ka.checkPageAccess(this.page.id, 'css')) {
            this.generalFields['resourcesCss'].hide();
        }

        this.generalFields['resourcesJs'].show();
        if (!ka.checkPageAccess(this.page.id, 'js')) {
            this.generalFields['resourcesJs'].hide();
        }


        //versions
        this.versions.show();
        this.versionsLoadBtns.each(function (btn) {
            btn.show();
        });
        if (!ka.checkPageAccess(this.page.id, 'loadVersion')) {
            this.versions.hide();
            this.versionsLoadBtns.each(function (btn) {
                btn.hide();
            });
        }


        this.versionsSetLiveBtns.each(function (btn) {
            btn.show();
        });
        if (!ka.checkPageAccess(this.page.id, 'setLive')) {
            this.versionsSetLiveBtns.each(function (btn) {
                btn.hide();
            });
        }

        //contents
        /*var options = [];

         var _langs = $H({
         text: _('Text'),
         layoutelement: _('Layout Element'),
         picture: _('Picture'),
         plugin: _('Plugin'),
         pointer: _('Pointer'),
         template: _('Template'),
         navigation: _('Navigation'),
         html: _('HTML'),
         php: _('PHP')
         });

         _langs.each(function(label,type){
         if( !ka.checkPageAccess( this.page.id, 'content-'+type ) )
         return;
         options.include({i: type, label: label})
         }.bind(this));

         var newF = new ka.Field({
         label: _('Type'),
         type: 'select',
         help: 'admin/element-type',
         small: 1,
         tableItems: options,
         table_key: 'i',
         table_label: 'label'
         }).inject( this.elementPropertyFields.eTypeSelect.main, 'after' );

         var old = this.elementPropertyFields.eTypeSelect.destroy();
         this.elementPropertyFields.eTypeSelect = newF;
         */
    },

    toAlternativPane: function () {
        var found = false;
        if (this.inDomainModus) {
            ['domain', 'domainTheme', 'domainProperties', 'domainSettings'].each(function (item) {
                if (!this.viewButtons[item].isHidden()) {
                    found = item;
                }
            }.bind(this))
        } else {
            ['general', 'rights', 'contents', 'resources', 'properties', 'searchIndex', 'versioning'].each(function (item) {
                if (!this.viewButtons[item].isHidden()) {
                    found = item;
                }
            }.bind(this))
        }

        if (!found) {
            this.viewButtons.each(function (button, key) {
                this.panes[key].setStyle('display', 'none');
            }.bind(this));

            this.saveDomainGrp.hide();
            this.savePageGrp.hide();

            this.viewTypeGrpDomain.hide();
            this.viewTypeGrp.hide();
        } else {
            this.viewType(found, true);
        }
    },

    loadDomain: function (pDomain) {

        if (typeOf(pDomain) == 'object') {
            pDomain = pDomain.id;
        }


        this.inDomainModus = true;

        if (this.oldLoadDomainRequest) this.oldLoadDomainRequest.cancel();


        this.win.setLoading(true, null, {left: this.main.getStyle('left')});

        this.oldLoadDomainRequest = new Request.JSON({url: _path + 'admin/pages/domain/get', noCache: 1, onComplete: function (pResult) {

            var res = pResult.domain;

            this.domainFields.each(function (item, key) {
                item.show();
                item.fireEvent('check-depends');
            });

            this.viewButtons.each(function (field) {
                field.show();
            })

            this.win.setTitle(res.domain);

            this.deleteDomainGrp.show();

            if (!ka.checkDomainAccess(res.id, 'deleteDomain')) {
                this.deleteDomainGrp.hide();
            }

            if (!ka.checkDomainAccess(res.id, 'domain')) {
                if (!ka.checkDomainAccess(res.id, 'theme')) {
                    this.viewButtons['domainTheme'].hide();
                }
            }

            if (!ka.checkDomainAccess(res.id, 'domainProperties')) {
                this.viewButtons['domainProperties'].hide();
            }

            if (!ka.checkDomainAccess(res.id, 'settings')) {
                this.viewButtons['domainSettings'].hide();
            }


            if (!ka.checkDomainAccess(res.id, 'domainName')) {
                this.domainFields['domain'].hide();
            }
            if (!ka.checkDomainAccess(res.id, 'domainTitle')) {
                this.domainFields['title_format'].hide();
            }
            if (!ka.checkDomainAccess(res.id, 'domainStartpage')) {
                this.domainFields['startpage_id'].hide();
            }
            if (!ka.checkDomainAccess(res.id, 'domainPath')) {
                this.domainFields['path'].hide();
            }
            if (!ka.checkDomainAccess(res.id, 'domainFavicon')) {
                this.domainFields['favicon'].hide();
            }
            if (!ka.checkDomainAccess(res.id, 'domainLanguage')) {
                this.domainFields['lang'].hide();
            }
            if (!ka.checkDomainAccess(res.id, 'domainLanguageMaster')) {
                this.domainFields['master'].hide();
            }
            if (!ka.checkDomainAccess(res.id, 'domainEmail')) {
                this.domainFields['email'].hide();
            }
            if (!ka.checkDomainAccess(res.id, 'limitLayouts')) {
                this.domainFields['layouts'].hide();
            }


            if (!ka.checkDomainAccess(res.id, 'aliasRedirect')) {
                this.domainFields['alias'].hide();
                this.domainFields['redirect'].hide();
            }

            if (!ka.checkDomainAccess(res.id, 'phpLocale')) {
                this.domainFields['phplocale'].hide();
            }
            if (!ka.checkDomainAccess(res.id, 'robotRules')) {
                this.domainFields['robots'].hide();
            }

            if (!ka.checkDomainAccess(res.id, '404')) {
                this.domainFields['page404_id'].hide();
                this.domainFields['page404interface'].hide();
            }


            if (!ka.checkDomainAccess(res.id, 'domainOther')) {
                this.domainFields['resourcecompression'].hide();
            }


            this.currentDomain = res;

            this.viewType('domain');

            this.changeType();

            this.win.params = {id: res.id, lang: res.lang, domain: true};

            this.currentDomain.session = JSON.decode(this.currentDomain.session);
            this.domainSessionFields.setValue(this.currentDomain.session);


            if (this.currentDomain.session && this.currentDomain.session.auth_class) {
                if (this.auth_params_objects[this.currentDomain.session.auth_class]) {
                    this.auth_params_objects[this.currentDomain.session.auth_class].setValue(this.currentDomain.session.auth_params);
                }
            }

            this.showDomainMaster(res.id);

            //set domain propertie to default
            Object.each(this._domainPropertiesFields, function (fields, extKey) {
                Object.each(fields, function (field) {
                    field.setValue();
                })
            });
            this.currentDomain.extproperties = JSON.decode(this.currentDomain.extproperties);
            if (this.currentDomain.extproperties) {
                //set page values
               Object.each(this.currentDomain.extproperties, function (properties, extKey) {
                    Object.each(properties, function (property, propertyKey) {
                        if (this._domainPropertiesFields[extKey] && this._domainPropertiesFields[extKey][propertyKey]) {
                            this._domainPropertiesFields[extKey][propertyKey].setValue(property);
                        }
                    }.bind(this))
                }.bind(this))
            }

            this.domainFields['domain'].setValue(res.domain);
            this.domainFields['title_format'].setValue(res.title_format);
            this.domainFields['startpage_id'].setValue(res.startpage_id);
            this.domainFields['startpage_id'].field.domain = res.id;
            this.domainFields['lang'].setValue(res.lang);
            this.domainFields['master'].setValue(res.master);
            this.domainFields['phplocale'].setValue(res.phplocale);
            this.domainFields['robots'].setValue(res.robots);
            this.domainFields['favicon'].setValue(res.favicon);
            this.domainFields['path'].setValue(res.path);
            this.domainFields['email'].setValue(res.email);

            this.domainFields['page404_id'].setValue(res.page404_id);
            this.domainFields['page404interface'].setValue(res.page404interface);
            this.domainFields['alias'].setValue(res.alias);
            this.domainFields['redirect'].setValue(res.redirect);

            this.domainFields['resourcecompression'].setValue(res.resourcecompression);
            this.domainFields['layouts'].setValue(JSON.decode(res.layouts));

            this.domainExtensionsCreate();

            this.domainFieldsPublicProperties.setValue(JSON.decode(res.themeproperties));

            this.toAlternativPane();

            this.rdomain = this.retrieveDomainData();

            this.win.setLoading(false);

        }.bind(this)}).post({id: pDomain});

    },

    domainExtensionsCreate: function () {

        pModules = ka.settings.config;
        Object.each(pModules, function (item, key) {
            if (!item) return;
            if (!item.properties) return;
            var titleTxt = (item.title[window._session.lang]) ? item.title[window._session.lang] : item.title['en'];
            var title = new Element('h3', {
                html: titleTxt
            }).inject(this.domainExtensionsPane);

            $H(item.properties).each(function (item, key) {

                item.small = 1;

                new ka.Field(item).inject(this.domainExtensionsPane);

            }.bind(this));


        }.bind(this));
    },

    showDomainMaster: function (pRsn) {
        if (this.oldLoadDomainMasterRequest) this.oldLoadDomainMasterRequest.cancel();
        this.domainMasterPane.set('html', 'lade ...');
        this.oldLoadDomainMasterRequest = new Request.JSON({url: _path + 'admin/pages/domain/getMaster', noCache: 1, onComplete: function (res) {

            this.domainMasterPane.set('html', '');
            if (res && this.currentDomain.id != res.id) {
                var langTitle = res.lang;
                if (ka.settings.langs[res.lang]) {
                    langTitle = ka.settings.langs[res.lang].langtitle
                }

                this.domainMasterPane.set('html', _('Current language master is: ') + langTitle + ' (' + ka.settings.langs[res.lang].title + ', ' + res.lang + ')');
            }

        }.bind(this)}).post({id: pRsn});
    },

    addDomain: function () {
        var domain = this.win._prompt(_('Domain:'), '', function (p) {
            if (!p) return;
            this.lastSaveRequest = new Request.JSON({url: _path + 'admin/pages/domain/add', noCache: 1, onComplete: function (res) {
                ka.loadSettings();
                this.changeLanguage();
            }.bind(this)}).post({ domain: p, lang: this.language });
        }.bind(this));
    },

    saveDomain: function () {

        this.saveDomainBtn.startTip(_('Save ...'));

        if (this.lastSaveRequest) this.lastSaveRequest.cancel();

        this.overlayStart();

        var req = this.retrieveDomainData();
        if (!req) return;

        this.rdomain = req;

        this.lastSaveRequest = new Request.JSON({url: _path + 'admin/pages/domain/save', noCache: 1, onComplete: function (res) {
            this.overlay.destroy();
            this.saveDomainBtn.stopTip(_('Saved'));
            if (this.currentDomain.lang != req.lang) {
                this.changeLanguage();
            } else {
                this.domainTrees[this.currentDomain.id].reload();
            }
            this.currentDomain = req;
            ka.settings.domains.each(function (d, index) {
                if (d.id == req.id) {
                    ka.settings.domains[index] = req;
                }
            });
        }.bind(this)}).post(req);
    },

    retrieveDomainData: function () {

        var req = {};
        req.id = this.currentDomain.id;
        req.domain = this.domainFields['domain'].getValue();
        req.title_format = this.domainFields['title_format'].getValue();
        req.startpage_id = this.domainFields['startpage_id'].getValue();
        req.lang = this.domainFields['lang'].getValue();
        req.master = this.domainFields['master'].getValue();
        req.phplocale = this.domainFields['phplocale'].getValue();
        req.robots = this.domainFields['robots'].getValue();
        req.favicon = this.domainFields['favicon'].getValue();
        req.path = this.domainFields['path'].getValue();
        req.email = this.domainFields['email'].getValue();
        req.page404_id = this.domainFields['page404_id'].getValue();
        req.page404interface = this.domainFields['page404interface'].getValue();
        req.alias = this.domainFields['alias'].getValue();
        req.redirect = this.domainFields['redirect'].getValue();

        req.resourcecompression = this.domainFields['resourcecompression'].getValue();
        req.layouts = JSON.encode(this.domainFields['layouts'].getValue());
        req.themeproperties = JSON.encode(this.domainFieldsPublicProperties.getValue());

        req.session = this.domainSessionFields.getValue();

        var obj = this.auth_params_objects[ req.session.auth_class ];

        if (obj) {
            if (!obj.isOk()) {
                this.saveDomainBtn.stopTip(_('Failed - Check values'));
                return false;
            }
            req.session['auth_params'] = obj.getValue();
        }

        //properties
        var properties = {};
        Object.each(this._domainPropertiesFields, function (fields, extKey) {
            properties[extKey] = {};
            Object.each(fields, function (field, fieldKey) {
                properties[extKey][fieldKey] = field.getValue();
            })
        });
        req.extproperties = JSON.encode(properties);

        return req;
    },


    _createDomain: function () {
        var p = new Element('div', {
            'class': 'admin-pages-pane'
        }).inject(this.main);

        this.domainFields = new Hash();

        this.domainFields['domain'] = new ka.Field({label: _('Domain'), desc: _('Please make sure, that this domains points to this Kryn.cms installation. Otherwise you are not able to manage the content under the content tab.'), type: 'text', empty: false}).inject(p);

        this.domainFields['startpage_id'] = new ka.Field({label: _('Startpage'), type: 'pageChooser', empty: false, onlyIntern: true, cookie: 'startpage'}).inject(p);

        this.domainFields['title_format'] = new ka.Field({label: _('Title'), type: 'text', desc: _("Use %title as page title or %path as breadcrumb path.") + '<br />' + _("To generate own titles you can set for example %s").replace('%s', 'myExtensionClass::myTitleFunction'), empty: false}).inject(p);

        this.domainFields['path'] = new ka.Field({label: _('Path'), type: 'text', desc: _("Installation path of kryn. Default '/'")}).inject(p);


        var tableItems = [];
        $H(ka.settings.langs).each(function (lang, id) {
            tableItems.include({ id: id, label: lang.langtitle + ' (' + lang.title + ', ' + id + ')' });
        });
        this.domainFields['lang'] = new ka.Field({label: _('Language'), type: 'select',
            table_label: 'label', table_key: 'id',
            tableItems: tableItems
        }).inject(p);


        this.domainFields['master'] = new ka.Field({label: _('Language master'), type: 'checkbox'}).inject(p);

        this.domainMasterPane = new Element('div', {
            style: 'padding-left: 30px; color: gray;'
        }).inject(p);

        this.panes['domain'] = p;


        /* Domain-Theme */

        var p = new Element('div', {
            'class': 'admin-pages-pane'
        }).inject(this.main);


        this.domainFieldsPublicProperties = this.createPublicPropertiesBoard(_('Theme properties'));
        this.domainFieldsPublicProperties.inject(p);

        this.domainFields['layouts'] = new ka.Field({label: _('Limit selectable layouts'), desc: _('If you want to limit layouts to choose'),
            type: 'select', multiple: true, size: 6}).inject(p);

        $H(ka.settings.layouts).each(function (la, key) {
            var group = new Element('optgroup', {
                label: key
            }).inject(this.domainFields['layouts'].input);
            $H(la).each(function (layoutFile, layoutTitle) {
                new Element('option', {
                    html: layoutTitle,
                    value: layoutFile
                }).inject(group);
            })
        }.bind(this));


        var tableItems = [];
        $H(ka.settings.langs).each(function (lang, id) {
            if (id != this.language) {
                tableItems.include({ id: id, label: lang.langtitle + ' (' + lang.title + ', ' + id + ')' });
            }
        }.bind(this));

        this.panes['domainTheme'] = p;


        /* Sessions */

        var p = new Element('div', {
            'class': 'admin-pages-pane'
        }).inject(this.main);

        var fields = {
            'session_storage': {
                label: _('Session storage'),
                'default': 'database',
                items: {
                    'database': _('SQL Database')
                }
            },
            'session_timeout': {
                label: _('Session timeout'),
                type: 'text',
                'default': '3600'
            },

            'info': {
                'type': 'html',
                'label': _('Backend authentication'),
                'desc': _('Backend authentication settings are set under:<br />Butterfly -> Settings -> Session.')
            },

            'auth_class': {
                'label': _('Frontend authentication'),
                'desc': _('Please note that the user "admin" authenticate always against the Kryn.cms user.'),
                'type': 'select',
                'table_items': {
                    'kryn': _('Kryn.cms users')
                },
                depends: {
                    'auth_params[email_login]': {
                        'label': _('Allow email login'),
                        'type': 'checkbox',
                        'needValue': 'kryn'
                    }
                }
            }
        };

        var origin = ka.getFieldCaching();
        fields = Object.merge(fields, origin);

        fields.cache_type.label = _('Session storage');

        delete fields.cache_type.items.files;
        fields.session_storage = Object.merge(fields.session_storage, fields.cache_type);

        fields.session_storage['depends']['session_storage_config[servers]'] = Object.clone(origin.cache_type['depends']['cache_params[servers]']);
        delete fields.session_storage['depends']['cache_params[servers]'];

        fields.session_storage['depends']['session_storage_config[files_path]'] = Object.clone(origin.cache_type['depends']['cache_params[files_path]']);
        delete fields.session_storage['depends']['cache_params[files_path]'];

        delete fields.cache_type;

        fields.session_storage['depends']['session_auto_garbage_collector'] = {
            needValue: 'database',
            label: _('Automatic session garbage collector'),
            desc: _('Decreases the performance when dealing with huge count of sessions. For more performance start the session garbage collector through a cronjob. Press the help icon for more informations.'),
            help: 'session_garbage_collector',
            type: 'checkbox',
            'default': '0'
        };

        this.auth_params = {};
        this.auth_params_panes = {};

        Object.each(ka.settings.configs, function (config, id) {
            if (config.auth) {
                Object.each(config.auth, function (auth_fields, auth_class) {
                    Object.each(auth_fields, function (field, field_id) {
                        //field.needValue = id+'/'+auth_class;
                        //fields.auth_class.depends[ 'auth_params['+auth_class+']['+field_id+']'  ] = field;
                        fields.auth_class.table_items[ id + '/' + auth_class  ] = auth_class.capitalize();
                    }.bind(this));
                }.bind(this));
            }
        }.bind(this));

        this.domainSessionFields = new ka.FieldForm(p, fields);

        this.auth_params_objects = {};
        Object.each(ka.settings.configs, function (config, id) {
            if (config.auth) {
                Object.each(config.auth, function (auth_fields, auth_class) {

                    this.auth_params_panes[id + '/' + auth_class] = new Element('div', {
                        'style': 'display: none;'
                    }).inject(this.domainSessionFields.fields['auth_class'].childContainer);

                    this.auth_params_objects[ id + '/' + auth_class ] = new ka.FieldForm(this.auth_params_panes[id + '/' + auth_class], auth_fields);
                }.bind(this));
            }
        }.bind(this));

        this.domainSessionFields.fields['auth_class'].addEvent('check-depends', function () {
            Object.each(this.auth_params_panes, function (pane) {
                pane.setStyle('display', 'none');
            }.bind(this));
            var pane = this.auth_params_panes[ this.domainSessionFields.fields['auth_class'].getValue() ];

            if (pane) {
                pane.setStyle('display', 'block');
            }
        }.bind(this));

        this.domainSessionFields.fields['auth_class'].fireEvent('check-depends');

        this.panes['domainSessions'] = p;


        /* Domain-Settings */

        var p = new Element('div', {
            'class': 'admin-pages-pane'
        }).inject(this.main);

        var table = new Element('table', {width: '100%'}).inject(p);
        var tbody = new Element('tbody').inject(table);

        this.domainFields['favicon'] = new ka.Field({
            label: _('Favicon'), type: 'file', desc: _('Choose a favicon. Filetype .ico'),
            tableitem: 1, tableitem_title_width: 250
        }, tbody);


        this.domainFields['email'] = new ka.Field({
            label: _('Email sender'), desc: _('Extensions can use this email in outgoing emails as sender.'),
            tableitem: 1, tableitem_title_width: 250
        }, tbody);


        this.domainFields['alias'] = new ka.Field({
            label: _('Alias'), type: 'text',
            desc: _("Define one or more alias for the domain above. Comma separated alias domain list to this domain."),
            tableitem: 1, tableitem_title_width: 250
        }, tbody);

        this.domainFields['redirect'] = new ka.Field({
            label: _('Redirect'), type: 'text',
            desc: _("This domains redirect to the domain defined above. Comma separated redirect domain list to this domain"),
            tableitem: 1, tableitem_title_width: 250
        }, tbody);


        this.domainFields['phplocale'] = new ka.Field({
            label: 'PHP-Locale', type: 'text', desc: _('Locale LC_ALL in PHP'),
            tableitem: 1, tableitem_title_width: 250
        }, tbody);


        this.domainFields['robots'] = new ka.Field({
            label: 'Robot rules', type: 'textarea', desc: _('Define here the rules for search engines. (robots.txt)'),
            tableitem: 1, tableitem_title_width: 250
        }, tbody);

        this.domainFields['resourcecompression'] = new ka.Field({
            label: _('Css and JS compression'),
            desc: _('Merge all css files in one, same with javascript files. This improve the page render time'),
            type: 'checkbox',
            tableitem: 1, tableitem_title_width: 250
        }, tbody);

        this.domainFields['page404_id'] = new ka.Field({
            label: _('404-Page'), type: 'pageChooser', empty: false, onlyIntern: true, cookie: 'startpage',
            tableitem: 1, tableitem_title_width: 250
        }, tbody);

        this.domainFields['page404interface'] = new ka.Field({
            label: _('404-Interface'), desc: _('PHP file'), type: 'fileChooser', empty: false, cookie: 'file',
            tableitem: 1, tableitem_title_width: 250
        }, tbody);


        this.panes['domainSettings'] = p;
    },

    createPublicPropertiesBoard: function (pTitle) {
        var field = new Element('div', {
            'class': 'ka-field-main'
        });

        new Element('div', {
            'class': 'ka-field-title',
            html: '<div class="title">' + pTitle + '</div>'
        }).inject(field);

        var fieldContent = new Element('div', {
            'class': 'ka-field-field'
        }).inject(field);

        field.domainFieldsPublicProperties = {};

        Object.each(ka.settings.themeProperties, function (publicProperties, extKey) {
            field.domainFieldsPublicProperties[ extKey ] = {};
            Object.each(publicProperties, function (la, tKey) {

                field.domainFieldsPublicProperties[ extKey ][ tKey ] = {};

                var laDiv = new Element('div', {
                    'style': 'padding: 2px 0px;'
                }).inject(fieldContent);

                new Element('h3', {
                    html: tKey
                }).inject(laDiv);

                var table = new Element('table', {width: '100%', style: 'background-color: #e8e8e8'}).inject(laDiv);
                var tbody = new Element('tbody').inject(table);

                Object.each(la, function (fieldOpts, fKey) {


                    if (typeOf(fieldOpts) == 'array') {

                        if (fieldOpts[1] == 'page') {
                            fieldOpts.onlyIntern = 1;
                        }
                        fieldOpts['label'] = fieldOpts[0];
                        fieldOpts['type'] = fieldOpts[1];

                    }

                    fieldOpts.tableitem = 1;
                    fieldOpts.tableitem_title_width = 250;

                    field.domainFieldsPublicProperties[ extKey ][ tKey ][ fKey ] = new ka.Field(fieldOpts, tbody);
                }.bind(this))

            }.bind(this));
        }.bind(this));

        field.setValue = function (pValues) {
            $H(pValues).each(function (properties, extKey) {
                $H(properties).each(function (la, tKey) {
                    $H(la).each(function (value, fKey) {
                        if (field.domainFieldsPublicProperties[ extKey ] && field.domainFieldsPublicProperties[ extKey ][ tKey ] && field.domainFieldsPublicProperties[ extKey ][ tKey ][ fKey ]) {
                            field.domainFieldsPublicProperties[ extKey ][ tKey ][ fKey ].setValue(value);
                        }
                    }.bind(this))
                }.bind(this));
            }.bind(this));
        }

        field.getValue = function () {
            var res = {};
            $H(this.domainFieldsPublicProperties).each(function (properties, extKey) {
                res[ extKey ] = {};
                $H(properties).each(function (la, tKey) {
                    res[ extKey ][ tKey ] = {};
                    $H(la).each(function (opts, fKey) {
                        res[ extKey ][ tKey ][ fKey ] = field.domainFieldsPublicProperties[ extKey ][ tKey ][ fKey ].getValue();
                    }.bind(this))
                }.bind(this));
            }.bind(this));
            return res;
        }

        return field;

    },

    _createGeneral: function () {
        var p = new Element('div', {
            'class': 'admin-pages-pane'
        }).inject(this.main);
        this.generalFields = new Hash();

        this.generalFields['type'] = new ka.Field({label: _('Type'), type: 'imagegroup',
            table_label: 'label', table_key: 'id',
            items: {
                0: {label: _('Default'), src: _path + 'admin/images/icons/page_green.png'},
                1: {label: _('Link'), src: _path + 'admin/images/icons/link.png'},
                2: {label: _('Folder'), src: _path + 'admin/images/icons/folder.png'},
                3: {label: _('Deposit'), src: _path + 'admin/images/icons/page_white_text.png'}
            }
        }).inject(p);

        this.generalFields['type'].addEvent('change', this.changeType.bind(this));

        this.generalFields['title'] = new ka.Field({label: _('Title (navigation)'), type: 'text', empty: false}).inject(p);

        this.generalFields['page_title'] = new ka.Field({label: _('Alternative page title'), type: 'text'}).inject(p);


        this.generalFields['link'] = new ka.Field({label: _('Target'), desc: _('Extern links with "http://"'), type: 'chooser', empty: false, cookie: 'pageLink'}).inject(p);

        //targets
        this.generalFields['target'] = new ka.Field({label: _('Open in'), type: 'select',
            table_label: 'label', table_key: 'id',
            tableItems: [
                {label: _('Same window'), id: '_self'},
                {label: _('New window'), id: '_blank'}
            ]
        }).inject(p);


        //URL
        this.generalFields['url'] = new ka.Field({label: _('URL'), type: 'text', empty: false, check: 'kurl', help: 'admin/url'}).inject(p);

        this.urlAliase = new Element('div', {
            style: 'padding: 5px; padding-left: 26px;'
        }).inject(p);

        this.generalFieldsUrlPath = new Element('span', {
            html: 'Domain'
        }).inject(this.generalFields['url'].input, 'before');
        this.generalFields['url'].input.setStyle('width', 140);

        this.aliase = new Element('div').inject(p);
        new Element('div', {
            //            style: 'color: #999; padding-left: 25px;',
            //            html: 'Es leiten <b>5</b> Aliase auf diese Seite. [Bearbeiten]'
        }).inject(this.aliase);


        /*xxx
         //TEMPLATE
         var tableItems = [];
         ka.settings.templates.each(function(template){
         tableItems.include({ id: template, label: template });
         });
         this.generalFields['template'] = new ka.Field(
         {label: 'Template', type: 'select',
         table_label: 'label', table_key: 'id',
         tableItems: tableItems
         }).inject( p );
         this.generalFields['template'].input.addEvent('change', this._loadContent.bind(this));
         */

        //METAS
        this.metas = new Element('div').inject(p);
        this.metaTitle = new Element('div', {
            style: 'margin-top: 5px; border-top: 1px solid #ccc;height: 21px; padding-left: 20px; font-weight: bold;',
            html: 'Metas<br />'
        }).inject(this.metas);

        this.metaPane = new Element('ol', {
            style: 'margin-left: 20px; padding: 0px; padding-bottom: 4px; padding-left: 20px;'
        }).inject(this.metas);

        var addMeta = new Element('img', {
            src: _path + 'admin/images/icons/add.png',
            title: _('Add'),
            style: 'cursor: pointer'
        }).addEvent('click', function () {
            this.addMeta();
        }.bind(this)).setStyle('left', 1).setStyle('top', 3).setStyle('position', 'relative').inject(this.metaTitle);

        this.generalFields['metaKeywords'] = new ka.Field({label: _('Keywords'), type: 'text'}).inject(this.metas);

        this.generalFields['metaDesc'] = new ka.Field({label: _('Description'), type: 'textarea'}).inject(this.metas);


        this.panes['general'] = p;
    },

    _createRights: function () {
        var p = new Element('div', {
            'class': 'admin-pages-pane'
        }).inject(this.main);

        this.generalFields['visible'] = new ka.Field({label: _('Visible (navigation)'), type: 'checkbox'}).inject(p);

        this.generalFields['access_denied'] = new ka.Field({label: _('Access denied'), type: 'checkbox'}).inject(p);

        this.generalFields['force_https'] = new ka.Field({label: _('Force HTTPS'), type: 'checkbox'}).inject(p);

        this.generalFields['access_from'] = new ka.Field({label: _('Release at'), type: 'datetime'}).inject(p);

        this.generalFields['access_to'] = new ka.Field({label: _('Hide at'), type: 'datetime'}).inject(p);

        this.generalFields['access_from_groups'] = new ka.Field({label: _('Limit access to groups'), desc: ('For no restrictions let it empty'),
            type: 'textlist', panel_width: 320,
            store: 'admin/backend/stores/groups'
        }).inject(p);


        this.generalFields['access_nohidenavi'] = new ka.Field({label: _('Show in navigation by no access'), desc: _('Shows this page in the navigations also with no access'), type: 'checkbox'}).inject(p);

        this.generalFields['access_redirectto'] = new ka.Field({label: _('Redirect to page by no access'), desc: _('Choose a page, if you want to redirect the user to a page by no access.'), type: 'page'}).inject(p);

        this.generalFields['access_need_via'] = new ka.Field({label: _('Verify access with this service'), desc: _('Only if group limition is active'), type: 'select',
            tableItems: [
                {id: 0, name: 'Kryn.cms-Session'},
                {id: 1, name: 'Htaccess'}
            ], table_label: 'name', table_key: 'id'
        }).inject(p);

        this.panes['rights'] = p;
    },


    /*
     *
     *  CONTENTS 
     *
     */

    _createContents: function () {
        var p = new Element('div', {
            'class': 'admin-pages-pane'
        }).inject(this.main);

        var t = new Element('div', {
            style: 'position: absolute; left: 0px; right: 0px; top: 0px; height: 32px; text-align: right; border-bottom: 1px solid gray;'
        }).inject(p);

        var table = new Element('table', {style: 'float: right'}).inject(t);
        var tbody = new Element('tbody').inject(table);
        var tr = new Element('tr').inject(tbody);

        /* test new drag'n'drop */
        this.contentItems = new Element('div', {
            'class': 'pages-possibleContentItems',
            style: 'position: absolute; left: 8px; top: 5px; height: 15px; width: 200px;'
        }).inject(t);

        /* test end */


        var td = new Element('td').inject(tr);
        new Element('div', {
            text: _('Show layout'),
            style: 'float: left; padding: 5px;'
        }).inject(td);

        /*
        var cmmcid = new Date().getTime() + '_contentManagemModeCheckBox';
        this.contentManageModeCheckbox = new Element('input', {
            type: 'checkbox',
            id: cmmcid,
            checked: true
        }).addEvent('click', function () {
            if (this.contentManageModeCheckbox.checked) {
                this.contentManageMode = 'layout';
            } else {
                this.contentManageMode = 'list';
            }
            this._loadContentLayout();
        }.bind(this)).inject(td, 'top');
        */

        this.showLayoutBtn = new ka.Checkbox();
        this.showLayoutBtn.addEvent('change', function () {
            if (this.showLayoutBtn.getValue()) {
                this.contentManageMode = 'layout';
            } else {
                this.contentManageMode = 'list';
            }
            this._loadContentLayout();
        }.bind(this))
        document.id(this.showLayoutBtn).inject(td);
        document.id(this.showLayoutBtn).setStyle('float', 'left');
        document.id(this.showLayoutBtn).setStyle('top', 1);
        this.showLayoutBtn.setValue(1);

        this.layout = new ka.Select();
        this.layout.addEvent('change', function () {
            this._loadContentLayout();
        }.bind(this));
        document.id(this.layout).inject(this.win.titleGroups);
        document.id(this.layout).setStyles({
            position: 'absolute',
            right: 4,
            width: 170
        });

        this.layout.show = function(){
            document.id(this.layout).setStyle('display', 'block');
        }.bind(this);

        this.layout.hide = function(){
            document.id(this.layout).setStyle('display', 'none');
        }.bind(this);

        this.layout.hide();

        this.generalFields['layout'] = this.layout;

        if (ka.settings.layouts.length == 0) {
            this.win._alert(_('No layouts found. Install layout extensions or create one.'), function () {
                this.win.close();
            }.bind(this));
            return;
        }


        new Element('td', {style: 'padding: 0px 2px;color: gray;', html: ' | '}).inject(tr);

        var td = new Element('td').inject(tr);
        this.versionNoVersions = new Element('div', {text: _('No versions')}).inject(td);


        this.versions = new ka.Select(td);
        this.versions.addEvent('change', function(){
            this.loadVersion(this.versions.value);
        }.bind(this));
        document.id(this.versions).setStyle('width', 250);
        document.id(this.versions).setStyle('top', 1);
        document.id(this.versions).setStyle('right', 1);

        this.versions.show = function(){
            document.id(this.versions).setStyle('display', 'block');
        }.bind(this);

        this.versions.hide = function(){
            document.id(this.versions).setStyle('display', 'none');
        }.bind(this);


        this.iframePanel = new Element('div', {
            style: 'position: absolute; left: 0px; right: 0px; top: 33px; bottom: 0px; background-color: white; border-top: 1px solid white;'
        }).inject(p);
        this.__newIframe();

        this.panes['contents'] = p;
    },


    __newIframe: function () {
        if (this.iframe) this.iframe.destroy();
        //this.iframe = new Element('iframe', {
        this.iframe = new IFrame('iframe_pages', {
            'frameborder': 0,
            styles: {
                position: 'absolute',
                left: 0, top: 0, width: '100%', height: '100%',
                border: 0
            }
        }).inject(this.iframePanel);
    },


    _loadContent: function () {

        this.lastLoadedContentRsn = this.page.id;

        var layout = this.layout.getValue();
        if (!this.iframe || !this.iframe.contentWindow || !this.iframe.contentWindow.Asset || !this.oldPage || (this.page.layout != layout) || (this.page.domain_id != this.oldPage.domain_id)) {
            this.__newIframe();

            var w = this.iframe.contentWindow;
            var d = this.iframe.contentWindow.document;
            var _this = this;

            this.iframe.addEvent('load', function () {
                _this._loadContentLayout();
            }.bind(this));
            this.win.iframe = this.iframe;

            if (this.oldLoadTemplateRequest) {
                this.oldLoadTemplateRequest.cancel();
            }

            //cut last slash
            var path = location.pathname;
            if (path.substr(path.length - 1, 1) == '/') {
                path = path.substr(0, path.length - 1);
            }

            //cut length of 'admin'
            path = path.substr(0, path.length - 5);

            this.iframe.set('src', _path + 'admin/pages/getTemplate/id:' + this.page.id + '/json:0/domain=' + location.host + '/?path=' + path);

        } else {
            this._loadContentLayout();
        }

    },

    _loadContentLayout: function () {

        var w = this.iframe.contentWindow;
        if (!w.Asset) {
            logger('window(mootools) is not ready in _loadContentLayout()');
            /*this.trys++;
             if( this.trys < 15 ){
             this._loadContentLayout.delay(150, this);
             return;
             } else {
             alert('Problem with mootools.');
             }*/
        }
        this.trys = 0;

        w.ka = ka;
        w.win = this.win;
        w.currentPage = this.page;
        w.kpage = this;

        w.document.body.removeEvents('click');

        w.addEvent('click', function (e) {
            if (!e) return;
            w.fireEvent('deselect-content-elements');
        }.bind(this));
        
        this.win.border.addEvent('click', function (e) {
            if (!e) return;
            w.fireEvent('deselect-content-elements');
        }.bind(this));

        w.addEvent('deselect-content-elements', function () {

            if (this.ignoreNextDeselectAll) {
                this.ignoreNextDeselectAll = false;
                return;
            }

            this._deselectAllElements();
        }.bind(this));

        if (this.oldLoadContentRequest) {
            this.oldLoadContentRequest.cancel();
        }

        if (this.lastLoadedLayoutCssFiles) {
            this.lastLoadedLayoutCssFiles.each(function (cssAsset) {
                try {
                    if (cssAsset.destroy) {
                        cssAsset.destroy();
                    }
                } catch (e) {
                    cssAsset.href = '';
                }
            });
        }

        var layout = this.layout.getValue();

        if (this.noLayoutOverlay) {
            this.noLayoutOverlay.destroy();
        }

        if (layout == '' && this.generalFields['type'].getValue() == 0) {
            //view overlay
            this.noLayoutOverlay = new Element('div', {
                'class': 'ka-pages-nolayoutoverlay'
            }).inject(this.iframePanel);

            //todo, when we have versions, then use following as bg
            //ka-pages-nolayout-withversions-bg.png


            this.noLayoutOverlayText = new Element('div', {
                'class': 'ka-pages-nolayoutoverlay-text',
                html: _('Please choose a layout for this page.')
            }).inject(this.noLayoutOverlay);
        }

        if (this.ablageModus == true) {
            layout = 'kryn/blankLayout.tpl';
        }

        this.oldLoadContentRequest = new Request.JSON({url: _path + 'admin/pages/getLayout', noCache: 1,
            onComplete: this._renderContentLayout.bind(this) }).post({ name: layout, id: this.page.id });

    },

    _updateDraggerBarPosition: function () {
        if (Browser.Engine.webkit) {
            var mytop = this.iframe.contentWindow.document.body.scrollTop;
            var myleft = this.iframe.contentWindow.document.body.scrollLeft;
        } else {
            var mytop = this.iframe.contentWindow.document.html.scrollTop;
            var myleft = this.iframe.contentWindow.document.html.scrollLeft;
        }
        if (this.contentItemsHidden != true) {
            this.contentItems.morph({
                'top': mytop,
                'right': (myleft * -1)
            });
        } else {
            this.contentItems.morph({
                'top': mytop - 323,
                'right': (myleft * -1)
            });
        }
    },

    _createDraggerBar: function () {

        return;
        this.win.onResizeComplete = this._updateDraggerBarPosition.bind(this);
        this.contentItems = new Element('div', {
            'class': 'ka-admin-pages-possibleContentItems'
        }).set('tween', {duration: 400, transition: Fx.Transitions.Cubic.easeOut}).inject(this.iframe.contentWindow.document.body);

        this.contentItemsToggler = new Element('div', {
            'class': 'ka-admin-pages-possibleContentItemsToggler',
            title: _('Hide element bar')
        }).inject(this.contentItems);

        this.contentItems.set('morph', {duration: 400, transition: Fx.Transitions.Cubic.easeOut});

        /*this.tinyMceToolbar = new Element('div', {
         'id': 'tinyMceToolbar',
         'class': 'mceEditor o2k7Skin o2k7SkinSilver',
         'style': 'position: absolute; left: 0px; top: 30px; right: 0px; height: 60px;'
         }).inject( this.panes['contents'] );
         */

        this.iframe.contentWindow.document.html.style.marginTop = '45px';
        this.iframe.contentWindow.document.html.style.marginRight = '35px';

        this.iframe.contentWindow.document.addEvent('scroll', function () {
            this._updateDraggerBarPosition();
        }.bind(this));
        this._updateDraggerBarPosition();

        this.__draggerItems = {};

        this._langs = $H({
            text: _('Text'),
            layoutelement: _('Layout Element'),
            picture: _('Picture'),
            plugin: _('Plugin'),
            pointer: _('Pointer'),
            template: _('Template'),
            navigation: _('Navigation'),
            html: _('HTML'),
            php: _('PHP')
        });

        this._langs.each(function (label, type) {

            if (!ka.checkPageAccess(this.page.id, 'content-' + type)) {
                return;
            }

            var element = {lang: type};

            this.__draggerItems[type.toLowerCase()] = this.__buildDragItem(element);

        }.bind(this));

        var height = $H(this.__draggerItems).getLength() * 41;


        this.contentItemsToggler.addEvent('click', function () {
            if (this.contentItemsHidden != true) {
                this.contentItemsHidden = true;
                this.contentItemsToggler.set('class', 'ka-admin-pages-possibleContentItemsToggler ka-admin-pages-possibleContentItemsTogglerTop');
                this.contentItems.tween('top', height * -1);
            } else {
                this.contentItemsHidden = false;
                this.contentItemsToggler.set('class', 'ka-admin-pages-possibleContentItemsToggler');
                this.contentItems.tween('top', 0);
            }
        }.bind(this))

        this.contentItems.setStyle('height', height);
    },

    __buildDragItem: function (element) {
        var type = element.lang;
        var div = new Element('div', {
            title: this._langs[type],
            style: '',
            'class': 'ka-layoutContent-main',
            lang: type
        }).store('fromBar', true).inject(this.contentItems);

        new Element('img', {
            src: _path + 'admin/images/ka-keditor-elementtypes-item-' + type + '-bg.png'
        }).inject(div);

        return div;
    },

    checkSortedItems: function (element, clone) {
    },


    /* plugin chooser pane */
    /*
     showPluginChooserPane: function(){

     var toolbarSize = this.elementPropertyToolbar.getSize();

     this.pluginChooserPane.setStyle('left', toolbarSize.x);
     var width = 600;


     if( toolbarSize.y < 10 ){
     this.pluginChooserPane.tween('height', this.elementPropertyHeight);
     this.pluginChooserPane.setStyle('width', width);
     } else {
     this.pluginChooserPane.setStyle('height', this.elementPropertyHeight);
     this.pluginChooserPane.tween('width', width);
     }

     },

     hidePluginChooserPane: function( pToolbarStillOpen ){
     if( pToolbarStillOpen )
     this.pluginChooserPane.tween('width', 1);
     else
     this.pluginChooserPane.tween('height', 1);
     },
     */



    createNewDraggItem: function (element) {
        var type = element.lang;
        this.__draggerItems[type] = this.__buildDragItem(element, element).inject(element, 'after');

        /*
         *  new Element('div', {
         title: _(type),
         'class': 'ka-layoutContent-main',
         style: '',
         lang: type
         })
         .store('fromBar', true)

         new Element('img', {
         src: _path+ PATH_WEB + '/admin/images/ka-keditor-elementtypes-item-'+type+'-bg.png'
         }).inject( this.__draggerItems[type] );
         */

        this.initContentLayoutSort();
    },

    _renderContentLayout: function (pLayout) {
        var w = this.iframe.contentWindow;

        //        this.layoutAsset = new w.Asset.css( _path+ PATH_WEB + '/css/layout_'+this.layout.getValue()+'.css' );
        //xxx w.$('krynContentManager_layoutContent').set('html', pLayout.tpl );


        if (this.contentManageMode == 'list') {
            this.iframe.contentWindow.document.body.setStyle('display', 'none');
        }

        $(this.iframe.contentWindow.document.body).set('html', pLayout.tpl);

        if (this.contentItems) {
            this.contentItems.destroy();
        }

        this._createDraggerBar();

        this.win.contentCss = '';
        if (pLayout.css) {
            pLayout.css.each(function (css) {
                var css = new Element('link', {
                    rel: "stylesheet",
                    type: "text/css",
                    href: _baseUrl + ((css.substr(0,1)=='/') ? css.substr(1) : css)
                }).inject(w.document.head);
                this.lastLoadedLayoutCssFiles.include(css);
            }.bind(this));
        }

        w.$$('a').set('href', 'javascript: ;');
        w.$$('a').onclick = null;

        this.layoutBoxes = {};
        this.iframe.contentWindow.pageObj = this;

        this.layoutBoxes = ka.renderLayoutElements(this.iframe.contentWindow, this);
        //this._renderContentLayoutSearchAndFindBoxes( $(this.iframe.contentWindow.document.body) );

        if (this.contentManageMode == 'list') {

            var div = new Element('div', {'class': 'ka-admin-pages-manageModeList'})
            Object.each(this.layoutBoxes, function (layoutBox) {
                layoutBox.inject(div);
            }.bind(this));
            this.iframe.contentWindow.document.body.empty();
            div.inject(this.iframe.contentWindow.document.body);

            this.iframe.contentWindow.document.body.setStyle('display', 'block');
            this.iframe.contentWindow.document.body.setStyle('text-align', 'left');
        }

        var contents = this.page.contents;
        if (typeOf(this.page.contents) == 'string') {
            contents = new Hash(JSON.decode(this.page.contents));
        }

        Object.each(this.layoutBoxes, function (editLayout, boxId) {
            editLayout.setContents(contents[boxId]);
        });

        this.initContentLayoutSort();

        this.layoutBoxesInitialized = true;
        this.rpage = this.retrieveData();

    },

    initContentLayoutSort: function () {
        /*var list = [];
         this.layoutBoxes.each(function(layoutBox){
         list.include( layoutBox.main );

         if( layoutBox.contents.each ){
         layoutBox.contents.each(function(layoutContent){
         list.include( layoutContent.main );
         });
         }
         });*/

        return;
        if (this.sortables) {
            this.sortables.detach();
        }

        var _this = this;
        this.sortables = new Sortables([$(this.iframe.contentWindow).$$('.ka-layoutBox-container'), this.contentItems], {
            clone: true,
            handle: '.ka-layoutContent-mover',
            revert: true,
            precalculate: true,
            stopPropagation: true,
            opacity: 0.3,
            onStart: function (element, clone) {
                if (element.getElement('span.mceEditor')) {
                    //tinymce.EditorManager.remove( element.retrieve('tinyMceId') );
                    element.getElement('span.mceEditor').setStyle('display', 'none');
                    element.retrieve('layoutContent').toData();
                }
                if (element.retrieve('fromBar') == true) {
                    element.set('class', 'ka-layoutContent-main inDragMode');
                    _this.createNewDraggItem(element);
                    element.store('newElementCreated', true);
                }
            },
            onComplete: function (element) {
                if (element.getElement('span.mceEditor')) {
                    element.retrieve('layoutContent').type2Text(true);
                    //element.getElement('span.mceEditor').setStyle('display', 'block');
                    //parent.initTinyWithoutResize( element.retrieve('tinyMceId') );
                }
                if (element.retrieve('fromBar') == true && element.retrieve('newElementCreated') == true) {
                    if (element.getParent().get('class') != 'ka-admin-pages-possibleContentItems') {
                        var layoutBox = element.getParent().retrieve('layoutBox');
                        layoutBox.drop(element.lang, element);
                    }
                    element.destroy();
                }
            },
            onSort: function (element, clone) {
                //                _this.checkSortedItems(element,clone);
            }
        });

        var _this = this;
        Object.each(this.layoutBoxes, function (layoutBox) {
            _this.sortables.removeItems(layoutBox.title);
            if (layoutBox.contents.each) {
                layoutBox.contents.each(function (layoutContent) {
                    _this.sortables.addItems(layoutContent.main);
                });
            }
        });
    },

    loadVersion: function (pVersion, pCallback) {

        if (this.oldVersionRequest) {
            this.oldVersionRequest.cancel();
        }

        this.versions.setValue(pVersion);

        this.oldVersionRequest = new Request.JSON({url: _path + 'admin/pages/getVersion', noCache: 1, async: false, onComplete: function (res) {

            this.page.contents = res;
            if (!this.iframe) {
                this._loadContent();
            } else {
                Object.each(this.layoutBoxes, function (editLayout, boxId) {
                    var contents = [];
                    editLayout.clear();
                    if (res && res[boxId]) {
                        contents = res[boxId];
                    }
                    editLayout.setContents(contents);
                });
                this.initContentLayoutSort();
            }

            this.loadedVersion = pVersion;

            if (pCallback) {
                pCallback(res);
            }

        }.bind(this)}).post({id: this.page.id, version: pVersion });
    },

    /*
     _renderContentLayoutSearchAndFindBoxes: function( pElement, pCode ){

     if( !pElement.getFirst() && pElement.get('text').search(/{slot.+}/) >= 0 ){

     var value = pElement.get('text');
     value = value.substr( 6, value.length-7 );

     var options = {};

     var exp = /([a-zA-Z0-9-_]+)=([^"']([^\s]*)|["]{1}([^"]*)["]{1}|[']{1}([^']*)[']{1})/g;
     while( res = exp.exec( value ) ){
     options[ res[1] ] = res[4];
     }
     exp = null;

     if( options.id+0 > 0 ){
     //var idRegex = /{slot.+id="(\d+)".*}/;
     //var res = idRegex.exec( pElement.get('text') );
     this.layoutBoxes[ options.id ] = new ka.LayoutBox( pElement, options.name, this.win, options.css, options['default'], this, options );
     }

     }

     if( pElement.getFirst() ){
     pElement.getChildren().each(function(child){
     this._renderContentLayoutSearchAndFindBoxes( child );
     }.bind(this));
     }
     },*/

    /*setElementPropertyToolbar: function( pElement ){
     this._showElementPropertyToolbar();
     },

     _hideElementPropertyToolbar: function(){


     //this.elementPropertyToolbarContent.empty();
     //this.elementPropertyToolbarAccordion.display(-1);

     this.elementPropertyToolbar.tween('height', 1);      
     this.treeContainer.tween('bottom', 3);
     this.hidePluginChooserPane();
     },

     _showElementPropertyToolbar: function(){
     height = this._calcElementPropertyToolbarHeight();
     if( this.inHideMode != true ) 
     this.treeContainer.tween('bottom', height+2);
     this.elementPropertyToolbar.tween('height', height);





     accHeight = this._calcAccordionHeight();
     //check if accordion height has changed if so -> reinit
     if(!this.lastAccordionHeight || this.lastAccordionHeight != accHeight) 
     this._initElementSettingsToolbarAccordion(accHeight);

     //display first element
     this.elementPropertyToolbarAccordion.display(0);


     },

     _calcElementPropertyToolbarHeight : function () {
     var height = 221;
     tY = this.tree.getSize().y;
     if( tY*0.4 > height ){
     height = tY*0.4;
     }
     this.elementPropertyHeight = height;
     return height;
     },*/


    _deselectAllElements: function (pContent) {

        var selected = 0;

        Object.each(this.layoutBoxes, function (box, id) {
            selected = selected + box.deselectAll(pContent);
        });

    },

    /*
     *
     *  RESOURCES
     */

    _createResources: function () {
        var p = new Element('div', {
            'class': 'admin-pages-pane'
        }).inject(this.main);

        this.generalFields['resourcesCss'] = new ka.Field({label: 'CSS', type: 'textarea'}).inject(p);
        this.generalFields['resourcesCss'].input.setStyles({
            height: 200, width: 600
        });

        this.generalFields['resourcesJs'] = new ka.Field({label: 'Javascript', type: 'textarea'}).inject(p);
        this.generalFields['resourcesJs'].input.setStyles({
            height: 200, width: 600
        });

        this.panes['resources'] = p;
    },

    addJsRessource: function (pFile) {
        var div = new Element('div', {
            style: 'padding: 2px;'
        }).inject(this.resourcesJsFiles);
        new Element('span', {
            text: pFile
        }).inject(div);

        new Element('img', {
            src: _path + 'admin/images/icons/delete.png',
            title: 'Entfernen',
            style: 'cursor: pointer; float: right;'
        }).inject(div);
    },

    _createVersioning: function () {
        var p = new Element('div', {
            'class': 'admin-pages-pane',
            style: 'padding: 4px;'
        }).inject(this.main);

        this.panes['versioning'] = p;
    },

    _createProperties: function () {
        var p = new Element('div', {
            'class': 'admin-pages-pane',
            style: 'padding: 4px;'
        }).inject(this.main);

        this._pagePropertiesFields = {};

        ka.settings.modules.each(function (ext) {

            var config = ka.settings.configs[ext];

            if (config && config.pageProperties) {

                var extFields = {};


                var title = config.title['en'];
                if (config.title[window._session.lang]) {
                    title = config.title[window._session.lang];
                }

                new Element('h3', {text: title}).inject(p);

                var table = new Element('table', {width: '100%'}).inject(p);
                var tbody = new Element('tbody').inject(table);

                $H(config.pageProperties).each(function (property, key) {
                    property.tableitem = 1;
                    property.tableitem_title_width = 250;
                    extFields[key] = new ka.Field(property).inject(tbody);

                }.bind(this));

                this._pagePropertiesFields[ext] = extFields;
            }
        }.bind(this));

        this.panes['properties'] = p;
    },

    _createDomainProperties: function () {

        /* domain extension properties */

        var p = new Element('div', {
            'class': 'admin-pages-pane'
        }).inject(this.main);
        this.domainExtensionsPane = p;
        this.panes['domainProperties'] = p;


        this._domainPropertiesFields = {};

        ka.settings.modules.each(function (ext) {

            var config = ka.settings.configs[ext];

            if (config && config.domainProperties) {

                var extFields = {};


                var title = config.title['en'];
                if (config.title[window._session.lang]) {
                    title = config.title[window._session.lang];
                }


                new Element('h3', {text: title}).inject(p);
                var table = new Element('table', {width: '100%'}).inject(p);
                var tbody = new Element('tbody').inject(table);

                $H(config.domainProperties).each(function (property, key) {
                    property.tableitem = 1;
                    property.tableitem_title_width = 250;
                    extFields[key] = new ka.Field(property, tbody);

                }.bind(this));

                this._domainPropertiesFields[ext] = extFields;
            }
        }.bind(this));

    },

    loadVersionOverview: function () {
        if (this.lastVersionOverviewRequest) {
            this.lastVersionOverviewRequest.cancel();
        }

        this.lastVersionOverviewRequest = new Request.JSON({url: _path + 'admin/pages/getPageVersions/', noCache: 1, onComplete: function (pRes) {
            this._loadVersionOverview(pRes);
        }.bind(this)}).post({id: this.page.id});
        //pageVersion: 'live' : 'version';

    },

    _loadVersionOverview: function (pValues) {
        var p = this.panes['versioning'];
        p.empty();

        this.versionsSetLiveBtns = [];
        this.versionsLoadBtns = [];

        this.versionTable = new ka.Table([
            [_('#'), 50],
            [_('Live'), 40],
            [_('Owner')],
            [_('Created'), 120],
            [_('Actions'), 200]
        ]).inject(p);

        if (pValues.versions.count == 0) {

            new Element('div', {
                'text': _('No version exists.')
            }).inject(p);

        } else {
            pValues.versions.each(function (item) {

                var actions = new Element('span');

                if (ka.checkPageAccess(this.page.id, 'loadVersion')) {
                    var ld = new ka.Button(_('Load')).addEvent('click', function () {

                        this.viewType('contents', true);
                        this.loadVersion(item.id);

                    }.bind(this)).inject(actions);

                    this.versionsLoadBtns.include(ld);
                }

                if (ka.checkPageAccess(this.page.id, 'setLive')) {
                    var sl = new ka.Button(_('Set Live')).addEvent('click', function () {

                        this.win._confirm(_('Publish this version ?'), function (e) {
                            if (!e) return;
                            new Request.JSON({url: _path + 'admin/pages/setLive/', noCache: 1, onComplete: function () {
                                this.loadVersionOverview();
                                this.loadVersions();
                                var d = this.domainTrees[this.page.domain_id];
                                if (d) {
                                    d.reload()
                                }
                            }.bind(this)}).post({version: item.id});
                        }.bind(this));

                    }.bind(this)).inject(actions);

                    this.versionsSetLiveBtns.include(sl);
                }

                this.versionTable.addRow([
                    item.id, new Element('img', {src: _path + 'admin/images/icons/' + ((pValues.active == 1) ? 'accept' : 'delete') + '.png'}), item.username, (new Date(item.created * 1000).format('%d.%m.%Y %H:%M')), actions
                ]);

            }.bind(this));
        }
    },

    createVersionLine: function (pValues) {

        trClass = '';
        if (pValues.id == this.versions.getValue()) {
            trClass = 'activeVersion';
        }

        var tr = new Element('tr', {'class': trClass});

        new Element('td', {
            text: '#' + pValues.id,
            width: 50
        }).inject(tr);

        var icon = (pValues.active == 1) ? 'accept' : 'delete';

        new Element('td', {
            html: '<img src="' + _path + 'admin/images/icons/' + icon + '.png" />',
            width: 50
        }).inject(tr);

        new Element('td', {
            text: pValues.username
        }).inject(tr);

        new Element('td', {
            text: new Date(pValues.created * 1000).format('%d.%m.%Y %H:%M')
        }).inject(tr);


        var actions = new Element('td', {
        }).inject(tr);

        var ld = new ka.Button(_('Load')).addEvent('click', function () {

            this.viewType('contents', true);
            this.loadVersion(pValues.id);

        }.bind(this)).inject(actions);

        this.versionsLoadBtns.include(ld);
        if (!ka.checkPageAccess(this.page.id, 'loadVersion')) {
            ld.hide();
        }

        var sl = new ka.Button(_('Set Live')).addEvent('click', function () {

            this.win._confirm(_('Publish this version ?'), function (e) {
                if (!e) return;
                new Request.JSON({url: _path + 'admin/pages/setLive/', noCache: 1, onComplete: function () {
                    this.loadVersionOverview();
                    this.loadVersions();
                    var d = this.domainTrees[this.page.domain_id];
                    if (d) {
                        d.reload()
                    }
                }.bind(this)}).post({version: pValues.id});
            }.bind(this));

        }.bind(this)).inject(actions);

        this.versionsSetLiveBtns.include(sl);


        if (!ka.checkPageAccess(this.page.id, 'setLive')) {
            sl.hide();
        }


        return tr;
    },

    _createSearchIndexPane: function () {
        var p = new Element('div', {
            'class': 'admin-pages-pane',
            'style': 'padding: 10px;'
        }).inject(this.main);


        /*
         fieldAddToBl = new Element('div', { 'class' : 'ka-field-main'}).inject(p);
         this.setToBlListBtn = fieldAddToBl;
         fieldAddToBlTitle = new Element('div', { 'class' : 'ka-field-title'}).inject(fieldAddToBl);
         new Element('div', { 'class' : 'title', 'text' : _('Add this page to the search index blacklist')}).inject(fieldAddToBlTitle);


         fieldAddToBlBtn = new Element('div', { 'class' : 'ka-field-field', 'style' : 'cursor: pointer'}).inject(fieldAddToBl);    	 
         new Element('img', { 'src' : _path+ PATH_WEB + '/admin/images/icons/lightning_delete.png'}).inject(fieldAddToBlBtn);
         fieldAddToBlBtn.addEvent('click', function() { this.addPageToBlacklist(this.page.url) }.bind(this));
         */

        this.generalFields['unsearchable'] = new ka.Field({label: _('Exclude this page from search index'), type: 'checkbox'}).inject(p);


        this.generalFields['search_words'] = new ka.Field({label: _('Search words'), type: 'textarea'}).inject(p);

        new Element('div', { 'class': 'title', 'text': _('Search indexes for this site')})
        .inject(new Element('div', { 'class': 'ka-field-title' })
        .inject(new Element('div', { 'class': 'ka-field-main', 'style': 'margin-top:10px;' }).inject(p)));

        this.sioTableDiv = new Element('div', {
            style: 'position: absolute; left: 0px; top: 208px; right: 0px; bottom: 0px; overflow: auto;'
        }).inject(p);


        this.sioTable = new ka.Table().inject(this.sioTableDiv);
        this.sioTable.setColumns([
            [_('Url'), 300],
            [_('Title'), 150],
            [_('Date of index'), 120],
            [_('Content hash'), 250],
            [_('Action'), 50]
        ]);

        this.panes['searchIndex'] = p;
    },

    addPageToBlacklist: function (pUrl) {

        this.win.setLoading(true, null, {left: this.main.getStyle('left')});

        new Request.JSON({ url: _path + 'admin/backend/window/loadClass/saveItem', noCache: 1,
            onComplete: function (pSRes) {
                if (pSRes) {
                    nMsg = _('The URL ') + '&quot;<b>' + pUrl + '</b>&quot;' + _(' has been added successfully to your search index blacklist.')
                    ka.helpsystem.newBubble(_('URL successfully added!'), nMsg, 10000);
                    this.loadSearchIndexOverview();
                }
                this.win.setLoading(false);
            }.bind(this)


        }).post({'url': pUrl, 'domain_id': this.page.domain_id, 'code': 'system/searchBlacklist/edit', 'module': 'admin', 'editFaked': 1});
    },


    loadSearchIndexOverview: function () {
        if (this.seachIndexOverviewRequest) {
            this.seachIndexOverviewRequest.cancel();
        }

        this.sioTable.loading(true);
        this.seachIndexOverviewRequest = new Request.JSON({url: _path + 'admin/backend/searchIndexer/getSearchIndexOverview', noCache: 1,
            onComplete: function (res) {
                this.sioTable.loading(false);
                res.each(function (pVal, pKey) {
                    var vUrl = this.getBaseUrl(this.page) + pVal[0].substr(1) + '/';

                    res[pKey][4] = '';
                    res[pKey][4] += '&nbsp;<a href="' + vUrl + '" target="_blank"><img src="' + _path + 'admin/images/icons/eye.png" title="' + _('View this page') + '" /></a>';
                }.bind(this));
                this.sioTable.setValues(res);

                var addToBlacklistBtns = this.sioTableDiv.getElements('a.addToBlacklistBtn');
                if (addToBlacklistBtns) {
                    addToBlacklistBtns.each(function (pItem) {
                        var itemUrl = pItem.get('href');
                        pItem.set('href', 'javascript:;');
                        pItem.addEvent('click', function () {
                            this.addPageToBlacklist(itemUrl);
                        }.bind(this));
                    }.bind(this));
                }

            }.bind(this)}).post({page_id: this.page.id});
    },

    clearMeta: function () {
        this.metaPane.empty();
    },

    addMeta: function (pVals) {
        if (!pVals) pVals = {key: '', value: ''};

        var main = new Element('li', {
            style: 'padding-top: 2px; margin-left: 0px;',
            'class': 'ka-field-field'
        }).inject(this.metaPane);

        new Element('span', {html: 'Name: '}).inject(main);

        var key = new Element('input', {
            value: pVals.key,
            'class': 'text', style: 'width: 70px; margin-right: 5px;'
        }).inject(main);

        new Element('span', {html: 'Wert: '}).inject(main);
        var valueInput = new Element('input', {
            value: pVals.value,
            'class': 'text', style: 'width: 120px;'
        }).inject(main);

        new Element('img', {
            src: _path + 'admin/images/icons/delete.png',
            align: 'top',
            title: 'Löschen',
            style: 'cursor: pointer;'
        }).addEvent('click', function () {
            key.value = '';
            main.destroy();
        }.bind(this)).inject(main);

        this._metas.include({ key: key, value: valueInput });

    },

    retrieveData: function (pAndClose) {
        var res = new Hash();
        res.domain_id = this.page.domain_id;
        res.include('id', this.id);


        //general data
        this.generalFields.each(function (field, fieldId) {
            res.include(fieldId, field.getValue());
        });

        //properties
        var properties = {};
        Object.each(this._pagePropertiesFields, function (fields, extKey) {
            properties[extKey] = {};
            Object.each(fields, function (field, fieldKey) {
                properties[extKey][fieldKey] = field.getValue();
            })
        });
        res.properties = JSON.encode(properties);

        //meta-extra todo

        var meta = [];
        meta.include({name: 'keywords', value: this.generalFields['metaKeywords'].getValue()});
        meta.include({name: 'description', value: this.generalFields['metaDesc'].getValue()});
        this._metas.each(function (mymeta) {
            if (mymeta.key && mymeta.key.value != '') {
                meta.include({name: mymeta.key.value, value: mymeta.value.value});
            }
        });

        res.meta = JSON.encode(meta);


        //content 

        if (this.layoutBoxesInitialized == false) {
            res.include('dontSaveContents', 1);
        } else {
            try {
                res.include('contents', JSON.encode(this.retrieveContents(pAndClose)));
            } catch (e) {
                logger('Error in retrieveData();');
                logger(e);
                res.include('dontSaveContents', 1);
            }
        }
        //res.include( 'contents', contents );
        return res;
    },

    retrieveContents: function (pAndClose) {
        var contents = new Hash();
        Object.each(this.layoutBoxes, function (pBox, pBoxId) {
            contents.include(pBoxId, pBox.getValue(pAndClose));
        });
        return contents;
    },

    saveAs: function () {

    },

    saveAndClose: function (pAndPublish) {
        this.save(true, pAndPublish);
    },

    save: function (pAndClose, pAndPublish) {

        var req = this.retrieveData(pAndClose);
        if (!req) return;

        this.rpage = req;

        if (pAndPublish == true) {
            req.andPublish = 1;
        }

        if (pAndPublish && this.page.url != req.url) {
            var obj = this.win.newDialog(_('You have changed the URL. Should the system creates a new alias for the old one?'));


            new ka.Button(_('No')).addEvent('click', function () {

                obj.close();
                this._save(req, pAndPublish);

            }.bind(this)).inject(obj.bottom);

            new ka.Button(_('Yes')).addEvent('click', function () {

                obj.close();
                req.newAlias = 1;
                this._save(req, pAndPublish);

            }.bind(this)).inject(obj.bottom);


            new ka.Button(_('Yes with subpages')).addEvent('click', function () {

                obj.close();
                req.newAlias = 1;
                req.newAliasWithSub = 1;
                this._save(req, pAndPublish);

            }.bind(this)).inject(obj.bottom);

            obj.center();

        } else {
            this._save(req, pAndPublish);
        }
    },

    _save: function (pReq, pAndPublish) {

        if (pAndPublish) {
            this.saveButtonPublish.startTip(_('Save ...'));
        } else {
            this.saveButton.startTip(_('Save ...'));
        }

        if (this.lastSaveRequest) this.lastSaveRequest.cancel();

        this.overlayStart();
        this.lastSaveWasPublished = pAndPublish;

        this.lastSaveRequest = new Request.JSON({url: _path + 'admin/pages/save', noCache: 1, onComplete: function (res) {

            this.overlay.destroy();

            if (pAndPublish) {
                this.saveButtonPublish.stopTip(_('Saved'));
            } else {
                this.saveButton.stopTip(_('Saved'));
            }

            var d = this.domainTrees[this.page.domain_id];
            if (d && (this.page.title != res.title || this.page.type != res.type || this.page.visible != res.visible || this.page.access_denied != res.access_denied) || ( this.page.draft_exist == 1 && pAndPublish) || this.page.access_from_groups != res.access_from_groups || ( this.page.draft_exist == 0 && !pAndPublish)) {
                d.reloadParentOfActive();
            }
            if (res) {
                this.page = res;
                this.toggleSearchIndexButton(this.page.type);
            }

            if (pReq.andPublish != 1) {
                this.loadedVersion = res.version_id;
            } else {
                this.loadedVersion = '-';
            }

            this.loadVersions();
            this.loadAliases();

        }.bind(this)}).post(pReq);
    },

    viewType: function (pType, pOnlyTabs) {
        this.currentViewType = pType;
        this.viewButtons.each(function (button) {
            button.setPressed(false);
        });


        if (pType != 'empty') {
            this.viewButtons[pType].setPressed(true);
            this.showPane(pType);
        } else {
            this.hidePanes();
        }
        if (!pOnlyTabs) {
            if (pType == 'contents') {
                if (this.lastLoadedContentRsn != this.page.id) {
                    this._loadContent();
                }
            }
            if (pType == 'versioning') {
                this.loadVersionOverview();
            }
        }
    },

    hidePanes: function () {
        this.panes.each(function (panes) {
            panes.setStyle('display', 'none');
        });
    },

    showPane: function (pPane) {
        this.hidePanes();
        var p = this.panes[pPane];
        if (p.setStyle) {
            p.setStyle('display', 'block');
        }
    },

    pageAdd: function (pDomain) {



        var domaintitle = '';
        ka.settings.domains.each(function (domain) {
            if (domain.id == pDomain) {
                domaintitle = domain.domain;
            }
        });

        ka.wm.openWindow('admin', 'pages/addDialog', null, this.win.id, {
            onChoose: function (pTitles, pTarget, pPos) {
                //alert(pPage.title);
            },
            domain_id: pDomain,
            onComplete: function (pDomain) {
                this.domainTrees[pDomain].reload();
            }.bind(this)
        }, true);
    },

    hasUnsavedPageChanges: function () {

        if (!this.page) return false;

        var currentData = this.retrieveData();
        if (!currentData) return true;

        var hasUnsaved = false;

        var blacklist = ['dontSaveContents'];

        Object.each(currentData, function (value, id) {
            if (blacklist.contains(id)) return;

            if (typeOf(this.rpage[id]) == 'null') {
                this.rpage[id] = '';
            }

            if (value + "" != this.rpage[id]) {
                //logger(id+ ': '+value+' != '+this.rpage[id]);
                hasUnsaved = true;
            }
        }.bind(this));

        return hasUnsaved;

    },

    hasUnsavedDomainChanges: function () {

        if (!this.rdomain) return false;

        var currentData = this.retrieveDomainData();
        if (!currentData) return true;

        var hasUnsaved = false;

        var blacklist = [];

        Object.each(currentData, function (value, id) {
            if (blacklist.contains(id)) return;

            if (typeOf(this.rdomain[id]) == 'null') {
                this.rdomain[id] = '';
            }

            if (value + "" != this.rdomain[id]) {
                //logger(id+ ': '+value+' != '+this.rdomain[id]);
                hasUnsaved = true;
            }
        }.bind(this));

        return hasUnsaved;
    },


    prepareToLoadItem: function (pItem, pIsDomain) {

        var hasUnsaved = false;

        if (this.inDomainModus) {
            hasUnsaved = this.hasUnsavedDomainChanges();
        } else {
            hasUnsaved = this.hasUnsavedPageChanges();
        }

        Object.each(this.domainTrees, function (domain) {
            domain.unselect();
        });

        if (hasUnsaved) {

            this.win._confirm(_('There are unsaved data. Want to continue?'), function (pAccepted) {

                if (pAccepted) {
                    if (pIsDomain) {
                        this.loadDomain(pItem.id);
                    } else {
                        this.loadPage(pItem.id);
                    }
                } else {
                    //select old
                    Object.each(this.domainTrees, function (tree, domain_id) {
                        if (this.inDomainModus) {
                            if (domain_id == this.currentDomain.id) {
                                tree.select(0);
                            }
                        } else {
                            tree.select(this.page.id);
                        }
                    }.bind(this));
                }
            }.bind(this));
        } else {
            if (pIsDomain) {
                this.loadDomain(pItem.id)
            } else {
                this.loadPage(pItem.id);
            }
        }

    },

    loadTree: function (pOpts) {
        var _this = this;
        this.domainTrees = {};

        this.treeContainer.empty();

        if (!pOpts) {
            pOpts = {};
        }

        var openDomain = false;
        if (ka.settings.domains.length == 1) {
            openDomain = true;
        }

        ka.settings.domains.each(function (domain) {

            if (domain.lang != this.language) return;

            this.domainTrees[domain.id] = new ka.ObjectTree(this.treeContainer, 'node', {
                rootId: domain.id,

                onSelection: function (pPage, pObject) {
                    this.prepareToLoadItem(pPage, pObject.objectKey == 'node'?false:true);
                }.bind(this),

                withObjectAdd: true,
                onObjectAdd: this.pageAdd.bind(this)

            }, {win: this.win});

            /*this.domainTrees[domain.id] = new ka.pagesTree(_this.treeContainer, domain.id, {

                onClick: function (pPage) {
                    this.prepareToLoadItem(pPage);
                }.bind(this),

                onDomainClick: function (pDomain) {
                    this.prepareToLoadItem(pDomain, true);
                }.bind(this),

                onMoveComplete: function () {
                    if (this.page && this.page.domain_id == domain.id) {
                        this.loadAliases();
                    }
                }.bind(this),
                withPageAdd: true,
                onPageAdd: this.pageAdd.bind(this),
                withContext: true,
                openFirstLevel: openDomain,
                selectDomain: (pOpts.selectDomain == domain.id) ? true : false,
                selectPage: pOpts.selectPage
            }, {
                pageObj: this,
                win: this.win
            });
            */

            /*if (pOpts.selectDomain == domain.id) {
                this.loadDomain(domain);
            }*/

        }.bind(this));

    }

});
