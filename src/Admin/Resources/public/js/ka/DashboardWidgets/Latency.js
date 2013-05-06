ka.DashboardWidgets.Latency = new Class({
    Extends: ka.DashboardWidgets.Base,

    streamPath: 'admin/latency',

    gauges: [],

    create: function () {
        this.header = new Element('h3', {
            style: 'margin: 0; padding: 0',
            text: ka.tc('dashboardWidget.latency', 'Latency')
        })
        .inject(this.main);

        this.latency = new Element('div', {
            style: 'padding: 5px; text-align: center; font-size: 52px;',
            text: '23ms'
        }).inject(this.main);

        this.average15 = new Element('div', {
            style: 'padding: 5px; text-align: center; font-size: 22px;',
            text: '31ms / one hour'
        }).inject(this.main);

        this.average15 = new Element('div', {
            style: 'padding: 5px; text-align: center; font-size: 22px;',
            text: '25ms / day'
        }).inject(this.main);
    },

    update: function (value) {
    }
});