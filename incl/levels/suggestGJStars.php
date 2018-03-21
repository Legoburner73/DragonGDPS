<?php
//error_reporting(0);
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
require_once "../lib/mainLib.php";
$gs = new mainLib();
$gjp = $ep->remove($_POST["gjp"]);
$stars = $ep->remove($_POST["stars"]);
$feature = $ep->remove($_POST["feature"]);
$levelID = $ep->remove($_POST["levelID"]);
$accountID = $ep->remove($_POST["accountID"]);
if($accountID != "" AND $gjp != ""){
	$GJPCheck = new GJPCheck();
	$gjpresult = $GJPCheck->check($gjp,$accountID);
	if($gjpresult == 1){
		$permState = $gs->checkPermission($accountID, "actionRateStars");
		if($permState){
			$difficulty = $gs->getDiffFromStars($stars);
			$gs->rateLevel($accountID, $levelID, $stars, $difficulty["diff"], $difficulty["auto"], $difficulty["demon"]);
			$gs->featureLevel($accountID, $levelID, $feature);
			$gs->verifyCoinsLevel($accountID, $levelID, 1);
			//open a connection to my server
			$inside_data = array("levelID" => $levelD, "stars" => $stars, "difficulty" => $difficulty["diff"], "auto" => $difficulty["auto"], "demon" => $difficulty["demon"]);
			$inside_data_string = json_encode($inside_data);
			$data = array("event" => "suggested_stars", "data" => $inside_data_string);                                                                    
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
			echo 1;
		}else{
			echo -1;
		}
	}else{echo -1;}
}else{echo -1;}
?>
