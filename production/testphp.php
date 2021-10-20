
<?php
var_dump("11"==11);
$array = array('fc' => array(), 'jmd' => array());
$array["fc"][] = array('alert' => 'alert', 'email' => 'Test');
$array["fc"][] = array('alert' => 'alert3', 'email' => 'Test3');
var_dump($array);
echo "<br><br>";

$alert_arrayx = array();
$alert_arrayx[] = $array["fc"];
var_dump($alert_arrayx[0][1]['alert']);
echo "<br><br>".$alert_arrayx[0][1]['alert'];
$date = new DateTime('2000-01-01', new DateTimeZone('Pacific/Nauru'));
echo $date->format('Y-m-d H:i:sP') . "<br>";

$date->setTimezone(new DateTimeZone('Pacific/Chatham'));
echo $date->format('Y-m-d H:i:sP') . "<br>";

echo date("M d, Y");
echo "<br>";
date_default_timezone_set('America/New_York');
$date= date('M d, Y H:i:s') ;
echo '<br>AMERICA DATE'.$date.' ';
date_default_timezone_set('America/Jamaica');
 $date= date('M d, Y H:i:s') ;
 echo '<br>JAMAICA DATE'.$date.'<br>';
 echo "dd<br>";
 $date= date('Ymd') ;
 echo $date;
 echo "<br>";
 $date = 20180513;
 $datez= new DateTime($date) ;
 echo $datez->format('M d, Y');
 echo "==<br>";
 $time =830;
 $date_str=sprintf('%08d', $date);
 
 
 //$numbers_only = preg_replace("/[^\d]/", "", $date);
 echo preg_replace("/^1?(\d{4})(\d{2})(\d{2})$/", "$1-$2-$3", $date_str);
 echo "<br>";
 echo preg_replace("/^1?(\d{2})(\d{2})$/", "$1:$2",$date_str=sprintf('%04d', $time));
 echo "<br>";
 $date = new DateTime('NOW');
echo $date->format('Y-m-d H:i:s');
 echo "<br>";
 echo $_SERVER['SERVER_NAME'];
 echo "<br>";
 echo $_SERVER['SERVER_NAME'];
 echo "<br>";
 $indicesServer = array('PHP_SELF',
 'argv',
 'argc',
 'GATEWAY_INTERFACE',
 'SERVER_ADDR',
 'SERVER_NAME',
 'SERVER_SOFTWARE',
 'SERVER_PROTOCOL',
 'REQUEST_METHOD',
 'REQUEST_TIME',
 'REQUEST_TIME_FLOAT',
 'QUERY_STRING',
 'DOCUMENT_ROOT',
 'HTTP_ACCEPT',
 'HTTP_ACCEPT_CHARSET',
 'HTTP_ACCEPT_ENCODING',
 'HTTP_ACCEPT_LANGUAGE',
 'HTTP_CONNECTION',
 'HTTP_HOST',
 'HTTP_REFERER',
 'HTTP_USER_AGENT',
 'HTTPS',
 'REMOTE_ADDR',
 'REMOTE_HOST',
 'REMOTE_PORT',
 'REMOTE_USER',
 'REDIRECT_REMOTE_USER',
 'SCRIPT_FILENAME',
 'SERVER_ADMIN',
 'SERVER_PORT',
 'SERVER_SIGNATURE',
 'PATH_TRANSLATED',
 'SCRIPT_NAME',
 'REQUEST_URI',
 'PHP_AUTH_DIGEST',
 'PHP_AUTH_USER',
 'PHP_AUTH_PW',
 'AUTH_TYPE',
 'PATH_INFO',
 'ORIG_PATH_INFO') ;
 
 echo '<table cellpadding="10">' ;
 foreach ($indicesServer as $arg) {
     if (isset($_SERVER[$arg])) {
         echo '<tr><td>'.$arg.'</td><td>' . $_SERVER[$arg] . '</td></tr>' ;
     }
     else {
         echo '<tr><td>'.$arg.'</td><td>-</td></tr>' ;
     }
 }
 echo '</table>' ; 
 echo "<br>";
 echo(strstr(strtoupper('ggFreight Collect'),'FREIGHT'));
 echo "<br>";
 echo(!strstr(strtoupper('ggFreight Collect'),'FREIGHT'));
 echo "<br>";
 echo(!strstr(strtoupper('ggFredight Collect'),'FREIGHT'));
 echo "<br>";
 echo('mand');
 echo "<br>";
echo  $date->format('Y-m-d H:i:s').'<br>';
 echo Date("Y-m-d", strtotime( $date->format('Ymd')." -2 Month "));
 echo "<br>xx";
 echo Date("Y-m-d", strtotime( $date->format('Ymd')." -2 Year "));
?>
