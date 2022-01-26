<?php 
//Display guest welcome message, Login and Registration links
//when shopper has yet to login,
$content1 = "<div class='row'>
                <div class='col-sm-12'>
                    <a href='index.php'>
                        <img src='Images/donut.png' alt='Logo'
                        class='img-fluid' id='navbar-logo' />
                        <span id='navbar-storename'>
                            Delicious<br />
                            Donuts
                        </span>
                    </a>
                </div>
            </div>";
$content2 = "<li class='nav-item'>
		     <a class='nav-link' href='register.php'>Sign Up</a></li>
			 <li class='nav-item'>
		     <a class='nav-link' href='login.php'>Login</a></li>
             <li class='nav-item'>
             <a class='nav-link' href='shoppingCart.php'><img #cart-icon src='Images/shopping-cart.png' onmouseover='hover(this);' onmouseout='unhover(this);' alt='Cart' class='img-fluid' style='width: 25px;'/></a></li>";

// if(isset($_SESSION["ShopperName"])) { 
//     //Display a greeting message, Change Password and logout links 
//     //after shopper has logged in.
//     $content1 = "Welcome <b>$_SESSION[ShopperName]</b>";
//     $content2 = "<li class='nav-item'>
//                  <a class='nav-link' href='changePassword.php'>Change Password</a></li>
//                  <li class='nav-item'>
//                  <a class='nav-link' href='logout.php'>Logout</a></li>";	

//     //Display number of item in cart
// 	if (isset($_SESSION["NumCartItem"])) {
//         if ($_SESSION["NumCartItem"] != 0) {
//             $content1 .= ", $_SESSION[NumCartItem] item(s) in shopping cart";
//         }
//     }
// }
?>
<!-- Display a navbar which is visible before or after collapsing -->
<nav class="navbar navbar-expand-md navbar-dark bg-custom" style="box-shadow: none;">
    <!-- Dynamic Text Display -->
    <span class="navbar-text ml-md-2"
        style="color: #F7Be81; max-width: 80%;">
        <?php echo $content1; ?> <!-- echo means to display, this line displays $content1 defined in line 4 -->
    </span>
    <!-- Toggler/Collapsible Button -->
    <button class="navbar-toggler" type="button" data-toggle="collapse"
            data-target="#collapsibleNavbar">
        <span class="navbar-toggler-icon" id="custom-toggler"></span> <!-- hamburger button -->
    </button>
</nav>

<?php 
$content3 = "<div style='background-color:#fec0cc; padding-left: 24px;'>Welcome Guest!<br /></div>";
if(isset($_SESSION["ShopperName"])) { 
	//To Do 1 (Practical 2) - 
    //Display a greeting message, Change Password and logout links 
    //after shopper has logged in.
    $content3 = "<div style='background-color:#fec0cc; padding-left: 24px;'>Welcome <b>$_SESSION[ShopperName]!</b></div>";
    $content2 = "<li class='nav-item'>
                 <a class='nav-link' href='changePassword.php'>Change Password</a></li>
                 <li class='nav-item'>
                 <a class='nav-link' href='logout.php'>Logout</a></li>
                 <li class='nav-item'>
                <a class='nav-link' href='shoppingCart.php'><img #cart-icon src='Images/shopping-cart.png' onmouseover='hover(this);' onmouseout='unhover(this);' alt='Cart' class='img-fluid' style='width: 25px;'/></a></li>";	

    //Display number of item in cart
	if (isset($_SESSION["NumCartItem"])) {
        if ($_SESSION["NumCartItem"] != 0) {
            // $content1 .= ", $_SESSION[NumCartItem] item(s) in shopping cart";
        }
    }
}
echo $content3;
?>

<!-- Define a collapsible navbar -->
<nav class="navbar navbar-expand-md navbar-light bg-custom" style="box-shadow: none;">
    <!-- Collapsible part of navbar -->
    <div class="collapse navbar-collapse" id="collapsibleNavbar">
        <!-- Left-justified menu items -->
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="category.php">Product Categories</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="search.php">Product Search</a>
            </li>
        </ul>
        <!-- Right-justified menu items -->
        <ul class="navbar-nav ml-auto">
            <?php echo $content2; ?>
        </ul>
    </div>
</nav>

<script>
    function hover(element) {
        element.setAttribute('src', 'Images/shopping-cart-hover.png');
    }

    function unhover(element) {
        element.setAttribute('src', 'Images/shopping-cart.png');
    }
</script>