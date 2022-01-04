<?php
    // Detect the current session
    session_start();
    // Include the Page Layout header
    include("header.php");
?>
<div style="background-image: url('Images/donut-background.jpg'); background-size: 10%;">
<!-- Create a centrally located container -->
<div style="width: 80%; max-width: 900px; margin: auto; background-color: white; padding: 15px 50px 0px;">
<!-- Create a HTML Form within the container -->
<form action="checkLogin.php" method="post">
<!-- 1st row - Header Row -->
<div class="form-group row">
    <div class="col-sm-9 offset-sm-3">
        <span class="page-title">Member Login</span>
    </div>
</div>
<!-- 2nd row  Entry of email address -->
<div class="form-group row">
    <label class="col-sm-3 col-form-label" for="email">
        Email Address:
    </label>
    <div class="col-sm-9">
        <input  class="form-control" type="email"
                name="email" id="email" required />
    </div>
</div>
<!-- 3rd row - Entry of password -->
<div class="form-group row">
    <label class="col-sm-3 col-form-label" for="password">
        Password:
    </label>
    <div class="col-sm-9">
        <input class="form-control" type="password"
               name="password" id="password" required />
    </div>
</div>
<?php 
    if (isset($_SESSION["LoginError"])) {
        echo "<div class='form-group row'>
                <label class='col-sm-3 col-form-label'></label>
                <div class='col-sm-9'>
                    <h5 style='color:red; font-size: 100%;'>$_SESSION[LoginError]</h5>
                </div>
            </div>";
        unset($_SESSION["LoginError"]);
    }
?>

<!-- 4th row - Login button -->
<div class="form-group row" style="margin-bottom: 0px;">
    <div class="col-sm-9 offset-sm-3">
        <button type="submit" class="btn btn-primary" style="background-color: #fec0cc; color: black;">Login</button>
        <p>Please <a href="register.php" style="text-decoration: underline">sign up</a> if you do not have an account.</p>
        <p><a href="forgetPassword.php">Forget Password</a></p>
    </div>
</div>
</form>
</div>
</div>
<?php
// Include the Page Layout footer
include("footer.php");
?>