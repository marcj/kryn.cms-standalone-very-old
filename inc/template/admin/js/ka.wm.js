/* ka window.manager */

window.addEvent('resize', function () {
    ka.wm.checkDimensionsAndSendResize();
});


ka.wm = {

    windows: new Hash({}),

    /* depend: [was => mitWem] */
    depend: new Hash({}),
    lastWindow: 0,
    events: {},
    zIndex: 1000,

    openWindow: function (pModule, pWindowCode, pLink, pDependOn, pParams, pInline) {
        /*
         pDependOn:
         0..x: ID of a legal window
         -1: current/active window
         */
        if (pDependOn == -1) {
            pDependOn = ka.wm.lastWindow;
        }

        var id = pModule + '::' + pWindowCode;
        if (pLink && pLink.onlyonce && this.checkOpen(id)) {
            return this.toFront(id);
        }
        return ka.wm.loadWindow(pModule, pWindowCode, pLink, pDependOn, pParams, pInline);
    },

    checkDimensionsAndSendResize: function () {
        if (ka.wm.goDimensionsCheck) {
            $clear(ka.wm.goDimensionsCheck);
        }

        ka.wm.goDimensionsCheck = (function () {
            ka.wm._checkDimensions();
        }).delay(300);
    },

    _checkDimensions: function () {
        Object.each(ka.wm.windows, function (win) {
            if (win) {
                win.checkDimensions();
                win.fireEvent('resize');
            }
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

    open: function (pTarget, pParams, pDepend, pInline) {
        var firstSlash = pTarget.indexOf('/');
        var module = pTarget.substr(0, firstSlash);
        var path = pTarget.substr(firstSlash + 1, pTarget.length);
        return ka.wm.openWindow(module, path, null, pDepend, pParams, pInline);
    },

    dependExist: function (pWindowId) {
        var dep = false;
        Object.each(ka.wm.depend, function (win, key) {
            if (win == pWindowId) {
                dep = true;
            } //a depend exist
        });
        return dep;
    },

    getDepend: function (pWindowId) {
        //irgendwie unnötig ?
        // bzw ist doch getWindow()?
        return ka.wm.depend[ pWindowId ];
    },

    getOpener: function (pId) {

        return ka.wm.windows[ ka.wm.getDepend(pId) ];
    },

    getWindow: function (pId) {
        if (pId == -1) {
            pId == ka.wm.lastWindow;
        }
        return ka.wm.windows[ pId ];
    },

    getDependOn: function (pWindowId) {
        var reswin = null;
        Object.each(ka.wm.depend, function (win, key) {
            if (win == pWindowId) {
                reswin = ka.wm.windows[key];
            } //a depend exist
        });
        return reswin;
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
            if (win) {
                win.loadDimensions();
            }
        });
    },

    toFront: function (pWindowId) {
        if (ka.wm.dependExist(pWindowId)) {
            return false;
        }
        if (ka.wm.lastWindow > 0 && ka.wm.windows[ ka.wm.lastWindow ] && ka.wm.lastWindow != pWindowId) {
            ka.wm.windows[ ka.wm.lastWindow ].toBack();
        }
        ka.wm.lastWindow = pWindowId;
        return true;
    },

    setFrontWindow: function (pWinId) {
        Object.each(ka.wm.windows, function (win, winId) {
            if (win) win.inFront = false;
        });
    },

    loadWindow: function (pModule, pWindowCode, pLink, pDependOn, pParams, pInline) {
        var instance = ka.wm.windows.getLength() + 1;

        if (pDependOn > 0) {
            ka.wm.depend.include(instance, pDependOn);
            var w = ka.wm.windows[ pDependOn ];
            if (w) {
                w.toDependMode(pInline);
            }
        }

        var win = new ka.kwindow(pModule, pWindowCode, pLink, instance, pParams, pInline);
        ka.wm.windows.include(instance, win);
        ka.wm.updateWindowBar();
        return win;
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

        if (shortTitle == '') {
            bar.setStyle('display', 'none');
        }

        bar.set('text', shortTitle);


        if (document.body.hasClass('ka-no-desktop')){
            new Element('div', {
                style: 'position: absolute; right: 4px; top: 0px; font-weight: bold; color: white; font-size: 14px;',
                text: 'x'
            })
            .addEvent('click', function(){
                pWindow.close();
            })
            .inject(bar);
        }

        return bar;
    },

    close: function (pWindow) {
        var id = pWindow.id;

        var dependOn = ka.wm.depend[ id ];
        if (dependOn) {
            if (ka.wm.windows[dependOn]) {
                ka.wm.windows[dependOn].removeDependMode();
            }
        }
        delete ka.wm.depend[ id ];
        delete ka.wm.windows[ id ];

        if (dependOn) {
            if (ka.wm.windows[dependOn]) {
                ka.wm.windows[dependOn].toFront();
            }
        }

        ka.wm.updateWindowBar();
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
        $('windowList').empty();

        var c = 0;
        Object.each(ka.wm.windows, function (win, winId) {
            if (!win) return;
            if (win.inline) return;

            var item = ka.wm.newListBar(win);
            item.inject($('windowList'));

            c++;

            if (win.inFront && win.isOpen) {
                item.addClass('wm-bar-item-active');
            } else {
                item.removeClass('wm-bar-item-active');
            }

            if (ka.wm.dependExist(winId)) {
                var dependWindow = ka.wm.getDependOn(winId);
                if (dependWindow.inFront && dependWindow.isOpen) {
                    item.addClass('wm-bar-item-active');
                }
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
            if (win) {
                win.close();
            }
        });
    },

    hideContents: function () {
        Object.each(ka.wm.windows, function (win, winId) {
            if (win) {
                win.content.setStyle('display', 'none');
            }
        });
    },

    showContents: function () {
        Object.each(ka.wm.windows, function (win, winId) {
            if (win) {
                win.content.setStyle('display', 'block');
            }
        });
    }

};
