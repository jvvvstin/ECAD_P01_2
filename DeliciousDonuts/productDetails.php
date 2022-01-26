<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>
<div style="background-image: url('Images/donut-background.jpg'); background-size: 10%;">
<!-- Create a container, 90% width of viewport -->
<div style='width:80%; max-width: 900px; background-color: white; margin:auto;'>

<?php 
$disabled = "";
$pid=$_GET["pid"]; // Read Product ID from query string
if ($pid == "") {
    header("Location: category.php");
    exit;
}

// Include the PHP file that establishes database connection handle: $conn
include_once("mysql_conn.php"); 

// Get the product details and average ratings (%) [e.g. 70%]
$qry = "SELECT p.*, AVG(r.Rank)/5.0 * 100 AS 'Ratings' 
        FROM Product p 
        LEFT JOIN Ranking r on r.ProductID = p.ProductID
        WHERE p.ProductID=?
        GROUP BY ProductID";

$stmt = $conn->prepare($qry);
$stmt->bind_param("i", $pid); 	// "i" - integer 
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Get the current date, without time
$todaysDate = new DateTime('now');
$date = $todaysDate->format('Y-m-d');

// Checks if there are more than 0 rows
// if 0 rows returned, means that ProductID does not exist in the database
if ($result->num_rows > 0) {
    // Display Product information. Starting ....
    while ($row = $result->fetch_array()) {
        
        // Checks if product is currently on offer
        // Offered = 1 and current date is between OfferStartDate and OfferEndDate 
        $onOffer = $row["Offered"] == 1 && $date >= $row["OfferStartDate"] && $date <= $row["OfferEndDate"];
        
        // Display Page Header
        // Product's name is read from the "ProductTitle" column of "product" table.
        echo "<div class='row' style='padding: 5px; margin: 0px;'>";

        // Left column - display the product's description
        echo "<div class='col-md-7' style='padding: 15px'>";
        echo "<span class='page-title'>$row[ProductTitle]</span>";
        
        // Display special offer image if product is on offer
        if ($onOffer) {
            $src = "./Images/offer.png";
            echo "<img style='width: 50px; margin-left: 15px;' src=$src alt='Special Offer icon' />";
        }
        $rate_bg = 0;
        if ($row["Ratings"] != null) {
            $rate_bg = $row["Ratings"];
        }

        // Displays the ratings in stars
        echo "<div class='containerdiv' style='margin: 15px 0;'>
                <div>
                    <img class='rating-stars' src='https://image.ibb.co/jpMUXa/stars_blank.png' alt='Empty stars'>
                </div>
                <div class='cornerimage' style='width:$rate_bg%;'>
                    <img class='rating-stars' src='https://image.ibb.co/caxgdF/stars_full.png' alt='Filled stars'>
                </div>
            </div>";

        // Display the product's specification
        echo "<div class='col-md-12' style='padding: 0px;'>";
        echo "<p style='font-size: 120%;'><strong>Description: </strong>$row[ProductDesc]</p>";
        
        // Retrieve the product specifications (e.g. Sweetness, ingredients)
        $qry = "SELECT s.SpecName, ps.SpecVal FROM productspec ps
                INNER JOIN specification s ON ps.SpecID = s.SpecID
                WHERE ps.ProductID = ?
                ORDER BY ps.Priority";
        $stmt = $conn->prepare($qry);
        $stmt->bind_param("i", $pid);   // "i" - integer
        $stmt->execute();
        $result2 = $stmt->get_result();
        $stmt->close();
        while($row2 = $result2->fetch_array()) {
            echo "<strong>".$row2["SpecName"].": </strong>".$row2["SpecVal"]."<br />";
        }
        echo "</div>";  // End of product's specification div
        echo "</div>";  // End of left column
        
        // Right column - display the product's image
        $img = "./Images/products/$row[ProductImage]";
        echo "<div class='col-md-5' style='vertical-align: top; padding: 15px; text-align: center;'>";
        echo "<p style='display: flex; justify-content: center;'><img class='product-img' src=$img /></p>";

        // Right column - display the product's price   
        $formattedPrice = number_format($row["Price"], 2);
        if ($onOffer) { // Checks if product is currently on offer
            $offerPrice = number_format($row["OfferedPrice"], 2);

            // Strike out original price and display the current offered price
            echo "<p style='margin-top: 14px; display: inline-block;'>Price: <span style='color: red;'><del>S$ $formattedPrice</del></span> <span style='font-weight: bold; color: red; font-size: 130%;'>
                S$ $offerPrice</span></p>";
        }
        else {
            // Display the original/current price
            echo "<p style='margin-top: 14px; display: inline-block;'>Price:<span style='font-weight: bold; color: red;'>
                S$ $formattedPrice</span></p>";
        }
        
        // Checks if product is currently out of stock (Quantity = 0)
        if ($row["Quantity"] <= 0) {
            // Display out of stock message
            echo "<p style='color: red; margin: 0px'>Out of Stock!</p>";
            $disabled = "disabled"; // Set button status to disabled later
        }
        
    }
}
else {
    // If product ID does not exist in the database, return user to Product Catalog page
    header("Location: category.php");
    exit;
}

// To Do 1:  Ending ....

// To Do 2:  Create a Form for adding the product to shopping cart. Starting ....
echo "<form action='cartFunctions.php' method='post'>";
echo "<input type='hidden' name='action' value='add' />";
echo "<input type='hidden' name='product_id' value='$pid' />";
echo "Quantity: <input type='number' name='quantity' value='1'
                 min='1' max='10' style='width: 40px' required $disabled/>";
echo "<button $disabled type='submit' style='border-radius: 3px; margin-left: 5px; margin-top: 10px;'>Add to Cart</button>";
echo "</form>";
echo "</div>";  // End of right column
echo "</div>";
echo "</div>";  // End of row
echo "</div>";

$conn->close(); // Close database connnection
include("footer.php"); // Include the Page Layout footer
?>
