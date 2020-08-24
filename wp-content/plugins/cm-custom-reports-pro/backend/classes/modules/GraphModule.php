<?php
CMCR_Graph_Module::init();

class CMCR_Graph_Module {

    public static function init() {
        add_filter( 'cmcr_graph_options', array( __CLASS__, 'changeGraphSpan' ), 10, 2 );
    }

    /**
     * Adjust the graph span to the filtered dates
     * @param type $options
     * @param type $data
     * @return type
     */
    public static function changeGraphSpan( $options, $data ) {
        $dataRangeSpan = CMCR_Settings::getOption( CMCR_Settings::OPTION_DATA_RANGE_SPAN );
        if ( $dataRangeSpan ) {
            $postArray = filter_input_array( INPUT_POST );
            $data      = $postArray;

            if ( !empty( $data[ 'date_from' ] ) ) {
                $options[ 'xaxis' ][ 'min' ] = (int) strtotime( $data[ 'date_from' ] ) * 1000;
            }

            if ( !empty( $data[ 'date_to' ] ) ) {
                $options[ 'xaxis' ][ 'max' ] = (int) strtotime( $data[ 'date_to' ] ) * 1000;
            }
        }

        return $options;
    }

    public static function checkIfHasData( $data ) {
        $result = FALSE;
        if ( !empty( $data[ 'data' ] ) ) {
            foreach ( $data[ 'data' ] as $key => $value ) {
                if ( !empty( $value[ 'data' ] ) ) {
                    $result = TRUE;
                    break;
                }
            }
        }

        return $result;
    }

    public static function getIconUrl( $icon ) {
        $url = CM_Custom_Reports_Backend::$imagesPath . $icon . '-icon.png';
        return $url;
    }

    public static function outputGraphTypeSelect( $reportOptions ) {
        $selected = self::getGraphTypeFromOptions( $reportOptions );

        $possibleGraphTypes = array(
            'bars'   => CM_Custom_Reports::__( 'Bars' ),
            'points' => CM_Custom_Reports::__( 'Points' ),
            'pie'    => CM_Custom_Reports::__( 'Pie' ),
            'lines'  => CM_Custom_Reports::__( 'Lines' ),
        );

        $possibleGraphTypes = apply_filters( 'cmcr_possible_graph_types_' . $reportOptions[ 'slug' ], $possibleGraphTypes );

        if ( 1 == count( $possibleGraphTypes ) ) {
            return '';
        }
        ob_start();
        ?>
        <label><?php echo CMCR_Labels::getLocalized( 'choose_graph_type' ) ?>
            <select id="graph-type-select" name="cmcr-graph-type">
                <?php
                foreach ( $possibleGraphTypes as $key => $value ) {
                    echo '<option value="' . $key . '" ' . selected( $key, $selected ) . '>' . $value . '</option>';
                }
                ?>
            </select>
        </label>
        <?php
        $content = ob_get_clean();
        return $content;
    }

    public static function getGraphTypeFromOptions( $reportOptions ) {
        if ( !empty( $reportOptions[ 'graph' ][ 'series' ] ) ) {
            if ( is_array( $reportOptions[ 'graph' ][ 'series' ] ) ) {
                $seriesKeys = array_keys( $reportOptions[ 'graph' ][ 'series' ] );
                $result     = reset( $seriesKeys );
            } else {
                $result = $reportOptions[ 'graph' ][ 'series' ];
            }
        } else {
            $result = 'bars';
        }
        return $result;
    }

