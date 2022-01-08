<?php
ob_start();
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

$cid = $_GET["cid"];	// Read Category ID from query string

// Form SQL to retrieve list of products associated to the Category ID
// Retrieves products that belong to the selected category, in alphabetical order
// Product details + product's average ratings
$qry = "SELECT p.*, AVG(r.Rank)/5.0 * 100 AS 'Ratings'
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

// Get the current date, without time
$todaysDate = new DateTime('now');
$date = $todaysDate->format('Y-m-d');

if ($result->num_rows > 0) {
    $row_num = 1;
    // Display each product in a row
    while($row = $result->fetch_array()) {
        $onOffer = $row["Offered"] == 1 && $date >= $row["OfferStartDate"] && $date <= $row["OfferEndDate"]; // Product is on offer if Offered = 1
        //                                                                                                      and current date is between OfferStartDate
        //                                                                                                      and OfferEndDate
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
        echo "<p style='display: inline-block;'><a style='font-size: 150%; color: black;' href=$product>$row[ProductTitle]</a></p>";
        if ($onOffer) { // Display special offer image if product is currently on offer
            $src = "./Images/offer.png";
            echo "<img style='width: 50px; margin-left: 15px;' src=$src alt='Special Offer icon' />";
        }
        $rate_bg = 0;
        if ($row["Ratings"] != null) {
            $rate_bg = $row["Ratings"];
        }
        // Displays product's ratings in number of stars
        echo "<div class='containerdiv'>
                <div>
                    <img class='rating-stars' src='https://image.ibb.co/jpMUXa/stars_blank.png' alt='Empty stars'>
                </div>
                <div class='cornerimage' style='width:$rate_bg%;'>
                    <img class='rating-stars' src='https://image.ibb.co/caxgdF/stars_full.png' alt='Filled stars'>
                </div>
            </div>";

        if ($onOffer) {
            // If product is on offer, strike out the original price and display the current offered price
            $offerPrice = number_format($row["OfferedPrice"], 2);
            echo "<p style='margin-top: 14px; display: inline-block;'>Price: <span style='color: red;'><del>S$ $formattedPrice</del></span> <span style='font-weight: bold; color: red;'>
                S$ $offerPrice</span></p>";
        }
        else {
            // If product is not on offer, display the product's current/original price
            echo "<p style='margin-top: 14px; display: inline-block;'>Price:<span style='font-weight: bold; color: red;'>
                S$ $formattedPrice</span></p>";
        }
        
        // Checks if product is currently out of stock (Quantity = 0)
        if ($row["Quantity"] <= 0) {
            // Display out of stock message
            echo "<p style='color: red;'>This product is currently <u>out of stock</u>!</p>";
        }
    
        echo "</div>";

        // Right column - display the product's image
        $img = "./Images/products/$row[ProductImage]";
        echo "<div class='col-md-4 productlisting-img'>";	// 33% of row width
        echo "<a href=$product><img class='product-img' src='$img' /></a>";
        echo "</div>";
        echo "</div>";	// End of a row
        echo "<hr>";

        $row_num +=1;
    }
}
else {
    header("Location: category.php");
    exit;
}

?>
</div> <!-- End of container -->
</div>
<?php
$conn->close(); // Close database connnection
include("footer.php"); // Include the Page Layout footer
ob_end_flush();
?>
