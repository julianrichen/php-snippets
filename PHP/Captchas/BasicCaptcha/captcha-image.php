<?php
session_start();
require("Captcha.php");
$captcha = new Captcha();
echo $captcha->generate();