<?php
/**
 * CM Ad Changer
 *
 * @author CreativeMinds (http://ad-changer.cminds.com)
 * @copyright Copyright (c) 2013, CreativeMinds
 */
?>
<div class="wrap ad_changer ac_history">
    <h2><?php echo $plugin_data['Name']; ?> : Statistics</h2>
    <?php
    ac_top_menu();
    $disableHistoryTable = get_option('acs_disable_history_table', null);
    if($disableHistoryTable == 1){
        echo '<div class="clear"></div>';
        cminds_show_message(translate( 'Statistics not available due to history functionality disabled.'), true);
    }
    ?>
    <div id="asc_stats_tabs" class="cmac-clear ac-edit-form">
        <ul>
            <li><a href="<?php echo get_bloginfo('wpurl') . '/wp-admin/admin-ajax.php?action=acs_get_month_report' ?>">Report by Month</a></li>
            <li><a href="<?php echo get_bloginfo('wpurl') . '/wp-admin/admin-ajax.php?action=acs_get_day_report' ?>">Report by Period</a></li>
            <li><a href="<?php echo get_bloginfo('wpurl') . '/wp-admin/admin-ajax.php?action=acs_get_clients_logs' ?>">Clients Last Request</a></li>
            <li><a href="<?php echo get_bloginfo('wpurl') . '/wp-admin/admin-ajax.php?action=acs_get_history' ?>">Access Log</a></li>
            <li><a href="<?php echo get_bloginfo('wpurl') . '/wp-admin/admin-ajax.php?action=acs_get_server_load' ?>">Server Load</a></li>
            <li><a href="<?php echo get_bloginfo('wpurl') . '/wp-admin/admin-ajax.php?action=acs_get_group_report' ?>">Report by Group</a></li>
        </ul>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            preloaderHtml = '<div id="loader-wrapper"><div id="loader"></div><div id="loaderMessage">Loading</div></div>';
            jQuery("#asc_stats_tabs").tabs({
                beforeLoad: function (event, ui) {
                    ui.panel.html(preloaderHtml);
                    ui.jqXHR.error(function () {
                        ui.panel.html(
                                "Trying to load the information for this tab...");
                    });
                },
                load: function (event, ui) {
                    var tabs_options = jQuery("#asc_stats_tabs").tabs("option");

                    if (tabs_options['active'] != null)
                        selected_tab = tabs_options['active'];
                    else {
                        if (tabs_options['selected'] != null)
                            selected_tab = tabs_options['selected'];
                        else {
                            alert('jQuery Version is not supported');
                            return;
                        }
                    }

                    // Month report tab
                    if (selected_tab == 0) {
                        bind_generate_click(ui.panel);
                    }

                    // Clients Logs tab
                    if (selected_tab == 1) {
                        bind_generate_click_day(ui.panel);
                    }

                    // History tab
                    if (selected_tab == 3) {
                        bind_history_paging_click(ui.panel);

                        // History export
                        bind_export_history_click();

                        // Empty history
                        bind_empty_history_click(ui.panel);

                        // Filter
                        bind_filter_history_click(ui.panel);
                    }

                    // Server load tab
                    if (selected_tab == 4) {
                        bind_get_server_load_click(ui.panel);
                    }

                    // Groups tab
                    if (selected_tab == 5) {
                        bind_generate_click_groups(ui.panel);

                        bind_updata_click();

                    }
                    init_tooltips();
                }
            });
        })

        function bind_generate_click (panel) {
            jQuery('#generate_button').bind('click', function () {
                var campaignId = jQuery('#months_campaign_id').val();
                var month = jQuery('#month').val();
                jQuery(panel).html(preloaderHtml);
                jQuery(panel).load(jQuery(this).attr('action') + '&campaign_id=' + campaignId + '&month=' + encodeURIComponent(month), function () {
                    bind_generate_click(panel);
                    init_tooltips();
                });
                return true;
            });
        }

        function bind_generate_click_day (panel) {
            jQuery('#generate_button_days').bind('click', function () {

                var campaignId = jQuery('#days_campaign_id').val();
                var dateFrom = jQuery('#date_from').val();
                var dateTo = jQuery('#date_to').val();
                jQuery(panel).html(preloaderHtml);
                jQuery(panel).load(jQuery(this).attr('action') + '&campaign_id=' + campaignId + '&date_from=' + dateFrom + '&date_to=' + dateTo, function () {
                    bind_generate_click_day(panel);
                    init_tooltips();
                });
                return true;
            });
        }

        function bind_generate_click_groups (panel) {
            jQuery('#generate_button_groups').bind('click', function () {
                var groupId = jQuery('#group_id').val();
                var month = jQuery('#group_id_month').val();
                jQuery(panel).html(preloaderHtml);
                jQuery(panel).load(jQuery(this).attr('action') + '&group_id=' + groupId + '&month=' + encodeURIComponent(month), function () {
                    bind_generate_click_groups(panel);
                    init_tooltips();
                });
                return true;
            });
        }

        function bind_history_paging_click (panel) {
            jQuery('.asc_pagination a').bind('click', function (e) {
                e.preventDefault();
                for(i = 0; i < jQuery('input[name="filter_events"]').length; i++){
                    if (jQuery(jQuery('input[name="filter_events"]')[i]).attr('checked') == 'checked')
                        radio_id = jQuery(jQuery('input[name="filter_events"]')[i]).attr('id');
                }
                //		jQuery('#ui-id-3').attr('href',jQuery(this).attr('href')+'&events_filter='+jQuery('input#'+radio_id).val());
                jQuery(panel).load(jQuery(this).attr('href') + '&events_filter=' + jQuery('input#' + radio_id).val() + '&campaign_name=' + encodeURIComponent(jQuery('input#filter_campaign_name').val()) + '&advertiser_id=' + jQuery('select#advertiser_id').val(), function () {
                    bind_history_paging_click(panel);
                    bind_export_history_click();
                    bind_empty_history_click();
                    bind_filter_history_click(panel);
                    init_tooltips();
                });
                //		jQuery( "#asc_stats_tabs" ).tabs("load",2);
            })
        }

        function bind_export_history_click () {
            jQuery('#history_csv_export_button').click(function () {
                radio_id = false;
                for(i = 0; i < jQuery('input[name="filter_events"]').length; i++)
                    if (jQuery(jQuery('input[name="filter_events"]')[i]).attr('checked') == 'checked')
                        radio_id = jQuery(jQuery('input[name="filter_events"]')[i]).attr('id');

                if (radio_id != false)
                    document.location.href = jQuery(this).attr('action') + '&events_filter=' + jQuery('input#' + radio_id).val() + '&campaign_name=' + encodeURIComponent(jQuery('input#filter_campaign_name').val()) + '&advertiser_id=' + jQuery('select#advertiser_id').val();
            });
        }

        function bind_empty_history_click (panel) {
            jQuery('#empty_history_button').click(function () {
                for(i = 0; i < jQuery('input[name="filter_events"]').length; i++){
                    if (jQuery(jQuery('input[name="filter_events"]')[i]).attr('checked') == 'checked')
                        radio_id = jQuery(jQuery('input[name="filter_events"]')[i]).attr('id');
                }
                if (!confirm('Are You sure?'))
                    return;
                //		jQuery('#ui-id-3').attr('href',jQuery(this).attr('action'));
                jQuery(panel).load(jQuery(this).attr('action'));
                //		jQuery( "#asc_stats_tabs" ).tabs("load",2);
            });
        }

        function bind_filter_history_click (panel) {
            jQuery('#history_filter_button').bind('click', function () {
                for(i = 0; i < jQuery('input[name="filter_events"]').length; i++){
                    if (jQuery(jQuery('input[name="filter_events"]')[i]).attr('checked') == 'checked')
                        radio_id = jQuery(jQuery('input[name="filter_events"]')[i]).attr('id');
                }
                jQuery('#ui-id-3').attr('href', jQuery(this).attr('action') + '&events_filter=' + jQuery('input#' + radio_id).val() + '&campaign_name=' + jQuery('input#filter_campaign_name').val());
                var eventsFilter = jQuery('input#' + radio_id).val();
                var campaignName = jQuery('input#filter_campaign_name').val();
                var advertiser = jQuery('select#advertiser_id').val() || '';
                jQuery(panel).html(preloaderHtml);
                jQuery(panel).load(jQuery(this).attr('action') + '&events_filter=' + eventsFilter + '&campaign_name=' + encodeURIComponent(campaignName) + '&advertiser_id=' + advertiser, function () {
                    bind_history_paging_click(panel);
                    bind_export_history_click();
                    bind_empty_history_click();
                    bind_filter_history_click(panel);
                    init_tooltips();
                });
                //					jQuery( "#asc_stats_tabs" ).tabs("load",2);
                //					jQuery( this ).trigger('load');
                return true;
            })
        }

        function bind_get_server_load_click (panel) {
            jQuery('#get_server_load_button').bind('click', function () {
                var timeRange = jQuery('select#time_range').val();
                var campaignId = jQuery('select#campaign_id2').val();
                jQuery(panel).html(preloaderHtml);
                jQuery(panel).load(jQuery(this).attr('action') + '&time_range=' + timeRange + '&campaign_id=' + campaignId, function () {
                    bind_get_server_load_click(panel);
                })
            })
        }

        function init_tooltips () {
            jQuery('.banner_image_link').tooltip({
                items: "a",
                content: function () {
                    var element = jQuery(this);
                    return element.attr('content');
                },
                position: {
                    my: "left top",
                    at: "right top"
                }
            });
            jQuery('.banner_image_tooltip').tooltip({
                content: function () {
                    var element = jQuery(this), url;
                    url = element.attr('title');
                    if(url != ''){
                        return '<img src="' + url + '" />';
                    }else{
                        return "No image on server."
                    }
                },
                position: {
                    my: "left top",
                    at: "right top"
                }
            });
        }

        function draw_graph (data) {
            jQuery.plot("#server_load_graph", [data], {
                series: {
                    bars: {
                        show: true,
                        barWidth: 0.5,
                        align: "center"
                    }
                },
                xaxis: {
                    mode: "categories",
                    tickLength: 0
                },
                yaxis: {
                    minTickSize: 5
                }
            });
        }
        //Bug fixing
        function bind_updata_click () { 
            jQuery('#update_data_group').bind('click', function () {
              var group_id = jQuery('#group_id').val();

              jQuery.ajax({
                url : '<?php echo admin_url('admin-ajax.php'); ?>',
                type : 'POST',
                data: { 'action' : 'update_data', 'group_id' : group_id }
              })
              .done(function() {
                alert( "Data was successfully updated" );
              })
              .fail(function() {
                alert( "Something went wrong" );
              });

              return true;
            })
        }
    </script>
</div>
