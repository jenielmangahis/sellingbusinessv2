<?php
/**
 * Advanced Ads Flash Ad Type
 *
 * @package   Advanced_Ads
 * @author    Thomas Maier <thomas.maier@webgilde.com>
 * @license   GPL-2.0+
 * @link      http://webgilde.com
 * @copyright 2015 Thomas Maier, webgilde GmbH
 *
 * Class containing information about the content ad type
 * this should also work as an example for other ad types
 *
 */
class Advanced_Ads_Ad_Type_Flash extends Advanced_Ads_Ad_Type_Abstract{

	/**
	 * ID - internal type of the ad type
	 *
	 * must be static so set your own ad type ID here
	 * use slug like format, only lower case, underscores and hyphens
	 *
	 * @since 1.2.0
	 */
	public $ID = 'flash';

	/**
	 * set basic attributes
	 *
	 * @since 1.2.0
	 */
	public function __construct() {
		$this->title = __( 'Flash File', 'advanced-ads-pro' );
		$this->description = __( 'Upload files and animations in the flash format (.swf).', 'advanced-ads-pro' );
		$this->parameters = array(
			'content' => '',
			'flash_file_url' => ''
		);
	}

	/**
	 * output for the ad parameters metabox
	 *
	 * @param obj $ad ad object
	 * @since 1.2.0
	 */
	public function render_parameters($ad){
		// load tinymc content exitor
		$content = ( isset( $ad->content ) ) ? $ad->content : '';
		$filename = ( isset( $ad->output['flash_file_url'] ) ) ? $ad->output['flash_file_url'] : '';

		?><p class="description"><?php _e( 'Url of the flash file.', 'advanced-ads-pro' ); ?></p>
		<input type="text" name="advanced_ad[output][flash_file_url]" placeholder="<?php _e( 'Insert file url', 'advanced-ads-pro' ); ?>" id="advads-pro-flash-url" value="<?php echo $filename; ?>"/>
		<a href="#" data-uploader-title="<?php _e( 'Insert File', 'advanced-ads-pro' ); ?>" data-uploader-button-text="<?php _e( 'Insert', 'advanced-ads-pro' ); ?>" class="advads_flash_upload" onclick="return false;"><?php _e( 'Open Media Library', 'advanced-ads-pro' ); ?></a>
		<br/><br/>
		<p class="description"><?php _e( 'Insert code that should be displayed if flash is not supported.', 'advanced-ads-pro' ); ?></p>
		<textarea id="advads-ad-content-plain" cols="40" rows="10" name="advanced_ad[content]"><?php echo $content; ?></textarea><?php
	}

	/**
	 * prepare the ads frontend output by adding <object> tags
	 *
	 * @param obj $ad ad object
	 * @return str $content ad content prepared for frontend output
	 * @since 1.2.0
	 */
	public function prepare_output($ad){

		$filename = ( isset( $ad->output['flash_file_url'] ) ) ? $ad->output['flash_file_url'] : '';
		$content = ( isset( $ad->content ) ) ? $ad->content : '';
		$width = ( isset( $ad->width ) ) ? $ad->width : 0;
		$height = ( isset( $ad->height ) ) ? $ad->height : 0;

		if( $filename === '' || $width === 0 || $height === 0 ) {
		    return;
		}

		ob_start();
		?><object id="flashcontent" width="<?php echo $width; ?>" height="<?php echo $height; ?>"><param name="movie" value="<?php echo $filename; ?>" />
		<!--[if !IE]>--><object type="application/x-shockwave-flash" data="<?php echo $filename; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>"><!--<![endif]-->
		<p><?php echo $content; ?></p>
		<!--[if !IE]>-->
		</object>
		<!--<![endif]-->
		</object><?php
		return ob_get_clean();
	}

}