if (typeof ka == 'undefined') window.ka = {};

ka.clipboard = {};
ka.settings = {};

ka.performance = false;
ka.streamParams = {};

ka.uploads = {};
ka._links = {};

PATH_MODULE = 'module/';
PATH_MEDIA = 'media/';


/**
 * @deprecated Use t() instead
 * @param string p
 */
window._ = function (p) {
    return t(p);
    //return _kml2html(p);
};

window.addEvent('domready', function(){
    ka.adminInterface = new ka.AdminInterface();
});


if (typeOf(ka.langs) != 'object') this.langs = {};

window.logger = function(){
    if (typeOf(console) != "undefined") {
        var args = arguments;
        if (args.length == 1) args = args[0];
        console.log(args);
    }
};

document.addEvent('touchmove', function (event) {
    event.preventDefault();
});


/**
 * Opens the frontend in a new tab.
 */
ka.openFrontend = function () {
    if (top) {
        top.open(_path, '_blank');
    }
};

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

window.tf = function(){
    var args = Array.from(arguments);
    var text = args.shift();
    return text.sprintf.apply(text, args);
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
        throw 'ka.findWindow(): pElement is not an element.';
    }

    var window = pElement.getParent('.kwindow-border');

    return window?window.retrieve('win'):false;

}

window.ka.entrypoint = {

    open: function(pEntrypoint, pOptions, pSource, pInline, pDependWindowId){

        var entrypoint = ka.entrypoint.get(pEntrypoint);

        if (!entrypoint){
            throw 'Can not be found entrypoint: '+pEntrypoint;
            return false;
        }

        if (['custom', 'iframe', 'list', 'edit', 'add', 'combine'].contains(entrypoint.type)){
            ka.wm.open(pEntrypoint, pOptions, pDependWindowId, pInline, pSource);
        } else if(entrypoint.type == 'function'){
            ka.entrypoint.exec(entrypoint, pOptions, pSource);
        }


    },

    getRelative: function(pCurrent, pEntryPoint){

        if (typeOf(pEntryPoint) != 'string' || !pEntryPoint) return pCurrent;

        if (pEntryPoint.substr(0,1) == '/')
            return pEntryPoint;

        var current = pCurrent+'';
        if (current.substr(current.length-1, 1) != '/')
            current += '/';

        return current+pEntryPoint;


    },

    //executes a entry point from type function
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

        if (ka.settings.configs.admin.entryPoints[extension]){
            config = ka.settings.configs.admin;
            splitted.unshift(extension);
        } else
            config = ka.settings.configs[extension];
        if (!config){
            throw 'Config not found for module '+extension;
        }

        tempEntry = config.entryPoints[splitted.shift()];
        path.push(tempEntry['title']);

        while(item = splitted.shift()){
            if (tempEntry.children && tempEntry.children[item]){
                tempEntry = tempEntry.children[item];
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



ka.newBubble = function(pTitle, pText, pDuration){
    return ka.helpsystem.newBubble(pTitle, pText, pDuration);
}

/**
 * Adds a prefix to the keys of pFields.
 * Good to group some values of fields of ka.Parse.
 *
 * Example:
 *
 *   pFields = {
 *      field1: {type: 'text', label: 'Field 1'},
 *      field2: {type: 'checkbox', label: 'Field 2'}
 *   }
 *
 *   pPrefix = 'options'
 *
 *   pFields will be changed to:
 *   {
 *      'options[field1]': {type: 'text', label: 'Field 1'},
 *      'options[field2]': {type: 'checkbox', label: 'Field 2'}
 *   }
 *
 * @param {Array} pFields Reference to object.
 * @param {String} pPrefix
 */
ka.addFieldKeyPrefix = function(pFields, pPrefix){
    Object.each(pFields, function(field, key){
        pFields[pPrefix+'['+key+']'] = field;
        delete pFields[key];
        if (pFields.children)
            ka.addFieldKeyPrefix(field.children, pPrefix);
    });
}

/**
 * Resolve path notations and returns the appropriate class.
 *
 * @param {String} pClassPath
 * @return {Class|Function}
 */
ka.getClass = function(pClassPath){
    pClassPath = pClassPath.replace('[\'', '');
    pClassPath = pClassPath.replace('\']', '');

    if (pClassPath.indexOf('.') > 0 ){
        var path = pClassPath.split('.');
        var clazz = null;
        Array.each(path, function(item){
            clazz = clazz ? clazz[item] : window[item];
        });
        return clazz;
    }

    return window[pClassPath];
}

/**
 * Encodes a value from url usage.
 * If Array, it encodes the whole array an implodes it with comma.
 * If Object, it encodes the while object an implodes the <key>=<value> pairs with a comma.
 *
 * @param {String} pValue
 * @return {STring}
 */
ka.urlEncode = function(pValue){

    if (typeOf(pValue) == 'string'){
        return encodeURIComponent(pValue);
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

/**
 * Decodes a value for url usage.
 * @param {String} pValue
 * @return {String}
 */
ka.urlDecode = function(pValue){

    if (typeOf(pValue) != 'string') return pValue;

    try {
        return decodeURIComponent(pValue);
    } catch(e){
        return pValue;
    }

}

/**
 * Returns a absolute path.
 * If pPath begins with # it returns pPath
 * if pPath is not a string it returns pPath
 * if pPath contains http:// on the beginning it returns pPath
 *
 * @param {String} pPath
 * @return {String}
 */
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

/**
 * Returns a list of the primary keys if pObjectKey.
 *
 * @param {String} pObjectKey
 * @return {Array}
 */
ka.getObjectPrimaryList = function(pObjectKey){
    var def = ka.getObjectDefinition(pObjectKey);

    var res = [];
    Object.each(def.fields, function(field, key){
        if (field.primaryKey)
            res.push(key);
    });

    return res;
}

/**
 * Return only the primary keys of pItem as object.
 *
 * @param {String} pObjectKey
 * @param {Object} pItem
 */
ka.getObjectPk = function(pObjectKey, pItem){
    var pks = ka.getObjectPrimaryList(pObjectKey);
    var result = {};
    Array.each(pks, function(pk){
        result[pk] = pItem[pk];
    });
    return result;
}

/**
 * This just cut off object://<objectName>/ and returns the primary key part.
 *
 * @param {String} pUri Internal uri
 * @return {String}
 */
ka.getCroppedObjectId = function(pUri){

    if (pUri.indexOf('object://') == 0)
        pUri = pUri.substr(9);

    var idx = pUri.indexOf('/');

    return pUri.substr(idx+1);
}

/**
 * Returns the id of an object item for the usage in urls (internal uri's) - urlencoded.
 * If you need the full uri, you ka.getObjectUrl
 *
 * @param {String} pObjectKey
 * @param {Array}  pItem
 * @return {String} urlencoded internal uri part of the id.
 */
ka.getObjectUrlId = function(pObjectKey, pItem){
    if (!pItem) throw 'pItem missing.';
    var pks = ka.getObjectPrimaryList(pObjectKey);

    if (pks.length == 0 ) throw pObjectKey+' does not have primary keys.';

    var urlId = '';
    if (pks.length == 1 && typeOf(pItem) != 'object'){
        return ka.urlEncode(pItem)+'';
    } else {
        Array.each(pks, function(pk){
            urlId += ka.urlEncode(pItem[pk])+',';
        });
        return urlId.substr(0, urlId.length-1);
    }

}

/**
 * Just convert the arguments into a new string :
 *    object://<pObjectKey>/<pId>
 *
 *
 * @param {String} pObjectKey
 * @param {String} pId Has to be urlencoded (use ka.urlEncode())
 * @return {String}
 */
ka.getObjectUrl = function(pObjectKey, pId){
    return 'object://'+pObjectKey+'/'+pId;
}

/**
 * Returns the object key (not id) from an object uri.
 *
 * @param pUrl
 */
ka.getObjectKey = function(pUrl){
    if (typeOf(pUrl) != 'string') throw 'pUrl is not a string';

    if (pUrl.indexOf('object://') == 0)
        pUrl = pUrl.substr(9);

    var idx = pUrl.indexOf('/');
    if (idx == -1) return pUrl;

    return pUrl.substr(0, idx);
}

/**
 * Returns the PK of an object from a internal object uri as object or string.
 * Since the ID part of the url is urlencoded, we return it urldecoded.
 *
 * Examples:
 *
 *  pUrl = object://user/1
 *  => 1
 *
 *  pUrl = object://user/1/3
 *  => [1,3]
 *
 *  pUrl = object://file/%2Fadmin%2Fimages%2Fhi.jpg
 *  => /admin/images/hi.jpg
 *
 * @param  {String} pUrl   object://user/1
 * @return {String|Object}  If we have only one pk, it returns a string, otherwise an array.
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

    if (id.indexOf(';') != -1){
        Array.each(id.split(';'), function(tId){
            res.push(ka.urlDecode(tId));
        });
        return res;
    } else {
        return ka.urlDecode(id);
    }
}

/**
 * Returns the object label, based on a label field or label template (defined
 * in the object definition).
 * This function calls perhaps the REST API to get all information.
 * If you already have an item object, you should probably use ka.getObjectLabelByItem();
 *
 * You can call this function really fast consecutively, since it queues all and fires
 * only one REST API call that receives all items at once per object key.(at least after 50ms of the last call).
 *
 * @param {String} pUri
 * @param {Function} pCb the callback function.
 *
 */
ka.getObjectLabel = function(pUri, pCb){

    var objectKey = ka.getObjectKey(pUri);

    if (ka.getObjectLabelBusy[objectKey]){
        ka.getObjectLabel.delay(10, ka.getObjectLabel, [pUri, pCb]);
        return;
    }

    if (ka.getObjectLabelQTimer[objectKey])
        clearTimeout(ka.getObjectLabelQTimer[objectKey]);

    if (!ka.getObjectLabelQ[objectKey])
        ka.getObjectLabelQ[objectKey] = {};

    if (!ka.getObjectLabelQ[objectKey][pUri])
        ka.getObjectLabelQ[objectKey][pUri] = [];

    ka.getObjectLabelQ[objectKey][pUri].push(pCb);

    ka.getObjectLabelQTimer[objectKey] = (function(){

        ka.getObjectLabelBusy = true;

        var uri = 'object://'+ka.urlEncode(objectKey)+'/';
        Object.each(ka.getObjectLabelQ[objectKey], function(cbs, requestedUri){
            uri += ka.getCroppedObjectId(requestedUri)+';';
        });
        if (uri.substr(uri.length-1, 1)==';')
            uri = uri.substr(0, uri.length-1);

        new Request.JSON({url: _path + 'admin/objects',
            noCache: 1, noErrorReporting: true,
            onComplete: function(pResponse){

                var result, id, cb;

                Object.each(pResponse.data, function(item, pk){

                    if (item === null) return;

                    id = 'object://'+objectKey+'/'+pk;
                    result = ka.getObjectLabelByItem(objectKey, item);

                    //if the pUri and id differs, then the appropriate cb
                    //is not called. Like 'files' object that accepts
                    //two ids: the numeric id and the path.
                    //TODO, search solution for this

                    if (ka.getObjectLabelQ[objectKey][id]){
                        while( (cb = ka.getObjectLabelQ[objectKey][id].pop()) ){
                            cb(result);
                        }
                    }

                });

                //call the callback of invalid requests with false argument.
                Object.each(ka.getObjectLabelQ[objectKey], function(cbs){
                    cbs.each(function(cb){
                        cb.attempt(false);
                    });
                });

                ka.getObjectLabelBusy[objectKey] = false;
                ka.getObjectLabelQ[objectKey] = {};

            }}).get({uri: uri, returnKeyAsRequested: 1});

    }).delay(50);

}
ka.getObjectLabelQ = {};
ka.getObjectLabelBusy = {};
ka.getObjectLabelQTimer = {};

/**
 * Returns the object label, based on a label field or label template (defined
 * in the object definition).
 *
 * @param {String} pObjectKey
 * @param {Object} pItem
 * @param {String} pMode 'default', 'field' or 'tree'. Default is 'default'
 * @param {Object} pDefinition overwrite definitions stored in the pObjectKey
 * @return {String}
 */
ka.getObjectLabelByItem = function(pObjectKey, pItem, pMode, pDefinition){

    var definition = ka.getObjectDefinition(pObjectKey);
    if (!definition) throw 'Definition not found '+pObjectKey;

    var template = (pDefinition && pDefinition.labelTemplate) ? pDefinition.labelTemplate : definition.labelTemplate;
    var label = (pDefinition && pDefinition.labelField) ? pDefinition.labelField : definition.labelField;

    if (pDefinition){
        ['fieldTemplate', 'fieldLabel', 'treeTemplate', 'treeLabel'].each(function(map){
            if (typeOf(pDefinition[map]) !== 'null') definition[map] = pDefinition[map];
        });
    }

    /* field ui */
    if (pMode == 'field' && definition.fieldTemplate)
        template = definition.fieldTemplate;

    if (pMode == 'field' && definition.fieldLabel)
        label = definition.fieldLabel;

    /* tree */
    if (pMode == 'tree' && definition.treeTemplate)
        template = definition.treeTemplate;

    if (pMode == 'tree' && definition.treeLabel)
        label = definition.treeLabel;

    if (!template){
        //we only have an label field, so return it
        return mowla.fetch('{label}', {label: pItem[label]});
    }

    return mowla.fetch(template, pItem);
}

/**
 * Returns all labels for a object item.
 *
 * @param {Object}  pFields  The array of fields definition, that defines /how/ you want to show the data. limited range of 'type' usage.
 * @param {Object}  pItem
 * @param {String} pObjectKey
 * @param {Boolean} pRelationsAsArray Relations would be returned as arrays/origin or as string(default).
 *
 * @return {Object}
 */
ka.getObjectLabels = function(pFields, pItem, pObjectKey, pRelationsAsArray){

    var data = pItem, dataKey;
    Object.each(pFields, function(field, fieldId){
        dataKey = fieldId;
        if (pRelationsAsArray && dataKey.indexOf('.') > 0) dataKey = dataKey.split('.')[0];

        data[dataKey] = ka.getObjectFieldLabel(pItem, field, fieldId, pObjectKey, pRelationsAsArray);
    }.bind(this));

    return data;
}

/**
 * Returns a single label for a field of a object item.
 *
 * @param {Object} pValue
 * @param {Object} pField The array of fields definition, that defines /how/ you want to show the data. limited range of 'type' usage.
 * @param {String} pFieldId
 * @param {String} pObjectKey
 * @param {Boolean} pRelationsAsArray
 *
 * @return {String}
 */
ka.getObjectFieldLabel = function(pValue, pField, pFieldId, pObjectKey, pRelationsAsArray){

    var fields = ka.getObjectDefinition(pObjectKey);
    if (!fields) throw 'Object not found '+pObjectKey;

    var fieldId = pFieldId;
    if (pFieldId.indexOf('.') > 0){
        fieldId = pFieldId.split('.')[0];
    }

    fields = fields['fields'];
    var field = fields[fieldId];

    var showAsField = pField || field;
    if (!showAsField.type){
        Object.each(field, function(v, i){
            if (!showAsField[i])
                showAsField[i] = v;
        });
    }

    pValue = Object.clone(pValue);

    if (!field) return typeOf(pValue[fieldId]) != 'null' ? pValue[fieldId] : '';

    var value = pValue[fieldId] || '';

    if (showAsField.type == 'predefined'){
        showAsField = ka.getObjectDefinition(field.object).fields[field.field];
    }

    if (showAsField.format == 'timestamp') {
        value = new Date(value * 1000).toLocaleString();
    }

    if (showAsField.type == 'datetime' || showAsField.type == 'date') {
        if (value != 0 && value) {
            var format = ( !showAsField.format ) ? '%d.%m.%Y %H:%M' : showAsField.format;
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
            var def = ka.getObjectDefinition(pObjectKey);
            label = def.labelField;
        }
    }

    if (typeOf(pValue[relation]) == 'object'){
        //to-one relation
        value = {};
        if (pRelationsAsArray){
            value[label] = pValue[relation][label];
            return value;
        } else {
            return pValue[relation] ? pValue[relation][label] : '';
        }
    }

    if (typeOf(pValue[relation]) == 'array'){
        //to-many relation
        //we join by pField['join'] char, default is ';'
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

    if (showAsField.type && showAsField.type.toLowerCase() == 'imagemap'){
        //TODO
    }

    if (field.imageMap) {
        return '<img src="' + _path + field.imageMap[value] + '"/>';
    }
    return value;
}

/**
 * Returns the module title of the given module key.
 *
 * @param {String} pKey
 * @return {String} Or false, if the module does not exist/its not activated.
 */
ka.getExtensionTitle = function(pKey){

    var config = ka.settings.configs[pKey];
    if (!config) return false;

    if (typeOf(config.title) != 'string'){
        return config.title[window._session.lang] || config.title['en'];
    }

    return config.title;
}

ka.tryLock = function (pWin, pKey, pForce) {
    if (!pForce) {

        new Request.JSON({url: _path + 'admin/backend/tryLock', noCache: 1, onComplete: function (res) {

            if (!res.locked) {
                ka.lockNotPossible(pWin, res);
            }

        }}).get({key: pKey, force: pForce ? 1 : 0});

    } else {
        ka.lockContent(pKey);
    }
}

ka.alreadyLocked = function (pWin, pResult) {

    pWin._alert(t('Currently, a other user has this content open.'));

}

ka.bytesToSize = function (bytes) {
    var sizes = ['Bytes', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];
    if (!bytes) return '0 Bytes';
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    if (i == 0) {
        return (bytes / Math.pow(1024, i)) + ' ' + sizes[i];
    }
    return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
};


ka.clearCache = function () {

    if (!ka.cacheToolTip) {
        ka.cacheToolTip = new ka.Tooltip($('ka-btn-clear-cache'), t('Clearing cache ...'), 'top');
    }
    ka.cacheToolTip.show();

    new Request.JSON({url: _path + 'admin/backend/cache', noCache: 1, onComplete: function (res) {
        ka.cacheToolTip.stop(t('Cache cleared'));
    }}).delete();


}

ka.getDomain = function (pRsn) {
    var result = [];
    ka.settings.domains.each(function (domain) {
        if (domain.id == pRsn) {
            result = domain;
        }
    })
    return result;
}

ka.loadSettings = function (pOnlyThisKeys) {
    if (!ka.settings) ka.settings = {};

    new Request.JSON({url: _path + 'admin/backend/settings', noCache: 1, async: false, onComplete: function (res) {
        if (res.error == 'access_denied') return;

        Object.each(res.data, function(val,key){
            ka.settings[key] = val;
        })

        ka.settings['images'] = ['jpg', 'jpeg', 'bmp', 'png', 'gif', 'psd'];

        if (!ka.settings.user)
            ka.settings.user = {};

        if (typeOf(ka.settings.user) != 'object')
            ka.settings.user = {};

        if (!ka.settings['user']['windows'])
            ka.settings['user']['windows'] = {};

        if (!ka.settings.user.userBg)
            ka.settings.user.userBg = 'admin/images/userBgs/defaultImages/color-blue.jpg';

        if (ka.settings.user && ka.settings.user.userBg) {
            document.id(document.body).setStyle('background-image', 'url(' + _path + PATH_MEDIA + ka.settings.user.userBg + ')');
        }

        if (ka.settings.system && ka.settings.system.systemTitle) {
            document.title = ka.settings.system.systemTitle + t(' | Kryn.cms Administration');
        }

    }.bind(this)}).get({lang: window._session.lang, keys: pOnlyThisKeys});
}

ka.loadLanguage = function (pLang) {
    if (!pLang) pLang = 'en';
    window._session.lang = pLang;

    Cookie.write('kryn_language', pLang);

    Asset.javascript(_path + 'admin/ui/languagePluralForm?lang=' + pLang);

    new Request.JSON({url: _path + 'admin/ui/language?lang=' + pLang, async: false, noCache: 1, onComplete: function(pResponse){
        ka.lang = pResponse.data;
        Locale.define('en-US', 'Date', ka.lang);
    }}).get();

}


ka.saveUserSettings = function () {
    if (ka.lastSaveUserSettings) {
        ka.lastSaveUserSettings.cancel();
    }

    ka.settings.user = new Hash(ka.settings.user);

    ka.lastSaveUserSettings = new Request.JSON({url: _path + 'admin/backend/user-settings', noCache: 1, onComplete: function (res) {
    }}).post({ settings: JSON.encode(ka.settings.user) });
}

ka.resetWindows = function () {
    ka.settings.user['windows'] = new Hash();
    ka.saveUserSettings();
    ka.wm.resizeAll();
}


ka.addStreamParam = function (pKey, pVal) {
    ka.streamParams[pKey] = pVal;
}

ka.removeStreamParam = function (pKey) {
    delete ka.streamParams[pKey];
}


ka.loadStream = function () {
    if (ka._lastStreamid) {
        clearTimeout(ka._lastStreamid);
    }

    if (ka._lastStreamCounter) {
        clearTimeout(ka._lastStreamCounter);
    }

    _lastStreamCounter = (function () {
        if (window._session.user_id > 0) {
            new Request.JSON({url: _path + 'admin/backend/stream', noCache: 1, onComplete: function (res) {
                if (res) {
                    if (res.error == 'access_denied') {
                        ka.ai.logout(true);
                    } else {
                        ka.streamParams.last = res.last;
                        window.fireEvent('stream', res);
                        $('serverTime').set('html', res.time);
                    }
                }
                ka._lastStreamid = ka.loadStream.delay(5 * 1000);
            }}).post(ka.streamParams);
        }
    }).delay(50);
}

ka.getClipboard = function () {
    return ka.clipboard;
}

ka.setClipboard = function (pTitle, pType, pValue) {
    ka.clipboard = { type: pType, value: pValue };
    //ka.clipboardMenu.set('html', pTitle);
    //ka.clipboardMenu.tween('top', 50);
}

ka.clearClipboard = function () {
    ka.clipboard = {};
    //ka.clipboardMenu.tween('top', 20);
}

ka.closeDialogsBodys = [];

ka.closeDialog = function () {

    var killedOne = false;
    Array.each(ka.closeDialogsBodys, function(body){
        if (killedOne) return;

        var last = document.body.getLast('.ka-dialog-overlay');
        if (last){
            killedOne = true;
            last.close();
        }
    });
}

ka.openDialog = function (item) {
    if (!item.element || !item.element.getParent) {
        throw 'Got no element.';
    }

    var target = document.body;

    if (item.target && item.target.getWindow())
        target = item.target.getWindow().document.body;


    if (!ka.closeDialogsBodys.contains(target))
        ka.closeDialogsBodys.push(target);


    var autoPositionLastOverlay = new Element('div', {
        'class': 'ka-dialog-overlay',
        style: 'position: absolute; left:0px; top: 0px; right:0px; bottom:0px;background-color: white; z-index: 201000;',
        styles: {
            opacity: 0.001
        }
    }).addEvent('click', function (e) {

        ka.closeDialog();
        e.stopPropagation();
        this.fireEvent('close');
        if (item.onClose) item.onClose();

    }).inject(target);

    autoPositionLastOverlay.close = function(){
        autoPositionLastOverlay.destroy();
        delete autoPositionLastOverlay;
    };

    item.element.setStyle('z-index', 201001);

    var size = item.target.getWindow().getScrollSize();

    autoPositionLastOverlay.setStyles({
        width: size.x,
        height: size.y
    });

    ka.autoPositionLastItem = item.element;

    item.element.inject(target);

    if (!item.offset) item.offset = {};

    if (!item.primary) {
        item.primary = {
            'position': 'bottomRight',
            'edge': 'upperRight',
            offset: item.offset
        }
    }
    if (!item.secondary) {
        item.secondary = {
            'position': 'upperRight',
            'edge': 'bottomRight',
            offset: item.offset
        }
    }

    item.primary.relativeTo = item.target;
    item.secondary.relativeTo = item.target;

    item.element.position(item.primary);

    var pos = item.element.getPosition();
    var size = item.element.getSize();

    var bsize = item.element.getParent().getSize();
    var bscroll = item.element.getParent().getScroll();
    var height;

    item.element.setStyle('height', '');

    item.minHeight = item.element.getSize().y;

    if (size.y + pos.y > bsize.y + bscroll.y) {
        height = bsize.y - pos.y - 10;
    }

    if (height) {
        if (item.minHeight && height < item.minHeight) {
            item.element.position(item.secondary);
        } else {
            item.element.setStyle('height', height);
        }
    }

    return autoPositionLastOverlay;
}

ka.getPrimariesForObject = function(pObjectKey){

    var definition = ka.getObjectDefinition(pObjectKey);

    var result = {};

    if (!definition) {
        logger('Can not found object definition for object "'+pObjectKey+'"');
        return;
    }

    Object.each(definition.fields, function(field, fieldKey){

        if (field.primaryKey){
            result[fieldKey] = Object.clone(field);
        }

    });

    return result;
}

ka.getPrimaryListForObject = function(pObjectKey){

    var definition = ka.getObjectDefinition(pObjectKey);

    var result = [];

    if (!definition) {
        logger('Can not found object definition for object "'+pObjectKey+'"');
        return;
    }

    Object.each(definition.fields, function(field, fieldKey){

        if (field.primaryKey){
            result.push(fieldKey);
        }

    });

    return result;
}

ka.getObjectDefinition = function(pObjectKey){

    if (typeOf(pObjectKey) != 'string') throw 'pObjectKey is not a string: '+pObjectKey;
    var module = (""+pObjectKey.split('\\')[0]).toLowerCase();
    var name = pObjectKey.split('\\')[1];

    if (ka.settings.configs[module] && ka.settings.configs[module]['objects'][name]){
        var config = ka.settings.configs[module]['objects'][name];
        config._key = pObjectKey;
        return config;
    }

}


ka.getFieldCaching = function () {

    return {
        'cache_type': {
            label: _('Cache storage'),
            type: 'select',
            items: {
                'memcached': _('Memcached'),
                'redis': _('Redis'),
                'apc': _('APC'),
                'files': _('Files')
            },
            'depends': {
                'cache_params[servers]': {
                    needValue: ['memcached', 'redis'],
                    'label': 'Servers',
                    'type': 'array',
                    startWith: 1,
                    'width': 310,
                    'columns': [
                        {'label': _('IP')},
                        {'label': _('Port'), width: 50}
                    ],
                    'fields': {
                        ip: {
                            type: 'text',
                            width: '95%',
                            empty: false
                        },
                        port: {
                            type: 'number',
                            width: 50,
                            empty: false
                        }
                    }
                },
                'cache_params[files_path]': {
                    needValue: 'files',
                    type: 'text',
                    label: 'Caching directory',
                    'default': 'cache/object/'
                }
            }
        }
    }
}


ka.renderLayoutElements = function (pDom, pClassObj) {

    var layoutBoxes = {};

    pDom.getWindow().$$('.kryn_layout_content, .kryn_layout_slot').each(function (item) {

        var options = {};
        if (item.get('params')) {
            var options = JSON.decode(item.get('params'));
        }

        if (item.hasClass('kryn_layout_slot')) {
            layoutBoxes[ options.id ] = new ka.LayoutBox(item, options, pClassObj);
        } //options.name, this.win, options.css, options['default'], this, options );
        else {
            layoutBoxes[ options.id ] = new ka.ContentBox(item, options, pClassObj);
        }

    });

    return layoutBoxes;
}

ka.pregQuote = function(str){
    // http://kevin.vanzonneveld.net
    // +   original by: booeyOH
    // +   improved by: Ates Goral (http://magnetiq.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Onno Marsman
    // *     example 1: preg_quote("$40");
    // *     returns 1: '\$40'
    // *     example 2: preg_quote("*RRRING* Hello?");
    // *     returns 2: '\*RRRING\* Hello\?'
    // *     example 3: preg_quote("\\.+*?[^]$(){}=!<>|:");
    // *     returns 3: '\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:'

    return (str+'').replace(/([\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:])/g, "\\$1");
}

initWysiwyg = function (pElement, pOptions) {

    var options = {
        extraClass: 'SilkTheme',
        //flyingToolbar: true,
        dimensions: {
            x: '100%'
        },
        actions: 'bold italic underline strikethrough | formatBlock justifyleft justifycenter justifyright justifyfull | insertunorderedlist insertorderedlist indent outdent | undo redo | tableadd | createlink unlink | image | toggleview'
    };

    if (pOptions) {
        options = Object.append(options, pOptions);
    }

    return new MooEditable(document.id(pElement), options);
}
/*
 initSmallTiny
 initTiny
 initResizeTiny
 initTinyWithoutResize*/