    public static function displayGraph( $args = array() ) {
        $report = CM_Custom_Reports_Backend::getCurrentReport();

        wp_enqueue_script( 'cmcr-graph-flot', CM_Custom_Reports_Backend::$libsPath . 'flot/jquery.flot.js', array( 'jquery' ) );
        wp_enqueue_script( 'cmcr-graph-flot-time', CM_Custom_Reports_Backend::$libsPath . 'flot/jquery.flot.time.js', array( 'jquery' ) );
        wp_enqueue_script( 'cmcr-graph-flot-axislabels', CM_Custom_Reports_Backend::$libsPath . 'flot/jquery.flot.axislabels.js', array( 'cmcr-graph-flot' ) );
        wp_enqueue_script( 'cmcr-graph-flot-tooltip', CM_Custom_Reports_Backend::$libsPath . 'flot/jquery.flot.tooltip.min.js', array( 'cmcr-graph-flot' ) );
        wp_enqueue_script( 'cmcr-graph-flot-pie', CM_Custom_Reports_Backend::$libsPath . 'flot/jquery.flot.pie.js', array( 'cmcr-graph-flot' ) );
        wp_enqueue_script( 'cmcr-graph-flot-orderBars', CM_Custom_Reports_Backend::$libsPath . 'flot/jquery.flot.orderBars.js', array( 'cmcr-graph-flot' ) );
        wp_enqueue_script( 'cmcr-js-pdf', CM_Custom_Reports_Backend::$libsPath . 'jspdf/jspdf.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'cmcr-html2canvas-pdf', CM_Custom_Reports_Backend::$libsPath . 'html2canvas/html2canvas.js', array( 'jquery', 'cmcr-js-pdf' ) );
        wp_enqueue_script( 'cmcr-canvas2image', CM_Custom_Reports_Backend::$libsPath . 'canvas2image/canvas2image.js', array( 'jquery' ) );
        wp_enqueue_script( 'cmcr-html2pdf', CM_Custom_Reports_Backend::$libsPath . 'html2pdf/html2pdf.js', array( 'jquery', 'cmcr-html2canvas-pdf', 'cmcr-js-pdf' ) );

        do_action( 'cmcr_before_display_graph', $report );

        $graphData = array(
            'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
            'placeholder_id' => 'cmcr_graph_placeholder'
        );
        $graphData = apply_filters( 'cmcr_graph_data', $graphData, $report );

        if ( !empty( $args[ 'data' ] ) ) {
            $graphData[ 'data' ] = $args[ 'data' ];
            remove_all_filters( 'cmcr_graph_tab_controls_output-' . $report->getReportSlug() );
        } else {
            $graphData[ 'data' ] = $report->getData();
        }

        $hasData = self::checkIfHasData( $graphData );

        $reportOptions = $report->getReportOptions();

        $output = '';
        $output .= apply_filters( 'cmcr_graph_tab_above_controls_output', '' );
        $output .= '<div class="cmcr-graph-controls-container">';
        $output .= '<a href="" id="graph-to-pdf" class="cmcr-float-right"><img src="' . self::getIconUrl( 'pdf' ) . '" title="' . CMCR_Labels::getLocalized( 'download_pdf' ) . '" /> </a>';
        if ( empty( $reportOptions[ 'hide_csv' ] ) ) {
            $output .= '<a href="" id="graph-to-csv" class="cmcr-float-right"><img src="' . self::getIconUrl( 'csv' ) . '" title="' . CMCR_Labels::getLocalized( 'download_csv' ) . '" /> </a>';
        }
        $output .= self::outputGraphTypeSelect( $reportOptions );
        $output .= apply_filters( 'cmcr_graph_tab_controls_output-' . $report->getReportSlug(), '' );
        $output .= '<div class="clear clearfix"></div></div>';
        $output .= apply_filters( 'cmcr_graph_tab_below_controls_output', '' );

        if ( !empty( $args[ 'name_filter_args' ] ) ) {
            $reportOptions[ 'name_filtered' ] = $report->getReportNameFiltered( $args[ 'name_filter_args' ] );
        }

        if ( is_array( $reportOptions ) ) {
            if ( array_key_exists( 'graph_datepicker', $reportOptions ) && is_array( $reportOptions[ 'graph_datepicker' ] ) ) {
                $graphData[ 'datepicker_options' ] = apply_filters( 'cmcr_graph_datepicker_options', $reportOptions[ 'graph_datepicker' ] );
            }

            $graphData[ 'current_report' ] = $reportOptions;
        }

        if ( $hasData ) {
            if ( is_array( $reportOptions ) && array_key_exists( 'graph', $reportOptions ) && is_array( $reportOptions[ 'graph' ] ) ) {
                $graphData[ 'options' ] = apply_filters( 'cmcr_graph_options', $reportOptions[ 'graph' ], $graphData );
            }

            $output .= '<div id="cmcr_graph_placeholder" class="cmcr_graph_placeholder"></div>';
        } else {
            $output .= CMCR_Labels::getLocalized( 'graph_no_data' );
        }

        wp_localize_script( 'cmcr-admin-scripts', 'cmcr_graph_data', apply_filters( 'cmcr_graph_js_data', $graphData ) );

        $graphTabOutput = apply_filters( 'cmcr_graph_tab_output', $output, $args );

        return $graphTabOutput;
    }

    public static function addGraphTab( $tabs ) {
        $tabs[ '1' ] = _( 'Graph' );
        return $tabs;
    }

}
