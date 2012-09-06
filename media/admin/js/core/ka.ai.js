
if (typeof window.ka == 'undefined') {
    window.ka = {};
}
window.kaExist = true;

window.ka.ai = {};

document.addEvent('touchmove', function (event) {
    event.preventDefault();
});

if (typeOf(ka.langs) != 'object') ka.langs = {};

window.logger = function (pVal) {
    if (typeOf(console) != "undefined") {
        console.log(pVal);
    }
};

ka.openFrontend = function () {
    if (top) {
        top.open(_path, '_blank');
    }
};

ka.mobile = false;

/**
 * @deprecated Use t() instead
 * @param string p
 */
window._ = function (p) {
    return t(p);
    //return _kml2html(p);
};

/**
 * Request.JSON - extended to get some informations about calls
 */
Request.JSON = new Class({
    Extends: Request.JSON,

    initialize: function(options){
        this.parent(options);
        this.addEvent('failure', this.booboo.bind(this));
        this.addEvent('error', this.invalidJson.bind(this));

        this.addEvent('complete', function(pData){
            window.fireEvent('restCall', [pData, this]);
        }.bind(this));

        if (options.noErrorReporting === true) return;
        this.addEvent('complete', this.checkError.bind(this));
    },

    send: function(options){
        this.data = options.data;
        return this.parent(options);
    },

    invalidJson: function(){

        if (ka.lastRequestBubble){
            ka.lastRequestBubble.die();
            delete ka.lastRequestBubble;
        }

        if (ka.helpsystem){
            ka.lastRequestBubble = ka.helpsystem.newBubble(
                t('Response error'),
                t('Server\' response is not valid JSON. Looks like the server has serious troubles. :-(')+
                    "<br/>"+'URI: %s'.replace('%s', this.options.url)+'<br/><a class="ka-Button" href="javascript:;">Details</a>',
                15000);
        }

    },

    booboo: function(){

        if (ka.lastRequestBubble){
            ka.lastRequestBubble.die();
            delete ka.lastRequestBubble;
        }

        if (ka.helpsystem){
            ka.lastRequestBubble = ka.helpsystem.newBubble(
                t('Request error'),
                t('There has been a error occured during the last request. Either you lost your internet connection or the server has serious troubles.')+
                    "<br/>"+'URI: %s'.replace('%s', this.options.url)+'<br/><a class="ka-Button" href="javascript:;">Details</a>',
                15000);
        }

    },

    checkError: function(pResult){


        if (pResult && pResult.error){

            if (typeOf(this.options.noErrorReporting) == 'array' && this.options.noErrorReporting.contains(pResult.error))
                return false;

            if (ka.lastRequestBubble){
                ka.lastRequestBubble.die();
                delete ka.lastRequestBubble;
            }

            if (pResult.error == "access_denied"){

                if (ka.helpsystem){
                    ka.lastRequestBubble = ka.helpsystem.newBubble(
                        t('Access denied'),
                        t('You started a secured action or requested a secured information.')+
                            "<br/>"+'URI: %s'.replace('%s', this.options.url)+'<br/><a class="ka-Button" onclick="ka.open(\'admin/system/rest-logger\')">Details</a>',
                        15000);
                }

            } else {

                if (ka.helpsystem){
                    ka.lastRequestBubble = ka.helpsystem.newBubble(
                    t('Request error'),
                    t('There has been a error occured during the last request. It looks like the server has currently some troubles. Please try it again.')+
                        "<br/><br/>"+t('Error code: %s').replace('%s', pResult.error)+
                        "<br/>"+t('Error message: %s').replace('%s', pResult.message)+
                        "<br/>"+'URI: %s'.replace('%s', this.options.url)+'<br/><a class="ka-Button" href="javascript:;">Details</a>',
                    15000);
                }
            }
        }

    }
})



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

window.ka.findWindow = function(pElement){

    if (!typeOf(pElement)){
        return logger('ka.findWindow(): pElement is not an element.')
    }

    var window = pElement.getParent('.kwindow-border');

    return window?window.retrieve('win'):false;

}

