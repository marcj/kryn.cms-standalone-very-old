ka.FieldTypes.Tree = new Class({
    
    Extends: ka.FieldAbstract,

    options: {
        object: '',
        scope: null,
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

    createLayout: function(){

        var clazz = ka.ObjectTree;
        var definition = ka.getObjectDefinition(this.options.object);
        if (!definition) throw 'Object not found '+this.options.object;
        if (!definition.nested) throw 'Object is not a nested set '+this.options.object;

        if (definition.chooserBrowserJavascriptClass){
            if (!(clazz = ka.getClass(definition.chooserBrowserJavascriptClass))){
                throw 'Class does not exist '+definition.chooserBrowserJavascriptClass;
            }
        }

        if (!this.options.labelTemplate){
            this.options.labelTemplate = definition.chooserFieldDataModelFieldTemplate;
        }

        this.tree = new clazz(this.fieldInstance.fieldPanel, this.options.object, this.options);
        this.tree.addEvent('change', this.fieldInstance.fireChange);
    },

    setValue: function(pValue){
        this.tree.setValue(pValue);
    },

    getValue: function(){
        return this.tree.getValue();
    }
});