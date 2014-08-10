<?php
/* 
 * -------------------------------------------------------
 * readTime
 * -------------------------------------------------------
 * @Version: 1.0.0
 * @Author:  FireDart
 * @Link:    http://www.firedartstudios.com/
 * @GitHub:  https://github.com/FireDart/snippets/DateTime
 * @License: The MIT License (MIT)
 * 
 * Calculates the average time it would take to read a 
 * body of text depending on words per minutes, ex.
 * 
 * "Less than a minute."
 * "3 hours 14 minutes."
 * "15 minutes."
 * 
 * -------------------------------------------------------
 * Requirements
 * -------------------------------------------------------
 * PHP 5.3.0+
 * 
 * -------------------------------------------------------
 * Suggestion on WPM (Words Per Minute)
 * -------------------------------------------------------
 * The default words per minute is 300, this is the average 
 * of an adult. Need to target a specific level?
 * 
 * 3rd Grade              = 150wpm
 * 8th Grade              = 250wpm
 * Average Adult          = 300wpm
 * College Student        = 450wpm
 * High Level Executive   = 575wpm
 * College Professor      = 675wpm
 * Speed Readers          = 1,500wpm
 * Speed Reading Champion = 4,700wpm (Don't even try)
 * 
 * -------------------------------------------------------
 * Usage
 * -------------------------------------------------------
 * Basic (using 300/m)
 * echo readTime("The entire text");
 * 
 * All options
 * echo readTime("The entire text", 450);
 * 
 */
/* 
 * readTime
 * 
 * Calculates the average time it would take to read a 
 * body of text depending on words per minutes.
 * 
 * @param  mixed $text  The text you want to scan
 * @param  int   $speed Words per minutes, 300 average adult
 * @return str
 */
function readTime($text = null, $speed = 300) {
	try {
		// Check if any text exists to scan
		if(empty($text)) {
			throw new Exception('No content to analyze.');
		}
		// Make sure speed is no 0
		if($speed == 0) {
			throw new Exception('Words per minute can not be 0.');
		}
		// Trip extra space
		$length = trim($text);
		// Remove html from text
		$length = strip_tags($length);
		// Explode each space to count words
		$length = explode(" ", $length);
		// Count amount of words
		$length = count($length);
		// Get amount of words divided by words per minute
		$time   = round($length / $speed);
		// Is the words per minute 0? Less then a minute read then
		if($time == 0) {
			return "Less than a minute.";
		} else {
			// Calculate hours and minutes for text
			$hours = floor($time / 60);
			$minutes = $time - $hours * 60;
			$readTime = '';
			// Check how many hours it would take to read
			if($hours > 0) {
				$readTime .= $hours . " hour";
				// Do we need to add an s?
				if($hours > 1) {
					$readTime .= "s";
				}
				// If minutes is not 0 add space
				if($minutes != 0) {
					$readTime .= " and ";
				}
			}
			if($minutes > 0) {
				$readTime .= $minutes . " minute";
				// Do we need to add an s?
				if($minutes > 1) {
					$readTime .= "s";
				}
			}
			// Return string, ex.
			// "3 hours 14 minutes." or "15 minutes."
			return $readTime . ".";
		}
	// Catch all errors and report back
	} catch(Exception $e) {
		return $e->getMessage();
	}
}