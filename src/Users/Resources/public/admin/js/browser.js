var users_browser = new Class({
	
	initialize: function( pWin ){
		this.win = pWin;
		
		
		this.options = this.win.params;
		
		this.tabGroup = this.win.addTabGroup();

		this.buttons = {};
		this.buttons['users'] = this.tabGroup.addButton(_('Users'), _path+ PATH_WEB + '/admin/images/icons/user.png', this.selectTab.bind(this, 'users'));
		this.buttons['groups'] = this.tabGroup.addButton(_('Groups'), _path+ PATH_WEB + '/admin/images/icons/group.png', this.selectTab.bind(this, 'groups'));
	
		this.searchInput = new Element('input', {
			'class': 'text',
			style: 'position: absolute; top: 27px; right: 7px;'
		})
		.addEvent('keyup', this.didKeyup.bind(this))
		.inject( this.win.border );
		
		this.panes = {};
		Object.each(this.buttons, function(button, key){
			this.panes[key] = new Element('div').inject( this.win.content );
		}.bind(this));
		
		this.tableGroups = new ka.Table([
		    [_("Name")],
		    [_("Users count"), 100]
		]).inject(this.panes['groups']);
		
		this.tableUsers = new ka.Table([
 		    [_("First name")],
 		    [_("Last name")],
 		    [_("Username"), 150]
 		]).inject(this.panes['users']);
		
		this.bottomBar = this.win.addBottomBar();

		this.bottomBar.addButton(_('Cancel')).addEvent('click', this.cancelAndClose.bind(this));
		this.bottomBar.addButton(_('Choose')).addEvent('click', this.choose.bind(this));
		
		this.win.addEvent('close', this.cancel.bind(this));
		
		this.selectTab('users');
	},
	
	cancel: function(){
		if( this.options.onCancel )
        	this.options.onCancel();
	},
	
	cancelAndClose: function(){
	
	   this.cancel();
	   this.win.close();
	
	},
	
	choose: function(){
        
		var target_id = this.tableUsers.selected();
		var target_type = 2; //user
		
		if( this.tableGroups.selected() ){
			target_type = 1;//group
			target_id = this.tableGroups.selected();
		}
		
		if( target_id ){
    		target_id = target_id.retrieve('row');
    		target_id = target_id.id;
		}
		
		if( this.options.onChoose )
			this.options.onChoose( target_type, target_id );
		
		this.win.close();
		
	},
	
	selectTab: function( pId ){
		
		this.type = pId;
		
		Object.each(this.buttons, function(button, key){
			button.setPressed(false);
			this.panes[key].setStyle('display', 'none');
		}.bind(this));

		this.buttons[pId].setPressed(true);
		this.panes[pId].setStyle('display', 'block');

		this.doSearch();
		
	},
	
	didKeyup: function(){
		
		if( this.didKeyupTimer )
			clearTimeout(this.didKeyupTimer);
		
		var _do = function(){
			this.doSearch();
		}.bind(this);
		
		this.didKeyupTimer = _do.delay(100);
		
	},
	
	doSearch: function(){
		
		
		var query = this.searchInput.value;
		
		var req = {
			query: query,
			type: this.type
		}

		var table = this.tableGroups;
		if( this.type == 'users' )
			table = this.tableUsers;
		
		table.loading(true);
		new Request.JSON({url: _path+'admin/users/browser', noCache: 1, onComplete: this.renderResult.bind(this)}).post(req);
		
	},
	
	renderResult: function(pRes){

		var table = this.tableGroups;
		if( this.type == 'users' ){
			table = this.tableUsers;
			
			table.empty();
			Array.each(pRes, function(row){
				var tr = table.addRow([row.first_name, row.last_name, row.username]);
				tr.store('row', row);
				this.prepareTr(tr, 'users');
			}.bind(this));
		} else {

			table.empty();
			Array.each(pRes, function(row){
				var tr = table.addRow([row.name, row.usercount]);
				tr.store('row', row);
				this.prepareTr(tr, 'groups');
			}.bind(this));
		}

		table.loading(false);
		
		
	},
	
	prepareTr: function( pTr, pType ){
		
		pTr.getElements('td').addEvent('click', function(){

			this.tableUsers.deselect();
			this.tableGroups.deselect();
			pTr.addClass('active');
			
		}.bind(this));
		
		pTr.getElements('td').addEvent('dblclick', function(){
			
			this.choose();
		
		}.bind(this));
	}
	
	
	
	
});