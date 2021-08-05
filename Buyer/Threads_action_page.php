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
		
		// Select all unique non-deleted item threads which this user commented on
		$sql = "SELECT DISTINCT comments.item_thread_id from comments join item_threads on comments.item_thread_id = item_threads.item_thread_id 
			where user_id = '".$_POST["user_id"]."' and item_threads.is_deleted = '0'
			";
			
		$result2 = $conn->query($sql);

		if ($result2->num_rows > 0) {
			$myObj->item_threads = array();
			$myObj->item_threads_count = $result2->num_rows;
			$i = 0;
			while($row2 = $result2->fetch_assoc()) {
				$myObj->item_threads[$i] = new stdClass();
				
				// Get item_thread info

				$sql = "SELECT item_threads.date_created, item_threads.image_location, item_threads.is_closed, item_threads.book_title, item_threads.price,
						companies.name, users.is_active
					from item_threads join companies on companies.company_id = item_threads.company_id 
					join users on companies.user_id = users.user_id
					where item_thread_id = '".$row2["item_thread_id"]."'
				";
				
				$result3 = $conn->query($sql);

				if ($result3->num_rows == 1) {
					while($row3 = $result3->fetch_assoc()) {
						// Select user's latest comment on that item thread
						$sql = "SELECT * from comments where item_thread_id = '".$row2["item_thread_id"]."'
							order by datetime_created DESC
							LIMIT 1
						";
						
						$result4 = $conn->query($sql);

						if ($result4->num_rows == 1) {
							while($row4 = $result4->fetch_assoc()) {
								
								// Prepare date values
								$dateStr=date_create($row3["date_created"]);
								$dcr = date_format($dateStr, "M d, Y");
								
								$dateStr=date_create($row4["datetime_created"]);
								$dco = date_format($dateStr, "M d, Y");
								
								// Prepare image resource
								if(file_exists($row3["image_location"])){
									$myObj->item_threads[$i]->image_location = $row3["image_location"] . "?" . filemtime($row3["image_location"]);
								}
								else{
									$myObj->item_threads[$i]->image_location = "https://via.placeholder.com/32";
								}
								
								// Thread status string - Closed if thread is inactive or company account is disabled
								if(strcmp($row3["is_closed"],"1") == 0 || strcmp($row3["is_active"],"0") == 0){
									$myObj->item_threads[$i]->is_closed = "Closed";
								}
								else{
									$myObj->item_threads[$i]->is_closed = "Open";
								}
								
								// Insert row info to JSON object
								$myObj->item_threads[$i]->book_title = $row3["book_title"];
								$myObj->item_threads[$i]->price = $row3["price"];
								$myObj->item_threads[$i]->name = $row3["name"];
								$myObj->item_threads[$i]->date_created = $dcr;
								$myObj->item_threads[$i]->date_commented = $dco;
								$myObj->item_threads[$i]->item_thread_id = $row2["item_thread_id"];
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
			$myObj->item_threads_count = 0;
			
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