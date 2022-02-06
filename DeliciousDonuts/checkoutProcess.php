<?php
session_start();
include("header.php"); // Include the Page Layout header
include_once("myPayPal.php");
include_once("mysql_conn.php"); 
//testing
//$_SESSION["ShopperID"] = 1;
//$_SESSION["Cart"] = 1;

if($_POST) //Post Data received from checkout page
{
	//
	$qty = 0;
    $qry = "SELECT * FROM shopcartitem WHERE ShopCartID = ?";
        $stmt = $conn->prepare($qry);

        $stmt->bind_param("i", $_SESSION["Cart"]);
        $stmt->execute();
        $result=$stmt->get_result();

        if($result->num_rows > 0){
            while($row = $result->fetch_array()){
                $qry = "SELECT Quantity FROM product WHERE ProductID = ?";
                $stmt = $conn->prepare($qry);
                $stmt->bind_param("i", $row["ProductID"]);
                if ($stmt->execute()){
                    $stmt->bind_result($qty);
                    while ($stmt->fetch()) {
                        $a = array('Quantity' => $qty);
                    }
                    $stmt->close();
                    if ($row["Quantity"] > $qty){
                        $a = $row["ProductID"];
                        $b = $row["Name"];
                        $c = $row["Quantity"];
                        echo "<div style='text-align: center; padding-top: 5%;'>";
                        echo "<h3>Product $a : $b is out of stock!</h3><br />";
                        echo "<h4>Requested Quantity: $c</h4>";
                        echo "<h4>Please return to shopping cart to amend your purchase.</h4> <br />";
                        echo "<h4><a href='shoppingCart.php'>Proceed Back to Shopping Cart</a></h4>";
                        echo "</div>";
                        include("footer.php");
                        exit;
                    }

                }
    		
		    }
    }

    $paypal_data = '';
    // Get all items from the shopping cart, concatenate to the variable $paypal_data
    // $_SESSION['Items'] is an associative array
    foreach($_SESSION['Items'] as $key=>$item) {
        $paypal_data .= '&L_PAYMENTREQUEST_0_QTY'.$key.'='.urlencode($item["quantity"]);
        $paypal_data .= '&L_PAYMENTREQUEST_0_AMT'.$key.'='.urlencode($item["price"]);
        $paypal_data .= '&L_PAYMENTREQUEST_0_NAME'.$key.'='.urlencode($item["name"]);
        $paypal_data .= '&L_PAYMENTREQUEST_0_NUMBER'.$key.'='.urlencode($item["productId"]);
    }
	
	//Get form data
    $_SESSION["Tax"] = $_POST["Tax"];
    $_SESSION["ShipName"] = $_POST["ShipName"];
    $_SESSION["BillAddress"] = $_POST["BillAddress"];
    $_SESSION["BillCountry"] = "Singapore";
    $_SESSION["ShipPhone"] = $_POST["ShipPhone"];
    $_SESSION["ShipEmail"] = $_POST["ShipEmail"];
    $deliveryMode = $_POST["delivery"];
    $_SESSION["ShipCharge"] = $_POST["delivery"];
    $_SESSION["Message"] = $_POST["Message"];
    //$_SESSION["FinalTotal"] = substr($_POST["finalTotal"],1);
    $_SESSION["BillPhone"] = $_POST["BillPhone"];
    $_SESSION["BillEmail"] = $_POST["BillEmail"];

    $hour = Date("H");
    //get delivery date & timeslot
    if ($deliveryMode == "5"){
        $_SESSION["DeliveryMode"] = "Express";
        if($hour < 10){
            $_SESSION["DeliveryTime"] = "9am - 12noon";
            $_SESSION["DeliveryDate"] = date("Y-m-d");
        }

        else if ($hour >= 10 && $hour < 13){
            $_SESSION["DeliveryTime"] = "12noon - 3pm";
            $_SESSION["DeliveryDate"] = date("Y-m-d");
        }

        else if ($hour >= 13 && $hour < 16){
            $_SESSION["DeliveryTime"] = "3pm - 6pm";
            $_SESSION["DeliveryDate"] = date("Y-m-d");
        }

        else{
            $_SESSION["DeliveryTime"] = "9am - 12noon";
            $_SESSION["DeliveryDate"] = date("Y-m-d", strtotime("+1 day"));
        }
    }

    else{
        $_SESSION["DeliveryMode"] = "Normal";
        if($hour < "12"){
            $_SESSION["DeliveryTime"] = "9am - 12noon";
            $_SESSION["DeliveryDate"] = date("Y-m-d", strtotime("+1 day"));
        }

        else if($hour >= "12" && $hour < 15 ){
            $_SESSION["DeliveryTime"] = "12noon - 3pm";
            $_SESSION["DeliveryDate"] = date("Y-m-d", strtotime("+1 day"));
        }

        //within 24 hours: even if order is placed late at night,
        //it will be delivered at the latest timeslot the next day
        else if($hour >= 15){
            $_SESSION["DeliveryTime"] = "3pm - 6pm";
            $_SESSION["DeliveryDate"] = date("Y-m-d", strtotime("+1 day"));
        }
    }

    //Data to be sent to PayPal
	$padata = '&CURRENCYCODE='.urlencode($PayPalCurrencyCode).
    '&PAYMENTACTION=Sale'.
    '&ALLOWNOTE=1'.
    '&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($PayPalCurrencyCode).
    '&PAYMENTREQUEST_0_AMT='.urlencode($_SESSION["SubTotal"] +
                                       $_SESSION["Tax"] + 
                                       $_SESSION["ShipCharge"]).
    '&PAYMENTREQUEST_0_ITEMAMT='.urlencode($_SESSION["SubTotal"]). 
    '&PAYMENTREQUEST_0_SHIPPINGAMT='.urlencode($_SESSION["ShipCharge"]). 
    '&PAYMENTREQUEST_0_TAXAMT='.urlencode($_SESSION["Tax"]). 	
    '&BRANDNAME='.urlencode("Delicious Donuts").
    $paypal_data.				
    '&RETURNURL='.urlencode($PayPalReturnURL ).
    '&CANCELURL='.urlencode($PayPalCancelURL);

    //We need to execute the "SetExpressCheckOut" method to obtain paypal token
	$httpParsedResponseAr = PPHttpPost('SetExpressCheckout', $padata, $PayPalApiUsername, 
    $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);

    //Respond according to message we receive from Paypal
    if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || 
    "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {					
    if($PayPalMode=='sandbox')
    $paypalmode = '.sandbox';
    else
    $paypalmode = '';

    //Redirect user to PayPal store with Token received.
    $paypalurl ='https://www'.$paypalmode. 
    '.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token='.
    $httpParsedResponseAr["TOKEN"].'';
    header('Location: '.$paypalurl);
    }
    else {
        //Show error message
        echo "<div style='color:red'><b>SetExpressCheckOut failed : </b>".
            urldecode($httpParsedResponseAr["L_LONGMESSAGE0"])."</div>";
        echo "<pre>".print_r($httpParsedResponseAr)."</pre>";
    }
}

