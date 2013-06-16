/* ka window.manager */
ka.wm = {
    windows: {},

    /* depend: [was => mitWem] */
    depend: {},
    lastWindow: null,
    events: {},
    zIndex: 1000,

    activeWindowInformation: [],
    tempItems: {},

    openWindow: function (pEntryPoint, pLink, pParentWindowId, pParams, pInline) {
        var win;

        if (!ka.entrypoint.get(pEntryPoint)) {
            logger(tf('Entry point `%s` not found.', pEntryPoint));
            return;
        }

        if ((win = this.checkOpen(pEntryPoint)) && !pInline) {
            return win.toFront();
        }

        return ka.wm.loadWindow(pEntryPoint, pLink, pParentWindowId, pParams, pInline);
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
        if (pId == -1 && ka.wm.lastWindow) {
            pId = ka.wm.lastWindow.getId();
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
            if (win && pWindow.id != winId) {
                win.toBack();
            }
        });
        ka.wm.lastWindow = pWindow;
        console.log('setFrontWindow', ka.wm.lastWindow);
    },

    loadWindow: function (pEntryPoint, pLink, pParentWindowId, pParams, pInline) {
        var instance = Object.getLength(ka.wm.windows) + 1;

        if (pParentWindowId == -1) {
            console.log('lastWindow', ka.wm.lastWindow);
            pParentWindowId = ka.wm.lastWindow ? ka.wm.lastWindow.id : false;
        }

        if (false === pParentWindowId || (pParentWindowId && !ka.wm.getWindow(pParentWindowId))) {
            throw tf('Parent `%d` window not found.', pParentWindowId);
        }

        logger('loadWindow: ', pEntryPoint, pInline, pParentWindowId);
        ka.wm.windows[instance] = new ka.Window(pEntryPoint, pLink, instance, pParams, pInline, pParentWindowId);
        ka.wm.windows[instance].toFront();
        ka.wm.updateWindowBar();
        ka.wm.reloadHashtag();
    },

    close: function (pWindow) {
        var parent = pWindow.getParentId();
        if (parent) {
            parent = ka.wm.getWindow(parent);
            parent.removeChildren();
        }

        if (ka.wm.tempItems[pWindow.getId()]) {
            ka.wm.tempItems[pWindow.getId()].destroy();
            delete ka.wm.tempItems[pWindow.getId()];
        }

        delete ka.wm.windows[pWindow.id];

        if (parent) {
            parent.toFront();
        } else {
            ka.wm.bringLastWindow2Front();
        }

        ka.wm.updateWindowBar();
        ka.wm.reloadHashtag();
    },

    bringLastWindow2Front: function () {

        var lastWindow;

        Object.each(ka.wm.windows, function (win) {
            if (!win) {
                return;
            }
            if (!lastWindow || win.border.getStyle('z-index') > lastWindow.border.getStyle('z-index')) {
                lastWindow = win;
            }
        });

        if (lastWindow) {
            lastWindow.toFront();
        }
    },

    getWindowsCount: function () {
        var count = 0;
        Object.each(ka.wm.windows, function (win, winId) {
            if (!win) {
                return;
            }
            if (win.inline) {
                return;
            }
            count++;
        });
        return count;
    },

    updateWindowBar: function () {
        var openWindows = 0;

        var wmTabContainer = ka.adminInterface.getWMTabContainer();

        wmTabContainer.empty();
        var fragment = document.createDocumentFragment();

        var el, icon;
        Object.each(ka.wm.windows, function (win) {
            if (win.getParentId()) {
                return;
            }

            el = new Element('div', {
                'class': 'ka-wm-tab' + (win.isInFront() ? ' ka-wm-tab-active' : ''),
                text: win.getTitle() || win.getFullTitle()
            })
            .addEvent('click', function(){ win.toFront(); });

            if (icon = (win.getEntryPointDefinition() || {}).icon) {
                console.log(icon);
                if ('#' === icon.substr(0, 1)) {
                    el.addClass(icon.substr(1));
                } else {
                    //new img
                }
            }


            fragment.appendChild(el);
        });

        wmTabContainer.appendChild(fragment);

        ka.wm.reloadHashtag();
    },

    reloadHashtag: function (pForce) {

        var hash = '';

        Object.each(ka.wm.windows, function (win) {
            if (win.isInFront() && !win.isInline()) {
                hash = win.getEntryPoint() + ( win.getParameter() ? '!' + JSON.encode(win.getParameter()) : '' );
            }
        });

        if (hash != window.location.hash) {
            window.location.hash = hash;
        }

    },

    handleHashtag: function (pForce) {
        if (ka.wm.hashHandled && !pForce) {
            return;
        }

        ka.wm.hashHandled = true;

        if (window.location.hash) {

            var first = window.location.hash.indexOf('!');
            var entryPoint = window.location.hash.substr(1);
            var parameters = null;
            if (first !== -1) {
                entryPoint = entryPoint.substr(0, first - 1);

                parameters = window.location.hash.substr(first + 1);
                if (parameters) {
                    parameters = JSON.decode(parameters);
                }
            }

            ka.wm.open(entryPoint, parameters);
        }
    },

    removeActiveWindowInformation: function () {
        ka.adminInterface.mainMenuTopNavigation.getElements('a').removeClass('ka-main-menu-item-active');
        ka.adminInterface.mainMenu.getElements('a').removeClass('ka-main-menu-item-active');
        ka.adminInterface.mainMenuTopNavigation.getElements('a').removeClass('ka-main-menu-item-open');
        ka.adminInterface.mainLinks.getElements('a').removeClass('ka-main-menu-item-open');
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
