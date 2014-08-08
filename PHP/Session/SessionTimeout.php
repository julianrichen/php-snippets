<?php
/* 
 * -------------------------------------------------------
 * SessionTimeout
 * -------------------------------------------------------
 * @Version: 1.0.0
 * @Author:  FireDart
 * @Link:    http://firedartstudios.com/
 * @GitHub:  https://github.com/FireDart/snippets/PHP/Session
 * @License: The MIT License (MIT)
 * 
 * Kills the session of a user after an amount of time.
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
 * sessionTimeout
 * 
 * Kills a session after a specific amount of time and 
 * redirects user.
 * 
 * @param int $minutes How many minutes till the session 
 * 					   timesout
 * @param str $redirect Where should the function redirect 
 * 						after timeout
 * @return mixed Will return errors if any, otherwise nothing
 */
function sessionTimeout($minutes, $redirect = null) {
	try {
		if(session_id() == '') {
			throw new Exception('No session has been started, please add session_start().');
		}
		if(!isset($minutes) || !is_int($minutes)) {
			throw new Exception('No timeout amount set or expects integer.');
		}
		if(!isset($redirect)) {
			$redirect = $_SERVER['REQUEST_URI'];
		}
		if(!isset($_SESSION['timeout'])) {
			$_SESSION['timeout'] = time();
		}
		$inactive = 60 * $minutes; 
		$currentSession = time() - $_SESSION['timeout'];
		if($currentSession > $inactive) {
			session_destroy();
			header("Location: " . $redirect);
		}
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}