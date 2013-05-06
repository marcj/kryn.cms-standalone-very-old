ka.DashboardWidgets = ka.DashboardWidgets || {};

ka.DashboardWidgets.Base = new Class({

    Binds: ['update'],
    Implements: [Options, Events],

    /**
     * @var {Element}
     */
    container: null,

    streamPath: null,

    initialize: function (container, options) {
        this.container = container;
        this.setOptions(options);

        this.main = new Element('div', {
            'class': 'ka-Dashboard-widget'
        }).inject(this.container);

        this.create();

        if (null !== this.streamPath) {
            ka.registerStream(this.streamPath, this.update);
        }

    },

    toElement: function () {
        return this.main;
    },

    destroy: function () {
        if (null !== this.streamPath) {
            ka.deRegisterStream(this.streamPath, this.update);
        }
    },

    update: function (value, stream) {

    },

    /**
     * Overwrite this method.
     * Use `this.main` to inject your stuff.
     */
    create: function () {

    }
});