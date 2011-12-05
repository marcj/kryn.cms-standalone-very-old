var admin_system_languages = new Class({

    initialize: function (pWin) {
        this.win = pWin;
        this._createLayout();
    },

    _createLayout: function () {

        this.win.content.empty();

        this.info = new Element('div', {
            style: 'position: absolute; left: 0px; right: 0px; top: 0px; height: 23px; border-bottom: 2px solid gray; padding: 4px; font-weight: bold; color: gray; text-align: center;',
            html: _('The native language is english. Do not translate the english language, unless you want to adjust some phrases.')
        }).inject(this.win.content);

        this.main = new Element('div', {
            'class': 'admin-system-languages-main'
        }).inject(this.win.content);

        this.topNavi = this.win.addSmallTabGroup();
        this.buttons = {};
        this.buttons['extensions'] = this.topNavi.addButton(_('Extensions'));
        this.buttons['extensions'].setPressed(true);

        this.languageSelect = new ka.Select();
        this.languageSelect.addEvent('change', this.loadOverview.bind(this));
        this.languageSelect.inject(this.win.titleGroups);
        this.languageSelect.setStyle('top', 2);

        Object.each(ka.settings.langs, function (lang, id) {
            this.languageSelect.add(id, lang.langtitle + ' (' + lang.title + ', ' + id + ')');
        }.bind(this));

        this.languageSelect.setValue(window._session.lang);

        this.loader = new ka.loader().inject(this.win.content);

        this.loadOverview();

    },

    loadOverview: function () {

        this.main.empty();

        this.extensionsDivs = {};
        this.progressBars = {};
        this.translateBtn = {};

        Object.each(ka.settings.configs, function(config,id){

            var title = config.title['en'];
            if( config.title[window._session.lang] ){
                title = config.title[window._session.lang];
            }

            new Element('h3', {
                text: title,
                style: 'font-weight:bold'
            }).inject( this.main );

            this.extensionsDivs[ id ] = new Element('div', {
                style: 'height: 38px; position: relative;'
            }).inject( this.main );
            this.renderExtensionOverview( id );

        }.bind(this));
    },

    renderExtensionOverview: function (pExtensionId) {
        var div = this.extensionsDivs[ pExtensionId ];
        div.empty();

        var left = new Element('div', {style: 'position: absolute; left: 5px; top: 10px; right: 90px;'}).inject( div );
        this.progressBars[pExtensionId] = new ka.Progress(_('Extracting ...'), true);
        this.progressBars[pExtensionId].inject( left );

        var right = new Element('div', {style: 'position: absolute; right: 10px; top: 5px;'}).inject( div )
        this.translateBtn[pExtensionId] = new ka.Button(_('Translate')).inject( right );
        this.translateBtn[pExtensionId].addEvent('click', function(){
            ka.wm.open('admin/system/languages/edit', {lang: this.languageSelect.getValue(), module: pExtensionId});
        }.bind(this));
        this.translateBtn[pExtensionId].deactivate();

        this.loadExtensionOverview(pExtensionId);

    },

    loadExtensionOverview: function(pExtensionId){

        this.lastRequests = new Request.JSON({url: _path+'admin/system/languages/overviewExtract', noCache:1,
        onComplete: function( pRes ){

            this.progressBars[pExtensionId].setUnlimited( false );
            this.progressBars[pExtensionId].setValue( (pRes.countTranslated/pRes.count)*100 );

            this.progressBars[pExtensionId].setText(
                _('%1 of %2 translated')
                    .replace('%1', pRes.countTranslated)
                    .replace('%2', pRes['count'])
            );

            this.translateBtn[pExtensionId].activate();
        }.bind(this)}).post({module: pExtensionId, lang: this.languageSelect.getValue()});

    }

    /*
    save: function () {
        var mods = {};
        this.loader.show();
        this.main.getElements('.system-language-mod').each(function (modDiv) {
            mods[ modDiv.lang ] = {};
            modDiv.getElements('tr').each(function (tr) {


                var rtl = tr.getElements('input')[0].checked;
                var value = tr.getElements('input')[1].value;

                if (rtl) {
                    value = [value, 1];
                }
                if (value != '') {
                    mods[ modDiv.lang ][ tr.getElements('td')[0].get('text') ] = value;
                }
            });
        });

        var req = {};
        req.langs = JSON.encode(mods);
        req.lang = this.languageSelect.value;

        new Request.JSON({url: _path + 'admin/system/languages/saveAllLanguages', onComplete: function () {
            this.loader.hide();
        }.bind(this)}).post(req);
    },

    loadLanguage: function () {
        this.loader.show();
        new Request.JSON({url: _path + 'admin/system/languages/getAllLanguages', noCache: 1, onComplete: function (res) {
            this._loadLanguage(res);
            this.loader.hide();
        }.bind(this)}).post({lang: this.languageSelect.value});

    },


    _loadLanguage: function (pLangs) {
        this.main.empty();
        $H(pLangs).each(function (mod, modKey) {

            var lang = window._session.lang;
            if (!mod.config) return;
            var title = ( mod.config.title[lang] ) ? mod.config.title[lang] : mod.config.title['en'];

            var h3 = new Element('h3', {
                text: title,
                style: 'cursor: pointer;'
            }).inject(this.main);

            var langDiv = new Element('div', {
                'class': 'system-language-mod',
                style: 'display: none; padding: 2px;',
                lang: modKey
            }).inject(this.main);

            var img = new Element('img', {
                src: _path + 'inc/template/admin/images/icons/tree_plus.png',
                style: 'position: relative; top: 1px; margin-right: 3px;',
                lang: 0
            }).addEvent('click',
                function (e) {
                    if (this.lang == 0) {
                        this.src = _path + 'inc/template/admin/images/icons/tree_minus.png';
                        this.lang = 1;
                    } else {
                        this.src = _path + 'inc/template/admin/images/icons/tree_plus.png';
                        this.lang = 0;
                    }
                    langDiv.setStyle('display', (this.lang == 0) ? 'none' : 'block');
                    if (e) {
                        e.stop();
                    }

                }).inject(h3, 'top');

            h3.addEvent('click', function (e) {
                img.fireEvent('click');
                e.stop();
            });

            var table = new Element('table', {
                cellpadding: 0, cellspacing: 0,
                width: '99%'
            }).inject(langDiv);
            var tbody = new Element('tbody').inject(table);

            this._renderLangs(mod.lang, tbody);

        }.bind(this));
    },

    _renderLangs: function (pLangs, pContainer) {
        $H(pLangs).each(function (lang, key) {

            var tr = new Element('tr', {'class': 'system-language-mod-item'}).inject(pContainer);
            new Element('td', {text: key, valign: 'top', width: '50%'}).inject(tr);

            var value = lang;
            var rtl = false;
            if ($type(value) == 'array') {
                value = value[0];
                rtl = true;
            }


            var tdMiddle = new Element('td', {width: 30}).inject(tr);
            new Element('input', {type: 'checkbox', title: _('RTL?'), checked: rtl}).inject(tdMiddle);

            var tdRight = new Element('td').inject(tr);
            new Element('input', {value: value, 'class': 'text', valign: 'top', style: 'width: 99%'}).inject(tdRight);
        });
    }
    */

    /*
     viewType: function( pType ){
     this.buttons.each(function(button,id){
     button.setPressed(false);
     }.bind(this));
     this.buttons[pType].setPressed(true);
     }*/

});
