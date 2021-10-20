<?php
//echo "My first PHP script!";
$myfile = fopen("tables.json", "r") or die("Unable to open file!");
$str= fread($myfile,filesize("tables.json"));
fclose($myfile);
$tablesjson = json_decode($str);
//echo(  $tablesjson->{"address"}->{"pkey"});

?>