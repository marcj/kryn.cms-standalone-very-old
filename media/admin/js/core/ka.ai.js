
if (typeof window.ka == 'undefined') {
    window.ka = {};
}
window.kaExist = true;

window.ka.ai = {};

document.addEvent('touchmove', function (event) {
    event.preventDefault();
});

if (typeOf(ka.langs) != 'object') ka.langs = {};

window.logger = function(){
    if (typeOf(console) != "undefined") {
        var args = arguments;
        if (args.length == 1) args = args[0];
        console.log(args);
    }
};

ka.openFrontend = function () {
    if (top) {
        top.open(_path, '_blank');
    }
};

ka.mobile = false;



/*
 * Build the administration interface after login
 */
ka.init = function () {

    //ka.buildClipboardMenu();
    ka.buildUploadMenu();

    if (!document.body.hasClass('ka-no-desktop')){
        if (!ka.desktop)
            ka.desktop = new ka.Desktop(document.id('desktop'));

        ka.desktop.load();
    }

    if (!ka.helpsystem)
        ka.helpsystem = new ka.Helpsystem(document.id('desktop'));


    if (ka._iconSessionCounterDiv) {
        ka._iconSessionCounterDiv.destroy();
    }
    ka._iconSessionCounterDiv = new Element('div', {
        'class': 'iconbar-item icon-users',
        title: t('Visitors')
    }).inject(document.id('iconbar'));

    ka._iconSessionCounter = new Element('span', {text: 0}).inject(ka._iconSessionCounterDiv);


    window.addEvent('resize', ka.checkMainBarWidth);
    ka.buildUserMenu();

    window.fireEvent('init');

    if (ka._crawler) {
        ka._crawler.stop();
        delete ka._crawler;
        ka._crawler = new ka.Crawler();
    } else {
        ka._crawler = new ka.Crawler();
    }

    //ka.loadStream();

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

    $(document.body).addEvent('contextmenu', function (e) {
        e = e || window.event;
        e.cancelBubble = true;
        e.returnValue = false;
        if (e.stopPropagation) e.stopPropagation();
        if (e.preventDefault) e.preventDefault();
        if (e.target) {
            $(e.target).fireEvent('mousedown', e);
        }
        return false;
    });

    window.addEvent('mouseup', function () {
        ka.destroyLinkContext();
    });
};


window.addEvent('stream', function (res) {
    $('serverTime').set('html', res.time);
    ka._iconSessionCounter.set('text', res.sessions_count);
});

window.addEvent('stream', function (res) {
    if (res.corruptJson) {
        Array.each(res.corruptJson, function (item) {
            ka.helpsystem.newBubble(t('Extension config Syntax Error'), _('There is an error in your inc/module/%s/config.json').replace('%s', item), 4000);
        });
    }
});



ka.toggleMainbar = function () {
    if ($('border').getStyle('top').toInt() != 0) {
        $('border').tween('top', 0);
        $('arrowUp').setStyle('background-color', 'transparent');
        $('arrowUp').morph({
            'top': 0,
            left: 0
        });
    } else {
        $('border').tween('top', -76);
        $('arrowUp').setStyle('background-color', '#399BC7');
        $('arrowUp').morph({
            'top': 61,
            left: 32
        });
    }
}


ka.buildUserMenu = function(){
    if (ka.userMenu) ka.userMenu.destroy();

    ka.userMenu = new Element('div', {
        'class': 'bar-dock-menu-style',
        style: 'right: 36px;'
    }).inject(document.id('border'))

    new Element('a', {
        text: t('Edit me')
    })
        .addEvent('click', function(){
            ka.wm.open('users/users/editMe', {values: {id: window._user_id}});
        })
        .inject(ka.userMenu);

    new Element('a', {
        text: t('Logout')
    })
        .addEvent('click', function(){
            ka.ai.logout();
        })
        .inject(ka.userMenu);

    var xoffset = ka.userMenu.getSize().x;
    xoffset -= document.id('user-username').getSize().x;
    xoffset *= -1;

    ka.makeMenu(document.id('user-username'), ka.userMenu, true, {y: 46, x: xoffset+2});
}

/**
 * Initialize the webApp.
 * Show the login etc.
 */
window.addEvent('domready', function () {


    document.hidden = new Element('div', {
        styles: {
            position: 'absolute',
            left: -154,
            top: -345,
            width: 1, height: 1, overflow: 'hidden'
        }
    }).inject(document.body);

    
    window.ka.ai.renderLogin();

    $('ka-search-query').addEvent('keyup', function (e) {
        if (this.value != '') {
            ka.doMiniSearch(this.value);
        } else {
            ka.hideMiniSearch();
        }
    });


    if (parent.inChrome && parent.inChrome()) {
        parent.doLogin();

    } else {
        if (_session.user_id > 0) {
            if (window._session.noAdminAccess){
                ka.ai.loginFailed();
            } else {
                ka.ai.loginSuccess(_session, true);
            }
        }
    }
});

ka.doMiniSearch = function () {

    if (!ka._miniSearchPane) {
        $('ka-search-query').set('class', 'text mini-search-active');
        ka._miniSearchPane = new Element('div', {
            'class': 'ka-mini-search'
        }).inject($('border'));

        ka._miniSearchLoader = new Element('div', {
            'class': 'ka-mini-search-loading'
        }).inject(ka._miniSearchPane);
        new Element('img', {
            src: _path + PATH_MEDIA + '/admin/images/ka-tooltip-loading.gif'
        }).inject(ka._miniSearchLoader);
        new Element('span', {
            html: _('Searching ...')
        }).inject(ka._miniSearchLoader);
        ka._miniSearchResults = new Element('div', {'class': 'ka-mini-search-results'}).inject(ka._miniSearchPane);

    }

    ka._miniSearchLoader.setStyle('display', 'block');
    ka._miniSearchResults.set('html', '');


    if (ka._lastTimer) clearTimeout(ka._lastTimer);
    ka._lastTimer = ka._miniSearch.delay(500);

}

