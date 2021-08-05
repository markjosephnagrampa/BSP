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
	if(isset($_POST["CompaniesForValidation"])){
		$myObj = new stdClass();
		$myObj->companies = array();
		// Select all pending companies
		$sql = "SELECT companies.company_id, companies.image_location, companies.name, companies.address, companies.contact_no,
						companies.email, users.name as users_name, users.contact_no as users_contact_no
						from companies join users on companies.user_id = users.user_id
						where companies.approval_status = 'pending'
			";
			
		$result2 = $conn->query($sql);

		if ($result2->num_rows > 0) {
			$myObj->companies_count = $result2->num_rows;
			$i = 0;
			while($row2 = $result2->fetch_assoc()) {
				$myObj->companies[$i] = new stdClass();
				
				if(file_exists($row2["image_location"])){
					$myObj->companies[$i]->image_location = $row2["image_location"] . "?" . filemtime($row2["image_location"]);
				}
				else{
					$myObj->companies[$i]->image_location = "https://via.placeholder.com/32";
				}
				foreach ($row2 as $key => $value) {
					if(strcmp($key,"image_location") != 0){
						$myObj->companies[$i]->$key = $value;
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
			$myObj->companies_count = 0;
			
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
		
		
	}
	
	else if(isset($_POST["approve_company"])){
		$myObj = new stdClass();
		
		// Edit the company with that id
		$sql = "UPDATE companies SET approval_status = 'approved' WHERE company_id = ".$_POST["company_id"];
		if ($conn->query($sql) === TRUE) {
			$myObj->message="Company account approved successfully!";
			$myObj->alert="success";
			$myObj->id = $_POST["company_id"];
			
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
		else{
			$myObj->message="Unable to modify company record. Please try again.";
			$myObj->alert="danger";
			
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
	}
	
	else if(isset($_POST["deny_company"])){
		$myObj = new stdClass();
		
		// Edit the item_thread with that id
		$sql = "UPDATE companies SET approval_status = 'denied', date_denied = '".date("Y-m-d")."' WHERE company_id = ".$_POST["company_id"];
		if ($conn->query($sql) === TRUE) {
			$myObj->message="Company account denied successfully!";
			$myObj->alert="success";
			$myObj->id = $_POST["company_id"];
			
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
		else{
			$myObj->message="Unable to modify company record. Please try again.";
			$myObj->alert="danger";
			
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