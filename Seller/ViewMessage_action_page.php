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
	
	if(isset($_POST["view_message"])){
		$myObj = new stdClass();
	
		// Select Item Thread Record
		$sql = "SELECT * from messages join users on messages.user_id = users.user_id where messages.user_id = '".$_POST["message_user_id"]."' and company_id = '".$_POST["company_id"]."'
				order by datetime_created ASC
			";
			
		$result2 = $conn->query($sql);

		if ($result2->num_rows > 0) {
			$myObj->messages = array();
			$myObj->messages_count = $result2->num_rows;
			$i = 0;
			while($row2 = $result2->fetch_assoc()) {
				
				$myObj->messages[$i] = new stdClass();
				foreach ($row2 as $key => $value) {
					if(strcmp($key,"image_location") == 0){
						if(file_exists($value)){
							$myObj->messages[$i]->$key = $value . "?" . filemtime($value);
						}
						else{
							$myObj->messages[$i]->$key = "https://via.placeholder.com/45";
						}
					}
					else if(strcmp($key,"datetime_created") == 0){
						$dateStr=date_create($row2[$key]);
						$myObj->messages[$i]->$key = date_format($dateStr,"M d, Y h:i:s a");
					}
					else if(strcmp($key,"is_active") == 0){
						if(strcmp($value,"1") == 0){
							$myObj->message_user_is_active = "1";
						}
						else{
							$myObj->message_user_is_active = "0";
						}
					}
					else{
						$myObj->messages[$i]->$key = $value;
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
	
	else if(isset($_POST["reply"])){
		$myObj = new stdClass();
		
		// Insert message
		$dt = date("Y-m-d H:i:s");
		$sql = "INSERT INTO messages (company_id,user_id,message,datetime_created,is_sent_by_buyer,is_admin_announcement)
					VALUES ('".$_POST["company_id"]."','".$_POST["message_user_id"]."','".$_POST["message"]."','".$dt."','0','0')";
					
		$last_id = -1;
		if ($conn->query($sql) === TRUE) {
			$last_id = $conn->insert_id;
			$display_date = date_format(date_create($dt),"M d, Y h:i:s a");
			
			$myObj->messages = new stdClass();
			$myObj->messages->datetime_created = $display_date;
			$myObj->messages->message = $_POST["message"];
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
	
	$conn->close();
	
	
	function test_input($data) {
	  $data = trim($data);
	  $data = stripslashes($data);
	  $data = htmlspecialchars($data);
	  return $data;
	}
?>