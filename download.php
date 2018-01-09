<?php
// Check download token
if (empty($_GET['mime']) OR empty($_GET['token']))
{
	exit('Invalid download token');
}

// Set operation params
$mime = filter_var($_GET['mime']);
$ext  = str_replace(array('/', 'x-'), '', strstr($mime, '/'));
$url  = base64_decode(filter_var($_GET['token']));
$name = urldecode($_GET['title']). '.' .$ext; 

// Fetch and serve
if ($url)
{
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

$size = remotefileSize($url);

//var_dump($size); exit();


	// Generate the server headers
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE)
	{
		header('Content-Type: "' . $mime . '"');
		header('Content-Disposition: attachment; filename="' . $name . '"');
		header('Expires: 0');
		header('Content-Length: '.$size);
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header("Content-Transfer-Encoding: binary");
		header('Pragma: public');
	}
	else
	{
		header('Content-Type: "' . $mime . '"');
		header('Content-Disposition: attachment; filename="' . $name . '"');
		header("Content-Transfer-Encoding: binary");
		header('Expires: 0');
		header('Content-Length: '.$size);
		header('Pragma: no-cache');
	}

	readfile($url);
	exit;
}

// Not found
exit('File not found');
