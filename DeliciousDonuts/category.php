<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>
<div style="background-image: url('Images/donut-background.jpg'); background-size: 10%;">
<!-- Create a container, 60% width of viewport -->
<div style="width:80%; max-width: 900px; background-color: white; margin:auto;">
<!-- Display Page Header -->
<div class="row" style="padding:5px; margin: 0px;"> <!-- Start of header row -->
    <div class="col-12 productcat-title">
        <span class="page-title">Product Categories</span>
        <p>Select a category listed below:</p>
    </div>
</div> <!-- End of header row -->
<?php 
// Include the PHP file that establishes database connection handle: $conn
include_once("mysql_conn.php");

$qry = "SELECT * FROM Category ORDER BY CatName"; // Form SQL to select all categories in alphabetical order
$result = $conn->query($qry); // Execute the SQL and get the result

$row_num = 1;
while($row = $result->fetch_array()) {
    if ($row_num == $result->num_rows) { // Checks if its last row
        // If last row, increase padding for bottom
        echo "<div class='row' style='padding: 5px 0px 50px 0px; margin: 15px 0px 0px 0px;'>";  // Start a new row
    }
    else {
        echo "<div class='row' style='padding: 5px; margin: 15px 0px;'>";  // Start a new row
    }

    // Left column - display the category's image that links to the product listing page
    $img = "./Images/category/$row[CatImage]";
    $catname = urlencode($row["CatName"]);
    $catproduct = "catProduct.php?cid=$row[CategoryID]&catName=$catname";
    echo "<div class='col-md-4 catproduct-img'>"; // 33% of row width
    echo "<a href=$catproduct><img src='$img' /></a>";
    echo "</div>";

    // Right column - display category's description in a new paragraph
    echo "<div class='col-md-8'>"; // 67% of row width
    echo "$row[CatDesc]";
    echo "</div>";
    echo "</div>";  // End of a row
    $row_num += 1;
}
?>

</div>
</div>
<?php
$conn->close(); // Close database connnection
include("footer.php"); // Include the Page Layout footer
?>