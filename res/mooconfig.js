var Site = {
	
	startMooForm: function(){
		if ($('download')) Site.download();
		if ($("process")) Site.process();
	},
	
	process: function(){
		$('select_all').addEvent('click', function(e){
		 	var inputs = $$("#process input.extkey");
		 	inputs.each(function(input){ input.checked = true; });
		});
		$('select_none').addEvent('click', function(e){
		 	var inputs = $$("#process input.extkey");
		 	inputs.each(function(input){ input.checked = false; });
		});
	},

	download: function(){
		var inputs = $$('input[deps]');
		inputs.each(function(input){
			input.addEvent('click', function(event){ Site.toggleDeps(this); });
		});
		$('select_all').addEvent('click', function(){
			var inputs = $$('input[deps]');
			inputs.each(function(input){ Site.check(input); });
		});
		$('select_none').addEvent('click', function(){
			var inputs = $$('input[deps]');
			inputs.each(function(input){ Site.uncheck(input); });
		});
	},
	
	toggleDeps: function(input){
		if (input.checked){
			Site.check(input);
		}
		else {
			Site.uncheck(input);
		}
	},
	
	uncheck: function(input){
		input.checked = false;
		var deps = input.get('deps');
		if (deps){
			Site.uncheckDepending(input.get('id'));
		}
	},
	
	check: function(input){
		input.checked = true;
		var deps = input.get('deps');
		if (deps){
			Site.checkDependants(deps.split(','));
		}
	},
	
	checkDependants: function(deps){
		deps.each(function(input){
			input = $(input);
			if (input && !input.checked) Site.check(input);
		});
	},
	
	uncheckDepending: function(component){
		var depending = $$('input[deps]:checked').each(function(input){
			if (input.get('deps').split(',').contains(component)) Site.uncheck(input);
		});
	}
	
};

window.addEvent('domready', function(){
	Site.startMooForm();
});