window.ka.entrypoint = {

    open: function(pEntrypoint, pOptions, pSource, pInline, pDependWindowId){

        var entrypoint = ka.entrypoint.get(pEntrypoint);

        if (!entrypoint){
            logger('Can not be found entrypoint: '+pEntrypoint);
            return false;
        }

        if (['custom', 'iframe', 'list', 'edit', 'add', 'combine'].contains(entrypoint.type)){
            ka.wm.open(pEntrypoint, pOptions, pDependWindowId, pInline, pSource);
        } else if(entrypoint.type == 'function'){
            ka.entrypoint.exec(entrypoint, pOptions, pSource);
        }


    },

    //executes a entrypont from type function
    exec: function(pEntrypoint, pOptions, pSource){

        if (pEntrypoint.functionType == 'global'){
            if (window[pEntrypoint.functionName]){
                window[pEntrypoint.functionName](pOptions);
            }
        } else if(pEntrypoint.functionType == 'code'){
            eval(pEntrypoint.functionCode);
        }

    },

    get: function(pEntrypoint){

        if (typeOf(pEntrypoint) != 'string') return false;

        var splitted = pEntrypoint.split('/');
        var extension = splitted[0];

        splitted.shift();

        var code = splitted.join('/');

        var tempEntry = false;

        var path = [], config, notFound = false;

        if (ka.settings.configs.admin.admin[extension]){
            config = ka.settings.configs.admin;
            splitted.unshift(extension);
        } else
            config = ka.settings.configs[extension];

        tempEntry = config.admin[splitted.shift()];
        path.push(tempEntry['title']);

        while(item = splitted.shift()){
            if (tempEntry.childs && tempEntry.childs[item]){
                tempEntry = tempEntry.childs[item];
                path.push(tempEntry['title']);
            } else {
                notFound = true;
                break;
            }
        };

        if (notFound) return false;

        tempEntry._path = path;
        tempEntry._module = extension;
        tempEntry._code = code;

        return tempEntry;
    }

};

