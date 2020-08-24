<?php
new CMCR_Wp_Logins_By_User_Report();

class CMCR_Wp_Logins_By_User_Report extends CMCR_Report_Base {

	public function init() {
		add_filter( 'cmcr_graph_tab_controls_output-' . $this->getReportSlug(), array( $this, 'addGraphControls' ) );
		add_filter( 'cmcr_report_name_filter', array( 'CMCR_Report_Base', 'addReportNameContent' ), 10, 2 );
	}

	public function addGraphControls( $output ) {
		$postArray			 = filter_input_array( INPUT_POST );
		ob_start();
		?>
		<form method="post" action="">
			<input type="text" name="date_from" value="<?php echo!empty( $postArray[ 'date_from' ] ) ? $postArray[ 'date_from' ] : '' ?>" class="datepicker" />
			<input type="text" name="date_to" value="<?php echo!empty( $postArray[ 'date_to' ] ) ? $postArray[ 'date_to' ] : '' ?>" class="datepicker" />
			<input type="submit" value="Filter">
		</form>
		<?php
		$graphControlsOutput = ob_get_clean();
		$output				 = $graphControlsOutput . $output;
		return $output;
	}

	public function getReportSlug() {
		return 'wp-logins-by-user';
	}

	public function getReportDescription() {
		return CM_Custom_Reports::__( 'Report number of user logins to the WP by user' );
	}

	public function getReportName() {
		return CM_Custom_Reports::__( 'WP Logins By User' );
	}

	public function getGroups() {
		return array( 'wp' => CM_Custom_Reports::__( 'Wordpress' ) );
	}

	public function getReportExtraOptions() {
		$graphOptions = array(
			'axisLabels' => array(
				'show' => true
			),
			'xaxis'		 => array(
				'axisLabel'		 => 'Day',
				'mode'			 => 'time',
				'timeformat'	 => CM_Custom_Reports_Backend::getDateFormat( 'flot' ),
				'minTickSize'	 => array( 1, "day" )
			),
			'yaxis'		 => array(
				'axisLabel'		 => 'Amount',
				'min'			 => 0,
				'minTickSize'	 => 1,
				'tickDecimals'	 => 0
			),
			'series'	 => array(
				'bars' => array(
					'show'		 => TRUE,
					'fill'		 => TRUE,
					'show'		 => 1,
					'order'		 => 1,
					'barWidth'	 => 24 * 60 * 60 * 100,
				// 'align'		 => 'center'
				)
			),
			'grid'		 => array(
				'hoverable'		 => TRUE,
				'clickable'		 => TRUE,
				'autoHighlight'	 => TRUE,
			)
		);

		$reportOptions = array(
			'cron'				 => TRUE,
			'graph'				 => $graphOptions,
			'graph_datepicker'	 => array(
				'showOn'		 => 'both',
				'showAnim'		 => 'fadeIn',
				'dateFormat'	 => CM_Custom_Reports_Backend::getDateFormat( 'datepicker' ),
				'buttonImage'	 => CM_Custom_Reports_Backend::$imagesPath . 'calendar.gif',
			)
		);

		return $reportOptions;
	}

	/**
	 * Return the list of possible Graph Types
	 * @param type $possibleGraphTypes
	 * @return type
	 */
	public function getPossibleGraphTypes( $possibleGraphTypes ) {
		foreach ( $possibleGraphTypes as $key => $value ) {
			if ( !in_array( $key, array( 'bars', 'points', 'pie' ) ) ) {
				unset( $possibleGraphTypes[ $key ] );
			}
		}
		return $possibleGraphTypes;
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
			$dateQuery[ 'before' ] = date( 'd-m-Y' );
		}

		return $dateQuery;
	}

	public function getData( $dataArgs = array( 'json' => FALSE ) ) {
		static $savedData = array();

		$result		 = array();
		$loginByDate = array();
		$loginByUser = array();
		$dataLogins	 = array();

		$args = array(
			'type' => 'wp-login',
		);

		$json = !empty( $dataArgs[ 'json' ] ) ? $dataArgs[ 'json' ] : false;

		if ( empty( $dataArgs[ 'date_query' ] ) ) {
			$args[ 'date_query' ] = self::addDataFilter();
		} else {
			$args[ 'date_query' ] = $dataArgs[ 'date_query' ];
		}

		if ( !empty( $args[ 'date_query' ][ 'before' ] ) && !empty( $args[ 'date_query' ][ 'after' ] ) ) {
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
		$logins = CMCR_Event_Log_List_Table::getLogEntriesByArgs( $args );
		if ( !empty( $logins ) ) {
			foreach ( $logins as $login ) {
				$time	 = strtotime( $login->time );
				$offset = get_option('gmt_offset') * 3600;
				$time += $offset; // UTC to Wordpress Timezone
				$user	 = isset( $login->data[ 'user_login' ] ) ? $login->data[ 'user_login' ] : CMCR_Labels::getLocalized( 'unknown-user' );

				$realDate	 = CM_Custom_Reports_Backend::getDate( $time );
				$realTime	 = strtotime( $realDate );

				if ( !isset( $loginByDate[ $user ][ $realTime ] ) ) {
					$loginByDate[ $user ][ $realTime ] = 0;
				}
				$loginByDate[ $user ][ $realTime ] ++;

				/*
				 * Sum the logins of user
				 */
				if ( !isset( $loginByUser[ $user ] ) ) {
					$loginByUser[ $user ] = 0;
				}
				$loginByUser[ $user ] ++;
			}

			if ( !empty( $loginByDate ) ) {
				foreach ( $loginByDate as $userId => $loginData ) {
					$dataLogins = array();
					ksort( $loginData );

					reset( $loginData );
					$first_key = key( $loginData );
					self::updateDataDateFrom( date( 'd-m-Y', $first_key ) );

					foreach ( $loginData as $key => $value ) {
						$dataLogins[] = array( (int) $key * 1000, $value );
					}

					$userLabel = $userId . ' (Total logins: ' . $loginByUser[ $userId ] . ')';

					$result[] = array(
						'label'	 => $userLabel,
						'data'	 => $dataLogins
					);
				}
			}
		}

		if ( $json ) {
			$result = json_encode( $result );
		}

		$savedData[ $argsKey ] = $result;
		return $result;
	}

}
