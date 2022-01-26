<?php 
// Detect the current session
session_start();
// Include the Page Layout header
include("header.php"); 
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.10.2/mdb.min.css" rel="stylesheet"/>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.10.2/mdb.min.js"></script>

<!-- Carousel wrapper -->
<div id="carouselDarkVariant" class="carousel slide carousel-fade carousel-dark" data-mdb-ride="carousel">
  <!-- Indicators -->
  <div class="carousel-indicators">
    <button
      type="button"
      data-mdb-target="#carouselDarkVariant"
      data-mdb-slide-to="0"
      class="active"
      aria-current="true"
      aria-label="Slide 1"
    ></button>
    <button
      type="button"
      data-mdb-target="#carouselDarkVariant"
      data-mdb-slide-to="1"
      aria-label="Slide 2"
    ></button>
    <button
      type="button"
      data-mdb-target="#carouselDarkVariant"
      data-mdb-slide-to="2"
      aria-label="Slide 3"
    ></button>
  </div>

  <!-- Inner -->
  <div class="carousel-inner">
    <!-- Single item -->
    <div class="carousel-item active">
      <img src="Images/homepageDonut.jpg" class="d-block w-100" alt="donuts"/>
      <div class="carousel-caption d-none d-md-block">
        <!-- <h5>First slide label</h5>
        <p>Nulla vitae elit libero, a pharetra augue mollis interdum.</p> -->
      </div>
    </div>

    <!-- Single item -->
    <div class="carousel-item">
      <img src="Images/homepageDonut2.jpg" class="d-block w-100" alt="donuts"/>
      <div class="carousel-caption d-none d-md-block">
        <!-- <h5>Delicious Donuts</h5>
        <p>delicious donuts for sale</p> -->
      </div>
    </div>

    <!-- Single item -->
    <div class="carousel-item">
      <img src="Images/homepageDonut3.jpg" class="d-block w-100" alt="donut plate"/>
      <div class="carousel-caption d-none d-md-block">
        <!-- <h5>Third slide label</h5>
        <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur.</p> -->
      </div>
    </div>
  </div>
  <!-- Inner -->
</div>
<!-- Carousel wrapper -->

<?php 

$qry = "SELECT * FROM Product WHERE CURDATE() BETWEEN OfferStartDate AND OfferEndDate AND Offered = ?";
include_once("mysql_conn.php");
$stmt = $conn->prepare($qry);
$onOffer = 1;
$stmt->bind_param("i", $onOffer);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
if (mysqli_num_rows($result) > 0) {
  echo "<h1 style='font-size: 300%; margin-top: 25px; text-align: center; font-weight: bold;'>On Offer</h1>";
  echo"<div class='products-container row'>";
        while ($row = $result->fetch_array()) {
            $product = "productDetails.php?pid=$row[ProductID]";
            $productTitle = $row["ProductTitle"];
            $productImg = "./Images/products/$row[ProductImage]";
            $formattedOriPrice = number_format($row["Price"], 2);
            $formattedPrice = number_format($row["OfferedPrice"], 2);
            echo"<div class='product-container col-sm-4' style='display: flex; justify-content: center; margin-bottom: 25px;'>
                     <div class='product-container-inner' onclick=\"location.href='$product';\" style='margin: 0px 15px; background-color: #fec0cc; border-radius: 15px; width: 275px;'>
                         <img style='width: 80%; margin-top: 25px; border-radius: 15px;' src=$productImg alt='Image of $productTitle' />
                         <p class='productTitle' style='font-weight: bold; margin: 15px 0px 0px 0px;'>$productTitle</p>
                         <p style='margin: 0px;'><del>$$formattedOriPrice</del></p><p style='color: red; font-size: 150%;'>$$formattedPrice</p>
                     </div>
                 </div>";
        }
        echo "</div>";
}

?>

<?php 
// Include the Page Layout footer
include("footer.php"); 
?>
