<?php
session_start(); //Detect the current session

include_once("mysql_conn.php");

$name = $_POST["name"];
$dob = $_POST["dob"];
$address = $_POST["address"];
$country = $_POST["country"];
$phone = "(65) " . $_POST["phone"];
$email = $_POST["email"];
$securityqns = $_POST["securityqns"];
$securityans = $_POST["securityans"];

$nowDT = new DateTime('now');
$dtFormatted = $nowDT->format("Y-m-d h:i:s");

$password = password_hash($_POST["password"], PASSWORD_DEFAULT);

$qry = "SELECT ShopperID FROM Shopper WHERE Email=?";
$stmt = $conn->prepare($qry);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();


if ($result->num_rows > 0)
{
    $Message = "<h2 style='color:red;'>Error creating account</h2>
                <p>There is an existing account with the same email. Please try again <a href='javascript:history.back()'>here</a></p>";
}
else
{
    $qry = "INSERT INTO Shopper (Name, BirthDate, Address, Country, Phone, Email, Password, PwdQuestion, PwdAnswer, ActiveStatus, DateEntered)
            VALUES (?,?,?,?,?,?,?,?,?,1,?)";

    $stmt = $conn->prepare($qry);
    $stmt->bind_param("ssssssssss", $name, $dob, $address, $country, $phone, $email, $password, $securityqns, $securityans, $dtFormatted);

    if ($stmt->execute()) // SQL statement executed successfully
    {
        //Retrieve the shopper ID assigned to the new shopper
        $qry = "SELECT LAST_INSERT_ID() AS ShopperID";
        $result = $conn->query($qry);   //Execute the SQL and get the returned result

        while($row = $result->fetch_array()){
            $_SESSION["ShopperID"] = $row["ShopperID"];
        }

        //Successful message and Shopper ID
        $Message = "<h2 style='color:green;'>Account created successfully.</h2>
                    <p>You have sucessfully created your account. Click <a href='index.php'>here</a> to start shopping</p>";

        //  Save the shopper name in a session variable
        $_SESSION["ShopperName"] = $name;
    }
    else    //ERROR MESSAGE
    {
        $Message = "<h2 style='color:red'> Error in updating records</h2>";

    }
    $stmt->close();

}

//  Close Database connection
$conn->close();
include("header.php");

//  Display message
echo "<br />";
echo "<div class='text-center'>";
echo $Message;
echo "</div>";
//  Display Page Layout Footer
include("footer.php");
?>