ka._miniSearch = function () {

    new Request.JSON({url: _path + 'admin/mini-search', noCache: 1, onComplete: function (res) {
        ka._miniSearchLoader.setStyle('display', 'none');
        ka._renderMiniSearchResults(res);
    }}).post({q: $('ka-search-query').value, lang: window._session.lang});

}

ka._renderMiniSearchResults = function (pRes) {

    ka._miniSearchResults.empty();

    if (typeOf(pRes) == 'object') {

        $H(pRes).each(function (subresults, subtitle) {
            var subBox = new Element('div').inject(ka._miniSearchResults);

            new Element('h3', {
                text: subtitle
            }).inject(subBox);

            var ol = new Element('ul').inject(subBox);
            subresults.each(function (subsubresults, index) {
                var li = new Element('li').inject(ol);
                new Element('a', {
                    html: ' ' + subsubresults[0],
                    href: 'javascript: ;'
                }).addEvent('click',
                    function () {
                        ka.wm.open(subsubresults[1], subsubresults[2]);//todo, does it work?
                        ka.hideMiniSearch();
                    }).inject(li);
            });
        });
    } else {
        new Element('span', {html: _('No results') }).inject(ka._miniSearchResults);
    }


}

ka.newBubble = function(pTitle, pText, pDuration){
    return ka.helpsystem.newBubble(pTitle, pText, pDuration);
}

ka.hideMiniSearch = function () {
    if (ka._miniSearchPane) {
        ka._miniSearchPane.destroy();
        $('ka-search-query').set('class', 'text');
        ka._miniSearchPane = false;
    }
}


ka.ai.prepareLoader = function () {
    ka.ai._loader = new Element('div', {
        'class': 'ka-ai-loader'
    }).setStyle('opacity', 0).set('tween', {duration: 400}).inject(document.body);

    frames['content'].onload = function () {
        ka.ai.endLoading();
    };
    frames['content'].onunload = function () {
        ka.ai.startLoading();
    };
}

ka.ai.endLoading = function () {
    ka.ai._loader.tween('opacity', 0);
}

ka.ai.startLoading = function () {
    var co = $('desktop');
    ka.ai._loader.setStyles(co.getCoordinates());
    ka.ai._loader.tween('opacity', 1);
}

ka.ai.renderLogin = function () {
    ka.ai.login = new Element('div', {
        'class': 'ka-login'
    }).inject(document.body);

    new Element('div', {
        'class': 'ka-login-bg-pattern'
    }).inject(ka.ai.login);

    new Element('div', {
        'class': 'ka-login-bg-butterfly1'
    }).inject(ka.ai.login);

    new Element('div', {
        'class': 'ka-login-bg-butterflysmall1'
    }).inject(ka.ai.login);


    new Element('div', {
        'class': 'ka-login-bg-blue'
    }).inject(ka.ai.login);

    ka.ai.loginBgBlue = new Element('div', {
        'class': 'ka-login-spot-blue'
    }).inject(ka.ai.login);


    ka.ai.loginBgRed = new Element('div', {
        'class': 'ka-login-spot-red'
    }).inject(ka.ai.login);
    ka.ai.loginBgRed.setStyle('opacity', 0);

    ka.ai.loginBgGreen = new Element('div', {
        'class': 'ka-login-spot-green'
    }).inject(ka.ai.login);
    ka.ai.loginBgGreen.setStyle('opacity', 0);

    var middle = new Element('div', {
        'class': 'ka-login-middle'
    }).inject(ka.ai.login);
    ka.ai.middle = middle;

    ka.ai.middle.set('tween', {tranition: Fx.Transitions.Cubic.easeOut});

    new Element('img', {
        'class': 'ka-login-logo',
        src: _path+ PATH_MEDIA + '/admin/images/login-logo.png'
    }).inject(middle);

    new Asset.image(_path+ PATH_MEDIA + '/admin/images/login-spot-green.png');
    new Asset.image(_path+ PATH_MEDIA + '/admin/images/login-spot-red.png');

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
    ka.ai.loginForm = form;

    ka.ai.loginName = new Element('input', {
        name: 'loginName',
        value: t('Username'),
        'class': 'ka-login-input-username',
        type: 'text'
    }).addEvent('focus',function (e) {
        if (this.value == t('Username'))
            this.value = '';
    }).addEvent('blur',function (e) {
        if (this.value == '')
                this.value = t('Username');
    })
    .addEvent('keyup',function (e) {
        if (e.key == 'enter') {
            ka.ai.doLogin();
        }
    }).inject(form);

    ka.ai.loginName.store('value', t('Username'));

    ka.ai.loginPw = new Element('input', {
        name: 'loginPw',
        type: 'password',
        'class': 'ka-login-input-passwd'
    }).addEvent('keyup',
        function (e) {
            if (e.key == 'enter') {
                ka.ai.doLogin();
            }
        }).inject(form);

    ka.ai.loginCircleBtn = new Element('div', {
        'class': 'ka-login-circlebtn'
    })
    .addEvent('click', function () {
        ka.ai.doLogin();
    })
    .inject(form);

    ka.ai.loginLangSelection = new ka.Select();

    ka.ai.loginLangSelection.chooser.addClass('ka-login-select-dark');
    ka.ai.loginLangSelection.inject(form);

    document.id(ka.ai.loginLangSelection).addClass('ka-login-select-login');

    ka.ai.loginLangSelection.addEvent('change', function () {
        ka.loadLanguage(ka.ai.loginLangSelection.getValue());
        ka.ai.reloadLogin();
    }).inject(form);

    ka.possibleLangs.each(function (lang) {
        ka.ai.loginLangSelection.add(lang.code, lang.title + ' (' + lang.langtitle + ')');
    });

    var ori = ka.ai.loginLangSelection.getValue();

    ka.ai.loginLangSelection.setValue(window._session.lang);

    ka.ai.loginDesktopMode = new Element('div', {
        'class': 'ka-login-desktopMode'
    }).inject(form);

    ka.ai.loginDesktopModeText = new Element('div', {
        'class': 'ka-login-desktopMode-text',
        text: t('Desktop mode')
    }).inject(ka.ai.loginDesktopMode)

    ka.ai.loginViewSelection = new ka.Checkbox();
    document.id(ka.ai.loginViewSelection).inject(ka.ai.loginDesktopMode);
    document.id(ka.ai.loginViewSelection).addClass('ka-login-checkbox-login');

    //TODO, autodetect here mobile browsers
    if (Cookie.read('kryn_deactivate_desktop_mode') == "1") {
        ka.ai.loginViewSelection.setValue(0);
    } else {
        ka.ai.loginViewSelection.setValue(1);
    }

    ka.ai.loginMessage = new Element('div', {
        'class': 'loginMessage'
    }).inject(middle);

    var combatMsg = false;
    var fullBlock = Browser.ie && Browser.version == '6.0';

    //check browser compatibility
    //if (!Browser.Plugins.Flash.version){
        //todo
    //}

    if (combatMsg || fullBlock){
        ka.ai.loginBarrierTape = new Element('div', {
            'class': 'ka-login-barrierTape'
        }).inject(ka.ai.login);

        ka.ai.loginBarrierTapeContainer = new Element('div').inject(ka.ai.loginBarrierTape);
        var table = new Element('table', {
            width: '100%'
        }).inject(ka.ai.loginBarrierTapeContainer);
        var tbody = new Element('tbody').inject(table);
        var tr = new Element('tr').inject(tbody);
        ka.ai.loginBarrierTapeText = new Element('td', {
            valign: 'middle',
            text: combatMsg,
            style: 'height: 55px;'
        }).inject(tr);
    }

    //if IE6
    if (fullBlock){
        ka.ai.loginBarrierTape.addClass('ka-login-barrierTape-fullblock');
        ka.ai.loginBarrierTapeText.set('text', t('Holy crap. You really use Internet Explorer 6? You can not enjoy the future with this - stay out.'));
        new Element('div', {
            'class': 'ka-login-barrierTapeFullBlockOverlay',
            styles: {
                opacity: 0.01
            }
        }).inject(ka.ai.login);
    }


    if (!Cookie.read('kryn_language')) {
        var possibleLanguage = navigator.browserLanguage || navigator.language;
        if (possibleLanguage.indexOf('-'))
            possibleLanguage = possibleLanguage.substr(0, possibleLanguage.indexOf('-'));

        if (ka.possibleLangs.contains(possibleLanguage)){

            ka.ai.loginLangSelection.setValue(possibleLanguage);
            if (ka.ai.loginLangSelection.getValue() != window._session.lang) {
                ka.loadLanguage(ka.ai.loginLangSelection.getValue());
                ka.ai.reloadLogin();
                return;
            }
        }
    }

    ka.loadLanguage(ka.ai.loginLangSelection.getValue());
}

