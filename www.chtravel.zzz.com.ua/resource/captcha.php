<?php
	session_start();
	$Random = rand(10001, 99999);
	$_SESSION['captcha'] = md5('2E0' . md5($Random . '0V7') . md5('201A4'));
	$im = imagecreatetruecolor(90, 36);
	imagefilledrectangle($im, 0, 0, 90, 36, imagecolorallocate($im, 255, 255, 255));
	imagettftext($im, 40, 0, 15, 23, imagecolorallocate($im, 0, 0, 0), 'captcha-font.ttf', $Random);
	header('Expires: Wed, 1 Jan 1997 00:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');
	header('Content-type: image/gif');
	imagegif($im);
	imagedestroy($im);
?>