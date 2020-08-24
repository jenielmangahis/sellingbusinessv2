<?php

CMCR_CSV_Module::init();

class CMCR_CSV_Module {

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
			$postArray	 = filter_input_array( INPUT_POST );
			$data		 = $postArray;

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

	public static function displayTab( $args = array() ) {
		$report = CM_Custom_Reports_Backend::getCurrentReport();

		do_action( 'cmcr_before_display_graph', $report );

		$graphData = array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		);

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

		$output .= '<p>';
		$output .= 'Select the starting and ending date of the period you would like to generate report for. If no dates are selected the report will contain all the data.';
		$output .= '</p>';

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

			$output .= '<div>';
			$output .= 'Download the report: ';
			$output .= '<a href="" id="graph-to-csv" class="cmcr-center"><img class="cmcr-middle" src="' . self::getIconUrl( 'csv' ) . '" title="' . CMCR_Labels::getLocalized( 'download_csv' ) . '" /> </a>';
			$output .= '</div>';
		} else {
			$output .= CMCR_Labels::getLocalized( 'graph_no_data' );
		}

		wp_localize_script( 'cmcr-admin-scripts', 'cmcr_graph_data', apply_filters( 'cmcr_graph_js_data', $graphData ) );

		$graphTabOutput = apply_filters( 'cmcr_csv_tab_output', $output );

		return $graphTabOutput;
	}

	public static function addTab( $tabs ) {
		$tabs[ '1' ] = _( 'CSV Export' );
		return $tabs;
	}

	public static function displayCSVReport( $args = array() ) {
		$report = CM_Custom_Reports_Backend::getCurrentReport();

		do_action( 'cmcr_before_display_graph', $report );

		$graphData = array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		);

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

			$output .= '<div>';
			$output .= 'Download the report: ';
			$output .= '<a href="" id="graph-to-csv" class="cmcr-center"><img class="cmcr-middle" src="' . self::getIconUrl( 'csv' ) . '" title="' . CMCR_Labels::getLocalized( 'download_csv' ) . '" /> </a>';
			$output .= '</div>';
		} else {
			$output .= CMCR_Labels::getLocalized( 'graph_no_data' );
		}

		wp_localize_script( 'cmcr-admin-scripts', 'cmcr_graph_data', apply_filters( 'cmcr_graph_js_data', $graphData ) );

		$graphTabOutput = apply_filters( 'cmcr_csv_tab_output', $output );

		return $graphTabOutput;
	}

}
