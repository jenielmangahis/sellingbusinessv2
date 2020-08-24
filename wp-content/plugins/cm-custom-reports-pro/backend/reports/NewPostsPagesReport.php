<?php
new CMCR_New_Posts_Pages_Report();

class CMCR_New_Posts_Pages_Report extends CMCR_Report_Base {

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
		return 'new-posts-pages';
	}

	public function getReportDescription() {
		return CM_Custom_Reports::__( 'CSV report containing basic information about all published posts and pages (date, type, title, link and categories).' );
	}

	public function getReportName() {
		return CM_Custom_Reports::__( 'Posts &amp; Pages' );
	}

	public function getGroups() {
		return array( 'posts' => $this->getReportName() );
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
			$dateQuery[ 'before' ] = date( 'd-m-Y' );
		}

		return $dateQuery;
	}

	public function getColumns() {
		$columns = array(
			'date'			 => 'Published Date',
			'type'			 => 'Page Type',
			'description'	 => 'Description',
			'link'			 => 'Link',
			'categories'	 => 'Categories',
		);
		return $columns;
	}

	public function getData( $dataArgs = array( 'json' => FALSE ) ) {
		static $savedData = array();

		$postByDate	 = array();
		$dataPosts	 = array();

		$args = array(
			'post_type'		 => array( 'post', 'page' ),
			'posts_per_page' => -1,
			'fields'		 => 'ids',
			'orderby'		 => 'date',
			'order'			 => 'asc'
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
		$query	 = new WP_Query( $args );
		$posts	 = $query->get_posts();
		if ( !empty( $posts ) ) {
			$firstPost = true;

			foreach ( $posts as $postId ) {
				$post		 = get_post( $postId );
				$time		 = strtotime( $post->post_date );
				$realDate	 = CM_Custom_Reports_Backend::getDate( $time );
				$realTime	 = strtotime( $realDate );

				if ( $firstPost ) {
					self::updateDataDateFrom( date( 'd-m-Y', $realTime ) );
					$firstPost = false;
				}

				if('post' === $post->post_type){
					$categoriesArr = $cats = wp_get_object_terms($post->ID, 'category', array('fields' => 'names'));
					if(!empty($categoriesArr)){
						$categories = implode(',', $categoriesArr);
					}
				} else {
					$categories = '';
				}

				$postData = array(
					'date'			 => $realDate,
					'type'			 => ('post' === $post->post_type) ? 'Blog Post' : 'Static Page',
					'description'	 => $post->post_title,
					'link'			 => get_permalink( $post ),
					'categories'	 => $categories,
				);

				$dataPosts[] = array_values( $postData );
			}
		}

		$result = array(
			array(
				'label'	 => __( 'Posts' ),
				'data'	 => $dataPosts,
			),
		);

		if ( $json ) {
			$result = json_encode( $result );
		}
		$savedData[ $argsKey ] = $result;
		return $result;
	}

}
