var Site = {
	
	startMooForm: function(){
		Site.trs = $$('tr.option');
		Site.chks = $$('table.download div.check');

		if ($('download')) Site.download();
		if ($('process')) Site.process();
	},
	
	process: function(){
		var allinputs = $$(Site.chks);

		allinputs.each(function(chk){
			chk.inputElement = chk.getElement('input');
			chk.inputElement.setStyle('display', 'none');
		});
		
		allinputs.each(function(chk){
			chk.inputElement = chk.getElement('input');
			if (chk.inputElement.checked) Site.select(chk);
		});

		Site.processparse();

		$('select_all').addEvent('click', function(){
			Site.chks.each(function(chk){ Site.select(chk); });
		});

		$('select_none').addEvent('click', function(){
			Site.chks.each(function(chk){ Site.deselect(chk); });
		});
		
	},

	download: function(){
		Site.radios = $$('#compression-options div.check');

		Site.lib_active = [];
		
		$('select_all').addEvent('click', function(){
			Site.chks.each(function(chk){ Site.select(chk); });
			Site.enableLib('mootools-core', true);
			Site.enableLib('mootools-more', true);
		});

		$('select_none').addEvent('click', function(){
			Site.chks.each(function(chk){ Site.deselect(chk); });
			Site.disableLib('mootools-core', true);
			Site.disableLib('mootools-more', true);
		});
		
		//alert (Site.radios);
		Site.fx = [];
		Site.parse();
		
		var allinputs = $$(Site.chks, Site.radios);
		
		allinputs.each(function(chk){
			chk.inputElement = chk.getElement('input');
			chk.inputElement.setStyle('display', 'none');
		});
		
		allinputs.each(function(chk){
			if (chk.inputElement.checked) Site.select(chk);
		});
		
		Site.select(Site.radios[0]);

	},
	
	select: function(chk){
		if (Site.trs[chk.index]) Site.trs[chk.index].addClass('selected');
		chk.inputElement.checked = 'checked';
		if (!chk.hasClass('lib_check')) {
			Site.fx[chk.index].start({
				'color': '#000'
			});
		}
		
		chk.addClass('selected');
		
		if (chk.deps){
			chk.deps.each(function(id){
				if (!id || !$(id)) return dbug.log(id);
				if (!$(id).hasClass('selected')&& !$(id).inputElement.disabled) Site.select($(id));
			});
		} else if (chk.inputElement.type == 'radio'){
			Site.radios.each(function(other){
				if (other == chk) return;
				Site.deselect(other);
			});
		}
	},	
	
	all: function(){
		Site.chks.each(function(chk){
			if (!chk.hasClass('lib_check') && !chk.hasClass('exclude')) Site.select(chk);
		});
	},
	
	none: function(){
		Site.chks.each(function(chk){
			Site.deselect(chk);
		});
	},
	
	deselect: function(chk){
		chk.inputElement.checked = false;
		if (Site.trs[chk.index]) Site.trs[chk.index].removeClass('selected');
		if (!chk.hasClass('lib_check')) {
			Site.fx[chk.index].start({
				'color': '#000'
			});
		}
		chk.removeClass('selected');
		
		if (chk.deps && !chk.inputElement.disabled){
			Site.chks.each(function(other){
				if (other == chk) return;
				if (other.deps && other.deps.contains(chk.id) && other.hasClass('selected')) Site.deselect(other);
			});
		}
	},	
	
	isOn: function(lib){ 
	  $$('#download_'+lib+' div.check').getElement('input').some(function(el){ 
	  	if (el.checked) Site.lib_active[lib] = true;
		});
	},

	disableLib: function(lib, useFx){
		Site.deselect($('include_'+lib));
		if (useFx) Site.lib_sliders[lib].slideOut();
		else Site.lib_sliders[lib].hide();
		$('slider_'+lib).getElements('div.check input').each(function(input){
			input.set('disabled', true);
		});
	},

	enableLib: function(lib, useFx){
		Site.select($('include_'+lib));
		if (useFx) Site.lib_sliders[lib].slideIn();
		else Site.lib_sliders[lib].show();
		$('slider_'+lib).getElements('div.check input').each(function(input){
			input.set('disabled', false);
		});
		Site.chks.each(function(chk){
			if (chk.inputElement && chk.inputElement.checked) {
				Site.select(chk);
			}
		});
	},
	
	enableChk: function(chk, tr){
		chk.inputElement.set('disabled', false);
		tr.setStyle('opacity', 1);
	},

	disableChk: function(chk, tr){
		chk.inputElement.set('disabled', true);
		tr.setStyle('opacity', 0.2);
	},
	
	processparse: function(){
		Site.trs.each(function(tr, i){
			var chk = tr.getElement('div.check');
			if (!chk) return;
			try {
				chk.index = i;
				chk.inputElement = chk.getElement('input');
				if (Site.trs[chk.index] && chk.hasClass('selected')) Site.trs[chk.index].addClass('selected');								
				tr.addEvent('click', function(e){
					if (!chk.hasClass('selected')) {
						Site.select(chk);
						Site.enableChk(chk, tr);
					} else if (tr.hasClass('check')) {
						if (e.control||e.meta) Site.disableChk(chk, tr);
						Site.deselect(chk);
					}
				});
			}catch(e){
			}

		});
		
	},

	parse: function(){
		Site.lib_sliders = {};
		$$('.lib_check').each(function(chk){
			var lib = chk.id.split("_")[1];
			chk.inputElement = chk.getElement('input');			

			Site.isOn(lib);
			if (Site.lib_active[lib]) $('include_' + lib).inputElement.checked = true;

			Site.lib_sliders[lib] = new Fx.Slide($('slider_'+lib), {duration: 500, transition: Fx.Transitions.Quad.easeOut, wait: false}).show();
			if (!$('include_' + lib).inputElement.checked) {
				Site.disableLib(lib, false);
			} else {
				Site.enableLib(lib, false);
			}
			chk.getParent().addEvent('click', function(){
				if (!chk.hasClass('selected')) {
					Site.enableLib(lib, true);
				} else {
					Site.disableLib(lib, true);
					$$('#download_'+lib+' div.check').getElement('input').each(function(chk){ Site.select(chk); });
				}
			});
		});
	
		Site.trs.each(function(tr, i){
			Site.fx[i] = new Fx.Morph(tr, {wait: false, duration: 300});

			var chk = tr.getElement('div.check');
			if (!chk) return;
			try {
				chk.index = i;
				var dp = chk.getProperty('deps');
				if (dp) chk.deps = dp.split(',');
				tr.addEvent('click', function(e){
					if (!chk.hasClass('selected')) {
						Site.select(chk);
						Site.enableChk(chk, tr);
					} else if (tr.hasClass('check')) {
						if (e.control||e.meta) Site.disableChk(chk, tr);
						Site.deselect(chk);
					}
				});
			}catch(e){
			}

		});
	}
	
};

window.addEvent('domready', function(){
	Site.startMooForm();
});

