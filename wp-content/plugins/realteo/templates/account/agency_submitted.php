
<div class="col-md-8">
	<div class="row">
		<?php 
		// Successful

			switch ( get_post_status( $data->id ) ) {
				case 'publish' :
					echo '<div class="notification closeable success">' . __( 'Your agency has been published.', 'realteo' ) . ' <a href="' . get_permalink( $data->id ) . '">' . __( 'View &rarr;', 'realteo' ) . '</a>' . '</div>';
				break;				
		
				case 'pending' :
				case 'draft' :
					echo '<div class="notification closeable warning">' . __( 'Your agency has been saved and is awaiting admin approval', 'realteo' ). '</div>';
				break;
				default :
					echo '<div class="notification closeable warning">' . __( 'Your changes have been saved.', 'realteo' ) . '</div>';
				break;
		} ?>
	</div>
</div>