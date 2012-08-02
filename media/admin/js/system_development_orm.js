var admin_system_development_orm = new Class({


	initialize: function(pWin){
		this.win = pWin;
		this._createLayout();
	},

	_createLayout: function(){

		this.win.content.empty();

		this.buttonBar = this.win.addBottomBar();

		this.btnEnv  = this.buttonBar.addButton('Propel Build Environment', this.callPropelGen.bind(this, 'environment'));
		this.btnCheck  = this.buttonBar.addButton('Check model.xml', this.callPropelGen.bind(this, 'check'));
		this.btnModel  = this.buttonBar.addButton('Write Model', this.callPropelGen.bind(this, 'models'));
		this.btnUpdate = this.buttonBar.addButton('Update Database', this.callPropelGen.bind(this, 'update'));

	},

	callPropelGen: function(pCmd){

		//prepare gui
		[this.btnEnv, this.btnCheck, this.btnModel, this.btnUpdate].invoke('setEnabled', false);
		this.win.content.empty();

		this.progressBar = new ka.Progress(t('Wait for action.'));
		this.progressBar.inject(this.win.content);

		this.resultContainer = new Element('div', {
			style: 'position: absolute; left: 5px; right: 5px; top: 25px; bottom: 5px;'
				 + 'border: 1px solid silver; background-color: white; white-space: pre; padding: 5px; overflow: auto'
		}).inject(this.win.content);

		this._callGen(pCmd);

	},

	done: function(){

		//activate gui
		[this.btnEnv, this.btnCheck, this.btnModel, this.btnUpdate].invoke('setEnabled', true);

	},

	requestCompleted: function(pResult){

		if (pResult.error){
			new Element('h2', {style: 'color: red', text: 'Failed'}).inject(this.resultContainer);
		new Element('div', {text: pResult.message}).inject(this.resultContainer);

		} else {
			new Element('h2', {style: 'color: green', text: 'Success'}).inject(this.resultContainer);
			new Element('div', {text: pResult.data}).inject(this.resultContainer);

		}

		this.progressBar.setText(t('Done.'));

		this.displayWaitForAction = (function(){
			this.progressBar.setText(t('Wait for action.'));
		}).delay(2000, this);

		this.done();
	},

	_callGen: function(pCmd){

		if (this.displayWaitForAction)
			clearTimeout(this.displayWaitForAction);

		this.resultContainer.set('text', '');

		this.progressBar.setText(t('Requesting ...'));

		this.lr = new Request.JSON({url: _path+'admin/system/orm/'+pCmd, noCache: 1,
			onComplete: this.requestCompleted.bind(this)}).get();

	}


});