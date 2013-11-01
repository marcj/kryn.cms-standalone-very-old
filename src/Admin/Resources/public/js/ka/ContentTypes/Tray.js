ka.ContentTypes = ka.ContentTypes || {};

ka.ContentTypes.Tray = new Class({

    Extends: ka.ContentAbstract,
    Binds: ['applyValue'],

    Statics: {
        icon: 'icon-clipboard-2',
        label: 'Tray'
    },

    options: {

    },

    createLayout: function () {
        this.main = new Element('div', {
            'class': 'ka-normalize ka-content-plugin'
        }).inject(this.contentInstance);

        this.iconDiv = new Element('div', {
            'class': 'ka-content-inner-icon icon-clipboard-2'
        }).inject(this.main);

        this.inner = new Element('div', {
            'class': 'ka-content-inner ka-normalize'
        }).inject(this.main);
    },

    renderValue: function () {
        this.inner.empty();
        if ('object' !== typeOf(this.value)) {
            this.value = {};
        }

        if (this.lastRenderedNode !== this.value.node || !this.value.node) {
            if (!this.value.node) {
                this.inner.set('text', t('Please choose a tray node.'));
            } else {
                this.inner.set('text', 'Loading ...');
                ka.getObjectLabel('core:node/' + this.value.node, function(label, item) {
                    if (false === label) {
                        this.inner.set('text', t('Tray not found.'));
                    } else {
                        if (3 === item.type) {
                            this.inner.set('text', '» ' + label);
                        } else {
                            this.inner.set('text', t('Not a depot chosen.'));
                        }
                    }
                }.bind(this));
            }
        }

        this.lastRenderedNode = this.value.node;
    },

    /**
     * adds/loads all additional fields to the inspector.
     */
    selected: function(inspectorContainer) {
        var toolbarContainer = new Element('div', {
            'class': 'ka-content-deposit-toolbarContainer'
        }).inject(inspectorContainer);

        this.nodeInput = new ka.Field({
            'label': t('Tray'),
            type: 'page',
            width: 'auto',
            onChange: function(){
                this.fireChange();
            }.bind(this)
        }, toolbarContainer);

        if (this.value && this.value.node) {
            this.nodeInput.setValue(this.value.node);
        }
    },

    fireChange: function() {
        if ('object' !== typeOf(this.value)) {
            this.value = {};
        }

        this.value.node = this.nodeInput.getValue();
        this.renderValue();
    },

    setValue: function (pValue) {
        this.value = typeOf(pValue) !== 'string' ? pValue : JSON.decode(pValue);
        this.renderValue();
    },

    getValue: function () {
        return typeOf(this.value) == 'string' ? this.value : JSON.encode(this.value);
    }

});
