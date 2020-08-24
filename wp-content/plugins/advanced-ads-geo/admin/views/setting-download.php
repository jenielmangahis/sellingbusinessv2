<?php if( $last_update ) : 
    ?><p><?php printf( __( 'Last update: %s', 'advanced-ads-geo' ), date_i18n( get_option( 'date_format' ), $last_update ) ); ?></p><?php
endif;
if( true !== $this->license_valid() ) :
    ?><p class="advads-error-message"><?php printf( __( 'The license of the Geo add-on is invalid. <a href="%s">Please purchase or extend a license</a> in order to update geo locations.', 'advanced-ads-geo' ), ADVADS_URL . 'add-ons/geo-targeting/'); ?></p><?php
elseif( $this->is_update_available() ) :
?>
<p style="color: red"><?php _e("In order to use Geo Targeting, please download the geo location database by clicking on the button below.", 'advanced-ads-geo' ); ?></p>
<button type="button" id="download_geolite" class="button-secondary"><?php _e( 'Update geo location database', 'advanced-ads-geo' ); ?> (~66MB)</button>
<span class="advads-loader" id="advads-geo-loader" style="display: none;"></span>
<p class="advads-error-message hidden" id="advads-geo-upload-error"><?php _e( 'Database update failed', 'advanced-ads-geo' ); ?></p>
<p class="advads-success-message hidden" id="advads-geo-upload-success"><?php _e( 'Database update successful', 'advanced-ads-geo' ); ?></p>
<script>
    jQuery('#download_geolite').on('click', function (e) {

	var el = jQuery(this);
	el.blur();
	el.attr('disabled', 'disabled');
	
	var data = {
	    action: 'advads_download_geolite_database',
	};
	
	jQuery('#advads-geo-loader').show();

	jQuery.post( ajaxurl, data, function (r) {
	    try
	    {
		if (!r) {
		    jQuery('#advads-geo-upload-error').show();
		    jQuery('#advads-geo-upload-success').hide();
		    return;
		} else {
		    jQuery('#advads-geo-upload-error').hide();
		    jQuery('#advads-geo-upload-success').show();
		}
	    }
	    catch (ex)
	    {
		    jQuery('#advads-geo-upload-error').html( ex.message ).show();
	    }
	})
	.done(function() {
	    jQuery('#advads-geo-loader').hide();
	})
	.fail(function (jqXHR, errormessage, errorThrown) {
	    jQuery('#advads-geo-upload-error').html( errormessage ).show();
	})
	
    });
</script>
<?php else :
    ?><p><?php printf(__( 'Next possible update on %s.', 'advanced-ads-geo' ), date_i18n( get_option( 'date_format' ), $next_update )); ?></p>
    <p class="description"><?php _e( 'The databases are updated on the first Tuesday (midnight, GMT) of each month.', 'advanced-ads-geo' ); ?></p>
	<?php
endif;