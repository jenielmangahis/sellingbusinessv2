<?php 

	$option9   = unserialize(get_option('sfsi_section9_options',false));

?>
<div class="tab9">
		    
    <ul class="sfsi_icn_listing8">

    	<!--**********************  Show them via a widget section **************************************-->
		
			<?php @include(SFSI_DOCROOT.'/views/subviews/que3/sfsi_que3_place_via_widget.php'); ?>
        
    	<!--**********************  Define the location on the page **************************************-->

			<?php @include(SFSI_DOCROOT.'/views/subviews/que3/sfsi_que3_place_via_float.php'); ?>
        
        <!--**********************  Place via shortcode *******************************************-->
			
			<?php 	@include(SFSI_DOCROOT.'/views/subviews/que3/sfsi_que3_place_via_shortcode.php'); ?>
			
        
        <!--**********************  Show them after post****************************************-->
			
			<?php @include(SFSI_DOCROOT.'/views/subviews/que3/sfsi_que3_place_via_after_posts.php'); ?>

		<!--**********************  Show pinterest over image hover  post****************************************-->

			<li class="sfsi_show_via_onhover disabled_checkbox">
				
				<div class="radio_section tb_4_ck">
					<span class="checkbox" style="background-position:0px 0px!important;width:31px"></span>
					<input name="" type="checkbox" disable value="" class="hide" style="display:none;"  /></div>
				
				<div class="sfsi_right_info">
					
	                <p style="display:block">
						<span class="sfsi_toglepstpgspn" style="width:50%;display:inline-block;float:left;">Show a Pinterest icon over images on mouse-over </span> <span><a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=pinterest_icon_mouse_over&utm_medium=link" target="_blank" style="font-weight:800">Premium feature</a></span>
					</p>
					
				</div>
			</li>

	</ul>
	
	
	<p class="sfsi_premium_feature_note">
		In the Premium Plugin you can also <b>exclude icons</b> from showing on certain pages. <a target="_blank" href="https://www.ultimatelysocial.com/usm-premium/"><b>See all features</b></a>
	</p>


	<?php sfsi_ask_for_help(9); ?>

    
	<!-- SAVE BUTTON SECTION   --> 
	<div class="save_button">

       <img src="<?php echo SFSI_PLUGURL ?>images/ajax-loader.gif" class="loader-img" />

       <?php  $nonce = wp_create_nonce("update_step9"); ?>

        <a href="javascript:;" id="sfsi_save9" title="Save" data-nonce="<?php echo $nonce;?>">Save</a>

  	</div>
    <!-- END SAVE BUTTON SECTION   -->
	
    <a class="sfsiColbtn closeSec" href="javascript:;">Collapse area
    </a>

	<label class="closeSec"></label>
    
	<!-- ERROR AND SUCCESS MESSAGE AREA-->
	<p class="red_txt errorMsg" style="display:none"> </p>
	<p class="green_txt sucMsg" style="display:none"> </p>
	<div class="clear"></div>
	
</div>

<?php
function sfsi_premium_isSeletcted($givenVal, $value)
{
	if($givenVal == $value)
		return 'selected="true"';
	else
		return '';
}
?>