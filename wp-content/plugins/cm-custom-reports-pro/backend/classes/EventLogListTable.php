<?php
if ( !class_exists( 'WP_List_Table' ) ) {
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class CMCR_Event_Log_List_Table extends WP_List_Table {

	const DB_VERSION = '1.3';

	protected $orderBy	 = 'time';
	protected $order	 = 'DESC';
	protected $perpage	 = '';

	public static function getTableName() {
		global $wpdb;
		$tablePrefix = $wpdb->prefix;
		$tableName	 = $tablePrefix . "cmcr_event_log";
		return $tableName;
	}

	public static function _install() {
		$table_name = self::getTableName();

		$installed_ver = get_option( 'cmcr_event_log_db_version' );
		if ( $installed_ver != self::DB_VERSION ) {
			$sql = "CREATE TABLE " . $table_name . " (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          type VARCHAR(40) NOT NULL,
	  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
          data TEXT,
          PRIMARY KEY  (id)
        );";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta( $sql );

			// notice that we are updating option, rather than adding it
			update_option( 'cmcr_event_log_db_version', self::DB_VERSION );
		}
	}

	public function __construct() {
		parent::__construct( array(
			'singular'	 => 'cmcr_event_log',
			'plural'	 => 'cmcr_event_logs',
			'ajax'		 => true
		) );
	}

	public function extra_tablenav( $which ) {
		$report_type	 = filter_input( INPUT_GET, "report" );
		$timeframe_type	 = filter_input( INPUT_GET, "timeframe" );

		if ( empty( $timeframe_type ) ) {
			$timeframe_type = '1';
		}

		if ( $which === 'top' ) {
			ob_start()
			?>

			<strong>Time-frame:</strong>
			&nbsp
			<select name="timeframe" id="timeframe" style="width: auto;" value="<?php $timeframe_type; ?> ">
				<option value="1" <?php selected( '1', $timeframe_type ); ?> >Last 24 Hours</option>
				<option value="2" <?php selected( '2', $timeframe_type ); ?> >Last 48 Hours</option>
				<option value="7" <?php selected( '7', $timeframe_type ); ?> >Last Week</option>
				<option value="14" <?php selected( '14', $timeframe_type ); ?> >Last Two Week</option>
				<option value="30" <?php selected( '30', $timeframe_type ); ?> >Last Month</option>
				<option value="90" <?php selected( '90', $timeframe_type ); ?> >Last Quarter</option>
				<option value="365" <?php selected( '365', $timeframe_type ); ?> >Last Year</option>
				<option value="3650" <?php selected( '3650', $timeframe_type ); ?> >Ever Since</option>
			</select>
			&nbsp;&nbsp;&nbsp;

			<input type="submit" class="button-primary" value="Filter" name="LogsReport" />
			<div style="clear: both; height: 10px;"></div>

			<div class=clear></div>
			<?php
			$content = ob_get_clean();
			echo $content;
		}
	}

	public function get_columns() {

		$columns = array(
			'cmcr_event_log_type'	 => __( 'Event Type' ),
			'cmcr_event_log_time'	 => __( 'Sent' ),
//            'cmcr_event_log_show' => __('Options')
		);

		return $columns;
	}

	public function get_columns_fields() {
		$columnsFields = array(
			'cmcr_event_log_type'	 => 'type',
			'cmcr_event_log_time'	 => 'time',
			'cmcr_event_log_show'	 => 'id',
		);

		return $columnsFields;
	}

	public function get_sortable_columns() {
		return $sortable = array(
			'cmcr_event_log_id'		 => array( 'id', 'DESC' ),
			'cmcr_event_log_time'	 => array( 'time', 'DESC' ),
			'cmcr_event_log_type'	 => array( 'type', 'DESC' ),
		);
	}

	public function get_column_info() {
		return array(
			$this->get_columns(),
			array(),
			$this->get_sortable_columns(),
			$this->get_primary_column_name(),
		);
	}

	public static function logEvent( $event, $input = NULL ) {
		$data[ 'type' ]	 = $event;
		$data[ 'data' ]	 = $input;
		self::addLogEntry( $data );
	}

	/**
	 * Adds the log entry
	 *
	 * @global type $wpdb
	 * @param type $data
	 * @return array array of all data and the result of the insert
	 */
	protected static function addLogEntry( $data = array() ) {
		global $wpdb;

		$format = array( '%s', '%s', '%s' );

		if ( empty( $data[ 'data' ][ 'time' ] ) ) {
			$baseData[ 'time' ] = date( 'Y-m-d H:i:s', time() );
		} else {
			$baseData[ 'time' ] = $data[ 'data' ][ 'time' ];
			unset($data[ 'data' ][ 'time' ]);
		}

		$dbData				 = array_merge( $baseData, $data );
		$dbData[ 'data' ]	 = maybe_serialize( $dbData[ 'data' ] );
		$result				 = $wpdb->insert( self::getTableName(), $dbData, $format );

		$dbData[ 'result' ] = $result;
		return $dbData;
	}

	public static function getLogEntry( $id, $returnType = OBJECT ) {
		global $wpdb;
		$query			 = self::getQuery();
		$preparedQuery	 = $wpdb->prepare( $query . ' WHERE id=%d ', $id );
		$result			 = $wpdb->get_row( $preparedQuery, $returnType );
		if ( is_object( $result ) ) {
			$result->data = maybe_unserialize( $result->data );
		}
		return $result;
	}

	public static function getLogEntriesByType( $type, $returnType = OBJECT ) {
		global $wpdb;
		$query			 = self::getQuery();
		$preparedQuery	 = $wpdb->prepare( $query . ' WHERE type=%s ', $type );
		$results		 = $wpdb->get_results( $preparedQuery, $returnType );
		if ( is_array( $results ) ) {
			foreach ( $results as $key => $result ) {
				if ( is_object( $result ) ) {
					$results[ $key ]->data = maybe_unserialize( $result->data );
				}
			}
		}
		return $results;
	}

	public static function getLogEntriesByArgs( $args = array(), $returnType = OBJECT ) {
		global $wpdb;

		$whereArr	 = array();
		$whereSql	 = '';
		$whereArgs	 = array();

		$query = self::getQuery();

		if ( !empty( $args[ 'type' ] ) ) {
			if ( is_string( $args[ 'type' ] ) ) {
				$whereArr[]	 = 'type=%s';
				$whereArgs[] = $args[ 'type' ];
			} else if ( is_array( $args[ 'type' ] ) ) {

				/*
				 * We need additional array imploded by OR
				 */
				$whereTypeArr = array();
				foreach ( $args[ 'type' ] as $type ) {
					$whereTypeArr[]	 = 'type=%s';
					$whereArgs[]	 = $type;
				}
				if ( !empty( $whereTypeArr ) ) {
					$whereArr[] = '(' . implode( ' OR ', $whereTypeArr ) . ')';
				}
			}
		}

		if ( !empty( $args[ 'date_query' ] ) ) {
			$date_query_object	 = new WP_Date_Query( $args[ 'date_query' ], 'time' );
			$where				 = preg_replace( '/^\s*AND\s*/', '', $date_query_object->get_sql() );
			$postsTable			 = $wpdb->posts;
			$whereArr[]			 = str_replace( $postsTable . '.post_date', 'time', $where );
		}

		if ( !empty( $whereArr ) ) {
			$whereSql = ' WHERE 1=1 AND ' . implode( ' AND ', $whereArr );
		}

		$preparedQuery = $wpdb->prepare( $query . $whereSql, $whereArgs );

		$results = $wpdb->get_results( $preparedQuery, $returnType );
		if ( is_array( $results ) ) {
			foreach ( $results as $key => $result ) {
				if ( is_object( $result ) ) {
					$results[ $key ]->data = maybe_unserialize( $result->data );
				}
			}
		}
		return $results;
	}
	public static function getLogAndDownloadEntries( $args = array(), $returnType = OBJECT ) {
		global $wpdb;

		$whereArr	 = array();
		$whereSql	 = '';
		$whereArgs	 = array();

		$tableName = self::getTableName();
		$query = "SELECT {$tableName}.id, {$tableName}.type, {$tableName}.time, {$tableName}.data, c.comment_post_ID, c.comment_date FROM {$tableName}";

		$joinQuery = " LEFT JOIN {$wpdb->commentmeta} AS cm ON cm.meta_value=". self::getTableName().".id  AND cm.meta_key='custom_report_log' LEFT JOIN {$wpdb->comments} AS c ON c.comment_id=cm.comment_id ";

		if ( !empty( $args[ 'type' ] ) ) {
			if ( is_string( $args[ 'type' ] ) ) {
				$whereArr[]	 = 'type=%s';
				$whereArgs[] = $args[ 'type' ];
			} else if ( is_array( $args[ 'type' ] ) ) {

				/*
				 * We need additional array imploded by OR
				 */
				$whereTypeArr = array();
				foreach ( $args[ 'type' ] as $type ) {
					$whereTypeArr[]	 = 'type=%s';
					$whereArgs[]	 = $type;
				}
				if ( !empty( $whereTypeArr ) ) {
					$whereArr[] = '(' . implode( ' OR ', $whereTypeArr ) . ')';
				}
			}
		}

		if ( !empty( $args[ 'date_query' ] ) ) {
			$date_query_object	 = new WP_Date_Query( $args[ 'date_query' ], 'time' );
			$where				 = preg_replace( '/^\s*AND\s*/', '', $date_query_object->get_sql() );
			$postsTable			 = $wpdb->posts;
			$whereArr[]			 = str_replace( $postsTable . '.post_date', 'time', $where );
		}

		if ( !empty( $whereArr ) ) {
			$whereSql = ' WHERE 1=1 AND ' . implode( ' AND ', $whereArr );
		}

		$preparedQuery = $wpdb->prepare( $query . $joinQuery . $whereSql, $whereArgs );

		$results = $wpdb->get_results( $preparedQuery, $returnType );
		if ( is_array( $results ) ) {
			foreach ( $results as $key => $result ) {
				if ( is_object( $result ) ) {
					$results[ $key ]->data = maybe_unserialize( $result->data );
				}
			}
		}
		return $results;
	}

	public static function deleteLogEntry( $id ) {
		global $wpdb;
		$query			 = 'DELETE FROM ' . self::getTableName();
		$preparedQuery	 = $wpdb->prepare( $query . ' WHERE id=%d ', $id );
		$result			 = $wpdb->query( $preparedQuery );
		return $result;
	}

	public static function getReportUrl( $reportKey ) {
		$result = esc_url( add_query_arg( array( 'action' => 'cm_custom_reports_gateway', 'cmcr_report_key' => $reportKey ), admin_url( 'admin-ajax.php' ) ) );
		return $result;
	}

	public static function getDeleteUrl( $logKey ) {
		$result = esc_url( add_query_arg( array( 'page' => 'cm-custom-reports-event', 'cmcr_log' => $logKey, 'action' => 'delete' ), admin_url( 'admin.php' ) ) );
		$result .= '#tabs_2';
		return $result;
	}

	public static function getQuery() {
		$sqlQuery = 'SELECT * FROM ' . self::getTableName();
		return $sqlQuery;
	}

	public function prepare_items() {
		global $wpdb, $_wp_column_headers;

		$screen		 = get_current_screen();
		$query		 = self::getQuery();
		$whereArr	 = array();

		$getParams = filter_input_array( INPUT_GET );

		$getOrderby	 = filter_input( INPUT_GET, "orderby" );
		$getOrder	 = filter_input( INPUT_GET, "order" );
		$getPaged	 = filter_input( INPUT_GET, "paged" );

		$orderby = !empty( $getOrderby ) ? esc_sql( $getOrderby ) : $this->orderBy;
		$order	 = !empty( $getOrder ) ? esc_sql( $getOrder ) : $this->order;

		if ( !empty( $getParams[ 'timeframe' ] ) && is_numeric( $getParams[ 'timeframe' ] ) ) {
			$whereArr[]		 = 'DateDiff(CurDate(), time) <= %d';
			$whereArrArgs[]	 = $getParams[ 'timeframe' ];
		}

		if ( !empty( $getParams[ 'report' ] ) && $getParams[ 'report' ] != 'All' ) {
			$whereArr[]		 = 'report = %s';
			$whereArrArgs[]	 = $getParams[ 'report' ];
		}

		if ( !empty( $whereArr ) ) {
			$query .= ' WHERE ' . implode( ' AND ', $whereArr );
			$query = $wpdb->prepare( $query, $whereArrArgs );
		}

		if ( !empty( $orderby ) & !empty( $order ) ) {
			$query .= ' ORDER BY ' . $orderby . ' ' . $order;
		}

		$totalitems	 = $wpdb->query( $query );
		$perpage	 = $this->perpage ? $this->perpage : 10;

		$paged = !empty( $getPaged ) ? esc_sql( $getPaged ) : '';

		if ( empty( $paged ) || !is_numeric( $paged ) || $paged <= 0 ) {
			$paged = 1;
		}

		$totalpages = ceil( $totalitems / $perpage );

		if ( !empty( $paged ) && !empty( $perpage ) ) {
			$offset = ($paged - 1) * $perpage;
			$query .= ' LIMIT ' . (int) $offset . ',' . (int) $perpage;
		}

		$this->set_pagination_args( array(
			"total_items"	 => $totalitems,
			"total_pages"	 => $totalpages,
			"per_page"		 => $perpage,
		) );

		$columns = $this->get_columns();
		if ( $screen ) {
			$_wp_column_headers[ $screen->id ] = $columns;
		}

		$this->items = $wpdb->get_results( $query );
	}

	/**
	 * Display the rows of records in the table
	 * @return string, echo the markup of the rows
	 */
	function display_rows() {
		/*
		 * Get the records registered in the prepare_items method
		 */
		$records = $this->items;

		/*
		 * Get the columns registered in the get_columns and get_sortable_columns methods
		 */
		list( $columns, $hidden ) = $this->get_column_info();

		/*
		 * Loop for each record
		 */
		if ( !empty( $records ) ) {
			foreach ( $records as $item ) {

				/*
				 * Open the line
				 */
				echo '<tr id="record_' . $item->id . '">';
				foreach ( $columns as $column_name => $column_display_name ) {
					/*
					 * Style attributes for each col
					 */
					$class	 = "class='$column_name column-$column_name'";
					$style	 = '';
					if ( in_array( $column_name, $hidden ) ) {
						$style = ' style="display:none;"';
					}
					$attributes = $class . $style;

					/*
					 * Display the cell
					 */
					switch ( $column_name ) {
						case "cmcr_event_log_type": echo '<td ' . $attributes . '>' . $item->type . '</td>';
							break;
						case "cmcr_event_log_time": echo '<td ' . $attributes . '>' . $item->time . '</td>';
							break;
						case "cmcr_event_log_show": {
								echo '<td ' . $attributes . '>';
								echo '</td>';
							}
							break;
					}
				}

				/*
				 * Close the line
				 */
				echo'</tr>';
			}
		}
	}

}
