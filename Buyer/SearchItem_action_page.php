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
	if(isset($_POST["search_item"])){
		$myObj = new stdClass();
		
		// Select all non-deleted, non-closed, active company account item threads
		$sql = "SELECT item_threads.item_thread_id, item_threads.book_title, item_threads.binding_type, item_threads.author, item_threads.genre, item_threads.price,
				item_threads.image_location, item_threads.date_created,
				companies.name, companies.image_location as company_image_location
				
				from item_threads 
				join companies on item_threads.company_id = companies.company_id
				join users on users.user_id = companies.user_id
				
				where item_threads.is_closed = '0' and item_threads.is_deleted = '0' and users.is_active = '1'
			";
			
		$result2 = $conn->query($sql);

		if ($result2->num_rows > 0) {
			$myObj->item_threads = array();
			$myObj->item_threads_count = $result2->num_rows;
			$i = 0;
			while($row2 = $result2->fetch_assoc()) {
				$myObj->item_threads[$i] = new stdClass();
				
				// Get no. of comments

				$sql = "SELECT comment_id from comments where item_thread_id = " .$row2["item_thread_id"]. "
				";
				
				$result3 = $conn->query($sql);
				$myObj->item_threads[$i]->comment_count = $result3->num_rows;
				
				$myObj->item_threads[$i]->item_thread_id = $row2["item_thread_id"];
				$myObj->item_threads[$i]->book_title = $row2["book_title"];
				$myObj->item_threads[$i]->binding_type = $row2["binding_type"];
				$myObj->item_threads[$i]->author = $row2["author"];
				$myObj->item_threads[$i]->genre = $row2["genre"];
				$myObj->item_threads[$i]->price = $row2["price"];
				$myObj->item_threads[$i]->name = $row2["name"];
				
				$dateStr=date_create($row2["date_created"]);
				$myObj->item_threads[$i]->date_created = date_format($dateStr,"M d, Y");
				
				if(file_exists($row2["company_image_location"])){
					$myObj->item_threads[$i]->company_image_location = $row2["company_image_location"] . "?" . filemtime($row2["company_image_location"]);
				}
				else{
					$myObj->item_threads[$i]->company_image_location = "https://via.placeholder.com/32";
				}
				
				if(file_exists($row2["image_location"])){
					$myObj->item_threads[$i]->image_location = $row2["image_location"] . "?" . filemtime($row2["image_location"]);
				}
				else{
					$myObj->item_threads[$i]->image_location = "https://via.placeholder.com/200";
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
			$myObj->item_threads = array();
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
		
		
	}
	else if(isset($_POST["search"])){
		$myObj = new stdClass();
		
		// Select all non-deleted, non-closed item threads with text like the search value
		$sql = "SELECT item_threads.item_thread_id, item_threads.book_title, item_threads.binding_type, item_threads.author, item_threads.genre, item_threads.price,
				item_threads.image_location, item_threads.date_created,
				companies.name, companies.image_location as company_image_location
				from item_threads join companies on item_threads.company_id = companies.company_id
				
				where item_threads.is_closed = '0' and item_threads.is_deleted = '0' 
				and (
					LOWER(item_threads.book_title) LIKE LOWER('%".$_POST["search_bar"]."%') or
					LOWER(item_threads.author) LIKE LOWER('%".$_POST["search_bar"]."%') or
					LOWER(item_threads.genre) LIKE LOWER('%".$_POST["search_bar"]."%') or
					LOWER(companies.name) LIKE LOWER('%".$_POST["search_bar"]."%')
				)
			";
			
		$result2 = $conn->query($sql);

		if ($result2->num_rows > 0) {
			$myObj->item_threads = array();
			$myObj->item_threads_count = $result2->num_rows;
			$i = 0;
			while($row2 = $result2->fetch_assoc()) {
				$myObj->item_threads[$i] = new stdClass();
				
				// Get no. of comments

				$sql = "SELECT comment_id from comments where item_thread_id = " .$row2["item_thread_id"]. "
				";
				
				$result3 = $conn->query($sql);
				$myObj->item_threads[$i]->comment_count = $result3->num_rows;
				
				$myObj->item_threads[$i]->item_thread_id = $row2["item_thread_id"];
				$myObj->item_threads[$i]->book_title = $row2["book_title"];
				$myObj->item_threads[$i]->binding_type = $row2["binding_type"];
				$myObj->item_threads[$i]->author = $row2["author"];
				$myObj->item_threads[$i]->genre = $row2["genre"];
				$myObj->item_threads[$i]->price = $row2["price"];
				$myObj->item_threads[$i]->name = $row2["name"];
				
				$dateStr=date_create($row2["date_created"]);
				$myObj->item_threads[$i]->date_created = date_format($dateStr,"M d, Y");
				
				if(file_exists($row2["company_image_location"])){
					$myObj->item_threads[$i]->company_image_location = $row2["company_image_location"] . "?" . filemtime($row2["company_image_location"]);
				}
				else{
					$myObj->item_threads[$i]->company_image_location = "https://via.placeholder.com/32";
				}
				
				if(file_exists($row2["image_location"])){
					$myObj->item_threads[$i]->image_location = $row2["image_location"] . "?" . filemtime($row2["image_location"]);
				}
				else{
					$myObj->item_threads[$i]->image_location = "https://via.placeholder.com/200";
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
			$myObj->item_threads = array();
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