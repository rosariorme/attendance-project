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
    if (isset($postdata) && !empty($postdata)) {
        $request = json_decode($postdata);
        $creationdate = date("Y-m-d h:i:s");
        $userid = $request->userid;
        $courseid = $request->courseid;        
        $noteid = @$request->noteid;
        $title = @$request->title;
        $text = @$request->text;	
         if ($noteid != "") {
            // Create connection
            $conn = new mysqli($servernameDB, $usernameDB, $passwordDB, $dbnameDB);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }             
			if(isset($noteid) && $noteid !=""){
				if ($noteid == 0){
					//Insert
					// $mysqli->insert_id;
					$sql = "INSERT INTO notes_users(userid, text, title, creationdate, courseid) VALUES ('$userid', '$text', '$title', '$creationdate', '$courseid')";
					$result = $conn->query($sql);
					$noteid = $conn->insert_id;
				}else{
					//Update
					$sql = "UPDATE notes_users SET text='$text',title='$title' WHERE noteid='$noteid'";
					$result = $conn->query($sql);
				}
			}
			
			//echo $sql;
            $sql = "SELECT noteid, userid, text, title, creationdate, courseid FROM notes_users WHERE noteid = '$noteid'";
            $result = $conn->query($sql);
            $r2= array();
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $r=array("userid"=>utf8_decode($row["userid"])
                    ,"courseid"=>utf8_decode($row["courseid"])
                    ,"title"=>utf8_decode($row["title"])
                    ,"text"=>utf8_decode($row["text"])
					,"noteid"=>$row["noteid"]
                    );
                    array_push($r2,$r);
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