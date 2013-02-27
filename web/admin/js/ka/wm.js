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

    activeWindowInformation: [],
    tempItems: {},

    openWindow: function (pEntryPoint, pLink, pParentWindowId, pParams, pInline) {

        if (pLink && pLink.onlyOnce && this.checkOpen(pEntryPoint)) {
            return this.toFront(pEntryPoint);
        }
        return ka.wm.loadWindow(pEntryPoint, pLink, pParentWindowId, pParams, pInline);
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

    open: function (pEntryPoint, pParams, pParentWindowId, pInline) {
        return ka.wm.openWindow(pEntryPoint, null, pParentWindowId, pParams, pInline);
    },

    getWindow: function (pId) {
        if (pId == -1){
            pId == ka.wm.lastWindow;
        }
        return ka.wm.windows[ pId ];
    },

    sendSoftReload: function (pEntryPoint) {
        ka.wm.softReloadWindows(pEntryPoint);
    },

    softReloadWindows: function (pEntryPoint) {
        Object.each(ka.wm.windows, function (win) {
            if (win && win.getEntryPoint() == pEntryPoint) {
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

    loadWindow: function (pEntryPoint, pLink, pParentWindowId, pParams, pInline) {
        var instance = Object.getLength(ka.wm.windows) + 1;

        if (pParentWindowId == -1)
            pParentWindowId = ka.wm.lastWindow?ka.wm.lastWindow.id:false;

        if (pParentWindowId && !ka.wm.getWindow(pParentWindowId)) throw 'Parent window not found.';

        if (pParentWindowId && pInline) {
            ka.wm.getWindow(pParentWindowId).prepareInlineContainer();
        }

        ka.wm.windows[instance] = new ka.Window(pEntryPoint, pLink, instance, pParams, pInline, pParentWindowId);
        ka.wm.windows[instance].toFront();
        if (pParentWindowId){
            ka.wm.getWindow(pParentWindowId).setChildren(ka.wm.windows[instance]);
        }
        ka.wm.updateWindowBar();
    },

    close: function (pWindow) {

        var parent = pWindow.getParentId();
        if (parent){
            parent = ka.wm.getWindow(parent);
            parent.removeChildren();
        }

        if (ka.wm.tempItems[pWindow.getEntryPoint()]) {
            delete ka.wm.tempItems[pWindow.getEntryPoint()];
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

        ka.wm.removeActiveWindowInformation();

        Object.each(ka.wm.windows, function (win) {
            if (win.getParentId()) return;
            ka.wm.addActiveWindowInformation(win);

            if (win.isInFront()){

                var menuItem = ka.adminInterface.getMenuItem(win.getEntryPoint());
                if (menuItem) {
                    menuItem.object.addClass('ka-main-menu-active');
                } else if (!ka.wm.tempItems[win.getEntryPoint()]){
                    var item = ka.adminInterface.addTempLink(win);
                    item.addClass('ka-main-menu-active');
                    ka.wm.tempItems[win.getEntryPoint()] = item;
                } if (menuItem = ka.wm.tempItems[win.getEntryPoint()]){
                    menuItem.set('text', win.getTitle());
                    menuItem.addClass('ka-main-menu-active');
                }
            }

        });

    },

    removeActiveWindowInformation: function(){

        ka.adminInterface.mainLinks.getElements('a').removeClass('ka-main-menu-active');

        Array.each(ka.wm.activeWindowInformation, function(entryPoint){
            var menuItem = ka.adminInterface.getMenuItem(entryPoint);
            menuItem.object.knob.destroy();
            delete menuItem.object.knob;
        });
        ka.wm.activeWindowInformation = [];
    },

    addActiveWindowInformation: function(pWin){
        var menuItem = ka.adminInterface.getMenuItem(pWin.getEntryPoint());

        if (menuItem && !menuItem.object.knob){
            menuItem.object.knob = new Element('span', {
                html: '&bullet;',
                'class': 'ka-main-menu-item-active-window-information-item'
            }).inject(menuItem.object.activeWindowInformationContainer);

            ka.wm.activeWindowInformation.push(pWin.getEntryPoint());
        }
    },

    checkOpen: function (pEntryPoint, pInstanceId, pParams) {
        opened = false;
        Object.each(ka.wm.windows, function (win) {
            if (win && win.getEntryPoint() == pEntryPoint) {
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
