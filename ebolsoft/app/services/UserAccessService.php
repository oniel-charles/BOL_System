<?php
   
   //require_once 'JWT_Helper.php';
   
   define("SECRET_SERVER_KEY", "22yDML2b=!fdfdTsDHjLWp$,SW");
   


   if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&       strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{
    //Request identified as ajax request
   if($_SERVER["REQUEST_METHOD"] == "POST") {
   
   }
}


   function login($mysqli) {
    $user_name =  $mysqli->real_escape_string($_POST['user_name']);
    $password =  base64_decode($mysqli->real_escape_string($_POST['password']));
         
    // Using prepared statements means that SQL injection is not possible. 
    if ($stmt = $mysqli->prepare("SELECT id, user_name, password,full_name 
        FROM user_profile
       WHERE user_name = ?
        LIMIT 1")) {
        $stmt->bind_param('s', $user_name);  // Bind "$user_name" to parameter.
        $stmt->execute();    // Execute the prepared query.
        $stmt->store_result();
 
        // get variables from result.
        $stmt->bind_result($user_id, $user_name, $db_password,$full_name);
        $stmt->fetch();

        if ($stmt->num_rows == 1) {
            // If the user exists we check if the account is locked
            // from too many login attempts 

           // if (checkbrute($user_id, $mysqli) == true) {
               if(false){
                // Account is locked 
                // Send an email to user saying their account is locked
                return false;
            } else {
                
                // Check if the password in the database matches
                // the password the user submitted. We are using
                // the password_verify function to avoid timing attacks.
                if (password_verify($password, $db_password)) {
                    // Password is correct!
                    // Get the user-agent string of the user.
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    // XSS protection as we might print this value
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                   
                    $user_name = preg_replace("/[^a-zA-Z0-9_\-]+/", 
                                                                "", 
                                                                $user_name);                   
                    // Login successful.
                    $access_token=createToken($user_name,$full_name,$user_id);
                    $token_array = array('token'=>$access_token);
                    exit(json_encode($token_array));
                } else {
                    // Password is not correct
                    // We record this attempt in the database
                    $now = time();
                    return false;
                }
            }
        } else {
            echo ('<br><br>out here'. $mysqli->error);
            // No user exists.
            return false;
        }
    }else {
            echo ('<br><br>out here'. $mysqli->error);
            // No user exists.
            return false;
        }
}

function updateUser($mysqli){
    $error_msg = "";
    if (isset($_POST['user_name'], $_POST['password'])) {
        // Sanitize and validate the data passed in
        $user_name = filter_input(INPUT_POST, 'user_name', FILTER_SANITIZE_STRING);
        $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
        $password  = base64_decode(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));
        

            $password = password_hash($password, PASSWORD_BCRYPT);     
            // update user into the database 
            if ($update_stmt = $mysqli->prepare("UPDATE user_profile set user_name=?, password=?,full_name=?,status=? where id=?")) {
                $update_stmt->bind_param('ssssd', $user_name, $password,$full_name,$status,$id);           
  
                // Execute the prepared query.
                if (! $update_stmt->execute()) {
                    echo('Location: ../error.php?err=Registration failure: INSERT'. $mysqli->error);
                }else{ return true;}
            }else{echo($update_stmt);}
            //echo('Location: ./register_success.php');
            return false;
        
    }
    }

    function changePasswprd($mysqli){
        $claims = authenticateToken();
        if (!$claims){
            return null;
        }
        $id=$claims['id'];
        $user=$claims['user'];
        $name=$claims['full_name'];
        $error_msg = "";

        if (isset($_POST['current_password'], $_POST['new_password'], $_POST['confirm_password'])) {
            if ($stmt = $mysqli->prepare("SELECT id, user_name, password,full_name FROM user_profile WHERE id = ? LIMIT 1")) {
                $stmt->bind_param('d', $id);  
                $stmt->execute();  
                $stmt->store_result();
                $stmt->bind_result($user_id, $user_name, $db_password,$full_name);
                $stmt->fetch();
                if ($stmt->num_rows == 1) {
                        $password =  base64_decode($mysqli->real_escape_string($_POST['current_password']));
                        if (!password_verify($password, $db_password)) {
                            return null;
                        }
                    
                } else {
                    return null;
                }
            }
                $new_password  = base64_decode(filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_STRING));           
                $new_password = password_hash($new_password, PASSWORD_BCRYPT);     
                // update user into the database 
                if ($update_stmt = $mysqli->prepare("UPDATE user_profile set  password=? where id=?")) {
                    $update_stmt->bind_param('sd', $new_password,$id);           
      
                    // Execute the prepared query.
                    if (! $update_stmt->execute()) {
                        echo('Location: ../error.php?err=Registration failure: INSERT'. $mysqli->error);
                    }else{ 
                        
                    $token=  createToken($user,$name,$id);
                    $token_array = array('token'=>$token);
                    exit(json_encode($token_array));
                    }
                }else{echo($update_stmt);}
                return null;
            
        }
        return null;
        }

