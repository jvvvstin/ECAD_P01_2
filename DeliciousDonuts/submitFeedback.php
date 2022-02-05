<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
include_once("mysql_conn.php");
if (!isset($_SESSION["ShopperID"])) { // Check if user logged in 
	// redirect to login page if the session variable shopperid is not set
	header ("Location: login.php");
	exit;
}

$donut = $_POST['donut'];
$comment = $_POST['comment'];
$rating = $_POST['rating'];
$shopperid = $_SESSION["ShopperID"];

$qry = "INSERT INTO Ranking (ShopperID, ProductID, Rank, Comment) VALUES (?,?,?,?)";
$stmt = $conn->prepare($qry);
$stmt->bind_param("iiis", $shopperid, $donut, $rating, $comment);

$stmt->execute(); // SQL statement executed successfully

?>
<br />
<div class="row align-items-center">
    <div class="col-sm">
    </div>
    <div class="col-sm">
        <h2 style="text-align:center; color:green;">Feedback submitted</h2>
    </div>
    <div class="col-sm">
    </div>
</div>
<div class="row align-items-center">
    <div class="col-sm">
    </div>
    <div class="col-sm">
        <p style="text-align:center;">We have received your feedback. Click <a href='feedback.php'>here</a> to view all feedbacks on our donuts</p></p>
    </div>
    <div class="col-sm">
    </div>
</div>
<br />
</div> <!-- Closing container -->
<?php 
include("footer.php"); // Include the Page Layout footer
?>