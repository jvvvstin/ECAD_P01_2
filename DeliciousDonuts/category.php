<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>
<div style="background-image: url('Images/donut-background.jpg'); background-size: 10%;">
<!-- Create a container, 60% width of viewport -->
<div style="width:80%; max-width: 900px; background-color: white; margin:auto;">
<!-- Display Page Header -->
<div class="row" style="padding:5px; margin: 0px;"> <!-- Start of header row -->
    <div class="col-12">
        <span class="page-title">Product Categories</span>
        <p>Select a category listed below:</p>
    </div>
</div> <!-- End of header row -->
<?php 
// Include the PHP file that establishes database connection handle: $conn
include_once("mysql_conn.php");

// To Do:  Starting ....
$qry = "SELECT * FROM Category";    // Form SQL to select all categories
$result = $conn->query($qry);       // Execute the SQL and get the result

// Display each category in a row
// while($row = $result->fetch_array()) {
//     echo "<div class='row' style='padding: 5px'>";  // Start a new row

//     // Left column - display a text link showing the category's name,
//     //               display category's description in a new paragraph
//     $catname = urlencode($row["CatName"]);
//     $img = "./Images/category/$row[CatImage]";
//     $catproduct = "catProduct.php?cid=$row[CategoryID]&catName=$catname";
//     echo "<div class='col-12'>"; // 67% of row width
//     echo "<a style='display: block;' href=$catproduct><img src='$img' /></a>";
//     // echo "<p><a href=$catproduct>$row[CatName]</a></p>";
//     echo "$row[CatDesc]";
//     echo "</div>";

//     // // Right column - display the category's image
//     // $img = "./Images/category/$row[CatImage]";
//     // echo "<div class='col-4'>"; // 33% of row width
//     // echo "<img src='$img' />";
//     // echo "</div>";

//     echo "</div>";  // End of a row
// }

$row_num = 1;
while($row = $result->fetch_array()) {
    if ($row_num == $result->num_rows) { // Checks if its last row
        // If last row, increase padding for bottom
        echo "<div class='row' style='padding: 5px 0px 50px 0px; margin: 15px 0px 0px 0px;'>";  // Start a new row
    }
    else {
        echo "<div class='row' style='padding: 5px; margin: 15px 0px;'>";  // Start a new row
    }

    // Left column - display the category's image
    $img = "./Images/category/$row[CatImage]";
    $catname = urlencode($row["CatName"]);
    $catproduct = "catProduct.php?cid=$row[CategoryID]&catName=$catname";
    echo "<div class='col-md-4 catproduct-img'>"; // 33% of row width
    echo "<a href=$catproduct><img src='$img' /></a>";
    echo "</div>";

    // Right column - display a text link showing the category's name,
    //               display category's description in a new paragraph
    echo "<div class='col-md-8'>"; // 67% of row width
    echo "$row[CatDesc]";
    echo "</div>";

    echo "</div>";  // End of a row
    $row_num += 1;
}
?>

<!-- To Do:  Ending .... -->
</div>
</div>
<?php
$conn->close(); // Close database connnection
include("footer.php"); // Include the Page Layout footer
?>