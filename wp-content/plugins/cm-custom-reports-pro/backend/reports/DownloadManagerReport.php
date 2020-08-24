<?php
if (class_exists('CMDM')) {
		new CMCR_Download_Manager_Report();
}

class CMCR_Download_Manager_Report extends CMCR_Report_Base {

	public function init() {
			add_filter( 'cmcr_graph_tab_controls_output-' . $this->getReportSlug(), array( $this, 'addGraphControls' ) );
			add_filter( 'cmcr_report_name_filter', array( 'CMCR_Report_Base', 'addReportNameContent' ), 10, 2 );
			add_filter( 'cmcr-report-' . $this->getReportSlug() . '-tabs-array', array( 'CMCR_Cron_Module', 'addCronTab' ) );
			add_filter( 'cmcr-report-' . $this->getReportSlug() . '-tab-content-2', array( 'CMCR_Cron_Module', 'displayCron' ) );
	}

	public function addGraphControls( $output ) {
		$postArray			 = filter_input_array( INPUT_POST );
		ob_start();
		?>
		<form method="post" action="">
			<input type="text" name="date_from" value="<?php echo!empty( $postArray[ 'date_from' ] ) ? $postArray[ 'date_from' ] : '' ?>" class="datepicker" />
			<input type="text" name="date_to" value="<?php echo!empty( $postArray[ 'date_to' ] ) ? $postArray[ 'date_to' ] : '' ?>" class="datepicker" />
			<input type="submit" value="Set Range">
		</form>
		<?php
		$graphControlsOutput = ob_get_clean();
		$output				 = $graphControlsOutput . $output;
		return $output;
	}

	public function getReportSlug() {
		return 'download-manager';
	}

	public function getReportDescription() {
		return CM_Custom_Reports::__( 'CSV report containing user e-mail, login date, file name, download date.' );
	}

	public function getReportName() {
		return CM_Custom_Reports::__( 'Download Manager' );
	}

	public function getGroups() {
		return array( 'download_manager' => CM_Custom_Reports::__( 'Download Manager' ) );
	}

	public function getReportExtraOptions() {
		$reportOptions = array(
			'csv_headers' => $this->getColumns(),
			'graph_datepicker' => array(
					'showOn'      => 'both',
					'showAnim'    => 'fadeIn',
					'dateFormat'  => CM_Custom_Reports_Backend::getDateFormat('datepicker'),
					'buttonImage' => CM_Custom_Reports_Backend::$imagesPath . 'calendar.gif',
			)
		);
		return $reportOptions;
	}

	public function addTabs() {
		add_filter( 'cmcr-report-' . $this->getReportSlug() . '-tabs-array', array( 'CMCR_CSV_Module', 'addTab' ) );
		add_filter( 'cmcr-report-' . $this->getReportSlug() . '-tab-content-1', array( 'CMCR_CSV_Module', 'displayTab' ) );
	}

	public static function addDataFilter() {
		$dateQuery	 = array();
		$postArray	 = filter_input_array( INPUT_POST );

		if ( !empty( $postArray[ 'date_from' ] ) ) {
			$dateQuery[ 'after' ] = $postArray[ 'date_from' ];
		}
		if ( !empty( $postArray[ 'date_to' ] ) ) {
			$dateQuery[ 'before' ] = $postArray[ 'date_to' ];
		} else {
			$dateQuery[ 'before' ] = CM_Custom_Reports_Backend::getDate();
		}

		return $dateQuery;
	}

	public function getColumns() {
		$columns = array(
			'email'		 => 'E-mail',
			'login'		 => 'Login date',
			'document' => 'File',
			'download' => 'Download date',
		);
		return $columns;
	}

	public function getData( $dataArgs = array( 'json' => FALSE ) ) {
		static $savedData = array();

		$dataLogins = array();

		$args = array(
			'type' => 'wp-login',
		);

		$json = !empty( $dataArgs[ 'json' ] ) ? $dataArgs[ 'json' ] : false;

		if ( empty( $dataArgs[ 'date_query' ] ) ) {
			$args[ 'date_query' ] = self::addDataFilter();
		} else {
			$args[ 'date_query' ] = $dataArgs[ 'date_query' ];
		}

		if ( !empty( $args[ 'date_query' ][ 'before' ] ) || !empty( $args[ 'date_query' ][ 'after' ] ) ) {
			$args[ 'date_query' ][ 'inclusive' ] = true;
			$args[ 'date_query' ][ 'before' ] .= '23:59:59';
		}

		$argsKey = sha1( maybe_serialize( $args ) );
		if ( !empty( $savedData[ $argsKey ] ) ) {
			return $savedData[ $argsKey ];
		}

		/*
		 * Posts
		 */
		$logins = CMCR_Event_Log_List_Table::getLogAndDownloadEntries( $args );
		if ( !empty( $logins ) ) {
			$firstPost = true;

			foreach ( $logins as $login ) {
				$time		 = strtotime( $login->time );
				$offset = get_option('gmt_offset') * 3600;
				$time += $offset; // UTC to Wordpress Timezone
				$user		 = isset( $login->data[ 'user_login' ] ) ? $login->data[ 'user_login' ] : CMCR_Labels::getLocalized( 'unknown-user' );
				$realDate	 = CM_Custom_Reports_Backend::getDate( $time, 'datetime' );
				$realTime	 = strtotime( $realDate );

				if ( $firstPost ) {
					self::updateDataDateFrom( date( 'd-m-Y', $realTime ) );
					$firstPost = false;
				}

				$userObj = get_user_by( 'login', $user );
				if ( empty( $userObj ) ) {
					/*
					 * Can't get more info abour missing users
					 */
					$userData = array(
						'email'		 => '-not available-',
						'login'		 => $realDate,
					);
				} else {
					$userData = array(
						'email'		 => $userObj->user_email,
						'login'		 => $realDate,
					);
				}
				if (!empty($login->comment_post_ID) && !empty($login->comment_date)) {
						$document = get_post($login->comment_post_ID );
						$downloadDate = date('m/d/Y H:i:s', strtotime($login->comment_date));
						$downloadData = array(
								'document' => $document->post_title,
							 	'download' => $downloadDate
						);

						$userData = array_merge( $userData, $downloadData );
				}

				$dataLogins[] = array_values( $userData );

			}
		}

		$result = array(
			array(
				'label'	 => __( 'Download Manager Report' ),
				'data'	 => $dataLogins
			),
		);

		if ( $json ) {
			$result = json_encode( $result );
		}

		$savedData[ $argsKey ] = $result;
		return $result;
	}

}
