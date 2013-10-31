var admin_system_development_restLogger = new Class({

    initialize: function (pWin) {

        this.win = pWin;

        this.onCall = this.onCall.bind(this);
        this.win.addEvent('close', function () {
            window.removeEvent('restCall', this.onCall);
        }.bind(this));

        this._createLayout();

    },

    _createLayout: function () {

        this.win.content.empty();

        this.table = new ka.Table([
            [t('Date'), '90px'],
            [t('Method'), '50px'],
            [t('Url'), '250px'],
            [t('Response'), null, "html"]
        ], {
            absolute: true
        });

        this.table.inject(this.win.content);

        window.addEvent('restCall', this.onCall);

    },

    onCall: function (pData, pRequest) {

        if (typeOf(pData) != 'object') {
            return;
        }

        if (!pRequest.data) {
            pRequest.data = {};
        }

        this.table.addRow([
            (new Date()).format('%H:%m:%S:%L'),
            pRequest.data.method || pRequest.options.method,
            pRequest.options.url,
            '<pre style="line-height: 13px; white-space: pre-wrap;">' +
                pRequest.xhr.responseText.replace('<', '&lt;').replace('>', '&gt;') + '</pre>'
        ]);
    }

});