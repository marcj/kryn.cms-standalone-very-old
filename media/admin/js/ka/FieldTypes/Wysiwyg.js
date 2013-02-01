ka.FieldTypes.Wysiwyg = new Class({

    Extends: ka.FieldAbstract,

    value: '',

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
         *     'media/myExt/css/wysiwyg-style1.css'
         * Example 2:
         *     ['media/myExt/css/wysiwyg-style1.css','media/myExt/css/wysiwyg-style2.css']
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
                toolbar: [
                    ['Bold','Italic', 'Underline','Strike'],
                    ['Undo','Redo'],
                    ['NumberedList','BulletedList','-','Outdent','Indent'],
                    ['Link','Unlink'],
                    ['Format'],
                    [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord'],
                    ['Source']
                ]
            },
            standard: {
                toolbar: [
                    ['Bold','Italic', 'Underline','Strike'],
                    ['Undo','Redo'],
                    ['NumberedList','BulletedList','-','Outdent','Indent'],
                    ['Link','Unlink','Anchor'],
                    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Blockquote'],
                    ['Styles','Format','Font','FontSize'],
                    ['Image','Flash','Table','HorizontalRule','Smiley'],
                    [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord']
                ]
            },
            full: {
                toolbarGroups: [
                    { name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
                    { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
                    { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
                    { name: 'forms' },
                    '/',
                    { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                    { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
                    { name: 'links' },
                    { name: 'insert' },
                    '/',
                    { name: 'styles' },
                    { name: 'colors' },
                    { name: 'tools' },
                    { name: 'others' },
                    { name: 'about' }
                ]
            }
        }

    },

    createLayout: function(){

        this.main = new Element('div', {
            contentEditable: "true",
            'class': 'selectable ka-Field-wysiwyg'
        }).inject(this.fieldInstance.fieldPanel);

        if (this.options.inputHeight)
            this.main.setStyle('height', this.options.inputHeight);

        if (this.options.inputWidth)
            this.main.setStyle('width', this.options.inputWidth);

        if (this.options['class'])
            this.main.addClass(this.options['class']);

        var config = this.options.configs[this.options.preset] || {};

        config.toolbarLocation = this.options.toolbarLocation;
        config.autoGrow_onStartup = true;

        if (this.options.removeButtons)
            config.removeButtons = this.options.removeButtons;

        config.extraPlugins = '';
        if (this.options.extraPlugins)
            config.extraPlugins = this.options.extraPlugins;

        config.extraPlugins += ',autogrow';

        if (this.options.contentsCss){
            if (typeOf(this.options.contentsCss) == 'string'){
                if (this.options.contentsCss.substr(0,1) != '/')
                    this.options.contentsCss = _path+this.options.contentsCss;

                config.contentsCss = _path+this.options.contentsCss
            } else if (typeOf(this.options.contentsCss) == 'array'){

                config.contentsCss = [];
                Array.each(this.options.contentsCss, function(css){

                    if (css.substr(0,1) != '/')
                        css = _path+css;
                    config.contentsCss.push(css);
                });
            }
        }

        Object.each(this.options.customConfig, function(v,k){
            config[k] = v;
        });

        this.editor = CKEDITOR.replace(this.main, config);

        //this.editor.on('instanceReady', this.editorReady.bind(this));

    },

    editorReady: function(){
        this.ready = true;
        this.main.inject(this.fieldInstance.fieldPanel);
    },

    toElement: function(){
        return this.main;
    },

    setValue: function(pValue){
        this.value = pValue;
        if (this.ready)
            this.editor.setData(this.value);
    },

    getValue: function(){
        if (!this.ready)
            return this.value;
        else return this.editor.getData();
    }
});