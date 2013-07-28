ka.ContentTypes = ka.ContentTypes || {};

ka.ContentTypes.Image = new Class({

    Extends: ka.ContentAbstract,

    Statics: {
        icon: 'icon-images',
        label: 'Image',
        mimeTypes: ['image/png', 'image/jpeg', 'image/jpg', 'image/gif']
    },

    value: {},

    options: {

    },

    createLayout: function() {
        this.main = new Element('div', {
            'class': 'ka-contentType-image'
        }).inject(this.contentInstance);

        if (this.getContentInstance().drop && ka.ContentTypes.Image.mimeTypes.contains(this.getContentInstance().drop.type)) {
            this.renderDrop(this.getContentInstance().drop);
        } else {
            this.renderChooser();
        }
    },

    renderDrop: function(file) {
        this.file = file;
        var reader = new FileReader();

        reader.onload = function(e) {
            this.main.empty();
            this.image = new Element('img', {
                src: e.target.result,
                styles: {
                    'max-width': '100%'
                }
            }).inject(this.main);
            this.value.image = e.target.result;
        }.bind(this);

        this.main.set('text', t('Reading ...'));

        this.progressBar = new Element('div', {
            src: '0%',
            styles: {
                textAlign: 'center'
            }
        }).inject(this.main);

        reader.onprogress = function(e) {
            var percentLoaded = Math.round((e.loaded / e.total) * 100);
            this.progressBar.set('text', percentLoaded + '%');
        }.bind(this);

        reader.readAsDataURL(this.file);
    },

    renderValue: function() {
        this.main.empty();
        if (this.value.image) {
            this.image = new Element('img', {
                src: this.value.image,
                styles: {
                    'max-width': '100%'
                }
            }).inject(this.main);
        } else {
            this.renderChooser();
        }
    },

    setValue: function(value) {
        this.value = value || {};
        this.renderValue();
    },

    getValue: function() {
        return this.value;
    },

    save: function(callback) {

    },

    renderChooser: function() {
        new Element('input', {
            type: 'file'
        }).inject(this.main);
    },

    loadInspector: function(parent) {

    }
});