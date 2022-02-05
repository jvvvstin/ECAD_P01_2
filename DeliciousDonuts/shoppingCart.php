<?php 
// Include the code that contains shopping cart's functions.
// Current session is detected in "cartFunctions.php, hence need not start session here.
include_once("cartFunctions.php");
include("header.php"); // Include the Page Layout header


if (! isset($_SESSION["ShopperID"])) { // Check if user logged in 
	// redirect to login page if the session variable shopperid is not set
	header ("Location: login.php");
	exit;
}

echo "<div id='myShopCart container' style='width:1200px;margin:auto'>"; // Start of main container
if (isset($_SESSION["Cart"])) {
	echo"<center>"	;
	echo "<p class='page-title' style='font-size:30px;padding:20px;'>Shopping Cart</p>"; 
	include_once("mysql_conn.php");
	// Retrieve from database and display shopping cart in a table
	$qry = "SELECT *, (ShopCartItem.Price*ShopCartItem.Quantity) AS Total,ShopCartItem.Quantity AS CQuantity FROM ShopCartItem INNER JOIN Product ON ShopCartItem.ProductID = Product.ProductID WHERE ShopCartID=?";
	$stmt = $conn->prepare($qry);
	$stmt->bind_param("i", $_SESSION["Cart"]);
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();
	
	if ($result->num_rows > 0) {
		// the page header and header row of shopping cart page
		echo "<div style=float:left >"; // Start of shopping cart table container
		echo "<div class='table-responsive' >"; // Bootstrap responsive table
		echo "<table class='table table-hover' style='border:0.5px solid lightgray'>"; // Start of table
		echo "<thead class='cart-header' style='background-color:#fec0cc'>";
		echo "<tr>";
		echo "<th width='300px' colspan='2'>Product</th>";
		echo "<th width='60px'>Quantity</th>";
		echo "<th width='90px'>Price</th>";
		echo "<th width='120px'>Total</th>";
		echo "<th>&nbsp;</th>";
		echo "</tr>";
		echo "</thead>";
		// Declare an array to store the shopping cart items in session variable 
		$_SESSION["Items"]=array();	
		// Display the shopping cart content
		$subTotal = 0; // Declare a variable to compute subtotal before tax
		$totalQuantity = 0; // Declare a variable to compute number of items
		echo "<tbody>"; // Start of table's body section
		//header("Location: productDetails.php?pid=$value[ProductID]");
		while ($row = $result->fetch_array()) {
			echo "<tr>";
			$img = "./Images/products/$row[ProductImage]";
			echo "<td style='width:14%'><input type='image' src=$img title='Product Picture' width='100%' height='100%'/>";
			echo "</td>";
			echo "<td style='width:43%'><b> <a href='./productDetails.php?pid=$row[ProductID]'  style='color:black' >$row[Name]</a></b><br />";
			echo "<p style=font-size:9.5px>$row[ProductDesc]";
			echo "<p style=font-size:11px>Product ID: $row[ProductID]</td>";
			echo "<td>";
			echo "<form action='cartFunctions.php' method='post'>";
			echo "<select name='quantity' onChange='this.form.submit()'>";
			for ($i = 1; $i <= 10; $i++) {
				if ($i == $row["CQuantity"])
					$selected = "selected";
				else
					$selected = "";
				echo "<option value='$i' $selected>$i</option>";
			}
			echo "</select>";
			echo "<input type='hidden' name='action' value='update' />";
			echo "<input type='hidden' name='product_id' value='$row[ProductID]' />";
			echo "</form>";
			echo "</td>";
			echo "<td>$$row[Price]</td>";
			$formattedTotal = number_format($row["Total"], 2);
			echo "<td>$$formattedTotal</td>";
			echo "<td>";
			echo "<form action='cartFunctions.php' method='post'>";
			echo "<input type='hidden' name='action' value='remove' />";
			echo "<input type='hidden' name='product_id' value='$row[ProductID]' />";
			echo "<input type='image' src='images/close.png' title='Remove Item' width='13px' height='13px'/>";
			echo "</form>";
			echo "</td>";
			echo "</tr>";	
		    // Store the shopping cart items in session variable as an associate array
			$_SESSION["Items"][] = array("productId"=>$row["ProductID"],
										 "name"=>$row["Name"],
										 "price"=>$row["Price"],
										 "quantity"=>$row["CQuantity"]);

			// Accumulate the running sub-total
			$subTotal += $row["Total"];
			$totalQuantity += $row["CQuantity"];
		}
		echo "</tbody>"; // End of table's body section
		echo "</table>"; // End of table
		echo "</div>"; // End of Bootstrap responsive table
		echo "</div>"; // End of Bootstrap responsive table

		echo "<div style='float:right;margin-left:5px;width:25%;border:0.5px solid lightgray'>"; // Start a container for checkout
		// Display the subtotal & total Quantity at the end of the shopping cart
		echo "<p style='text-align:left; font-size:25px;padding-top:5px;padding-left:20px;background-color:#fec0cc;color:white'>CART TOTALS";
		echo "<p style='text-align:left; font-size:15px;padding-left:20px'>
			  Total Item(s): &nbsp;&nbsp;&nbsp;&nbsp;". $totalQuantity;
		echo "<p style='text-align:left; font-size:15px;padding-left:20px'>
			  Subtotal   S$: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".  number_format($subTotal, 2);
		
		$_SESSION["SubTotal"] = round($subTotal, 2);	
		
		echo "<form method='post' action='checkout.php'>";
		if ($subTotal > 50)
		{
			echo "<p style='text-align:left; font-size:15px;color:red;padding-left:20px'>Normal Delivery Charges waived!";
			echo"<input  class='form-control' type='hidden'
                name='waived' id='waived' value='true' />";
		}
		else{
			echo"<input  class='form-control' type='hidden'
                name='waived' id='waived' value='false' />";
		}
		echo "<button type='submit' class='cartbtn' style='color:white;margin-left:1px;margin-bottom:10px;margin-top:10px; background-color: rgb(0, 132, 255); font-weight: 500; font-size: 18px; height: 50px; padding: 0 30px; border: none; border-radius: 5px; cursor: pointer;'>Proceed to Checkout</button>";
	

		echo "</form></p>";		
		echo "</div>"; // End of container for checkout
	}
	else {
		echo "<h3 style='text-align:center; color:red;'>Empty shopping cart!</h3>";
	}
	$conn->close(); // Close database connection
	
}
else {
	echo "<h3 style='text-align:center; color:red;'>Empty shopping cart!</h3>";
}	
		echo "</div>"; // End of main container
include("footer.php"); // Include the Page Layout footer
?>
