<?php
/* 
 * -------------------------------------------------------
 * forceDownload
 * -------------------------------------------------------
 * @Version: 1.0.0
 * @Author:  FireDart
 * @Link:    http://firedartstudios.com/
 * @GitHub:  https://github.com/FireDart/snippets/Downloads
 * @License: The MIT License (MIT)
 * 
 * Allows you to:
 * + Force a download of a file
 * + Give that download file a name
 * + Limit the speed of the download in KB/s
 * + Allow resumable downloads
 * 
 * -------------------------------------------------------
 * WARNING
 * -------------------------------------------------------
 * MAKE SURE YOU SANATIZE DOWNLOAD PATHS IF IT IS A USER 
 * INPUT, OTHERWISE ATTACKS CAN GAIN ACCESS TO YOUR INTERAL 
 * FILE SYSTEM.
 * 
 * -------------------------------------------------------
 * Recommendations
 * -------------------------------------------------------
 * This script does provide the ability to limit the users 
 * download rate, however, it is suggested that you use an 
 * alternative in apache2, nginx, Lighttpd or what other 
 * software you are using to host your site.
 * 
 * apache2 (No built in feature use mod_bw)
 * - http://bwmod.sourceforge.net/
 * - sudo apt-get install libapache2-mod-bw
 * - sudo a2enmod bw
 * - Guide to install: 
 * - http://freedif.org/bandwidth-restriction-how-to-limit-the-download-speed-for-your-visitors/
 * nginx
 * - http://wiki.nginx.org/NginxHttpCoreModule#limit_rate
 * Lighttpd
 * - http://redmine.lighttpd.net/projects/lighttpd/wiki/Docs_TrafficShaping#Selective-traffic-shaping-plugin15-svn
 * 
 * -------------------------------------------------------
 * Requirements
 * -------------------------------------------------------
 * PHP 5.3.0+
 * 
 * -------------------------------------------------------
 * Usage
 * -------------------------------------------------------
 * Basic
 * forceDownload("path/to/file.ext");
 * 
 * All features
 * forceDownload("path/to/file.ext", "myFileName.ext", 1000);
 * 
 */
/* 
 * forceDownload
 * 
 * Forces the page to download the specified file
 * 
 * @param str $download The path to the file you want to force a download
 * @param str $name     The name of the file the user will see when he downloads
 * @param int $speed    The speed in kb/s of the download, (ex. 1000KB/s = 1MB/s)
 * @return mixed
 */
