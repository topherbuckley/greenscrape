<?php

function mssafe_csv($filepath, $data, $header = array())
{
    if ( $fp = fopen($filepath, 'w') ) {
        $show_header = true;
        if ( empty($header) ) {
            $show_header = false;
            reset($data);
            $line = current($data);
            if ( !empty($line) ) {
                reset($line);
                $first = current($line);
                if ( substr($first, 0, 2) == 'ID' && !preg_match('/["\\s,]/', $first) ) {
                    array_shift($data);
                    array_shift($line);
                    if ( empty($line) ) {
                        fwrite($fp, "\"{$first}\"\r\n");
                    } else {
                        fwrite($fp, "\"{$first}\",");
                        fputcsv($fp, $line);
                        fseek($fp, -1, SEEK_CUR);
                        fwrite($fp, "\r\n");
                    }
                }
            }
        } else {
            reset($header);
            $first = current($header);
            if ( substr($first, 0, 2) == 'ID' && !preg_match('/["\\s,]/', $first) ) {
                array_shift($header);
                if ( empty($header) ) {
                    $show_header = false;
                    fwrite($fp, "\"{$first}\"\r\n");
                } else {
                    fwrite($fp, "\"{$first}\",");
                }
            }
        }
        if ( $show_header ) {
            fputcsv($fp, $header);
            fseek($fp, -1, SEEK_CUR);
            fwrite($fp, "\r\n");
        }
        foreach ( $data as $line ) {
            fputcsv($fp, $line);
            fseek($fp, -1, SEEK_CUR);
            fwrite($fp, "\r\n");
        }
        fclose($fp);
    } else {
        return false;
    }
    return true;
}

?>