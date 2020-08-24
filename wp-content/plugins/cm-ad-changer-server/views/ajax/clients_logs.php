<?php
/**
 * CM Ad Changer
 *
 * @author CreativeMinds (http://ad-changer.cminds.com)
 * @copyright Copyright (c) 2013, CreativeMinds
 */
?>

<div class="clients_logs">
    <?php if( $clients_logs && !empty($clients_logs) ) : ?>
        <table cellspacing=0 cellpadding=3 class="ads_list">
            <thead>
                <tr>
                    <th>Campaign</th>
                    <th>Banner</th>
                    <th>Client Domain</th>
                    <th>Last Request</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($clients_logs as $log) : ?>
                    <tr>
                        <td>
                            <?php
                            if( $log->campaign_name )
                            {
                                echo '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=ac_server_campaigns&action=edit&campaign_id=' . $log->campaign_id . '" target="_blank">';
                                echo $log->campaign_name;
                                echo '</a>';
                            }
                            else echo '- removed -';
                            ?>
                        </td>
                        <td>
                            <?php
                            if( $log->banner_name )
                            {
                                echo '<a href="javascript:void(0)" filename="' . $log->filename . '" class="banner_image_link">';
                                echo $log->banner_name;
                                echo '</a>';
                            }
                            else echo '- removed -';
                            ?>
                        </td>
                        <td>
                            <a href="<?php echo $log->referer_url ?>" target="_blank"><?php echo $log->referer_url ?></a>
                        </td>
                        <td>
                            <?php echo date('d.m.Y H:i:s', strtotime($log->regdate)); ?>
                        </td>
                    </tr>
                    <?php
                endforeach;
                ?>
            </tbody>
        </table>
        <?php
    else:
        echo 'No clients logs found';
    endif;
    ?>
</div>