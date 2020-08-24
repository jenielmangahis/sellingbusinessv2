<?php $user_packages = realteo_user_packages( get_current_user_id() ); ?>
<div class="col-md-8">
<?php if ( $user_packages ) : ?>
	<table class="manage-table responsive-table packages-table">
	<tr>
		<th><i class="fa fa-file-text"></i> <?php esc_html_e('Package name','realteo'); ?></th>
		<th><i class="fa fa-pencli"></i> <?php esc_html_e('Description','realteo'); ?></th>
		<th></th>
	</tr>
		<?php 
		foreach ( $user_packages as $key => $package ) :
			$package = realteo_get_package( $package );
			?>
			<tr>
				<td class="title-container"><span><?php echo $package->get_title(); ?></span></td>
				<td class="expire-date">
					<?php
					if ( $package->get_limit() ) {
						printf( _n( 'You have %1$s business posted out of %2$d', 'You have %1$s business posted out of %2$d', $package->get_count(), 'realteo' ), $package->get_count(), $package->get_limit() );
					} else {
						printf( _n( 'You have %s business posted', 'You have %s business posted', $package->get_count(), 'realteo' ), $package->get_count() );
					}

					if ( $package->get_duration() ) {
						printf( ', ' . _n( 'listed for %s day', 'listed for %s days', $package->get_duration(), 'realteo' ), $package->get_duration() );
					}

					$checked = 0; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>

<?php else: ?>
	<div class="notification notice margin-bottom-20">
			<p><?php echo sprintf( 
				__( 'You haven\'t bought any packages yet, you can do it while <a href="%s">adding a business</a>.', 'realteo' ), 
			get_permalink( realteo_get_option( 'submit_property_page' ) ),
			get_permalink( wc_get_page_id( 'shop' ) ) );	 ?></p>
		</div><a href="<?php echo get_permalink( realteo_get_option( 'submit_property_page' ) ); ?>" class="margin-top-20 button"> <?php esc_html_e('Submit New Business','realteo'); ?></a>
<?php endif; ?>
	
	<!-- <a href="<?php echo get_permalink( wc_get_page_id( 'shop' ) ) ; ?>" class="margin-top-20 button"><i class="sl sl-icon-basket"></i> <?php esc_html_e('Purchase New Package','realteo'); ?></a> -->

</div>

</div>