<?php
include('adodb.inc.php');
$dbtype = "mysqli";
//$conni = newADOConnection('mysqli');
@$connFarmasi = &ADONewConnection($dbtype);  # create a connection
/*$conni->debug=TRUE;*/

$dbhostnameFarmasi = "192.168.0.48";
$dbusernameFarmasi = "konekFarmasi";
$dbpasswordFarmasi = "12345678";
$dbnameFarmasi = "sosodoro";

$connFarmasi->PConnect($dbhostnameFarmasi,$dbusernameFarmasi,$dbpasswordFarmasi, $dbnameFarmasi);
?>