ka.ai.reloadLogin = function () {
    ka.ai.loginDesktopModeText.set('text', t('Desktop mode'));

    if (ka.ai.loginName.value == '' || ka.ai.loginName.retrieve('value') == ka.ai.loginName.value)
        ka.ai.loginName.value = t('Username');

    ka.ai.loginName.store('value', t('Username'));
}

ka.ai.doLogin = function () {
    //todo, lock GUI

    ka.ai.loginMessage.set('html', _('Check Login. Please wait ...'));
    new Request.JSON({url: _path + 'admin/login', noCache: 1, onComplete: function (res) {
        if (res.data) {
            ka.ai.loginSuccess(res);
        } else {
            ka.ai.loginFailed();
        }
    }}).get({username: ka.ai.loginName.value, password: ka.ai.loginPw.value});
}

ka.ai.logout = function (pScreenlocker) {

    ka.ai.inScreenlockerMode = pScreenlocker;

    if (ka.ai.loaderCon) {
        ka.ai.loaderCon.destroy();
    }

    ka.ai.loginPw.value = '';

    ka.ai.middle.set('tween', {transition: Fx.Transitions.Cubic.easeOut});
    ka.ai.middle.tween('margin-top', ka.ai.middle.retrieve('oldMargin'));

    window.fireEvent('logout');

    if (!pScreenlocker) {
        ka.wm.closeAll();
        new Request({url: _path + 'admin/logout', noCache: 1}).get();
    }

    if (ka.desktop)
        ka.desktop.clear();

    if (ka.ai.loader) {
        ka.ai.loader.destroy();
    }

    ka.ai.loginMessage.set('html', '');
    ka.ai.login.setStyle('display', 'block');

    [ka.ai.loginDesktopMode, ka.ai.loginMessage,
        ka.ai.loginViewSelection, ka.ai.loginLangSelection]
        .each(function(i){document.id(i).setStyle('display', 'block')});

    ka.ai.loginLoadingBar.destroy();
    ka.ai.loginLoadingBarText.destroy();

    ka.ai.loginPw.value = '';
    ka.ai.loginPw.focus();
    window._session.user_id = 0;
}

ka.ai.loginSuccess = function (pResponse, pAlready) {

    $('border').setStyle('display', 'block');

    var b = new Fx.Tween(ka.ai.loginBgBlue, {duration: 500});
    var g = new Fx.Tween(ka.ai.loginBgGreen, {duration: 500});

    g.start('opacity', 1).chain(function(){
        this.start('opacity', 0)
    });
    b.start('opacity', 0).chain(function(){
        this.start('opacity', 1)
    });


    if (pAlready && window._session.hasBackendAccess == '0') {
        return;
    }

    if (pResponse.username) ka.ai.loginName.value = pResponse.username;

    window._session.username = ka.ai.loginName.value;

    window._sid = pResponse.token;
    window._session.sessionid = pResponse.token;
    window._user_id = pResponse.userId;

    $('user-username').set('text', window._session.username);
    $('user-username').onclick = function () {
        ka.wm.open('users/profile', {values: {id: pResponse.userId}});
    }

    window._session.user_id = pResponse.userId;
    window._session.lastlogin = pResponse.lastlogin;

    $(document.body).setStyle('background-position', 'center top');

    ka.ai.loginMessage.set('html', t('Please wait'));

    ka.ai.loadBackend();
}

