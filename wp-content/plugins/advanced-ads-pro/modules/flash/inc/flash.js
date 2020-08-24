jQuery(document).ready(function($){

	$('body').on('click', '.advads_flash_upload', function(e) {

		e.preventDefault();

		var button = $(this);

		// If the media frame already exists, reopen it.
		if ( file_frame ) {
			//file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
			file_frame.open();
			return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media( {
			frame: 'post',
			state: 'insert',
			title: button.data( 'uploader-title' ),
			button: {
				text: button.data( 'uploader-button-text' )
			},
			multiple: $( this ).data( 'multiple' ) == '0' ? false : true  // Set to true to allow multiple files to be selected
		} );

		/*file_frame.on( 'menu:render:default', function( view ) {
			// Store our views in an object.
			var views = {};

			// Unset default menu items
			view.unset( 'library-separator' );
			view.unset( 'gallery' );
			view.unset( 'featured-image' );
			view.unset( 'embed' );

			// Initialize the views in our view object.
			view.set( views );
		} );*/

		// When an image is selected, run a callback.
		file_frame.on( 'insert', function() {

			var selection = file_frame.state().get('selection');
			selection.each( function( attachment, index ) {
				attachment = attachment.toJSON();
				if ( 0 === index ) {
					// place first attachment in field
					$( '#advads-pro-flash-url' ).val( attachment.url );
				}
			});
		});

		// Finally, open the modal
		file_frame.open();
	});

	// WP 3.5+ uploader
	var file_frame;
	window.formfield = '';
});