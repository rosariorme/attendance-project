<?php
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
     if (isset($postdata)) {
         $request = json_decode($postdata);
         $name = $request->name;
         $username = $request->username;
         $password = md5($request->password);
         $role = $request->role;
         if ($username != "" && $request->password !="" && $request->name !="" ) {
             
            $servernameDB = "localhost";
            $usernameDB = "root";
            $passwordDB = "";
            $dbnameDB = "ziksa";

            // Create connection
            $conn = new mysqli($servernameDB, $usernameDB, $passwordDB, $dbnameDB);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            } 

            $sql = "INSERT INTO user (name, username, password,role_id) VALUES ('$name', '$username', '$password', '$role')";

            if ($conn->query($sql) === TRUE) {
                $r=array("done"=>1);
                echo json_encode($r);
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
         }else {
         echo "Empty username parameter!";
         }
     }else {
        echo "Not called properly with username parameter!";
     }
?>