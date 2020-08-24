<?php

$cminds_plugin_config = array(
    'plugin-is-pro'            => TRUE,
    'plugin-is-addon'          => FALSE,
    'plugin-version'           => '1.8.15',
    'plugin-abbrev'            => 'cmac',
    'plugin-addons'            => array(
        array( 'title' => 'CM Ad Changer Client', 'description' => 'Lets you connect remote WordPress sites to your server plugin to serve the same campaigns over multiple sites.', 'link' => 'https://www.cminds.com/store/ad-changer-multiple-client-licenses-addition-for-wordpress-by-creativeminds/' ),
    ),
    'plugin-show-shortcodes'   => TRUE,
    'plugin-shortcodes-action' => 'cmac-shortcodes',
    'plugin-shortcodes'        => '<p><strong><?php echo __( "Notice: shortcodes are case-sensitive." ); ?></strong></p>
<article class="cm-shortcode-desc">
    <header>
        <h4>[cm_ad_changer]</h4>
        <span>CM AdChanger Ad</span>
    </header>
    <div class="cm-shortcode-desc-inner">
        <h5>Parameters:</h5>
        <ul>
            <li><strong>campaign_id</strong> - ID of a campaign (required*)</li>
            <li><strong>group_id</strong> - ID of a campaign group (required*)</li>
            <li><strong>linked_banner</strong> - Banner is a linked image or just image. Can be 1 or 0 (default: 1)</li>
            <li><strong>debug</strong> - Show the debug info. Can be 1 or 0 (default: 0)</li>
            <li><strong>wrapper</strong> -  Wraps the banner with a div tag. Can be 1 or 0 (default: 0)</li>
            <li><strong>class</strong> -  Allows to select the HTML class name for the banner.</li>
            <li><strong>no_responsive</strong> -  Disable the banner responsiveness. Can be 1 or 0 (default: 0)</li>
            <li><strong>custom_css</strong> -  The CSS code which would only be outputted if the banner is shown. (default: empty)</li>
            <li><strong>allow_inject_js</strong> -  Whether to allow server to inject JS or not. Can be 1 or 0 (default: 0)</li>
            <li><strong>allow_inject_html</strong> -  Whether to allow server to send the HTML Ads or not. Can be 1 or 0 (default: 0)</li>
            <li><strong>width</strong> -  Width of the banner image (default: auto)</li>
            <li><strong>height</strong> -  Height of the banner image (default: auto)</li>
            <li><strong>* - You have to provide either a Group ID or a Campaign ID</strong></li>
        </ul>
        <h5>Example</h5>
        <p><kbd>[cm_ad_changer campaign_id="1" class="CM Ad" debug="1"]</kbd></p>
        <p>Shows the banner/banners from the selected Campaign or Group of Campaigns.</p>
        <h5>Usage</h5>
        <p>To insert the ads container into a page or post use following shortcode: [cm_ad_changer].</p>
        <p>To use it outsite of the content use the following code: &lt;?php echo do_shortcode(\'[cm_ad_changer]\'); ?&gt;</p>
    </div>
</article>',
    'plugin-settings-url'      => admin_url( 'admin.php?page=ac_server' ),
    'plugin-file'              => CMAC_PLUGIN_FILE,
    'plugin-dir-path'          => plugin_dir_path( CMAC_PLUGIN_FILE ),
    'plugin-dir-url'           => plugin_dir_url( CMAC_PLUGIN_FILE ),
    'plugin-basename'          => plugin_basename( CMAC_PLUGIN_FILE ),
    'plugin-icon'              => '',
    'plugin-name'              => CMAC_LICENSE_NAME,
    'plugin-license-name'      => CMAC_LICENSE_NAME,
    'plugin-slug'              => '',
    'plugin-short-slug'        => 'ad-changer',
    'plugin-menu-item'         => 'ac_server',
    'plugin-textdomain'        => CMAC_SLUG_NAME,
    'plugin-userguide-key'     => '174-cm-ad-changer-cmac',
    'plugin-store-url'         => 'https://www.cminds.com/store/adchanger/',
    'plugin-support-url'       => 'https://wordpress.org/support/plugin/cm-ad-changer',
    'plugin-review-url'        => 'https://wordpress.org/support/view/plugin-reviews/cm-ad-changer',
    'plugin-changelog-url'     => CMAC_RELEASE_NOTES,
    'plugin-licensing-aliases' => array( 'CM Ad Changer Pro', 'CM Ad Changer Pro Special' ),
);
