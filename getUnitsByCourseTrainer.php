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
            // Create connection
            $conn = new mysqli($servernameDB, $usernameDB, $passwordDB, $dbnameDB);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            } 
            $sql = "SELECT course_unit.course_id,course_unit.id as unitid ,COALESCE(att.confirm,0) as confirmed,COALESCE(att2.confirm,0) as attended FROM course_unit left join (select count(attendance.user_id) as confirm,attendance.course_id,attendance.unitid from attendance where attendance.action = 'confirm' group by attendance.unitid,attendance.course_id) att on course_unit.id = att.unitid and att.course_id = course_unit.course_id left join (select count(attendance.user_id) as confirm,attendance.course_id,attendance.unitid from attendance where attendance.inarea = 1 and attendance.action = 'confirm' group by attendance.unitid,attendance.course_id) att2 on course_unit.id = att2.unitid and att2.course_id = course_unit.course_id WHERE course_unit.course_id = '$courseid'";
            $result = $conn->query($sql);
            $r2= array();
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $r=array("course_id"=>utf8_decode($row["course_id"])
						,"unitid"=>utf8_decode($row["unitid"])
						,"confirmed"=>utf8_decode($row["confirmed"])
						,"attended"=>utf8_decode($row["attended"])
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