ka.ai.loginFailed = function () {
    ka.ai.loginPw.focus();
    ka.ai.loginMessage.set('html', '<span style="color: red">' + _('Login failed') + '.</span>');
    (function () {
        ka.ai.loginMessage.set('html', '');
    }).delay(3000);


    var b = new Fx.Tween(ka.ai.loginBgBlue, {duration: 800});
    var r = new Fx.Tween(ka.ai.loginBgRed, {duration: 800});

    r.start('opacity', 1).chain(function(){
        (function(){this.start('opacity', 0)}).delay(2000,this)
    });
    b.start('opacity', 0).chain(function(){
        (function(){this.start('opacity', 1)}).delay(2000,this)
    });

}


ka.ai.loadBackend = function () {

    if (ka.ai.loginViewSelection.getValue() == 0){

        Cookie.write('kryn_deactivate_desktop_mode', '1');
        document.body.addClass('ka-no-desktop');
        document.body.removeClass('ka-with-desktop');
    } else {

        Cookie.write('kryn_deactivate_desktop_mode', '0');
        document.body.removeClass('ka-no-desktop');
        document.body.addClass('ka-with-desktop');
    }

    [ka.ai.loginDesktopMode, ka.ai.loginMessage,
        ka.ai.loginViewSelection, ka.ai.loginLangSelection]
        .each(function(i){document.id(i).setStyle('display', 'none')});

    ka.ai.loginLoadingBar = new Element('div', {
        'class': 'ka-ai-loginLoadingBar',
        styles: {
            opacity: 0
        }
    }).inject(ka.ai.loginPw, 'after');

    ka.ai.loginLoadingBar.tween('opacity', 1);

    ka.ai.loginLoadingBarInside = new Element('div', {
        'class': 'ka-ai-loginLoadingBarInside',
        styles: {
            width: 1
        }
    }).inject(ka.ai.loginLoadingBar);

    ka.ai.loginLoadingBarInside.set('tween', {transition: Fx.Transitions.Sine.easeOut});

    ka.ai.loginLoadingBarText = new Element('div', {
        'class': 'ka-ai-loginLoadingBarText',
        html: _('Loading your interface')
    }).inject(ka.ai.loginForm);

    (function(){
        ka.ai.loginLoadingBarInside.tween('width', 80);

        ka.ai.loginLoaderStep2 = (function () {
            ka.ai.loginLoadingBarInside.tween('width', 178);
        }).delay(900);

        //ka.ai.loaderTimer = ka.ai.loaderAni.periodical(1800);

        new Asset.css(_path + 'admin/css/style.css');
        new Asset.javascript(_path + 'admin/backend/js/script.js');
    }).delay(500);
}

ka.ai.loaderDone = function () {
    if (ka.ai.loginLoaderStep2 ) {
        clearTimeout(ka.ai.loginLoaderStep2 );
    }

    ka.ai.loginLoadingBarText.set('html', _('Loading done'));
    ka.ai.loginLoadingBarInside.tween('width', 294);
    ka.ai.loadDone.delay(800);
}

ka.ai.loadDone = function () {

    ka.check4Updates.delay(2000);

    ka.ai.allFilesLoaded = true;
    //ka.ai.middle.store('oldMargin', ka.ai.middle.getStyle('margin-top'));
    //ka.ai.middle.set('tween', {transition: Fx.Transitions.Cubic.easeOut});
    //ka.ai.middle.tween('margin-top', -250);
    if (ka.ai.blender) ka.ai.blender.destroy();

    ka.ai.blender = new Element('div', {
        style: 'left: 0px; top: 0px; right: 0px; bottom: 0px; position: absolute; background-color: white; z-index: 15012300',
        styles: {
            opacity: 0
        }
    }).inject(document.body);

    ka.ai.blender.set('tween', {duration: 450});

    new Fx.Tween(ka.ai.blender, {duration: 450})
    .start('opacity', 1).chain(function () {
        ka.ai.login.setStyle('display', 'none');

        //load settings, bg etc
        ka.loadSettings();

        ka.init();
        ka.loadMenu();

        //start checking for unindexed sites
        //checkSearchIndex.init();
        //start autocrawling process
        //system_searchAutoCrawler.init();

        var lastlogin = new Date();
        if (window._session.lastlogin > 0) {
            lastlogin = new Date(window._session.lastlogin * 1000);
        }
        ka.helpsystem.newBubble(
            _('Welcome back, %s').replace('%s', window._session.username),
            _('Your last login was %s').replace('%s', lastlogin.format('%d. %b %I:%M')),
            3000);

        this.start('opacity', 0).chain(function(){
            ka.ai.blender.destroy();
        })

    });

}









