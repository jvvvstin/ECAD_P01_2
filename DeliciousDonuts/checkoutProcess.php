<?php
session_start();
include("header.php"); // Include the Page Layout header
include_once("mysql_conn.php"); 
//testing
//$_SESSION["ShopperID"] = 1;
//$_SESSION["Cart"] = 1;

if(isset($_SESSION["Cart"])) //if a cart variable exists
{
    // Retrieve shopping cart items from database
	$qry = "SELECT *, (Price*Quantity) AS Total FROM ShopCartItem WHERE ShopCartID=?";
	$stmt = $conn->prepare($qry);
	$stmt->bind_param("i", $_SESSION["Cart"]);
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();

    if ($result->num_rows > 0) {
		// Declare an array to store the shopping cart items in session variable 
		$_SESSION["Items"] = array();	
        //compute subtotal
        $subTotal = 0;
         echo "<div class='row' style='width: 100%;'>";
         echo "<div class='col-sm-6' style='padding-left:3%;'>";
         echo "<table class='form-container' style='float: right; width: 100%; display: flex; justify-content: flex-start; align-items: flex-start; flex-direction: column;'>";
         echo "<h2 class='form-title' style='padding-top: 13%; font-size: 36px; margin-bottom: 2.5rem; font-weight: 500; opacity: 0.8;'>Your Order</h2>";
         echo "<tbody style='width:100%'>";
         echo " <tr>
         <th style='display: table-cell; padding: 0.5em 0.5em 0 0; text-align: left; font-size: x-large; width: 72%'>Product Name</th>
         <th style='display: table-cell; padding: 0.5em 0.5em 0 0; text-align: left; font-size: x-large;'>Product Total</th>
         </tr>";
         while ($row = $result->fetch_array()) {
            echo "<tr style='line-height: 3;'>";
             echo "<th style='display: table-cell; padding: 0.5em 0.5em 0 0; text-align: left; font-size: large; font-weight: normal; width: 72%'>$row[Name]&nbsp;<strong>x&nbsp;$row[Quantity]</strong></th>";
             echo "<th style='display: table-cell; padding: 0.5em 0.5em 0 0; text-align: right; font-size: large; font-weight: normal;'><strong>$$row[Total]</strong></th>";
             echo "</tr>";
             $subTotal += $row["Total"];
             $_SESSION["SubTotal"] = $subTotal;
         }
    }

    $total = 0;
    $final = 0;
    $delivery = 2;
    $tax = 0;
    // Retrieve gst details from database
    $qry2 = "SELECT * FROM gst WHERE EffectiveDate < curdate()
            ORDER BY EffectiveDate DESC LIMIT 1";
    $stmt = $conn->prepare($qry2);
    $stmt->execute();
    $result = $stmt->get_result();
    $waived = NULL;

    if (isset($_POST["waived"])){
        $waived = $_POST["waived"];
    }

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_array()) {
        $tax = round($subTotal*($row["TaxRate"]/100),2);
        $total = round($subTotal*(1 + ($row["TaxRate"]/100)),2);
        echo "<tr style='line-height: 3;'>";
        echo "<th style='display: table-cell; padding: 0.5em 0.5em 0 0; text-align: left; font-size: large; font-weight: normal; width: 72%'><strong style='color: grey;'>Subtotal (w/ GST)</strong></th>";
        echo "<th style='display: table-cell; padding: 0.5em 0.5em 0 0; text-align: right; font-size: large; font-weight: normal;'><strong id='subTotal' name='subTotal'>$$total</strong></th>";
        echo "</tr>";
        }
    }
     echo "<tr style='line-height: 3;'>";
     if ($waived == "true"){
        echo "<th style='display: table-cell; padding: 0.5em 0.5em 0 0; text-align: left; font-size: large; font-weight: normal; width: 72%'><strong style='color: grey;'>Delivery Charge</strong></th>";
        echo "<th style='display: table-cell; padding: 0.5em 0.5em 0 0; text-align: right; font-size: large; font-weight: normal;'><strong id='deliveryValue'>waived</strong></th>";
    }
    else {
        echo "<th style='display: table-cell; padding: 0.5em 0.5em 0 0; text-align: left; font-size: large; font-weight: normal; width: 72%'><strong style='color: grey;'>Delivery Charge</strong></th>";
        echo "<th style='display: table-cell; padding: 0.5em 0.5em 0 0; text-align: right; font-size: large; font-weight: normal;'><strong id='deliveryValue'></strong></th>";
    }
     echo "</tr>";
     echo"<tr style='border-bottom:1px solid black;'></tr>";
     echo "<tr style='line-height: 3;'>";
     echo "<th style='display: table-cell; padding: 0.5em 0.5em 0 0; text-align: left; font-size: large; font-weight: normal; width: 72%'><strong>Total</strong></th>";
     echo "<th style='display: table-cell; padding: 0.5em 0.5em 0 0; text-align: right; font-size: large; font-weight: normal;'><strong id='totalCost' name='totalCost'>$$total</strong></th>";
     echo "</tr>";
     echo "</tbody>
     </table>
     </div>";

    echo "
        <div class='col-sm-6'>
            <form method='post' action='checkoutProcess.php' class='form-container' style='float: left; width: 100%; margin-top: 6rem; display: flex; justify-content: flex-start; align-items: flex-start; flex-direction: column;'>
            <h2 class='form-title' style='font-size: 36px; margin-bottom: 2.5rem; font-weight: 500; opacity: 0.8;'>Delivery Details</h2>
            <div class='checkout-form' style='display: flex; justify-content: center; align-items: flex-start; flex-direction: column; width: 100%;'>
                <div class='input-line' style='display: flex; justify-content: center; align-items: flex-start; margin-bottom: 2rem; width: 100%;'>
                    <label for='name' style='font-size: 16px; color: grey; margin-bottom: 0.5rem;'>Deliver To&nbsp;<abbr style='color: red;'>*</abbr>&emsp;&emsp;&emsp;&emsp;&emsp;</label>
                    <input type='text' style='width: 100%; height: 40px; padding: 0 10px; background-color: #f2f2f2; border-radius: 5px; border: none; font-size: 18px;' name='ShipName' id='ShipName' placeholder='John Ecader' required>
                </div>
                <div class='input-line' style='display: flex; justify-content: center; align-items: flex-start; margin-bottom: 2rem; width: 100%;'>
                    <label for='name' style='font-size: 16px; color: grey; margin-bottom: 0.5rem;'>Recipient Mobile No&nbsp;<abbr style='color: red;'>*</abbr></label>
                    <input type='text' style='width: 100%; height: 40px; padding: 0 10px; background-color: #f2f2f2; border-radius: 5px; border: none; font-size: 18px;' id='ShipPhone' name='ShipPhone' placeholder='(65) 1234 5678' required>
                </div>
                <div class='input-line' style='display: flex; justify-content: center; align-items: flex-start; margin-bottom: 2rem; width: 100%;'>
                    <label for='name' style='font-size: 16px; color: grey; margin-bottom: 0.5rem;'>Recipient Email&nbsp;<abbr style='color: red;'>*</abbr>&emsp;&emsp;&ensp;</label>
                    <input type='text' style='width: 100%; height: 40px; padding: 0 10px; background-color: #f2f2f2; border-radius: 5px; border: none; font-size: 18px;' id='ShipEmail' name='ShipEmail' placeholder='' required>
                </div>
                <div class='input-line' style='display: flex; justify-content: center; align-items: flex-start; margin-bottom: 2rem; width: 100%;'>
                    <label for='name' style='font-size: 16px; color: grey; margin-bottom: 0.5rem;'>Message&emsp;&emsp;&emsp;&emsp;</label>
                    <textarea style='width: 100%; height: 40px; padding: 0 10px; background-color: #f2f2f2; border-radius: 5px; border: none; font-size: 18px;' id='Message' name='Message' placeholder='Merry Christmas!'></textarea>
                </div>
                <div class='input-line' style='display: flex; justify-content: center; align-items: flex-start; margin-bottom: 2rem; width: 100%;'>";

                if ($waived == "true"){
                    echo" <label for='name' style='padding-right: 10%; font-size: 16px; color: grey; margin-bottom: 0.5rem;'>Delivery Mode&nbsp;<abbr style='color: red;'>*</abbr>&emsp;&emsp;&emsp;&emsp;&emsp;</label>
                    <input type='radio' onclick='check()' style='width: 10%; height: 20px; padding: 0 10px; background-color: #f2f2f2; border: none;' id='DeliveryMode' name='delivery' value='0'>
                    <label for='name' value='normal' style='padding-right: 20%; font-size: 16px; color: grey; margin-bottom: 0.5rem;'>Normal</label>
                    <input type='radio' onclick='check()' value='5' style='width: 10%; height: 20px; padding: 0 10px; background-color: #f2f2f2; border: none;' id='DeliveryMode' name='delivery' value='5'>
                    <label for='name' style='padding-right: 20%; font-size: 16px; color: grey; margin-bottom: 0.5rem;'>Express</label>
                    </div>";
                }
                else {
                    echo" <label for='name' style='padding-right: 10%; font-size: 16px; color: grey; margin-bottom: 0.5rem;'>Delivery Mode&nbsp;<abbr style='color: red;'>*</abbr>&emsp;&emsp;&emsp;&emsp;&emsp;</label>
                    <input type='radio' onclick='check()' style='width: 10%; height: 20px; padding: 0 10px; background-color: #f2f2f2; border: none;' id='DeliveryMode' name='delivery' value='2' required>
                    <label for='name' value='normal' style='padding-right: 20%; font-size: 16px; color: grey; margin-bottom: 0.5rem;'>Normal</label>
                    <input type='radio' onclick='check()' value='5' style='width: 10%; height: 20px; padding: 0 10px; background-color: #f2f2f2; border: none;' id='DeliveryMode' name='delivery' value='5' required>
                    <label for='name' style='padding-right: 20%; font-size: 16px; color: grey; margin-bottom: 0.5rem;'>Express</label>
                    </div>";
                }
                echo "
                <br/>
                <br/>
                <div class='input-line' style='display: flex; justify-content: center; align-items: flex-start; margin-bottom: 2rem; width: 100%;'>
                    <label for='name' style='font-size: 16px; color: grey; margin-bottom: 0.5rem;'>Billing Address&nbsp;<abbr style='color: red;'>*</abbr>&emsp;&nbsp;</label>
                    <input type='text' style='width: 100%; height: 40px; padding: 0 10px; background-color: #f2f2f2; border-radius: 5px; border: none; font-size: 18px;' id='BillAddress' name='BillAddress' placeholder='' required>
                </div>
                <div class='input-line' style='display: flex; justify-content: center; align-items: flex-start; margin-bottom: 2rem; width: 100%;'>
                    <label for='name' style='font-size: 16px; color: grey; margin-bottom: 0.5rem;'>Billing Mobile No&nbsp;<abbr style='color: red;'>*</abbr></label>
                    <input type='text' style='width: 100%; height: 40px; padding: 0 10px; background-color: #f2f2f2; border-radius: 5px; border: none; font-size: 18px;' id='BillPhone' name='BillPhone' placeholder='(65) 1234 5678' required>
                </div>
                <div class='input-line' style='display: flex; justify-content: center; align-items: flex-start; margin-bottom: 2rem; width: 100%;'>
                    <label for='name' style='font-size: 16px; color: grey; margin-bottom: 0.5rem;'>Billing Email&nbsp;<abbr style='color: red;'>*</abbr>&emsp;&emsp;&ensp;</label>
                    <input type='text' style='width: 100%; height: 40px; padding: 0 10px; background-color: #f2f2f2; border-radius: 5px; border: none; font-size: 18px;' id='BillEmail' name='BillEmail' placeholder='' required>
                </div>
                <input type='hidden' name='Tax' id='Tax' value='$tax'>
                <input type='hidden' name='finalTotal' id='finalTotal' value=''>
                <div class='input-container' style='display: flex; justify-content: right; align-items: center; width: 100%;'>
                <!--<button type='submit' style='float: right; background-color: rgb(0, 132, 255); color: white; font-weight: 500; font-size: 18px; height: 50px; padding: 0 30px; border: none; border-radius: 5px; cursor: pointer;'>Continue</button>-->
                <input type='image' style='float:right;' src='https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif'>
            </div>
            </form>
        </div>
        </div>
        </div>";
}
?>
<script>
    function check() {
        if (document.querySelector('input[name="delivery"]:checked').value == "0"){
            document.getElementById("deliveryValue").innerHTML = "waived";
        }
        else {
            document.getElementById("deliveryValue").innerHTML = '$' + document.querySelector('input[name="delivery"]:checked').value;
        }
        document.getElementById("totalCost").innerHTML = '$' + (Number.parseFloat(document.getElementById("subTotal").innerText.substr(1),10) + Number.parseFloat(document.querySelector('input[name="delivery"]:checked').value,10)).toFixed(2).toString();
        document.getElementById("FinalTotal").innerHTML = '$' + (Number.parseFloat(document.getElementById("subTotal").innerText.substr(1),10) + Number.parseFloat(document.querySelector('input[name="delivery"]:checked').value,10)).toFixed(2).toString();
    };
</script>
<?php
include("footer.php"); // Include the Page Layout footer
?>
