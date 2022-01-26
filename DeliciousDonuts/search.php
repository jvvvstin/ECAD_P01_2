<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>

<div style="background-image: url('Images/donut-background.jpg'); background-size: 10%;">
<!-- HTML Form to collect search keyword and submit it to the same page in server -->
<div style="width:80%; max-width: 900px; background-color: white; margin:auto; min-height: 375px;"> <!-- Container -->
<form id="frmSearch" name="frmSearch" method="post" action="">
    <div class="form-group row" style='padding: 5px; margin: 0px;'> <!-- 1st row -->
        <div class="col-sm-9 offset-sm-3">
            <span class="page-title">Product Search</span>
        </div>
    </div> <!-- End of 1st row -->
    <div class="form-group row" style='padding: 5px; margin: 0px;'> <!-- 2nd row -->
        <label id="searchLabel" for="keywords" 
               class="col-sm-3 col-form-label">Product:</label>
        <div class="col-sm-6">
            <input style="margin-bottom: 10px;" class="form-control" name="keywords" id="keywords" placeholder="Search for a product..."
                   type="search" />
        </div>
        <div class="col-sm-3">
            <!-- <button id="searchBtn" type="submit" style="border-radius: 3px;">Search</button> -->
            <button type='button' id="searchBtn" onclick="submitForms();" style="border-radius: 3px;">Search</button>
        </div>
    </div>  <!-- End of 2nd row -->

<?php 
include_once("mysql_conn.php");

// Get the current date, without time
$todaysDate = new DateTime('now');
$date = $todaysDate->format('Y-m-d');

// Retrieve all products, in alphabetical order
$qry = "SELECT * FROM Product
        ORDER BY ProductTitle";
$result = $conn->query($qry);
$prices = array(); // Used to store the current price (original/offer price if on offer)
//                    Will be used to determine the min-max price of all the products for range sliders

// Get the current date, without time
$todaysDate = new DateTime('now');
$date = $todaysDate->format('Y-m-d');

// Used to store all the products retrieved from the database
$products = array();

while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row; // Store all the row records in the array
}

// Loop through each product
foreach($products as $key => $value) {
    // Checks if the product is currently on offer
    $onOffer = $value["Offered"] == 1 && $date >= $value["OfferStartDate"] && $date <= $value["OfferEndDate"];
    if ($onOffer) { // If the product is on offer, get the offer price
        array_push($prices, $value["OfferedPrice"]);
    }
    else { // If the product is not on offer, get the current/original price
        array_push($prices, $value["Price"]);
    }
}

// Get the min and max of the prices of the products for range slider
$min_price = number_format(min($prices),2);
$max_price = number_format(max($prices),2);
$min_sweet = 0;
$max_sweet = 5;

// Displays the price and sweetness range sliders
echo "<div class='row' style='padding: 5px; margin: 0px;'>
    <div class='col-md-3'>
        <div class='list-group'>
            <h5>Price (S$)</h5>
            <input type='hidden' name='hidden_minimum_price' id='hidden_minimum_price' value='$min_price' />
            <input type='hidden' name='hidden_maximum_price' id='hidden_maximum_price' value='$max_price' />
            <p id='price_show'>$min_price - $max_price</p>
            <div id='price_range'></div>
        </div>
        <br />
        <div class='list-group'>
            <h5>Sweetness (Out of 5)</h5>
            <input type='hidden' name='hidden_minimum_sweetness' id='hidden_minimum_sweetness' value='$min_sweet' />
            <input type='hidden' name='hidden_maximum_sweetness' id='hidden_maximum_sweetness' value='$max_sweet' />
            <p id='sweetness_show'>$min_sweet - $max_sweet</p>
            <div id='sweetness_range'></div>
        </div>                     
    </div>";

// Stores the products
echo"<div class=col-md-9><div id='allProducts' class='products-container row'>";

