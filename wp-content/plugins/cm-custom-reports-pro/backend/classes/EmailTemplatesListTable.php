<?php

if ( !class_exists( 'WP_List_Table' ) ) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class CMCR_Email_Templates_List_Table extends WP_List_Table {

    const DB_VERSION = '1.2';

    protected $orderBy = 'DESC';
    protected $order   = '';
    protected $perpage = '';

    public static function getTableName() {
        global $wpdb;
        $tablePrefix = $wpdb->prefix;
        $tableName   = $tablePrefix . "cmcr_templates";
        return $tableName;
    }

    public static function _install() {
        $table_name = self::getTableName();

        $installed_ver = get_option( 'cmcr_templates_db_version' );
        if ( $installed_ver != self::DB_VERSION ) {
            $sql = "CREATE TABLE " . $table_name . " (
          id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
          name VARCHAR(64) NOT NULL,
          subject VARCHAR(255) NOT NULL,
          content TEXT NULL,
          PRIMARY KEY  (id)
        );";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta( $sql );

            // notice that we are updating option, rather than adding it
            update_option( 'cmcr_templates_db_version', self::DB_VERSION );
        }
    }

    public function __construct() {
        parent::__construct( array(
            'singular' => 'cmcr_template',
            'plural'   => 'cmcr_templates',
            'ajax'     => true
        ) );
    }

    public function extra_tablenav( $which ) {
        $report_type    = filter_input( INPUT_GET, "report" );
        $timeframe_type = filter_input( INPUT_GET, "timeframe" );

        if ( $which === 'top' ) {
            $url     = esc_url( add_query_arg( array( 'page' => 'cm-custom-reports-templates', 'cmcr_template' => 'new' ), admin_url( 'admin.php' ) ) );
            $content = '<a href="' . $url . '" class="button-primary" value="Filter">' . CM_Custom_Reports::__( 'New Template' ) . '</a>';
            echo $content;
        }
    }

    public function get_columns() {

        $columns = array(
            'cmcr_email_template_name'    => __( 'Template Name' ),
            'cmcr_email_template_subject' => __( 'Subject' ),
            'cmcr_email_template_content' => __( 'Content' ),
            'cmcr_email_template_show'    => __( 'Options' )
        );

        return $columns;
    }

    public function get_columns_fields() {
        $columnsFields = array(
            'cmcr_email_template_name'    => 'name',
            'cmcr_email_template_subject' => 'subject',
            'cmcr_email_template_content' => 'content',
            'cmcr_email_template_show'    => 'id',
        );

        return $columnsFields;
    }

    public function get_sortable_columns() {
        return $sortable = array(
            'cmcr_email_template_time'   => array( 'time', 'DESC' ),
            'cmcr_email_template_report' => array( 'report', 'DESC' ),
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

    public static function saveTemplate() {
        $post = filter_input_array( INPUT_POST );

        if ( !empty( $post ) ) {
            if ( isset( $post[ '_cmcr-nonce' ] ) && wp_verify_nonce( $post[ '_cmcr-nonce' ], 'cmcr-single-template' ) ) {
                $saveArr = array();
                foreach ( $post as $key => $value ) {
                    if ( strpos( $key, 'cmcr_template' ) !== FALSE ) {
                        $saveArr[ $key ] = $value;
                    }
                }

                if ( !empty( $saveArr ) ) {
                    global $wpdb;
                    $data   = array(
                        'name'    => $saveArr[ 'cmcr_template_name' ],
                        'subject' => $saveArr[ 'cmcr_template_subject' ],
                        'content' => $saveArr[ 'cmcr_template_content' ],
                    );
                    $format = array( '%s', '%s', '%s' );

                    if ( !empty( $saveArr[ 'cmcr_template_id' ] ) ) {
                        $where       = array( 'id' => $saveArr[ 'cmcr_template_id' ] );
                        $whereFormat = array( '%d' );
                        $result      = $wpdb->update( self::getTableName(), $data, $where, $format, $whereFormat );
                    } else {
                        $result = $wpdb->insert( self::getTableName(), $data, $format );
                        if ( $result ) {
                            $url = self::getTemplateUrl( $wpdb->insert_id );
                            wp_redirect( $url );
                        }
                    }

                    return $result;
                }
            }
        }
    }

    public static function getListUrl() {
        $url = esc_url( add_query_arg( array( 'page' => 'cm-custom-reports-templates' ), admin_url( 'admin.php' ) ) );
        return $url;
    }

    public static function getTemplateUrl( $id ) {
        $url = esc_url( add_query_arg( array( 'cmcr_template' => $id ), self::getListUrl() ) );
        return $url;
    }

    public static function getDeleteUrl( $templateId ) {
        $result = esc_url( add_query_arg( array( 'cmcr_template' => $templateId, 'action' => 'delete' ), self::getListUrl() ) );
        return $result;
    }

    public static function getQuery() {
        $sqlQuery = 'SELECT * FROM ' . self::getTableName();
        return $sqlQuery;
    }

    public function prepare_items() {
        global $wpdb, $_wp_column_headers;

        $screen = get_current_screen();
        $query  = self::getQuery();

        $getOrderby = filter_input( INPUT_GET, "orderby" );
        $getOrder   = filter_input( INPUT_GET, "order" );
        $getPaged   = filter_input( INPUT_GET, "paged" );

        $orderby = !empty( $getOrderby ) ? esc_sql( $getOrderby ) : $this->orderBy;
        $order   = !empty( $getOrder ) ? esc_sql( $getOrder ) : $this->order;

        if ( !empty( $orderby ) & !empty( $order ) ) {
            $query .= ' ORDER BY ' . $orderby . ' ' . $order;
        }

        $totalitems = $wpdb->query( $query );
        $perpage    = $this->perpage ? $this->perpage : 5;

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
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page"    => $perpage,
        ) );

        $columns = $this->get_columns();
        if ( $screen ) {
            $_wp_column_headers[ $screen->id ] = $columns;
        }

        $this->items = $wpdb->get_results( $query );
    }

    public static function getTemplates() {
        global $wpdb;
        $query  = self::getQuery();
        $result = $wpdb->get_results( $query, OBJECT_K );
        return $result;
    }

    public static function getTemplate( $id ) {
        if ( !is_numeric( $id ) ) {
            return '-Wrong Id-';
        }

        global $wpdb;
        $query  = self::getQuery();
        $query .= ' WHERE id=%d';
        $result = $wpdb->get_row( $wpdb->prepare( $query, $id ) );
        if ( is_null( $result ) ) {
            return '<span class="error">-Missing template-</span>';
        }
        return $result;
    }

    public static function getDefaultEmailSubject( $data = array() ) {
        $subject = apply_filters( 'cmcr_email_subject', CM_Custom_Reports::__( 'CM Custom Report' ), $data );
        return $subject;
    }

    public static function getDefaultEmailContent( $data = array() ) {
        $content = apply_filters( 'cmcr_email_content', CM_Custom_Reports::__( 'Here is the link to your report: [report_link]' ), $data );
        return $content;
    }

    public static function getDefaultTemplate( $data = array() ) {
        $defaultTemplate = new stdClass();

        $defaultTemplate->subject = self::getDefaultEmailSubject( $data );
        $defaultTemplate->content = self::getDefaultEmailContent( $data );
        return $defaultTemplate;
    }

    public static function deleteTemplate( $id ) {
        if ( !is_numeric( $id ) ) {
            return '-Wrong Id-';
        }

        global $wpdb;
        $query  = 'DELETE FROM ' . self::getTableName() . ' WHERE id=%d';
        $result = $wpdb->query( $wpdb->prepare( $query, $id ) );
        return $result;
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
                    $class = "class='$column_name column-$column_name'";
                    $style = '';
                    if ( in_array( $column_name, $hidden ) ) {
                        $style = ' style="display:none;"';
                    }
                    $attributes = $class . $style;

                    /*
                     * Display the cell
                     */
                    switch ( $column_name ) {
                        case "cmcr_email_template_name": echo '<td ' . $attributes . '>' . $item->name . '</td>';
                            break;
                        case "cmcr_email_template_subject": echo '<td ' . $attributes . '>' . $item->subject . '</td>';
                            break;
                        case "cmcr_email_template_content": echo '<td ' . $attributes . '>' . $item->content . '</td>';
                            break;
                        case "cmcr_email_template_show": {
                                $url       = self::getTemplateUrl( $item->id );
                                echo '<td ' . $attributes . '>';
                                echo '<a class="button-primary" href="' . $url . '">' . CM_Custom_Reports::__( 'Edit Template' ) . '</a>';
                                $deleteUrl = self::getDeleteUrl( $item->id );
                                echo ' <a class="button-secondary" href="' . $deleteUrl . '">' . CM_Custom_Reports::__( 'Delete Template' ) . '</a>';
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
