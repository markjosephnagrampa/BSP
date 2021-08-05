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
	if(isset($_POST["Threads"])){
		$myObj = new stdClass();
		
		// Select all non-deleted item_threads with this company's id
		$sql = "SELECT * from item_threads where company_id = '".$_POST["company_id"]."' and is_deleted = '0'
				order by 
				is_closed ASC,
				date_created DESC
			";
			
		$result2 = $conn->query($sql);

		if ($result2->num_rows > 0) {
			$myObj->item_threads = array();
			$myObj->item_threads_count = $result2->num_rows;
			$i = 0;
			while($row2 = $result2->fetch_assoc()) {
				$myObj->item_threads[$i] = new stdClass();
				foreach ($row2 as $key => $value) {
					if(strcmp($key,"image_location") == 0){
						if(file_exists($value)){
							$myObj->item_threads[$i]->$key = $value . "?" . filemtime($value);
						}
						else{
							$myObj->item_threads[$i]->$key = "https://via.placeholder.com/32";
						}
					}
					else if(strcmp($key,"date_created") == 0){
						$dateStr=date_create($row2[$key]);
						$myObj->item_threads[$i]->$key = date_format($dateStr, "M d, Y");
					}
					else if(strcmp($key,"is_closed") == 0){
						if($row2[$key] == 0){
							$myObj->item_threads[$i]->$key = "Open";
						}
						else{
							$myObj->item_threads[$i]->$key = "Closed";
						}
					}
					else{
						$myObj->item_threads[$i]->$key = $value;
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
			$myObj->item_threads_count = 0;
			
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
		
		
	}
	
	else if(isset($_POST["open_item"])){
		$myObj = new stdClass();
		
		// Edit the item_thread with that id
		$sql = "UPDATE item_threads SET is_closed='0', date_closed = NULL WHERE item_thread_id = ".$_POST["item_thread_id"];
		if ($conn->query($sql) === TRUE) {
			$myObj->message="Thread updated!";
			$myObj->alert="success";
			$myObj->id = $_POST["item_thread_id"];
			
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
		else{
			$myObj->message="Unable to modify thread. Please try again.";
			$myObj->alert="danger";
			
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
	}
	
	else if(isset($_POST["close_item"])){
		$myObj = new stdClass();
		
		// Edit the item_thread with that id
		$sql = "UPDATE item_threads SET is_closed='1', date_closed = '".date("Y-m-d")."' WHERE item_thread_id = ".$_POST["item_thread_id"];
		if ($conn->query($sql) === TRUE) {
			$myObj->message="Thread updated!";
			$myObj->alert="success";
			$myObj->id = $_POST["item_thread_id"];
			
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
		else{
			$myObj->message="Unable to modify thread. Please try again.";
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