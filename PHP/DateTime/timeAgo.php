<?php
/* 
 * -------------------------------------------------------
 * timeAgo
 * -------------------------------------------------------
 * @Version: 1.0.0
 * @Author:  FireDart
 * @Link:    http://firedartstudios.com/
 * @GitHub:  https://github.com/FireDart/snippets/PHP/DateTime
 * @License: The MIT License (MIT)
 * 
 * Take a Date, Time or DateTime and turns it into a human 
 * friendly time ago format, ex.
 * 
 * "Just now." (time < 30 seconds)
 * "1 minute ago."
 * "23 hours ago."
 * "3 months ago."
 * 
 * timeAgo can also handel future dates but is not recommended
 * "In 30 seconds."
 * "In 5 months."
 * "In 2 years."
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
 * echo timeAgo('2014-07-27 20:00:00');
 * 
 * All options
 * echo timeAgo('2014-07-27 20:00:00', "America/New_York", false);
 * 
 */
/* 
 * timeAgo
 * 
 * Take a Date, Time or DateTime and turns it into a human 
 * friendly time ago format, ex.
 * 
 * @param mixed   $date     The date of "time ago"
 * @param str     $timezone The timezone you are the user is in
 * @param boolean $friendly Do we allow "Just now" & "In a moment"
 * @return str
 */
function timeAgo($date, $timezone = null, $friendly = true) {
	// Use try/catch loop for DateTime
	try {
		// If you need to set your default_timezone, list of zones:
		// http://php.net/manual/en/timezones.php
		if($timezone != null) {
			// Check if timezone is valid
			if(!in_array($timezone, timezone_identifiers_list())) {
				throw new Exception('Please input a valid timezone.');
			}
			date_default_timezone_set($timezone);
		}
		// Get the current moment in time
		$now      = new DateTime();
		// Get the time we will be working with & validate
		$ago      = new DateTime($date);
		// How much time is in-between them?
		$interval = $now->diff($ago);
		// Get intervals of each units
		$year     = $interval->format('%y');
		$month    = $interval->format('%m');
		$day      = $interval->format('%d');
		$hour     = $interval->format('%h');
		$minute   = $interval->format('%i');
		$second   = $interval->format('%s');
		// Check if it is a date in the future
		if($interval->invert == 0) {
			// If it is we might as well handel it
			if($interval->format('%y') == "00000") {
				$time = $year;
				$unit = "year";
			} elseif($interval->format('%m%y') == "0000"){
				$time = $month;
				$unit = "month";
			} elseif($interval->format('%d%m%y') == "000"){
				$time = $day;
				$unit = "day";
			} elseif($interval->format('%h%d%m%y') == "00"){
				$time = $hour;
				$unit = "hour";
			} elseif($interval->format('%i%h%d%m%y') == "0"){
				$time = $minute;
				$unit = "minute";
			} else{
				$time = $second;
				$unit = "second";
			}
		}
		// Check what unit we should use
		if($interval->format('%i%h%d%m%y') == "00000") {
			$time = $second;
			$unit = "second";
		} elseif($interval->format('%h%d%m%y') == "0000"){
			$time = $minute;
			$unit = "minute";
		} elseif($interval->format('%d%m%y') == "000"){
			$time = $hour;
			$unit = "hour";
		} elseif($interval->format('%m%y') == "00"){
			$time = $day;
			$unit = "day";
		} elseif($interval->format('%y') == "0"){
			$time = $month;
			$unit = "month";
		} else{
			$time = $year;
			$unit = "year";
		}
		// Make it more personable
		if($unit == "second" && $time < 30 && $interval->invert != 0 && $friendly === true) {
			return "Just now.";
		}
		if($unit == "second" && $time < 30 && $interval->invert == 0 && $friendly === true) {
			return "In a moment.";
		}
		// Add s if number is greater then 1
		if($time > 1) {
			$unit .= "s";
		}
		// Return sentence
		if($interval->invert == 0) {
			return "In {$time} {$unit}.";
		} else {
			return "{$time} {$unit} ago.";
		}
	// Catch all errors and report back
	} catch(Exception $e) {
		echo $e->getMessage();
	}
}
