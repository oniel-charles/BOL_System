<?php
require_once(__DIR__ . '/clicksend-php/lib/Configuration.php');
require_once(__DIR__ . '/clicksend-php/lib/api/AccountApi.php');



// Configure HTTP basic authorization: BasicAuth
$config = ClickSend\Configuration::getDefaultConfiguration()
              ->setUsername('USERNAME')
              ->setPassword('API_KEY');

$apiInstance = new ClickSend\Api\AccountApi(new Client(),$config);
$account = new \ClickSend\Model\Account(); // \ClickSend\Model\Account | Account model
$account->setUserName("johndoe");
$account->setPassword("pass");
$account->setUserPhone("533-481-1041");
$account->setUserEmail("johndoesdfds1@awesome.com");
$account->setUserFirstName("John");
$account->setUserLastName("Doe");
$account->setAccountName("The Awesome Company");
$account->setCountry("US");

try {
    $result = $apiInstance->accountPost($account);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AccountApi->accountPost: ', $e->getMessage(), PHP_EOL;
}
?>