function createUser($mysqli){
$error_msg = "";
 
if (isset($_POST['user_name'], $_POST['password'])) {
    
    // Sanitize and validate the data passed in
    $user_name = filter_input(INPUT_POST, 'user_name', FILTER_SANITIZE_STRING);
    $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
    $password  = base64_decode(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
   
    if (strlen($password) != 128) {
        // The hashed pwd should be 128 characters long.
        // If it's not, something really odd has happened
      //  $error_msg .= '<p class="error">Invalid password configuration.</p>';
    }
 
 
    // check existing user_name
    $prep_stmt = "SELECT id FROM user_profile WHERE user_name = ? LIMIT 1";
    $stmt = $mysqli->prepare($prep_stmt);
 
    if ($stmt) {
        $stmt->bind_param('s', $user_name);
        $stmt->execute();
        $stmt->store_result();
 
                if ($stmt->num_rows == 1) {
                        // A user with this user_name already exists
                        $error_msg .= '<p class="error">A user with this user_name already exists</p>';
                        $stmt->close();
                }
        } else {
                echo ('<br><br>out here'. $mysqli->error);
                $error_msg .= '<p class="error">Database error line 55</p>';
                $stmt->close();
        }

    // TODO: 
    // We'll also have to account for the situation where the user doesn't have
    // rights to do registration, by checking what type of user is attempting to
    // perform the operation.
    
    if (empty($error_msg)) {
 
        // Create hashed password using the password_hash function.
        // This function salts it with a random salt and can be verified with
        // the password_verify function.
        $password = password_hash($password, PASSWORD_BCRYPT);        
        // Insert the new user into the database 
        if ($insert_stmt = $mysqli->prepare("INSERT INTO user_profile (user_name, password,full_name,status) VALUES (?,?,?,?)")) {
            $insert_stmt->bind_param('ssss', $user_name, $password,$full_name,$status);           
            // Execute the prepared query.
            if (! $insert_stmt->execute()) {
                echo($insert_stmt.'Location: ../error.php?err=Registration failure: INSERT'. $mysqli->error);
            }else{ 
                echo mysqli_insert_id($mysqli);
                return true;}
        }else{echo($insert_stmt);}
        //echo('Location: ./register_success.php');
        return false;
    }
}
}

function createToken($user,$name,$user_id){    
 /*   $payload = array();
    $payload['user'] = $user; // putting hard coded right now but it can be dynamically from DB.
    $payload['nbf'] =time();
    $payload['iat'] =time();
    $payload['exp'] =time()+10;
    $payload['iss']=$_SERVER['REMOTE_ADDR'];
    $encodedToken =  JWT::encode($payload, SECRET_SERVER_KEY);*/

    $algorithm = 'HS256';
    $time = time();
    $leeway = 5; // seconds
    $ttl = 24*60*60; // seconds
    $claims = array('iss'=>$_SERVER['REMOTE_ADDR'],'user'=>$user,'full_name'=>$name,'id'=>$user_id);
    $encodedToken = generateToken($claims,$time,$ttl,$algorithm,SECRET_SERVER_KEY);

    return $encodedToken;
}
function authenticateToken(){
    $token=getBearerToken();
    $algorithm = 'HS256';
    $time = time();
    $leeway = 5; // seconds
    $ttl = 60*60*24; // seconds
    $claims = getVerifiedClaims($token,$time,$leeway,$ttl,$algorithm,SECRET_SERVER_KEY);
    if (!$claims){
        return null;
    }
    try {
            if ($claims['iss']==$_SERVER['REMOTE_ADDR']) {
                return $claims;
            } 
       
    } catch (\Exception $e) { // Also tried JwtException
        echo('$e.message 44');
        return null;
    }

    return null;
}

/** 
 * Get hearder Authorization
 * */
function getAuthorizationHeader(){
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    }
    else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        //print_r($requestHeaders);
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}
/**
* get access token from header
* */
function getBearerToken() {
$headers = getAuthorizationHeader();
// HEADER: Get the access token from the header
if (!empty($headers)) {
    if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
        return $matches[1];
    }
}
return null;
}


function getVerifiedClaims($token,$time,$leeway,$ttl,$algorithm,$secret) {
    $algorithms = array('HS256'=>'sha256','HS384'=>'sha384','HS512'=>'sha512');
    if (!isset($algorithms[$algorithm])) return false;
    $hmac = $algorithms[$algorithm];
    $token = explode('.',$token);
    if (count($token)<3) return false;
    $header = json_decode(base64_decode(strtr($token[0],'-_','+/')),true);
    if (!$secret) return false;
    if ($header['typ']!='JWT') return false;
    if ($header['alg']!=$algorithm) return false;
    $signature = bin2hex(base64_decode(strtr($token[2],'-_','+/')));
    if ($signature!=hash_hmac($hmac,"$token[0].$token[1]",$secret)) return false;
    $claims = json_decode(base64_decode(strtr($token[1],'-_','+/')),true);
    if (!$claims) return false;
    if (isset($claims['nbf']) && $time+$leeway<$claims['nbf']) return false;
    if (isset($claims['iat']) && $time+$leeway<$claims['iat']) return false;
    if (isset($claims['exp']) && $time-$leeway>$claims['exp']) return false;
    if (isset($claims['iat']) && !isset($claims['exp'])) {
       if ($time-$leeway>$claims['iat']+$ttl) return false;
    }
    return $claims;
}

function generateToken($claims,$time,$ttl,$algorithm,$secret) {
    $algorithms = array('HS256'=>'sha256','HS384'=>'sha384','HS512'=>'sha512');
    $header = array();
    $header['typ']='JWT';
    $header['alg']=$algorithm;
    $token = array();
    $token[0] = rtrim(strtr(base64_encode(json_encode((object)$header)),'+/','-_'),'=');
    $claims['iat'] = $time;
    $claims['exp'] = $time + $ttl;
    $token[1] = rtrim(strtr(base64_encode(json_encode((object)$claims)),'+/','-_'),'=');
    if (!isset($algorithms[$algorithm])) return false;
    $hmac = $algorithms[$algorithm];
    $signature = hash_hmac($hmac,"$token[0].$token[1]",$secret,true);
    $token[2] = rtrim(strtr(base64_encode($signature),'+/','-_'),'=');
    return implode('.',$token);
}

?>
