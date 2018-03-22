<?php
chdir(dirname(__FILE__));
include "../lib/connection.php";
require "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
if(empty($_POST["gjp"]) OR empty($_POST["requestID"]) OR empty($_POST["accountID"])){
	exit("-1");
}
$accountID = $ep->remove($_POST["accountID"]);
$gjp = $ep->remove($_POST["gjp"]);
$requestID = $ep->remove($_POST["requestID"]);
$GJPCheck = new GJPCheck();
$gjpresult = $GJPCheck->check($gjp,$accountID);
if($gjpresult == 1){
	// ACCEPTING FOR USER 2
	$query = $db->prepare("SELECT accountID, toAccountID FROM friendreqs WHERE ID = :requestID");
	$query->execute([':requestID' => $requestID]);
	$request = $query->fetch();
	$reqAccountID = $request["accountID"];
	$toAccountID = $request["toAccountID"];
	if($toAccountID != $accountID){
		exit("-1");
	}
	$query = $db->prepare("INSERT INTO friendships (person1, person2, isNew1, isNew2)
	VALUES (:accountID, :targetAccountID, 1, 1)");

	$query->execute([':accountID' => $reqAccountID, ':targetAccountID' => $toAccountID]);
	//REMOVING THE REQUEST
	$query = $db->prepare("DELETE from friendreqs WHERE ID=:requestID LIMIT 1");
	$query->execute([':requestID' => $requestID]);
	//WHY ARE WE SHOUTING?
	$inside_data = array('user' => $reqAccountID, 'friend' => $toAcccountID);
        $inside_data_string = json_encode($inside_data);
        $data = array("event" => "friend_made", "data" => $inside_data_string);                                                                    
	$data_string = json_encode($data);                                                                                   
                                                                                                                     
	$ch = curl_init('http://legodev.glitch.me/api/dragongdps');                                                                      
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    		'Content-Type: application/json',                                                                                
    		'Content-Length: ' . strlen($data_string))                                                                       
	);                                                                                                                   
                                                                                                                     
	$result = curl_exec($ch);
	curl_close($ch);
	//RESPONSE SO IT DOESNT SAY "FAILED"
	echo "1";
}else{
	echo "-1";
}
?>
