<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
include_once("mysql_conn.php");

?>
<br />
<div class="row align-items-center">
    <div class="col-sm">
    </div>
    <div class="col-sm">
        <h2 style="text-align:center;">Donut Rating</h2>
    </div>
    <div class="col-sm">
    </div>
</div>
<div class="row align-items-center">
    <div class="col-sm">
    </div>
    <div class="col-sm">
        <p style="text-align:center;">Feedback and reviews of our donuts</p>
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

echo "<div style='width:70%; margin:auto;'>
<form name='feedback' method='get' action=''>
    <div class='form-group row'>
        <label class='col-sm-3 col-form-label' for='donut'>
        Donut: </label>
        <div class='col-sm-6'>
            <select class='form-control' name='donut' id='donut' type='search'>
                <option value=''>All feedback</option>";
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
    <div class='col-sm-3'>
            <button type='submit'>View</button>
        </div>
</div>
</div>
</form> ";

?>
<?php
if (isset($_GET['donut'])) 
{
    $search_item = $_GET['donut'];
    $rank = 0;
    $counter = 0;
    $pTitle = "";

    $comment = array();
    $user = array();
    $rating = array();
    $productTitle = array();

    $qry = "SELECT r.*, p.*, s.* 
            FROM ranking r 
            INNER JOIN Product p ON r.ProductID = p.ProductID
            INNER JOIN Shopper s ON r.ShopperID = s.ShopperID
            ORDER BY r.Rank DESC";

    $stmt = $conn->prepare($qry);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0)
    {
        while($row = $result->fetch_array())
        {
            if($search_item == '')
            {
                $pTitle = "All Donuts ";
                array_push($comment, $row["Comment"]);
                array_push($user, $row["Name"]);
                array_push($rating, $row["Rank"]);
                array_push($productTitle, $row["ProductTitle"]);

                $rank += $row["Rank"];
                $counter += 1;
            }
            else if($row["ProductID"] == $search_item)
            {
                array_push($comment, $row["Comment"]);
                array_push($user, $row["Name"]);
                array_push($rating, $row["Rank"]);
                array_push($productTitle, $row["ProductTitle"]);
                $pTitle = $row["ProductTitle"];
                $rank += $row["Rank"];
                $counter += 1;
            }
        }

        if($rank == 0)
        {
            $Message = "<h2>There is no rating for this product</h2>";
        }
        else
        {
            $average_rating = $rank/$counter;
            $Message = "<h2>Average Rating for $pTitle: $average_rating / 5 </h2>";
        }

    }
    echo "<br />";
    echo "<div class='text-center'>";
    echo $Message;
    echo "</div>";
    echo "<br />";
    echo "<br />";


    for ($i = 0; $i < count($user); $i++)
    {
        echo "<div class='row align-items-center'>
        <div class='col-sm-2'>
        </div>
        <div class='col-sm-7'>";
            echo "<b><p>$user[$i]</p></b>";
            echo "<p><i>$productTitle[$i]</i></p>";
            echo "<p>$comment[$i]</p>";
        echo"    </div>
        <div class='col-sm-3'>";
            echo "<b><p>$rating[$i] out of 5 rating</p></b>";
        echo"   </div>
    </div>";
    echo "<br />";
    }
}
?>

<?php 
include("footer.php"); // Include the Page Layout footer
?>


