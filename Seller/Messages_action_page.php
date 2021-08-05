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
	if(isset($_POST["Messages"])){
		$myObj = new stdClass();
		
		// Select the admin announcement
		$sql = "SELECT * from messages join users on messages.user_id = users.user_id where is_admin_announcement = '1'
				order by datetime_created DESC
				LIMIT 1
			";
			
		$result2 = $conn->query($sql);

		if ($result2->num_rows == 1) {
			$myObj->announcement = new stdClass();
			while($row2 = $result2->fetch_assoc()) {
				if(file_exists($row2["image_location"])){
					$myObj->announcement->image_location = $row2["image_location"] . "?" . filemtime($row2["image_location"]);
				}
				else{
					$myObj->announcement->image_location = "https://via.placeholder.com/45";
				}
					$myObj->announcement->user_id = $row2["user_id"];
					$myObj->announcement->name = $row2["name"];
					$myObj->announcement->email = $row2["email"];
					$myObj->announcement->message = $row2["message"];
					$dateStr=date_create($row2["datetime_created"]);
					$myObj->announcement->datetime_created = date_format($dateStr,"M d, Y h:i:s a");
			}
		}
		
		// Select all distinct conversations with this company id
		$sql = "SELECT DISTINCT user_id from messages where company_id = '".$_POST["company_id"]."'
			";
			
		$result2 = $conn->query($sql);

		if ($result2->num_rows > 0) {
			
			$myObj->messages = array();
			$myObj->messages_count = $result2->num_rows;
			$i = 0;
			while($row2 = $result2->fetch_assoc()) {
				$myObj->messages[$i] = new stdClass();
				
				// Select last message in this conversation
				$sql = "SELECT * from messages join users on messages.user_id = users.user_id where messages.user_id = '".$row2["user_id"]."'
						and messages.company_id = '".$_POST["company_id"]."'
						order by datetime_created DESC
						LIMIT 1
					";
					
				$result3 = $conn->query($sql);

				if ($result3->num_rows == 1) {
					while($row3 = $result3->fetch_assoc()) {
						foreach ($row3 as $key => $value) {
							if(strcmp($key,"image_location") == 0){
								if(file_exists($value)){
									$myObj->messages[$i]->$key = $value . "?" . filemtime($value);
								}
								else{
									$myObj->messages[$i]->$key = "https://via.placeholder.com/45";
								}
							}
							else if(strcmp($key,"datetime_created") == 0){
								$dateStr=date_create($row3[$key]);
								$myObj->messages[$i]->$key = date_format($dateStr,"M d, Y h:i:s a");
							}
							else if(strcmp($key,"is_sent_by_buyer") == 0){
								if($value == "1"){
									$myObj->messages[$i]->$key = "right";
								}
								else{
									$myObj->messages[$i]->$key = "left";
								}
							}
							else{
								$myObj->messages[$i]->$key = $value;
							}
						}
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
			$myObj->messages_count = 0;
			
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
	}
	
	else if(isset($_POST["send"])){
		$myObj = new stdClass();
		
		// 1. Check if that user email exists and it is a buyer
		$sql = "SELECT * from users where email = '".$_POST["email"]."' and user_type = 'buyer'
			";
			
		$result2 = $conn->query($sql);

		if ($result2->num_rows == 1) {
			while($row2 = $result2->fetch_assoc()) {
				
				// Prevent sending a message if the user account is closed
				if(strcmp($row2["is_active"],"0") == 0){
					$myObj->message = "Unable to send message. That user account has been deactivated by the administrator.";
					$myObj->alert="danger";
					
					$myJSON = json_encode($myObj);
					echo $myJSON;
					$conn->close();
					exit();
				}
				
				// 2. Insert message
				$dt = date("Y-m-d H:i:s");
				$sql = "INSERT INTO messages (company_id,user_id,message,datetime_created,is_sent_by_buyer,is_admin_announcement)
							VALUES ('".$_POST["company_id"]."','".$row2["user_id"]."','".$_POST["message"]."','".$dt."','0','0')";
							
				$last_id = -1;
				if ($conn->query($sql) === TRUE) {
					$last_id = $conn->insert_id;
					$display_date = date_format(date_create($dt),"M d, Y h:i:s a");
					
					$myObj->messages = new stdClass();
					$myObj->messages->datetime_created = $display_date;
					$myObj->messages->email = $row2["email"];
					$myObj->messages->message = $_POST["message"];
					$myObj->messages->name = $row2["name"];
					$myObj->messages->user_id = $row2["user_id"];
					$myObj->messages->is_sent_by_buyer = "left"; // company sent the last message
					
					$image_location = "";
					if(file_exists($row2["image_location"])){
						$myObj->messages->image_location = $row2["image_location"] . "?" . filemtime($row2["image_location"]);
					}
					else{
						$myObj->messages->image_location = "https://via.placeholder.com/45";
					}
					
					$myObj->message = "Message sent!";
					$myObj->alert="success";
					
					$myJSON = json_encode($myObj);
					echo $myJSON;
					$conn->close();
					exit();
				}
				else{
					$myObj->message = "DB Insertion Error.";
					$myObj->alert="danger";
					
					$myJSON = json_encode($myObj);
					echo $myJSON;
					$conn->close();
					exit();
				}
			}
		}
		else{
			$myObj->message="A buyer with that email doesn't exist.";
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