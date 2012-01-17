ka.autoChooser = new Class({

    Implements: [Options, Events],

    container: false,
    objectKey: '',
    win: false,

    options: {
        multi: false
    },


    currentPage: 1,

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

        this.table.body.setStyle('padding-bottom', 24);

        this.table.addEvent('select', function(){
            this.fireEvent('select');
        }.bind(this));

        document.id(this.table).inject(this.container);

        this.container.setStyle('overflow', 'hidden');

        this.pagination = new Element('div', {
            'class': 'ka-autoChooser-pagination-container gradient'
        }).inject(this.container);

        this.pagination.setStyle('bottom', -25);

        this.imgToLeft = new Element('img', {
            src: _path+'inc/template/admin/images/icons/control_back.png'
        })
        .addEvent('click', this.pageToLeft.bind(this))
        .inject(this.pagination);

        this.iCurrentPage = new Element('input', {
            value: '-',
            maxlength: 5
        })
        .addEvent('keydown', function(e){

            if (e.control == false && e.meta == false && e.key.length == 1 && !e.key.test(/[0-9]/))
                e.stop();

            if (e.key == 'enter')
                this.loadPage(this.iCurrentPage.value);

        }.bind(this))
        .addEvent('keyup', function(e){
            this.value = this.value.replace(/[^0-9]+/, '');
        })
        .addEvent('blur', function(e){
            if (this.value == ''){
                this.value = 1;
                this.loadPage(this.iCurrentPage.value);
            }
        })
        .inject(this.pagination);

        new Element('span', {
            text: '/'
        }).inject(this.pagination);

        this.sMaxPages = new Element('span', {
            text: ''
        }).inject(this.pagination);

        this.imgToRight = new Element('img', {
            src: _path+'inc/template/admin/images/icons/control_play.png'
        })
        .addEvent('click', this.pageToRight.bind(this))
        .inject(this.pagination);

        this.loadPage(1);


        this.pagination.tween('bottom', 0);
    },

    pageToLeft: function(){

        if (this.currentPage<=1) return false;
        this.loadPage(--this.currentPage);

    },

    pageToRight: function(){

        if (this.currentPage>=this.maxPages) return false;
        this.loadPage(++this.currentPage);

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

            this.renderResult(pRes.items);
            this.renderActions(pPage, pRes.pages, pRes.count);

        }.bind(this)}).post({object: this.objectKey, page: pPage});

    },

    renderActions: function(pPage, pMaxPages, pMaxItems){

        this.currentPage = pPage;
        this.maxPages = pMaxPages;
        this.sMaxPages.set('text', pMaxPages+'('+pMaxItems+')');
        this.iCurrentPage.value = pPage;

        this.imgToLeft.setStyle('opacity', (pPage == 1)?0.5:1);
        this.imgToRight.setStyle('opacity', (pPage == pMaxPages)?0.5:1);


    },

    renderResult: function(pItems){

        this.table.empty();

        Array.each(pItems, function(item){

            var row  = [];
            Object.each(item, function(col){
                row.include(col);
            })

            var tr = this.table.addRow(row);
            tr.store('item', item);

        }.bind(this));

    }


})