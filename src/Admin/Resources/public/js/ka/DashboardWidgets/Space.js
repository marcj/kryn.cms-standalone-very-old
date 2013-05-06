ka.DashboardWidgets.Space = new Class({
    Extends: ka.DashboardWidgets.Base,

    streamPath: 'admin/space',

    gauges: [],

    create: function () {
        this.header = new Element('h3', {
            style: 'margin: 0; padding: 0',
            text: ka.tc('dashboardWidget.space', 'Space')
        })
        .inject(this.main);

        this.load = new Element('div', {
            style: 'padding: 5px; text-align: center',
            text: ' '
        }).inject(this.main);

    },

    update: function (value) {
        if (0 === this.gauges.length) {
            Array.each(value, function (space, id) {
                this.gauges.push(
                    new ka.ui.Speedmeter(this.main, {
                        title: 'HDD ' + space.name,
                        label: 'GB',
                        max: (space.size  / 1024 / 1024).toFixed(0),
                        value: (space.used  / 1024 / 1024).toFixed(0)
                    })
                )
            }.bind(this));
        }

        var total = 0;
        var used = 0;
        Array.each(value, function (space, id) {
            this.gauges[id].setValue((space.used  / 1024 / 1024).toFixed(0));
            total += space.size;
            used  += space.used;
        }.bind(this));

        this.load.set('text',
            tf('Total used: %s', ka.bytesToSize(used * 1024)) + ' / ' +
            tf('Total size: %s', ka.bytesToSize(total * 1024))
        );
    }
});