function forceDownload($download, $name = null, $speed = null) {
	try {
		// Try and disable gzip compression for IE
		if(ini_get('zlib.output_compression')) {
			ini_set('zlib.output_compression', 'Off');
		}
		// Can the file been found?
		if(!file_exists($download) && !is_file($download)) {
			throw new Exception('Unable to find specified download, are you point to a valid file?');
		}
		// Get a name for our file
		if($name == null) {
			$name  = basename($download);
		}
		// Get file extension
		// This is split apart to follow PHP strict standard
		$extension = explode('.', $download);
		$extension = end($extension);
		$extension = strtolower($extension);
		// Security check
		$blacklist = array("php", "ini", "conf");
		if(in_array($extension, $blacklist)) {
			throw new Exception('Sorry but this type of file is blacklisted and can\'t be downloaded unless the administrator white lists it.');
		}
		// Check that the provided $name is the same file type
		// This is split apart to follow PHP strict standard
		$nameExten = explode('.', $name);
		$nameExten = end($nameExten);
		$nameExten = strtolower($nameExten);
		if($nameExten != $extension) {
			throw new Exception('The name of the file you supplied does not match the original download extension.');
		}
		// Get file size
		$fileSize  = filesize($download);
		// Content-Types
		$ctype     = "application/octet-stream";
		// List of specific ctypes, add more if can
		$ctypes    = array(
			// General
			 "txt" => "text/plain",
			 "htm" => "text/html",
			"html" => "text/html",
			 "css" => "text/css",
			  "js" => "application/javascript",
			"json" => "application/json",
			 "xml" => "application/xml",
			 
			// Archive
			 "zip" => "application/zip",
			 "rar" => "application/x-rar-compressed",
			  "7z" => "application/x-7z-compressed",
			 "exe" => "application/octet-stream",
			 "msi" => "application/x-msdownload",
			 "cab" => "application/vnd.ms-cab-compressed",
			
			// Images
			"jpeg" => "image/jpg",
			 "jpg" => "image/jpg",
			 "png" => "image/png",
			 "gif" => "image/gif",
			"webp" => "image/webp",
			 "bmp" => "image/bmp",
			 "ico" => "image/vnd.microsoft.icon",
			"tiff" => "image/tiff",
			 "tif" => "image/tiff",
			 "svg" => "image/svg+xml",
			 "svgz" => "image/svg+xml",
			 
			// Audio
			 "mp3" => "audio/mpeg",
			"flac" => "audio/x-flac",
			 "ogg" => "audio/ogg",
			 "wma" => "audio/x-ms-wma",
			 
			// Video
			 "mp4" => "video/mp4",
			 "mkv" => "video/x-matroska, audio/x-matroska",
			"webm" => "video/webm, audio/webm",
			 "ogv" => "video/ogv",
			 "wmv" => "video/x-ms-wmv",
			 "mpg" => "video/mpeg",
			 "avi" => "video/x-msvideo",
			 
			// Adobe
			 "pdf" => "application/pdf",
			 "psd" => "image/vnd.adobe.photoshop",
			  "ai" => "application/postscript",
			 "eps" => "application/postscript",
			  "ps" => "application/postscript",

			// MS Office
			 "doc" => "application/msword",
			 "rtf" => "application/rtf",
			 "xls" => "application/vnd.ms-excel",
			 "ppt" => "application/vnd.ms-powerpoint",

			// LibreOffice
			 "odt" => "application/vnd.oasis.opendocument.text",
			 "ods" => "application/vnd.oasis.opendocument.spreadsheet",
		);
		// Use specific Content-Type if we can
		if(isset($ctypes[$extension])) {
			$ctype = $ctypes[$extension];
		}
		// We got everything we need, send headers & start file download
		// We will be sending chunks for more reliable transfer & to allow resumable downloads
		$file = fopen($download, "rb"); // rb, b is for Windows but might as well use it on Linux
		if(isset($speed) && is_int($speed)) {
			// We make the script sleep for a 1 second below for every chunk
			$chunk = 1024 * $speed;
		} else {
			// 8KB so we don't kill the server, this will go as fast as the script executes (not per second)
			$chunk = 1024 * 8;
		}
		// Start processing file
		if(!$file) {
			throw new Exception('Could not open file.');
		} else {
			// Set headers
			header('Content-Description: File Transfer');
			// Size
			if(isset($_SERVER['HTTP_RANGE'])) {
				file_put_contents("logs.txt", $_SERVER['HTTP_RANGE'] . "\n", FILE_APPEND);
				// Learn about range:
				// http://tools.ietf.org/id/draft-ietf-http-range-retrieval-00.txt	  range1, range2/total
				list($param, $range) = explode('=', $_SERVER['HTTP_RANGE']); // bytes=0-1024,123-124/total
				// Bad request - range unit is not 'bytes'
				if(strtolower(trim($param)) != 'bytes') {
					header("HTTP/1.1 400 Invalid Request");
					exit;
				}
				// Split ranges
				$range = explode(',', $range);
				// We only deal with the first requested range
				$range = explode('-', $range[0]);
				// Bad request - 'bytes' parameter is not valid
				if(count($range) != 2) {
					header("HTTP/1.1 400 Invalid Request");
					exit;
				}
				// Check range for valid values
				if(empty($range[0])) {
					$offset = 0;
				} else {
					$offset = $range[0];
				}
				if(empty($range[1])) {
					$end = $fileSize - 1;
				} else {
					$end = $range[1];
				}
				if($offset > 0 || $end < ($filesize - 1)) {
					header("HTTP/1.1 206 Partial Content");
				}
				// Allow resumable downloads
				header("Accept-Ranges: bytes");
				header("Content-Range: bytes $offset-$end/$fileSize");
			} else {
				$offset = 0;
			}
			header("Content-Length: " . $fileSize);
			// IE 6 fix
			header("Pragma: public");
			header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
			// Prevent Caching
			header("Expires: -1");
			// Type
			header("Content-Type: " . $ctype);
			// Attach file
			header("Content-Disposition: attachment; filename=\"$name\"");
			// Do we need to start at a specific amount?
			fseek($file, $offset);
			// Begin download
			ob_start();
			while(!feof($file)) {
				echo fread($file, $chunk);
				flush();
				ob_flush();
				// If a speed limit is set, limit every 1 second
				if(isset($speed)) {
					sleep(1);
				}
			}
			fclose($file);
			exit;
		}
	// Catch all errors and report back
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}