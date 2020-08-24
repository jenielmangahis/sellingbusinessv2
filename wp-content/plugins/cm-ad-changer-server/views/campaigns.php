<?php
/**
 * CM Ad Changer
 *
 * @author CreativeMinds (http://ad-changer.cminds.com)
 * @copyright Copyright (c) 2013, CreativeMinds
 */
if ( !isset( $_GET[ 'acs_admin_action' ] ) ) {
    $_GET[ 'acs_admin_action' ] = '';
}
$fields_data[ 'status' ]             = isset( $fields_data[ 'status' ] ) ? $fields_data[ 'status' ] : '';
$fields_data[ 'banner_new_window' ]  = isset( $fields_data[ 'banner_new_window' ] ) ? $fields_data[ 'banner_new_window' ] : '';
$fields_data[ 'send_notifications' ] = isset( $fields_data[ 'send_notifications' ] ) ? $fields_data[ 'send_notifications' ] : '';
$fields_data[ 'category_ids' ]       = isset( $fields_data[ 'category_ids' ] ) ? $fields_data[ 'category_ids' ] : array();
$fields_data[ 'use_cloud' ]          = isset( $fields_data[ 'use_cloud' ] ) ? $fields_data[ 'use_cloud' ] : '';
$disableHistoryTable                 = get_option( 'acs_disable_history_table', null );
?>

<script type="text/javascript">
    var base_url = '<?php echo get_bloginfo( 'wpurl' ) ?>';
    var plugin_url = '<?php echo ACS_PLUGIN_URL ?>';
    var upload_tmp_path = '<?php echo cmac_get_upload_url() . AC_TMP_UPLOAD_PATH; ?>';
    var banners_limit = <?php echo BANNERS_PER_CAMPAIGN_LIMIT; ?>;
    var banner_variations_limit = <?php echo BANNER_VARIATIONS_LIMIT; ?>;
    var next_banner_index = 0;
    var label_descriptions = new Object();
    label_descriptions.banner_title = '<?php echo $label_descriptions[ 'banner_title' ]; ?>';
    label_descriptions.banner_title_tag = '<?php echo $label_descriptions[ 'banner_title_tag' ]; ?>';
    label_descriptions.banner_alt_tag = '<?php echo $label_descriptions[ 'banner_alt_tag' ]; ?>';
    label_descriptions.banner_link = '<?php echo $label_descriptions[ 'banner_link' ]; ?>';
    label_descriptions.banner_weight = '<?php echo $label_descriptions[ 'banner_weight' ]; ?>';

    jQuery( document ).ready( function () {
        jQuery( '#cmac_toggle_scroll' ).on( 'click', function () {
            jQuery( '.campaigns_list_scroll' ).toggleClass( 'cmac_noscroll' );
        } );
    } );
</script>

<style>
    .cmac_noscroll{
        overflow: initial !important;
    }
</style>

