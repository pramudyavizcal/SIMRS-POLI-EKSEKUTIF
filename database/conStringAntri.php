<?php
	$servernameAntri = "192.168.0.169";
	$usernameAntri = "RSUDssdr";
	$passwordAntri = "12345678";
	$dbnameAntri = "sosodoro";


	$conniAntri = new mysqli($servernameAntri, $usernameAntri, $passwordAntri, $dbnameAntri);
    // Check connection
    if ($conniAntri->connect_error) {
        die("Connection failed: " . $conniAntri->connect_error);
    }
?>
