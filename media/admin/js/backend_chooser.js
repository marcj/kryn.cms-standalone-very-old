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

        */
    },

    objectChooserInstance: {},
    pane2ObjectId: [],

    initialize: function (pWin) {
        this.win = pWin;

        this.setOptions(this.win.params);

        logger(this.options);
        this.value = this.win.params.value;
        this.p = _path + PATH_MEDIA + '/admin/images/';

        this.options.multi = (this.options.multi) ? true : false;

        this.cookie = (this.win.params.cookie) ? this.win.params.cookie : '';
        this.cookie = 'kFieldChooser_' + this.cookie + '_';

        this.bottomBar = this.win.addBottomBar();
        this.bottomBar.addButton(t('Close'), this.win.close.bind(this.win));
        this.bottomBar.addButton(t('Choose'), function(){this.choose();}.bind(this));

        this._createLayout();
    },

    saveCookie: function () {

        Cookie.write(this.cookie + 'lastTab', this.currentPane);
    },

    _createLayout: function () {

        this.tapPane = new ka.TabPane(this.win.content, true, this.win);

        this.tapPane.addEvent('change', this.changeTab.bind(this));
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

        var needDomainSelection = false;
        var needLanguageSelection = false;


        Object.each(ka.settings.configs, function(config, extKey){

            if (config.objects){
                Object.each(config.objects, function(object, objectKey){

                    if (this.options.objects && !this.options.objects.contains(objectKey)) return;

                    if (object.selectable){
                        this.createObjectChooser(objectKey);
                        if (object.multiLanguage)
                            needLanguageSelection = true;
                        if (object.domainDepended)
                            needDomainSelection = true;
                    }
                }.bind(this));
            }

        }.bind(this));


        var domainRight = 1;

        if (needLanguageSelection){
            this.sLanguage = new ka.Select(this.win.titleGroups);

            document.id(this.sLanguage).setStyles({
                'position': 'absolute',
                'right': 1,
                'top': 0,
                'width': 110
            });
            domainRight =+ 134;

            this.sLanguage.addEvent('change', this.changeLanguage.bind(this));

            this.sLanguage.add('', t('Unassigned language'));

            Object.each(ka.settings.langs, function (lang, id) {
                this.sLanguage.add(id, lang.langtitle + ' (' + lang.title + ', ' + id + ')');
            }.bind(this));
        }

        if (needDomainSelection){
            this.sDomain = new ka.Select(this.win.titleGroups);

            document.id(this.sDomain).setStyles({
                'position': 'absolute',
                'right': domainRight,
                'top': 0,
                'width': 110
            });

            this.sDomain.addEvent('change', this.changeDomain.bind(this));

            Object.each(ka.settings.domains, function (domain) {
                this.sDomain.add(domain.rsn, '['+domain.lang+'] '+ domain.domain);
            }.bind(this));
        }


        this.changeTab(0);
    },

    changeTab: function(pTabIndex){

        var objectKey = this.pane2ObjectId[pTabIndex];
        if (!objectKey) return;

        var definition = ka.getObjectDefinition(objectKey);

        if (definition.multiLanguage)
            this.sLanguage.setStyle('visibility', 'visible');
        else if (this.sLanguage)
            this.sLanguage.setStyle('visibility', 'hidden');

        if (definition.domainDepended)
            this.sDomain.setStyle('visibility', 'visible');
        else if (this.sDomain)
            this.sDomain.setStyle('visibility', 'hidden');


    },

    changeDomain: function(){



    },

    changeLanguage: function(){


    },

    createObjectChooser: function(pObjectKey){

        var objectDefinition = ka.getObjectDefinition(pObjectKey);

        var bundle = this.tapPane.addPane(objectDefinition.label, objectDefinition.chooser_icon);
        this.pane2ObjectId[bundle.id] = pObjectKey;

        var objectOptions = this.options.objectOptions[pObjectKey];

        if (this.options.objects.length == 1 && !objectOptions){
            objectOptions = this.options.objectOptions;
        }

        if (!objectOptions)
            objectOptions = {};

        objectOptions.multi = this.options.multi;

        if (objectDefinition.chooserBrowserType == 'custom' && objectDefinition.chooserBrowserJavascriptClass){

            var chooserClass = window[objectDefinition.chooserBrowserJavascriptClass];

            if (objectDefinition.chooserBrowserJavascriptClass.indexOf('.') !== false){

                var split = objectDefinition.chooserBrowserJavascriptClass.split('.');
                chooserClass = window;
                split.each(function(s){
                    chooserClass = chooserClass[s];
                })
            }

            if (!chooserClass){
                this.win._alert(t("Can't find chooser class '%class%' in object '%object%'.")
                    .replace('%class%', objectDefinition.chooserBrowserJavascriptClass)
                    .replace('%object%', pObjectKey)
                )
            } else {
                this.objectChooserInstance[pObjectKey] = new chooserClass(
                    bundle.pane,
                    objectOptions,
                    this.win
                );
            }
        } else {
            this.objectChooserInstance[pObjectKey] = new ka.AutoChooser(
                bundle.pane,
                objectOptions,
                this.win,
                pObjectKey
            );
        }

        if (this.objectChooserInstance[pObjectKey] && this.objectChooserInstance[pObjectKey].addEvent){

            this.objectChooserInstance[pObjectKey].addEvent('select', function(){
                this.deselectAll(pObjectKey);
            }.bind(this));

            this.objectChooserInstance[pObjectKey].addEvent('instantSelect', function(){
                this.choose(pObjectKey);
            }.bind(this));

        }

    },

    deselectAll: function(pWithoutThisObjectKey){

        Object.each(this.objectChooserInstance, function(obj, objectKey){

            if (obj && objectKey != pWithoutThisObjectKey && obj.deselect){
                obj.deselect();
            }

        });

    },

    choose: function(pObjectKey){

        if (!pObjectKey){
            pObjectKey = this.pane2ObjectId[this.tapPane.index];
        }

        if (pObjectKey && this.objectChooserInstance[pObjectKey] && this.objectChooserInstance[pObjectKey].getValue){

            var value = this.objectChooserInstance[pObjectKey].getValue();
            if (!value)
                return;

            var url = 'object://'+pObjectKey+'/'+ka.urlEncode(value);

            this.saveCookie();
            this.saveCookie();
            this.fireEvent('select', url);
            this.win.close();
        }
    }

});
