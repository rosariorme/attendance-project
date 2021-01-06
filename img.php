<?php

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
		$json_64_encoded_string = $_GET["image"];
		$image1 = base64_decode($json_64_encoded_string);
		$image = addslashes($image1);
			$output_file = "uploads/"."IMG_".strtotime(date('Y-m-d H:i:s')).".jpeg";
			if(!is_dir("uploads/")){
			mkdir( "uploads/" , 0755, true);
			}
			$ifp = fopen($output_file, "wb");
			fwrite($ifp, $image); 
			fclose($ifp);
			$documenturl = $output_file;
			echo $documenturl;
?>