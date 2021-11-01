<?php
	// Authorisation details.
	$username = "leonchamier@hotmail.com";
	$hash = "9d8e5c804b0ef52fb38dd1f1fbb23f746f02de0d79d65b86aab6022bd6c7ae84";

	// Config variables. Consult http://api.txtlocal.com/docs for more info.
	$test = "0";

	// Data for text message. This is the text message data.
	$sender = "DENNIS SHIP"; // This is who the message appears to be from.
	$numbers = "18765711212"; // A single number or a comma-seperated list of numbers
	$message = "Your shipment is ready. Please use this link http://192.168.100.65/dev/appointment/?ref=578768&code=OCXBWR to make an appointment.";
	// 612 chars or less
	// A single number or a comma-seperated list of numbers
	$message = urlencode($message);
	$data = "username=".$username."&hash=".$hash."&message=".$message."&sender=".$sender."&numbers=".$numbers."&test=".$test;
	$ch = curl_init('http://api.txtlocal.com/send/?');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch); // This is the result from the API
    curl_close($ch);
    var_dump($result);
?>