// source --> https://beta.sellingbusinessesaustralia.com.au/wp-content/themes/sellingbusiness/assets/js/scripts.js 
(function($) { // work as an alias for jQuery()
/**
 * setDropdown focus
 *
 * Copyright (c) 2015 Christian Niño Duller
 * Version: 0.0.1
 * http://www.bonsaimedia.com.au
 */
$.fn.setFocusEvent = function (options) {
  // default settings:
  var defaults = {
	  	f_in_Evnt: null,
		f_out_Evnt: null
	};
  var settings = $.extend( {}, defaults, options );
    return this.each(function (e) {
        var navTag = false,
			navTimer,
			clkTmr,
			btn = $(this),
			wrpr = btn.parent(),
			mMenu = $(this);
		btn.click(function() {
			if (!navTag) {
				setIn();
			}else{
				setOut();
			}
			clearTimeout(navTimer);
			return false;
		});
		
		function setIn(){
			wrpr.addClass('focus').attr("tabindex", -2).focus();
			navTag = true;
			if (settings.f_in_Evnt instanceof Function) { settings.f_in_Evnt.call(null, btn); }
		};
		function setOut(){
			//return true;
			wrpr.removeAttr("tabindex").removeClass('focus');
            navTag = false;
			if (settings.f_out_Evnt instanceof Function) { settings.f_out_Evnt.call(null, btn); }
		};
		
		wrpr.find('a').click(function () {
			clkTmr = setTimeout(function () {
				clearTimeout(clkTmr);
            	clearTimeout(navTimer);
			},100);
			clearTimeout(navTimer);
		});
		
        wrpr.focusout(function () {
			navTimer = setTimeout(function () {
				if(wrpr.find('input:focus').length){
					wrpr.attr("tabindex", -2);
					clearTimeout(navTimer);
					return;
				}else{
					setOut();
				}
					
			}, 150);
        });
    })
};
	
	$( function() {
    $( ".datepicker, #_contract_start_date, #_contract_end_date" ).datepicker();
  } );

})( jQuery );