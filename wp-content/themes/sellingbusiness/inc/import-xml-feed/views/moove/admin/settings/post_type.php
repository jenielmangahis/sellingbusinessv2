<?php
function custom_fields($fields){
	//pr($fields);
	//exit();
	//$fields['property']
	unset($fields['property']['_header55']);
	unset($fields['property']['_gallery']);
	
	$fields['property']['property_description']['type'] = 'text';
	
	foreach($fields['property'] as $key=>$property){
		if($property['type']=='text'){			
			$fields['property'][$key]['type'] = 'select';
			$fields['property'][$key]['class'] = 'moove-importer-dynamic-select';
		}
	}
	return $fields;
}
add_filter( 'submit_property_form_fields', 'custom_fields' );
?>
<div class="moove-importer-ajax-spinner"></div>
<!-- moove-importer-ajax-spinner -->
<div class="moove-importer-ajax-import-overlay import-work-on moove-hidden">
    <div class="import-notice">
        <h2><?php _e( 'Feed import started' , 'import-xml-feed' ); ?> </h2>
        <h4><?php _e( 'Please wait finishing the import!' , 'import-xml-feed' ); ?></h4>
    </div>
    <!-- import-notice -->
    <div class="moove-importer-ajax-import-progress-bar">
        <span></span>
    </div>
    <!-- moove-importer-ajax-import-progress-bar -->
    <div class="moove-importer-percentage">
        0%
    </div>
    <!-- moove-importer-percentage -->
    <div class="moove-start-new-import">
        <a href="<?php echo home_url( '/test-page-for-broker/' ); ?>" class="button button-secondary"><?php _e( 'Start new import' , 'import-xml-feed' ); ?> </a>
    </div>
    <!-- moove-start-new-import -->
</div>
<!-- moove-importer-ajax-import-overlay -->
<div class="moove-feed-importer-where moove-hidden">
<!-- <div class="moove-feed-importer-where "> -->
    <h3><?php _e( 'Feed data matching' , 'import-xml-feed' ); ?></h3>
    <hr>
    <?php 
	$data[''] = array('post_type'=>'property');
	
	if ( count( $data ) ) :
        $acf_groups = moove_importer_get_acf_groups();
    ?>
        <div class="moove-feed-importer-taxonomies">
            <div class="moove-post-fields">
                <span class="moove-title-error"></span>
                <div class="property_fields">
                <?php
				echo do_shortcode('[realteo_submit_property]');
				?>
                </div>
                <h3><?php _e( 'Featured image url' , 'import-xml-feed' ); ?></h3>
                <select name="moove-importer-post-type-ftrimage" id="moove-importer-post-type-ftrimage" class="moove-importer-dynamic-select"></select>                
                <?php
				/*
                <h4><?php _e( 'Post title' , 'import-xml-feed' ); ?> *</h4>
				$realteo_form->init_fields();
				$template_loader = new Realteo_Template_Loader;
				$template_loader->set_template_data( 
					array( 
						'action' 		=> $realteo_form->get_action(),
						'fields' 		=> $realteo_form->fields,
						'form'      	=> $realteo_form->form_name,
						'property_edit' => $realteo_form->property_edit,
						'property_id'   => $realteo_form->get_property_id(),
						'step'      	=> $realteo_form->get_step(),
						'submit_button_text' => apply_filters( 'submit_property_form_submit_button_text', __( 'Preview', 'realteo' ) )
						) 
					)->get_template_part( 'property-submit' );
					*/
				/*
				?>
                <select name="moove-importer-post-type-posttitle" id="moove-importer-post-type-posttitle" class="moove-importer-dynamic-select" required>
                </select>

                <h4><?php _e( 'Post content' , 'import-xml-feed' ); ?></h4>
                <select name="moove-importer-post-type-postcontent" id="moove-importer-post-type-postcontent" class="moove-importer-dynamic-select" required>
                </select>

                <h4><?php _e( 'Post date' , 'import-xml-feed' ); ?></h4>
                <select name="moove-importer-post-type-postdate" id="moove-importer-post-type-postdate" class="moove-importer-dynamic-select" required>
                </select>


                <h4><?php _e( 'Post excerpt' , 'import-xml-feed' ); ?></h4>
                <select name="moove-importer-post-type-postexcerpt" id="moove-importer-post-type-postexcerpt" class="moove-importer-dynamic-select" required>
                </select>

                <h4><?php _e( 'Post status' , 'import-xml-feed' ); ?></h4>
                <select name="moove-importer-post-type-status" id="moove-importer-post-type-status" >
                    <option value="publish"><?php _e( 'Published' , 'import-xml-feed' ); ?></option>
                    <option value="pending"><?php _e( 'Pending review' , 'import-xml-feed' ); ?></option>
                    <option value="draft"><?php _e( 'Draft' , 'import-xml-feed' ); ?></option>
                </select>

                <h4><?php _e( 'Post author' , 'import-xml-feed' ); ?></h4>
                <select name="moove-importer-post-type-author" id="moove-importer-post-type-author" >
                    <?php $wp_users = get_users( array( 'who' => 'authors' ) ); ?>
                    <?php foreach ( $wp_users as $wp_user ) : ?>
                        <option value="<?php echo $wp_user->ID ?>" <?php if ( wp_get_current_user()->ID === $wp_user->ID ) : echo "selected='selected'"; endif; ?> ><?php echo $wp_user->user_nicename; ?></option>
                    <?php endforeach; ?>
                </select>

                <h4><?php _e( 'Featured image url' , 'import-xml-feed' ); ?></h4>
                <select name="moove-importer-post-type-ftrimage" id="moove-importer-post-type-ftrimage" class="moove-importer-dynamic-select">
                </select>
                <input type="hidden" name="moove-importer-post-type-select" value="property" />
                <input type="hidden" name="moove-importer-post-type-author" value="<?=get_current_user_id()?>" />
                <input type="hidden" name="moove-importer-post-type-status" value="publish" />
				<?php */ ?>
                <?php
					//todo
					$user_packages = realteo_user_packages( get_current_user_id() );
				?>
                <input type="hidden" name="moove_feed_importer_limit" value="2" />
            </div>
            
        </div>
        <!-- moove-feed-importer-taxonomies -->
    <?php endif;?>
    <div class="moove-submit-btn-cnt moove-hidden">
        <br />
    	<a href="#" class="button button-primary moove-start-import-feed"><?php _e('START IMPORT', 'import-xml-feed'); ?></a>
    </div>
    <!-- moove-submit-btn-cnt -->
