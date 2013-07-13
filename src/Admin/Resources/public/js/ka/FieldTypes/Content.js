ka.FieldTypes.Content = new Class({
    Extends: ka.FieldAbstract,

    options: {
        /**
         * If we display the save buttons etc.
         */
        standalone: false
    },

    createLayout: function() {
        this.mainLayout = new ka.Layout(this.getContainer(), {
            layout: [
                {columns: [null], height: 50},
                {columns: [null, 180]}
            ],
            splitter: [
                [2, 2, 'left']
            ]
        });

        this.mainLayout.getCell(1, 1).addClass('ka-ActionBar');
        this.mainLayout.getTd(1, 1).set('colspan', 2);

        this.headerLayout = new ka.Layout(this.mainLayout.getCell(1, 1), {
            fixed: false,
            layout: [
                {columns: [null, 100]}
            ]
        });

        this.buttonGroup = new ka.ButtonGroup(this.headerLayout.getCell(1, 1));
        this.layoutBtn = this.buttonGroup.addButton(t('Layout'), '#icon-layout');
        this.listBtn = this.buttonGroup.addButton(t('List'), '#icon-list-4');

        this.layoutBtn.setPressed(true);

        this.headerLayout.getCell(1, 2).setStyle('text-align', 'right');
        this.headerLayout.getCell(1, 2).setStyle('white-space', 'nowrap');

        if (this.options.standalone) {
            new Element('span', {
                text: t('Layout:'),
                style: 'line-height: 30px; display: inline-block; padding-right: 5px;'
            }).inject(this.headerLayout.getCell(1, 2));

            this.layoutSelection = new ka.Field({
                noWrapper: true,
                type: 'layout'
            }, this.headerLayout.getCell(1, 2));

            this.actionGroup = new ka.ButtonGroup(this.headerLayout.getCell(1, 2));

            this.actionGroup.addButton(t('Reset'), '#icon-escape');
            this.actionGroup.addButton(t('Versions'), '#icon-history');

            this.saveBtn = new ka.Button(t('Save'))
                .setButtonStyle('blue')
                .inject(this.headerLayout.getCell(1, 2));
        } else {
            this.mainLayout.getCell(1, 1).addClass('ka-Field-content-actionBar');
            //attach to the FormField class, since we need the information which page is loaded
            //and which layout we should use.
            //todo
        }

        this.win.setTitle(t('Home'));
        var id = (Math.random() * 10 * (Math.random() * 10)).toString(36).slice(2);

        window.addEvent('krynEditorLoaded', function(editor) {
            if (editor && editor.getId() == id) {
                this.setEditor(editor);
            }
        }.bind(this));

//        window.addEvent('ckEditorReady', function(content, toolbar){
//             if (content && instanceOf(content, ka.ContentAbstract)) {
//                if (id == content.getContentInstance().getSlot().getEditor().getId()) {
//                    document.id(toolbar).inject(this.headerLayout.getCell(1,1));
//                }
//             }
//        }.bind(this));

        var options = {
            standalone: this.options.standalone
        };

        var params = {
            '_kryn_editor': 1,
            '_kryn_editor_id': id,
            '_kryn_editor_options': options
        };

        this.mainLayout.getCell(2, 1).setStyles({
            'border': '1px solid silver',
            'background': 'url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAYAAACp8Z5+AAAAHklEQVQIW2NkQAXGjEh8YyD7LEwAzAFJggTgHJAAAE+uAzjGgU3wAAAAAElFTkSuQmCC) repeat'
//            'background-color': '#22638e',
//            'background-image': 'linear-gradient(rgba(255,255,255, 0.05) 1px, transparent 1px),\
//            linear-gradient(90deg, rgba(255,255,255,0.05) 1px, transparent 1px)',
//            'background-size': '10px 10px, 10px 10px',
//            'background-position': '-1px -1px, -1px -1px'
        });

        this.mainLayout.getCell(2, 2).setStyles({
            'border': '1px solid silver',
            'border-left': 0
        });

        this.frameContainer = new Element('div', {
            style: 'position: absolute; left: 20px; top: 20px; right: 20px; bottom: 50px;'
        }).inject(this.mainLayout.getCell(2, 1));

        this.optionsContainer = new Element('div', {
            'style': 'position: absolute; left: 20px; height: 30px; right: 20px; bottom: 5px; color: #444;'
        }).inject(this.mainLayout.getCell(2, 1));

        new Element('span', {
            text: t('Zoom:'),
            style: 'padding-right: 5px; line-height: 30px;'
        }).inject(this.optionsContainer);

        this.slider = new ka.Slider(this.optionsContainer, {
            steps: 200
        });

        this.zoomValue = new Element('span', {
            text: '100%',
            style: 'padding-left: 5px; line-height: 30px;'
        }).inject(this.optionsContainer);

        this.slider.setValue(100);

        this.iframe = new Element('iframe', {
            src: _path + '?' + Object.toQueryString(params),
            frameborder: 0,
            style: 'position: relative; display: block; left: -2px; border: 3px solid #fff; height: 100%; width: 100%;'
        }).inject(this.frameContainer);

        this.slider.addEvent('change', function(step) {
            if (0 == step) step = 1;
            this.zoomValue.set('text', step + '%');
            var val = step / 100;
            document.id(this.iframe.contentWindow.document.body).setStyle('zoom', step + '%');
            //console.log(this.iframe.contentWindow.document.body);
            //this.iframe.setStyle('-webkit-transform', 'scale(' + val + ')');
            //this.iframe.setStyle('-moz-transform', 'scale(' + val + ')');
        }.bind(this));

        this.renderSidebar()
    },

    setEditor: function(editor) {
        this.editor = editor;
    },

    renderSidebar: function() {
        this.sidebar = new Element('div', {
            'class': 'ka-normalize ka-editor-sidebar ka-scrolling'
        }).inject(this.mainLayout.getCell(2, 2));

        this.inspector = new Element('div', {
            'class': 'ka-editor-inspector'
        }).inject(this.sidebar);

        this.inspectorTitle = new Element('div', {
            'class': 'ka-editor-inspector-title',
            text: t('Inspector')
        }).inject(this.inspector);

        this.inspectorContainer = new Element('div', {
            'class': 'ka-editor-inspector-container',
            text: t('Nothing selected.'),
            style: 'color: gray; text-align: center;'
        }).inject(this.inspector);

        this.contentElements = new Element('div', {
            'class': 'ka-editor-contentElements'
        }).inject(this.sidebar);

        this.contentElementsTitle = new Element('div', {
            'class': 'ka-editor-contentElements-title',
            text: t('Content elements')
        }).inject(this.inspector);

        this.contentElementsContainer = new Element('div', {
            'class': 'ka-editor-contentElements-container'
        }).inject(this.inspector);

        Object.each(ka.ContentTypes, function(content, type) {
            this.addContentTypeIcon(type, content);
        }.bind(this));

        this.plugins = new Element('div', {
            'class': 'ka-editor-plugins'
        }).inject(this.sidebar);

        this.pluginsTitle = new Element('div', {
            'class': 'ka-editor-plugins-title',
            text: t('Plugins')
        }).inject(this.inspector);

        this.pluginsContainer = new Element('div', {
            'class': 'ka-editor-plugins-container'
        }).inject(this.inspector);

        Object.each(ka.settings.configs, function(config, bundleName) {
            this.addPlugins(bundleName, config);
        }.bind(this));
    },

    addPlugins: function(bundleName, config) {
        var self = this;
        if (config.plugins) {
            var a;

            new Element('div', {
                'class': 'ka-editor-plugins-subTitle',
                text: config.label || bundleName
            }).inject(this.pluginsContainer);

            Object.each(config.plugins, function(plugin, pluginId) {
                a = new Element('a', {
                    href: 'javascript: ;',
                    draggable: true,
                    text: plugin.label || plugin.id,
                    'class': 'ka-editor-sidebar-item ka-editor-sidebar-draggable ' + (plugin.icon || 'icon-cube-2')
                }).inject(this.pluginsContainer);

                a.addEvent('dragstart', function() {
                    self.dragStart(this);
                });
                a.addEvent('dragend', function() {
                    self.dragEnd(this);
                });

                a.kaContentType = 'plugin';
            }.bind(this));
        }
    },

    addContentTypeIcon: function(pType, pContent) {
        var type = new pContent;
        var self = this;

        var a = new Element('a', {
            href: 'javascript: ;',
            text: type.label,
            draggable: true,
            'class': 'ka-editor-sidebar-item ka-editor-sidebar-draggable ' + type.icon
        }).inject(this.contentElementsContainer);

        a.addEvent('dragstart', function() {
            self.dragStart(this);
        });
        a.addEvent('dragend', function() {
            self.dragEnd(this);
        });

        a.kaContentType = pType;
    },

    dragStart: function(item) {
        console.log('start', item);
        if (this.editor) {
            this.editor.highlightSlotsBubbles(true);
        }
    },

    dragEnd: function(item) {
        if (this.editor) {
            this.editor.highlightSlotsBubbles(false);
        }
    }

});