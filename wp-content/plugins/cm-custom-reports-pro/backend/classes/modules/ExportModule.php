<?php

CMCR_Export_Module::init();

class CMCR_Export_Module {

	public static function init() {
		add_action( 'wp_ajax_cmcr-export-csv', array( __CLASS__, 'exportCSV' ) );
	}

	public static function exportCSV() {
		$postArray = filter_input_array( INPUT_POST );

		if ( !empty( $postArray[ 'reportData' ] ) ) {
			$reportData	 = json_decode( $postArray[ 'reportData' ], TRUE );
			$reportMeta	 = json_decode( $postArray[ 'reportMeta' ], TRUE );

			if ( !empty( $reportData ) ) {
				$customHeaders	 = false;
				$csvData		 = array();
				$headers		 = array();
				$data			 = array();

				foreach ( $reportData as $series ) {
					if ( !empty( $reportMeta[ 'csv_headers' ] ) ) {
						$headers		 = array_values( $reportMeta[ 'csv_headers' ] );
						$customHeaders	 = TRUE;
						$baseKey		 = 0;
					} else {
						$headers = array_merge( $headers, array( 'Time', $series[ 'label' ] ) );
						$baseKey = count( $headers ) - 2;
					}

					if ( !empty( $series[ 'data' ] ) ) {
						foreach ( $series[ 'data' ] as $key => $value ) {

							if ( !$customHeaders ) {
								if ( empty( $data[ $key ] ) ) {
									$dataRow = array();
								} else {
									$dataRow = $data[ $key ];
								}
								$dataRow[ $baseKey ]	 = CM_Custom_Reports_Backend::getDate( ($value[ 0 ] / 1000 ) );
								$dataRow[ 1 + $baseKey ] = $value[ 1 ];

								$data[ $key ] = $dataRow;
							} else {
								/*
								 * Just add the whole row
								 */
								if ( is_array( $value ) ) {
									$data[] = $value;
								}
							}
						}
					}
				}

				$csvData[]	 = $headers;
				$columnCount = count( $headers );

				if ( !$customHeaders ) {
					foreach ( $data as $key => $value ) {
						for ( $i = 0; $i < $columnCount; $i++ ) {
							if ( empty( $value[ $i ] ) ) {
								$data[ $key ][ $i ] = '';
							}
						}
						ksort( $data[ $key ] );
					}
				}
				$csvData = array_merge( $csvData, $data );

				include_once CM_Custom_Reports_Backend::$classPath . 'CSV.php';

				$fileName = !empty( $reportMeta[ 'name_filtered' ] ) ? sanitize_title_with_dashes( $reportMeta[ 'name_filtered' ] ) : 'cm-custom-report';

				$csvModule = new CM_CSV();
				$csvModule->stream( $csvData, $fileName );
			}
		}
		echo 'Something went wrong.';
		die();
	}

}
