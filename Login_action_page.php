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
	
	if(isset($_POST["PageLoad"])){
				
		$myObj = new stdClass();
		$myObj->items = array();
		$myObj->response = "set";
		
		// Select all non closed, non deleted, active company account items for sale
		$sql = "SELECT item_threads.book_title, item_threads.image_location, item_threads.price 
				from item_threads
				join companies on companies.company_id = item_threads.company_id
				join users on users.user_id = companies.user_id
				where item_threads.is_closed = '0' and item_threads.is_deleted = '0' and users.is_active = '1' order by rand() LIMIT 5
				";
				
				$result = $conn->query($sql);

				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						$ItemImageLocation = $row["image_location"];
						// Remove ../ since images resource is in the same directory
						$ItemImageLocation = substr($ItemImageLocation,3);
						
						if(!file_exists($ItemImageLocation)){
							$ItemImageLocation = "https://via.placeholder.com/500x700";
						}
						else{
							$ItemImageLocation .= "?" . filemtime($ItemImageLocation);
						}
						$item_price = number_format($row["price"], 2, '.', ',');
						array_push($myObj->items,array($row["book_title"],$ItemImageLocation,$item_price));
					}
				}
		
		$myJSON = json_encode($myObj);
					
		echo $myJSON;
		
		$conn->close();
		exit();
	}
	
	else if(isset($_POST["Buyer_Register"])){
		$myObj = new stdClass();
		$myObj->response = "set";
		
		// Check if password fields match
		if(strcmp($_POST["password"],$_POST["c_password"]) != 0){
			$myObj->message = "Password fields don't match!";
			$myObj->alert="danger";
			
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
		
		// Check if email is taken
		$sql = "SELECT * from users where email = '".$_POST["email"]."'
				";
		$result = $conn->query($sql);
		
		if ($result->num_rows > 0) {
			$myObj->message = "Email is already taken!";
			$myObj->alert="danger";
			
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
		
		else{
			// Insert User
			$sql = "INSERT INTO users (email,name,user_type,is_active,contact_no,password)
								VALUES ('".$_POST["email"]."','".$_POST["name"]."','buyer','1','".$_POST["contact_no"]."','".$_POST["password"]."')";
						
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
			
			// Check AJAX image
			if(strcmp($_POST["hasImage"],"false") == 0 || !file_exists($_FILES['image']['tmp_name']) || !is_uploaded_file($_FILES['image']['tmp_name'])) {
				$myObj->message = "Account Registered!";
				$myObj->alert="success";
				
				$myJSON = json_encode($myObj);
				echo $myJSON;
				$conn->close();
				exit();
			}
			else{
				// Edit User
				
				// Insert Image
				// Upload Image
				$target_dir = "../img/users/";
				$target_file = $target_dir . basename($_FILES["image"]["name"]);
				$uploadOk = 1;
				$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
				$fileName = $target_dir . $last_id;
				$destination = $target_dir . $last_id .".". $imageFileType;
				$target_dir_login_page = "img/users/";
				$destination_login_page = $target_dir_login_page . $last_id .".". $imageFileType;

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
					if (move_uploaded_file($_FILES["image"]["tmp_name"], $destination_login_page)) {
						
					} else {
						
					}
				}
				
				// Update user table
				
				if(file_exists($destination_login_page)){
					
					$sql = "UPDATE users SET image_location='".$destination."' WHERE user_id = ".$last_id;
					if ($conn->query($sql) === TRUE) {
						$myObj->message="Account Registered!";
						$myObj->alert="success";
						
						$myJSON = json_encode($myObj);
						echo $myJSON;
						$conn->close();
						exit();
					}
					else{
						$myObj->message="Account image insertion error!";
						$myObj->alert="danger";
						
						$myJSON = json_encode($myObj);
						echo $myJSON;
						$conn->close();
						exit();
					}
				}
			}
		}
			
		$myJSON = json_encode($myObj);
		echo $myJSON;
		$conn->close();
		exit();
	}
	
	else if(isset($_POST["Company_Register"])){
		$myObj = new stdClass();
		$myObj->response = "set";
		
		// Check if password fields match
		if(strcmp($_POST["password"],$_POST["c_password"]) != 0){
			$myObj->message = "Password fields don't match!";
			$myObj->alert="danger";
			
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
		
		// Check if company name is taken
		$sql = "SELECT * from companies where name = '".$_POST["name"]."'
				";
		$result = $conn->query($sql);
		
		if ($result->num_rows > 0) {
			$myObj->message = "Seller name is already taken!";
			$myObj->alert="danger";
			
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
		
		// Check if email is taken
		$sql = "SELECT * from users where email = '".$_POST["email"]."'
				";
		$result = $conn->query($sql);
		
		if ($result->num_rows > 0) {
			$myObj->message = "Email is already taken!";
			$myObj->alert="danger";
			
			$myJSON = json_encode($myObj);
			echo $myJSON;
			$conn->close();
			exit();
		}
		
		else{
			// Insert User
			$sql = "INSERT INTO users (email,name,user_type,is_active,contact_no,password)
								VALUES ('".$_POST["email"]."','".$_POST["users_name"]."','seller','1','".$_POST["users_contact_no"]."','".$_POST["password"]."')";
						
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
			
			// Insert Company
			$sql = "INSERT INTO companies (name,email,address,contact_no,user_id,approval_status)
								VALUES ('".$_POST["name"]."','".$_POST["email"]."','".$_POST["address"]."','".$_POST["contact_no"]."','".$last_id."','pending')";
						
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
			
			// Check AJAX image
			if(strcmp($_POST["hasImage"],"false") == 0 || !file_exists($_FILES['image']['tmp_name']) || !is_uploaded_file($_FILES['image']['tmp_name'])) {
				$myObj->message = "Account Registered!";
				$myObj->alert="success";
				
				$myJSON = json_encode($myObj);
				echo $myJSON;
				$conn->close();
				exit();
			}
			else{
				// Edit User
				
				// Insert Image
				// Upload Image
				$target_dir = "../img/companies/";
				$target_file = $target_dir . basename($_FILES["image"]["name"]);
				$uploadOk = 1;
				$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
				$fileName = $target_dir . $last_id;
				$destination = $target_dir . $last_id .".". $imageFileType;
				$target_dir_login_page = "img/companies/";
				$destination_login_page = $target_dir_login_page . $last_id .".". $imageFileType;

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
					if (move_uploaded_file($_FILES["image"]["tmp_name"], $destination_login_page)) {
						
					} else {
						
					}
				}
				
				// Update companies table
				
				if(file_exists($destination_login_page)){
					
					$sql = "UPDATE companies SET image_location='".$destination."' WHERE company_id = ".$last_id;
					if ($conn->query($sql) === TRUE) {
						$myObj->message="Account Registered!";
						$myObj->alert="success";
						
						$myJSON = json_encode($myObj);
						echo $myJSON;
						$conn->close();
						exit();
					}
					else{
						$myObj->message="Account image insertion error!";
						$myObj->alert="danger";
						
						$myJSON = json_encode($myObj);
						echo $myJSON;
						$conn->close();
						exit();
					}
				}
			}
		}
			
		$myJSON = json_encode($myObj);
		echo $myJSON;
		$conn->close();
		exit();
	}
	
	else if(isset($_POST["Login"])){
				
		$myObj = new stdClass();
		$myObj->response = "login failed";
		$myObj->message = "Invalid Credentials! Please double check your email and password.";
		$myObj->alert = "danger";
		
		$sql = "SELECT * from users where email = '".$_POST["email"]."' and password = '".$_POST["password"]."'
				";
				
		$result = $conn->query($sql);

		if ($result->num_rows == 1) {
			while($row = $result->fetch_assoc()) {
				
				// If account was deactivated, prevent login
				if($row["is_active"] == false){
					$myObj->message = "Account disabled. Please contact administration.";
					$myObj->alert = "danger";
					
					$myJSON = json_encode($myObj);
					echo $myJSON;	
					$conn->close();
					exit();
				}
				
				// If buyer or admin, send user record only
				if(strcmp($row["user_type"],"admin") == 0 || strcmp($row["user_type"],"buyer") == 0){
				
					// Delete all 1 week old closed item_threads 
					if(strcmp($row["user_type"],"admin") == 0){
						$dt_today = date("Y-m-d");
						$sql2 = "UPDATE item_threads SET is_deleted = '1'
							WHERE is_closed = '1' and date_closed IS NOT NULL and DATEDIFF('".$dt_today."',item_threads.date_closed) >= 7
						";
						$conn->query($sql2);
					}
				
					$myObj->user = new stdClass();
					foreach ($row as $key => $value) {
						if(strcmp($key,"image_location") == 0){
							$value = substr($value,3); // remove ../ since image came from the same directory
							if(file_exists($value)){
								$myObj->user->$key = "../" . $value . "?" . filemtime($value);
							}
							else{
								$myObj->user->$key = "https://via.placeholder.com/250";
							}
						}
						else{
							$myObj->user->$key = $value;
						}
					}
					$myObj->response = "login success";
					
					$myJSON = json_encode($myObj);
					echo $myJSON;	
					$conn->close();
					exit();
				}
				
				// If seller, send user and company record
				if(strcmp($row["user_type"],"seller") == 0){
				
					$myObj->user = new stdClass();
					foreach ($row as $key => $value) {
						$myObj->user->$key = $value;
					}
					
					$sql = "SELECT * from companies where user_id = '".$row["user_id"]."'
					";
					
					$result2 = $conn->query($sql);

					if ($result2->num_rows == 1) {
						while($row2 = $result2->fetch_assoc()) {
							
							if(strcmp($row2["approval_status"],"denied") == 0){
								$dateStr=date_create($row2["date_denied"]);
								$dt = date_format($dateStr,"M d, Y");
								$myObj->response = "login failed";
								$myObj->message = "Your company's request to use the platform was denied on ".$dt.".";
								$myObj->alert = "danger";
								
								$myJSON = json_encode($myObj);
								echo $myJSON;	
								$conn->close();
								exit();
							}
							else if(strcmp($row2["approval_status"],"pending") == 0){
								$myObj->response = "login failed";
								$myObj->message = "Your company account is still undergoing verification.";
								$myObj->alert = "warning";
								
								$myJSON = json_encode($myObj);
								echo $myJSON;	
								$conn->close();
								exit();
							}
							
							$myObj->company = new stdClass();
							foreach ($row2 as $key => $value) {
								if(strcmp($key,"image_location") == 0){
									$value = substr($value,3); // remove ../ since image came from the same directory
									
									if(file_exists($value)){
										$myObj->company->$key = "../" . $value . "?" . filemtime($value);
									}
									else{
										$myObj->company->$key = "https://via.placeholder.com/250";
									}
								}
								else{
									$myObj->company->$key = $value;
								}
							}
							
							$myObj->response = "login success";
					
							$myJSON = json_encode($myObj);
							echo $myJSON;	
							$conn->close();
							exit();
						}
					}
					
					$myObj->response = "login failed";
					$myObj->message = "An error occurred while logging in. Please try again.";
					$myObj->alert = "danger";
					
					$myJSON = json_encode($myObj);
					echo $myJSON;	
					$conn->close();
					exit();
				}
			}
		}
		
		$myJSON = json_encode($myObj);
		echo $myJSON;
		$conn->close();
		exit();
	}
	
	$conn->close();
	
	
	function test_input($data) {
	  $data = trim($data);
	  $data = stripslashes($data);
	  $data = htmlspecialchars($data);
	  return $data;
	}
?>