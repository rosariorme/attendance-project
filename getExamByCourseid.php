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
		
         if ($courseid != "") {
            // Create connection
            $conn = new mysqli($servernameDB, $usernameDB, $passwordDB, $dbnameDB);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
			$sql3 = "SELECT id, exam_title, creation_datetime, course_id, start_date, end_date, enabled, question, answer1, answer2, answer3, answer4, answer5, correct_answer FROM exam WHERE course_id = '$courseid'";
            $result3 = $conn->query($sql3);
            $r3= array();
            if ($result3->num_rows > 0) {
                while($row = $result3->fetch_assoc()) {
                    $r=array(
					"examid"=>utf8_decode($row["id"]),
					"exam_title"=>utf8_decode($row["exam_title"]),
					"creation_datetime"=>utf8_decode($row["creation_datetime"]),
					"course_id"=>utf8_decode($row["course_id"]),
					"start_date"=>utf8_decode($row["start_date"]),
					"end_date"=>utf8_decode($row["end_date"]),
					"enabled"=>utf8_decode($row["enabled"]),
					"question"=>utf8_decode($row["question"]),
					"answer1"=>utf8_decode($row["answer1"]),
					"answer2"=>utf8_decode($row["answer2"]),
					"answer3"=>utf8_decode($row["answer3"]),
					"answer4"=>utf8_decode($row["answer4"]),
					"answer5"=>utf8_decode($row["answer5"]),
					"correct_answer"=>utf8_decode($row["correct_answer"])
					);
                    array_push($r3,$r);
                }
            }
           
            echo json_encode($r3, JSON_UNESCAPED_UNICODE);

            $conn->close();
         }else {
         echo "Empty username parameter!";
         }
     }else {
        echo "Not called properly with username parameter!";
     }
?>