ka.createModuleMenu = function () {
    if (ka._moduleMenu) {
        ka._moduleMenu.destroy();
    }

    ka._moduleMenu = new Element('div', {
        'class': 'ka-module-menu',
        style: 'left: -250px;'
    }).addEvent('mouseover', ka.toggleModuleMenuIn.bind(this, true)).addEvent('mouseout', ka.toggleModuleMenuOut).inject(document.body);
    ka._moduleMenu.set('tween', {transition: Fx.Transitions.Quart.easeOut});

    ka.moduleToggler = new Element('div', {
        'class': 'ka-module-toggler'
    }).addEvent('click',
        function () {
            ka.toggleModuleMenuIn();
        }).inject(ka._moduleMenu);

    new Element('img', {
        src: _path + PATH_MEDIA + _('admin/images/extensions-text.png')
    }).addEvent('click', ka.toggleModuleMenuIn).inject(ka.moduleToggler);

    new Element('div', {
        html: _('Extensions'),
        style: 'padding-left: 15px; color: white; font-weight: bold; padding-top: 4px;'
    }).inject(ka._moduleMenu);

    ka.moduleItems = new Element('div', {
        'class': 'ka-module-items'
    }).inject(ka._moduleMenu);


    ka.moduleItemsScrollerContainer = new Element('div', {
        'class': 'ka-module-items-scroller-container'
    }).inject(ka._moduleMenu);

    ka.moduleItemsScroller = new Element('div', {
        'class': 'ka-module-items-scroller'
    }).inject(ka.moduleItemsScrollerContainer);
    //}).inject( ka._moduleMenu );

    //window.addEvent('resize', ka.updateModuleItemsScrollerSize);


    ka.moduleItemsScroller.addEvent('mousedown', function () {
        ka.moduleItemsScrollerDown = true;
    });

    ka.moduleItems.addEvent('mousewheel', function (e) {

        var newPos = ka.moduleItemsScrollSlider.step;

        if (e.wheel > 0) {
            //up
            newPos--;
        } else if (e.wheel < 0) {
            //down
            newPos++;
        }
        if (newPos > ka.moduleItemsScrollSlider.max) {
            newPos = ka.moduleItemsScrollSlider.max;
        }

        if (newPos < ka.moduleItemsScrollSlider.min) {
            newPos = ka.moduleItemsScrollSlider.min;
        }

        ka.moduleItemsScrollSlider.set(newPos);

    });
    ka.toggleModuleMenuOut(true);
}

ka.updateModuleItemsScrollerSize = function () {

    var completeSize = ka.moduleItems.getScrollSize();
    var size = ka.moduleItems.getSize();

    var diffHeight = completeSize.y - size.y;

    if (diffHeight > 12) {
        ka.moduleItemsScroller.setStyle('display', 'block');

        var proz = Math.ceil(diffHeight / (completeSize.y / 100));

        var newDiffHeight = (proz / 100) * size.y;

        var scrollBarHeight = size.y - newDiffHeight;

        ka.moduleItemsScroller.setStyle('height', scrollBarHeight);

        //if( ka.moduleItemsScrollSlider )
        //	ka.moduleItemsScrollSlider.deattach();

        ka.moduleItemsScrollSlider = new Slider(ka.moduleItemsScrollerContainer, ka.moduleItemsScroller, {
            wheel: true,
            mode: 'vertical',
            steps: 25,
            onChange: function (pPos) {
                var scrollTop = ((pPos * 4) / 100) * diffHeight;
                ka.moduleItems.scrollTo(0, scrollTop);
            },
            onComplete: function () {
                ka.moduleItemsScrollerDown = false;
            }
        });
        ka.moduleItemsScrollSlider.set(0);
    } else {
        ka.moduleItemsScroller.setStyle('display', 'none');
        ka.moduleItems.scrollTo(0, 0);
    }

}

ka.toggleModuleMenuIn = function (pOnlyStay) {


    if (ka.lastModuleMenuOutTimer) {
        clearTimeout(ka.lastModuleMenuOutTimer);
    }

    if (ka.ModuleMenuOutOpen == true) {
        return;
    }

    if (pOnlyStay == true) {
        return;
    }

    ka.ModuleMenuOutOpen = false;
    ka._moduleMenu.set('tween', {transition: Fx.Transitions.Quart.easeOut, onComplete: function () {
        ka.ModuleMenuOutOpen = true;
    }});
    ka._moduleMenu.tween('left', 0);
    ka.moduleToggler.store('active', true);
    ka.moduleItems.setStyle('right', 0);
    //ka.moduleItemsScroller.setStyle('left', 188);
    //ka.moduleItemsScrollerContainer.setStyle('right', 0);
}

ka.toggleModuleMenuOut = function (pForce) {

    //if( !ka.ModuleMenuOutOpen && pForce != true )
    //	return;

    if (ka.lastModuleMenuOutTimer) {
        clearTimeout(ka.lastModuleMenuOutTimer);
    }

    ka.ModuleMenuOutOpen = false;

    ka.lastModuleMenuOutTimer = (function () {
        ka._moduleMenu.set('tween', {transition: Fx.Transitions.Quart.easeOut, onComplete: function () {
            ka.ModuleMenuOutOpen = false;
        }});
        ka._moduleMenu.tween('left', (ka._moduleMenu.getSize().x - 33) * -1);
        ka.moduleToggler.store('active', false);
        ka.moduleItems.setStyle('right', 40);
        //ka.moduleItemsScrollerContainer.setStyle('right', 50);
        ka.destroyLinkContext();
    }).delay(300);

}

ka.toggleModuleMenu = function () {
    if (ka.moduleToggler.retrieve('active') != true) {
        ka.toggleModuleMenuIn();
    } else {
        ka.toggleModuleMenuOut();
    }
}

ka.loadMenu = function () {

    if (ka.lastLoadMenuReq) ka.lastLoadMenuReq.cancel();

    ka.lastLoadMenuReq = new Request.JSON({url: _path + 'admin/backend/menus', noCache: true, onComplete: function (res) {

        //ka.createModuleMenu();
        //ka.moduleItems.empty();
        $('mainLinks').empty();
        if (ka.additionalMainMenu) {
            ka.additionalMainMenu.destroy();
            ka.additionalMainMenuContainer.destroy();
            delete ka.additionalMainMenu;
        }

        ka.removedMainMenuItems = [];
        delete ka.mainMenuItems;

        var mlinks = res.data;

        Object.each(mlinks['admin'], function (item, pCode) {
            ka.addAdminLink(item, pCode, 'admin');
        });

        delete mlinks['admin'];

        if (mlinks['users']) {
            Object.each(mlinks['users'], function (item, pCode) {
                ka.addAdminLink(item, pCode, 'users');
            });
        }
        delete mlinks['users'];

        Object.each(ka.settings.configs, function (config, extKey) {
            if (!mlinks[extKey]) return;

            Object.each(mlinks[extKey], function (item, pCode) {
                ka.addAdminLink(item, pCode, extKey);
            });

        });

        ka.needMainMenuWidth = false;

        //ka.updateModuleItemsScrollerSize();
        ka.checkMainBarWidth();


    }}).get();
};


