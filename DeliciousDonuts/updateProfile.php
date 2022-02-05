<?php
session_start(); //Detect the current session

include_once("mysql_conn.php");

// Read the data input from previous page
$name = $_POST["name"];
$dob = $_POST["dob"];
$address = $_POST["address"];
$country = $_POST["country"];
$phone = "(65) " . $_POST["phone"];
$email = $_POST["email"]; //ecader@gmail.com

$qry = "SELECT ShopperID FROM Shopper WHERE Email=?";
$stmt = $conn->prepare($qry);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

$is_current_email = TRUE;
if ($result->num_rows > 0) //ecader@gmail.com
{
    while($row = $result->fetch_array()){
        if(strtolower($_SESSION["ShopperEmail"]) == strtolower($email)){ 
            $is_current_email = TRUE;
        }
        else
        {
            $is_current_email = FALSE;
            $Message = "<h2 style='color:red;'>Error updating profile</h2>
                        <p>There is an existing account with the same email. Please try again <a href='javascript:history.back()'>here</a></p>";
        }
    }
}

if ($is_current_email == TRUE)
{
    $qry = "UPDATE Shopper SET Name=?, BirthDate=?, Address=?, Country=?, Phone=?, Email=? WHERE ShopperID=?";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("ssssssi", $name, $dob, $address, $country, $phone, $email, $_SESSION["ShopperID"]);
    
    if ($stmt->execute()) // SQL statement executed successfully
    {
        $Message = "<h2 style='color:green;'>Profile updated successfully</h2>
                    <p>You have sucessfully update your profile. Click <a href='index.php'>here</a> to continue shopping</p>";
        $_SESSION["ShopperName"] = $name;
        $_SESSION["ShopperEmail"] = $email;
    }
    else    //ERROR MESSAGE
    {
        $Message = "<h2 style='color:red'> Error in updating records</h2>";
    }
    
    //  Release the resource allocated for prepared statement
    $stmt->close();
}

//  Close Database connection
$conn->close();
//  Display Page Layout header with updated session state and links
include("header.php");
//  Display message
echo "<br />";
echo "<div class='text-center'>";
echo $Message;
echo "</div>";

//  Display Page Layout Footer
include("footer.php");
?>
