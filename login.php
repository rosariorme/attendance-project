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
 
 
    //http://stackoverflow.com/questions/15485354/angular-http-post-to-php-and-undefined
    $postdata = file_get_contents("php://input");
    if (isset($postdata) && !empty($postdata)) {
        $request = json_decode($postdata);
		//var_dump($request);
        $username = $request->username;
		$password = md5($request->password);

         if ($username != "" && $request->password !="") {
            // Create connection
            $conn = new mysqli($servernameDB, $usernameDB, $passwordDB, $dbnameDB);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            } 

            $sql = "SELECT * FROM user WHERE username='$username' AND password='$password'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    //echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
                    $r=array("id"=>utf8_decode($row["id"])
                    //,"address"=>utf8_decode($row["address"])
                    ,"name"=>utf8_decode($row["name"])
                    //,"phone"=>utf8_decode($row["phone"])
                    //,"city"=>utf8_decode($row["city"])
                    //,"state"=>utf8_decode($row["state"])
                    //,"country"=>utf8_decode($row["country"])
                    //,"profile_img"=> utf8_decode($row["profile_img"])
                    //,"verified"=>utf8_decode($row["verified"])
                    //,"active"=>utf8_decode($row["active"])
                    ,"role_id"=>utf8_decode($row["role_id"])
                    );
					
                    echo json_encode($r, JSON_UNESCAPED_UNICODE);
                    //var_dump($row["profile_img"]);
                }
            } else {
                echo "0 results";
            }
            $conn->close();
            //Login
            //echo "Server returns: " . $username;
         }else {
         echo "Empty username parameter!";
         }
     }else {
        echo "Not called properly with username parameter!";
     }
?>