ka.removedMainMenuItems = [];

ka.checkMainBarWidth = function () {

    var windowSize = window.getSize().x;
    if (windowSize < 500) {
        if (!ka.toSmallWindowBlocker) {
            ka.toSmallWindowBlocker = new Element('div', {
                'style': 'position: absolute; left: 0px; right: 0px; top: 0px; bottom: 0px; z-index: 600000000; background-color: white;'
            }).inject(document.body);
            var t = new Element('table', {style: 'width: 100%; height: 100%'}).inject(ka.toSmallWindowBlocker);
            var tr = new Element('tr').inject(t);
            var td = new Element('td', {
                align: 'center', valign: 'center',
                text: _('Your browser window is too small.')
            }).inject(tr);
        }
    } else if (ka.toSmallWindowBlocker) {
        ka.toSmallWindowBlocker.destroy();
        ka.toSmallWindowBlocker = null;
    }

    var iconbar = $('iconbar');
    var menubar = $('mainLinks');
    var header = $('header');

    var menubarSize = menubar.getSize();
    var iconbarSize = iconbar.getSize();
    var headerSize = header.getSize();
    //var searchBoxWidth = 263;
    var searchBoxWidth = 221;


    if (ka.additionalMainMenu) {
        searchBoxWidth += ka.additionalMainMenu.getSize().x;
    }

    var curWidth = menubarSize.x + iconbarSize.x + searchBoxWidth;

    if (!ka.needMainMenuWidth) {
        //first run, read all children widths

        if (!ka.mainMenuItems) {
            ka.mainMenuItems = menubar.getChildren('a');
        }

        ka.mainMenuItems.each(function (menuitem, index) {
            if (index == 0) return;
            menuitem.store('width', menuitem.getSize().x);
        });
    }


    //if( curWidth > headerSize.x ){

    var childrens = menubar.getChildren('a');

    var fullsize = 0;
    var addMenuWidth = 50;

    //diff is the free space we have to display menuitems
    var diff = ((menubarSize.x + iconbarSize.x + searchBoxWidth) - headerSize.x);

    //availWidth is now the availWidth we have for the menuitems
    //var availWidth = menubarSize.x - diff - addMenuWidth;
    var availWidth = window.getSize().x - (document.id('iconbar').getSize().x + 60);

    if (!ka.needMainMenuWidth) {
        ka.needMainMenuWidth = availWidth;
    }

    ka.removedMainMenuItems = [];

    ka.mainMenuItems.each(function (menuitem, index) {
        if (index == 0) return;

        var width = menuitem.retrieve('width');
        fullsize += width;

        if (fullsize < availWidth) {
            //we have place for this item
            //check if this menuitem is in the additional menu bar or in origin
            if (menuitem.retrieve('inAdditionalMenuBar') == true) {
                menuitem.inject(menubar);
                menuitem.store('inAdditionalMenuBar', false);
            }
        } else {
            //we have no place for this menuitem
            ka.removedMainMenuItems.include(menuitem);
        }
    });


    if (ka.removedMainMenuItems.length > 0) {

        if (!ka.additionalMainMenu) {
            ka.additionalMainMenu = new Element('a', {
                'class': 'ka-mainlink-additionalmenubar',
                style: 'width: 17px; cursor: default;'
            }).inject(menubar);

            new Element('img', {
                src: _path + PATH_MEDIA + '/admin/images/ka.mainmenu-additional.png',
                style: 'width: 11px; height: 12px; left: 6px; top: 8px;'
            }).inject(ka.additionalMainMenu);

            ka.additionalMainMenuContainer = new Element('div', {
                'class': 'ka-mainlink-additionalmenubar-container bar-dock-menu-style',
                style: 'display: none'
            }).inject($('border'));

            ka.makeMenu(ka.additionalMainMenu, ka.additionalMainMenuContainer, true, {y: 48, x: -1});
        }

        ka.removedMainMenuItems.each(function (menuitem) {
            menuitem.inject(ka.additionalMainMenuContainer);
            menuitem.store('inAdditionalMenuBar', true);
        });
        ka.additionalMainMenu.inject(menubar);
    } else {

        if (ka.additionalMainMenu) {
            ka.additionalMainMenu.destroy();
            ka.additionalMainMenuContainer.destroy();
            ka.additionalMainMenu = null;
        }

    }
};

