(function ( $ ) {
	"use strict";

	$(function () {


		$('#realteo-fafe-fields-editor').sortable({
			items: '.form_item',
			handle: '.handle',
			cursor: 'move',
			containment: 'parent',
			placeholder: 'my-placeholder',
			/*stop: function(event, ui) {
		        $(".form_item").each(function(i, el){
		        	
		            $(this).find('input').attr('name').replace(/\d+/, $(el).index())
		            
		        });
		    }*/
		});	


		$('.field-options-custom tbody').sortable();


		$('#realteo-fafe-forms-editor').sortable({
			items: '.form_item',
			handle: '.handle',
			cursor: 'move',
			containment: 'parent',
			placeholder: 'my-placeholder',
			stop: function(event, ui) {
		        $(".form_item").each(function(i, el){
		            $(this).find('.priority_field').val($(el).index());
		        });
		    },
		    receive: function (e, ui) {
		        ui.sender.data('copied', true);
		    }
		});

		function randomIntFromInterval(min,max){
		    return Math.floor(Math.random()*(max-min+1)+min);
		}
		$( ".form-editor-available-elements-container" ).sortable({
			items: '.form_item',
			handle: '.handle',
			connectWith: '.form-editor-container',
			helper: function (e, li) {
				
				if(li.hasClass('form_item_header')) {
					var copy = li.clone();
					var formRowCount = $('#realteo-fafe-forms-editor .form_item').length+25;
					$('.name-container input',copy).val('header'+randomIntFromInterval(20,990));
					$('input',copy).attr('name').replace(/^(\[)\d+(\].+)$/, '$1' + formRowCount + '$2');
					copy.find('input,select').each(function() {
				        var $this = $(this);
				        console.log($this);
				        $this.attr('name', $this.attr('name').replace(/\[(\d+)\]/, '[' + formRowCount + ']'));
				        
				    });
				    formRowCount++;
					this.copyHelper = copy.insertAfter(li);
					 $(this).data('copied', false);
			        return li.clone();
				} else {
					return li.data('copied', true);
				}
		    },
			stop: function(event, ui) {
				var copied = $(this).data('copied');

		        if (!copied) {
		            this.copyHelper.remove();
		        }

		        this.copyHelper = null;
				$(".form_item").each(function(i, el){
					var i = $(el).index();
					
					if( $(this).find('.priority_field').lenght > 0 ) {
						$(this).find('.priority_field').attr('name').replace(/(\[\d\])/, '[' + $(el).index() + ']'); 	
					}
		        
		    	});
		    }
		});

		$(".realteo-forms-builder").on('click', '#realteo-show-names', function(){
			$('.name-container').show();
		});

		$('.form-editor-container').on( 'click', '.element_title', function() {
			$(this).next().slideToggle();
		});

	 
	    $(".remove_item").click(function(event) {
 			event.preventDefault();
 			if (window.confirm("Are you sure?")) {
	 			$(this).parent().fadeOut(300, function() { $(this).remove(); });
	 		}
 		});
 		$(".remove_row").click(function(event) {
 			event.preventDefault();
 			if (window.confirm("Are you sure?")) {
	 			$(this).parent().fadeOut(300, function() { $(this).remove(); });
	 		}
 		});

	    
		/*fields editor*/
		$('#realteo-fafe-fields-editor, #realteo-fafe-forms-editor')
		.on( 'init', function() {
			$('.step-error-too-many').hide();
			$('.step-error-exceed').hide();
			$(this).find( '.field-type-selector' ).change();
			$(this).find( '.field-type select' ).change();
			$(this).find( '.field-edit-class-select' ).change();
			$(this).find( '.field-options-data-source-choose' ).change();
		})
		.on( 'change', '.field-type select', function() {
			$(this).parent().parent().find('.field-options').hide();

			if ( 'select' === $(this).val() || 'select_multiple' === $(this).val() || 'checkbox' === $(this).val() || 'multicheck_split' === $(this).val()) {
				$(this).parent().parent().find('.field-options').show();
			} 

		})
		.on( 'change', '.field-options-data-source-choose', function() {
			if ( 'predefined' === $(this).val() ) {
				$(this).parent().find('.field-options-predefined').show();
				$(this).parent().find('.field-options-custom').hide();
			}
			if ( 'custom' === $(this).val() ) {
				$(this).parent().find('.field-options-predefined').hide().val("");
				$(this).parent().find('.field-options-custom').show();
			}
			if ( '' === $(this).val() ) {
				$(this).parent().find('.field-options-predefined').hide().val("");
				$(this).parent().find('.field-options-custom').hide();
			}
		})
		.on( 'change', '.field-edit-class-select', function() {
			if ( 'col-md-12' === $(this).val() ) {
				$(this).parent().parent().find('.open_row-container').hide().find('input').prop('checked', true);
				$(this).parent().parent().find('.close_row-container').hide().find('input').prop('checked', true);
				
			} else {
				$(this).parent().parent().find('.open_row-container').show();
				$(this).parent().parent().find('.close_row-container').show();
				
			}
			if ( '' === $(this).val() ) {
				$(this).parent().parent().find('.open_row-container').hide().find('input').prop('checked', false);
				$(this).parent().parent().find('.close_row-container').hide().find('input').prop('checked', false);
			}
			if ( 'custom' === $(this).val() ) {
				$(this).parent().find('.field-options-predefined').hide().val("");
				$(this).parent().find('.field-options-custom').show();
			}
		})
		.on( 'click', '.remove-row', function(e){
			e.preventDefault();
			
 			if (window.confirm("Are you sure?")) {
	 			$(this).parent().parent().fadeOut(300, function() { $(this).remove(); });
	 		}
		})
		.on( 'click', '.add-new-option-table', function(e){
			e.preventDefault();
			var $tbody = $(this).closest('table').find('tbody');
			var row    = $tbody.data( 'field' );
			row = row.replace( /\[-1\]/g, "[" + $tbody.find('tr').size() + "]");
			$tbody.append( row );
		})
		.on('change', '.step-container', function() {
			var form = $(this).parent().parent();
			var max = form.find('.max-container input').val();
			var min = form.find('.min-container input').val();
			var step = $(this).find('input').val();
			$('.step-error-too-many').hide();
			$('.step-error-exceed').hide();
			if(step > (max-min)){
				form.find('.step-error-exceed').show();
			}
			var offset = 0;
			var len = (Math.abs(max - min)  + ((offset || 0) * 2)) / (step || 1) + 1;
			console.log(len);
			if(len > 30){
				form.find('.step-error-too-many').show();
			}
		})
		.on('change', '.min-container', function() {
			var form = $(this).parent().parent();
			var max = form.find('.max-container input').val();
			var min = form.find('.min-container input').val();
			var step = $(this).find('input').val();
			$('.step-error-too-many').hide();
			$('.step-error-exceed').hide();
			if(step > (max-min)){
				form.find('.step-error-exceed').show();
			}
			var offset = 0;
			var len = (Math.abs(max - min)  + ((offset || 0) * 2)) / (step || 1) + 1;
			console.log(len);
			if(len > 30){
				form.find('.step-error-too-many').show();
			}
		})
		.on('change', '.max-container', function() {
			var form = $(this).parent().parent();
			var max = form.find('.max-container input').val();
			var min = form.find('.min-container input').val();
			var step = $(this).find('input').val();
			$('.step-error-too-many').hide();
			$('.step-error-exceed').hide();
			if(step > (max-min)){
				form.find('.step-error-exceed').show();
			}
			var offset = 0;
			var len = (Math.abs(max - min)  + ((offset || 0) * 2)) / (step || 1) + 1;
			console.log(len);
			if(len > 30){
				form.find('.step-error-too-many').show();
			}
		})
		.on('change', '.field-type-selector', function() {
		  var form = $(this).parent().parent();
		  var type = $(this).val();
		
		  switch (type) { 
				case 'select': 
				case 'multicheck_split': 
				case 'multi-select': 
					form.find('.options-container').show();
					form.find('.multi-container').show();
					form.find('.max-container').hide();
					form.find('.min-container').hide();
					form.find('.step-container').hide();
					form.find('.unit-container').hide();
					form.find('.taxonomy-container').hide();
					break;
				case 'select-taxonomy': 
				case 'term-select': 
					form.find('.multi-container').show();
					form.find('.taxonomy-container').show();
					form.find('.options-container').hide();
					form.find('.max-container').hide();
					form.find('.min-container').hide();
					form.find('.step-container').hide();
					form.find('.unit-container').hide();
					break;
				case 'input-select': 
				case 'slider': 
				case 'double-input': 
					form.find('.options-container').hide();
					form.find('.multi-container').hide();
					form.find('.max-container').show();
					form.find('.min-container').show();
					form.find('.step-container').show();
					form.find('.unit-container').show();
					break;		
				case 'multi-checkbox': 
				case 'multi-checkbox-row': 
					form.find('.options-container').show();
					form.find('.taxonomy-container').show();
					form.find('.multi-container').hide();
					form.find('.max-container').hide();
					form.find('.min-container').hide();
					form.find('.step-container').hide();
					form.find('.unit-container').hide();
					break;
				case 'header': 
					form.find('.max-container').hide();
					form.find('.min-container').hide();
					form.find('.multi-container').hide();
					form.find('.step-container').hide();
					form.find('.unit-container').hide();
					form.find('.options-container').hide();
					form.find('.taxonomy-container').hide();
					break;
				default:
					form.find('.max-container').hide();
					form.find('.min-container').hide();
					form.find('.multi-container').hide();
					form.find('.step-container').hide();
					form.find('.unit-container').show();
					form.find('.options-container').hide();
					form.find('.taxonomy-container').hide();
			}

		  // Does some stuff and logs the event to the console
		});
		
		$('#realteo-fafe-fields-editor').trigger( 'init' );
		$('#realteo-fafe-forms-editor').trigger( 'init' );


		$('.realteo-forms-builder-top').on('click', '.add_new_item', function(e) {
			e.preventDefault();
			var name;
		    do {
		        name=prompt("Please enter field name");
		    }
			while(name.length < 2);
			var clone    = $('#realteo-fafe-fields-editor').data( 'clone' );
			var id = string_to_slug(name);
			var index = $('.form_item').size()+1; 
			clone = clone.replace( /\[-2\]/g, "[" + index + "]").replace( /clone/g, name);
			$('#realteo-fafe-fields-editor').append(clone);
			$('#realteo-fafe-fields-editor .form_item:last-child .edit-form-field').toggle().find('.field-id input').val('_'+id);
		});


		$('.realteo-form-editor table')
		.on( 'click', '.add-new-main-option', function(e){
			e.preventDefault();
			var $tbody = $(this).closest('table').find('tbody');
			var row    = $tbody.data( 'field' );
			
			row = row.replace( /\[-1\]/g, "[" + $tbody.find('tr').size() + "]");
			
			$tbody.append( row );
		})
		.on( 'click', '.remove-row', function(e){
			e.preventDefault();
			
 			if (window.confirm("Are you sure?")) {
	 			$(this).parent().parent().fadeOut(300, function() { $(this).remove(); });
	 		}
		})
		
		function string_to_slug (str) {
		    str = str.replace(/^\s+|\s+$/g, ''); // trim
		    str = str.toLowerCase();
		  
		    // remove accents, swap ñ for n, etc
		    var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
		    var to   = "aaaaeeeeiiiioooouuuunc------";
		    for (var i=0, l=from.length ; i<l ; i++) {
		        str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
		    }

		    str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
		        .replace(/\s+/g, '_') // collapse whitespace and replace by -
		        .replace(/-+/g, '_'); // collapse dashes

		    return str;
		}

		
	/*eof*/

	});

}(jQuery));
