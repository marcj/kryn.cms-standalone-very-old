ka.FieldTypes.Content = new Class({
    Extends: ka.FieldAbstract,

    options: {
        /**
         * If we display the save buttons etc.
         */
        standalone: false
    },

    preview: 0,

    createLayout: function() {
        this.mainLayout = new ka.Layout(this.getContainer(), {
            layout: [
                {columns: [null], height: 50},
                {columns: [null, 230]}
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
        this.headerLayout.getCell(1, 1).addClass('ka-ActionBar-left');

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
        }

        this.win.setTitle(t('Home'));

        this.mainLayout.getCell(2, 1).setStyles({
            'border': '1px solid silver',
            'background': 'url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAYAAACp8Z5+AAAAIklEQVQIW2NkQAL37t37zwjjgzhKSkqMYAEYB8RmROaABAAGgA+evuWXiAAAAABJRU5ErkJggg==) repeat'
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
                {columns: [null]},
                {columns: [null], height: 30}
            ]
        });

        this.optionsContainer = this.editableAreaLayout.getCell(2, 1);

        new Element('span', {
            text: t('Zoom:'),
            style: 'padding-right: 5px; line-height: 30px;'
        }).inject(this.optionsContainer);

        this.slider = new ka.Slider(this.optionsContainer, {
            steps: 100
        });

        this.zoomValue = new Element('span', {
            text: '100%',
            style: 'padding-left: 5px; line-height: 30px;'
        }).inject(this.optionsContainer);

        this.slider.setValue(100);

        var iframeContainer = this.editableAreaLayout.getCell(1, 1);

        this.iframe = new Element('iframe', {
            frameborder: 0,
            style: 'display: block; border: 0; height: 100%; width: 100%;'
        }).inject(iframeContainer);

        iframeContainer.setStyle('border', '1px solid #d5d5d5');
        iframeContainer.addClass('ka-scrolling');

        if (this.options.standalone) {
            this.domainSelection = new ka.Select(this.headerLayout.getCell(1, 1), {
                object: 'core:domain',
                onChange: function(item) {
                    this.loadEditor(this.domainSelection.getValue());
                }.bind(this)
            });
        } else {
            this.loadEditor(this.options.domainId);
        }

        this.slider.addEvent('change', function(step) {
            if (0 == step) step = 1;
            this.zoomValue.set('text', step + '%');
            var val = step / 100;
            document.id(this.iframe.contentWindow.document.body).setStyle('zoom', step + '%');
        }.bind(this));

        this.renderSidebar()
    },

    loadEditor: function(domainId) {
        var options = {
            standalone: this.options.standalone,
            domainId: domainId
        };

        var id = (Math.random() * 10 * (Math.random() * 10)).toString(36).slice(2);

        window.addEvent('krynEditorLoaded', function(editor) {
            if (editor && editor.getId() == id) {
                this.setEditor(editor);
                editor.setContentField(this);
            }
        }.bind(this));

        var params = {
            '_kryn_editor': 1,
            '_kryn_editor_id': id,
            '_kryn_editor_options': options
        };

        this.iframe.set('src', _path + '?' + Object.toQueryString(params));
    },

    save: function() {
        if (this.editor) {
            var value = this.editor.getValue();

            if (this.lastSaveRq) {
                this.lastSaveRq.cancel();
            }

            console.log('save', value);
            this.lastSaveRq = new Request.JSON({url: this.getUrl(), onComplete: function (pResponse) {

            }.bind(this)}).post({content: value});
        }
    },

    getUrl: function () {
        return _pathAdmin + 'admin/object/Core:Node/' + this.editor.options.node.id + '?_method=patch';
    },

    selectElement: function(element) {
        this.select(element.kaContentInstance);
    },

    select: function(content) {
        if (this.lastContent === content) return;

        this.deselect();

        this.inspectorContainer.setStyle('color');
        this.inspectorContainer.setStyle('text-align');

        content.setSelected(true);

        this.lastContent = content;
    },

    getSelected: function() {
        return this.lastContent;
    },

    deselect: function() {
        if (this.lastContent) {
            this.lastContent.setSelected(false);
            delete this.lastContent;
        }

        this.nothingSelected();
    },

    setEditor: function(editor) {
        this.editor = editor;
    },

    renderSidebar: function() {
        this.sidebar = new Element('div', {
            'class': 'ka-normalize ka-scrolling ka-editor-sidebar'
        }).inject(this.mainLayout.getCell(2, 2), 'top');

        new Element('div', {
            text: t('Show slots'),
            style: 'cursor: default',
            'class': 'ka-editor-sidebar-item icon-checkbox-partial'
        })
            .addEvent('mouseover', function() {
                if (this.editor)
                    this.editor.highlightSlots(true);
            }.bind(this))
            .addEvent('mouseout', function() {
                if (this.editor)
                    this.editor.highlightSlots(false);
            }.bind(this))
            .inject(this.sidebar);

        this.showPreview = new Element('div', {
            text: t('Toggle preview'),
            'class': 'ka-editor-sidebar-item icon-eye-4'
        })
            .addEvent('click', function() {
                this.togglePreview();
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
        }).inject(this.contentElements);

        this.contentElementsContainer = new Element('div', {
            'class': 'ka-editor-contentElements-container'
        }).inject(this.contentElements);

        Object.each(ka.ContentTypes, function(content, type) {
            this.addContentTypeIcon(type, content);
        }.bind(this));

        this.plugins = new Element('div', {
            'class': 'ka-editor-plugins'
        }).inject(this.sidebar);

        this.pluginsTitle = new Element('div', {
            'class': 'ka-editor-plugins-title',
            text: t('Plugins')
        }).inject(this.plugins);

        this.pluginsContainer = new Element('div', {
            'class': 'ka-editor-plugins-container'
        }).inject(this.plugins);

        Object.each(ka.settings.configs, function(config, bundleName) {
            this.addPlugins(bundleName, config);
        }.bind(this));
    },

    togglePreview: function() {
        var active = ++this.preview % 2;

        if (active) {
            this.showPreview.addClass('ka-editor-sidebar-item-active');
        } else {
            this.showPreview.removeClass('ka-editor-sidebar-item-active');
        }

        if (this.editor) {
            this.editor.setPreview(active);
        }
    },

    nothingSelected: function() {
        this.inspectorContainer.set('text', t('Nothing selected.'));
        this.inspectorContainer.setStyle('color', 'gray');
        this.inspectorContainer.setStyle('text-align', 'center');
    },

    getInspectorContainer: function() {
        return this.inspectorContainer;
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
        var self = this;

        var a = new Element('div', {
            text: pContent.label,
            draggable: true,
            'class': 'ka-editor-sidebar-item ka-editor-sidebar-draggable ' + (pContent.icon || '')
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