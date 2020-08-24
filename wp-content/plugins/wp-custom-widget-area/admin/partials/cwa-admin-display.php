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
class CWA_view
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
 				<li class="active">
 					<h3><a href="<?php echo admin_url();?>admin.php?page=custom_widget_area"><?php _e( 'Custom widget area', 'wp-custom-widget-area' ); ?></a></h3>
 				</li>
 				<li>
 					<h3><a href="<?php echo admin_url();?>admin.php?page=custom_menu_location"><?php _e( 'Custom menu location', 'wp-custom-widget-area' ); ?></a></h3>
 				</li>
 				<li>
 					<h3><a href="<?php echo admin_url();?>admin.php?page=cwa_help"><?php _e( 'Help', 'wp-custom-widget-area' ); ?></a></h3>
 				</li>
 			</ul>
            

			<div class="welcome-panel custom-wp">
				<div class="col col-8">
					<?php 
						self::widgetForm();
					?>
				</div>
				
			</div>
		</div>
		<div class="cwa-error" style="display:none;">
			
		</div>
		
		<div id="cwa-table-wrap">
		<?php
			self::widgetTable();
		?>
		</div>
		<?php 
	}
	public function widgetForm(){
		?>
		<form class="cwa-form" method="post" action="" id="cwa-form">
			<input type="hidden" name="id">
			<input type="hidden" name="task">
			<input type="hidden" name="updateid">
			<div class="basic">
				<div class="cwa-form-row">
					<label  class="cwa-form-label" for="cwa_name" >Name </label><input type="text" id="cwa_name" name="cwa_name" placeholder="Widget area name" required>	<span class="cwa-form-message"></span>
				</div>
				<div class="cwa-form-row">
					<label class="cwa-form-label" for="cwa_id">Id </label><input type="text" name="cwa_id" id="cwa_id" placeholder="Widget area id" required><span class="cwa-form-message"></span>
				</div>
				<div class="cwa-form-row">
					<label class="cwa-form-label" for="cwa_description">Description</label><input type="text" id="cwa_description" name="cwa_description" placeholder="Description"><span class="cwa-form-message"></span>
				</div> 
					
			</div>
			<div class="advanced hide">
				<div class="cwa-form-row">
					<label class="cwa-form-label" for="cwa_widget_class">Widget class</label><input type="text" id="cwa_widget_class" name="cwa_widget_class" placeholder="Class"><span class="cwa-form-message"></span>
				</div>
				<div class="cwa-form-row">
					<label class="cwa-form-label" for="cwa_widget_wrapper">Before/After widget </label>
					<select name="cwa_widget_wrapper" id="cwa_widget_wrapper">
						<option selected value="li">li</option>
						<option value="div">div</option>
						<option value="aside">aside</option>
						<option value="span">span</option>
					</select>
					<textarea id="before_after_widget" name="before_after_widget" placeholder="Before/ After widget" title='i.e. <br>[{ <br> &nbsp;&nbsp; "tag": "div", <br> &nbsp;&nbsp; "id": "widget-outer", <br> &nbsp;&nbsp; "class": "widget-outer", <br> &nbsp;&nbsp; "data-tag": "outer" <br> }, <br>	{ <br> &nbsp;&nbsp;	 "tag": "div", <br> &nbsp;&nbsp; "class": "widget-inner", <br> &nbsp;&nbsp; ... <br>}, <br> ...'] class="tooltip hidden" style="min-height: 150px;" ></textarea>
					<input id="widget_wrapper_tg" class="tg" name="widget_wrapper_tg" type="hidden">

					<a class="fieldSwitcher">Custom</a>
					<span class="cwa-form-message"></span>
				</div>
				<div class="cwa-form-row">
					<label class="cwa-form-label" for="cwa_widget_header_class">Widget title class</label><input type="text" id="cwa_widget_header_class" name="cwa_widget_header_class" placeholder="Class"><span class="cwa-form-message"></span>
				</div>
				<div class="cwa-form-row">
					<label class="cwa-form-label" for="cwa_widget_header_wrapper">Before/After widget title </label>
					<select id="cwa_widget_header_wrapper" name="cwa_widget_header_wrapper">
						<option value="h1">h1</option>
						<option selected value="h2">h2</option>
						<option value="h3">h3</option>
						<option value="h4">h4</option>
						<option value="h5">h5</option>
						<option value="h6">h6</option>
					</select>
					<textarea id="before_after_title" name="before_after_title" placeholder="Before/ After widget" title='i.e. <br>[{ <br> &nbsp;&nbsp; "tag": "div", <br> &nbsp;&nbsp; "id": "widget-outer", <br> &nbsp;&nbsp; "class": "widget-outer", <br> &nbsp;&nbsp; "data-tag": "outer" <br> }, <br>	{ <br> &nbsp;&nbsp;	 "tag": "div", <br> &nbsp;&nbsp; "class": "widget-inner", <br> &nbsp;&nbsp; ... <br>}, <br> ...]' class="tooltip hidden" style="min-height: 150px;" ></textarea>
					<input id="widget_header_wrapper_tg" name="widget_header_wrapper_tg" class="tg" type="hidden">
					<a class="fieldSwitcher">Custom</a>
					<span class="cwa-form-message"></span>

				</div>
				
			</div>

			<div class="cwa-form-row">
					<a href="#" id="cwa-advance-btn">Advanced</a>
				</div> 	
			<div class="cwa-form-row">
				<label class="cwa-form-label"> </label><input type="submit" name="create" value="Create" class="cwa-btn cwa-btn-primary"> <input type="reset" value="Cancel" name="cancel" class="cwa-btn cwa-btn-danger">	
			</div>
		</form>

		<?php
	}
	public function widgetTable(){
		$data = self::getWidgetData();
		//var_dump($data);
		?>
		<table class="cwa-table">
			<thead>
				<tr>
					<th>Sn</th>
					<th>Name</th>
					<th>Id</th>
					<th>Description</th>
					<td>Widget class</th>
					<td>Widget header class</th>
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
						<td colspan="8" class="no-data">There is no data. create a new Widget area by filling above form.</td>
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
						<td><?php echo $table->cwa_description; ?></td>
						<td><?php echo $table->cwa_widget_class; ?></td>
						<td><?php echo $table->cwa_widget_header_class; ?></td>
						<td><a href="#get_shortcode" data-id="<?php echo $table->cwa_id; ?>" class="cwa-detail-link tooltip" title="[cwa id='<?php echo $table->cwa_id; ?>']">Get shortcode</a> </td>
						<td><a href="#get_code" data-id="<?php echo $table->cwa_id; ?>" class="cwa-detail-link tooltip" title="dynamic_sidebar( '<?php echo $table->cwa_id; ?>' );">Get code</a> / <a href="#edit" data-id="<?php echo $table->cwa_id; ?>" class="cwa-edit-link">Edit</a> / <a href="#delete" data-id="<?php echo $table->cwa_id; ?>" class="cwa-delete-link">Delete</a></td>
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

	public function getWidgetData(){
		global $wpdb;
		$table_name = TABLE_NAME;
		$sql = "SELECT * FROM $table_name WHERE cwa_type='widget'";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$row = $wpdb->get_results( $sql, 'OBJECT');
		return $row;
	}

}
?>
