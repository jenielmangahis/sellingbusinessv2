<?php

abstract class CMCR_Report_Base {

    protected static $instance   = NULL;
    public static $loadedReports = array();
    public static $dataDateFrom  = NULL;

    abstract public function init();

    abstract public function getData();

    abstract public function getReportSlug();

    abstract public function getReportName();

    abstract public function getReportDescription();

    abstract public function getReportExtraOptions();

    abstract public function getGroups();

    public function __construct() {
        $slug = $this->getReportSlug();
        if ( !in_array( $slug, self::$loadedReports ) ) {
            self::$loadedReports[] = $slug;
            add_filter( 'cmcr_loaded_reports', array( $this, 'addReport' ) );
            add_action( 'cmcr_after_report_loaded', array( $this, 'addTabs' ) );
            add_filter( 'cmcr_report_groups_' . $slug, array( $this, 'addFavoritedGroup' ) );
            add_filter( 'cmcr_possible_graph_types_' . $slug, array( $this, 'getPossibleGraphTypes' ) );
            $this->init();
        }
    }

    public function getAdditionalCronActions() {
        return array();
    }

    public function getPossibleGraphTypes( $possibleGraphTypes ) {
        return $possibleGraphTypes;
    }

    public function addToFavorites() {
        $favoritedList = CMCR_Settings::getOption( CMCR_Settings::OPTION_FAVORITED_REPORTS );

        if ( !is_array( $favoritedList ) ) {
            $favoritedList = explode( ',', $favoritedList );
        }

        $slug = $this->getReportSlug();
        if ( !in_array( $slug, $favoritedList ) ) {
            $favoritedList[] = $slug;
            CMCR_Settings::setOption( CMCR_Settings::OPTION_FAVORITED_REPORTS, $favoritedList );
        }
    }

    public function removeFromFavorites() {
        $favoritedList = CMCR_Settings::getOption( CMCR_Settings::OPTION_FAVORITED_REPORTS );

        if ( !is_array( $favoritedList ) ) {
            $favoritedList = explode( ',', $favoritedList );
        }

        $slug = $this->getReportSlug();
        if ( in_array( $slug, $favoritedList ) ) {
            if ( ($key = array_search( $slug, $favoritedList )) !== false ) {
                unset( $favoritedList[ $key ] );
            }
            CMCR_Settings::setOption( CMCR_Settings::OPTION_FAVORITED_REPORTS, $favoritedList );
        }
    }

    public function addFavoritedGroup( $groups ) {
        $favoritedList = CMCR_Settings::getOption( CMCR_Settings::OPTION_FAVORITED_REPORTS );

        if ( !is_array( $favoritedList ) ) {
            $favoritedList = explode( ',', $favoritedList );
        }

        $reportSlug = $this->getReportSlug();
        if ( in_array( $reportSlug, $favoritedList ) ) {
            $groups[ 'fav' ] = 'Favorites';
        }
        return $groups;
    }

    public function getGroupsFiltered() {
        $groups = $this->getGroups();
        if ( empty( $groups ) ) {
            $groups = array();
        }
        $reportSlug   = $this->getReportSlug();
        $reportGroups = apply_filters( 'cmcr_report_groups_' . $reportSlug, $groups );
        return $reportGroups;
    }

    public function getDataFiltered() {
        $args = apply_filters( 'cmcr_report_data_args', array() );
        return $this->getData( $args );
    }

    public static function updateDataDateFrom( $newDataDateFrom = NULL ) {
        if ( $newDataDateFrom && (self::$dataDateFrom === NULL || strtotime( $newDataDateFrom ) < self::$dataDateFrom) ) {
            self::$dataDateFrom = $newDataDateFrom;
        }
    }

    public static function isReportPage() {
        $page = filter_input( INPUT_GET, 'cmcr_report' );
        return !empty( $page );
    }

