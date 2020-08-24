<?php
function help_page(){
	global $plugin_url;
 ?>
	

	<div class="wrap cwa">
	 
	    <div id="icon-themes" class="icon32"><br /></div>
		<ul class="tabs">
			<li>
				<h3><a href="<?php echo admin_url();?>admin.php?page=custom_widget_area"><?php _e( 'Custom widget area', 'wp-custom-widget-area' ); ?></a></h3>
			</li>
			<li>
				<h3><a href="<?php echo admin_url();?>admin.php?page=custom_menu_location"><?php _e( 'Custom menu location', 'wp-custom-widget-area' ); ?></a></h3>
			</li>
			<li class="active">
				<h3><a href="<?php echo admin_url();?>admin.php?page=cwa_help"><?php _e( 'Help', 'wp-custom-widget-area' ); ?></a></h3>
			</li>
		</ul>
	    

		<div class="welcome-panel custom-wp help-page">
			<div id="tab-container" class="tab-container">
			  <ul class='etabs'>
			    <li class='tab'><a href="#custom-widget-area"><span class="wp-menu-image dashicons-before dashicons-editor-help"></span>Custom widget area</a></li>
			    <li class='tab'><a href="#custom-menu-location"><span class="wp-menu-image dashicons-before dashicons-editor-help"></span>Custom menu location</a></li>
			  </ul>
			  <div class="tab-content">
				  <div id="custom-widget-area">
				    <div class="how-to">
						<h2>How to use?</h2>
						<p>
							<ol class="list">
								<li><h4>Create a new Widget area.</h4>
									<br>
									<img src="<?php echo $plugin_url;?>/admin/img/help/cwa_2.png" >
								</li>
								<li><h4>Click on the "get code" link.</h4>
									<br>
									<img src="<?php echo $plugin_url;?>/admin/img/help/cwa_3.png" >
								</li>
								<li><h4>Copy the code</h4>
									<br>
									<img src="<?php echo $plugin_url;?>/admin/img/help/cwa_4.png" >
								</li>
								<li><h4>and Paste it in a wordpress theme where you want to display it.</h4>
									<br>
									<img src="<?php echo $plugin_url;?>/admin/img/help/cwa_widget_codebase.png" >
								</li>
								<li><h4>Go to Dashboard Appearance > widgets and add widgets to widget area.</h4>
									<br>
									<img src="<?php echo $plugin_url;?>/admin/img/help/cwa_manage_widget.png" >
								</li>
								<li><h4>Then reload your site.</h4>
								</li>
							</ol>
						</p>
						<br/>
						<h2 style="margin-top: 0;">How to Use it in page or post content?</h2>
						<p>
							<ol class="list">
								<li><h4>Click on the "get shortcode" link form widget area table below.</h4>
									<br>
									<img src="<?php echo $plugin_url;?>/admin/img/help/cwa_4.png" >
								</li>
								<li><h4>Copy the shortcode and Paste it in a post or page editor where you want to display it.</h4>
									<br>
									<img src="<?php echo $plugin_url;?>/admin/img/help/cwa_widget_shortcode_admin.png" >
								</li>
								<li><h4>Reload your site.</h4>
							</ol>
						</p>	
						<br/>
						<h2 style="margin-top: 0;">How to customize widget style?</h2>
						<p>
							<ol class="list">
								<li><h4>Click on the advance link while creating new widget area and add widget class.</h4>
									<br>
									<img src="<?php echo $plugin_url;?>/admin/img/help/cwa_1_advance.png" >
								</li>
								<li><h4>Then add custom css targeting your widget area class. i.e. </h4><br>
								<code>
									.mynewwidgetareaclass a{
										color: red;
									} 
								</code><br>
								at the bottom of your style.css 
								where ".mynewwidgetareaclass" is your widget area class.
								</li>
							</ol>
							<br>

						</p>
						<h2 style="margin-top: 0;">How to add custom wrapper tag? [New]</h2>	
						<p>
							<ol class="list">
								<li><h4>Click on custom link. </h4>
									<br>
									<img src="<?php echo $plugin_url;?>/admin/img/help/cwa_widget_advanced_1.png">
								</li>
								<li><h4>Enter a valid Json object array </h4>
									<p>
										i.e:
										<code>
											[{
												"tag": "div",
												"id" : "outer-widget",
												"class" : "outer-class"
											},
											{
												"tag": "div",
												"id" : "inner-widget",
												"class" : "inner-class"
											}]
										</code><br>
									</p>

									<img src="<?php echo $plugin_url;?>/admin/img/help/cwa_widget_advanced_2.png">
								</li>
								
								<li>
									<h4>Then submit a changes by clicking update button. </h4>
									
								</li>
							</ol>
						</p>

						<h2 style="margin-top: 0;">How to Update existing widget area? </h2>	
						<p>
							<ol class="list">
								<li><h4>Click on the edit link. </h4>
									<br>
									<img src="<?php echo $plugin_url;?>/admin/img/help/cwa_1_edit.png" >
								</li>
								<li>
									<h4>Edit widget area field values. </h4><br>
								</li>
								<li>
									<h4>Then submit a changes by clicking update button. </h4>
									<br>
									<img src="<?php echo $plugin_url;?>/admin/img/help/cwa_1_edit_save.png" >
								</li>
							</ol>
						</p>

					</div>
				  </div>
				  <div id="custom-menu-location">
				    <div class="how-to">
						<h3>How to use?</h3>
						<p>
							<ol class="list">
								<li><h4>Create a new Menu Location.</h4>
									<br>
									<img src="<?php echo $plugin_url;?>/admin/img/help/cwa_menu_1.png" ></li>
								<li><h4>Click on the "get code" link from table below.</h4>
									<br>
									<img src="<?php echo $plugin_url;?>/admin/img/help/cwa_menu_3.png" ></li>
								<li><h4>Copy the code and Paste it in a wordpress theme where you want to display it.</h4>
									<br>
									<img src="<?php echo $plugin_url;?>/admin/img/help/cwa_menu_codebase.png" ></li>
								<li><h4>assign menu to the location.</h4>
									<br>
									<img src="<?php echo $plugin_url;?>/admin/img/help/cwa_menu_admin.png" ></li>
								<li><h4>Reload the site.</h4></li>
							</ol>
						</p>
						<br/>
						<h3 style="margin-top: 0;">How to Use it in page or post content?</h3>
						<p>
							<ol class="list">
								<li><h4>Click on the "get shortcode" link form table below.</h4>
									<br>
									<img src="<?php echo $plugin_url;?>/admin/img/help/cwa_menu_4.png" ></li>
								<li><h4>Copy the shortcode and Paste it in a post or page editor where you want to display it.</h4>
									<br>
									<img src="<?php echo $plugin_url;?>/admin/img/help/cwa_menu_shortcode_admin.png" ></li>
							</ol>
						</p>	
						<br/>
						<h3 style="margin-top: 0;">How to customize menu style?</h3>
						<p>
							<ol class="list">
								<li><h4>Pass the extra arguments while calling function</h4><br>
									i.e.<br>
									<code>
										wp_nav_menu( array( 'theme_location'	=> 'footer-location', 'menu_class'      => 'Cwa-menu', [arguments] => ['values']...	) );
									</code> 
									<br>
									<a href="https://codex.wordpress.org/Function_Reference/wp_nav_menu" target="_blank" > Cick here </a> to know more about available Parameters.
									<br>
									<pre style="word-wrap: break-word;">[Note: for shortcode pass arguments like <code>[menu theme_location='footer-location' 'menu_class'='Cwa-menu' [arguments]=[values]...]</code></pre>
								</li>
								<li><h4>Make sure you have passed custom menu class options i.e. 'menu_class' like in above code.</h4>
									
								<li><h4>Add custom css targeting your menu_class or container_class etc. i.e.</h4> <br>
								<code>
									.Cwa-menu a{
										color: red;
									} 
								</code><br>
								at the bottom of your style.css.
								</li>
							</ol>
						</p>	
					</div>
				  </div>
			  </div>
			</div>
		</div>
	</div>

<?php 
}
?>