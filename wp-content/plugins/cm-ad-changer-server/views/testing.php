<?php
/**
 * CM Ad Changer
 *
 * @author CreativeMinds (http://ad-changer.cminds.com)
 * @copyright Copyright (c) 2013, CreativeMinds
 */
?>

<div class="wrap ad_changer ac_testing">
    <h2><?php echo $plugin_data['Name']; ?> : Testing</h2>
    <?php
    ac_top_menu();
    ?>

    <div class="ac-edit-form cmac-clear">
        <form id="acs_settings_form" method="post">
            <!-- Test section -->
            <table cellspacing=3 cellpadding=0 border=0 id="testing_fields">
                <tr>
                    <td>
                        <label class="acc-form-label" for="acs_campaign_id" >ID: </label>
                    </td>
                    <td>
                        <input type="text" name="acs_campaign_id" id="acs_campaign_id" size="4" value="<?php echo isset($fields_data['acs_campaign_id']) ? $fields_data['acs_campaign_id'] : '' ?>" />
                    </td>
                    <td>
                        <input type="radio" name="type" value="campaign"<?php if(empty($fields_data['type']) || $fields_data['type'] == 'campaign'){echo ' checked';}?>>Campaign
                        <input type="radio" name="type" value="campaign_group"<?php if(!empty($fields_data['type']) && $fields_data['type'] == 'campaign_group'){echo ' checked';}?>>Campaign group
                    </td>
                </tr>

            </table>
            <input type="submit" value="Start Test" id="submit_button" style="float: left !important;">
        </form>
    </div>
    <?php
    if( isset($fields_data['acs_campaign_id']) && $fields_data['acs_campaign_id'] > 0 )
    {
        echo '<br/><br/><h3>Test Campaign Output</h3>';
        $content_sortcode = '[cm_ad_changer ' . ((!empty($fields_data['type']) && $fields_data['type'] == 'campaign_group')?('group_id'):('campaign_id')) . '="' . $fields_data['acs_campaign_id'] . '" debug="1"]';
        echo '<br>Preforming the following shortcode:<br> <strong>' . $content_sortcode . '</strong><br><br>';
        echo '<div><strong>Banners should appear below:</strong><br/>';
        echo do_shortcode($content_sortcode);
        echo '<div>';
    }
    ?>
</div>