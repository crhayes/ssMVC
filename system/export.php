<?php

class Export {

    function csv_from_mysql_resource($resource, $file_name)
    {
        $output = "";
        $headers_printed = false;

        while ($row = mysql_fetch_array($resource, MYSQL_ASSOC))
        {
            // print out column names as the first row
            if (!$headers_printed)
            {
                $output .= join(',', str_replace('_', ' ', array_keys($row))) . "\n";
                $headers_printed = true;
            }

            // remove newlines from all the fields and
            // surround them with quote marks
            foreach ($row as &$value)
            {
                $value = str_replace("\r\n", "", $value);
                $value = "\"" . $value . "\"";
            }

            $output .= join(',', $row) . "\n";
        }

        // set the headers
        $size_in_bytes = strlen($output);
        header("Content-type: application/csv");
        header("Content-disposition:  attachment; filename=$file_name; size=$size_in_bytes");

        return $output;
    }

}