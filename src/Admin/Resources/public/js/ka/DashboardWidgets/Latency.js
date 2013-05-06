ka.DashboardWidgets.Latency = new Class({
    Extends: ka.DashboardWidgets.Base,

    streamPath: 'admin/latency',

    create: function () {
        this.header = new Element('h3', {
            style: 'margin: 0; padding: 0',
            text: ka.tc('dashboardWidget.latency', 'Latency')
        })
        .inject(this.main);

        this.latency = new Element('div', {
            style: 'padding: 5px; text-align: center;',
            text: 'Frontend'
        }).inject(this.main);

        this.latency = new Element('div', {
            style: 'padding: 5px; text-align: center; font-size: 52px;',
            text: '23ms'
        }).inject(this.main);

        this.average15 = new Element('div', {
            style: 'padding: 5px; text-align: center;',
            text: 'Backend / DB / Session / Cache'
        }).inject(this.main);

        this.average15 = new Element('div', {
            style: 'padding: 5px; text-align: center; font-size: 18px;',
            text: '31ms / 5ms / 1ms / 1ms'
        }).inject(this.main);
    },

    update: function (value) {
    }
});