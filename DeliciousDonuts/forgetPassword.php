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
        <h2 style="text-align:center;">Password Security Retrieval</h2>
    </div>
    <div class="col-sm">
    </div>
</div>
<div class="row align-items-center">
    <div class="col-sm">
    </div>
    <div class="col-sm">
        <p style="text-align:center;">Please enter your email to load your security question</p>
    </div>
    <div class="col-sm">
    </div>
</div>
<br />

<div style="width:80%; margin:auto;">
<form name="forgetPassword" method="post" onsubmit="" action="retrieveEmail.php">
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="email">
         Email: </label>
        <div class="col-sm-9">
            <input class="form-control" name="email" id="email" 
                   type="email" required />
        </div>
    </div>

    <div class="form-group row">       
        <div class="col-sm-9 offset-sm-3">
            <button type="submit">Submit</button>
        </div>
    </div>
</form>

</div> <!-- Closing container -->
<?php 
include("footer.php"); // Include the Page Layout footer
?>