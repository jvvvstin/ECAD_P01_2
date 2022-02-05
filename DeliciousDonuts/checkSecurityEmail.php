<script type="text/javascript">
    var timeleft = 10;
    var downloadTimer = setInterval(function(){
    timeleft--;
    document.getElementById("countdowntimer").textContent = timeleft;
    if(timeleft <= 0){
        clearInterval(downloadTimer);
        window.location.href = "login.php";
    }
    },1000);

</script>
<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header

function password_generate($chars) 
{
  $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
  return substr(str_shuffle($data), 0, $chars);
}


include_once("mysql_conn.php");

$email = $_POST["email"];
$answer = $_POST["answer"];

$qry = "SELECT PwdAnswer FROM Shopper WHERE Email=?";
$stmt = $conn->prepare($qry);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

$change_password = false;
if ($result->num_rows > 0)
{
    while($row = $result->fetch_array()){
        $pwdAnswer = $row["PwdAnswer"];
        if($pwdAnswer == $answer)
        {
            $default_password = password_generate(8);
            $Message = "<h2>Password Security Retrieval</h2>
                        <p>Your new password is: </p>
                        <p><b>$default_password</b></p>";
            
            $change_password = true;

        }
        else
        {
            $Message = "<h2 style='color:red;'>Error retrieving password</h2>
                        <p>Wrong answer to Security Question. Please try again <a href='forgetPassword.php'>here</a></p>";
        }
    }
}
echo "<br />";
echo "<div class='text-center'>";
echo $Message;


if($change_password){
    $password = password_hash($default_password, PASSWORD_DEFAULT);
    $qry = "UPDATE Shopper SET Password=? WHERE Email=?";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("ss", $password, $email);
    $stmt->execute();
    $stmt->close();
    echo "You will be redirected to login page in <span id='countdowntimer'><b>10</b></span> second(s).";

}

$conn->close();
echo "</div>";
?>
<?php 
include("footer.php"); // Include the Page Layout footer
?>