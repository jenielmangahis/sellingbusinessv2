<?php
/**
 * CM Ad Changer
 *
 * @author CreativeMinds (http://ad-changer.cminds.com)
 * @copyright Copyright (c) 2013, CreativeMinds
 */
if( !isset($_GET['acs_admin_action']) )
{
    $_GET['acs_admin_action'] = '';
}
?>

<script type="text/javascript">
    var base_url = '<?php echo get_bloginfo('wpurl') ?>';
    var plugin_url = '<?php echo ACS_PLUGIN_URL ?>';
    var upload_tmp_path = '<?php echo cmac_get_upload_url() . AC_TMP_UPLOAD_PATH; ?>';
    var banners_limit = <?php echo BANNERS_PER_CAMPAIGN_LIMIT; ?>;
    var banner_variations_limit = <?php echo BANNER_VARIATIONS_LIMIT; ?>;
    var next_banner_index = 0;
    var label_descriptions = new Object();
    label_descriptions.banner_title = '<?php echo $label_descriptions['banner_title']; ?>';
    label_descriptions.banner_title_tag = '<?php echo $label_descriptions['banner_title_tag']; ?>';
    label_descriptions.banner_alt_tag = '<?php echo $label_descriptions['banner_alt_tag']; ?>';
    label_descriptions.banner_link = '<?php echo $label_descriptions['banner_link']; ?>';
    label_descriptions.banner_weight = '<?php echo $label_descriptions['banner_weight']; ?>';</script>

