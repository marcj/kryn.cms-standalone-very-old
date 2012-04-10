ka.windowEdit = new Class({

    Implements: Events,

    inline: false,

    fieldToTabOIndex: {}, //index fieldkey to main-tabid

    winParams: {}, //copy of pWin.params in constructor

    initialize: function (pWin, pContainer) {
        this.win = pWin;

        this.winParams = Object.clone(this.win.params); //copy


        if (!this.winParams.item && this.winParams.values)
            this.winParams.item = this.winParams.values; //compatibility

        if (!pContainer) {
            this.container = this.win.content;
            this.container.setStyle('overflow', 'visible');
        } else {
            this.inline = true;
            this.container = pContainer;
        }

        this.bCheckClose = this.checkClose.bind(this);
        this.bCheckTabFieldWidth = this.checkTabFieldWidth.bind(this);

        this.win.addEvent('close', this.bCheckClose);
        this.win.addEvent('resize', this.bCheckTabFieldWidth);

        if (this.win.module && this.win.code)
            this.load();
    },

    destroy: function () {

        this.win.removeEvent('close', this.bCheckClose);
        this.win.removeEvent('resize', this.bCheckTabFieldWidth);

        if (this.languageTip){
            this.languageTip.stop();
            delete this.languageTip;
        }

        Object.each(this._buttons, function (button, id) {
            button.stopTip();
        });

        if (this.topTabGroup) {
            this.topTabGroup.destroy();
        }

        if (this.actionsNavi) {
            this.actionsNavi.destroy();
        }

        if (this.actionsNaviDel) {
            this.actionsNaviDel.destroy();
        }

        if (this.versioningSelect) {
            this.versioningSelect.destroy();
        }

        if (this.languageSelect) {
            this.languageSelect.destroy();
        }

        delete this.versioningSelect;
        delete this.languageSelect;

        this.container.empty();

    },

    load: function () {
        var _this = this;

        this.container.set('html', '<div style="text-align: center; padding: 50px; color: silver">'+t('Loading definition ...')+'</div>');

        new Request.JSON({url: _path + 'admin/' + this.win.module + '/' + this.win.code+'?cmd=getClassDefinition', noCache: true, onComplete: function (res) {
            this.render(res);
        }.bind(this)}).post();
    },

    generateItemParams: function (pVersion) {
        var req = {};

        if (pVersion) {
            req.version = pVersion;
        }

        if (this.winParams && this.winParams.item) {
            this.values.primary.each(function (prim) {
                req[ prim ] = this.winParams.item[prim];
            }.bind(this));
        }

        return req;
    },

    loadItem: function (pVersion) {
        var _this = this;
        var req = this.generateItemParams(pVersion);

        if (this.lastRq) {
            this.lastRq.cancel();
        }

        this.win.setLoading(true, null, {left: this.container.getStyle('left')});

        this.lastRq = new Request.JSON({url: _path + 'admin/' + this.win.module + '/' + this.win.code + '?cmd=getItem',
        noCache: true, onComplete: function (res) {
            this._loadItem(res);
        }.bind(this)}).post(req);
    },

    _loadItem: function (pItem) {
        this.item = pItem;

        this.previewUrls = pItem.preview_urls;

        var first = false;

        Object.each(this.fields, function (field, fieldId) {

            if (first == false && typeOf(pItem.values[fieldId]) == 'string') {
                this.win.setTitle(pItem.values[fieldId]);
                first = true;
            }
            try {

                if (typeOf(pItem.values[fieldId]) == 'null') {
                    field.setValue('');

                } else if (!field.field.startempty) {
                    field.setValue(pItem.values[fieldId]);
                }

                if (field.field.depends) {
                    field.fireEvent('change', field.getValue());
                }

            } catch (e) {
                //logger( "Error with "+fieldId+": "+e);
            }
        }.bind(this));

        if (this.values.multiLanguage && this.languageSelect.getValue() != this.item.values.lang) {
            this.languageSelect.setValue(this.item.values.lang);
            this.changeLanguage();
        }

        this.renderVersionItems();

        this.win.setLoading(false);
        this.fireEvent('load', pItem);

        this.ritem = this.retrieveData(true);
    },

    renderPreviews: function () {

        if (!this.values.previewPlugins) {
            return;
        }

        //this.previewBtn;

        this.previewBox = new Element('div', {
            'class': 'ka-Select-chooser'
        });

        this.previewBox.addEvent('click', function (e) {
            e.stop();
        });

        var target = this.container.getParent('.kwindow-border');
        this.previewBox.inject(target);

        this.previewBox.setStyle('display', 'none');

        //this.values.previewPlugins

        document.body.addEvent('click', this.closePreviewBox.bind(this));

        if (!this.values.previewPluginPages) {
            return;
        }

        Object.each(this.values.previewPlugins, function (item, pluginId) {

            var title = ka.settings.configs[this.win.module].plugins[pluginId][0];


            new Element('div', {
                html: title,
                href: 'javascript:;',
                style: 'font-weight:bold; padding: 3px; padding-left: 15px;'
            }).inject(this.previewBox);

            var index = pluginId;
            if (pluginId.indexOf('/') === -1) {
                index = this.win.module + '/' + pluginId;
            }

            Object.each(this.values.previewPluginPages[index], function (pages, domain_rsn) {

                Object.each(pages, function (page, page_rsn) {

                    var domain = ka.getDomain(domain_rsn);
                    if (domain) {
                        new Element('a', {
                            html: '<span style="color: gray">[' + domain.lang + ']</span> ' + page.path,
                            style: 'padding-left: 21px',
                            href: 'javascript:;'
                        }).addEvent('click', this.doPreview.bind(this, [page_rsn, index])).inject(this.previewBox);
                    }


                }.bind(this));

            }.bind(this));

        }.bind(this));

    },

    preview: function (e) {
        this.togglePreviewBox(e);
    },

    doPreview: function (pPageRsn, pPluginId) {
        this.closePreviewBox();

        if (this.lastPreviewWin) {
            this.lastPreviewWin.close();
        }

        var url = this.previewUrls[pPluginId][pPageRsn];

        if (this.versioningSelect.getValue() != '-') {
            url += '?kryn_framework_version_id=' + this.versioningSelect.getValue() + '&kryn_framework_code=' + pPluginId;
        }

        this.lastPreviewWin = window.open(url, '_blank');

    },

    setPreviewValue: function () {
        this.closePreviewBox();
    },

    closePreviewBox: function () {
        this.previewBoxOpened = false;
        this.previewBox.setStyle('display', 'none');
    },

    togglePreviewBox: function (e) {

        if (this.previewBoxOpened == true) {
            this.closePreviewBox();
        } else {
            if (e && e.stop) {
                document.body.fireEvent('click');
                e.stop();
            }
            this.openPreviewBox();
        }
    },

    openPreviewBox: function () {

        this.previewBox.setStyle('display', 'block');

        this.previewBox.position({
            relativeTo: this.previewBtn,
            position: 'bottomRight',
            edge: 'upperRight'
        });

        var pos = this.previewBox.getPosition();
        var size = this.previewBox.getSize();

        var bsize = window.getSize($('desktop'));

        if (size.y + pos.y > bsize.y) {
            this.previewBox.setStyle('height', bsize.y - pos.y - 10);
        }

        this.previewBoxOpened = true;
    },

    loadVersions: function () {

        var req = this.generateItemParams();
        new Request.JSON({url: _path + 'admin/' + this.win.module + '/' + this.win.code + '?cmd=getItem', noCache: true, onComplete: function (res) {

            if (res && res.versions) {
                this.item.versions = res.versions;
                this.renderVersionItems();
            }

        }.bind(this)}).post(req);

    },

    renderVersionItems: function () {
        if (this.values.versioning != true) return;

        this.versioningSelect.empty();
        this.versioningSelect.chooser.setStyle('width', 210);
        this.versioningSelect.add('-', _('-- LIVE --'));

        /*new Element('option', {
         text: _('-- LIVE --'),
         value: ''
         }).inject( this.versioningSelect );*/

        if ($type(this.item.versions) == 'array') {
            this.item.versions.each(function (version, id) {
                this.versioningSelect.add(version.version, version.title);
            }.bind(this));
        }

        if (this.item.version) {
            this.versioningSelect.setValue(this.item.version);
        }

    },

    render: function (pValues) {
        this.values = pValues;

        this.container.empty();

        this.win.setLoading(true, null, {left: 265});

        this.fields = {};

        this.renderMultilanguage();

        this.renderVersions();

        this.renderPreviews();

        this.renderFields();

        this.renderSaveActionBar();

        this.fireEvent('render');

        if (this.winParams){
            this.loadItem();
        }
    },

    renderFields: function () {

        if (this.values.fields && typeOf(this.values.fields) != 'array') {

            this.form = new Element('div', {
                'class': 'ka-windowEdit-form'
            }).inject(this.container);

            if (this.values.layout) {
                this.form.set('html', this.values.layout);
            }

            var parser = new ka.parse(this.form, this.values.fields, {tabsInWindowHeader: 1}, {win: this.win});
            this.fields = parser.getFields();

            this._buttons = parser.getTabButtons();

            if (parser.firstLevelTabBar)
                this.topTabGroup = parser.firstLevelTabBar.buttonGroup;

        } else if (this.values.tabFields) {
            //backward compatible

            this.topTabGroup = this.win.addSmallTabGroup();

            this._panes = {};
            this._buttons = {};
            this.firstTab = '';
            this.fields = {};

            Object.each(this.values.tabFields, function (fields, title) {

                if (this.firstTab == '') this.firstTab = title;

                this._panes[ title ] = new Element('div', {
                    'class': 'ka-windowEdit-form',
                    style: 'display: none;'
                }).inject(this.container);

                //backward compatibility
                if (this.values.tabLayouts && this.values.tabLayouts[title]) {
                    this._panes[title].set('html', this.values.tabLayouts[title]);
                }

                //this._renderFields( fields, this._panes[ title ] );

                var parser = new ka.parse(this._panes[ title ], fields, {}, {win: this.win});
                var pfields = parser.getFields();
                Object.append(this.fields, pfields);

                this._buttons[ title ] = this.topTabGroup.addButton(t(title), this.changeTab.bind(this, title));
            }.bind(this));
            this.changeTab(this.firstTab);
        }


        //generate index, fieldkey => main-tabid
        Object.each(this.values.fields, function(item, key){
            if (item.type == 'tab')
                this.setFieldToTabIdIndex(item.depends, key);
        }.bind(this));


        //generate index, fieldkey => main-tabid
        Object.each(this.values.tabFields, function(items, key){
            this.setFieldToTabIdIndex(items, key);
        }.bind(this));


    },

    setFieldToTabIdIndex: function(childs, tabId){
        Object.each(childs, function(item, key){
            this.fieldToTabOIndex[key] = tabId;
            if (item.depends){
                this.setFieldToTabIdIndex(item.depends, tabId);
            }
        }.bind(this));
    },

    renderVersions: function () {

        if (this.values.versioning == true) {

            /*this.versioningSelect = new Element('select', {
             style: 'position: absolute; right: '+versioningSelectRight+'px; top: 27px; width: 160px;'
             }).inject( this.win.border );*/


            var versioningSelectRight = 5;
            if (this.values.multiLanguage) {
                versioningSelectRight = 150;
            }

            this.versioningSelect = new ka.Select();
            this.versioningSelect.inject(this.win.titleGroups);
            this.versioningSelect.setStyle('width', 120);
            this.versioningSelect.setStyle('top', 0);
            this.versioningSelect.setStyle('right', versioningSelectRight);
            this.versioningSelect.setStyle('position', 'absolute');

            this.versioningSelect.addEvent('change', this.changeVersion.bind(this));

        }

    },

    renderMultilanguage: function () {

        if (this.values.multiLanguage) {
            this.win.extendHead();

            this.languageSelect = new ka.Select();
            this.languageSelect.inject(this.win.titleGroups);
            this.languageSelect.setStyle('width', 120);
            this.languageSelect.setStyle('top', 0);
            this.languageSelect.setStyle('right', 5);
            this.languageSelect.setStyle('position', 'absolute');


            this.languageSelect.addEvent('change', this.changeLanguage.bind(this));

            this.languageSelect.add('', _('-- Please Select --'));

            Object.each(ka.settings.langs, function (lang, id) {

                this.languageSelect.add(id, lang.langtitle + ' (' + lang.title + ', ' + id + ')');

            }.bind(this));

            if (this.winParams && this.winParams.item) {
                this.languageSelect.setValue(this.winParams.item.lang);
            }

        }

    },

    changeVersion: function () {
        var value = this.versioningSelect.getValue();
        if (value == '-') {
            value = null;
        }

        this.loadItem(value);
    },

    changeLanguage: function () {
        Object.each(this.fields, function (item, fieldId) {

            if (item.field.type == 'select' && item.field.multiLanguage) {
                item.field.lang = this.languageSelect.getValue();
                item.renderItems();
            }
        }.bind(this));


        if (this.languageTip && this.languageSelect.getValue() != ''){
            this.languageTip.stop();
            delete this.languageTip;
        }
    },

    changeTab: function (pTab) {
        this.currentTab = pTab;
        Object.each(this._buttons, function (button, id) {
            button.setPressed(false);
            this._panes[ id ].setStyle('display', 'none');
        }.bind(this));
        this._panes[ pTab ].setStyle('display', 'block');
        this._buttons[ pTab ].setPressed(true);

        this._buttons[ pTab ].stopTip();
    },

    renderSaveActionBar: function () {
        var _this = this;


        this.actionsNavi = this.win.addButtonGroup();

        this.saveBtn = this.actionsNavi.addButton(_('Save'), _path + 'inc/template/admin/images/button-save.png', function () {
            this._save();
        }.bind(this));

        if (this.values.previewPlugins) {
            this.previewBtn = this.actionsNavi.addButton(_('Preview'), _path + 'inc/template/admin/images/icons/eye.png', this.preview.bindWithEvent(this));
        }

        if (this.values.versioning == true) {
            this.saveAndPublishBtn = this.actionsNavi.addButton(_('Save and publish'), _path + 'inc/template/admin/images/button-save-and-publish.png', function () {
                _this._save(false, true);
            }.bind(this));
        }

        this.checkTabFieldWidth();
    },

    checkTabFieldWidth: function(){

        if (!this.topTabGroup) return;

        if (!this.cachedTabItems)
            this.cachedTabItems = document.id(this.topTabGroup).getElements('a');

        var actionsMaxLeftPos = 5;
        if (this.versioningSelect)
            actionsMaxLeftPos += document.id(this.versioningSelect).getSize().x+10

        if (this.languageSelect)
            actionsMaxLeftPos += document.id(this.languageSelect).getSize().x+10

        var actionNaviWidth = this.actionsNavi ? document.id(this.actionsNavi).getSize().x : 0;

        var fieldsMaxWidth = this.win.titleGroups.getSize().x - actionNaviWidth - 17 - 20 -
                             (actionsMaxLeftPos + document.id(this.topTabGroup).getPosition(this.win.titleGroups).x);


        if (this.tooMuchTabFieldsButton)
            this.tooMuchTabFieldsButton.destroy();

        this.cachedTabItems.removeClass('ka-tabGroup-item-last');
        this.cachedTabItems.inject(document.hidden);
        this.cachedTabItems[0].inject(document.id(this.topTabGroup));
        var curWidth = this.cachedTabItems[0].getSize().x;

        var itemCount = this.cachedTabItems.length-1;

        if (!this.overhangingItemsContainer)
            this.overhangingItemsContainer = new Element('div', {'class': 'ka-windowEdit-overhangingItemsContainer'});

        var removeTooMuchTabFieldsButton = false, atLeastOneItemMoved = false;

        this.cachedTabItems.each(function(button,id){
            if (id == 0) return;

            curWidth += button.getSize().x;
            if ((curWidth < fieldsMaxWidth && id < itemCount) || (id == itemCount && curWidth < fieldsMaxWidth+20)) {
                button.inject(document.id(this.topTabGroup));
            } else {
                atLeastOneItemMoved = true;
                button.inject(this.overhangingItemsContainer);
            }

        }.bind(this));

        this.cachedTabItems.getLast().addClass('ka-tabGroup-item-last');

        if (atLeastOneItemMoved){

            this.tooMuchTabFieldsButton = new Element('a', {
                'class': 'ka-tabGroup-item ka-tabGroup-item-last'
            }).inject(document.id(this.topTabGroup));

            new Element('img', {
                src: _path+'inc/template/admin/images/ka.mainmenu-additional.png',
                style: 'left: 1px; top: 6px;'
            }).inject(this.tooMuchTabFieldsButton);

            this.tooMuchTabFieldsButton.addEvent('click', function(){
                if (!this.overhangingItemsContainer.getParent()){
                    this.overhangingItemsContainer.inject(this.win.border);
                    ka.openDialog({
                        element: this.overhangingItemsContainer,
                        target: this.tooMuchTabFieldsButton,
                        offset: {y: 0, x: 1}
                    });

                    /*ka.openDialog({
                        element: this.chooser,
                        target: this.box,
                        onClose: this.close.bind(this)
                    });*/
                }
            }.bind(this));

        } else {

            this.cachedTabItems.getLast().addClass('ka-tabGroup-item-last');
        }

    },

    removeTooltip: function(){
        this.stopTip();
        this.removeEvent('click', this.removeTooltip);
    },

    retrieveData: function (pWithoutEmptyCheck) {

        var go = true;
        var req = {};

        Object.each(this.fields, function (item, fieldId) {

            if (!instanceOf(item, ka.field)) return;

            if (['window_list'].contains(item.type)) return;

            if (!pWithoutEmptyCheck && !item.isHidden() && !item.isOk()) {

                var properTabKey = this.fieldToTabOIndex[fieldId];
                if (!properTabKey) return;
                var tabButton = this.fields[properTabKey];

                if (tabButton && !tabButton.isPressed()){

                    tabButton.startTip(t('Please fill!'));
                    tabButton.toolTip.loader.set('src', _path + 'inc/template/admin/images/icons/error.png');
                    tabButton.toolTip.loader.setStyle('position', 'relative');
                    tabButton.toolTip.loader.setStyle('top', '-2px');
                    document.id(tabButton.toolTip).setStyle('top', document.id(tabButton.toolTip).getStyle('top').toInt()+2);

                    tabButton.addEvent('click', this.removeTooltip);
                } else {
                    tabButton.stopTip();
                }

                item.highlight();

                go = false;
            }
            var value = item.getValue();

            if (item.field.relation == 'n-n') {
                req[ fieldId ] = JSON.encode(value);
            } else if (typeOf(value) == 'object') {
                req[ fieldId ] = JSON.encode(value);
            } else {
                req[ fieldId ] = value;
            }

        }.bind(this));

        if (this.values.multiLanguage) {
            if (!pWithoutEmptyCheck && this.languageSelect.getValue() == ''){

                if (!this.languageTip){
                    this.languageTip = new ka.tooltip(this.languageSelect, _('Please fill!'), null, null,
                        _path + 'inc/template/admin/images/icons/error.png');
                }
                this.languageTip.show();

                return false;
            } else if (!pWithoutEmptyCheck && this.languageTip){
                this.languageTip.stop();
            }
            req['lang'] = this.languageSelect.getValue();
        }

        if (go == false) {
            return false;
        }
        return req;

    },

    hasUnsavedChanges: function () {

        if (!this.ritem) return false;

        var currentData = this.retrieveData(true);
        if (!currentData) return true;

        var hasUnsaved = false;

        var blacklist = [];

        Object.each(currentData, function (value, id) {
            if (blacklist.contains(id)) return;

            if (typeOf(this.ritem[id]) == 'null') {
                this.ritem[id] = '';
            }
            if (typeOf(value) == 'null') {
                value = '';
            }

            if (value + "" != this.ritem[id]) {
                hasUnsaved = true;
            }
        }.bind(this));

        return hasUnsaved;
    },

    checkClose: function () {

        var hasUnsaved = this.hasUnsavedChanges();


        if (hasUnsaved) {
            this.win.interruptClose = true;
            this.win._confirm(_('There are unsaved data. Want to continue?'), function (pAccepted) {
                if (pAccepted) {
                    this.win.close();
                }
            }.bind(this));
        } else {
            this.win.close();
        }

    },

    _save: function (pClose, pPublish) {
        var go = true;
        var _this = this;
        var req = {};

        var data = this.retrieveData();

        if (!data) return;

        this.ritem = data;

        if (this.item) {
            req = Object.merge(this.item, data);
        } else {
            req = data;
        }

        req.publish = (pPublish == true) ? 1 : 0;

        if (go) {

            if (pPublish) {
                this.saveAndPublishBtn.startTip(_('Save ...'));
            } else {
                this.saveBtn.startTip(_('Save ...'));
            }

            if (_this.win.module == 'users' && (_this.win.code == 'users/edit/' || _this.win.code == 'users/edit' ||
                                                _this.win.code == 'users/editMe' || _this.win.code == 'users/editMe/')
                ) {
                if (!ka.settings['user']) ka.settings['user'] = {};
                ka.settings['user']['adminLanguage'] = req['adminLanguage'];
            }

            if (this.winParams && this.winParams.item) {

                if (!this.windowAdd) {
                    this.values.primary.each(function (prim) {
                        req[ prim ] = this.winParams.item[prim];
                    }.bind(this));
                }

            }

            new Request.JSON({url: _path + 'admin/' + this.win.module + '/' + this.win.code + '?cmd=saveItem', noCache: true, onComplete: function (res) {

                window.fireEvent('softReload', this.win.module + '/' + this.win.code.substr(0, this.win.code.lastIndexOf('/')));

                if (pPublish) {
                    this.saveAndPublishBtn.stopTip(_('Saved'));
                } else {
                    this.saveBtn.stopTip(_('Saved'));
                }


                if (!pClose && this.saveNoClose) {
                    this.saveNoClose.stopTip(_('Done'));
                }

                if (res.version_rsn) {
                    this.item.version = res.version_rsn;
                }

                if (this.values.loadSettingsAfterSave == true) ka.loadSettings();
                if (this.values.load_settings == true) ka.loadSettings();

                this.previewUrls = res.preview_urls;

                this.fireEvent('save', [req, res, pPublish]);

                // Before close, perform saveSuccess
                this._saveSuccess();

                if ((!pClose || this.inline ) && this.values.versioning == true) this.loadVersions();

                if (pClose) {
                    this.win.close();
                }

            }.bind(this)}).post(req);
        }
    },

    _saveSuccess: function () {
    }

});