<?php

 $requestMethod=$_SERVER['REQUEST_METHOD'];
 $post_data = json_decode(file_get_contents('php://input'),true);
 echo $requestMethod.' Post Return OHC '.$post_data['name'];
?>
