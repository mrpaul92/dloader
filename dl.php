<?php

$file = isset($_GET['file']) ? $_GET['file'] : null;

if(!$file) die('error');

download_file($file);

function download_file( $fullPath ){


    $fullPath = base64_decode($fullPath);

    $parsed_url = parse_url( $fullPath );

    $parse_query = parse_str($parsed_url['query'], $output);

    $mime = $output['mime'];
    $title = $output['title'];



    // Must be fresh start
    if( headers_sent() )
        die('Headers Sent');

    // Required for some browsers
    if(ini_get('zlib.output_compression'))
        ini_set('zlib.output_compression', 'Off');

    // File Exists?
    //if( url_exists($fullPath) ){
    
    if( $fullPath ){


        // Parse Info / Get Extension
        $fsize = remotefileSize($fullPath);
        $ext  = str_replace(array('/', 'x-'), '', strstr($mime, '/'));

        // fix
        //if($ext == '3gpp') $ext = '3gp';

        $name = urldecode($title). '.' .$ext;


        // Determine Content Type
        switch ($ext) {
            case "mp4": $ctype="video/mp4"; break;
            case "3gpp": $ctype="video/3gpp"; break;
            case "3gp": $ctype="video/3gp"; break;

            case "pdf": $ctype="application/pdf"; break;
            case "exe": $ctype="application/octet-stream"; break;
            case "zip": $ctype="application/zip"; break;
            case "doc": $ctype="application/msword"; break;
            case "xls": $ctype="application/vnd.ms-excel"; break;
            case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
            case "gif": $ctype="image/gif"; break;
            case "png": $ctype="image/png"; break;
            case "jpeg":
            case "jpg": $ctype="image/jpg"; break;
            default: die('Forbidden !!!');
        }

        header("Pragma: public"); // required
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false); // required for certain browsers
        header("Content-Type: $ctype");
        header("Content-Disposition: attachment; filename=\"".basename($name)."\";" );
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".$fsize);
        ob_clean();
        flush();
        readfile( $fullPath );
    } else{
        die('File Not Found');
    }

}




// get the filesize
function remotefileSize($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
    curl_exec($ch);
    $filesize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    curl_close($ch);
    if ($filesize) return $filesize;
}

// function for url exists
function url_exists( $url ){
    $headers = get_headers($url);
    //var_dump($headers); exit();
    return stripos($headers[0],"302 Found") ? true : false;
}



?>