// Loops through all the products and display them
foreach($products as $key => $value) {
    $product = "productDetails.php?pid=$value[ProductID]";
    $productTitle = $value["ProductTitle"];
    $productImg = "./Images/products/$value[ProductImage]";
    $price = 0;
    $onOffer = $value["Offered"] == 1 && $date >= $value["OfferStartDate"] && $date <= $value["OfferEndDate"];
    if ($onOffer) {
        $price = $value["OfferedPrice"];
    }
    else {
        $price = $value["Price"];
    }
    $formattedPrice = number_format($price, 2);
    if ($value["Quantity"] <= 0) {
        echo"<div class='product-container col-sm-4'>
                <div class='product-container-inner' onclick=\"location.href='$product';\" style='margin: 0px 15px'>
                    <img class='productImg' src=$productImg alt='Image of $productTitle' />
                    <p class='productTitle'>$productTitle</p>
                    <p class='productTitle'>$$formattedPrice</p>
                    <p style='color: red; margin: 0px'>Out of Stock!</p>
                </div>
            </div>";
    }
    else {
        echo"<div class='product-container col-sm-4'>
                <div class='product-container-inner' onclick=\"location.href='$product';\" style='margin: 0px 15px'>
                    <img class='productImg' src=$productImg alt='Image of $productTitle' />
                    <p class='productTitle'>$productTitle</p>
                    <p class='productTitle'>$$formattedPrice</p>
                </div>
            </div>";
    }
}

echo "</div>";
// Include the PHP file that establishes database connection handle: $conn
// The non-empty search keyword is sent to server

