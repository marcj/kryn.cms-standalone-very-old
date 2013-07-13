ka.ContentTypes = ka.ContentTypes || {};

ka.ContentTypes.Text = new Class({
    Extends: ka.ContentAbstract,

    icon: 'icon-font',
    label: 'Text',

    options: {

        /**
         * If you want to add custom css classes to the main element.
         *
         * @var {String}
         */
        'class': '',

        /**
         * CSS file/s to be used as content styling. Use Path relative to install directory.
         *
         * Example 1:
         *     'myExt/css/wysiwyg-style1.css'
         * Example 2:
         *     ['myExt/css/wysiwyg-style1.css','myExt/css/wysiwyg-style2.css']
         *
         * @var {String|Array}
         */
        contentsCss: '',

        /**
         * Preset of the toolbar. There are:
         * `simple`, `standard` and `full`.
         * You can modify the toolbar more detailed through `removeButtons`, `extraPlugins` and `toolbar`.
         *
         * @var {String}
         */
        preset: 'standard',

        /**
         * The position of the toolbar.
         * `top`, `bottom`
         *
         * @var {String}
         */
        toolbarLocation: 'top',

        /**
         * Remove actions of the toolbar. Comma separated.
         *
         * Example 1:
         *        remove: 'ads, asd'
         *
         * Example: 2
         *
         * @var {String}
         */
        removeButtons: '',

        /**
         * Includes additionally plugins. Comma separated.
         *
         * @var {String}
         */
        extraPlugins: '',

        /**
         * Custom toolbar buttons.
         *
         * Take a look at
         * http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html#.toolbar_Full
         * and
         * http://docs.cksource.com/CKEditor_3.x/Developers_Guide/Toolbar
         * for more information.
         *
         *
         * @var {Array}
         */
        toolbar: null,

        autoGrow_onStartup: false,

        /**
         * Sets the height of the editor to a fix value. Default is auto.
         *
         * @var {String}
         */
        inputHeight: null,

        /**
         * Sets the width of the editor to a fix value. Default is auto.
         *
         * @var {String}
         */
        inputWidth: null,

        /**
         * You can set some own config variables. See here what's possible: http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html
         *
         * @var {Object}
         */
        customConfig: {},

        configs: {
            simple: {
                toolbarGroups: [
                    {name: 'basicstyles', groups: ['undo', 'basicstyles', 'align', 'styles']},
                    '/',
                    {name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align']},
                    {name: 'links'},
                    {name: 'insert'}
               ]
            },
            standard: {
                toolbarGroups: [
                    {name: 'basicstyles', groups: ['undo', 'basicstyles', 'align', 'styles', 'colors']},
                    {name: 'paragraph', groups: ['list', 'indent', 'blocks']},
                    {name: 'links'},
                    {name: 'insert'},
                    {name: 'tools'},
                    {name: 'others'}
               ]
            },
            full: {
                toolbarGroups: [
                    {name: 'document', groups: ['mode', 'document', 'doctools']},
                    {name: 'clipboard', groups: ['clipboard', 'undo']},
                    {name: 'editing', groups: ['find', 'selection', 'spellchecker']},
                    {name: 'forms'},
                    '/',
                    {name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
                    {name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align']},
                    {name: 'links'},
                    {name: 'insert'},
                    '/',
                    {name: 'styles'},
                    {name: 'colors'},
                    {name: 'tools'},
                    {name: 'others'},
                    {name: 'about'}
               ]
            }
        }
    },

    createLayout: function () {
        this.main = new Element('div', {
            contentEditable: true,
            'class': 'ka-content-text selectable'
        }).inject(this.contentInstance);

        var config = this.getEditorConfig();
        this.checkChange = this.checkChange.bind(this);
        this.editorReady = this.editorReady.bind(this);

        this.editor = this.getDOMWindow().CKEDITOR.inline(this.main, config);

        this.editor.on('instanceReady', this.editorReady);

        this.editor.on('change', this.checkChange);
        this.editor.on('blur', this.checkChange);
        this.editor.on('focus', this.checkChange);
        this.editor.on('key', this.checkChange);
        this.editor.on('paste', this.checkChange);
        this.editor.on('execCommand', this.checkChange);
        this.main.addEvent('keyup', this.checkChange);
    },

    getDOMWindow: function() {
        return this.main.getDocument().window;
    },

    getDOMDocument: function() {
        return this.main.getDocument();
    },

    checkChange: function () {
        if (this.ready) {
            if (this.oldData != this.editor.getData()) {
                this.contentInstance.fireChange();
            }
        }
    },

    focus: function () {
        this.main.focus();
    },

    getEditorConfig: function () {
        var config = this.options.configs[this.options.preset] || {};

        config.toolbarLocation = this.options.toolbarLocation;
        config.autoGrow_onStartup = true;

        if (this.options.removeButtons) {
            config.removeButtons = this.options.removeButtons;
        }

        config.extraPlugins = '';
        if (this.options.extraPlugins) {
            config.extraPlugins = this.options.extraPlugins;
        }

        config.extraPlugins += ',autogrow';

        if (this.options.contentsCss) {
            if (typeOf(this.options.contentsCss) == 'string') {
                if (this.options.contentsCss.substr(0, 1) != '/') {
                    this.options.contentsCss = _path + this.options.contentsCss;
                }

                config.contentsCss = _path + this.options.contentsCss
            } else if (typeOf(this.options.contentsCss) == 'array') {

                config.contentsCss = [];
                Array.each(this.options.contentsCss, function (css) {

                    if (css.substr(0, 1) != '/') {
                        css = _path + css;
                    }
                    config.contentsCss.push(css);
                });
            }
        }

        Object.each(this.options.customConfig, function (v, k) {
            config[k] = v;
        });

        return config;
    },

    setValue: function (pValue) {
        this.value = pValue;
        if (this.ready) {
            this.editor.setData(this.value);
        }
    },

    getValue: function () {
        if (!this.ready) {
            return this.value;
        }
        else {
            return this.editor.getData();
        }
    },

    editorReady: function () {
        this.ready = true;

        var toolbar = this.getDOMDocument().id('cke_' + this.editor.name);
        toolbar.addClass('kryn_cke_toolbar');

        top.window.fireEvent('ckEditorReady', [this, toolbar]);

        if (this.value) {
            this.editor.setData(this.value);
            this.oldData = this.editor.getData();
        }
    }

});

