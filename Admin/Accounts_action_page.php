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
	if(isset($_POST["Accounts"])){
		$myObj = new stdClass();
		$myObj->accounts = new stdClass();
		$myObj->accounts->companies = array();
		$myObj->accounts->users = array();
		
		// Select all approved companies
		$sql = "SELECT companies.company_id, companies.user_id, companies.email, companies.name, companies.image_location, users.is_active as users_is_active
				from companies join users on companies.user_id = users.user_id where companies.approval_status = 'approved'
			";
			
		$result2 = $conn->query($sql);

		if ($result2->num_rows > 0) {
			$myObj->accounts->companies_count = $result2->num_rows;
			$i = 0;
			while($row2 = $result2->fetch_assoc()) {
				$myObj->accounts->companies[$i] = new stdClass();
				
				if(file_exists($row2["image_location"])){
					$myObj->accounts->companies[$i]->image_location = $row2["image_location"] . "?" . filemtime($row2["image_location"]);
				}
				else{
					$myObj->accounts->companies[$i]->image_location = "https://via.placeholder.com/32";
				}
				foreach ($row2 as $key => $value) {
					if(strcmp($key,"image_location") == 0){
					}
					else if(strcmp($key,"users_is_active") == 0){
						if(strcmp($value,"0") == 0 ){
							$myObj->accounts->companies[$i]->$key = "Inactive";
						}
						else{
							$myObj->accounts->companies[$i]->$key = "Active";
						}
					}
					else{
						$myObj->accounts->companies[$i]->$key = $value;
					}
				}
				
				// Get no. of reports filed against this company
				$sql = "SELECT complaint_id from complaints where is_sent_by_buyer = '1' and company_id = '".$row2["company_id"]."'
					";
					
				$result3 = $conn->query($sql);
				$myObj->accounts->companies[$i]->complaints_count = "(". $result3->num_rows . ")";
				
				$i++;
			}
			
			// Sort by complaints count
			usort($myObj->accounts->companies, function($a, $b) { //Sort the array using a user defined function
				if($a->users_is_active > $b->users_is_active){
					return 1;
				}
				else if($a->users_is_active == $b->users_is_active){
					return $a->complaints_count > $b->complaints_count ? -1 : 1;
				}
				else{
					return -1;
				}
			});
		}
		
		else{
			$myObj->accounts->companies_count = 0;
		}
		
		// Select all buyers
		$sql = "SELECT users.user_id, users.name, users.email, users.image_location, users.is_active from users where users.user_type = 'buyer'
			";
			
		$result2 = $conn->query($sql);

		if ($result2->num_rows > 0) {
			$myObj->accounts->users_count = $result2->num_rows;
			$i = 0;
			while($row2 = $result2->fetch_assoc()) {
				$myObj->accounts->users[$i] = new stdClass();
				
				if(file_exists($row2["image_location"])){
					$myObj->accounts->users[$i]->image_location = $row2["image_location"] . "?" . filemtime($row2["image_location"]);
				}
				else{
					$myObj->accounts->users[$i]->image_location = "https://via.placeholder.com/32";
				}
				foreach ($row2 as $key => $value) {
					if(strcmp($key,"image_location") == 0){
					}
					else if(strcmp($key,"is_active") == 0){
						if(strcmp($value,"0") == 0 ){
							$myObj->accounts->users[$i]->$key = "Inactive";
						}
						else{
							$myObj->accounts->users[$i]->$key = "Active";
						}
					}
					else{
						$myObj->accounts->users[$i]->$key = $value;
					}
				}
				
				// Get no. of reports filed against this buyer
				$sql = "SELECT complaint_id from complaints where is_sent_by_buyer = '0' and user_id = '".$row2["user_id"]."'
					";
					
				$result3 = $conn->query($sql);
				$myObj->accounts->users[$i]->complaints_count = "(". $result3->num_rows . ")";
				
				$i++;
			}
			
			// Sort by complaints count
			usort($myObj->accounts->users, function($a, $b) { //Sort the array using a user defined function
				if($a->is_active > $b->is_active){
					return 1;
				}
				else if($a->is_active == $b->is_active){
					return $a->complaints_count > $b->complaints_count ? -1 : 1;
				}
				else{
					return -1;
				}
			});
		}
		
		else{
			$myObj->accounts->users_count = 0;
		}
		
		
		
		$myJSON = json_encode($myObj);
		echo $myJSON;
		$conn->close();
		exit();
	}
	
	else if(isset($_POST["activate_user"])){
		$myObj = new stdClass();
		
		// Edit the company with that id
		$sql = "UPDATE users SET is_active = '1' WHERE user_id = ".$_POST["user_id"];
		if ($conn->query($sql) === TRUE) {
			$myObj->message="Buyer account edited successfully!";
			$myObj->alert="success";
			$myObj->id=$_POST["user_id"];
			
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
		else{
			$myObj->message="Unable to modify buyer record. Please try again.";
			$myObj->alert="danger";
			
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
	}
	
	else if(isset($_POST["deactivate_user"])){
		$myObj = new stdClass();
		
		// Edit the company with that id
		$sql = "UPDATE users SET is_active = '0' WHERE user_id = ".$_POST["user_id"];
		if ($conn->query($sql) === TRUE) {
			$myObj->message="Buyer account edited successfully!";
			$myObj->alert="success";
			$myObj->id=$_POST["user_id"];
			
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
		else{
			$myObj->message="Unable to modify buyer record. Please try again.";
			$myObj->alert="danger";
			
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
	}
	
	else if(isset($_POST["activate_company"])){
		$myObj = new stdClass();
		
		// Edit the company with that id
		$sql = "UPDATE users SET is_active = '1' WHERE user_id = ".$_POST["user_id"];
		if ($conn->query($sql) === TRUE) {
			$myObj->message="Seller account edited successfully!";
			$myObj->alert="success";
			$myObj->id=$_POST["user_id"];
			
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
		else{
			$myObj->message="Unable to modify seller record. Please try again.";
			$myObj->alert="danger";
			
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
	}
	
	else if(isset($_POST["deactivate_company"])){
		$myObj = new stdClass();
		
		// Edit the company with that id
		$sql = "UPDATE users SET is_active = '0' WHERE user_id = ".$_POST["user_id"];
		if ($conn->query($sql) === TRUE) {
			$myObj->message="Seller account edited successfully!";
			$myObj->alert="success";
			$myObj->id=$_POST["user_id"];
			
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
		else{
			$myObj->message="Unable to modify seller record. Please try again.";
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