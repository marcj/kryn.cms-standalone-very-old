ka.AdminInterface = new Class({

    Implements: [Events, Options],

    mobile: false,

    removedMainMenuItems: [],

    _links: {},

    options: {
        frontPage: false
    },

    /**
     * Builds the login etc.
     */
    initialize: function(pOptions){

        this.setOptions(pOptions);

        if (this.isInit) return; else this.isInit = true;

        document.hiddenElement = new Element('div', {
            styles: {
                position: 'absolute',
                left: -154,
                top: -345,
                width: 1, height: 1, overflow: 'hidden'
            }
        }).inject(document.body);

        this.createLayout();

        if (!this.options.frontPage){
            this.renderLogin();
        }
    },

    createLayout: function(){

        if (this.options.frontPage){

            this.border = document.body;

        } else {
            this.border = new Element('div', {
                'class': 'ka-border ka-admin'
            }).inject(document.body);
        }

        this.header = new Element('div', {
            'class': 'ka-main-menu ka-admin'
        }).inject(this.border);

        this.headerInner = new Element('div', {
            'class': 'ka-main-menu-inner gradient'
        }).inject(this.header);

        this.headerInner.setStyle('top', -50);
        this.headerInner.setStyle('display', 'none');

        this.userNameBtn = new Element('a', {
            'class': 'ka-main-menu-login-name',
            href: 'javascript: ;'
        }).inject(this.headerInner);

        this.headerRight = new Element('div', {
            'class': 'ka-main-menu-additional'
        }).inject(this.headerInner);

        this.mainLinks = new Element('div',{
            'class': 'ka-mainLinks ka-scrolling'
        }).inject(this.headerInner);

        this.mainTempLinks = new Element('div', {
            'class': 'ka-mainTempLinks'
        }).inject(this.mainLinks);

        this.openFrontendBtnContainer = new Element('div', {
            'class': 'ka-iconbar-item'
        }).inject(this.headerRight);

        this.openFrontendBtn = new Element('a', {
            'class': 'icon-eye',
            title: t('Open Frontend'),
            href: 'javascript: ;'
        })
        .addEvent('click', function(){ ka.openFrontend(); })
        .inject(this.openFrontendBtnContainer);

        this.headerIconBar = new Element('div', {
            'class': 'ka-iconbar-item'
        }).inject(this.headerRight);

        this.openSearchIndexBtn = new Element('a', {
            'class': 'icon-search-8',
            title: t('Search engine index'),
            href: 'javascript: ;'
        })
        .addEvent('click', function(){ ka.openSearchContext(); } )
        .inject(this.headerIconBar);

        this.clearCacheBtn = new Element('a', {
            'class': 'icon-trashcan-6',
            title: t('Clear cache'),
            href: 'javascript: ;'
        })
        .addEvent('click', function(){ this.clearCache();}.bind(this) )
        .inject(this.headerIconBar);

        this.openHelpBtn = new Element('a', {
            'class': 'icon-info-5',
            title: t('Help'),
            href: 'javascript: ;'
        })
        .addEvent('click', function(){ ka.clearCache(); } )
        .inject(this.headerIconBar);

        if (this.options.frontPage){
            this.desktopContainer = new Element('div', {
                'class': 'ka-desktop ka-admin'
            }).inject(document.body);
        } else {
            this.desktopContainer = new Element('div', {
                'class': 'ka-desktop ka-admin'
            }).inject(this.border);
        }

    },

    isFrontPage: function(){
        return this.options.frontPage;
    },

    clearCache: function () {

        if (!this.cacheToolTip) {
            this.cacheToolTip = new ka.Tooltip(this.clearCacheBtn, t('Clearing cache ...'), 'top');
        }
        this.cacheToolTip.show();

        new Request.JSON({url: _path + 'admin/backend/cache', noCache: 1, onComplete: function (res) {
            this.cacheToolTip.stop(t('Cache cleared'));
        }.bind(this)}).delete();

    },

    /*
     * Build the administration interface after login
     */
    renderBackend: function () {

        //this.buildClipboardMenu();
        //this.buildUploadMenu();

        if (this.options.frontPage){
            document.body.addClass('ka-frontpage-backend');
        }

        if (!this.helpsystem)
            this.helpsystem = new ka.Helpsystem(document.body);

        if (this._iconSessionCounterDiv) {
            this._iconSessionCounterDiv.destroy();
        }
        this._iconSessionCounterDiv = new Element('div', {
            'class': 'ka-iconbar-item icon-users',
            title: t('Visitors')
        }).inject(this.headerRight);

        this._iconSessionCounter = new Element('span', {text: 0}).inject(this._iconSessionCounterDiv);

        if (!this.searchContainer) {


            this.searchContainerPadding = new Element('div', {
                'class': 'ka-search-padding'
            }).inject(this.headerRight);

            this.searchContainer = new Element('div', {
                'class': 'ka-search'
            }).inject(this.headerRight);

            this.searchInput = new ka.Field({
                type: 'text',
                noWrapper: true
            }, this.searchContainer);

            document.id(this.searchInput).addClass('ka-search-input');

            this.searchInput.addEvent('change', function() {
                if (this.searchInput.getValue() != '') {
                    this.doMiniSearch(this.searchInput.getValue());
                } else {
                    this.hideMiniSearch();
                }
            }.bind(this));

            this.searchIcon = new Element('img', {
                'class': 'ka-search-query-icon',
                src: 'admin/images/icon-search-loupe.png'
            }).inject(this.searchContainer);
        }

        window.fireEvent('init');

        if (this._crawler) {
            this._crawler.stop();
            delete this._crawler;
            this._crawler = new ka.Crawler();
        } else {
            this._crawler = new ka.Crawler();
        }

        //this.loadStream();

        window.onbeforeunload = function (evt) {

            if (ka.wm.getWindowsCount() > 0) {
                var message = _('There are open windows. Are you sure you want to leaving the administration?');
                if (typeof evt == 'undefined') {
                    evt = window.event;
                }
                if (evt) {
                    evt.returnValue = message;
                }
                return message;
            }
        };

        document.id(document.body).addEvent('contextmenu', function (e) {
            e = e || window.event;
            e.cancelBubble = true;
            e.returnValue = false;
            if (e.stopPropagation) e.stopPropagation();
            if (e.preventDefault) e.preventDefault();
            if (e.target) {
                document.id(e.target).fireEvent('mousedown', e);
            }
            return false;
        });

        window.addEvent('mouseup', function () {
            this.destroyLinkContext();
        }.bind(this));


        window.addEvent('stream', function (res) {
            document.id('serverTime').set('html', res.time);
            this._iconSessionCounter.set('text', res.sessions_count);
        });

        window.addEvent('stream', function (res) {
            if (res.corruptJson) {
                Array.each(res.corruptJson, function (item) {
                    this.helpsystem.newBubble(t('Extension config Syntax Error'), _('There is an error in your inc/module/%s/config.json').replace('%s', item), 4000);
                }.bind(this));
            }
        });
    },



    toggleMainbar: function () {
        if (this.border.getStyle('top').toInt() != 0) {
            this.border.tween('top', 0);
            document.id('arrowUp').setStyle('background-color', 'transparent');
            document.id('arrowUp').morph({
                'top': 0,
                left: 0
            });
        } else {
            this.border.tween('top', -76);
            document.id('arrowUp').setStyle('background-color', '#399BC7');
            document.id('arrowUp').morph({
                'top': 61,
                left: 32
            });
        }
    },

    doMiniSearch: function () {

        if (!this._miniSearchPane) {

            this._miniSearchPane = new Element('div', {
                'class': 'ka-mini-search'
            }).inject(this.border);

            this._miniSearchLoader = new Element('div', {
                'class': 'ka-mini-search-loading'
            }).inject(this._miniSearchPane);
            new Element('img', {
                src: _path + 'admin/images/ka-tooltip-loading.gif'
            }).inject(this._miniSearchLoader);
            new Element('span', {
                html: '<br/>'+t('Searching ...')
            }).inject(this._miniSearchLoader);
            this._miniSearchResults = new Element('div', {'class': 'ka-mini-search-results'}).inject(this._miniSearchPane);

        }

        this._miniSearchLoader.setStyle('display', 'block');
        this._miniSearchResults.set('html', '');

        if (this._lastTimer) clearTimeout(this._lastTimer);
        this._lastTimer = this._miniSearch.delay(500, this);

    },

    _miniSearch: function () {

        new Request.JSON({url: _path + 'admin/backend/search', noCache: 1, onComplete: function (pResponse) {
            this._miniSearchLoader.setStyle('display', 'none');
            this._renderMiniSearchResults(pResponse.data);
        }.bind(this)}).get({q: this.searchInput.getValue(), lang: window._session.lang});

    },

    _renderMiniSearchResults: function (pRes) {

        this._miniSearchResults.empty();

        if (typeOf(pRes) == 'object') {

            Object.each(pRes, function (subresults, subtitle) {
                var subBox = new Element('div').inject(this._miniSearchResults);

                new Element('h3', {
                    text: subtitle
                }).inject(subBox);

                var ol = new Element('ul').inject(subBox);
                Array.each(subresults, function (subsubresults, index) {
                    var li = new Element('li').inject(ol);
                    new Element('a', {
                        html: ' ' + subsubresults[0],
                        href: 'javascript: ;'
                    }).addEvent('click', function () {
                        ka.wm.open(subsubresults[1], subsubresults[2]);
                        this.hideMiniSearch();
                    }.bind(this)).inject(li);
                }.bind(this));
            }.bind(this));
        } else {
            new Element('span', {html: '<br/>'+t('No results') }).inject(this._miniSearchResults);
        }

    },


    hideMiniSearch: function () {
        if (this._miniSearchPane) {
            this._miniSearchPane.destroy();
            this._miniSearchPane = false;
        }
    },


    prepareLoader: function () {
        this._loader = new Element('div', {
            'class': 'ka-ai-loader'
        }).setStyle('opacity', 0).set('tween', {duration: 400}).inject(document.body);

        frames['content'].onload = function () {
            this.endLoading();
        };
        frames['content'].onunload = function () {
            this.startLoading();
        };
    },

    endLoading: function () {
        this._loader.tween('opacity', 0);
    },

    getDesktop: function(){
        return this.desktopContainer;
    },

    startLoading: function () {
        var co = this.desktopContainer;
        this._loader.setStyles(co.getCoordinates());
        this._loader.tween('opacity', 1);
    },

    renderLogin: function () {

        logger('!renderLogin');

        this.login = new Element('div', {
            'class': 'ka-login ka-admin'
        }).inject(document.body);

        new Element('div', {
            'class': 'ka-login-bg-pattern'
        }).inject(this.login);

        new Element('div', {
            'class': 'ka-login-bg-butterfly1'
        }).inject(this.login);

        new Element('div', {
            'class': 'ka-login-bg-butterflysmall1'
        }).inject(this.login);


        new Element('div', {
            'class': 'ka-login-bg-blue'
        }).inject(this.login);

        this.loginBgBlue = new Element('div', {
            'class': 'ka-login-spot-blue'
        }).inject(this.login);


        this.loginBgRed = new Element('div', {
            'class': 'ka-login-spot-red'
        }).inject(this.login);
        this.loginBgRed.setStyle('opacity', 0);

        this.loginBgGreen = new Element('div', {
            'class': 'ka-login-spot-green'
        }).inject(this.login);
        this.loginBgGreen.setStyle('opacity', 0);

        var middle = new Element('div', {
            'class': 'ka-login-middle'
        }).inject(this.login);
        this.middle = middle;

        this.middle.set('tween', {tranition: Fx.Transitions.Cubic.easeOut});

        new Element('img', {
            'class': 'ka-login-logo',
            src: _path+ 'admin/images/login-logo.png'
        }).inject(middle);

        new Asset.image(_path+ '/admin/images/login-spot-green.png');
        new Asset.image(_path+ '/admin/images/login-spot-red.png');

        var form = new Element('form', {
            id: 'loginForm',
            'class': 'ka-login-middle-form',
            action: './admin',
            autocomplete: 'off',
            method: 'post'
        }).addEvent('submit',
            function (e) {
                e.stop()
            }).inject(middle);
        this.loginForm = form;

        this.loginName = new Element('input', {
            name: 'loginName',
            'class': 'ka-login-input-username',
            type: 'text',
            placeholder: t('Username')
        })
        .addEvent('keyup',function (e) {
            if (e.key == 'enter') {
                this.doLogin();
            }
        }.bind(this)).inject(form);

        this.loginName.store('value', t('Username'));

        this.loginPw = new Element('input', {
            name: 'loginPw',
            type: 'password',
            'class': 'ka-login-input-passwd',
            placeholder: t('Password')
        }).addEvent('keyup', function (e) {
            if (e.key == 'enter') {
                this.doLogin();
            }
        }.bind(this)).inject(form);

        this.loginCircleBtn = new Element('div', {
            'class': 'ka-login-circlebtn'
        })
        .addEvent('click', function () {
            this.doLogin();
        }.bind(this))
        .inject(form);

        this.loginLangSelection = new ka.Select();

        this.loginLangSelection.chooser.addClass('ka-login-select-dark');
        this.loginLangSelection.inject(form);

        document.id(this.loginLangSelection).addClass('ka-login-select-login');

        this.loginLangSelection.addEvent('change', function () {
            this.loadLanguage(this.loginLangSelection.getValue());
            this.reloadLogin();
        }).inject(form);

        Object.each(ka.possibleLangs, function (lang) {
            this.loginLangSelection.add(lang.code, lang.title + ' (' + lang.langtitle + ')');
        }.bind(this));

        var ori = this.loginLangSelection.getValue();

        this.loginLangSelection.setValue(window._session.lang);

        this.loginMessage = new Element('div', {
            'class': 'loginMessage'
        }).inject(middle);

        var combatMsg = false;
        var fullBlock = Browser.ie && Browser.version == '6.0';

        //check browser compatibility
        //if (!Browser.Plugins.Flash.version){
            //todo
        //}

        if (combatMsg || fullBlock){
            this.loginBarrierTape = new Element('div', {
                'class': 'ka-login-barrierTape'
            }).inject(this.login);

            this.loginBarrierTapeContainer = new Element('div').inject(this.loginBarrierTape);
            var table = new Element('table', {
                width: '100%'
            }).inject(this.loginBarrierTapeContainer);
            var tbody = new Element('tbody').inject(table);
            var tr = new Element('tr').inject(tbody);
            this.loginBarrierTapeText = new Element('td', {
                valign: 'middle',
                text: combatMsg,
                style: 'height: 55px;'
            }).inject(tr);
        }

        //if IE6
        if (fullBlock){
            this.loginBarrierTape.addClass('ka-login-barrierTape-fullblock');
            this.loginBarrierTapeText.set('text', t('Holy crap. You really use Internet Explorer 6? You can not enjoy the future with this - stay out.'));
            new Element('div', {
                'class': 'ka-login-barrierTapeFullBlockOverlay',
                styles: {
                    opacity: 0.01
                }
            }).inject(this.login);
        }


        if (!Cookie.read('kryn_language')) {
            var possibleLanguage = navigator.browserLanguage || navigator.language;
            if (possibleLanguage.indexOf('-'))
                possibleLanguage = possibleLanguage.substr(0, possibleLanguage.indexOf('-'));

            if (ka.possibleLangs[possibleLanguage]){

                this.loginLangSelection.setValue(possibleLanguage);
                if (this.loginLangSelection.getValue() != window._session.lang) {
                    ka.loadLanguage(this.loginLangSelection.getValue());
                    this.reloadLogin();
                    return;
                }
            }
        }

        ka.loadLanguage(this.loginLangSelection.getValue());


        if (parent.inChrome && parent.inChrome()) {
            parent.doLogin();

        } else {
            if (_session.user_id > 0) {
                if (window._session.noAdminAccess){
                    this.loginFailed();
                } else {
                    this.loginSuccess(_session, true);
                }
            }
        }

    },

    reloadLogin: function () {

        if (this.loginName.value == '' || this.loginName.retrieve('value') == this.loginName.value)
            this.loginName.value = t('Username');

        this.loginName.store('value', t('Username'));
    },

    doLogin: function () {
        //todo, lock GUI

        logger('doLogin');

        this.loginMessage.set('html', t('Check Login. Please wait ...'));
        new Request.JSON({url: _path + 'admin/login', noCache: 1, onComplete: function (res) {
            if (res.data) {
                this.loginSuccess(res);
            } else {
                this.loginFailed();
            }
        }.bind(this)}).get({username: this.loginName.value, password: this.loginPw.value});
    },

    logout: function (pScreenlocker) {

        this.inScreenlockerMode = pScreenlocker;

        if (this.loaderCon) {
            this.loaderCon.destroy();
        }

        this.loginPw.value = '';

        this.middle.set('tween', {transition: Fx.Transitions.Cubic.easeOut});
        this.middle.tween('margin-top', this.middle.retrieve('oldMargin'));

        window.fireEvent('logout');

        if (!pScreenlocker) {
            ka.wm.closeAll();
            new Request({url: _path + 'admin/logout', noCache: 1}).get();
        }

        if (this.loader) {
            this.loader.destroy();
        }

        this.loginMessage.set('html', '');
        this.login.setStyle('display', 'block');

        [this.loginMessage, this.loginLangSelection]
            .each(function(i){document.id(i).setStyle('display', 'block')});

        this.loginLoadingBar.destroy();
        this.loginLoadingBarText.destroy();

        this.loginPw.value = '';
        this.loginPw.focus();
        window._session.user_id = 0;
    },

    loginSuccess: function (pResponse, pAlready) {

        logger('loginSuccess');

        this.border.setStyle('display', 'block');

        var b = new Fx.Tween(this.loginBgBlue, {duration: 500});
        var g = new Fx.Tween(this.loginBgGreen, {duration: 500});

        g.start('opacity', 1).chain(function(){
            this.start('opacity', 0)
        });
        b.start('opacity', 0).chain(function(){
            this.start('opacity', 1)
        });


        if (pAlready && window._session.hasBackendAccess == '0') {
            return;
        }

        if (pResponse.username) this.loginName.value = pResponse.username;

        window._session.username = this.loginName.value;

        window._sid = pResponse.token;
        window._session.sessionid = pResponse.token;
        window._user_id = pResponse.userId;

        if (this.logoutButton) {
            this.logoutButton.destroy();
            delete this.logoutButton;
        }

        if (this.editMeButton) {
            this.editMeButton.destroy();
            delete this.editMeButton;
        }

        this.userNameBtn.set('text', tf('Logged in as %s', window._session.username));

        this.editMeButton = new ka.Button(t('Edit me'))
            .addEvent('click', function(){
                ka.wm.open('users/users/editMe', {values: {id: window._user_id}});
            })
            .inject(this.userNameBtn, 'after');

        this.logoutButton = new ka.Button(t('Logout'))
            .addEvent('click', function(){
                this.logout();
            }.bind(this))
            .inject(this.userNameBtn, 'after');



        this.userNameBtn.onclick = function () {
            ka.wm.open('users/profile', {values: {id: pResponse.userId}});
        }

        window._session.user_id = pResponse.userId;
        window._session.lastlogin = pResponse.lastlogin;

        document.id(document.body).setStyle('background-position', 'center top');

        this.loginMessage.set('html', t('Please wait'));

        this.loadBackend();
    },

    loginFailed: function () {
        this.loginPw.focus();
        this.loginMessage.set('html', '<span style="color: red">' + _('Login failed') + '.</span>');
        (function () {
            this.loginMessage.set('html', '');
        }).delay(3000);


        var b = new Fx.Tween(this.loginBgBlue, {duration: 800});
        var r = new Fx.Tween(this.loginBgRed, {duration: 800});

        r.start('opacity', 1).chain(function(){
            (function(){this.start('opacity', 0)}).delay(2000,this)
        });
        b.start('opacity', 0).chain(function(){
            (function(){this.start('opacity', 1)}).delay(2000,this)
        });

    },


    loadBackend: function () {

        [this.loginMessage, this.loginLangSelection]
            .each(function(i){document.id(i).setStyle('display', 'none')});

        this.loginLoadingBar = new Element('div', {
            'class': 'ka-ai-loginLoadingBar',
            styles: {
                opacity: 0
            }
        }).inject(this.loginPw, 'after');

        this.loginLoadingBar.tween('opacity', 1);

        this.loginLoadingBarInside = new Element('div', {
            'class': 'ka-ai-loginLoadingBarInside',
            styles: {
                width: 1
            }
        }).inject(this.loginLoadingBar);

        this.loginLoadingBarInside.set('tween', {transition: Fx.Transitions.Sine.easeOut});

        this.loginLoadingBarText = new Element('div', {
            'class': 'ka-ai-loginLoadingBarText',
            html: _('Loading your interface')
        }).inject(this.loginForm);

        (function(){
            this.loginLoadingBarInside.tween('width', 80);

            this.loginLoaderStep2 = (function () {
                this.loginLoadingBarInside.tween('width', 178);
            }).delay(900, this);

            this.loginLoaderStep3 = (function () {
                this.loginLoadingBarInside.tween('width', 258);
            }).delay(1500, this);

            new Asset.css(_path + 'admin/css/style.css');
            new Asset.javascript(_path + 'admin/backend/js/script.js');
        }).delay(500, this);

    },

    loaderDone: function () {
        if (this.loginLoaderStep2) clearTimeout(this.loginLoaderStep2);
        if (this.loginLoaderStep3) clearTimeout(this.loginLoaderStep3);

        if (this.options.frontPage){

            ka.loadSettings();
            this.renderBackend();
            this.loadMenu();

        } else {
            this.loginLoadingBarText.set('html', t('Loading done'));
            this.loginLoadingBarInside.tween('width', 294);
            this.loadDone.delay(100, this);
        }
    },

    loadDone: function () {

        logger('loadDone');
        this.check4Updates.delay(2000, this);

        this.allFilesLoaded = true;
        //this.middle.store('oldMargin', this.middle.getStyle('margin-top'));
        //this.middle.set('tween', {transition: Fx.Transitions.Cubic.easeOut});
        //this.middle.tween('margin-top', -250);
        if (this.blender) this.blender.destroy();

        this.blender = new Element('div', {
            style: 'left: 0px; top: 0px; right: 0px; bottom: 0px; position: absolute; background-color: white; z-index: 15012300',
            styles: {
                opacity: 0
            }
        }).inject(document.body);

        this.blender.set('tween', {duration: 450});

        var self = this;

        new Fx.Tween(this.blender, {duration: 450})
        .start('opacity', 1).chain(function () {

            self.login.setStyle('display', 'none');

            //load settings, bg etc
            ka.loadSettings();
            self.renderBackend();
            self.loadMenu();

            //start checking for unindexed sites
            //checkSearchIndex.init();
            //start autocrawling process
            //system_searchAutoCrawler.init();

            var lastlogin = new Date();
            if (window._session.lastlogin > 0) {
                lastlogin = new Date(window._session.lastlogin * 1000);
            }
            if (self.helpsystem){
                self.helpsystem.newBubble(
                    t('Welcome back, %s').replace('%s', window._session.username),
                    t('Your last login was %s').replace('%s', lastlogin.format('%d. %b %I:%M')),
                    3000);
            }

            this.start('opacity', 0).chain(function(){
                self.blender.destroy();
            })

        });

    },

    toggleModuleMenuIn: function (pOnlyStay) {


        if (this.lastModuleMenuOutTimer) {
            clearTimeout(this.lastModuleMenuOutTimer);
        }

        if (this.ModuleMenuOutOpen == true) {
            return;
        }

        if (pOnlyStay == true) {
            return;
        }

        this.ModuleMenuOutOpen = false;
        this._moduleMenu.set('tween', {transition: Fx.Transitions.Quart.easeOut, onComplete: function () {
            this.ModuleMenuOutOpen = true;
        }});
        this._moduleMenu.tween('left', 0);
        this.moduleToggler.store('active', true);
        this.moduleItems.setStyle('right', 0);
        //this.moduleItemsScroller.setStyle('left', 188);
        //this.moduleItemsScrollerContainer.setStyle('right', 0);
    },

    toggleModuleMenuOut: function (pForce) {

        //if( !this.ModuleMenuOutOpen && pForce != true )
        //	return;

        if (this.lastModuleMenuOutTimer) {
            clearTimeout(this.lastModuleMenuOutTimer);
        }

        this.ModuleMenuOutOpen = false;

        this.lastModuleMenuOutTimer = (function () {
            this._moduleMenu.set('tween', {transition: Fx.Transitions.Quart.easeOut, onComplete: function () {
                this.ModuleMenuOutOpen = false;
            }});
            this._moduleMenu.tween('left', (this._moduleMenu.getSize().x - 33) * -1);
            this.moduleToggler.store('active', false);
            this.moduleItems.setStyle('right', 40);
            //this.moduleItemsScrollerContainer.setStyle('right', 50);
            this.destroyLinkContext();
        }).delay(300, this);

    },

    toggleModuleMenu: function () {
        if (this.moduleToggler.retrieve('active') != true) {
            this.toggleModuleMenuIn();
        } else {
            this.toggleModuleMenuOut();
        }
    },

    loadMenu: function () {

        if (this.lastLoadMenuReq) this.lastLoadMenuReq.cancel();

        this.lastLoadMenuReq = new Request.JSON({url: _path + 'admin/backend/menus', noCache: true, onComplete: function (res) {

            this.mainTempLinks.dispose();
            this.mainLinks.empty();
            this.mainTempLinks.inject(this.mainLinks);
            delete ka.wm.tempLinksSplitter;

            if (this.additionalMainMenu) {
                this.additionalMainMenu.destroy();
                this.additionalMainMenuContainer.destroy();
                delete this.additionalMainMenu;
            }

            this.removedMainMenuItems = [];
            delete this.mainMenuItems;

            var mlinks = res.data;

            new Element('div', {
                'class': 'ka-main-menu-splitter'
            }).inject(this.mainLinks, 'before');

            Object.each(mlinks['admin'], function (item, pCode) {
                this.addAdminLink(item, pCode, 'admin');
            }.bind(this));

            delete mlinks['admin'];

            if (mlinks['users']) {
                this.addAdminHeadline('users');
                Object.each(mlinks['users'], function (item, pCode) {
                    this.addAdminLink(item, pCode, 'users');
                }.bind(this));
            }
            delete mlinks['users'];

            Object.each(ka.settings.configs, function (config, extKey) {
                if (!mlinks[extKey]) return;

                this.addAdminHeadline(extKey);
                Object.each(mlinks[extKey], function (item, pCode) {
                    this.addAdminLink(item, pCode, extKey);
                }.bind(this));

            }.bind(this));

            this.needMainMenuWidth = false;

            this.headerInner.setStyle('display', 'block');
            if (document.referrer.search('_kryn_editor=1') === -1){
                this.headerInner.set('tween', {transition: Fx.Transitions.Bounce.easeOut});
                this.headerInner.tween('top', 0);
            } else {
                this.headerInner.setStyle('top', 0);
            }


        }.bind(this)}).get();
    },

    makeMenu: function (pToggler, pMenu, pCalPosition, pOffset) {


        pMenu.setStyle('display', 'none');

        var showMenu = function () {
            pMenu.setStyle('display', 'block');
            pMenu.store('this.makeMenu.canHide', false);

            if (pCalPosition) {
                var pos = pToggler.getPosition(this.border);
                if (pOffset) {
                    if (pOffset.x) {
                        pos.x += pOffset.x;
                    }
                    if (pOffset.y) {
                        pos.y += pOffset.y;
                    }
                }
                pMenu.setStyles({
                    'left': pos.x,
                    'top': pos.y
                });
            }
        };

        var _hideMenu = function () {
            if (pMenu.retrieve('this.makeMenu.canHide') != true) return;
            pMenu.setStyle('display', 'none');
        };

        var hideMenu = function () {
            pMenu.store('this.makeMenu.canHide', true);
            _hideMenu.delay(250);
        };

        pToggler.addEvent('mouseover', showMenu);
        pToggler.addEvent('mouseout', hideMenu);
        pMenu.addEvent('mouseover', showMenu);
        pMenu.addEvent('mouseout', hideMenu);

        //this.additionalMainMenu, this.additionalMainMenuContainer, true, {y: 80});
    },


    addAdminHeadline: function(pExtKey){
        var config = ka.settings.configs[pExtKey];
        if (config) {

            new Element('div', {
                'class': 'ka-main-menu-splitter'
            }).inject(this.mainLinks);

            new Element('h2', {
                'class': 'ka-main-menu-headline',
                text: config.title,
                title: config.desc ? config.desc : ''
            }).inject(this.mainLinks);
        }
    },

    addTempLink: function(pWin){

        var mlink = new Element('a', {
            text: (this.entryPoint ? this.entryPoint.title:'')+' » '+pWin.getTitle(),
            'class': 'ka-main-menu-item'
        }).inject(this.mainTempLinks, 'top');

        var entryPoint = pWin.getEntryPointDefinition();

        if (entryPoint.icon) {
            mlink.addClass('ka-main-menu-item-hasIcon');
            if (entryPoint.icon.substr(0,1) == '#'){
                mlink.addClass(entryPoint.icon.substr(1));
            } else {
                mlink.addClass('ka-main-menu-item-hasImageAsIcon');
                new Element('img', {
                    src: _path + entryPoint.icon
                }).inject(mlink, 'top');
            }
        } else {
            mlink.addClass('ka-main-menu-item-hasNoIcon');
        }

        mlink.activeWindowInformationContainer = new Element('div', {
            'class': 'ka-main-menu-item-window-information-container'
        }).inject(mlink);

        return mlink;
    },

    addAdminLink: function (pLink, pCode, pExtCode) {

        var mlink = false;

        if (!pLink.isLink) return;

        mlink = new Element('a', {
            text: pLink.title,
            'class': 'ka-main-menu-item'
        }).inject(this.mainLinks);

        mlink.activeWindowInformationContainer = new Element('div', {
            'class': 'ka-main-menu-item-window-information-container'
        }).inject(mlink);

        if (pLink.icon) {
            mlink.addClass('ka-main-menu-item-hasIcon');
            if (pLink.icon.substr(0,1) == '#'){
                mlink.addClass(pLink.icon.substr(1));
            } else {
                mlink.addClass('ka-main-menu-item-hasImageAsIcon');
                new Element('img', {
                    src: _path + pLink.icon
                }).inject(mlink, 'top');
            }
        } else {
            mlink.addClass('ka-main-menu-item-hasNoIcon');
        }

        var menu = new Element('div', {
            'class': 'ka-menu-item-children'
        }).inject(this.mainLinks);

        var hasActiveChilds = false;

        Object.each(pLink.children, function (item, code) {

            if (!item.isLink) return;

            hasActiveChilds = true;
            var sublink = new Element('a', {
                text: item.title,
                'class': 'ka-main-menu-item ka-module-items-deactivated'
            }).inject(menu);

            if (item.type) {
                sublink.removeClass('ka-module-items-deactivated');
                sublink.addEvent('click', function () {
                    ka.wm.openWindow(pExtCode + '/' + pCode + '/' + code, pLink);
                }.bind(this))
            }

            if (item.icon) {
                sublink.addClass('ka-main-menu-item-hasIcon');
                if (item.icon.substr(0,1) == '#'){
                    sublink.addClass(item.icon.substr(1));
                } else {
                    sublink.addClass('ka-main-menu-item-hasImageAsIcon');
                    new Element('img', {
                        src: _path + item.icon
                    }).inject(sublink, 'top');
                }
            } else {
                sublink.addClass('ka-main-menu-item-hasNoIcon');
            }

            sublink.activeWindowInformationContainer = new Element('div', {
                'class': 'ka-main-menu-item-window-information-container'
            }).inject(sublink);

            this._links[ pExtCode + '/' + pCode+'/'+code ] = {
                level: 'sub',
                object: sublink,
                link: item,
                module: pExtCode,
                code: pCode+'/'+code,
                path: pExtCode + '/' + pCode+'/'+code,
                title: item.title
            };

        }.bind(this));

        if (!hasActiveChilds){
            menu.destroy();
        } else {
            var childOpener;

            mlink.addClass('ka-menu-item-hasChilds');

            childOpener = new Element('a', {
                'class': 'ka-menu-item-childopener'
            }).inject(mlink);

            new Element('img', {
                src: _path+ 'admin/images/ka-mainmenu-item-tree_minus.png'
            }).inject(childOpener);

            childOpener.addEvent('click', function(e){
                e.stop();
                if (menu.getStyle('display') != 'block'){
                    menu.setStyle('display', 'block');
                } else {
                    menu.setStyle('display', 'none');
                }

            });


        }

        this._links[ pExtCode + '/' + pCode ] = {
            level: 'main',
            object: mlink,
            link: pLink,
            module: pExtCode,
            code: pCode,
            path: pExtCode + '/' + pCode,
            title: pLink.title
        };

        pLink.module = pExtCode;
        pLink.code = pCode;
        mlink.store('link', pLink);
        this.linkClick(mlink);


    },

    getMenuItem: function(pEntryPoint){
        return this._links[pEntryPoint];
    },


    destroyLinkContext: function () {

        if (this._lastLinkContextDiv) {
            this._lastLinkContextDiv.destroy();
            this._lastLinkContextDiv = null;
        }

    },

    linkClick: function (pLink) {
        var mlink = pLink.retrieve('link');

        if (['iframe', 'list', 'combine', 'custom', 'add', 'edit'].indexOf(mlink.type) != -1) {

            var link = this._links[mlink.module + '/' + mlink.code];

            pLink.getParent().addClass('ka-module-items-activated');

            pLink.addEvent('click', function (e) {
                this.destroyLinkContext();

                if (e.rightClick) return;
                e.stopPropagation();
                e.stop();

                var windows = [];
                Object.each(ka.wm.windows, function (pwindow) {
                    if (!pwindow) return;
                    if (pwindow.code == mlink.code && pwindow.module == mlink.module) {
                        windows.include(pwindow);
                    }
                }.bind(this));


                if (windows.length == 0) {
                    //none exists, just open
                    ka.wm.open(mlink.module+'/'+mlink.code);
                } else if (windows.length == 1) {
                    //only one is open, bring it to front
                    windows[0].toFront();
                } else if (windows.length > 1) {
                    //open contextmenu
                    e.stopPropagation();
                    e.stop();
                    this._openLinkContext(link);
                }

                delete windows;
            }.bind(this));

            pLink.addEvent('mouseup', function (e) {

                if (e.rightClick) {
                    e.stopPropagation();
                    this._openLinkContext(link);
                }
            }.bind(this));
        }
    },

    _openLinkContext: function (pLink) {

        if (this._lastLinkContextDiv) {
            this._lastLinkContextDiv.destroy();
            this._lastLinkContextDiv = null;
        }

        var pos = {x: 0, y: 0};
        var corner = false;

        var parent = pLink.object.getParent('.ka-module-menu');
        if (!parent) {
            parent = document.body;
        }
        var div = new Element('div', {
            'class': 'ka-linkcontext-main ka-linkcontext-sub'
        }).inject(parent);

        corner = new Element('div', {
            'class': 'ka-tooltip-corner-top',
            style: 'height: 15px; width: 30px;'
        }).inject(div);

        pos = pLink.object.getPosition(pLink.object.getParent('.ka-module-menu'));
        var size = pLink.object.getSize();

        div.setStyle('left', pos.x);
        div.setStyle('top', pos.y + size.y);
        if (pLink.level == 'main') {

            corner.setStyle('bottom', 'auto');
            corner.setStyle('top', -8);
        }

        this._lastLinkContextDiv = div;

        var windows = [];
        Object.each(ka.wm.windows, function (pwindow) {
            if (!pwindow) return;
            if (pwindow.code == pLink.code && pwindow.module == pLink.module) {
                windows.include(pwindow);
            }
        }.bind(this));

        var opener = new Element('a', {
            text: _('Open new %s').replace('%s', "'" + pLink.title + "'"),
            'class': 'ka-linkcontext-opener'
        }).addEvent('click',
            function () {
                ka.wm.openWindow(pLink.module + '/'+ pLink.code);
                this._lastLinkContextDiv.destroy();
            }).inject(div);

        if (windows.length == 0) {
            opener.addClass('ka-linkcontext-last');
        }

        var lastItem = false;
        windows.each(function (window) {
            lastItem = new Element('a', {
                text: '#' + window.id + ' ' + window.getTitle()
            }).addEvent('click',
                function () {
                    window.toFront();
                    this._lastLinkContextDiv.destroy();
                }).inject(div);
        });

        if (pLink.level == 'sub') {
            var bsize = div.getSize();
            var wsize = window.getSize();
            var mtop = div.getPosition(document.body).y;

            if (mtop + bsize.y > wsize.y) {
                mtop = pos.y - bsize.y;
                div.setStyle('top', mtop);
                corner.set('class', 'ka-tooltip-corner');
                corner.setStyle('bottom', '-15px');
            } else {
                corner.setStyle('top', '-7px');
            }
            if (lastItem) {
                lastItem.addClass('ka-linkcontext-last');
            }
        }

        delete windows;

    },


    startSearchCrawlerInfo: function (pHtml) {
        this.stopSearchCrawlerInfo();

        this.startSearchCrawlerInfoMenu = new Element('div', {
            'class': 'ka-updates-menu',
            style: 'left: 170px; width: 177px;'
        }).inject(this.border);

        this.startSearchCrawlerInfoMenuHtml = new Element('div', {
            html: pHtml
        }).inject(this.startSearchCrawlerInfoMenu);

        this.startSearchCrawlerProgressLine = new Element('div', {
            style: 'position: absolute; bottom: 1px; left: 4px; width: 0px; height: 1px; background-color: #444;'
        }).inject(this.startSearchCrawlerInfoMenu);

        this.startSearchCrawlerInfoMenu.tween('top', 48);
    },

    setSearchCrawlerInfo: function (pHtml) {
        this.startSearchCrawlerInfoMenuHtml.set('html', pHtml);
    },

    stopSearchCrawlerInfo: function (pOutroText) {
        if (!this.startSearchCrawlerInfoMenu) return;

        var doOut = function () {
            this.startSearchCrawlerInfoMenu.tween('top', 17);
        }.bind(this);

        if (pOutroText) {
            this.startSearchCrawlerInfoMenuHtml.set('html', pOutroText);
            doOut.delay(2000);
        } else {
            doOut.call();
        }

    },

    setSearchCrawlerProgress: function (pPos) {
        var maxLength = 177 - 8;
        var pos = maxLength * pPos / 100;
        this.startSearchCrawlerProgressLine.set('tween', {duration: 100});
        this.startSearchCrawlerProgressLine.tween('width', pos);
    },

    stopSearchCrawlerProgres: function () {
        this.startSearchCrawlerProgressLine.set('tween', {duration: 10});
        this.startSearchCrawlerProgressLine.tween('width', 0);
    },

    openSearchContextClose: function () {
        if (this.openSearchContextLast) {
            this.openSearchContextLast.destroy();
        }

    },

    openSearchContext: function () {

        var button = this.openSearchIndexBtn;

        this.openSearchContextClose();

        this.openSearchContextLast = new Element('div', {
            'class': 'ka-searchcontext'
        }).inject(this.border);

        var pos = button.getPosition(this.border);
        var size = this.border.getSize();
        var right = size.x - pos.x;

        this.openSearchContextLast.setStyle('right', right - 30);

        new Element('img', {
            'class': 'ka-searchcontext-arrow',
            src: _path + 'admin/images/ka-tooltip-corner-top.png'
        }).inject(this.openSearchContextLast);

        this.openSearchContextContent = new Element('div', {
            'class': 'ka-searchcontext-content'
        }).inject(this.openSearchContextLast);

        this.openSearchContextBottom = new Element('div', {
            'class': 'ka-searchcontext-bottom'
        }).inject(this.openSearchContextLast);

        new ka.Button(t('Indexed pages')).addEvent('click',
            function () {
                ka.wm.open('admin/system/searchIndexerList');
            }).inject(this.openSearchContextBottom);

        this.openSearchContextClearIndex = new ka.Button(_('Clear index')).addEvent('click',
            function () {
                this.openSearchContextClearIndex.startTip(_('Clearing index ...'));

                new Request.JSON({url: _path + 'admin/backend/searchIndexer/clearIndex', noCache: 1, onComplete: function (pRes) {
                    this.openSearchContextClearIndex.stopTip(_('Done'));
                }.bind(this)}).post();
            }).inject(this.openSearchContextBottom);

        new Element('a', {
            style: 'position: absolute; right: 5px; top: 3px; text-decoration: none; font-size: 13px;',
            text: 'x',
            title: _('Close'),
            href: 'javascript: ;'
        }).addEvent('click', this.openSearchContextClose).inject(this.openSearchContextLast);

        this.openSearchContextLoad();

    },


    openSearchContextLoad: function () {

        this.openSearchContextContent.set('html', '<br /><br /><div style="text-align: center; color: gray;">' + _('Loading ...') + '</div>');


        //todo
        this.openSearchContextTable = new ka.Table([
            [_('Domain'), 190],
            [_('Indexed pages')]
        ]);

        new Request.JSON({url: _path + 'admin/backend/searchIndexer/getIndexedPages4AllDomains',
            noCache: 1,
            onComplete: function (pRes) {

                this.openSearchContextContent.empty();

                this.openSearchContextTable.inject(this.openSearchContextContent);

                if (pRes) {
                    pRes.each(function (domain) {
                        this.openSearchContextTable.addRow([domain.domain + '<span style="color:gray"> (' + domain.lang + ')</span>', domain.indexedcount]);
                    });
                }

            }
        }.bind(this)).post();

    },


    displayNewUpdates: function (pModules) {
        if (this.newUpdatesMenu) {
            this.newUpdatesMenu.destroy();
        }

        var html = _('New updates !');
        /*
         pModules.each(function(item){
         html += item.name+' ('+item.newVersion+')<br />';
         });
         */
        this.newUpdatesMenu = new Element('div', {
            'class': 'ka-updates-menu',
            html: html
        })/*
         .addEvent('mouseover', function(){
         this.tween('height', this.scrollHeight );
         })
         .addEvent('mouseout', function(){
         this.tween('height', 24 );
         })
         */.addEvent('click',
            function () {
                ka.wm.open('admin/system/module', {updates: 1});
            }).inject(this.border);
        this.newUpdatesMenu.tween('top', 48);
    },

    buildClipboardMenu: function () {
        this.clipboardMenu = new Element('div', {
            'class': 'ka-clipboard-menu'
        }).inject(this.header, 'before');
    },

    buildUploadMenu: function () {
        this.uploadMenu = new Element('div', {
            'class': 'ka-upload-menu',
            styles: {
                height: 22
            }
        }).addEvent('mouseover',
            function () {
                this.tween('height', this.scrollHeight);
            }).addEvent('mouseout',
            function () {
                this.tween('height', 22);
            }).inject(this.header, 'before');

        this.uploadMenuInfo = new Element('div', {
            'class': 'ka-upload-menu-info'
        }).inject(this.uploadMenu);
    },



    check4Updates: function () {
        if (window._session.user_id == 0) return;
        new Request.JSON({url: _path + 'admin/system/module/manager/check-updates', noCache: 1, onComplete: function (res) {
            if (res && res.found) {
                this.displayNewUpdates(res.modules);
            }
            this.check4Updates.delay(10 * (60 * 1000), this);
        }.bind(this)}).get();
    }

});