<?php
function logger($data)
{ 
	$today = date('ymd');
	$now = date('y/m/d H:i:s');

	$name = $_SESSION['name'];
	$filename = "logs/" . $name . ".txt";

	// open file
	$fd = fopen($filename, "a");

	// write string
	fwrite($fd, $now . "\t" . $name . "\t" . $_SERVER["REQUEST_URI"] . "\t" . $data . "\n");

	// close file
	fclose($fd);

}
?>