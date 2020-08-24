<?php
/**
 * CM Ad Changer
 *
 * @author CreativeMinds (http://ad-changer.cminds.com)
 * @copyright Copyright (c) 2013, CreativeMinds
 */
$time_range = isset($_GET['time_range']) ? $_GET['time_range'] : 'hour';
$campaign_id = isset($_GET['campaign_id']) ? $_GET['campaign_id'] : '0';
?>
<div class="acs_statistics_filter">
    <label>Filter by: </label>&nbsp;&nbsp;&nbsp;
    <select name="time_range" id="time_range">
        <option value="hour" <?php echo ($time_range == 'hour' ? 'selected=selected' : '') ?>>Last hour</option>
        <option value="day" <?php echo ($time_range == 'day' ? 'selected=selected' : '') ?>>Last day</option>
        <option value="week" <?php echo ($time_range == 'week' ? 'selected=selected' : '') ?>>Last week</option>
        <option value="month" <?php echo ($time_range == 'month' ? 'selected=selected' : '') ?>>Last month</option>
        <option value="six_months" <?php echo ($time_range == 'six_months' ? 'selected=selected' : '') ?>>Recent 6 months</option>
    </select>&nbsp;&nbsp;
    <?php
    if( isset($campaigns) && !empty($campaigns) )
    {
        ?>
        <select name="campaign_id2" id="campaign_id2">
            <option value="0"  <?php echo ($campaign_id == '0' ? 'selected=selected' : '') ?>>All Campaigns</option>
            <?php
            foreach($campaigns as $campaign)
            {
                echo '<option value="' . $campaign->campaign_id . '" ' . ($campaign_id == $campaign->campaign_id ? 'selected=selected' : '') . '>' . $campaign->title . '</option>';
            }
            ?>
        </select>&nbsp;&nbsp;
        <?php
    }
    ?>
    <input type="submit" value="Filter" id="get_server_load_button" action="<?php echo get_bloginfo('wpurl') . '/wp-admin/admin-ajax.php?action=acs_get_server_load' ?>" />
</div>

<div id="server_load_info">
    <?php
    switch($_GET['time_range'])
    {
        case 'day':
            $cnt = $data['cnt'];
            echo 'Activity during last day: ';
            break;
        case 'week':
            $cnt = $data['cnt'];
            echo 'Activity during last week: ';
            break;
        case 'month':
            $cnt = $data['cnt'];
            echo 'Activity during last month: ';
            break;
        case 'six_months':
            $cnt = $data['cnt'];
            echo 'Activity during last last six months: ';
            break;
        default: // should be hour
            $cnt = $data;
            echo 'Activity during last hour: ';
            break;
    }
    echo '<b>' . $cnt . ' ' . 'request' . ($cnt != 1 ? 's' : '') . '</b>';
    ?>
</div>


<div id="server_load_graph" style="width:600px;height:500px;display:none;">
</div>

<?php
if( $_GET['time_range'] == 'six_months' )
{
    echo "<script type=\"text/javascript\">\n";
    echo "var data = new Array();\n";
    echo "data=[";
    $month_requests_js = array();
    foreach($data['month_requests'] as $month => $requests)
    {
        $month_requests_js[] = '["' . $month . '",' . $requests . ']';
    }
    echo implode(',', $month_requests_js) . '];' . "\n";
    echo "jQuery('#server_load_graph').show();\n";
    echo "draw_graph(data);";
    echo "\n</script>";
}

if( $_GET['time_range'] == 'month' )
{
    echo "<script type=\"text/javascript\">\n";
    echo "var data = new Array();\n";
    echo "data=[";
    $month_requests_js = array();
    foreach($data['cur_month_requests'] as $day => $requests)
    {
        $month_requests_js[] = '[' . $day . ',' . $requests . ']';
    }
    echo implode(',', $month_requests_js) . '];' . "\n";
    echo "jQuery('#server_load_graph').show();\n";
    echo "draw_graph(data);";
    echo "\n</script>";
}

if( $_GET['time_range'] == 'week' )
{
    echo "<script type=\"text/javascript\">\n";
    echo "var data = new Array();\n";
    echo "data=[";
    $week_requests_js = array();
    foreach($data['cur_week_requests'] as $day => $requests) $week_requests_js[] = '["' . $day . '",' . $requests . ']';
    echo implode(',', $week_requests_js) . '];' . "\n";
    echo "jQuery('#server_load_graph').show();\n";
    echo "draw_graph(data);";
    echo "\n</script>";
}

if( $_GET['time_range'] == 'day' )
{
    echo "<script type=\"text/javascript\">\n";
    echo "var data = new Array();\n";
    echo "data=[";
    $day_requests_js = array();
    foreach($data['today_requests'] as $hour => $requests) $day_requests_js[] = '[' . $hour . ',' . $requests . ']';
    echo implode(',', $day_requests_js) . '];' . "\n";
    echo "jQuery('#server_load_graph').show();\n";
    echo "draw_graph(data);";
    echo "\n</script>";
}