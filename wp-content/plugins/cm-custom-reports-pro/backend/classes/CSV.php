<?php
if( !class_exists('CM_CSV') )
{

    class CM_CSV
    {
        public $errors = array();
        public $delimiter = ',';
        public $enclosure = '"';

        const DEFAULT_CHARACTER_ENCODING = 'UTF-8';

        public function __construct()
        {
            ini_set('auto_detect_line_endings', TRUE);
            iconv_set_encoding('input_encoding', self::DEFAULT_CHARACTER_ENCODING);
        }

        public function stream($exportData, $filename = 'csv_export')
        {
            $outstream = fopen("php://temp", 'r+');

            foreach($exportData as $line)
            {
                fputcsv($outstream, $line, ',', '"');
            }
            rewind($outstream);

            header('Content-Encoding: UTF-8');
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename=' . $filename . '.csv');
            /*
             * Why including the BOM? - Marcin
             */
            echo "\xEF\xBB\xBF"; // UTF-8 BOM
            while(!feof($outstream))
            {
                echo fgets($outstream);
            }
            fclose($outstream);
            exit;
        }

        public function save($csv_data = array(), $filename = 'csvdata', $path = '/tmp')
        {
            if( empty($csv_data) )
            {
                return FALSE;
            }

            $file_path = $path . '/' . $filename . '.csv';
            $error_message = "Unable to write to file (location: '{$file_path}').  Perhaps try checking the file and folder via FTP.  If there's no file, it may be a permissions issue.  If there's a file, but it's only partially complete, then make sure you haven't run out of disk space.";

            $file = fopen($file_path, 'ab');
            foreach($csv_data as $csv_row)
            {
                $write_successful = fputcsv($file, $csv_row, $this->delimiter, $this->enclosure);
                if( !$write_successful )
                {
                    $this->errors[] = $error_message;
                    break;
                }
            }
            fclose($file);

            return FALSE;
        }

        public function line_count($file_path, $offset = -1)
        {
            $linecount = 0;
            $file = fopen($file_path, 'r');

            while($row = fgetcsv($file, NULL, $this->delimiter, $this->enclosure))
            {
                if( is_array($row) && count($row) > 1 )
                {
                    $linecount++;
                }
            }

            fclose($file);

            return $linecount + $offset; # Don't count CSV header row
        }

        public function load($file_path, $start = 0, $limit = 500)
        {
            if( !$this->file_valid($file_path) )
            {
                return FALSE;
            }

            $file = fopen($file_path, 'r');

            $csv_data = array();

            $title_row = fgetcsv($file, NULL, $this->delimiter, $this->enclosure);
            if( $title_row )
            {
                // Intercept 'id' field and change to 'ID'.  Needs to be 'id' to prevent an excel bug, but ID is preferable to match the posts table.
                if( $title_row[0] == 'id' )
                {
                    $title_row[0] = 'ID';
                }

                for($i = 1; $i < $start; $i++)
                {
                    $row = fgetcsv($file, NULL, $this->delimiter, $this->enclosure);
                }

                $count = 0;

                while($row = fgetcsv($file, NULL, $this->delimiter, $this->enclosure))
                {
                    if( $count >= $limit )
                    {
                        break;
                    }
                    if( is_array($row) && count($row) == count($title_row) )
                    {
                        $csv_data[] = array_combine($title_row, $row);
                        $count++;
                    }
                }
            }

            return $csv_data;
        }

        public function file_valid($file_path)
        {
            $file = fopen($file_path, 'r');
            $title_row = fgetcsv($file, NULL, $this->delimiter, $this->enclosure);

            if( $title_row === FALSE )
            {
                return FALSE;
            }

            if( is_array($title_row) && empty($title_row) )
            {
                return FALSE;
            }
            if( count($title_row) == 1 )
            {
                return FALSE;
            }

            $first_row = fgetcsv($file, NULL, $this->delimiter, $this->enclosure);

            if( count($title_row) <> count($first_row) )
            {
                return FALSE;
            }

            return TRUE;
        }

    }
}
