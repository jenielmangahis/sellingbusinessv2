<?php
/**
 * CM Ad Changer
 *
 * @author CreativeMinds (http://ad-changer.cminds.com)
 * @copyright Copyright (c) 2014, CreativeMinds
 */
if(!empty($result)){
    $daysRangeData = $result['data'];
    $counters = $result['counters'];
}
$message = '';
if(!empty($result['message'])){
    $message = $result['message'];
} 
?>
<script type="text/javascript">
        jQuery(document).ready(function () {
           jQuery('.date').datepicker({
               dateFormat: "yy-mm-dd" 
           });
           jQuery('#clear_dates').bind('click', function () {
                    jQuery('.date').val('');
                return true;
            });
        });
</script>
<div id="ac_filter">
    <?php 
    if(!empty($message)){
    ?>
        <div class="error"><p><strong><?php echo $message?></strong></p></div>
    <?php
    }
    ?>
    <div class="left">
        <select id="days_campaign_id" style="max-width: 150px;">
            <?php
            if( $campaigns && !empty($campaigns) ) foreach($campaigns as $campaign) echo '<option value="' . $campaign->campaign_id . '" ' .
                    (isset($_GET['campaign_id']) && $_GET['campaign_id'] == $campaign->campaign_id ? 'selected=selected' : '') . ' >' .
                    $campaign->title .
                    '</option>';
            ?>
        </select>
    </div>
    <div class="left">
        <label for="date_from">From day (or single day):</label>
        <input type="text" class="date" name="date_from" id="date_from" readonly size="10" value="<?php echo ((!empty($_GET['date_from']))?($_GET['date_from']):(date("Y-m-01"))); ?>" placeholder="Click to choose"/>
        <label for="date_from">To day:</label>
        <input type="text" class="date" name="date_to" id="date_to" readonly size="10" value="<?php echo ((!empty($_GET['date_to']))?($_GET['date_to']):(date("Y-m-d"))); ?>" placeholder="Click to choose"/>
    </div>
    <div>
        <input type="submit" class="day_raport_submit" value="Generate Report" id="generate_button_days" action="<?php echo get_bloginfo('wpurl') . '/wp-admin/admin-ajax.php?action=acs_get_day_report&action2=get_days_details' ?>" />
        <input type="submit" class="day_raport_submit" value="Clear Dates" id="clear_dates" />
    </div>
</div>
<div class="cmac-clear"></div>
<?php
if( isset($daysRangeData) && !empty($daysRangeData) )
{
    ?>
    <table class="ac_statistics_day_details" cellspacing=0>
        <thead>
            <tr>
                <th class="left_head">Banner Name:</th>
                <th class="middle_head">Campaign:</th>
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
            foreach($daysRangeData as $date => $file){
                $odd = false;
                ?>
                <tr>
                    <td colspan="5" class="table_center_row">
                        <?php echo date("l d F Y", strtotime($date));?>
                    </td>
                </tr>
                <?php
                foreach ($file as $title => $stats){
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
                        <?php if($title): ?>
                            <?php echo $title ?>
                        <?php else: ?>
                            <span>Banner (id: <?php echo $stats['banner_id']; ?>)</span>
                        <?php endif; ?>
                    </td>
                    <td class="middle_content">
                        <span><?php echo $stats['campaign_title']; ?></span>
                    </td>
                    <td class="middle_content <?php echo (($odd)?('odd'):('')); ?>">
                        <?php echo $stats['clicks']; ?>
                    </td>
                    <td class="middle_content <?php echo (($odd)?('odd'):('')); ?>">
                        <?php echo $stats['impressions']; ?>
                    </td>
                    <td class="right_content <?php echo (($odd)?('odd'):('')); ?>">
                        <?php
                        if( $stats['impressions'] == 0 ) echo '0';
                        else echo round(($stats['clicks'] / $stats['impressions']) * 100);
                        ?>%
                    </td>
                </tr>
                <?php
                ($odd)?($odd = false):($odd = true);
                }
            }
            ?>
        </tbody>
    </table>
    <?php
}