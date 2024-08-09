<?php
$showAlert = false;
$showError = false;
include 'partials/_dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'partials/_dbconnect.php';
    $entered_otp = $_POST['otp'];
    $email = $_POST['email'];

    // Retrieve user details based on OTP
    $stmt = $conn->prepare("SELECT * FROM users WHERE otp = ? AND email = ?");
    $stmt->bind_param("ss", $entered_otp, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_time = new DateTime(); // Current time
        $expiration_time = new DateTime($row['otp_expiration']); // Expiration time from DB

        if ($current_time <= $expiration_time) {
            // OTP is valid, proceed to password input
            echo "OTP verified successfully.";
            // Redirect to password input page or display form here
        } else {
            $showAlert= "OTP has expired.";
        }
    } else {
        $showError = "Invalid OTP.";
    }
}
?>




<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>Verify OTP</title>
  </head>
  <body>
    <?php require 'partials/_nav.php' ?>
    <?php
    if($showAlert){
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong>Success!</strong> You are logged in.
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div> ';
    }
    if($showError){
      echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong>'. $showError.' 
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div> ';
      }
    ?>
    <div class="container">
        <h1 class="text-center">Verify OTP </h1>
        <form action="" method="post" style="display: flex; align-items: center; flex-direction: column;">
            <div class="mb-3  col-md-4">
                <label for="otp"     class="form-label">OTP</label>
                <input type="text" class="form-control" id="otp" name="otp" placeholder="OTP" >
            </div>
            
            <button type="submit" class="btn btn-primary col-md-4">Verify OTP</button>
        </form>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  </body>
</html>