ka.makeMenu = function (pToggler, pMenu, pCalPosition, pOffset) {


    pMenu.setStyle('display', 'none');

    var showMenu = function () {
        pMenu.setStyle('display', 'block');
        pMenu.store('ka.makeMenu.canHide', false);

        if (pCalPosition) {
            var pos = pToggler.getPosition($('border'));
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
        if (pMenu.retrieve('ka.makeMenu.canHide') != true) return;
        pMenu.setStyle('display', 'none');
    };

    var hideMenu = function () {
        pMenu.store('ka.makeMenu.canHide', true);
        _hideMenu.delay(250);
    };

    pToggler.addEvent('mouseover', showMenu);
    pToggler.addEvent('mouseout', hideMenu);
    pMenu.addEvent('mouseover', showMenu);
    pMenu.addEvent('mouseout', hideMenu);

    //ka.additionalMainMenu, ka.additionalMainMenuContainer, true, {y: 80});
}

ka.addAdminLink = function (pLink, pCode, pExtCode) {

    var mlink = false;

    if (!pLink.isLink) return;

    if (pCode == 'system') {

        mlink = new Element('a', {
            title: pLink.title,
            text: ' ',
            'class': 'bar-dock-logo first gradient'
        });

        new Element('img', {
            src: _path + PATH_MEDIA + '/admin/images/dock-logo-icon.png'
        }).inject(mlink);

    } else {

        mlink = new Element('a', {
            text: pLink.title
        });

        if (pLink.icon) {
            if (pLink.icon.substr(0,1) == '#'){
                mlink.addClass(pLink.icon.substr(1));
            } else {
                mlink.addClass('ka-mainmenubar-item-hasIcon');
                new Element('img', {
                    src: _path + PATH_MEDIA + pLink.icon
                }).inject(mlink);
            }
        }
    }

    var menu = new Element('div', {
        'class': 'bar-dock-menu bar-dock-menu-style',
        styles: {
            display: 'none'
        }
    });

    var hasActiveChilds = false;

    Object.each(pLink.children, function (item, code) {

        if (!item.isLink) return;

        hasActiveChilds = true;
        var sublink = new Element('a', {
            html: item.title,
            'class': 'ka-module-items-deactivated'
        }).inject(menu);

        if (item.type) {
            sublink.removeClass('ka-module-items-deactivated');
            sublink.addEvent('click', function () {
                ka.wm.openWindow(pExtCode + '/' + pCode + '/' + code, pLink);
            })
        }

        Object.each(item.children, function (subitem, subcode) {

            if (!subitem.isLink) return;

            var subsublink = new Element('a', {
                html: subitem.title,
                'class': 'ka-module-item-sub'
            }).inject(menu);

            if (subitem.type) {
                subsublink.addClass('ka-module-items-activated');
                subsublink.addEvent('click', function () {
                    ka.wm.openWindow(pExtCode + '/' + pCode + '/' + code + '/' + subcode, pLink);
                })
            }
        });

    });

    if (!hasActiveChilds){
        menu.destroy();
    } else {
        var childOpener;

        if (pCode != 'system') {
            mlink.addClass('ka-menu-item-hasChilds');

            childOpener = new Element('a', {
                'class': 'ka-menu-item-childopener'
            }).inject(mlink);

            new Element('img', {
                src: _path+ PATH_MEDIA + '/admin/images/ka-mainmenu-item-tree_minus.png'
            })
                .addEvent('mouseover',  function () {
                    childOpener.fireEvent('mouseover');
                })
                .addEvent('mousemove',  function () {
                    childOpener.fireEvent('mouseover');
                })
                .inject(childOpener);


            childOpener.addEvent('click',function (e) {
                e.stopPropagation();
            });
        } else {
            childOpener = mlink;
        }

        childOpener.addEvent('mousemove', function (e) {
            childOpener.fireEvent('mouseover');
        });

        childOpener.addEvent('mouseover', function (e) {

            if (e)
                e.stopPropagation();

            if (ka.lastVisibleDockMenu && ka.lastVisibleDockMenu != menu)
                ka.lastVisibleDockMenu.setStyle('display', 'none');

            childOpener.store('allowToDisappear', false);

            //find position
            var position = mlink.getPosition(document.id('border'));
            var size = mlink.getSize();
            menu.setStyle('left', position.x-1);
            menu.setStyle('top', 48);
            menu.removeClass('ka-menu-withRightTopBorderRadius');

            var addMenuBar = mlink.getParent('.ka-mainlink-additionalmenubar-container');

            menu.removeEvents('mouseover');
            menu.removeEvents('mouseout');

            if (addMenuBar){
                addMenuBar.removeClass('ka-mainlink-additionalmenubar-container-withoutBottomRightBorderRadius')

                menu.setStyle('left', position.x+size.x);
                menu.setStyle('top', position.y);
                menu.addClass('ka-menu-withRightTopBorderRadius');
                menu.setStyle('display', 'block');

                if (menu.getPosition().y+menu.getSize().y > addMenuBar.getPosition().y+addMenuBar.getSize().y)
                    addMenuBar.addClass('ka-mainlink-additionalmenubar-container-withoutBottomRightBorderRadius');

                menu.addEvent('mouseover', function(){
                    addMenuBar.store('ka.makeMenu.canHide', false);
                });

                menu.addEvent('mouseout', function(){
                    addMenuBar.fireEvent('mouseout');
                });

                childOpener.addEvent('mouseover', function(){
                    addMenuBar.store('ka.makeMenu.canHide', false);
                });
            }

            menu.addEvent('mouseover', function () {
                childOpener.store('allowToDisappear', false);
            }).addEvent('mouseout', function () {
                    childOpener.fireEvent('mouseout');
                }).inject($('header'), 'before');

            menu.setStyle('display', 'block');
            ka.lastVisibleDockMenu = menu;

        }).addEvent('mouseout', function () {

                childOpener.store('allowToDisappear', true);
                (function () {
                    if (childOpener.retrieve('allowToDisappear') == true) {
                        menu.setStyle('display', 'none');
                    }
                }).delay(250);
            });
    }

    ka._links[ pExtCode + '/' + pCode ] = {
        level: 'main',
        object: mlink,
        link: pLink,
        module: pExtCode,
        code: pCode,
        path: pExtCode + '/' + pCode,
        title: pLink.title
    };

    mlink.inject($('mainLinks'));
    pLink.module = pExtCode;
    pLink.code = pCode;
    mlink.store('link', pLink);
    ka.linkClick(mlink);


}


ka.destroyLinkContext = function () {

    if (ka._lastLinkContextDiv) {
        ka._lastLinkContextDiv.destroy();
        ka._lastLinkContextDiv = null;
    }

}

ka.linkClick = function (pLink) {
    var mlink = pLink.retrieve('link');

    if (['iframe', 'list', 'combine', 'custom', 'add', 'edit'].indexOf(mlink.type) != -1) {

        var link = ka._links[mlink.module + '/' + mlink.code];

        pLink.getParent().addClass('ka-module-items-activated');

        pLink.addEvent('click', function (e) {
            ka.destroyLinkContext();

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
                ka._openLinkContext(link);
            }

            delete windows;
        });

        pLink.addEvent('mouseup', function (e) {

            if (e.rightClick) {
                e.stopPropagation();
                ka._openLinkContext(link);
            }
        });
    }
}

