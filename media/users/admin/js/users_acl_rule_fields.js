var users_acl_rule_fields = new Class({

    Implements: Events,

    initialize: function( pField, pParent, pRefs ){

        this.field = pField;
        this.parent = pParent;

        this.parent.setStyle('margin', 0);

        this.value = '';

        this.main = new Element('div', {

        }).inject(this.parent);

        new Element('div', {
            style: 'color: silver; padding: 5px 0px;',
            text: t('Please note that these fields are only for view/edit/add forms, not for the listing.')
        }).inject(this.main);

        var border = this.parent.getParent('.kwindow-border');
        if( border )
            this.win = border.retrieve('win');

        if (pRefs.win)
            this.win = pRefs.win;

        this.renderFields();

    },

    renderFields: function(){

        var definition = ka.getObjectDefinition(this.field.object);

        if (!definition){
            new Element('div', {'class': 'error', text: 'Invalid object: '+this.field.object}).inject(this.main);
            return;
        }

        if (!definition.fields || Object.getLength(definition.fields) == 0){
            new Element('div', {
                style: 'color: silver; padding: 20px; text-align: center;',
                text: t('There are no fields to manage.')
            }).inject(this.main);
            return;
        }

        this.table = new ka.Table([
            [t('Field'), '200'],
            [t('Access'), '140'],
            [t('Detailed field rules')]

        ], {absolute: false, hover: false});


        Object.each(definition.fields, function(def, key){

            var select = new ka.Select();
            document.id(select).setStyle('width', 140);
            select.add('2', [t('Inherited'), 'admin/images/icons/arrow_turn_right_up.png']);
            select.add('0', [t('Deny'), 'admin/images/icons/exclamation.png']);
            select.add('1', [t('Allow'), 'admin/images/icons/accept.png']);

            var more = '';

            if (def.type == 'object'){
                more = this.createConditionRule(def, key);
            } else {
                more = this.createConditionRule(def, key, true);
            }

            this.table.addRow([
                def.label || key,
                select,
                more
            ]);

        }.bind(this));


        this.table.inject(this.main);

    },

    createConditionRule: function(pDefinition, pKey, pOnlyThisFieldCondition){

        var div = new Element('div');

        var conditions = new Element('div', {
            style: 'padding-left: 15px;'
        }).inject(div);


        new ka.Button([t('Add field rule'), 'admin/images/icons/add.png'])
        .addEvent('click', this.addFieldRule.bind(this, [conditions, pKey, pOnlyThisFieldCondition]))
        .inject(div);

        return div;
    },

    addFieldRule: function(pContainer, pFieldKey, pOnlyThisFieldCondition){

        var div = new Element('div', {
            style: 'border: 1px solid silver; position: relative; margin-top: 15px; '+
            'padding-top: 15px; margin-bottom: 5px; background-color: #eee;'
        });

        var actions = new Element('div', {
            styles: {
                position: 'absolute',
                left: 15,
                top: -11
            }
        }).inject(div);

        var select = new ka.Select(actions);
        document.id(select).setStyles({
            width: 100,
            marginRight: 4
        });

        var images = new Element('div', {
            'class': 'ka-Select-box',
            style: 'width: 69px'
        }).inject(actions);

        var imagesContainer = new Element('div', {
            'class': 'ka-Select-box-title',
            style: 'right: 6px;'
        }).inject(images);

        new Element('img', {src: _path+ PATH_MEDIA + '/admin/images/icons/arrow_up.png'})
        .addEvent('click', function(){
            if (div.getPrevious()){
                div.inject(div.getPrevious(), 'before');
            }
        })
        .inject(imagesContainer);

        new Element('img', {src: _path+ PATH_MEDIA + '/admin/images/icons/arrow_down.png'})
        .addEvent('click', function(){
            if (div.getNext()){
                div.inject(div.getNext(), 'after');
            }
        }).inject(imagesContainer);

        new Element('img', {src: _path+ PATH_MEDIA + '/admin/images/icons/delete.png'})
        .addEvent('click', function(){
            this.win._confirm(t('Really delete?'), function(a){
                if (!a) return;
                div.destroy();
            })
        }.bind(this))
        .inject(imagesContainer);

        select.add('0', [t('Deny'), 'admin/images/icons/exclamation.png']);
        select.add('1', [t('Allow'), 'admin/images/icons/accept.png']);


        if (pOnlyThisFieldCondition){

            new ka.field({
                noWrapper: true,
                type: 'fieldCondition',
                field: pFieldKey,
                object: this.field.object,
                startWith: 1
            }, div, {win: this.win})
        } else {
            var objectDefinition = ka.getObjectDefinition(this.field.object);
            var fieldDefinition = objectDefinition.fields[pFieldKey];

            new ka.field({
                noWrapper: true,
                type: 'objectCondition',
                object: fieldDefinition.object,
                startWith: 1
            }, div, {win: this.win})
        }


        div.inject(pContainer);

    },


    getValue: function(){
        return this.value;
    },

    setValue: function( pValue ){
        if( !pValue || pValue == '' ) return;


        this.value = pValue;

    },

    isEmpty: function(){
        if( this.value == '' ) return true;
        return false;
    },

    highlight: function(){
        this.main.highlight();
    }

});