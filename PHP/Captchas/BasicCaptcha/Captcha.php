<?php
/* 
 * -------------------------------------------------------
 * Captcha
 * -------------------------------------------------------
 * @Version: 1.0.0
 * @Author:  FireDart
 * @Link:    http://www.firedartstudios.com/
 * @GitHub:  https://github.com/FireDart/snippets/Security
 * @License: The MIT License (MIT)
 * 
 * Generates a captcha to use on forms and other 
 * applications that need validation.
 * 
 * Offers:
 * + Random captcha text
 * + Custom fonts
 * + Custom backgrounds
 * + Rotated fonts
 * + Validation feature
 * 
 * -------------------------------------------------------
 * TO-DO
 * -------------------------------------------------------
 * + Custom background support
 * + Better selection of fonts
 * + Warped text
 * + Combination of word & math challenges (ex. 23 + 18 = ?)
 * + Multi word challenges (like ReCaptcha)
 * + Reload features
 * + Audio features (html5)
 * + General overall testing and improvements
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
 * $captcha = new Captcha();
 * 
 * // Generate image
 * echo $captcha->generate();
 * 
 * // Validate string entered by user
 * // Returns true/false
 * $captcha->validate($stringToValidate);
 * 
 */
class Captcha {
	/*
	 * @var $sessionName
	 *  Name of the session for captcha, default is "captcha"
	 */
	public $sessioName = "captcha";
	
	/*
	 * @var $fonts
	 *  List of fonts for captcha
	 *  Fonts from http://openfontlibrary.org/
	 */
	public $fonts = array(
			// Times New Yorker
			// http://www.dafont.com/times-new-yorker.font
			"times_new_yorker.ttf",
			// Karla Bold Stencil
			// http://openfontlibrary.org/en/font/karla-bold-stencil
			"Karla_BoldStencil.ttf",
			// Avería Sans
			//http://openfontlibrary.org/en/font/averia-sans
			"AveriaSans-Bold.ttf",
			// Anonymous Pro
			// http://openfontlibrary.org/en/font/anonymous-pro
			"Anonymous-Pro.ttf",
		);
	
	/*
	 * @var $dir
	 *  List of directories
	 */
	public $dir = array(
			"fonts" => "fonts/",
		);
	
	/* 
	 * __construct
	 * 
	 * Does some simple test to see if the captcha can be used
	 * 
	 * @param void
	 * @return void
	 */
	public function __construct() {
		try {
			// Check for GD library
			if(!function_exists('gd_info')) {
				throw new Exception('Required GD library is missing');
			}
			// Check if a session has even been started
			if(session_id() == '') {
				// throw new Exception('No session has been started, please add session_start().');
				// Instead of throwing exception above let use just call it instead
				session_start();
			}
		} catch(Exception $e) {
			// Use die in this case because we need the above working
			die($e->getMessage());
		}
	}
	
	/* 
	 * generate
	 * 
	 * Generates a captcha image
	 * 
	 * @param int $length The length of the captcha
	 * @param int $width  The width of the captcha image
	 * @param int $height The height of the captcha image
	 * @return img
	 */
	public function generate($length = 8, $width = 250, $height = 80) {
		try {
			// Make sure the $length parameter supplied is an integer
			if(!is_int($length)) {
				throw new Exception('The length you supplied is not a integer!');
			}
			// Check that a valid width & height is set
			if(!is_int($width) || !is_int($height)) {
				throw new Exception('Width and height must be an integer; minimum suggested for 8 characters is 250x80.');
			}
			// Check that supplied files exists
			if(!empty($this->fonts)) {
				// Foreach font in array check if file exists
				foreach($this->fonts as $font) {
					// If it does not, throw new exception
					if(!file_exists($this->dir['fonts'] . $font)) {
						throw new Exception('The supplied font "' . $font . '" can not be accessed.');
					}
				}
			}
			// Create a data bank of a-zA-Z0-9 characters
			$dataBank = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));
			// Create new captcha string
			$captcha = '';
			// Create a captcha string that is equal to the $length
			for($i = 0; $i < $length; $i++) {
				// Add a random character from the $dataBank using rand(0, (<total amount of characters> -1))
				$captcha .= $dataBank[rand(0, (count($dataBank) -1))];
			}
			// Set session
			$_SESSION[$this->sessioName] = $captcha;
			// Turn the text into an image
			$image = imagecreate($width, $height);
			imagecolorallocate($image, 255, 255, 255);
			$color  = imagecolorallocate($image, 0, 0, 0);
			// Build background
			// Add lines
			for($i = 0; $i < 20; $i++) {
				imageline($image, 0, rand()%$height, $width, rand()%$height, $color);
			}
			// Add dots/noise
			for($i = 0; $i < 1000; $i++) {
				imagesetpixel($image, rand()%$width, rand()%$height, $color);
			}
			// Build font
			$font = array();
			// Get font
			$font['file']  = $this->dir['fonts'] . $this->fonts[rand(0, (count($this->fonts) -1))];
			// Get font size
			$font['size']  = 30;
			// Get font angle
			$font['angle'] = rand(-5, 5);
			// Get captcha size
			$dimensions = imagettfbbox($font['size'], $font['angle'], $font['file'], $captcha);
			// Get font width & height
			$font['width']  = abs($dimensions[4] - $dimensions[0]);
			$font['height'] = abs($dimensions[5] - $dimensions[1]);
			// Get font coordinates
			$font['x'] = abs(($width - ($dimensions[4] - $dimensions[6])) / 2);
			$font['y'] = abs((($height - ($dimensions[3] - $dimensions[5])) / 2) + $font['size'] + 4);
			// Write captcha
			imagettftext($image, $font['size'], $font['angle'], $font['x'], $font['y'], $color, $font['file'], $captcha);
			// Make the header an image
			header('Content-type: image/png');
			// Load in image to the browser
			imagepng($image);
			// Free image data from memory to save resources
			imagedestroy($image);
		} catch(Exception $e) {
			return $e->getMessage();
		}
	}
	
	/* 
	 * validate
	 * 
	 * Validates the users input
	 * 
	 * @param mixed $captcha The captcha entered
	 * @return boolean
	 */
	public function validate($captcha) {
		if($captcha == $_SESSION['captcha']) {
			return true;
		} else {
			return false;
		}
	}
}