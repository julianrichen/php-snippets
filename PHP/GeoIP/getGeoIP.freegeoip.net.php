<?php
/* 
 * -------------------------------------------------------
 * getGeoIP.freegeoip.net
 * -------------------------------------------------------
 * @Version: 1.0.0
 * @Author:  FireDart
 * @Link:    http://www.firedartstudios.com/
 * @GitHub:  https://github.com/FireDart/snippets/PHP/GeoIP/
 * @License: The MIT License (MIT)
 * 
 * Used to get geo information from a selected ip using the 
 * freegeoip.net service, up to 10,000 queries an hour.
 * 
 * -------------------------------------------------------
 * Requirements
 * -------------------------------------------------------
 * PHP 5.3.0+
 * 
 * -------------------------------------------------------
 * Usage
 * -------------------------------------------------------
 * Basic / Detect IP
 * getGeoIP();
 * 
 * Input IP to check
 * getGeoIP("aaa.bbb.ccc.ddd", false);
 * 
 */
/* 
 * getGeoIP
 * 
 * Returns GEO info about an IP address from 
 * FreeGeoIP.net, allows 10,000 queries per hour.
 * 
 * @param str     $ip        IP to check leave blank to get REMOTE_ADDR
 * @param boolean $jsonArray Return JSON as array?
 * @return (obj|booealn) If info can be return use obj, otherwise report false.
 */
function getGeoIP($ip = null, $jsonArray = false) {
	try {
		// If no IP is provided use the current users IP
		if($ip == null) {
			$ip   = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
		}
		// If the IP is equal to 127.0.0.1 (IPv4) or ::1 (IPv6) then cancel, won't work on localhost
		if($ip == "127.0.0.1" || $ip == "::1") {
			throw new Exception('You are on a local sever, this script won\'t work right.');
		}
		// Make sure IP provided is valid
		if(!filter_var($ip, FILTER_VALIDATE_IP)) {
			throw new Exception('Invalid IP address "' . $ip . '".');
		}
		if(!is_bool($jsonArray)) {
			throw new Exception('The second parameter must be a boolean - true (return array) or false (return JSON object); default is false.');
		}
		// Fetch JSON data with the IP provided
		$url  = "http://freegeoip.net/json/" . $ip;
		// Return the contents, supress errors because we will check in a bit
		$json = @file_get_contents($url);
		// Did we manage to get data?
		if($json === false) {
			return false;
		}
		// Decode JSON
		$json = json_decode($json, $jsonArray);
		// If an error happens we can assume the JSON is bad or invalid IP
		if($json === null) {
			// Return false
			return false;
		} else {
			// Otherwise return JSON data
			return $json;
		}
	} catch(Exception $e) {
		return $e->getMessage();
	}
}