<div class="wrap ad_changer">
    <h2><?php echo $plugin_data['Name']; ?> : Groups</h2>

    <?php
    ac_top_menu();
    if( isset($errors) && !empty($errors) )
    {
        ?>
        <ul class="ac_error cmac-clear">
            <?php
            foreach($errors as $error) echo '<li>' . $error . '</li>';
            ?>
        </ul>
        <?php
    }
    if( $success ) echo '<div class="ac_success cmac-clear">' . $success . '</div>';
    //workaround for error handling
    ?>
    <div id="errors_container" style="display: none;"><ul class="ac_error cmac-clear"><li id="errors_container_li"></li></ul></div>
    <div class="ac-edit-form">
        <input type="submit" value="Create new Group" class="right cmac-clear" id="new_group_button" />
        <div class="cmac-clear"></div>
        <?php
        if( !empty($groups) )
        {
            ?>

            <div class="groups_list_table_head">
                <div style="text-align: left !important;">Group Name</div>
                <div>Group ID</div>
                <div>Order</div>
                <div>Campaigns</div>
                <div>Created On</div>
                <div>Actions</div>
            </div>
            <div class="campaigns_list_scroll cmac-clear">
                <div id="groups_list" class="ads_list" cellspacing=0 cellpadding=0 border=0>
                    <?php
                    foreach($groups as $group)
                    {
                        ?>
                        <div class="row<?php echo isset($fields_data['group_id']) && $fields_data['group_id'] == $group->group_id ? ' selected_group' : '' ?>" group_id="<?php echo $group->group_id ?>">
                            <div class="ac_cell">
                                <a href="<?php echo get_bloginfo('wpurl') ?>/wp-admin/admin.php?page=ac_server_groups&action=edit&group_id=<?php echo $group->group_id ?>"><?php echo $group->description; ?></a>
                            </div>
                            <div class="ac_cell">
                                <?php echo $group->group_id; ?>
                            </div>
                            <div class="ac_cell">
                                <?php
                                switch($group->group_order)
                                {
                                    case '1':
                                        echo 'Random';
                                        break;
                                    case '2':
                                        echo 'Selected';
                                        break;
                                }
                                ?>
                            </div>
                            <div class="ac_cell">
                                <?php echo $group->campaigns_cnt; ?>
                            </div>
                            <div class="ac_cell">
                                <?php echo (($group->created_on != '0000-00-00 00:00:00')?(date("d-m-Y", strtotime($group->created_on))):(' --- ')); ?>
                            </div>
                            <div class="actions ac_cell">
                                <a href="<?php echo get_bloginfo('wpurl') ?>/wp-admin/admin.php?page=ac_server_groups&action=edit&group_id=<?php echo $group->group_id ?>"><img src="<?php echo ACS_PLUGIN_URL . '/assets/images/edit.png' ?>" alt="Edit Group" title="Edit Group" /></a>
                                <a href="<?php echo get_bloginfo('wpurl') ?>/wp-admin/admin.php?page=ac_server_groups&action=delete&group_id=<?php echo $group->group_id ?>" class="delete_campaign_link"><img src="<?php echo ACS_PLUGIN_URL . '/assets/images/trash.png' ?>" alt="Delete Group" title="Delete Group" /></a>
                                <a href="<?php echo get_bloginfo('wpurl') ?>/wp-admin/admin.php?page=ac_server_groups&action=duplicate&group_id=<?php echo $group->group_id ?>" ><img src="<?php echo ACS_PLUGIN_URL . '/assets/images/duplct.png' ?>" alt="Duplicate Group" title="Duplicate Group"  /></a>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
        }

        if( (isset($fields_data['group_id']) || (isset($_GET['acs_admin_action']) && $_GET['acs_admin_action'] == 'new_group' && empty($_POST)) ) ) :

            $commentText = (isset($fields_data['description']) ? esc_html(stripslashes($fields_data['description'])) : '');
            ?>

            <div class="ac-edit-form">
                <form id="campaign_form" class="cmac-clear ac-form" <?php echo (isset($fields_data['group_id']) || (isset($_GET['acs_admin_action']) && $_GET['acs_admin_action'] == 'new_group' && empty($_POST)) ? 'style="display:block !important"' : '') ?> method="post">
                    <?php
                    if( isset($fields_data['group_id']) )
                    {
                        echo '<input type="hidden" name="group_id" value="' . $fields_data['group_id'] . '" />';
                    }
                    ?>

                    <div class="right" style="margin-bottom: 5px;">
                        <input type="submit" value="<?php echo (isset($fields_data['group_id']) ? 'Save' : 'Add') ?>" name="submit" id="submit_button" class="right">
                    </div>

                    <div id="ac-fields" class="cmac-clear">
                        <table cellspacing=0 cellpadding=0 border=0 class="cmac-clear" id="campaign_fields" style="width:100%">
                            <tr>
                                <td>
                                    <label class="ac-form-label" for="description" class="cmac-clear" >Group Name </label><div class="field_help" title="<?php echo $label_descriptions['comment'] ?>"></div>
                                </td>
                                <td>
                                    <input type="text" maxlength="50" size="50" value="<?php echo $commentText; ?>" name="description" id="comment" value="<?php echo $commentText; ?>" />
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <label class="ac-form-label">Group Order </label><div class="field_help" title="<?php echo $label_descriptions['group_order'] ?>"></div>
                                </td>
                                <td>
                                    <label><input type="radio" aria-required="true" name="group_order" id="use_random" value="1" <?php if (!empty($fields_data['group_order'])){checked('1', $fields_data['group_order']);} ?> />&nbsp;Random</label><br/>
                                    <label><input type="radio" aria-required="true" name="group_order" id="use_ordered" value="2" <?php if (!empty($fields_data['group_order'])){checked('2', $fields_data['group_order']);} ?> />&nbsp;Selected</label><br/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="ac-form-label">Campaigns In The Group </label><div class="field_help" title="<?php echo $label_descriptions['group_campaigns'] ?>"></div>
                                </td>
                                <td>
                                    <div class="right">
                                        <select id="new_campaign_id" name="new_campaign_id">
                                            <?php
                                            if(empty($campaignsForSelect)){
                                                $campaignsForSelect = $campaigns;
                                            }
                                            foreach($campaignsForSelect as $campaign){
                                                echo '<option value="' . $campaign->campaign_id . '" > ' . $campaign->title . ' </option>';
                                            }
                                            ?>
                                        </select>
                                        <input type="submit" value="Add" name="submit" id="add_campaign_button">
                                    </div>
                                    <div class="cmac-clear"></div>
                                    <div class="campaigns_list_table_head">
                                        <div style="text-align: left !important;">Campaign Name</div>
                                        <div>Advertiser</div>
                                        <div>Campaign ID</div>
                                        <div>Campaign Type</div>
                                        <div>Weight</div>
                                        <div>Status</div>
                                        <div>Actions</div>
                                    </div>
                                    <div class="campaigns_list_scroll cmac-clear">
                                        <div id="campaigns_list" class="ads_list" cellspacing=0 cellpadding=0 border=0>
                                            <?php
                                            if( !empty($groupCampaigns) )
                                            {
                                                foreach($groupCampaigns as $campaign)
                                                {
                                                    ?>
                                                    <div class="row<?php echo isset($fields_data['campaign_id']) && $fields_data['campaign_id'] == $campaign->campaign_id ? ' selected_campaign' : '' ?>" campaign_id="<?php echo $campaign->campaign_id ?>">
                                                        <div class="ac_cell">
                                                            <a href="<?php echo get_bloginfo('wpurl') ?>/wp-admin/admin.php?page=ac_server_campaigns&action=edit&campaign_id=<?php echo $campaign->campaign_id ?>" class="field_tip" title="<?php echo $campaign->comment ?>"><?php echo $campaign->title; ?></a>
                                                        </div>
                                                        <div class="ac_cell">
                                                            <?php
                                                            if( isset($campaign->advertiser) && !empty($campaign->advertiser) ) echo $campaign->advertiser['name'];
                                                            ?>
                                                        </div>
                                                        <div class="ac_cell"><?php echo $campaign->campaign_id; ?></div>
                                                        <div class="ac_cell">
                                                            <?php
                                                            switch($campaign->banner_display_method)
                                                            {
                                                                case 'selected':
                                                                    echo 'Selected';
                                                                    break;
                                                                case 'random':
                                                                    echo 'Random';
                                                                    break;
                                                                case 'all':
                                                                    echo 'Rotated';
                                                                    break;
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="ac_cell"><a href="<?php echo get_bloginfo('wpurl') ?>/wp-admin/admin.php?page=ac_server_campaigns&action=edit&campaign_id=<?php echo $campaign->campaign_id ?>" title="Click to edit the Campaign Weight on the Campaign page"><?php echo $campaign->group_priority; ?></a></div>
                                                        <div class="ac_cell"><?php echo ($campaign->status == '1' ? 'Active' : 'Inactive') ?></div>
                                                        <div class="actions ac_cell">
                                                            <a href="<?php echo get_bloginfo('wpurl') ?>/wp-admin/admin.php?page=ac_server_groups&action=remove_campaign&group_id=<?php echo $group->group_id ?>&campaign_id=<?php echo $campaign->campaign_id ?>" class="delete_campaign_link"><img src="<?php echo ACS_PLUGIN_URL . '/assets/images/trash.png' ?>" alt="Remove Campaign" title="Remove Campaign" /></a>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="right">
                        <input type="submit" value="<?php echo (isset($fields_data['group_id']) ? 'Save' : 'Add') ?>" name="submit" id="submit_button">
                    </div>
                    <?php wp_nonce_field('CM_ADCHANGER_GROUPS_SETTINGS', 'groups_settings_noncename'); ?>
                </form>
            </div>
        </div>
    </div>
    <?php

endif;
