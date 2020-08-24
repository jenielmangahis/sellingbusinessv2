<!-- Titlebar
================================================== -->
<?php
if(isset($_GET['_offer_type']) && isset($_GET['_property_type'])){
		
		$output ='';
		$property_type = ($_GET['_property_type'])?$_GET['_property_type'].'_image':'default_property_banner_image';
		$bannerbg = get_field($property_type, 'option');
		
		$properties =  get_option('realteo_property_types_fields');
		
		//$search_title = 'Search result';
		
		$count = $GLOBALS['wp_query']->found_posts;
		$search_title = "Listings";
		$sub_res = sprintf(_n(  'We\'ve found <em class="count_properties">%s</em> <em class="count_text">business</em> for you', 'We\'ve found <em class="count_properties">%s</em> <em class="count_text">businesses</em> for you' , $count, 'realteo' ), $count); 
		
		foreach ($properties as $indx=>$key ) {
			$id = sanitize_title($key);	
			if($id==$_GET['_property_type']){
				$search_title = $key;
				break;
			}
		}
		if(!$bannerbg){
			$bannerbg = get_field('default_property_banner_image', 'option');
		}
	
		if(!empty($bannerbg)) { 
			$opacity = get_option('findeo_search_bg_opacity',0.45);
			$color = get_option('findeo_search_color','#36383e');
			$output = 'data-background="'.esc_attr($bannerbg['url']).'" data-img-width="'.esc_attr($bannerbg['width']).'" data-img-height="'.esc_attr($bannerbg['height']).'" 
			data-diff="300"	data-color="'.esc_attr($color).'" data-color-opacity="'.esc_attr($opacity).'"';
		}
?>
	<div class="parallax margin-bottom-40" <?=$output?>>
    	<div class="container">
			<div class="row"><div class="col-md-12">
				<div class="search-container">
                	<h1 class="page-title" style="color:#fff"><?=$search_title?></h1>
                    <h3 style="color:#fff"><?=$sub_res?></h3>
                </div>
            </div></div>
        </div>
	</div>
<?php
	} else if(is_tax( 'region' )){
		$tax = $wp_query->get_queried_object();
		$bannerbg = get_field('image_banner', $tax);
		if(empty($bannerbg)){
			$bannerbg = get_field('default_property_banner_image', 'option');
		}
		$search_title = $tax->name;
		
		if(!empty($bannerbg)) { 
			$opacity = get_option('findeo_search_bg_opacity',0.45);
			$color = get_option('findeo_search_color','#36383e');
			$output = 'data-background="'.esc_attr($bannerbg['url']).'" data-img-width="'.esc_attr($bannerbg['width']).'" data-img-height="'.esc_attr($bannerbg['height']).'" 
			data-diff="300"	data-color="'.esc_attr($color).'" data-color-opacity="'.esc_attr($opacity).'"';
		}
?>
	<div class="parallax margin-bottom-40" <?=$output?>>
    	<div class="container">
			<div class="row"><div class="col-md-12">
				<div class="search-container">
                	<h1 class="page-title" style="color:#fff"><?=$search_title?></h1>
                </div>
            </div></div>
        </div>
	</div>	
<?php	
	} else{ // else load default
?>
<div class="parallax titlebar"
	data-background="<?php echo get_option('pp_properties_header_upload'); ?>"
	data-color="#333333"
	data-color-opacity="0.7"
	data-img-width="800"
	data-img-height="505">

	<div id="titlebar">
		<div class="container">
			<div class="row">
				<div class="col-md-12">

					<?php
					the_archive_title( '<h1 class="page-title">', '</h1>' );
					$subtitle = get_option('findeo_properties_archive_subtitle');
					if(isset($_GET['keyword_search'])) {
						?>
						<span>
						<?php
						$count = $GLOBALS['wp_query']->found_posts;
						printf(_n(  'We\'ve found <em class="count_properties">%s</em> <em class="count_text">business</em> for you', 'We\'ve found <em class="count_properties">%s</em> <em class="count_text">businesses</em> for you' , $count, 'realteo' ), $count); 
						?>
						</span>
						<?php
					} else {
						if($subtitle) {
							'<span>'.$subtitle.'</span>';
						}
					}
					

				?>
					
					<!-- Breadcrumbs -->
					<?php if(function_exists('bcn_display')) { ?>
			        <nav id="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
						<ul>
				        	<?php bcn_display_list(); ?>
				        </ul>
					</nav>
					<?php } ?>

				</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>