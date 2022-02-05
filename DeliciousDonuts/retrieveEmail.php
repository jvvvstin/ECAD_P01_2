<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header

include_once("mysql_conn.php");

$email = $_POST["email"];

$qry = "SELECT PwdQuestion FROM Shopper WHERE Email=?";
$stmt = $conn->prepare($qry);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows > 0) 
{
    while($row = $result->fetch_array()){
        $pwdQuest = $row["PwdQuestion"];
        $Message = "<h2>Password Security Retrieval</h2>
                    <p>Please enter your email to load your security question</p>
                    <p>Your Security Question is:
                    <b>$pwdQuest</b>";
    }
}
else
{
    $Message = "<h2 style='color:red;'>Error retrieving security question</h2>
                <p>Invalid email address entered. Please try again <a href='javascript:history.back()'>here</a></p>";
}

$conn->close();

echo "<br />";
echo "<div class='text-center'>";
echo $Message;
echo "</div>";

echo "<div style='width:80%; margin:auto;'>";
echo "<form name='securityAns' method='post' action='checkSecurityEmail.php'>";
echo    "<div class='form-group row'>";
echo        "<label class='col-sm-3 col-form-label' for='answer'> Security Answer: </label>";
echo        "<div class='col-sm-9'>";
echo            "<input class='form-control' name='answer' id='answer' type='text' required />";
echo        "</div>";
echo    "</div>";
echo    "<input type='hidden' name='email' id='email' value='$email' />";
echo    "<div class='form-group row'>";
echo        "<div class='col-sm-9 offset-sm-3'>";
echo            "<button type='submit'>Submit</button>";
echo        "</div>";
echo    "</div>";
echo  "</form>";
echo "</div>";
?>


<?php 
include("footer.php"); // Include the Page Layout footer
?>