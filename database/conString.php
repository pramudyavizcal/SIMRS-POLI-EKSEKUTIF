<?php
	$servername = "192.168.0.99";
	$username = "SidikJari";
	$password = "djatikoesoemo";
	$dbname = "sosodoro";

	$conni = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conni->connect_error) {
        die("Connection failed: " . $conni->connect_error);
    }
?>
