ka.autoChooser = new Class({

    Implements: [Options, Events],

    container: false,
    objectKey: '',
    win: false,

    options: {
        multi: false
    },

    initialize: function(pContainer, pObjectKey, pChooserOptions, pWindowInstance){

        this.container = pContainer;
        this.objectKey = pObjectKey;
        this.setOptions(pChooserOptions);
        this.win = pWindowInstance;

        this._createLayout();

    },

    _createLayout: function(){

        this.container.empty();

        var columns = [];

        var primaries = ka.getPrimariesForObject(this.objectKey);

        Object.each(primaries, function(field, fieldKey){
            columns.include([
                field.label, field.width?field.width:80
            ]);
        });

        var objectDefinition = ka.getObjectDefinition(this.objectKey);

        Object.each(objectDefinition.chooserAutoColumns, function(column, key){

            columns.include([column.label, column.width?column.width:null]);

        });

        this.table = new ka.Table(columns, {
            selectable: true,
            multi: this.options.multi
        });

        this.table.addEvent('select', function(){
            this.fireEvent('select');
        }.bind(this));

        document.id(this.table).inject(this.container);


        this.loadPage(1);
    },

    deselect: function(){
        this.table.deselect();
    },

    getValue: function(){

        var tr = this.table.selected();
        var item = tr.retrieve('item');

        var primaries = ka.getPrimariesForObject(this.objectKey);

        var result;

        if (Object.getLength(primaries) > 1){
            result = [];

            Object.each(primaries, function(field, fieldKey){
                result.include(item[fieldKey]);
            });

        } else if (Object.getLength(primaries) == 1){
            Object.each(primaries, function(field, fieldKey){
                result = item[fieldKey];
            });
        } else {
            logger('There are no primaries for object '+this.objectKey);
        }

        return result;
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

            var tr = this.table.addRow(row);
            tr.store('item', item);

        }.bind(this));


    }


})