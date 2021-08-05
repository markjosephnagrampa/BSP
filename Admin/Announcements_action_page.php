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
	
	if(isset($_POST["post_btn"])){
		$myObj = new stdClass();
		
		// Check if an admin announcement for this user already exists
		$sql = "SELECT * from messages where is_admin_announcement = '1' and user_id = '".$_POST["user_id"]."'
			";
			
		$result2 = $conn->query($sql);

		// If it does, replace its contents with the new announcement
		if ($result2->num_rows == 1) {
			while($row2 = $result2->fetch_assoc()) {
				$sql = "UPDATE messages SET message = '".$_POST["comment"]."', datetime_created = '".date("Y-m-d H:i:s")."' WHERE message_id = ".$row2["message_id"];
				if ($conn->query($sql) === TRUE) {
					$myObj->message="Announcement posted successfully!";
					$myObj->alert="success";
					
					$myJSON = json_encode($myObj);
					echo $myJSON;
					$conn->close();
					exit();
				}
				else{
					$myObj->message="Unable to post announcement. Please try again.";
					$myObj->alert="danger";
					
					$myJSON = json_encode($myObj);
					echo $myJSON;
					$conn->close();
					exit();
				}
			}
		}
		
		// If it doesn't, insert a new announcement message
		else{
			// Insert message
			$dt = date("Y-m-d H:i:s");
			$sql = "INSERT INTO messages (user_id,message,datetime_created,is_sent_by_buyer,is_admin_announcement)
						VALUES ('".$_POST["user_id"]."','".$_POST["comment"]."','".$dt."','0','1')";
						
			$last_id = -1;
			if ($conn->query($sql) === TRUE) {
				$last_id = $conn->insert_id;
				
				$myObj->message="Announcement posted successfully!";
				$myObj->alert="success";
				
				$myJSON = json_encode($myObj);
				echo $myJSON;
				$conn->close();
				exit();
			}
			else{
				$myObj->message = "Unable to post announcement. Please try again.";
				$myObj->alert="danger";
				
				$myJSON = json_encode($myObj);
				echo $myJSON;
				$conn->close();
				exit();
			}
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