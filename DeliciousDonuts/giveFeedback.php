<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
include_once("mysql_conn.php");
if (!isset($_SESSION["ShopperID"])) { // Check if user logged in 
	// redirect to login page if the session variable shopperid is not set
	header ("Location: login.php");
	exit;
}

?>
<br />
<div class="row align-items-center">
    <div class="col-sm">
    </div>
    <div class="col-sm">
        <h2 style="text-align:center;">Submit a feedback</h2>
    </div>
    <div class="col-sm">
    </div>
</div>
<div class="row align-items-center">
    <div class="col-sm">
    </div>
    <div class="col-sm">
        <p style="text-align:center;">Tell us your favourite donut, we greatly appreciate all the feedback</p>
    </div>
    <div class="col-sm">
    </div>
</div>
<br />

<?php

$qry = "SELECT * FROM Product";
$stmt = $conn->prepare($qry);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

echo "<div style='width:80%; margin:auto;'>
        <form name='feedback' method='post' action='submitFeedback.php'>
            <div class='form-group row'>
                <label class='col-sm-3 col-form-label' for='donut'>
                Donut: </label>
                <div class='col-sm-9'>
                    <select class='form-control' name='donut' id='donut'>";
                    if ($result->num_rows > 0){
                        while($row = $result->fetch_array())
                        {
                            $productTitle = $row['ProductTitle'];
                            $productId = $row['ProductID'];
                            echo "<option value=$productId>$productTitle</option>";
                        }
                    }
echo "</select>
                </div>
            </div>
            <div class='form-group row'>
            <label class='col-sm-3 col-form-label' for='comment'>Comment:</label>
                <div class='col-sm-9'>
                    <textarea class='form-control' name='comment' id='comment'
                            cols='25' rows='4' required></textarea>
                </div>
            </div>
            <div class='form-group row'>
            <label class='col-sm-3 col-form-label' for='rating'>
            Ranking: </label>
            <div class='col-sm-9'>
            <select class='form-control' name='rating' id='rating'>
                <option value=1>1 - Extremely poor</option>
                <option value=2>2 - Slightly poor</option>
                <option value=3>3 - Average</option>
                <option value=4>4 - Above Average</option>
                <option value=5>5 - Excellent</option>
            </select>
            </div>
            </div>
            <div class='form-group row'>       
                <div class='col-sm-9 offset-sm-3'>
                    <button type='submit'>Submit</button>
                </div>
            </div>
        </form> ";
?>
</div> <!-- Closing container -->
<?php 
include("footer.php"); // Include the Page Layout footer
?>