/* ka window.manager */

window.addEvent('resize', function () {
    ka.wm.checkDimensionsAndSendResize();
});

ka.wm = {

    windows: {},

    /* depend: [was => mitWem] */
    depend: {},
    lastWindow: 0,
    events: {},
    zIndex: 1000,

    openWindow: function (pModule, pWindowCode, pLink, pParentWindowId, pParams, pInline) {

        var id = pModule + '::' + pWindowCode;
        if (pLink && pLink.onlyOnce && this.checkOpen(id)) {
            return this.toFront(id);
        }
        return ka.wm.loadWindow(pModule, pWindowCode, pLink, pParentWindowId, pParams, pInline);
    },

    checkDimensionsAndSendResize: function () {
        if (ka.wm.goDimensionsCheck) {
            clearTimeout(ka.wm.goDimensionsCheck);
        }

        ka.wm.goDimensionsCheck = (function(){
            try {
                ka.wm._checkDimensions();
            } catch(e){
                logger('checkDimensions failed.');
            }
        }).delay(300);
    },

    _checkDimensions: function () {
        Object.each(ka.wm.windows, function (win) {
            win.checkDimensions();
            win.fireEvent('resize');
        });
    },

    addEvent: function (pEv, pFunc) {
        if (!ka.wm.events[pEv]) {
            ka.wm.events[pEv] = [];
        }

        ka.wm.events[pEv].include(pFunc);
    },

    fireEvent: function (pEv) {
        if (ka.wm.events[pEv]) {
            Object.each(ka.wm.events[pEv], function (func) {
                $try(func);
            });
        }
    },

    open: function (pTarget, pParams, pParentWindowId, pInline) {
        var firstSlash = pTarget.indexOf('/');
        if (firstSlash == -1) return logger('Invalid entrypoint: '+pTarget);
        var module = pTarget.substr(0, firstSlash);
        var path = pTarget.substr(firstSlash + 1, pTarget.length);
        return ka.wm.openWindow(module, path, null, pParentWindowId, pParams, pInline);
    },

    getWindow: function (pId) {
        if (pId == -1){
            pId == ka.wm.lastWindow;
        }
        return ka.wm.windows[ pId ];
    },

    sendSoftReload: function (pTarget) {
        var firstSlash = pTarget.indexOf('/');
        var module = pTarget.substr(0, firstSlash);
        var path = pTarget.substr(firstSlash + 1, pTarget.length);
        ka.wm.softReloadWindows(module, path);
    },

    softReloadWindows: function (pModule, pCode) {
        Object.each(ka.wm.windows, function (win) {
            if (win && win.module == pModule && win.code == pCode) {
                win.softReload();
            }
        });
    },

    resizeAll: function () {
        ka.settings['user']['windows'] = {};
        Object.each(ka.wm.windows, function (win) {
            win.loadDimensions();
        });
    },

    setFrontWindow: function (pWindow) {
        Object.each(ka.wm.windows, function (win, winId) {
            if (win && pWindow.id != winId) win.inFront = false;
        });
        ka.wm.lastWindow = pWindow;
    },

    loadWindow: function (pModule, pWindowCode, pLink, pParentWindowId, pParams, pInline) {
        var instance = Object.getLength(ka.wm.windows) + 1;

        if (pParentWindowId == -1)
            pParentWindowId = ka.wm.lastWindow?ka.wm.lastWindow.id:false;

        if (pParentWindowId && !ka.wm.getWindow(pParentWindowId)) throw 'Parent window not found.';

        if (pParentWindowId && pInline) {
            ka.wm.getWindow(pParentWindowId).prepareInlineContainer();
        }

        ka.wm.windows[instance] = new ka.Window(pModule, pWindowCode, pLink, instance, pParams, pInline, pParentWindowId);
        ka.wm.windows[instance].toFront();
        if (pParentWindowId){
            ka.wm.getWindow(pParentWindowId).setChildren(ka.wm.windows[instance]);
        }
        ka.wm.updateWindowBar();
    },

    newListBar: function (pWindow) {
        pWindow.setBarButton(bar);
        var bar = new Element('a', {
            'class': 'wm-bar-item',
            title: pWindow.getFullTitle()
        });

        pWindow.setBarButton(bar);

        bar.addEvent('click', function () {

            if (pWindow.isOpen && pWindow.inFront) {
                if (!document.body.hasClass('ka-no-desktop'))
                    pWindow.minimize();
            } else if (!pWindow.inFront || !pWindow.isOpen) {
                pWindow.toFront();
            }
        });
        shortTitle = pWindow.getFullTitle();

        if (shortTitle.length > 22) {
            shortTitle = shortTitle.substr(0, 19) + '...';
        }

        if (shortTitle == ''){
            bar.setStyle('display', 'none');
        }

        bar.set('text', shortTitle);


        if (document.body.hasClass('ka-no-desktop')){
            new Element('div', {
                'class': 'wm-bar-item-closer',
                text: 'x'
            })
            .addEvent('click', function(e){
                e.stopPropagation();
                pWindow.close(true);
            })
            .inject(bar);
        }

        return bar;
    },

    close: function (pWindow) {

        var parent = pWindow.getParentId();
        if (parent){
            parent = ka.wm.getWindow(parent);
            parent.removeChildren();
        }

        delete ka.wm.windows[pWindow.id];

        if (parent){
            parent.toFront();
        } else {
            ka.wm.bringLastWindow2Front();
        }

        ka.wm.updateWindowBar();
    },

    bringLastWindow2Front: function(){

        var lastWindow;

        Object.each(ka.wm.windows, function (win) {
            if (!win) return;
            if (!lastWindow || win.border.getStyle('z-index') > lastWindow.border.getStyle('z-index')){
                lastWindow = win;
            }
        });

        if (lastWindow){
            lastWindow.toFront();
        }
    },

    getWindowsCount: function () {
        var count = 0;
        Object.each(ka.wm.windows, function (win, winId) {
            if (!win) return;
            if (win.inline) return;
            count++;
        });
        return count;
    },

    updateWindowBar: function () {

        document.id('windowList').getChildren().destroy();

        var c = 0;
        Object.each(ka.wm.windows, function (win, winId) {

            if (win.getParentId()) return;

            var item = ka.wm.newListBar(win);
            item.inject($('windowList'));

            c++;

            if (win.isInFront()) {
                item.addClass('wm-bar-item-active');
            } else {
                item.removeClass('wm-bar-item-active');
            }

        });

        if (c > 1 || document.body.hasClass('ka-no-desktop')) {
            $('windowList').setStyle('display', 'block');
            if (!document.body.hasClass('ka-no-desktop'))
                $('desktop').setStyle('bottom', 27);
        } else {
            $('windowList').setStyle('display', 'none');
            $('desktop').setStyle('bottom', 0);
        }

    },

    checkOpen: function (pModule, pCode, pInstanceId, pParams) {
        opened = false;
        Object.each(ka.wm.windows, function (win) {
            //if( win && win.module == pModule && win.code == pCode && win.params == pParams ){
            if (win && win.module == pModule && win.code == pCode) {
                if (pInstanceId > 0 && pInstanceId == win.id) {
                    return;
                }
                opened = win;
            }
        });
        return opened;
    },

    closeAll: function () {
        Object.each(ka.wm.windows, function (win) {
            win.close();
        });
    },

    hideContents: function () {
        Object.each(ka.wm.windows, function (win, winId) {
            win.content.setStyle('display', 'none');
        });
    },

    showContents: function () {
        Object.each(ka.wm.windows, function (win, winId) {
            win.content.setStyle('display', 'block');
        });
    }

};
