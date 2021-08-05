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
	
	if(isset($_POST["Post_Item"])){
		$myObj = new stdClass();
		
		// Prepare variables to be inserted
		$_POST["price"] = number_format($_POST["price"], 2, '.', ',');
		$_POST["author"] = ucwords($_POST["author"]);
		$_POST["genre"] = ucwords($_POST["genre"]);
		
		// Insert Item Record
		$sql = "INSERT INTO item_threads (book_title,binding_type,author,genre,price,image_location,company_id,date_created,is_closed,is_deleted)
					VALUES ('".$_POST["book_title"]."','".$_POST["binding_type"]."','".$_POST["author"]."','".$_POST["genre"]."','".$_POST["price"]."','','".$_POST["company_id"]."','".date("Y-m-d H:i:s")."','0','0')";
					
		$last_id = -1;
		if ($conn->query($sql) === TRUE) {
			$last_id = $conn->insert_id;
		}
		else{
			$myObj->message = "DB Insertion Error.";
			$myObj->alert="danger";
			
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
		// Update its Image Location
		// Check if image was not updated
		if(strcmp($_POST["hasImage"],"false") == 0 || !file_exists($_FILES['image']['tmp_name']) || !is_uploaded_file($_FILES['image']['tmp_name'])) {
			$myObj->message = "No image selected!";
			$myObj->alert="danger";
			
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
		else{
			// Insert Image
			// Upload Image
			$target_dir = "../img/items/";
			$target_file = $target_dir . basename($_FILES["image"]["name"]);
			$uploadOk = 1;
			$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
			$fileName = $target_dir . $last_id;
			$destination = $target_dir . $last_id .".". $imageFileType;

			// Check if image file is a actual image or fake image
			$check = getimagesize($_FILES["image"]["tmp_name"]);
			if($check !== false) {
				$uploadOk = 1;
			} 
			else {
				$uploadOk = 0;
				$myObj->message="The selected file is not an image.";
				$myObj->alert="danger";
			}
			
			// Check if file exists
			$img_extensions = array("tiff","pjp","jfif","gif","svg","bmp","png","jpeg","svgz","jpg","webp","ico","xbm","dib","tif","pjpeg","avif");
			
			foreach ($img_extensions as $extension) {
				if(file_exists($fileName.".".$extension)){
					if (!unlink($fileName.".".$extension)) {
					}
					else {
					}
				}
			}					

			// Check file size
			if ($_FILES["image"]["size"] > 1000000) {
				$uploadOk = 0;
				$myObj->message="The file you selected is too large.";
				$myObj->alert="danger";
			}

			// Allow certain file formats
			$isValidExtension = false;
			foreach ($img_extensions as $extension) {
				if($imageFileType == $extension){
					$isValidExtension = true;
					break;
				}
			}
			
			if(!$isValidExtension){
				$uploadOk = 0;
				$myObj->message="Invalid file extension!";
				$myObj->alert="danger";
			}

			// Check if $uploadOk is set to 0 by an error
			if ($uploadOk == 0) {
				$myJSON = json_encode($myObj);
				echo $myJSON;
				$conn->close();
				exit();
			} 
			// if everything is ok, try to upload file
			else {
				if (move_uploaded_file($_FILES["image"]["tmp_name"], $destination)) {
					
				} else {
					
				}
			}
			
			// Update record's image
			if(file_exists($destination)){
				
				$sql = "UPDATE item_threads SET image_location='".$destination."' WHERE item_thread_id = ".$last_id;
				if ($conn->query($sql) === TRUE) {
					$myObj->message="Item posted!";
					$myObj->alert="success";
					
					$myJSON = json_encode($myObj);
					echo $myJSON;
					$conn->close();
					exit();
				}
				else{
					$myObj->message="Item image insertion error!";
					$myObj->alert="danger";
					
					$myJSON = json_encode($myObj);
					echo $myJSON;
					$conn->close();
					exit();
				}
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