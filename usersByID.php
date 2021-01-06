<?php
require "config.php";
 
 if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }
 
    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
 
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
 
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
 
        exit(0);
    }
    
    $postdata = file_get_contents("php://input");
    //$postdata ="aa";
    if (isset($postdata) && !empty($postdata)) {
         $request = json_decode($postdata);
         $userid = $request->userid;
         //$userid = 1;
         if ($userid != "") {
            // Create connection
            $conn = new mysqli($servernameDB, $usernameDB, $passwordDB, $dbnameDB);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            } 
            $sql = "SELECT * FROM user WHERE id = '$userid'";
            $result = $conn->query($sql);
            $r2= array();
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $r=array("userid"=>utf8_decode($row["id"])
                    ,"name"=>utf8_decode($row["name"])
                    ,"username"=>utf8_decode($row["username"])
					,"email"=>utf8_decode($row["email"])
                    ,"active"=>utf8_decode($row["active"])
                    ,"address"=>utf8_decode($row["address"])
                    ,"phone"=>utf8_decode($row["phone"])
                    ,"city"=> utf8_decode($row["city"])
                    ,"state"=>utf8_decode($row["state"])
                    ,"country"=>utf8_decode($row["country"])
                    ,"profile_img"=>utf8_decode($row["profile_img"])
                    ,"rating"=>utf8_decode($row["rating"])
                    ,"status"=>utf8_decode($row["status"])
                    ,"role_id"=>utf8_decode($row["role_id"])
                    ,"corporate"=>utf8_decode($row["corporate"])
                    );
                    array_push($r2,$r);
                }
            }
			
            $sql = "SELECT detailid, userid, pincode, reference_name, reference_relationship, reference_phone, bank_userfullname, bank_name, bank_accountnumber, bank_ifsccode FROM user_details WHERE userid = '$userid'";
            $result = $conn->query($sql);
            $document = 0;
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $r=array("detailid"=>utf8_decode($row["detailid"])
                    ,"userid"=>utf8_decode($row["userid"])
                    ,"pincode"=>utf8_decode($row["pincode"])
					,"reference_name"=>utf8_decode($row["reference_name"])
                    ,"reference_relationship"=>utf8_decode($row["reference_relationship"])
                    ,"reference_phone"=>utf8_decode($row["reference_phone"])
                    ,"bank_userfullname"=> utf8_decode($row["bank_userfullname"])
                    ,"bank_name"=>utf8_decode($row["bank_name"])
                    ,"bank_accountnumber"=>utf8_decode($row["bank_accountnumber"])
                    ,"bank_ifsccode"=>utf8_decode($row["bank_ifsccode"])
                    );
                    array_push($r2,$r);
                }
            }
			$sql_doc = "SELECT documentid, documenttype, documentimage FROM documents WHERE userid = '$userid'";
			$result_doc = $conn->query($sql_doc);
            //$document = 0;
            if ($result_doc->num_rows > 0) {
                while($row_doc = $result_doc->fetch_assoc()) {
                    $r_doc=array("documentid"=>utf8_decode($row_doc["documentid"])
                    ,"documenttype"=>utf8_decode($row_doc["documenttype"])
                    ,"documentimage"=>base64_encode($row_doc["documentimage"])
					);
                    array_push($r2,$r_doc);
					//$document = $row["documentid"];
                }
            }
			
            echo json_encode($r2, JSON_UNESCAPED_UNICODE);
            $conn->close();
         }else {
         echo "Empty username parameter!";
         }
     }else {
        echo "Not called properly with username parameter!";
     }
?>