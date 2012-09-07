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
    }
});
