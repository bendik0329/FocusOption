<?php
// Start using sessions
session_start();

// Define Jpeg image headers
header("Content-type: image/jpeg");

// Set values such as background image and text color
$image = imagecreatefromjpeg("./background.jpg") or exit("Script coludn't find gd's background");
$color = imagecolorallocate($image, 0, 0, 0); # Black color

// Create random security code and set as session
// session_register("codes");
$_SESSION["codes"] = substr(strtoupper(md5(rand(1, 999999))), 0, 6);

// Write the security code in the image
imagestring($image, 4, 15, 2.5, $_SESSION["codes"], $color);
imagestring($image, 4, 14, 2.5, $_SESSION["codes"], $color); # Bold

// Create the security code and destroy the picture
imagejpeg($image);
imagedestroy($image);
?>