</div>
<!-- moove-feed-importer-where -->
<div class="moove-feed-importer-from" style="float:left;">
    <h3><?php _e( 'Feed import setup' , 'import-xml-feed' ); ?></h3>
    <span class="moove-hidden"><a href="#" class="select_another_source button button-secondary"><?php _e( 'Select Another Source' , 'import-xml-feed' ); ?></a> </span>
    <hr>
    <div class="moove-feed-xml-cnt">
        <form action="" class="moove-feed-importer-src-form">
            <h4><?php _e( 'Select the feed source' , 'import-xml-feed' ); ?></h4>
            <div class="moove-importer-radio-cnt">
                <label for="feed_url"><input type="radio" id="feed_url" value="url" name="moove-importer-feed-src" style="display:inline-block" checked="true"/> <?php _e( 'URL' , 'import-xml-feed' ); ?></label>
            <!-- moove-importer-radio-cnt -->
                <div class="moove-importer-src-url moove-to-hide">
                    <label for="moove_importer_url"><?php _e( 'Type the file URL' , 'import-xml-feed' ); ?></label>
                    <input type="text" name="moove_importer_url" id="moove_importer_url">
                </div>
                <br />
                <label for="feed_upload"><input type="radio" id="feed_upload" value="upload" style="display:inline-block" name="moove-importer-feed-src"/> <?php _e( 'UPLOAD' , 'import-xml-feed' ); ?></label>
            <!-- moove-importer-src-url -->
            <div class="file-upload moove-importer-src-upload moove-to-hide moove-hidden ">
                <?php _e('Select XML/RSS file','import-xml-feed'); ?>:
                <br />
                <input type="file" name="moove_importer_file" id="moove_importer_file"><br /><br />
            </div>
            </div>
            <!-- file-upload moove-importer-src-upload -->
            <button class="button button-primary moove-importer-read-file"><?php _e( 'Check DATA' , 'import-xml-feed' ); ?></button>
            <div class="moove-feed-xml-error moove-hidden">
                <h4 style="color: red">
                    <strong><?php _e( 'Wrong or unreadable XML file! Please try again! Check your file extension, should be *.xml or *.rss', 'import-xml-feed' ); ?></strong>
                </h4>
            </div>
            <!-- moove-feed-xml-error -->
        </form>
        <div class="moove-feed-xml-node-select moove-hidden">
            <div class="node-select-cnt">
            </div>
            <!-- node-select-cnt -->
            <button class="button button-primary moove-importer-create-preview"><?php _e( 'Create Preview' , 'import-xml-feed' ); ?></button>
        </div>
        <!-- moove-feed-xml-node-select -->
    </div>
    <!-- moove-feed-xml-cnt -->
    <div class="moove-feed-xml-preview moove-hidden">
        <h4><?php _e( 'Moove Feed Xml Preview' , 'import-xml-feed' ); ?></h4>
        <div class="moove-feed-xml-preview-container">
        </div>
        <!-- moove-feed-xml-preview-container -->
    </div>
    <!-- moove-feed-xml-preview -->
</div>
<!-- moove-feed-importer-from -->
