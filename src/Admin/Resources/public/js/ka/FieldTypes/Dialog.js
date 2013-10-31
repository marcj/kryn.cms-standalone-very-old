ka.FieldTypes.Dialog = new Class({
    Extends: ka.FieldAbstract,

    Statics: {
        options: {
            buttonLabel: {
                type: 'text',
                label: 'Button label'
            },
            minWidth: {
                lable: 'Min width',
                desc: 'px, numbers or % values.',
                type: 'text'
            },
            minHeight: {
                lable: 'Min height',
                desc: 'px, numbers or % values.',
                type: 'text'
            }
        }
    },

    options: {
        buttonLabel: ''
    },

    createLayout: function (container) {
        //deactivate auto-hiding of the childrenContainer.
        this.fieldInstance.handleChildsMySelf = true;

        this.button = new ka.Button(this.options.buttonLabel || this.options.label).inject(container);

        var copy = this.fieldInstance.prepareChildContainer;

        this.fieldInstance.prepareChildContainer = function() {
            this.childContainer = this.fieldInstance.childContainer = new Element('div', {
                'class': 'ka-field-childrenContainer',
                style: 'display: none'
            }).inject(container);
        }.bind(this);

        this.button.addEvent('click', this.openDialog.bind(this));
    },

    toElement: function() {
        return this.button.toElement();
    },

    openDialog: function() {
        this.dialog = new ka.Dialog(this.getWin(), Object.merge(this.options, {
            withButtons: true,
            cancelButton: false,
            applyButtonLabel: t('OK')
        }));

        if (this.childContainer) {
            this.childContainer.inject(this.dialog.getContentContainer());
            this.childContainer.setStyle('display');

            this.dialog.addEvent('closed', function(){
                this.childContainer.setStyle('display', 'none');
                this.childContainer.inject(document.id(this), 'after');
            }.bind(this));
        }

        this.dialog.center(true);
    }
});