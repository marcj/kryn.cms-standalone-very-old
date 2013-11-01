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

        this.main.addEventListener('dragover', function(event) {
            var validDrop = true;
            if (validDrop) {
                event.stopPropagation();
                event.preventDefault();
                event.dataTransfer.dropEffect = 'move';
            }
        }.bind(this));

        this.main.addEventListener('drop', function(event) {
            var items = event.dataTransfer.files.length > 0 ? event.dataTransfer.files : null;

            if (!items && event.dataTransfer.types) {
                items = [];
                Array.each(event.dataTransfer.types, function(type) {
                    var dataType = event.dataTransfer.getData(type);
                    items.push({
                        type: type,
                        getAsString: function(cb) {
                            cb(dataType);
                        }
                    });
                });
            }

            if (0 < items.length) {
                event.stopPropagation();
                event.preventDefault();
                this.renderDrop(items[0]);
            }
        }.bind(this));
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
            text: '0%',
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

    /**
     *
     * @param {ka.SaveProgress} saveProgress
     */
    save: function(saveProgress) {
        saveProgress.setProgressRange(100);
        if (this.file && !this.fileWatcher) {
            this.file.target = '/Unclassified/';
            this.file.autoRename = true;
            this.file.html5 = true;
            this.fileWatcher = ka.getAdminInterface().getFileUploader().newFileUpload(this.file);
            this.fileWatcher.addEvent('done', function() {
                saveProgress.done();
            });
            this.fileWatcher.addEvent('progress', function(progress) {
                saveProgress.progress(progress);
            });
            this.fileWatcher.addEvent('cancel', function() {
                saveProgress.cancel();
            });
            this.fileWatcher.addEvent('error', function() {
                saveProgress.error();
            });
        } else {
            saveProgress.done();
        }
    },

    stopSaving: function() {
        if (this.fileWatcher) {
            this.fileWatcher.cancel();
        }
    },

    renderChooser: function() {
        this.iconDiv = new Element('div', {
            'class': 'ka-content-inner-icon icon-images'
        }).inject(this.main);

        this.inner = new Element('div', {
            'class': 'ka-content-inner ka-normalize',
            text: t('Choose or drop a image.')
        }).inject(this.main);
    },

    selected: function(inspectorContainer) {
        var toolbarContainer = new Element('div', {
            'class': 'ka-content-image-toolbarContainer'
        }).inject(inspectorContainer);

        this.input = new ka.Field({
            label: 'Image',
            type: 'file',
            width: 'auto',
            onChange: function(file) {
                console.log(file);
            }
        }, toolbarContainer);
    }
});