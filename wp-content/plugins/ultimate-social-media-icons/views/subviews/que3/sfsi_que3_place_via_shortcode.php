	<?php 

		$sfsi_show_via_shortcode  = isset($option9['sfsi_show_via_shortcode']) && !empty($option9['sfsi_show_via_shortcode']) ? $option9['sfsi_show_via_shortcode'] : "no"; 

		$checked 	 = '';
		$label_style = 'style="display:none;"';

		if("yes" == $sfsi_show_via_shortcode){
			$checked 	 = 'checked="true"';
			$label_style = 'style="display:block;"';
		}

	?>

		<li class="sfsi_show_via_shortcode">
			
			<div class="radio_section tb_4_ck" onclick="checkforinfoslction_checkbox(this);">
				<input name="sfsi_show_via_shortcode" <?php echo $checked; ?> type="checkbox" value="<?php echo $sfsi_show_via_shortcode; ?>" class="styled"  />
			</div>
			
			<div class="sfsi_right_info">
					
				<p>
					<span class="sfsi_toglepstpgspn">Place via shortcode</span><br>                    

					<div class="kckslctn" <?php echo $label_style;?>>
	                	
						<p>Please use the shortcode  <b>[DISPLAY_ULTIMATE_SOCIAL_ICONS]</b> to place the icons anywhere you want.</p> 

						<p>Or, place the icons directly into our (theme) codes by using <b>&lt;?php echo do_shortcode('[DISPLAY_ULTIMATE_SOCIAL_ICONS]'); ?&gt;</b></p>

						<p>Want to show icons <b>vertically</b> or <b>centralize the icons</b> in the shortcode container? Or need <b>different settings for mobile</b>?  Check out the <a href="https://www.ultimatelysocial.com/usm-premium/" target="_blank"><b>Premium Plugin.</b></a></p> 

	                </div>				
				</p>
			</div>			
		</li>