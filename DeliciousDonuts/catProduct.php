<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>
<div style="background-image: url('Images/donut-background.jpg'); background-size: 10%;">
<!-- Create a container, 60% width of viewport -->
<div style='width:80%; max-width: 900px; background-color: white; margin:auto;'>
<!-- Display Page Header - Category's name is read 
     from the query string passed from previous page -->
<div class="row" style="padding:5px; margin: 0px;">
	<div class="col-12">
		<span class="page-title"><?php echo "$_GET[catName]"; ?></span>
	</div>
</div>

<?php 
// Include the PHP file that establishes database connection handle: $conn
include_once("mysql_conn.php");

// To Do:  Starting ....
$cid = $_GET["cid"];	// Read Category ID from query string
// Form SQL to retrieve list of products associated to the Category ID
$qry = "SELECT p.ProductID, p.ProductTitle, p.ProductImage, p.Price, p.Quantity, AVG(r.Rank)/5.0 * 100 AS 'Ratings'
        FROM CatProduct cp INNER JOIN Product p on cp.ProductID = p.ProductID 
        LEFT JOIN Ranking r on r.ProductID = p.ProductID
        WHERE cp.CategoryID = ?
        GROUP BY p.ProductID
        ORDER BY p.ProductTitle;";
$stmt = $conn->prepare($qry);
$stmt->bind_param("i", $cid);	// "i" - integer
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

$row_num = 1;
$outputString = '';
// Display each product in a row
while($row = $result->fetch_array()) {
	if ($row_num == $result->num_rows) { // Checks if its last row
        // If last row, increase padding for bottom
        echo "<div class='row' style='padding: 5px 0px 50px 0px; margin: 15px 0px 0px 0px;'>";  // Start a new row
    }
    else {
        echo "<div class='row' style='padding: 5px; margin: 15px 0px;'>";  // Start a new row
    }

	// Left column - display a text link showing the product's name,
	//				 display the selling price in red in a new paragraph
	$product = "productDetails.php?pid=$row[ProductID]";
	$formattedPrice = number_format($row["Price"], 2);
	echo "<div class='col-md-8'>";	// 67% of row width
	echo "<p><a style='font-size: 150%; color: black;' href=$product>$row[ProductTitle]</a></p>";
    $rate_bg = 0;
    if ($row["Ratings"] != null) {
        $rate_bg = $row["Ratings"];
    }
    echo "<div class='containerdiv'>
            <div>
                <img class='rating-stars' src='https://image.ibb.co/jpMUXa/stars_blank.png' alt='Empty stars'>
            </div>
            <div class='cornerimage' style='width:$rate_bg%;'>
                <img class='rating-stars' src='https://image.ibb.co/caxgdF/stars_full.png' alt='Filled stars'>
            </div>
          </div>";
	echo "<p style='margin-top: 14px;'>Price:<span style='font-weight: bold; color: red;'>
		  S$ $formattedPrice</span></p>";
    
    if ($row["Quantity"] <= 0) {
        echo "<p style='color: red;'>This product is currently <u>out of stock</u>!</p>";
    }

    
            
	echo "</div>";

    $rate_bg = 0;
    if ($row["Ratings"] != null) {
        $rate_bg = $row["Ratings"];
    }
	// Right column - display the product's image
	$img = "./Images/products/$row[ProductImage]";
	echo "<div class='col-md-4 productlisting-img'>";	// 33% of row width
	echo "<a href=$product><img class='product-img' src='$img' /></a>";
	echo "</div>";
	echo "</div>";	// End of a row
    echo "<hr>";

    $row_num +=1;
}
?>
<!-- To Do:  Ending .... -->
</div> <!-- End of container -->
</div>
<?php
$conn->close(); // Close database connnection
include("footer.php"); // Include the Page Layout footer
?>
