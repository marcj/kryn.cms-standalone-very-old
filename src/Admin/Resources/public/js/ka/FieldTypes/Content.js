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
                {columns: [null]}
            ]
        });

        this.mainLayout.getCell(1, 1).addClass('ka-ActionBar');

        this.headerLayout = new ka.Layout(this.mainLayout.getCell(1, 1), {
            fixed: false,
            layout: [
                {columns: [null, 100]}
            ]
        });

        this.buttonGroup = new ka.ButtonGroup(this.headerLayout.getCell(1,1));
        this.layoutBtn = this.buttonGroup.addButton(t('Layout'), '#icon-layout');
        this.listBtn = this.buttonGroup.addButton(t('List'), '#icon-list-4');

        this.layoutBtn.setPressed(true);

        this.headerLayout.getCell(1,2).setStyle('text-align', 'right');
        this.headerLayout.getCell(1,2).setStyle('white-space', 'nowrap');

        if (this.options.standalone) {
            this.actionGroup = new ka.ButtonGroup(this.headerLayout.getCell(1, 2));

            this.actionGroup.addButton(t('Reset'), '#icon-escape');
            this.actionGroup.addButton(t('Versions'), '#icon-history');

            this.layoutSelection = new ka.Field({
                noWrapper: true,
                type: 'layout'
            }, this.actionGroup);

            this.saveBtn = new ka.Button(t('Save'))
                .setButtonStyle('blue')
                .inject(this.headerLayout.getCell(1,2));
        } else {
            this.mainLayout.getCell(1, 1).addClass('ka-Field-content-actionBar');
            //attach to the FormField class, since we need the information which page is loaded
            //and which layout we should use.
            //todo
        }

        this.win.setTitle(t('Home'));
        var id = (Math.random() * 10 * (Math.random()*10)).toString(36).slice(2);

        window.addEvent('krynEditorLoaded', function(editor) {
            if (editor && editor.getId() == id) {
                this.setValue(editor);
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

        this.mainLayout.getCell(2, 1).setStyle('border', '1px solid silver');

        this.iframe = new Element('iframe', {
            src: _path + '?' + Object.toQueryString(params),
            frameborder: 0,
            style: 'position: absolute; display: block; border: 0; height: 100%; width: 100%;'
        }).inject(this.mainLayout.getCell(2, 1));

        //this.fieldInstance.fieldPanel;
    }

});