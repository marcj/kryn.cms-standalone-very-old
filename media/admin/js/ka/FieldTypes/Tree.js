ka.FieldTypes.Tree = new Class({
    
    Extends: ka.FieldAbstract,

    Binds: ['selected'],

    options: {
        object: '',

        /**
         * The pk value of the scope.
         * If this is null, we display a ka.Select chooser of all scopes (object entries from RootAsObject)
         *
         * @var {Mixed}
         */
        scope: null,

        /**
         * if the object has a scope (RootAsObject), then we display the root's object as ka.Select (true) or
         * we display all roots at once (false)
         *
         * @var boolean
         */
        scopeChooser: true,

        /**
         * if the object behind the scope (RootAsObject) is multiLanguage, we can filter by it.
         *
         * @var {Boolean}
         */
        scopeLanguage: null,

        /**
         * TODO, can be useful
         * @var [Boolean}
         */
        scopeCondition: false,

        /**
         * Enables the drag'n'drop moving.
         *
         * @var {Boolean}
         */
        move: true,

        /**
         * Enables the 'add'-icon.
         * @var {Boolean}
         */
        withObjectAdd: false,

        /**
         * The icon of the add-icon
         * @var {String}
         */
        iconAdd: 'admin/images/icons/add.png',

        icon: null,

        /**
         * Enables the opening of the first level during the first load.
         *
         * @var {Boolean}
         */
        openFirstLevel: null,

        /**
         * If you want to change the root object. Thats not very often the case.
         * @var {String}
         */
        rootObject: null,

        /**
         * Enables the context menu (edit, delete etc.)
         * @var {Boolean}
         */
        withContext: true,

        /**
         * Initial selects the object of the given pk.
         *
         * @var {Mixed}
         */
        selectObject: null,

        /**
         * @var {Object}
         */
        iconMap: null,


        /**
         * Enabled the selection.
         * @var {Boolean}
         */
        selectable: true,



        labelTemplate: false,
        objectFields: ''
    },

    trees: [],

    definition: {},

    createLayout: function(){

        this.definition = ka.getObjectDefinition(this.options.object);

        if (!this.definition) throw 'Object not found '+this.options.object;
        if (!this.definition.nested) throw 'Object is not a nested set '+this.options.object;


        if (!this.options.labelTemplate){
            this.options.labelTemplate = this.definition.labelTemplate;
        }

        if (this.definition.nestedRootAsObject && !this.options.scope){

            if (this.options.scopeChooser){
                var options = {
                    object: this.definition.nestedRootObject,
                    objectLanguage: this.options.scopeLanguage
                };

                this.scopeField = new ka.Select(this.fieldInstance.fieldPanel, options);

                this.scopeField.addEvent('change', function(){
                    this.loadTree(this.scopeField.getValue());
                }.bind(this));
            } else {

                //load all scope entries
                new Request.JSON({url: _path+'admin/object-roots/'+ka.urlEncode(this.options.object),
                onComplete: function(pResponse){

                    this.treesContainer.empty();
                    this.trees = [];

                    if (pResponse.data){

                        Array.each(pResponse.data, function(item){
                            this.addTree(item);
                        }.bind(this));
                    }


                }.bind(this)}).get();

            }

            this.treesContainer = new Element('div').inject(this.fieldInstance.fieldPanel);
        } else {
            this.treesContainer = this.fieldInstance.fieldPanel;
            this.loadTree(this.options.scope);
        }
    },

    loadTree: function(pScope){

        this.treesContainer.empty();

        this.trees = [];

        this.addTree(pScope);
    },

    addTree: function(pScope){

        var clazz = ka.ObjectTree;
        if (this.definition.treeInterface && this.definition.treeInterface != 'default'){
            if (!this.definition.treeInterfaceClass){
                throw 'TreeInterface class in "treeInterfaceClass" is not defined.'
            } else {
                if (!(clazz = ka.getClass(this.definition.treeInterfaceClass))){
                    throw 'Class does not exist '+this.definition.treeInterfaceClass;
                }
            }
        }

        this.options.scope = pScope;
        var tree= new clazz(this.treesContainer, this.options.object, this.options);
        tree.addEvent('change', this.fieldInstance.fireChange);
        tree.addEvent('select', this.selected);

        var proxyMethods = ['deselect', 'getItem', 'select'];
        proxyMethods.each(function(method){
            this.fieldInstance[method] = tree[method];
        }.bind(this));

        var proxyEvents = ['ready', 'childrenLoaded'];
        proxyEvents.each(function(event){
            tree.addEvent(event, function(p){
                this.fieldInstance.fireEvent(event, p);
            }.bind(this));

        }.bind(this));

        this.trees.include(tree);
        return tree;
    },

    getSelectedTree: function(){

        var selected = null;
        Array.each(this.trees, function(tree){
            if (selected) return;
            if (tree.hasSelected()) selected = tree;
        });

        return tree;

    },

    selected: function(pItem, pDom){
        this.fireEvent('select', [pItem, pDom]);
    },

    setValue: function(pValue){

        Array.each(this.trees, function(tree){
            tree.setValue(pValue);
        });
    },

    getValue: function(){
        var value = null;
        Array.each(this.trees, function(tree){
            if (value !== null) return;
            if (tree.hasSelected()){
                value = tree.getValue();
            }
        });
        return value;
    }
});