    public static function addReportNameContent( $output, $data = array() ) {
        $additionalOutput = '';

        if ( !self::isReportPage() ) {
            return $output;
        }

        if ( empty( $data ) ) {
            $postArray = filter_input_array( INPUT_POST );
            $data      = $postArray;
        }

        if ( !empty( $data[ 'date_from' ] ) ) {
            $additionalOutput .= $data[ 'date_from' ];
        } else {
            $dataDateFrom = self::$dataDateFrom;
            $additionalOutput .= $dataDateFrom;
        }

        if ( !empty( $data[ 'date_to' ] ) ) {
            $additionalOutput .= ' ' . sprintf( CM_Custom_Reports::__( 'to %s' ), $data[ 'date_to' ] );
        } else {
            $additionalOutput .= ' ' . sprintf( CM_Custom_Reports::__( 'to %s' ), CM_Custom_Reports_Backend::getDate() );
        }

        if ( $additionalOutput ) {
            $output .= ' - ' . $additionalOutput;
        }

        return $output;
    }

    public function getReportNameFiltered( $data = array() ) {
        return apply_filters( 'cmcr_report_name_filter', $this->getReportName(), $data );
    }

    public function getReportOptions() {
        $reportBaseOptions                    = array();
        $reportBaseOptions[ 'slug' ]          = $this->getReportSlug();
        $reportBaseOptions[ 'class' ]         = $this->getReportClassname();
        $reportBaseOptions[ 'name' ]          = $this->getReportName();
        $reportBaseOptions[ 'name_filtered' ] = $this->getReportNameFiltered();
        $reportBaseOptions[ 'description' ]   = $this->getReportDescription();
        $reportBaseOptions[ 'db' ]            = $this->getSavedReportOptions();

        $reportOptions = array_merge( $reportBaseOptions, $this->getReportExtraOptions() );
        return $reportOptions;
    }

    public function getSavedReportOptions() {
        $optionName         = 'cmcr_report_options_' . $this->getReportSlug();
        $savedReportOptions = get_option( $optionName, array() );
        return (array) $savedReportOptions;
    }

    public function setSavedReportOptions( $value = array() ) {
        $optionName = 'cmcr_report_options_' . $this->getReportSlug();
        $saveResult = update_option( $optionName, $value );
        return (bool) $saveResult;
    }

    public function getOption( $optionKey, $default = FALSE ) {
        $options = $this->getReportOptions();
        $value   = (!empty( $options[ $optionKey ] )) ? $options[ $optionKey ] : $default;
        $value   = ($default === $value && !empty( $options[ 'db' ][ $optionKey ] )) ? $options[ 'db' ][ $optionKey ] : $default;
        return $value;
    }

    public function addReport( $reports ) {
        $reportOptions                       = $this->getReportOptions();
        $reports[ $reportOptions[ 'slug' ] ] = apply_filters( 'cmcr_load_single_report', $reportOptions );
        do_action( 'cmcr_after_report_loaded', $reports, $reportOptions );
        return $reports;
    }

    public function addTabs() {
        $options = $this->getReportOptions();
        if ( array_key_exists( 'graph', $options ) || in_array( 'graph', $options, true ) ) {
            add_filter( 'cmcr-report-' . $this->getReportSlug() . '-tabs-array', array( 'CMCR_Graph_Module', 'addGraphTab' ) );
            add_filter( 'cmcr-report-' . $this->getReportSlug() . '-tab-content-1', array( 'CMCR_Graph_Module', 'displayGraph' ) );
        }
        if ( array_key_exists( 'cron', $options ) || in_array( 'cron', $options, true ) ) {
            add_filter( 'cmcr-report-' . $this->getReportSlug() . '-tabs-array', array( 'CMCR_Cron_Module', 'addCronTab' ) );
            add_filter( 'cmcr-report-' . $this->getReportSlug() . '-tab-content-2', array( 'CMCR_Cron_Module', 'displayCron' ) );
        }
    }

    public function getReportClassname() {
        return get_called_class();
    }

    public function getReportIconUrl() {
        $iconUrl = CM_Custom_Reports_Backend::$imagesPath . 'default-icon.png';
        return $iconUrl;
    }

    public function isFavorited() {
        $groups = $this->getGroupsFiltered();
        $result = array_key_exists( 'fav', $groups );
        return (bool) $result;
    }

    public function getFavIconUrl() {
        $isFavorited = $this->isFavorited();
        $icon        = $isFavorited ? 'fav-hover-icon' : 'fav-icon';
        $iconUrl     = CM_Custom_Reports_Backend::$imagesPath . $icon . '.png';
        return $iconUrl;
    }

