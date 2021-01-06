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
         $userid = @$request->userid;
         if ($userid != "") {
            // Create connection
            $conn = new mysqli($servernameDB, $usernameDB, $passwordDB, $dbnameDB);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            } 
            $sql = "SELECT concat(course.course_description,' - ',course_unit.unit_description) as title, course_unit.time_start, course_unit.time_end, course_unit.address_details, course_unit.details FROM users_programs inner join course on course.id = users_programs.programid inner join course_unit on course_unit.course_id = course.id where users_programs.userid = '$userid'";
            $result = $conn->query($sql);
            $r2= array();
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $r=array("title"=>utf8_decode($row["title"])
                    ,"time_start"=>utf8_decode($row["time_start"])
                    ,"time_end"=>utf8_decode($row["time_end"])
                    ,"address_details"=>utf8_decode($row["address_details"])
                    ,"details"=>utf8_decode($row["details"])
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