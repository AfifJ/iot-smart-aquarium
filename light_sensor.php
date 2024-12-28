<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//----------------------------------------------------------------------------

if ($_SERVER["REQUEST_METHOD"] == "POST") {

		$light = escape_data($_POST["light"]);
		
		$sql = "INSERT INTO tbl_temperature(light,created_date) 
			VALUES('".$light."','".date("Y-m-d H:i:s")."')";

		if($conn->query($sql) === FALSE)
			{ echo "Error: " . $sql . "<br>" . $conn->error; }

		echo "OK. INSERT ID: ";
		echo $conn->insert_id;
	}
	//MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM
//----------------------------------------------------------------------------
else
{
	echo "No HTTP POST request found";
}
//----------------------------------------------------------------------------


function escape_data($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}