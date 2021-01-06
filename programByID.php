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
		 //var_dump($request);
         $role_id = @$request->role_id;
         $userid = @$request->userid;
         $courseid = @$request->courseid;
		 
         if ($role_id != "") {
            // Create connection
            $conn = new mysqli($servernameDB, $usernameDB, $passwordDB, $dbnameDB);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            } 
            $sql = "SELECT * FROM course WHERE id = '$courseid'";
            $result = $conn->query($sql);
            $r2= array();
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $r=array(
					"course_description"=>utf8_decode($row["course_description"])
                    ,"courseid"=>utf8_decode($row["id"])
                    ,"course_name"=>utf8_decode($row["course_name"])
                    ,"course_start_date"=>utf8_decode($row["course_start_date"])
                    ,"course_end_date"=>utf8_decode($row["course_end_date"])
                    ,"venue"=>utf8_decode($row["venue"])
                    ,"course_cost"=>utf8_decode($row["course_cost"])
                    ,"course_pass_mark"=> utf8_decode($row["course_pass_mark"])
                    ,"exams_enabled"=>utf8_decode($row["exams_enabled"])
                    ,"pretest_enabled"=>utf8_decode($row["pretest_enabled"])
                    ,"trainerid"=>utf8_decode($row["trainerid"])
                    ,"rating"=>utf8_decode($row["rating"])
                    ,"preview_image"=>utf8_decode($row["preview_image"])
                    ,"pdf_url"=>utf8_decode($row["pdf_url"])
                    ,"cvs_url"=>utf8_decode($row["cvs_url"])
                    );
                    array_push($r2,$r);
                }
            }
            
            $sql2 = "SELECT count(userid) as participants,state FROM users_programs WHERE programid = '$courseid' GROUP BY state";
            $result2 = $conn->query($sql2);
            $rarray2 = array();
            if ($result2->num_rows > 0) {
				$r=array("CONFIRMED"=>"0"
                    ,"PENDING"=>"0"
                    ,"DECLINED"=>"0"
                    );
                while($row = $result2->fetch_assoc()) {
					$r[$row["state"]] = $row["participants"];
                    array_push($rarray2,$r);
                }
            }
            
            $sqlr = "SELECT cast(sum(answerpointage)/count(id) as decimal(18,2)) as rating FROM feedback_answers WHERE courseid = '$courseid'";
            $resultr = $conn->query($sqlr);
            $rating = 0;
            if ($resultr->num_rows > 0) {
                while($row = $resultr->fetch_assoc()) {
					if($row["rating"] == null){
						$rating = 0;
					}else{
                    $rating = $row["rating"];
					}
                }
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
			$sql4 = "SELECT name, phone, city, state, country, corporate, Brief, Experience FROM user INNER JOIN trainer_details ON trainer_details.trainerid = user.id WHERE id = '$courseid'";
            $result4 = $conn->query($sql4);
            $r4= array();
            if ($result4->num_rows > 0) {
                while($row = $result4->fetch_assoc()) {
                    $r=array(
					"name"=>utf8_decode($row["name"]),
					"contact"=>utf8_decode($row["phone"]),
					"city"=>utf8_decode($row["city"]),
					"state"=>utf8_decode($row["state"]),
					"country"=>utf8_decode($row["country"]),
					"company"=>utf8_decode($row["corporate"]),
					"brief"=>utf8_decode($row["Brief"]),
					"experience"=>utf8_decode($row["Experience"])
					);
                    array_push($r4,$r);
                }
            }

            $resultcomplete = array();
            $resultcomplete["info"] = $r2;
            $resultcomplete["participants"] = $rarray2;
            $resultcomplete["rating"] = $rating;
            $resultcomplete["preexam"] = $r3;
            $resultcomplete["trainer"] = $r4;
			/*$r_student[0]["myattendance"] = $r_myatt[0]["attendance"];
            $resultcomplete["student"] = $r_student;*/
            echo json_encode($resultcomplete, JSON_UNESCAPED_UNICODE);

            $conn->close();
         }else {
         echo "Empty username parameter!";
         }
     }else {
        echo "Not called properly with username parameter!";
     }
?>