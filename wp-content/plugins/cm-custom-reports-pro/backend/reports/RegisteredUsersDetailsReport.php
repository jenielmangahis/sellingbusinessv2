<?php
new CMCR_Registered_Users_Details_Report();

class CMCR_Registered_Users_Details_Report extends CMCR_Report_Base {

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
		return 'registered-users-details';
	}

	public function getReportDescription() {
		return CM_Custom_Reports::__( 'Report containing the details of the registered users (name, e-mail and registration date)' );
	}

	public function getReportName() {
		return CM_Custom_Reports::__( 'Registered Users Details' );
	}

	public function getGroups() {
		return array( 'users' => CM_Custom_Reports::__( 'Users' ) );
	}

	public function getReportExtraOptions() {
		$reportOptions = array(
			'csv_headers' => $this->getColumns()
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
			'name'			 => 'Name',
			'username'		 => 'Username',
			'email'			 => 'E-mail',
			'registration'	 => 'Registration date',
		);
		return $columns;
	}

	public function getData( $dataArgs = array( 'json' => FALSE ) ) {
		static $savedData = array();

		$dataUsers	 = array();

		$args = array(
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
		$query	 = new WP_User_Query( $args );
		$users	 = $query->results;
		if ( !empty( $users ) ) {
			$firstPost = true;

			foreach ( $users as $user ) {
				$time		 = strtotime( $user->user_registered );
				$realDate	 = CM_Custom_Reports_Backend::getDate( $time, 'datetime' );
				$realTime	 = strtotime( $realDate );

				if ( $firstPost ) {
					self::updateDataDateFrom( date( 'd-m-Y', $realTime ) );
					$firstPost = false;
				}

				$userData = array(
					'name'			 => $user->first_name . ' ' . $user->last_name,
					'username'		 => $user->user_login,
					'email'			 => $user->user_email,
					'registration'	 => $realDate,
				);

				$dataUsers[] = array_values( $userData );
			}
		}

		$result = array(
			array(
				'label'	 => __( 'Registered Users' ),
				'data'	 => $dataUsers
			),
		);

		if ( $json ) {
			$result = json_encode( $result );
		}
		$savedData[ $argsKey ] = $result;
		return $result;
	}

}
