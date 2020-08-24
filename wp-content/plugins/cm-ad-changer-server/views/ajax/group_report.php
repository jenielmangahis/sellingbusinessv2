<?php
/**
 * CM Ad Changer
 *
 * @author CreativeMinds (http://ad-changer.cminds.com)
 * @copyright Copyright (c) 2014, CreativeMinds
 */
if(!empty($group_details)){
    $group_details_data = $group_details['data'];
    $counters = $group_details['counters'];
}
$message = '';
if(!empty($group_details['message'])){
    $message = $group_details['message'];
}
?>

<div id="ac_filter">
    <?php
    if(!empty($message)){
    ?>
        <div class="error"><p><strong><?php echo $message?></strong></p></div>
    <?php
    }
    ?>
    <div class="left">
        <select id="group_id">
            <?php
            if( $groups && !empty($groups) ) foreach($groups as $group) echo '<option value="' . $group->group_id . '" ' .
                    (isset($_GET['group_id']) && $_GET['group_id'] == $group->group_id ? 'selected=selected' : '') . ' >' .
                    $group->description .
                    '</option>';
            ?>
        </select>
    </div>
    <div class="left">
        <select id="group_id_month">
            <?php
            if( $months && !empty($months) ) foreach($months as $month) echo '<option value="' . $month . '" ' .
                    (isset($_GET['month']) && $_GET['month'] == $month ? 'selected=selected' : '') . ' >' .
                    $month .
                    '</option>';
            ?>
        </select>
    </div>
    <div>
        <input type="submit" value="Generate Report" id="generate_button_groups" action="<?php echo get_bloginfo('wpurl') . '/wp-admin/admin-ajax.php?action=acs_get_group_report&action2=get_group_details' ?>" />
    </div>
    <div>
        <input type="submit" value="Update Data" id="update_data_group" />
    </div>
</div>
<div class="cmac-clear"></div>
<?php
if( isset($group_details_data) && !empty($group_details_data) ){
    ?>
    <table class="month_details ads_list ac_statistics_day_details" cellspacing=0 cellpadding=3>
        <thead>
            <tr>
                <th class="left_head">Banner Name</th>
                <th class="middle_head">Campaign</th>
                <th class="middle_head">Clicks: <?php echo $counters['clicks']?></th>
                <th class="middle_head">Impressions: <?php echo $counters['impressions']?></th>
                <th class="right_head">Rate: <?php
                        if( $counters['impressions'] == 0 ) echo '0';
                        else echo round(($counters['clicks'] / $counters['impressions']) * 100);
                        ?>%
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach($group_details_data as $banner_title => $stats)
            {
                if( file_exists(cmac_get_upload_dir() . $stats['filename'])){
                    $filename = cmac_get_upload_dir() . $stats['filename'];
                    $info = pathinfo(cmac_get_upload_dir() . $stats['filename']);
                    if( file_exists(cmac_get_upload_dir() . $info['filename'] . BANNER_THUMB_WIDTH . 'x' . BANNER_THUMB_HEIGHT . '.' . $info['extension']) ){
                        $thumb_url = cmac_get_upload_url() . $info['filename'] . BANNER_THUMB_WIDTH . 'x' . BANNER_THUMB_HEIGHT . '.' . $info['extension'];
                    }
                    else{
                        $thumb_url = cmac_get_upload_url() . $stats['filename'];
                    }
                }else{
                    $thumb_url = '';
                }
                ?>
                <tr>
                    <td class="left_content" <?php if($stats['banner_type'] != 0):?> style="padding-left: 25px;" <?php endif; ?>>
                        <?php if($stats['banner_type'] == 0): ?>
                            <img src="<?php echo ACS_PLUGIN_URL . '/assets/images/mag-glass.png' ?>" class="banner_image_tooltip" title="<?php echo $thumb_url ?>"/>
                        <?php endif; ?>
                        <?php if($banner_title): ?>
                            <?php echo $banner_title ?>
                        <?php else: ?>
                            <span>Banner (id: <?php echo $stats['banner_id']; ?>)</span>
                        <?php endif; ?>
                    </td>
                    <td class="middle_content">
                        <span><?php echo $stats['campaign_title']; ?></span>
                    </td>
                    <td class="middle_content">
                        <?php echo $stats['clicks']; ?>
                    </td>
                    <td class="middle_content">
                        <?php echo $stats['impressions']; ?>
                    </td>
                    <td class="right_content">
                        <?php
                        if( $stats['impressions'] == 0 ) echo '0';
                        else echo round(($stats['clicks'] / $stats['impressions']) * 100);
                        ?>%
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
}