// Checks if sweetness and price has been submitted
if (isset($_POST["hidden_minimum_price"]) || isset($_POST["hidden_maximum_price"]) || isset($_POST["hidden_minimum_sweetness"]) || isset($_POST["hidden_maximum_sweetness"])) {
    // Remove all the products displayed earlier on
    echo "<script>document.getElementById('allProducts').style.display = 'none';</script>";
    $searchCondition = "Search conditions: ";

    // SQL qeuery string to retrieve the products from the database that matches the conditions specified
    $qry = "SELECT p.*, ps.SpecVal 
                FROM Product p 
                INNER JOIN ProductSpec ps ON p.ProductID =  ps.ProductID 
                INNER JOIN Specification s ON ps.SpecID = s.SpecID 
                WHERE s.SpecName = 'Sweetness (Out of 5)' ";
    
    // SQL to retrieve products between the max and min price specified
    // Price used to determine if its between the max and min price stated will use original price by default, unless the product is on offer
    $qry = $qry . "AND ((p.Offered = 1 AND CURDATE() BETWEEN p.OfferStartDate AND p.OfferEndDate AND p.OfferedPrice BETWEEN $_POST[hidden_minimum_price] AND $_POST[hidden_maximum_price]) OR (p.Offered = 0 AND p.Price BETWEEN $_POST[hidden_minimum_price] AND $_POST[hidden_maximum_price]) OR (p.Offered = 1 AND (CURDATE() < p.OfferStartDate OR CURDATE() > p.OfferEndDate) AND p.Price BETWEEN $_POST[hidden_minimum_price] AND $_POST[hidden_maximum_price])) ";
    $min_price = number_format($_POST["hidden_minimum_price"], 2);
    $max_price = number_format($_POST["hidden_maximum_price"], 2);
    $searchCondition = $searchCondition . "Price: $$min_price - $$max_price, ";

    // SQL to retrieve products between the max and min sweetness specified
    $qry = $qry . "AND ps.SpecVal BETWEEN $_POST[hidden_minimum_sweetness] AND $_POST[hidden_maximum_sweetness] ";
    $searchCondition = $searchCondition . "Sweetness: $_POST[hidden_minimum_sweetness] - $_POST[hidden_maximum_sweetness]";
    $keyword = "";
    
    // Checks if a keyword is entered into the search bar
    if (isset($_POST["keywords"]) && trim($_POST['keywords']) != "") {
        $searchCondition = $searchCondition . ", Keyword: $_POST[keywords]";
        $keyword = "%".$_POST["keywords"]."%";

        // SQL to retrieve products which title or product desc contains the keywords specified by the user
        $qry = $qry . "AND (p.ProductTitle LIKE ? OR p.ProductDesc LIKE ?) ";
    }
    
    // SQL to sort the products retrieved in alphabetical order
    $qry = $qry . "ORDER BY p.ProductTitle";
    if ($keyword == "") {
        $result = $conn->query($qry);
    }
    else {
        $stmt = $conn->prepare($qry);
        $stmt->bind_param("ss", $keyword, $keyword);   // "ss" - string
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    }

    echo "<p style='font-weight: bold; font-size: 120%; margin: 0 0 0 20px;'>$searchCondition</p>";
    
    // Checks if there are any records returned
    if(mysqli_num_rows($result) > 0) {
        echo"<div class='products-container row'>";
        while ($row = $result->fetch_array()) {
            $product = "productDetails.php?pid=$row[ProductID]";
            $productTitle = $row["ProductTitle"];
            $productImg = "./Images/products/$row[ProductImage]";
            $price = 0;
            $onOffer = $row["Offered"] == 1 && $date >= $row["OfferStartDate"] && $date <= $row["OfferEndDate"];
            if ($onOffer) {
                $price = $row["OfferedPrice"];
            }
            else {
                $price = $row["Price"];
            }
            $formattedPrice = number_format($price, 2);
            if ($row["Quantity"] <= 0) {
                echo"<div class='product-container col-sm-4'>
                     <div class='product-container-inner' onclick=\"location.href='$product';\" style='margin: 0px 15px'>
                         <img class='productImg' src=$productImg alt='Image of $productTitle' />
                         <p class='productTitle'>$productTitle</p>
                         <p class='productTitle'>$$formattedPrice</p>
                         <p style='color: red; margin: 0px'>Out of Stock!</p>
                     </div>
                 </div>";
            }
            else {
                echo"<div class='product-container col-sm-4'>
                     <div class='product-container-inner' onclick=\"location.href='$product';\" style='margin: 0px 15px'>
                         <img class='productImg' src=$productImg alt='Image of $productTitle' />
                         <p class='productTitle'>$productTitle</p>
                         <p class='productTitle'>$$formattedPrice</p>
                     </div>
                 </div>";
            }
            
        }
        echo "</div>";
    }
    else {
        echo "<p style='font-weight: bold; font-size: 120%; color: red; margin-left: 20px;'>No results found!</p>";
        echo "</div>";
    }
    unset($_POST["hidden_minimum_price"]);
    unset($_POST["hidden_maximum_price"]);
    unset($_POST["hidden_minimum_sweetness"]);
    unset($_POST["hidden_maximum_sweetness"]);
    unset($_POST["keywords"]);
}

echo "</div>"; // End of container
echo "</div>";
echo "</div>";
echo "</form>";
?>

</div>


<?php
include("footer.php"); // Include the Page Layout footer
?>

<script>
    $(function(){
        
        $('#price_range').slider({
            range: true,
            min: <?php echo $min_price?>,
            max: <?php echo $max_price?>,
            values: [<?php echo $min_price?>, <?php echo $max_price?>],
            step: 0.10,
            stop: function(event, ui)
            {
                $('#price_show').html(ui.values[0] + ' - ' + ui.values[1]);
                $('#hidden_minimum_price').val(ui.values[0]);
                $('#hidden_maximum_price').val(ui.values[1]);
            }
        });

        $('#sweetness_range').slider({
            range: true,
            min: <?php echo $min_sweet?>,
            max: <?php echo $max_sweet?>,
            values: [<?php echo $min_sweet?>, <?php echo $max_sweet?>],
            step: 0.5,
            stop: function(event, ui)
            {
                $('#sweetness_show').html(ui.values[0] + ' - ' + ui.values[1]);
                $('#hidden_minimum_sweetness').val(ui.values[0]);
                $('#hidden_maximum_sweetness').val(ui.values[1]);
            }
        });

        submitForms = function() {
            document.forms[0].submit();
        }
    });
</script>