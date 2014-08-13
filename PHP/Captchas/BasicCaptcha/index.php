<?php
// THIS PAGE IS AN EXAMPLE OF THE CAPTCHA IMPLEMENTATION
// YOU MIGHT HAVE TO PLAY AROUND WITH IT FOR A BIT TO MEET YOUR NEEDS
session_start();
require("Captcha.php");
$captcha = new Captcha();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Captcha example</title>
</head>
<body>
<?php
if(isset($_POST['captcha'])) {
	if($captcha->validate($_POST['captcha'])) {
		echo '<div style="color: green;">Right captcha entered!</div>';
	} else {
		echo '<div style="color: red;">Wrong captcha entered, please try again.</div>';
	}
}
?>
<br />
<img alt="Captcha" src="captcha-image.php" />
<form action="" method="post" name="captcha">
	<label for="captcha">For security please enter the above captcha</label>
	<br />
	<input name="captcha" id="captcha" type="text" />
	<input name="verify" id="verify" type="submit" value="Verify" />
</form>
</body>
</html>