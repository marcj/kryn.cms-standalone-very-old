var admin_system_settings = new Class({

    systemValues: {},

    initialize: function (pWin) {

        this.win = pWin;
        this.preload();

    },

    preload: function () {

        this.tabGroup = this.win.addSmallTabGroup();
        this.tabButtons = {};
        this.tabButtons['general'] = this.tabGroup.addButton(_('General'), this.changeType.bind(this, 'general'));
        this.tabButtons['system'] = this.tabGroup.addButton(_('System'), this.changeType.bind(this, 'system'));
        this.tabButtons['cdn'] = this.tabGroup.addButton(_('CDN'), this.changeType.bind(this, 'cdn'));
        this.tabButtons['auth'] = this.tabGroup.addButton(_('Session'), this.changeType.bind(this, 'auth'));
        this.tabButtons['database'] = this.tabGroup.addButton(_('Database'), this.changeType.bind(this, 'database'));
        this.tabButtons['caching'] = this.tabGroup.addButton(_('Caching'), this.changeType.bind(this, 'caching'));

        //       this.tabButtons['install'] = this.tabGroup.addButton('Neue installieren', '', this.changeType.bind(this,'install'));

        this.saveGrp = this.win.addButtonGroup();
        this.saveButton = this.saveGrp.addButton(_('Save'), _path + PATH_MEDIA + '/admin/images/button-save.png', this.save.bind(this));

        this.panes = {};
        Object.each(this.tabButtons, function (item, key) {
            this.panes[key] = new Element('div', {
                'class': 'admin-system-settings-pane',
                lang: key
            }).inject(this.win.content);
        }.bind(this))

        this.loader = new ka.Loader().inject(this.win.content);

        this.loader.show();

        new Request.JSON({url: _path + 'admin/system/settings/preload', noCache: 1, onComplete: function (res) {
            this.langs = res.langs;
            this.timezones = [];
            res.timezones.each(function (timezone) {
                this.timezones.include({l: timezone});
            }.bind(this));
            this._createLayout();
            this.load();
        }.bind(this)}).post();
    },

    _createLayout: function () {

        //        this.panes['install'] = new Element('div', {
        //            'class': 'admin-system-module-pane'
        //        }).inject( this.win.content );


        this.fields = {};

        var p = this.panes['general'];
        this.fields['systemtitle'] = new ka.Field({
            label: _('System title'), desc: 'Adds a title to the administration titel'
        }).inject(p);


        this.fields['update finder'] = new ka.Field({
            label: _('Update finder'),
            type: 'checkbox'
        }).inject(p);

        this.fields['communityEmail'] = new ka.Field({
            label: _('Community connect'), desc: _('If you want to publish your own extensions, layout packs or other stuff, you have to connect with the community server. Enter your community email to connect with.')
        }).inject(p);

        this.fields['languages'] = new ka.Field({
            label: _('Languages'), desc: _('Limit the language selection. (systemwide)'), empty: false,
            type: 'textlist', store: 'admin/backend/stores/languages'
        }).inject(p);

        this.changeType('general');

        var p = this.panes['system'];

        var fields = {
            'cronjob_key': {
                label: _('Cronjob key'),
                type: 'label'
            },
            'display_errors': {
                label: _('Display errors'),
                desc: _('Prints errors to the frontend clients. You should deactivate this in productive systems'),
                type: 'checkbox'
            },
            'log_errors': {
                label: _('Save debug informations into a file'),
                desc: _('Stores the debug logs (klog()) into a file. Deactivates the log viewer.'),
                type: 'checkbox',
                depends: {
                    'log_errors_file': {
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
                label: _('Session storage'),
                'default': 'database',
                items: {
                    'database': _('SQL Database')
                }
            },
            'session_timeout': {
                label: _('Session timeout'),
                type: 'text',
                'default': '3600'
            },
            'passwd_hash_compatibility': {
                'type': 'checkbox',
                'label': _('Activate the compatibility in the authentication with older Kryn.cms'),
                'default': 1,
                'desc': _('If you did upgrade from a older version than 1.0 than you should probably let this checkbox active.')
            },
            'info': {
                'type': 'html',
                'label': _('Frontend authentication'),
                'desc': _('Frontend authentication settings are set under:<br />Pages -> Domain -> Session.')
            },

            'auth_class': {
                'label': _('Backend authentication'),
                'desc': _('Please note that the user "admin" authenticate always against the Kryn.cms user.'),
                'type': 'select',
                'table_items': {
                    'kryn': _('Kryn.cms users')
                },
                depends: {
                    'auth_params[email_login]': {
                        'label': _('Allow email login'),
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
            db_type: {
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
            db_server: {
                label: t('Database server'),
                desc: t('Example: localhost. For SQLite enter the path'),
                empty: false
            },
            db_user: {
                needValue: ['postgresql', 'mysql', 'mssql'],
                againstField: 'db_type',
                label: t('Database login'), empty: false
            },
            db_passwd: {
                needValue: ['postgresql', 'mysql', 'mssql'],
                againstField: 'db_type',
                label: t('Database password'), type: 'password'
            },
            db_name: {
                needValue: ['postgresql', 'mysql', 'mssql'],
                againstField: 'db_type',
                label: t('Database name'), empty: false
            },
            db_prefix: {
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
        if (this.lr) {
            this.lr.cancel();
        }

        this.loader.show();

        this.lr = new Request.JSON({url: _path + 'admin/system/settings/loadSettings', noCache: 1, onComplete: function (res) {

            this.systemValues = res.system;
            Object.each(this.fields, function (field, key) {
                if (!field) return;

                if (key.indexOf('[') != -1) {
                    field.setArrayValue(res.system, key, true);
                } else {
                    field.setValue(res.system[key], true);
                }

            });

            if (res.system.auth_params) {
                if (this.auth_params_objects[res.system.auth_class]) {
                    this.auth_params_objects[res.system.auth_class].setValue(res.system.auth_params);
                }
            }

            this.databaseFieldObj.setValue(res.system);

            this.oldCommunityEmail = res.system['communityEmail'];

            var langs = [];
            Object.each(res.langs, function (l, k) {
                langs.include(l.code);
            });
            this.fields['languages'].setValue(langs);

            this.loader.hide();
        }.bind(this)}).post();
    },

    save: function () {
        var req = {}
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

        var values = this.databaseFieldObj.getValue();
        Object.each(values, function(val, key){
            req[key] = val;
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
