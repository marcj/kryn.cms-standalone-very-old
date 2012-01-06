/* admin index */
if (typeof window.ka == 'undefined') {
    window.ka = {};
}
window.kaExist = true;

window.ka.ai = {};

document.addEvent('touchmove', function (event) {
    event.preventDefault();
});

if ($type(ka.langs) != 'object') ka.langs = {};

var logger = function (pVal) {
    if (typeof console != "undefined") {
        console.log(pVal);
    }
}

ka.openFrontend = function () {
    if (top) {
        top.open(_path, '_blank');
    }
}

/**
 * @deprecated Use t() instead
 * @param string p
 */
window._ = function (p) {
    return t(p);
    //return _kml2html(p);
}

/**
 * Return a translated message pMsg with plural and context ability
 *
 * @param string pMsg     message id (msgid)
 * @param string pPlural  message id plural (msgid_plural)
 * @param int    pCount   the count for plural
 * @param string pContext the message id of the context (msgctxt)
 */
window.t = function(pMsg, pPlural, pCount, pContext) {
    return _kml2html(ka.translate(pMsg, pPlural, pCount, pContext));
}

ka.translate = function(pMsg, pPlural, pCount, pContext) {
    if (!ka && parent) ka = parent.ka;
    if (ka && !ka.lang && parent && parent.ka) ka.lang = parent.ka.lang;
    var id = (!pContext) ? pMsg : pContext + "\004" + pMsg;

    if (ka.lang && ka.lang[id]){
        if (typeOf(ka.lang[id]) == 'array') {
            if (pCount){
                var fn = 'gettext_plural_fn_'+ka.lang['__lang'];
                var plural = window[fn](pCount)+0;

                if (pCount && ka.lang[id][plural])
                    return ka.lang[id][plural].replace('%d', pCount);
                else
                    return ((pCount === null || pCount === false || pCount === 1) ? pMsg : pPlural);
            } else {
                return ka.lang[id][0];
            }
        } else {
            return ka.lang[id];
        }
    } else {
        return ((!pCount || pCount === 1) && pCount !== 0) ? pMsg : pPlural;
    }
}

/**
 * Return a translated message $pMsg within a context $pContext
 *
 * @param string pContext the message id of the context
 * @param string pMsg     message id
 */
window.tc = function(pContext, pMsg) {
    return t(pMsg, null, null, pContext);
}

window._kml2html = function (pRes) {

    var kml = ['ka:help'];
    if (pRes) {
        pRes = pRes.replace(/<ka:help\s+id="(.*)">(.*)<\/ka:help>/g, '<a href="javascript:;" onclick="ka.wm.open(\'admin/help\', {id: \'$1\'}); return false;">$2</a>');
    }
    return pRes;
}

ka._wysiwygId2Win = new Hash({});

ka.tinyPopup2Win = new Hash({});

window.addEvent('load', function () {

    window.ka.ai.renderLogin();

    document.hidden = new Element('div', {
        styles: {
            position: 'absolute',
            left: -154,
            top: -345,
            width: 1, height: 1, overflow: 'hidden'
        }
    }).inject(document.body);

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
        if (_session.user_rsn > 0) {
            ka.ai.loginSuccess(_session, true);
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
            src: _path + 'inc/template/admin/images/ka-tooltip-loading.gif'
        }).inject(ka._miniSearchLoader);
        new Element('span', {
            html: _('Searching ...')
        }).inject(ka._miniSearchLoader);
        ka._miniSearchResults = new Element('div', {'class': 'ka-mini-search-results'}).inject(ka._miniSearchPane);

    }

    ka._miniSearchLoader.setStyle('display', 'block');
    ka._miniSearchResults.set('html', '');


    if (ka._lastTimer) $clear(ka._lastTimer);
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

    if ($type(pRes) == 'object') {

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
                        ka.wm.open(subsubresults[1], subsubresults[2]);
                        ka.hideMiniSearch();
                    }).inject(li);
            });
        });
    } else {
        new Element('span', {html: _('No results') }).inject(ka._miniSearchResults);
    }


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
    var co = $('middle');
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
        src: _path+'inc/template/admin/images/login-logo.png'
    }).inject(middle);

    new Asset.image(_path+'inc/template/admin/images/login-spot-green.png');
    new Asset.image(_path+'inc/template/admin/images/login-spot-red.png');

    ka.ai.loginMiddleBg = new Element('div', {
        'class': 'ka-login-middleBg'
    }).inject(ka.ai.middle);

    var form = new Element('form', {
        id: 'loginForm',
        'class': 'ka-login-middle-form',
        action: './admin',
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
    ka.ai.loginViewSelection.setValue(1);


    ka.ai.loginMessage = new Element('div', {
        'class': 'loginMessage'
    }).inject(middle);


    if (!Cookie.read('kryn_language')) {
        ka.ai.loginLangSelection.setValue(navigator.browserLanguage || navigator.language);
        if (ka.ai.loginLangSelection.getValue() != window._session.lang) {
            ka.loadLanguage(ka.ai.loginLangSelection.getValue());
            ka.ai.reloadLogin();
            return;
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
    ka.ai.loginMessage.set('html', _('Check Login. Please wait ...'));
    new Request.JSON({url: _path + 'admin/?admin-users-login=1&json:1', noCache: 1, onComplete: function (res) {
        if (res.user_rsn > 0) {
            ka.ai.loginSuccess(res);
        } else {
            ka.ai.loginFailed();
        }
    }}).post({ username: ka.ai.loginName.value, passwd: ka.ai.loginPw.value });
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
        new Request({url: _path + 'admin/?admin-users-logout=1'}).post();
    }

    if (ka._desktop)
        ka._desktop.clear();

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
    window._session.user_rsn = 0;
}

ka.ai.loginSuccess = function (pId, pAlready) {

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

    ka.ai.loginName.value = pId.username;
    window._sid = pId.sessionid;
    window._session.sessionid = pId.sessionid;

    $('user.username').set('text', ka.ai.loginName.value);
    $('user.username').onclick = function () {
        ka.wm.open('users/users/editMe/', {values: {rsn: pId.user_rsn}});
    }

    window._session.user_rsn = pId.user_rsn;
    window._session.username = pId.username;
    window._session.lastlogin = pId.lastlogin;

    $(document.body).setStyle('background-position', 'center top');

    ka.ai.loginMessage.set('html', _('Please wait'));

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
        document.body.addClass('ka-no-desktop');
        document.body.removeClass('ka-with-desktop');
    } else {
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

        new Asset.css(_path + 'admin/loadCss/style.css');
        new Asset.javascript(_path + 'admin/backend/loadJs/script.js');
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
        ka._helpsystem.newBubble(
            _('Welcome back, %s').replace('%s', window._session.username),
            _('Your last login was %s').replace('%s', lastlogin.format('%d. %b %I:%M')),
            3000);

        this.start('opacity', 0).chain(function(){
            ka.ai.blender.destroy();
        })

    });

}