    public function getFavUrl() {
        $isFavorited = $this->isFavorited();
        $urlBase     = CM_Custom_Reports_Backend::getReportListUrl();
        $argArrayKey = $isFavorited ? 'unfav' : 'fav';
        $url         = esc_url( add_query_arg( array( $argArrayKey => $this->getReportSlug() ), $urlBase ) );
        return $url;
    }

    public function getReportAdditionalInfo() {
        $content = '';

        $options  = $this->getReportOptions();
        $on       = !empty( $options[ 'db' ][ 'cmcr_cron_on' ] ) ? $options[ 'db' ][ 'cmcr_cron_on' ] : FALSE;
        $interval = !empty( $options[ 'db' ][ 'cmcr_cron_interval' ] ) ? $options[ 'db' ][ 'cmcr_cron_interval' ] : 'none';
        $hour     = !empty( $options[ 'db' ][ 'cmcr_cron_hour' ] ) ? $options[ 'db' ][ 'cmcr_cron_hour' ] : '00:00';

        if ( $on && $interval !== 'none' ) {
            $content .= '<br/><span> Scheduled: ' . $interval . ' at ' . $hour . '</span>';
        }
        return $content;
    }

    public function displayReportOnList() {
        $url            = CM_Custom_Reports_Backend::getReportUriBySlug( $this->getReportSlug() );
        $favUrl         = $this->getFavUrl();
        $favoritedClass = $this->isFavorited() ? 'cmcr_report_unfav' : 'cmcr_report_fav';
        ob_start()
        ?>
        <div class="cmcr_report_icon_container">
            <a href="<?php echo $url; ?>"><img src="<?php echo $this->getReportIconUrl(); ?>" /></a>
        </div>
        <div class="cmcr_report_info_container">

            <div class="<?php echo $favoritedClass; ?>">
                <a href="<?php echo $favUrl; ?>" title="Add to favorites"><img src="<?php echo $this->getFavIconUrl(); ?>" /></a>
            </div>

            <div class="cmcr_report_link">
                <a href="<?php echo $url; ?>"><?php echo $this->getReportName() ?></a>
            </div>
            <div class="cmcr_report_description">
                <?php echo $this->getReportDescription(); ?>
            </div>
            <div class="cmcr_report_additional_info">
                <?php echo $this->getReportAdditionalInfo(); ?>
            </div>
        </div>
        <?php
        $content        = ob_get_clean();
        return $content;
    }

    public function displayReport() {
        ob_start();
        include_once CM_Custom_Reports_Backend::$viewsPath . 'single-report.phtml';
        $content = ob_get_clean();

        return $content;
    }

    protected function getReportTabs() {
        $settingsTabsArrayBase = array(
//            '99' => 'Info',
        );

        $reportSlug        = $this->getReportSlug();
        $filterName        = 'cmcr-report-' . $reportSlug . '-tabs-array';
        $settingsTabsArray = apply_filters( $filterName, $settingsTabsArrayBase );
        return $settingsTabsArray;
    }

    /**
     * Function displays (default) or returns the setttings tabs
     *
     * @param type $return
     * @return string
     */
    protected function displayReportTabs( $return = false ) {
        $content = '';

        $reportSlug        = $this->getReportSlug();
        $settingsTabsArray = $this->getReportTabs();
        if ( $settingsTabsArray ) {
            foreach ( $settingsTabsArray as $tabKey => $tabLabel ) {
                $filterName = 'cmcr-report-' . $reportSlug . '-tab-content-' . $tabKey;

                $content .= '<div id="tabs-' . $tabKey . '">';
                $tabContent = apply_filters( $filterName, '' );
                $content .= $tabContent;
                $content .= '</div>';
            }
        }

        if ( $return ) {
            return $content;
        }
        echo $content;
    }

    /**
     * Function displays (default) or returns the setttings tabs
     *
     * @param type $return
     * @return string
     */
    protected function displayReportTabsControls( $return = false ) {
        $content           = '';
        $settingsTabsArray = $this->getReportTabs();

        ksort( $settingsTabsArray );

        if ( $settingsTabsArray ) {
            $content .= '<ul>';
            foreach ( $settingsTabsArray as $tabKey => $tabLabel ) {
                $content .= '<li><a href="#tabs-' . $tabKey . '">' . $tabLabel . '</a></li>';
            }
            $content .= '</ul>';
        }

        if ( $return ) {
            return $content;
        }
        echo $content;
    }

}
