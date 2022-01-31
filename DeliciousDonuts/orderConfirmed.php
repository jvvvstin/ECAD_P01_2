<?php
session_start();

include("header.php");

if(isset($_SESSION["OrderID"])){
    echo "<p>Checkout successful. Your order number is $_SESSION[OrderID]</p>";
    
    echo "<p>Thank you for your purchase.&nbsp;&nbsp;";
    echo "<a href='index.php'>Continue shopping</a></p>";



}

include("footer.php");


?>