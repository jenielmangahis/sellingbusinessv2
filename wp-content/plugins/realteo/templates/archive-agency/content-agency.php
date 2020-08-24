<!-- Agency -->
<div class="agent agent-page agency">

	<a href="<?php the_permalink( ); ?>" class="agent-avatar">
		<?php 
			if(has_post_thumbnail()){
				the_post_thumbnail(); 	
			} else { ?>
				<img src="<?php echo plugin_dir_url( dirname(__FILE__) ); ?>images/agency_placeholder.png" alt="">
				 
			<?php } ?>
	</a>

	<div class="agent-content">
		<div class="agent-name">
			<h4><a href="<?php the_permalink( ); ?>"><?php the_title(); ?></a></h4>
			<?php if(get_the_agency_address()): ?>
				<span><i class="fa fa-map-marker"></i> <?php the_agency_address(); ?></span>
			<?php endif; ?>
		</div>

		<?php the_excerpt(); ?>
		
		<?php $contact_fields = Realteo_Agency::agency_contact_fields(); 
		$contact_output = '';
		foreach ($contact_fields as $contact => $value) {
			$meta_value = get_post_meta($post->ID, $value['id'],true); 
			if(!empty($meta_value)) {
				if(isset($contact_fields[$contact]['options']) && !empty($contact_fields[$contact]['options'])) {
					$contact_output .= '
						<li class="agency-contact-'.$value['id'].'">'
						. $value['name'].': <span>'.$contact_fields['fields'][$contact]['options'][$meta_value].'</span></li>';	
				} else {
					
					$contact_output .= '
						<li class="agency-contact-'.$value['id'].'">';
							if(isset($value['icon_class']) && !empty($value['icon_class'])) :
								$contact_output .= '<i class="'.$value['icon_class'].'"></i>';
							else:
								$contact_output .= $value['name'];
							endif;
							$contact_output .=  ' <span>'.$meta_value.'</span>
						</li>';
				}	
			}
		}
		if(!empty($contact_output)) : ?>
			<ul class="agent-contact-details">
				<?php echo $contact_output; ?>
			</ul>
		<?php endif; ?>


		<?php $social_fields = Realteo_Agency::agency_social_fields(); 
		$social_output = '';
		foreach ($social_fields as $social => $value) {
			$meta_value = get_post_meta($post->ID, $value['id'],true); 
			if(!empty($meta_value)) {
				$social_output .= '<li><a class="'.$value['id'].'" href="'.$meta_value.'"><i class="icon-'.$value['id'].'"></i></a></li>';
			}
		}
		if(!empty($social_output)) : ?>
			<ul class="social-icons">
				<?php echo $social_output; ?>
			</ul>
		<?php endif; ?>
		<div class="clearfix"></div>
	</div>

</div>