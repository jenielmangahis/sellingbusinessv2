<?php

/**
 * Provide a dashboard view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.1.5
 *
 * @package    Custom_Widget_Area
 * @subpackage Custom_Widget_Area/admin/partials
 */
/**
* 
*/
class Menu_view
{
	
	public function __construct()
	{
		# code...
	}
	
	public function displayView(){

		
		global $purl;
		?>
		
		<div class="wrap cwa">
 
            <div id="icon-themes" class="icon32"><br /></div>
 			<ul class="tabs">
 				<li>
 					<h3><a href="<?php echo admin_url();?>admin.php?page=custom_widget_area"><?php _e( 'Custom widget area', 'wp-custom-widget-area' ); ?></a></h3>
 				</li>
 				<li class="active">
 					<h3><a href="<?php echo admin_url();?>admin.php?page=custom_menu_location"><?php _e( 'Custom menu location', 'wp-custom-widget-area' ); ?></a></h3>
 				</li>
 				<li>
 					<h3><a href="<?php echo admin_url();?>admin.php?page=cwa_help"><?php _e( 'Help', 'wp-custom-widget-area' ); ?></a></h3>
 				</li>
 			</ul>
            
			<div class="welcome-panel custom-wp">
				<div class="col col-8">
					<?php 
						self::menuForm();
					?>
				</div>
				
			</div>
		</div>
		<div class="cwa-error" style="display:none;">
			
		</div>
		<div id="cwa-table-wrap">
		<?php
			self::menuTable();
		?>
		</div>
		<?php 
	}
	public function menuForm(){
		?>
		<form class="cwa-form" method="post" action="" id="cwa-menu-form">
			<input type="hidden" name="id">
			<div class="basic">
				<div class="cwa-form-row">
					<label  class="cwa-form-label">Name </label><input type="text" name="cwa_name" placeholder="Menu location name" required>	<span class="cwa-form-message"></span>
				</div>
				<div class="cwa-form-row">
					<label class="cwa-form-label">Theme location (Id) </label><input type="text" name="cwa_id" placeholder="Menu location id" required><span class="cwa-form-message"></span>
				</div>
				
					
			</div>
			<div class="cwa-form-row">
				<label class="cwa-form-label"> </label><input type="submit" name="create" value="Create" class="cwa-btn cwa-btn-primary"> <input type="reset" value="Cancel" name="cancel" class="cwa-btn cwa-btn-danger">	
			</div>
		</form>

		<?php
	}
	public function menuTable(){
		$data = self::getMenuData();
		//var_dump($data);
		?>
		<table class="cwa-table responstable">
			<thead>
				<tr>
					<th>Sn</th>
					<th width='30%' >Name</th>
					<th width='30%'>Theme location (Id) </th>
					
					<th></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$count = 1;
				if(empty($data)){
					?>
					<tr>
						<td colspan="8" class="no-data">There is no data. create a new Menu location by filling above form.</td>
					</tr>
					<?php 
				}
				foreach ($data as $table) {
					# code...
					?>
					<tr>
						<td><?php echo $count ?></td>
						<td><?php echo $table->cwa_name; ?></td>
						<td><?php echo $table->cwa_id; ?></td>
					
						<td><a href="#get_shortcode" data-id="<?php echo $table->cwa_id; ?>" class="cwa-detail-link tooltip" title="[menu theme_location='<?php echo $table->cwa_id; ?>']">Get shortcode</a> </td>
						<td><a href="#get_code" data-id="<?php echo $table->cwa_id; ?>" class="cwa-detail-link tooltip" title="wp_nav_menu( array( 'theme_location'	=> '<?php echo $table->cwa_id; ?>'	) );">Get code</a> / <a href="#delete" data-id="<?php echo $table->cwa_id; ?>" class="cwa-menu-delete-link">Delete</a></td>
					</tr>
					<?php
					$count++;
				}
				?>
			</tbody>
		</table>
		<?php
		if(isset($_POST['action']))
			die();
	}

	public function getMenuData(){
		global $wpdb;
		$table_name = TABLE_NAME;

		$sql = "SELECT * FROM $table_name WHERE cwa_type='menu'";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$row = $wpdb->get_results( $sql, 'OBJECT');
		return $row;
	}

}
?>
