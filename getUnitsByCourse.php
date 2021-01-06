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
    
    if (isset($postdata)) {
         $request = json_decode($postdata);
		 $courseid = $request->courseid;
		 $userid = $request->userid;
            // Create connection
            $conn = new mysqli($servernameDB, $usernameDB, $passwordDB, $dbnameDB);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            } 
            $sql = "SELECT id,course_id, unit_description, location, date, COALESCE(saved,0) as saved,COALESCE(inarea,0) as inarea,COALESCE(action,'pending') as action FROM course_unit left join (select count(attendance.id) as saved,attendance.unitid from attendance where attendance.user_id = '$userid' and attendance.course_id = '$courseid' group by attendance.unitid) att on course_unit.id = att.unitid left join (SELECT action, inarea,unitid FROM attendance WHERE course_id = '$courseid' and user_id = '$userid' group by action, inarea,unitid) att2 on course_unit.id = att2.unitid WHERE course_id = '$courseid'";
            $result = $conn->query($sql);
            $r2= array();
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $r=array("course_id"=>utf8_decode($row["course_id"])
						,"unit_id"=>utf8_decode($row["id"])
						,"unit_description"=>utf8_decode($row["unit_description"])
						,"location"=>utf8_decode($row["location"])
						,"date"=>utf8_decode($row["date"])
						,"saved"=>utf8_decode($row["saved"])
						,"inarea"=>utf8_decode($row["inarea"])
						,"action"=>utf8_decode($row["action"])
					);
                    array_push($r2,$r);
                }
            }
           
            echo json_encode($r2, JSON_UNESCAPED_UNICODE);

            $conn->close();

     }else {
        echo "Not called properly with username parameter!";
     }
?>