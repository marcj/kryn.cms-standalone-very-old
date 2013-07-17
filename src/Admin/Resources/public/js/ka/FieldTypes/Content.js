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
                .addEvent('click', this.save.bind(this))
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
            'background': 'url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAYAAACp8Z5+AAAAIklEQVQIW2NkQAL37t37zwjjgzhKSkqMYAEYB8RmROaABAAGgA+evuWXiAAAAABJRU5ErkJggg==) repeat'
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

        this.editableAreaContainer = new Element('div', {
            style: 'position: absolute; left: 15px; right: 15px; top: 15px; bottom: 0px;'
        }).inject(this.mainLayout.getCell(2, 1));

        this.editableAreaLayout = new ka.Layout(this.editableAreaContainer, {
            layout: [
                {columns: [null], height: 1},
                {columns: [null]},
                {columns: [null], height: 30}
            ]
        });

        this.toolbarContainer = this.editableAreaLayout.getCell(1, 1);
        this.optionsContainer = this.editableAreaLayout.getCell(3, 1);

        this.toolbarContainer.setStyle('padding-bottom', 5);

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
            style: 'position: relative; display: block; left: -1px; border: 1px solid #d5d5d5; height: 100%; width: 100%;'
        }).inject(this.editableAreaLayout.getCell(2, 1));

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

    save: function() {
        if (this.editor) {
            var value = this.editor.getValue();
            console.log('save', value);
        }
    },

    setEditor: function(editor) {
        this.editor = editor;

        window.addEvent('ckEditorReady', function(textInstance, toolbar) {
            toolbar.inject(this.toolbarContainer);
        }.bind(this));
    },

    renderSidebar: function() {
        this.sidebar = new Element('div', {
            'class': 'ka-normalize ka-editor-sidebar ka-scrolling'
        }).inject(this.mainLayout.getCell(2, 2));

         new Element('div', {
             text: t('Show slots'),
             style: 'cursor: default',
             'class': 'ka-editor-sidebar-item icon-checkbox-partial'
         })
            .addEvent('mouseover', function(){
                if (this.editor)
                    this.editor.highlightSlots(true);
            }.bind(this))
            .addEvent('mouseout', function(){
                if (this.editor)
                    this.editor.highlightSlots(false);
            }.bind(this))
            .inject(this.sidebar);

         new Element('div', {
             text: t('Toggle preview'),
             'class': 'ka-editor-sidebar-item icon-eye-4'
         })
            .addEvent('click', function(){
                if (this.editor)
                    this.editor.togglePreview();
            }.bind(this))
            .inject(this.sidebar);

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
                a = new Element('div', {
                    draggable: true,
                    text: plugin.label || plugin.id,
                    'class': 'ka-editor-sidebar-item ka-editor-sidebar-draggable ' + (plugin.icon || 'icon-cube-2')
                }).inject(this.pluginsContainer);

                a.addListener('dragstart', function(e) {
                    self.dragStart(this, e);
                });
                a.addListener('dragend', function(e) {
                    self.dragEnd(this, e);
                });

                a.kaContentType = 'plugin';
                a.kaContentValue = {bundle: bundleName, plugin: pluginId};
            }.bind(this));
        }
    },

    addContentTypeIcon: function(pType, pContent) {
        var type = new pContent;
        var self = this;

        var a = new Element('div', {
            text: type.label,
            draggable: true,
            'class': 'ka-editor-sidebar-item ka-editor-sidebar-draggable ' + type.icon
        }).inject(this.contentElementsContainer);

        a.addListener('dragstart', function(e) {
            self.dragStart(this, e);
        });
        a.addListener('dragend', function(e) {
            self.dragEnd(this, e);
        });

        a.kaContentType = pType;
    },

    dragStart: function(item, e) {
        var data = {};
        data.type = item.kaContentType;

        if (item.kaContentValue) {
            data.content = item.kaContentValue;
        }

        e.dataTransfer.effectAllowed = 'copy';
        e.dataTransfer.setData('application/json', JSON.encode(data));

        if (this.editor) {
            this.editor.highlightSlotsBubbles(true);
        }
    },

    dragEnd: function(item, e) {
        if (this.editor) {
            this.editor.highlightSlotsBubbles(false);
        }
    }

});