ka.WindowAdd = new Class({
    Extends: ka.WindowEdit,

    initialize: function (pWin, pContainer) {
        this.windowAdd = true;
        this.parent(pWin, pContainer);
    },

    loadItem: function () {

        //ist in render() am ende also lösche unnötigen balast
        this.win.setLoading(false);

        if (this.winParams.item){
            this.saveBtn.setText([t('Save'), '#icon-checkmark-6']);
            this.removeBtn.show();
            if (this.previewBtn) this.previewBtn.show();
        } else {
            this.saveBtn.setText([t('Add'), '#icon-checkmark-6']);
            this.removeBtn.hide();
            if (this.previewBtn) this.previewBtn.hide();
        }

        
        var first = this.container.getElement('input[type=text]');
        if (first) {
            first.focus();
        }

        this.ritem = this.retrieveData(true);

        this.openAddItem();
    },


    /**
     * Opens a first step overlay, that points then to the actual form if only nestedAddWithPositionSelection is set.
     * If addMultiple is set, we use $addMultipleFields $addMultipleFixedFields for the insertion and ignore the
     * actual form.
     * If nothing is set, this method does nothing.
     *
     */
    openAddItem: function(){

        if ((this.classProperties.asNested && this.classProperties.nestedAddWithPositionSelection) || this.classProperties.addMultiple){

            //show dialog with
            this.createNewFirstDialog();

            if (this.tabPane)
                this.tabPane.hide();

            if (this.classProperties.addMultiple){

                if (this.classProperties.nestedAddWithPositionSelection){
                    this.addItemMultiAddLayout = new ka.LayoutHorizontal(this.addDialogFieldContainer, {
                        columns: [null, this.classProperties.addMultipleFieldContainerWidth]
                    });

                    new ka.LayoutSplitter(this.addItemMultiAddLayout.getColumn(2), 'left');

                    this.addDialogLayoutPositionChooser = this.addNestedObjectPositionChooser(this.addItemMultiAddLayout.getColumn(1));

                    this.populateAddMultipleForm(this.addItemMultiAddLayout.getColumn(2));
                } else {
                    this.populateAddMultipleForm(this.addDialogFieldContainer);
                }

            } else {

                this.addDialogFieldContainer.setStyle('position', 'relative');
                this.addDialogLayoutPositionChooser = this.addNestedObjectPositionChooser(this.addDialogFieldContainer);

            }

            if (this.addDialogLayoutPositionChooser){

                this.addDialogLayoutPositionChooser.addEvent('positionChoose', function(pDom, pDirection, pItem, pChooser){

                    this.addItemToAdd = {direction: pDirection, id: pDom.id, objectKey: pDom.objectKey};

                    this.checkAddItemForm();
                }.bind(this));

            }


            if (!this.classProperties.addMultiple && this.classProperties.nestedAddWithPositionSelection){

                this.openAddItemNextButton = new ka.Button(tc('addNestedObjectChoosePositionDialog', 'Next'))
                    .inject(this.openAddItemPageBottom);

                this.openAddItemNextButton.setButtonStyle('blue');
                this.openAddItemNextButton.setEnabled(false);
            } else if (this.classProperties.addMultiple){

                this.openAddItemSaveButton = new ka.Button(tc('addNestedObjectChoosePositionDialog', 'Save'))
                    .inject(this.openAddItemPageBottom);

                this.openAddItemSaveButton.setButtonStyle('blue');
                this.openAddItemSaveButton.setEnabled(false);
            }

        }
    },

    checkAddItemForm: function(){

        var valid = true;

        if (!this.addItemToAdd) valid = false;

        if (this.classProperties.addMultiple){
            if (this.addMultipleFieldForm && !this.addMultipleFieldForm.checkValid()) valid = false;

            if (this.openAddItemSaveButton)
                this.openAddItemSaveButton.setEnabled(valid);
        }
        if (this.openAddItemNextButton)
            this.openAddItemNextButton.setEnabled(valid);

    },

    populateAddMultipleForm: function(pContainer){


        var fields = {};

        if (typeOf(this.classProperties.addMultipleFixedFields) == 'array' &&
            this.classProperties.addMultipleFixedFields.length > 0){

            Array.each(this.classProperties.addMultipleFixedFields, function(item, key){
                fields[key] = item;
            });

        }

        if (typeOf(this.classProperties.addMultipleFields) == 'array' &&
            this.classProperties.addMultipleFields.length > 0){



            fields.__perItemFields = {
                title: t('Values per entry'),
                type: 'array',
                columns: [],
                fields: {}
            };

            Array.each(this.classProperties.addMultipleFields, function(item, key){

                var column = {};
                column.label = item.label;
                column.desc = item.desc;
                column.width = item.width;

                fields.__perItemFields.columns.push(column);

                fields.__perItemFields.fields[key] = item;
            });

        }

        this.addMultipleFieldForm = new ka.FieldForm(pContainer, fields);

    },

    createNewFirstDialog: function(){

        this.addNestedAddPage = new Element('div', {
            'class': 'ka-windowEdit-form-addDialog'
        }).inject(this.container);

        this.addDialogLayout = new ka.LayoutVertical(this.addNestedAddPage, {
            rows: [null, 30],
            gridLayout: true
        });

        this.addDialogFieldContainer = this.addDialogLayout.getContentRow(1);

        this.openAddItemPageBottom = new Element('div', {
            'class': 'kwindow-win-buttonBar'
        }).inject(this.addDialogLayout.getContentRow(2));

        this.openAddItemCancelButton = new ka.Button(t('Cancel')).inject(this.openAddItemPageBottom);

        this.openAddItemCancelButton.addEvent('click', function(){
            alert('todo')
        }.bind(this));


    },

    addNestedObjectPositionChooser: function(pContainer){
        var objectOptions = {};
        var fieldObject;

        objectOptions.type = 'tree';
        objectOptions.object = this.classProperties.object;
        objectOptions.scopeChooser = false;
        objectOptions.noWrapper = true;
        objectOptions.selectable = false;
        objectOptions.moveable = this.classProperties.nestedMoveable;

        var lastSelected;

        var choosePosition = function(pChooser, pDom, pDirection, pItem){

            if (lastSelected)
                lastSelected.removeClass('ka-objectTree-positionChooser-item-active');

            lastSelected = pChooser;
            lastSelected.addClass('ka-objectTree-positionChooser-item-active');

            fieldObject.fireEvent('positionChoose', [pDom, pDirection, pItem, pChooser]);

        }

        var addChooser = function(pDom, pDirection, pItem){

            var div;

            if (pDirection != 'into'){
                div = new Element('div', {
                    styles: {
                        paddingLeft: pDom.getStyle('padding-left').toInt()+18
                    }
                }).inject(pDom.childrenContainer, pDirection);
            } else {

                div = pDom.span;
                pDom.insertedAddChooser = true;
            }

            var a = new Element('a',{
                html: ' <------ &nbsp;&nbsp;',
                'class': 'ka-objectTree-positionChooser-item',
                href: 'javascript:;',
                style: 'text-decoration: none;'
            })
                .addEvent('click', function(){
                    choosePosition(this, pDom, pDirection, pItem);
                })
                .inject(div);

            new Element('span', {
                'class': 'ka-objectTree-positionChooser-item-text',
                text: pDirection == 'into' ? tc('addNestedObjectChoosePositionDialog', 'Into this!') : tc('addNestedObjectChoosePositionDialog', 'Add here!')
            }).inject(a);

            return div;
        }

        objectOptions.onChildrenLoaded = function(pItem, pDom){

            if (pDom.childrenContainer){
                var children = pDom.childrenContainer.getChildren('.ka-objectTree-item');
                if (children.length > 0){
                    pDom.childrenContainer.getChildren('.ka-objectTree-item').each(function(item){
                        addChooser(item, 'after', item.objectEntry);
                        addChooser(item, 'into', item.objectEntry);
                    });
                }
            }

            if (!pDom.insertedAddChooser)
                addChooser(pDom, 'into', pDom.objectEntry);

        }.bind(this);

        if (this.languageSelect)
            objectOptions.scopeLanguage = this.languageSelect.getValue();

        var treeContainer = new Element('div', {
            style: 'position: absolute; left: 0; right: 0; top: 0; bottom: 0; overflow: auto;'
        }).inject(pContainer);

        fieldObject = new ka.Field(objectOptions, treeContainer);
        return fieldObject;

    },

    nestedItemSelected: function(pItem, pDom){
        //pDom.objectKey
        //pDom.id

        if (pDom.objectKey == this.classProperties.object){

            if (_this.classProperties.edit){

                ka.entrypoint.open(ka.entrypoint.getRelative(_this.win.getEntryPoint(), _this.classProperties.editEntrypoint), {
                    item: pItem.values
                }, this);

            }

        } else if (this.classProperties.nestedRootEdit){
            var entryPoint = ka.entrypoint.getRelative(this.win.getEntryPoint(), this.classProperties.nestedRootEditEntrypoint);
            ka.entrypoint.open(entryPoint);
        }

    }
});
