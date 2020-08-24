(function( $ ) {
	'use strict';

	/**
	 * All of the code for your Dashboard-specific JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this
	 * function.
	 *
	 * From here, you're able to define handlers for when the DOM is
	 * ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and so on.
	 *
	 * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
	 * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
	 * be doing this, we should try to minimize doing that in our own work.
	 */
	 $(document).ready(function(){
		 $('#cwa-form').on('submit', function(e){
		 	e.preventDefault();

		 	var flag = validateForm($(this).serializeArray());
		 	if(flag){
		 		//console.log();
		 		var form_array = {};
		 		$(this).serializeArray().map(function(item){
		 			var vl = [];
		 			if(item.value!==null)
		 				form_array[item.name] = item.value;
		 			
		 		});
		 		//if(checkId(form_array.cwa_id)){	
			 		$.post(ajaxurl,{'action': 'add_cwa', 'data': form_array}, function(data){ 
			 			//console.log(data);
						reloadCwaTable();
						showCwaError(data);
						if(!Array.isArray(data))
							resetForm();
					 });	
		 		//}
		 	}
		 	
		 });
		 $('.cwa-form input[name=cancel]').on('click', function(){
		 	resetForm();
		 });
		$('#cwa-form input[name=cwa_name]').on('change', function(){
			var widget_id = $('#cwa-form input[name=cwa_id]'),
			cwaId = $(this).val().replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-').replace(/ /g,"-").toLowerCase();

			if(widget_id.val() === ''){
				widget_id.val(cwaId);
				checkId(widget_id,cwaId, 'widget');
			}
				
		});
		$('#cwa-form input[name=cwa_id]').on('keyup', function(){
			var self = this;
			checkId(self, $(self).val(), 'widget');
		});


		$('#cwa-form #before_after_widget, #cwa-form #before_after_title').on('keyup', function(){
			var self = this;
			if(!isJSON($(this).val()) && $(this).val() != ""){
				$(this).parent().find('.cwa-form-message').html('<label class="cwa-warning" style="position:absolute; top: 40%; max-width: 178px;">Error: Please Enter Valid Json object.</label>');
			}
			else{
				$(this).parent().find('.cwa-form-message').html('');
			}
		});

		$('#cwa-form a.fieldSwitcher').on('click', fieldSwitcher);
		
		$(document).on('click', '.cwa-edit-link', function(e){
			e.preventDefault();
			var id = $(this).data('id');

			$.post(ajaxurl,{'action': 'get_cwa', 'data': {'cwa_id': id}}, function(data){ 
				//console.log(data);
				if(data.message)
					showCwaError(data);
				$('#cwa-form input[name=task]').val('update');
				$('#cwa-form input[name=updateid]').val(data['cwa_id']);
				$('#cwa-form input[name=cwa_id]').prop('disabled', 'disabled');

				var contentElementsArray = ['li','div','span','aside'];
				var headerElementArray = ['h1','h2','h3','h4','h5','h6'];
				for (var k in data){
				    if (typeof data[k] !== 'function') {
				         //alert("Key is " + k + ", value is" + target[k]);
				        if(k == "cwa_widget_wrapper"){
				        	console.log(data[k]);
				        	if(contentElementsArray.indexOf(data[k]) >= 0){
				        		$('#cwa-form [name='+k+']').val(data[k]);
				        		if($('#cwa-form #before_after_widget').siblings('a.fieldSwitcher').text() == "Default"){
				        			$('#cwa-form #before_after_widget').siblings('a.fieldSwitcher').trigger('click');
				        		}
				        	}
				        	else{
				        		$('#cwa-form #before_after_widget').val(data[k]);
				        		if($('#cwa-form #before_after_widget').siblings('a.fieldSwitcher').text() == "Custom"){
				        			$('#cwa-form #before_after_widget').siblings('a.fieldSwitcher').trigger('click');
				        		}
				        	}
				        }else if(k == "cwa_widget_header_wrapper"){
				        	if(headerElementArray.indexOf(data[k]) >= 0){
				        		$('#cwa-form [name='+k+']').val(data[k]);
				        		if($('#cwa-form #before_after_title').siblings('a.fieldSwitcher').text() == "Default"){
				        			$('#cwa-form #before_after_title').siblings('a.fieldSwitcher').trigger('click');
				        		}
				        	}
				        	else{
				        		$('#cwa-form #before_after_title').val(data[k]);
				        		if($('#cwa-form #before_after_title').siblings('a.fieldSwitcher').text() == "Custom"){
				        			$('#cwa-form #before_after_title').siblings('a.fieldSwitcher').trigger('click');
				        		}
				        	}
				        }
				        else{
				        	$('#cwa-form [name='+k+']').val(data[k]);
				        }
				        
				    }
				}
				//console.log(data);
				$('.cwa-form input[type="submit"]' ).val('Update');
				$('.cwa-form input[name="cwa_name"]' ).focus();
			 });
		});

		$(document).on('click', '.cwa-delete-link', function(e){
			e.preventDefault();
			var id = $(this).data('id');

			$.post(ajaxurl,{'action': 'delete_cwa', 'data': {'cwa_id': id}}, function(data){ 
				//console.log(data);
				showCwaError(data);
				
				reloadCwaTable();
			 });
		});
		$(document).on('click', '.cwa-menu-delete-link', function(e){
			e.preventDefault();
			var id = $(this).data('id');

			$.post(ajaxurl,{'action': 'delete_menu', 'data': {'cwa_id': id}}, function(data){ 
				//console.log(data);
				showCwaError(data);
				
				reloadMenuTable();
			 });
		});



		$('#cwa-advance-btn').on('click', function(e){
			e.preventDefault();
			$('#cwa-form .advanced').toggle('show').toggleClass('hide');
			//console.log($('#cwa-form .advanced.hide'));
			if($('#cwa-form .advanced.hide').length===0)
				$(this).text('Basic');
			else
				$(this).text('Advanced');
		});

		/* menu scripts starts*/
		$('#cwa-menu-form').on('submit', function(e){
		 	e.preventDefault();

		 	var flag = validateForm($(this).serializeArray());
		 	if(flag){
		 		//console.log();
		 		var form_array = {};
		 		$(this).serializeArray().map(function(item){
		 			var vl = [];
		 			if(item.value!==null)
		 				form_array[item.name] = item.value;
		 			
		 		});
		 		//if(checkId(form_array.cwa_id)){	
			 		$.post(ajaxurl,{'action': 'add_menu', 'data': form_array}, function(data){ 
			 			//console.log(data);
						reloadMenuTable();
						showCwaError(data);
						
						resetForm();
					 });	
		 		//}
		 	}
		 	
		 });

		$('#cwa-menu-form input[name=cwa_name]').on('change', function(){
			
			var menu_id = $('#cwa-menu-form input[name=cwa_id]'),
			cwaId = $(this).val().replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-').replace(/ /g,"-").toLowerCase();
			//console.log(menu_id.val());
			if(menu_id.val() === ''){
				menu_id.val(cwaId);
				checkId(menu_id,cwaId, 'menu');
			}
				
		});

		$('#cwa-menu-form input[name=cwa_id]').on('keyup', function(){
			//console.log("hey");
			var self = this;
			checkId(self, $(self).val(), 'menu');
		});
		

		$('.more').on('click', function(e){
			e.preventDefault();
			$(this).parent().toggleClass('show-less');
			var text = ($(this).html() == "Less")? "Read more" : "Less";
			$(this).html(text);
		});


		//tab plugin
		$('#tab-container').easytabs();

		runTooltip();
		enableTextareaTab();
	 });

	function validateForm(arr){
		//window.xt = arr;
		return true;
	}
	function checkId(self, cwa_id, type){
		$.post(ajaxurl,{'action': (type=='widget')?'check_cwa_id': 'check_menu_id', 'data': {'cwa_id': cwa_id}}, function(data){ 
				//console.log(data);
				if(data.code === 0){
					$(self).next('.cwa-form-message').html("<label class='cwa-warning' style='padding-left: 5px;'>"+data.message+"</label>");
				}
				else{
					$(self).next('.cwa-form-message').html("<label class='cwa-success' style='padding-left: 5px;'>"+data.message+"</label>");
				}

				//console.log(data);
			 });
	};
	function reloadCwaTable(){
		$.post(ajaxurl,{'action': 'reloadTable'}, function(data){ 

				$('#cwa-table-wrap').html(data);
				runTooltip();
				//console.log(data);
			 });
	}
	function reloadMenuTable(){
		$.post(ajaxurl,{'action': 'reloadMenuTable'}, function(data){ 

				$('#cwa-table-wrap').html(data);
				runTooltip();
				//console.log(data);
			 });
	}
	function showCwaError(obj){
		if(Array.isArray(obj)){
			var message = '';
			for(var i = 0; i<obj.length; i++){
				var type = (obj[i].code === 0)? "cwa-warning" : "cwa-success" ;
				message += obj[i].message + "<br>";
			}
		}else{
			var type = (obj.code === 0)? "cwa-warning" : "cwa-success" ;
			//console.log(obj.code === 0);
			var message = obj.message;
			
		}
		$('.cwa-error').html(message).addClass(type).fadeIn();
			setTimeout(function(){
				$('.cwa-error').fadeOut().html("").removeClass(type);
			}, 5000);
		
	}
	function resetForm(){
		$('.cwa-form input[type="hidden"]' ).val('');
		$('.cwa-form input[disabled]' ).prop('disabled', false);
		$('.cwa-form select' ).children(':first-child').prop('selected', true);
		$('.cwa-form input[type="text"]' ).val('');
		$('.cwa-form textarea' ).val('');
		$('.cwa-form input[type="submit"]' ).val('Create');
		$('.cwa-form  .cwa-form-message' ).empty();

	}
	function runTooltip(){
		$('.tooltip').tooltipster({
		 		contentAsHTML: true,
		 	 	animation: 'fade',
			   	delay: 200,
			   	interactive: true,
			   	//theme: 'tooltipster-default',
			   	trigger: 'click'
		 });
	}
	function enableTextareaTab(){
		$(document).delegate('.cwa-form-row textarea', 'keydown', function(e) {
		  var keyCode = e.keyCode || e.which;

		  if (keyCode == 9) {
		    e.preventDefault();
		    var start = $(this).get(0).selectionStart;
		    var end = $(this).get(0).selectionEnd;

		    // set textarea value to: text before caret + tab + text after caret
		    $(this).val($(this).val().substring(0, start)
		                + "\t"
		                + $(this).val().substring(end));

		    // put caret at right position again
		    $(this).get(0).selectionStart =
		    $(this).get(0).selectionEnd = start + 1;
		  }
		});
	}
	function fieldSwitcher(){
		if($(this).parent().find('textarea').hasClass('hidden')){
			$(this).text("Default");
			$(this).parent().find('.tg').val('custom');

			//console.log($(this).parent().find('.tg').val());
		}else{
			$(this).text("Custom");
			$(this).parent().find('.tg').val('');
		}
		$(this).parent().find('select').toggleClass('hidden');
		$(this).parent().find('textarea').toggleClass('hidden');
		
	}

	function isJSON(data) {
	   var ret = true;
	   try {
	      JSON.parse(data);
	   }catch(e) {
	      ret = false;
	   }
	   return ret;
	}
})( jQuery );
