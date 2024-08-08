<?php
include 'partials/_dbconnect.php';
$login = false; // Assuming this was meant to track login status
$showError = false; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Validate email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result->num_rows) {
        $showError ="No user found with that email.";
        exit;
    }

    // Generate OTP
    $otp = bin2hex(random_bytes(3)); // Generates a 6-character alphanumeric string

    // Store OTP and its expiration time in the database
    $expiration_time = date('Y-m-d H:i:s', strtotime('+2 minutes'));
    $stmt = $conn->prepare("UPDATE users SET otp = ?, otp_expiration = ? WHERE email = ?");
    $stmt->bind_param("sss", $otp, $expiration_time, $email);
    $stmt->execute();

    // Send OTP via email
    $to = $email;
    $subject = "Your OTP";
    $message = "Your OTP is: " . $otp;
    mail($to, $subject, $message);

    $showAlert="OTP sent to your email.";
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>Login</title>
  </head>
  <body>
    <?php require 'partials/_nav.php' ?>
    <?php
    if($login){
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
        <h2 class="text-center">Please Login </h2>
        <form action="/test/login.php" method="post" style="display: flex; align-items: center; flex-direction: column;">
            <div class="mb-3  col-md-4">
                <label for="email"     class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp">
            </div>
            
            <button type="submit" class="btn btn-primary col-md-4">Request OTP</button>
        </form>
    </div>
    <div class="container">
        <h2 class="text-center">Password </h2>
        <form action="/test/login.php" method="post" style="display: flex; align-items: center; flex-direction: column;">
            <div class="mb-3  col-md-4">
                <label for="email"     class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp">
            </div>
            
            <button type="submit" class="btn btn-primary col-md-4">Request OTP</button>
        </form>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  </body>
</html>