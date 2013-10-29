ka.FileTree = new Class({
    Extends: ka.ObjectTree,

    addItem: function(pItem, pParent) {
        var a = this.parent(pItem, pParent);
        a.addClass('admin-files-item');
        a.fileItem = pItem;
    }
});