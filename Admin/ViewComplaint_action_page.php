<?php
	session_start();
	date_default_timezone_set('Etc/GMT-8'); // Set time zone to Philippine time
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "bsp_db";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
	  die("Connection failed: " . $conn->connect_error);
	}
	
	foreach ($_POST as $a => $b) {
		$a = test_input($b);
	}
	if(isset($_POST["ViewComplaint"])){
		$myObj = new stdClass();
		$myObj->complaints = array();
		
		// Get name of the accussed
		if(strcmp($_POST["is_company"],"true") == 0){
			$sql = "SELECT companies.name from companies where companies.user_id = '".$_POST["accounts_id"]."' LIMIT 1";
			$result = $conn->query($sql);
			
			if($result->num_rows == 1){
				while($row = $result->fetch_assoc()) {
					$myObj->accused = $row["name"];
				}
			}
		}
		else if(strcmp($_POST["is_company"],"false") == 0){
			$sql = "SELECT users.name from users where users.user_id = '".$_POST["accounts_id"]."'";
			$result = $conn->query($sql);
			
			if($result->num_rows == 1){
				while($row = $result->fetch_assoc()) {
					$myObj->accused = $row["name"];
				}
			}
		}
		
		
		$sql = "";
		// Determine whether the accused is a company or not
		if(strcmp($_POST["is_company"],"true") == 0){
			$sql = "SELECT companies.company_id from companies where companies.user_id = '".$_POST["accounts_id"]."' LIMIT 1";
			$result = $conn->query($sql);
			
			if($result->num_rows == 1){
				while($row = $result->fetch_assoc()) {
					$sql = "SELECT complaints.complaint_id, complaints.date_created, complaints.complaint_type, complaints.message, complaints.image_location, users.name
							from complaints join users on complaints.user_id = users.user_id
							where complaints.is_sent_by_buyer = '1' and complaints.company_id = '".$row["company_id"]."'
							order by complaints.date_created ASC
					";
				}
			}
			else{
				$sql = "";
			}
		}
		else if(strcmp($_POST["is_company"],"false") == 0){
			$sql = "SELECT complaints.complaint_id, complaints.date_created, complaints.complaint_type, complaints.message, complaints.image_location, companies.name
					from complaints join companies on complaints.company_id = companies.company_id
					where complaints.is_sent_by_buyer = '0' and complaints.user_id = '".$_POST["accounts_id"]."'
					order by complaints.date_created ASC
			";
		}
		
		$result2 = $conn->query($sql);

		if ($result2->num_rows > 0) {
			$myObj->complaints_count = $result2->num_rows;
			$i = 0;
			while($row2 = $result2->fetch_assoc()) {
				$myObj->complaints[$i] = new stdClass();
				
				if(file_exists($row2["image_location"])){
					$myObj->complaints[$i]->image_location = $row2["image_location"] . "?" . filemtime($row2["image_location"]);
				}
				else{
					$myObj->complaints[$i]->image_location = "https://via.placeholder.com/32";
				}
				$dateStr=date_create($row2["date_created"]);
				$myObj->complaints[$i]->date_created = date_format($dateStr,"M d, Y");
				
				foreach ($row2 as $key => $value) {
					if(strcmp($key,"image_location") != 0 && strcmp($key,"date_created") != 0){
						$myObj->complaints[$i]->$key = $value;
					}
				}
				$i++;
			}
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
		
		else{
			$myObj->complaints_count = 0;
			
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
	}
	
	$conn->close();
	
	
	function test_input($data) {
	  $data = trim($data);
	  $data = stripslashes($data);
	  $data = htmlspecialchars($data);
	  return $data;
	}
?>