//Paypal redirects back to this page using ReturnURL, We should receive TOKEN and Payer ID
if(isset($_GET["token"]) && isset($_GET["PayerID"])) 
{	
	//we will be using these two variables to execute the "DoExpressCheckoutPayment"
	//Note: we haven't received any payment yet.
	$token = $_GET["token"];
	$playerid = $_GET["PayerID"];
	$paypal_data = '';
	
	// Get all items from the shopping cart, concatenate to the variable $paypal_data
	// $_SESSION['Items'] is an associative array
	foreach($_SESSION['Items'] as $key=>$item) 
	{
		$paypal_data .= '&L_PAYMENTREQUEST_0_QTY'.$key.'='.urlencode($item["quantity"]);
	  	$paypal_data .= '&L_PAYMENTREQUEST_0_AMT'.$key.'='.urlencode($item["price"]);
	  	$paypal_data .= '&L_PAYMENTREQUEST_0_NAME'.$key.'='.urlencode($item["name"]);
		$paypal_data .= '&L_PAYMENTREQUEST_0_NUMBER'.$key.'='.urlencode($item["productId"]);
	}
	
	//Data to be sent to PayPal
	$padata = '&TOKEN='.urlencode($token).
			  '&PAYERID='.urlencode($playerid).
			  '&PAYMENTREQUEST_0_PAYMENTACTION='.urlencode("SALE").
			  $paypal_data.	
			  '&PAYMENTREQUEST_0_ITEMAMT='.urlencode($_SESSION["SubTotal"]).
              '&PAYMENTREQUEST_0_TAXAMT='.urlencode($_SESSION["Tax"]).
              '&PAYMENTREQUEST_0_SHIPPINGAMT='.urlencode($_SESSION["ShipCharge"]).
			  '&PAYMENTREQUEST_0_AMT='.urlencode($_SESSION["SubTotal"] + 
			                                     $_SESSION["Tax"] + 
								                 $_SESSION["ShipCharge"]).
			  '&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($PayPalCurrencyCode);
	
	//We need to execute the "DoExpressCheckoutPayment" at this point 
	//to receive payment from user.
	$httpParsedResponseAr = PPHttpPost('DoExpressCheckoutPayment', $padata, 
	                                   $PayPalApiUsername, $PayPalApiPassword, 
									   $PayPalApiSignature, $PayPalMode);
	
	//Check if everything went ok..
	if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || 
	   "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) 
	{
		// To Do 5 (DIY): Update stock inventory in product table 
		//                after successful checkout
        $qry = "SELECT * FROM shopcartitem WHERE ShopCartID = ?";

        $stmt = $conn->prepare($qry);

        $stmt->bind_param("i", $_SESSION["Cart"]);
        $stmt->execute();
        $result=$stmt->get_result();

        if($result->num_rows > 0){
        while($row = $result->fetch_array()){
            $qry2 = 'UPDATE Product Set Quantity = Quantity-? WHERE ProductID = ?';
            $stmt2 = $conn->prepare($qry2);
            $stmt2->bind_param("dd", $row["Quantity"], $row["ProductID"]);
            $stmt2->execute();
            $stmt2->close();
        }
        }
		
		// End of To Do 5
	
		// To Do 2: Update shopcart table, close the shopping cart (OrderPlaced=1)
		$total = $_SESSION["SubTotal"] + $_SESSION["Tax"] + $_SESSION["ShipCharge"];
		$qry = "UPDATE shopcart SET OrderPlaced=1, Quantity=?, SubTotal=?, ShipCharge=?, Tax=?, Total=? WHERE ShopCartID=?";
		$stmt = $conn->prepare($qry);
		// "i" - integer, "d" - double
		$stmt->bind_param("iddddi", $_SESSION["NumCartItem"],
						  $_SESSION["SubTotal"], $_SESSION["ShipCharge"],
						  $_SESSION["Tax"], $total,
						  $_SESSION["Cart"]);
		$stmt->execute();
		$stmt->close();
		// End of To Do 2
		
		//We need to execute the "GetTransactionDetails" API Call at this point 
		//to get customer details
		$transactionID = urlencode(
		                 $httpParsedResponseAr["PAYMENTINFO_0_TRANSACTIONID"]);
		$nvpStr = "&TRANSACTIONID=".$transactionID;
		$httpParsedResponseAr = PPHttpPost('GetTransactionDetails', $nvpStr, 
		                                   $PayPalApiUsername, $PayPalApiPassword, 
										   $PayPalApiSignature, $PayPalMode);

		if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || 
		   "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) 
		   {
			//gennerate order entry and feed back orderID information
			//You may have more information for the generated order entry 
			//if you set those information in the PayPal test accounts.
			
			$ShipName = addslashes(urldecode($httpParsedResponseAr["SHIPTONAME"]));
			
			$ShipAddress = urldecode($httpParsedResponseAr["SHIPTOSTREET"]);
			if (isset($httpParsedResponseAr["SHIPTOSTREET2"]))
				$BillingAddress .= ' '.urldecode($httpParsedResponseAr["SHIPTOSTREET2"]);
			if (isset($httpParsedResponseAr["SHIPTOCITY"]))
			    $BillingAddress .= ' '.urldecode($httpParsedResponseAr["SHIPTOCITY"]);
			if (isset($httpParsedResponseAr["SHIPTOSTATE"]))
			    $ShipAddress .= ' '.urldecode($httpParsedResponseAr["SHIPTOSTATE"]);
			$ShipAddress .= ' '.urldecode($httpParsedResponseAr["SHIPTOCOUNTRYNAME"]). 
			                ' '.urldecode($httpParsedResponseAr["SHIPTOZIP"]);
				
			$ShipCountry = urldecode(
			               $httpParsedResponseAr["SHIPTOCOUNTRYNAME"]);
			
			$ShipEmail = urldecode($httpParsedResponseAr["EMAIL"]);			
			
			// To Do 3: Insert an Order record with shipping information
			//          Get the Order ID and save it in session variable.
			$qry = "INSERT INTO orderdata (ShopCartID, ShipName, ShipAddress, ShipCountry,ShipPhone,
												 ShipEmail, BillName, BillAddress, BillCountry, BillPhone, BillEmail,
												  DeliveryDate, DeliveryTime, DeliveryMode, Message, DateOrdered) VALUE(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$stmt = $conn->prepare($qry);
			$stmt->bind_param("isssssssssssssss", $_SESSION["Cart"], $_SESSION["ShipName"], $ShipAddress, $ShipCountry, $_SESSION["ShipPhone"], $ShipEmail, $_SESSION["ShipName"], $_SESSION["BillAddress"], $_SESSION["BillCountry"], $_SESSION["BillPhone"], $_SESSION["BillEmail"], $_SESSION["DeliveryDate"], $_SESSION["DeliveryTime"], $_SESSION["DeliveryMode"], $_SESSION["Message"], $_SESSION["DeliverDate"]);
			$stmt->execute();
			$stmt->close();
			$qry = "SELECT LAST_INSERT_ID() AS OrderID";
			$result = $conn->query($qry);
			$row = $result->fetch_array();
			$_SESSION["OrderID"] = $row["OrderID"];
			// End of To Do 3
				
			$conn->close();
				  
			// To Do 4A: Reset the "Number of Items in Cart" session variable to zero.
			$_SESSION["NumCartItem"] = 0;
	  		
			// To Do 4B: Clear the session variable that contains Shopping Cart ID.
			unset($_SESSION["Cart"]);
			
			// To Do 4C: Redirect shopper to the order confirmed page.
			header("Location: orderConfirmed.php");
			exit;
		} 
		else 
		{
		    echo "<div style='color:red'><b>GetTransactionDetails failed:</b>".
			                urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
			echo "<pre>".print_r($httpParsedResponseAr)."</pre>";
			$conn->close();
		}
	}
	else {
		echo "<div style='color:red'><b>DoExpressCheckoutPayment failed : </b>".
		                urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
		echo "<pre>".print_r($httpParsedResponseAr)."</pre>";
	}
}
?>
<?php
include("footer.php"); // Include the Page Layout footer
?>
