<?php

include './APIClient2.php';

// api endpoint
$apiendpoint = 'https://api.transmitsms.com/send-sms.json';
// api key
$apikey = 'c62a42c5df919032f43d3a0b8929f2df';
// api secret
$apisecret = 'leon@chamier';
// send sms post body
$post = [
    'from' => 'DENNIS SHIP',
    'to'      => '18764368426',
    'message' => 'Your shipment is ready. Please use this link http://192.168.100.65/dev/appointment/?ref=578768&code=OCXBWR to make an appointment. Username: 578768 Password: OCXBWR'
];

$params = http_build_query($post);

$ch = curl_init($apiendpoint);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
curl_setopt( $ch, CURLOPT_POST, 1 );
curl_setopt( $ch, CURLOPT_POSTFIELDS, $params );
curl_setopt( $ch, CURLOPT_HTTPHEADER,
    [
        'content-type: application/x-www-form-urlencoded',
        'Authorization: Basic ' . base64_encode("$apikey:$apisecret")
    ]
);

$res = curl_exec( $ch );
curl_close( $ch );

echo $res;
?>