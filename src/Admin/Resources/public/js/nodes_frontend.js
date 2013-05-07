var admin_nodes_frontend = new Class({

	initialize: function(pWin){
		this.win = pWin;
		this.createLayout();
	},

	createLayout: function(){

        this.wrapper = new Element('div', {
            'class': 'ka-admin-nodes-frontend-wrapper'
        }).inject(this.win.content);

        this.win.hideTitleGroups();

        this.win.setTitle(t('Home'));
	
		this.iframe = new Element('iframe', {
			src: _path+'?_kryn_editor=1',
			frameborder: 0
		}).inject(this.wrapper);

	}

});