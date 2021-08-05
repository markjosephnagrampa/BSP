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
	
	if(isset($_POST["update"])){
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
		
		else{
			// Update user record
			$sql = "UPDATE users SET name='".$_POST["user_name"]."', password='".$_POST["password"]."', contact_no='".$_POST["user_contact_no"]."' WHERE user_id = ".$_POST["user_id"];
			$last_id = -1;
			
			if ($conn->query($sql) === TRUE) {
				//$last_id = $conn->insert_id;
				$last_id = $_POST["user_id"];
			}
			else{
				$myObj->message="User record update error! Please try again.";
				$myObj->alert="danger";
				
				$myJSON = json_encode($myObj);
				echo $myJSON;
				$conn->close();
				exit();
			}
			
			// Update company record
			$sql = "UPDATE companies SET contact_no='".$_POST["contact_no"]."', address='".$_POST["address"]."' WHERE company_id = ".$_POST["company_id"];
			$last_id = -1;
			
			if ($conn->query($sql) === TRUE) {
				//$last_id = $conn->insert_id;
				$last_id = $_POST["company_id"];
			}
			else{
				$myObj->message="Company record update error! Please try again.";
				$myObj->alert="danger";
				
				$myJSON = json_encode($myObj);
				echo $myJSON;
				$conn->close();
				exit();
			}
			
			// Check if image was not updated
			if(strcmp($_POST["hasImage"],"false") == 0 || !file_exists($_FILES['image']['tmp_name']) || !is_uploaded_file($_FILES['image']['tmp_name'])) {
				
				// Re-select user record for sessionStorage variable update
				
				$sql = "SELECT * from users where user_id = '".$_POST["user_id"]."'
					";
					
				$result2 = $conn->query($sql);

				if ($result2->num_rows == 1) {
					while($row2 = $result2->fetch_assoc()) {
			
						$myObj->message = "Profile Updated!";
						$myObj->alert="success";
						
						$myObj->user = new stdClass();
						foreach ($row2 as $key => $value) {
							if(strcmp($key,"image_location") == 0){
								if(file_exists($value)){
									$myObj->user->$key = $value . "?" . filemtime($value);
								}
								else{
									$myObj->user->$key = "";
								}
							}
							else{
								$myObj->user->$key = $value;
							}
						}
						
						// Re-select company record as well
						$sql = "SELECT * from companies where company_id = '".$_POST["company_id"]."'
							";
							
						$result2 = $conn->query($sql);

						if ($result2->num_rows == 1) {
							while($row2 = $result2->fetch_assoc()) {
								$myObj->company = new stdClass();
								if(strcmp($key,"image_location") == 0){
									if(file_exists($value)){
										$myObj->company->$key = $value . "?" . filemtime($value);
									}
									else{
										$myObj->company->$key = "";
									}
								}
								else{
									$myObj->company->$key = $value;
								}
							}
						}
						
						$myJSON = json_encode($myObj);
						echo $myJSON;
						$conn->close();
						exit();
					}
				}
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
				
				// Update companies table
				
				if(file_exists($destination)){
					
					$sql = "UPDATE companies SET image_location='".$destination."' WHERE company_id = ".$_POST["company_id"];
					if ($conn->query($sql) === TRUE) {
						// Re-select user record for sessionStorage variable update
						$sql = "SELECT * from users where user_id = '".$_POST["user_id"]."'
							";
							
						$result2 = $conn->query($sql);

						if ($result2->num_rows == 1) {
							while($row2 = $result2->fetch_assoc()) {
					
								$myObj->message = "Profile Updated!";
								$myObj->update_avatar="true";
								$myObj->alert="success";
								
								$myObj->user = new stdClass();
								foreach ($row2 as $key => $value) {
									if(strcmp($key,"image_location") == 0){
										if(file_exists($value)){
											$myObj->user->$key = $value . "?" . filemtime($value);
										}
										else{
											$myObj->user->$key = "";
										}
									}
									else{
										$myObj->user->$key = $value;
									}
								}
								
								// Re-select company record as well
								$sql = "SELECT * from companies where company_id = '".$_POST["company_id"]."'
									";
									
								$result2 = $conn->query($sql);

								if ($result2->num_rows == 1) {
									while($row2 = $result2->fetch_assoc()) {
										$myObj->company = new stdClass();
										foreach ($row2 as $key => $value) {
											if(strcmp($key,"image_location") == 0){
												if(file_exists($value)){
													$myObj->company->$key = $value . "?" . filemtime($value);
												}
												else{
													$myObj->company->$key = "";
												}
											}
											else{
												$myObj->company->$key = $value;
											}
										}
									}
								}
								
								$myJSON = json_encode($myObj);
								echo $myJSON;
								$conn->close();
								exit();
							}
						}
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
	
	$conn->close();
	
	
	function test_input($data) {
	  $data = trim($data);
	  $data = stripslashes($data);
	  $data = htmlspecialchars($data);
	  return $data;
	}
?>