<?php
$filedata=$_POST["file"];
$filename=$_POST["filename"];
$file = fopen($filename,"w");
echo $filedata;
fwrite($file,$filedata);
fclose($file);
?> 