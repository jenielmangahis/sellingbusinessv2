<?php

// reference the Dompdf namespace
use Dompdf\Dompdf;
use Dompdf\Options;

CMCR_PDF_Module::init();

class CMCR_PDF_Module {

    public static function init() {
        include_once CMCR_PLUGIN_DIR . '/backend/libs/dompdf/autoload.inc.php';
        add_action( 'wp_ajax_cmcr-export-pdf', array( __CLASS__, 'exportPDF' ) );
        add_action( 'wp_ajax_nopriv_cmcr-export-pdf', array( __CLASS__, 'exportPDF' ) );
    }

    public static function exportPDF() {
        $postArray = filter_input_array( INPUT_POST );

        if ( !empty( $postArray[ 'reportData' ] ) ) {
            $reportData = json_decode( $postArray[ 'reportData' ], TRUE );
            $reportMeta = json_decode( $postArray[ 'reportMeta' ], TRUE );

            if ( !empty( $reportData ) && !empty( $reportMeta[ 'class' ] ) ) {
                $output = '';
                $args   = array( 'output_pdf_template' => 1 );
                $report = new $reportMeta[ 'class' ];
                CM_Custom_Reports_Backend::setCurrentReport( $report );
                $html   = $report->outputPDFTemplate( $output, $args );
                CMCR_PDF_Module::outputToBrowser( $html );
            }
        }
        echo 'Something went wrong.';
        die();
    }

    public static function validateFilename( $filename = '' ) {
        if ( empty( $filename ) || false === strpos( $filename, '.pdf' ) ) {
            $report   = CM_Custom_Reports_Backend::getCurrentReport();
            $date     = date( 'Y-m-d' );
            $filename = $report->getReportSlug() . '-report-' . $date . '.pdf';
        }
        return $filename;
    }

    public static function saveToFile( $html, $file = '' ) {
        @ini_set( 'max_execution_time', 300 );
        $result   = false;
        $filename = self::validateFilename( $file );

        $options = new Options();
        $options->set( 'isRemoteEnabled', true );
        $options->set( 'isHtml5ParserEnabled', true );
        $dompdf  = new Dompdf( $options );
        $dompdf->loadHtml( $html );
        $dompdf->setPaper( 'A4', 'portrait' );
        $dompdf->render();
        $output  = $dompdf->output();

        if ( !empty( $output ) ) {
            $result = wp_upload_bits( $filename, null, $output );
        }
        return $result;
    }

    public static function saveToAttachments( $html, $filename = '' ) {
        $fileArr     = self::saveToFile( $html, $filename );
        $attachments = array();
        if ( !empty( $fileArr[ 'file' ] ) ) {
            $attachments[] = $fileArr[ 'file' ];
        }
        return $attachments;
    }

    public static function outputToBrowser( $html, $file = '' ) {
        @ini_set( 'max_execution_time', 300 );
        $result   = false;
        $filename = self::validateFilename( $file );

        $options = new Options();
        $options->set( 'isRemoteEnabled', true );
        $options->set( 'isHtml5ParserEnabled', true );
        $dompdf  = new Dompdf( $options );
        $dompdf->loadHtml( $html );
        $dompdf->setPaper( 'A4', 'portrait' );
        $dompdf->render();
        $output  = $dompdf->stream( $filename );
        return $result;
    }

}
