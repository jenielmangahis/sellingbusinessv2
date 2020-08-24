<?php
/**
 * Moove_Importer_Content File Doc Comment
 *
 * @category 	Moove_Importer_Content
 * @package   moove-feed-importer
 * @author    Gaspar Nemes
 */

load_textdomain( 'moove', plugins_url( __FILE__ ) . DIRECTORY_SEPARATOR . 'languages' );

/**
 * Moove_Importer_Content Class Doc Comment
 *
 * @category Class
 * @package  Moove_Importer_Content
 * @author   Gaspar Nemes
 */
class Moove_Importer_Content {
	/**
	 * Construct
	 */
	function __construct() {
		$this->moove_register_content_elements();
	}
	/**
	 * Register actions
	 *
	 * @return void
	 */
	function moove_register_content_elements() {

	}
}
$moove_importer_content_provider = new Moove_Importer_Content();
