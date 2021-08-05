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
	
	if(isset($_POST["view_thread"])){
		$myObj = new stdClass();
	
		// Select Item Thread Record
		$sql = "SELECT * from item_threads where item_thread_id = '".$_POST["item_thread_id"]."'
			";
			
		$result2 = $conn->query($sql);

		if ($result2->num_rows == 1) {
			while($row2 = $result2->fetch_assoc()) {
				$myObj->item_thread = new stdClass();
				foreach ($row2 as $key => $value) {
					if(strcmp($key,"image_location") == 0){
						if(file_exists($row2[$key])){
							$myObj->item_thread->$key = $value . "?" . filemtime($value);
						}
						else{
							$myObj->item_thread->$key = "https://via.placeholder.com/200";
						}
					}
					else if(strcmp($key,"is_closed") == 0){
						if(strcmp($value,"1") == 0){
							$myObj->item_thread_is_closed = "1";
						}
						else{
							$myObj->item_thread_is_closed = "0";
						}
					}
					else{
						$myObj->item_thread->$key = $value;
					}
				}
			}
		}
		else{
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
		
		// Select All Comments of that Thread
		$sql = "SELECT * from comments join users on comments.user_id = users.user_id
				where item_thread_id = '".$_POST["item_thread_id"]."'
				order by datetime_created ASC
			";
			
		$result2 = $conn->query($sql);

		if ($result2->num_rows > 0) {
			$myObj->comments_count = $result2->num_rows;
			$myObj->comments = array();
			$i = 0;
			while($row2 = $result2->fetch_assoc()) {
				$myObj->comments[$i] = new stdClass();
				if(strcmp($row2["user_type"],"seller") != 0){
					foreach ($row2 as $key => $value) {
						if(strcmp($key,"image_location") == 0){
							if(file_exists($row2[$key])){
								$myObj->comments[$i]->$key = $value . "?" . filemtime($value);
							}
							else{
								$myObj->comments[$i]->$key = "https://via.placeholder.com/45";
							}
						}
						else if(strcmp($key,"datetime_created") == 0){
							$dateStr=date_create($row2[$key]);
							$myObj->comments[$i]->$key = date_format($dateStr,"M d, Y h:i:s a");
						}
						else{
							$myObj->comments[$i]->$key = $value;
						}
					}
				}
				else{
					// Select Company Profile if the comment came from the seller
					$sql = "SELECT companies.name as name, companies.email as email, companies.image_location as image_location, comments.message as message, comments.datetime_created as datetime_created
							from comments join users on comments.user_id = users.user_id join companies on users.user_id = companies.user_id
							where comment_id = '".$row2["comment_id"]."'
						";
						
					$result3 = $conn->query($sql);

					if ($result3->num_rows == 1) {
						while($row3 = $result3->fetch_assoc()) {
							foreach ($row3 as $key => $value) {
								if(strcmp($key,"image_location") == 0){
									if(file_exists($row3[$key])){
										$myObj->comments[$i]->$key = $value . "?" . filemtime($value);
									}
									else{
										$myObj->comments[$i]->$key = "https://via.placeholder.com/45";
									}
								}
								else if(strcmp($key,"datetime_created") == 0){
									$dateStr=date_create($row3[$key]);
									$myObj->comments[$i]->$key = date_format($dateStr,"M d, Y h:i:s a");
								}
								else{
									$myObj->comments[$i]->$key = $value;
								}
							}
						}
					}
					else{
						
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
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
	}
	
	else if(isset($_POST["btn_comment"])){
		$myObj = new stdClass();
		
		// Insert comment
		$dt = date("Y-m-d H:i:s");
		$sql = "INSERT INTO comments (item_thread_id,user_id,message,datetime_created)
					VALUES ('".$_POST["item_thread_id"]."','".$_POST["user_id"]."','".$_POST["message"]."','".$dt."')";
					
		$last_id = -1;
		if ($conn->query($sql) === TRUE) {
			$last_id = $conn->insert_id;
			$display_date = date_format(date_create($dt),"M d, Y h:i:s a");
			
			$myObj->comments = new stdClass();
			$myObj->comments->datetime_created = $display_date;
			$myObj->comments->message = $_POST["message"];
			$myObj->message = "Comment posted successfully!";
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