<div class="wrap ad_changer">
    <h2><?php echo $plugin_data[ 'Name' ]; ?> : Campaigns</h2>
    <?php
    ac_top_menu();
    if ( isset( $errors ) && !empty( $errors ) ) {
        ?>
        <ul class="ac_error cmac-clear">
            <?php
            foreach ( $errors as $error )
                echo '<li>' . $error . '</li>';
            ?>
        </ul>
        <?php
    }
    if ( $success ) {
        echo '<div class="ac_success cmac-clear">' . $success . '</div>';
    }
    ?>
    <div class="ac-edit-form">
        <input type="submit" value="Create new Campaign" class="right cmac-clear" id="new_campaign_button" />
        <div class="cmac-clear"></div>
        <?php
        if ( !empty( $campaigns ) ) {
            ?>
            <button id="cmac_toggle_scroll">Toggle scroll</button>
            <div class="campaigns_list_table_head">
                <div style="text-align: left !important;">Campaign Name</div>
                <div>Advertiser</div>
                <div>Campaign ID</div>
                <div>Campaign Type</div>
                <div>Group ID</div>
                <div>Images</div>
                <div>Clicks</div>
                <div>Impressions</div>
                <div>Ratio</div>
                <div>Status</div>
                <div>Actions</div>
            </div>
            <div class="campaigns_list_scroll cmac-clear">
                <div id="campaigns_list" class="ads_list" cellspacing=0 cellpadding=0 border=0>
                    <?php
                    foreach ( $campaigns as $campaign ) {
                        ?>
                        <div class="row<?php echo isset( $fields_data[ 'campaign_id' ] ) && $fields_data[ 'campaign_id' ] == $campaign->campaign_id ? ' selected_campaign' : '' ?>" campaign_id="<?php echo $campaign->campaign_id ?>">
                            <div class="ac_cell">
                                <a href="<?php echo get_bloginfo( 'wpurl' ) ?>/wp-admin/admin.php?page=ac_server_campaigns&action=edit&campaign_id=<?php echo $campaign->campaign_id ?>" class="field_tip" title="<?php echo $campaign->comment ?>"><?php echo $campaign->title; ?></a>
                            </div>
                            <div class="ac_cell">
                                <?php
                                if ( isset( $campaign->advertiser ) && !empty( $campaign->advertiser ) )
                                    echo $campaign->advertiser[ 'name' ];
                                ?>
                            </div>
                            <div class="ac_cell"><?php echo $campaign->campaign_id; ?></div>
                            <div class="ac_cell">
                                <?php
                                switch ( $campaign->banner_display_method ) {
                                    case 'random':
                                        echo 'Random';
                                        break;
                                    case 'selected':
                                        echo 'Selected';
                                        break;
                                    case 'all':
                                        echo 'Rotated';
                                        break;
                                }
                                echo ' / ';
                                switch ( $campaign->campaign_type_id ) {
                                    case '0':
                                        echo 'Image';
                                        break;
                                    case '1':
                                        echo 'HTML';
                                        break;
                                    case '2':
                                        echo 'AdSense';
                                        break;
                                    case '3':
                                        echo 'Video';
                                        break;
                                    case '4':
                                        echo 'Floating';
                                        break;
                                    case '5':
                                        echo 'Flying bottom';
                                        break;
                                }
                                ?>
                            </div>
                            <div class="ac_cell">
                                <?php
                                echo $campaign->group_id;
                                ?>
                            </div>
                            <div class="ac_cell"><?php echo $campaign->banners_cnt; ?></div>
                            <div class="ac_cell"><?php echo $campaign->clicks_cnt; ?></div>
                            <div class="ac_cell"><?php echo $campaign->impressions_cnt; ?></div>
                            <div class="ac_cell">
                                <?php
                                $clicks_rate = 0;
                                if ( (int) $campaign->impressions_cnt > 0 ) {
                                    $clicks_rate = ((int) $campaign->clicks_cnt / (int) $campaign->impressions_cnt) * 100;
                                }

                                if ( (int) $clicks_rate == (float) $clicks_rate ) {
                                    echo $clicks_rate;
                                } else {
                                    echo number_format( $clicks_rate, 2, '.', '' );
                                }
                                ?>
                            </div>
                            <div class="ac_cell"><?php echo ($campaign->status == '1' ? 'Active' : 'Inactive') ?></div>
                            <div class="actions ac_cell">
                                <a href="<?php echo get_bloginfo( 'wpurl' ) ?>/wp-admin/admin.php?page=ac_server_campaigns&action=edit&campaign_id=<?php echo $campaign->campaign_id ?>"><img src="<?php echo ACS_PLUGIN_URL . '/assets/images/edit.png' ?>" alt="Edit Campaign" title="Edit Campaign" /></a>
                                <a href="<?php echo get_bloginfo( 'wpurl' ) ?>/wp-admin/admin.php?page=ac_server_campaigns&action=delete&campaign_id=<?php echo $campaign->campaign_id ?>" class="delete_campaign_link"><img src="<?php echo ACS_PLUGIN_URL . '/assets/images/trash.png' ?>" alt="Delete Campaign" title="Delete Campaign" /></a>
                                <a href="<?php echo get_bloginfo( 'wpurl' ) ?>/wp-admin/admin.php?page=ac_server_campaigns&action=duplicate&campaign_id=<?php echo $campaign->campaign_id ?>" ><img src="<?php echo ACS_PLUGIN_URL . '/assets/images/duplct.png' ?>" alt="Duplicate Campaign" title="Duplicate Campaign"  /></a>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
        }

        if ( isset( $fields_data[ 'title' ] ) || (isset( $_GET[ 'acs_admin_action' ] ) && $_GET[ 'acs_admin_action' ] == 'new_campaign') ) :
            ?>

            <div class="ac-edit-form">
                <form id="campaign_form" class="cmac-clear ac-form" <?php echo (isset( $fields_data[ 'title' ] ) || (isset( $_GET[ 'acs_admin_action' ] ) && $_GET[ 'acs_admin_action' ] == 'new_campaign') ? 'style="display:block !important"' : '') ?> method="post">
                    <?php
                    if ( isset( $fields_data[ 'campaign_id' ] ) ) {
                        echo '<input type="hidden" name="campaign_id" value="' . $fields_data[ 'campaign_id' ] . '" />';
                    }
                    ?>
                    <div class="right" style="margin-bottom: 5px;">
                        <input type="submit" value="<?php echo (isset( $fields_data[ 'campaign_id' ] ) ? 'Save' : 'Add') ?>" name="submit" id="submit_button" class="right">

                    </div>
                    <div id="ac-fields" class="cmac-clear">
                        <ul>
                            <li><a href="#campaign_fields">Campaign Settings</a></li>
                            <li><a href="#banners_fields">Campaign Banners</a></li>
                            <li><a href="#periods_fields">Campaign Activity Settings</a></li>
                        </ul>
                        <table cellspacing=0 cellpadding=0 border=0 class="cmac-clear" id="campaign_fields" style="width:100%">
                            <tr>
                                <td>
                                    <label class="ac-form-label" for="title" >Campaign Name <span class="required" style="color:red">*</span> </label><div class="field_help" title="<?php echo $label_descriptions[ 'title' ] ?>"></div><br/>
                                    <?php
                                    if ( isset( $fields_data ) && isset( $fields_data[ 'campaign_id' ] ) )
                                        echo '<br><strong>Campaign ID <div class="field_help" title="' . $label_descriptions[ 'campaign_id' ] . '"></div> :' . $fields_data[ 'campaign_id' ] . '</strong>';
                                    ?>
                                </td>
                                <td>
                                    <input type="text" aria-required="true" value="<?php echo (isset( $fields_data[ 'title' ] ) ? $fields_data[ 'title' ] : '') ?>" name="title" id="title" /></br>
                                </td>
                            </tr>
                            <?php apply_filters( 'cmadcd-campaign-allowed-users-dropdown', $fields_data ) ?>
                            <tr>
                                <td>
                                    <label class="ac-form-label" for="group_id">Campaign Group:</label>
                                    <div class="field_help" title="<?php echo $label_descriptions[ 'group_id' ] ?>"></div>
                                </td>
                                <td>
                                    <select id="group_id" name="group_id">
                                        <option value="0">-None-</option>
                                        <?php
                                        foreach ( $campaign_groups as $campaign_group ) {
                                            $currentGroupId = isset( $fields_data[ 'group_id' ] ) ? $fields_data[ 'group_id' ] : '';
                                            echo '<option value="' . $campaign_group->group_id . '" ' . selected( $campaign_group->group_id, $currentGroupId ) . ' > ' . $campaign_group->description . ' </option>';
                                        }
                                        ?>
                                    </select>
                                    <label class="ac-form-label" for="group_priority">Campaign Weight:</label>
                                    <div class="field_help" title="<?php echo $label_descriptions[ 'group_priority' ] ?>"></div>
                                    <input type="text" aria-required="false" value="<?php echo (isset( $fields_data[ 'group_priority' ] ) ? $fields_data[ 'group_priority' ] : '0') ?>" name="group_priority" id="group_priority" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="ac-form-label" for="comment" class="cmac-clear" >Campaign Notes </label><div class="field_help" title="<?php echo $label_descriptions[ 'comment' ] ?>"></div>
                                </td>
                                <td>
                                    <textarea value="<?php echo (isset( $fields_data[ 'comment' ] ) ? esc_html( stripslashes( $fields_data[ 'comment' ] ) ) : '') ?>" name="comment" id="comment" rows=5 cols=60><?php echo (isset( $fields_data[ 'comment' ] ) ? esc_html( stripslashes( $fields_data[ 'comment' ] ) ) : '') ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="ac-form-label" for="link" >Target URL </label><div class="field_help" title="<?php echo $label_descriptions[ 'link' ] ?>"></div>
                                </td>
                                <td>
                                    <input type="text" aria-required="false" value="<?php echo (isset( $fields_data[ 'link' ] ) ? $fields_data[ 'link' ] : '') ?>" name="link" id="link" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="ac-form-label" for="banner_new_window">Open target URL in new window </label><div class="field_help" title="<?php echo $label_descriptions[ 'banner_url_in_new_window' ] ?>"></div>
                                </td>
                                <td>
                                    <input type="checkbox" aria-required="true" name="banner_new_window" id="banner_new_window" <?php echo ($fields_data[ 'banner_new_window' ] == 'on' || $fields_data[ 'banner_new_window' ] == '1' || $_GET[ 'acs_admin_action' ] == 'new_campaign' ? 'checked=checked' : '') ?> />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="ac-form-label" for="status">Campaign Status </label><div class="field_help" title="<?php echo $label_descriptions[ 'status' ] ?>"></div>
                                </td>
                                <td>
                                    <input type="checkbox" aria-required="true" name="status" id="status" <?php echo ((empty( $_POST ) && $_GET[ 'acs_admin_action' ] == 'new_campaign') || $fields_data[ 'status' ] == 'on' || $fields_data[ 'status' ] == '1' ? 'checked=checked' : '') ?> />&nbsp;Active
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="ac-form-label" for="manager_email">Campaign Manager Email:</label><div class="field_help" title="<?php echo $label_descriptions[ 'email_notifications' ] ?>"></div>
                                </td>
                                <td>
                                    <input type="text" name="manager_email" id="manager_email" value="<?php echo (isset( $fields_data[ 'manager_email' ] ) ? $fields_data[ 'manager_email' ] : '') ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="ac-form-label" for="send_notifications">Send Notifications </label><div class="field_help" title="<?php echo $label_descriptions[ 'send_notifications' ] ?>"></div>
                                </td>
                                <td>
                                    <input type="checkbox" name="send_notifications" id="send_notifications" <?php echo ($fields_data[ 'send_notifications' ] == 'on' || $fields_data[ 'send_notifications' ] == '1' ? 'checked=checked' : '') ?>  />
                                </td>
                            </tr>

                            </tr>

                            <tr>
                                <td>
                                    <label class="ac-form-label" for="max_impressions">Max Impressions </label><div class="field_help" title="<?php echo $label_descriptions[ 'max_impressions' ] ?>"></div>
                                </td>
                                <td>
                                    <input type="text" aria-required="true" name="max_impressions" id="max_impressions" value="<?php echo isset( $fields_data[ 'max_impressions' ] ) && is_numeric( $fields_data[ 'max_impressions' ] ) ? $fields_data[ 'max_impressions' ] : '0' ?>" />
                                    <script type="text/javascript">
                                        jQuery( document ).ready( function () {
                                            jQuery( '#max_impressions' ).spinner( { min: 0, step: 100 } );
                                        } );</script>
                                    <?php
                                    if ( $disableHistoryTable == 1 ) {
                                        echo '<div class="inlineMessageError"><strong>' . translate( 'Max Impressions limitation not available due to history functionality disabled.' ) . '</strong></div>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="ac-form-label" for="max_clicks">Max Clicks </label><div class="field_help" title="<?php echo $label_descriptions[ 'max_clicks' ] ?>"></div>
                                </td>
                                <td>
                                    <input type="text" aria-required="true" name="max_clicks" id="max_clicks" value="<?php echo isset( $fields_data[ 'max_clicks' ] ) ? $fields_data[ 'max_clicks' ] : '0' ?>" />
                                    <script type="text/javascript">
                                        jQuery( document ).ready( function () {
                                            jQuery( '#max_clicks' ).spinner( { min: 0, step: 100 } );
                                        } );</script>
                                    <?php
                                    if ( $disableHistoryTable == 1 ) {
                                        echo '<div class="inlineMessageError"><strong>' . translate( 'Max Clicks limitation not available due to history functionality disabled.' ) . '</strong></div>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="ac-form-label" for="">Approved Domains </label><div class="field_help" title="<?php echo $label_descriptions[ 'categories' ] ?>"></div>
                                </td>
                                <td>
                                    <input type="text" value="<?php echo str_replace( array( 'http://', 'https://' ), '', get_bloginfo( 'wpurl' ) ); ?>" disabled=disabled id="server_domain" />
                                    <div class="categories">
                                        <?php
                                        if ( isset( $fields_data[ 'category_title' ] ) && !empty( $fields_data[ 'category_title' ] ) ) {
                                            foreach ( $fields_data[ 'category_title' ] as $category_index => $category_title ) {
                                                ?>
                                                <div class="category_row">
            <!--								<input type="checkbox" aria-required="true" name="categories[]" value="<?php echo $fields_data[ 'category_ids' ][ $category_index ] ?>" <?php echo (isset( $fields_data[ 'categories' ] ) && in_array( $fields_data[ 'category_ids' ][ $category_index ], $fields_data[ 'categories' ] ) ? 'checked=checked' : '') ?> />&nbsp; -->
                                                    <input type="text" name="category_title[]" value="<?php echo $category_title ?>" />
                                                    <input type="hidden" name="category_ids[]" value="<?php echo $fields_data[ 'category_ids' ][ $category_index ] ?>" />
                                                    <a href="#" class="delete_link"><img src="<?php echo ACS_PLUGIN_URL . '/assets/images/close.png' ?>" /></a>
                                                </div>
                                                <?php
                                            }
                                        } else
                                            echo 'There are no domain limitations set';
                                        ?>
                                    </div>
                                    <script type="text/javascript">
                                        var max_cat_id =<?php echo (count( $fields_data[ 'category_ids' ] ) > 0 ? max( $fields_data[ 'category_ids' ] ) : 0) ?>;</script>
                                    <a href="#" id="add_category" class="add_link"><img src="<?php echo ACS_PLUGIN_URL . '/assets/images/plus.png' ?>" /></a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="ac-form-label" for="">Advertisers </label><div class="field_help" title="<?php echo $label_descriptions[ 'advertiser' ] ?>"></div>
                                </td>
                                <td>
                                    <div id="advertisers">
                                        <select id="advertiser_id" name="advertiser_id">
                                            <option value="0">Select Advertiser </option>
                                            <?php
                                            if ( isset( $fields_data[ 'advertisers' ] ) && !empty( $fields_data[ 'advertisers' ] ) )
                                                foreach ( $fields_data[ 'advertisers' ] as $advertiser )
                                                    echo '<option value="' . $advertiser[ 'advertiser_id' ] . '" ' . (isset( $fields_data[ 'advertiser' ][ 'advertiser_id' ] ) && $fields_data[ 'advertiser' ][ 'advertiser_id' ] == $advertiser[ 'advertiser_id' ] ? 'selected=selected' : '') . '>' . $advertiser[ 'name' ] . '</option>';
                                            ?>
                                        </select>
                                        <div id="add_advertiser_fields">
                                            <input type="text" id="advertiser_name" name="advertiser_name" value="<?php echo (isset( $fields_data[ 'advertiser' ][ 'name' ] ) ? $fields_data[ 'advertiser' ][ 'name' ] : '') ?>" />
                                        </div>
                                    </div>
                                    <?php if ( !isset( $fields_data[ 'advertiser' ][ 'name' ] ) || empty( $fields_data[ 'advertiser' ][ 'name' ] ) )  ?>
                                    <script type="text/javascript">
                                        jQuery( document ).ready( function () {
                                            CM_AdsChanger.show_add_advertiser_fields();
                                        } )
                                    </script>
                                </td>
                            </tr>
                            <tr>
                                <td valign=top>
                                    <label class="ac-form-label" for="custom_js" >Custom JS </label><div class="field_help" title="<?php echo $label_descriptions[ 'custom_js' ] ?>"></div>
                                </td>
                                <td>
                                    <textarea id="custom_js" name="custom_js" rows=5 cols=60><?php echo isset( $fields_data[ 'custom_js' ] ) ? stripslashes( $fields_data[ 'custom_js' ] ) : '' ?></textarea>
                                </td>
                            </tr>
                        </table>

                        <table cellspacing=0 cellpadding=0 border=0 id="banners_fields">
                            <tr>
                                <td>
                                    <label class="ac-form-label" for="campaign_type_id"><?php echo __( 'Campaign Type:' ); ?></label>
                                    <div class="field_help" title="<?php echo $label_descriptions[ 'campaign_type_id' ] ?>"></div>
                                </td>
                                <td>
                                    <select id="campaign_type_id" name="campaign_type_id">
                                        <?php
                                        $currentCampaignType = isset( $fields_data[ 'campaign_type_id' ] ) ? $fields_data[ 'campaign_type_id' ] : '0';
                                        ?>
                                        <option value="0" <?php selected( '0', $currentCampaignType ) ?> ><?php echo __( 'Image Banners' ); ?></option>
                                        <option value="1" <?php selected( '1', $currentCampaignType ) ?> ><?php echo __( 'HTML Ads' ); ?></option>
                                        <option value="2" <?php selected( '2', $currentCampaignType ) ?> ><?php echo __( 'AdSense Campaign' ); ?></option>
                                        <option value="3" <?php selected( '3', $currentCampaignType ) ?> ><?php echo __( 'Video Campaign' ); ?></option>
                                        <option value="4" <?php selected( '4', $currentCampaignType ) ?> ><?php echo __( 'Floating Banner' ); ?></option>
                                        <option value="5" <?php selected( '5', $currentCampaignType ) ?> ><?php echo __( 'Flyin Bottom Page Banner' ); ?></option>
                                    </select>
                                </td>
                            </tr>

                            <tbody id="banner_ads_part" class="campaign_type_part">
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="cloud_url"><?php echo __( 'Cloud Storage URL:' ); ?></label><div class="field_help" title="<?php echo $label_descriptions[ 'cloud_url' ] ?>"></div>
                                    </td>
                                    <td>
                                        <input type="text" name="cloud_url" id="cloud_url" value="<?php echo (isset( $fields_data[ 'cloud_url' ] ) ? $fields_data[ 'cloud_url' ] : '') ?>" />&nbsp;
                                        <input type="checkbox" name="use_cloud" id="use_cloud" <?php echo ($fields_data[ 'use_cloud' ] == 'on' || $fields_data[ 'use_cloud' ] == '1' ? 'checked=checked' : '') ?> onclick="if ( jQuery( '#cloud_url' ).val() == '' ) {
                                                        jQuery( this ).removeAttr( 'checked' );
                                                    }"  />&nbsp;<label for="use_cloud">Use Cloud Storage</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="use_selected_banner">Display Method </label><div class="field_help" title="<?php echo $label_descriptions[ 'banner_display_method' ] ?>"></div>
                                    </td>
                                    <td>
                                        <input type="radio" aria-required="true" name="banner_display_method" id="use_selected_banner" <?php echo (isset( $fields_data[ 'banner_display_method' ] ) && $fields_data[ 'banner_display_method' ] == 'selected' ? 'checked=checked' : '') ?> value="selected" />&nbsp;<label for="use_selected_banner">Selected Banner</label><br/>
                                        <input type="radio" aria-required="true" name="banner_display_method" id="use_random_banner" <?php echo (isset( $fields_data[ 'banner_display_method' ] ) && $fields_data[ 'banner_display_method' ] == 'random' ? 'checked=checked' : (!isset( $fields_data[ 'banner_display_method' ] ) ? 'checked=checked' : '')) ?> value="random" />&nbsp;<label for="use_random_banner">Random Banner</label><br/>
                                        <input type="radio" aria-required="true" name="banner_display_method" id="use_all_banners" <?php echo (isset( $fields_data[ 'banner_display_method' ] ) && $fields_data[ 'banner_display_method' ] == 'all' ? 'checked=checked' : '') ?> value="all" />&nbsp;<label for="use_all_banners">Rotated Banner</label>
                                        <input type="checkbox" name="rotated_random" id="rotated_random" class='rotated_random' <?php
                                        if ( !empty( $fields_data['meta'][ 'rotated_random' ] ) ) {
                                            echo 'checked';
                                        }
                                        ?> />&nbsp;Randomize Rotated Banners
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="campaign_images">Campaign Images </label><div class="field_help" title="<?php echo $label_descriptions[ 'campaign_images' ] ?>"></div>
                                    </td>
                                    <td>
                                        <div id="container">
                                            <input type="button" value="Select files" id="pickfiles" class="pickfiles">
                                            <input type="button" value="Remove all images" id="remove_all_images" class="remove_all_images cmac-clear">
                                            <div id="filelist" class="cmac-clear">
                                                <?php
                                                if ( isset( $fields_data[ 'banner_filename' ] ) ) {
                                                    foreach ( $fields_data[ 'banner_filename' ] as $banner_index => $banner_filename ) {
                                                        $clicks_rate = 0;
                                                        if ( (int) $fields_data[ 'banner_clicks_cnt' ][ $banner_index ] > 0 ) {
                                                            if ( $fields_data[ 'banner_impressions_cnt' ][ $banner_index ] > 0 )
                                                                $clicks_rate = ((int) $fields_data[ 'banner_clicks_cnt' ][ $banner_index ] / (int) $fields_data[ 'banner_impressions_cnt' ][ $banner_index ]) * 100;
                                                            else
                                                                $clicks_rate = 100;
                                                        }
                                                        if ( (int) $clicks_rate != (float) $clicks_rate )
                                                            $clicks_rate = number_format( $clicks_rate, 2, '.', '' );

                                                        //if(@file_get_contents(get_bloginfo('wpurl') . '/wp-content/uploads/'.AC_UPLOAD_PATH.''.$banner_filename)){
                                                        $fullSizeUrl = cmac_get_upload_url() . $banner_filename;
                                                        if ( file_exists( cmac_get_upload_dir() . $banner_filename ) ) {
                                                            $filename = cmac_get_upload_dir() . $banner_filename;

                                                            $info = pathinfo( cmac_get_upload_dir() . $banner_filename );

                                                            if ( file_exists( cmac_get_upload_dir() . $info[ 'filename' ] . BANNER_THUMB_WIDTH . 'x' . BANNER_THUMB_HEIGHT . '.' . $info[ 'extension' ] ) ) {
                                                                $thumb_url = cmac_get_upload_url() . $info[ 'filename' ] . BANNER_THUMB_WIDTH . 'x' . BANNER_THUMB_HEIGHT . '.' . $info[ 'extension' ];
                                                            } else {
                                                                $thumb_url = cmac_get_upload_url() . $banner_filename;
                                                            }
                                                        } else {
                                                            $filename = cmac_get_upload_dir() . AC_TMP_UPLOAD_PATH . $banner_filename;

                                                            $info = pathinfo( cmac_get_upload_dir() . AC_TMP_UPLOAD_PATH . $banner_filename );

                                                            if ( file_exists( cmac_get_upload_dir() . AC_TMP_UPLOAD_PATH . $info[ 'filename' ] . BANNER_THUMB_WIDTH . 'x' . BANNER_THUMB_HEIGHT . '.' . $info[ 'extension' ] ) )
                                                                $thumb_url = cmac_get_upload_url() . AC_TMP_UPLOAD_PATH . $info[ 'filename' ] . BANNER_THUMB_WIDTH . 'x' . BANNER_THUMB_HEIGHT . '.' . $info[ 'extension' ];
                                                            else
                                                                $thumb_url = cmac_get_upload_url() . AC_TMP_UPLOAD_PATH . $banner_filename;
                                                        }

                                                        // image info
                                                        $image_size            = getimagesize( $filename );
                                                        $filesize              = round( filesize( $filename ) / 1024 );
                                                        $image_width           = $image_size[ 0 ];
                                                        $image_height          = $image_size[ 1 ];
                                                        $mime_splitted         = explode( '/', $image_size[ 'mime' ] );
                                                        $ext                   = $mime_splitted[ 1 ];
                                                        $image_info            = '<b>Dimensions:</b> ' . $image_width . 'x' . $image_height . "<br/>";
                                                        $image_info .= '<b>Size:</b> ' . $filesize . ' kb' . "<br/>";
                                                        $image_info .= '<b>Type:</b> ' . $ext;
                                                        echo '<div class="plupload_image">';
                                                        echo '<img src="' . $thumb_url . '" class="banner_image" title="' . $image_info . '" />';
                                                        echo '<input type="hidden" name="banner_filename[]" value="' . $banner_filename . '" />';
                                                        echo '<table class="banner_info" border=0>';
                                                        echo '<tr><td><label for="banner_title' . $banner_index . '">Name</label> <div class="field_help" title="' . $label_descriptions[ 'banner_title' ] . '"></div></td><td><input type="text" name="banner_title[]" id="banner_title' . $banner_index . '" maxlength="50" value="' . (isset( $fields_data[ 'banner_title' ][ $banner_index ] ) ? $fields_data[ 'banner_title' ][ $banner_index ] : '') . '" /></td></tr>';
                                                        echo '<tr><td><label for="banner_title_tag' . $banner_index . '">Banner Title</label> <div class="field_help" title="' . $label_descriptions[ 'banner_title_tag' ] . '"></div></td><td><input type="text" name="banner_title_tag[]" id="banner_title_tag' . $banner_index . '" maxlength="50" value="' . (isset( $fields_data[ 'banner_title_tag' ][ $banner_index ] ) ? $fields_data[ 'banner_title_tag' ][ $banner_index ] : '') . '" /></td></tr>';
                                                        echo '<tr><td><label for="banner_alt_tag' . $banner_index . '">Banner Alt</label> <div class="field_help" title="' . $label_descriptions[ 'banner_alt_tag' ] . '"></div></td><td><input type="text" name="banner_alt_tag[]" id="banner_alt_tag' . $banner_index . '" maxlength="50" value="' . (isset( $fields_data[ 'banner_alt_tag' ][ $banner_index ] ) ? $fields_data[ 'banner_alt_tag' ][ $banner_index ] : '') . '" /></td></tr>';
                                                        echo '<tr><td><label for="banner_link' . $banner_index . '">Target URL</label> <div class="field_help" title="' . $label_descriptions[ 'banner_link' ] . '"></div></td><td><input type="text" name="banner_link[]" id="banner_link' . $banner_index . '" maxlength="150" value="' . (isset( $fields_data[ 'banner_link' ][ $banner_index ] ) ? $fields_data[ 'banner_link' ][ $banner_index ] : '') . '" /></td></tr>';
                                                        echo '<tr><td><label for="banner_weight' . $banner_index . '">Weight</label> <div class="field_help" title="' . $label_descriptions[ 'banner_weight' ] . '"></div></td><td><input type="text" name="banner_weight[]" id="banner_weight' . $banner_index . '" maxlength="4" value="' . (isset( $fields_data[ 'banner_weight' ][ $banner_index ] ) && is_numeric( $fields_data[ 'banner_weight' ][ $banner_index ] ) ? $fields_data[ 'banner_weight' ][ $banner_index ] : '0') . '" class="num_field" /></td></tr>';
                                                        echo '<tr><td><label for="custom_banner_new_window' . $banner_index . '">Banner Link Behavior</label> <div class="field_help" title="' . $label_descriptions[ 'custom_banner_new_window' ] . '"></div></td><td>';
                                                        ?>
                                                        <select id="custom_banner_new_window<?php echo $banner_index; ?>" name="custom_banner_new_window[]">
                                                            <?php
                                                            $customBannerNewWindow = isset( $fields_data[ 'custom_banner_new_window' ][ $banner_index ] ) ? $fields_data[ 'custom_banner_new_window' ][ $banner_index ] : '';
                                                            ?>
                                                            <option value="campaign_default" <?php selected( 'campaign_default', $customBannerNewWindow ) ?> ><?php echo __( 'Campaign Default' ); ?></option>
                                                            <option value="target_blank" <?php selected( 'target_blank', $customBannerNewWindow ) ?> ><?php echo __( 'Target Blank' ); ?></option>
                                                            <option value="target_self" <?php selected( 'target_self', $customBannerNewWindow ) ?> ><?php echo __( 'Target Seft' ); ?></option>
                                                        </select>
                                                        <?php
                                                        echo '</td></tr>';
                                                        echo '<tr><td><label for="banner_expiration_date' . $banner_index . '">Banner Expiration Date</label> <div class="field_help" title="' . $label_descriptions[ 'banner_expiration_date' ] . '"></div></td>';
                                                        echo '<td id="banner_dates"><span class="date_range_row"><input type="text" name="banner_expiration_date[]" id="banner_expiration_date' . $banner_index . '" maxlength="50" value="' . (isset( $fields_data[ 'meta' ][ $banner_index ][ 'banner_expiration_date' ] ) ? $fields_data[ 'meta' ][ $banner_index ][ 'banner_expiration_date' ] : '') . '" /></span></td></tr>';
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <?php
                                                                echo $label_descriptions[ 'see_full' ];
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <a href="javascript:void(0)" content="<?php echo $fullSizeUrl; ?>" class="banner_full_image_tooltip">
                                                                    <img src="<?php echo ACS_PLUGIN_URL . '/assets/images/eye.png' ?>" />
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                        echo '</table>';
                                                        echo '<div class="ac_explanation cmac-clear">Click on image to select the banner</div>';
                                                        echo '<div class="clicks_and_impressions">';
                                                        echo '<div class="impressions">' . ($fields_data[ 'banner_impressions_cnt' ][ $banner_index ] ? $fields_data[ 'banner_impressions_cnt' ][ $banner_index ] : 0) . '</div>';
                                                        echo '<div class="clicks">' . ($fields_data[ 'banner_clicks_cnt' ][ $banner_index ] ? $fields_data[ 'banner_clicks_cnt' ][ $banner_index ] : 0) . '</div>';
                                                        echo '<div class="percent">' . $clicks_rate . '</div>';
                                                        echo '</div>';
                                                        echo '<img src="' . ACS_PLUGIN_URL . '/assets/images/close.png' . '" class="delete_button" />';

                                                        echo '<div class="cmac-clear"></div><div class="banner_variations" id="banner_variations_container">';
                                                        echo '<input type="button" value="Add variations" id="banner_variation' . $banner_index . '" class="pickfiles cmac-clear"><div class="cmac-clear"></div>';
                                                        if ( isset( $fields_data[ 'banner_variation' ] ) && isset( $fields_data[ 'banner_variation' ][ $banner_filename ] ) && !empty( $fields_data[ 'banner_variation' ][ $banner_filename ] ) ) {
                                                            foreach ( $fields_data[ 'banner_variation' ][ $banner_filename ] as $banner_variation_filename ) {
                                                                if ( file_exists( cmac_get_upload_dir() . $banner_variation_filename ) ) {
                                                                    $filename = cmac_get_upload_dir() . $banner_variation_filename;

                                                                    $info         = pathinfo( $filename );
                                                                    $image_size   = getimagesize( $filename );
                                                                    $image_width  = $image_size[ 0 ];
                                                                    $image_height = $image_size[ 1 ];

                                                                    $thumb_url = cmac_get_upload_url() . $info[ 'filename' ] . BANNER_VARIATION_THUMB_WIDTH . 'x' . BANNER_VARIATION_THUMB_HEIGHT . '.' . $info[ 'extension' ];
                                                                } else {
                                                                    $filename = cmac_get_upload_dir() . AC_TMP_UPLOAD_PATH . $banner_variation_filename;

                                                                    $info         = pathinfo( $filename );
                                                                    $image_size   = getimagesize( $filename );
                                                                    $image_width  = $image_size[ 0 ];
                                                                    $image_height = $image_size[ 1 ];

                                                                    $thumb_url = cmac_get_upload_url() . AC_TMP_UPLOAD_PATH . $info[ 'filename' ] . BANNER_VARIATION_THUMB_WIDTH . 'x' . BANNER_VARIATION_THUMB_HEIGHT . '.' . $info[ 'extension' ];
                                                                }
                                                                echo '<div class="banner_variation">';
                                                                echo '<img src="' . $thumb_url . '" class="banner_variation_image" />';
                                                                echo '<input type="hidden" name="banner_variation[' . $banner_filename . '][]" value="' . $banner_variation_filename . '" />';
                                                                echo '<div class="variation_dimensions">' . $image_width . 'x' . $image_height . '</div>';
                                                                echo '<img src="' . ACS_PLUGIN_URL . '/assets/images/close.png' . '" class="delete_button" />';
                                                                echo '</div>';
                                                            }
                                                        }
                                                        echo '</div>';
                                                        echo'</div>';
                                                    }

                                                    if ( isset( $fields_data[ 'selected_banner' ] ) && !empty( $fields_data[ 'selected_banner' ] ) )
                                                        echo '<script type="text/javascript">
											jQuery(document).ready(function(){
												CM_AdsChanger.check_banner(jQuery(\'#filelist input[type="hidden"][value="' . $fields_data[ 'selected_banner' ] . '"]\').parent());
											})
										  </script>';
                                                }
                                                ?>
                                                <script type="text/javascript">
                                                    jQuery( document ).ready( function () {
                                                        jQuery( 'input[name^="banner_weight"]' ).spinner( { min: 0, max: 100, step: 10 } );
                                                        jQuery( '.plupload_image .banner_image' ).speechbubble();
                                                        jQuery( '.banner_full_image_tooltip' ).tooltip( {
                                                            tooltipClass: "ui-tooltip-full-size",
                                                            items: "a",
                                                            content: function () {
                                                                var element = jQuery( this ), url;
                                                                url = element.attr( 'content' );
                                                                if ( url != '' ) {
                                                                    return '<img src="' + url + '" />';
                                                                } else {
                                                                    return "No image on server."
                                                                }
                                                            },
                                                            position: {
                                                                my: "left top",
                                                                at: "right top"
                                                            }
                                                        } );
                                                    } )
                                                </script>
                                            </div>
                                        </div>
                                        <div class="selected_banner_details">
                                            <label class="ac-form-label">Selected Image URL: </label>
                                            <div id="selected_banner_url"></div>
                                            <label class="ac-form-label" for="selected_image">Selected Image Name: </label>
                                            <div id="selected_banner"></div>
                                            <input type="hidden" name="selected_banner" value="" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"><?php
                                        if ( $disableHistoryTable == 1 ) {
                                            echo '<div class="clear"></div>';
                                            echo '<div class="inlineMessageError"><strong>' . translate( 'Impressions and clicks counters for both campaigns and banners will not be accurate due to history functionality disabled.' ) . '</strong></div>';
                                            echo '<div class="clear"></div>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
 
                            <tbody id="html_ads_part" class="campaign_type_part">
                                <tr>
                                    <?php
                                    $currentBannerDisplayMethod = $currentCampaignType == '1' && isset( $fields_data[ 'banner_display_method' ] ) ? $fields_data[ 'banner_display_method' ] : '';
                                    ?>
                                    <td>
                                        <label class="ac-form-label" for="use_selected_banner">Display Method </label><div class="field_help" title="<?php echo $label_descriptions[ 'banner_display_method' ] ?>"></div>
                                    </td>
                                    <td>
                                        <input type="radio" aria-required="true" name="banner_display_method" id="use_selected_banner" value="selected"  <?php checked( 'selected', $currentBannerDisplayMethod ); ?> />&nbsp;<label for="use_selected_banner">Selected Banner</label><br/>
                                        <input type="radio" aria-required="true" name="banner_display_method" id="use_random_banner" value="random" <?php checked( 'random', $currentBannerDisplayMethod ); ?> />&nbsp;<label for="use_random_banner">Random Banner</label><br/>
                                        <input type="hidden" name="selected_html_ad" value="" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="width" >Width </label><div class="field_help" title="<?php echo $label_descriptions[ 'campaign_width' ] ?>"></div>
                                    </td>
                                    <td>
                                        <input size="20" type="text" placeholder="auto" aria-required="false" value="<?php echo (isset( $fields_data[ 'width' ] ) ? $fields_data[ 'width' ] : '') ?>" name="width" id="width" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="height" >Height </label><div class="field_help" title="<?php echo $label_descriptions[ 'campaign_height' ] ?>"></div>
                                    </td>
                                    <td>
                                        <input size="20" type="text" placeholder="auto" aria-required="false" value="<?php echo (isset( $fields_data[ 'height' ] ) ? $fields_data[ 'height' ] : '') ?>" name="height" id="height" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="addesigner" >AdDesigner </label><div class="field_help" title="<?php echo $label_descriptions[ 'campaign_addesigner' ] ?>"></div>
                                    </td>
                                    <td>
                                        <a href="#" class="button adddesigner_trigger" id="adddesigner_trigger">Show AdDesigner</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="campaign_images">Campaign HTML Ads </label>
                                        <div class="field_help" title="<?php echo $label_descriptions[ 'campaign_html_ads' ] ?>"></div>
                                    </td>
                                    <td>
                                        <div id="container">
                                            <div id="html_ad_list" class="cmac-clear cmac-textareas cmac-group">
                                                <?php
                                                wp_print_styles( 'editor-buttons' );

                                                ob_start();
                                                wp_editor( '', 'content', array(
                                                    'dfw'           => true,
                                                    'editor_height' => 1,
                                                    'tinymce'       => array(
                                                        'resize'             => true,
                                                        'add_unload_trigger' => false,
                                                    )
                                                ) );
                                                $content = ob_get_contents();
                                                ob_end_clean();
                                                $index   = '';
                                                if ( isset( $fields_data[ 'meta' ] ) ) {
                                                    foreach ( $fields_data[ 'meta' ] as $index => $meta ) {
                                                        if ( !isset( $meta[ 'html' ] ) ) {
                                                            continue;
                                                        }
                                                        $impressions = ($fields_data[ 'banner_impressions_cnt' ][ $index ] ? $fields_data[ 'banner_impressions_cnt' ][ $index ] : 0);
                                                        $clicks      = ($fields_data[ 'banner_clicks_cnt' ][ $index ] ? $fields_data[ 'banner_clicks_cnt' ][ $index ] : 0);
                                                        $weight      = ($fields_data[ 'banner_weight' ][ $index ] ? $fields_data[ 'banner_weight' ][ $index ] : 0);

                                                        if ( $impressions != 0 ) {
                                                            $clicks_rate = $clicks / $impressions * 100;
                                                            if ( (int) $clicks_rate != (float) $clicks_rate ) {
                                                                $clicks_rate = number_format( $clicks_rate, 2, '.', '' );
                                                            }
                                                        } else {
                                                            $clicks_rate = 0;
                                                        }

                                                        $settings = array( 'textarea_name' => 'html_ads[]', 'id' => 'html-ad-text_' . $index, 'teeny' => true, 'rows' => 10 );

                                                        $isSelected = '';
                                                        if ( !empty( $fields_data[ 'selected_banner_id' ] ) && $index == $fields_data[ 'selected_banner_id' ] ) {
                                                            $isSelected = 'selected';
                                                        }
                                                        echo '<div class="html_ad_wrapper single_element_wrapper cmac_group-html-banner-group ' . $isSelected . '">';
                                                        echo '<div class="group-control dodelete" title="' . __( 'Click to remove "HTML Banner"', '' ) . '"></div>';
                                                        echo '<input type="hidden" name="banner_ids[]" value="' . $index . '" />';

                                                        echo '<div class="clicks_and_impressions">';
                                                        echo '<div class="impressions">' . $impressions . '</div>';
                                                        echo '<div class="clicks">' . $clicks . '</div>';
                                                        echo '<div class="percent">' . $clicks_rate . '</div>';
                                                        echo '</div>';
                                                        ?>
                                                        <div class="customEditor wp-core-ui wp-editor-wrap <?php echo 'tmce-active'; ?>">
                                                            <div class="wp-editor-tools hide-if-no-js">

                                                                <div class="wp-media-buttons custom_upload_buttons">
                                                                    <?php do_action( 'media_buttons' ); ?>
                                                                </div>

                                                                <div class="wp-editor-tabs">
                                                                    <a data-mode="html" class="wp-switch-editor switch-html"> <?php _ex( 'Text', 'Name for the Text editor tab (formerly HTML)' ); ?></a>
                                                                    <a data-mode="tmce" class="wp-switch-editor switch-tmce"><?php _e( 'Visual' ); ?></a>
                                                                </div>

                                                            </div><!-- .wp-editor-tools -->
                                                            <div class="wp-editor-container">
                                                                <textarea name="html_ads[]" rows="5" class="wp-editor-area"><?php echo esc_html( stripslashes( $meta[ 'html' ] ) ); ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div>
                                                        <label for="html_title<?php echo $index; ?>">Title</label>
                                                        <div class="field_help" title="<?php echo $label_descriptions[ 'html_title' ] ?>"></div>
                                                            <input type="text" name="html_title[]" id="html_title<?php echo $index; ?>" value="<?php echo isset( $fields_data[ 'html_title' ][ $index ] ) ? $fields_data[ 'html_title' ][ $index ] : ''; ?>" />
                                                        </div>
                                                        <?php
                                                        echo '<div><label for="banner_weight' . $index . '">Weight</label><div class="field_help" title="' . $label_descriptions[ 'banner_weight' ] . '"></div><input type="text" name="banner_weight[]" id="banner_weight' . $index . '" maxlength="4" value="' . (isset( $fields_data[ 'banner_weight' ][ $index ] ) && is_numeric( $fields_data[ 'banner_weight' ][ $index ] ) ? $fields_data[ 'banner_weight' ][ $index ] : '0') . '" class="num_field" /></div>';
                                                        echo '<div><label for="banner_link' . $index . '">Target URL</label><div class="field_help" title="' . $label_descriptions[ 'banner_link' ] . '"></div><input type="text" name="banner_link[]" id="banner_link' . $index . '" maxlength="150" value="' . (isset( $fields_data[ 'banner_link' ][ $index ] ) ? $fields_data[ 'banner_link' ][ $index ] : '') . '" /></div>';
                                                        echo '<div id="banner_dates"><label for="banner_expiration_date' . $index . '">Banner Expiration Date</label> <div class="field_help" title="' . $label_descriptions[ 'banner_expiration_date' ] . '"></div>';
                                                        echo '<input type="text" name="banner_expiration_date[]" id="banner_expiration_date' . $index . '" maxlength="50" value="' . (isset( $fields_data[ 'meta' ][ $index ][ 'banner_expiration_date' ] ) ? $fields_data[ 'meta' ][ $index ][ 'banner_expiration_date' ] : '') . '" /></div>';
                                                        ?>
                                                        <div>
                                                            <label class="ac-form-label" for="banner_custom_js<?php echo $index; ?>" > Custom JS scripts</label><div class="field_help" title="<?php echo $label_descriptions[ 'banner_custom_js' ] ?>"></div>
                                                            <input type="checkbox" name="enable_banner_custom_js<?php echo $index; ?>" id="enable_banner_custom_js<?php echo $index; ?>" class='enable_banner_custom_js' <?php
                                                            if ( !empty( $fields_data[ 'banner_custom_js' ][ $index ] ) ) {
                                                                echo 'checked';
                                                            }
                                                            ?> />&nbsp;Enable
                                                            <textarea rows="5" aria-required="false" name="banner_custom_js[]" id="banner_custom_js<?php echo $index; ?>" class='banner_custom_js' /><?php echo (isset( $fields_data[ 'banner_custom_js' ][ $index ] ) ? esc_html( stripslashes( $fields_data[ 'banner_custom_js' ][ $index ] ) ) : '') ?></textarea>
                                                        </div>
                                                        <?php
                                                        echo '</div>';
                                                    }
                                                }
                                                ?>

                                                <div class="html_ad_wrapper single_element_wrapper cmac_group-html-banner-group tocopy last">
                                                    <div class="group-control dodelete" title="<?php _e( 'Click to remove "HTML Banner"', '' ); ?>"></div>

                                                    <input type="hidden" name="banner_ids[]" value="new" />
                                                    <div class="clicks_and_impressions">
                                                        <div class="impressions">0</div>
                                                        <div class="clicks">0</div>
                                                        <div class="percent">0</div>
                                                    </div>

                                                    <?php
                                                    global $wp_version;
                                                    if ( version_compare( $wp_version, '4.3', '<' ) ) {
                                                        add_filter( 'the_editor_content', 'wp_richedit_pre' );
                                                    } else {
                                                        add_filter( 'the_editor_content', 'format_for_editor' );
                                                    }
                                                    $switch_class = 'tmce-active';
                                                    ?>

                                                    <div class="customEditor wp-core-ui wp-editor-wrap <?php echo 'tmce-active'; ?>">
                                                        <div class="wp-editor-tools hide-if-no-js">

                                                            <div class="wp-media-buttons custom_upload_buttons">
                                                                <?php do_action( 'media_buttons' ); ?>
                                                            </div>

                                                            <div class="wp-editor-tabs">
                                                                <a data-mode="html" class="wp-switch-editor switch-html"> <?php _ex( 'Text', 'Name for the Text editor tab (formerly HTML)' ); ?></a>
                                                                <a data-mode="tmce" class="wp-switch-editor switch-tmce"><?php _e( 'Visual' ); ?></a>
                                                            </div>

                                                        </div><!-- .wp-editor-tools -->
                                                        <div class="wp-editor-container">
                                                            <textarea name="html_ads[]" rows="5" class="wp-editor-area"></textarea>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label for="html_title">Title</label>
                                                        <div class="field_help" title="<?php echo $label_descriptions[ 'html_title' ] ?>"></div>
                                                        <input type="text" name="html_title[]" id="html_title" value="" />
                                                    </div>

                                                    <div>
                                                        <label for="banner_weight">Weight</label>
                                                        <div class="field_help" title="<?php echo $label_descriptions[ 'banner_weight' ] ?>"></div>
                                                        <input type="text" name="banner_weight[]" id="banner_weight" maxlength="4" value="0" class="num_field" />
                                                    </div>
                                                    <div>
                                                        <label for="banner_link">Target URL</label>
                                                        <div class="field_help" title="<?php echo $label_descriptions[ 'banner_link' ] ?>"></div>
                                                        <input type="text" name="banner_link[]" id="banner_link<?php echo $index ?>" maxlength="150" value="" />
                                                    </div>
                                                    <div>
                                                        <label class="ac-form-label" for="banner_custom_js<?php echo $index; ?>" > Custom JS scripts</label><div class="field_help" title="<?php echo $label_descriptions[ 'banner_custom_js' ] ?>"></div>
                                                        <input type="checkbox" name="enable_banner_custom_js<?php echo $index; ?>" id="enable_banner_custom_js<?php echo $index; ?>" class='enable_banner_custom_js' />&nbsp;Enable
                                                        <textarea rows="5" aria-required="false" name="banner_custom_js[]" id="banner_custom_js<?php echo $index; ?>" class='banner_custom_js' /></textarea>
                                                    </div>
                                                </div>
                                                <!-- end of to copy -->

                                                <p><a href="#" class="docopy-html-banner-group button"><span class="icon add"></span><?php _e( 'Add HTML Banner', '' ); ?></a></p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"><?php
                                        if ( $disableHistoryTable == 1 ) {
                                            echo '<div class="clear"></div>';
                                            echo '<div class="inlineMessageError"><strong>' . translate( 'Impressions and clicks counters for both campaigns and banners will not be accurate due to history functionality disabled.' ) . '</strong></div>';
                                            echo '<div class="clear"></div>';
                                        }
                                        ?>
                                    </td>
                                </tr>

                            </tbody>

                            <tbody id="adsense_ads_part" class="campaign_type_part">
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="adsense_client" >AdSense Client ID </label><div class="field_help" title="<?php echo $label_descriptions[ 'adsense_client' ] ?>"></div>
                                    </td>
                                    <td>
                                        <input size="40" type="text" placeholder="Format: ca-pub-XXXXXXXXXXXXXXXX" aria-required="false" value="<?php echo (isset( $fields_data[ 'adsense_client' ] ) ? $fields_data[ 'adsense_client' ] : '') ?>" name="adsense_client" id="link" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="adsense_slot" >AdSense Advertisement Slot ID </label><div class="field_help" title="<?php echo $label_descriptions[ 'adsense_slot' ] ?>"></div>
                                    </td>
                                    <td>
                                        <input size="20" type="text" placeholder="Format: XXXXXXXXXX" aria-required="false" value="<?php echo (isset( $fields_data[ 'adsense_slot' ] ) ? $fields_data[ 'adsense_slot' ] : '') ?>" name="adsense_slot" id="link" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="height" >Banner Height </label><div class="field_help" title="<?php echo $label_descriptions[ 'campaign_height' ] ?>"></div>
                                    </td>
                                    <td>
                                        <input size="20" type="text" placeholder="eg. 200px" aria-required="false" value="<?php echo (isset( $fields_data[ 'height' ] ) ? $fields_data[ 'height' ] : '') ?>" name="height" id="link" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="width" > Banner Width </label><div class="field_help" title="<?php echo $label_descriptions[ 'campaign_width' ] ?>"></div>
                                    </td>
                                    <td>
                                        <input size="20" type="text" placeholder="eg. 350px" aria-required="false" value="<?php echo (isset( $fields_data[ 'width' ] ) ? $fields_data[ 'width' ] : '') ?>" name="width" id="link" />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"><?php
                                        if ( $disableHistoryTable == 1 ) {
                                            echo '<div class="clear"></div>';
                                            echo '<div class="inlineMessageError"><strong>' . translate( 'Impressions and clicks counters for both campaigns and banners will not be accurate due to history functionality disabled.' ) . '</strong></div>';
                                            echo '<div class="clear"></div>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            </tbody>

                            <tbody id="video_ads_part" class="campaign_type_part">
                                <tr>
                                    <?php
                                    $currentBannerDisplayMethod = $currentCampaignType == '3' && isset( $fields_data[ 'banner_display_method' ] ) ? $fields_data[ 'banner_display_method' ] : '';
                                    ?>
                                    <td>
                                        <label class="ac-form-label" for="use_selected_banner">Display Method </label><div class="field_help" title="<?php echo $label_descriptions[ 'banner_display_method' ] ?>"></div>
                                    </td>
                                    <td>
                                        <input type="radio" aria-required="true" name="banner_display_method" id="use_selected_banner" value="selected"  <?php checked( 'selected', $currentBannerDisplayMethod ); ?> />&nbsp;<label for="use_selected_banner">Selected Banner</label><br/>
                                        <input type="radio" aria-required="true" name="banner_display_method" id="use_random_banner" value="random" <?php checked( 'random', $currentBannerDisplayMethod ); ?> />&nbsp;<label for="use_random_banner">Random Banner</label><br/>
                                        <input type="hidden" name="selected_video_ad" value="" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="campaign_images">Campaign Video Ads </label>
                                        <div class="field_help" title="<?php echo $label_descriptions[ 'campaign_video_ads' ] ?>"></div>
                                    </td>
                                    <td>
                                        <div id="container">
                                            <div id="video_ad_list" class="cmac-clear cmac-group">
                                                <?php
                                                if ( isset( $fields_data[ 'meta' ] ) ) {
                                                    foreach ( $fields_data[ 'meta' ] as $index => $meta ) {
                                                        if ( !isset( $meta[ 'video' ] ) ) {
                                                            continue;
                                                        }
                                                        $impressions = ($fields_data[ 'banner_impressions_cnt' ][ $index ] ? $fields_data[ 'banner_impressions_cnt' ][ $index ] : 0);
                                                        $clicks      = ($fields_data[ 'banner_clicks_cnt' ][ $index ] ? $fields_data[ 'banner_clicks_cnt' ][ $index ] : 0);
                                                        $weight      = ($fields_data[ 'banner_weight' ][ $index ] ? $fields_data[ 'banner_weight' ][ $index ] : 0);

                                                        if ( $impressions != 0 ) {
                                                            $clicks_rate = $clicks / $impressions * 100;
                                                            if ( (int) $clicks_rate != (float) $clicks_rate ) {
                                                                $clicks_rate = number_format( $clicks_rate, 2, '.', '' );
                                                            }
                                                        } else {
                                                            $clicks_rate = 0;
                                                        }

                                                        $settings = array( 'textarea_name' => 'html_ads[]', 'id' => 'html-ad-text_' . $index, 'teeny' => true, 'rows' => 10 );

                                                        $isSelected = '';
                                                        if ( !empty( $fields_data[ 'selected_banner_id' ] ) && $index == $fields_data[ 'selected_banner_id' ] ) {
                                                            $isSelected = 'selected';
                                                        }
                                                        echo '<div class="html_ad_wrapper single_element_wrapper cmac_group-video-banner-group ' . $isSelected . '">';
                                                        echo '<div class="group-control dodelete" title="' . __( 'Click to remove "Video Banner"', '' ) . '"></div>';
                                                        echo '<input type="hidden" name="banner_ids[]" value="' . $index . '" />';

                                                        echo '<div class="clicks_and_impressions">';
                                                        echo '<div class="impressions">' . $impressions . '</div>';
                                                        echo '<div class="clicks">' . $clicks . '</div>';
                                                        echo '<div class="percent">' . $clicks_rate . '</div>';
                                                        echo '</div>';

                                                        echo '<div class="">';
                                                        echo '<textarea name="video_ads[]" rows="5" class="">' . $meta[ 'video' ] . '</textarea>';
                                                        echo '</div>';

                                                        echo '<div><label for="banner_weight' . $index . '">Weight</label><div class="field_help" title="' . $label_descriptions[ 'banner_weight' ] . '"></div><input type="text" name="banner_weight[]" id="banner_weight' . $index . '" maxlength="4" value="' . (isset( $fields_data[ 'banner_weight' ][ $index ] ) && is_numeric( $fields_data[ 'banner_weight' ][ $index ] ) ? $fields_data[ 'banner_weight' ][ $index ] : '0') . '" class="num_field" /></div>';
                                                        echo '</div>';
                                                    }
                                                }
                                                ?>

                                                <div class="html_ad_wrapper single_element_wrapper cmac_group-video-banner-group tocopy last">
                                                    <div class="group-control dodelete" title="<?php _e( 'Click to remove "Video Banner"', '' ); ?>"></div>

                                                    <input type="hidden" name="banner_ids[]" value="new" />
                                                    <div class="clicks_and_impressions">
                                                        <div class="impressions">0</div>
                                                        <div class="clicks">0</div>
                                                        <div class="percent">0</div>
                                                    </div>

                                                    <div class="">
                                                        <textarea name="video_ads[]" rows="5" class=""></textarea>
                                                    </div>

                                                    <div>
                                                        <label for="banner_weight">Weight</label>
                                                        <div class="field_help" title="<?php echo $label_descriptions[ 'banner_weight' ] ?>"></div>
                                                        <input type="text" name="banner_weight[]" id="banner_weight" maxlength="4" value="0" class="num_field" />
                                                    </div>
                                                </div>

                                                <p><a href="#" class="docopy-video-banner-group button"><span class="icon add"></span><?php _e( 'Add Video Banner', '' ); ?></a></p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"><?php
                                        if ( $disableHistoryTable == 1 ) {
                                            echo '<div class="clear"></div>';
                                            echo '<div class="inlineMessageError"><strong>' . translate( 'Impressions and clicks counters for both campaigns and banners will not be accurate due to history functionality disabled.' ) . '</strong></div>';
                                            echo '<div class="clear"></div>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                if ( $currentCampaignType == '3' && isset( $fields_data[ 'selected_banner_id' ] ) && !empty( $fields_data[ 'selected_banner_id' ] ) ) {
//                                    echo '<script type="text/javascript">
//                                            jQuery(document).ready(function(){
//                                                    CM_AdsChanger.select_ad(jQuery(\'#video_ad_list input[type="hidden"][value="' . $fields_data[ 'selected_banner_id' ] . '"]\').parents(".single_element_wrapper"));
//                                            })
//                                      </script>';
                                }
                                ?>
                            </tbody>
                            <tbody id="floating_ads_part" class="campaign_type_part">
                                <tr>
                                    <?php
                                    $currentBannerDisplayMethod = $currentCampaignType == '4' && isset( $fields_data[ 'banner_display_method' ] ) ? $fields_data[ 'banner_display_method' ] : '';
                                    ?>
                                    <td>
                                        <label class="ac-form-label" for="use_selected_banner"><?php echo __( 'Display Method' ); ?> </label><div class="field_help" title="<?php echo $label_descriptions[ 'banner_display_method' ] ?>"></div>
                                    </td>
                                    <td>
                                        <input type="radio" aria-required="true" name="banner_display_method" id="use_selected_banner" value="selected"  <?php checked( 'selected', $currentBannerDisplayMethod ); ?> />&nbsp;<label for="use_selected_banner">Selected Banner</label><br/>
                                        <input type="radio" aria-required="true" name="banner_display_method" id="use_random_banner" value="random" <?php checked( 'random', $currentBannerDisplayMethod ); ?> />&nbsp;<label for="use_random_banner">Random Banner</label><br/>
                                        <input type="hidden" name="selected_html_ad" value="" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="width" ><?php echo __( 'Width' ); ?> </label><div class="field_help" title="<?php echo $label_descriptions[ 'campaign_width' ] ?>"></div>
                                    </td>
                                    <td>
                                        <input size="20" type="text" placeholder="auto" aria-required="false" value="<?php echo (isset( $fields_data[ 'width' ] ) ? $fields_data[ 'width' ] : '') ?>" name="width" id="width" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="height" ><?php echo __( 'Height' ); ?> </label><div class="field_help" title="<?php echo $label_descriptions[ 'campaign_height' ] ?>"></div>
                                    </td>
                                    <td>
                                        <input size="20" type="text" placeholder="auto" aria-required="false" value="<?php echo (isset( $fields_data[ 'height' ] ) ? $fields_data[ 'height' ] : '') ?>" name="height" id="height" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="background" ><?php echo __( 'Background Color' ); ?> </label><div class="field_help" title="<?php echo $label_descriptions[ 'background' ] ?>"></div>
                                    </td>
                                    <td>
                                        <input size="20" type="text" placeholder="#FFFFFF" aria-required="false" value="<?php echo (isset( $fields_data[ 'background' ] ) ? $fields_data[ 'background' ] : '') ?>" name="background" id="background" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="seconds_to_show" ><?php echo __( 'Delay To Show' ); ?> </label><div class="field_help" title="<?php echo $label_descriptions[ 'seconds_to_show' ] ?>"></div>
                                    </td>
                                    <td>
                                        <input size="20" type="text" placeholder="0" aria-required="false" value="<?php echo (isset( $fields_data[ 'seconds_to_show' ] ) ? $fields_data[ 'seconds_to_show' ] : '') ?>" name="seconds_to_show" id="seconds_to_show" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="banner_edges" ><?php echo __( 'Banner Shape' ); ?> </label><div class="field_help" title="<?php echo $label_descriptions[ 'banner_edges' ] ?>"></div>
                                    </td>
                                    <td>
                                        <select id="banner_edges" name="banner_edges">
                                            <?php
                                            $bannerEdges                = isset( $fields_data[ 'banner_edges' ] ) ? $fields_data[ 'banner_edges' ] : '';
                                            ?>
                                            <option value="rounded" <?php selected( 'rounded', $bannerEdges ) ?> ><?php echo __( 'Rounded Edges' ); ?></option>
                                            <option value="sharp" <?php selected( 'sharp', $bannerEdges ) ?> ><?php echo __( 'Sharp Edges' ); ?></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="show_effect" ><?php echo __( 'Show Effect' ); ?> </label><div class="field_help" title="<?php echo $label_descriptions[ 'show_effect' ] ?>"></div>
                                    </td>
                                    <td>
                                        <select id="show_effect" name="show_effect">
                                            <?php
                                            $show_effect                = isset( $fields_data[ 'show_effect' ] ) ? $fields_data[ 'show_effect' ] : '';
                                            ?>
                                            <option value="popin" <?php selected( 'popin', $show_effect ) ?> ><?php echo __( 'Pop In' ); ?></option>
                                            <option value="bounce" <?php selected( 'bounce', $show_effect ) ?> ><?php echo __( 'Bounce' ); ?></option>
                                            <option value="shake" <?php selected( 'shake', $show_effect ) ?> ><?php echo __( 'Shake' ); ?></option>
                                            <option value="flash" <?php selected( 'flash', $show_effect ) ?> ><?php echo __( 'Flash' ); ?></option>
                                            <option value="tada" <?php selected( 'tada', $show_effect ) ?> ><?php echo __( 'Tada' ); ?></option>
                                            <option value="swing" <?php selected( 'swing', $show_effect ) ?> ><?php echo __( 'Swing' ); ?></option>
                                            <option value="rotateIn" <?php selected( 'rotateIn', $show_effect ) ?> ><?php echo __( 'Rotate In' ); ?></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="user_show_method" ><?php echo __( 'Interval' ); ?> </label><div class="field_help" title="<?php echo $label_descriptions[ 'user_show_method' ] ?>"></div>
                                    </td>
                                    <td>
                                        <select id="user_show_method" name="user_show_method">
                                            <?php
                                            $userShowMethod             = isset( $fields_data[ 'user_show_method' ] ) ? $fields_data[ 'user_show_method' ] : '';
                                            ?>
                                            <option value="always" <?php selected( 'always', $userShowMethod ) ?> ><?php echo __( 'Every Time Page Loads' ); ?></option>
                                            <option value="once" <?php selected( 'once', $userShowMethod ) ?> ><?php echo __( 'Only First Time Page Loads' ); ?></option>
                                        </select>
                                        <span id="resetFloatingBannerCookieContainer">
                                            <div class="field_help" title="<?php echo $label_descriptions[ 'reset_floating_banner_cookie_time' ] ?>"></div>
                                            <label for="reset_floating_banner_cookie_time">Days Until Interval Reset</label>
                                            <input size="2" type="text" aria-required="false" value="<?php echo (isset( $fields_data[ 'reset_floating_banner_cookie_time' ] ) ? $fields_data[ 'reset_floating_banner_cookie_time' ] : '') ?>" name="reset_floating_banner_cookie_time" id="reset_floating_banner_cookie_time" />
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="underlay_type" ><?php echo __( 'Banner Underlay Type' ); ?> </label><div class="field_help" title="<?php echo $label_descriptions[ 'underlay_type' ] ?>"></div>
                                    </td>
                                    <td>
                                        <select id="underlay_type" name="underlay_type">
                                            <?php
                                            $underlay_type              = isset( $fields_data[ 'underlay_type' ] ) ? $fields_data[ 'underlay_type' ] : 'dark';
                                            ?>
                                            <option value="dark" <?php selected( 'dark', $underlay_type ) ?> ><?php echo __( 'Dark Underlay' ); ?></option>
                                            <option value="light" <?php selected( 'light', $underlay_type ) ?> ><?php echo __( 'Light Underlay' ); ?></option>
                                            <option value="no" <?php selected( 'no', $underlay_type ) ?> ><?php echo __( 'No Underlay' ); ?></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="addesigner" ><?php echo __( 'AdDesigner' ); ?> </label><div class="field_help" title="<?php echo $label_descriptions[ 'campaign_addesigner' ] ?>"></div>
                                    </td>
                                    <td>
                                        <a href="#" class="button adddesigner_trigger" id="adddesigner_trigger"><?php echo __( 'Show AdDesigner' ); ?></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="campaign_images"><?php echo __( 'Campaign HTML Ads' ); ?> </label>
                                        <div class="field_help" title="<?php echo $label_descriptions[ 'campaign_html_ads' ] ?>"></div>
                                    </td>
                                    <td>
                                        <div id="container">
                                            <div id="floating_html_ad_list" class="cmac-clear cmac-textareas cmac-group">
                                                <?php
                                                wp_print_styles( 'editor-buttons' );

                                                ob_start();
                                                wp_editor( '', 'content', array(
                                                    'dfw'           => true,
                                                    'editor_height' => 1,
                                                    'tinymce'       => array(
                                                        'resize'             => true,
                                                        'add_unload_trigger' => false,
                                                        'relative_urls'      => false,
                                                        'remove_script_host' => false,
                                                        'convert_urls'       => false
                                                    )
                                                ) );
                                                $content = ob_get_contents();
                                                ob_end_clean();
                                                $index   = '';
                                                if ( isset( $fields_data[ 'meta' ] ) ) {
                                                    $i = 1;
                                                    foreach ( $fields_data[ 'meta' ] as $index => $meta ) {
                                                        if ( !isset( $meta[ 'html' ] ) ) {
                                                            continue;
                                                        }
                                                        $impressions = ($fields_data[ 'banner_impressions_cnt' ][ $index ] ? $fields_data[ 'banner_impressions_cnt' ][ $index ] : 0);
                                                        $clicks      = ($fields_data[ 'banner_clicks_cnt' ][ $index ] ? $fields_data[ 'banner_clicks_cnt' ][ $index ] : 0);
                                                        $weight      = ($fields_data[ 'banner_weight' ][ $index ] ? $fields_data[ 'banner_weight' ][ $index ] : 0);

                                                        if ( $impressions != 0 ) {
                                                            $clicks_rate = $clicks / $impressions * 100;
                                                            if ( (int) $clicks_rate != (float) $clicks_rate ) {
                                                                $clicks_rate = number_format( $clicks_rate, 2, '.', '' );
                                                            }
                                                        } else {
                                                            $clicks_rate = 0;
                                                        }

                                                        $settings = array( 'textarea_name' => 'html_ads[]', 'id' => 'html-ad-text_' . $index, 'teeny' => true, 'rows' => 10 );

                                                        $isSelected = '';
                                                        if ( !empty( $fields_data[ 'selected_banner_id' ] ) && $index == $fields_data[ 'selected_banner_id' ] ) {
                                                            $isSelected = 'selected';
                                                        }
                                                        echo '<div class="html_ad_wrapper single_element_wrapper cmac_group-html-banner-group ' . $isSelected . '">';
                                                        echo '<div class="group-control dodelete" title="' . __( 'Click to remove "HTML Banner"', '' ) . '"></div>';
                                                        echo '<input type="hidden" name="banner_ids[]" value="' . $index . '" />';

                                                        echo '<div class="clicks_and_impressions">';
                                                        echo '<div class="impressions">' . $impressions . '</div>';
                                                        echo '<div class="clicks">' . $clicks . '</div>';
                                                        echo '<div class="percent">' . $clicks_rate . '</div>';
                                                        echo '</div>';
                                                        ?>
                                                        <div class="customEditor wp-core-ui wp-editor-wrap <?php echo 'tmce-active'; ?>">
                                                            <div class="wp-editor-tools hide-if-no-js">

                                                                <div class="wp-media-buttons custom_upload_buttons">
                                                                    <?php do_action( 'media_buttons', 'mceEditor-' . $i ); ?>
                                                                </div>

                                                                <div class="wp-editor-tabs">
                                                                    <a data-mode="html" class="wp-switch-editor switch-html"> <?php _ex( 'Text', 'Name for the Text editor tab (formerly HTML)' ); ?></a>
                                                                    <a data-mode="tmce" class="wp-switch-editor switch-tmce"><?php _e( 'Visual' ); ?></a>
                                                                </div>

                                                            </div><!-- .wp-editor-tools -->
                                                            <div class="wp-editor-container">
                                                                <textarea name="html_ads[]" rows="5" class="wp-editor-area"><?php echo esc_html( stripslashes( $meta[ 'html' ] ) ); ?></textarea>
                                                            </div>
                                                        </div>
                                                        <?php
                                                        echo '<div><label for="banner_weight' . $index . '">Weight</label><div class="field_help" title="' . $label_descriptions[ 'banner_weight' ] . '"></div><input type="text" name="banner_weight[]" id="banner_weight' . $index . '" maxlength="4" value="' . (isset( $fields_data[ 'banner_weight' ][ $index ] ) && is_numeric( $fields_data[ 'banner_weight' ][ $index ] ) ? $fields_data[ 'banner_weight' ][ $index ] : '0') . '" class="num_field" /></div>';
                                                        echo '<div><label for="filename' . $index . '">Banner Name</label><div class="field_help" title="' . $label_descriptions[ 'banner_name_for_statistics' ] . '"></div><input type="text" name="filename[]" id="filename' . $index . '" maxlength="150" value="' . (isset( $fields_data[ 'floating_ad_banner_filenames' ][ $index ] ) ? $fields_data[ 'floating_ad_banner_filenames' ][ $index ] : '') . '" /></div>';
                                                        echo '</div>';
                                                        $i++;
                                                    }
                                                }
                                                ?>

                                                <div class="html_ad_wrapper single_element_wrapper cmac_group-html-banner-group tocopy last">
                                                    <div class="group-control dodelete" title="<?php _e( 'Click to remove "HTML Banner"', '' ); ?>"></div>

                                                    <input type="hidden" name="banner_ids[]" value="new" />
                                                    <div class="clicks_and_impressions">
                                                        <div class="impressions">0</div>
                                                        <div class="clicks">0</div>
                                                        <div class="percent">0</div>
                                                    </div>

                                                    <?php
                                                    global $wp_version;
                                                    if ( version_compare( $wp_version, '4.3', '<' ) ) {
                                                        add_filter( 'the_editor_content', 'wp_richedit_pre' );
                                                    } else {
                                                        add_filter( 'the_editor_content', 'format_for_editor' );
                                                    }
                                                    $switch_class = 'tmce-active';
                                                    ?>

                                                    <div class="customEditor wp-core-ui wp-editor-wrap <?php echo 'tmce-active'; ?>">
                                                        <div class="wp-editor-tools hide-if-no-js">

                                                            <div class="wp-media-buttons custom_upload_buttons">
                                                                <?php do_action( 'media_buttons' ); ?>
                                                            </div>

                                                            <div class="wp-editor-tabs">
                                                                <a data-mode="html" class="wp-switch-editor switch-html"> <?php _ex( 'Text', 'Name for the Text editor tab (formerly HTML)' ); ?></a>
                                                                <a data-mode="tmce" class="wp-switch-editor switch-tmce"><?php _e( 'Visual' ); ?></a>
                                                            </div>

                                                        </div><!-- .wp-editor-tools -->
                                                        <div class="wp-editor-container">
                                                            <textarea name="html_ads[]" rows="5" class="wp-editor-area"></textarea>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label for="banner_weight">Weight</label>
                                                        <div class="field_help" title="<?php echo $label_descriptions[ 'banner_weight' ] ?>"></div>
                                                        <input type="text" name="banner_weight[]" id="banner_weight" maxlength="4" value="0" class="num_field" />
                                                    </div>
                                                    <?php
                                                    echo '<div><label for="filename">Banner Name</label><div class="field_help" title="' . $label_descriptions[ 'banner_name_for_statistics' ] . '"></div><input type="text" name="filename[]" id="filename" maxlength="150" value="" /></div>';
                                                    ?>
                                                    <!--<div>
                                                            <label for="banner_link">Target URL</label>
                                                            <div class="field_help" title="<?php echo $label_descriptions[ 'banner_link' ] ?>"></div>
                                                            <input type="text" name="banner_link[]" id="banner_link<?php echo $index ?>" maxlength="150" value="" />
                                                    </div>-->
                                                </div>

                                                <p><a href="#" class="docopy-html-banner-group button"><span class="icon add"></span><?php _e( 'Add HTML Banner', '' ); ?></a></p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"><?php
                                        if ( $disableHistoryTable == 1 ) {
                                            echo '<div class="clear"></div>';
                                            echo '<div class="inlineMessageError"><strong>' . translate( 'Impressions and clicks counters for both campaigns and banners will not be accurate due to history functionality disabled.' ) . '</strong></div>';
                                            echo '<div class="clear"></div>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                if ( ($currentCampaignType == '1' || $currentCampaignType == '4') && isset( $fields_data[ 'selected_banner_id' ] ) && !empty( $fields_data[ 'selected_banner_id' ] ) ) {
//                                    echo '<script type="text/javascript">
//                                            jQuery(document).ready(function(){
//                                                    CM_AdsChanger.select_ad(jQuery(\'#floating_html_ad_list input[type="hidden"][value="' . $fields_data[ 'selected_banner_id' ] . '"]\').parents(".single_element_wrapper"));
//                                            })
//                                      </script>';
                                }
                                ?>
                            </tbody>
                            <tbody id="floating_bottom_ads_part" class="campaign_type_part">
                                <tr>
                                    <?php
                                    $currentBannerDisplayMethod = $currentCampaignType == '5' && isset( $fields_data[ 'banner_display_method' ] ) ? $fields_data[ 'banner_display_method' ] : '';
                                    ?>
                                    <td>
                                        <label class="ac-form-label" for="use_selected_banner"><?php echo __( 'Display Method' ); ?> </label><div class="field_help" title="<?php echo $label_descriptions[ 'banner_display_method' ] ?>"></div>
                                    </td>
                                    <td>
                                        <input type="radio" aria-required="true" name="banner_display_method" id="use_selected_banner" value="selected"  <?php checked( 'selected', $currentBannerDisplayMethod ); ?> />&nbsp;<label for="use_selected_banner">Selected Banner</label><br/>
                                        <input type="radio" aria-required="true" name="banner_display_method" id="use_random_banner" value="random" <?php checked( 'random', $currentBannerDisplayMethod ); ?> />&nbsp;<label for="use_random_banner">Random Banner</label><br/>
                                        <input type="hidden" name="selected_html_ad" value="" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="width" ><?php echo __( 'Width' ); ?> </label><div class="field_help" title="<?php echo $label_descriptions[ 'campaign_width' ] ?>"></div>
                                    </td>
                                    <td>
                                        <input size="20" type="text" placeholder="auto" aria-required="false" value="<?php echo (isset( $fields_data[ 'width' ] ) ? $fields_data[ 'width' ] : '') ?>" name="width" id="width" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="height" ><?php echo __( 'Height' ); ?> </label><div class="field_help" title="<?php echo $label_descriptions[ 'campaign_height' ] ?>"></div>
                                    </td>
                                    <td>
                                        <input size="20" type="text" placeholder="auto" aria-required="false" value="<?php echo (isset( $fields_data[ 'height' ] ) ? $fields_data[ 'height' ] : '') ?>" name="height" id="height" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="background" ><?php echo __( 'Background Color' ); ?> </label><div class="field_help" title="<?php echo $label_descriptions[ 'background' ] ?>"></div>
                                    </td>
                                    <td>
                                        <input size="20" type="text" placeholder="#FFFFFF" aria-required="false" value="<?php echo (isset( $fields_data[ 'background' ] ) ? $fields_data[ 'background' ] : '') ?>" name="background" id="background" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="seconds_to_show" ><?php echo __( 'Delay To Show' ); ?> </label><div class="field_help" title="<?php echo $label_descriptions[ 'seconds_to_show' ] ?>"></div>
                                    </td>
                                    <td>
                                        <input size="20" type="text" placeholder="0" aria-required="false" value="<?php echo (isset( $fields_data[ 'seconds_to_show' ] ) ? $fields_data[ 'seconds_to_show' ] : '') ?>" name="seconds_to_show" id="seconds_to_show" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="banner_edges" ><?php echo __( 'Banner Shape' ); ?> </label><div class="field_help" title="<?php echo $label_descriptions[ 'banner_edges' ] ?>"></div>
                                    </td>
                                    <td>
                                        <select id="banner_edges" name="banner_edges">
                                            <?php
                                            $bannerEdges                = isset( $fields_data[ 'banner_edges' ] ) ? $fields_data[ 'banner_edges' ] : '';
                                            ?>
                                            <option value="rounded" <?php selected( 'rounded', $bannerEdges ) ?> ><?php echo __( 'Rounded Edges' ); ?></option>
                                            <option value="sharp" <?php selected( 'sharp', $bannerEdges ) ?> ><?php echo __( 'Sharp Edges' ); ?></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="show_effect" ><?php echo __( 'Show Effect' ); ?> </label><div class="field_help" title="<?php echo $label_descriptions[ 'show_effect' ] ?>"></div>
                                    </td>
                                    <td>
                                        <select id="show_effect" name="show_effect">
                                            <?php
                                            $show_effect                = isset( $fields_data[ 'show_effect' ] ) ? $fields_data[ 'show_effect' ] : '';
                                            ?>
                                            <option value="popin" <?php selected( 'popin', $show_effect ) ?> ><?php echo __( 'Pop In' ); ?></option>
                                            <option value="bounce" <?php selected( 'bounce', $show_effect ) ?> ><?php echo __( 'Bounce' ); ?></option>
                                            <option value="shake" <?php selected( 'shake', $show_effect ) ?> ><?php echo __( 'Shake' ); ?></option>
                                            <option value="flash" <?php selected( 'flash', $show_effect ) ?> ><?php echo __( 'Flash' ); ?></option>
                                            <option value="tada" <?php selected( 'tada', $show_effect ) ?> ><?php echo __( 'Tada' ); ?></option>
                                            <option value="swing" <?php selected( 'swing', $show_effect ) ?> ><?php echo __( 'Swing' ); ?></option>
                                            <option value="rotateIn" <?php selected( 'rotateIn', $show_effect ) ?> ><?php echo __( 'Rotate In' ); ?></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="user_show_method" ><?php echo __( 'Interval' ); ?> </label><div class="field_help" title="<?php echo $label_descriptions[ 'user_show_method' ] ?>"></div>
                                    </td>
                                    <td>
                                        <select id="user_show_method-flying-bottom" name="user_show_method">
                                            <?php
                                            $userShowMethod             = isset( $fields_data[ 'user_show_method' ] ) ? $fields_data[ 'user_show_method' ] : '';
                                            ?>
                                            <option value="always" <?php selected( 'always', $userShowMethod ) ?> ><?php echo __( 'Every Time Page Loads' ); ?></option>
                                            <option value="once" <?php selected( 'once', $userShowMethod ) ?> ><?php echo __( 'Only First Time Page Loads' ); ?></option>
                                        </select>
                                        <span id="resetFloatingBottomBannerCookieContainer">
                                            <div class="field_help" title="<?php echo $label_descriptions[ 'reset_floating_banner_cookie_time' ] ?>"></div>
                                            <label for="reset_floating_banner_cookie_time">Days Until Interval Reset</label>
                                            <input size="2" type="text" aria-required="false" value="<?php echo (isset( $fields_data[ 'reset_floating_banner_cookie_time' ] ) ? $fields_data[ 'reset_floating_banner_cookie_time' ] : '') ?>" name="reset_floating_banner_cookie_time" id="reset_floating_banner_cookie_time" />
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="addesigner" ><?php echo __( 'AdDesigner' ); ?> </label><div class="field_help" title="<?php echo $label_descriptions[ 'campaign_addesigner' ] ?>"></div>
                                    </td>
                                    <td>
                                        <a href="#" class="button adddesigner_trigger" id="adddesigner_trigger"><?php echo __( 'Show AdDesigner' ); ?></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="ac-form-label" for="campaign_images"><?php echo __( 'Campaign HTML Ads' ); ?> </label>
                                        <div class="field_help" title="<?php echo $label_descriptions[ 'campaign_html_ads' ] ?>"></div>
                                    </td>
                                    <td>
                                        <div id="container">
                                            <div id="floating_bottom_html_ad_list" class="cmac-clear cmac-textareas cmac-group">
                                                <?php
                                                wp_print_styles( 'editor-buttons' );

                                                ob_start();
                                                wp_editor( '', 'content', array(
                                                    'dfw'           => true,
                                                    'editor_height' => 1,
                                                    'tinymce'       => array(
                                                        'resize'             => true,
                                                        'add_unload_trigger' => false,
                                                        'relative_urls'      => false,
                                                        'remove_script_host' => false,
                                                        'convert_urls'       => false
                                                    )
                                                ) );
                                                $content = ob_get_contents();
                                                ob_end_clean();
                                                $index   = '';
                                                if ( isset( $fields_data[ 'meta' ] ) ) {
                                                    foreach ( $fields_data[ 'meta' ] as $index => $meta ) {
                                                        if ( !isset( $meta[ 'html' ] ) ) {
                                                            continue;
                                                        }
                                                        $impressions = ($fields_data[ 'banner_impressions_cnt' ][ $index ] ? $fields_data[ 'banner_impressions_cnt' ][ $index ] : 0);
                                                        $clicks      = ($fields_data[ 'banner_clicks_cnt' ][ $index ] ? $fields_data[ 'banner_clicks_cnt' ][ $index ] : 0);
                                                        $weight      = ($fields_data[ 'banner_weight' ][ $index ] ? $fields_data[ 'banner_weight' ][ $index ] : 0);

                                                        if ( $impressions != 0 ) {
                                                            $clicks_rate = $clicks / $impressions * 100;
                                                            if ( (int) $clicks_rate != (float) $clicks_rate ) {
                                                                $clicks_rate = number_format( $clicks_rate, 2, '.', '' );
                                                            }
                                                        } else {
                                                            $clicks_rate = 0;
                                                        }

                                                        $settings = array( 'textarea_name' => 'html_ads[]', 'id' => 'html-ad-text_' . $index, 'teeny' => true, 'rows' => 10 );

                                                        $isSelected = '';
                                                        if ( !empty( $fields_data[ 'selected_banner_id' ] ) && $index == $fields_data[ 'selected_banner_id' ] ) {
                                                            $isSelected = 'selected';
                                                        }

                                                        echo '<div class="html_ad_wrapper single_element_wrapper cmac_group-html-banner-group ' . $isSelected . '">';
                                                        echo '<div class="group-control dodelete" title="' . __( 'Click to remove "HTML Banner"', '' ) . '"></div>';
                                                        echo '<input type="hidden" name="banner_ids[]" value="' . $index . '" />';

                                                        echo '<div class="clicks_and_impressions">';
                                                        echo '<div class="impressions">' . $impressions . '</div>';
                                                        echo '<div class="clicks">' . $clicks . '</div>';
                                                        echo '<div class="percent">' . $clicks_rate . '</div>';
                                                        echo '</div>';
                                                        ?>
                                                        <div class="customEditor wp-core-ui wp-editor-wrap <?php echo 'tmce-active'; ?>">
                                                            <div class="wp-editor-tools hide-if-no-js">

                                                                <div class="wp-media-buttons custom_upload_buttons">
                                                                    <?php do_action( 'media_buttons' ); ?>
                                                                </div>

                                                                <div class="wp-editor-tabs">
                                                                    <a data-mode="html" class="wp-switch-editor switch-html"> <?php _ex( 'Text', 'Name for the Text editor tab (formerly HTML)' ); ?></a>
                                                                    <a data-mode="tmce" class="wp-switch-editor switch-tmce"><?php _e( 'Visual' ); ?></a>
                                                                </div>

                                                            </div><!-- .wp-editor-tools -->
                                                            <div class="wp-editor-container">
                                                                <textarea name="html_ads[]" rows="5" class="wp-editor-area"><?php echo esc_html( stripslashes( $meta[ 'html' ] ) ); ?></textarea>
                                                            </div>
                                                        </div>
                                                        <?php
                                                        echo '<div><label for="banner_weight' . $index . '">Weight</label><div class="field_help" title="' . $label_descriptions[ 'banner_weight' ] . '"></div><input type="text" name="banner_weight[]" id="banner_weight' . $index . '" maxlength="4" value="' . (isset( $fields_data[ 'banner_weight' ][ $index ] ) && is_numeric( $fields_data[ 'banner_weight' ][ $index ] ) ? $fields_data[ 'banner_weight' ][ $index ] : '0') . '" class="num_field" /></div>';
                                                        echo '<div><label for="filename' . $index . '">Banner Name</label><div class="field_help" title="' . $label_descriptions[ 'banner_name_for_statistics' ] . '"></div><input type="text" name="filename[]" id="filename' . $index . '" maxlength="150" value="' . (isset( $fields_data[ 'floating_ad_banner_filenames' ][ $index ] ) ? $fields_data[ 'floating_ad_banner_filenames' ][ $index ] : '') . '" /></div>';
                                                        echo '</div>';
                                                    }
                                                }
                                                ?>

                                                <div class="html_ad_wrapper single_element_wrapper cmac_group-html-banner-group tocopy last">
                                                    <div class="group-control dodelete" title="<?php _e( 'Click to remove "HTML Banner"', '' ); ?>"></div>

                                                    <input type="hidden" name="banner_ids[]" value="new" />
                                                    <div class="clicks_and_impressions">
                                                        <div class="impressions">0</div>
                                                        <div class="clicks">0</div>
                                                        <div class="percent">0</div>
                                                    </div>

                                                    <?php
                                                    global $wp_version;
                                                    if ( version_compare( $wp_version, '4.3', '<' ) ) {
                                                        add_filter( 'the_editor_content', 'wp_richedit_pre' );
                                                    } else {
                                                        add_filter( 'the_editor_content', 'format_for_editor' );
                                                    }
                                                    $switch_class = 'tmce-active';
                                                    ?>

                                                    <div class="customEditor wp-core-ui wp-editor-wrap <?php echo 'tmce-active'; ?>">
                                                        <div class="wp-editor-tools hide-if-no-js">

                                                            <div class="wp-media-buttons custom_upload_buttons">
                                                                <?php do_action( 'media_buttons' ); ?>
                                                            </div>

                                                            <div class="wp-editor-tabs">
                                                                <a data-mode="html" class="wp-switch-editor switch-html"> <?php _ex( 'Text', 'Name for the Text editor tab (formerly HTML)' ); ?></a>
                                                                <a data-mode="tmce" class="wp-switch-editor switch-tmce"><?php _e( 'Visual' ); ?></a>
                                                            </div>

                                                        </div><!-- .wp-editor-tools -->
                                                        <div class="wp-editor-container">
                                                            <textarea name="html_ads[]" rows="5" class="wp-editor-area"></textarea>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label for="banner_weight">Weight</label>
                                                        <div class="field_help" title="<?php echo $label_descriptions[ 'banner_weight' ] ?>"></div>
                                                        <input type="text" name="banner_weight[]" id="banner_weight" maxlength="4" value="0" class="num_field" />
                                                    </div>
                                                    <?php
                                                    echo '<div><label for="filename">Banner Name</label><div class="field_help" title="' . $label_descriptions[ 'banner_name_for_statistics' ] . '"></div><input type="text" name="filename[]" id="filename" maxlength="150" value="" /></div>';
                                                    ?>
                                                    <!--<div>
                                                            <label for="banner_link">Target URL</label>
                                                            <div class="field_help" title="<?php echo $label_descriptions[ 'banner_link' ] ?>"></div>
                                                            <input type="text" name="banner_link[]" id="banner_link<?php echo $index ?>" maxlength="150" value="" />
                                                    </div>-->
                                                </div>

                                                <p><a href="#" class="docopy-html-banner-group button"><span class="icon add"></span><?php _e( 'Add HTML Banner', '' ); ?></a></p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"><?php
                                        if ( $disableHistoryTable == 1 ) {
                                            echo '<div class="clear"></div>';
                                            echo '<div class="inlineMessageError"><strong>' . translate( 'Impressions and clicks counters for both campaigns and banners will not be accurate due to history functionality disabled.' ) . '</strong></div>';
                                            echo '<div class="clear"></div>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                if ( ($currentCampaignType == '1' || $currentCampaignType == '5') && isset( $fields_data[ 'selected_banner_id' ] ) && !empty( $fields_data[ 'selected_banner_id' ] ) ) {
//                                    echo '<script type="text/javascript">
//                                            jQuery(document).ready(function(){
//                                                    CM_AdsChanger.select_ad(jQuery(\'#floating_bottom_html_ad_list input[type="hidden"][value="' . $fields_data[ 'selected_banner_id' ] . '"]\').parents(".single_element_wrapper"));
//                                            })
//                                      </script>';
                                }
                                ?>
                            </tbody>
                        </table>
                        <table cellspacing=0 cellpadding=0 border=0 id="periods_fields">
                            <tr>
                                <td>
                                    <label class="ac-form-label" for="active_dates">Activity Dates </label><div class="field_help" title="<?php echo $label_descriptions[ 'active_dates' ] ?>"></div>
                                </td>
                                <td>
                                    <div id="dates">
                                        <?php
                                        if ( isset( $fields_data[ 'date_from' ] ) && !empty( $fields_data[ 'date_from' ] ) )
                                            foreach ( $fields_data[ 'date_from' ] as $period_index => $date_from ) {
                                                echo '<div class="date_range_row">';
                                                echo '<input type="text" name="date_from[]" class="date" value="' . $date_from . '" />&nbsp;';
                                                echo '<input class="h_spinner ac_spinner" name="hours_from[]" value="' . $fields_data[ 'hours_from' ][ $period_index ] . '" />&nbsp;h&nbsp;';
                                                echo '<input class="m_spinner ac_spinner" name="mins_from[]" value="' . $fields_data[ 'mins_from' ][ $period_index ] . '" />&nbsp;m';
                                                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="' . ACS_PLUGIN_URL . '/assets/images/arrow_right.png' . '" style="vertical-align:bottom" />&nbsp;&nbsp;&nbsp;&nbsp;';
                                                echo '<input type="text" name="date_till[]" class="date" value="' . $fields_data[ 'date_till' ][ $period_index ] . '" />&nbsp;';
                                                echo '<input class="h_spinner ac_spinner" name="hours_to[]" value="' . $fields_data[ 'hours_to' ][ $period_index ] . '" />&nbsp;h&nbsp;';
                                                echo '<input class="m_spinner ac_spinner" name="mins_to[]" value="' . $fields_data[ 'mins_to' ][ $period_index ] . '" />&nbsp;m&nbsp;';
                                                echo '<a href="#" class="delete_link"><img src="' . ACS_PLUGIN_URL . '/assets/images/close.png' . '" /></a>';
                                                echo '</div>';
                                            } else
                                            echo 'There are no limitations set';
                                        ?>
                                    </div>
                                    <a href="#" id="add_active_date_range" class="add_link"><img src="<?php echo ACS_PLUGIN_URL . '/assets/images/plus.png' ?>" /></a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="ac-form-label" for="active_dates">Activity Days </label><div class="field_help" title="<?php echo $label_descriptions[ 'active_week_days' ] ?>"></div>
                                </td>
                                <td>
                                    <div id="active_week_days">
                                        <div>
                                            <input type="checkbox" name="active_week_days[]" value="Sun" id="sun" <?php echo (isset( $fields_data[ 'active_week_days' ] ) && in_array( 'Sun', $fields_data[ 'active_week_days' ] ) ? 'checked=checked' : '') ?> />&nbsp;<label for="sun" >Sunday</label>
                                        </div>
                                        <div>
                                            <input type="checkbox" name="active_week_days[]" value="Mon" id="mon" <?php echo (isset( $fields_data[ 'active_week_days' ] ) && in_array( 'Mon', $fields_data[ 'active_week_days' ] ) ? 'checked=checked' : '') ?> />&nbsp;<label for="mon" >Monday</label>
                                        </div>
                                        <div>
                                            <input type="checkbox" name="active_week_days[]" value="Tue" id="tue" <?php echo (isset( $fields_data[ 'active_week_days' ] ) && in_array( 'Tue', $fields_data[ 'active_week_days' ] ) ? 'checked=checked' : '') ?> />&nbsp;<label for="tue" >Tuesday</label>
                                        </div>
                                        <div>
                                            <input type="checkbox" name="active_week_days[]" value="Wed" id="wed" <?php echo (isset( $fields_data[ 'active_week_days' ] ) && in_array( 'Wed', $fields_data[ 'active_week_days' ] ) ? 'checked=checked' : '') ?> />&nbsp;<label for="wed" >Wednesday</label>
                                        </div>
                                        <div>
                                            <input type="checkbox" name="active_week_days[]" value="Thu" id="thu" <?php echo (isset( $fields_data[ 'active_week_days' ] ) && in_array( 'Thu', $fields_data[ 'active_week_days' ] ) ? 'checked=checked' : '') ?> />&nbsp;<label for="thu" >Thursday</label>
                                        </div>
                                        <div>
                                            <input type="checkbox" name="active_week_days[]" value="Fri" id="fri" <?php echo (isset( $fields_data[ 'active_week_days' ] ) && in_array( 'Fri', $fields_data[ 'active_week_days' ] ) ? 'checked=checked' : '') ?> />&nbsp;<label for="fri" >Friday</label>
                                        </div>
                                        <div>
                                            <input type="checkbox" name="active_week_days[]" value="Sat" id="sat" <?php echo (isset( $fields_data[ 'active_week_days' ] ) && in_array( 'Sat', $fields_data[ 'active_week_days' ] ) ? 'checked=checked' : '') ?> />&nbsp;<label for="sat" >Saturday</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="right">
                        <input type="submit" value="<?php echo (isset( $fields_data[ 'campaign_id' ] ) ? 'Save' : 'Add') ?>" name="submit" id="submit_button">
                    </div>
                    <?php wp_nonce_field( 'CM_ADCHANGER_CAMPAIGN_SETTINGS', 'campaign_settings_noncename' ); ?>
                </form>
            </div>
        </div>
    </div>
    <iframe src="<?php echo bloginfo( 'wpurl' ) . '?acs_action=get_addesigner' ?>" id="cmac_addesigner_container"></iframe>
    <?php


endif;