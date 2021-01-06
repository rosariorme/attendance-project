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
        $modified_date = date("Y-m-d h:i:s");
        $userid = $request->userid;
        
        $active = @$request->active;
        $address = @$request->address;
        $name = @$request->name;
        $username = @$request->username;
        $password = md5(@$request->password);
        $phone = @$request->phone;
        $city = @$request->city;
        $state = @$request->state;
        $country = @$request->country;
        $profile_img = @$request->profile_img;
        $status = @$request->status;
        $rating = @$request->rating;
        $hour_cost = @$request->hour_cost;
        $years_experience = @$request->years_experience;
        $cv = @$request->cv;
        $role_id = @$request->role_id;
        $corporate = @$request->corporate;
        $group_id = @$request->group_id;
        $active = @$request->active;
        $verified = @$request->verified;
        $invite_employees = @$request->invite_employees;
		
        $email = @$request->email;
		
		//Profile
		$detailid = @$request->detailid;
        $pincode = @$request->pincode;
		$reference_name = @$request->reference_name;
		$reference_relationship = @$request->reference_relationship;
		$reference_phone = @$request->reference_phone;
		$bank_userfullname = @$request->bank_userfullname;
		$bank_name = @$request->bank_name;
		$bank_accountnumber = @$request->bank_accountnumber;
		$bank_ifsccode = @$request->bank_ifsccode;
		
         if ($userid != "") {
            // Create connection
            $conn = new mysqli($servernameDB, $usernameDB, $passwordDB, $dbnameDB);
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            } 
            //update data
            $sql = "UPDATE user SET modified_date='$modified_date'";
            if (isset($name) && $name !="") { $sql .= ",name='$name'"; }
            if (isset($username) && $username !="") { $sql .= ",username='$username'"; }
            if (isset($request->password) && $request->password !="") { $sql .= ",password='$password'"; }
            if (isset($address) && $address !="") { $sql .= ",address='$address'"; }
            if (isset($phone) && $phone !="") { $sql .= ",phone='$phone'"; }
            if (isset($city) && $city !="") { $sql .= ",city='$city'"; }
            if (isset($state) && $state !="") { $sql .= ",state='$state'"; }
            if (isset($email) && $email !="") { $sql .= ",email='$email'"; }
            if (isset($country) && $country !="") { $sql .= ",country='$country'"; }
            if (isset($profile_img) && $profile_img !="") { $sql .= ",profile_img='$profile_img'"; }
            if (isset($status) && $status !="") { $sql .= ",status='$status'"; }
            if (isset($rating) && $rating !="") { $sql .= ",rating='$rating'"; }
            if (isset($hour_cost) && $hour_cost !="") { $sql .= ",hour_cost='$hour_cost'"; }
            if (isset($years_experience) && $years_experience !="") { $sql .= ",years_experience='$years_experience'"; }
            if (isset($cv) && $cv !="") { $sql .= ",cv='$cv'"; }
            if (isset($role_id) && $role_id !="") { $sql .= ",role_id='$role_id'"; }
            if (isset($corporate) && $corporate !="") { $sql .= ",corporate='$corporate'"; }
            if (isset($group_id) && $group_id !="") { $sql .= ",group_id='$group_id'"; }
            if (isset($active) && $active !="") { $sql .= ",active='$active'"; }
            if (isset($verified) && $verified !="") { $sql .= ",verified='$verified'"; }
            if (isset($invite_employees) && $invite_employees !="") { $sql .= ",invite_employees='$invite_employees'"; }
            $sql .= " WHERE id = '$userid'";
            $result = $conn->query($sql);
            
			if(isset($detailid) && $detailid !=""){
				if ($detailid == 0){
					//Insert
					// $mysqli->insert_id;
					$sql = "INSERT INTO user_details(userid, pincode, reference_name, reference_relationship, reference_phone, bank_userfullname, bank_name, bank_accountnumber, bank_ifsccode, modified_date) VALUES ('$userid', '$pincode', '$reference_name', '$reference_relationship', '$reference_phone', '$bank_userfullname', '$bank_name', '$bank_accountnumber', '$bank_ifsccode', '$modified_date')";
					$result = $conn->query($sql);
					$detailid = $conn->insert_id;
				}else{
					//Update
					$sql = "UPDATE user_details SET userid='$userid',pincode='$pincode',reference_name='$reference_name',reference_relationship='$reference_relationship',reference_phone='$reference_phone',bank_userfullname='$bank_userfullname',bank_name='$bank_name',bank_accountnumber='$bank_accountnumber',bank_ifsccode='$bank_ifsccode',modified_date='$modified_date' WHERE detailid = '$detailid'";
					$result = $conn->query($sql);
				}
			}
			
			//echo $sql;
            $sql = "SELECT * FROM user WHERE id = '$userid'";
            $result = $conn->query($sql);
            $r2= array();
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $r=array("userid"=>utf8_decode($row["id"])
                    ,"name"=>utf8_decode($row["name"])
                    ,"username"=>utf8_decode($row["username"])
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
                    ,"email"=>utf8_decode($row["email"])
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