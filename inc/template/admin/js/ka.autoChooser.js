ka.autoChooser = new Class({

    container: false,
    objectKey: '',
    objectDefinition: {},
    win: false,

    initialize: function(pContainer, pObjectKey, pObjectDefinition, pWindowInstance){

        this.container = pContainer;
        this.objectKey = pObjectKey;
        this.objectDefinition = pObjectDefinition;
        this.win = pWindowInstance;

        this._createLayout();

    },

    _createLayout: function(){

        this.container.empty();

        var columns = [
            ["ID", 50]
        ];


        Object.each(this.objectDefinition.chooserAutoColumns, function(column, key){

            columns.include([column.label, column.width?column.width:null]);

        });


        this.table = new ka.Table(columns);

        document.id(this.table).inject(this.container);


        this.loadPage(1);
    },

    loadPage: function(pPage){

        if (this.lr)
            this.lr.cancel();

        this.lr = new Request.JSON({url: _path+'admin/backend/autoChooser', noCache: 1, onComplete: function(pRes){

            this.renderResult(pRes);
            this.renderActions(pPage, pRes.maxPages, pRes.maxItems);

        }.bind(this)}).post({object: this.objectKey, page: pPage});

    },

    renderActions: function(pPage, pMaxPages, pMaxItems){



    },

    renderResult: function(pResult){

        this.table.empty();

        Array.each(pResult.items, function(item){

            var row  = [];
            Object.each(item, function(col){
                row.include(col);
            })

            this.table.addRow(row);

        }.bind(this));


    }


})