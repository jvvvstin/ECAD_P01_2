<?php
// Detect the current session
session_start();

// Include the PHP file that establishes database connection handle: $conn
include_once("mysql_conn.php");

// Include the Page Layout header
include("header.php"); 

// Reading inputs entered in previous page
$email = $_POST["email"];
$pwd = $_POST["password"];

// Define the SQL statement to retrieve the Shopper that matches the email address entered
$qry = "SELECT ShopperID, Name, Password FROM Shopper
		WHERE Email = ?";
$stmt = $conn->prepare($qry);
$stmt->bind_param("s", $email);

if ($stmt->execute()) { // checks if the SQL statement executed successfully
	$result = $stmt->bind_result($shopperID, $name, $acc_pwd); // Execute the SQL and get the returned result
	$result = $stmt->fetch();
	// Verifies that a password matches a hash
	if (password_verify($pwd,$acc_pwd) == true) {
		// Save user's info in session variables
		$_SESSION["ShopperName"] = $name;
		$_SESSION["ShopperID"] = $shopperID;
		$_SESSION["ShopperEmail"] = $email;

		// Release the resource allocated for prepared statement
		$stmt->close();

		// $qry = "SELECT sc.ShopCartID, COUNT(sci.ProductID) AS NumItems
		// 		FROM ShopCart sc LEFT JOIN ShopCartItem sci
		// 		ON sc.ShopCartID = sci.ShopCartID
		// 		WHERE sc.ShopperID = ? AND sc.OrderPlaced = ?";
		// $stmt = $conn->prepare($qry);
		// $orderPlaced = 0;
		// $stmt->bind_param("ii", $shopperID, $orderPlaced);

		// if ($stmt->execute()) {
		// 	$result = $stmt->bind_result($cartID, $numItems);
		// 	$result = $stmt->fetch();
		// 	$stmt->close();

		// 	$_SESSION["Cart"] = $cartID;
		// 	if ($numItems != 0) {
		// 		$_SESSION["NumCartItem"] = $numItems;
		// 	}
		// }

		// Close database connection
		// $conn->close();

		// Redirect to home page
		header("Location: index.php");
		exit;
	}
	else {
		// echo  "<h3 style='color:red'>Invalid Login Credentials</h3>";
		$_SESSION["LoginError"] = "Invalid Login Credentials!";
		// $_SESSION["LoginErrorShown"] = false;
		header("Location: login.php");
		exit;
	}
}

// Include the Page Layout footer
include("footer.php");
?>