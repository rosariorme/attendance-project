<?php
require "config.php";

 //http://stackoverflow.com/questions/18382740/cors-not-working-php
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
    if (isset($postdata) && !empty($postdata)) {
        $request = json_decode($postdata);
		$documentid = $request->documentid;
		$documenttype = $request->documenttype;
		$userid = $request->userid;
		$json_64_encoded_string = $request->image;
		$image1 = base64_decode($json_64_encoded_string);
		$image = addslashes($image1);

		
         if ($request->image != "" && $userid !="" && $documentid !="" && $documenttype !="") {
			 //upload file
			$documentimage = $image;
			
            $conn = new mysqli($servernameDB, $usernameDB, $passwordDB, $dbnameDB);
            if ($conn->connect_error) {	die("Connection failed: " . $conn->connect_error);	} 
			$modified_date = date("Y-m-d h:i:s");
			if(isset($documentid) && $documentid !="" && isset($documenttype) && $documenttype !="" && isset($documentimage) && $documentimage !="" ){
				if ($documentid == 0){
					//Insert
					$sql ="INSERT INTO documents(documenttype, documentimage, modified_date,userid) VALUES ('$documenttype','$documentimage','$modified_date','$userid')";
					$result = $conn->query($sql);
					$documentid = $conn->insert_id;
				}else{
					//Update
					$sql ="UPDATE documents SET modified_date = '$modified_date'";
					if (isset($documenttype) && $documenttype !="") { $sql .= ",documenttype='$documenttype'"; }
					if (isset($documentimage) && $documentimage !="") { $sql .= ",documentimage='$documentimage'"; }
					$sql.="WHERE userid = '$userid' AND documentid='$documentid'";
					$result = $conn->query($sql);
					
				}
			}
            $sql = "SELECT * FROM documents WHERE documentid='$documentid' AND userid='$userid'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $r=array("documentid"=>utf8_decode($row["documentid"])
                    ,"documenttype"=>utf8_decode($row["documenttype"])
                    ,"documentimage"=>base64_encode($row["documentimage"])
                    );
                    echo json_encode($r, JSON_UNESCAPED_UNICODE);
                }
            } else {
                echo "0 results";
            }
            $conn->close();
         }else {
         echo "Empty username parameter!";
         }
     }else {
        echo "Not called properly with username parameter!";
     }
?>