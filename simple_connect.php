<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "my_database";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	$sql = "INSERT INTO customer( name, surname, phone_number )
	VALUES ( 'DJER', 'Emmanuel', '+7 900 652 75 00'   )";

	if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
	} 
	else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}

	$conn->close();

 ?>