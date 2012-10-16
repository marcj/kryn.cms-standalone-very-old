var admin_system_settings = new Class({

    Binds: ['renderData', 'save'],

    systemValues: {},

    initialize: function (pWin) {

        this.win = pWin;

        this.preload();

    },

    preload: function () {

        this.win.setLoading(true);

        new Request.JSON({url: _path + 'admin/system/config/labels', noCache: 1, onComplete: function (pResponse) {
            var res = pResponse.data;

            this.langs = res.langs;
            this.timezones = [];
            res.timezones.each(function (timezone) {
                this.timezones.include({l: timezone});
            }.bind(this));

            this._createLayout();

        }.bind(this)}).get();

    },

    renderData: function(pValues){

        this.win.setLoading(false);
    },

    _createLayout: function () {

        logger(this.langs);

        var fields = {
            '__general__': {
                type: 'tab',
                label: t('General'),
                children: {
                    systemTitle: {
                        type: 'text',
                        label: 'System title',
                        desc: t('Appears in the administration title.')
                    },
                    checkUpdates: {
                        type: 'checkbox',
                        label: t('Check for updates')
                    },
                    languages: {
                        type: 'textboxList',
                        label: t('Languages'),
                        desc: t('Limit the language selection, system-wide.'),
                        itemsKey: 'code',
                        labelTemplate: '{title} ({langtitle}, {code})',
                        items: this.langs
                    },
                    communityConnect: {
                        type: 'text',
                        label: t('Community connect'),
                        desc: t('If you want to publish own extensions to the extensions.kryn.org, you have to enter here your email of your kryn.org account.')
                    }
                }
            },
            '__system__': {
                type: 'tab',
                label: t('System'),
                children: {
                    'cache[class]': {
                        label: t('Caching driver'),
                        type: 'select',
                        items: {
                        },
                        children: {
                        }
                    },
                    '__errorLog__': {
                        type: 'childrenSwitcher',
                        label: t('Error log'),
                        children: {

                            'displayErrors': {
                                label: t('Display errors'),
                                desc: t('Prints errors to the frontend clients. You should deactivate this in productive systems'),
                                type: 'checkbox'
                            },
                            'displayRestErrors': {
                                label: t('Display REST error'),
                                type: 'checkbox',
                                desc: t('Display more information in REST errors, like line number, file path and debug trace.')
                            },
                            'logErrors': {
                                label: t('Save errors into a log file'),
                                type: 'checkbox',
                                children: {
                                    'logErrorsFile': {
                                        needValue: 1,
                                        label: t('Log file'),
                                        desc: t('Example: kryn.log')
                                    }
                                }
                            },
                            'dbErrorPrintSql': {
                                label: t('Display the full SQL in the error log'),
                                type: 'checkbox'
                            },
                            'dbExceptionsNoStop': {
                                label: t('Do not stop the script during an query failure'),
                                type: 'checkbox'
                            },
                            'debugLogSqls': {
                                label: t('[Debug] Log all SQL queries'),
                                desc: t('Deactivate this on productive machines, otherwise it will blow up your logs!'),
                                type: 'checkbox'
                            }
                        }
                    },
                    'timezone': {
                        label: t('Server timezone'),
                        type: 'select',
                        items: this.timezones,
                        itemsKey: 'code'
                    },
                    fileGroupPermission: {
                        label: t('File group permission'),
                        type: 'select',
                        items: {rw: t('Read/Write'), r: t('Read'), '-': t('Nothing')}
                    },
                    fileEveryonePermission: {
                        label: t('File everyone permission'),
                        type: 'select',
                        items: {rw: t('Read/Write'), r: t('Read'), '-': t('Nothing')}
                    }
                }
            },
            '__media__': {
                type: 'tab',
                label: t('Media'),
                children: {
                    localFileUrl: {
                        type: 'text',
                        label: t('Local file URL'),
                        desc: t('For http proxy (vanish, AWS cloudfront etc) you should enter the URL here. Default is empty.')
                    },
                    mounts: {
                        label: t('External file mount'),
                        desc: t('Here you can connect with a external cloud storage server'),
                        type: 'array',
                        asHash: true,
                        columns: [
                            {label: t('Mount name'), width: 100},
                            t('Driver')
                        ],
                        fields: {
                            name: {
                                type: 'text'
                            },
                            driver: {
                                type: 'select'
                            }
                        }

                    }
                }
            },
            '__client__': {
                type: 'tab',
                label: t('Client'),
                children: {
                    'client[class]': {
                        type: 'select',
                        label: t('Backend client driver'),
                        desc: t('Login, session processing etc. The user "admin" always authenticate against the Kryn.cms users database.'),
                        items: {
                            '\\Core\\Client\\KrynUsers': t('Kryn.cms users database')
                        },
                        children: {
                            'client[config][\\Core\\Client\\KrynUsers][email_login]': {
                                'label': t('Allow email login'),
                                'type': 'checkbox',
                                'needValue': '\\Core\\Client\\KrynUsers'
                            },
                            'client[config][\\Core\\Client\\KrynUsers][timeout]': {
                                label: t('Session timeout'),
                                type: 'text',
                                'default': '3600',
                                'needValue': '\\Core\\Client\\KrynUsers'
                            },
                            'client[config][\\Core\\Client\\KrynUsers][passwordHashCompat]': {
                                'type': 'checkbox',
                                'label': t('Activate the compatibility in the authentication with older Kryn.cms'),
                                'default': 1,
                                'needValue': '\\Core\\Client\\KrynUsers',
                                'desc': t('If you did upgrade from a older version than 1.0 than you should probably let this checkbox active.')
                            }
                        }
                    },
                    'session[class]': {
                        type: 'select',
                        label: t('Session storage'),
                        items: {
                            '\\Core\\Cache\\PHPSessions': t('PHP-Sessions')
                        },
                        children: {
                        }
                    },
                    '__info__': {
                        'type': 'label',
                        'label': t('Frontend client handling'),
                        'desc': t('You can overrite these settiongs per domain under <br />Pages -> Domain -> Session.')
                    }
                }
            }

        };

        Object.each(ka.settings.configs, function(config){

            //map FAL driver
            if (config.falDriver){
                if (!fields.__media__.children.mounts.fields.driver.items)
                    fields.__media__.children.mounts.fields.driver.items = {};

                if (!fields.__media__.children.mounts.fields.driver.children)
                    fields.__media__.children.mounts.fields.driver.children = {};

                Object.each(config.falDriver, function(driver, key){
                    fields.__media__.children.mounts.fields.driver.items[driver.class] = driver.title;

                    if (driver.properties){
                        Object.each(driver.properties, function(property){
                           property.needValue = driver.class;
                        });
                        ka.addFieldKeyPrefix(driver.properties, 'driverOptions['+driver.class+']')
                        Object.append(fields.__media__.children.mounts.fields.driver.children, driver.properties);
                    }
                });
           }

            //map Auth driver
            if (config.clientDriver){

                Object.each(config.clientDriver, function(driver, key){
                    fields.__client__.children['client[class]'].items[driver.class] = driver.title;

                    if (driver.properties){
                        Object.each(driver.properties, function(property){
                            property.needValue = driver.class;
                        });
                        var properties = Object.clone(driver.properties);
                        ka.addFieldKeyPrefix(properties, 'client[config]['+driver.class+']')
                        Object.append(fields.__client__.children['client[class]'].children, properties);
                    }
                });
            }

            //map cache driver
            if (config.cacheDriver){

                Object.each(config.cacheDriver, function(driver, key){
                    fields.__system__.children['cache[class]'].items[driver.class] = driver.title;

                    if (driver.properties){
                        Object.each(driver.properties, function(property){
                            property.needValue = driver.class;
                        });
                        var properties = Object.clone(driver.properties);
                        ka.addFieldKeyPrefix(properties, 'cache[config]['+driver.class+']')
                        Object.append(fields.__system__.children['cache[class]'].children, properties);
                    }


                    fields.__client__.children['session[class]'].items[driver.class] = driver.title;

                    if (driver.properties){
                        Object.each(driver.properties, function(property){
                            property.needValue = driver.class;
                        });
                        var properties = Object.clone(driver.properties);
                        ka.addFieldKeyPrefix(properties, 'session[config]['+driver.class+']')
                        Object.append(fields.__client__.children['session[class]'].children, properties);
                    }
                });
            }

        });

        this.bottomBar = this.win.addBottomBar();

        this.saveBtn = this.bottomBar.addButton(t('Save')).setButtonStyle('blue').addEvent('click', this.save);

        this.fieldObject = new ka.Parse(this.win.content, fields, {
            tabsInWindowHeader: true
        }, {
            win: this.win
        });

        this.load();

    },



    save: function(){

        var data = this.fieldObject.getValue();

        //map config

        data.session.config = data.session.config ? data.session.config[data.session.class]:{};
        data.client.config = data.client.config ? data.client.config[data.client.class]:{};
        data.cache.config = data.cache.config ? data.cache.config[data.cache.class]:{};

        logger(data);

        this.saveBtn.startTip(t('Saving ...'));

        if (this.lastSave)
            this.lastSave.cancel();

        this.lastSave = new Request.JSON({url: _path+'admin/system/config', onComplete: function(pResponse){

            if (pResponse.error){
                this.win.alert(pResponse.error+': '+pResponse.message);
                this.saveBtn.stopTip(t('Failed'));
            } else {
                this.saveBtn.stopTip(t('Done'));
            }

        }.bind(this)}).post(data);
    },

    penes: function(){

        //        this.panes['install'] = new Element('div', {
        //            'class': 'admin-system-module-pane'
        //        }).inject( this.win.content );


        this.fields = {};

        var p = this.panes['general'];
        this.fields['systemTitle'] = new ka.Field({
            label: t('System title'), desc: 'Adds a title to the administration titel'
        }).inject(p);


        this.fields['update finder'] = new ka.Field({
            label: t('Update finder'),
            type: 'checkbox'
        }).inject(p);

        this.fields['communityEmail'] = new ka.Field({
            label: t('Community connect'), desc: _('If you want to publish your own extensions, layout packs or other stuff, you have to connect with the community server. Enter your community email to connect with.')
        }).inject(p);

        this.fields['languages'] = new ka.Field({
            label: t('Languages'), desc: t('Limit the language selection. (systemwide)'), empty: false,
            type: 'textboxList', store: 'admin/backend/stores/languages'
        }).inject(p);

        this.changeType('general');

        p = this.panes['system'];

        var fields = {
            'displayErrors': {
                label: t('Display errors'),
                desc: t('Prints errors to the frontend clients. You should deactivate this in productive systems'),
                type: 'checkbox'
            },
            'logErrors': {
                label: t('Save debug informations into a file'),
                desc: t('Stores the debug logs (klog()) into a file. Deactivates the log viewer.'),
                type: 'checkbox',
                depends: {
                    'logErrorsFile': {
                        needValue: 1,
                        label: _('Log file'),
                        desc: _('Example: inc/kryn.log')
                    }
                }
            },
            'db_error_print_sql': {
                label: t('Display the full SQL in the logs when a query fails'),
                type: 'checkbox'
            },
            'db_exceptions_nostop': {
                label: t('Do not stop the script during an query failure'),
                type: 'checkbox'
            },
            'debug_log_sqls': {
                label: t('[Debug] Log SQL queries'),
                desc: t('Deactivate this on productive machines, otherwise it will blow up your logs!'),
                type: 'checkbox'
            },
            'timezone': {
                label: _('Server timezone'),
                type: 'select',
                tableItems: this.timezones,
                table_key: 'l',
                table_label: 'l'
            }
        };

        var systemFields = new ka.Parse(p, fields);
        Object.each(systemFields.getFields(), function (item, id) {
            this.fields[ id ] = item;
        }.bind(this));


        /*
         *
         * CDN
         *
         */

        var p = this.panes['cdn'];

        var fields = {
            'cdn_folders': {
                label: _('Magic folders'),
                type: 'array',
                asHash: true,
                columns: [
                    [t('Name'), 250],
                    [t('Options')]
                ],
                fields: {

                    name: {
                        type: 'text'
                    },

                    options: {
                        type: 'select',
                        items: {
                            bla: 'hi'
                        },
                        depends: {
                            icon: {
                                label: t('Icon file'),
                                desc: t('Optional. Default is normal folder icon.'),
                                type: 'file'
                            }
                        }
                    }

                }
            }
        };

        var cdnFields = new ka.Parse(p, fields);
        Object.each(cdnFields.getFields(), function (item, id) {
            this.fields[ id ] = item;
        }.bind(this));



        /**
         *
         * AUTH
         *
         */


        var p = this.panes['auth'];

        var fields = {
            'session_storage': {
                label: t('Session storage'),
                'default': 'database',
                items: {
                    'database': t('SQL Database')
                }
            },
            'session_timeout': {
                label: t('Session timeout'),
                type: 'text',
                'default': '3600'
            },
            'passwd_hash_compat': {
                'type': 'checkbox',
                'label': t('Activate the compatibility in the authentication with older Kryn.cms'),
                'default': 1,
                'desc': t('If you did upgrade from a older version than 1.0 than you should probably let this checkbox active.')
            },
            'info': {
                'type': 'label',
                'label': t('Frontend authentication'),
                'desc': t('Frontend authentication settings are set under:<br />Pages -> Domain -> Session.')
            },

            'auth_class': {
                'label': t('Backend authentication'),
                'desc': t('Please note that the user "admin" authenticate always against the Kryn.cms user.'),
                'type': 'select',
                'table_items': {
                    'kryn': t('Kryn.cms users')
                },
                depends: {
                    'auth_params[email_login]': {
                        'label': t('Allow email login'),
                        'type': 'checkbox',
                        'needValue': 'kryn'
                    }
                }
            }
        };

        var origin = ka.getFieldCaching();
        fields = Object.merge(fields, origin);

        fields.cache_type.label = _('Session storage');

        delete fields.cache_type.items.files;
        fields.session_storage = Object.merge(fields.session_storage, fields.cache_type);

        fields.session_storage['depends']['session_storage_config[servers]'] = Object.clone(origin.cache_type['depends']['cache_params[servers]']);
        delete fields.session_storage['depends']['cache_params[servers]'];

        fields.session_storage['depends']['session_storage_config[files_path]'] = Object.clone(origin.cache_type['depends']['cache_params[files_path]']);
        delete fields.session_storage['depends']['cache_params[files_path]'];

        delete fields.cache_type;

        fields.session_storage['depends']['session_auto_garbage_collector'] = {
            needValue: 'database',
            label: _('Automatic session garbage collector'),
            desc: _('Decreases the performance when dealing with huge count of sessions. For more performance start the session garbage collector through a cronjob. Press the help icon for more informations.'),
            help: 'session_garbage_collector',
            type: 'checkbox',
            'default': '0'
        };

        this.auth_params = {};
        this.auth_params_panes = {};

        Object.each(ka.settings.configs, function (config, id) {
            if (config.auth) {
                Object.each(config.auth, function (auth_fields, auth_class) {
                    Object.each(auth_fields, function (field, field_id) {
                        //field.needValue = id+'/'+auth_class;
                        //fields.auth_class.depends[ 'auth_params['+auth_class+']['+field_id+']'  ] = field;
                        fields.auth_class.table_items[ id + '/' + auth_class  ] = auth_class.capitalize();
                    }.bind(this));
                }.bind(this));
            }
        }.bind(this));

        this.authObj = new ka.Parse(p, fields);
        Object.each(this.authObj.getFields(), function (item, id) {
            this.fields[ id ] = item;
        }.bind(this));

        this.auth_params_objects = {};
        Object.each(ka.settings.configs, function (config, id) {
            if (config.auth) {
                Object.each(config.auth, function (auth_fields, auth_class) {

                    this.auth_params_panes[id + '/' + auth_class] = new Element('div', {
                        'style': 'display: none;'
                    }).inject(this.fields['auth_class'].childContainer);

                    this.auth_params_objects[ id + '/' + auth_class ] = new ka.Parse(this.auth_params_panes[id + '/' + auth_class], auth_fields);
                }.bind(this));
            }
        }.bind(this));

        this.fields['auth_class'].addEvent('check-depends', function () {
            Object.each(this.auth_params_panes, function (pane) {
                pane.setStyle('display', 'none');
            }.bind(this));
            var pane = this.auth_params_panes[ this.fields['auth_class'].getValue() ];

            if (pane) {
                pane.setStyle('display', 'block');
            }
        }.bind(this));

        this.fields['auth_class'].fireEvent('check-depends');


        var p = this.panes['database'];

        var databaseFields = {
            type: {
                label: t('Database type'),
                empty: false,
                type: 'select',
                items: {
                    mysql: 'MySQL',
                    postgresql: 'PostgreSQL',
                    sqlite: 'SQLite',
                    mssql: 'MSSQL'
                }
            },
            server: {
                label: t('Database server'),
                desc: t('Example: localhost. For SQLite enter the path'),
                empty: false
            },
            user: {
                needValue: ['postgresql', 'mysql', 'mssql'],
                againstField: 'db_type',
                label: t('Database login'), empty: false
            },
            passwd: {
                needValue: ['postgresql', 'mysql', 'mssql'],
                againstField: 'db_type',
                label: t('Database password'), type: 'password'
            },
            name: {
                needValue: ['postgresql', 'mysql', 'mssql'],
                againstField: 'db_type',
                label: t('Database name'), empty: false
            },
            prefix: {
                label: t('Database prefix'), empty: false
            }
        };

        this.databaseFieldObj = new ka.Parse(p, databaseFields);

        var p = this.panes['caching'];

        this.fields['media_cache'] = new ka.Field({
            label: _('Media cache path'), desc: 'Default is cache/media/. This folder is for caching template files, so it should be available via HTTP.'
        }).inject(p);

        var origin = ka.getFieldCaching();

        origin.cache_type['default'] = 'files';

        this.cacheObj = new ka.Parse(p, origin);
        Object.each(this.cacheObj.getFields(), function (item, id) {
            this.fields[ id ] = item;
        }.bind(this));

        this.chachingPane = new Element('div').inject(p);

    },

    changeType: function (pType) {
        Object.each(this.tabButtons, function (button, id) {
            button.setPressed(false);
            this.panes[id].setStyle('display', 'none');
        }.bind(this));
        this.panes[ pType ].setStyle('display', 'block');
        this.tabButtons[ pType ].setPressed(true);
    },

    load: function () {
        if (this.lr) this.lr.cancel();

        this.lr = new Request.JSON({url: _path + 'admin/system/config', noCache: 1, onComplete: this.renderData}).get();

        return;
//        function (pResponse) {
//
//            var res  = pResponse.data;
//
//            this.systemValues = res.system;
//            Object.each(this.fields, function (field, key) {
//                if (!field) return;
//
//                if (key.indexOf('[') != -1) {
//                    field.setArrayValue(res.system, key, true);
//                } else {
//                    field.setValue(res.system[key], true);
//                }
//
//            });
//
//            if (res.system.auth_params) {
//                if (this.auth_params_objects[res.system.auth_class]) {
//                    this.auth_params_objects[res.system.auth_class].setValue(res.system.auth_params);
//                }
//            }
//
//            this.databaseFieldObj.setValue(res.system);
//
//            this.oldCommunityEmail = res.system['communityEmail'];
//
//            var langs = [];
//            Object.each(res.langs, function (l, k) {
//                langs.include(l.code);
//            });
//            this.fields['languages'].setValue(langs);
//
//            this.win.setLoading(false);
//
//        }.bind(this)}).get();
    },

    save23: function () {
        var req = {};
        var dontGo = false;

        Object.each(this.fields, function (field, key) {
            if (!field) return;
            if (dontGo) return;
            if (!field.isOk()) {
                dontGo = true;
                var parent = field.main.getParent();
                if (!parent.get('lang')) {
                    parent = field.main.getParent().getParent();
                }

                this.changeType(parent.get('lang'));
            }
            req[key] = field.getValue();
        }.bind(this));

        var auth_class = this.fields['auth_class'].getValue();
        var obj = this.auth_params_objects[ auth_class ];

        if (obj) {
            if (!obj.isOk()) return;
            req['auth_params'] = obj.getValue();
        }
        if (dontGo) return;

        if (!this.databaseFieldObj.isOk()){
            this.changeType('database');
            return;
        }

        req['database'] = {};

        var values = this.databaseFieldObj.getValue();
        Object.each(values, function(val, key){
            req['database'][key] = val;
        })


        this.saveButton.startTip(_('Saving ...'));

        this.loader.show();

        if (this.ls) {
            this.ls.cancel();
        }

        this.ls = new Request.JSON({url: _path + 'admin/system/settings/saveSettings', noCache: 1, onComplete: function (r) {
            if (r.needPw) {
                this.saveButton.startTip(_('Wating ...'));
                this.win._passwordPrompt(_('Please enter your password'), '', this.saveCommunity.bind(this));
            } else {
                this.saveButton.stopTip(_('Saved'));
                ka.loadSettings();
                this.loader.hide();
            }
        }.bind(this)}).post(req);
    },

    saveCommunity: function (pPasswd) {
        if (!pPasswd) {
            this.loader.hide();
        }
        if (this.lsc) {
            this.lsc.cancel();
        }
        this.lsc = new Request.JSON({url: _path + 'admin/system/settings/saveCommunity', noCache: 1, onComplete: function (r) {
            this.loader.hide();
            if (r == 2) {
                this.saveButton.stopTip(_('Error'));
                this.win._alert(_('Cannot connect to community server.'));
                return;
            }
            if (r == 0) {
                this.saveButton.stopTip(_('Error'));
                this.win._alert(_('Access denied'));
                this.fields['communityEmail'].setValue(this.oldCommunityEmail);
                return;
            }
            this.saveButton.stopTip(_('Saved'));
            ka.loadSettings();
        }.bind(this)}).post({email: this.fields['communityEmail'].getValue(), passwd: pPasswd });
    }
});
