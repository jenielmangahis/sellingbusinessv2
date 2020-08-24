<?php
/**
 * CM Ad Changer
 *
 * @author CreativeMinds (http://ad-changer.cminds.com)
 * @copyright Copyright (c) 2013, CreativeMinds
 */
?>
<div class="cmac-clear history_actions">
    <input type="submit" value="Export to CSV" id="history_csv_export_button" action="<?php echo get_bloginfo('wpurl') . '/wp-admin/admin-ajax.php?action=acs_export_history' ?>" />
    <input type="submit" value="Delete Access Log" id="empty_history_button" action="<?php echo get_bloginfo('wpurl') . '/wp-admin/admin-ajax.php?action=acs_empty_history' ?>" />
</div>
<div class="acs_statistics_filter">
    <label>Filter log by: </label>&nbsp;&nbsp;&nbsp;<input type="radio" name="filter_events" value="all" id="filter_all" <?php echo (!isset($_GET['events_filter']) || $_GET['events_filter'] == 'all' ? 'checked=checked' : '') ?> />&nbsp;<label for="filter_all">All</label>&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="radio" name="filter_events" value="impression" id="filter_impressions" <?php echo (isset($_GET['events_filter']) && $_GET['events_filter'] == 'impression' ? 'checked=checked' : '') ?> />&nbsp;<label for="filter_impressions">Impressions</label>&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="radio" name="filter_events" value="click" id="filter_clicks" <?php echo (isset($_GET['events_filter']) && $_GET['events_filter'] == 'click' ? 'checked=checked' : '') ?> />&nbsp;<label for="filter_clicks">Clicks</label>&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="text" placeholder="Campaign Name" name="filter_campaign_name" value="<?php echo isset($_GET['campaign_name']) ? $_GET['campaign_name'] : ''; ?>" id="filter_campaign_name"/>&nbsp;
    <?php
    if( $advertisers && !empty($advertisers) )
    {
        ?>
        <select id="advertiser_id" name="advertiser_id">
            <option value="0">Advertiser</option>
            <?php
            foreach($advertisers as $advertiser) echo '<option value="' . $advertiser['advertiser_id'] . '" ' . (isset($_GET['advertiser_id']) && $_GET['advertiser_id'] == $advertiser['advertiser_id'] ? 'selected=selected' : '') . '>' . $advertiser['name'] . '</option>';
            ?>
        </select>
        <?php
    }
    ?>
    <input type="submit" value="Filter" id="history_filter_button" action="<?php echo get_bloginfo('wpurl') . '/wp-admin/admin-ajax.php?action=acs_get_history' ?>" />
</div>
<?php
if( $history && !empty($history) )
{
    echo 'history is not empty';
    ?>
    <table id="ads_history" cellspacing=0 cellpadding=3 class="ads_list" style="display:block !important" >
        <thead>
            <tr>
                <th>
                    Event
                </th>
                <th>
                    Campaign Name
                </th>
                <th>
                    Campaign Type
                </th>
                <th>
                    Advertiser
                </th>
                <th>
                    Banner Name
                </th>
                <th>
                    Referer URL
                </th>
                <th>
                    Remote IP
                </th>
                <th>
                    Remote Country
                </th>
                <th>
                    Date
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach($history as $event)
            {
                ?>
                <tr>
                    <td>
                        <?php echo ucfirst($event->event_type); ?>
                    </td>
                    <td>
                        <?php
                        if( $event->campaign_title )
                        {
                            echo '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=ac_server_campaigns&action=edit&campaign_id=' . $event->campaign_id . '" target="_blank">';
                            echo $event->campaign_title;
                            echo '</a>';
                        }
                        else echo '- removed -';
                        ?>
                    </td>
                    <td>
                        <?php
                        if( $event->campaign_type )
                        {
                            switch($event->campaign_type)
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
                        }
                        else echo '';
                        ?>
                    </td>
                    <td>
                        <?php
                        if( $event->advertiser_name ) echo $event->advertiser_name;
                        ?>
                    </td>
                    <td>
                        <?php
                        if( (int) $event->parent_image_id != 0 )
                        {
                            $parent_banner = AC_Data::get_banner($event->parent_image_id);
                            $event->title = $parent_banner['title'];
                        }
                        if( isset($event->title) )
                        {
                            if( !empty($event->filename) )
                            {
                                /*
                                 * workaround for floating banner and floating bottom banner
                                 */
                                if($event->type != 4 && $event->type != 5){
                                    $filename = cmac_get_upload_dir() . $event->filename;
                                    $image_size = getimagesize($filename);
                                    if( !empty($image_size) && is_array($image_size) ){
                                        $title = $event->title . '(' . $image_size[0] . 'x' . $image_size[1] . ')';

                                        $fileUrl = cmac_get_upload_url() . $event->filename;

                                        $content = '<img src="' . $fileUrl . '" class="banner_tooltip_image" />';
                                        $prepareContent = htmlspecialchars(esc_attr($content), ENT_COMPAT, 'UTF-8', false);

                                        echo '<a href="javascript:void(0)" content="' . $prepareContent . '" class="banner_image_link">';
                                        echo $title;
                                        echo '</a>';
                                    }
                                    else{
                                        echo '- removed -';
                                    }
                                }else{
                                    $meta = unserialize($event->meta);
                                    echo '<a href="javascript:void(0)" content="' . htmlspecialchars(esc_attr($meta['html']), ENT_COMPAT, 'UTF-8', false) . '" class="banner_image_link">';
                                    echo $event->filename;
                                    echo '</a>';
                                }
                                
                            }
                            else
                            {
                                $banner = AC_Data::get_banner($event->banner_id);
                                $meta = maybe_unserialize($banner['meta']);

                                $content = !empty($meta) ? reset($meta) : '';
                                $prepareContent = htmlspecialchars(esc_attr($content), ENT_COMPAT, 'UTF-8', false);

                                if( $prepareContent )
                                {
                                    echo '<a href="javascript:void(0)" content="' . $prepareContent . '" class="banner_image_link">';
                                    echo '[no title]';
                                    echo '</a>';
                                }
                                else
                                {
                                    echo '<a href="javascript:void(0)" class="banner_image_link">';
                                    echo '[no preview]';
                                    echo '</a>';
                                }
                            }
                        }
                        else echo '- removed -';
                        ?>
                    </td>
                    <td>
                        <a href="<?php echo $event->webpage_url; ?>" title="<?php echo $event->webpage_url; ?>" target="_blank"><?php echo $event->referer_url ?></a>
                    </td>
                    <td>
                        <?php echo $event->remote_ip ?>
                    </td>
                    <td>
                        <?php echo $event->remote_country ?>
                    </td>
                    <td>
                        <?php echo date('Y/m/d H:i:s', strtotime($event->regdate)); ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php echo $ac_pagination; ?>
    <?php
}
else
{
    echo '<div class="cmac-clear">There are no records found</div>';
}