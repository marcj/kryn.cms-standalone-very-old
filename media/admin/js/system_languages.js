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

        this.loader = new ka.Loader().inject(this.win.content);

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
});
