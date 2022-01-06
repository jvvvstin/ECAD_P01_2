<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>

<div style="background-image: url('Images/donut-background.jpg'); background-size: 10%;">
<!-- HTML Form to collect search keyword and submit it to the same page in server -->
<div style="width:80%; max-width: 900px; background-color: white; margin:auto; min-height: 375px;"> <!-- Container -->
<form name="frmSearch" method="get" action="">
    <div class="form-group row" style='padding: 5px; margin: 0px;'> <!-- 1st row -->
        <div class="col-sm-9 offset-sm-3">
            <span class="page-title">Product Search</span>
        </div>
    </div> <!-- End of 1st row -->
    <div class="form-group row" style='padding: 5px; margin: 0px;'> <!-- 2nd row -->
        <label for="keywords" 
               class="col-sm-3 col-form-label">Product:</label>
        <div class="col-sm-6">
            <input style="margin-bottom: 10px;" class="form-control" name="keywords" id="keywords" placeholder="Search for a product..."
                   type="search" />
        </div>
        <div class="col-sm-3">
            <button id="searchBtn" type="submit" style="border-radius: 3px;">Search</button>
        </div>
    </div>  <!-- End of 2nd row -->
</form>


<?php
ob_start();
$content = "<h2 style='text-align: center; margin-top: 20px;'>Search for a donut and <br />satisfy your cravings!</h2><br />
<div style='padding: 5px 0px 20px 0px; margin: 0px; text-align: center;'>
    <img style='width: 130px;' src='./Images/donuts-search.png' alt='Picture of donuts' />
</div>";
echo $content;
// Include the PHP file that establishes database connection handle: $conn
include_once("mysql_conn.php");
// The non-empty search keyword is sent to server
if (isset($_GET["keywords"]) && trim($_GET['keywords']) != "") {
    ob_end_clean();
    // Retrieve list of product records with "ProductTitle" 
	// contains the keyword entered by shopper, and display them in a table.
    $keyword = "%".$_GET["keywords"]."%";
	$qry = "SELECT * FROM Product
            WHERE ProductTitle LIKE ?
            OR ProductDesc LIKE ?";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("ss", $keyword, $keyword);   // "ss" - string
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    echo "<p style='font-weight: bold; margin: 0 0 0 20px;'>Search results for $_GET[keywords]:</p>";
    if(mysqli_num_rows($result) > 0) {
        echo"<div class='products-container row'>";
        while ($row = $result->fetch_array()) {
            $product = "productDetails.php?pid=$row[ProductID]";
            $productTitle = $row["ProductTitle"];
            $productImg = "./Images/products/$row[ProductImage]";
            // echo $productImg;
            echo"<div class='product-container col-sm-4'>
                     <div class='product-container-inner' onclick=\"location.href='$product';\" style='margin: 0px 15px'>
                         <img class=productImg src=$productImg alt='Image of $productTitle' />
                         <p class='productTitle'>$productTitle</p>
                     </div>
                 </div>";

            // echo "<div class='row' style='padding: 5px'>";  // Start a new row
            // echo "<p style='margin: 0px 0px 0px 20px;'><a href=$product>$row[ProductTitle]</a></p>";
            // echo "</div>";
        }
        echo "</div>";
    }
    else {
        echo "<p style='font-weight: bold; color: red; margin-left: 20px;'>No results found!</p>";
    }
    
	// To Do (DIY): End of Code
}
echo "</div>"; // End of container
?>

</div>

<?php
include("footer.php"); // Include the Page Layout footer
?>