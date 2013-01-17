ka.FieldTypes.Tree = new Class({
    
    Extends: ka.FieldAbstract,

    Binds: ['selected'],

    options: {
        object: '',
        scope: null,

        scopeChooser: false,

        move: true,
        withObjectAdd: false,
        iconAdd: 'admin/images/icons/add.png',

        icon: null,
        openFirstLevel: null,
        rootObject: null,
        withContext: true,

        selectObject: null,
        iconMap: null,

        labelTemplate: false,
        objectFields: ''
    },

    definition: {},

    createLayout: function(){

        this.definition = ka.getObjectDefinition(this.options.object);
        if (!this.definition) throw 'Object not found '+this.options.object;
        if (!this.definition.nested) throw 'Object is not a nested set '+this.options.object;


        if (!this.options.labelTemplate){
            this.options.labelTemplate = this.definition.labelTemplate;
        }


        if (this.definition.nestedRootAsObject && !this.options.scope){
            var options = {
                object: this.definition.nestedRootObject
            };

            this.scopeField = new ka.Select(this.fieldInstance.fieldPanel, options);

            this.scopeField.addEvent('change', function(){
                this.loadTree(this.scopeField.getValue());
            }.bind(this));

            this.treeContainer = new Element('div').inject(this.fieldInstance.fieldPanel);
        } else {
            this.treeContainer = this.fieldInstance.fieldPanel;
            this.loadTree(this.options.scope);
        }
    },

    loadTree: function(pScope){

        this.treeContainer.empty();

        var clazz = ka.ObjectTree;
        if (this.definition.treeInterface != 'default'){
            if (!this.definition.treeInterfaceClass){
                throw 'TreeInterface class in "treeInterfaceClass" is not defined.'
            } else {
                if (!(clazz = ka.getClass(this.definition.treeInterfaceClass))){
                    throw 'Class does not exist '+this.definition.treeInterfaceClass;
                }
            }
        }

        this.options.scope = pScope;
        this.tree = new clazz(this.treeContainer, this.options.object, this.options);
        this.tree.addEvent('change', this.fieldInstance.fireChange);
        this.tree.addEvent('select', this.selected);


        var proxyMethods = ['deselect', 'getItem', 'select'];
        proxyMethods.each(function(method){
            this.fieldInstance[method] = this.tree[method];
        }.bind(this));

        var proxyEvents = ['ready', 'childrenLoaded'];
        proxyEvents.each(function(event){
            this.tree.addEvent(event, function(p){
                this.fieldInstance.fireEvent(event, p);
            }.bind(this));

        }.bind(this));
    },

    selected: function(pItem, pDom){
        this.fireEvent('select', [pItem, pDom]);
    },

    setValue: function(pValue){
        this.tree.setValue(pValue);
    },

    getValue: function(){
        return this.tree.getValue();
    }
});