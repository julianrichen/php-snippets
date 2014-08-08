<?php
/* 
 * -------------------------------------------------------
 * Get GeoIP from freegeoip.net
 * -------------------------------------------------------
 * @Version: 1.0.0
 * @Author:  FireDart
 * @Link:    http://www.firedartstudios.com/
 * @GitHub:  https://github.com/FireDart/snippets/PHP/GeoIP/
 * @License: The MIT License (MIT)
 * 
 * Used to get geo information from an ip using freegeoip.net.
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
 * getGeoIP("aaa.bbb.ccc.ddd");
 * 
 */
function getGeoIP($ip = null) {
	try {
		if($ip == null) {
			$ip   = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
		}
		if($ip == "127.0.0.1" || $ip == "::1") {
			throw new Exception('You are on a local sever, this script won\'t work right.');
		}
		$url  = "http://freegeoip.net/json/" . $ip;
		$json = @file_get_contents($url);
		if($json == false) {
			throw new Exception('Unable to get data from freegeoip.net');  
		}
		$json = json_decode($json);
		if($json === null) {
			throw new Exception('Not a valid IP address or error in looking up ip');
		} else {
			return $json;
		}
	} catch (Exception $e) {
		return $e->getMessage();
	}
}