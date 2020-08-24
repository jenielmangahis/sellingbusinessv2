jQuery( document ).ready(function( $ ){
    function advads_selling_toggle_details_section(){
	
	// get active sections
	var active = $('.advanced-ads-selling-setup-ad-type:checked').val();
	
	// choose active sections
	if( 'image' === active ){
	    $('#advanced-ads-selling-setup-ad-details-upload-label, #advanced-ads-selling-setup-ad-details-image-upload, #advanced-ads-selling-setup-ad-details-url, #advanced-ads-selling-setup-ad-details-url-input').show();
	    $('#advanced-ads-selling-setup-ad-details-html-label, #advanced-ads-selling-setup-ad-details-html').hide();
	} else { // html is fallback
	    $('#advanced-ads-selling-setup-ad-details-html-label, #advanced-ads-selling-setup-ad-details-html').show();
	    $('#advanced-ads-selling-setup-ad-details-upload-label, #advanced-ads-selling-setup-ad-details-image-upload, #advanced-ads-selling-setup-ad-details-url, #advanced-ads-selling-setup-ad-details-url-input').hide();
	}
	
    }
    advads_selling_toggle_details_section();
    // trigger, when selection is changed
    $('.advanced-ads-selling-setup-ad-type').click( advads_selling_toggle_details_section );
    // submit frontend ad form
    $('.advanced-ads-selling-setup-ad-details-submit').click( function( e ){
		var self = this;
		if($('#advanced-ads-selling-setup-ad-details-upload-input').is(':visible')){

			//document.forms[0].addEventListener('submit', function( evt ) {
		    	var file = document.getElementById('advanced-ads-selling-setup-ad-details-upload-input').files[0];
			    if(file && file.size > 1048576) { // 1 MB (this size is in bytes)
			         //Prevent default and display error
			        e.preventDefault();
			        alert('File Size too large');
			        return false;
			    }
			}
    	});
        // send request
        /*$.ajax({
            type: 'POST',
            url: advads_selling_ajax_url,
            //dataType: 'json',
            data: {
                action: 'advads_selling_ad_setup',
                formdata: $( this ).parents('form').serialize()
            },

            success:function(data, textStatus, XMLHttpRequest){
                if( ! data ) {
		    $('.ad-submit-success').show();
		    console.log( self );
		    $( self ).parents('form').remove();
		} else {
		    $('.ad-submit-error').text( data ).show();
		}
            },

            error: function(MLHttpRequest, textStatus, errorThrown){
                console.log(errorThrown);
                $('.ad-submit-error').show();
            }

        });*/
    /*});*/
    // update prices dynamically
    if( 'object' == typeof AdvancedAdSelling ){
	var price_array = AdvancedAdSelling.product_prices;
    }
    jQuery('#advads_selling_option_ad_price input').on('change', advads_selling_update_price );
    function advads_selling_update_price(){
	    // when ad expiry is given
	    var total_price = 0;
	    if(  jQuery('#advads_selling_option_ad_price input:checked').length ){
		var price_index = jQuery('#advads_selling_option_ad_price input').length - jQuery( this ).parents('li').index() - 1; // needed to be reversed
		total_price = parseInt( price_array[price_index]['price'] );		
	    }
	    
	    total_price = total_price.toFixed(2);
		total_price = total_price.toString();
		total_price = total_price.replace('.', AdvancedAdSelling.woocommerce_price_decimal_sep);
	    
	    // write price into frontend
	    var selector = jQuery('.price .woocommerce-ad-price');
	    var noRemove = selector.find('.woocommerce-Price-currencySymbol');
	    jQuery('.price .woocommerce-ad-price').html(noRemove);
	    jQuery('.price .woocommerce-ad-price .woocommerce-Price-currencySymbol').after(total_price);
    }
});