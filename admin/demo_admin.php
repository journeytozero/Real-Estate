<?php

require_once "../admin/partials/db.php";

//Demo Admin credentials

$username = "admin";
$email = "admin@mail.com";
$password = "1234";

$sql = "SELECT * FROM admins WHERE username = ?";

$stmt = $conn->prepare($sql);
$stmt->execute([$username]);

if($stmt->rowCount() > 0)
{
	echo "<h3>Admin user already exists</h3>";	
}

else
{
	$hashed_password = password_hash($password, PASSWORD_DEFAULT);
	$insert_sql = "INSERT INTO admins(username, email,password) VALUES(?,?,?)";

	 $insert_stmt = $conn->prepare($insert_sql);

	 if($insert_stmt->execute([$username, $email, $hashed_password]))
	 {
	 	echo "<h3>Demo Admin user created successfully</h3>";
	 	echo "<p><b>Username:</b>$username</p>";
	 	echo "<p><b>Password:</b>$password</p>";
        echo "<p><b>Email:</b>$email</p>";
	 	echo "<a href='login.php'>Go to Login</a>";
	 }

	 else
	 {
	 	echo "<h3>Failed to create admin user</h3>";
	 }
}

?>