ka.urlEncode = function(pValue){

    if (typeOf(pValue) == 'string'){
        if (pValue.test(/[\/=\?#:]/)){
            return encodeURIComponent(pValue);
        }
    } else if (typeOf(pValue) == 'array'){
        var result = '';
        Array.each(pValue, function(item){
             result += ka.urlEncode(item)+',';
        });
        return result.substr(0, result.length-1);
    } else if (typeOf(pValue) == 'object'){
        var result = '';
        Array.each(pValue, function(item, key){
             result += key+'='+ka.urlEncode(item)+',';
        });
        return result.substr(0, result.length-1);
    }

    return pValue;

}

ka.urlDecode = function(pValue){

    if (typeOf(pValue) != 'string') return pValue;

    try {
        return decodeURIComponent(pValue);
    } catch(e){
        return pValue;
    }

}

ka.mediaPath = function(pPath){

    if (typeOf(pPath) != 'string') return pPath;

    if (pPath.substr(0,1) == '#') return pPath;

    if (pPath.substr(0,1) == '/'){
        return _path+pPath.substr(1);
    } else if (pPath.substr(0,7) == 'http://'){
        return pPath;
    } else {
        return _path+'media/'+pPath;
    }

}

ka.getObjectPrimaryList = function(pObjectKey){
    var def = ka.getObjectDefinition(pObjectKey);

    var res = [];
    Object.each(def.fields, function(field, key){
        if (field.primaryKey)
            res.push(key);
    });

    return res;
}

ka.getObjectUrlId = function(pObjectKey, pItem){
    var pks = ka.getObjectPrimaryList(pObjectKey);

    var urlId = '';
    Array.each(pks, function(pk){
        urlId += ka.urlEncode(pItem[pk])+'-';
    });

    return urlId.substr(0, urlId.length-1);
}

/**
 * Returns the primarykey/s of an object by the internal object uri.
 *
 * @param  string pUrl   object://user/1
 * @return array|string  If we have only one pk, it returns a string, otherwise an array.
 */
ka.getObjectId = function(pUrl){
    if (typeOf(pUrl) != 'string') return pUrl;
    var res = [];

    if (pUrl.indexOf('object://') != -1){
        var id = pUrl.substr(10+pUrl.substr('object://'.length).indexOf('/'));
    } else if (pUrl.indexOf('/') != -1){
        var id = pUrl.substr(pUrl.indexOf('/'));
    } else {
        var id = pUrl;
    }

    if (id.indexOf(',') != -1){
        Array.each(id.split(','), function(tId){
            res.push(ka.urlDecode(tId));
        });
        return res;
    } else {
        if (id.indexOf(',') != -1){
            Array.each(id.split(','), function(tId){
                res.push(ka.urlDecode(tId));
            });
            return res.substr(0, res.length-1);
        } else {
            return ka.urlDecode(id);
        }
    }
}


ka.getObjectLabels = function(pFields, pItem, pObjectId, pRelationsAsArray){

    var data = pItem, dataKey;
    Object.each(pFields, function(field, fieldId){
        dataKey = fieldId;
        if (pRelationsAsArray && dataKey.indexOf('.') > 0) dataKey = dataKey.split('.')[0];

        data[dataKey] = ka.getObjectLabel(pItem, field, fieldId, pObjectId, pRelationsAsArray);
    }.bind(this));
    
    return data;
}

ka.getObjectLabel = function(pValue, pField, pFieldId, pObjectId, pRelationsAsArray){

    var value = pValue[pFieldId] || '';

    var field = pField;
    if (field.type == 'predefined'){
        field = ka.getObjectDefinition(field.object).fields[field.field];
    }

    if (field.format == 'timestamp') {
        value = new Date(value * 1000).toLocaleString();
    }

    if (field.type == 'datetime' || field.type == 'date') {
        if (value != 0 && value) {
            var format = ( !field.format ) ? '%d.%m.%Y %H:%M' : field.format;
            value = new Date(value * 1000).format(format);
        } else {
            value = '';
        }
    }

    //relations
    var label, relation;
    if (field.type == 'object' || !field.type){
        if (pFieldId.indexOf('.') > 0){
            relation = pFieldId.split('.')[0];
            label = pFieldId.split('.')[1];
        } else {
            //find label
            var def = ka.getObjectDefinition(pObjectId);
            label = def.objectLabel;
        }
    }
    if (typeOf(pValue[relation]) == 'object'){
        //to-one relation
        value = {};
        if (pRelationsAsArray){
            value[label] = pValue[relation][label];
            return value;
        } else {
            return pValue[relation][label];
        }
    }

    if (typeOf(pValue[relation]) == 'array'){
        //to-many relation
        //we join by pField['join'] char, default is ','
        value = [];
        Array.each(pValue[relation], function(relValue){
            value.push(relValue[label]);
        });
        var joined = value.join(pField['join'] || ', ');
        if (pRelationsAsArray){
            value = {};
            value[label] = joined;
            return value;
        } else {
            return joined;
        }
    }

    if (field.type == 'select') {
        value = pValue[pFieldId +'_'+ pField.table_label] || pValue[pFieldId +'_'+ pField.tableLabel] || pValue[pFieldId + '__label'];
    }

    if (field.type == 'imagemap'){
    }

    if (field.imageMap) {
        return '<img src="' + _path + field.imageMap[value] + '"/>';
    } else if (field.type == 'html') {
        return value;
    } else if(typeOf(value) == 'string'){
        return value.replace('<', '&lt;').replace('>', '&gt;');
    } else if(typeOf(value) == 'number'){
        return value;
    }
    return '';
}

ka.getExtensionTitle = function(pExtensionKey){

    var config = ka.settings.configs[pExtensionKey];
    if (!config) return false;

    if (typeOf(config.title) != 'string'){
        return config.title[window._session.lang] || config.title['en'];
    }

    return config.title;

}


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
                        ka.wm.open(subsubresults[1], subsubresults[2]);
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
        new Request({url: _path + 'admin/?admin-users-logout=1'}).post();
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

ka.ai.loginSuccess = function (pId, pAlready) {

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

    ka.ai.loginName.value = pId.username;
    window._sid = pId.sessionid;
    window._session.sessionid = pId.sessionid;
    window._user_id = pId.user_id

    $('user-username').set('text', ka.ai.loginName.value);
    $('user-username').onclick = function () {
        ka.wm.open('users/profile', {values: {id: pId.user_id}});
    }

    window._session.user_id = pId.user_id;
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
