<?php
/* 
 * -------------------------------------------------------
 * sessionInactivity
 * -------------------------------------------------------
 * @Version: 1.0.0
 * @Author:  FireDart
 * @Link:    http://firedartstudios.com/
 * @GitHub:  https://github.com/FireDart/snippets/PHP/Session
 * @License: The MIT License (MIT)
 * 
 * Kills the session of a user after a continues amount of 
 * time being inactive.
 * 
 * -------------------------------------------------------
 * Requirements
 * -------------------------------------------------------
 * PHP 5.4.0+
 * 
 * -------------------------------------------------------
 * Usage
 * -------------------------------------------------------
 * session_start();
 * sessionTimeout(10, "logout.php");
 *                 ^       ^
 *    10 Minutes - |       | - Redirect location
 */
/* 
 * sessionInactivity
 * 
 * Kills the session of a user after a continues amount of 
 * time being inactive.
 * 
 * @param int $minutes How many minutes till the session 
 * 					   timesout
 * @param str $redirect Where should the function redirect 
 * 						after timeout
 * @return mixed Will return errors if any, otherwise nothing
 */
function sessionInactivity($minutes, $redirect = null) {
	try {
		// Check if a session has even been started
		if(session_id() == '') {
			throw new Exception('No session has been started, please add session_start().');
		}
		// Check if timeout is set & is an integer
		if(!isset($minutes) || !is_int($minutes)) {
			throw new Exception('No timeout amount set or expects integer.');
		}
		// If no redirect is set use the current page
		if(!isset($redirect)) {
			$redirect = $_SERVER['REQUEST_URI'];
		}
		// If timeout session is not set, create it
		$_SESSION['timeout'] = time();
		// Calculate how many seconds
		$inactive = 60 * $minutes;
		// Calculate current inactive session
		$currentSession = time() - $_SESSION['timeout'];
		// If current session is longer then timeout destroy session & redirect
		if($currentSession > $inactive) {
			session_destroy();
			header("Location: " . $redirect);
		}
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}