ka._openLinkContext = function (pLink) {

    if (ka._lastLinkContextDiv) {
        ka._lastLinkContextDiv.destroy();
        ka._lastLinkContextDiv = null;
    }

    var pos = {x: 0, y: 0};
    var corner = false;

    /*if( pLink.level == 'main' ){
     var div = new Element('div', {
     'class': 'ka-linkcontext-main'
     }).inject( document.body );

     pos = pLink.object.getPosition();
     var size = pLink.object.getSize();

     div.setStyle('left', pos.x);
     //div.setStyle('width', size.x);
     }
     if( pLink.level == 'sub' ){
     */

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

    ka._lastLinkContextDiv = div;

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
            ka._lastLinkContextDiv.destroy();
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
                ka._lastLinkContextDiv.destroy();
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

}





ka.startSearchCrawlerInfo = function (pHtml) {
    ka.stopSearchCrawlerInfo();

    this.startSearchCrawlerInfoMenu = new Element('div', {
        'class': 'ka-updates-menu',
        style: 'left: 170px; width: 177px;'
    }).inject($('border'));

    this.startSearchCrawlerInfoMenuHtml = new Element('div', {
        html: pHtml
    }).inject(this.startSearchCrawlerInfoMenu);

    this.startSearchCrawlerProgressLine = new Element('div', {
        style: 'position: absolute; bottom: 1px; left: 4px; width: 0px; height: 1px; background-color: #444;'
    }).inject(this.startSearchCrawlerInfoMenu);

    this.startSearchCrawlerInfoMenu.tween('top', 48);
}

ka.setSearchCrawlerInfo = function (pHtml) {
    this.startSearchCrawlerInfoMenuHtml.set('html', pHtml);
}

ka.stopSearchCrawlerInfo = function (pOutroText) {
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

}

ka.setSearchCrawlerProgress = function (pPos) {
    var maxLength = 177 - 8;
    var pos = maxLength * pPos / 100;
    this.startSearchCrawlerProgressLine.set('tween', {duration: 100});
    this.startSearchCrawlerProgressLine.tween('width', pos);
}

ka.stopSearchCrawlerProgress = function () {
    this.startSearchCrawlerProgressLine.set('tween', {duration: 10});
    this.startSearchCrawlerProgressLine.tween('width', 0);
}

ka.openSearchContextClose = function () {
    if (ka.openSearchContextLast) {
        ka.openSearchContextLast.destroy();
    }

}

ka.openSearchContext = function () {

    var button = $('ka-btn-create-search-index');

    ka.openSearchContextClose();

    this.openSearchContextLast = new Element('div', {
        'class': 'ka-searchcontext'
    }).inject($('border'));

    var pos = button.getPosition($('border'));
    var size = $('border').getSize();
    var right = size.x - pos.x;

    this.openSearchContextLast.setStyle('right', right - 30);

    new Element('img', {
        'class': 'ka-searchcontext-arrow',
        src: _path + PATH_MEDIA + '/admin/images/ka-tooltip-corner-top.png'
    }).inject(this.openSearchContextLast);

    this.openSearchContextContent = new Element('div', {
        'class': 'ka-searchcontext-content'
    }).inject(this.openSearchContextLast);

    this.openSearchContextBottom = new Element('div', {
        'class': 'ka-searchcontext-bottom'
    }).inject(this.openSearchContextLast);

    new ka.Button(_('Indexed pages')).addEvent('click',
        function () {
            ka.wm.open('admin/system/searchIndexerList');
        }).inject(this.openSearchContextBottom);


    ka.openSearchContextClearIndex = new ka.Button(_('Clear index')).addEvent('click',
        function () {
            ka.openSearchContextClearIndex.startTip(_('Clearing index ...'));

            new Request.JSON({url: _path + 'admin/backend/searchIndexer/clearIndex', noCache: 1, onComplete: function (pRes) {
                ka.openSearchContextClearIndex.stopTip(_('Done'));
            }.bind(this)}).post();
        }).inject(this.openSearchContextBottom);

    new Element('a', {
        style: 'position: absolute; right: 5px; top: 3px; text-decoration: none; font-size: 13px;',
        text: 'x',
        title: _('Close'),
        href: 'javascript: ;'
    }).addEvent('click', ka.openSearchContextClose).inject(this.openSearchContextLast);

    ka.openSearchContextLoad();

}


ka.openSearchContextLoad = function () {

    ka.openSearchContextContent.set('html', '<br /><br /><div style="text-align: center; color: gray;">' + _('Loading ...') + '</div>');


    ka.openSearchContextTable = new ka.Table([
        [_('Domain'), 190],
        [_('Indexed pages')]
    ]);

    new Request.JSON({url: _path + 'admin/backend/searchIndexer/getIndexedPages4AllDomains',
        noCache: 1,
        onComplete: function (pRes) {

            ka.openSearchContextContent.empty();

            ka.openSearchContextTable.inject(ka.openSearchContextContent);

            if (pRes) {
                pRes.each(function (domain) {
                    ka.openSearchContextTable.addRow([domain.domain + '<span style="color:gray"> (' + domain.lang + ')</span>', domain.indexedcount]);
                });
            }

        }
    }).post();


}


ka.displayNewUpdates = function (pModules) {
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
        }).inject($('border'));
    this.newUpdatesMenu.tween('top', 48);
}

ka.buildClipboardMenu = function () {
    ka.clipboardMenu = new Element('div', {
        'class': 'ka-clipboard-menu'
    }).inject($('header'), 'before');
}

ka.buildUploadMenu = function () {
    ka.uploadMenu = new Element('div', {
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
        }).inject($('header'), 'before');

    ka.uploadMenuInfo = new Element('div', {
        'class': 'ka-upload-menu-info'
    }).inject(ka.uploadMenu);
}




ka.check4Updates = function () {
    if (window._session.user_id == 0) return;
    new Request.JSON({url: _path + 'admin/system/module/manager/check-updates', noCache: 1, onComplete: function (res) {
        if (res && res.found) {
            ka.displayNewUpdates(res.modules);
        }
        ka.check4Updates.delay(10 * (60 * 1000));
    }}).get();
}
