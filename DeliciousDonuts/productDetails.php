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

// Include the PHP file that establishes database connection handle: $conn
include_once("mysql_conn.php"); 
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


// Display Product information. Starting ....
while ($row = $result->fetch_array()) {
    // Display Page Header
    // Product's name is read from the "ProductTitle" column of "product" table.
    echo "<div class='row' style='padding: 5px; margin: 0px;'>";

    // Left column - display the product's description
    echo "<div class='col-md-8' style='padding: 15px'>";
    echo "<span class='page-title'>$row[ProductTitle]</span>";
    $rate_bg = 0;
    if ($row["Ratings"] != null) {
        $rate_bg = $row["Ratings"];
    }
    echo "<div class='containerdiv' style='margin: 15px 0;'>
            <div>
                <img class='rating-stars' src='https://image.ibb.co/jpMUXa/stars_blank.png' alt='Empty stars'>
            </div>
            <div class='cornerimage' style='width:$rate_bg%;'>
                <img class='rating-stars' src='https://image.ibb.co/caxgdF/stars_full.png' alt='Filled stars'>
            </div>
          </div>";

    // echo "<div class='row' style='padding: 5px; margin: 15px 0px 0px 0px;'>";   // Start a new row

    // Display the product's specification
    echo "<div class='col-md-12' style='padding: 0px;'>";
    echo "<p style='font-size: 120%;'><strong>Description: </strong>$row[ProductDesc]</p>";
    
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
    echo "<div class='col-md-4' style='vertical-align: top; padding: 15px;'>";
    echo "<p style='display: flex; justify-content: center;'><img class='product-img' src=$img /></p>";

    // Right column - display the product's price
    $formattedPrice = number_format($row["Price"], 2);
    echo "Price: <span style='font-weight: bold; color: red'>
          S$ $formattedPrice</span>";
    if ($row["Quantity"] <= 0) {
        echo "<p style='color: red; margin: 0px'>Out of Stock!</p>";
        $disabled = "disabled";
    }
    
}
// To Do 1:  Ending ....

// To Do 2:  Create a Form for adding the product to shopping cart. Starting ....
echo "<form action='cartFunctions.php' method='post'>";
echo "<input type='hidden' name='action' value='add' />";
echo "<input type='hidden' name='product_id' value='$pid' />";
echo "Quantity: <input type='number' name='quantity' value='1'
                 min='1' max='10' style='width: 40px' required />";
echo "<button $disabled type='submit' style='border-radius: 3px; margin-left: 5px; margin-top: 10px;'>Add to Cart</button>";
echo "</form>";
echo "</div>";  // End of right column
echo "</div>";
// echo "</div>";  // End of row

$conn->close(); // Close database connnection
include("footer.php"); // Include the Page Layout footer
?>
