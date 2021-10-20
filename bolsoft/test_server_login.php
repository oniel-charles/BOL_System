

<?php

$user =array("user_name" => "kingstonuser","password" => base64_encode("qualityone"));
$user =array("user_name" => "mobayuser","password" => base64_encode("qualityone"));
$token ="";
$result =CallAPI('POST','http://ebolsoft.qualityoneintlshipping.com/app/login',$user,$token);
var_dump($result);
$json = json_decode($result, true);
//var_dump($json);
echo $json["token"];
$data =CallAPI('GET','http://ebolsoft.qualityoneintlshipping.com/app/export/master/45',$user,$json["token"]);
var_dump($data);
//$data =CallAPI('GET','http://ebolsoft.qualityoneintlshipping.com/app/booking',$user,$json["token"]);
$booking=json_decode($data, true);
for ($x = 0; $x < sizeof($booking) ; $x++) {
     $vessel =CallAPI('GET','http://ebolsoft.qualityoneintlshipping.com/app/vessel/'.$booking[$x]["vessel_id"],$user,$json["token"]);
     $booking[$x]["vessel_id"]=$vessel;
    
}    

var_dump($booking);
function CallAPI($method, $url, $data = false,$token)
{
    $curl = curl_init();

    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    curl_setopt($curl, CURLOPT_USERAGENT, "PHP-ADMIN");
    // Optional Authentication:
    //curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    //curl_setopt($curl, CURLOPT_USERPWD, "username:password");
    //Set your auth headers
    if($token){
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
        ));
    }

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}

?>

