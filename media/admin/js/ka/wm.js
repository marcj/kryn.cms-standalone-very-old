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

    newListBar: function (pWindow) {
        pWindow.setBarButton(bar);
        var bar = new Element('a', {
            'class': 'ka-tabGroup-item gradient wm-bar-item',
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
            new Element('span', {
                'class': 'wm-bar-item-closer'
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
