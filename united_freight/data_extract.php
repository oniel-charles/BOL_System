<?php
   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'root');
   define('DB_PASSWORD', '');
   define('DB_DATABASE', 'dennisprod');
   $mysqli = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);  

   set_time_limit(2000);
   $search = array("'", "\n\r","\n","\r");
   $replace = array("\'", ", ", "", ", ");

   //$con=mysqli_connect("localhost", "root","","mydatabase");

    $tableName  = 'voyage';
    $backupFile = './data_extract.sql';
    $query      = "LOAD DATA INFILE '$backupFile' INTO TABLE $tableName";
    $query      = "mysqldump -t -u root -p  dennisprod voyagee --where='1=1'";//datetime LIKE '2014-09%'";

    $result = mysqli_query($mysqli,$query);
    if (!$result) {echo mysqli_error($mysqli)."<br>".$query."<br>";}

   // voyage ->523 ==> bl_id ->38752
   //COMMODITY
 /*  $csv = array_map('str_getcsv', file('.\data_csv\commodity.csv'));
   $sql = " INSERT INTO `commodity` (`id`, `commodity_code`, `description`) VALUES ";
   for ($i=1;$i<sizeof($csv); $i++){  
        $values = "(".$csv[$i][1].",'".$csv[$i][3]."','".$csv[$i][4]."')";
        $result = mysqli_query($mysqli,$sql.$values);   
        if (!$result) {echo mysqli_error($mysqli)."<br>".$sql.$values."<br>";}
   }
   */



?>