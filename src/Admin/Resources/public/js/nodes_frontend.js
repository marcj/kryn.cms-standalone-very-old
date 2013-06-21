var admin_nodes_frontend = new Class({

    initialize: function (pWin) {
        this.win = pWin;
        this.createLayout();
    },

    createLayout: function () {
        this.wrapper = new Element('div', {
            'class': 'ka-admin-nodes-frontend-wrapper'
        }).inject(this.win.content);

        this.headerLayout = new ka.Layout(this.win.getTitleGroupContainer(), {
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

        new ka.Button(t('Reset'))
            .inject(this.headerLayout.getCell(1,2));

        new ka.Button(t('Versions'))
            .inject(this.headerLayout.getCell(1,2));

        this.layoutSelection = new ka.Field({
            noWrapper: true,
            type: 'layout'
        }, this.headerLayout.getCell(1,2));

        this.saveBtn = new ka.Button(t('Save'))
            .setButtonStyle('blue')
            .inject(this.headerLayout.getCell(1,2));

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

        this.iframe = new Element('iframe', {
            src: _path + '?_kryn_editor=1&_kryn_editor_id=' + id,
            frameborder: 0
        }).inject(this.wrapper);
    },

    setValue: function(editor) {
        this.layoutSelection.setValue(editor.getNode().layout);

    }

});